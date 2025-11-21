<?php
require_once __DIR__ . '/config.php';

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function current_user() {
    global $pdo;
    if (!is_logged_in()) return null;
    static $user = null;
    if ($user === null) {
        $stmt = $pdo->prepare("SELECT id, name, email, phone, is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    return $user;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . 'public/auth/login.php');
        exit;
    }
}

function validate_rpsu_email($email) {
    return (bool)preg_match('/^[A-Za-z0-9._%+-]+@rpsu\\.edu\\.bd$/i', $email);
}

/**
 * Upload image securely and return a public URL (UPLOAD_URL + filename) on success,
 * or null on failure.
 */
function upload_image($file) {
    // no file uploaded
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) return null;

    // ensure upload dir exists and is writable
    if (!is_dir(UPLOAD_DIR)) {
        if (!mkdir(UPLOAD_DIR, 0755, true)) return null;
    }

    // size limit: 5 MB
    $maxBytes = 5 * 1024 * 1024;
    if ($file['size'] > $maxBytes) return null;

    // reliable mime check
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];
    if (!isset($allowed[$mime])) return null;

    $ext = $allowed[$mime];
    $fname = uniqid('img_', true) . '.' . $ext;
    $dest = rtrim(UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fname;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }

    // make sure UPLOAD_URL ends with a slash
    $uploadUrl = rtrim(UPLOAD_URL, '/') . '/';

    // return public URL (e.g. /uploads/img_....jpg)
    return $uploadUrl . $fname;
}

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}