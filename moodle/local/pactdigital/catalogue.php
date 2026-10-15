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

// ── Mapping domaine → slug, couleur, emoji ───────────────────────────────────
function pact_get_domain_meta($catname) {
    $map = [
        'fondamentales' => ['slug' => 'fondamentales', 'color' => '#129b40', 'light' => '#e8f7ee', 'emoji' => '🌱', 'label' => 'Fondamentales'],
        'managériales'  => ['slug' => 'manageriales',  'color' => '#1a6faf', 'light' => '#e3eef8', 'emoji' => '📊', 'label' => 'Managériales'],
        'manageri'      => ['slug' => 'manageriales',  'color' => '#1a6faf', 'light' => '#e3eef8', 'emoji' => '📊', 'label' => 'Managériales'],
        'sectorielles'  => ['slug' => 'sectorielles',  'color' => '#e67e22', 'light' => '#fef3e8', 'emoji' => '🏛️', 'label' => 'Sectorielles'],
        'émergentes'    => ['slug' => 'emergentes',    'color' => '#8e44ad', 'light' => '#f4eafb', 'emoji' => '🚀', 'label' => 'Émergentes'],
        'emergentes'    => ['slug' => 'emergentes',    'color' => '#8e44ad', 'light' => '#f4eafb', 'emoji' => '🚀', 'label' => 'Émergentes'],
    ];
    $name_lower = mb_strtolower($catname);
    foreach ($map as $keyword => $meta) {
        if (mb_strpos($name_lower, $keyword) !== false) {
            return $meta;
        }
    }
    return ['slug' => 'autres', 'color' => '#555', 'light' => '#f5f5f5', 'emoji' => '📚', 'label' => 'Autres'];
}

// ── Niveau badge selon shortname ─────────────────────────────────────────────
function pact_get_level_badge($shortname) {
    $s = strtolower($shortname);
    if (strpos($s, 'or') !== false || strpos($s, 'gold') !== false || strpos($s, 'diaman') !== false) {
        return ['label' => '💎 Diamant',  'cls' => 'badge-diamant'];
    }
    if (strpos($s, 'argent') !== false || strpos($s, 'silver') !== false) {
        return ['label' => '🥈 Argent', 'cls' => 'badge-argent'];
    }
    if (strpos($s, 'bronze') !== false) {
        return ['label' => '🥉 Bronze',  'cls' => 'badge-bronze'];
    }
    // Fallback: chercher dans le nom
    return ['label' => '🎓 Formation', 'cls' => 'badge-default'];
}

// ── Charger tous les cours visibles (hors site) ──────────────────────────────
$sql = "SELECT c.id, c.fullname, c.shortname, c.summary, c.categoryid,
               c.visible, c.timecreated,
               cat.name AS catname, cat.parent AS catparent,
               cat2.name AS rootcatname
        FROM {course} c
        JOIN {course_categories} cat ON cat.id = c.categoryid
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
    
    // Progression si connecté
    $progress_pct = -1;
    if (isloggedin() && !isguestuser()) {
        $completion = new completion_info($course_obj);
        if ($completion->is_enabled()) {
            $progress = \core_completion\progress::get_course_progress_percentage($course_obj, $USER->id);
            if ($progress !== null) {
                $progress_pct = (int) round($progress);
            }
        }
    }
    
    $safe_fullname = htmlspecialchars($c->fullname, ENT_QUOTES, 'UTF-8');
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

    $courses_html .= '<div class="pact-course-card" data-domain="' . $domain_meta['slug'] . '" data-title="' . $safe_fullname . '">';
    $courses_html .= '  <div class="pact-card-image" style="' . $card_style . '">';
    if (!$img_url) {
        $courses_html .= '    <div class="pact-card-emoji">' . $domain_meta['emoji'] . '</div>';
    }
    $courses_html .= '    <span class="pact-domain-chip" style="background:' . $domain_meta['color'] . '">'
                       . $domain_meta['emoji'] . ' ' . $domain_meta['label'] . '</span>';
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
    $courses_html .= '    <a href="' . $view_url . '" class="pact-btn-start">' . $btn_icon . ' ' . $btn_label . '</a>';
    $courses_html .= '  </div>';
    $courses_html .= '</div>';
}

$total_courses = count($courses_raw);

// ─── Render ───────────────────────────────────────────────────────────────────
echo $OUTPUT->header();
?>

<style>
/* ═══ CATALOGUE NETFLIX DU SAVOIR — PACTDIGITAL-BF ═══════════════════════ */

.pact-catalogue-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem 4rem;
    font-family: 'Segoe UI', 'Plus Jakarta Sans', Arial, sans-serif;
}

/* ── Hero banner ─────────────────────────────────────────────── */
.pact-catalogue-hero {
    background: linear-gradient(135deg, #0d7a32 0%, #129b40 50%, #1db954 100%);
    border-radius: 20px;
    padding: 3rem 2.5rem;
    margin-bottom: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
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
    font-size: 2.4rem;
    font-weight: 800;
    margin: 0 0 0.5rem;
    position: relative; z-index: 1;
}
.pact-catalogue-hero p {
    font-size: 1.1rem;
    opacity: 0.9;
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
    font-size: 2rem;
    font-weight: 800;
    color: #fad615;
    display: block;
}
.pact-hero-stat .lbl {
    font-size: 0.8rem;
    opacity: 0.85;
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
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    font-size: 0.95rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
}
.pact-filter-search input:focus {
    border-color: #129b40;
    box-shadow: 0 0 0 3px rgba(18,155,64,0.15);
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
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    border: 2px solid #e0e0e0;
    background: white;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #555;
    white-space: nowrap;
}
.pact-pill:hover {
    border-color: #129b40;
    color: #129b40;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(18,155,64,0.15);
}
.pact-pill.active {
    background: #129b40;
    border-color: #129b40;
    color: white;
    box-shadow: 0 4px 12px rgba(18,155,64,0.3);
}
.pact-pill[data-domain="fondamentales"].active { background: #129b40; border-color: #129b40; }
.pact-pill[data-domain="manageriales"].active  { background: #1a6faf; border-color: #1a6faf; }
.pact-pill[data-domain="sectorielles"].active  { background: #e67e22; border-color: #e67e22; }
.pact-pill[data-domain="emergentes"].active    { background: #8e44ad; border-color: #8e44ad; }

/* ── Compteur de résultats ───────────────────────────────────── */
.pact-results-info {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.pact-results-count {
    font-weight: 700;
    color: #129b40;
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
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    display: flex;
    flex-direction: column;
    position: relative;
}
.pact-course-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}
.pact-course-card.hidden {
    display: none !important;
}

/* Image section */
.pact-card-image {
    height: 160px;
    position: relative;
    background: linear-gradient(135deg, #129b4022, #129b4044);
    display: flex;
    align-items: center;
    justify-content: center;
}
.pact-card-emoji {
    font-size: 4rem;
    opacity: 0.7;
}
.pact-domain-chip {
    position: absolute;
    top: 10px; left: 10px;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.72rem;
    font-weight: 700;
    color: white;
    letter-spacing: 0.03em;
    backdrop-filter: blur(4px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.pact-level-badge {
    position: absolute;
    top: 10px; right: 10px;
    padding: 0.2rem 0.6rem;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 700;
}
.badge-bronze  { background: #cd7f32; color: white; }
.badge-argent  { background: #aaa; color: white; }
.badge-diamant { background: linear-gradient(135deg, #b9f2ff, #89cff0); color: #003; }
.badge-default { background: #eee; color: #555; }

/* Card body */
.pact-card-body {
    padding: 1.2rem 1.2rem 1.4rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.pact-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 0.5rem;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.pact-card-desc {
    font-size: 0.82rem;
    color: #777;
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
    color: #888;
}

/* Progress bar */
.pact-card-progress {
    height: 6px;
    background: #eee;
    border-radius: 3px;
    margin-bottom: 0.8rem;
    position: relative;
    overflow: visible;
}
.pact-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #129b40, #1db954);
    border-radius: 3px;
    transition: width 0.6s ease;
}
.pact-progress-label {
    position: absolute;
    right: 0; top: -18px;
    font-size: 0.72rem;
    font-weight: 700;
    color: #129b40;
}

/* Bouton commencer */
.pact-btn-start {
    display: block;
    text-align: center;
    padding: 0.65rem 1rem;
    background: linear-gradient(135deg, #129b40, #0d7a32);
    color: white !important;
    font-weight: 700;
    font-size: 0.9rem;
    border-radius: 10px;
    text-decoration: none !important;
    transition: all 0.2s ease;
    margin-top: auto;
}
.pact-btn-start:hover {
    background: linear-gradient(135deg, #0d7a32, #075a24);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(18,155,64,0.4);
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
        <h1>📚 Catalogue des formations</h1>
        <p>Explorez les formations numériques du <strong>Programme PACTDIGITAL-BF</strong> — Ministère de la Transition Digitale, des Postes et des Communications Électroniques</p>
        <div class="pact-hero-stats">
            <div class="pact-hero-stat">
                <span class="val"><?= $total_courses ?></span>
                <span class="lbl">Formations</span>
            </div>
            <div class="pact-hero-stat">
                <span class="val"><?= count($root_cats) ?></span>
                <span class="lbl">Domaines</span>
            </div>
            <div class="pact-hero-stat">
                <span class="val">100%</span>
                <span class="lbl">En ligne</span>
            </div>
            <div class="pact-hero-stat">
                <span class="val">🆓</span>
                <span class="lbl">Gratuit</span>
            </div>
        </div>
    </div>

    <!-- ── Barre de filtres ── -->
    <div class="pact-filters-bar">
        <div class="pact-filter-search">
            <input type="text" id="pact-search" placeholder="Rechercher une formation…" autocomplete="off">
        </div>
        <div class="pact-filter-pills">
            <button class="pact-pill active" data-domain="all">🌐 Tous</button>
            <?php foreach ($root_cats as $cat):
                $meta = pact_get_domain_meta($cat->name);
            ?>
            <button class="pact-pill" data-domain="<?= $meta['slug'] ?>">
                <?= $meta['emoji'] ?> <?= $meta['label'] ?>
            </button>
            <?php endforeach; ?>
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
    const pills   = document.querySelectorAll('.pact-pill');
    const cards   = document.querySelectorAll('.pact-course-card');
    const search  = document.getElementById('pact-search');
    const counter = document.getElementById('pact-count');
    const empty   = document.getElementById('pact-empty');
    let activeDomain = 'all';
    let searchQuery  = '';

    function applyFilters() {
        let visible = 0;
        cards.forEach(card => {
            const domain = card.dataset.domain;
            const title  = (card.dataset.title || '').toLowerCase();
            const matchDomain = (activeDomain === 'all' || domain === activeDomain);
            const matchSearch = !searchQuery || title.includes(searchQuery);
            if (matchDomain && matchSearch) {
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

    search.addEventListener('input', () => {
        searchQuery = search.value.trim().toLowerCase();
        applyFilters();
    });
})();
</script>

<?php
echo $OUTPUT->footer();
