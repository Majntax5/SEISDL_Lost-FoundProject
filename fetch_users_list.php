<?php
include 'db_connect.php';

$stmt = $conn->query("SELECT id, name, public_key FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['users'=>$users]);
?>
