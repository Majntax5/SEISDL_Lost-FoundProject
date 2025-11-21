<?php
require 'config.php';

// Accept both JSON body and HTML form POST
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data) {
    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';
} else {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
}

if (!$email || !$password) {
    jsonResponse(['error' => 'Email and password required']);
}

$stmt = $pdo->prepare("SELECT id, password_hash, is_admin, name, email, public_key FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    jsonResponse(['error' => 'Invalid credentials']);
}

$_SESSION['user_id'] = $user['id'];

jsonResponse([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'is_admin' => $user['is_admin'],
        'public_key' => $user['public_key']
    ]
]);
