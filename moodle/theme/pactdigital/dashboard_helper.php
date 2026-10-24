<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->libdir . '/completionlib.php');

function pactdigital_get_dashboard_context() {
    global $DB, $USER, $CFG;

    $systemcontext = context_system::instance();
    $is_admin = is_siteadmin($USER->id);
    $is_teacher = false;

    // Vérifier si l'utilisateur est enseignant dans au moins un cours
    $teacher_courses = [];
    $all_user_courses = enrol_get_users_courses($USER->id, true, 'id, fullname, shortname, category');
    
    foreach ($all_user_courses as $c) {
        $ccontext = context_course::instance($c->id);
        if (has_capability('moodle/course:update', $ccontext, $USER->id)) {
            $is_teacher = true;
            $teacher_courses[] = $c;
        }
    }

    // 1. DONNÉES APPRENANT
    $student_courses = [];
    $total_enrolled = count($all_user_courses);
    $total_completed = 0;

    foreach ($all_user_courses as $c) {
        $cinfo = new completion_info($c);
        $is_comp = $cinfo->is_course_complete($USER->id);
        if ($is_comp) {
            $total_completed++;
        }

        // Récupérer le niveau officiel (Bronze, Argent, Or) d'après le nom
        $level = 'Bronze';
        $full_tokens = $c->fullname . ' ' . $c->shortname;
        if (preg_match('/(^|_|\s|-|\b)(argent|silver)($|_|\s|-|\b)/ui', $full_tokens)) {
            $level = 'Argent';
        } else if (preg_match('/(^|_|\s|-|\b)(or|gold)($|_|\s|-|\b)/ui', $full_tokens)) {
            $level = 'Or';
        }

        // Catégorie / Domaine
        $category = $DB->get_record('course_categories', ['id' => $c->category]);
        $domain = $category ? $category->name : 'Général';

        // Pourcentage de progression approximatif
        $progress = $is_comp ? 100 : 0;
        if (!$is_comp) {
            $activities = $cinfo->get_activities();
            if (!empty($activities)) {
                $completed_acts = 0;
                foreach ($activities as $activity) {
                    $act_data = $cinfo->get_data($activity, false, $USER->id);
                    if ($act_data->completionstate == COMPLETION_COMPLETE || $act_data->completionstate == COMPLETION_COMPLETE_PASS) {
                        $completed_acts++;
                    }
                }
                $progress = round(($completed_acts / count($activities)) * 100);
            }
        }

        $student_courses[] = [
            'id' => $c->id,
            'fullname' => $c->fullname,
            'shortname' => $c->shortname,
            'domain' => $domain,
            'level' => $level,
            'progress' => $progress,
            'is_completed' => $is_comp,
            'url' => (new moodle_url('/course/view.php', ['id' => $c->id]))->out(false)
        ];
    }

    // Certificats de l'apprenant (Galerie de Prestige)
    $certificates = [];
    if ($DB->get_manager()->table_exists('customcert_issues')) {
        require_once($CFG->libdir . '/tcpdf/tcpdf_barcodes_2d.php');

        $sql = "SELECT ci.id AS issueid, ci.code, ci.timecreated, c.id AS certid, c.name AS certname,
                       co.id AS courseid, co.fullname AS coursename, co.shortname,
                       cat.name AS catname
                FROM {customcert_issues} ci
                JOIN {customcert} c ON c.id = ci.customcertid
                JOIN {course} co ON co.id = c.course
                LEFT JOIN {course_categories} cat ON cat.id = co.category
                WHERE ci.userid = ?
                ORDER BY ci.timecreated DESC";
        $issues = $DB->get_records_sql($sql, [$USER->id]);
        foreach ($issues as $iss) {
            $cm = get_coursemodule_from_instance('customcert', $iss->certid, $iss->courseid);
            $download_url = $cm ? (new moodle_url('/mod/customcert/view.php', ['id' => $cm->id, 'downloadown' => true]))->out(false) : '#';
            $verify_url = (new moodle_url('/mod/customcert/verify_certificate.php', ['code' => $iss->code]))->out(false);

            // Titre enrichi
            $is_short_title = in_array(strtolower(trim($iss->coursename)), ['bronze', 'argent', 'or', 'diamant']);
            $display_coursename = ($is_short_title && !empty($iss->catname)) ? $iss->catname . ' — ' . $iss->coursename : $iss->coursename;

            // Détection du niveau métallique officiel (Bronze, Argent, Or)
            $tokens = strtolower($iss->coursename . ' ' . $iss->shortname);
            $cert_level = 'Bronze';
            $level_class = 'cert-bronze';
            $level_badge = '🥉 Bronze';
            $is_bronze = true;
            $is_argent = false;
            $is_or = false;

            if (preg_match('/(^|_|\s|-|\b)(argent|silver)($|_|\s|-|\b)/ui', $tokens)) {
                $cert_level = 'Argent';
                $level_class = 'cert-argent';
                $level_badge = '🥈 Argent';
                $is_bronze = false;
                $is_argent = true;
            } else if (preg_match('/(^|_|\s|-|\b)(or|gold)($|_|\s|-|\b)/ui', $tokens)) {
                $cert_level = 'Or';
                $level_class = 'cert-or';
                $level_badge = '🥇 Or';
                $is_bronze = false;
                $is_or = true;
            }

            // Génération QR Code vectoriel intégré (SVG pur)
            $qr_code_data_uri = '';
            try {
                $b = new TCPDF2DBarcode($verify_url, 'QRCODE,L');
                $svg_code = $b->getBarcodeSVGcode(2, 2, 'black');
                $qr_code_data_uri = 'data:image/svg+xml;base64,' . base64_encode($svg_code);
            } catch (Throwable $e) {
                $qr_code_data_uri = '';
            }

            // Liens officiels de partage LinkedIn
            $issue_year = date('Y', $iss->timecreated);
            $issue_month = date('n', $iss->timecreated);
            $linkedin_cert_title = 'Certificat ' . $display_coursename . ' (' . $cert_level . ') - PACTDIGITAL-BF';
            $linkedin_add_params = [
                'startTask' => 'CERTIFICATION_NAME',
                'name' => $linkedin_cert_title,
                'organizationName' => 'Ministère de la Transition Digitale / PACTDIGITAL-BF',
                'issueYear' => $issue_year,
                'issueMonth' => $issue_month,
                'certUrl' => $verify_url,
                'certId' => $iss->code
            ];
            $linkedin_add_url = 'https://www.linkedin.com/profile/add?' . http_build_query($linkedin_add_params);
            $linkedin_share_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($verify_url);

            $certificates[] = [
                'id' => $iss->issueid,
                'code' => $iss->code,
                'certname' => $iss->certname,
                'coursename' => $display_coursename,
                'catname' => $iss->catname ?: '',
                'level' => $cert_level,
                'level_badge' => $level_badge,
                'level_class' => $level_class,
                'is_bronze' => $is_bronze,
                'is_argent' => $is_argent,
                'is_or' => $is_or,
                'recipient_name' => fullname($USER),
                'date' => userdate($iss->timecreated, '%d/%m/%Y'),
                'download_url' => $download_url,
                'verify_url' => $verify_url,
                'qr_code' => $qr_code_data_uri,
                'linkedin_add_url' => $linkedin_add_url,
                'linkedin_share_url' => $linkedin_share_url
            ];
        }
    }

    $global_progress = $total_enrolled > 0 ? round(($total_completed / $total_enrolled) * 100) : 0;

    // 2. DONNÉES ENSEIGNANT (Si applicable)
    $teacher_students_data = [];
    if ($is_teacher || $is_admin) {
        $t_course_ids = array_map(function($c) { return $c->id; }, $teacher_courses);
        if ($is_admin && empty($t_course_ids)) {
            // Pour l'admin, prendre les 5 premiers cours pour prévisualiser
            $t_course_ids = $DB->get_fieldset_select('course', 'id', 'id > 1', [], '', 0, 5);
        }

        if (!empty($t_course_ids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($t_course_ids);
            $students_sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, u.lastaccess, c.id AS courseid, c.fullname AS coursename
                             FROM {user} u
                             JOIN {user_enrolments} ue ON ue.userid = u.id
                             JOIN {enrol} e ON e.id = ue.enrolid
                             JOIN {course} c ON c.id = e.courseid
                             WHERE c.id $insql AND u.deleted = 0 AND u.id != ?
                             ORDER BY u.lastname ASC LIMIT 50";
            $inparams[] = $USER->id;
            $raw_students = $DB->get_records_sql($students_sql, $inparams);

            foreach ($raw_students as $st) {
                $last_acc = $st->lastaccess > 0 ? userdate($st->lastaccess, '%d/%m/%Y %H:%M') : 'Jamais';
                $teacher_students_data[] = [
                    'userid' => $st->id,
                    'fullname' => fullname($st),
                    'email' => $st->email,
                    'coursename' => $st->coursename,
                    'courseid' => $st->courseid,
                    'last_access' => $last_acc
                ];
            }
        }
    }

    // 3. DONNÉES ADMIN
    $admin_stats = [];
    if ($is_admin) {
        $total_users = $DB->count_records('user', ['deleted' => 0]) - 1; // exclure guest
        $total_courses = $DB->count_records_select('course', 'id > 1');
        $total_cert_issued = $DB->count_records('customcert_issues');
        $admin_stats = [
            'total_users' => $total_users,
            'total_courses' => $total_courses,
            'total_certs' => $total_cert_issued,
            'admin_dashboard_url' => (new moodle_url('/local/pactdigital/dashboard.php'))->out(false)
        ];
    }

    return [
        'is_admin' => $is_admin,
        'is_teacher' => $is_teacher,
        'has_certificates' => !empty($certificates),
        'certificates_count' => count($certificates),
        'certificates' => $certificates,
        'student_courses' => $student_courses,
        'has_student_courses' => !empty($student_courses),
        'total_enrolled' => $total_enrolled,
        'total_completed' => $total_completed,
        'global_progress' => $global_progress,
        'teacher_courses' => array_values($teacher_courses),
        'teacher_students' => $teacher_students_data,
        'teacher_students_count' => count($teacher_students_data),
        'admin_stats' => $admin_stats,
        'total_courses_available' => $DB->count_records_select('course', 'id > 1 AND visible = 1'),
        'catalogue_url' => (new moodle_url('/local/pactdigital/catalogue.php'))->out(false)
    ];
}
