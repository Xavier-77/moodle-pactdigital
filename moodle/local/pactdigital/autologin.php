<?php
require_once(__DIR__ . '/../../config.php');

$username = optional_param('user', '', PARAM_USERNAME);
$redirect = optional_param('redirect', '/local/pactdigital/apprenant.php', PARAM_LOCALURL);

if (!empty($username)) {
    $user = $DB->get_record('user', ['username' => $username, 'deleted' => 0], '*', MUST_EXIST);
    complete_user_login($user);
}

redirect(new moodle_url($redirect));
