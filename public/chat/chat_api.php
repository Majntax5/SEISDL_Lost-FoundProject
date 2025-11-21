<?php
// Lightweight chat API for polling + actions
require_once __DIR__ . '/../../inc/functions.php';
if (!is_logged_in()) { header('HTTP/1.1 401 Unauthorized'); echo "Unauthorized"; exit; }
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $room = intval($_GET['room'] ?? 0);
    $stmt = $pdo->prepare("SELECT cm.*, u.name FROM chat_messages cm JOIN users u ON u.id = cm.user_id WHERE cm.room_id = ? ORDER BY cm.created_at ASC");
    $stmt->execute([$room]);
    $msg = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($msg);
    exit;
}

$action = $_POST['action'] ?? 'send';
if ($action === 'create_room') {
    $name = trim($_POST['room_name'] ?? '');
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO chat_rooms (name) VALUES (?)");
        $stmt->execute([$name]);
    }
    header('Location: chat.php'); exit;
}

// send message
if ($action === 'send') {
    $room_id = intval($_POST['room_id'] ?? 0);
    $content = trim($_POST['message'] ?? '');
    if ($room_id && $content) {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (room_id, user_id, content, is_bot) VALUES (?, ?, ?, 0)");
        $stmt->execute([$room_id, $user['id'], $content]);

        // Simple chatbot triggers (server side)
        $lc = strtolower($content);
        $bot_response = null;
        if (strpos($lc, 'help') !== false) {
            $bot_response = "Bot: If you lost something, post it in Dashboard with details and a photo. Use exact location for better results.";
        } elseif (strpos($lc, 'where') !== false) {
            $bot_response = "Bot: Check the latest posts under 'Found Items' or search by location.";
        } elseif (strpos($lc, 'hello') !== false || strpos($lc, 'hi') !== false) {
            $bot_response = "Bot: Hello! I'm the RPSU assistant. Ask me 'help' for tips.";
        }

        if ($bot_response) {
            $stmt = $pdo->prepare("INSERT INTO chat_messages (room_id, user_id, content, is_bot) VALUES (?, ?, ?, 1)");
            // bot is stored as user_id = current user (for simplicity); or we could create a dedicated system user
            $stmt->execute([$room_id, $user['id'], $bot_response]);
        }
    }
    header('HTTP/1.1 204 No Content'); exit;
}