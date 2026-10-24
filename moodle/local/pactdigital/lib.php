<?php
/**
 * PACTDIGITAL-BF — Fonctions partagées du plugin local
 */

defined('MOODLE_INTERNAL') || die();

function local_pactdigital_extend_navigation_site_administration(navigation_node $navigation) {
    $context = context_system::instance();
    if (!has_capability('moodle/site:config', $context) && !has_capability('local/pactdigital:viewdashboard', $context)) {
        return;
    }
    $url = new moodle_url('/local/pactdigital/dashboard.php');
    $navigation->add(
        'Tableau de bord PACTDIGITAL',
        $url,
        navigation_node::TYPE_CUSTOM,
        null,
        'pactdigital_dashboard',
        new pix_icon('i/report', '')
    );
}

/**
 * Vérifie et délivre automatiquement l'attestation officielle et le badge numérique
 * dès que l'apprenant remplit la condition de réussite (score >= 70% ou achèvement du cours).
 * Aucune action manuelle n'est requise de la part du formateur ou de l'administrateur.
 *
 * @param int $userid Identifiant de l'apprenant
 * @param int $courseid Identifiant du cours
 * @return array Résultat de la délivrance automatique
 */
function pactdigital_auto_award_credentials($userid, $courseid) {
    global $DB;

    if (empty($userid) || empty($courseid) || $courseid <= 1) {
        return ['awarded' => false];
    }

    $now = time();

    // 1. Vérifier si l'utilisateur a réussi un examen dans ce cours ou a déjà complété
    $has_passed = false;
    $completion = $DB->get_record('course_completions', ['userid' => $userid, 'course' => $courseid]);
    if ($completion && !empty($completion->timecompleted)) {
        $has_passed = true;
    } else {
        // Vérifier les tentatives de quiz terminées dans ce cours
        $sql = "SELECT qa.id, qa.sumgrades, q.grade AS maxgrade
                FROM {quiz_attempts} qa
                JOIN {quiz} q ON q.id = qa.quiz
                WHERE qa.userid = :userid AND q.course = :courseid AND qa.state = 'finished' AND qa.preview = 0";
        $attempts = $DB->get_records_sql($sql, ['userid' => $userid, 'courseid' => $courseid]);
        foreach ($attempts as $att) {
            $score_pct = ($att->maxgrade > 0) ? ($att->sumgrades / $att->maxgrade) * 100 : 0;
            if ($score_pct >= 70) {
                $has_passed = true;
                break;
            }
        }
    }

    if (!$has_passed) {
        return ['awarded' => false];
    }

    // 2. Marquer l'achèvement du cours automatiquement si pas encore enregistré
    if (!$completion) {
        $cc = new stdClass();
        $cc->userid = $userid;
        $cc->course = $courseid;
        $cc->timeenrolled = $now - 86400;
        $cc->timestarted = $now - 3600;
        $cc->timecompleted = $now;
        $cc->reaggregate = 0;
        $DB->insert_record('course_completions', $cc);
    } else if (empty($completion->timecompleted)) {
        $completion->timecompleted = $now;
        $DB->update_record('course_completions', $completion);
    }

    // 3. Délivrer l'attestation personnalisée (customcert) automatiquement
    $cert = $DB->get_record('customcert', ['course' => $courseid]);
    $issued_cert = null;
    if ($cert) {
        $existing_issue = $DB->get_record('customcert_issues', ['customcertid' => $cert->id, 'userid' => $userid]);
        if (!$existing_issue) {
            // Générer un code unique à 10 caractères (A-Z, 2-9)
            $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            do {
                $code = '';
                for ($i = 0; $i < 10; $i++) {
                    $code .= $chars[random_int(0, strlen($chars) - 1)];
                }
            } while ($DB->record_exists('customcert_issues', ['code' => $code]));

            $new_issue = new stdClass();
            $new_issue->customcertid = $cert->id;
            $new_issue->userid = $userid;
            $new_issue->code = $code;
            $new_issue->emailed = 0;
            $new_issue->timecreated = $now;
            $new_issue->id = $DB->insert_record('customcert_issues', $new_issue);
            $issued_cert = $new_issue;
        } else {
            $issued_cert = $existing_issue;
        }
    }

    // 4. Délivrer le badge numérique automatiquement
    $course = $DB->get_record('course', ['id' => $courseid]);
    $cat = $DB->get_record('course_categories', ['id' => $course->category]);
    $domain_name = $cat ? $cat->name : 'Numérique';
    $level_name = in_array($course->fullname, ['Bronze', 'Argent', 'Or']) ? $course->fullname : 'Bronze';

    // Trouver ou créer le badge officiel pour ce cours
    $badge = $DB->get_record('badge', ['courseid' => $courseid, 'status' => 2]);
    if (!$badge) {
        $b = new stdClass();
        $b->name = "Badge " . $level_name . " — " . $domain_name;
        $b->description = "Atteste de la validation des compétences numériques du cours " . $domain_name . " (" . $level_name . ").";
        $b->timecreated = $now;
        $b->timemodified = $now;
        $b->usercreated = 2; // admin
        $b->usermodified = 2;
        $b->issuername = "MTDPCE — PACTDIGITAL-BF";
        $b->issuerurl = "https://pactdigital.gov.bf";
        $b->issuercontact = "contact@pactdigital.gov.bf";
        $b->expiredate = null;
        $b->expireperiod = null;
        $b->type = 2; // Course badge
        $b->courseid = $courseid;
        $b->message = "Félicitations pour l'obtention de votre badge numérique PACTDIGITAL-BF !";
        $b->messagesubject = "Nouveau badge obtenu : " . $b->name;
        $b->attachment = 1;
        $b->notification = 1;
        $b->status = 2; // Active
        $b->nextcron = null;
        $b->version = "1.0";
        $b->language = "fr";
        $b->imagecaption = "Badge PACTDIGITAL-BF";
        $b->id = $DB->insert_record('badge', $b);
        $badge = $b;
    }

    // Décerner le badge à l'apprenant s'il ne l'a pas encore reçu
    $issued_badge = $DB->get_record('badge_issued', ['badgeid' => $badge->id, 'userid' => $userid]);
    if (!$issued_badge) {
        $bi = new stdClass();
        $bi->badgeid = $badge->id;
        $bi->userid = $userid;
        $bi->uniquehash = sha1(microtime() . $badge->id . $userid);
        $bi->dateissued = $now;
        $bi->dateexpire = null;
        $bi->visible = 1;
        $bi->issuernotified = $now;
        $bi->id = $DB->insert_record('badge_issued', $bi);
        $issued_badge = $bi;
    }

    return [
        'awarded'     => true,
        'certificate' => $issued_cert,
        'badge'       => $issued_badge,
    ];
}
