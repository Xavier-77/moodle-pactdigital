<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');

/**
 * Prépare et structure toutes les données nécessaires à la page d'accueil moderne (style Udemy)
 * 
 * @return array Contexte complet pour le template frontpage
 */
function pactdigital_get_frontpage_context() {
    global $DB, $CFG, $USER;

    $is_logged_in = isloggedin() && !isguestuser();
    $wwwroot = $CFG->wwwroot;

    // 1. Catégories racines officielles (Piliers)
    $cat_meta = [
        9 => [
            'slug'        => 'fondamentales',
            'name'        => 'Compétences numériques fondamentales',
            'short'       => 'Fondamentales',
            'icon'        => '💻',
            'color'       => '#129b40',
            'desc'        => 'Bureautique, navigation internet, communication numérique et sécurité essentielle au quotidien.',
            'badge'       => '9 domaines • 27 cours'
        ],
        10 => [
            'slug'        => 'manageriales',
            'name'        => 'Compétences numériques managériales',
            'short'       => 'Managériales',
            'icon'        => '📊',
            'color'       => '#0284c7',
            'desc'        => 'Outils collaboratifs, gouvernance numérique, management de projets et transition digitale du service public.',
            'badge'       => '3 domaines • 9 cours'
        ],
        11 => [
            'slug'        => 'sectorielles',
            'name'        => 'Compétences numériques sectorielles',
            'short'       => 'Sectorielles',
            'icon'        => '🏭',
            'color'       => '#f59e0b',
            'desc'        => 'Usages spécialisés par métier : commerce et vente en ligne, animation et formation à l\'aide du numérique.',
            'badge'       => '2 domaines • 6 cours'
        ],
        12 => [
            'slug'        => 'emergentes',
            'name'        => 'Compétences numériques émergentes',
            'short'       => 'Émergentes',
            'icon'        => '🚀',
            'color'       => '#7c3aed',
            'desc'        => 'Technologies de pointe : Intelligence Artificielle, Blockchain, Réalité virtuelle, IoT et Informatique quantique.',
            'badge'       => '6 domaines • 18 cours'
        ],
    ];

    // 2. Récupération de tous les cours avec métadonnées et images de couverture
    $sql_courses = "
        SELECT c.*,
               cc.name AS domain_name,
               cc.id AS domain_id,
               cc.parent AS root_cat_id
        FROM {course} c
        JOIN {course_categories} cc ON cc.id = c.category
        WHERE c.id > 1 AND c.visible = 1
        ORDER BY c.sortorder ASC
    ";
    $all_courses = $DB->get_records_sql($sql_courses);

    $courses_by_cat = [
        'featured'      => [],
        'fondamentales' => [],
        'manageriales'  => [],
        'sectorielles'  => [],
        'emergentes'    => [],
    ];

    $domain_counts = [];

    foreach ($all_courses as $c) {
        $root_id = (int)$c->root_cat_id;
        $slug = $cat_meta[$root_id]['slug'] ?? 'fondamentales';

        // Détection du niveau
        $raw_title = trim($c->fullname);
        $level_info = [
            'level' => 'bronze',
            'label' => 'Bronze',
            'icon'  => '',
            'cls'   => 'level-bronze'
        ];
        if (stripos($raw_title, 'argent') !== false || $raw_title === 'Diamant') {
            $level_info = ['level' => 'argent', 'label' => 'Argent', 'icon' => '', 'cls' => 'level-argent'];
        } else if (stripos($raw_title, 'or') !== false) {
            $level_info = ['level' => 'or', 'label' => 'Or', 'icon' => '', 'cls' => 'level-or'];
        }

        // Titre d'affichage harmonisé
        if (in_array($raw_title, ['Bronze', 'Argent', 'Or', 'Diamant'])) {
            $display_title = $c->domain_name . ' — ' . $level_info['label'];
        } else {
            $display_title = $c->fullname;
        }

        // Image de couverture
        $img_url = '';
        try {
            $course_element = new core_course_list_element($c);
            foreach ($course_element->get_course_overviewfiles() as $file) {
                if ($file->is_valid_image()) {
                    $img_url = moodle_url::make_pluginfile_url(
                        $file->get_contextid(),
                        $file->get_component(),
                        $file->get_filearea(),
                        null,
                        $file->get_filepath(),
                        $file->get_filename()
                    )->out(false);
                    break;
                }
            }
        } catch (\Exception $e) {
            $img_url = '';
        }

        // Résumé raccourci
        $clean_summary = strip_tags($c->summary ?? '');
        if (empty($clean_summary)) {
            $clean_summary = "Formation officielle certifiante du programme national PACTDIGITAL-BF au niveau {$level_info['label']}.";
        }
        $summary_short = mb_strimwidth($clean_summary, 0, 100, '…');

        $course_card = [
            'id'            => $c->id,
            'title'         => $display_title,
            'domain_name'   => $c->domain_name,
            'summary'       => $summary_short,
            'level'         => $level_info['level'],
            'level_label'   => $level_info['label'],
            'level_icon'    => '',
            'level_cls'     => $level_info['cls'],
            'img_url'       => $img_url,
            'view_url'      => (new moodle_url('/course/view.php', ['id' => $c->id]))->out(false),
            'welcome_url'   => (new moodle_url('/local/pactdigital/course_welcome.php', ['id' => $c->id]))->out(false),
            'catalogue_url' => (new moodle_url('/local/pactdigital/catalogue.php', ['domain' => $slug]))->out(false),
            'cat_color'     => $cat_meta[$root_id]['color'] ?? '#129b40',
            'cat_icon'      => '',
        ];


        // Remplir les onglets de catégories
        if (count($courses_by_cat[$slug]) < 8) {
            $courses_by_cat[$slug][] = $course_card;
        }

        // Sélection des cours "À la une" (mélange équilibré de niveaux Bronze et Argent)
        if (count($courses_by_cat['featured']) < 8 && in_array($level_info['level'], ['bronze', 'argent'])) {
            $courses_by_cat['featured'][] = $course_card;
        }

        $domain_counts[$c->domain_name] = true;
    }

    // Structure des onglets pour le template (sans icônes)
    $tabs = [
        [
            'id'          => 'tab-featured',
            'target'      => 'pane-featured',
            'label'       => 'À la une',
            'active'      => true,
            'title'       => 'Formations recommandées pour démarrer',
            'desc'        => 'Une sélection des formations les plus consultées pour acquérir rapidement les compétences numériques indispensables.',
            'courses'     => $courses_by_cat['featured'],
            'explore_url' => (new moodle_url('/local/pactdigital/catalogue.php'))->out(false),
            'explore_lbl' => 'Explorer tout le catalogue (60 cours)',
        ],
        [
            'id'          => 'tab-fondamentales',
            'target'      => 'pane-fondamentales',
            'label'       => 'Fondamentales',
            'active'      => false,
            'title'       => $cat_meta[9]['name'],
            'desc'        => $cat_meta[9]['desc'],
            'courses'     => $courses_by_cat['fondamentales'],
            'explore_url' => (new moodle_url('/local/pactdigital/catalogue.php', ['domain' => 'fondamentales']))->out(false),
            'explore_lbl' => 'Voir les 27 cours Fondamentaux',
        ],
        [
            'id'          => 'tab-manageriales',
            'target'      => 'pane-manageriales',
            'label'       => 'Managériales',
            'active'      => false,
            'title'       => $cat_meta[10]['name'],
            'desc'        => $cat_meta[10]['desc'],
            'courses'     => $courses_by_cat['manageriales'],
            'explore_url' => (new moodle_url('/local/pactdigital/catalogue.php', ['domain' => 'manageriales']))->out(false),
            'explore_lbl' => 'Voir les 9 cours Managériaux',
        ],
        [
            'id'          => 'tab-sectorielles',
            'target'      => 'pane-sectorielles',
            'label'       => 'Sectorielles',
            'active'      => false,
            'title'       => $cat_meta[11]['name'],
            'desc'        => $cat_meta[11]['desc'],
            'courses'     => $courses_by_cat['sectorielles'],
            'explore_url' => (new moodle_url('/local/pactdigital/catalogue.php', ['domain' => 'sectorielles']))->out(false),
            'explore_lbl' => 'Voir les 6 cours Sectoriels',
        ],
        [
            'id'          => 'tab-emergentes',
            'target'      => 'pane-emergentes',
            'label'       => 'Émergentes',
            'active'      => false,
            'title'       => $cat_meta[12]['name'],
            'desc'        => $cat_meta[12]['desc'],
            'courses'     => $courses_by_cat['emergentes'],
            'explore_url' => (new moodle_url('/local/pactdigital/catalogue.php', ['domain' => 'emergentes']))->out(false),
            'explore_lbl' => 'Voir les 18 cours Émergents',
        ],
    ];


    // Grille des 4 piliers thématiques
    $pillars = [];
    foreach ($cat_meta as $cid => $meta) {
        $pillars[] = [
            'id'    => $cid,
            'name'  => $meta['name'],
            'short' => $meta['short'],
            'slug'  => $meta['slug'],
            'icon'  => $meta['icon'],
            'color' => $meta['color'],
            'desc'  => $meta['desc'],
            'badge' => $meta['badge'],
            'url'   => (new moodle_url('/local/pactdigital/catalogue.php', ['domain' => $meta['slug']]))->out(false),
        ];
    }

    return [
        'is_logged_in'    => $is_logged_in,
        'user_fullname'   => $is_logged_in ? fullname($USER) : '',
        'stats' => [
            'courses'     => count($all_courses),
            'domains'     => count($domain_counts),
            'levels'      => 3,
            'free'        => '100% Gratuit',
        ],
        'tabs'            => $tabs,
        'pillars'         => $pillars,
        'urls' => [
            'signup'      => (new moodle_url('/local/pactdigital/signup.php'))->out(false),
            'login'       => (new moodle_url('/login/index.php'))->out(false),
            'catalogue'   => (new moodle_url('/local/pactdigital/catalogue.php'))->out(false),
            'verify'      => (new moodle_url('/local/pactdigital/verify.php'))->out(false),
            'formateur'   => (new moodle_url('/local/pactdigital/formateur.php'))->out(false),
            'apprenant'   => (new moodle_url('/local/pactdigital/apprenant.php'))->out(false),
            'dashboard'   => (new moodle_url('/my/'))->out(false),
            'logout'      => (new moodle_url('/login/logout.php', ['sesskey' => sesskey()]))->out(false),
        ],
    ];
}
