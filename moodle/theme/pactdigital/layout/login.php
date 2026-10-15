<?php
defined('MOODLE_INTERNAL') || die();

global $PAGE, $CFG;

// Rediriger uniquement les accès à /login/signup.php (natif Moodle) vers /local/pactdigital/signup.php
$current_uri = $_SERVER['REQUEST_URI'] ?? '';
$page_url    = isset($PAGE) ? $PAGE->url->out(false) : '';
if ((strpos($current_uri, '/login/signup.php') !== false || strpos($page_url, '/login/signup.php') !== false)
    && strpos($current_uri, '/local/pactdigital/') === false) {
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
