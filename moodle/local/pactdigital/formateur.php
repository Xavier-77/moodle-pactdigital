<?php
/**
 * PACTDIGITAL-BF — Espace Formateur & Cockpit de Suivi Pédagogique
 * 
 * Permet au formateur de :
 * - Suivre la progression et l'avancement de sa cohorte (alertes décrochage)
 * - Consulter et corriger les devoirs en attente d'évaluation
 * - Analyser les résultats des quiz
 * - Envoyer des relances ciblées aux apprenants inactifs
 * - Suivre les attestations et certifications obtenues
 * 
 * NOTE IMPORTANTE : Cet espace est strictement dédié à l'accompagnement,
 * à la notation et à la consultation. L'édition/modification du contenu
 * du cours est désactivée ici.
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/completionlib.php');

require_login();

$courseid = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);

$PAGE->set_url(new moodle_url('/local/pactdigital/formateur.php', $courseid ? ['id' => $courseid] : []));
$PAGE->set_pagelayout('incourse');

// ── Récupérer la liste des cours où l'utilisateur a un rôle d'encadrant ─────
$is_admin = is_siteadmin();

if ($is_admin) {
    // L'administrateur a accès à tous les cours
    $sql_courses = "SELECT c.id, c.fullname, c.shortname, c.summary, c.category, cat.name AS catname
                    FROM {course} c
                    JOIN {course_categories} cat ON cat.id = c.category
                    WHERE c.id > 1 AND c.visible = 1
                    ORDER BY c.fullname ASC";
    $my_courses = $DB->get_records_sql($sql_courses);
} else {
    // Cours où l'utilisateur a un rôle d'enseignant / formateur / non-editing teacher
    $sql_courses = "SELECT DISTINCT c.id, c.fullname, c.shortname, c.summary, c.category, cat.name AS catname
                    FROM {course} c
                    JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = " . CONTEXT_COURSE . "
                    JOIN {role_assignments} ra ON ra.contextid = ctx.id
                    JOIN {role} r ON r.id = ra.roleid
                    JOIN {course_categories} cat ON cat.id = c.category
                    WHERE ra.userid = :userid 
                      AND c.id > 1
                      AND r.shortname IN ('editingteacher', 'teacher', 'coursecreator', 'manager')
                    ORDER BY c.fullname ASC";
    $my_courses = $DB->get_records_sql($sql_courses, ['userid' => $USER->id]);
}

// Si aucun cours spécifié mais qu'il n'y a qu'un seul cours assigné, on le sélectionne directement
if ($courseid === 0 && count($my_courses) === 1) {
    $first_course = reset($my_courses);
    $courseid = $first_course->id;
}

// ── Traitement d'un envoi de relance (Message / Notification) ───────────────
$notification_msg = '';
$notification_type = 'success';

if ($action === 'send_reminder' && $courseid > 0 && confirm_sesskey()) {
    $selected_users = optional_param_array('userids', [], PARAM_INT);
    $subject        = clean_text(required_param('subject', PARAM_TEXT));
    $messagebody    = clean_text(required_param('messagebody', PARAM_RAW));

    if (empty($selected_users)) {
        $notification_msg = "Veuillez sélectionner au moins un apprenant à relancer.";
        $notification_type = 'error';
    } elseif (empty($subject) || empty($messagebody)) {
        $notification_msg = "L'objet et le message ne peuvent pas être vides.";
        $notification_type = 'error';
    } else {
        $sent_count = 0;
        foreach ($selected_users as $uid) {
            $target_user = $DB->get_record('user', ['id' => $uid, 'deleted' => 0]);
            if ($target_user) {
                // Création et envoi de la notification / message Moodle
                $eventdata = new \core\message\message();
                $eventdata->courseid          = $courseid;
                $eventdata->component         = 'moodle';
                $eventdata->name              = 'instantmessage';
                $eventdata->userfrom          = $USER;
                $eventdata->userto            = $target_user;
                $eventdata->subject           = $subject;
                $eventdata->fullmessage       = $messagebody;
                $eventdata->fullmessageformat = FORMAT_MARKDOWN;
                $eventdata->fullmessagehtml   = format_text($messagebody, FORMAT_MARKDOWN);
                $eventdata->smallmessage      = $subject;
                $eventdata->notification      = 1;
                
                try {
                    message_send($eventdata);
                    $sent_count++;
                } catch (\Exception $e) {
                    // Erreur silencieuse d'envoi individuel
                }
            }
        }
        $notification_msg = "✅ Relance envoyée avec succès à {$sent_count} apprenant(s).";
        $notification_type = 'success';
    }
}

// ── Si un cours précis est sélectionné, préparer les données du Cockpit ────
$course = null;
$learners_data = [];
$assignments_data = [];
$quizzes_data = [];
$kpis = [
    'total_learners'     => 0,
    'completed_learners' => 0,
    'inactive_learners'  => 0,
    'pending_grades'     => 0,
    'avg_progress'       => 0,
];

if ($courseid > 0) {
    $course = $DB->get_record('course', ['id' => $courseid, 'visible' => 1]);
    if (!$course) {
        redirect(new moodle_url('/local/pactdigital/formateur.php'), 'Cours introuvable.', 3);
    }
    
    $context = context_course::instance($courseid);
    
    // Vérifier les permissions
    if (!$is_admin && !has_capability('moodle/course:viewparticipants', $context) && !isset($my_courses[$courseid])) {
        redirect(new moodle_url('/'), 'Accès refusé à cet espace de suivi.', 3);
    }

    $PAGE->set_context($context);
    $PAGE->set_title('Espace Formateur — ' . $course->fullname);
    $PAGE->set_heading($course->fullname);

    // 1. Liste des apprenants inscrits
    $sql_students = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, u.lastaccess,
                            ula.timeaccess AS lastcourseaccess,
                            cc.timecompleted,
                            (SELECT d.data FROM {user_info_data} d 
                             JOIN {user_info_field} f ON f.id = d.fieldid 
                             WHERE f.shortname = 'region' AND d.userid = u.id LIMIT 1) AS region,
                            (SELECT d.data FROM {user_info_data} d 
                             JOIN {user_info_field} f ON f.id = d.fieldid 
                             WHERE f.shortname = 'commune' AND d.userid = u.id LIMIT 1) AS commune
                     FROM {user} u
                     JOIN {user_enrolments} ue ON ue.userid = u.id
                     JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = :courseid
                     LEFT JOIN {user_lastaccess} ula ON ula.userid = u.id AND ula.courseid = :courseid2
                     LEFT JOIN {course_completions} cc ON cc.userid = u.id AND cc.course = :courseid3
                     WHERE u.deleted = 0 AND ue.status = 0
                     ORDER BY u.lastname ASC, u.firstname ASC";

    $raw_learners = $DB->get_records_sql($sql_students, [
        'courseid'  => $courseid,
        'courseid2' => $courseid,
        'courseid3' => $courseid,
    ]);

    // Module count pour calculer le progrès
    $completion = new completion_info($course);
    $is_completion_enabled = $completion->is_enabled();

    $now = time();
    $seven_days_ago = $now - (7 * 86400);
    $total_progress_sum = 0;

    foreach ($raw_learners as $l) {
        $progress_pct = 0;
        if ($is_completion_enabled) {
            $pct = \core_completion\progress::get_course_progress_percentage($course, $l->id);
            if ($pct !== null) {
                $progress_pct = (int) round($pct);
            }
        } elseif (!empty($l->timecompleted)) {
            $progress_pct = 100;
        }

        $total_progress_sum += $progress_pct;

        // Détection inactivité (> 7 jours)
        $last_seen = $l->lastcourseaccess ?: $l->lastaccess;
        $is_inactive = empty($last_seen) || ($last_seen < $seven_days_ago);
        $is_completed = !empty($l->timecompleted) || ($progress_pct >= 100);

        if ($is_completed) {
            $kpis['completed_learners']++;
        }
        if ($is_inactive && !$is_completed) {
            $kpis['inactive_learners']++;
        }

        $learners_data[] = [
            'id'           => $l->id,
            'name'         => fullname($l),
            'email'        => $l->email,
            'region'       => $l->region ?: 'Non renseignée',
            'commune'      => $l->commune ?: '–',
            'last_access'  => $last_seen ? userdate($last_seen, '%d/%m/%Y %H:%M') : 'Jamais',
            'is_inactive'  => $is_inactive,
            'is_completed' => $is_completed,
            'progress'     => $progress_pct,
        ];
    }

    $kpis['total_learners'] = count($learners_data);
    $kpis['avg_progress'] = $kpis['total_learners'] > 0 ? (int) round($total_progress_sum / $kpis['total_learners']) : 0;

    // 2. Activités Devoirs (Assign) & soumissions à corriger
    $sql_assigns = "SELECT a.id, a.name, a.duedate, cm.id AS cmid,
                           (SELECT COUNT(DISTINCT s.id) FROM {assign_submission} s 
                            WHERE s.assignment = a.id AND s.status = 'submitted') AS submissions_count,
                           (SELECT COUNT(DISTINCT g.id) FROM {assign_grades} g 
                            WHERE g.assignment = a.id AND g.grade IS NOT NULL AND g.grade >= 0) AS graded_count
                    FROM {assign} a
                    JOIN {course_modules} cm ON cm.instance = a.id
                    JOIN {modules} m ON m.id = cm.module AND m.name = 'assign'
                    WHERE a.course = :courseid AND cm.deletioninprogress = 0
                    ORDER BY a.duedate ASC, a.name ASC";

    $raw_assigns = $DB->get_records_sql($sql_assigns, ['courseid' => $courseid]);
    foreach ($raw_assigns as $as) {
        $pending = max(0, $as->submissions_count - $as->graded_count);
        $kpis['pending_grades'] += $pending;
        $assignments_data[] = [
            'id'          => $as->id,
            'cmid'        => $as->cmid,
            'name'        => $as->name,
            'duedate'     => $as->duedate ? userdate($as->duedate, '%d/%m/%Y') : 'Aucune date limite',
            'submissions' => (int) $as->submissions_count,
            'graded'      => (int) $as->graded_count,
            'pending'     => $pending,
        ];
    }

    // 3. Activités Quiz & Statistiques rapides
    $sql_quizzes = "SELECT q.id, q.name, cm.id AS cmid,
                           COUNT(qa.id) AS attempts_count,
                           AVG(qa.sumgrades) AS avg_score
                    FROM {quiz} q
                    JOIN {course_modules} cm ON cm.instance = q.id
                    JOIN {modules} m ON m.id = cm.module AND m.name = 'quiz'
                    LEFT JOIN {quiz_attempts} qa ON qa.quiz = q.id AND qa.state = 'finished'
                    WHERE q.course = :courseid AND cm.deletioninprogress = 0
                    GROUP BY q.id, q.name, cm.id
                    ORDER BY q.name ASC";

    $raw_quizzes = $DB->get_records_sql($sql_quizzes, ['courseid' => $courseid]);
    foreach ($raw_quizzes as $qz) {
        $quizzes_data[] = [
            'id'       => $qz->id,
            'cmid'     => $qz->cmid,
            'name'     => $qz->name,
            'attempts' => (int) $qz->attempts_count,
            'avg'      => $qz->avg_score !== null ? round($qz->avg_score, 1) : null,
        ];
    }
} else {
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title('Espace Formateur — Sélection du cours');
    $PAGE->set_heading('Espace Formateur & Tutorat PACTDIGITAL');
}

echo $OUTPUT->header();
?>

<style>
:root {
    --green:  #145B37;
    --green-light: #e8f5ee;
    --red:    #D32F2F;
    --gold:   #d4af37;
    --blue:   #1565C0;
    --grey:   #64748b;
    --dark:   #1e293b;
    --light:  #f8fafc;
    --border: #e2e8f0;
    --white:  #ffffff;
    --shadow: 0 4px 15px rgba(20, 91, 55, 0.08);
}

.pact-trainer-wrapper {
    max-width: 1400px;
    margin: 0 auto 50px auto;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    color: var(--dark);
}

/* ── Banner Header ─────────────────────────────────────────── */
.trainer-hero {
    background: linear-gradient(135deg, var(--green) 0%, #1e7a4a 100%);
    border-radius: 16px;
    padding: 24px 30px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: var(--shadow);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}
.trainer-hero h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 6px 0;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.trainer-hero p {
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0;
}
.trainer-badge-readonly {
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.35);
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #fff;
}

/* ── Course Selector ─────────────────────────────────────────── */
.course-selector-bar {
    background: var(--white);
    padding: 16px 22px;
    border-radius: 12px;
    border: 1px solid var(--border);
    margin-bottom: 24px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
}
.course-selector-bar label {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--green);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.course-select-input {
    flex: 1;
    min-width: 280px;
    max-width: 500px;
    padding: 9px 14px;
    border-radius: 8px;
    border: 1.5px solid var(--border);
    font-size: 0.92rem;
    color: var(--dark);
    font-weight: 600;
    background: #fff;
}
.course-select-input:focus {
    outline: none;
    border-color: var(--green);
}

/* ── Notification Banner ─────────────────────────────────────── */
.pact-alert {
    padding: 12px 18px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.pact-alert-success { background: #e8f5ee; color: var(--green); border-left: 4px solid var(--green); }
.pact-alert-error   { background: #fee2e2; color: var(--red); border-left: 4px solid var(--red); }

/* ── KPIs Grid ───────────────────────────────────────────────── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.kpi-box {
    background: var(--white);
    border-radius: 12px;
    padding: 18px 20px;
    border: 1px solid var(--border);
    border-left: 4px solid var(--green);
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    gap: 4px;
    position: relative;
    overflow: hidden;
}
.kpi-box.red  { border-left-color: var(--red); }
.kpi-box.gold { border-left-color: var(--gold); }
.kpi-box.blue { border-left-color: var(--blue); }
.kpi-box .label {
    font-size: 0.76rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--grey);
    letter-spacing: 0.5px;
}
.kpi-box .val {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--green);
    line-height: 1.1;
}
.kpi-box.red .val  { color: var(--red); }
.kpi-box.gold .val { color: #b8860b; }
.kpi-box.blue .val { color: var(--blue); }
.kpi-box .sub {
    font-size: 0.75rem;
    color: var(--grey);
}

/* ── Navigation Tabs ─────────────────────────────────────────── */
.trainer-tabs {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid var(--border);
    margin-bottom: 22px;
    overflow-x: auto;
    padding-bottom: 2px;
}
.tab-btn {
    background: transparent;
    border: none;
    padding: 12px 18px;
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--grey);
    cursor: pointer;
    border-radius: 8px 8px 0 0;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
}
.tab-btn:hover {
    color: var(--green);
    background: var(--green-light);
}
.tab-btn.active {
    color: var(--green);
    border-bottom-color: var(--green);
    background: #fff;
    font-weight: 700;
}
.tab-badge {
    background: var(--red);
    color: #fff;
    font-size: 0.72rem;
    padding: 2px 7px;
    border-radius: 10px;
    font-weight: 700;
}

/* ── Tab Content Panels ──────────────────────────────────────── */
.tab-panel {
    display: none;
    animation: fadeIn 0.2s ease-out;
}
.tab-panel.active {
    display: block;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

.panel-card {
    background: var(--white);
    border-radius: 12px;
    border: 1px solid var(--border);
    padding: 22px;
    box-shadow: var(--shadow);
    margin-bottom: 24px;
}
.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 18px;
}
.panel-header h3 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--green);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ── Table Styling ───────────────────────────────────────────── */
.table-responsive {
    overflow-x: auto;
    max-height: 520px;
}
table.pact-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.86rem;
}
table.pact-table th {
    background: var(--green);
    color: #fff;
    text-align: left;
    padding: 10px 14px;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
}
table.pact-table td {
    padding: 10px 14px;
    border-bottom: 1px solid var(--border);
    color: var(--dark);
    vertical-align: middle;
}
table.pact-table tr:hover td {
    background: #f7faf8;
}

/* ── Badges & Status ─────────────────────────────────────────── */
.badge-pill {
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-green { background: #dcfce7; color: #15803d; }
.badge-red   { background: #fee2e2; color: #b91c1c; }
.badge-gold  { background: #fef3c7; color: #b45309; }
.badge-blue  { background: #e0f2fe; color: #0369a1; }

.progress-track {
    width: 100px;
    height: 7px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
    display: inline-block;
    vertical-align: middle;
    margin-right: 6px;
}
.progress-fill {
    height: 100%;
    background: var(--green);
    border-radius: 4px;
}

/* ── Action Buttons ──────────────────────────────────────────── */
.btn-pact {
    padding: 7px 14px;
    border-radius: 6px;
    border: none;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s;
}
.btn-pact-green { background: var(--green); color: #fff; }
.btn-pact-green:hover { background: #0e4428; color: #fff; }
.btn-pact-outline { background: #fff; color: var(--green); border: 1.5px solid var(--green); }
.btn-pact-outline:hover { background: var(--green-light); }
.btn-pact-red { background: var(--red); color: #fff; }
.btn-pact-red:hover { background: #a82222; }

/* ── Message Form ────────────────────────────────────────────── */
.msg-form-group {
    margin-bottom: 14px;
}
.msg-form-group label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--grey);
    margin-bottom: 4px;
}
.msg-form-group input, .msg-form-group textarea, .msg-form-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 0.88rem;
    box-sizing: border-box;
    font-family: inherit;
}
.msg-form-group input:focus, .msg-form-group textarea:focus {
    outline: none;
    border-color: var(--green);
}

/* ── Empty State ─────────────────────────────────────────────── */
.empty-box {
    text-align: center;
    padding: 40px 20px;
    color: var(--grey);
}
.empty-box .icon { font-size: 2.5rem; margin-bottom: 10px; }
</style>

<div class="pact-trainer-wrapper">

  <!-- Header Banner -->
  <div class="trainer-hero">
    <div>
      <h1>🧑‍🏫 Espace Formateur & Tutorat PACTDIGITAL</h1>
      <p>Suivi pédagogique, baromètre d'avancement, notation des devoirs et relances bienveillantes</p>
    </div>
    <div class="trainer-badge-readonly">
      🛡️ Mode Accompagnement & Notation (Contenu non modifiable)
    </div>
  </div>

  <?php if (!empty($notification_msg)): ?>
    <div class="pact-alert pact-alert-<?= $notification_type ?>">
      <?= $notification_msg ?>
    </div>
  <?php endif; ?>

  <!-- Selecteur de cours -->
  <div class="course-selector-bar">
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;flex:1;">
      <label for="course-picker">📚 Formation encadrée :</label>
      <select id="course-picker" class="course-select-input" onchange="window.location.href='formateur.php?id=' + this.value;">
        <?php if (empty($my_courses)): ?>
          <option value="0">Aucun cours sous votre encadrement</option>
        <?php else: ?>
          <?php foreach ($my_courses as $c): ?>
            <option value="<?= $c->id ?>" <?= ($c->id == $courseid) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c->fullname) ?> (<?= htmlspecialchars($c->catname) ?>)
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </div>

    <?php if ($courseid > 0): ?>
      <div>
        <a href="<?= (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false) ?>" 
           class="btn-pact btn-pact-outline" target="_blank" title="Voir la page du cours">
          👁️ Consulter la vue du cours
        </a>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($courseid === 0 || empty($course)): ?>
    <!-- Écran d'accueil sans cours sélectionné -->
    <div class="panel-card empty-box">
      <div class="icon">📖</div>
      <h3>Veuillez sélectionner un cours ci-dessus</h3>
      <p>Choisissez l'une de vos formations pour accéder au suivi en temps réel de votre cohorte.</p>
    </div>
  <?php else: ?>

    <!-- ── 4 KPI CARDS ─────────────────────────────────────────── -->
    <div class="kpi-grid">
      <div class="kpi-box">
        <div class="label">👥 Apprenants inscrits</div>
        <div class="val"><?= $kpis['total_learners'] ?></div>
        <div class="sub">Cohorte active</div>
      </div>
      <div class="kpi-box blue">
        <div class="label">📈 Progression moyenne</div>
        <div class="val"><?= $kpis['avg_progress'] ?>%</div>
        <div class="sub">Avancement global</div>
      </div>
      <div class="kpi-box red">
        <div class="label">⚠️ Inactifs (> 7 jours)</div>
        <div class="val"><?= $kpis['inactive_learners'] ?></div>
        <div class="sub">Apprenants à relancer</div>
      </div>
      <div class="kpi-box <?= ($kpis['pending_grades'] > 0) ? 'gold' : '' ?>">
        <div class="label">📬 Devoirs à noter</div>
        <div class="val"><?= $kpis['pending_grades'] ?></div>
        <div class="sub">En attente d'évaluation</div>
      </div>
    </div>

    <!-- ── ONGLETS DU COCKPIT ──────────────────────────────────── -->
    <div class="trainer-tabs">
      <button class="tab-btn active" onclick="switchTab('tab-progression')">
        📊 Suivi & Avancement (<?= $kpis['total_learners'] ?>)
      </button>
      <button class="tab-btn" onclick="switchTab('tab-evaluations')">
        ✍️ Évaluations & Devoirs
        <?php if ($kpis['pending_grades'] > 0): ?>
          <span class="tab-badge"><?= $kpis['pending_grades'] ?></span>
        <?php endif; ?>
      </button>
      <button class="tab-btn" onclick="switchTab('tab-relance')">
        📢 Relance & Messagerie
        <?php if ($kpis['inactive_learners'] > 0): ?>
          <span class="tab-badge"><?= $kpis['inactive_learners'] ?> inactifs</span>
        <?php endif; ?>
      </button>
      <button class="tab-btn" onclick="switchTab('tab-certifications')">
        🏆 Certifications délivrées (<?= $kpis['completed_learners'] ?>)
      </button>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ONGLET 1 : SUIVI & AVANCEMENT                              -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="tab-panel active" id="tab-progression">
      <div class="panel-card">
        <div class="panel-header">
          <h3>👥 Tableau d'avancement des apprenants</h3>
          <div style="display:flex;gap:10px;align-items:center;">
            <input type="text" id="search-student" placeholder="🔍 Rechercher un nom…" 
                   oninput="filterLearners()" 
                   style="padding:7px 12px;border:1px solid var(--border);border-radius:6px;font-size:0.85rem;">
            <select id="filter-status" onchange="filterLearners()" 
                    style="padding:7px 12px;border:1px solid var(--border);border-radius:6px;font-size:0.85rem;">
              <option value="all">Tous les statuts</option>
              <option value="inactive">⚠️ Inactifs (> 7 jours)</option>
              <option value="completed">✅ Formation terminée</option>
              <option value="in_progress">🔄 En cours</option>
            </select>
          </div>
        </div>

        <div class="table-responsive">
          <table class="pact-table" id="table-learners">
            <thead>
              <tr>
                <th>Apprenant</th>
                <th>Région / Commune</th>
                <th>Dernier accès au cours</th>
                <th>Progression</th>
                <th>Statut</th>
                <th>Action rapide</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($learners_data)): ?>
                <tr><td colspan="6" class="empty-box">Aucun apprenant inscrit pour le moment.</td></tr>
              <?php else: ?>
                <?php foreach ($learners_data as $l): ?>
                  <tr class="learner-row" 
                      data-name="<?= strtolower(htmlspecialchars($l['name'])) ?>"
                      data-status="<?= $l['is_completed'] ? 'completed' : ($l['is_inactive'] ? 'inactive' : 'in_progress') ?>">
                    <td>
                      <strong><?= htmlspecialchars($l['name']) ?></strong><br>
                      <small style="color:var(--grey);"><?= htmlspecialchars($l['email']) ?></small>
                    </td>
                    <td>
                      <?= htmlspecialchars($l['region']) ?><br>
                      <small style="color:var(--grey);"><?= htmlspecialchars($l['commune']) ?></small>
                    </td>
                    <td><?= $l['last_access'] ?></td>
                    <td>
                      <div class="progress-track">
                        <div class="progress-fill" style="width: <?= $l['progress'] ?>%;"></div>
                      </div>
                      <strong><?= $l['progress'] ?>%</strong>
                    </td>
                    <td>
                      <?php if ($l['is_completed']): ?>
                        <span class="badge-pill badge-green">✅ Terminé</span>
                      <?php elseif ($l['is_inactive']): ?>
                        <span class="badge-pill badge-red">⚠️ Inactif > 7j</span>
                      <?php else: ?>
                        <span class="badge-pill badge-blue">🔄 En cours</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <button type="button" class="btn-pact btn-pact-outline" 
                              onclick="prefillReminder(<?= $l['id'] ?>, '<?= addslashes(htmlspecialchars($l['name'])) ?>')">
                        💬 Relancer
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ONGLET 2 : ÉVALUATIONS & DEVOIRS                           -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="tab-panel" id="tab-evaluations">
      <!-- Section Devoirs (Assign) -->
      <div class="panel-card">
        <div class="panel-header">
          <h3>📬 Devoirs & Travaux pratiques à corriger</h3>
          <a href="<?= (new moodle_url('/grade/report/grader/index.php', ['id' => $courseid]))->out(false) ?>" 
             class="btn-pact btn-pact-green" target="_blank">
            📊 Ouvrir le carnet de notes complet
          </a>
        </div>

        <?php if (empty($assignments_data)): ?>
          <div class="empty-box">
            <div class="icon">📝</div>
            <p>Ce cours ne comporte aucun devoir de type "Dépôt de travail".</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="pact-table">
              <thead>
                <tr>
                  <th>Devoir</th>
                  <th>Date limite</th>
                  <th>Copies remises</th>
                  <th>Déjà notées</th>
                  <th>À corriger</th>
                  <th>Action de correction</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($assignments_data as $as): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($as['name']) ?></strong></td>
                    <td><?= $as['duedate'] ?></td>
                    <td><?= $as['submissions'] ?></td>
                    <td><?= $as['graded'] ?></td>
                    <td>
                      <?php if ($as['pending'] > 0): ?>
                        <span class="badge-pill badge-red">🚨 <?= $as['pending'] ?> en attente</span>
                      <?php else: ?>
                        <span class="badge-pill badge-green">✅ Tout est noté</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <!-- Lien direct vers l'écran de notation du devoir (sans édition) -->
                      <a href="<?= (new moodle_url('/mod/assign/view.php', ['id' => $as['cmid'], 'action' => 'grading']))->out(false) ?>" 
                         class="btn-pact btn-pact-green" target="_blank">
                        ✍️ Noter les copies
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Section Quiz / Auto-évaluations -->
      <div class="panel-card">
        <div class="panel-header">
          <h3>🎯 Quiz & Tests d'auto-évaluation</h3>
        </div>

        <?php if (empty($quizzes_data)): ?>
          <div class="empty-box">
            <div class="icon">❓</div>
            <p>Ce cours ne comporte pas de Quiz.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="pact-table">
              <thead>
                <tr>
                  <th>Titre du Quiz</th>
                  <th>Tentatives enregistrées</th>
                  <th>Note moyenne</th>
                  <th>Rapport des tentatives</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($quizzes_data as $qz): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($qz['name']) ?></strong></td>
                    <td><?= $qz['attempts'] ?> tentative(s)</td>
                    <td>
                      <?= ($qz['avg'] !== null) ? "<strong>{$qz['avg']}</strong> / note max" : '–' ?>
                    </td>
                    <td>
                      <a href="<?= (new moodle_url('/mod/quiz/report.php', ['id' => $qz['cmid'], 'mode' => 'overview']))->out(false) ?>" 
                         class="btn-pact btn-pact-outline" target="_blank">
                        📊 Voir les résultats & questions
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ONGLET 3 : RELANCE & MESSAGERIE                            -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="tab-panel" id="tab-relance">
      <div class="panel-card">
        <div class="panel-header">
          <h3>📢 Envoyer une relance ou un encouragement ciblé</h3>
          <span style="font-size:0.85rem;color:var(--grey);">
            Les messages sont délivrés instantanément dans l'espace Moodle de l'apprenant.
          </span>
        </div>

        <form method="post" action="formateur.php?id=<?= $courseid ?>&action=send_reminder">
          <input type="hidden" name="sesskey" value="<?= sesskey() ?>">

          <!-- Pré-sélection rapide -->
          <div style="background:var(--light);padding:14px;border-radius:8px;margin-bottom:16px;border:1px solid var(--border);">
            <div style="font-weight:700;font-size:0.85rem;color:var(--green);margin-bottom:8px;">
              ⚡ Sélection rapide des destinataires :
            </div>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
              <button type="button" class="btn-pact btn-pact-outline" onclick="selectInactivesOnly()">
                ⚠️ Cocher tous les inactifs (> 7 jours)
              </button>
              <button type="button" class="btn-pact btn-pact-outline" onclick="toggleAllReminders(true)">
                Tout cocher
              </button>
              <button type="button" class="btn-pact btn-pact-outline" onclick="toggleAllReminders(false)">
                Tout décocher
              </button>
            </div>
          </div>

          <!-- Liste des apprenants avec checkboxes -->
          <div class="msg-form-group">
            <label>Destinataires sélectionnés (<span id="count-selected">0</span>) :</label>
            <div style="max-height:200px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:10px;background:#fff;">
              <?php foreach ($learners_data as $l): ?>
                <label style="display:flex;align-items:center;gap:8px;padding:4px 0;font-size:0.85rem;cursor:pointer;">
                  <input type="checkbox" name="userids[]" value="<?= $l['id'] ?>" 
                         class="reminder-checkbox <?= $l['is_inactive'] ? 'is-inactive-user' : '' ?>"
                         onchange="updateSelectedCount()">
                  <span><?= htmlspecialchars($l['name']) ?></span>
                  <?php if ($l['is_inactive']): ?>
                    <span class="badge-pill badge-red" style="font-size:0.68rem;">Inactif</span>
                  <?php endif; ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Modèles rapides de message -->
          <div class="msg-form-group">
            <label>Modèle de message pré-rempli :</label>
            <select onchange="applyTemplate(this.value)" style="background:#fff;">
              <option value="">Sélectionner un modèle rapide…</option>
              <option value="inactivity">⚠️ Relance d'inactivité bienveillante</option>
              <option value="assignment">📝 Rappel de rendu de devoir</option>
              <option value="congrats">👏 Félicitations pour la progression</option>
            </select>
          </div>

          <div class="msg-form-group">
            <label>Objet du message :</label>
            <input type="text" name="subject" id="msg-subject" required 
                   placeholder="Ex: PACTDIGITAL — Des nouvelles de votre formation">
          </div>

          <div class="msg-form-group">
            <label>Corps du message :</label>
            <textarea name="messagebody" id="msg-body" rows="5" required 
                      placeholder="Écrivez votre message d'encouragement ici…"></textarea>
          </div>

          <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn-pact btn-pact-green" style="padding:10px 22px;font-size:0.92rem;">
              🚀 Envoyer la relance
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ONGLET 4 : CERTIFICATIONS & RÉUSSITE                      -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="tab-panel" id="tab-certifications">
      <div class="panel-card">
        <div class="panel-header">
          <h3>🏆 Apprenants certifiés / Formation validée</h3>
          <button type="button" class="btn-pact btn-pact-outline" onclick="exportCertifiedCSV()">
            📥 Télécharger la liste (.csv)
          </button>
        </div>

        <?php 
          $certified_list = array_filter($learners_data, function($item) {
              return $item['is_completed'];
          });
        ?>

        <?php if (empty($certified_list)): ?>
          <div class="empty-box">
            <div class="icon">🎖️</div>
            <p>Aucun apprenant n'a encore validé l'intégralité du cours pour le moment.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="pact-table" id="table-certified">
              <thead>
                <tr>
                  <th>Nom et prénom</th>
                  <th>Adresse email</th>
                  <th>Région</th>
                  <th>Progression</th>
                  <th>Statut d'attestation</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($certified_list as $c): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= htmlspecialchars($c['region']) ?></td>
                    <td><span class="badge-pill badge-green">100% Validé</span></td>
                    <td><span class="badge-pill badge-green">🏆 Éligible à l'attestation</span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

  <?php endif; ?>

</div>

<script>
// ── Gestion des onglets ───────────────────────────────────────────
function switchTab(targetId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    
    const panel = document.getElementById(targetId);
    if (panel) {
        panel.classList.add('active');
    }
    
    // Met le bouton actif
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => {
        if (btn.getAttribute('onclick').includes(targetId)) {
            btn.classList.add('active');
        }
    });
}

// ── Filtrage de la table des apprenants ───────────────────────────
function filterLearners() {
    const searchVal = document.getElementById('search-student').value.toLowerCase().trim();
    const statusVal = document.getElementById('filter-status').value;
    const rows = document.querySelectorAll('.learner-row');

    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const status = row.getAttribute('data-status');

        const matchName = !searchVal || name.includes(searchVal);
        let matchStatus = (statusVal === 'all') || (status === statusVal);

        if (matchName && matchStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ── Pré-remplissage et gestion des relances ───────────────────────
function updateSelectedCount() {
    const count = document.querySelectorAll('.reminder-checkbox:checked').length;
    const countEl = document.getElementById('count-selected');
    if (countEl) countEl.innerText = count;
}

function toggleAllReminders(check) {
    document.querySelectorAll('.reminder-checkbox').forEach(cb => cb.checked = check);
    updateSelectedCount();
}

function selectInactivesOnly() {
    document.querySelectorAll('.reminder-checkbox').forEach(cb => {
        cb.checked = cb.classList.contains('is-inactive-user');
    });
    updateSelectedCount();
}

function prefillReminder(userId, userName) {
    switchTab('tab-relance');
    document.querySelectorAll('.reminder-checkbox').forEach(cb => {
        cb.checked = (cb.value == userId);
    });
    updateSelectedCount();
    
    document.getElementById('msg-subject').value = "PACTDIGITAL — Point sur votre formation";
    document.getElementById('msg-body').value = "Bonjour " + userName + ",\n\nJe me permets de vous contacter pour savoir comment se passe votre progression dans la formation. Avez-vous rencontré des difficultés sur un chapitre particulier ? N'hésitez pas à me poser vos questions !\n\nBien cordialement,\nVotre formateur PACTDIGITAL.";
}

function applyTemplate(type) {
    const sub = document.getElementById('msg-subject');
    const body = document.getElementById('msg-body');

    if (type === 'inactivity') {
        sub.value = "⚠️ PACTDIGITAL — Nous espérons vous revoir très vite !";
        body.value = "Bonjour,\n\nNous avons remarqué que vous ne vous êtes pas connecté à la formation depuis quelques jours. Tout va bien ?\n\nN'hésitez pas à revenir sur la plateforme pour finaliser vos modules et obtenir votre certification PACTDIGITAL. Si vous avez besoin d'aide ou si vous rencontrez un blocage technique, écrivez-moi directement ici.\n\nBonne continuation !";
    } else if (type === 'assignment') {
        sub.value = "📝 PACTDIGITAL — Rappel pour le rendu de votre devoir";
        body.value = "Bonjour,\n\nCeci est un petit rappel amical concernant l'évaluation du module en cours. Pensez à déposer votre travail avant la date limite pour recevoir vos commentaires et valider votre attestation.\n\nBon courage pour vos travaux !";
    } else if (type === 'congrats') {
        sub.value = "👏 PACTDIGITAL — Félicitations pour votre avancement !";
        body.value = "Bonjour,\n\nBravo pour votre assiduité et vos excellents résultats récents dans la formation. Continuez ainsi, vous êtes sur la bonne voie pour décrocher votre certificat PACTDIGITAL !\n\nExcellente continuation.";
    }
}

// ── Export CSV des certifiés ─────────────────────────────────────
function exportCertifiedCSV() {
    const table = document.getElementById('table-certified');
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');
    rows.forEach(r => {
        const cols = r.querySelectorAll('th, td');
        let rowData = [];
        cols.forEach(c => {
            let text = c.innerText.replace(/"/g, '""').trim();
            rowData.push('"' + text + '"');
        });
        csv.push(rowData.join(';'));
    });

    const csvContent = "\uFEFF" + csv.join("\n");
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = "pactdigital_certifies_cours_" + <?= json_encode($courseid) ?> + ".csv";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>

<?php
echo $OUTPUT->footer();
