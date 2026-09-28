<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

try {
    
    if ($method === 'GET') {
        
        $patientId = (int)($_GET['patient_id'] ?? 0);
        if (!$patientId) jsonError('Patient ID requis');
        
        $stmt = $db->prepare("
            SELECT * FROM patient_notes
            WHERE patient_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$patientId]);
        $notes = $stmt->fetchAll();
        
        jsonSuccess($notes);
        
    } elseif ($method === 'POST') {
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $patientId = (int)($data['patient_id'] ?? 0);
        $content = trim($data['content'] ?? '');
        
        if (!$patientId) jsonError('Patient ID requis');
        if (!$content) jsonError('Le contenu est requis');
        
        $stmt = $db->prepare("
            INSERT INTO patient_notes (patient_id, content)
            VALUES (?, ?)
        ");
        $stmt->execute([$patientId, $content]);
        
        jsonSuccess(['id' => (int)$db->lastInsertId()], 'Note ajoutée');
        
    } elseif ($method === 'PUT') {
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $id = (int)($data['id'] ?? 0);
        $content = trim($data['content'] ?? '');
        
        if (!$id) jsonError('ID requis');
        if (!$content) jsonError('Le contenu est requis');
        
        $stmt = $db->prepare("UPDATE patient_notes SET content = ? WHERE id = ?");
        $stmt->execute([$content, $id]);
        
        jsonSuccess([], 'Note modifiée');
        
    } elseif ($method === 'DELETE') {
        
        parse_str(file_get_contents('php://input'), $data);
        $id = (int)($data['id'] ?? 0);
        
        if (!$id) jsonError('ID requis');
        
        $stmt = $db->prepare("DELETE FROM patient_notes WHERE id = ?");
        $stmt->execute([$id]);
        
        jsonSuccess([], 'Note supprimée');
        
    } else {
        jsonError('Méthode non supportée', 405);
    }
    
} catch (Exception $e) {
    jsonError('Erreur serveur: ' . $e->getMessage(), 500);
}