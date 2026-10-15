<?php
defined('MOODLE_INTERNAL') || die();

function theme_pactdigital_get_main_scss_content($theme) {
    global $CFG;
    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_pactdigital', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    return $scss;
}

function theme_pactdigital_get_pre_scss($theme) {
    global $CFG;
    $scss = '';
    $configurable = [
        // Variables to configure based on theme settings could go here
    ];

    $configurable = array_map(function($value) {
        return !empty($value);
    }, $configurable);

    foreach ($configurable as $var => $configured) {
        if ($configured) {
            $scss .= '$' . $var . ': ' . $theme->settings->{$var} . ";\n";
        }
    }
    
    // Inject Partis Digital Logo Colors (Burkina Faso flag colors)
    $scss .= '$primary: #129b40;' . "\n";   // Green
    $scss .= '$secondary: #fad615;' . "\n"; // Yellow
    $scss .= '$danger: #d02027;' . "\n";    // Red
    $scss .= '$dark: #000000;' . "\n";      // Black

    
    // Add standard raw SCSS from Boost
    if (function_exists('theme_boost_get_pre_scss')) {
        $scss .= theme_boost_get_pre_scss($theme);
    }
    
    return $scss;
}

function theme_pactdigital_get_extra_scss($theme) {
    global $CFG;
    $scss = '';
    
    if (function_exists('theme_boost_get_extra_scss')) {
        $scss .= theme_boost_get_extra_scss($theme);
    }

    // Inject our custom CSS for the theme
    $content = file_get_contents($CFG->dirroot . '/theme/pactdigital/scss/pactdigital.scss');
    if ($content !== false) {
        $scss .= "\n" . $content;
    }

    return $scss;
}
