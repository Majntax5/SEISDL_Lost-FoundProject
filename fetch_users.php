<?php
include 'db_connect.php';
session_start();
if(!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'Login required']); exit; }

$search = $_GET['q'] ?? '';
$stmt = $conn->prepare("SELECT id, name, phone FROM users WHERE id != :me AND name LIKE :search ORDER BY name ASC");
$stmt->execute([':me'=>$_SESSION['user_id'], ':search'=>'%'.$search.'%']);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['users'=>$users]);
?>
