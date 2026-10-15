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
$current_user_name = fullname($USER);

$armoiries_path = $CFG->dirroot . '/theme/pactdigital/pix/armoiries.png';
$armoiries_base64 = '';
if (file_exists($armoiries_path)) {
    $armoiries_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($armoiries_path));
}

echo $OUTPUT->header();
?>

<style>
:root {
    --green:  #145B37;
    --red:    #D32F2F;
    --gold:   #d4af37;
    --blue:   #1565C0;
    --grey:   #757575;
    --light:  #F5F7F5;
    --border: #dce8e2;
    --white:  #ffffff;
    --shadow: 0 2px 12px rgba(20,91,55,.10);
}
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f0f4f0;font-family:'Segoe UI',Arial,sans-serif}

/* ── Layout ───────────────────────────────────────────────────────── */
.pact-dash{padding:20px;max-width:1600px;margin:0 auto}

/* ── Header bar ──────────────────────────────────────────────────── */
.pact-header{
    background:linear-gradient(135deg,var(--green) 0%,#1e7a4a 100%);
    color:#fff;padding:22px 28px;border-radius:12px;
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:24px;box-shadow:var(--shadow);
}
.pact-header h1{font-size:1.5rem;font-weight:700;letter-spacing:.5px}
.pact-header p{font-size:.85rem;opacity:.85;margin-top:4px}
.pact-header .logo-area{display:flex;gap:12px;align-items:center}
.pact-header .badge{
    background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);
    padding:4px 12px;border-radius:20px;font-size:.78rem
}

/* ── Filter bar ──────────────────────────────────────────────────── */
.filter-bar{
    background:var(--white);border:1px solid var(--border);
    border-radius:12px;padding:18px 22px;margin-bottom:24px;
    box-shadow:var(--shadow);
}
.filter-bar h3{color:var(--green);font-size:.95rem;margin-bottom:14px;
    display:flex;align-items:center;gap:8px}
.filter-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(175px,1fr));
    gap:12px;align-items:end
}
.filter-group label{font-size:.78rem;color:var(--grey);font-weight:600;
    text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px}
.filter-group select,.filter-group input{
    width:100%;padding:8px 10px;border:1px solid var(--border);
    border-radius:7px;font-size:.88rem;background:#fff;color:#333;
    transition:border-color .2s;
}
.filter-group select:focus,.filter-group input:focus{
    outline:none;border-color:var(--green)}
.filter-actions{display:flex;gap:10px;align-items:flex-end}
.btn{padding:8px 20px;border:none;border-radius:7px;cursor:pointer;
    font-size:.88rem;font-weight:600;transition:all .2s}
.btn-green{background:var(--green);color:#fff}
.btn-green:hover{background:#0f4228}
.btn-outline{background:#fff;color:var(--green);border:1.5px solid var(--green)}
.btn-outline:hover{background:#e8f5ee}
.btn-red{background:var(--red);color:#fff}
.btn-red:hover{background:#b71c1c}

/* ── KPI Cards ───────────────────────────────────────────────────── */
.kpi-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(190px,1fr));
    gap:16px;margin-bottom:24px
}
.kpi-card{
    background:var(--white);border-radius:12px;padding:20px;
    box-shadow:var(--shadow);border-left:4px solid var(--green);
    display:flex;flex-direction:column;gap:6px;position:relative;overflow:hidden
}
.kpi-card::before{
    content:'';position:absolute;right:-15px;top:-15px;
    width:70px;height:70px;border-radius:50%;
    background:rgba(20,91,55,.06)
}
.kpi-card.red{border-left-color:var(--red)}
.kpi-card.gold{border-left-color:var(--gold)}
.kpi-card.blue{border-left-color:var(--blue)}
.kpi-card .icon{font-size:1.5rem}
.kpi-card .label{font-size:.78rem;color:var(--grey);font-weight:600;
    text-transform:uppercase;letter-spacing:.4px}
.kpi-card .value{font-size:2rem;font-weight:800;color:var(--green);line-height:1}
.kpi-card.red .value{color:var(--red)}
.kpi-card.gold .value{color:#b8860b}
.kpi-card.blue .value{color:var(--blue)}
.kpi-card .sub{font-size:.75rem;color:var(--grey)}

/* ── Charts grid ─────────────────────────────────────────────────── */
.charts-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(380px,1fr));
    gap:20px;margin-bottom:24px
}
.chart-card{
    background:var(--white);border-radius:12px;padding:22px;
    box-shadow:var(--shadow);
}
.chart-card.full{grid-column:1/-1}
.chart-card h3{color:var(--green);font-size:.95rem;font-weight:700;
    margin-bottom:16px;display:flex;align-items:center;gap:8px}
.chart-wrap{position:relative;height:260px}
.chart-wrap.small{height:200px}
.chart-wrap.tall{height:320px}

/* ── Table ────────────────────────────────────────────────────────── */
.table-card{
    background:var(--white);border-radius:12px;padding:22px;
    box-shadow:var(--shadow);margin-bottom:24px
}
.table-card h3{color:var(--green);font-size:.95rem;font-weight:700;
    margin-bottom:16px;display:flex;align-items:center;justify-content:space-between}
.table-search{
    padding:7px 12px;border:1px solid var(--border);border-radius:7px;
    font-size:.85rem;width:240px
}
.tbl-wrap{overflow-x:auto;max-height:480px;overflow-y:auto}
table{width:100%;border-collapse:collapse;font-size:.82rem}
thead{position:sticky;top:0;z-index:2}
thead th{
    background:var(--green);color:#fff;padding:10px 12px;
    text-align:left;font-weight:600;white-space:nowrap;cursor:pointer
}
thead th:hover{background:#0f4228}
tbody tr{border-bottom:1px solid var(--border);transition:background .15s}
tbody tr:hover{background:#f0f9f4}
tbody td{padding:9px 12px;white-space:nowrap;color:#333}
.badge-pill{
    display:inline-block;padding:2px 9px;border-radius:20px;
    font-size:.72rem;font-weight:700
}
.badge-bronze{background:#f5e6d3;color:#8B4513}
.badge-or{background:#fff8e1;color:#b8860b}
.badge-diamant{background:#e3f2fd;color:#1565C0}
.taux-bar{
    display:flex;align-items:center;gap:6px
}
.taux-bar .bar{
    flex:1;height:6px;background:#e0e0e0;border-radius:3px;overflow:hidden
}
.taux-bar .fill{height:100%;background:var(--green);border-radius:3px}
.pagination{display:flex;gap:6px;margin-top:14px;align-items:center;
    justify-content:flex-end}
.pagination button{
    padding:5px 12px;border:1px solid var(--border);border-radius:6px;
    background:#fff;cursor:pointer;font-size:.82rem
}
.pagination button.active{background:var(--green);color:#fff;border-color:var(--green)}
.pagination button:hover:not(.active){background:#e8f5ee}

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
      <h1>📊 Tableau de bord PACTDIGITAL-BF</h1>
      <p>Statistiques complètes de la plateforme de formation numérique</p>
    </div>
    <div class="logo-area">
      <span class="badge">🕐 Mis à jour: <span id="last-update">–</span></span>
      <a href="<?= (new moodle_url('/local/pactdigital/formateur.php'))->out(false) ?>" class="btn" style="background:rgba(255,255,255,0.2);color:#fff;font-weight:700;border:1px solid rgba(255,255,255,0.4);border-radius:20px;padding:8px 16px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
        🧑‍🏫 Espace Formateur
      </a>
      <button class="btn" onclick="openReportModal()" style="background:#fad615;color:#000;font-weight:700;border:none;border-radius:20px;padding:8px 18px;box-shadow:0 3px 8px rgba(0,0,0,0.2);display:inline-flex;align-items:center;gap:6px;cursor:pointer;transition:transform 0.15s;">
        📄 Exporter le rapport (PDF / Excel)
      </button>
    </div>
  </div>

  <!-- Filter bar -->
  <div class="filter-bar">
    <h3>🔍 Filtres</h3>
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
          <option value="Or">Or</option>
          <option value="Diamant">Diamant</option>
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
      <h3>📈 Inscriptions dans le temps</h3>
      <div class="chart-wrap"><canvas id="chart-timeline"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>⚧ Répartition par sexe</h3>
      <div class="chart-wrap small"><canvas id="chart-gender"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>🎓 Statut des apprenants</h3>
      <div class="chart-wrap small"><canvas id="chart-statut"></canvas></div>
    </div>
  </div>

  <!-- Charts Row 2 -->
  <div class="charts-grid">
    <div class="chart-card">
      <h3>🗺️ Répartition par Région</h3>
      <div class="chart-wrap tall"><canvas id="chart-region"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>📍 Répartition par Province</h3>
      <div class="chart-wrap tall"><canvas id="chart-province"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>🏘️ Répartition par Commune</h3>
      <div class="chart-wrap tall"><canvas id="chart-commune"></canvas></div>
    </div>
  </div>

  <!-- Charts Row 3 -->
  <div class="charts-grid">
    <div class="chart-card">
      <h3>📚 Inscriptions par catégorie</h3>
      <div class="chart-wrap tall"><canvas id="chart-categories"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>🏆 Niveaux Bronze / Or / Diamant</h3>
      <div class="chart-wrap"><canvas id="chart-niveaux"></canvas></div>
    </div>
    <div class="chart-card">
      <h3>🎓 Niveau d'instruction</h3>
      <div class="chart-wrap small"><canvas id="chart-instruction"></canvas></div>
    </div>
  </div>

  <!-- Top Courses Table -->
  <div class="table-card">
    <h3>
      🏅 Top 10 formations les plus suivies
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
        👥 Liste des apprenants
      </h3>
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <input type="text" class="table-search" id="learner-search"
               placeholder="🔍 Rechercher un apprenant…" oninput="filterTable()">
        <button class="btn" onclick="openLearnerExportModal()" style="background:#fad615;color:#000;font-weight:700;border:none;border-radius:20px;padding:8px 16px;box-shadow:0 2px 6px rgba(0,0,0,0.15);display:inline-flex;align-items:center;gap:6px;cursor:pointer;font-size:.85rem;transition:all 0.15s;">
          📥 Exporter la liste (CSV / Excel)
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
      <h2>📄 Personnaliser le Rapport Global</h2>
      <button class="pact-modal-close" onclick="closeReportModal()">&times;</button>
    </div>
    <div class="pact-modal-body">
      
      <!-- Format Picker -->
      <div class="modal-section">
        <div class="modal-section-title">Format de sortie</div>
        <div class="format-pills">
          <label class="format-pill selected" id="pill-report-pdf" onclick="selectReportFormat('pdf')">
            <input type="radio" name="report_format" value="pdf" checked>
            <div class="icon">📄</div>
            <div class="name">Rapport PDF</div>
            <div class="desc">Mise en page officielle A4 / Prêt à imprimer</div>
          </label>
          <label class="format-pill" id="pill-report-xlsx" onclick="selectReportFormat('xlsx')">
            <input type="radio" name="report_format" value="xlsx">
            <div class="icon">📊</div>
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
          <label class="custom-check"><input type="checkbox" id="rep-sec-kpi" checked> 📈 Indicateurs clés (KPIs)</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-demo" checked> ⚧ Sexe, Statut & Instruction</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-geo" checked> 🗺️ Région, Province, Commune</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-niveaux" checked> 🏆 Niveaux Bronze, Or, Diamant</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-top" checked> 🏅 Top 10 Formations</label>
          <label class="custom-check"><input type="checkbox" id="rep-sec-filters" checked> 🔍 Filtres actuellement appliqués</label>
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
        📄 Générer le rapport
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
      <h2>📥 Personnaliser l'Export des Apprenants</h2>
      <button class="pact-modal-close" onclick="closeLearnerExportModal()">&times;</button>
    </div>
    <div class="pact-modal-body">

      <!-- Format Picker -->
      <div class="modal-section">
        <div class="modal-section-title">Format de fichier</div>
        <div class="format-pills">
          <label class="format-pill selected" id="pill-learn-csv-semi" onclick="selectLearnerFormat('csv-semi')">
            <input type="radio" name="learner_format" value="csv-semi" checked>
            <div class="icon">📑</div>
            <div class="name">CSV (; point-virgule)</div>
            <div class="desc">Recommandé pour Excel (France / Francophone)</div>
          </label>
          <label class="format-pill" id="pill-learn-csv-comma" onclick="selectLearnerFormat('csv-comma')">
            <input type="radio" name="learner_format" value="csv-comma">
            <div class="icon">📑</div>
            <div class="name">CSV (, virgule)</div>
            <div class="desc">Standard international / Outils d'analyse</div>
          </label>
          <label class="format-pill" id="pill-learn-xlsx" onclick="selectLearnerFormat('xlsx')">
            <input type="radio" name="learner_format" value="xlsx">
            <div class="icon">📊</div>
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
        📥 Télécharger la liste
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
const ARMOIRIES_SRC = <?= json_encode($armoiries_base64) ?>;
const CURRENT_USER_NAME = <?= json_encode($current_user_name) ?>;
const GREEN  = '#145B37';
const RED    = '#D32F2F';
const GOLD   = '#d4af37';
const BLUE   = '#1565C0';
const COLORS = ['#145B37','#D32F2F','#d4af37','#1565C0','#6a1b9a','#00838f','#e65100','#33691e','#4e342e','#00695c','#283593','#880e4f'];

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

    populateSelect('f-sexe',     fo.sexe);
    populateSelect('f-region',   fo.region);
    populateSelect('f-province', fo.province);
    populateSelect('f-commune',  fo.commune);
    populateSelect('f-statut',   fo.statut);

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
    renderProvince(d.province || {});
    renderCommune(d.commune || {});
    renderCategories(d.categories || {});
    renderNiveaux(d.niveaux || {});
    renderInstruction(d.instruction || {});
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
        { icon:'👥', label:'Total apprenants',       value: k.total_users     || 0, color:'' },
        { icon:'✅', label:'Apprenants actifs (30j)', value: k.active_users    || 0, color:'' },
        { icon:'📚', label:'Inscriptions',            value: k.total_enrolments|| 0, color:'blue' },
        { icon:'🎓', label:'Cours complétés',         value: k.total_completions||0, color:'' },
        { icon:'📜', label:'Attestations délivrées',  value: k.total_certificates||0, color:'gold' },
        { icon:'🏆', label:'Taux de complétion',      value: (k.taux||0) + '%', color: (k.taux||0) >= 50 ? '' : 'red' },
    ];
    document.getElementById('kpi-grid').innerHTML = cards.map(c => `
        <div class="kpi-card ${c.color}">
            <div class="icon">${c.icon}</div>
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
    const labels = ['Bronze', 'Or', 'Diamant'];
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

// ── Top Courses ───────────────────────────────────────────────────────────
function renderTopCourses(courses) {
    const levelClass = n => n === 'Bronze' ? 'badge-bronze' : n === 'Or' ? 'badge-or' : 'badge-diamant';
    const body = document.getElementById('tbl-courses-body');
    if (!courses.length) {
        body.innerHTML = '<tr><td colspan="5" class="empty">Aucune donnée</td></tr>';
        return;
    }
    body.innerHTML = courses.map(c => `
        <tr>
          <td><span class="badge-pill ${levelClass(c.course)}">${c.course}</span></td>
          <td>${c.category}</td>
          <td><strong>${c.enroled}</strong></td>
          <td>${c.completed}</td>
          <td>
            <div class="taux-bar">
              <div class="bar"><div class="fill" style="width:${c.taux}%"></div></div>
              <span>${c.taux}%</span>
            </div>
          </td>
        </tr>
    `).join('');
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
        const taux = l.enroled > 0 ? Math.round((l.completed / l.enroled) * 100) : 0;
        return `<tr>
          <td><strong>${l.nom}</strong><br><small style="color:var(--grey)">${l.email||''}</small></td>
          <td>${l.sexe||'–'}</td>
          <td>${l.region||'–'}</td>
          <td>${l.province||'–'}</td>
          <td>${l.commune||'–'}</td>
          <td>${l.statut||'–'}</td>
          <td>${l.niveau_instruction||'–'}</td>
          <td>${l.enroled}</td>
          <td>${l.completed}</td>
          <td>${l.certificates}</td>
          <td>
            <div class="taux-bar">
              <div class="bar"><div class="fill" style="width:${taux}%"></div></div>
              <span>${taux}%</span>
            </div>
          </td>
          <td>${l.last_access}</td>
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
        btn.innerHTML = '📄 Imprimer / Enregistrer en PDF';
    } else {
        btn.innerHTML = '📊 Télécharger en Excel (.xlsx)';
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
          <div class="print-sec-title">📈 Indicateurs Clés de Performance (KPIs)</div>
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
          <div class="print-sec-title">⚧ Répartition Démographique des Apprenants</div>
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
          <div class="print-sec-title">🗺️ Répartition Géographique (Territoire)</div>
          <table class="print-table"><thead><tr><th style="width:33%;">Régions</th><th style="width:33%;">Provinces</th><th style="width:34%;">Communes</th></tr></thead>
          <tbody><tr>
            <td style="vertical-align:top;">${Object.entries(reg).map(([k,v])=>`<div><strong>${k||'Non renseignée'}:</strong> ${v}</div>`).join('')||'<em>Aucune donnée</em>'}</td>
            <td style="vertical-align:top;">${Object.entries(prov).map(([k,v])=>`<div><strong>${k||'Non renseignée'}:</strong> ${v}</div>`).join('')||'<em>Aucune donnée</em>'}</td>
            <td style="vertical-align:top;">${Object.entries(com).map(([k,v])=>`<div><strong>${k||'Non renseignée'}:</strong> ${v}</div>`).join('')||'<em>Aucune donnée</em>'}</td>
          </tr></tbody></table>
        </div>`;
    }

    if (incNiveaux && d.niveaux) {
        const labels=['Bronze','Or','Diamant'], enr=d.niveaux.enrol||{}, comp=d.niveaux.complete||{};
        body += `
        <div class="print-section">
          <div class="print-sec-title">🏆 Niveaux de Certification (Bronze / Or / Diamant)</div>
          <table class="print-table"><thead><tr><th>Niveau</th><th>Inscrits</th><th>Complétés</th><th>Taux de réussite</th></tr></thead>
          <tbody>${labels.map(l=>{const e=enr[l]||0,c=comp[l]||0,tx=e>0?Math.round((c/e)*100):0;return`<tr><td><strong>${l}</strong></td><td>${e}</td><td>${c}</td><td><strong>${tx}%</strong></td></tr>`;}).join('')}</tbody>
          </table>
        </div>`;
    }

    if (incTop && d.top_courses && d.top_courses.length) {
        body += `
        <div class="print-section">
          <div class="print-sec-title">🏅 Top 10 des Formations les Plus Suivies</div>
          <table class="print-table"><thead><tr><th>Formation</th><th>Catégorie</th><th>Inscrits</th><th>Complétés</th><th>Taux</th></tr></thead>
          <tbody>${d.top_courses.map(c=>`<tr><td><strong>${c.course}</strong></td><td>${c.category}</td><td>${c.enroled}</td><td>${c.completed}</td><td><strong>${c.taux}%</strong></td></tr>`).join('')}</tbody>
          </table>
        </div>`;
    }

    if (notes) {
        body += `
        <div class="print-section" style="background:#fdfdfd;border:1px solid #ddd;padding:10px 14px;border-radius:4px;">
          <div class="print-sec-title" style="margin-bottom:4px;border:none;">📝 Notes &amp; Remarques</div>
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
            ...['Bronze', 'Or', 'Diamant'].map(l => {
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
    nom:                { label: 'Nom et prénom',          val: l => l.nom || '' },
    email:              { label: 'Adresse email',          val: l => l.email || '' },
    sexe:               { label: 'Sexe',                   val: l => l.sexe || '–' },
    region:             { label: 'Région',                 val: l => l.region || '–' },
    province:           { label: 'Province',               val: l => l.province || '–' },
    commune:            { label: 'Commune',                val: l => l.commune || '–' },
    statut:             { label: 'Statut',                 val: l => l.statut || '–' },
    niveau_instruction: { label: "Niveau d'instruction",   val: l => l.niveau_instruction || '–' },
    inscription:        { label: "Date d'inscription",     val: l => l.inscription || '–' },
    enroled:            { label: 'Formations inscrites',   val: l => l.enroled ?? 0 },
    completed:          { label: 'Formations complétées',  val: l => l.completed ?? 0 },
    certificates:       { label: 'Attestations délivrées', val: l => l.certificates ?? 0 },
    taux:               { label: 'Taux de complétion',     val: l => l.enroled > 0 ? Math.round((l.completed / l.enroled) * 100) + '%' : '0%' },
    last_access:        { label: 'Dernière connexion',     val: l => l.last_access || '–' }
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
        document.getElementById(id).value = '';
    });
    document.getElementById('f-catid').value  = '0';
    document.getElementById('f-period').value = '0';
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
