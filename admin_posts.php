<?php
// admin_posts.php
require 'config.php';
requireLogin();
$user = getCurrentUser();
if (!$user['is_admin']) {
    http_response_code(403);
    jsonResponse(['error'=>'Forbidden']);
}

$stmt = $pdo->query('SELECT p.*, u.name as author_name, u.email as author_email FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC');
jsonResponse(['posts'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
