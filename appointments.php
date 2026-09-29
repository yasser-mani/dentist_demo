<?php
require_once 'includes/config.php';
require_once 'includes/helpers.php';
$pageTitle = 'Rendez-vous';
require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="page-header-content">
        <h2 class="page-title">Gestion des rendez-vous</h2>
        <p class="page-description">Consultez et planifiez les rendez-vous</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary" id="btnAddAppointment">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/></svg>
            Nouveau rendez-vous
        </button>
    </div>
</div>

<div class="appointments-grid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rendez-vous à venir</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="data-table" id="upcomingTable">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date et heure</th>
                            <th>Motif</th>
                            <th>Statut</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="5" class="skeleton-text">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rendez-vous passés</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="data-table" id="pastTable">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date et heure</th>
                            <th>Motif</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="4" class="skeleton-text">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$pageScripts = ['assets/js/appointments.js'];
require_once 'includes/footer.php';
?>