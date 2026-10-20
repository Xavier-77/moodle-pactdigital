<?php
/**
 * PACTDIGITAL-BF — Contact & Assistance Citoyenne (VIS-09)
 *
 * Page publique permettant à tout citoyen ou apprenant de poser une question,
 * signaler un problème ou solliciter l'assistance du support national MTDPCE / DGTD.
 */

require_once('../../config.php');

$PAGE->set_url(new moodle_url('/local/pactdigital/contact.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Contact & Assistance — PACTDIGITAL-BF');
$PAGE->set_heading('Contact & Assistance');

$ticket_submitted = false;
$ticket_ref = '';
$err_msg = '';

$user_fullname = '';
$user_phone = '';
$user_email = '';
$user_region = '';

if (isloggedin() && !isguestuser()) {
    $user_fullname = fullname($USER);
    $user_phone = $USER->phone1 ?: '';
    $user_email = (strpos($USER->email, '@pactdigital.bf') === false) ? $USER->email : '';
    
    // Fetch region if set
    $reg_val = $DB->get_field_sql(
        "SELECT d.data FROM {user_info_data} d 
         JOIN {user_info_field} f ON f.id = d.fieldid 
         WHERE f.shortname = 'region' AND d.userid = :uid LIMIT 1",
        ['uid' => $USER->id]
    );
    if ($reg_val) {
        $user_region = $reg_val;
    }
}

// ── Traitement du formulaire POST ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {
    $nom_prenom = clean_param(required_param('fullname', PARAM_TEXT), PARAM_TEXT);
    $telephone  = clean_param(required_param('phone', PARAM_RAW), PARAM_RAW);
    $email      = clean_param(optional_param('email', '', PARAM_EMAIL), PARAM_EMAIL);
    $region     = clean_param(optional_param('region', '', PARAM_TEXT), PARAM_TEXT);
    $categorie  = clean_param(required_param('category', PARAM_TEXT), PARAM_TEXT);
    $sujet      = clean_param(required_param('subject', PARAM_TEXT), PARAM_TEXT);
    $message    = clean_param(required_param('message', PARAM_RAW), PARAM_RAW);

    if (empty($nom_prenom) || empty($telephone) || empty($sujet) || empty($message)) {
        $err_msg = "Veuillez renseigner tous les champs obligatoires marqués d'un astérisque (*).";
    } else {
        // Génération d'un numéro de ticket unique
        $ticket_ref = 'PACT-BF-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $ticket_submitted = true;

        // Envoi d'une notification aux administrateurs du site
        $admins = get_admins();
        $admin_target = reset($admins);

        if ($admin_target) {
            $msg_obj = new \core\message\message();
            $msg_obj->courseid          = SITEID;
            $msg_obj->component         = 'moodle';
            $msg_obj->name              = 'instantmessage';
            $msg_obj->userfrom          = core_user::get_noreply_user();
            $msg_obj->userto            = $admin_target;
            $msg_obj->subject           = "[PACTDIGITAL Ticket #{$ticket_ref}] {$sujet}";
            $full_text = "Nouveau ticket citoyen reçu :\n"
                       . "Référence : {$ticket_ref}\n"
                       . "Nom : {$nom_prenom}\n"
                       . "Téléphone : {$telephone}\n"
                       . "Email : " . ($email ?: 'Non renseigné') . "\n"
                       . "Région : " . ($region ?: 'Non renseignée') . "\n"
                       . "Catégorie : {$categorie}\n"
                       . "Objet : {$sujet}\n\n"
                       . "Message :\n{$message}\n";
            $msg_obj->fullmessage       = $full_text;
            $msg_obj->fullmessageformat = FORMAT_PLAIN;
            $msg_obj->fullmessagehtml   = nl2br(htmlspecialchars($full_text));
            $msg_obj->smallmessage      = "Nouveau ticket #{$ticket_ref} de {$nom_prenom}";
            $msg_obj->notification      = 1;

            try {
                message_send($msg_obj);
            } catch (\Exception $e) {
                // Continuer malgré l'erreur d'envoi interne
            }
        }
    }
}

echo $OUTPUT->header();
?>

<style>
:root {
    --pact-green: #145B37;
    --pact-green-light: #e8f5ee;
    --pact-yellow: #fad615;
    --pact-red: #D32F2F;
    --pact-dark: #1e293b;
    --pact-grey: #64748b;
    --pact-border: #e2e8f0;
}

.contact-wrapper {
    max-width: 1100px;
    margin: 0 auto 50px auto;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    color: var(--pact-dark);
}

.contact-hero {
    background: linear-gradient(135deg, #145B37 0%, #1e7a4a 100%);
    border-radius: 16px;
    padding: 35px 30px;
    color: #fff;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(20, 91, 55, 0.15);
    text-align: center;
}
.contact-hero h1 {
    font-size: 2.1rem;
    font-weight: 800;
    margin: 0 0 10px 0;
    color: #fff;
}
.contact-hero p {
    font-size: 1.05rem;
    opacity: 0.92;
    max-width: 720px;
    margin: 0 auto;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 30px;
}
@media (max-width: 850px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
}

.contact-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid var(--pact-border);
    padding: 28px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.04);
}
.contact-card h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--pact-green);
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-group {
    margin-bottom: 16px;
}
.form-group label {
    display: block;
    font-size: 0.86rem;
    font-weight: 700;
    color: var(--pact-dark);
    margin-bottom: 6px;
}
.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--pact-border);
    border-radius: 8px;
    font-size: 0.92rem;
    box-sizing: border-box;
    font-family: inherit;
    transition: border-color 0.2s;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: var(--pact-green);
    box-shadow: 0 0 0 3px rgba(20, 91, 55, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
@media (max-width: 500px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.btn-submit-ticket {
    background: var(--pact-green);
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    padding: 12px 28px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
    width: 100%;
    justify-content: center;
}
.btn-submit-ticket:hover {
    background: #0e4428;
}

/* ── Side Info Box ───────────────────────────────────────────── */
.info-item {
    display: flex;
    gap: 14px;
    margin-bottom: 20px;
    align-items: flex-start;
}
.info-icon {
    font-size: 1.5rem;
    background: var(--pact-green-light);
    color: var(--pact-green);
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.info-text strong {
    display: block;
    font-size: 0.92rem;
    color: var(--pact-dark);
    margin-bottom: 2px;
}
.info-text p {
    margin: 0;
    font-size: 0.86rem;
    color: var(--pact-grey);
    line-height: 1.4;
}

.num-vert-badge {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 10px;
    padding: 14px 18px;
    margin-top: 24px;
    text-align: center;
}
.num-vert-badge .num {
    font-size: 1.8rem;
    font-weight: 800;
    color: #b45309;
    letter-spacing: 2px;
}
.num-vert-badge small {
    display: block;
    color: #92400e;
    font-size: 0.8rem;
    font-weight: 600;
}

/* ── Success Receipt ─────────────────────────────────────────── */
.ticket-receipt {
    background: #ecfdf5;
    border: 2px solid #10b981;
    border-radius: 14px;
    padding: 30px;
    text-align: center;
    margin-bottom: 30px;
}
.ticket-receipt h3 {
    color: #065f46;
    font-size: 1.4rem;
    font-weight: 800;
    margin: 0 0 10px 0;
}
.ticket-receipt p {
    color: #047857;
    font-size: 0.95rem;
    margin: 0 0 16px 0;
}
.ticket-code {
    display: inline-block;
    background: #fff;
    border: 2px dashed #059669;
    color: #065f46;
    font-family: monospace;
    font-size: 1.6rem;
    font-weight: 800;
    padding: 8px 24px;
    border-radius: 8px;
    letter-spacing: 1px;
    margin-bottom: 14px;
}
</style>

<div class="contact-wrapper">

    <!-- Hero Header -->
    <div class="contact-hero">
        <h1>💬 Contact & Support Citoyen</h1>
        <p>Une question, un blocage technique ou besoin d'orientation sur vos cours ? Le support national PACTDIGITAL-BF est à votre service.</p>
    </div>

    <?php if ($ticket_submitted): ?>
        <!-- Success Receipt -->
        <div class="ticket-receipt">
            <div style="font-size:3rem;margin-bottom:8px;">✅</div>
            <h3>Votre demande d'assistance a été enregistrée avec succès !</h3>
            <p>Notre équipe support traitera votre message dans les meilleurs délais. Conservez précieusement votre référence :</p>
            <div class="ticket-code"><?= htmlspecialchars($ticket_ref) ?></div>
            <p style="font-size:0.85rem;color:#065f46;">
                Un SMS ou appel de confirmation vous sera transmis au numéro fourni si nécessaire.
            </p>
            <div style="margin-top:20px;">
                <a href="/local/pactdigital/contact.php" class="btn-submit-ticket" style="display:inline-flex;width:auto;padding:10px 24px;">
                    Envoyer une autre demande
                </a>
            </div>
        </div>
    <?php else: ?>

        <?php if (!empty($err_msg)): ?>
            <div style="background:#fee2e2;border:1px solid #ef4444;color:#b91c1c;padding:12px 18px;border-radius:8px;margin-bottom:20px;font-size:0.9rem;">
                ⚠️ <?= $err_msg ?>
            </div>
        <?php endif; ?>

        <div class="contact-grid">

            <!-- Colonne 1 : Formulaire -->
            <div class="contact-card">
                <h2>✍️ Formulaire de demande d'assistance</h2>
                <form method="post" action="contact.php">
                    <input type="hidden" name="sesskey" value="<?= sesskey() ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="fullname">Nom et Prénom(s) *</label>
                            <input type="text" name="fullname" id="fullname" required 
                                   value="<?= htmlspecialchars($user_fullname) ?>" 
                                   placeholder="Ex: OUEDRAOGO Fatou">
                        </div>
                        <div class="form-group">
                            <label for="phone">Numéro de téléphone *</label>
                            <input type="tel" name="phone" id="phone" required 
                                   value="<?= htmlspecialchars($user_phone) ?>" 
                                   placeholder="Ex: 70 12 34 56">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Adresse email (optionnelle)</label>
                            <input type="email" name="email" id="email" 
                                   value="<?= htmlspecialchars($user_email) ?>" 
                                   placeholder="Ex: citoyen@exemple.bf">
                        </div>
                        <div class="form-group">
                            <label for="region">Région</label>
                            <select name="region" id="region">
                                <option value="">Sélectionnez votre région…</option>
                                <?php
                                $regions = [
                                    'Boucle du Mouhoun', 'Cascades', 'Centre (Ouagadougou)',
                                    'Centre-Est', 'Centre-Nord', 'Centre-Ouest', 'Centre-Sud',
                                    'Est', 'Hauts-Bassins (Bobo)', 'Nord', 'Plateau-Central',
                                    'Sahel', 'Sud-Ouest'
                                ];
                                foreach ($regions as $r):
                                ?>
                                    <option value="<?= $r ?>" <?= ($user_region === $r) ? 'selected' : '' ?>><?= $r ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category">Catégorie de la demande *</label>
                        <select name="category" id="category" required>
                            <option value="Compte et Inscription">📱 Inscription ou connexion à la plateforme</option>
                            <option value="Parcours et Cours">📚 Difficulté sur un module de formation</option>
                            <option value="Voie 2 et Examen">⚡ Examen direct / Certification (Voie 2)</option>
                            <option value="Attestation et QR Code">🏆 Problème d'attestation ou de vérification QR Code</option>
                            <option value="Devenir Formateur / Relais">🧑‍🏫 Devenir formateur communal ou centre relais</option>
                            <option value="Autre demande">💡 Autre demande d'information</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subject">Objet de votre demande *</label>
                        <input type="text" name="subject" id="subject" required 
                               placeholder="Ex: Récupération de mon attestation Bronze">
                    </div>

                    <div class="form-group">
                        <label for="message">Description détaillée de votre situation *</label>
                        <textarea name="message" id="message" rows="5" required 
                                  placeholder="Expliquez-nous en détail votre demande ou le problème rencontré…"></textarea>
                    </div>

                    <button type="submit" class="btn-submit-ticket">
                        🚀 Transmettre ma demande au support
                    </button>
                </form>
            </div>

            <!-- Colonne 2 : Coordonnées & Points de contact -->
            <div>
                <div class="contact-card">
                    <h2>🏛️ Institutions & Permanence</h2>

                    <div class="info-item">
                        <div class="info-icon">🏛️</div>
                        <div class="info-text">
                            <strong>Ministère de Tutelle</strong>
                            <p>Ministère de la Transition Digitale, des Postes et des Communications Électroniques (MTDPCE)</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">🎯</div>
                        <div class="info-text">
                            <strong>Direction Opérationnelle</strong>
                            <p>Direction Générale de la Transition Digitale (DGTD)</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <div class="info-text">
                            <strong>Siège National</strong>
                            <p>Avenue de l'Indépendance, Ouagadougou, Burkina Faso</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">✉️</div>
                        <div class="info-text">
                            <strong>Courriel Officiel</strong>
                            <p>support@pactdigital.bf<br>contact@transition-digitale.gov.bf</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">🕒</div>
                        <div class="info-text">
                            <strong>Horaires d'Assistance</strong>
                            <p>Lundi au Vendredi : 07h30 – 16h00 (Heure locale BF)</p>
                        </div>
                    </div>

                    <div class="num-vert-badge">
                        <small>ASSISTANCE CITOYENNE GRATUITE</small>
                        <div class="num">34 34</div>
                        <small>Appel gratuit depuis les réseaux nationaux</small>
                    </div>
                </div>
            </div>

        </div>

    <?php endif; ?>

</div>

<?php
echo $OUTPUT->footer();
