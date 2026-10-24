<?php
/**
 * PACTDIGITAL-BF — Catalogue des formations "Netflix du savoir"
 * Page moderne avec filtres par domaine et cards visuelles
 */

require_once('../../config.php');
require_once($CFG->libdir . '/filelib.php');

if ($CFG->forcelogin) {
    require_login();
}

$PAGE->set_url(new moodle_url('/local/pactdigital/catalogue.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Catalogue des formations — PACTDIGITAL-BF');
$PAGE->set_heading('Catalogue des formations');

// ── Récupérer les catégories racines (profondeur 1) ──────────────────────────
$root_cats = $DB->get_records('course_categories', ['depth' => 1, 'visible' => 1], 'sortorder ASC');

// ── Mapping domaine → slug, couleur (Charte Graphique v2.0 MTDPCE) ───────────
function pact_get_domain_meta($catname) {
    $map = [
        'fondamentales' => ['slug' => 'fondamentales', 'color' => '#00843b', 'light' => '#ecf7f0', 'emoji' => '', 'label' => 'Fondamentales'],
        'managériales'  => ['slug' => 'manageriales',  'color' => '#2a6d9b', 'light' => '#eef4f9', 'emoji' => '', 'label' => 'Managériales'],
        'manageri'      => ['slug' => 'manageriales',  'color' => '#2a6d9b', 'light' => '#eef4f9', 'emoji' => '', 'label' => 'Managériales'],
        'sectorielles'  => ['slug' => 'sectorielles',  'color' => '#b07800', 'light' => '#fdf4e3', 'emoji' => '', 'label' => 'Sectorielles'],
        'émergentes'    => ['slug' => 'emergentes',    'color' => '#045527', 'light' => '#d5f0e0', 'emoji' => '', 'label' => 'Émergentes'],
        'emergentes'    => ['slug' => 'emergentes',    'color' => '#045527', 'light' => '#d5f0e0', 'emoji' => '', 'label' => 'Émergentes'],
    ];
    $name_lower = mb_strtolower($catname);
    foreach ($map as $keyword => $meta) {
        if (mb_strpos($name_lower, $keyword) !== false) {
            return $meta;
        }
    }
    return ['slug' => 'autres', 'color' => '#635c55', 'light' => '#f7f5f2', 'emoji' => '', 'label' => 'Autres'];
}

// ── Niveau badge selon shortname ─────────────────────────────────────────────
function pact_get_level_badge($shortname) {
    $s = strtolower($shortname);
    if (preg_match('/(^|_|\s)or($|_|\s)/i', $s)) {
        return ['label' => '🥇 Or', 'cls' => 'badge-or', 'level' => 'or'];
    }
    if (strpos($s, 'argent') !== false || strpos($s, 'silver') !== false || strpos($s, 'diaman') !== false) {
        return ['label' => '🥈 Argent', 'cls' => 'badge-argent', 'level' => 'argent'];
    }
    if (strpos($s, 'bronze') !== false) {
        return ['label' => '🥉 Bronze', 'cls' => 'badge-bronze', 'level' => 'bronze'];
    }
    // Fallback: chercher dans le nom
    return ['label' => '🎓 Formation', 'cls' => 'badge-default', 'level' => 'all'];
}

// ── Charger tous les cours visibles (hors site) ──────────────────────────────
$sql = "SELECT c.id, c.fullname, c.shortname, c.summary, c.category,
               c.visible, c.timecreated,
               cat.name AS catname, cat.parent AS catparent,
               cat2.name AS rootcatname
        FROM {course} c
        JOIN {course_categories} cat ON cat.id = c.category
        LEFT JOIN {course_categories} cat2 ON cat2.id = (
            CASE WHEN cat.depth = 1 THEN cat.id
                 ELSE (SELECT id FROM {course_categories} WHERE id = cat.parent LIMIT 1)
            END
        )
        WHERE c.id > 1 AND c.visible = 1
        ORDER BY cat.parent ASC, cat.sortorder ASC, c.sortorder ASC, c.id ASC";

$courses_raw = $DB->get_records_sql($sql);

// ── Construire le tableau JS des cours ───────────────────────────────────────
$courses_for_js = [];
$courses_html   = '';

foreach ($courses_raw as $c) {
    $root_cat_name = $c->rootcatname ?: $c->catname;
    $domain_meta   = pact_get_domain_meta($root_cat_name);
    $level_badge   = pact_get_level_badge($c->shortname);
    $view_url      = (new moodle_url('/course/view.php', ['id' => $c->id]))->out(false);
    
    // Image du cours
    $course_obj = get_course($c->id);
    $course_element = new core_course_list_element($course_obj);
    $img_url = '';
    foreach ($course_element->get_course_overviewfiles() as $file) {
        if ($file->is_valid_image()) {
            $img_url = moodle_url::make_pluginfile_url(
                $file->get_contextid(), $file->get_component(),
                $file->get_filearea(), null,
                $file->get_filepath(), $file->get_filename()
            )->out(false);
            break;
        }
    }
    
    // Durée estimée (en modules)
    $module_count = $DB->count_records('course_modules', ['course' => $c->id, 'deletioninprogress' => 0]);
    $duration_str = $module_count > 0 ? $module_count . ' activité' . ($module_count > 1 ? 's' : '') : 'Auto-rythmé';

    // Vérifier si l'utilisateur est inscrit
    $is_enrolled_user = false;
    if (isloggedin() && !isguestuser()) {
        $is_enrolled_user = is_enrolled(context_course::instance($c->id), $USER->id);
    }

    // Progression si connecté et inscrit
    $progress_pct = -1;
    if ($is_enrolled_user) {
        $completion = new completion_info($course_obj);
        if ($completion->is_enabled()) {
            $progress = \core_completion\progress::get_course_progress_percentage($course_obj, $USER->id);
            if ($progress !== null) {
                $progress_pct = (int) round($progress);
            }
        } else {
            $progress_pct = 0;
        }
    }

    // Formatter le titre du cours : si juste "Bronze", "Argent", "Or", préfixer par le nom du domaine
    $raw_title = trim($c->fullname);
    if (in_array($raw_title, ['Bronze', 'Argent', 'Or'])) {
        $level_label = $raw_title;
        $course_title = $c->catname . ' — ' . $level_label;
    } else {
        $course_title = $c->fullname;
    }
    $safe_fullname = htmlspecialchars($course_title, ENT_QUOTES, 'UTF-8');
    $safe_summary  = htmlspecialchars(strip_tags($c->summary ?? ''), ENT_QUOTES, 'UTF-8');
    $summary_short = mb_strimwidth(strip_tags($c->summary ?? ''), 0, 120, '…');

    $courses_for_js[] = [
        'id'     => $c->id,
        'domain' => $domain_meta['slug'],
    ];
    
    // ── Génération HTML de la card ────────────────────────────────────────────
    $card_style = $img_url
        ? "background-image: url('{$img_url}'); background-size: cover; background-position: center;"
        : "background: linear-gradient(135deg, {$domain_meta['color']}22 0%, {$domain_meta['color']}44 100%);";
    
    $progress_html = '';
    if ($progress_pct >= 0) {
        $progress_html = '<div class="pact-card-progress">
            <div class="pact-progress-bar" style="width:' . $progress_pct . '%"></div>
            <span class="pact-progress-label">' . $progress_pct . '%</span>
        </div>';
    }

    $btn_label = ($progress_pct > 0 && $progress_pct < 100) ? 'Continuer' :
                 ($progress_pct >= 100 ? 'Revoir' : 'Commencer');
    $btn_icon  = ($progress_pct > 0 && $progress_pct < 100) ? '▶️' :
                 ($progress_pct >= 100 ? '✅' : '🎯');

    $courses_html .= '<div class="pact-course-card" data-domain="' . $domain_meta['slug'] . '" data-level="' . $level_badge['level'] . '" data-title="' . $safe_fullname . '">';
    $courses_html .= '  <div class="pact-card-image" style="' . $card_style . '">';
    if (!$img_url && !empty($domain_meta['emoji'])) {
        $courses_html .= '    <div class="pact-card-emoji">' . $domain_meta['emoji'] . '</div>';
    }
    $courses_html .= '    <span class="pact-domain-chip" style="background:' . $domain_meta['color'] . '">'
                       . $domain_meta['label'] . '</span>';
    $courses_html .= '    <span class="pact-level-badge ' . $level_badge['cls'] . '">' . $level_badge['label'] . '</span>';
    $courses_html .= '  </div>';
    $courses_html .= '  <div class="pact-card-body">';
    $courses_html .= '    <h3 class="pact-card-title">' . $safe_fullname . '</h3>';
    if ($summary_short) {
        $courses_html .= '    <p class="pact-card-desc">' . htmlspecialchars($summary_short, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    $courses_html .= '    <div class="pact-card-meta">';
    $courses_html .= '      <span class="pact-meta-item">⏱️ ' . $duration_str . '</span>';
    $courses_html .= '    </div>';
    $courses_html .= $progress_html;

    // ── Boutons d'action selon l'état de l'utilisateur ───────────────────────
    if (!isloggedin() || isguestuser()) {
        $signup_url = (new moodle_url('/local/pactdigital/signup.php', ['wantsurl' => $view_url]))->out(false);
        $login_url  = (new moodle_url('/login/index.php', ['wantsurl' => $view_url]))->out(false);
        $courses_html .= '    <a href="' . $signup_url . '" class="pact-btn-start">S\'inscrire à cette formation</a>';
        $courses_html .= '    <a href="' . $login_url . '" style="display:block; text-align:center; font-size:0.78rem; color:#64748b; margin-top:6px; text-decoration:none;">Déjà un compte ? Se connecter</a>';
    } elseif ($is_enrolled_user) {
        // Déjà inscrit → page d'accueil du cours
        $welcome_url = (new moodle_url('/local/pactdigital/course_welcome.php', ['id' => $c->id]))->out(false);
        $courses_html .= '    <a href="' . $welcome_url . '" class="pact-btn-start">' . $btn_label . '</a>';
    } else {
        // Non inscrit → inscription directe en 1 clic (âge vérifié à la création du compte)
        $sesskey_val = sesskey();
        $courses_html .= '    <button class="pact-btn-start pact-btn-enrol"'
            . ' data-courseid="' . $c->id . '"'
            . ' data-coursename="' . $safe_fullname . '"'
            . ' data-sesskey="' . $sesskey_val . '"'
            . ' onclick="directEnrol(this)">S\'inscrire en 1 clic</button>';
    }

    $courses_html .= '  </div>';
    $courses_html .= '</div>';
}

$total_courses = count($courses_raw);
$total_domains = $DB->count_records('course_categories', ['depth' => 2]);

// ─── Render ───────────────────────────────────────────────────────────────────
echo $OUTPUT->header();
?>

<style>
/* ═══ CATALOGUE DES FORMATIONS — CONFORME CHARTE GRAPHIQUE V2.0 (MTDPCE) ═════ */

.pact-catalogue-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem 4rem;
    font-family: var(--police-texte, "Inter", system-ui, sans-serif);
    color: var(--texte, #211d1a);
}

/* ── Hero banner ─────────────────────────────────────────────── */
.pact-catalogue-hero {
    background: linear-gradient(135deg, var(--vert-900, #032e16) 0%, var(--vert-700, #045527) 50%, var(--vert-500, #00843b) 100%);
    border-radius: var(--rayon-lg, 12px);
    padding: 2.75rem 2.5rem;
    margin-bottom: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: var(--ombre-md);
}
.pact-catalogue-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 300px; height: 300px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.pact-catalogue-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; left: -60px;
    width: 400px; height: 400px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
}
.pact-catalogue-hero h1 {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 2.3rem;
    font-weight: 800;
    margin: 0 0 0.5rem;
    position: relative; z-index: 1;
    color: #ffffff;
}
.pact-catalogue-hero p {
    font-size: 1.05rem;
    opacity: 0.92;
    margin: 0 0 1.5rem;
    position: relative; z-index: 1;
}
.pact-hero-stats {
    display: flex;
    gap: 2rem;
    position: relative; z-index: 1;
    flex-wrap: wrap;
}
.pact-hero-stat {
    text-align: center;
}
.pact-hero-stat .val {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 2rem;
    font-weight: 800;
    color: var(--or-300, #ffed00);
    display: block;
}
.pact-hero-stat .lbl {
    font-size: 0.78rem;
    opacity: 0.88;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* ── Barre de filtres ────────────────────────────────────────── */
.pact-filters-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
    margin-bottom: 2rem;
}
.pact-filter-search {
    flex: 1;
    min-width: 200px;
    position: relative;
}
.pact-filter-search input {
    width: 100%;
    padding: 0.65rem 1rem 0.65rem 2.8rem;
    border: 1.5px solid var(--bordure-forte, #8e867f);
    border-radius: var(--rayon-md, 8px);
    font-size: 0.95rem;
    color: var(--texte, #211d1a);
    background: var(--surface, #ffffff);
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
}
.pact-filter-search input:focus {
    border-color: var(--vert-500, #00843b);
    box-shadow: 0 0 0 3px var(--vert-50, #ecf7f0);
}
.pact-filter-search::before {
    content: '🔍';
    position: absolute;
    left: 0.9rem; top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    pointer-events: none;
}
.pact-filter-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.pact-pill {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    padding: 0.5rem 1.2rem;
    border-radius: var(--rayon-md, 8px);
    border: 1.5px solid var(--bordure, #e2ddd6);
    background: var(--surface, #ffffff);
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    color: var(--texte-attenue, #635c55);
    white-space: nowrap;
}
.pact-pill:hover {
    border-color: var(--vert-500, #00843b);
    color: var(--vert-700, #045527);
    transform: translateY(-1px);
    box-shadow: var(--ombre-sm);
}
.pact-pill.active {
    background: var(--action, #00843b);
    border-color: var(--action, #00843b);
    color: white;
    box-shadow: var(--ombre-sm);
}
.pact-pill[data-domain="fondamentales"].active { background: #00843b; border-color: #00843b; }
.pact-pill[data-domain="manageriales"].active  { background: #2a6d9b; border-color: #2a6d9b; }
.pact-pill[data-domain="sectorielles"].active  { background: #b07800; border-color: #b07800; }
.pact-pill[data-domain="emergentes"].active    { background: #045527; border-color: #045527; }

/* ── Compteur de résultats ───────────────────────────────────── */
.pact-results-info {
    font-size: 0.9rem;
    color: var(--texte-attenue, #635c55);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.pact-results-count {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-weight: 700;
    color: var(--vert-700, #045527);
    font-size: 1rem;
}

/* ── Grille de cards ─────────────────────────────────────────── */
.pact-courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

/* ── Card ────────────────────────────────────────────────────── */
.pact-course-card {
    background: var(--surface, #ffffff);
    border: 1px solid var(--bordure, #e2ddd6);
    border-radius: var(--rayon-lg, 12px);
    overflow: hidden;
    box-shadow: var(--ombre-sm);
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    display: flex;
    flex-direction: column;
    position: relative;
}
.pact-course-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--ombre-lg);
    border-color: var(--vert-500, #00843b);
}
.pact-course-card.hidden {
    display: none !important;
}

/* Image section */
.pact-card-image {
    height: 160px;
    position: relative;
    background: linear-gradient(135deg, rgba(0,132,59,0.12), rgba(0,132,59,0.25));
    display: flex;
    align-items: center;
    justify-content: center;
}
.pact-card-emoji {
    font-size: 4rem;
    opacity: 0.7;
}
.pact-domain-chip {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    position: absolute;
    top: 10px; left: 10px;
    padding: 0.25rem 0.75rem;
    border-radius: var(--rayon-sm, 4px);
    font-size: 0.72rem;
    font-weight: 700;
    color: white;
    letter-spacing: 0.03em;
    backdrop-filter: blur(4px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.pact-level-badge {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    position: absolute;
    top: 10px; right: 10px;
    padding: 0.2rem 0.6rem;
    border-radius: var(--rayon-sm, 4px);
    font-size: 0.7rem;
    font-weight: 700;
}
.badge-bronze  { background: var(--ocre-700, #7d5500); color: white; }
.badge-argent  { background: var(--neutre-700, #4d4742); color: white; }
.badge-or      { background: var(--or-300, #ffed00); color: var(--neutre-900, #211d1a); }
.badge-diamant { background: var(--bleu-500, #2a6d9b); color: white; }
.badge-default { background: var(--surface-enfoncee, #f7f5f2); color: var(--texte-attenue, #635c55); }

.pact-level-pill {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    padding: 0.35rem 0.9rem;
    border-radius: var(--rayon-md, 8px);
    border: 1.5px solid var(--bordure, #e2ddd6);
    background: var(--surface, #ffffff);
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    color: var(--texte-attenue, #635c55);
    white-space: nowrap;
}
.pact-level-pill:hover {
    border-color: var(--vert-500, #00843b);
    color: var(--vert-700, #045527);
}
.pact-level-pill.active {
    background: var(--neutre-900, #211d1a);
    border-color: var(--neutre-900, #211d1a);
    color: white;
}

/* Card body */
.pact-card-body {
    padding: 1.2rem 1.2rem 1.4rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.pact-card-title {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 1rem;
    font-weight: 700;
    color: var(--texte, #211d1a);
    margin: 0 0 0.5rem;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.pact-card-desc {
    font-size: 0.82rem;
    color: var(--texte-attenue, #635c55);
    line-height: 1.5;
    margin: 0 0 0.8rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
}
.pact-card-meta {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-bottom: 0.8rem;
}
.pact-meta-item {
    font-size: 0.78rem;
    color: var(--texte-faible, #756d66);
}

/* Progress bar */
.pact-card-progress {
    height: 6px;
    background: var(--bordure, #e2ddd6);
    border-radius: 3px;
    margin-bottom: 0.8rem;
    position: relative;
    overflow: visible;
}
.pact-progress-bar {
    height: 100%;
    background: var(--vert-500, #00843b);
    border-radius: 3px;
    transition: width 0.6s ease;
}
.pact-progress-label {
    position: absolute;
    right: 0; top: -18px;
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--vert-700, #045527);
}

/* Bouton commencer */
.pact-btn-start {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    display: block;
    text-align: center;
    padding: 0.65rem 1rem;
    background: var(--action, #00843b);
    color: white !important;
    font-weight: 700;
    font-size: 0.9rem;
    border-radius: var(--rayon-md, 8px);
    text-decoration: none !important;
    transition: all 0.2s ease;
    margin-top: auto;
    border: none;
}
.pact-btn-start:hover {
    background: var(--action-survol, #026c31);
    transform: translateY(-1px);
    box-shadow: var(--ombre-md);
    color: white !important;
}

/* ── Empty state ─────────────────────────────────────────────── */
.pact-empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: #aaa;
    display: none;
}
.pact-empty-state .pact-empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}
.pact-empty-state h3 {
    color: #bbb;
    font-size: 1.2rem;
}

/* ── Responsive ──────────────────────────────────────────────── */
@media (max-width: 768px) {
    .pact-catalogue-hero h1 { font-size: 1.8rem; }
    .pact-courses-grid { grid-template-columns: 1fr 1fr; gap: 1rem; }
}
@media (max-width: 480px) {
    .pact-courses-grid { grid-template-columns: 1fr; }
    .pact-catalogue-hero { padding: 2rem 1.5rem; }
}
</style>

<div class="pact-catalogue-page">

    <!-- ── Hero banner ── -->
    <div class="pact-catalogue-hero">
        <h1>Catalogue des formations</h1>
        <p>Explorez les formations numériques du <strong>Programme PACTDIGITAL-BF</strong> — Ministère de la Transition Digitale, des Postes et des Communications Électroniques</p>
        <div class="pact-hero-stats">
            <div class="pact-hero-stat">
                <span class="val"><?= $total_courses ?></span>
                <span class="lbl">Formations</span>
            </div>
            <div class="pact-hero-stat">
                <span class="val"><?= $total_domains ?></span>
                <span class="lbl">Domaines</span>
            </div>
            <div class="pact-hero-stat">
                <span class="val"><?= count($root_cats) ?></span>
                <span class="lbl">Catégories</span>
            </div>
            <div class="pact-hero-stat">
                <span class="val">🆓</span>
                <span class="lbl">100% Gratuit</span>
            </div>
        </div>
    </div>

    <!-- ── Barre de filtres ── -->
    <div class="pact-filters-bar">
        <div class="pact-filter-search">
            <input type="text" id="pact-search" placeholder="Rechercher une formation (ex. protection, bureautique, IA…)…" autocomplete="off">
        </div>
        <div class="pact-filter-pills">
            <button class="pact-pill active" data-domain="all">Tous les domaines</button>
            <?php foreach ($root_cats as $cat):
                $meta = pact_get_domain_meta($cat->name);
            ?>
            <button class="pact-pill" data-domain="<?= $meta['slug'] ?>">
                <?= $meta['label'] ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="pact-filter-pills" style="width: 100%; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <span style="font-size: 0.84rem; font-weight: 700; color: #475569; margin-right: 4px;">Niveau :</span>
            <button class="pact-level-pill active" data-level="all">Tous</button>
            <button class="pact-level-pill" data-level="bronze">🥉 Bronze (Débutant)</button>
            <button class="pact-level-pill" data-level="argent">🥈 Argent (Intermédiaire)</button>
            <button class="pact-level-pill" data-level="or">🥇 Or (Avancé)</button>
        </div>
    </div>

    <!-- ── Résultats ── -->
    <div class="pact-results-info">
        <span id="pact-count" class="pact-results-count"><?= $total_courses ?></span> formation(s) trouvée(s)
    </div>

    <!-- ── Grille ── -->
    <div class="pact-courses-grid" id="pact-grid">
        <?= $courses_html ?>
        <div class="pact-empty-state" id="pact-empty">
            <div class="pact-empty-icon">🔍</div>
            <h3>Aucune formation trouvée</h3>
            <p>Essayez un autre filtre ou modifiez votre recherche.</p>
        </div>
    </div>

</div>

<script>
(function() {
    const pills      = document.querySelectorAll('.pact-pill');
    const levelPills = document.querySelectorAll('.pact-level-pill');
    const cards      = document.querySelectorAll('.pact-course-card');
    const search     = document.getElementById('pact-search');
    const counter    = document.getElementById('pact-count');
    const empty      = document.getElementById('pact-empty');
    let activeDomain = 'all';
    let activeLevel  = 'all';
    let searchQuery  = '';

    function applyFilters() {
        let visible = 0;
        cards.forEach(card => {
            const domain = card.dataset.domain;
            const level  = card.dataset.level;
            const title  = (card.dataset.title || '').toLowerCase();
            const matchDomain = (activeDomain === 'all' || domain === activeDomain);
            const matchLevel  = (activeLevel === 'all' || level === activeLevel);
            const matchSearch = !searchQuery || title.includes(searchQuery);
            if (matchDomain && matchLevel && matchSearch) {
                card.classList.remove('hidden');
                visible++;
            } else {
                card.classList.add('hidden');
            }
        });
        counter.textContent = visible;
        empty.style.display = visible === 0 ? 'block' : 'none';
    }

    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            pills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            activeDomain = pill.dataset.domain;
            applyFilters();
        });
    });

    levelPills.forEach(lpill => {
        lpill.addEventListener('click', () => {
            levelPills.forEach(p => p.classList.remove('active'));
            lpill.classList.add('active');
            activeLevel = lpill.dataset.level;
            applyFilters();
        });
    });

    search.addEventListener('input', () => {
        searchQuery = search.value.trim().toLowerCase();
        applyFilters();
    });
})();
</script>

<!-- ════════════════════════════════════════════════════════════════════ -->
<!--  MODAL D'INSCRIPTION EN 1 CLIC — AVEC CONTRÔLE D'ÂGE              -->
<!-- ════════════════════════════════════════════════════════════════════ -->
<style>
.enrol-modal-backdrop {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.55); backdrop-filter: blur(4px);
    z-index: 99999; display: none;
    align-items: center; justify-content: center; padding: 1rem;
}
.enrol-modal-backdrop.open { display: flex; }
.enrol-modal {
    background: white;
    border-radius: 18px;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    overflow: hidden;
    animation: enrolFadeIn 0.2s ease-out;
}
@keyframes enrolFadeIn {
    from { opacity: 0; transform: translateY(-16px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.enrol-modal-header {
    background: linear-gradient(135deg, var(--vert-800, #04411f) 0%, var(--vert-500, #00843b) 100%);
    color: white;
    padding: 1.3rem 1.6rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.enrol-modal-header h2 {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 1.05rem;
    font-weight: 800;
    margin: 0;
    color: white;
    display: flex;
    align-items: center;
    gap: 8px;
}
.enrol-modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 30px; height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.enrol-modal-close:hover { background: rgba(255,255,255,0.35); }
.enrol-modal-body { padding: 1.5rem; }
.enrol-course-name {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-weight: 700;
    font-size: 1rem;
    color: var(--vert-700, #045527);
    margin-bottom: 1rem;
    padding: 0.7rem 1rem;
    background: var(--vert-50, #ecf7f0);
    border-radius: var(--rayon-md, 8px);
    border-left: 4px solid var(--vert-500, #00843b);
}
.enrol-age-group {
    margin-bottom: 1.2rem;
}
.enrol-age-group label {
    display: block;
    font-size: 0.83rem;
    font-weight: 700;
    color: var(--texte, #211d1a);
    margin-bottom: 6px;
}
.enrol-age-group .help-text {
    font-size: 0.76rem;
    color: var(--texte-attenue, #635c55);
    margin-top: 4px;
}
.enrol-year-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid var(--bordure-forte, #8e867f);
    border-radius: var(--rayon-md, 8px);
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
    letter-spacing: 2px;
    transition: border-color 0.2s;
    box-sizing: border-box;
}
.enrol-year-input:focus {
    outline: none;
    border-color: var(--vert-500, #00843b);
    box-shadow: 0 0 0 3px var(--vert-50, #ecf7f0);
}
.enrol-error {
    background: var(--rouge-50, #fdecef);
    color: var(--rouge-700, #a2040e);
    padding: 0.75rem 1rem;
    border-radius: var(--rayon-md, 8px);
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1rem;
    display: none;
    align-items: center;
    gap: 8px;
}
.enrol-success {
    background: var(--vert-50, #ecf7f0);
    color: var(--vert-700, #045527);
    padding: 0.75rem 1rem;
    border-radius: var(--rayon-md, 8px);
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1rem;
    display: none;
    align-items: center;
    gap: 8px;
}
.enrol-modal-footer {
    padding: 1rem 1.5rem;
    background: var(--surface-enfoncee, #f7f5f2);
    border-top: 1px solid var(--bordure, #e2ddd6);
    display: flex;
    gap: 0.7rem;
    justify-content: flex-end;
}
.btn-enrol-confirm {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    background: var(--action, #00843b);
    color: white;
    border: none;
    padding: 0.7rem 1.6rem;
    border-radius: var(--rayon-md, 8px);
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}
.btn-enrol-confirm:hover:not(:disabled) {
    background: var(--action-survol, #026c31);
    transform: translateY(-1px);
    box-shadow: var(--ombre-md);
}
.btn-enrol-confirm:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-enrol-cancel {
    background: var(--surface, #ffffff);
    color: var(--texte-attenue, #635c55);
    border: 1px solid var(--bordure, #e2ddd6);
    padding: 0.7rem 1.2rem;
    border-radius: var(--rayon-md, 8px);
    font-size: 0.88rem;
    cursor: pointer;
}
.btn-enrol-cancel:hover { background: var(--surface-enfoncee, #f7f5f2); }
.pact-btn-enrol { cursor: pointer; font-family: inherit; }

/* Toast notification */
.pact-toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.pact-toast {
    background: var(--neutre-900, #211d1a);
    color: white;
    padding: 12px 18px;
    border-radius: var(--rayon-md, 8px);
    font-size: 0.9rem;
    font-weight: 600;
    box-shadow: var(--ombre-lg);
    border-left: 4px solid var(--vert-500, #00843b);
    max-width: 400px;
    animation: toastIn 0.25s ease-out;
}
.pact-toast.error { border-left-color: var(--rouge-500, #e30613); background: var(--rouge-700, #a2040e); }
.pact-toast.success { border-left-color: var(--vert-500, #00843b); background: var(--vert-800, #04411f); }
@keyframes toastIn {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}
</style>

<div class="pact-toast-container" id="toast-container"></div>

<script>
(function() {
    window.directEnrol = async function(btn) {
        if (!btn || btn.disabled) return;
        const courseId   = btn.dataset.courseid;
        const sesskey    = btn.dataset.sesskey;
        const courseName = btn.dataset.coursename;

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Inscription en cours…';

        try {
            const formData = new URLSearchParams({
                sesskey:    sesskey,
                courseid:   courseId
            });

            const resp = await fetch('<?= (new moodle_url('/local/pactdigital/enrol_ajax.php'))->out(false) ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            });

            const data = await resp.json();

            if (data.success) {
                btn.innerHTML = 'Inscrit !';
                btn.style.background = 'var(--vert-700, #045527)';
                showToast(data.message || 'Inscription réussie !', 'success');

                setTimeout(() => {
                    if (data.course_url) {
                        window.location.href = data.course_url;
                    } else {
                        window.location.reload();
                    }
                }, 600);
            } else {
                showToast(data.message || 'Une erreur est survenue lors de l\'inscription.', 'error');
                btn.disabled = false;
                btn.innerHTML = originalText;

                if (data.redirect) {
                    setTimeout(() => { window.location.href = data.redirect; }, 1500);
                }
            }
        } catch (err) {
            showToast('Erreur réseau. Veuillez réessayer.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    };

    function showToast(msg, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = 'pact-toast ' + type;
        toast.textContent = msg;
        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 400); }, 3500);
    }
})();
</script>

<?php
echo $OUTPUT->footer();
