<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

try {
    
    if ($method === 'GET') {
        
        $filter = $_GET['filter'] ?? 'upcoming'; // upcoming | past | today
        
        $sql = "
            SELECT a.*, p.full_name as patient_name
            FROM appointments a
            JOIN patients p ON a.patient_id = p.id
            WHERE 1=1
        ";
        
        if ($filter === 'today') {
            $sql .= " AND DATE(a.appointment_date) = CURDATE() AND a.status = 'scheduled'";
        } elseif ($filter === 'upcoming') {
            $sql .= " AND a.appointment_date >= NOW() AND a.status = 'scheduled'";
        } elseif ($filter === 'past') {
            $sql .= " AND (a.appointment_date < NOW() OR a.status IN ('completed','cancelled'))";
        }
        
        $sql .= " ORDER BY a.appointment_date " . ($filter === 'past' ? 'DESC' : 'ASC');
        
        if ($filter === 'today' || $filter === 'upcoming') {
            $sql .= " LIMIT 20";
        } else {
            $sql .= " LIMIT 50";
        }
        
        $stmt = $db->query($sql);
        $appointments = $stmt->fetchAll();
        
        jsonSuccess($appointments);
        
    } elseif ($method === 'POST') {
        
        // Create appointment
        $data = json_decode(file_get_contents('php://input'), true);
        
        $patientId = (int)($data['patient_id'] ?? 0);
        $date = trim($data['appointment_date'] ?? '');
        $reason = trim($data['reason'] ?? '');
        
        if (!$patientId) jsonError('Patient requis');
        if (!$date) jsonError('Date requise');
        if (!isValidDate($date)) jsonError('Date invalide');
        
        $stmt = $db->prepare("
            INSERT INTO appointments (patient_id, appointment_date, reason, status)
            VALUES (?, ?, ?, 'scheduled')
        ");
        $stmt->execute([$patientId, $date, $reason ?: null]);
        
        jsonSuccess(['id' => (int)$db->lastInsertId()], 'Rendez-vous créé');
        
    } elseif ($method === 'PATCH') {
        
        // Update status
        $data = json_decode(file_get_contents('php://input'), true);
        
        $id = (int)($data['id'] ?? 0);
        $status = $data['status'] ?? '';
        
        if (!$id) jsonError('ID requis');
        if (!in_array($status, ['scheduled','completed','cancelled'], true)) {
            jsonError('Statut invalide');
        }
        
        $stmt = $db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        jsonSuccess([], 'Statut mis à jour');
        
    } elseif ($method === 'DELETE') {
        
        parse_str(file_get_contents('php://input'), $data);
        $id = (int)($data['id'] ?? 0);
        
        if (!$id) jsonError('ID requis');
        
        $stmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        
        jsonSuccess([], 'Rendez-vous supprimé');
        
    } else {
        jsonError('Méthode non supportée', 405);
    }
    
} catch (Exception $e) {
    jsonError('Erreur serveur: ' . $e->getMessage(), 500);
}