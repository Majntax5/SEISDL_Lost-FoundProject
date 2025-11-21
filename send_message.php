<?php
include 'db_connect.php';
session_start();
if(!isset($_SESSION['user_id'])){ echo json_encode(['error'=>'Login required']); exit; }

$data = json_decode(file_get_contents('php://input'), true);
$sender = $_SESSION['user_id'];
$receiver = $data['receiver_id'];
$message = $data['message']; // Can be encrypted
$iv = $data['iv'] ?? null;

$stmt = $conn->prepare("INSERT INTO chats (sender_id, receiver_id, message, iv) VALUES (:s,:r,:m,:iv)");
$stmt->execute([':s'=>$sender, ':r'=>$receiver, ':m'=>$message, ':iv'=>$iv]);
echo json_encode(['success'=>true]);
?>
