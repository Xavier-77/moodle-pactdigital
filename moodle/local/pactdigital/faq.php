<?php
/**
 * PACTDIGITAL-BF — Foire Aux Questions (FAQ Citoyenne) (VIS-08)
 *
 * Page publique interactive permettant aux citoyens, apprenants et formateurs
 * de trouver immédiatement des réponses claires sur le programme national.
 */

require_once('../../config.php');

$PAGE->set_url(new moodle_url('/local/pactdigital/faq.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Foire Aux Questions (FAQ) — PACTDIGITAL-BF');
$PAGE->set_heading('Foire Aux Questions');

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

.faq-container {
    max-width: 1100px;
    margin: 0 auto 50px auto;
    font-family: var(--police-texte, "Inter", system-ui, -apple-system, sans-serif);
    color: var(--pact-dark);
}

.faq-hero {
    background: linear-gradient(135deg, var(--vert-900, #032e16) 0%, var(--vert-700, #045527) 60%, var(--vert-500, #00843b) 100%);
    border-radius: var(--rayon-lg, 12px);
    padding: 40px 30px;
    color: #fff;
    text-align: center;
    margin-bottom: 30px;
    box-shadow: var(--ombre-md);
}
.faq-hero h1 {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 2.1rem;
    font-weight: 800;
    margin: 0 0 10px 0;
    color: #fff;
}
.faq-hero p {
    font-size: 1.05rem;
    opacity: 0.92;
    max-width: 750px;
    margin: 0 auto 24px auto;
}

.faq-search-box {
    max-width: 600px;
    margin: 0 auto;
    position: relative;
}
.faq-search-input {
    width: 100%;
    padding: 14px 20px 14px 48px;
    border-radius: var(--rayon-md, 8px);
    border: none;
    font-size: 1rem;
    box-shadow: var(--ombre-md);
    outline: none;
    color: var(--pact-dark);
}
.faq-search-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.2rem;
    color: var(--pact-grey);
}

/* ── Filter Pills ────────────────────────────────────────────── */
.faq-filters {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}
.faq-filter-btn {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    background: #fff;
    border: 1px solid var(--pact-border);
    padding: 8px 18px;
    border-radius: var(--rayon-md, 8px);
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--pact-grey);
    cursor: pointer;
    transition: all 0.2s;
}
.faq-filter-btn:hover, .faq-filter-btn.active {
    background: var(--action, #00843b);
    color: #fff;
    border-color: var(--action, #00843b);
}

/* ── Accordion ───────────────────────────────────────────────── */
.faq-accordion {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.faq-item {
    background: #fff;
    border: 1px solid var(--pact-border);
    border-radius: var(--rayon-lg, 12px);
    overflow: hidden;
    transition: all 0.2s;
    box-shadow: var(--ombre-sm);
}
.faq-item.active {
    border-color: var(--vert-500, #00843b);
    box-shadow: var(--ombre-md);
}
.faq-question {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    padding: 18px 24px;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--pact-dark);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    user-select: none;
}
.faq-question:hover {
    background: var(--surface-basse, #fcfbf9);
    color: var(--pact-green);
}
.faq-toggle-icon {
    font-size: 1.3rem;
    transition: transform 0.25s ease;
    color: var(--pact-green);
}
.faq-item.active .faq-toggle-icon {
    transform: rotate(45deg);
}
.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out, padding 0.3s ease;
    background: #fff;
    padding: 0 24px;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--pact-dark);
    border-top: 1px solid transparent;
}
.faq-item.active .faq-answer {
    max-height: 400px;
    padding: 14px 24px 20px 24px;
    border-top-color: var(--pact-border);
}
.faq-tag {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 700;
    background: var(--pact-green-light);
    color: var(--pact-green);
    margin-right: 8px;
    vertical-align: middle;
}

/* ── Bottom CTA ──────────────────────────────────────────────── */
.faq-cta-box {
    background: var(--surface-enfoncee, #f7f5f2);
    border: 1.5px dashed var(--bordure-forte, #8e867f);
    border-radius: var(--rayon-lg, 12px);
    padding: 30px;
    text-align: center;
    margin-top: 40px;
}
.faq-cta-box h3 {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--pact-green);
    margin: 0 0 8px 0;
}
.faq-cta-box p {
    font-size: 0.92rem;
    color: var(--pact-grey);
    margin: 0 0 18px 0;
}
.btn-faq-contact {
    font-family: var(--police-titre, "Archivo", system-ui, sans-serif);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--action, #00843b);
    color: #fff;
    font-weight: 700;
    font-size: 0.92rem;
    padding: 10px 24px;
    border-radius: var(--rayon-md, 8px);
    text-decoration: none;
    transition: all 0.2s;
}
.btn-faq-contact:hover {
    background: var(--action-survol, #026c31);
    color: #fff;
    transform: translateY(-1px);
}
</style>

<div class="faq-container">

    <!-- Hero Section -->
    <div class="faq-hero">
        <h1>❓ Foire Aux Questions (FAQ)</h1>
        <p>Retrouvez toutes les réponses officielles sur le programme PACTDIGITAL-BF, les inscriptions sans email, les certifications Bronze/Argent/Or et l'accès par examen direct.</p>
        
        <div class="faq-search-box">
            <span class="faq-search-icon">🔍</span>
            <input type="text" id="faq-search-input" class="faq-search-input" placeholder="Rechercher une question ou un mot-clé (ex: certificat, téléphone, voie 2)…" oninput="filterFaq()">
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="faq-filters">
        <button class="faq-filter-btn active" onclick="filterCategory('all', this)">Toutes les questions</button>
        <button class="faq-filter-btn" onclick="filterCategory('inscription', this)">📱 Inscription & Compte</button>
        <button class="faq-filter-btn" onclick="filterCategory('parcours', this)">🎓 Parcours & Niveaux</button>
        <button class="faq-filter-btn" onclick="filterCategory('voie2', this)">⚡ Voie 2 (Examen direct)</button>
        <button class="faq-filter-btn" onclick="filterCategory('certificat', this)">🏆 Certificats & QR Code</button>
        <button class="faq-filter-btn" onclick="filterCategory('formateurs', this)">🧑‍🏫 Formateurs & Régions</button>
    </div>

    <!-- Accordion Items -->
    <div class="faq-accordion" id="faq-accordion">

        <!-- Q1 -->
        <div class="faq-item" data-cat="inscription">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">📱 Inscription</span> Qui peut s'inscrire au programme PACTDIGITAL-BF ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                Le Programme National de Littératie Numérique (PACTDIGITAL-BF) est ouvert à l'ensemble des citoyennes et citoyens burkinabè : élèves, étudiants, femmes, jeunes en insertion, agents publics de l'État, salariés du secteur privé, entrepreneurs, artisans et commerçants. Aucune condition préalable de diplôme n'est requise.
            </div>
        </div>

        <!-- Q2 -->
        <div class="faq-item" data-cat="inscription">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">📱 Inscription</span> L'inscription et les cours sont-ils payants ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <strong>Non, l'ensemble du programme est 100% gratuit.</strong> PACTDIGITAL-BF est une initiative publique de l'État burkinabè financée et pilotée par le Ministère de la Transition Digitale, des Postes et des Communications Électroniques (MTDPCE) avec l'appui de partenaires au développement. Aucun frais d'inscription, de cours ou de délivrance d'attestation ne vous sera jamais demandé.
            </div>
        </div>

        <!-- Q3 -->
        <div class="faq-item" data-cat="inscription">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">📱 Inscription</span> Est-il possible de s'inscrire sans adresse email ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <strong>Oui, tout à fait !</strong> Afin de garantir l'inclusion numérique de tous, l'adresse email est strictement optionnelle. Vous pouvez créer votre compte citoyen simplement avec votre <strong>numéro de téléphone burkinabè (Orange, Moov ou Telecel)</strong>. Votre numéro sert d'identifiant unique pour vous connecter.
            </div>
        </div>

        <!-- Q4 -->
        <div class="faq-item" data-cat="parcours">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">🎓 Parcours</span> Quels sont les différents niveaux de compétences (Bronze, Argent, Or) ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                Le référentiel national PACTDIGITAL-BF s'articule autour de 3 paliers progressifs :
                <ul>
                    <li><strong>🥉 Niveau Bronze (Initié) :</strong> Prise en main du smartphone/ordinateur, navigation internet en toute sécurité, messagerie, protection des données personnelles.</li>
                    <li><strong>🥈 Niveau Argent (Praticien) :</strong> Bureautique avancée (documents, tableurs), collaboration cloud, démarches administratives en ligne (e-services publics du Burkina Faso), hygiène cybernétique.</li>
                    <li><strong>🥇 Niveau Or (Expert / Leader) :</strong> Gestion de projets numériques, veille informationnelle, automatisation, sensibilisation à l'Intelligence Artificielle et aux technologies émergentes.</li>
                </ul>
            </div>
        </div>

        <!-- Q5 -->
        <div class="faq-item" data-cat="voie2">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">⚡ Voie 2</span> Qu'est-ce que la « Voie 2 » ou Certification par Examen Direct ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                La <strong>Voie 2 (Validation des Acquis / Examen direct)</strong> permet aux citoyens disposant déjà de compétences numériques de passer directement l'examen de certification d'un cours sans obligation de suivre tous les modules d'apprentissage pas à pas. En cas de réussite avec un score d'au moins 70%, votre attestation officielle vous est instantanément délivrée. Si vous échouez, vous pouvez reprendre la Voie 1 (parcours d'apprentissage complet).
            </div>
        </div>

        <!-- Q6 -->
        <div class="faq-item" data-cat="certificat">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">🏆 Certificat</span> Quelle est la valeur de l'attestation officielle PACTDIGITAL-BF ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                L'attestation officielle PACTDIGITAL-BF est délivrée sous l'autorité du MTDPCE et de la DGTD. Chaque certificat comporte un <strong>QR Code sécurisé et une clé cryptographique unique à 12 caractères</strong> vérifiable en direct sur le portail public national. Elle atteste formellement de vos compétences numériques auprès des employeurs, concours et administrations.
            </div>
        </div>

        <!-- Q7 -->
        <div class="faq-item" data-cat="certificat">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">🏆 Certificat</span> Comment un employeur peut-il vérifier mon attestation ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                L'employeur peut simplement scanner le QR Code présent sur votre attestation PDF ou papier avec son smartphone, ou se rendre sur la page de vérification <a href="/local/pactdigital/verify.php"><strong>/local/pactdigital/verify.php</strong></a> et saisir votre code d'attestation. Le système affichera immédiatement votre nom, la formation validée, la note obtenue et la date d'émission.
            </div>
        </div>

        <!-- Q8 -->
        <div class="faq-item" data-cat="parcours">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">🎓 Parcours</span> Peut-on suivre les cours sur smartphone ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <strong>Oui !</strong> La plateforme est 100% responsive et optimisée pour un affichage fluide sur smartphone Android ou iOS. Les contenus sont compressés pour réduire au maximum la consommation de vos données mobiles (forfaits data).
            </div>
        </div>

        <!-- Q9 -->
        <div class="faq-item" data-cat="formateurs">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">🧑‍🏫 Formateurs</span> Comment bénéficier d'un accompagnement en présentiel dans ma commune ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                Des cohortes présentielles encadrées par des formateurs certifiés PACTDIGITAL sont déployées dans les 17 régions et 47 provinces du Burkina Faso. Les séances ont lieu dans les Maisons de la Jeunesse, cyberclasses scolaires et centres communaux. Renseignez-vous auprès de votre direction régionale MTDPCE ou sur notre page de contact.
            </div>
        </div>

        <!-- Q10 -->
        <div class="faq-item" data-cat="inscription">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span><span class="faq-tag">📱 Inscription</span> J'ai oublié mon mot de passe, comment le réinitialiser ?</span>
                <span class="faq-toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                Sur la page de connexion, cliquez sur <em>« Mot de passe oublié ? »</em>. Saisissez votre numéro de téléphone ou votre adresse email. Si vous avez été inscrit par un formateur de cohorte, votre mot de passe temporaire initial est <strong><code>Pactdigital2026!</code></strong>.
            </div>
        </div>

    </div>

    <!-- Bottom Contact CTA -->
    <div class="faq-cta-box">
        <h3>💬 Vous avez une autre question ou besoin d'assistance ?</h3>
        <p>Notre équipe support et les relais régionaux du MTDPCE sont à votre écoute pour vous guider.</p>
        <a href="/local/pactdigital/contact.php" class="btn-faq-contact">
            📩 Contacter le Support Citoyen
        </a>
    </div>

</div>

<script>
function toggleFaq(headerEl) {
    const item = headerEl.closest('.faq-item');
    const isActive = item.classList.contains('active');
    
    // Fermer les autres (optionnel : style accordéon exclusif ou multi)
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));

    if (!isActive) {
        item.classList.add('active');
    }
}

function filterCategory(cat, btn) {
    document.querySelectorAll('.faq-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const searchVal = document.getElementById('faq-search-input').value.toLowerCase().trim();
    applyFilters(cat, searchVal);
}

function filterFaq() {
    const searchVal = document.getElementById('faq-search-input').value.toLowerCase().trim();
    const activeBtn = document.querySelector('.faq-filter-btn.active');
    const cat = activeBtn ? activeBtn.getAttribute('onclick').match(/'([^']+)'/)[1] : 'all';
    applyFilters(cat, searchVal);
}

function applyFilters(category, query) {
    const items = document.querySelectorAll('.faq-item');
    items.forEach(item => {
        const itemCat = item.getAttribute('data-cat');
        const text = item.innerText.toLowerCase();
        
        const matchesCat = (category === 'all' || itemCat === category);
        const matchesQuery = (query === '' || text.includes(query));

        if (matchesCat && matchesQuery) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php
echo $OUTPUT->footer();
