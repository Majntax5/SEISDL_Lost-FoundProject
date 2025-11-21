<?php
require_once __DIR__ . '/../inc/functions.php';
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, u.name, u.email FROM posts p JOIN users u ON u.id = p.user_id WHERE p.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    http_response_code(404);
    echo "Post not found";
    exit;
}

$user = current_user();

// handle sending a message tied to this post
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message']) && $user) {
    $content = trim($_POST['content'] ?? '');
    if (!$content) $errors[] = 'Message cannot be empty';
    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO post_messages (post_id, sender_id, receiver_id, content) VALUES (?, ?, ?, ?)");
        // receiver is post owner
        $stmt->execute([$post['id'], $user['id'], $post['user_id'], $content]);
        header('Location: post_view.php?id=' . $post['id']);
        exit;
    }
}

// fetch messages for this post
$ms = $pdo->prepare("SELECT pm.*, u.name FROM post_messages pm JOIN users u ON u.id = pm.sender_id WHERE pm.post_id = ? ORDER BY pm.created_at ASC");
$ms->execute([$post['id']]);
$messages = $ms->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title><?php echo h($post['title']); ?> - RPSU Lost & Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-600 text-white p-4">
  <div class="container mx-auto flex justify-between">
    <div>RPSU Lost & Found</div>
    <div>
      <a class="mr-4" href="index.php">Home</a>
      <a class="mr-4" href="dashboard.php">Dashboard</a>
      <?php if ($user): ?><a href="auth/logout.php">Logout</a><?php endif; ?>
    </div>
  </div>
</nav>

<main class="container mx-auto p-6">
  <div class="bg-white rounded shadow p-6">
    <div class="grid md:grid-cols-3 gap-6">
      <div class="md:col-span-2">
        <h2 class="text-2xl font-bold"><?php echo h($post['title']); ?></h2>
        <div class="text-sm text-gray-600">Posted by <?php echo h($post['name']); ?> • <?php echo h($post['location']); ?> • <?php echo h($post['status']); ?></div>
        <?php if ($post['image_path']): ?>
          <!-- show full image without cropping; allow it to grow but cap max height -->
          <div class="bg-gray-100 rounded overflow-hidden mt-4">
            <img class="w-full object-contain max-h-96" src="<?php echo h($post['image_path']); ?>" alt="image">
          </div>
        <?php endif; ?>
        <p class="mt-4"><?php echo nl2br(h($post['description'])); ?></p>
      </div>

      <div class="md:col-span-1">
        <div class="p-4 border rounded">
          <h3 class="font-semibold">Contact</h3>
          <div class="text-sm text-gray-700">Owner: <?php echo h($post['name']); ?></div>
          <div class="text-xs text-gray-500">Email: <?php echo h($post['email']); ?></div>
          <hr class="my-3">
          <?php if ($user): ?>
            <form method="post">
              <label class="block mb-2">Send a message to owner<textarea name="content" class="w-full p-2 border rounded" rows="4" required></textarea></label>
              <button name="send_message" class="w-full bg-blue-600 text-white p-2 rounded">Send</button>
            </form>
          <?php else: ?>
            <div class="text-sm">Please <a class="text-blue-600" href="auth/login.php">login</a> to message.</div>
          <?php endif; ?>
        </div>

        <div class="mt-4 p-4 border rounded">
          <h4 class="font-semibold mb-2">Messages</h4>
          <?php foreach ($messages as $m): ?>
            <div class="mb-2">
              <div class="text-xs text-gray-500"><?php echo h($m['name']); ?> • <?php echo h($m['created_at']); ?></div>
              <div class="bg-gray-100 p-2 rounded"><?php echo nl2br(h($m['content'])); ?></div>
            </div>
          <?php endforeach; ?>
        </div>

      </div>
    </div>
  </div>
</main>
</body>
</html>