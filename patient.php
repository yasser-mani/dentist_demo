<?php
require_once 'includes/config.php';
require_once 'includes/helpers.php';
$pageTitle = 'Profil patient';
require_once 'includes/header.php';

$patientId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$patientId) {
    header('Location: patients.php');
    exit;
}
?>

<div class="profile-header" id="profileHeader">
    <div class="profile-breadcrumb">
        <a href="patients.php">← Retour aux patients</a>
    </div>
    <div class="profile-title skeleton-text">Chargement...</div>
</div>

<div class="profile-grid">
    <!-- Left column: Personal Info + Dental Chart -->
    <div class="profile-main">
        <!-- Patient info card -->
        <div class="card" id="patientInfoCard">
            <div class="card-header">
                <h3 class="card-title">Informations personnelles</h3>
                <button class="btn btn-sm btn-secondary" id="btnEditInfo">Modifier</button>
            </div>
            <div class="card-body skeleton-text">
                Chargement...
            </div>
        </div>

        <!-- Dental chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Carte dentaire</h3>
            </div>
            <div class="card-body">
                <div id="dentalChartLegend" class="chart-legend">
                    <div class="legend-item"><span class="legend-color" style="background:#fff;border:2px solid #d1d5db;"></span> Saine</div>
                    <div class="legend-item"><span class="legend-color" style="background:#ef4444;"></span> Intervention nécessaire</div>
                    <div class="legend-item"><span class="legend-color" style="background:#f59e0b;"></span> Traitement en cours</div>
                    <div class="legend-item"><span class="legend-color" style="background:#22c55e;"></span> Traitée</div>
                </div>
                <div id="dentalChart"></div>
                <div id="toothPanel" class="tooth-panel" style="display:none;">
                    <div class="tooth-panel-header">
                        <h4 id="toothPanelTitle">Dent</h4>
                        <button class="tooth-panel-close" id="closePanelBtn">✕</button>
                    </div>
                    <div class="tooth-panel-body">
                        <div class="form-group">
                            <label>État</label>
                            <select id="toothState" class="form-control">
                                <option value="healthy">Saine</option>
                                <option value="needs_intervention">Intervention nécessaire</option>
                                <option value="in_progress">Traitement en cours</option>
                                <option value="treated">Traitée</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Traitement</label>
                            <input type="text" id="toothTreatment" class="form-control" placeholder="Ex: Couronne céramique">
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea id="toothNotes" class="form-control" rows="3" placeholder="Observations..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" id="toothDate" class="form-control">
                        </div>
                        <button class="btn btn-primary btn-block" id="saveToothBtn">Enregistrer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right column: Notes + Attachments + PDF Report -->
    <div class="profile-sidebar">
        <!-- Notes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Notes</h3>
                <button class="btn btn-sm btn-secondary" id="btnAddNote">Ajouter</button>
            </div>
            <div class="card-body">
                <div id="notesList" class="notes-list skeleton-text">
                    Chargement...
                </div>
            </div>
        </div>

        <!-- Attachments -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pièces jointes</h3>
                <label for="uploadFile" class="btn btn-sm btn-secondary" style="cursor:pointer;">Ajouter</label>
                <input type="file" id="uploadFile" style="display:none;" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
            </div>
            <div class="card-body">
                <div id="attachmentsList" class="attachments-list skeleton-text">
                    Chargement...
                </div>
            </div>
        </div>

        <!-- PDF Report -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rapport patient</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Générez un rapport PDF complet avec toutes les informations du patient.</p>
                <a href="pdf/report.php?patient_id=<?= $patientId ?>" target="_blank" class="btn btn-primary btn-block">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="margin-right:8px;">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"/>
                    </svg>
                    Générer le PDF
                </a>
            </div>
        </div>
    </div>
</div>

<script>
window.PATIENT_ID = <?= $patientId ?>;
</script>

<?php
$pageScripts = [
    'assets/js/profile.js',
    'assets/js/dental-chart.js',
    'assets/js/notes.js',
    'assets/js/attachments.js',
];
require_once 'includes/footer.php';
?>