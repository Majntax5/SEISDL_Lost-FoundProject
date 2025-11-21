<?php
// delete_message.php
require 'config.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$msg_id = intval($data['message_id'] ?? 0);
if (!$msg_id) jsonResponse(['error'=>'message_id required']);

$user = getCurrentUser();
$stmt = $pdo->prepare('SELECT * FROM messages WHERE id = ?');
$stmt->execute([$msg_id]);
$msg = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$msg) jsonResponse(['error'=>'Message not found']);

// allow delete if user is sender or receiver
if ($msg['sender_id'] == $user['id']) {
    $stmt = $pdo->prepare('UPDATE messages SET deleted_by_sender=1 WHERE id=?');
    $stmt->execute([$msg_id]);
}
if ($msg['receiver_id'] == $user['id']) {
    $stmt = $pdo->prepare('UPDATE messages SET deleted_by_receiver=1 WHERE id=?');
    $stmt->execute([$msg_id]);
}

jsonResponse(['success'=>true]);
