<?php
// fetch_posts.php
require 'config.php';

$limit = intval($_GET['limit'] ?? 50);
$offset = intval($_GET['offset'] ?? 0);

$stmt = $pdo->prepare('SELECT p.*, u.name as author_name, u.email as author_email FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT ? OFFSET ?');
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
jsonResponse(['posts'=>$posts]);
