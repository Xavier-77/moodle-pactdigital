<?php
/**
 * PACTDIGITAL-BF — Page d'accueil du cours (Course Welcome Page)
 *
 * Affiche :
 * 1. Un accueil avec 2 boutons : "Commencer la formation" / "Passer directement l'examen"
 * 2. L'écran de rappel des règles avant l'examen (durée, nb questions, seuil, tentatives restantes)
 * 3. Gère la limitation des tentatives pour l'examen direct (max 2)
 * 4. Fiches mémo PDF téléchargeables par séquence
 *
 * URL : /local/pactdigital/course_welcome.php?id=COURSEID
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/gradelib.php');

require_login();

if (isguestuser()) {
    redirect(new moodle_url('/login/index.php'));
}

$courseid = required_param('id', PARAM_INT);

// ── Vérification cours ────────────────────────────────────────────────────────
$course = $DB->get_record('course', ['id' => $courseid, 'visible' => 1], '*', MUST_EXIST);
$context = context_course::instance($courseid);

// ── Vérifier si inscrit ───────────────────────────────────────────────────────
if (!is_enrolled($context, $USER->id)) {
    redirect(
        new moodle_url('/local/pactdigital/catalogue.php'),
        'Vous devez d\'abord vous inscrire à cette formation.',
        3
    );
}

$PAGE->set_url(new moodle_url('/local/pactdigital/course_welcome.php', ['id' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title('Accueil — ' . $course->fullname);
$PAGE->set_heading($course->fullname);

// ── Récupérer la progression de l'apprenant ───────────────────────────────────
$completion       = new completion_info($course);
$completion_enabled = $completion->is_enabled();
$progress_pct     = 0;
if ($completion_enabled) {
    $pct = \core_completion\progress::get_course_progress_percentage($course, $USER->id);
    if ($pct !== null) {
        $progress_pct = (int) round($pct);
    }
}
$is_completed = ($progress_pct >= 100);

// ── Récupérer l'examen final du cours (dernier quiz ou quiz avec "examen" dans le nom) ──
$sql_quiz = "SELECT q.id, q.name, q.intro, q.timeopen, q.timeclose,
                    q.timelimit, q.attempts AS maxattempts, q.grade AS maxgrade,
                    q.questionsperpage, cm.id AS cmid
             FROM {quiz} q
             JOIN {course_modules} cm ON cm.instance = q.id
             JOIN {modules} m ON m.id = cm.module AND m.name = 'quiz'
             WHERE q.course = :courseid AND cm.deletioninprogress = 0
             ORDER BY 
                 CASE WHEN LOWER(q.name) LIKE '%examen%' OR LOWER(q.name) LIKE '%final%' OR LOWER(q.name) LIKE '%certification%' THEN 0 ELSE 1 END ASC,
                 q.id DESC
             LIMIT 1";

$final_quiz = $DB->get_record_sql($sql_quiz, ['courseid' => $courseid]);

// ── Compter les tentatives à l'examen direct ──────────────────────────────────
$direct_attempts  = 0;
$direct_max       = 2; // Max 2 tentatives d'examen direct (sans suivre les séquences)
$last_attempt     = null;
$quiz_attempts    = [];

if ($final_quiz) {
    // Compter les tentatives finies
    $sql_attempts = "SELECT qa.id, qa.attempt, qa.sumgrades, qa.state, qa.timefinish,
                            qa.timestart
                     FROM {quiz_attempts} qa
                     WHERE qa.quiz = :quizid AND qa.userid = :userid
                     ORDER BY qa.attempt DESC";
    
    $quiz_attempts = $DB->get_records_sql($sql_attempts, [
        'quizid' => $final_quiz->id,
        'userid' => $USER->id
    ]);
    
    $finished_attempts = array_filter($quiz_attempts, fn($a) => $a->state === 'finished');
    $total_finished    = count($finished_attempts);
    
    if ($final_quiz->maxattempts > 0) {
        $attempts_remaining = max(0, $final_quiz->maxattempts - $total_finished);
    } else {
        $attempts_remaining = null; // illimité
    }
    
    // Tentatives d'examen direct (métadonnée préférence utilisateur)
    $direct_attempts = (int) get_user_preferences('pact_direct_exam_' . $final_quiz->id, 0);
    $direct_blocked  = ($direct_attempts >= $direct_max);
    
    // Compter les questions du quiz
    $sql_qcount = "SELECT COUNT(qs.id) FROM {quiz_slots} qs WHERE qs.quizid = :quizid";
    $nb_questions = (int) $DB->count_records_sql($sql_qcount, ['quizid' => $final_quiz->id]);
    
    // Dernier résultat
    if (!empty($finished_attempts)) {
        $last_attempt = reset($finished_attempts);
        $last_score_pct = ($final_quiz->maxgrade > 0)
            ? round(($last_attempt->sumgrades / $final_quiz->maxgrade) * 100, 1)
            : 0;
        $last_passed = ($last_score_pct >= 70);
    }
} else {
    $direct_blocked = false;
    $attempts_remaining = null;
    $nb_questions = 0;
}

// ── Récupérer les sections/séquences du cours ─────────────────────────────────
$modinfo  = get_fast_modinfo($course);
$sections = $modinfo->get_section_info_all();
$course_sections = [];
$section_idx = 0;

foreach ($sections as $sec) {
    if ($sec->section === 0) continue; // Ignorer section 0 (générales)
    
    $section_name = !empty($sec->name) ? $sec->name : ('Séquence ' . $sec->section);
    $has_pdf_memo = true; // Toujours disponible via le générateur de fiches mémo
    $pdf_memo_url = (new moodle_url('/local/pactdigital/sequence_memo.php', ['courseid' => $courseid, 'section' => $sec->section]))->out(false);
    
    // Chercher un PDF officiel dans la section (priorité sur le générateur)
    if (!empty($sec->sequence)) {
        $cmids = explode(',', $sec->sequence);
        foreach ($cmids as $cmid) {
            if (!isset($modinfo->cms[$cmid])) continue;
            $cm = $modinfo->cms[$cmid];
            if ($cm->modname !== 'resource') continue;
            $name_lower = mb_strtolower($cm->name);
            if (strpos($name_lower, 'mémo') !== false || strpos($name_lower, 'memo') !== false
                || strpos($name_lower, 'fiche') !== false || strpos($name_lower, 'pdf') !== false
                || strpos($name_lower, 'résumé') !== false) {
                // PDF officiel trouvé — garder l'URL du générateur qui le sert en priorité
                break;
            }
        }
    }
    
    // Vérifier la complétion de la section
    $section_completed = false;
    if ($completion_enabled && !empty($sec->sequence)) {
        $cmids = explode(',', $sec->sequence);
        $section_activities = 0;
        $section_done = 0;
        foreach ($cmids as $cmid) {
            if (isset($modinfo->cms[$cmid])) {
                $cm = $modinfo->cms[$cmid];
                if ($cm->completion > 0) {
                    $section_activities++;
                    $comp_data = $completion->get_data($cm, false, $USER->id);
                    if ($comp_data && $comp_data->completionstate >= COMPLETION_COMPLETE) {
                        $section_done++;
                    }
                }
            }
        }
        $section_completed = ($section_activities > 0 && $section_done >= $section_activities);
    }
    
    $section_idx++;
    $course_sections[] = [
        'num'              => $section_idx,
        'name'             => $section_name,
        'url'              => (new moodle_url('/course/view.php', ['id' => $courseid, 'section' => $sec->section]))->out(false),
        'completed'        => $section_completed,
        'has_pdf_memo'     => $has_pdf_memo,
        'pdf_memo_url'     => $pdf_memo_url,
    ];
}

$total_sections   = count($course_sections);
$completed_sections = count(array_filter($course_sections, fn($s) => $s['completed']));

// ── Timer format ──────────────────────────────────────────────────────────────
$timelimit_str = '–';
if ($final_quiz && $final_quiz->timelimit > 0) {
    $minutes = (int) floor($final_quiz->timelimit / 60);
    $seconds = $final_quiz->timelimit % 60;
    $timelimit_str = $minutes . ' min' . ($seconds > 0 ? ' ' . $seconds . ' sec' : '');
}

$quiz_url = $final_quiz
    ? (new moodle_url('/mod/quiz/view.php', ['id' => $final_quiz->cmid]))->out(false)
    : '';

$course_view_url = (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false);
$sesskey_val     = sesskey();

echo $OUTPUT->header();
?>

<style>
/* ═══ COURSE WELCOME PAGE — PACTDIGITAL-BF ════════════════════════════════ */
:root {
    --green: #129b40;
    --green-dark: #0d7a32;
    --yellow: #fad615;
    --red: #D32F2F;
    --blue: #1565C0;
    --grey: #64748b;
    --border: #e2e8f0;
    --shadow: 0 4px 20px rgba(18,155,64,0.10);
}

.course-welcome-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 1.5rem 4rem;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

/* ── Hero du cours ──────────────────────────────────────────────────────── */
.course-hero {
    background: linear-gradient(135deg, #0d7a32 0%, #129b40 60%, #1db954 100%);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
}
.course-hero::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 300px; height: 300px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.course-hero h1 {
    font-size: 1.7rem;
    font-weight: 800;
    margin: 0 0 0.5rem;
    position: relative; z-index: 1;
}
.course-hero p {
    opacity: 0.9;
    margin: 0;
    position: relative; z-index: 1;
}
.progress-hero {
    margin-top: 1.5rem;
    position: relative; z-index: 1;
}
.progress-hero-bar {
    width: 100%;
    height: 10px;
    background: rgba(255,255,255,0.25);
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 0.4rem;
}
.progress-hero-fill {
    height: 100%;
    background: #fad615;
    border-radius: 5px;
    transition: width 0.8s ease;
}
.progress-hero-label {
    font-size: 0.85rem;
    opacity: 0.9;
}

/* ── Deux boutons principaux ────────────────────────────────────────────── */
.main-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}
@media (max-width: 600px) { .main-actions { grid-template-columns: 1fr; } }

.action-card {
    background: white;
    border-radius: 18px;
    border: 2px solid var(--border);
    padding: 2rem;
    text-align: center;
    box-shadow: var(--shadow);
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.8rem;
    text-decoration: none;
    color: inherit;
}
.action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 35px rgba(18,155,64,0.18);
}
.action-card.primary {
    border-color: var(--green);
    background: linear-gradient(135deg, #f0faf5, #e8f7ee);
}
.action-card.primary:hover { border-color: var(--green-dark); }
.action-card.secondary { border-color: #dbeafe; background: linear-gradient(135deg, #f0f7ff, #e8f2ff); }
.action-card.secondary:hover { border-color: var(--blue); }
.action-card.blocked { opacity: 0.55; cursor: not-allowed; pointer-events: none; border-color: #fca5a5; background: #fef2f2; }

.action-icon { font-size: 3rem; }
.action-title { font-size: 1.15rem; font-weight: 800; color: #1a1a1a; }
.action-desc { font-size: 0.84rem; color: var(--grey); line-height: 1.4; }
.action-badge {
    padding: 0.35rem 0.9rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}
.badge-green { background: #dcfce7; color: #15803d; }
.badge-blue  { background: #dbeafe; color: #1d4ed8; }
.badge-red   { background: #fee2e2; color: #b91c1c; }
.badge-yellow{ background: #fef3c7; color: #b45309; }
.badge-grey  { background: #f1f5f9; color: #64748b; }

/* ── Modal Règles de l'examen ───────────────────────────────────────────── */
.exam-modal-backdrop {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.6); backdrop-filter: blur(5px);
    z-index: 99999; display: none;
    align-items: center; justify-content: center; padding: 1rem;
}
.exam-modal-backdrop.open { display: flex; }
.exam-modal {
    background: white;
    border-radius: 20px;
    max-width: 560px;
    width: 100%;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3);
    overflow: hidden;
    animation: slideIn 0.25s ease-out;
}
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.exam-modal-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
    padding: 1.5rem 1.8rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.exam-modal-header h2 {
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0;
    color: white;
    display: flex;
    align-items: center;
    gap: 8px;
}
.modal-close-btn {
    background: rgba(255,255,255,0.15);
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
.modal-close-btn:hover { background: rgba(255,255,255,0.3); }

.exam-modal-body { padding: 1.8rem; }

.rule-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.rule-box {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
}
.rule-icon { font-size: 1.8rem; margin-bottom: 0.3rem; }
.rule-label { font-size: 0.72rem; text-transform: uppercase; color: var(--grey); font-weight: 600; letter-spacing: 0.04em; }
.rule-value { font-size: 1.2rem; font-weight: 800; color: #1a1a1a; }

.exam-warning {
    background: #fef3c7;
    border: 1px solid #fcd34d;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 10px;
    align-items: flex-start;
    font-size: 0.85rem;
    color: #92400e;
}
.exam-warning .icon { font-size: 1.2rem; flex-shrink: 0; }

.exam-modal-footer {
    padding: 1.2rem 1.8rem;
    background: #f8fafc;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 0.8rem;
    justify-content: flex-end;
}
.btn-start-exam {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    color: white;
    padding: 0.75rem 1.8rem;
    border-radius: 10px;
    border: none;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-start-exam:hover {
    background: linear-gradient(135deg, #0f0f1a, #0d1428);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    color: white;
}
.btn-cancel {
    background: white;
    color: var(--grey);
    border: 1px solid var(--border);
    padding: 0.75rem 1.2rem;
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.88rem;
}
.btn-cancel:hover { background: #f1f5f9; }

/* ── Sections/Séquences ─────────────────────────────────────────────────── */
.sequences-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    margin-bottom: 1.5rem;
}
.sequences-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--green);
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 1.2rem;
}
.sequence-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.9rem;
    border-radius: 10px;
    border: 1px solid var(--border);
    margin-bottom: 0.6rem;
    transition: background 0.15s;
}
.sequence-item:hover { background: #f7faf8; }
.sequence-num {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    font-weight: 800;
    flex-shrink: 0;
}
.seq-done   { background: #dcfce7; color: #15803d; }
.seq-todo   { background: #f1f5f9; color: #64748b; }
.sequence-name {
    flex: 1;
    font-weight: 600;
    font-size: 0.92rem;
    color: #1a1a1a;
}
.seq-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}
.btn-seq {
    padding: 0.4rem 0.9rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s;
    white-space: nowrap;
}
.btn-seq-green  { background: var(--green); color: white; }
.btn-seq-green:hover { background: var(--green-dark); color: white; }
.btn-seq-outline{ background: white; color: var(--green); border: 1.5px solid var(--green); }
.btn-seq-outline:hover { background: #f0faf5; color: var(--green); }
.btn-seq-pdf    { background: #fef3c7; color: #b45309; border: 1.5px solid #fcd34d; }
.btn-seq-pdf:hover { background: #fde68a; color: #92400e; }

/* ── Résultat précédent ──────────────────────────────────────────────────── */
.prev-result {
    background: white;
    border-radius: 14px;
    border: 1px solid var(--border);
    padding: 1.3rem 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.prev-result-icon { font-size: 2rem; }
.prev-result-info { flex: 1; }
.prev-result-title { font-weight: 700; margin-bottom: 0.2rem; }
.prev-result-score { font-size: 0.85rem; color: var(--grey); }
</style>

<div class="course-welcome-page">

    <!-- ── Hero ── -->
    <div class="course-hero">
        <h1>📘 <?= htmlspecialchars($course->fullname) ?></h1>
        <p>
            <?php if ($is_completed): ?>
                🎉 Félicitations ! Vous avez terminé cette formation.
            <?php elseif ($progress_pct > 0): ?>
                Votre formation est en cours — continuez sur votre lancée !
            <?php else: ?>
                Bienvenue ! Vous êtes prêt à commencer cette formation.
            <?php endif; ?>
        </p>
        <div class="progress-hero">
            <div class="progress-hero-bar">
                <div class="progress-hero-fill" style="width: <?= $progress_pct ?>%"></div>
            </div>
            <div class="progress-hero-label">
                <?= $progress_pct ?>% terminé &nbsp;·&nbsp;
                <?= $completed_sections ?> / <?= $total_sections ?> séquence(s) validée(s)
            </div>
        </div>
    </div>

    <!-- ── Résultat du dernier examen ── -->
    <?php if ($last_attempt ?? false): ?>
    <div class="prev-result">
        <div class="prev-result-icon">
            <?= ($last_passed ?? false) ? '✅' : '❌' ?>
        </div>
        <div class="prev-result-info">
            <div class="prev-result-title">
                Dernier résultat d'examen : <strong><?= ($last_score_pct ?? 0) ?>%</strong>
                <?= ($last_passed ?? false) ? '— Réussi ✅' : '— Insuffisant (seuil : 70%)' ?>
            </div>
            <div class="prev-result-score">
                Score : <?= number_format($last_attempt->sumgrades ?? 0, 1) ?> / <?= number_format($final_quiz->maxgrade ?? 0, 1) ?>
                &nbsp;·&nbsp; Passé le : <?= $last_attempt->timefinish ? userdate($last_attempt->timefinish, '%d/%m/%Y à %H:%M') : '–' ?>
            </div>
        </div>
        <?php if (!($last_passed ?? false)): ?>
        <div>
            <span class="action-badge badge-yellow">💡 Révision recommandée avant la prochaine tentative</span>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Deux boutons principaux ── -->
    <div class="main-actions">

        <!-- Bouton 1 : Commencer / Continuer la formation -->
        <a href="<?= $course_view_url ?>" class="action-card primary">
            <div class="action-icon"><?= $is_completed ? '🔁' : ($progress_pct > 0 ? '▶️' : '🚀') ?></div>
            <div class="action-title">
                <?= $is_completed ? 'Revoir la formation' : ($progress_pct > 0 ? 'Continuer la formation' : 'Commencer la formation') ?>
            </div>
            <div class="action-desc">
                <?= $is_completed ? 'Revoyez les séquences de cours et révisez à votre rythme.' : 'Suivez les séquences dans l\'ordre et progressez étape par étape.' ?>
            </div>
            <span class="action-badge badge-green">
                📚 <?= $completed_sections ?>/<?= $total_sections ?> séquences
            </span>
        </a>

        <!-- Bouton 2 : Passer directement l'examen -->
        <?php if ($final_quiz): ?>
            <?php if ($direct_blocked): ?>
            <!-- Bloqué après 2 tentatives directes -->
            <div class="action-card blocked">
                <div class="action-icon">⛔</div>
                <div class="action-title">Examen direct bloqué</div>
                <div class="action-desc">
                    Vous avez utilisé vos <?= $direct_max ?> tentatives d'examen direct sans suivre les séquences.
                    Suivez d'abord la formation pour débloquer l'accès.
                </div>
                <span class="action-badge badge-red">⛔ Tentatives épuisées (<?= $direct_attempts ?>/<?= $direct_max ?>)</span>
            </div>
            <?php else: ?>
            <!-- Accès à l'examen direct -->
            <button class="action-card secondary"
                    onclick="openExamModal(true)"
                    style="border: none; font-family: inherit;">
                <div class="action-icon">⚡</div>
                <div class="action-title">Passer directement l'examen</div>
                <div class="action-desc">
                    Si vous connaissez déjà le contenu, passez directement l'examen de certification sans suivre les séquences.
                </div>
                <span class="action-badge badge-blue">
                    ⚡ <?= $direct_max - $direct_attempts ?> tentative(s) directe(s) restante(s)
                </span>
            </button>
            <?php endif; ?>
        <?php else: ?>
        <div class="action-card" style="opacity:0.5; cursor:not-allowed;">
            <div class="action-icon">📝</div>
            <div class="action-title">Aucun examen configuré</div>
            <div class="action-desc">Cette formation ne dispose pas encore d'examen final.</div>
            <span class="action-badge badge-grey">Non disponible</span>
        </div>
        <?php endif; ?>

    </div>

    <!-- ── Séquences du cours ── -->
    <?php if (!empty($course_sections)): ?>
    <div class="sequences-card">
        <div class="sequences-title">📋 Séquences de la formation</div>

        <?php foreach ($course_sections as $sec): ?>
        <div class="sequence-item">
            <div class="sequence-num <?= $sec['completed'] ? 'seq-done' : 'seq-todo' ?>">
                <?= $sec['completed'] ? '✓' : $sec['num'] ?>
            </div>
            <div class="sequence-name"><?= htmlspecialchars($sec['name']) ?></div>
            <div class="seq-actions">
                <?php if ($sec['has_pdf_memo']): ?>
                <a href="<?= $sec['pdf_memo_url'] ?>" class="btn-seq btn-seq-pdf" target="_blank" title="Télécharger la fiche mémo PDF">
                    📄 Fiche mémo PDF
                </a>
                <?php endif; ?>
                <a href="<?= $sec['url'] ?>" class="btn-seq <?= $sec['completed'] ? 'btn-seq-outline' : 'btn-seq-green' ?>">
                    <?= $sec['completed'] ? '🔁 Revoir' : '▶️ Commencer' ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Bouton "Nouvelle tentative après échec" -->
        <?php if (($last_attempt ?? false) && !($last_passed ?? false)): ?>
        <div style="margin-top: 1.2rem; padding: 1rem; background: #f0faf5; border-radius: 10px; border: 1px dashed var(--green);">
            <div style="font-weight:700; color:var(--green); margin-bottom:0.4rem;">💡 Voie 1 — Révision avant nouvelle tentative</div>
            <div style="font-size:0.85rem; color:var(--grey); margin-bottom:0.8rem;">
                Vous n'avez pas atteint le seuil de 70% à l'examen. Révisez les séquences ci-dessus, puis relancez l'examen via le bouton ci-dessous.
            </div>
            <button onclick="openExamModal(false)" class="btn-seq btn-seq-green" style="width:100%; justify-content:center; padding:0.65rem;">
                🔄 Nouvelle tentative à l'examen (après révision)
            </button>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<!-- ════════════════════════════════════════════════════════════════════ -->
<!--  MODAL RÈGLES DE L'EXAMEN                                           -->
<!-- ════════════════════════════════════════════════════════════════════ -->
<div class="exam-modal-backdrop" id="exam-modal-backdrop" onclick="if(event.target===this)closeExamModal()">
    <div class="exam-modal">
        <div class="exam-modal-header">
            <h2>📋 Règles de l'examen</h2>
            <button class="modal-close-btn" onclick="closeExamModal()">&times;</button>
        </div>
        <div class="exam-modal-body">

            <!-- Grille des règles -->
            <div class="rule-grid">
                <div class="rule-box">
                    <div class="rule-icon">⏱️</div>
                    <div class="rule-label">Durée</div>
                    <div class="rule-value"><?= htmlspecialchars($timelimit_str) ?></div>
                </div>
                <div class="rule-box">
                    <div class="rule-icon">❓</div>
                    <div class="rule-label">Nb de questions</div>
                    <div class="rule-value"><?= $nb_questions ?: '–' ?></div>
                </div>
                <div class="rule-box">
                    <div class="rule-icon">🎯</div>
                    <div class="rule-label">Seuil de réussite</div>
                    <div class="rule-value">70%</div>
                </div>
                <div class="rule-box" id="rule-attempts-box">
                    <div class="rule-icon">🔁</div>
                    <div class="rule-label">Tentatives restantes</div>
                    <div class="rule-value" id="rule-attempts-val">–</div>
                </div>
            </div>

            <!-- Avertissement connexion requise -->
            <div class="exam-warning">
                <span class="icon">⚠️</span>
                <div>
                    <strong>Connexion internet requise pendant tout l'examen.</strong>
                    Vos réponses sont sauvegardées automatiquement toutes les 30 secondes.
                    Une déconnexion prolongée pourrait entraîner la perte de votre session.
                </div>
            </div>

            <div id="direct-exam-warning" style="display:none;">
                <div class="exam-warning" style="background:#fee2e2; border-color:#fca5a5; color:#7f1d1d;">
                    <span class="icon">🔴</span>
                    <div>
                        <strong>Attention — Tentative directe</strong> : Vous n'avez pas encore suivi toutes les séquences.
                        Une tentative directe sera comptabilisée. Après <strong><?= $direct_max ?> tentatives directes</strong>, l'accès à l'examen direct sera bloqué.
                    </div>
                </div>
            </div>

        </div>
        <div class="exam-modal-footer">
            <button class="btn-cancel" onclick="closeExamModal()">← Annuler</button>
            <a id="btn-launch-exam" href="<?= htmlspecialchars($quiz_url) ?>" class="btn-start-exam">
                🚀 Démarrer l'examen maintenant
            </a>
        </div>
    </div>
</div>

<script>
const IS_DIRECT_EXAM_BLOCKED   = <?= $direct_blocked ? 'true' : 'false' ?>;
const DIRECT_ATTEMPTS_CURRENT  = <?= $direct_attempts ?>;
const DIRECT_ATTEMPTS_MAX      = <?= $direct_max ?>;
const ATTEMPTS_REMAINING       = <?= json_encode($attempts_remaining) ?>;
const SESSKEY                  = '<?= $sesskey_val ?>';
const COURSE_ID                = <?= $courseid ?>;
const QUIZ_URL                 = '<?= addslashes($quiz_url) ?>';
const ENROL_AJAX_URL           = '<?= (new moodle_url('/local/pactdigital/mark_direct_attempt.php'))->out(false) ?>';

let isDirectAttempt = false;

function openExamModal(direct) {
    isDirectAttempt = direct;
    
    // Mettre à jour les tentatives restantes
    let attVal = '–';
    if (ATTEMPTS_REMAINING === null) {
        attVal = '∞';
    } else if (ATTEMPTS_REMAINING === 0) {
        attVal = '⛔ 0';
    } else {
        attVal = ATTEMPTS_REMAINING.toString();
        if (direct) {
            // Tentatives directes restantes
            const directRemaining = DIRECT_ATTEMPTS_MAX - DIRECT_ATTEMPTS_CURRENT;
            attVal = directRemaining + ' (directe)';
        }
    }
    document.getElementById('rule-attempts-val').textContent = attVal;
    
    // Afficher/masquer l'avertissement tentative directe
    const directWarn = document.getElementById('direct-exam-warning');
    directWarn.style.display = (direct && DIRECT_ATTEMPTS_CURRENT < DIRECT_ATTEMPTS_MAX) ? 'block' : 'none';
    
    document.getElementById('exam-modal-backdrop').classList.add('open');
}
function closeExamModal() {
    document.getElementById('exam-modal-backdrop').classList.remove('open');
}

// Intercepter le lancement de l'examen pour comptabiliser les tentatives directes
document.getElementById('btn-launch-exam').addEventListener('click', function(e) {
    if (isDirectAttempt) {
        e.preventDefault();
        // Envoyer une requête pour marquer la tentative directe
        fetch('<?= (new moodle_url('/local/pactdigital/mark_direct_attempt.php'))->out(false) ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `sesskey=${SESSKEY}&courseid=${COURSE_ID}&quizid=<?= $final_quiz ? $final_quiz->id : 0 ?>`
        })
        .then(r => r.json())
        .then(data => {
            window.location.href = QUIZ_URL;
        })
        .catch(() => {
            window.location.href = QUIZ_URL;
        });
    }
});
</script>

<?php
echo $OUTPUT->footer();
