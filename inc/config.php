<?php
// inc/config.php
// Update these values for your environment
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'rpsu_lostfound');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/'); // e.g. '/rpsu-lostfound/' or '/' if root
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    die("DB connection failed: " . $e->getMessage());
}