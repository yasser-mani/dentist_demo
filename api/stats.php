<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = getDB();
    
    // Total patients
    $total = $db->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    
    // Today's appointments (scheduled only)
    $today = $db->prepare("
        SELECT COUNT(*) FROM appointments
        WHERE DATE(appointment_date) = CURDATE()
          AND status = 'scheduled'
    ");
    $today->execute();
    $todayCount = $today->fetchColumn();
    
    // In treatment (status_id = 2)
    $treatment = $db->query("SELECT COUNT(*) FROM patients WHERE status_id = 2")->fetchColumn();
    
    // New patients (last 30 days)
    $new = $db->query("
        SELECT COUNT(*) FROM patients
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ")->fetchColumn();
    
    jsonSuccess([
        'total_patients' => (int)$total,
        'today_appointments' => (int)$todayCount,
        'in_treatment' => (int)$treatment,
        'new_patients' => (int)$new
    ]);
    
} catch (Exception $e) {
    jsonError('Erreur serveur', 500);
}