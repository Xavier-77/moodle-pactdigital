<?php
defined('MOODLE_INTERNAL') || die();

$bodyattributes = $OUTPUT->body_attributes(['pactdigital-split-login']);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'armoiries_url' => $OUTPUT->image_url('armoiries', 'theme_pactdigital')->out(),
    'logo_min_url' => $OUTPUT->image_url('logo_min', 'theme_pactdigital')->out()
];

echo $OUTPUT->render_from_template('theme_pactdigital/login', $templatecontext);
