<?php
require_once __DIR__ . '/../../inc/functions.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$name) $errors[] = 'Name required';
    if (!validate_rpsu_email($email)) $errors[] = 'You must use an @rpsu.edu.bd email';
    if (strlen($password) < 6) $errors[] = 'Password at least 6 chars';
    if ($password !== $password2) $errors[] = 'Passwords do not match';

    if (!$errors) {
        // check existing
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $hash]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header('Location: ' . BASE_URL . 'public/dashboard.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Signup - RPSU Lost & Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="background-image: url('../images/bg.jpg');background-size: cover;"  class="bg-gray-50 min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md">
    <div class="bg-white p-6 rounded shadow">
      <h2 class="text-xl font-bold mb-4">Create an account</h2>
      <?php if ($errors): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-3">
          <?php foreach ($errors as $e) echo "<div>" . h($e) . "</div>"; ?>
        </div>
      <?php endif; ?>
      <form method="post">
        <label class="block mb-2">Name<input name="name" class="w-full p-2 border rounded" required></label>
        <label class="block mb-2">RPSU Email<input name="email" type="email" class="w-full p-2 border rounded" required></label>
        <label class="block mb-2">Phone<input name="phone" class="w-full p-2 border rounded"></label>
        <label class="block mb-2">Password<input name="password" type="password" class="w-full p-2 border rounded" required></label>
        <label class="block mb-4">Confirm Password<input name="password2" type="password" class="w-full p-2 border rounded" required></label>
        <button class="w-full bg-blue-600 text-white p-2 rounded">Sign Up</button>
      </form>
      <div class="mt-3 text-center text-sm">
        Already have an account? <a class="text-blue-600" href="login.php">Login</a>
      </div>
    </div>
  </div>
</body>
</html>