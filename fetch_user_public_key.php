<?php
include 'db_connect.php';
$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT name, phone, public_key FROM users WHERE id=:id");
$stmt->execute([':id'=>$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($user ?: ['error'=>'User not found']);
?>
