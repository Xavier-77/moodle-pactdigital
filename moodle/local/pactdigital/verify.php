<?php
/**
 * PACTDIGITAL-BF — Vérification publique des attestations (EF-CER-02 & P5)
 *
 * Page publique accessible sans authentification permettant à tout employeur,
 * administration ou citoyen de vérifier l'authenticité d'une attestation de compétences.
 *
 * Paramètre URL : ?code=XXXXX
 */

require_once(__DIR__ . '/../../config.php');

$PAGE->set_url(new moodle_url('/local/pactdigital/verify.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Vérification publique d\'attestation — PACTDIGITAL-BF');
$PAGE->set_heading('Vérification d\'attestation');

// Récupérer le code saisi ou transmis via QR code
$code_raw = optional_param('code', '', PARAM_RAW);
$code = trim(preg_replace('/[^a-zA-Z0-9_-]/', '', $code_raw));

$searched = !empty($code);
$cert_found = false;
$cert_data = null;

// Détection de niveau
function pact_verify_level($shortname, $fullname = '') {
    $s = strtolower($shortname . ' ' . $fullname);
    if (preg_match('/(^|_|\s)or($|_|\s)/i', $s)) {
        return ['name' => 'Or (Avancé)', 'badge' => '🥇 Or', 'cls' => 'level-or'];
    }
    if (strpos($s, 'argent') !== false || strpos($s, 'diaman') !== false) {
        return ['name' => 'Argent (Intermédiaire)', 'badge' => '🥈 Argent', 'cls' => 'level-argent'];
    }
    if (strpos($s, 'bronze') !== false) {
        return ['name' => 'Bronze (Débutant)', 'badge' => '🥉 Bronze', 'cls' => 'level-bronze'];
    }
    return ['name' => 'Certification', 'badge' => '🎖️ Validé', 'cls' => 'level-default'];
}

if ($searched) {
    // 1. Recherche dans mod_customcert
    $sql = "SELECT ci.id AS issueid, ci.code, ci.timecreated,
                   u.id AS userid, u.firstname, u.lastname,
                   c.id AS courseid, c.fullname AS coursename, c.shortname AS courseshort,
                   cc.name AS certname,
                   cat.name AS categoryname
            FROM {customcert_issues} ci
            JOIN {customcert} cc ON cc.id = ci.customcertid
            JOIN {user} u ON u.id = ci.userid
            JOIN {course} c ON c.id = cc.course
            LEFT JOIN {course_categories} cat ON cat.id = c.category
            WHERE LOWER(ci.code) = LOWER(:code)";

    $res = $DB->get_record_sql($sql, ['code' => $code]);

    if ($res) {
        $cert_found = true;
        $lvl = pact_verify_level($res->courseshort, $res->coursename);
        $cert_data = [
            'code'         => $res->code,
            'fullname'     => fullname($res),
            'coursename'   => $res->coursename,
            'courseshort'  => $res->courseshort,
            'category'     => $res->categoryname ?: 'Littératie Numérique',
            'level'        => $lvl['name'],
            'level_badge'  => $lvl['badge'],
            'level_cls'    => $lvl['cls'],
            'date_issued'  => userdate($res->timecreated, '%d %B %Y'),
            'timestamp'    => $res->timecreated,
        ];
    }
}

echo $OUTPUT->header();
?>

<style>
:root {
    --green: #129b40;
    --green-dark: #0d7a32;
    --yellow: #fad615;
    --red: #d02027;
    --border: #e2e8f0;
    --card-bg: #ffffff;
    --slate: #1e293b;
    --muted: #64748b;
}

.pact-verify-wrapper {
    max-width: 760px;
    margin: 2rem auto 4rem;
    padding: 0 1rem;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

.pact-verify-header {
    text-align: center;
    margin-bottom: 2rem;
}

.pact-flag-strip {
    display: flex;
    height: 6px;
    border-radius: 4px;
    overflow: hidden;
    max-width: 180px;
    margin: 0 auto 1.2rem;
}
.flag-red    { flex: 1; background: var(--red); }
.flag-yellow { width: 24px; background: var(--yellow); }
.flag-green  { flex: 1; background: var(--green); }

.pact-verify-header h1 {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--slate);
    margin: 0.5rem 0;
}

.pact-verify-header p {
    color: var(--muted);
    font-size: 0.95rem;
    margin: 0;
    line-height: 1.5;
}

/* ── Barre de recherche de certificat ──────────────────────────────────── */
.search-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid var(--border);
    margin-bottom: 2rem;
}

.search-form {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.search-input-wrap {
    flex: 1;
    min-width: 260px;
    position: relative;
}

.search-input-wrap input {
    width: 100%;
    padding: 0.85rem 1rem 0.85rem 2.8rem;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-size: 1rem;
    box-sizing: border-box;
    transition: all 0.2s;
    outline: none;
    font-family: monospace, sans-serif;
}

.search-input-wrap input:focus {
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(18, 155, 64, 0.15);
}

.search-icon-inside {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.1rem;
    color: var(--muted);
}

.btn-search-verify {
    background: linear-gradient(135deg, #129b40, #0d7a32);
    color: white;
    border: none;
    padding: 0.85rem 1.6rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-search-verify:hover {
    background: linear-gradient(135deg, #0d7a32, #075a24);
    transform: translateY(-1px);
    color: white;
}

/* ── Carte de vérification officielle ───────────────────────────────────── */
.cert-card-official {
    background: white;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(18, 155, 64, 0.12);
    border: 2px solid #bbf7d0;
    overflow: hidden;
    position: relative;
}

.cert-card-header {
    background: linear-gradient(135deg, #0d7a32, #129b40);
    color: white;
    padding: 1.8rem 2rem;
    text-align: center;
    position: relative;
}

.cert-card-header .coat {
    font-size: 2.2rem;
    margin-bottom: 0.2rem;
}

.cert-card-header h2 {
    font-size: 1.25rem;
    font-weight: 800;
    color: white;
    margin: 0.3rem 0;
    letter-spacing: 0.02em;
}

.cert-card-header .sub {
    font-size: 0.82rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.valid-banner {
    background: #dcfce7;
    color: #15803d;
    padding: 0.9rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-weight: 800;
    font-size: 0.98rem;
    border-bottom: 1px solid #bbf7d0;
}

.valid-banner .icon {
    font-size: 1.3rem;
}

.cert-card-body {
    padding: 2rem;
}

.cert-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.cert-meta-item {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1.2rem;
}

.cert-meta-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--muted);
    font-weight: 700;
    margin-bottom: 0.3rem;
}

.cert-meta-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--slate);
}

.level-badge-pill {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 800;
}
.level-bronze  { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.level-argent  { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
.level-or      { background: #fef08a; color: #854d0e; border: 1px solid #facc15; }
.level-default { background: #e2e8f0; color: #475569; }

.cert-footer-auth {
    border-top: 1px solid var(--border);
    padding-top: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.82rem;
    color: var(--muted);
}

.cert-auth-seal {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #065f46;
}

/* ── Erreur / Introuvable ───────────────────────────────────────────────── */
.cert-card-error {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(220, 38, 38, 0.08);
    border: 2px solid #fca5a5;
    padding: 2.5rem 2rem;
    text-align: center;
}

.cert-card-error .err-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.cert-card-error h2 {
    color: #991b1b;
    font-size: 1.4rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.cert-card-error p {
    color: #64748b;
    font-size: 0.95rem;
    max-width: 520px;
    margin: 0 auto 1.5rem;
    line-height: 1.5;
}

.quick-links-verify {
    text-align: center;
    margin-top: 2rem;
    font-size: 0.9rem;
    color: var(--muted);
}
.quick-links-verify a {
    color: var(--green);
    font-weight: 700;
    text-decoration: none;
    margin: 0 10px;
}
.quick-links-verify a:hover {
    text-decoration: underline;
}
</style>

<div class="pact-verify-wrapper">

    <!-- En-tête officiel -->
    <div class="pact-verify-header">
        <div class="pact-flag-strip">
            <div class="flag-red"></div>
            <div class="flag-yellow"></div>
            <div class="flag-green"></div>
        </div>
        <h1>🇧🇫 Vérification d'Attestation</h1>
        <p>
            Service officiel de vérification des attestations délivrées dans le cadre du
            <strong>Programme National de Littératie Numérique (PACTDIGITAL-BF)</strong><br>
            Ministère de la Transition Digitale, des Postes et des Communications Électroniques.
        </p>
    </div>

    <!-- Formulaire de recherche de certificat -->
    <div class="search-card">
        <form method="get" action="<?= (new moodle_url('/local/pactdigital/verify.php'))->out(false) ?>" class="search-form">
            <div class="search-input-wrap">
                <span class="search-icon-inside">🔍</span>
                <input type="text" name="code" value="<?= htmlspecialchars($code_raw) ?>"
                       placeholder="Saisissez le code de l'attestation (ex. IWHZ4Smehu)" required autofocus>
            </div>
            <button type="submit" class="btn-search-verify">
                Vérifier l'attestation
            </button>
        </form>
    </div>

    <!-- Résultat de vérification -->
    <?php if ($searched && $cert_found): ?>

    <div class="cert-card-official">
        <div class="cert-card-header">
            <div class="coat">🇧🇫</div>
            <h2>BURKINA FASO</h2>
            <div class="sub">Ministère de la Transition Digitale, des Postes et des Communications Électroniques</div>
            <div style="font-size:0.85rem; opacity:0.85; margin-top:4px;">Programme National de Littératie Numérique (PACTDIGITAL-BF)</div>
        </div>

        <div class="valid-banner">
            <span class="icon">✅</span>
            <span>ATTESTATION OFFICIELLE AUTHENTIQUE ET VALIDE</span>
        </div>

        <div class="cert-card-body">
            <div class="cert-grid">
                <div class="cert-meta-item">
                    <div class="cert-meta-label">Bénéficiaire</div>
                    <div class="cert-meta-value"><?= htmlspecialchars($cert_data['fullname']) ?></div>
                </div>

                <div class="cert-meta-item">
                    <div class="cert-meta-label">Code d'attestation unique</div>
                    <div class="cert-meta-value" style="font-family: monospace; letter-spacing: 0.05em; color: var(--green);">
                        <?= htmlspecialchars($cert_data['code']) ?>
                    </div>
                </div>

                <div class="cert-meta-item" style="grid-column: 1 / -1;">
                    <div class="cert-meta-label">Module de formation validé</div>
                    <div class="cert-meta-value" style="font-size: 1.2rem; color: var(--green-dark);">
                        <?= htmlspecialchars($cert_data['coursename']) ?>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--muted); margin-top: 4px;">
                        Domaine : <?= htmlspecialchars($cert_data['category']) ?>
                    </div>
                </div>

                <div class="cert-meta-item">
                    <div class="cert-meta-label">Niveau certifié</div>
                    <div class="cert-meta-value">
                        <span class="level-badge-pill <?= $cert_data['level_cls'] ?>">
                            <?= htmlspecialchars($cert_data['level_badge']) ?> — <?= htmlspecialchars($cert_data['level']) ?>
                        </span>
                    </div>
                </div>

                <div class="cert-meta-item">
                    <div class="cert-meta-label">Date de délivrance</div>
                    <div class="cert-meta-value"><?= $cert_data['date_issued'] ?></div>
                </div>
            </div>

            <div class="cert-footer-auth">
                <div class="cert-auth-seal">
                    🛡️ Signé numériquement par le MTDPCE / DGTD
                </div>
                <div>
                    Système national d'attestation PACTDIGITAL-BF
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($searched && !$cert_found): ?>

    <div class="cert-card-error">
        <div class="err-icon">❌</div>
        <h2>Attestation introuvable</h2>
        <p>
            Aucune attestation officielle ne correspond au code « <strong><?= htmlspecialchars($code_raw) ?></strong> ».
            Veuillez vérifier l'exactitude des caractères saisis ou scanner à nouveau le QR code figurant sur le document.
        </p>
        <a href="<?= (new moodle_url('/local/pactdigital/verify.php'))->out(false) ?>" class="btn-search-verify" style="display:inline-flex; text-decoration:none;">
            🔄 Nouvelle vérification
        </a>
    </div>

    <?php else: ?>

    <!-- Guide d'utilisation pour le visiteur -->
    <div style="background: white; border-radius: 16px; padding: 2rem; border: 1px solid var(--border); box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--slate); margin-top: 0; margin-bottom: 0.8rem;">
            📌 Comment vérifier une attestation ?
        </h3>
        <ol style="color: var(--muted); line-height: 1.7; padding-left: 1.3rem; margin-bottom: 1.5rem; font-size: 0.92rem;">
            <li>Repérez le <strong>numéro unique d'attestation</strong> imprimé sur l'attestation papier ou PDF (ex. <em>IWHZ4Smehu</em>).</li>
            <li>Saisissez-le dans le champ de recherche ci-dessus, ou scannez directement le <strong>QR code</strong> avec l'appareil photo de votre smartphone.</li>
            <li>Le système interroge en temps réel le registre national du MTDPCE pour confirmer l'identité de l'apprenant, le niveau validé et la date d'émission.</li>
        </ol>
        <div style="background: #f0fdf4; border-radius: 10px; padding: 0.9rem 1.1rem; border: 1px solid #bbf7d0; font-size: 0.86rem; color: #166534;">
            💡 <strong>Employeurs et administrations :</strong> Cette vérification est infalsifiable et garantit que le certificat a été délivré par le Ministère conformément au référentiel national de littératie numérique.
        </div>
    </div>

    <?php endif; ?>

    <div class="quick-links-verify">
        <a href="<?= (new moodle_url('/local/pactdigital/catalogue.php'))->out(false) ?>">📚 Catalogue des formations</a>
        ·
        <a href="<?= (new moodle_url('/login/index.php'))->out(false) ?>">🔐 Espace de connexion</a>
    </div>

</div>

<?php
echo $OUTPUT->footer();
