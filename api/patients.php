<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

try {

    if ($method === 'GET') {

        if (isset($_GET['id'])) {
            // Get one patient
            $id = (int)$_GET['id'];
            $stmt = $db->prepare("
                SELECT p.*, ps.label as status_label, ps.badge_color,
                       (SELECT appointment_date FROM appointments
                        WHERE patient_id = p.id AND status = 'scheduled' AND appointment_date >= NOW()
                        ORDER BY appointment_date ASC LIMIT 1) as next_appointment
                FROM patients p
                JOIN patient_statuses ps ON p.status_id = ps.id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            $patient = $stmt->fetch();

            if (!$patient) {
                jsonError('Patient introuvable', 404);
            }

            jsonSuccess($patient);
        } else {
            // List/search
            $search = $_GET['search'] ?? '';
            $statusId = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

            // LIMIT can't be bound as a placeholder under real prepared
            // statements, so it's validated as an int and inlined directly.
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            if ($limit <= 0 || $limit > 500) {
                $limit = 50;
            }

            $sql = "
                SELECT p.*, ps.label as status_label, ps.badge_color,
                       (SELECT appointment_date FROM appointments
                        WHERE patient_id = p.id AND status = 'scheduled' AND appointment_date >= NOW()
                        ORDER BY appointment_date ASC LIMIT 1) as next_appointment
                FROM patients p
                JOIN patient_statuses ps ON p.status_id = ps.id
                WHERE 1=1
            ";

            $params = [];

            if ($search) {
                $sql .= " AND (p.full_name LIKE ? OR p.phone LIKE ?)";
                $params[] = '%' . $search . '%';
                $params[] = '%' . $search . '%';
            }

            if ($statusId) {
                $sql .= " AND p.status_id = ?";
                $params[] = $statusId;
            }

            $sql .= " ORDER BY p.created_at DESC LIMIT " . $limit;

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $patients = $stmt->fetchAll();

            jsonSuccess($patients);
        }

    } elseif ($method === 'POST') {

        // Create patient
        $data = json_decode(file_get_contents('php://input'), true);

        $name = trim($data['full_name'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $insurance = trim($data['insurance'] ?? '');
        $statusId = (int)($data['status_id'] ?? 1);

        if (!$name) jsonError('Le nom est requis');
        if (!$phone) jsonError('Le téléphone est requis');
        if (!isValidPhone($phone)) jsonError('Numéro de téléphone invalide');

        $stmt = $db->prepare("
            INSERT INTO patients (full_name, phone, insurance, status_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$name, $phone, $insurance ?: null, $statusId]);

        $id = $db->lastInsertId();

        jsonSuccess(['id' => (int)$id], 'Patient créé avec succès');

    } elseif ($method === 'PUT') {

        // Update patient
        $data = json_decode(file_get_contents('php://input'), true);

        $id = (int)($data['id'] ?? 0);
        if (!$id) jsonError('ID requis');

        $name = trim($data['full_name'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $insurance = trim($data['insurance'] ?? '');
        $statusId = (int)($data['status_id'] ?? 1);

        if (!$name) jsonError('Le nom est requis');
        if (!$phone) jsonError('Le téléphone est requis');
        if (!isValidPhone($phone)) jsonError('Numéro de téléphone invalide');

        $stmt = $db->prepare("
            UPDATE patients
            SET full_name = ?, phone = ?, insurance = ?, status_id = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $phone, $insurance ?: null, $statusId, $id]);

        jsonSuccess([], 'Patient mis à jour');

    } else {
        jsonError('Méthode non supportée', 405);
    }

} catch (Exception $e) {
    jsonError('Erreur serveur: ' . $e->getMessage(), 500);
}