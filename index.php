<?php
require_once 'includes/config.php';
require_once 'includes/helpers.php';
$pageTitle = 'Tableau de bord';
require_once 'includes/header.php';
?>

<div class="dashboard-grid">
    <!-- Stats cards -->
    <div class="stats-row" id="statsRow">
        <div class="stat-card skeleton">
            <div class="stat-icon" style="background: #dbeafe;">
                <svg width="24" height="24" fill="#2563eb" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total patients</div>
                <div class="stat-value">—</div>
            </div>
        </div>
        
        <div class="stat-card skeleton">
            <div class="stat-icon" style="background: #dcfce7;">
                <svg width="24" height="24" fill="#16a34a" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/></svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Rendez-vous aujourd'hui</div>
                <div class="stat-value">—</div>
            </div>
        </div>
        
        <div class="stat-card skeleton">
            <div class="stat-icon" style="background: #fef3c7;">
                <svg width="24" height="24" fill="#f59e0b" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">En traitement</div>
                <div class="stat-value">—</div>
            </div>
        </div>
        
        <div class="stat-card skeleton">
            <div class="stat-icon" style="background: #e0e7ff;">
                <svg width="24" height="24" fill="#4f46e5" viewBox="0 0 20 20"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/></svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Nouveaux patients (30j)</div>
                <div class="stat-value">—</div>
            </div>
        </div>
    </div>

    <!-- Today's appointments -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rendez-vous d'aujourd'hui</h3>
        </div>
        <div class="card-body">
            <div id="todayAppointments" class="appointments-list skeleton-text">
                Chargement...
            </div>
        </div>
    </div>

    <!-- Recent patients -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Patients récents</h3>
            <a href="patients.php" class="btn btn-sm btn-secondary">Voir tous</a>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="data-table" id="recentPatientsTable">
                    <thead>
                        <tr>
                            <th>Nom complet</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th>Ajouté le</th>
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
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/dashboard.js"></script>