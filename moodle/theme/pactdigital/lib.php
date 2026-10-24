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
    
    // Charte graphique de l'administration burkinabè v2.0 (MTDPCE)
    $scss .= '$primary: #00843b;' . "\n";   // Vert drapeau (--vert-500 / --action)
    $scss .= '$secondary: #ffed00;' . "\n"; // Or drapeau (--or-300 / --relief)
    $scss .= '$success: #00843b;' . "\n";   // Succès (--vert-500)
    $scss .= '$info: #2a6d9b;' . "\n";      // Bleu administratif (--bleu-500)
    $scss .= '$warning: #b07800;' . "\n";   // Ocre d'alerte (--ocre-500)
    $scss .= '$danger: #e30613;' . "\n";    // Rouge drapeau (--rouge-500 / --accent)
    $scss .= '$light: #f7f5f2;' . "\n";     // Neutre chaud latérite (--neutre-50)
    $scss .= '$dark: #211d1a;' . "\n";      // Neutre sombre chaud (--neutre-900)
    $scss .= '$body-bg: #ffffff;' . "\n";
    $scss .= '$body-color: #211d1a;' . "\n";
    $scss .= '$font-family-sans-serif: "Inter", "Segoe UI", system-ui, -apple-system, sans-serif;' . "\n";
    $scss .= '$headings-font-family: "Archivo", "Segoe UI", system-ui, -apple-system, sans-serif;' . "\n";

    
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

/**
 * Redirige automatiquement l'inscription native Moodle vers la page PACTDIGITAL
 * où l'e-mail est 100% facultatif et l'activation immédiate.
 */
function theme_pactdigital_page_init(\moodle_page $page) {
    global $CFG, $USER, $DB;
    $path = parse_url($page->url->out(false), PHP_URL_PATH);
    if ($path && strpos($path, '/login/signup.php') !== false) {
        redirect(new moodle_url('/local/pactdigital/signup.php'));
    }

    // Détection des rôles pour affichage contextuel des boutons de la barre de navigation
    if (isloggedin() && !isguestuser()) {
        if (is_siteadmin($USER->id)) {
            $page->add_body_class('pact-role-admin');
        } else if (has_capability('local/pactdigital:viewdashboard', context_system::instance())
                && has_capability('moodle/course:update', context_system::instance())) {
            // Gestionnaire (rôle manager au niveau site) : accès gestion, dashboard et espace formateur.
            $page->add_body_class('pact-role-manager');
        } else {
            $trainer_count = $DB->count_records_sql(
                "SELECT COUNT(*) FROM {role_assignments} WHERE userid = :uid AND roleid IN (3, 4, 9, 10)",
                ['uid' => $USER->id]
            );
            if ($trainer_count > 0) {
                $page->add_body_class('pact-role-trainer');
            } else {
                $page->add_body_class('pact-role-learner');
            }
        }
    } else {
        $page->add_body_class('pact-role-guest');
    }
}

/**
 * Injecte l'overlay minuteur PACT sur les pages d'examen quiz,
 * et redirige vers exam_result.php après soumission.
 * Hook appelé par Moodle lors du rendu du pied de page.
 */
function theme_pactdigital_before_footer() {
    global $PAGE, $CFG, $DB, $USER;

    // Détecter si on est sur une page de tentative de quiz
    $url_path = parse_url($PAGE->url->out(false), PHP_URL_PATH);
    $is_quiz_attempt = (strpos($url_path, '/mod/quiz/attempt.php') !== false);
    $is_quiz_summary = (strpos($url_path, '/mod/quiz/processattempt.php') !== false);
    
    if (!$is_quiz_attempt) {
        return;
    }

    // Récupérer l'ID de tentative
    $attemptid = optional_param('attempt', 0, PARAM_INT);
    if (!$attemptid) return;

    $attempt = $DB->get_record('quiz_attempts', ['id' => $attemptid, 'userid' => $USER->id]);
    if (!$attempt) return;

    $quiz = $DB->get_record('quiz', ['id' => $attempt->quiz]);
    if (!$quiz) return;

    $time_limit   = (int) $quiz->timelimit;
    $time_started = (int) $attempt->timestart;
    $result_url   = (new moodle_url('/local/pactdigital/exam_result.php', ['attemptid' => $attemptid]))->out(false);
    $sesskey_val  = sesskey();

    // Calculer le temps restant
    $now         = time();
    $elapsed     = $now - $time_started;
    $remaining   = ($time_limit > 0) ? max(0, $time_limit - $elapsed) : -1;

    $nb_questions = (int) $DB->count_records('quiz_slots', ['quizid' => $quiz->id]);

    ?>
    <style>
    /* ═══ PACT EXAM TIMER OVERLAY ═══════════════════════════════════════════ */
    #pact-exam-overlay {
        position: fixed;
        top: 16px;
        right: 16px;
        z-index: 99990;
        background: var(--vert-900, #032e16);
        color: var(--neutre-0, #ffffff);
        border-radius: var(--rayon-xl, 16px);
        padding: 1rem 1.3rem;
        min-width: 200px;
        box-shadow: var(--ombre-xl, 0 10px 30px rgba(33,29,26,0.4));
        font-family: var(--police-texte, 'Inter', 'Segoe UI', system-ui, sans-serif);
        border: 1px solid rgba(255,255,255,0.14);
    }
    #pact-exam-overlay .overlay-title {
        font-family: var(--police-titre, 'Archivo', sans-serif);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        opacity: 0.8;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    #pact-timer-display {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: 2px;
        font-family: var(--police-mono, ui-monospace, 'Cascadia Mono', monospace);
        color: var(--or-300, #ffed00);
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    #pact-timer-display.warning { color: var(--or-400, #e8d400); animation: pulse 1s infinite; }
    #pact-timer-display.danger  { color: var(--rouge-400, #f0636e); animation: pulse 0.5s infinite; }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.6; }
    }
    .overlay-info-row {
        font-size: 0.75rem;
        opacity: 0.85;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 0.25rem;
    }
    .overlay-save-status {
        font-size: 0.72rem;
        color: var(--vert-200, #a8dbbc);
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 5px;
        opacity: 0;
        transition: opacity 0.4s;
    }
    .overlay-save-status.visible { opacity: 1; }
    #pact-progress-dots {
        display: flex;
        gap: 3px;
        flex-wrap: wrap;
        margin-top: 0.6rem;
    }
    .progress-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.25);
    }
    .progress-dot.answered { background: var(--vert-300, #74c296); }

    /* ── Notification sauvegarde ─────────────────────────────────────────── */
    #pact-save-notif {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: var(--vert-800, #04411f);
        color: var(--vert-100, #d3eddd);
        padding: 10px 18px;
        border-radius: var(--rayon-lg, 10px);
        font-size: 0.88rem;
        font-weight: 600;
        z-index: 99999;
        display: none;
        align-items: center;
        gap: 8px;
        box-shadow: var(--ombre-lg, 0 6px 20px rgba(33,29,26,0.3));
        border-left: 4px solid var(--vert-500, #00843b);
        animation: slideRight 0.25s ease-out;
    }
    @keyframes slideRight {
        from { opacity: 0; transform: translateX(20px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    </style>

    <!-- Overlay minuteur -->
    <div id="pact-exam-overlay">
        <div class="overlay-title">⏱️ Examen en cours</div>
        <div id="pact-timer-display">
            <?php if ($remaining >= 0): ?>
                <?= gmdate('H:i:s', $remaining) ?>
            <?php else: ?>
                ∞
            <?php endif; ?>
        </div>
        <div class="overlay-info-row">❓ <?= $nb_questions ?> questions</div>
        <div class="overlay-info-row">🎯 Seuil : 70%</div>
        <div class="overlay-save-status" id="save-status-indicator">
            💾 Réponses sauvegardées
        </div>
        <div id="pact-progress-dots">
            <?php for ($i = 0; $i < $nb_questions; $i++): ?>
            <div class="progress-dot" id="dot-<?= $i ?>"></div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Notification de sauvegarde -->
    <div id="pact-save-notif">💾 Réponses sauvegardées automatiquement</div>

    <script>
    (function() {
        const REMAINING_SECS = <?= $remaining ?>;
        const HAS_TIMER      = REMAINING_SECS >= 0;
        const NB_QUESTIONS   = <?= $nb_questions ?>;
        const RESULT_URL     = '<?= addslashes($result_url) ?>';
        
        // ── Minuteur ─────────────────────────────────────────────────────────
        let secsLeft = REMAINING_SECS;
        const timerEl = document.getElementById('pact-timer-display');

        function formatTime(s) {
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            const sec = s % 60;
            return (h > 0 ? String(h).padStart(2, '0') + ':' : '')
                + String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
        }

        if (HAS_TIMER && timerEl) {
            const tick = setInterval(() => {
                secsLeft--;
                if (secsLeft <= 0) {
                    clearInterval(tick);
                    timerEl.textContent = '00:00';
                    timerEl.classList.add('danger');
                    return;
                }
                timerEl.textContent = formatTime(secsLeft);

                // Couleurs d'alerte
                timerEl.classList.remove('warning', 'danger');
                if (secsLeft <= 120) timerEl.classList.add('danger');
                else if (secsLeft <= 300) timerEl.classList.add('warning');
            }, 1000);
        }

        // ── Sauvegarde automatique toutes les 30 secondes ─────────────────────
        // Moodle gère déjà la sauvegarde automatique des quiz, mais on affiche la notif
        function triggerAutoSave() {
            // Déclencher le save natif de Moodle si disponible
            if (typeof M !== 'undefined' && M.mod_quiz && M.mod_quiz.timer) {
                // Moodle's quiz timer handles auto-save
            }
            
            // Afficher la notification visuelle
            const notif = document.getElementById('pact-save-notif');
            const indicator = document.getElementById('save-status-indicator');
            if (notif) {
                notif.style.display = 'flex';
                setTimeout(() => { notif.style.display = 'none'; }, 2500);
            }
            if (indicator) {
                indicator.classList.add('visible');
                setTimeout(() => { indicator.classList.remove('visible'); }, 3000);
            }

            // Marquer les questions répondues
            updateProgressDots();
        }

        // Sauvegarde toutes les 30 secondes
        setInterval(triggerAutoSave, 30000);
        // Première sauvegarde après 5 secondes
        setTimeout(triggerAutoSave, 5000);

        // ── Mise à jour des points de progression ─────────────────────────────
        function updateProgressDots() {
            const forms = document.querySelectorAll('.que');
            forms.forEach((q, i) => {
                const dot = document.getElementById('dot-' + i);
                if (!dot) return;
                // Vérifie si une réponse a été donnée
                const inputs = q.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea:not(:empty), input[type="text"]:not([value=""])');
                if (inputs.length > 0) {
                    dot.classList.add('answered');
                }
            });
        }

        // Mettre à jour les dots au changement
        document.addEventListener('change', updateProgressDots);
        document.addEventListener('input', updateProgressDots);

        // ── Redirection après soumission vers exam_result ─────────────────────
        // Observer la soumission du quiz pour rediriger vers notre page de résultat
        const submitBtns = document.querySelectorAll('input[type="submit"][name="finishattempt"], button[name="finishattempt"], .submitbtns input[type="submit"]');
        submitBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Stocker l'URL de résultat pour la redirection post-soumission
                sessionStorage.setItem('pact_exam_result_url', RESULT_URL);
            });
        });

        // Si on vient d'une soumission de quiz (page de résumé Moodle)
        const storedResultUrl = sessionStorage.getItem('pact_exam_result_url');
        if (storedResultUrl && window.location.href.includes('processattempt')) {
            sessionStorage.removeItem('pact_exam_result_url');
            window.location.href = storedResultUrl;
        }

        updateProgressDots();
    })();
    </script>
    <?php
}
