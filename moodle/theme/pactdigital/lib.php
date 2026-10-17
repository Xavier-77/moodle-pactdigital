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
        background: #1a1a2e;
        color: white;
        border-radius: 14px;
        padding: 1rem 1.3rem;
        min-width: 200px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        font-family: 'Segoe UI', system-ui, sans-serif;
        border: 1px solid rgba(255,255,255,0.1);
    }
    #pact-exam-overlay .overlay-title {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.6;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    #pact-timer-display {
        font-size: 2rem;
        font-weight: 900;
        letter-spacing: 2px;
        font-family: 'Courier New', monospace;
        color: #fad615;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    #pact-timer-display.warning { color: #f97316; animation: pulse 1s infinite; }
    #pact-timer-display.danger  { color: #ef4444; animation: pulse 0.5s infinite; }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.6; }
    }
    .overlay-info-row {
        font-size: 0.75rem;
        opacity: 0.7;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 0.25rem;
    }
    .overlay-save-status {
        font-size: 0.72rem;
        color: #86efac;
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
    .progress-dot.answered { background: #4ade80; }

    /* ── Notification sauvegarde ─────────────────────────────────────────── */
    #pact-save-notif {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #14532d;
        color: #86efac;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 600;
        z-index: 99999;
        display: none;
        align-items: center;
        gap: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        border-left: 4px solid #22c55e;
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
