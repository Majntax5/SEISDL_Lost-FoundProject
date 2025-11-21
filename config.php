<?php
// config.php
session_start();

$DB_HOST = '127.0.0.1';
$DB_NAME = 'rpsu_lostfound';
$DB_USER = 'root';
$DB_PASS = ''; // xampp default blank; change if you set a password

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection error: '.$e->getMessage()]);
    exit;
}

function jsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        jsonResponse(['error' => 'Unauthorized']);
    }
}

function getCurrentUser() {
    global $pdo;
    if (empty($_SESSION['user_id'])) return null;
    $stmt = $pdo->prepare('SELECT id, name, email, phone, public_key, profile_json, is_admin FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
