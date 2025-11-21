<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    $sql = "INSERT INTO chats (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    $stmt->execute();
}

// display messages
$result = $conn->query("SELECT * FROM chats WHERE sender_id={$_SESSION['user_id']} OR receiver_id={$_SESSION['user_id']} ORDER BY sent_at ASC");
while ($row = $result->fetch_assoc()) {
    echo "<p><b>User {$row['sender_id']}:</b> {$row['message']}</p>";
}
?>
