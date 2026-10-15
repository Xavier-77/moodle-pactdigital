<?php
/**
 * PACTDIGITAL-BF — Génération de Fiche Mémo PDF par Séquence
 *
 * Génère et sert un PDF récapitulatif d'une séquence de cours.
 * Si une ressource PDF officielle existe dans la section, elle est servie directement.
 * Sinon, un PDF de résumé est généré dynamiquement (avec les titres et activités).
 *
 * URL : /local/pactdigital/sequence_memo.php?courseid=X&section=N
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/completionlib.php');

require_login();

if (isguestuser()) {
    redirect(new moodle_url('/login/index.php'));
}

$courseid   = required_param('courseid', PARAM_INT);
$section_id = required_param('section', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid, 'visible' => 1], '*', MUST_EXIST);
$context = context_course::instance($courseid);

if (!is_enrolled($context, $USER->id) && !has_capability('moodle/course:viewparticipants', $context)) {
    redirect(new moodle_url('/local/pactdigital/catalogue.php'), 'Accès refusé.', 3);
}

$PAGE->set_url(new moodle_url('/local/pactdigital/sequence_memo.php', ['courseid' => $courseid, 'section' => $section_id]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Fiche Mémo — ' . $course->fullname);

// ── Récupérer les infos de la section ─────────────────────────────────────────
$section = $DB->get_record('course_sections', ['course' => $courseid, 'section' => $section_id]);
if (!$section) {
    redirect(new moodle_url('/local/pactdigital/course_welcome.php', ['id' => $courseid]), 'Section introuvable.', 3);
}

$section_name = !empty($section->name) ? $section->name : ('Séquence ' . $section_id);

$modinfo  = get_fast_modinfo($course);
$sec_info = $modinfo->get_section_info($section_id);

// ── Chercher une ressource PDF dans la section ────────────────────────────────
$pdf_resource = null;
if (!empty($sec_info->sequence)) {
    $cmids = explode(',', $sec_info->sequence);
    foreach ($cmids as $cmid) {
        if (!isset($modinfo->cms[$cmid])) continue;
        $cm = $modinfo->cms[$cmid];
        if ($cm->modname !== 'resource') continue;
        
        // Chercher les fichiers PDF associés à cette ressource
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            context_module::instance($cm->id)->id,
            'mod_resource',
            'content',
            0,
            'sortorder DESC, id ASC',
            false
        );
        
        foreach ($files as $file) {
            if ($file->get_mimetype() === 'application/pdf') {
                // Vérifier si c'est une fiche mémo
                $name_lower = mb_strtolower($cm->name);
                if (strpos($name_lower, 'mémo') !== false || strpos($name_lower, 'memo') !== false
                    || strpos($name_lower, 'fiche') !== false || strpos($name_lower, 'résumé') !== false
                    || strpos($name_lower, 'pdf') !== false) {
                    $pdf_resource = ['cm' => $cm, 'file' => $file];
                    break 2;
                }
            }
        }
    }
}

// Si un PDF officiel existe, le rediriger directement
if ($pdf_resource) {
    $file_url = moodle_url::make_pluginfile_url(
        $pdf_resource['file']->get_contextid(),
        $pdf_resource['file']->get_component(),
        $pdf_resource['file']->get_filearea(),
        $pdf_resource['file']->get_itemid(),
        $pdf_resource['file']->get_filepath(),
        $pdf_resource['file']->get_filename()
    );
    redirect($file_url);
}

// ── Aucun PDF existant : générer une fiche mémo HTML → impression PDF ─────────
// Récupérer les activités de la section
$activities = [];
if (!empty($sec_info->sequence)) {
    $cmids = explode(',', $sec_info->sequence);
    foreach ($cmids as $cmid) {
        if (!isset($modinfo->cms[$cmid])) continue;
        $cm = $modinfo->cms[$cmid];
        if (!$cm->uservisible) continue;
        
        $mod_icon = match ($cm->modname) {
            'quiz'     => '📝',
            'assign'   => '✍️',
            'resource' => '📄',
            'url'      => '🔗',
            'page'     => '📃',
            'forum'    => '💬',
            'video'    => '🎬',
            'scorm'    => '📦',
            'lesson'   => '📖',
            'folder'   => '📁',
            default    => '🔹',
        };
        
        $activities[] = [
            'name'    => $cm->name,
            'icon'    => $mod_icon,
            'modname' => $cm->modname,
            'url'     => (new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]))->out(false),
        ];
    }
}

// Résumé de la section
$section_summary = !empty($section->summary)
    ? strip_tags(format_text($section->summary, $section->summaryformat))
    : '';

echo $OUTPUT->header();
?>

<style>
/* ═══ FICHE MÉMO — PACTDIGITAL-BF ═══════════════════════════════════════ */
.memo-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 1.5rem;
    font-family: 'Segoe UI', Arial, sans-serif;
}

/* ── En-tête fiche ──────────────────────────────────────────────────── */
.memo-header {
    background: linear-gradient(135deg, #0d7a32, #129b40);
    color: white;
    border-radius: 14px;
    padding: 2rem 2.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.memo-header::before {
    content: '';
    position: absolute;
    right: -30px; top: -30px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,0.07);
    border-radius: 50%;
}
.memo-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.35);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 0.8rem;
    letter-spacing: 0.04em;
}
.memo-header h1 {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 0.4rem;
    color: white;
    position: relative; z-index: 1;
}
.memo-header p {
    opacity: 0.85;
    margin: 0;
    font-size: 0.9rem;
    position: relative; z-index: 1;
}
.memo-header .logo-area {
    position: absolute;
    right: 2rem; top: 50%;
    transform: translateY(-50%);
    font-size: 4rem;
    opacity: 0.2;
}

/* ── Section résumé ─────────────────────────────────────────────────── */
.memo-section-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.2rem;
    box-shadow: 0 2px 10px rgba(18,155,64,0.06);
}
.memo-section-title {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #129b40;
    letter-spacing: 0.05em;
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    gap: 6px;
}
.memo-summary-text {
    font-size: 0.9rem;
    line-height: 1.7;
    color: #374151;
}

/* ── Activités ──────────────────────────────────────────────────────── */
.memo-activities-list { list-style: none; padding: 0; margin: 0; }
.memo-activity-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.7rem 0.9rem;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    background: #fafafa;
    transition: background 0.15s;
}
.memo-activity-item:hover { background: #f0faf5; }
.memo-activity-icon { font-size: 1.3rem; flex-shrink: 0; }
.memo-activity-name {
    flex: 1;
    font-size: 0.88rem;
    font-weight: 600;
    color: #1a1a1a;
}
.memo-activity-link {
    font-size: 0.8rem;
    color: #129b40;
    text-decoration: none;
    font-weight: 700;
    padding: 0.3rem 0.7rem;
    border: 1px solid #129b40;
    border-radius: 6px;
    white-space: nowrap;
}
.memo-activity-link:hover { background: #f0faf5; }

/* ── Notes personnelles ─────────────────────────────────────────────── */
.memo-notes {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 1.5rem;
    background: #f8fafc;
    margin-top: 1.2rem;
}
.memo-notes-title {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.05em;
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    gap: 6px;
}
.notes-lines {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}
.note-line {
    border-bottom: 1px solid #e2e8f0;
    height: 30px;
    width: 100%;
}

/* ── Footer fiche ───────────────────────────────────────────────────── */
.memo-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #e2e8f0;
    padding-top: 1rem;
    margin-top: 1.5rem;
    font-size: 0.78rem;
    color: #94a3b8;
    flex-wrap: wrap;
    gap: 0.5rem;
}

/* ── Boutons d'action ───────────────────────────────────────────────── */
.memo-actions {
    display: flex;
    gap: 0.8rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.btn-print {
    background: linear-gradient(135deg, #129b40, #0d7a32);
    color: white;
    padding: 0.65rem 1.4rem;
    border-radius: 10px;
    border: none;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-print:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(18,155,64,0.3);
    color: white;
}
.btn-back {
    background: white;
    color: #64748b;
    padding: 0.65rem 1.2rem;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-back:hover { background: #f1f5f9; color: #64748b; }

/* ── Print mode ─────────────────────────────────────────────────────── */
@media print {
    .memo-actions, .navbar, header, footer, #page-navbar { display: none !important; }
    .memo-page { max-width: 100%; padding: 0; }
    .memo-header { border-radius: 0; }
    body { background: white !important; }
    @page { margin: 1.5cm; }
}
</style>

<div class="memo-page" id="memo-content">

    <!-- ── Actions → Imprimer / Retour ── -->
    <div class="memo-actions">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimer / Sauvegarder en PDF</button>
        <a href="<?= (new moodle_url('/local/pactdigital/course_welcome.php', ['id' => $courseid]))->out(false) ?>" class="btn-back">
            ← Retour à la formation
        </a>
    </div>

    <!-- ── En-tête de la fiche ── -->
    <div class="memo-header">
        <div class="memo-badge">📄 Fiche Mémo — PACTDIGITAL-BF</div>
        <h1><?= htmlspecialchars($section_name) ?></h1>
        <p>📚 <?= htmlspecialchars($course->fullname) ?> &nbsp;·&nbsp; 🗓️ <?= date('d/m/Y') ?></p>
        <div class="logo-area">📖</div>
    </div>

    <!-- ── Objectifs / Résumé de la section ── -->
    <?php if ($section_summary): ?>
    <div class="memo-section-card">
        <div class="memo-section-title">🎯 Résumé & Objectifs</div>
        <div class="memo-summary-text"><?= $section_summary ?></div>
    </div>
    <?php endif; ?>

    <!-- ── Points clés à retenir ── -->
    <div class="memo-section-card">
        <div class="memo-section-title">✅ Points clés à retenir</div>
        <div style="display:flex; flex-direction:column; gap:0.5rem;">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div style="display:flex; align-items:flex-start; gap:0.7rem;">
                <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:12px;font-size:0.75rem;font-weight:700;flex-shrink:0;margin-top:2px;"><?= $i ?></span>
                <div style="border-bottom:1px solid #e2e8f0;flex:1;min-height:24px;"></div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- ── Activités de la séquence ── -->
    <?php if (!empty($activities)): ?>
    <div class="memo-section-card">
        <div class="memo-section-title">📋 Activités de la séquence</div>
        <ul class="memo-activities-list">
            <?php foreach ($activities as $act): ?>
            <li class="memo-activity-item">
                <span class="memo-activity-icon"><?= $act['icon'] ?></span>
                <span class="memo-activity-name"><?= htmlspecialchars($act['name']) ?></span>
                <a href="<?= $act['url'] ?>" class="memo-activity-link" target="_blank">Accéder ↗</a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- ── Vocabulaire clé ── -->
    <div class="memo-section-card">
        <div class="memo-section-title">📖 Vocabulaire clé</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.6rem;">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div style="padding:0.6rem 0.8rem; border:1px solid #e2e8f0; border-radius:8px; background:#f8fafc;">
                <div style="font-size:0.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:4px;">Terme <?= $i ?></div>
                <div style="border-bottom:1px solid #e2e8f0;height:20px;margin-bottom:4px;"></div>
                <div style="font-size:0.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:4px;">Définition</div>
                <div style="border-bottom:1px solid #e2e8f0;height:20px;"></div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- ── Notes personnelles ── -->
    <div class="memo-notes">
        <div class="memo-notes-title">✏️ Mes notes personnelles</div>
        <div class="notes-lines">
            <?php for ($i = 0; $i < 6; $i++): ?>
            <div class="note-line"></div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- ── Footer ── -->
    <div class="memo-footer">
        <span>🎓 PACTDIGITAL-BF — Ministère de la Transition Digitale, des Postes et des Communications Électroniques</span>
        <span>Imprimé le <?= date('d/m/Y à H:i') ?> par <?= htmlspecialchars(fullname($USER)) ?></span>
    </div>

</div>

<?php
echo $OUTPUT->footer();
