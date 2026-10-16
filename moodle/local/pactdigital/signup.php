<?php
/**
 * PACTDIGITAL-BF — Inscription Citoyenne Simplifiée
 *
 * Permet l'inscription rapide de tout apprenant burkinabè :
 * - Email 100% OPTIONNEL (pour les apprenants sans adresse e-mail)
 * - Génération automatique d'un identifiant interne si pas d'email
 * - Contrôle de l'âge minimum (>= 18 ans) basé sur l'année de naissance
 * - Enregistrement des champs territoriaux (Région, Province, Commune)
 * - Activation immédiate sans confirmation par e-mail bloquante
 * - Connexion automatique à l'issue de l'inscription
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');

// ── Endpoint AJAX pour vérification d'unicité en temps réel ────────────────
$ajax_action = optional_param('action', '', PARAM_ALPHAEXT);
if ($ajax_action === 'check_username') {
    header('Content-Type: application/json; charset=utf-8');
    $uname = trim(optional_param('username', '', PARAM_RAW));
    $uname_norm = core_text::strtolower(preg_replace('/\s+/', '', $uname));

    if (empty($uname_norm) || strlen($uname_norm) < 3) {
        echo json_encode([
            'available' => false,
            'message'   => 'Le nom d\'utilisateur doit comporter au moins 3 caractères.'
        ]);
        exit;
    }

    $exists = $DB->record_exists_select('user', "deleted = 0 AND LOWER(username) = :u", ['u' => $uname_norm]);
    if ($exists) {
        echo json_encode([
            'available' => false,
            'message'   => '❌ Ce nom d\'utilisateur est déjà pris. Veuillez en choisir un autre.'
        ]);
    } else {
        echo json_encode([
            'available' => true,
            'message'   => '✅ Nom d\'utilisateur disponible !'
        ]);
    }
    exit;
}

if ($ajax_action === 'check_email') {
    header('Content-Type: application/json; charset=utf-8');
    $email = trim(optional_param('email', '', PARAM_RAW));

    if (empty($email)) {
        echo json_encode(['available' => true, 'message' => '']);
        exit;
    }

    $email_norm = core_text::strtolower($email);
    if (!validate_email($email_norm)) {
        echo json_encode([
            'available' => false,
            'message'   => '❌ Format d\'adresse e-mail invalide.'
        ]);
        exit;
    }

    $exists = $DB->record_exists_select('user', "deleted = 0 AND LOWER(email) = :e", ['e' => $email_norm]);
    if ($exists) {
        echo json_encode([
            'available' => false,
            'message'   => '❌ Cette adresse e-mail est déjà associée à un compte existant.'
        ]);
    } else {
        echo json_encode([
            'available' => true,
            'message'   => '✅ Adresse e-mail disponible !'
        ]);
    }
    exit;
}

// Si déjà connecté, rediriger vers l'espace apprenant
if (isloggedin() && !isguestuser()) {
    redirect(new moodle_url('/local/pactdigital/apprenant.php'));
}

$PAGE->set_url(new moodle_url('/local/pactdigital/signup.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Créer mon compte citoyen — PACTDIGITAL-BF');
$PAGE->set_heading('Inscription PACTDIGITAL-BF');

// URLs
$login_url   = (new moodle_url('/login/index.php'))->out(false);
$wantsurl    = optional_param('wantsurl', '', PARAM_URL);
$current_yr  = (int) date('Y');
$max_valid_yr = $current_yr - 18;

// Messages
$errors = [];
$form_data = [
    'firstname'             => '',
    'lastname'              => '',
    'username'              => '',
    'phone'                 => '',
    'email'                 => '',
    'birth_year'            => '',
    'sexe'                  => '',
    'region'                => '',
    'province'              => '',
    'commune'               => '',
    'statut'                => '',
    'niveau_instruction'    => '',
    'structure_appartenance' => '',
    'consentement'          => 0,
];

// Traitement de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_sesskey();

    $form_data['firstname']              = trim(optional_param('firstname', '', PARAM_TEXT));
    $form_data['lastname']               = trim(optional_param('lastname', '', PARAM_TEXT));
    $form_data['username']               = trim(optional_param('username', '', PARAM_RAW));
    $form_data['phone']                  = trim(optional_param('phone', '', PARAM_TEXT));
    $form_data['email']                  = trim(optional_param('email', '', PARAM_RAW));
    $form_data['birth_year']             = optional_param('birth_year', 0, PARAM_INT);
    $form_data['sexe']                   = optional_param('sexe', '', PARAM_TEXT);
    $form_data['region']                 = optional_param('region', '', PARAM_TEXT);
    $form_data['province']               = optional_param('province', '', PARAM_TEXT);
    $form_data['commune']                = optional_param('commune', '', PARAM_TEXT);
    $form_data['statut']                 = optional_param('statut', '', PARAM_TEXT);
    $form_data['niveau_instruction']     = optional_param('niveau_instruction', '', PARAM_TEXT);
    $form_data['structure_appartenance'] = trim(optional_param('structure_appartenance', '', PARAM_TEXT));
    $form_data['consentement']          = optional_param('consentement', 0, PARAM_INT);
    $password                            = optional_param('password', '', PARAM_RAW);
    $password_confirm                    = optional_param('password_confirm', '', PARAM_RAW);

    // ── 1. Validation Prénom & Nom ──────────────────────────────────────────
    if (empty($form_data['firstname'])) {
        $errors[] = 'Veuillez renseigner votre prénom.';
    }
    if (empty($form_data['lastname'])) {
        $errors[] = 'Veuillez renseigner votre nom de famille.';
    }

    // ── 2. Validation Téléphone (Obligatoire selon Annexe D) ──────────────────
    if (empty($form_data['phone'])) {
        $errors[] = 'Veuillez renseigner votre numéro de téléphone (contact obligatoire).';
    } else if (!preg_match('/^[0-9+\s\-()]{6,20}$/', $form_data['phone'])) {
        $errors[] = 'Veuillez renseigner un numéro de téléphone valide.';
    }

    // ── 3. Validation Nom d'utilisateur (UNICITÉ STRICTE DANS TOUS LES CAS) ─
    // Le nom d'utilisateur doit être unique, qu'un e-mail soit renseigné ou non
    $clean_username = core_text::strtolower(preg_replace('/\s+/', '', $form_data['username']));
    if (empty($clean_username) || strlen($clean_username) < 3) {
        $errors[] = 'Le nom d\'utilisateur doit comporter au moins 3 caractères (lettres, chiffres ou tirets).';
    } else {
        $username_exists = $DB->record_exists_select('user', 
            "deleted = 0 AND LOWER(username) = :u", 
            ['u' => $clean_username]
        );
        if ($username_exists) {
            $errors[] = 'Le nom d\'utilisateur « ' . htmlspecialchars($form_data['username']) . ' » est déjà pris. Même si vous avez renseigné une adresse e-mail, chaque compte doit avoir un nom d\'utilisateur strictement unique. Veuillez en choisir un autre (ou utiliser votre numéro de téléphone).';
        }
    }

    // ── 4. Contrôle d'âge minimum (18 ans) ──────────────────────────────────
    if (empty($form_data['birth_year']) || $form_data['birth_year'] < 1920 || $form_data['birth_year'] > $current_yr) {
        $errors[] = 'Veuillez indiquer une année de naissance valide (ex. 1996).';
    } else {
        $age = $current_yr - $form_data['birth_year'];
        if ($age < 18) {
            $errors[] = "L'inscription est réservée aux personnes de 18 ans et plus. Selon l'année renseignée ({$form_data['birth_year']}), vous avez {$age} ans.";
        }
    }

    // ── 5. Validation Consentement (EF-INS-03 / Loi 001-2021/AN) ────────────
    if (empty($form_data['consentement'])) {
        $errors[] = 'Vous devez accepter le traitement de vos données personnelles conformément à la loi n°001-2021/AN pour finaliser votre inscription.';
    }

    // ── 6. Validation Mot de passe ──────────────────────────────────────────
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit comporter au moins 6 caractères.';
    } else if ($password !== $password_confirm) {
        $errors[] = 'Les deux mots de passe ne correspondent pas.';
    }

    // ── 7. Gestion de l'EMAIL (OPTIONNEL mais UNICITÉ VÉRIFIÉE SI RENSEIGNÉ) ──
    $final_email = '';
    if (!empty($form_data['email'])) {
        $normalized_email = core_text::strtolower(trim($form_data['email']));
        if (!validate_email($normalized_email)) {
            $errors[] = "L'adresse e-mail renseignée n'est pas valide. Si vous n'avez pas d'e-mail, laissez simplement ce champ vide.";
        } else {
            $email_exists = $DB->record_exists_select('user', 
                "deleted = 0 AND LOWER(email) = :e", 
                ['e' => $normalized_email]
            );
            if ($email_exists) {
                $errors[] = 'Cette adresse e-mail est déjà associée à un compte existant. Si c\'est le vôtre, veuillez vous connecter.';
            } else {
                $final_email = $normalized_email;
            }
        }
    } else {
        // L'utilisateur N'A PAS d'email : on génère un identifiant interne unique valide pour Moodle
        $base_email = $clean_username . '@pactdigital.bf';
        if ($DB->record_exists('user', ['email' => $base_email])) {
            $final_email = $clean_username . '_' . time() . '@pactdigital.bf';
        } else {
            $final_email = $base_email;
        }
    }

    // ── 8. Création du compte si aucune erreur ──────────────────────────────
    if (empty($errors)) {
        try {
            $newuser = new stdClass();
            $newuser->username    = $clean_username;
            $newuser->password    = $password;
            $newuser->firstname   = $form_data['firstname'];
            $newuser->lastname    = $form_data['lastname'];
            $newuser->phone1      = $form_data['phone'];
            $newuser->email       = $final_email;
            $newuser->auth        = 'manual';
            $newuser->confirmed   = 1; // Activation immédiate sans mail requis
            $newuser->policyagreed= 1; // Consentement horodaté
            $newuser->mnethostid  = $CFG->mnet_localhost_id;
            $newuser->lang        = 'fr';
            $newuser->country     = 'BF';
            $newuser->city        = !empty($form_data['commune']) ? $form_data['commune'] : (!empty($form_data['region']) ? $form_data['region'] : 'Ouagadougou');
            $newuser->timecreated = time();
            $newuser->timemodified= time();

            // Création dans Moodle Core
            $userid = user_create_user($newuser, true, false);
            $user_obj = $DB->get_record('user', ['id' => $userid]);

            // Enregistrement des champs personnalisés de profil (Annexe D)
            $profile_fields = [
                'annee_naissance'       => (string) $form_data['birth_year'],
                'sexe'                  => $form_data['sexe'],
                'region'                => $form_data['region'],
                'province'              => $form_data['province'],
                'commune'               => $form_data['commune'],
                'statut'                => $form_data['statut'],
                'niveau_instruction'    => $form_data['niveau_instruction'],
                'structure_appartenance' => $form_data['structure_appartenance'],
                'consentement'          => '1',
            ];

            foreach ($profile_fields as $shortname => $val) {
                if ($val === '') continue;
                $field = $DB->get_record('user_info_field', ['shortname' => $shortname]);
                if ($field) {
                    $DB->insert_record('user_info_data', [
                        'userid'  => $userid,
                        'fieldid' => $field->id,
                        'data'    => $val,
                    ]);
                }
            }

            // Déclencher l'événement Moodle
            \core\event\user_created::create_from_userid($userid)->trigger();

            // Connexion immédiate de l'utilisateur
            complete_user_login($user_obj);

            // Redirection de bienvenue
            $dest = !empty($wantsurl) ? $wantsurl : (new moodle_url('/local/pactdigital/catalogue.php'))->out(false);
            redirect($dest, 'Bienvenue sur PACTDIGITAL-BF ! Votre compte citoyen a été créé avec succès.', 2);

        } catch (\Throwable $e) {
            error_log('[PACTDIGITAL] Erreur création compte : ' . $e->getMessage());
            $errors[] = 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer ou contacter l\'assistance.';
        }
    }
}

// Liste des régions du Burkina Faso
$regions_bf = [
    'Bankui', 'Djôrô', 'Goulmou', 'Guiriko', 'Kadiogo', 'Kuilsé',
    'Liptako', 'Nakambé', 'Nando', 'Nazinon', 'Oubri', 'Sirba',
    'Soum', 'Sourou', 'Tannounyan', 'Tapoa', 'Yaadga'
];

$provinces_field = $DB->get_record('user_info_field', ['shortname' => 'province']);
$provinces_bf = $provinces_field ? array_filter(array_map('trim', explode("\n", $provinces_field->param1))) : [];

$communes_field = $DB->get_record('user_info_field', ['shortname' => 'commune']);
$communes_bf = $communes_field ? array_filter(array_map('trim', explode("\n", $communes_field->param1))) : [];

$niveaux_field = $DB->get_record('user_info_field', ['shortname' => 'niveau_instruction']);
$niveaux_bf = $niveaux_field ? array_filter(array_map('trim', explode("\n", $niveaux_field->param1))) : ['Aucun', 'Primaire', 'Secondaire', 'Supérieur', 'Autre'];

$statuts_bf = [
    'élève/étudiant'       => 'Élève / Étudiant',
    'agent public'         => 'Agent public / Fonctionnaire',
    'entrepreneur'         => 'Entrepreneur / Secteur privé',
    'agriculteur/éleveur'  => 'Agriculteur / Éleveur',
    'en recherche d’emploi'=> 'En recherche d’emploi',
    'autre'                => 'Autre',
];

echo $OUTPUT->header();
?>

<style>
:root {
    --green: #129b40;
    --green-dark: #0d7a32;
    --yellow: #fad615;
    --red: #d02027;
    --border: #e2e8f0;
    --bg-light: #f8fafc;
}

body.pagelayout-login {
    background: #f1f5f9;
}

.pact-signup-container {
    max-width: 640px;
    margin: 2rem auto 4rem;
    padding: 0 1rem;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

.pact-signup-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(18, 155, 64, 0.10);
    border: 1px solid var(--border);
    overflow: hidden;
}

.pact-signup-header {
    background: linear-gradient(135deg, #0d7a32 0%, #129b40 60%, #1db954 100%);
    color: white;
    padding: 2.2rem 2rem 1.8rem;
    text-align: center;
    position: relative;
}

.pact-flag-bar {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 6px;
    display: flex;
}
.flag-red    { flex: 1; background: var(--red); }
.flag-yellow { width: 30px; background: var(--yellow); }
.flag-green  { flex: 1; background: var(--green); }

.pact-signup-header h1 {
    font-size: 1.6rem;
    font-weight: 800;
    margin: 0.5rem 0 0.3rem;
    color: white;
}

.pact-signup-header p {
    font-size: 0.92rem;
    opacity: 0.92;
    margin: 0;
}

.pact-signup-body {
    padding: 2rem;
}

.pact-info-box {
    background: #fefce8;
    border: 1.5px solid #fef08a;
    border-radius: 12px;
    padding: 0.9rem 1.1rem;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 12px;
    align-items: flex-start;
    font-size: 0.88rem;
    color: #854d0e;
    line-height: 1.45;
}

.pact-info-box .icon {
    font-size: 1.3rem;
    flex-shrink: 0;
}

.alert-danger-custom {
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #991b1b;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    margin-bottom: 1.5rem;
    font-size: 0.88rem;
}

.alert-danger-custom ul {
    margin: 0.4rem 0 0;
    padding-left: 1.3rem;
}

.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 580px) {
    .form-grid-2 { grid-template-columns: 1fr; }
    .pact-signup-body { padding: 1.4rem; }
}

.form-group-pact {
    margin-bottom: 1.2rem;
}

.form-group-pact label {
    display: block;
    font-size: 0.86rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.4rem;
}

.form-group-pact label .badge-optional {
    background: #e2e8f0;
    color: #475569;
    padding: 2px 7px;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 600;
    margin-left: 6px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.form-group-pact label .req {
    color: var(--red);
    margin-left: 3px;
}

.input-pact {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 0.95rem;
    box-sizing: border-box;
    transition: all 0.2s;
    background: white;
}

.input-pact:focus {
    outline: none;
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(18, 155, 64, 0.15);
}

.input-helper {
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 0.35rem;
    line-height: 1.4;
}

.btn-signup-pact {
    background: linear-gradient(135deg, #129b40, #0d7a32);
    color: white;
    width: 100%;
    padding: 0.9rem;
    border: none;
    border-radius: 12px;
    font-size: 1.05rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 1rem;
    box-shadow: 0 4px 15px rgba(18, 155, 64, 0.25);
}

.btn-signup-pact:hover {
    background: linear-gradient(135deg, #0d7a32, #075a24);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(18, 155, 64, 0.35);
    color: white;
}

.signup-footer-links {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.9rem;
    color: #64748b;
}

.signup-footer-links a {
    color: var(--green);
    font-weight: 700;
    text-decoration: none;
}

.signup-footer-links a:hover {
    text-decoration: underline;
}
</style>

<div class="pact-signup-container">
    <div class="pact-signup-card">
        <!-- Bannière aux couleurs du Burkina Faso -->
        <div class="pact-signup-header">
            <div class="pact-flag-bar">
                <div class="flag-red"></div>
                <div class="flag-yellow"></div>
                <div class="flag-green"></div>
            </div>
            <div style="font-size: 2.2rem; margin-bottom: 0.3rem;">🇧🇫</div>
            <h1>Créer mon compte citoyen</h1>
            <p>Plateforme nationale PACTDIGITAL-BF</p>
        </div>

        <div class="pact-signup-body">

            <!-- Encadré explicatif : pas besoin d'email -->
            <div class="pact-info-box">
                <span class="icon">💡</span>
                <div>
                    <strong>Vous n'avez pas d'adresse e-mail ?</strong>
                    <br>Aucun problème ! Le champ e-mail est <strong>facultatif</strong>. Vous utiliserez uniquement votre <strong>nom d'utilisateur</strong> et votre mot de passe pour vous connecter.
                </div>
            </div>

            <!-- Affichage des erreurs éventuelles -->
            <?php if (!empty($errors)): ?>
            <div class="alert-danger-custom">
                <strong>Veuillez corriger les éléments suivants :</strong>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="post" action="<?= (new moodle_url('/local/pactdigital/signup.php'))->out(false) ?>" autocomplete="off">
                <input type="hidden" name="sesskey" value="<?= sesskey() ?>">
                <?php if (!empty($wantsurl)): ?>
                    <input type="hidden" name="wantsurl" value="<?= htmlspecialchars($wantsurl) ?>">
                <?php endif; ?>

                <!-- 1. Identité -->
                <div class="form-grid-2">
                    <div class="form-group-pact">
                        <label for="firstname">Prénom <span class="req">*</span></label>
                        <input type="text" id="firstname" name="firstname" class="input-pact"
                               value="<?= htmlspecialchars($form_data['firstname']) ?>" placeholder="Ex. Awa" required>
                    </div>
                    <div class="form-group-pact">
                        <label for="lastname">Nom de famille <span class="req">*</span></label>
                        <input type="text" id="lastname" name="lastname" class="input-pact"
                               value="<?= htmlspecialchars($form_data['lastname']) ?>" placeholder="Ex. Ouedraogo" required>
                    </div>
                </div>

                <!-- 2. Nom d'utilisateur & Téléphone (Contact obligatoire) -->
                <div class="form-grid-2">
                    <div class="form-group-pact">
                        <label for="username">
                            Nom d'utilisateur <span class="req">*</span>
                            <span id="username-status" style="font-size: 0.78rem; font-weight: 600; margin-left: 8px;"></span>
                        </label>
                        <input type="text" id="username" name="username" class="input-pact"
                               value="<?= htmlspecialchars($form_data['username']) ?>"
                               placeholder="Ex. awa226" required autocomplete="username">
                        <div class="input-helper">
                            Identifiant unique de connexion.
                        </div>
                    </div>

                    <div class="form-group-pact">
                        <label for="phone">
                            📞 Téléphone (contact) <span class="req">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" class="input-pact"
                               value="<?= htmlspecialchars($form_data['phone']) ?>"
                               placeholder="Ex. 70 12 34 56" required autocomplete="tel">
                        <div class="input-helper">
                            Numéro de téléphone pour les notifications et le suivi.
                        </div>
                    </div>
                </div>

                <!-- 3. Année de naissance (Contrôle >= 18 ans) & Sexe -->
                <div class="form-grid-2">
                    <div class="form-group-pact">
                        <label for="birth_year">Année de naissance <span class="req">*</span></label>
                        <input type="number" id="birth_year" name="birth_year" class="input-pact"
                               min="1920" max="<?= $max_valid_yr ?>"
                               value="<?= $form_data['birth_year'] ? (int)$form_data['birth_year'] : '' ?>"
                               placeholder="Ex. 1998" required>
                        <div class="input-helper">Réservé aux personnes de 18 ans et plus (≤ <?= $max_valid_yr ?>).</div>
                    </div>

                    <div class="form-group-pact">
                        <label for="sexe">Sexe <span class="req">*</span></label>
                        <select id="sexe" name="sexe" class="input-pact" required>
                            <option value="">Sélectionnez...</option>
                            <option value="Femme" <?= ($form_data['sexe'] === 'Femme') ? 'selected' : '' ?>>Femme</option>
                            <option value="Homme" <?= ($form_data['sexe'] === 'Homme') ? 'selected' : '' ?>>Homme</option>
                        </select>
                        <div class="input-helper">Pour l'indicateur national d'inclusion (≥ 30% femmes).</div>
                    </div>
                </div>

                <!-- 4. Répartition territoriale (Région, Province, Commune) -->
                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 12px; padding: 1.1rem; margin-bottom: 1.2rem;">
                    <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b; margin-bottom: 0.8rem; display: flex; align-items: center; gap: 6px;">
                        📍 Localisation territoriale (Burkina Faso)
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group-pact">
                            <label for="region">Région</label>
                            <select id="region" name="region" class="input-pact">
                                <option value="">Sélectionnez votre région...</option>
                                <?php foreach ($regions_bf as $reg): ?>
                                    <option value="<?= htmlspecialchars($reg) ?>" <?= ($form_data['region'] === $reg) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group-pact">
                            <label for="province">Province</label>
                            <select id="province" name="province" class="input-pact">
                                <option value="">Sélectionnez votre province...</option>
                                <?php foreach ($provinces_bf as $prv): ?>
                                    <option value="<?= htmlspecialchars($prv) ?>" <?= ($form_data['province'] === $prv) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prv) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-pact" style="margin-bottom: 0;">
                        <label for="commune">Commune / Ville</label>
                        <input list="communes-list" id="commune" name="commune" class="input-pact"
                               value="<?= htmlspecialchars($form_data['commune']) ?>"
                               placeholder="Tapez pour rechercher votre commune (ex. Ouagadougou, Bobo-Dioulasso...)">
                        <datalist id="communes-list">
                            <?php foreach ($communes_bf as $com): ?>
                                <option value="<?= htmlspecialchars($com) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                </div>

                <!-- 5. Situation socioprofessionnelle -->
                <div class="form-grid-2">
                    <div class="form-group-pact">
                        <label for="statut">Statut professionnel <span class="req">*</span></label>
                        <select id="statut" name="statut" class="input-pact" required>
                            <option value="">Sélectionnez...</option>
                            <?php foreach ($statuts_bf as $k => $v): ?>
                                <option value="<?= htmlspecialchars($k) ?>" <?= ($form_data['statut'] === $k) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($v) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group-pact">
                        <label for="niveau_instruction">
                            Niveau d'instruction
                            <span class="badge-optional">Facultatif</span>
                        </label>
                        <select id="niveau_instruction" name="niveau_instruction" class="input-pact">
                            <option value="">Sélectionnez...</option>
                            <?php foreach ($niveaux_bf as $niv): ?>
                                <option value="<?= htmlspecialchars($niv) ?>" <?= ($form_data['niveau_instruction'] === $niv) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($niv) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group-pact">
                    <label for="structure_appartenance">
                        Structure d'appartenance
                        <span class="badge-optional">Facultatif</span>
                    </label>
                    <input type="text" id="structure_appartenance" name="structure_appartenance" class="input-pact"
                           value="<?= htmlspecialchars($form_data['structure_appartenance']) ?>"
                           placeholder="Ex. Ministère, Université, Entreprise, Association...">
                </div>

                <!-- 6. EMAIL — OPTIONNEL ! -->
                <div class="form-group-pact" style="background: #f0faf5; padding: 1rem; border-radius: 12px; border: 1.5px dashed var(--green);">
                    <label for="email" style="color: var(--green-dark);">
                        📧 Adresse e-mail
                        <span class="badge-optional">Facultatif</span>
                        <span id="email-status" style="font-size: 0.78rem; font-weight: 600; margin-left: 8px;"></span>
                    </label>
                    <input type="email" id="email" name="email" class="input-pact"
                           value="<?= htmlspecialchars($form_data['email']) ?>"
                           placeholder="laissez vide si vous n'avez pas d'e-mail">
                    <div class="input-helper" style="color: #065f46;">
                        ✅ Si vous n'avez pas d'adresse e-mail, <strong>laissez ce champ vide</strong>. Votre nom d'utilisateur suffira pour vous connecter.
                    </div>
                </div>

                <!-- 7. Mots de passe -->
                <div class="form-grid-2">
                    <div class="form-group-pact">
                        <label for="password">Mot de passe <span class="req">*</span></label>
                        <input type="password" id="password" name="password" class="input-pact"
                               placeholder="Au moins 6 caractères" required autocomplete="new-password">
                    </div>
                    <div class="form-group-pact">
                        <label for="password_confirm">Confirmer le mot de passe <span class="req">*</span></label>
                        <input type="password" id="password_confirm" name="password_confirm" class="input-pact"
                               placeholder="Répétez le mot de passe" required autocomplete="new-password">
                    </div>
                </div>

                <!-- 8. Consentement aux données personnelles (EF-INS-03 / Loi 001-2021/AN) -->
                <div class="form-group-pact" style="background: #fffbeb; border: 1.5px solid #fef3c7; border-radius: 12px; padding: 1rem; margin-top: 1.5rem;">
                    <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-weight: 500; font-size: 0.88rem; color: #78350f; line-height: 1.45;">
                        <input type="checkbox" name="consentement" value="1" <?= $form_data['consentement'] ? 'checked' : '' ?> required
                               style="width: 18px; height: 18px; margin-top: 2px; accent-color: var(--green); flex-shrink: 0;">
                        <span>
                            <strong>J'accepte le traitement de mes données personnelles</strong> dans le cadre du Programme National de Littératie Numérique (PACTDIGITAL-BF), conformément à la <strong>loi n°001-2021/AN</strong> du Burkina Faso relative à la protection des personnes à l'égard du traitement des données à caractère personnel. <span class="req">*</span>
                        </span>
                    </label>
                </div>

                <!-- Bouton de soumission -->
                <button type="submit" id="btn-submit-signup" class="btn-signup-pact">
                    🚀 Créer mon compte citoyen et commencer
                </button>
            </form>

            <div class="signup-footer-links">
                Vous avez déjà un compte ?
                <a href="<?= $login_url ?>">Connectez-vous ici</a>
            </div>

        </div>
    </div>
</div>

<script>
(function() {
    const usernameInput  = document.getElementById('username');
    const usernameStatus = document.getElementById('username-status');
    const emailInput     = document.getElementById('email');
    const emailStatus    = document.getElementById('email-status');
    const submitBtn      = document.getElementById('btn-submit-signup');

    let usernameAvailable = true;
    let emailAvailable    = true;

    function updateSubmitState() {
        if (!usernameAvailable || !emailAvailable) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
        } else {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        }
    }

    // Debounce timer pour vérification du nom d'utilisateur
    let userTimer = null;
    usernameInput.addEventListener('input', function() {
        clearTimeout(userTimer);
        const val = this.value.trim();
        if (val.length < 3) {
            usernameStatus.textContent = '';
            usernameInput.style.borderColor = '';
            usernameAvailable = true;
            updateSubmitState();
            return;
        }

        usernameStatus.textContent = '⏳ Vérification...';
        usernameStatus.style.color = '#64748b';

        userTimer = setTimeout(async function() {
            try {
                const resp = await fetch('<?= (new moodle_url('/local/pactdigital/signup.php'))->out(false) ?>?action=check_username&username=' + encodeURIComponent(val));
                const data = await resp.json();
                usernameStatus.textContent = data.message;
                if (data.available) {
                    usernameStatus.style.color = '#15803d';
                    usernameInput.style.borderColor = '#22c55e';
                    usernameAvailable = true;
                } else {
                    usernameStatus.style.color = '#b91c1c';
                    usernameInput.style.borderColor = '#ef4444';
                    usernameAvailable = false;
                }
            } catch (e) {
                usernameStatus.textContent = '';
                usernameAvailable = true;
            }
            updateSubmitState();
        }, 350);
    });

    // Debounce timer pour vérification de l'e-mail (si renseigné)
    let emailTimer = null;
    emailInput.addEventListener('input', function() {
        clearTimeout(emailTimer);
        const val = this.value.trim();
        if (!val) {
            emailStatus.textContent = '';
            emailInput.style.borderColor = '';
            emailAvailable = true;
            updateSubmitState();
            return;
        }

        emailStatus.textContent = '⏳ Vérification...';
        emailStatus.style.color = '#64748b';

        emailTimer = setTimeout(async function() {
            try {
                const resp = await fetch('<?= (new moodle_url('/local/pactdigital/signup.php'))->out(false) ?>?action=check_email&email=' + encodeURIComponent(val));
                const data = await resp.json();
                emailStatus.textContent = data.message;
                if (data.available) {
                    emailStatus.style.color = '#15803d';
                    emailInput.style.borderColor = '#22c55e';
                    emailAvailable = true;
                } else {
                    emailStatus.style.color = '#b91c1c';
                    emailInput.style.borderColor = '#ef4444';
                    emailAvailable = false;
                }
            } catch (e) {
                emailStatus.textContent = '';
                emailAvailable = true;
            }
            updateSubmitState();
        }, 350);
    });
})();
</script>

<?php
echo $OUTPUT->footer();
