<?php
/**
 * DentaFlow Configuration
 * Edit these values for your environment.
 */

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'dentaflow');
define('DB_USER', 'dentaflow');
define('DB_PASS', 'DentaFlow@2024!');
define('DB_CHARSET', 'utf8mb4');

// Application
define('APP_NAME', 'DentaFlow');
define('APP_URL', 'http://localhost/dentaflow');

// File uploads
define('UPLOAD_DIR', '/var/www/dentaflow-uploads/patients/');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
define('ALLOWED_MIMES', [
    'image/jpeg',
    'image/png',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

// Timezone
date_default_timezone_set('Europe/Paris');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);