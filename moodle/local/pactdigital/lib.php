<?php
/**
 * Hooks into Moodle navigation to add the dashboard link.
 */

defined('MOODLE_INTERNAL') || die();

function local_pactdigital_extend_navigation_site_administration(navigation_node $navigation) {
    $context = context_system::instance();
    if (!has_capability('moodle/site:config', $context) && !has_capability('local/pactdigital:viewdashboard', $context)) {
        return;
    }
    $url = new moodle_url('/local/pactdigital/dashboard.php');
    $navigation->add(
        '📊 Tableau de bord PACTDIGITAL',
        $url,
        navigation_node::TYPE_CUSTOM,
        null,
        'pactdigital_dashboard',
        new pix_icon('i/report', '')
    );
}
