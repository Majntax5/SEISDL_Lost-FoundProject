<?php
require_once __DIR__ . '/../../inc/functions.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($u && password_verify($password, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        header('Location: ' . BASE_URL . 'public/dashboard.php');
        exit;
    } else {
        $errors[] = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Login - RPSU Lost & Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="background-image: url('../images/bg.jpg'); background-size: cover;"  class="bg-gray-50 min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md">
    <div class="bg-white p-6 rounded shadow">
      <h2 class="text-xl font-bold mb-4">Login</h2>
      <?php if ($errors): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-3">
          <?php foreach ($errors as $e) echo "<div>" . h($e) . "</div>"; ?>
        </div>
      <?php endif; ?>
      <form method="post">
        <label class="block mb-2">Email<input name="email" type="email" class="w-full p-2 border rounded" required></label>
        <label class="block mb-4">Password<input name="password" type="password" class="w-full p-2 border rounded" required></label>
        <button class="w-full bg-blue-600 text-white p-2 rounded">Login</button>
      </form>
      <div class="mt-3 text-center text-sm">
        No account? <a class="text-blue-600" href="signup.php">Sign up</a>
      </div>
    </div>
  </div>
</body>
</html>