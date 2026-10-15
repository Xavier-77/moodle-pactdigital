<?php
defined('MOODLE_INTERNAL') || die();

global $PAGE, $CFG;

// Rediriger toute visite sur /login/signup.php vers l'inscription PACTDIGITAL
// où l'e-mail est facultatif et sans confirmation bloquante par courriel
$current_uri = $_SERVER['REQUEST_URI'] ?? '';
$page_url    = isset($PAGE) ? $PAGE->url->out(false) : '';
if (strpos($current_uri, 'signup.php') !== false || strpos($page_url, 'signup.php') !== false) {
    redirect(new moodle_url('/local/pactdigital/signup.php'));
}

$bodyattributes = $OUTPUT->body_attributes(['pactdigital-split-login']);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'armoiries_url' => $OUTPUT->image_url('armoiries', 'theme_pactdigital')->out(),
    'logo_min_url' => $OUTPUT->image_url('logo_min', 'theme_pactdigital')->out()
];

echo $OUTPUT->render_from_template('theme_pactdigital/login', $templatecontext);
