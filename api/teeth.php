<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

try {
    
    if ($method === 'GET') {
        
        // Get all teeth for a patient
        $patientId = (int)($_GET['patient_id'] ?? 0);
        if (!$patientId) jsonError('Patient ID requis');
        
        $stmt = $db->prepare("
            SELECT * FROM teeth_records
            WHERE patient_id = ?
        ");
        $stmt->execute([$patientId]);
        $teeth = $stmt->fetchAll();
        
        // Return as object keyed by tooth_number for easy lookup
        $map = [];
        foreach ($teeth as $t) {
            $map[$t['tooth_number']] = $t;
        }
        
        jsonSuccess($map);
        
    } elseif ($method === 'POST') {
        
        // Save (upsert) one tooth
        $data = json_decode(file_get_contents('php://input'), true);
        
        $patientId = (int)($data['patient_id'] ?? 0);
        $toothNumber = (int)($data['tooth_number'] ?? 0);
        $state = $data['state'] ?? 'healthy';
        $treatment = trim($data['treatment'] ?? '');
        $notes = trim($data['notes'] ?? '');
        $recordDate = $data['record_date'] ?? null;
        
        if (!$patientId) jsonError('Patient ID requis');
        if (!isValidToothNumber($toothNumber)) jsonError('Numéro de dent invalide');
        if (!isValidToothState($state)) jsonError('État invalide');
        
        if ($recordDate && !isValidDate($recordDate)) {
            jsonError('Date invalide');
        }
        
        // Upsert
        $stmt = $db->prepare("
            INSERT INTO teeth_records
            (patient_id, tooth_number, state, treatment, notes, record_date)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            state = VALUES(state),
            treatment = VALUES(treatment),
            notes = VALUES(notes),
            record_date = VALUES(record_date)
        ");
        
        $stmt->execute([
            $patientId,
            $toothNumber,
            $state,
            $treatment ?: null,
            $notes ?: null,
            $recordDate ?: null
        ]);
        
        jsonSuccess([], 'Dent enregistrée');
        
    } else {
        jsonError('Méthode non supportée', 405);
    }
    
} catch (Exception $e) {
    jsonError('Erreur serveur: ' . $e->getMessage(), 500);
}