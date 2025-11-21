<?php
require_once __DIR__ . '/../inc/functions.php';
$user = current_user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>RPSU Lost & Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between">
      <div class="text-lg font-semibold">RPSU Lost & Found</div>
      <div class="space-x-4">
        <?php if ($user): ?>
          <a class="hover:underline" href="dashboard.php">Dashboard</a>
          <a class="hover:underline" href="profile.php">Profile</a>
          <a class="hover:underline" href="chat/chat.php">Chat Rooms</a>
          <?php if ($user['is_admin']): ?>
            <a class="hover:underline" href="admin/admin.php">Admin</a>
          <?php endif; ?>
          <a class="hover:underline" href="auth/logout.php">Logout</a>
        <?php else: ?>
          <a class="hover:underline" href="auth/login.php">Login</a>
          <a class="hover:underline" href="auth/signup.php">Signup</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <main class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-6">
      <h1 class="text-2xl font-bold mb-2">Welcome to RPSU Lost & Found</h1>
      <p class="text-gray-700">If you lost or found an item on campus, post it here. Use your RPSU email to sign up.</p>
    </div>

    <section class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <?php
      // show recent posts
      $stmt = $pdo->query("SELECT p.*, u.name FROM posts p JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC LIMIT 9");
      $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($posts as $post):
      ?>
      <div class="bg-white rounded shadow p-4">
        <?php if ($post['image_path']): ?>
          <!-- changed object-cover -> object-contain, kept a fixed visual height so cards stay tidy -->
          <div class="bg-gray-100 rounded overflow-hidden">
            <img class="w-full h-48 object-contain" src="<?php echo h($post['image_path']); ?>" alt="image">
          </div>
        <?php endif; ?>
        <h3 class="mt-2 font-semibold"><?php echo h($post['title']); ?></h3>
        <p class="text-sm text-gray-600"><?php echo h(substr($post['description'], 0, 120)); ?>...</p>
        <div class="mt-2 text-xs text-gray-500">Posted by <?php echo h($post['name']); ?> • <?php echo h($post['location']); ?></div>
        <a class="inline-block mt-2 text-blue-600 hover:underline" href="post_view.php?id=<?php echo $post['id']; ?>">View</a>
      </div>
      <?php endforeach; ?>
    </section>
  </main>
</body>
</html>