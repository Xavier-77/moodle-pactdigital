<?php
/**
 * PACTDIGITAL-BF — Inscription Self-Service avec contrôle d'âge
 *
 * Endpoint AJAX pour :
 * 1. Vérifier que l'apprenant a l'âge minimum requis (basé sur l'année de naissance)
 * 2. Inscrire l'apprenant à un cours en une action
 * 
 * Usage : POST /local/pactdigital/enrol_ajax.php
 * Params : courseid (int), birth_year (int), sesskey (string)
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/enrollib.php');

header('Content-Type: application/json; charset=utf-8');

// ── Vérifications de sécurité ─────────────────────────────────────────────────
if (!isloggedin() || isguestuser()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour vous inscrire.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// Vérification du sesskey anti-CSRF
$sesskey    = required_param('sesskey', PARAM_RAW);
$courseid   = required_param('courseid', PARAM_INT);
$birth_year = optional_param('birth_year', 0, PARAM_INT);

if (!confirm_sesskey($sesskey)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Session invalide. Veuillez recharger la page.']);
    exit;
}

// ── Âge minimum : 18 ans ─────────────────────────────────────────────────────
define('PACT_MIN_AGE', 18);

$current_year = (int) date('Y');
if ($birth_year > 0) {
    $age = $current_year - $birth_year;
    if ($age < PACT_MIN_AGE) {
        echo json_encode([
            'success' => false,
            'message' => "Vous devez avoir au moins " . PACT_MIN_AGE . " ans pour vous inscrire. Selon l'année que vous avez indiquée ({$birth_year}), vous n'avez que {$age} ans.",
            'error_code' => 'AGE_RESTRICTION'
        ]);
        exit;
    }
    if ($birth_year < 1900 || $birth_year > $current_year) {
        echo json_encode([
            'success' => false,
            'message' => "L'année de naissance indiquée ({$birth_year}) semble incorrecte.",
            'error_code' => 'INVALID_YEAR'
        ]);
        exit;
    }
}

// ── Vérification du cours ─────────────────────────────────────────────────────
$course = $DB->get_record('course', ['id' => $courseid, 'visible' => 1]);
if (!$course || $courseid <= 1) {
    echo json_encode(['success' => false, 'message' => 'Formation introuvable ou non disponible.']);
    exit;
}

// ── Vérifier si déjà inscrit ──────────────────────────────────────────────────
if (is_enrolled(context_course::instance($courseid), $USER->id)) {
    echo json_encode([
        'success'    => true,
        'already'    => true,
        'message'    => 'Vous êtes déjà inscrit à cette formation.',
        'course_url' => (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false)
    ]);
    exit;
}

// ── Trouver la méthode d'inscription "guest" ou "self" ────────────────────────
$enrol_instances = enrol_get_instances($courseid, true);
$enrol_plugin    = null;
$enrol_instance  = null;

// Chercher en priorité : self-enrolment, puis guest, puis manual
$priority = ['self', 'guest', 'manual'];
foreach ($priority as $method) {
    foreach ($enrol_instances as $instance) {
        if ($instance->enrol === $method) {
            $plugin = enrol_get_plugin($method);
            if ($plugin) {
                $enrol_plugin   = $plugin;
                $enrol_instance = $instance;
                break 2;
            }
        }
    }
}

if (!$enrol_plugin || !$enrol_instance) {
    // Pas de méthode d'auto-inscription — utiliser l'enrôlement manuel comme fallback
    $manual_plugin = enrol_get_plugin('manual');
    if ($manual_plugin) {
        // Chercher ou créer une instance manuelle
        foreach ($enrol_instances as $instance) {
            if ($instance->enrol === 'manual') {
                $enrol_plugin   = $manual_plugin;
                $enrol_instance = $instance;
                break;
            }
        }
    }
    
    if (!$enrol_plugin) {
        echo json_encode([
            'success' => false,
            'message' => 'Aucune méthode d\'inscription disponible pour cette formation. Contactez un administrateur.'
        ]);
        exit;
    }
}

// ── Effectuer l'inscription ───────────────────────────────────────────────────
try {
    // Pour self-enrolment : vérifier si une clé d'inscription est requise
    if ($enrol_instance->enrol === 'self' && !empty($enrol_instance->password)) {
        // Cours avec mot de passe : rediriger vers la page standard
        echo json_encode([
            'success'    => false,
            'message'    => 'Cette formation nécessite une clé d\'inscription. Vous allez être redirigé.',
            'redirect'   => (new moodle_url('/enrol/index.php', ['id' => $courseid]))->out(false),
            'error_code' => 'PASSWORD_REQUIRED'
        ]);
        exit;
    }
    
    // Récupérer le rôle étudiant par défaut
    $default_role = (int) $DB->get_field('enrol', 'roleid', ['id' => $enrol_instance->id]);
    if (!$default_role) {
        $default_role = (int) $DB->get_field('role', 'id', ['shortname' => 'student']);
    }
    
    $enrol_plugin->enrol_user($enrol_instance, $USER->id, $default_role);
    
    // Journaliser l'événement
    $event = \core\event\user_enrolment_created::create([
        'objectid'      => $USER->id,
        'courseid'      => $courseid,
        'context'       => context_course::instance($courseid),
        'relateduserid' => $USER->id,
        'other'         => ['enrol' => $enrol_instance->enrol]
    ]);
    $event->trigger();
    
    echo json_encode([
        'success'    => true,
        'already'    => false,
        'message'    => '✅ Inscription réussie ! Vous êtes maintenant inscrit à la formation "' . htmlspecialchars($course->fullname) . '".',
        'course_url' => (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false),
        'course_name'=> $course->fullname,
    ]);
    
} catch (\Throwable $e) {
    error_log('[PACTDIGITAL] Erreur inscription cours ' . $courseid . ' pour user ' . $USER->id . ': ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer ou contacter un administrateur.'
    ]);
}
