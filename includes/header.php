<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> – <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="assets/img/logo.svg" alt="Logo" class="sidebar-logo">
                <h1 class="sidebar-title"><?= e(APP_NAME) ?></h1>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Tableau de bord
                </a>
                <a href="patients.php" class="nav-link <?= $currentPage === 'patients' || $currentPage === 'patient' ? 'active' : '' ?>">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                    Patients
                </a>
                <a href="appointments.php" class="nav-link <?= $currentPage === 'appointments' ? 'active' : '' ?>">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/></svg>
                    Rendez-vous
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
               
                <div class="top-bar-actions">
                    <span class="user-greeting">Bienvenue, Dr. Dupont</span>
                </div>
            </header>

            <div class="content-wrapper">