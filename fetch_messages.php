<?php
include 'db_connect.php';
session_start();
if(!isset($_SESSION['user_id'])){ echo json_encode(['error'=>'Login required']); exit; }

$other = intval($_GET['user_id']);
$me = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM chats WHERE (sender_id=:me AND receiver_id=:other) OR (sender_id=:other AND receiver_id=:me) ORDER BY created_at ASC");
$stmt->execute([':me'=>$me, ':other'=>$other]);
$msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['messages'=>$msgs]);
?>
