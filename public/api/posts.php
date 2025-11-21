<?php
// Simple API for posts CRUD (used by links in UI). Note: requires authentication for mutating operations.
require_once __DIR__ . '/../../inc/functions.php';
if (!is_logged_in()) {
    header('HTTP/1.1 401 Unauthorized'); echo "Unauthorized"; exit;
}
$user = current_user();
$action = $_REQUEST['action'] ?? '';

// Delete post (owner or admin)
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$p) { echo "Not found"; exit; }
    if ($p['user_id'] != $user['id'] && !$user['is_admin']) {
        header('HTTP/1.1 403 Forbidden'); echo "Forbidden"; exit;
    }
    $del = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $del->execute([$id]);
    header('Location: ' . BASE_URL . 'public/dashboard.php');
    exit;
}

// Admin update (basic)
if ($action === 'admin_update' && $user['is_admin'] && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $status = in_array($_POST['status'] ?? 'lost', ['lost','found','claimed']) ? $_POST['status'] : 'lost';
    $stmt = $pdo->prepare("UPDATE posts SET title=?, description=?, status=? WHERE id=?");
    $stmt->execute([$title,$desc,$status,$id]);
    header('Location: ' . BASE_URL . 'public/admin/admin.php');
    exit;
}

echo "OK";