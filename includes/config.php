<?php
/**
 * DentaFlow Configuration
 * Edit these values for your environment.
 */

// Database
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'dentaflow');
define('DB_USER', 'dentist_user');
define('DB_PASS', 'Dentaflow@2026!Db');
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

// Error reporting (disabled display so PHP notices/warnings don't corrupt JSON responses)
error_reporting(E_ALL);
ini_set('display_errors', 0);