<?php
session_start();
require 'db_connect.php'; // PDO

if (empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$uid = $_SESSION['user_id'];
$profile = [];

// Fetch existing profile
$stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$uid]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Handle POST (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full = $_POST['full_name'] ?? '';
    $nick = $_POST['nickname'] ?? '';
    $stdid = $_POST['student_id'] ?? '';
    $dept = $_POST['department'] ?? '';
    $batch = $_POST['batch'] ?? '';
    $photoPath = $profile['photo'] ?? '';

    // Handle file upload
    if (!empty($_FILES['photo']['name'])) {
        if (!is_dir('uploads')) mkdir('uploads', 0755, true);
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $target = 'uploads/profile_' . $uid . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photoPath = $target;
        }
    }

    if ($profile) {
        // Update existing profile
        $stmt = $conn->prepare("UPDATE profiles SET full_name=?, nickname=?, student_id=?, department=?, batch=?, photo=? WHERE user_id=?");
        $stmt->execute([$full, $nick, $stdid, $dept, $batch, $photoPath, $uid]);
    } else {
        // Insert new profile
        $stmt = $conn->prepare("INSERT INTO profiles (user_id, full_name, nickname, student_id, department, batch, photo) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$uid, $full, $nick, $stdid, $dept, $batch, $photoPath]);
    }

    // Redirect after saving
    header("Location: Allpost.html");
    exit;
}
?>
