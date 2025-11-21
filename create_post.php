<?php
// create_post.php
require 'config.php';
requireLogin();
$user = getCurrentUser();

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$image_path = $data['imagePath'] ?? null; // For demo, allow client to upload separately or provide base64 handler

if (!$title) jsonResponse(['error'=>'Title required']);

$stmt = $pdo->prepare('INSERT INTO posts (user_id,title,description,image_path) VALUES (?,?,?,?)');
$stmt->execute([$user['id'],$title,$description,$image_path]);
jsonResponse(['success'=>true,'post_id'=>$pdo->lastInsertId()]);
