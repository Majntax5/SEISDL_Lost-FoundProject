<?php
require 'db_connect.php'; 

// Receive FormData
$name = trim($_POST['suName'] ?? '');
$email = strtolower(trim($_POST['suEmail'] ?? ''));
$phone = trim($_POST['suPhone'] ?? '');
$password = $_POST['suPass'] ?? '';

$errors = [];

// Validation
if (!$name) $errors[] = "Name is required";
if (!$email) $errors[] = "Email is required";
if (!$password) $errors[] = "Password is required";

if (!empty($errors)) {
    foreach ($errors as $err) {
        echo $err . "<br>";
    }
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check duplicate email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo "Email already exists";
        exit;
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $phone, $hash])) {
        // Success → redirect to login page
        header("Location: login.html");
        exit;
    } else {
        echo "Database insert failed";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
