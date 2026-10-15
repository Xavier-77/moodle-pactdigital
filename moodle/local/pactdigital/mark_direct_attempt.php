<?php
/**
 * PACTDIGITAL-BF — Marquage d'une tentative directe à l'examen
 *
 * Endpoint AJAX appelé avant de lancer l'examen en mode "direct"
 * (sans avoir suivi les séquences). Incrémente le compteur en préférence utilisateur.
 *
 * POST params: sesskey, courseid, quizid
 */

require_once(__DIR__ . '/../../config.php');

header('Content-Type: application/json; charset=utf-8');

if (!isloggedin() || isguestuser()) {
    http_response_code(401);
    echo json_encode(['success' => false]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

$sesskey = required_param('sesskey', PARAM_RAW);
$courseid = required_param('courseid', PARAM_INT);
$quizid   = required_param('quizid', PARAM_INT);

if (!confirm_sesskey($sesskey)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Session invalide']);
    exit;
}

// Vérifier que l'utilisateur est inscrit au cours
$context = context_course::instance($courseid);
if (!is_enrolled($context, $USER->id)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non inscrit']);
    exit;
}

// Incrémenter le compteur de tentatives directes
$pref_key  = 'pact_direct_exam_' . $quizid;
$current   = (int) get_user_preferences($pref_key, 0);
$new_count = $current + 1;
set_user_preference($pref_key, $new_count);

echo json_encode([
    'success'         => true,
    'direct_attempts' => $new_count,
    'direct_max'      => 2,
    'blocked'         => ($new_count >= 2),
]);
