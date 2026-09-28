<?php
/**
 * Shared helper functions
 */

/**
 * Escape HTML output
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Send JSON response and exit
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send JSON error and exit
 */
function jsonError($message, $statusCode = 400) {
    jsonResponse(['success' => false, 'error' => $message], $statusCode);
}

/**
 * Send JSON success
 */
function jsonSuccess($data = [], $message = null) {
    $response = ['success' => true];
    if ($message !== null) {
        $response['message'] = $message;
    }
    $response['data'] = $data;
    jsonResponse($response);
}

/**
 * Validate phone (French format, lenient)
 */
function isValidPhone($phone) {
    return preg_match('/^[\d\s\.\-\+\(\)]{8,20}$/', $phone);
}

/**
 * Validate date (Y-m-d or Y-m-d H:i:s)
 */
function isValidDate($date) {
    $d = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    if ($d && $d->format('Y-m-d H:i:s') === $date) return true;
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Validate FDI tooth number
 */
function isValidToothNumber($num) {
    $valid = [
        11,12,13,14,15,16,17,18,
        21,22,23,24,25,26,27,28,
        31,32,33,34,35,36,37,38,
        41,42,43,44,45,46,47,48
    ];
    return in_array((int)$num, $valid, true);
}

/**
 * Validate tooth state
 */
function isValidToothState($state) {
    return in_array($state, ['healthy','needs_intervention','in_progress','treated'], true);
}

/**
 * Format date for display (French)
 */
function formatDate($date, $includeTime = false) {
    if (!$date) return '';
    $d = new DateTime($date);
    return $d->format($includeTime ? 'd/m/Y H:i' : 'd/m/Y');
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    if ($bytes < 1024) return $bytes . ' o';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' Ko';
    return round($bytes / 1048576, 1) . ' Mo';
}

/**
 * Generate random filename
 */
function generateRandomFilename($extension) {
    return bin2hex(random_bytes(16)) . '.' . strtolower($extension);
}

/**
 * Get file extension from filename
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Validate uploaded file
 */
function validateUpload($file) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return 'Fichier invalide.';
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Erreur lors du téléchargement.';
    }
    
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return 'Fichier trop volumineux (max ' . formatFileSize(UPLOAD_MAX_SIZE) . ').';
    }
    
    $ext = getFileExtension($file['name']);
    if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
        return 'Type de fichier non autorisé.';
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ALLOWED_MIMES, true)) {
        return 'Type MIME non autorisé.';
    }
    
    return null; // valid
}