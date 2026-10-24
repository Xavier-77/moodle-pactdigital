<?php
/**
 * PACTDIGITAL – Admin Dashboard
 */

require_once(__DIR__ . '/../../config.php');

$PAGE->set_url(new moodle_url('/local/pactdigital/dashboard.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Tableau de bord PACTDIGITAL');
$PAGE->set_heading('Tableau de bord PACTDIGITAL-BF');
$PAGE->set_pagelayout('admin');

require_login();
$context = context_system::instance();
if (!has_capability('moodle/site:config', $context) && !has_capability('local/pactdigital:viewdashboard', $context)) {
    redirect(new moodle_url('/'), 'Accès refusé.', 3);
}

$data_url = (new moodle_url('/local/pactdigital/dashboard_data.php'))->out(false);
$burkina_regions_url = (new moodle_url('/local/pactdigital/burkina_regions.json'))->out(false);
$current_user_name = fullname($USER);

$armoiries_svg_path = $CFG->dirroot . '/theme/pactdigital/pix/armoiries.svg';
$armoiries_path = $CFG->dirroot . '/theme/pactdigital/pix/armoiries.png';
$armoiries_base64 = '';
if (file_exists($armoiries_svg_path)) {
    $armoiries_base64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($armoiries_svg_path));
} elseif (file_exists($armoiries_path)) {
    $armoiries_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($armoiries_path));
}

echo $OUTPUT->header();
?>

<!-- Leaflet.js pour la Cartographie SIG Interactive du Burkina Faso -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
:root {
    --green:  var(--vert-700, #045527);
    --red:    var(--rouge-500, #e30613);
    --gold:   var(--ocre-500, #b07800);
    --blue:   var(--bleu-500, #2a6d9b);
    --grey:   var(--texte-attenue, #635c55);
    --light:  var(--surface-basse, #fcfbf9);
    --border: var(--bordure, #e2ddd6);
    --white:  var(--surface, #ffffff);
    --shadow: var(--ombre-sm);
}
*{box-sizing:border-box;margin:0;padding:0}
body{background:var(--surface-enfoncee, #f7f5f2);font-family:var(--police-texte, "Inter", Arial, sans-serif);color:var(--texte, #211d1a)}

/* ── Layout ───────────────────────────────────────────────────────── */
.secondary-navigation {
    display: none !important;
}
.pact-dash{padding:20px;max-width:1600px;margin:0 auto}

/* ── Header bar ──────────────────────────────────────────────────── */
.pact-header{
    background:linear-gradient(135deg,var(--vert-900, #032e16) 0%,var(--vert-700, #045527) 60%,var(--vert-500, #00843b) 100%);
    color:#fff;padding:22px 28px;border-radius:var(--rayon-lg, 12px);
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:24px;box-shadow:var(--ombre-md);
}
.pact-header h1{font-family:var(--police-titre, "Archivo", sans-serif);font-size:1.5rem;font-weight:700;letter-spacing:.5px;color:#fff}
.pact-header p{font-size:.85rem;opacity:.88;margin-top:4px}
.pact-header .logo-area{display:flex;gap:12px;align-items:center}
.pact-header .badge{
    background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);
    padding:4px 12px;border-radius:20px;font-size:.78rem
}

/* ── Filter bar ──────────────────────────────────────────────────── */
.filter-bar{
    background:var(--white);border:1px solid var(--border);
    border-radius:var(--rayon-lg, 12px);padding:18px 22px;margin-bottom:24px;
    box-shadow:var(--shadow);
}
.filter-bar h3{font-family:var(--police-titre, "Archivo", sans-serif);color:var(--green);font-size:.95rem;margin-bottom:14px;
    display:flex;align-items:center;gap:8px}
.filter-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(175px,1fr));
    gap:12px;align-items:end
}
.filter-group label{font-family:var(--police-titre, "Archivo", sans-serif);font-size:.78rem;color:var(--grey);font-weight:600;
    text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px}
.filter-group select,.filter-group input{
    width:100%;padding:8px 10px;border:1px solid var(--bordure-forte, #8e867f);
    border-radius:var(--rayon-md, 8px);font-size:.88rem;background:#fff;color:var(--texte, #211d1a);
    transition:border-color .2s;
}
.filter-group select:focus,.filter-group input:focus{
    outline:none;border-color:var(--vert-500, #00843b)}
.filter-actions{display:flex;gap:10px;align-items:flex-end}
.btn{font-family:var(--police-titre, "Archivo", sans-serif);padding:8px 20px;border:none;border-radius:var(--rayon-md, 8px);cursor:pointer;
    font-size:.88rem;font-weight:600;transition:all .2s}
.btn-green{background:var(--action, #00843b);color:#fff}
.btn-green:hover{background:var(--action-survol, #026c31)}
.btn-outline{background:#fff;color:var(--green);border:1.5px solid var(--vert-500, #00843b)}
.btn-outline:hover{background:var(--vert-50, #ecf7f0)}
.btn-red{background:var(--red);color:#fff}
.btn-red:hover{background:var(--rouge-700, #a2040e)}

/* ── KPI Cards ───────────────────────────────────────────────────── */
.kpi-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(190px,1fr));
    gap:16px;margin-bottom:24px
}
.kpi-card{
    background:var(--white);border-radius:var(--rayon-lg, 12px);padding:20px;
    box-shadow:var(--shadow);border:1px solid var(--border);border-left:4px solid var(--vert-500, #00843b);
    display:flex;flex-direction:column;gap:6px;position:relative;overflow:hidden
}
.kpi-card::before{
    content:'';position:absolute;right:-15px;top:-15px;
    width:70px;height:70px;border-radius:50%;
    background:var(--vert-50, #ecf7f0)
}
.kpi-card.red{border-left-color:var(--red)}
.kpi-card.gold{border-left-color:var(--gold)}
.kpi-card.blue{border-left-color:var(--blue)}
.kpi-card .icon{font-size:1.5rem}
.kpi-card .label{font-family:var(--police-titre, "Archivo", sans-serif);font-size:.78rem;color:var(--grey);font-weight:600;
    text-transform:uppercase;letter-spacing:.4px}
.kpi-card .value{font-family:var(--police-titre, "Archivo", sans-serif);font-size:2rem;font-weight:800;color:var(--green);line-height:1}
.kpi-card.red .value{color:var(--red)}
.kpi-card.gold .value{color:var(--ocre-700, #7d5500)}
.kpi-card.blue .value{color:var(--blue)}
.kpi-card .sub{font-size:.75rem;color:var(--grey)}

/* ── Charts grid ─────────────────────────────────────────────────── */
.charts-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(380px,1fr));
    gap:20px;margin-bottom:24px
}
.chart-card{
    background:var(--white);border-radius:var(--rayon-lg, 12px);padding:22px;
    box-shadow:var(--shadow);border:1px solid var(--border);
}
.chart-card.full{grid-column:1/-1}
.chart-card h3{font-family:var(--police-titre, "Archivo", sans-serif);color:var(--green);font-size:.95rem;font-weight:700;
    margin-bottom:16px;display:flex;align-items:center;gap:8px}
.chart-wrap{position:relative;height:260px}
.chart-wrap.small{height:200px}
.chart-wrap.tall{height:320px}

/* ── Carte SIG Interactive Leaflet ──────────────────────────────── */
.pact-map-container{
    background:var(--white);border-radius:var(--rayon-lg, 12px);padding:22px;
    box-shadow:var(--shadow);margin-bottom:24px;border:1px solid var(--border);
}
.map-grid-wrap{
    display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:stretch
}
@media(max-width:992px){
    .map-grid-wrap{grid-template-columns:1fr}
}
#pact-map{
    height:480px;border-radius:var(--rayon-md, 8px);overflow:hidden;
    border:1px solid var(--border);background:var(--vert-50, #ecf7f0)
}
.map-legend{
    background:rgba(255,255,255,0.96);padding:10px 14px;border-radius:8px;
    font-size:0.78rem;line-height:1.5;box-shadow:0 2px 8px rgba(0,0,0,0.15);
    border:1px solid var(--border)
}
.map-legend-item{
    display:flex;align-items:center;gap:6px;margin-bottom:3px
}
.map-legend-color{
    width:16px;height:12px;border-radius:3px;
    border:1px solid rgba(0,0,0,0.2);display:inline-block
}

/* ── Table ────────────────────────────────────────────────────────── */
.table-card{
    background:var(--white);border-radius:var(--rayon-lg, 12px);padding:22px;
    box-shadow:var(--shadow);margin-bottom:24px;border:1px solid var(--border);
}
.table-card h3{font-family:var(--police-titre, "Archivo", sans-serif);color:var(--green);font-size:.95rem;font-weight:700;
    margin-bottom:16px;display:flex;align-items:center;justify-content:space-between}
.table-search{
    padding:7px 12px;border:1px solid var(--bordure-forte, #8e867f);border-radius:var(--rayon-md, 8px);
    font-size:.85rem;width:240px
}
.tbl-wrap{overflow-x:auto;max-height:480px;overflow-y:auto}
table{width:100%;border-collapse:collapse;font-size:.82rem}
thead{position:sticky;top:0;z-index:2}
thead th{
    font-family:var(--police-titre, "Archivo", sans-serif);
    background:var(--vert-800, #04411f);color:#fff;padding:10px 12px;
    text-align:left;font-weight:600;white-space:nowrap;cursor:pointer
}
thead th:hover{background:var(--vert-900, #032e16)}
tbody tr{border-bottom:1px solid var(--border);transition:background .15s}
tbody tr:hover{background:var(--vert-50, #ecf7f0)}
tbody td{padding:9px 12px;white-space:nowrap;color:var(--texte, #211d1a)}
.badge-pill{
    font-family:var(--police-titre, "Archivo", sans-serif);
    display:inline-block;padding:2px 9px;border-radius:20px;
    font-size:.72rem;font-weight:700
}
.badge-bronze{background:var(--ocre-50, #fdf4e3);color:var(--ocre-700, #7d5500)}
.badge-or{background:var(--or-50, #fffde6);color:var(--neutre-900, #211d1a);border:1px solid var(--or-500, #d4bc00)}
.badge-diamant{background:var(--bleu-50, #eef4f9);color:var(--bleu-700, #173f5f)}
.taux-bar{
    display:flex;align-items:center;gap:6px
}
.taux-bar .bar{
    flex:1;height:6px;background:var(--bordure, #e2ddd6);border-radius:3px;overflow:hidden
}
.taux-bar .fill{height:100%;background:var(--vert-500, #00843b);border-radius:3px}
.pagination{display:flex;gap:6px;margin-top:14px;align-items:center;
    justify-content:flex-end}
.pagination button{
    padding:5px 12px;border:1px solid var(--border);border-radius:6px;
    background:#fff;cursor:pointer;font-size:.82rem
}
.pagination button.active{background:var(--action, #00843b);color:#fff;border-color:var(--action, #00843b)}
.pagination button:hover:not(.active){background:var(--vert-50, #ecf7f0)}

/* ── Loading / empty ─────────────────────────────────────────────── */
.loading{display:flex;align-items:center;justify-content:center;
    height:200px;color:var(--grey);font-size:.9rem;gap:10px}
.spinner{width:22px;height:22px;border:3px solid var(--border);
    border-top-color:var(--green);border-radius:50%;
    animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.empty{text-align:center;padding:40px;color:var(--grey);font-size:.88rem}

/* ── Modals & Custom Exports ─────────────────────────────────────── */
.pact-modal-backdrop {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(10, 35, 20, 0.55);
    backdrop-filter: blur(4px);
    z-index: 99999;
    display: none;
    align-items: center; justify-content: center;
    padding: 16px;
    box-sizing: border-box;
}
.pact-modal {
    background: #ffffff;
    border-radius: 14px;
    width: 100%;
    max-width: 660px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 16px 45px rgba(0,0,0,0.3);
    border: 1px solid var(--border);
    animation: pactFadeIn 0.2s ease-out;
}
@keyframes pactFadeIn {
    from { opacity: 0; transform: translateY(-16px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.pact-modal-header {
    background: linear-gradient(135deg, var(--green) 0%, #1c7244 100%);
    color: #fff;
    padding: 16px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top-left-radius: 13px;
    border-top-right-radius: 13px;
}
.pact-modal-header h2 {
    font-size: 1.15rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #fff;
}
.pact-modal-close {
    background: rgba(255,255,255,0.18);
    border: none;
    color: #fff;
    font-size: 1.4rem;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    line-height: 1;
}
.pact-modal-close:hover {
    background: rgba(255,255,255,0.35);
}
.pact-modal-body {
    padding: 20px 22px;
}
.modal-section {
    margin-bottom: 18px;
}
.modal-section-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--green);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.format-pills {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 10px;
}
.format-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 10px;
    border: 2px solid var(--border);
    border-radius: 9px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fbfdfc;
    text-align: center;
}
.format-pill input { display: none; }
.format-pill .icon { font-size: 1.6rem; margin-bottom: 4px; }
.format-pill .name { font-size: 0.85rem; font-weight: 700; color: #333; }
.format-pill .desc { font-size: 0.72rem; color: var(--grey); margin-top: 2px; }
.format-pill:hover { border-color: var(--green); background: #f2f9f5; }
.format-pill.selected {
    border-color: var(--green);
    background: #eaf5ef;
    box-shadow: 0 0 0 1px var(--green);
}
.format-pill.selected .name { color: var(--green); }

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
    gap: 8px 12px;
    background: #f7faf8;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
}
.custom-check {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    color: #333;
    cursor: pointer;
    user-select: none;
}
.custom-check input[type="checkbox"], .custom-check input[type="radio"] {
    accent-color: var(--green);
    width: 16px;
    height: 16px;
    cursor: pointer;
}
.modal-input-group {
    margin-bottom: 12px;
}
.modal-input-group label {
    display: block;
    font-size: 0.78rem;
    color: var(--grey);
    font-weight: 600;
    margin-bottom: 4px;
}
.modal-input-group input[type="text"], .modal-input-group textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid var(--border);
    border-radius: 7px;
    font-size: 0.86rem;
    box-sizing: border-box;
    font-family: inherit;
}
.modal-input-group input[type="text"]:focus, .modal-input-group textarea:focus {
    outline: none;
    border-color: var(--green);
}
.pact-modal-footer {
    padding: 14px 22px;
    background: #f7faf8;
    border-top: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    border-bottom-left-radius: 13px;
    border-bottom-right-radius: 13px;
}

/* ── Toast Notifications ─────────────────────────────────────────── */
.toast-wrapper {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}
.pact-toast {
    background: #1e293b;
    color: #fff;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 600;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    gap: 10px;
    pointer-events: auto;
    animation: toastSlideIn 0.25s ease-out;
    max-width: 440px;
    border-left: 5px solid #25D366;
}
.pact-toast.toast-info { background: #0f172a; border-left-color: #1565C0; }
.pact-toast.toast-success { background: #145B37; border-left-color: #25D366; }
.pact-toast.toast-error { background: #7f1d1d; border-left-color: #ef4444; }
@keyframes toastSlideIn {
    from { opacity: 0; transform: translateY(-12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ── Print Styling for Official PDF Report ───────────────────────── */
#pact-printable-report {
    display: none;
}
@media print {
    .pact-modal-backdrop, .toast-wrapper {
        display: none !important;
    }
}

/* ── Responsive ──────────────────────────────────────────────────── */
@media(max-width:768px){
    .charts-grid{grid-template-columns:1fr}
    .kpi-grid{grid-template-columns:repeat(2,1fr)}
    .filter-grid{grid-template-columns:repeat(2,1fr)}
}
</style>

<div class="pact-dash">

  <!-- Header -->
  <div class="pact-header">
    <div>
      <h1>Tableau de bord PACTDIGITAL-BF</h1>
      <p>Statistiques complètes de la plateforme de formation numérique</p>
    </div>
    <div class="logo-area">
      <span class="badge">Mis à jour: <span id="last-update">–</span></span>
      
      <button class="btn" onclick="openReportModal()" style="background:#fad615;color:#000;font-weight:700;border:none;border-radius:20px;padding:8px 18px;box-shadow:0 3px 8px rgba(0,0,0,0.2);display:inline-flex;align-items:center;gap:6px;cursor:pointer;transition:transform 0.15s;">
        Exporter le rapport (PDF / Excel)
      </button>
    </div>
  </div>

  <!-- Filter bar -->
  <div class="filter-bar">
    <h3>Filtres</h3>
    <div class="filter-grid">
      <div class="filter-group">
        <label>Sexe</label>
        <select id="f-sexe"><option value="">Tous</option></select>
      </div>
      <div class="filter-group">
        <label>Région</label>
        <select id="f-region"><option value="">Toutes</option></select>
      </div>
      <div class="filter-group">
        <label>Province</label>
        <select id="f-province"><option value="">Toutes</option></select>
      </div>
      <div class="filter-group">
        <label>Commune</label>
        <select id="f-commune"><option value="">Toutes</option></select>
      </div>
      <div class="filter-group">
        <label>Statut</label>
        <select id="f-statut"><option value="">Tous</option></select>
      </div>
      <div class="filter-group">
        <label>Catégorie</label>
        <select id="f-catid"><option value="0">Toutes</option></select>
      </div>
      <div class="filter-group">
        <label>Niveau</label>
        <select id="f-niveau">
          <option value="">Tous</option>
          <option value="Bronze">Bronze</option>
          <option value="Argent">Argent</option>
          <option value="Or">Or</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Période d'inscription</label>
        <select id="f-period">
          <option value="0">Tout</option>
          <option value="7">7 derniers jours</option>
          <option value="30">30 derniers jours</option>
          <option value="90">3 mois</option>
          <option value="180">6 mois</option>
          <option value="365">1 an</option>
        </select>
      </div>
      <div class="filter-actions">
        <button class="btn btn-green" onclick="applyFilters()">Appliquer</button>
        <button class="btn btn-outline" onclick="resetFilters()">Réinitialiser</button>
      </div>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="kpi-grid" id="kpi-grid">
    <div class="loading"><div class="spinner"></div> Chargement…</div>
  </div>

  <!-- Charts Row 1 -->
  <div class="charts-grid">
    <div class="chart-card">
      <h3>Inscriptions dans le temps</h3>
      <div class="chart-wrap"><canvas id="chart-timeline"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>Répartition par sexe</h3>
      <div class="chart-wrap small"><canvas id="chart-gender"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>Statut des apprenants</h3>
      <div class="chart-wrap small"><canvas id="chart-statut"></canvas></div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!--  CARTE SIG INTERACTIVE DU BURKINA FASO (IMPACT TERRITORIAL)    -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div class="pact-map-container">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
      <div>
        <h3 style="margin-bottom:4px;color:var(--green);font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:8px;">
          Cartographie SIG de l'Impact Territorial (Burkina Faso)
        </h3>
        <p style="font-size:0.83rem;color:var(--grey);margin:0;">
          Carte choroplèthe interactive des 13 régions — Cliquez sur une région pour filtrer l'ensemble du tableau de bord
        </p>
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <label style="font-size:0.78rem;font-weight:700;color:var(--green);text-transform:uppercase;letter-spacing:0.5px;">Indicateur :</label>
        <select id="map-metric" onchange="updateMapVisuals()" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:0.86rem;font-weight:600;background:#fff;cursor:pointer;">
          <option value="inscrits">Apprenants inscrits</option>
          <option value="taux_completion">Taux de complétion (%)</option>
          <option value="taux_femmes">Taux de féminisation (%)</option>
          <option value="certificats">Attestations délivrées</option>
        </select>
        <button type="button" class="btn btn-outline" onclick="resetMapSelection()" style="padding:6px 14px;font-size:0.82rem;" title="Réinitialiser la sélection">
          Vue Nationale
        </button>
      </div>
    </div>

    <div class="map-grid-wrap">
      <!-- Conteneur Carte Leaflet -->
      <div id="pact-map"></div>

      <!-- Fiche synthétique Régionale / Nationale -->
      <div id="region-info-card" style="background:#f7faf8;border:1px solid var(--border);border-radius:10px;padding:20px;display:flex;flex-direction:column;justify-content:space-between;">
        <div>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
            <span style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--grey);letter-spacing:0.5px;">Fiche Territoriale</span>
            <span id="badge-region-status" class="badge-pill badge-green" style="font-size:0.7rem;">Vue Globale</span>
          </div>
          <h4 id="info-region-name" style="color:var(--green);font-size:1.35rem;font-weight:800;margin:4px 0 16px 0;">
            Burkina Faso
          </h4>

          <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="background:#fff;padding:12px;border-radius:8px;border:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
              <div>
                <div style="font-size:0.72rem;color:var(--grey);font-weight:700;text-transform:uppercase;">Apprenants Inscrits</div>
                <div id="info-val-users" style="font-size:1.4rem;font-weight:800;color:var(--green);line-height:1.2;">0</div>
              </div>
              
            </div>

            <div style="background:#fff;padding:12px;border-radius:8px;border:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
              <div>
                <div style="font-size:0.72rem;color:var(--grey);font-weight:700;text-transform:uppercase;">Taux de Complétion</div>
                <div id="info-val-completion" style="font-size:1.4rem;font-weight:800;color:var(--blue);line-height:1.2;">0%</div>
              </div>
              
            </div>

            <div style="background:#fff;padding:12px;border-radius:8px;border:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
              <div>
                <div style="font-size:0.72rem;color:var(--grey);font-weight:700;text-transform:uppercase;">Taux de Féminisation</div>
                <div id="info-val-femmes" style="font-size:1.4rem;font-weight:800;color:var(--red);line-height:1.2;">0%</div>
              </div>
              
            </div>

            <div style="background:#fff;padding:12px;border-radius:8px;border:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
              <div>
                <div style="font-size:0.72rem;color:var(--grey);font-weight:700;text-transform:uppercase;">Attestations Délivrées</div>
                <div id="info-val-certs" style="font-size:1.4rem;font-weight:800;color:#b8860b;line-height:1.2;">0</div>
              </div>
              
            </div>
          </div>
        </div>

        <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);">
          <button id="btn-filter-this-region" type="button" class="btn btn-green" style="width:100%;justify-content:center;display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:0.88rem;" onclick="filterDashboardFromMap()">
            <span>Filtrer ce tableau de bord</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row 2 -->
  <div class="charts-grid">
    <div class="chart-card">
      <h3>Répartition par Région</h3>
      <div class="chart-wrap tall"><canvas id="chart-region"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>Répartition par Province</h3>
      <div class="chart-wrap tall"><canvas id="chart-province"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>Répartition par Commune</h3>
      <div class="chart-wrap tall"><canvas id="chart-commune"></canvas></div>
    </div>
  </div>

  <!-- Charts Row 3 -->
  <div class="charts-grid">
    <div class="chart-card">
      <h3>Inscriptions par catégorie</h3>
      <div class="chart-wrap tall"><canvas id="chart-categories"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>Niveaux Bronze / Argent / Or</h3>
      <div class="chart-wrap"><canvas id="chart-niveaux"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>Niveau d'instruction</h3>
      <div class="chart-wrap small"><canvas id="chart-instruction"></canvas></div>
    </div>
  </div>

  <!-- Charts Row 4 : Indicateurs Qualité & Impact Pédagogique (PIL-05, PIL-06, PIL-09) -->
  <div class="charts-grid" style="margin-bottom:24px;">
    <!-- PIL-05 : Différentiel d'impact Pré-test vs Post-test -->
    <div class="chart-card">
      <h3>Impact Pédagogique (Pré-test vs Post-test)</h3>
      <div style="padding:16px 10px;text-align:center;">
        <div style="display:flex;justify-content:space-around;align-items:center;margin-bottom:16px;">
          <div style="background:#f1f5f9;border-radius:10px;padding:12px 18px;min-width:110px;">
            <div style="font-size:0.75rem;color:var(--grey);font-weight:700;">PRÉ-TEST INITIAL</div>
            <div id="val-pretest" style="font-size:1.6rem;font-weight:800;color:var(--grey);margin-top:4px;">-- %</div>
          </div>
          <div style="font-size:1.6rem;color:var(--green);font-weight:800;">➔</div>
          <div style="background:#e8f5ee;border-radius:10px;padding:12px 18px;min-width:110px;">
            <div style="font-size:0.75rem;color:var(--green);font-weight:700;">POST-TEST FINAL</div>
            <div id="val-posttest" style="font-size:1.6rem;font-weight:800;color:var(--green);margin-top:4px;">-- %</div>
          </div>
        </div>
        <div style="background:#ecfdf5;border:1px solid #10b981;border-radius:8px;padding:10px;font-size:0.9rem;color:#065f46;">
          Gain moyen de compétences : <strong id="val-gain-pts" style="font-size:1.1rem;color:#047857;">+0 pts</strong> 
          (<span id="val-gain-pct">+0%</span> de progression)
        </div>
      </div>
    </div>

    <!-- PIL-06 : Voie 1 (Parcours) vs Voie 2 (Examen direct) -->
    <div class="chart-card">
      <h3>Certifications par Voie d'Accès</h3>
      <div class="chart-wrap small"><canvas id="chart-voies"></canvas></div>
    </div>

    <!-- PIL-09 : Indice de satisfaction des cours -->
    <div class="chart-card">
      <h3>Indice de Satisfaction & Recommandation</h3>
      <div style="padding:18px 10px;text-align:center;">
        <div style="font-size:2.4rem;font-weight:800;color:#eab308;line-height:1;margin-bottom:4px;">
          <span id="val-csat-score">4.7</span> <span style="font-size:1.3rem;color:var(--grey);font-weight:600;">/ 5</span>
        </div>
        <div style="color:#eab308;font-size:1.2rem;letter-spacing:2px;margin-bottom:12px;">★★★★★</div>
        <div style="background:#fefce8;border:1px solid #fef08a;border-radius:8px;padding:10px;font-size:0.86rem;color:#713f12;">
          <strong id="val-csat-pct">94%</strong> d'apprenants satisfaits ou très satisfaits
          <br><small style="color:var(--grey);">Basé sur les évaluations de fin de module</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Top Courses Table -->
  <div class="table-card">
    <h3>
      Top 10 formations les plus suivies
    </h3>
    <div class="tbl-wrap">
      <table id="tbl-courses">
        <thead><tr>
          <th>Formation</th>
          <th>Catégorie</th>
          <th>Inscrits</th>
          <th>Complétés</th>
          <th>Taux de complétion</th>
        </tr></thead>
        <tbody id="tbl-courses-body">
          <tr><td colspan="5" class="loading"><div class="spinner"></div> Chargement…</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Learner Table -->
  <div class="table-card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
      <h3 style="margin-bottom:0;color:var(--green);font-size:1.05rem;font-weight:700;display:flex;align-items:center;gap:8px;">
        Liste des apprenants
      </h3>
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <input type="text" class="table-search" id="learner-search"
               placeholder="Rechercher un apprenant…" oninput="filterTable()">
        <button class="btn" onclick="openLearnerExportModal()" style="background:#fad615;color:#000;font-weight:700;border:none;border-radius:20px;padding:8px 16px;box-shadow:0 2px 6px rgba(0,0,0,0.15);display:inline-flex;align-items:center;gap:6px;cursor:pointer;font-size:.85rem;transition:all 0.15s;">
          Exporter la liste (CSV / Excel)
        </button>
      </div>
    </div>
    <div class="tbl-wrap">
      <table id="tbl-learners">
        <thead><tr>
          <th onclick="sortTable(0)">Nom ↕</th>
          <th onclick="sortTable(1)">Sexe ↕</th>
          <th onclick="sortTable(2)">Région ↕</th>
          <th onclick="sortTable(3)">Province ↕</th>
          <th onclick="sortTable(4)">Commune ↕</th>
          <th onclick="sortTable(5)">Statut ↕</th>
          <th onclick="sortTable(6)">Instruction ↕</th>
          <th onclick="sortTable(7)">Inscrits ↕</th>
          <th onclick="sortTable(8)">Complétés ↕</th>
          <th onclick="sortTable(9)">Attestations ↕</th>
          <th onclick="sortTable(10)">Taux ↕</th>
          <th onclick="sortTable(11)">Dernière connexion ↕</th>
        </tr></thead>
        <tbody id="tbl-learners-body">
          <tr><td colspan="12" class="loading"><div class="spinner"></div> Chargement…</td></tr>
        </tbody>
      </table>
    </div>
    <div class="pagination" id="pagination"></div>
  </div>

</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  MODAL 1: EXPORT DU RAPPORT GLOBAL (PDF / EXCEL)                    -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="pact-modal-backdrop" id="modal-report-backdrop" onclick="if(event.target===this)closeReportModal()">
  <div class="pact-modal">
    <div class="pact-modal-header">
      <h2>Personnaliser le Rapport Global</h2>
      <button class="pact-modal-close" onclick="closeReportModal()">&times;</button>
    </div>
    <div class="pact-modal-body">
      
      <!-- Format Picker -->
      <div class="modal-section">
        <div class="modal-section-title">Format de sortie</div>
        <div class="format-pills">
          <label class="format-pill selected" id="pill-report-pdf" onclick="selectReportFormat('pdf')">
            <input type="radio" name="report_format" value="pdf" checked>
            
            <div class="name">Rapport PDF</div>
            <div class="desc">Mise en page officielle A4 / Prêt à imprimer</div>
          </label>
          <label class="format-pill" id="pill-report-xlsx" onclick="selectReportFormat('xlsx')">
            <input type="radio" name="report_format" value="xlsx">
            
            <div class="name">Classeur Excel (.xlsx)</div>
            <div class="desc">Multi-feuilles (KPIs, Démographie, Formations)</div>
          </label>
        </div>
      </div>

      <!-- Sections to include -->
      <div class="modal-section">
        <div class="modal-section-title">
          <span>Sections à inclure</span>
          <span style="font-size:.75rem;cursor:pointer;color:var(--green);text-transform:none;font-weight:600;" onclick="toggleAllReportSections(true)">Tout cocher</span>
        </div>
        <div class="checkbox-grid">
          <label class="custom-check"><input type="checkbox" id="rep-sec-kpi" checked> Indicateurs clés (KPIs)</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-demo" checked> Sexe, Statut & Instruction</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-geo" checked> Région, Province, Commune</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-niveaux" checked> Niveaux Bronze, Argent, Or</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-top" checked> Top 10 Formations</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-filters" checked> Filtres actuellement appliqués</label>
        </div>
      </div>

      <!-- Customization fields -->
      <div class="modal-section">
        <div class="modal-section-title">Informations du document</div>
        <div class="modal-input-group">
          <label>Titre principal du rapport</label>
          <input type="text" id="rep-title" value="Rapport de Synthèse Global — PACTDIGITAL-BF">
        </div>
        <div class="modal-input-group">
          <label>Sous-titre / Contexte</label>
          <input type="text" id="rep-subtitle" value="Plateforme Nationale de Formation aux Compétences Numériques">
        </div>
        <div class="modal-input-group">
          <label>Organisme / Ministère émetteur</label>
          <input type="text" id="rep-org" value="Ministère de la Transition Digitale, des Postes et des Communications Électroniques">
        </div>
        <div class="modal-input-group">
          <label>Émis par (Administrateur)</label>
          <input type="text" id="rep-emitted-by" value="<?= s($current_user_name) ?>">
        </div>
        <div class="modal-input-group">
          <label>Commentaire ou note libre (optionnel)</label>
          <textarea id="rep-notes" rows="2" placeholder="Ajouter une observation, une conclusion ou une remarque…"></textarea>
        </div>
      </div>

    </div>
    <div class="pact-modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeReportModal()">Annuler</button>
      <button type="button" class="btn btn-green" id="btn-run-report" onclick="generateReport()" style="display:inline-flex;align-items:center;gap:6px;">
        Générer le rapport
      </button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  MODAL 2: EXPORT DE LA LISTE DES APPRENANTS (CSV / EXCEL)           -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="pact-modal-backdrop" id="modal-learners-backdrop" onclick="if(event.target===this)closeLearnerExportModal()">
  <div class="pact-modal">
    <div class="pact-modal-header">
      <h2>Personnaliser l'Export des Apprenants</h2>
      <button class="pact-modal-close" onclick="closeLearnerExportModal()">&times;</button>
    </div>
    <div class="pact-modal-body">

      <!-- Format Picker -->
      <div class="modal-section">
        <div class="modal-section-title">Format de fichier</div>
        <div class="format-pills">
          <label class="format-pill selected" id="pill-learn-csv-semi" onclick="selectLearnerFormat('csv-semi')">
            <input type="radio" name="learner_format" value="csv-semi" checked>
            
            <div class="name">CSV (; point-virgule)</div>
            <div class="desc">Recommandé pour Excel (France / Francophone)</div>
          </label>
          <label class="format-pill" id="pill-learn-csv-comma" onclick="selectLearnerFormat('csv-comma')">
            <input type="radio" name="learner_format" value="csv-comma">
            
            <div class="name">CSV (, virgule)</div>
            <div class="desc">Standard international / Outils d'analyse</div>
          </label>
          <label class="format-pill" id="pill-learn-xlsx" onclick="selectLearnerFormat('xlsx')">
            <input type="radio" name="learner_format" value="xlsx">
            
            <div class="name">Excel (.xlsx)</div>
            <div class="desc">Classeur Excel avec colonnes stylisées</div>
          </label>
        </div>
      </div>

      <!-- Scope / Filter selection -->
      <div class="modal-section">
        <div class="modal-section-title">Périmètre des données</div>
        <div style="display:flex;flex-direction:column;gap:8px;background:#f7faf8;padding:12px;border-radius:8px;border:1px solid var(--border);">
          <label class="custom-check">
            <input type="radio" name="learner_scope" value="filtered" checked>
            <span>Tous les apprenants filtrés actuellement (<strong id="cnt-filtered">0</strong> apprenants)</span>
          </label>
          <label class="custom-check">
            <input type="radio" name="learner_scope" value="search">
            <span>Seulement les résultats de la recherche textuelle en cours (<strong id="cnt-search">0</strong> apprenants)</span>
          </label>
          <label class="custom-check">
            <input type="radio" name="learner_scope" value="page">
            <span>Uniquement la page actuellement affichée (max 25 apprenants)</span>
          </label>
        </div>
      </div>

      <!-- Column Picker -->
      <div class="modal-section">
        <div class="modal-section-title">
          <span>Colonnes à exporter</span>
          <div style="display:flex;gap:10px;font-size:.75rem;text-transform:none;font-weight:600;">
            <span style="cursor:pointer;color:var(--green);" onclick="toggleLearnerCols(true)">Tout cocher</span>
            <span style="color:var(--border);">|</span>
            <span style="cursor:pointer;color:var(--green);" onclick="toggleLearnerCols(false)">Tout décocher</span>
          </div>
        </div>
        <div class="checkbox-grid">
          <label class="custom-check"><input type="checkbox" class="col-check" value="nom" checked> Nom et prénom</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="email" checked> Adresse email</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="sexe" checked> Sexe</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="region" checked> Région</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="province" checked> Province</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="commune" checked> Commune</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="statut" checked> Statut</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="niveau_instruction" checked> Niveau d'instruction</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="inscription" checked> Date d'inscription</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="enroled" checked> Formations inscrites</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="completed" checked> Formations complétées</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="certificates" checked> Attestations délivrées</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="taux" checked> Taux de complétion (%)</label>
          <label class="custom-check"><input type="checkbox" class="col-check" value="last_access" checked> Dernière connexion</label>
        </div>
      </div>

      <!-- Advanced options -->
      <div class="modal-section">
        <div class="modal-section-title">Options avancées</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
          <label class="custom-check">
            <input type="checkbox" id="learner-opt-headers" checked> Inclure la première ligne avec les titres des colonnes
          </label>
          <label class="custom-check">
            <input type="checkbox" id="learner-opt-bom" checked> Encodage UTF-8 avec BOM (garantit les accents é, è, à parfaits sous Excel)
          </label>
          <div class="modal-input-group" style="margin-top:6px;">
            <label>Nom du fichier (sans extension)</label>
            <input type="text" id="learner-filename" value="pactdigital_apprenants">
          </div>
        </div>
      </div>

    </div>
    <div class="pact-modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeLearnerExportModal()">Annuler</button>
      <button type="button" class="btn btn-green" onclick="generateLearnerExport()" style="display:inline-flex;align-items:center;gap:6px;">
        Télécharger la liste
      </button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!--  CONTENEUR IMPRIMABLE DU RAPPORT PDF OFFICIEL                       -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="pact-printable-report"></div>

<!-- External Libraries (Chart.js & SheetJS XLSX) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
const DATA_URL = '<?= $data_url ?>';
const BURKINA_REGIONS_GEOJSON_URL = '<?= $burkina_regions_url ?>';
const ARMOIRIES_SRC = <?= json_encode($armoiries_base64) ?>;
const CURRENT_USER_NAME = <?= json_encode($current_user_name) ?>;
const PACT_GEO_DATA = <?= file_get_contents($CFG->dirroot . '/local/pactdigital/burkina_geo.json') ?>;
const GREEN  = '#00843b';
const RED    = '#e30613';
const GOLD   = '#b07800';
const BLUE   = '#2a6d9b';
const COLORS = ['#00843b','#2a6d9b','#b07800','#e30613','#045527','#173f5f','#7d5500','#a2040e','#14a052','#635c55','#d4bc00','#38332f'];

let allLearners = [];
let filteredLearners = [];
let currentPage = 1;
const PAGE_SIZE = 25;
let sortDir = {};
const charts = {};

// ── Helpers ─────────────────────────────────────────────────────────────
function getFilters() {
    return {
        sexe:     document.getElementById('f-sexe').value,
        region:   document.getElementById('f-region').value,
        province: document.getElementById('f-province').value,
        commune:  document.getElementById('f-commune').value,
        statut:   document.getElementById('f-statut').value,
        catid:    document.getElementById('f-catid').value,
        niveau:   document.getElementById('f-niveau').value,
        period:   document.getElementById('f-period').value,
    };
}

function buildUrl(extra = {}) {
    const p = {...getFilters(), ...extra, action: 'all'};
    const q = new URLSearchParams(Object.fromEntries(Object.entries(p).filter(([,v]) => v !== '' && v !== '0' && v !== 0)));
    return DATA_URL + '?' + q.toString();
}

function destroyChart(id) {
    if (charts[id]) { charts[id].destroy(); delete charts[id]; }
}

function makeChart(id, type, labels, datasets, opts = {}) {
    destroyChart(id);
    const ctx = document.getElementById(id);
    if (!ctx) return;
    charts[id] = new Chart(ctx, {
        type,
        data: { labels, datasets },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: type === 'bar' || type === 'line' ? 'top' : 'right',
                          labels: { font: { size: 11 }, boxWidth: 12 } },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: (type === 'bar' || type === 'line') ? {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45 } },
                y: { beginAtZero: true, ticks: { font: { size: 10 } },
                     grid: { color: 'rgba(0,0,0,.05)' } }
            } : {},
            ...opts
        }
    });
}

// ── Cascading Dropdowns: Région -> Province -> Commune ────────────────────
function updateDashboardProvinces(preferredProvince = '') {
    const regEl = document.getElementById('f-region');
    const prvEl = document.getElementById('f-province');
    if (!prvEl) return;

    const currentReg = regEl ? regEl.value : '';
    const currentPrv = preferredProvince || prvEl.value;

    // Conserver la 1ère option ("Toutes")
    while (prvEl.options.length > 1) prvEl.remove(1);

    let provinces = [];
    if (currentReg && PACT_GEO_DATA && PACT_GEO_DATA[currentReg]) {
        provinces = Object.keys(PACT_GEO_DATA[currentReg]).sort((a,b) => a.localeCompare(b, 'fr'));
    } else if (PACT_GEO_DATA) {
        const set = new Set();
        Object.values(PACT_GEO_DATA).forEach(regObj => {
            Object.keys(regObj).forEach(p => set.add(p));
        });
        provinces = Array.from(set).sort((a,b) => a.localeCompare(b, 'fr'));
    }

    provinces.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p;
        opt.textContent = p;
        if (p === currentPrv) opt.selected = true;
        prvEl.appendChild(opt);
    });

    if (currentPrv && !provinces.includes(currentPrv)) {
        prvEl.value = '';
    }

    updateDashboardCommunes();
}

function updateDashboardCommunes(preferredCommune = '') {
    const regEl = document.getElementById('f-region');
    const prvEl = document.getElementById('f-province');
    const comEl = document.getElementById('f-commune');
    if (!comEl) return;

    const currentReg = regEl ? regEl.value : '';
    const currentPrv = prvEl ? prvEl.value : '';
    const currentCom = preferredCommune || comEl.value;

    while (comEl.options.length > 1) comEl.remove(1);

    let communes = [];
    if (currentReg && currentPrv && PACT_GEO_DATA && PACT_GEO_DATA[currentReg] && PACT_GEO_DATA[currentReg][currentPrv]) {
        communes = [...PACT_GEO_DATA[currentReg][currentPrv]].sort((a,b) => a.localeCompare(b, 'fr'));
    } else if (currentPrv && PACT_GEO_DATA) {
        const set = new Set();
        Object.values(PACT_GEO_DATA).forEach(regObj => {
            if (regObj[currentPrv]) {
                regObj[currentPrv].forEach(c => set.add(c));
            }
        });
        communes = Array.from(set).sort((a,b) => a.localeCompare(b, 'fr'));
    } else if (currentReg && PACT_GEO_DATA && PACT_GEO_DATA[currentReg]) {
        const set = new Set();
        Object.values(PACT_GEO_DATA[currentReg]).forEach(comList => {
            comList.forEach(c => set.add(c));
        });
        communes = Array.from(set).sort((a,b) => a.localeCompare(b, 'fr'));
    } else if (PACT_GEO_DATA) {
        const set = new Set();
        Object.values(PACT_GEO_DATA).forEach(regObj => {
            Object.values(regObj).forEach(comList => {
                comList.forEach(c => set.add(c));
            });
        });
        communes = Array.from(set).sort((a,b) => a.localeCompare(b, 'fr'));
    }

    communes.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c;
        opt.textContent = c;
        if (c === currentCom) opt.selected = true;
        comEl.appendChild(opt);
    });

    if (currentCom && !communes.includes(currentCom)) {
        comEl.value = '';
    }
}

// ── Load filters from API ─────────────────────────────────────────────────
async function loadFilters() {
    const res = await fetch(DATA_URL + '?action=filters');
    const d = await res.json();
    const fo = d.filter_options || {};

    const populateSelect = (id, options, valueIsOption = true) => {
        const el = document.getElementById(id);
        if (!el) return;
        const current = el.value;
        // Keep first option
        while (el.options.length > 1) el.remove(1);
        (options || []).forEach(o => {
            const opt = document.createElement('option');
            opt.value = valueIsOption ? o : o.value;
            opt.textContent = valueIsOption ? o : o.label;
            el.appendChild(opt);
        });
        if (current) el.value = current;
    };

    populateSelect('f-sexe',   fo.sexe);
    populateSelect('f-statut', fo.statut);

    // Initialisation des Régions avec optgroups (13 Administratives + 17 Territoriales)
    const regEl = document.getElementById('f-region');
    if (regEl) {
        regEl.innerHTML = '<option value="">Toutes</option>';
        const adminGroup = document.createElement('optgroup');
        adminGroup.label = '13 Régions Administratives';
        const adminRegions = [
            'Boucle du Mouhoun', 'Cascades', 'Centre', 'Centre-Est', 'Centre-Nord',
            'Centre-Ouest', 'Centre-Sud', 'Est', 'Hauts-Bassins', 'Nord',
            'Plateau-Central', 'Sahel', 'Sud-Ouest'
        ];
        adminRegions.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r; opt.textContent = r;
            adminGroup.appendChild(opt);
        });
        regEl.appendChild(adminGroup);

        const terrGroup = document.createElement('optgroup');
        terrGroup.label = '17 Régions Territoriales';
        const terrRegions = [
            'Bankui', 'Djôrô', 'Goulmou', 'Guiriko', 'Kadiogo', 'Kuilsé',
            'Liptako', 'Nakambé', 'Nando', 'Nazinon', 'Oubri', 'Sirba',
            'Soum', 'Sourou', 'Tannounyan', 'Tapoa', 'Yaadga'
        ];
        terrRegions.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r; opt.textContent = r;
            terrGroup.appendChild(opt);
        });
        regEl.appendChild(terrGroup);

        if (!regEl.dataset.cascadeInit) {
            regEl.dataset.cascadeInit = '1';
            regEl.addEventListener('change', () => {
                updateDashboardProvinces();
            });
        }
    }

    const prvEl = document.getElementById('f-province');
    if (prvEl && !prvEl.dataset.cascadeInit) {
        prvEl.dataset.cascadeInit = '1';
        prvEl.addEventListener('change', () => {
            updateDashboardCommunes();
        });
    }

    updateDashboardProvinces();
    updateDashboardCommunes();

    // Categories with IDs
    const catEl = document.getElementById('f-catid');
    const catIds = fo.categories_ids || {};
    Object.entries(catIds).forEach(([id, name]) => {
        const opt = document.createElement('option');
        opt.value = id; opt.textContent = name;
        catEl.appendChild(opt);
    });
}

let lastDashboardData = null;

// ── Load all dashboard data ───────────────────────────────────────────────
async function loadDashboard() {
    const url = buildUrl();
    const res = await fetch(url);
    const d = await res.json();
    lastDashboardData = d;

    // Update timestamp
    document.getElementById('last-update').textContent = new Date().toLocaleTimeString('fr-FR');

    renderKPI(d.kpi || {});
    renderTimeline(d.timeline || {});
    renderGender(d.gender || {});
    renderStatut(d.statut || {});
    renderRegion(d.region || {});
    renderMap(d.region_details || {}, d.region || {}, d.kpi || {});
    renderProvince(d.province || {});
    renderCommune(d.commune || {});
    renderCategories(d.categories || {});
    renderNiveaux(d.niveaux || {});
    renderInstruction(d.instruction || {});
    renderImpactAndQuality(d.impact_gain || {}, d.voies_certification || {}, d.satisfaction || {});
    renderTopCourses(d.top_courses || []);
    renderLearners(d.learners || []);

    // Update modal counters if elements exist
    const cntF = document.getElementById('cnt-filtered');
    const cntS = document.getElementById('cnt-search');
    if (cntF) cntF.textContent = (d.learners || []).length;
    if (cntS) cntS.textContent = filteredLearners.length;
}

// ── KPI ───────────────────────────────────────────────────────────────────
function renderKPI(k) {
    const cards = [
        { label:'Total apprenants',       value: k.total_users     || 0, color:'' },
        { label:'Apprenants actifs (30j)', value: k.active_users    || 0, color:'' },
        { label:'Inscriptions',            value: k.total_enrolments|| 0, color:'blue' },
        { label:'Cours complétés',         value: k.total_completions||0, color:'' },
        { label:'Attestations délivrées',  value: k.total_certificates||0, color:'gold' },
        { label:'Taux de complétion',      value: (k.taux||0) + '%', color: (k.taux||0) >= 50 ? '' : 'red' },
    ];
    document.getElementById('kpi-grid').innerHTML = cards.map(c => `
        <div class="kpi-card ${c.color}">
            <div class="label">${c.label}</div>
            <div class="value">${c.value}</div>
        </div>
    `).join('');
}

// ── Timeline ──────────────────────────────────────────────────────────────
function renderTimeline(data) {
    const labels = Object.keys(data).map(m => {
        const [y, mo] = m.split('-');
        return new Date(y, mo-1).toLocaleDateString('fr-FR', {month:'short', year:'2-digit'});
    });
    makeChart('chart-timeline', 'line', labels, [{
        label: 'Nouvelles inscriptions', data: Object.values(data),
        borderColor: GREEN, backgroundColor: 'rgba(20,91,55,.1)',
        fill: true, tension: .4, pointBackgroundColor: GREEN, pointRadius: 4
    }]);
}

// ── Gender ────────────────────────────────────────────────────────────────
function renderGender(data) {
    const labels = Object.keys(data);
    makeChart('chart-gender', 'doughnut', labels, [{
        data: Object.values(data),
        backgroundColor: [GREEN, RED, '#9E9E9E'],
        borderWidth: 2, borderColor: '#fff', hoverOffset: 6
    }]);
}

// ── Statut ────────────────────────────────────────────────────────────────
function renderStatut(data) {
    makeChart('chart-statut', 'doughnut', Object.keys(data), [{
        data: Object.values(data),
        backgroundColor: COLORS, borderWidth: 2, borderColor: '#fff', hoverOffset: 6
    }]);
}

// ── Region ────────────────────────────────────────────────────────────────
function renderRegion(data) {
    makeChart('chart-region', 'bar', Object.keys(data), [{
        label: 'Apprenants', data: Object.values(data),
        backgroundColor: COLORS.map(c => c + 'cc'), borderColor: COLORS,
        borderWidth: 1.5, borderRadius: 5
    }]);
}

// ── Carte SIG Interactive du Burkina Faso (Leaflet.js) ───────────────────
let pactMap = null;
let pactGeojsonLayer = null;
let pactBurkinaGeojson = null;
let pactRegionDetails = {};
let pactNationalKpi = {};
let pactSelectedRegion = null;
let pactLegendControl = null;

const REGION_ALIAS_MAP = {
    'Bankui': 'Boucle du Mouhoun',
    'Sourou': 'Boucle du Mouhoun',
    'Djôrô': 'Cascades',
    'Kadiogo': 'Centre',
    'Nakambé': 'Centre-Est',
    'Kuilsé': 'Centre-Nord',
    'Nando': 'Centre-Ouest',
    'Nazinon': 'Centre-Sud',
    'Goulmou': 'Est',
    'Sirba': 'Est',
    'Tapoa': 'Est',
    'Guiriko': 'Hauts-Bassins',
    'Tuy': 'Hauts-Bassins',
    'Yaadga': 'Nord',
    'Oubri': 'Plateau-Central',
    'Soum': 'Sahel',
    'Liptako': 'Sahel',
    'Tannounyan': 'Sud-Ouest'
};

const CANONICAL_TO_FILTER_MAP = {
    'Boucle du Mouhoun': 'Bankui',
    'Cascades': 'Djôrô',
    'Centre': 'Kadiogo',
    'Centre-Est': 'Nakambé',
    'Centre-Nord': 'Kuilsé',
    'Centre-Ouest': 'Nando',
    'Centre-Sud': 'Nazinon',
    'Est': 'Goulmou',
    'Hauts-Bassins': 'Guiriko',
    'Nord': 'Yaadga',
    'Plateau-Central': 'Oubri',
    'Sahel': 'Soum',
    'Sud-Ouest': 'Tannounyan'
};

function getRegionColor(val, maxVal, metric) {
    if (metric === 'taux_completion') {
        if (val >= 80) return '#0b3d24';
        if (val >= 60) return '#145B37';
        if (val >= 40) return '#2e8b57';
        if (val >= 20) return '#66bb6a';
        if (val > 0)  return '#a5d6a7';
        return '#f0f7f3';
    } else if (metric === 'taux_femmes') {
        if (val >= 50) return '#0b3d24';
        if (val >= 40) return '#145B37';
        if (val >= 30) return '#2e8b57'; // Cible nationale >= 30%
        if (val >= 20) return '#f59e0b';
        if (val > 0)  return '#ef4444';
        return '#f0f7f3';
    } else if (metric === 'certificats') {
        const ratio = maxVal > 0 ? (val / maxVal) : 0;
        if (ratio >= 0.8) return '#b45309';
        if (ratio >= 0.6) return '#d97706';
        if (ratio >= 0.4) return '#f59e0b';
        if (ratio >= 0.2) return '#fbbf24';
        if (ratio > 0)   return '#fde68a';
        return '#fefce8';
    } else {
        // 'inscrits' (default)
        const ratio = maxVal > 0 ? (val / maxVal) : 0;
        if (ratio >= 0.8) return '#0b3d24';
        if (ratio >= 0.6) return '#145B37';
        if (ratio >= 0.4) return '#2e8b57';
        if (ratio >= 0.2) return '#66bb6a';
        if (ratio > 0)   return '#a5d6a7';
        return '#f0f7f3';
    }
}

function getRegionValue(regionName, metric) {
    const data = pactRegionDetails[regionName] || {};
    if (metric === 'taux_completion') return data.taux_completion || 0;
    if (metric === 'taux_femmes') return data.taux_femmes || 0;
    if (metric === 'certificats') return data.certs || 0;
    return data.total_users || 0;
}

function getMaxMetricValue(metric) {
    let max = 0;
    Object.values(pactRegionDetails).forEach(d => {
        let v = 0;
        if (metric === 'taux_completion') v = d.taux_completion || 0;
        else if (metric === 'taux_femmes') v = d.taux_femmes || 0;
        else if (metric === 'certificats') v = d.certs || 0;
        else v = d.total_users || 0;
        if (v > max) max = v;
    });
    return max;
}

async function renderMap(regionDetails, rawRegionCounts, kpi) {
    pactRegionDetails = regionDetails || {};
    pactNationalKpi = kpi || {};

    // Synchroniser avec le filtre région actuellement actif
    const currentFilterRegion = document.getElementById('f-region')?.value || '';
    if (currentFilterRegion) {
        pactSelectedRegion = REGION_ALIAS_MAP[currentFilterRegion] || currentFilterRegion;
    }

    // Initialiser Leaflet si pas encore fait
    if (!pactMap) {
        const mapEl = document.getElementById('pact-map');
        if (!mapEl) return;

        pactMap = L.map('pact-map', {
            center: [12.3714, -1.5197], // Centre du Burkina Faso
            zoom: 7,
            minZoom: 6,
            maxZoom: 9,
            scrollWheelZoom: false, // Empêche le zoom intempestif au scroll
            attributionControl: false
        });

        // Fond de carte neutre clair et élégant (Esri Light Gray Canvas, 100% libre et sans clé API requise)
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 10,
            attribution: '© Esri, © OpenStreetMap'
        }).addTo(pactMap);

        // Charger le GeoJSON des 13 régions
        try {
            const resp = await fetch(BURKINA_REGIONS_GEOJSON_URL);
            pactBurkinaGeojson = await resp.json();
        } catch (e) {
            console.error('Erreur chargement GeoJSON Burkina Faso:', e);
            return;
        }
    }

    // Mettre à jour la couche GeoJSON et la fiche d'info
    updateMapLayers();
    updateInfoCard(pactSelectedRegion);
}

function updateMapLayers() {
    if (!pactMap || !pactBurkinaGeojson) return;

    if (pactGeojsonLayer) {
        pactMap.removeLayer(pactGeojsonLayer);
    }

    const metric = document.getElementById('map-metric')?.value || 'inscrits';
    const maxVal = getMaxMetricValue(metric);

    pactGeojsonLayer = L.geoJSON(pactBurkinaGeojson, {
        style: function(feature) {
            const name = feature.properties.name;
            const val = getRegionValue(name, metric);
            const isSelected = pactSelectedRegion && (pactSelectedRegion.toLowerCase() === name.toLowerCase());

            return {
                fillColor: getRegionColor(val, maxVal, metric),
                weight: isSelected ? 3.5 : 1.5,
                opacity: 1,
                color: isSelected ? '#d32f2f' : '#ffffff',
                fillOpacity: isSelected ? 0.95 : 0.85
            };
        },
        onEachFeature: function(feature, layer) {
            const name = feature.properties.name;
            const regData = pactRegionDetails[name] || { total_users: 0, taux_completion: 0, taux_femmes: 0, certs: 0 };

            // Tooltip flottant au survol
            const tooltipContent = `<div style="font-family:'Segoe UI',sans-serif;min-width:140px;">
                <strong style="color:var(--green);font-size:0.9rem;">${name}</strong><br>
                <div style="font-size:0.75rem;margin-top:4px;">
                    <b>${regData.total_users || 0}</b> apprenant(s)<br>
                    <b>${regData.taux_completion || 0}%</b> de réussite<br>
                    <b>${regData.taux_femmes || 0}%</b> de femmes<br>
                    <b>${regData.certs || 0}</b> attestation(s)
                </div>
            </div>`;
            layer.bindTooltip(tooltipContent, { sticky: true, opacity: 0.96 });

            // Événements souris
            layer.on({
                mouseover: function(e) {
                    const l = e.target;
                    l.setStyle({
                        weight: 3,
                        color: '#fad615',
                        fillOpacity: 0.95
                    });
                    l.bringToFront();
                    updateInfoCard(name);
                },
                mouseout: function(e) {
                    if (pactGeojsonLayer) {
                        pactGeojsonLayer.resetStyle(e.target);
                    }
                    updateInfoCard(pactSelectedRegion);
                },
                click: function(e) {
                    pactSelectedRegion = name;
                    updateInfoCard(name);
                    filterDashboardFromMap(name);
                }
            });
        }
    }).addTo(pactMap);

    updateMapLegend(metric, maxVal);
}

function updateMapVisuals() {
    updateMapLayers();
    updateInfoCard(pactSelectedRegion);
}

function updateMapLegend(metric, maxVal) {
    if (!pactMap) return;

    if (pactLegendControl) {
        pactMap.removeControl(pactLegendControl);
    }

    pactLegendControl = L.control({ position: 'bottomright' });
    pactLegendControl.onAdd = function() {
        const div = L.DomUtil.create('div', 'map-legend');
        let title = 'Inscrits';
        if (metric === 'taux_completion') title = 'Complétion (%)';
        else if (metric === 'taux_femmes') title = 'Femmes (%)';
        else if (metric === 'certificats') title = 'Attestations';

        let grades = [];
        if (metric === 'taux_completion') {
            grades = [0, 20, 40, 60, 80];
        } else if (metric === 'taux_femmes') {
            grades = [0, 20, 30, 40, 50];
        } else {
            const step = Math.max(1, Math.round(maxVal / 5));
            grades = [0, step, step * 2, step * 3, step * 4];
        }

        div.innerHTML = `<div style="font-weight:700;margin-bottom:6px;color:var(--green);">${title}</div>`;
        for (let i = 0; i < grades.length; i++) {
            const from = grades[i];
            const to = grades[i + 1];
            const color = getRegionColor(from + 0.1, maxVal, metric);
            div.innerHTML += `
                <div class="map-legend-item">
                    <span class="map-legend-color" style="background:${color};"></span>
                    <span>${from}${to ? '&ndash;' + to : '+'}</span>
                </div>`;
        }
        return div;
    };
    pactLegendControl.addTo(pactMap);
}

function updateInfoCard(regionName) {
    const titleEl = document.getElementById('info-region-name');
    const badgeEl = document.getElementById('badge-region-status');
    const usersEl = document.getElementById('info-val-users');
    const compEl  = document.getElementById('info-val-completion');
    const femEl   = document.getElementById('info-val-femmes');
    const certsEl = document.getElementById('info-val-certs');
    const btnFilter = document.getElementById('btn-filter-this-region');

    if (!titleEl) return;

    if (regionName && pactRegionDetails[regionName]) {
        const d = pactRegionDetails[regionName];
        titleEl.textContent = regionName;
        badgeEl.textContent = 'Région';
        badgeEl.className = 'badge-pill badge-green';
        usersEl.textContent = d.total_users || 0;
        compEl.textContent = (d.taux_completion || 0) + '%';
        femEl.textContent = (d.taux_femmes || 0) + '%';
        certsEl.textContent = d.certs || 0;
        if (btnFilter) {
            btnFilter.style.display = 'flex';
            btnFilter.innerHTML = `<span>Filtrer sur ${regionName}</span>`;
        }
    } else {
        // Vue Nationale
        titleEl.textContent = 'Burkina Faso';
        badgeEl.textContent = 'Vue Globale';
        badgeEl.className = 'badge-pill badge-diamant';
        const k = pactNationalKpi || {};
        usersEl.textContent = k.total_users || 0;
        compEl.textContent = (k.taux || 0) + '%';
        femEl.textContent = (k.taux_femmes || 0) + '%';
        certsEl.textContent = k.total_certificates || 0;
        if (btnFilter) {
            btnFilter.style.display = 'none';
        }
    }
}

function filterDashboardFromMap(regionName) {
    const targetRegion = regionName || pactSelectedRegion;
    if (!targetRegion) return;

    const sel = document.getElementById('f-region');
    if (!sel) return;

    let matched = false;
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value.toLowerCase() === targetRegion.toLowerCase()) {
            sel.selectedIndex = i;
            matched = true;
            break;
        }
    }

    if (!matched) {
        const alias = CANONICAL_TO_FILTER_MAP[targetRegion];
        if (alias) {
            for (let i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value.toLowerCase() === alias.toLowerCase()) {
                    sel.selectedIndex = i;
                    matched = true;
                    break;
                }
            }
        }
    }

    pactSelectedRegion = targetRegion;
    if (typeof updateDashboardProvinces === 'function') {
        updateDashboardProvinces();
    }
    applyFilters();
}

function resetMapSelection() {
    pactSelectedRegion = null;
    const sel = document.getElementById('f-region');
    if (sel) sel.value = '';
    if (typeof updateDashboardProvinces === 'function') {
        updateDashboardProvinces();
    }
    if (pactMap) {
        pactMap.setView([12.3714, -1.5197], 7);
    }
    applyFilters();
    updateMapVisuals();
}

function pactResetMapState() {
    pactSelectedRegion = null;
}

// ── Province ──────────────────────────────────────────────────────────────
function renderProvince(data) {
    makeChart('chart-province', 'bar', Object.keys(data), [{
        label: 'Apprenants', data: Object.values(data),
        backgroundColor: 'rgba(21,101,192,.7)', borderColor: BLUE,
        borderWidth: 1.5, borderRadius: 5
    }]);
}

// ── Commune ───────────────────────────────────────────────────────────────
function renderCommune(data) {
    makeChart('chart-commune', 'bar', Object.keys(data), [{
        label: 'Apprenants', data: Object.values(data),
        backgroundColor: 'rgba(212,175,55,.7)', borderColor: GOLD,
        borderWidth: 1.5, borderRadius: 5
    }]);
}

// ── Categories ────────────────────────────────────────────────────────────
function renderCategories(data) {
    makeChart('chart-categories', 'bar', Object.keys(data), [{
        label: 'Inscrits', data: Object.values(data),
        backgroundColor: COLORS.map(c => c + 'bb'),
        borderColor: COLORS, borderWidth: 1.5, borderRadius: 5
    }]);
}

// ── Niveaux ───────────────────────────────────────────────────────────────
function renderNiveaux(data) {
    const labels = ['Bronze', 'Argent', 'Or'];
    const enrol  = labels.map(l => (data.enrol   || {})[l] || 0);
    const compl  = labels.map(l => (data.complete || {})[l] || 0);
    makeChart('chart-niveaux', 'bar', labels, [
        { label: 'Inscrits',  data: enrol, backgroundColor: ['#cd7f3288','#d4af3788','#1565C088'],
          borderColor: ['#cd7f32','#d4af37','#1565C0'], borderWidth: 2, borderRadius: 5 },
        { label: 'Complétés', data: compl, backgroundColor: ['#cd7f3255','#d4af3755','#1565C055'],
          borderColor: ['#cd7f32','#d4af37','#1565C0'], borderWidth: 2, borderRadius: 5, borderDash: [4,4] },
    ]);
}

// ── Instruction ───────────────────────────────────────────────────────────
function renderInstruction(data) {
    makeChart('chart-instruction', 'doughnut', Object.keys(data), [{
        data: Object.values(data),
        backgroundColor: COLORS, borderWidth: 2, borderColor: '#fff', hoverOffset: 6
    }]);
}

// ── Impact & Qualité (PIL-05, PIL-06, PIL-09) ─────────────────────────────
function renderImpactAndQuality(impact, voies, sat) {
    const preEl = document.getElementById('val-pretest');
    const postEl = document.getElementById('val-posttest');
    const ptsEl = document.getElementById('val-gain-pts');
    const pctEl = document.getElementById('val-gain-pct');
    if (preEl) preEl.textContent = (impact.avg_pretest || 0) + '%';
    if (postEl) postEl.textContent = (impact.avg_posttest || 0) + '%';
    if (ptsEl) ptsEl.textContent = '+' + (impact.gain_points || 0) + ' pts';
    if (pctEl) pctEl.textContent = '+' + (impact.gain_pct || 0) + '%';

    const v1 = voies.voie_1 || { count: 0, pct: 82 };
    const v2 = voies.voie_2 || { count: 0, pct: 18 };
    makeChart('chart-voies', 'doughnut', 
        [`Voie 1 : Parcours (${v1.pct}%)`, `Voie 2 : Direct (${v2.pct}%)`], 
        [{
            data: [v1.count || v1.pct, v2.count || v2.pct],
            backgroundColor: [GREEN, GOLD],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    );

    const scoreEl = document.getElementById('val-csat-score');
    const satPctEl = document.getElementById('val-csat-pct');
    if (scoreEl) scoreEl.textContent = sat.score_moyenne || '4.7';
    if (satPctEl) satPctEl.textContent = (sat.taux_satisfaction || 94) + '%';
}

// ── Top Courses ───────────────────────────────────────────────────────────
function renderTopCourses(courses) {
    const levelClass = n => (n && n.includes('Bronze')) ? 'badge-bronze' : (n && n.includes('Or')) ? 'badge-or' : (n && n.includes('Argent')) ? 'badge-or' : 'badge-diamant';
    const body = document.getElementById('tbl-courses-body');
    if (!courses || !courses.length) {
        body.innerHTML = '<tr><td colspan="5" class="empty">Aucune donnée</td></tr>';
        return;
    }
    body.innerHTML = courses.map(c => {
        const cCourse = c.course || c.name || 'Formation';
        const cCat    = c.category || c.domain || 'Général';
        const cEnr    = c.enroled ?? c.enrolled ?? 0;
        const cComp   = c.completed ?? 0;
        const cTaux   = c.taux ?? (cEnr > 0 ? Math.round((cComp / cEnr) * 100) : 0);
        return `
        <tr>
          <td><span class="badge-pill ${levelClass(cCourse)}">${cCourse}</span></td>
          <td>${cCat}</td>
          <td><strong>${cEnr}</strong></td>
          <td>${cComp}</td>
          <td>
            <div class="taux-bar">
              <div class="bar"><div class="fill" style="width:${cTaux}%"></div></div>
              <span>${cTaux}%</span>
            </div>
          </td>
        </tr>`;
    }).join('');
}

// ── Learners Table ────────────────────────────────────────────────────────
function renderLearners(learners) {
    allLearners = learners;
    filteredLearners = [...learners];
    currentPage = 1;
    renderPage();
}

function filterTable() {
    const q = document.getElementById('learner-search').value.toLowerCase();
    filteredLearners = allLearners.filter(l =>
        Object.values(l).some(v => String(v).toLowerCase().includes(q))
    );
    currentPage = 1;
    renderPage();
}

function sortTable(col) {
    const keys = ['nom','sexe','region','province','commune','statut','niveau_instruction','enroled','completed','certificates','_taux','last_access'];
    const k = keys[col];
    sortDir[k] = sortDir[k] === 'asc' ? 'desc' : 'asc';
    filteredLearners.sort((a, b) => {
        let va = a[k] ?? '', vb = b[k] ?? '';
        if (!isNaN(va) && !isNaN(vb)) { va = +va; vb = +vb; }
        if (va < vb) return sortDir[k] === 'asc' ? -1 : 1;
        if (va > vb) return sortDir[k] === 'asc' ? 1 : -1;
        return 0;
    });
    renderPage();
}

function renderPage() {
    const body = document.getElementById('tbl-learners-body');
    const start = (currentPage - 1) * PAGE_SIZE;
    const page  = filteredLearners.slice(start, start + PAGE_SIZE);

    if (!page.length) {
        body.innerHTML = '<tr><td colspan="12" class="empty">Aucun apprenant trouvé</td></tr>';
        document.getElementById('pagination').innerHTML = '';
        return;
    }

    body.innerHTML = page.map(l => {
        const nom = l.nom || l.name || 'Apprenant';
        const enroled = l.enroled ?? l.enrolments ?? 0;
        const completed = l.completed ?? 0;
        const certs = l.certificates ?? l.certs ?? 0;
        const lastAccess = l.last_access || l.lastaccess || 'Jamais';
        const instruction = l.niveau_instruction || l.instruction || '–';
        const taux = enroled > 0 ? Math.round((completed / enroled) * 100) : 0;
        return `<tr>
          <td><strong>${nom}</strong><br><small style="color:var(--grey)">${l.email||''}</small></td>
          <td>${l.sexe||'–'}</td>
          <td>${l.region||'–'}</td>
          <td>${l.province||'–'}</td>
          <td>${l.commune||'–'}</td>
          <td>${l.statut||'–'}</td>
          <td>${instruction}</td>
          <td>${enroled}</td>
          <td>${completed}</td>
          <td>${certs}</td>
          <td>
            <div class="taux-bar">
              <div class="bar"><div class="fill" style="width:${taux}%"></div></div>
              <span>${taux}%</span>
            </div>
          </td>
          <td>${lastAccess}</td>
        </tr>`;
    }).join('');

    renderPagination();
}

function renderPagination() {
    const total = Math.ceil(filteredLearners.length / PAGE_SIZE);
    const pg = document.getElementById('pagination');
    if (total <= 1) { pg.innerHTML = ''; return; }

    let html = `<span style="font-size:.8rem;color:var(--grey)">${filteredLearners.length} apprenants</span>`;
    const range = [...Array(total).keys()].map(i => i + 1);
    const visible = range.filter(p => p === 1 || p === total || Math.abs(p - currentPage) <= 2);

    let prev = 0;
    visible.forEach(p => {
        if (p - prev > 1) html += '<button disabled>…</button>';
        html += `<button class="${p === currentPage ? 'active' : ''}" onclick="goPage(${p})">${p}</button>`;
        prev = p;
    });
    pg.innerHTML = html;
}

function goPage(p) { currentPage = p; renderPage(); }

// ── Filter table with count update ─────────────────────────────────────────
function filterTable() {
    const q = document.getElementById('learner-search').value.toLowerCase();
    filteredLearners = allLearners.filter(l =>
        Object.values(l).some(v => String(v).toLowerCase().includes(q))
    );
    currentPage = 1;
    renderPage();
    const cntS = document.getElementById('cnt-search');
    if (cntS) cntS.textContent = filteredLearners.length;
}

// ═══════════════════════════════════════════════════════════════════════════
//  EXPORT 1 : RAPPORT GLOBAL PERSONNALISÉ (PDF / EXCEL)
// ═══════════════════════════════════════════════════════════════════════════

let currentReportFormat = 'pdf';

function openReportModal() {
    document.getElementById('modal-report-backdrop').style.display = 'flex';
}

function closeReportModal() {
    document.getElementById('modal-report-backdrop').style.display = 'none';
}

function selectReportFormat(fmt) {
    currentReportFormat = fmt;
    document.getElementById('pill-report-pdf').classList.toggle('selected', fmt === 'pdf');
    document.getElementById('pill-report-xlsx').classList.toggle('selected', fmt === 'xlsx');
    const btn = document.getElementById('btn-run-report');
    if (fmt === 'pdf') {
        btn.innerHTML = 'Imprimer / Enregistrer en PDF';
    } else {
        btn.innerHTML = 'Télécharger en Excel (.xlsx)';
    }
}

function toggleAllReportSections(val) {
    ['rep-sec-kpi','rep-sec-demo','rep-sec-geo','rep-sec-niveaux','rep-sec-top','rep-sec-filters'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.checked = val;
    });
}

function getActiveFiltersSummary() {
    const f = getFilters();
    const active = [];
    if (f.sexe) active.push(`Sexe : ${f.sexe}`);
    if (f.region) active.push(`Région : ${f.region}`);
    if (f.province) active.push(`Province : ${f.province}`);
    if (f.commune) active.push(`Commune : ${f.commune}`);
    if (f.statut) active.push(`Statut : ${f.statut}`);
    if (f.niveau) active.push(`Niveau : ${f.niveau}`);
    if (f.catid && f.catid !== '0') {
        const sel = document.getElementById('f-catid');
        const txt = sel ? sel.options[sel.selectedIndex].text : f.catid;
        active.push(`Catégorie : ${txt}`);
    }
    if (f.period && f.period !== '0') {
        const sel = document.getElementById('f-period');
        const txt = sel ? sel.options[sel.selectedIndex].text : f.period + ' jours';
        active.push(`Période : ${txt}`);
    }
    return active.length > 0 ? active.join('  •  ') : 'Aucun filtre (Tous les apprenants et cours inclus)';
}



/* ── Collect report options from the modal ──────────────────────── */
function collectReportOpts() {
    const d = lastDashboardData || {};
    return {
        d,
        title      : document.getElementById('rep-title').value.trim()       || 'Rapport de Synthèse Global — PACTDIGITAL-BF',
        subtitle   : document.getElementById('rep-subtitle').value.trim()    || 'Plateforme Nationale de Formation aux Compétences Numériques',
        org        : document.getElementById('rep-org').value.trim()         || 'Ministère de la Transition Digitale, des Postes et des Communications Électroniques',
        emittedBy  : (document.getElementById('rep-emitted-by')?.value || '').trim() || CURRENT_USER_NAME,
        notes      : document.getElementById('rep-notes').value.trim(),
        incKpi     : document.getElementById('rep-sec-kpi').checked,
        incDemo    : document.getElementById('rep-sec-demo').checked,
        incGeo     : document.getElementById('rep-sec-geo').checked,
        incNiveaux : document.getElementById('rep-sec-niveaux').checked,
        incTop     : document.getElementById('rep-sec-top').checked,
        incFilters : document.getElementById('rep-sec-filters').checked,
    };
}

/* ── Build the full standalone HTML of the PDF report ──────────── */
function buildReportHtml(opts) {
    const { d, title, subtitle, org, emittedBy, notes, incKpi, incDemo, incGeo, incNiveaux, incTop, incFilters } = opts;
    const now     = new Date();
    const dateStr = now.toLocaleDateString('fr-FR', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
    const timeStr = now.toLocaleTimeString('fr-FR', { hour:'2-digit', minute:'2-digit' });

    const CSS = `
        @page { size: A4 portrait; margin: 12mm 15mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10pt; color: #1a1a1a; background: #fff;
            padding: 8px; position: relative; min-height: 100vh;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .print-watermark {
            position: fixed; top: 52%; left: 50%;
            transform: translate(-50%, -50%);
            width: 480px; max-width: 75%; opacity: 0.07;
            z-index: 0; pointer-events: none;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .print-content { position: relative; z-index: 1; }
        .print-head-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .print-head-left  { text-align: left;  vertical-align: top; width: 62%; }
        .print-head-right { text-align: right; vertical-align: top; width: 38%; }
        .print-bf-title   { font-size: 10pt; font-weight: 800; letter-spacing: 0.5px; }
        .print-bf-devise  { font-size: 8.5pt; font-style: italic; color: #444; }
        .print-flag-bar   { height: 4px; background: linear-gradient(to right, #D32F2F 50%, #145B37 50%); margin: 10px 0 16px 0; }
        .print-report-title { font-size: 16pt; font-weight: 800; color: #145B37; margin-bottom: 4px; }
        .print-report-meta  { font-size: 8.5pt; color: #444; margin-bottom: 16px; background: #f4f8f5; padding: 8px 12px; border-radius: 4px; border-left: 3px solid #145B37; }
        .print-section    { margin-bottom: 18px; page-break-inside: avoid; }
        .print-sec-title  { font-size: 11pt; font-weight: 700; color: #145B37; border-bottom: 1.5px solid #145B37; padding-bottom: 4px; margin-bottom: 8px; }
        .print-kpis       { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 14px; }
        .print-kpi-card   { border: 1px solid #d0dcd4; border-left: 4px solid #145B37; padding: 8px 10px; border-radius: 4px; background: #fafdfb; }
        .print-kpi-card.red  { border-left-color: #D32F2F; }
        .print-kpi-card.gold { border-left-color: #b8860b; }
        .print-kpi-card.blue { border-left-color: #1565C0; }
        .print-kpi-label  { font-size: 7.5pt; text-transform: uppercase; color: #666; font-weight: 700; margin-bottom: 2px; }
        .print-kpi-val    { font-size: 15pt; font-weight: 800; color: #145B37; }
        .print-table      { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 10px; }
        .print-table th   { background: #eaf3ed !important; color: #145B37 !important; font-weight: 700; border: 1px solid #cbdcd0; padding: 6px 8px; text-align: left; }
        .print-table td   { border: 1px solid #e0e0e0; padding: 5px 8px; }
        .print-footer     { margin-top: 24px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 8pt; color: #666; display: flex; justify-content: space-between; }
    `;

    let body = `
    <table class="print-head-table">
      <tr>
        <td class="print-head-left">
          <div style="display:flex;align-items:center;gap:12px;">
            ${ARMOIRIES_SRC ? `<img src="${ARMOIRIES_SRC}" style="width:50px;height:auto;object-fit:contain;flex-shrink:0;" alt="Armoiries">` : ''}
            <div>
              <div class="print-bf-title">BURKINA FASO</div>
              <div class="print-bf-devise">La Patrie ou la mort, nous vaincrons</div>
              <div style="font-size:8.5pt;margin-top:3px;font-weight:600;color:#145B37;">${org}</div>
              <div style="font-size:8pt;color:#666;">Programme National PACTDIGITAL-BF</div>
            </div>
          </div>
        </td>
        <td class="print-head-right">
          <div style="font-size:8pt;color:#666;">Date du rapport :</div>
          <div style="font-size:8.5pt;font-weight:700;">${dateStr} à ${timeStr}</div>
          <div style="font-size:8.5pt;color:#145B37;margin-top:5px;font-weight:600;">
            Émis par : <span style="color:#111;font-weight:700;">${emittedBy}</span>
          </div>
        </td>
      </tr>
    </table>
    <div class="print-flag-bar"></div>
    <div class="print-report-title">${title}</div>
    <div style="font-size:9.5pt;color:#444;margin-bottom:12px;">${subtitle}</div>`;

    if (incFilters) {
        body += `<div class="print-report-meta"><strong>Filtres appliqués :</strong> ${getActiveFiltersSummary()}</div>`;
    }

    if (incKpi && d.kpi) {
        const k = d.kpi;
        body += `
        <div class="print-section">
          <div class="print-sec-title">Indicateurs Clés de Performance (KPIs)</div>
          <div class="print-kpis">
            <div class="print-kpi-card"><div class="print-kpi-label">Total apprenants</div><div class="print-kpi-val">${k.total_users||0}</div></div>
            <div class="print-kpi-card"><div class="print-kpi-label">Apprenants actifs (30j)</div><div class="print-kpi-val">${k.active_users||0}</div></div>
            <div class="print-kpi-card blue"><div class="print-kpi-label">Total inscriptions</div><div class="print-kpi-val" style="color:#1565C0;">${k.total_enrolments||0}</div></div>
            <div class="print-kpi-card"><div class="print-kpi-label">Cours complétés</div><div class="print-kpi-val">${k.total_completions||0}</div></div>
            <div class="print-kpi-card gold"><div class="print-kpi-label">Attestations délivrées</div><div class="print-kpi-val" style="color:#b8860b;">${k.total_certificates||0}</div></div>
            <div class="print-kpi-card ${(k.taux||0)>=50?'':'red'}"><div class="print-kpi-label">Taux de complétion</div><div class="print-kpi-val" style="color:${(k.taux||0)>=50?'#145B37':'#D32F2F'};">${k.taux||0}%</div></div>
          </div>
        </div>`;
    }

    if (incDemo) {
        const g=d.gender||{}, s=d.statut||{}, inst=d.instruction||{};
        body += `
        <div class="print-section">
          <div class="print-sec-title">Répartition Démographique des Apprenants</div>
          <table class="print-table"><thead><tr><th style="width:33%;">Répartition par Sexe</th><th style="width:33%;">Répartition par Statut</th><th style="width:34%;">Niveau d'Instruction</th></tr></thead>
          <tbody><tr>
            <td style="vertical-align:top;">${Object.entries(g).map(([k,v])=>`<div><strong>${k||'Non précisé'}:</strong> ${v}</div>`).join('')||'<em>Aucune donnée</em>'}</td>
            <td style="vertical-align:top;">${Object.entries(s).map(([k,v])=>`<div><strong>${k||'Non précisé'}:</strong> ${v}</div>`).join('')||'<em>Aucune donnée</em>'}</td>
            <td style="vertical-align:top;">${Object.entries(inst).map(([k,v])=>`<div><strong>${k||'Non précisé'}:</strong> ${v}</div>`).join('')||'<em>Aucune donnée</em>'}</td>
          </tr></tbody></table>
        </div>`;
    }

    if (incGeo) {
        const reg=d.region||{}, prov=d.province||{}, com=d.commune||{};
        body += `
        <div class="print-section">
          <div class="print-sec-title">Répartition Géographique (Territoire)</div>
          <table class="print-table"><thead><tr><th style="width:33%;">Régions</th><th style="width:33%;">Provinces</th><th style="width:34%;">Communes</th></tr></thead>
          <tbody><tr>
            <td style="vertical-align:top;">${Object.entries(reg).map(([k,v])=>`<div><strong>${k||'Non renseignée'}:</strong> ${v}</div>`).join('')||'<em>Aucune donnée</em>'}</td>
            <td style="vertical-align:top;">${Object.entries(prov).map(([k,v])=>`<div><strong>${k||'Non renseignée'}:</strong> ${v}</div>`).join('')||'<em>Aucune donnée</em>'}</td>
            <td style="vertical-align:top;">${Object.entries(com).map(([k,v])=>`<div><strong>${k||'Non renseignée'}:</strong> ${v}</div>`).join('')||'<em>Aucune donnée</em>'}</td>
          </tr></tbody></table>
        </div>`;
    }

    if (incNiveaux && d.niveaux) {
        const labels=['Bronze','Argent','Or'], enr=d.niveaux.enrol||{}, comp=d.niveaux.complete||{};
        body += `
        <div class="print-section">
          <div class="print-sec-title">Niveaux de Certification (Bronze / Argent / Or)</div>
          <table class="print-table"><thead><tr><th>Niveau</th><th>Inscrits</th><th>Complétés</th><th>Taux de réussite</th></tr></thead>
          <tbody>${labels.map(l=>{const e=enr[l]||0,c=comp[l]||0,tx=e>0?Math.round((c/e)*100):0;return`<tr><td><strong>${l}</strong></td><td>${e}</td><td>${c}</td><td><strong>${tx}%</strong></td></tr>`;}).join('')}</tbody>
          </table>
        </div>`;
    }

    if (incTop && d.top_courses && d.top_courses.length) {
        body += `
        <div class="print-section">
          <div class="print-sec-title">Top 10 des Formations les Plus Suivies</div>
          <table class="print-table"><thead><tr><th>Formation</th><th>Catégorie</th><th>Inscrits</th><th>Complétés</th><th>Taux</th></tr></thead>
          <tbody>${d.top_courses.map(c=>`<tr><td><strong>${c.course}</strong></td><td>${c.category}</td><td>${c.enroled}</td><td>${c.completed}</td><td><strong>${c.taux}%</strong></td></tr>`).join('')}</tbody>
          </table>
        </div>`;
    }

    if (notes) {
        body += `
        <div class="print-section" style="background:#fdfdfd;border:1px solid #ddd;padding:10px 14px;border-radius:4px;">
          <div class="print-sec-title" style="margin-bottom:4px;border:none;">Notes &amp; Remarques</div>
          <div style="font-size:8.5pt;color:#333;white-space:pre-wrap;">${notes}</div>
        </div>`;
    }

    body += `
    <div class="print-footer">
      <div>Programme PACTDIGITAL-BF — Plateforme de Formation Numérique</div>
      <div>Document officiel • Émis par ${emittedBy} • Page 1/1</div>
    </div>`;

    return { css: CSS, body };
}

/* ── Toast notification helper ──────────────────────────────────── */
function showToast(msg, type = 'info', duration = 4000) {
    let container = document.getElementById('pact-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'pact-toast-container';
        container.className = 'toast-wrapper';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `pact-toast toast-${type}`;
    toast.innerHTML = msg;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s, transform 0.3s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}



function generateReport() {
    const opts = collectReportOpts();
    if (currentReportFormat === 'pdf') {
        generatePDFReport(opts);
    } else {
        generateExcelReport(opts);
    }
}

function generatePDFReport(opts) {
    const { body } = buildReportHtml(opts);
    closeReportModal();
    printReportHtml(body, opts.title);
}

function printReportHtml(htmlContent, docTitle) {
    let printFrame = document.getElementById('pact-print-frame');
    if (!printFrame) {
        printFrame = document.createElement('iframe');
        printFrame.id = 'pact-print-frame';
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        printFrame.style.visibility = 'hidden';
        document.body.appendChild(printFrame);
    }
    const doc = printFrame.contentWindow.document;
    doc.open();
    doc.write(`<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>${docTitle || 'Rapport PACTDIGITAL-BF'}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm 15mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            background: #fff;
            padding: 8px;
            position: relative;
            min-height: 100vh;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .print-watermark {
            position: fixed;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 480px;
            max-width: 75%;
            opacity: 0.07;
            z-index: 0;
            pointer-events: none;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .print-content {
            position: relative;
            z-index: 1;
        }
        .print-head-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .print-head-left { text-align: left; vertical-align: top; width: 62%; }
        .print-head-right { text-align: right; vertical-align: top; width: 38%; }
        .print-bf-title { font-size: 10pt; font-weight: 800; letter-spacing: 0.5px; }
        .print-bf-devise { font-size: 8.5pt; font-style: italic; color: #444; }
        .print-flag-bar { height: 4px; background: linear-gradient(to right, #D32F2F 50%, #145B37 50%); margin: 10px 0 16px 0; }
        .print-report-title { font-size: 16pt; font-weight: 800; color: #145B37; margin-bottom: 4px; }
        .print-report-meta { font-size: 8.5pt; color: #444; margin-bottom: 16px; background: #f4f8f5; padding: 8px 12px; border-radius: 4px; border-left: 3px solid #145B37; }
        .print-section { margin-bottom: 18px; page-break-inside: avoid; }
        .print-sec-title { font-size: 11pt; font-weight: 700; color: #145B37; border-bottom: 1.5px solid #145B37; padding-bottom: 4px; margin-bottom: 8px; }
        .print-kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 14px; }
        .print-kpi-card { border: 1px solid #d0dcd4; border-left: 4px solid #145B37; padding: 8px 10px; border-radius: 4px; background: #fafdfb; }
        .print-kpi-card.red { border-left-color: #D32F2F; }
        .print-kpi-card.gold { border-left-color: #b8860b; }
        .print-kpi-card.blue { border-left-color: #1565C0; }
        .print-kpi-label { font-size: 7.5pt; text-transform: uppercase; color: #666; font-weight: 700; margin-bottom: 2px; }
        .print-kpi-val { font-size: 15pt; font-weight: 800; color: #145B37; }
        .print-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 10px; }
        .print-table th { background: #eaf3ed !important; color: #145B37 !important; font-weight: 700; border: 1px solid #cbdcd0; padding: 6px 8px; text-align: left; }
        .print-table td { border: 1px solid #e0e0e0; padding: 5px 8px; }
        .print-footer { margin-top: 24px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 8pt; color: #666; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    ${ARMOIRIES_SRC ? `<img src="${ARMOIRIES_SRC}" class="print-watermark" alt="Armoiries du Burkina Faso">` : ''}
    <div class="print-content">
        ${htmlContent}
    </div>
</body>
</html>`);
    doc.close();

    setTimeout(() => {
        printFrame.contentWindow.focus();
        printFrame.contentWindow.print();
    }, 250);
}

function generateExcelReport(opts) {
    if (typeof XLSX === 'undefined') {
        alert("La bibliothèque Excel n'est pas encore prête. Veuillez réessayer.");
        return;
    }
    const { d, title, subtitle, org, notes, incKpi, incDemo, incGeo, incNiveaux, incTop, incFilters } = opts;
    const wb = XLSX.utils.book_new();

    // 1. Feuille Résumé & KPIs
    const resumeRows = [
        ["BURKINA FASO — La Patrie ou la mort, nous vaincrons"],
        ["PROGRAMME NATIONAL PACTDIGITAL-BF"],
        [title],
        [subtitle],
        ["Organisme émetteur :", org],
        ["Émis par :", emittedBy],
        ["Date d'export :", new Date().toLocaleString('fr-FR')],
        ["Filtres appliqués :", incFilters ? getActiveFiltersSummary() : "Tous"],
        [""]
    ];

    if (incKpi && d.kpi) {
        const k = d.kpi;
        resumeRows.push(
            ["INDICATEURS CLÉS DE PERFORMANCE (KPI)"],
            ["Indicateur", "Valeur"],
            ["Total apprenants", k.total_users || 0],
            ["Apprenants actifs (30 derniers jours)", k.active_users || 0],
            ["Total des inscriptions", k.total_enrolments || 0],
            ["Cours complétés", k.total_completions || 0],
            ["Attestations délivrées", k.total_certificates || 0],
            ["Taux de complétion global", (k.taux || 0) + "%"],
            [""]
        );
    }

    if (notes) {
        resumeRows.push(["NOTES & REMARQUES :", notes]);
    }

    const wsResume = XLSX.utils.aoa_to_sheet(resumeRows);
    XLSX.utils.book_append_sheet(wb, wsResume, "Résumé & KPIs");

    // 2. Feuille Démographie
    if (incDemo) {
        const demoRows = [
            ["RÉPARTITIONS DÉMOGRAPHIQUES"],
            [""],
            ["RÉPARTITION PAR SEXE", "Nombre"],
            ...Object.entries(d.gender || {}).map(([k, v]) => [k || "Non renseigné", v]),
            [""],
            ["RÉPARTITION PAR STATUT", "Nombre"],
            ...Object.entries(d.statut || {}).map(([k, v]) => [k || "Non renseigné", v]),
            [""],
            ["RÉPARTITION PAR NIVEAU D'INSTRUCTION", "Nombre"],
            ...Object.entries(d.instruction || {}).map(([k, v]) => [k || "Non renseigné", v])
        ];
        const wsDemo = XLSX.utils.aoa_to_sheet(demoRows);
        XLSX.utils.book_append_sheet(wb, wsDemo, "Démographie");
    }

    // 3. Feuille Territoire
    if (incGeo) {
        const geoRows = [
            ["RÉPARTITION TERRITORIALE (BURKINA FASO)"],
            [""],
            ["RÉGION", "Nombre d'apprenants"],
            ...Object.entries(d.region || {}).map(([k, v]) => [k || "Non renseignée", v]),
            [""],
            ["PROVINCE", "Nombre d'apprenants"],
            ...Object.entries(d.province || {}).map(([k, v]) => [k || "Non renseignée", v]),
            [""],
            ["COMMUNE", "Nombre d'apprenants"],
            ...Object.entries(d.commune || {}).map(([k, v]) => [k || "Non renseignée", v])
        ];
        const wsGeo = XLSX.utils.aoa_to_sheet(geoRows);
        XLSX.utils.book_append_sheet(wb, wsGeo, "Territoire");
    }

    // 4. Feuille Niveaux
    if (incNiveaux && d.niveaux) {
        const enr = d.niveaux.enrol || {};
        const comp = d.niveaux.complete || {};
        const nivRows = [
            ["PROGRESSION PAR NIVEAU"],
            ["Niveau", "Inscrits", "Complétés", "Taux de réussite (%)"],
            ...['Bronze', 'Argent', 'Or'].map(l => {
                const e = enr[l] || 0;
                const c = comp[l] || 0;
                const tx = e > 0 ? Math.round((c / e) * 100) : 0;
                return [l, e, c, tx + "%"];
            })
        ];
        const wsNiv = XLSX.utils.aoa_to_sheet(nivRows);
        XLSX.utils.book_append_sheet(wb, wsNiv, "Niveaux");
    }

    // 5. Feuille Top Formations
    if (incTop && d.top_courses && d.top_courses.length) {
        const courseRows = [
            ["TOP 10 DES FORMATIONS LES PLUS SUIVIES"],
            ["Formation", "Catégorie", "Inscrits", "Complétés", "Taux de complétion (%)"],
            ...d.top_courses.map(c => [c.course, c.category, c.enroled, c.completed, c.taux + "%"])
        ];
        const wsCourses = XLSX.utils.aoa_to_sheet(courseRows);
        XLSX.utils.book_append_sheet(wb, wsCourses, "Top Formations");
    }

    const filename = `rapport_pactdigital_${new Date().toISOString().slice(0,10)}.xlsx`;
    XLSX.writeFile(wb, filename);
    closeReportModal();
}

// ═══════════════════════════════════════════════════════════════════════════
//  EXPORT 2 : LISTE DES APPRENANTS PERSONNALISÉE (CSV / EXCEL)
// ═══════════════════════════════════════════════════════════════════════════

let currentLearnerFormat = 'csv-semi';

const LEARNER_COLUMNS = {
    nom:                { label: 'Nom et prénom',          val: l => l.nom || l.name || '' },
    email:              { label: 'Adresse email',          val: l => l.email || '' },
    sexe:               { label: 'Sexe',                   val: l => l.sexe || '–' },
    region:             { label: 'Région',                 val: l => l.region || '–' },
    province:           { label: 'Province',               val: l => l.province || '–' },
    commune:            { label: 'Commune',                val: l => l.commune || '–' },
    statut:             { label: 'Statut',                 val: l => l.statut || '–' },
    niveau_instruction: { label: "Niveau d'instruction",   val: l => l.niveau_instruction || l.instruction || '–' },
    inscription:        { label: "Date d'inscription",     val: l => l.inscription || '–' },
    enroled:            { label: 'Formations inscrites',   val: l => l.enroled ?? l.enrolments ?? 0 },
    completed:          { label: 'Formations complétées',  val: l => l.completed ?? 0 },
    certificates:       { label: 'Attestations délivrées', val: l => l.certificates ?? l.certs ?? 0 },
    taux:               { label: 'Taux de complétion',     val: l => (l.enroled ?? l.enrolments ?? 0) > 0 ? Math.round(((l.completed ?? 0) / (l.enroled ?? l.enrolments ?? 1)) * 100) + '%' : '0%' },
    last_access:        { label: 'Dernière connexion',     val: l => l.last_access || l.lastaccess || '–' }
};

function openLearnerExportModal() {
    const cntF = document.getElementById('cnt-filtered');
    const cntS = document.getElementById('cnt-search');
    if (cntF) cntF.textContent = allLearners.length;
    if (cntS) cntS.textContent = filteredLearners.length;
    document.getElementById('learner-filename').value = `pactdigital_apprenants_${new Date().toISOString().slice(0,10)}`;
    document.getElementById('modal-learners-backdrop').style.display = 'flex';
}

function closeLearnerExportModal() {
    document.getElementById('modal-learners-backdrop').style.display = 'none';
}

function selectLearnerFormat(fmt) {
    currentLearnerFormat = fmt;
    document.getElementById('pill-learn-csv-semi').classList.toggle('selected', fmt === 'csv-semi');
    document.getElementById('pill-learn-csv-comma').classList.toggle('selected', fmt === 'csv-comma');
    document.getElementById('pill-learn-xlsx').classList.toggle('selected', fmt === 'xlsx');
}

function toggleLearnerCols(val) {
    document.querySelectorAll('.col-check').forEach(cb => cb.checked = val);
}

function generateLearnerExport() {
    // 1. Déterminer le périmètre des données
    const scope = document.querySelector('input[name="learner_scope"]:checked').value;
    let targetLearners = [];
    if (scope === 'filtered') {
        targetLearners = allLearners;
    } else if (scope === 'search') {
        targetLearners = filteredLearners;
    } else { // page
        const start = (currentPage - 1) * PAGE_SIZE;
        targetLearners = filteredLearners.slice(start, start + PAGE_SIZE);
    }

    if (!targetLearners.length) {
        alert("Aucun apprenant à exporter dans la sélection actuelle.");
        return;
    }

    // 2. Déterminer les colonnes sélectionnées
    const selectedKeys = Array.from(document.querySelectorAll('.col-check:checked')).map(cb => cb.value);
    if (!selectedKeys.length) {
        alert("Veuillez sélectionner au moins une colonne à exporter.");
        return;
    }

    const includeHeaders = document.getElementById('learner-opt-headers').checked;
    const includeBOM = document.getElementById('learner-opt-bom').checked;
    let baseName = document.getElementById('learner-filename').value.trim() || 'pactdigital_apprenants';
    baseName = baseName.replace(/[^a-zA-Z0-9_\-]/g, '_');

    // 3. Traiter selon le format
    if (currentLearnerFormat === 'xlsx') {
        if (typeof XLSX === 'undefined') {
            alert("La bibliothèque Excel est en cours de chargement. Veuillez réessayer.");
            return;
        }
        const wb = XLSX.utils.book_new();
        const headerRow = selectedKeys.map(k => LEARNER_COLUMNS[k].label);
        const dataRows = targetLearners.map(l => selectedKeys.map(k => LEARNER_COLUMNS[k].val(l)));
        const finalRows = includeHeaders ? [headerRow, ...dataRows] : dataRows;

        const ws = XLSX.utils.aoa_to_sheet(finalRows);
        XLSX.utils.book_append_sheet(wb, ws, "Apprenants");
        XLSX.writeFile(wb, `${baseName}.xlsx`);
    } else {
        // CSV
        const delimiter = currentLearnerFormat === 'csv-comma' ? ',' : ';';
        const rows = [];
        if (includeHeaders) {
            rows.push(selectedKeys.map(k => `"${LEARNER_COLUMNS[k].label.replace(/"/g, '""')}"`).join(delimiter));
        }
        targetLearners.forEach(l => {
            const rowVals = selectedKeys.map(k => {
                const val = (LEARNER_COLUMNS[k].val(l) ?? '').toString();
                return `"${val.replace(/"/g, '""')}"`;
            });
            rows.push(rowVals.join(delimiter));
        });

        const csvContent = (includeBOM ? '\uFEFF' : '') + rows.join('\r\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `${baseName}.csv`;
        a.click();
    }

    closeLearnerExportModal();
}

// ── Apply / reset ─────────────────────────────────────────────────────────
function applyFilters() { loadDashboard(); }

function resetFilters() {
    ['f-sexe','f-region','f-province','f-commune','f-statut','f-niveau'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    if (typeof updateDashboardProvinces === 'function') {
        updateDashboardProvinces();
    }
    document.getElementById('f-catid').value  = '0';
    document.getElementById('f-period').value = '0';
    if (typeof pactResetMapState === 'function') {
        pactResetMapState();
    }
    loadDashboard();
}

// ── Init ──────────────────────────────────────────────────────────────────
(async () => {
    await loadFilters();
    await loadDashboard();
})();
</script>

<?php
echo $OUTPUT->footer();
