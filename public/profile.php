<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
$user = current_user();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    if (!$name) $errors[] = 'Name required';
    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $user['id']]);
        header('Location: profile.php');
        exit;
    }
}
// reload user
$user = current_user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Profile - RPSU Lost & Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-600 text-white p-4">
  <div class="container mx-auto flex justify-between">
    <div>Profile</div>
    <div><a href="dashboard.php">Dashboard</a></div>
  </div>
</nav>

<main class="container mx-auto p-6">
  <div class="bg-white p-4 rounded shadow max-w-xl">
    <h2 class="font-semibold mb-3">Your Profile</h2>
    <?php if ($errors): ?><div class="bg-red-100 text-red-700 p-2 rounded mb-2"><?php foreach ($errors as $e) echo h($e); ?></div><?php endif; ?>
    <form method="post">
      <label class="block mb-2">Name<input name="name" value="<?php echo h($user['name']); ?>" class="w-full p-2 border rounded"></label>
      <label class="block mb-2">Email<input value="<?php echo h($user['email']); ?>" class="w-full p-2 border rounded bg-gray-100" disabled></label>
      <label class="block mb-4">Phone<input name="phone" value="<?php echo h($user['phone']); ?>" class="w-full p-2 border rounded"></label>
      <button class="bg-blue-600 text-white p-2 rounded">Save</button>
    </form>
  </div>
</main>
</body>
</html>