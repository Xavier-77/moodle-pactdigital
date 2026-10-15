<?php
/**
 * PACTDIGITAL-BF — Résultat immédiat après examen (Quiz)
 *
 * Page de résultat affiché immédiatement après la soumission d'une tentative de quiz.
 * Montre : score, statut (réussi/échoué), tentatives restantes, badge obtenu.
 *
 * Cette page est destinée à être affichée automatiquement par le thème
 * après qu'une tentative est finalisée.
 * URL directe : /local/pactdigital/exam_result.php?attemptid=X
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/gradelib.php');

require_login();

if (isguestuser()) {
    redirect(new moodle_url('/login/index.php'));
}

$attemptid = required_param('attemptid', PARAM_INT);

// ── Récupérer la tentative ────────────────────────────────────────────────────
$sql_attempt = "SELECT qa.id, qa.quiz AS quizid, qa.userid, qa.attempt AS attempt_num,
                       qa.sumgrades, qa.state, qa.timefinish, qa.timestart,
                       q.name AS quizname, q.grade AS maxgrade, q.course AS courseid,
                       q.attempts AS maxattempts,
                       c.fullname AS coursename, c.shortname AS courseshort,
                       cm.id AS cmid
                FROM {quiz_attempts} qa
                JOIN {quiz} q ON q.id = qa.quiz
                JOIN {course} c ON c.id = q.course
                JOIN {course_modules} cm ON cm.instance = q.id
                JOIN {modules} m ON m.id = cm.module AND m.name = 'quiz'
                WHERE qa.id = :attemptid";

$attempt = $DB->get_record_sql($sql_attempt, ['attemptid' => $attemptid]);

if (!$attempt) {
    redirect(new moodle_url('/my'), 'Tentative introuvable.', 3);
}

// Vérification : seul l'apprenant concerné peut voir son résultat
// (sauf admin/formateur)
$context = context_course::instance($attempt->courseid);
$is_trainer = is_siteadmin() || has_capability('moodle/grade:viewall', $context);
if ($attempt->userid != $USER->id && !$is_trainer) {
    redirect(new moodle_url('/my'), 'Accès refusé.', 3);
}

// ── Calculer le score ─────────────────────────────────────────────────────────
$score_pct = ($attempt->maxgrade > 0)
    ? round(($attempt->sumgrades / $attempt->maxgrade) * 100, 1)
    : 0;

$passed = ($score_pct >= 70);

// ── Compter les tentatives totales finies ─────────────────────────────────────
$finished_count = $DB->count_records('quiz_attempts', [
    'quiz'    => $attempt->quizid,
    'userid'  => $attempt->userid,
    'state'   => 'finished',
]);

$attempts_remaining = null;
if ($attempt->maxattempts > 0) {
    $attempts_remaining = max(0, $attempt->maxattempts - $finished_count);
}

$duration_secs = ($attempt->timefinish && $attempt->timestart)
    ? ($attempt->timefinish - $attempt->timestart)
    : 0;

$duration_str = '';
if ($duration_secs > 0) {
    $mins = (int) floor($duration_secs / 60);
    $secs = $duration_secs % 60;
    $duration_str = $mins . ' min' . ($secs > 0 ? ' ' . $secs . ' sec' : '');
}

// URLs utiles
$quiz_url        = (new moodle_url('/mod/quiz/view.php', ['id' => $attempt->cmid]))->out(false);
$course_url      = (new moodle_url('/course/view.php', ['id' => $attempt->courseid]))->out(false);
$welcome_url     = (new moodle_url('/local/pactdigital/course_welcome.php', ['id' => $attempt->courseid]))->out(false);
$my_results_url  = (new moodle_url('/local/pactdigital/apprenant.php'))->out(false);
$review_url      = (new moodle_url('/mod/quiz/review.php', ['attempt' => $attemptid]))->out(false);

$PAGE->set_url(new moodle_url('/local/pactdigital/exam_result.php', ['attemptid' => $attemptid]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title('Résultat de l\'examen — ' . $attempt->quizname);
$PAGE->set_heading($attempt->coursename);

echo $OUTPUT->header();
?>

<style>
/* ═══ EXAM RESULT PAGE — PACTDIGITAL-BF ══════════════════════════════════ */
.result-page {
    max-width: 680px;
    margin: 0 auto;
    padding: 1rem 1.5rem 4rem;
    font-family: 'Segoe UI', system-ui, sans-serif;
    text-align: center;
}

/* ── Score visuel ────────────────────────────────────────────────────── */
.result-hero {
    border-radius: 20px;
    padding: 3rem 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    color: white;
}
.result-hero.pass {
    background: linear-gradient(135deg, #0d7a32 0%, #129b40 60%, #16a34a 100%);
}
.result-hero.fail {
    background: linear-gradient(135deg, #991b1b 0%, #dc2626 60%, #ef4444 100%);
}
.result-hero::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 250px; height: 250px;
    background: rgba(255,255,255,0.07);
    border-radius: 50%;
}
.result-status-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: block;
    position: relative; z-index: 1;
    animation: bounceIn 0.5s ease-out;
}
@keyframes bounceIn {
    0%   { transform: scale(0); opacity: 0; }
    60%  { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(1); }
}
.result-status-text {
    font-size: 1.6rem;
    font-weight: 800;
    margin: 0 0 0.5rem;
    position: relative; z-index: 1;
}
.result-score-big {
    font-size: 4.5rem;
    font-weight: 900;
    line-height: 1;
    color: #fad615;
    margin: 0.5rem 0;
    position: relative; z-index: 1;
}
.result-score-sub {
    font-size: 1rem;
    opacity: 0.85;
    position: relative; z-index: 1;
}
.result-threshold {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 700;
    margin-top: 1rem;
    position: relative; z-index: 1;
}

/* ── Stats cards ─────────────────────────────────────────────────────── */
.result-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 500px) { .result-stats { grid-template-columns: 1fr 1fr; } }
.stat-box {
    background: white;
    border-radius: 12px;
    padding: 1.2rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.stat-icon { font-size: 1.5rem; margin-bottom: 0.3rem; }
.stat-label { font-size: 0.72rem; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.04em; }
.stat-value { font-size: 1.3rem; font-weight: 800; color: #1a1a1a; }

/* ── Badge numérique ─────────────────────────────────────────────────── */
.digital-badge {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 3px solid #fbbf24;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    animation: badgeAppear 0.6s ease-out 0.3s both;
}
@keyframes badgeAppear {
    from { opacity: 0; transform: scale(0.8) rotate(-5deg); }
    to   { opacity: 1; transform: scale(1) rotate(0); }
}
.badge-icon-big { font-size: 4rem; margin-bottom: 0.5rem; }
.badge-title-text { font-size: 1.15rem; font-weight: 800; color: #92400e; }
.badge-subtitle   { font-size: 0.82rem; color: #b45309; margin-top: 0.2rem; }

/* ── Actions ─────────────────────────────────────────────────────────── */
.result-actions {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    margin-bottom: 1.5rem;
}
.btn-result {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0.85rem 1.5rem;
    border-radius: 12px;
    font-size: 0.96rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    border: none;
    font-family: inherit;
}
.btn-result.primary {
    background: linear-gradient(135deg, #129b40, #0d7a32);
    color: white;
}
.btn-result.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(18,155,64,0.3);
    color: white;
}
.btn-result.secondary {
    background: white;
    color: #1565C0;
    border: 2px solid #1565C0;
}
.btn-result.secondary:hover { background: #dbeafe; color: #1565C0; }
.btn-result.outline {
    background: white;
    color: #64748b;
    border: 1.5px solid #e2e8f0;
}
.btn-result.outline:hover { background: #f1f5f9; color: #64748b; }
.btn-result.share-btn {
    background: linear-gradient(135deg, #0A66C2, #004d99);
    color: white;
}
.btn-result.share-btn:hover { opacity: 0.9; color: white; }

/* ── Message d'encouragement ─────────────────────────────────────────── */
.encouragement {
    background: #f0faf5;
    border: 1px solid #a7f3d0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    color: #065f46;
    line-height: 1.5;
    text-align: left;
}
.encouragement.warn {
    background: #fffbeb;
    border-color: #fcd34d;
    color: #78350f;
}

/* ── Share modal ─────────────────────────────────────────────────────── */
.share-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.55); backdrop-filter: blur(4px);
    z-index: 99999; display: none;
    align-items: center; justify-content: center; padding: 1rem;
}
.share-overlay.open { display: flex; }
.share-box {
    background: white;
    border-radius: 18px;
    padding: 2rem;
    max-width: 440px;
    width: 100%;
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    animation: fadeIn 0.2s ease-out;
    text-align: center;
}
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to   { opacity: 1; transform: scale(1); }
}
.share-box h3 { font-size: 1.2rem; color: #1a1a1a; margin-bottom: 1rem; }
.share-preview-card {
    background: linear-gradient(135deg, #0d7a32, #1db954);
    border-radius: 14px;
    padding: 1.5rem;
    color: white;
    margin-bottom: 1.2rem;
}
.share-preview-card .big-score { font-size: 3rem; font-weight: 900; color: #fad615; }
.share-social-btns {
    display: flex;
    gap: 0.7rem;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-social {
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-size: 0.86rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: opacity 0.15s;
}
.btn-social:hover { opacity: 0.85; }
.btn-linkedin { background: #0A66C2; color: white; }
.btn-facebook { background: #1877F2; color: white; }
.btn-copy     { background: #1e293b; color: white; }
.btn-close-share {
    margin-top: 1rem;
    background: none;
    border: 1px solid #e2e8f0;
    color: #64748b;
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.86rem;
    width: 100%;
}
.btn-close-share:hover { background: #f1f5f9; }
</style>

<div class="result-page">

    <!-- ── Hero Score ── -->
    <div class="result-hero <?= $passed ? 'pass' : 'fail' ?>">
        <span class="result-status-icon"><?= $passed ? '🏆' : '📚' ?></span>
        <div class="result-status-text">
            <?= $passed ? '🎉 Félicitations ! Examen réussi !' : '💪 Continuez à vous améliorer !' ?>
        </div>
        <div class="result-score-big"><?= $score_pct ?>%</div>
        <div class="result-score-sub">
            <?= number_format($attempt->sumgrades, 1) ?> / <?= number_format($attempt->maxgrade, 1) ?> points
        </div>
        <div class="result-threshold">
            Seuil de réussite : 70% &nbsp;·&nbsp; <?= $passed ? '✅ Atteint' : '❌ Non atteint' ?>
        </div>
    </div>

    <!-- ── Stats ── -->
    <div class="result-stats">
        <div class="stat-box">
            <div class="stat-icon">🔁</div>
            <div class="stat-label">Tentative</div>
            <div class="stat-value">N° <?= $attempt->attempt_num ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-icon">⏱️</div>
            <div class="stat-label">Durée</div>
            <div class="stat-value"><?= $duration_str ?: '–' ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-icon">🔁</div>
            <div class="stat-label">Restantes</div>
            <div class="stat-value">
                <?= ($attempts_remaining === null) ? '∞' : ($attempts_remaining . ' / ' . $attempt->maxattempts) ?>
            </div>
        </div>
    </div>

    <!-- ── Badge numérique si réussi ── -->
    <?php if ($passed): ?>
    <div class="digital-badge">
        <div class="badge-icon-big">🎖️</div>
        <div class="badge-title-text">Badge de réussite — <?= htmlspecialchars($attempt->courseshort) ?></div>
        <div class="badge-subtitle">Obtenu le <?= date('d/m/Y', $attempt->timefinish) ?> — Score : <?= $score_pct ?>%</div>
    </div>
    <?php endif; ?>

    <!-- ── Message d'encouragement ── -->
    <?php if ($passed): ?>
    <div class="encouragement">
        🌟 <strong>Excellent travail !</strong> Vous avez validé cette formation avec un score de <strong><?= $score_pct ?>%</strong>.
        Votre attestation de réussite est disponible dans votre <a href="<?= $my_results_url ?>">espace apprenant</a>.
    </div>
    <?php else: ?>
    <div class="encouragement warn">
        💡 <strong>Ne vous découragez pas !</strong> Révisez les séquences du cours et retentez l'examen.
        <?php if ($attempts_remaining !== null && $attempts_remaining > 0): ?>
            Il vous reste encore <strong><?= $attempts_remaining ?> tentative(s)</strong> disponible(s).
        <?php elseif ($attempts_remaining === null): ?>
            Vous pouvez repasser l'examen autant de fois que nécessaire.
        <?php else: ?>
            Vous avez atteint le nombre maximum de tentatives. Contactez votre formateur.
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Actions ── -->
    <div class="result-actions">
        <?php if ($passed): ?>
        <button class="btn-result primary" onclick="openShareOverlay()">
            🔗 Partager mon badge & résultat
        </button>
        <?php endif; ?>

        <a href="<?= $review_url ?>" class="btn-result secondary">
            📋 Voir le détail de mes réponses
        </a>

        <?php if (!$passed && ($attempts_remaining === null || $attempts_remaining > 0)): ?>
        <a href="<?= $welcome_url ?>" class="btn-result primary">
            🔄 Réviser et repasser l'examen
        </a>
        <?php endif; ?>

        <a href="<?= $welcome_url ?>" class="btn-result outline">
            ← Retour à la formation
        </a>

        <a href="<?= $my_results_url ?>" class="btn-result outline">
            📊 Voir tous mes résultats
        </a>
    </div>

</div>

<!-- ════════════════════════════════════════════════════════════════════ -->
<!--  MODAL DE PARTAGE DU RÉSULTAT                                       -->
<!-- ════════════════════════════════════════════════════════════════════ -->
<div class="share-overlay" id="share-overlay" onclick="if(event.target===this)closeShareOverlay()">
    <div class="share-box">
        <h3>🔗 Partager mon résultat</h3>

        <div class="share-preview-card">
            <div style="font-size:0.85rem;opacity:0.85;margin-bottom:0.3rem;">🎖️ <?= htmlspecialchars($attempt->quizname) ?></div>
            <div class="big-score"><?= $score_pct ?>%</div>
            <div style="font-size:0.9rem;margin-top:0.3rem;"><?= htmlspecialchars($attempt->coursename) ?></div>
            <div style="font-size:0.78rem;opacity:0.7;margin-top:0.3rem;">🇧🇫 PACTDIGITAL-BF · <?= date('d/m/Y') ?></div>
        </div>

        <div class="share-social-btns">
            <button class="btn-social btn-linkedin" onclick="shareLinkedIn()">💼 LinkedIn</button>
            <button class="btn-social btn-facebook" onclick="shareFacebook()">📘 Facebook</button>
            <button class="btn-social btn-copy" onclick="copyShareText()">📋 Copier</button>
        </div>
        <button class="btn-close-share" onclick="closeShareOverlay()">Fermer</button>
    </div>
</div>

<script>
const SHARE_TEXT = encodeURIComponent(
    `🎖️ Je viens d'obtenir ${<?= $score_pct ?>}% à l'examen "${<?= json_encode($attempt->quizname) ?>}" sur la plateforme PACTDIGITAL-BF 🇧🇫 #FormationNumerique #PACTDIGITAL`
);
const SHARE_URL = encodeURIComponent(window.location.origin + '/local/pactdigital/catalogue.php');

function openShareOverlay()  { document.getElementById('share-overlay').classList.add('open'); }
function closeShareOverlay() { document.getElementById('share-overlay').classList.remove('open'); }

function shareLinkedIn() {
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${SHARE_URL}&summary=${SHARE_TEXT}`, '_blank');
}
function shareFacebook() {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${SHARE_URL}&quote=${SHARE_TEXT}`, '_blank');
}
function copyShareText() {
    navigator.clipboard.writeText(decodeURIComponent(SHARE_TEXT)).then(() => {
        const btn = document.querySelector('.btn-copy');
        const orig = btn.textContent;
        btn.textContent = '✅ Copié !';
        setTimeout(() => { btn.textContent = orig; }, 2000);
    });
}
</script>

<?php
echo $OUTPUT->footer();
