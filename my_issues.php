<?php
// my_issues.php
require 'config.php';
requireLogin();
$user = getCurrentUser();

$stmt = $pdo->prepare('SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
jsonResponse(['posts'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
