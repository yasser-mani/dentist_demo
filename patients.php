<?php
require_once 'includes/config.php';
require_once 'includes/helpers.php';
$pageTitle = 'Patients';
require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="page-header-content">
        <h2 class="page-title">Gestion des patients</h2>
        <p class="page-description">Recherchez, ajoutez et gérez vos patients</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary" id="btnAddPatient">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/></svg>
            Ajouter un patient
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="filters-row">
            <input type="search" id="searchPatients" class="search-input" placeholder="Rechercher par nom ou téléphone...">
            <select id="filterStatus" class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="1">Nouveau rendez-vous</option>
                <option value="2">En traitement</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="data-table" id="patientsTable">
                <thead>
                    <tr>
                        <th>Nom complet</th>
                        <th>Téléphone</th>
                        <th>Assurance</th>
                        <th>Statut</th>
                        <th>Prochain RDV</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="6" class="skeleton-text">Chargement...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script src="assets/js/patients.js"></script>