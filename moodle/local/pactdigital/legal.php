<?php
/**
 * PACTDIGITAL-BF — Mentions Légales & Politique de Confidentialité (VIS-13)
 *
 * Page publique présentant le cadre juridique, l'éditeur officiel et la conformité
 * avec la Loi N° 001-2021/AN sur la protection des données personnelles au Burkina Faso.
 */

require_once('../../config.php');

$PAGE->set_url(new moodle_url('/local/pactdigital/legal.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Mentions Légales & Confidentialité — PACTDIGITAL-BF');
$PAGE->set_heading('Mentions Légales & Confidentialité');

echo $OUTPUT->header();
?>

<style>
:root {
    --pact-green: var(--vert-700, #045527);
    --pact-green-light: var(--vert-50, #ecf7f0);
    --pact-yellow: var(--or-300, #ffed00);
    --pact-red: var(--rouge-500, #e30613);
    --pact-dark: var(--texte, #211d1a);
    --pact-grey: var(--texte-attenue, #635c55);
    --pact-border: var(--bordure, #e2ddd6);
}

.legal-wrapper {
    max-width: 1000px;
    margin: 0 auto 60px auto;
    font-family: var(--police-texte, "Inter", system-ui, -apple-system, sans-serif);
    color: var(--pact-dark);
    line-height: 1.65;
}

.legal-hero {
    background: linear-gradient(135deg, var(--vert-900, #032e16) 0%, var(--vert-700, #045527) 60%, var(--vert-500, #00843b) 100%);
    border-radius: var(--rayon-lg, 12px);
    padding: 35px 30px;
    color: #fff;
    margin-bottom: 30px;
    box-shadow: var(--ombre-md);
    text-align: center;
}
.legal-hero h1 {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 2.1rem;
    font-weight: 800;
    margin: 0 0 10px 0;
    color: #fff;
}
.legal-hero p {
    font-size: 1.05rem;
    opacity: 0.92;
    max-width: 700px;
    margin: 0 auto;
}

.legal-card {
    background: #fff;
    border-radius: var(--rayon-lg, 12px);
    border: 1px solid var(--pact-border);
    padding: 32px;
    box-shadow: var(--ombre-sm);
    margin-bottom: 24px;
}
.legal-card h2 {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--pact-green);
    margin: 0 0 16px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--pact-green-light);
    display: flex;
    align-items: center;
    gap: 10px;
}
.legal-card h3 {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--pact-dark);
    margin: 20px 0 8px 0;
}
.legal-card p, .legal-card ul {
    font-size: 0.93rem;
    color: var(--pact-dark);
    margin: 0 0 14px 0;
}
.legal-card ul {
    padding-left: 24px;
}
.legal-card li {
    margin-bottom: 6px;
}

.law-badge {
    background: var(--vert-50, #ecf7f0);
    border-left: 4px solid var(--vert-500, #00843b);
    border-radius: var(--rayon-md, 8px);
    padding: 16px 20px;
    margin: 18px 0;
    font-size: 0.9rem;
    color: var(--vert-700, #045527);
}
.law-badge strong {
    color: var(--vert-800, #04411f);
}
</style>

<div class="legal-wrapper">

    <!-- Hero Header -->
    <div class="legal-hero">
        <h1>Mentions Légales &amp; Protection des Données</h1>
        <p>Cadre juridique officiel, politique de confidentialité et conformité nationale avec la Loi N° 001-2021/AN du Burkina Faso.</p>
    </div>

    <!-- Section 1 : Éditeur officiel -->
    <div class="legal-card">
        <h2>1. Éditeur de la Plateforme</h2>
        <p>
            La plateforme nationale <strong>PACTDIGITAL-BF</strong> (Programme National de Littératie Numérique) est un service public officiel édité et administré par :
        </p>
        <ul>
            <li><strong>Ministère de tutelle :</strong> Ministère de la Transition Digitale, des Postes et des Communications Électroniques (MTDPCE) du Burkina Faso.</li>
            <li><strong>Direction opérationnelle :</strong> Direction Générale de la Transition Digitale (DGTD).</li>
            <li><strong>Siège officiel :</strong> Avenue de l'Indépendance, Ouagadougou, Burkina Faso.</li>
            <li><strong>Courriel institutionnel :</strong> <a href="mailto:contact@transition-digitale.gov.bf">contact@transition-digitale.gov.bf</a></li>
            <li><strong>Directeur de la publication :</strong> Le Ministre de la Transition Digitale, des Postes et des Communications Électroniques ou son délégué légal.</li>
        </ul>
    </div>

    <!-- Section 2 : Cadre légal et conformité CIL -->
    <div class="legal-card" id="loi-001-2021">
        <h2>2. Protection des Données Personnelles &amp; Conformité CIL</h2>
        
        <div class="law-badge">
            <strong>Cadre législatif officiel :</strong><br>
            Le traitement des données au sein de la plateforme PACTDIGITAL-BF est strictement encadré par la 
            <strong>Loi N° 001-2021/AN du 30 mars 2021 portant protection des personnes à l'égard du traitement des données à caractère personnel au Burkina Faso</strong>, 
            sous le contrôle de la <strong>Commission de l'Informatique et des Libertés (CIL)</strong>.
        </div>

        <h3>A. Finalités de la collecte des données</h3>
        <p>Les données à caractère personnel collectées lors de l'inscription et du parcours de formation ont pour strictes finalités :</p>
        <ul>
            <li>La création du compte citoyen et la délivrance des accès sécurisés.</li>
            <li>Le suivi pédagogique individualisé (progression, participation aux quiz et devoirs).</li>
            <li>La génération et la vérification cryptographique des attestations officielles de réussite (QR Code).</li>
            <li>La production de statistiques territoriales anonymisées (par région, sexe et tranche d'âge) pour le pilotage des politiques publiques nationales d'inclusion numérique.</li>
        </ul>

        <h3>B. Nature des données collectées</h3>
        <p>Dans le respect du principe de minimisation des données :</p>
        <ul>
            <li><strong>Données obligatoires :</strong> Nom, prénom, numéro de téléphone (utilisé comme identifiant de connexion), sexe, région et commune de résidence.</li>
            <li><strong>Données facultatives :</strong> Adresse email, tranche d'âge, statut socio-professionnel, niveau d'instruction.</li>
        </ul>

        <h3>C. Absence d'exploitation commerciale</h3>
        <p>
            <strong>Aucune donnée personnelle n'est vendue, louée ou cédée à des tiers à des fins commerciales ou publicitaires.</strong> Les données restent la propriété souveraine de l'État burkinabè et sont conservées sur des infrastructures sécurisées nationales.
        </p>

        <h3>D. Droits des citoyens</h3>
        <p>
            Conformément aux articles 25 et suivants de la Loi N° 001-2021/AN, chaque citoyen inscrit bénéficie :
        </p>
        <ul>
            <li>D'un <strong>droit d'accès</strong> à ses données personnelles enregistrées.</li>
            <li>D'un <strong>droit de rectification</strong> des informations erronées ou obsolètes.</li>
            <li>D'un <strong>droit de suppression</strong> de son compte citoyen sur simple demande écrite.</li>
        </ul>
        <p>
            Pour exercer ces droits, vous pouvez nous adresser une demande via le formulaire de contact ou par courriel à <strong>support@pactdigital.bf</strong>.
        </p>

        <h3 id="articles-loi" style="margin-top: 24px; color: var(--pact-green);">E. Ce que spécifie la Loi N° 001-2021/AN (Articles clés)</h3>
        <p style="font-size: 0.9rem; color: #475569;">
            Voici les dispositions phares de la loi burkinabè garantissant la protection de vos renseignements personnels sur PACTDIGITAL-BF :
        </p>
        <div style="display: grid; gap: 12px; margin-top: 14px;">
            <div style="background: #f8fafc; border-left: 4px solid #00843b; border-radius: 6px; padding: 12px 16px;">
                <strong style="color: #045527; font-size: 0.95rem;">Article 14 — Principe de finalité et de proportionnalité :</strong>
                <p style="margin: 4px 0 0 0; font-size: 0.88rem; color: #334155;">
                    Les données doivent être collectées pour des finalités déterminées, explicites et légitimes. Elles ne peuvent être traitées ultérieurement de manière incompatible avec ces finalités, et doivent être adéquates, pertinentes et non excessives.
                </p>
            </div>
            <div style="background: #f8fafc; border-left: 4px solid #00843b; border-radius: 6px; padding: 12px 16px;">
                <strong style="color: #045527; font-size: 0.95rem;">Article 17 — Consentement préalable et éclairé :</strong>
                <p style="margin: 4px 0 0 0; font-size: 0.88rem; color: #334155;">
                    Le traitement des données personnelles n'est licite que si la personne concernée a donné son accord préalable, libre, spécifique et sans équivoque, après avoir été informée de la finalité du traitement.
                </p>
            </div>
            <div style="background: #f8fafc; border-left: 4px solid #00843b; border-radius: 6px; padding: 12px 16px;">
                <strong style="color: #045527; font-size: 0.95rem;">Articles 25 &amp; 26 — Droit d'information et droit d'accès :</strong>
                <p style="margin: 4px 0 0 0; font-size: 0.88rem; color: #334155;">
                    Toute personne a le droit de connaître l'identité du responsable de traitement, la nature des données enregistrées et d'en obtenir communication intégrale sans restriction.
                </p>
            </div>
            <div style="background: #f8fafc; border-left: 4px solid #00843b; border-radius: 6px; padding: 12px 16px;">
                <strong style="color: #045527; font-size: 0.95rem;">Articles 27 &amp; 28 — Droit de rectification et droit à l'oubli :</strong>
                <p style="margin: 4px 0 0 0; font-size: 0.88rem; color: #334155;">
                    Le citoyen peut exiger la mise à jour, la correction ou la suppression immédiate de ses données personnelles inexactes, périmées ou dont la conservation n'est plus justifiée.
                </p>
            </div>
            <div style="background: #f8fafc; border-left: 4px solid #00843b; border-radius: 6px; padding: 12px 16px;">
                <strong style="color: #045527; font-size: 0.95rem;">Article 46 — Sécurité, chiffrement et souveraineté :</strong>
                <p style="margin: 4px 0 0 0; font-size: 0.88rem; color: #334155;">
                    Le responsable de traitement est légalement tenu de mettre en œuvre toutes les mesures techniques pour préserver la confidentialité absolue des données et empêcher toute intrusion ou fuite vers des tiers non autorisés.
                </p>
            </div>
        </div>
    </div>

    <!-- Section 3 : Hébergement & Sécurité souveraine -->
    <div class="legal-card">
        <h2>3. Hébergement &amp; Sécurité du Système</h2>
        <p>
            L'infrastructure technique de PACTDIGITAL-BF est hébergée sur le réseau informatique national du Gouvernement du Burkina Faso.
        </p>
        <ul>
            <li>Les échanges de données sont systématiquement chiffrés selon les protocoles de sécurité standard (HTTPS / TLS).</li>
            <li>Les mots de passe sont hachés de manière irréversible avec des algorithmes cryptographiques robustes (BCrypt).</li>
            <li>Les attestations sont horodatées et vérifiables par signature numérique.</li>
        </ul>
    </div>

    <!-- Section 4 : Propriété intellectuelle -->
    <div class="legal-card">
        <h2>4. Propriété Intellectuelle &amp; Licence des Contenus</h2>
        <p>
            Les parcours pédagogiques, supports de cours, vidéos, infographies et référentiels de compétences publiés sur PACTDIGITAL-BF sont la propriété exclusive du MTDPCE ou mis à disposition sous licence libre éducative avec l'accord de leurs auteurs respectifs.
        </p>
        <p>
            Toute reproduction ou diffusion à but lucratif sans accord préalable exprès du Ministère est formellement interdite. Les citoyens sont toutefois encouragés à télécharger et partager les fiches mémos et guides pratiques pour un usage d'apprentissage personnel et familial.
        </p>
    </div>

    <!-- Section 5 : Conditions d'utilisation -->
    <div class="legal-card">
        <h2>5. Règles de Conduite &amp; Engagements de l'Apprenant</h2>
        <p>En s'inscrivant sur la plateforme, l'apprenant s'engage à :</p>
        <ul>
            <li>Fournir des renseignements sincères lors de son inscription.</li>
            <li>Préserver la confidentialité de son mot de passe.</li>
            <li>Passer les évaluations et examens de certification de manière loyale et personnelle.</li>
            <li>Respecter les autres membres de la communauté et les formateurs dans les espaces de discussion et d'échange.</li>
        </ul>
    </div>

</div>

<?php
echo $OUTPUT->footer();
