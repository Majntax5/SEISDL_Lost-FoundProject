<?php
// make_admin.php — run once to create admin user securely
require 'config.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        echo "Provide email and password";
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash,is_admin) VALUES (?,?,?,1)');
    $stmt->execute(['Administrator',$email,$hash]);
    echo "Admin created. ID: ".$pdo->lastInsertId();
    exit;
}
?>
<form method="post">
Admin Email: <input name="email"><br>
Password: <input name="password" type="password"><br>
<button type="submit">Create Admin</button>
</form>
