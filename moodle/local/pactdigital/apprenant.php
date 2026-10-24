<?php
/**
 * PACTDIGITAL-BF — Espace Apprenant
 *
 * Permet à l'apprenant de :
 * - Voir ses formations inscrites et sa progression
 * - Consulter ses notes et résultats (uniquement les siens)
 * - Accéder à ses badges et attestations
 * - Voir l'historique de ses tentatives aux examens
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/badgeslib.php');

require_login();

if (isguestuser()) {
    redirect(new moodle_url('/login/index.php'), get_string('noguestaccess', 'error'));
}

$PAGE->set_url(new moodle_url('/local/pactdigital/apprenant.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Mon Espace Apprenant — PACTDIGITAL-BF');
$PAGE->set_heading('Mon Espace Apprenant');

// ── Récupérer les cours auxquels l'apprenant est inscrit ─────────────────────
$sql_enrolled = "SELECT DISTINCT c.id, c.fullname, c.shortname, c.summary, c.category,
                        cat.name AS catname,
                        ue.timecreated AS enrol_date,
                        cc.timecompleted,
                        ula.timeaccess AS last_course_access
                 FROM {course} c
                 JOIN {enrol} e ON e.courseid = c.id
                 JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.userid = :userid
                 JOIN {course_categories} cat ON cat.id = c.category
                 LEFT JOIN {course_completions} cc ON cc.userid = :userid2 AND cc.course = c.id
                 LEFT JOIN {user_lastaccess} ula ON ula.userid = :userid3 AND ula.courseid = c.id
                 WHERE c.id > 1 AND c.visible = 1
                 ORDER BY ue.timecreated DESC";

$enrolled_courses = $DB->get_records_sql($sql_enrolled, [
    'userid'  => $USER->id,
    'userid2' => $USER->id,
    'userid3' => $USER->id,
]);

// ── Délivrance 100% automatique : attestation et badge dès réussite d'un cours ───
require_once(__DIR__ . '/lib.php');
foreach ($enrolled_courses as $enc) {
    pactdigital_auto_award_credentials($USER->id, $enc->id);
}

// ── Récupérer les résultats d'examen (quiz) pour cet apprenant uniquement ────
$sql_quiz_attempts = "SELECT qa.id, qa.quiz AS quizid, q.name AS quizname,
                             q.course AS courseid, c.fullname AS coursename, cat.name AS catname,
                             qa.attempt, qa.sumgrades, qa.state,
                             qa.timefinish, q.grade AS maxgrade, q.attempts AS maxattempts
                      FROM {quiz_attempts} qa
                      JOIN {quiz} q ON q.id = qa.quiz
                      JOIN {course} c ON c.id = q.course
                      JOIN {course_categories} cat ON cat.id = c.category
                      WHERE qa.userid = :userid AND qa.preview = 0
                      ORDER BY qa.timefinish DESC";

$quiz_attempts = $DB->get_records_sql($sql_quiz_attempts, ['userid' => $USER->id]);

// ── Récupérer les badges de l'apprenant ──────────────────────────────────────
$sql_badges = "SELECT bi.id, b.name, b.description, b.issuername,
                      bi.dateissued, b.courseid,
                      c.fullname AS coursename, cat.name AS catname
               FROM {badge_issued} bi
               JOIN {badge} b ON b.id = bi.badgeid
               LEFT JOIN {course} c ON c.id = b.courseid
               LEFT JOIN {course_categories} cat ON cat.id = c.category
               WHERE bi.userid = :userid AND b.status != 0
               ORDER BY bi.dateissued DESC";

$my_badges = $DB->get_records_sql($sql_badges, ['userid' => $USER->id]);
foreach ($my_badges as $b) {
    if (!empty($b->coursename) && in_array($b->coursename, ['Bronze', 'Argent', 'Or'])) {
        $b->coursename = ($b->catname ? $b->catname . ' — ' : '') . $b->coursename;
    }
}

// ── Récupérer les attestations délivrées (mod_customcert) ───────────────────
$sql_customcerts = "SELECT ci.id AS issueid, ci.code, ci.timecreated,
                           c.id AS courseid, c.fullname AS coursename, c.shortname, cat.name AS catname,
                           cm.id AS cmid
                    FROM {customcert_issues} ci
                    JOIN {customcert} cert ON cert.id = ci.customcertid
                    JOIN {course} c ON c.id = cert.course
                    JOIN {course_categories} cat ON cat.id = c.category
                    LEFT JOIN {modules} m ON m.name = 'customcert'
                    LEFT JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = cert.id
                    WHERE ci.userid = :userid
                    ORDER BY ci.timecreated DESC";
$issued_certs = $DB->get_records_sql($sql_customcerts, ['userid' => $USER->id]);

// Récupérer aussi les cours complétés au cas où l'émission customcert est en attente
$sql_certs = "SELECT cc.id, cc.course AS courseid, cc.timecompleted,
                     c.fullname AS coursename, c.shortname, cat.name AS catname
              FROM {course_completions} cc
              JOIN {course} c ON c.id = cc.course
              JOIN {course_categories} cat ON cat.id = c.category
              WHERE cc.userid = :userid AND cc.timecompleted IS NOT NULL
              ORDER BY cc.timecompleted DESC";
$my_completions = $DB->get_records_sql($sql_certs, ['userid' => $USER->id]);

// Combiner les attestations
$my_certs = [];
foreach ($issued_certs as $ic) {
    $cname = in_array($ic->coursename, ['Bronze', 'Argent', 'Or']) ? $ic->catname . ' — ' . $ic->coursename : $ic->coursename;
    $my_certs[$ic->courseid] = (object) [
        'id'            => $ic->issueid,
        'courseid'      => $ic->courseid,
        'coursename'    => $cname,
        'shortname'     => $ic->shortname,
        'timecompleted' => $ic->timecreated,
        'code'          => $ic->code,
        'cmid'          => $ic->cmid,
        'download_url'  => $ic->cmid ? (new moodle_url('/mod/customcert/view.php', ['id' => $ic->cmid, 'downloadown' => 1]))->out(false) : null,
        'verify_url'    => (new moodle_url('/local/pactdigital/verify.php', ['code' => $ic->code]))->out(false),
    ];
}
foreach ($my_completions as $mc) {
    if (!isset($my_certs[$mc->courseid])) {
        $cname = in_array($mc->coursename, ['Bronze', 'Argent', 'Or']) ? $mc->catname . ' — ' . $mc->coursename : $mc->coursename;
        $my_certs[$mc->courseid] = (object) [
            'id'            => $mc->id,
            'courseid'      => $mc->courseid,
            'coursename'    => $cname,
            'shortname'     => $mc->shortname,
            'timecompleted' => $mc->timecompleted,
            'code'          => null,
            'cmid'          => null,
            'download_url'  => (new moodle_url('/course/view.php', ['id' => $mc->courseid]))->out(false),
            'verify_url'    => null,
        ];
    }
}

// ── Statistiques globales de l'apprenant ─────────────────────────────────────
$total_enrolled     = count($enrolled_courses);
$total_completed    = count($my_certs);
$total_badges       = count($my_badges);
$total_in_progress  = max(0, $total_enrolled - $total_completed);

// ── Calcul de la progression par cours ───────────────────────────────────────
$courses_data = [];
foreach ($enrolled_courses as $c) {
    $course_obj = get_course($c->id);
    $completion = new completion_info($course_obj);
    $progress_pct = -1;
    if ($completion->is_enabled()) {
        $pct = \core_completion\progress::get_course_progress_percentage($course_obj, $USER->id);
        if ($pct !== null) {
            $progress_pct = (int) round($pct);
        }
    }
    if (!empty($c->timecompleted)) {
        $progress_pct = 100;
    }

    // Récupérer les résultats du dernier quiz de ce cours
    $last_quiz_result = null;
    foreach ($quiz_attempts as $qa) {
        if ($qa->courseid == $c->id && $qa->state === 'finished') {
            $score_pct = ($qa->maxgrade > 0) ? round(($qa->sumgrades / $qa->maxgrade) * 100, 1) : 0;
            $last_quiz_result = [
                'name'      => $qa->quizname,
                'score'     => $qa->sumgrades,
                'max'       => $qa->maxgrade,
                'score_pct' => $score_pct,
                'passed'    => ($score_pct >= 70),
                'date'      => $qa->timefinish ? userdate($qa->timefinish, '%d/%m/%Y') : '–',
                'attempts'  => (int) $qa->attempt,
            ];
            break;
        }
    }

    $courses_data[] = [
        'id'               => $c->id,
        'fullname'         => in_array($c->fullname, ['Bronze', 'Argent', 'Or']) ? $c->catname . ' — ' . $c->fullname : $c->fullname,
        'catname'          => $c->catname,
        'enrol_date'       => $c->enrol_date ? userdate($c->enrol_date, '%d/%m/%Y') : '–',
        'last_access'      => $c->last_course_access ? userdate($c->last_course_access, '%d/%m/%Y') : 'Jamais',
        'progress'         => max(0, $progress_pct),
        'completed'        => !empty($c->timecompleted) || ($progress_pct >= 100),
        'completion_date'  => !empty($c->timecompleted) ? userdate($c->timecompleted, '%d/%m/%Y') : null,
        'view_url'         => (new moodle_url('/course/view.php', ['id' => $c->id]))->out(false),
        'last_quiz'        => $last_quiz_result,
    ];
}

// ── Organiser les tentatives d'examens par quiz ───────────────────────────────
$quiz_results_grouped = [];
foreach ($quiz_attempts as $qa) {
    if ($qa->state !== 'finished') continue;
    $score_pct = ($qa->maxgrade > 0) ? round(($qa->sumgrades / $qa->maxgrade) * 100, 1) : 0;
    $quiz_results_grouped[$qa->quizid]['name']        = $qa->quizname;
    $quiz_results_grouped[$qa->quizid]['coursename']  = in_array($qa->coursename, ['Bronze', 'Argent', 'Or']) ? $qa->catname . ' — ' . $qa->coursename : $qa->coursename;
    $quiz_results_grouped[$qa->quizid]['courseid']    = $qa->courseid;
    $quiz_results_grouped[$qa->quizid]['maxattempts'] = $qa->maxattempts;
    $quiz_results_grouped[$qa->quizid]['attempts'][]  = [
        'num'       => $qa->attempt,
        'score'     => $qa->sumgrades,
        'max'       => $qa->maxgrade,
        'score_pct' => $score_pct,
        'passed'    => ($score_pct >= 70),
        'date'      => $qa->timefinish ? userdate($qa->timefinish, '%d/%m/%Y %H:%M') : '–',
    ];
}

echo $OUTPUT->header();
?>

<style>
/* ═══ ESPACE APPRENANT — CONFORME CHARTE GRAPHIQUE V2.0 (MTDPCE) ════════════ */
:root {
    --green:  var(--vert-500, #00843b);
    --green-dark: var(--vert-700, #045527);
    --yellow: var(--or-300, #ffed00);
    --red:    var(--rouge-500, #e30613);
    --blue:   var(--bleu-500, #2a6d9b);
    --grey:   var(--texte-attenue, #635c55);
    --border: var(--bordure, #e2ddd6);
    --white:  var(--surface, #ffffff);
    --shadow: var(--ombre-sm);
}

.pact-learner-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem 4rem;
    font-family: var(--police-texte, "Inter", system-ui, sans-serif);
    color: var(--texte, #211d1a);
}

/* ── Hero ──────────────────────────────────────────────────────────────── */
.learner-hero {
    background: linear-gradient(135deg, var(--vert-900, #032e16) 0%, var(--vert-700, #045527) 50%, var(--vert-500, #00843b) 100%);
    border-radius: var(--rayon-lg, 12px);
    padding: 2.5rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
    box-shadow: var(--ombre-md);
}
.learner-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 250px; height: 250px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.learner-hero h1 {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0 0 0.4rem;
    position: relative; z-index: 1;
    color: #ffffff;
}
.learner-hero p {
    opacity: 0.9;
    margin: 0;
    position: relative; z-index: 1;
}
.hero-stats {
    display: flex;
    gap: 1.5rem;
    position: relative; z-index: 1;
    flex-wrap: wrap;
}
.hero-stat {
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.22);
    border-radius: var(--rayon-md, 8px);
    padding: 0.8rem 1.2rem;
    text-align: center;
    min-width: 90px;
}
.hero-stat .val {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--or-300, #ffed00);
    display: block;
    line-height: 1;
}
.hero-stat .lbl {
    font-size: 0.75rem;
    opacity: 0.85;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

/* ── Section Tabs ──────────────────────────────────────────────────────── */
.learner-tabs {
    display: flex;
    gap: 0.5rem;
    border-bottom: 2px solid var(--border);
    margin-bottom: 2rem;
    overflow-x: auto;
}
.ltab-btn {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    background: transparent;
    border: none;
    padding: 0.85rem 1.2rem;
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--grey);
    cursor: pointer;
    border-radius: 8px 8px 0 0;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: all 0.2s;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    white-space: nowrap;
}
.ltab-btn:hover { color: var(--green-dark); background: var(--vert-50, #ecf7f0); }
.ltab-btn.active {
    color: var(--green-dark);
    border-bottom-color: var(--green);
    background: white;
    font-weight: 700;
}
.ltab-panel { display: none; }
.ltab-panel.active { display: block; animation: fadeIn 0.2s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

/* ── Cards génériques ───────────────────────────────────────────────────── */
.section-card {
    background: var(--white);
    border-radius: var(--rayon-lg, 12px);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    margin-bottom: 1.5rem;
}
.section-title {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 1rem;
    font-weight: 700;
    color: var(--green-dark);
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 1.2rem;
}

/* ── Course Cards ───────────────────────────────────────────────────────── */
.course-card {
    background: white;
    border-radius: var(--rayon-md, 8px);
    border: 1px solid var(--border);
    padding: 1.2rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 1.2rem;
    transition: box-shadow 0.2s, transform 0.2s;
    flex-wrap: wrap;
}
.course-card:hover {
    box-shadow: var(--ombre-md);
    transform: translateY(-2px);
    border-color: var(--green);
}
.course-icon {
    width: 52px;
    height: 52px;
    border-radius: var(--rayon-md, 8px);
    background: var(--vert-50, #ecf7f0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    flex-shrink: 0;
}
.course-info { flex: 1; min-width: 200px; }
.course-name {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-weight: 700;
    font-size: 0.96rem;
    color: var(--texte, #211d1a);
    margin-bottom: 0.2rem;
}
.course-cat {
    font-size: 0.78rem;
    color: var(--grey);
}
.course-progress-wrap {
    min-width: 160px;
    text-align: center;
}
.progress-track {
    width: 100%;
    height: 8px;
    background: var(--bordure, #e2ddd6);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.3rem;
}
.progress-fill {
    height: 100%;
    background: var(--vert-500, #00843b);
    border-radius: 4px;
    transition: width 0.6s ease;
}
.progress-pct {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--green-dark);
}
.badge-status {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-green  { background: var(--vert-50, #ecf7f0); color: var(--vert-700, #045527); }
.badge-blue   { background: var(--bleu-50, #eef4f9); color: var(--bleu-700, #173f5f); }
.badge-yellow { background: var(--ocre-50, #fdf4e3); color: var(--ocre-700, #7d5500); }
.badge-red    { background: var(--rouge-50, #fdecef); color: var(--rouge-700, #a2040e); }

.btn-course {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    padding: 0.5rem 1.1rem;
    border-radius: var(--rayon-md, 8px);
    font-size: 0.84rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--action, #00843b);
    color: white;
    transition: background 0.2s;
    white-space: nowrap;
}
.btn-course:hover {
    background: var(--action-survol, #026c31);
    color: white;
}
.btn-outline-course {
    background: white;
    color: var(--green-dark);
    border: 1.5px solid var(--green);
}
.btn-outline-course:hover {
    background: var(--vert-50, #ecf7f0);
    color: var(--green-dark);
}

/* ── Quiz Results Table ─────────────────────────────────────────────────── */
.results-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.86rem;
}
.results-table th {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    background: var(--vert-800, #04411f);
    color: white;
    padding: 10px 14px;
    text-align: left;
    font-weight: 600;
}
.results-table td {
    padding: 10px 14px;
    border-bottom: 1px solid var(--border);
    color: var(--texte, #211d1a);
    vertical-align: middle;
}
.results-table tr:hover td { background: var(--surface-basse, #fcfbf9); }
.score-bar {
    display: flex;
    align-items: center;
    gap: 8px;
}
.score-bar-track {
    flex: 1;
    height: 7px;
    background: var(--bordure, #e2ddd6);
    border-radius: 4px;
    overflow: hidden;
}
.score-bar-fill { height: 100%; border-radius: 4px; }
.score-bar-fill.pass { background: var(--green); }
.score-bar-fill.fail { background: var(--red); }

/* ── Badges Grid ─────────────────────────────────────────────────────────── */
.badges-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.2rem;
}
.badge-card {
    background: white;
    border-radius: var(--rayon-lg, 12px);
    border: 2px solid var(--or-500, #d4bc00);
    padding: 1.5rem;
    text-align: center;
    box-shadow: var(--shadow);
    transition: transform 0.2s, box-shadow 0.2s;
}
.badge-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--ombre-md);
}
.badge-icon { font-size: 3rem; margin-bottom: 0.6rem; }
.badge-name {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--texte, #211d1a);
    margin-bottom: 0.3rem;
}
.badge-course {
    font-size: 0.75rem;
    color: var(--grey);
    margin-bottom: 0.8rem;
}
.badge-date {
    font-size: 0.72rem;
    color: var(--grey);
    background: var(--surface-enfoncee, #f7f5f2);
    padding: 3px 10px;
    border-radius: 20px;
    display: inline-block;
    margin-bottom: 0.8rem;
}
.btn-share-badge {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 0.45rem 1rem;
    border-radius: var(--rayon-md, 8px);
    background: var(--action, #00843b);
    color: white;
    font-size: 0.78rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    width: 100%;
    justify-content: center;
}
.btn-share-badge:hover {
    background: var(--action-survol, #026c31);
    transform: translateY(-1px);
    box-shadow: var(--ombre-sm);
    color: white;
}

/* ── Empty State ─────────────────────────────────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--grey);
}
.empty-state .icon { font-size: 3rem; margin-bottom: 0.8rem; }
.empty-state h3 { font-size: 1.1rem; color: var(--texte-faible, #756d66); }

/* ── Share Modal ─────────────────────────────────────────────────────────── */
.share-modal-backdrop {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.55); backdrop-filter: blur(4px);
    z-index: 99999; display: none;
    align-items: center; justify-content: center; padding: 1rem;
}
.share-modal-backdrop.open { display: flex; }
.share-modal {
    background: white;
    border-radius: var(--rayon-lg, 12px);
    padding: 2rem;
    max-width: 480px;
    width: 100%;
    box-shadow: var(--ombre-lg);
    animation: fadeIn 0.2s ease-out;
}
.share-modal h3 {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--green-dark);
    margin: 0 0 0.5rem;
}
.share-preview {
    background: linear-gradient(135deg, var(--vert-800, #04411f), var(--vert-500, #00843b));
    border-radius: var(--rayon-lg, 12px);
    padding: 2rem;
    text-align: center;
    color: white;
    margin: 1.2rem 0;
}
.share-preview .big-icon { font-size: 4rem; margin-bottom: 0.5rem; }
.share-preview .badge-title { font-family: var(--police-titre, "Archivo", system-ui, sans-serif); font-size: 1.3rem; font-weight: 800; }
.share-preview .badge-info { font-size: 0.85rem; opacity: 0.85; }
.share-btns {
    display: flex;
    gap: 0.8rem;
    flex-wrap: wrap;
}
.btn-social {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    flex: 1;
    min-width: 120px;
    padding: 0.6rem 1rem;
    border-radius: var(--rayon-md, 8px);
    font-size: 0.85rem;
    font-weight: 700;
    text-align: center;
    cursor: pointer;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.15s;
    text-decoration: none;
}
.btn-linkedin { background: var(--bleu-500, #2a6d9b); color: white; }
.btn-linkedin:hover { background: var(--bleu-700, #173f5f); color: white; }
.btn-facebook { background: var(--bleu-700, #173f5f); color: white; }
.btn-facebook:hover { background: #0f2c44; color: white; }
.btn-copy { background: var(--neutre-900, #211d1a); color: white; }
.btn-copy:hover { background: var(--neutre-800, #38332f); color: white; }
.btn-close-modal {
    background: none;
    border: 1px solid var(--border);
    color: var(--grey);
    padding: 0.5rem 1.2rem;
    border-radius: var(--rayon-md, 8px);
    cursor: pointer;
    font-size: 0.85rem;
    margin-top: 1rem;
    width: 100%;
    transition: all 0.15s;
}
.btn-close-modal:hover { background: var(--surface-enfoncee, #f7f5f2); }

@media (max-width: 768px) {
    .learner-hero { flex-direction: column; }
    .hero-stats { justify-content: center; }
    .course-card { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="pact-learner-page">

    <!-- ── Hero ── -->
    <div class="learner-hero">
        <div>
            <h1>🎓 Mon Espace Apprenant</h1>
            <p>Bienvenue <strong><?= htmlspecialchars(fullname($USER)) ?></strong> — suivez vos formations, vos résultats et vos badges.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="val"><?= $total_enrolled ?></span>
                <span class="lbl">Inscrit(s)</span>
            </div>
            <div class="hero-stat">
                <span class="val"><?= $total_completed ?></span>
                <span class="lbl">Terminé(s)</span>
            </div>
            <div class="hero-stat">
                <span class="val"><?= $total_in_progress ?></span>
                <span class="lbl">En cours</span>
            </div>
            <div class="hero-stat">
                <span class="val"><?= $total_badges ?></span>
                <span class="lbl">Badges</span>
            </div>
        </div>
    </div>

    <!-- ── Navigation Tabs ── -->
    <div class="learner-tabs">
        <button class="ltab-btn active" onclick="switchLTab('tab-formations')">📚 Mes Formations (<?= $total_enrolled ?>)</button>
        <button class="ltab-btn" onclick="switchLTab('tab-resultats')">📊 Mes Résultats & Notes</button>
        <button class="ltab-btn" onclick="switchLTab('tab-badges')">🏆 Mes Badges (<?= $total_badges ?>)</button>
        <button class="ltab-btn" onclick="switchLTab('tab-catalogue')">🛒 S'inscrire à un cours</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!--  ONGLET 1 : MES FORMATIONS                                     -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="ltab-panel active" id="tab-formations">
        <div class="section-card">
            <div class="section-title">📚 Mes formations en cours</div>

            <?php if (empty($courses_data)): ?>
            <div class="empty-state">
                <div class="icon">📖</div>
                <h3>Vous n'êtes inscrit à aucune formation</h3>
                <p>Rendez-vous dans l'onglet "S'inscrire à un cours" pour découvrir les formations disponibles.</p>
                <button class="btn-course" style="margin: 1rem auto;" onclick="switchLTab('tab-catalogue')">
                    🛒 Voir le catalogue
                </button>
            </div>
            <?php else: ?>

            <?php foreach ($courses_data as $c): ?>
            <div class="course-card">
                <div class="course-icon">
                    <?= $c['completed'] ? '✅' : '📘' ?>
                </div>
                <div class="course-info">
                    <div class="course-name"><?= htmlspecialchars($c['fullname']) ?></div>
                    <div class="course-cat">📂 <?= htmlspecialchars($c['catname']) ?> &nbsp;·&nbsp; Inscrit le <?= $c['enrol_date'] ?></div>
                    <?php if ($c['last_quiz']): ?>
                    <div style="font-size:0.77rem; color:var(--grey); margin-top:4px;">
                        Dernier examen : <strong><?= $c['last_quiz']['score_pct'] ?>%</strong>
                        <?= $c['last_quiz']['passed'] ? '✅' : '❌' ?>
                        (<?= $c['last_quiz']['date'] ?>)
                    </div>
                    <?php endif; ?>
                </div>
                <div class="course-progress-wrap">
                    <div class="progress-track">
                        <div class="progress-fill" style="width: <?= $c['progress'] ?>%"></div>
                    </div>
                    <div class="progress-pct"><?= $c['progress'] ?>% complété</div>
                    <?php if ($c['completed']): ?>
                        <div class="badge-status badge-green" style="margin-top:5px;">✅ Terminé le <?= $c['completion_date'] ?></div>
                    <?php elseif ($c['progress'] > 0): ?>
                        <div class="badge-status badge-blue" style="margin-top:5px;">🔄 En cours</div>
                    <?php else: ?>
                        <div class="badge-status badge-yellow" style="margin-top:5px;">🆕 Non démarré</div>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="<?= $c['view_url'] ?>" class="btn-course">
                        <?= $c['completed'] ? '🔁 Revoir' : ($c['progress'] > 0 ? '▶️ Continuer' : '🚀 Commencer') ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!--  ONGLET 2 : MES RÉSULTATS                                      -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="ltab-panel" id="tab-resultats">
        <div class="section-card">
            <div class="section-title">📊 Mes résultats aux examens</div>

            <?php if (empty($quiz_results_grouped)): ?>
            <div class="empty-state">
                <div class="icon">📝</div>
                <h3>Aucun résultat d'examen enregistré</h3>
                <p>Passez vos premiers examens depuis vos formations inscrites.</p>
            </div>
            <?php else: ?>

            <?php foreach ($quiz_results_grouped as $qid => $qdata): ?>
            <div class="section-card" style="border-left: 4px solid var(--green); margin-bottom: 1.5rem;">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:1rem;">
                    <div>
                        <div style="font-weight:700; font-size:0.98rem;"><?= htmlspecialchars($qdata['name']) ?></div>
                        <div style="font-size:0.8rem; color:var(--grey);">📚 <?= htmlspecialchars($qdata['coursename']) ?></div>
                    </div>
                    <a href="<?= (new moodle_url('/course/view.php', ['id' => $qdata['courseid']]))->out(false) ?>" class="btn-outline-course btn-course" style="font-size:0.8rem; padding:0.4rem 0.9rem;">
                        Voir le cours ➔
                    </a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Tentative</th>
                                <th>Score</th>
                                <th>Réussite</th>
                                <th>Date</th>
                                <th>Résultat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($qdata['attempts'] as $att): ?>
                            <tr>
                                <td>N° <?= $att['num'] ?></td>
                                <td>
                                    <div class="score-bar">
                                        <div class="score-bar-track">
                                            <div class="score-bar-fill <?= $att['passed'] ? 'pass' : 'fail' ?>" style="width: <?= $att['score_pct'] ?>%"></div>
                                        </div>
                                        <strong><?= $att['score_pct'] ?>%</strong>
                                    </div>
                                </td>
                                <td><?= number_format($att['score'], 1) ?> / <?= number_format($att['max'], 1) ?></td>
                                <td><?= $att['date'] ?></td>
                                <td>
                                    <?php if ($att['passed']): ?>
                                        <span class="badge-status badge-green">✅ Réussi</span>
                                    <?php else: ?>
                                        <span class="badge-status badge-red">❌ Insuffisant</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php
                // Vérifier si la limite de tentatives est atteinte
                $attempt_count = count($qdata['attempts']);
                $max_att = $qdata['maxattempts'];
                if ($max_att > 0 && $attempt_count >= $max_att):
                ?>
                <div style="margin-top:0.8rem; padding: 0.7rem 1rem; background: #fee2e2; border-radius: 8px; color: var(--red); font-size:0.84rem; font-weight:600;">
                    ⛔ Nombre maximum de tentatives atteint (<?= $max_att ?> / <?= $max_att ?>)
                </div>
                <?php elseif ($attempt_count > 0 && !end($qdata['attempts'])['passed']): ?>
                <div style="margin-top:0.8rem; padding: 0.7rem 1rem; background: #fef3c7; border-radius: 8px; color: #b45309; font-size:0.84rem; font-weight:600;">
                    💡 Vous pouvez réviser le cours et repasser l'examen (tentative <?= $attempt_count + 1 ?><?= $max_att > 0 ? '/' . $max_att : '' ?>)
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <?php endif; ?>
        </div>

        <!-- ── Attestations ─────────────────────────────────────────── -->
        <div class="section-card">
            <div class="section-title">📜 Mes Attestations officielles de réussite (EF-CER-01 / EF-CER-03)</div>
            <?php if (!empty($my_certs)): ?>
            <p style="color:var(--grey); font-size:0.88rem; margin-top:-0.5rem; margin-bottom:1.2rem;">
                Toutes vos attestations délivrées par le MTDPCE sont disponibles ci-dessous avec leur numéro unique vérifiable publiquement.
            </p>
            <?php foreach ($my_certs as $cert): ?>
            <div class="course-card" style="border-left: 4px solid var(--yellow); padding: 1.2rem 1.4rem; margin-bottom: 1rem;">
                <div class="course-icon" style="background: linear-gradient(135deg, #fad61520, #fad61540); font-size: 2rem;">🏅</div>
                <div class="course-info" style="flex: 1;">
                    <div class="course-name" style="font-size: 1.05rem;"><?= htmlspecialchars($cert->coursename) ?></div>
                    <div class="course-cat" style="margin-top: 4px;">
                        🗓️ Délivrée le <?= userdate($cert->timecompleted, '%d/%m/%Y') ?>
                        <?php if (!empty($cert->code)): ?>
                            &nbsp;·&nbsp; 🔑 Code unique : <strong style="font-family:monospace; background:#e2e8f0; padding:2px 6px; border-radius:4px;"><?= htmlspecialchars($cert->code) ?></strong>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                    <?php if (!empty($cert->download_url) && !empty($cert->cmid)): ?>
                    <a href="<?= $cert->download_url ?>" target="_blank" class="btn-course" style="font-size:0.82rem; padding:0.45rem 0.9rem; background: linear-gradient(135deg, #129b40, #0d7a32); color:white; text-decoration:none;">
                        📥 Télécharger l'attestation PDF
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($cert->verify_url)): ?>
                    <a href="<?= $cert->verify_url ?>" target="_blank" class="btn-outline-course btn-course" style="font-size:0.82rem; padding:0.45rem 0.9rem; text-decoration:none; border-color: #129b40; color: #129b40;">
                        🛡️ Page de vérification publique
                    </a>
                    <?php endif; ?>

                    <a href="<?= (new moodle_url('/course/view.php', ['id' => $cert->courseid]))->out(false) ?>" class="btn-outline-course btn-course" style="font-size:0.82rem; padding:0.45rem 0.9rem; text-decoration:none;">
                        Revoir le cours
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="empty-state" style="padding: 2rem 1rem;">
                <div class="icon">📜</div>
                <h3 style="font-size: 1rem;">Aucune attestation obtenue pour l'instant</h3>
                <p>Vos attestations officielles délivrées par le MTDPCE avec QR Code apparaîtront ici dès que vous réussirez un cours.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!--  ONGLET 3 : MES BADGES                                         -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="ltab-panel" id="tab-badges">
        <div class="section-card">
            <div class="section-title">🏆 Mes badges numériques</div>

            <?php if (empty($my_badges)): ?>
            <div class="empty-state">
                <div class="icon">🏅</div>
                <h3>Aucun badge obtenu pour l'instant</h3>
                <p>Suivez et réussissez vos formations pour obtenir vos badges numériques certifiés.</p>
            </div>
            <?php else: ?>
            <div class="badges-grid">
                <?php foreach ($my_badges as $badge): ?>
                <div class="badge-card">
                    <div class="badge-icon">🎖️</div>
                    <div class="badge-name"><?= htmlspecialchars($badge->name) ?></div>
                    <?php if ($badge->coursename): ?>
                    <div class="badge-course">📚 <?= htmlspecialchars($badge->coursename) ?></div>
                    <?php endif; ?>
                    <div class="badge-date">🗓️ <?= userdate($badge->dateissued, '%d/%m/%Y') ?></div>
                    <button class="btn-share-badge"
                            onclick="openShareModal('<?= htmlspecialchars(addslashes($badge->name)) ?>', '<?= htmlspecialchars(addslashes($badge->coursename ?? 'PACTDIGITAL-BF')) ?>', '<?= userdate($badge->dateissued, '%d/%m/%Y') ?>')">
                        🔗 Partager ce badge
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!--  ONGLET 4 : CATALOGUE / AUTO-INSCRIPTION                       -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="ltab-panel" id="tab-catalogue">
        <div class="section-card" style="text-align:center; padding: 2.5rem;">
            <div style="font-size:3rem; margin-bottom:1rem;">📚</div>
            <h3 style="color:var(--green); font-size:1.3rem; margin-bottom:0.5rem;">Découvrir de nouvelles formations</h3>
            <p style="color:var(--grey); margin-bottom:1.5rem;">Accédez au catalogue complet et inscrivez-vous en un seul clic.</p>
            <a href="<?= (new moodle_url('/local/pactdigital/catalogue.php'))->out(false) ?>" class="btn-course" style="display:inline-flex; font-size:1rem; padding:0.8rem 2rem;">
                🚀 Voir le Catalogue des Formations
            </a>
        </div>
    </div>

</div>

<!-- ════════════════════════════════════════════════════════════════════ -->
<!--  MODAL PARTAGE DE BADGE                                             -->
<!-- ════════════════════════════════════════════════════════════════════ -->
<div class="share-modal-backdrop" id="share-modal-backdrop" onclick="if(event.target===this)closeShareModal()">
    <div class="share-modal">
        <h3>🔗 Partager mon badge</h3>
        <p style="color:var(--grey); font-size:0.88rem; margin-top:0;">Félicitations ! Partagez cette réussite sur vos réseaux.</p>

        <div class="share-preview" id="share-preview">
            <div class="big-icon">🎖️</div>
            <div class="badge-title" id="share-badge-name">–</div>
            <div class="badge-info" id="share-badge-course">–</div>
            <div class="badge-info" style="margin-top:4px;">🇧🇫 PACTDIGITAL-BF</div>
            <div class="badge-info" id="share-badge-date" style="opacity:0.7;">–</div>
        </div>

        <div style="font-size:0.82rem; color:var(--grey); margin-bottom:0.8rem;">Partager sur :</div>
        <div class="share-btns">
            <button class="btn-social btn-linkedin" id="btn-share-linkedin" onclick="shareLinkedIn()">
                💼 LinkedIn
            </button>
            <button class="btn-social btn-facebook" id="btn-share-facebook" onclick="shareFacebook()">
                📘 Facebook
            </button>
            <button class="btn-social btn-copy" onclick="copyShareLink()">
                📋 Copier le lien
            </button>
        </div>
        <button class="btn-close-modal" onclick="closeShareModal()">Fermer</button>
    </div>
</div>

<script>
// ── Navigation des onglets ────────────────────────────────────────────────────
function switchLTab(tabId) {
    document.querySelectorAll('.ltab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.ltab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    const btns = document.querySelectorAll('.ltab-btn');
    const tabMap = {
        'tab-formations': 0,
        'tab-resultats': 1,
        'tab-badges': 2,
        'tab-catalogue': 3
    };
    btns[tabMap[tabId]].classList.add('active');
}

// ── Modal Partage Badge ───────────────────────────────────────────────────────
let currentShareBadgeName = '';
let currentShareCourse    = '';

function openShareModal(badgeName, courseName, dateIssued) {
    currentShareBadgeName = badgeName;
    currentShareCourse    = courseName;
    document.getElementById('share-badge-name').textContent   = badgeName;
    document.getElementById('share-badge-course').textContent = '📚 ' + courseName;
    document.getElementById('share-badge-date').textContent   = '🗓️ Obtenu le ' + dateIssued;
    document.getElementById('share-modal-backdrop').classList.add('open');
}
function closeShareModal() {
    document.getElementById('share-modal-backdrop').classList.remove('open');
}

function getShareText() {
    return encodeURIComponent(
        `🎖️ Je viens d'obtenir le badge "${currentShareBadgeName}" pour la formation "${currentShareCourse}" sur la plateforme PACTDIGITAL-BF ! 🇧🇫 #FormationNumerique #PACTDIGITAL`
    );
}
function getShareUrl() {
    return encodeURIComponent(window.location.origin + '/local/pactdigital/catalogue.php');
}

function shareLinkedIn() {
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${getShareUrl()}&summary=${getShareText()}`, '_blank');
}
function shareFacebook() {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${getShareUrl()}&quote=${getShareText()}`, '_blank');
}
function copyShareLink() {
    const text = decodeURIComponent(getShareText());
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.querySelector('.btn-copy');
        btn.textContent = '✅ Copié !';
        setTimeout(() => { btn.textContent = '📋 Copier le lien'; }, 2000);
    });
}
</script>

<?php
echo $OUTPUT->footer();
