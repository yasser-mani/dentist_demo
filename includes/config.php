<?php
/**
 * DentaFlow Configuration
 * Edit these values for your environment.
 */

// Database
// Defaults match a stock XAMPP / WAMP / MAMP install (root, no password).
// Change these to match your own MySQL setup.
define('DB_HOST', 'localhost');
define('DB_NAME', 'dentaflow');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application
define('APP_NAME', 'DentaFlow');
define('APP_URL', 'http://localhost/dentaflow');

// File uploads (stored inside the project, under uploads/patients/)
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