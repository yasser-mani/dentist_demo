<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

/** Strip characters that would break a Content-Disposition header value. */
function safeHeaderFilename(string $name): string {
    $name = str_replace(["\r", "\n", '"'], '', $name);
    return $name !== '' ? $name : 'fichier';
}

try {

    if ($method === 'GET') {

        if (isset($_GET['action']) && $_GET['action'] === 'view') {
            // View/download a file
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) die('ID requis');

            $stmt = $db->prepare("SELECT * FROM attachments WHERE id = ?");
            $stmt->execute([$id]);
            $file = $stmt->fetch();

            if (!$file) die('Fichier introuvable');

            $path = __DIR__ . '/../uploads/' . $file['file_path'];
            if (!file_exists($path)) die('Fichier physique introuvable');

            $download = isset($_GET['download']);
            $safeName = safeHeaderFilename($file['original_name']);

            header('Content-Type: ' . $file['mime_type']);
            header('Content-Length: ' . $file['file_size']);

            if ($download) {
                header('Content-Disposition: attachment; filename="' . $safeName . '"');
            } else {
                // Inline for images/PDF
                if (in_array($file['mime_type'], ['image/jpeg','image/png','application/pdf'])) {
                    header('Content-Disposition: inline; filename="' . $safeName . '"');
                } else {
                    header('Content-Disposition: attachment; filename="' . $safeName . '"');
                }
            }

            readfile($path);
            exit;
        }

        // List attachments
        header('Content-Type: application/json; charset=utf-8');

        $patientId = (int)($_GET['patient_id'] ?? 0);
        if (!$patientId) jsonError('Patient ID requis');

        $stmt = $db->prepare("
            SELECT * FROM attachments
            WHERE patient_id = ?
            ORDER BY uploaded_at DESC
        ");
        $stmt->execute([$patientId]);
        $files = $stmt->fetchAll();

        jsonSuccess($files);

    } elseif ($method === 'POST') {

        // Upload
        header('Content-Type: application/json; charset=utf-8');

        $patientId = (int)($_POST['patient_id'] ?? 0);
        if (!$patientId) jsonError('Patient ID requis');

        if (!isset($_FILES['file'])) jsonError('Aucun fichier');

        $error = validateUpload($_FILES['file']);
        if ($error) jsonError($error);

        $originalName = $_FILES['file']['name'];
        $ext = getFileExtension($originalName);
        $storedName = generateRandomFilename($ext);
        $relativePath = 'patients/' . $storedName;
        $uploadDir = __DIR__ . '/../uploads/patients/';
        $fullPath = __DIR__ . '/../uploads/' . $relativePath;

        // Defensive: create the upload folder if it isn't there yet.
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $fullPath)) {
            jsonError('Échec du téléchargement');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fullPath);
        $size = filesize($fullPath);

        $stmt = $db->prepare("
            INSERT INTO attachments
            (patient_id, original_name, stored_name, file_type, mime_type, file_size, file_path)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $patientId,
            $originalName,
            $storedName,
            $ext,
            $mime,
            $size,
            $relativePath
        ]);

        jsonSuccess(['id' => (int)$db->lastInsertId()], 'Fichier téléchargé');

    } elseif ($method === 'DELETE') {

        header('Content-Type: application/json; charset=utf-8');

        parse_str(file_get_contents('php://input'), $data);
        $id = (int)($data['id'] ?? 0);

        if (!$id) jsonError('ID requis');

        $stmt = $db->prepare("SELECT * FROM attachments WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetch();

        if (!$file) jsonError('Fichier introuvable');

        // Delete physical file
        $path = __DIR__ . '/../uploads/' . $file['file_path'];
        if (file_exists($path)) {
            unlink($path);
        }

        // Delete record
        $stmt = $db->prepare("DELETE FROM attachments WHERE id = ?");
        $stmt->execute([$id]);

        jsonSuccess([], 'Fichier supprimé');

    } else {
        header('Content-Type: application/json; charset=utf-8');
        jsonError('Méthode non supportée', 405);
    }

} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    jsonError('Erreur serveur: ' . $e->getMessage(), 500);
}