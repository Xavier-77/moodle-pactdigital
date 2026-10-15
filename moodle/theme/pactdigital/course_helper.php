<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Récupère le contexte spécifique à l'espace formateur à l'intérieur d'un cours.
 * 
 * @param stdClass $course L'objet cours actuel
 * @return array Données à injecter dans le template
 */
function pactdigital_get_course_context($course) {
    global $DB, $USER, $CFG;

    if (empty($course) || !is_object($course) || $course->id <= 1 || !isloggedin() || isguestuser()) {
        return ['is_course_trainer' => false];
    }

    $ccontext = context_course::instance($course->id);
    $is_admin = is_siteadmin($USER->id);

    // Vérifier si l'utilisateur est formateur / tuteur / enseignant / gestionnaire dans ce cours
    $is_trainer = $is_admin 
        || has_capability('moodle/grade:viewall', $ccontext, $USER->id)
        || has_capability('moodle/course:viewparticipants', $ccontext, $USER->id)
        || has_capability('moodle/course:update', $ccontext, $USER->id);

    if (!$is_trainer) {
        return ['is_course_trainer' => false];
    }

    // 1. STATISTIQUES EN DIRECT DE LA COHORTE DU COURS
    $sql_students = "SELECT u.id, u.lastaccess, ula.timeaccess AS lastcourseaccess, cc.timecompleted
                     FROM {user} u
                     JOIN {user_enrolments} ue ON ue.userid = u.id
                     JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = :courseid
                     LEFT JOIN {user_lastaccess} ula ON ula.userid = u.id AND ula.courseid = :courseid2
                     LEFT JOIN {course_completions} cc ON cc.userid = u.id AND cc.course = :courseid3
                     WHERE u.deleted = 0 AND ue.status = 0";

    $students = $DB->get_records_sql($sql_students, [
        'courseid'  => $course->id,
        'courseid2' => $course->id,
        'courseid3' => $course->id
    ]);

    $total_students = count($students);
    $completed_students = 0;
    $inactive_students = 0;
    $now = time();
    $seven_days_ago = $now - (7 * 86400);

    $completion = new completion_info($course);
    $is_completion_enabled = $completion->is_enabled();
    $total_pct = 0;

    foreach ($students as $s) {
        $last_seen = $s->lastcourseaccess ?: $s->lastaccess;
        $is_comp = !empty($s->timecompleted);
        if ($is_comp) {
            $completed_students++;
            $total_pct += 100;
        } else {
            if ($is_completion_enabled) {
                $pct = \core_completion\progress::get_course_progress_percentage($course, $s->id);
                if ($pct !== null) {
                    $total_pct += (int) round($pct);
                }
            }
            if (empty($last_seen) || $last_seen < $seven_days_ago) {
                $inactive_students++;
            }
        }
    }
    $avg_progress = $total_students > 0 ? (int) round($total_pct / $total_students) : 0;

    // 2. DEVOIRS EN ATTENTE DE NOTATION
    $sql_pending_grades = "SELECT COUNT(DISTINCT s.id) AS pending
                           FROM {assign_submission} s
                           JOIN {assign} a ON a.id = s.assignment AND a.course = :courseid
                           LEFT JOIN {assign_grades} g ON g.assignment = a.id AND g.userid = s.userid AND g.attemptnumber = s.attemptnumber
                           WHERE s.status = 'submitted' AND (g.id IS NULL OR g.grade IS NULL OR g.grade < 0)";
    $pending_grades = (int) $DB->count_records_sql($sql_pending_grades, ['courseid' => $course->id]);

    // 3. GUIDES & ÉLÉMENTS SPÉCIFIQUES POUR LE FORMATEUR
    // Détection automatique des ressources dédiées à l'enseignant (fiches, corrigés, activités cachées aux étudiants)
    $trainer_guides = [];

    try {
        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();
        $processed_cmids = [];

        // D'abord : toutes les activités situées dans une section réservée au Formateur (Section 6 Espace Formateur, etc.)
        foreach ($sections as $sec) {
            $is_trainer_section = (stripos($sec->name, 'formateur') !== false || stripos($sec->name, 'tuteur') !== false || !$sec->visible);
            if ($is_trainer_section && !empty($sec->sequence)) {
                $cmids = explode(',', $sec->sequence);
                foreach ($cmids as $cmid) {
                    if (isset($modinfo->cms[$cmid])) {
                        $cm = $modinfo->cms[$cmid];
                        $processed_cmids[$cmid] = true;

                        $badge = '🔒 Espace Formateur';
                        $icon = '📁';
                        if ($cm->modname === 'attendance') {
                            $badge = '📋 Présence & Émargement';
                            $icon = '📅';
                        } elseif ($cm->modname === 'folder') {
                            $badge = '📂 Supports & Corrigés';
                            $icon = '📁';
                        } elseif (stripos($cm->name, 'guide') !== false) {
                            $badge = '📘 Guide Pédagogique';
                            $icon = '📘';
                        } elseif (stripos($cm->name, 'corrig') !== false || stripos($cm->name, 'barème') !== false) {
                            $badge = '🔑 Corrigé & Barème';
                            $icon = '🔑';
                        }

                        $trainer_guides[] = [
                            'id'        => $cm->id,
                            'name'      => $cm->name,
                            'badge'     => $badge,
                            'icon'      => $icon,
                            'url'       => $cm->url ? $cm->url->out(false) : (new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]))->out(false),
                            'is_hidden' => !$cm->visible || !$sec->visible,
                        ];
                    }
                }
            }
        }

        // Ensuite : toute autre activité individuelle dans le reste du cours
        foreach ($modinfo->cms as $cm) {
            if (isset($processed_cmids[$cm->id])) {
                continue;
            }
            $name_lower = mb_strtolower($cm->name);
            $is_trainer_resource = false;
            $guide_badge = 'Ressource Formateur';
            $guide_icon = '📘';

            if (strpos($name_lower, 'guide') !== false) {
                $is_trainer_resource = true;
                $guide_badge = 'Guide Pédagogique';
                $guide_icon = '📘';
            } elseif (strpos($name_lower, 'formateur') !== false || strpos($name_lower, 'tuteur') !== false || strpos($name_lower, 'enseignant') !== false) {
                $is_trainer_resource = true;
                $guide_badge = 'Document Formateur';
                $guide_icon = '🧑‍🏫';
            } elseif (strpos($name_lower, 'corrig') !== false || strpos($name_lower, 'barème') !== false || strpos($name_lower, 'bareme') !== false) {
                $is_trainer_resource = true;
                $guide_badge = 'Corrigé & Barème';
                $guide_icon = '🔑';
            } elseif (!$cm->visible) {
                $is_trainer_resource = true;
                $guide_badge = 'Élément réservé (Masqué)';
                $guide_icon = '🔒';
            }

            if ($is_trainer_resource) {
                $trainer_guides[] = [
                    'id'        => $cm->id,
                    'name'      => $cm->name,
                    'badge'     => $guide_badge,
                    'icon'      => $guide_icon,
                    'url'       => $cm->url ? $cm->url->out(false) : (new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]))->out(false),
                    'is_hidden' => !$cm->visible,
                ];
            }
        }
    } catch (\Throwable $e) {
        // En cas d'erreur modinfo
    }

    return [
        'is_course_trainer'   => true,
        'course_id'           => $course->id,
        'course_fullname'     => $course->fullname,
        'total_students'      => $total_students,
        'avg_progress'        => $avg_progress,
        'inactive_students'   => $inactive_students,
        'pending_grades'      => $pending_grades,
        'has_pending_grades'  => ($pending_grades > 0),
        'has_inactive'        => ($inactive_students > 0),
        'completed_students'  => $completed_students,
        'has_trainer_guides'  => !empty($trainer_guides),
        'trainer_guides'      => $trainer_guides,
        'trainer_cockpit_url' => (new moodle_url('/local/pactdigital/formateur.php', ['id' => $course->id]))->out(false),
        'grader_report_url'   => (new moodle_url('/grade/report/grader/index.php', ['id' => $course->id]))->out(false),
        'participants_url'    => (new moodle_url('/user/index.php', ['id' => $course->id]))->out(false),
    ];
}
