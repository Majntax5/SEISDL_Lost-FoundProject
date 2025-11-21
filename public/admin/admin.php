<?php
require_once __DIR__ . '/../../inc/functions.php';
require_login();
$user = current_user();
if (!$user['is_admin']) { header('HTTP/1.1 403 Forbidden'); echo "Access denied"; exit; }

// simple list posts with edit form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $status = in_array($_POST['status'], ['lost','found','claimed']) ? $_POST['status'] : 'lost';
    $stmt = $pdo->prepare("UPDATE posts SET title=?, description=?, status=? WHERE id=?");
    $stmt->execute([$title,$desc,$status,$id]);
    header('Location: admin.php'); exit;
}

$posts = $pdo->query("SELECT p.*, u.name FROM posts p JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Admin - RPSU Lost & Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-600 text-white p-4">
  <div class="container mx-auto flex justify-between"><div>Admin Panel</div><div><a href="../dashboard.php">Back</a></div></div>
</nav>

<main class="container mx-auto p-6">
  <div class="bg-white p-4 rounded shadow">
    <h2 class="font-bold mb-3">All Posts</h2>
    <?php foreach ($posts as $p): ?>
      <div class="border-b py-3">
        <div class="flex justify-between">
          <div>
            <div class="font-medium"><?php echo h($p['title']); ?> <span class="text-xs text-gray-500">by <?php echo h($p['name']); ?></span></div>
            <div class="text-xs text-gray-600"><?php echo h($p['location']); ?> • <?php echo h($p['status']); ?></div>
          </div>
          <div class="flex items-center gap-2">
            <a class="text-blue-600" href="../post_view.php?id=<?php echo $p['id']; ?>">View</a>
            <form method="post" class="flex items-center gap-2">
              <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
              <input type="text" name="title" value="<?php echo h($p['title']); ?>" class="border p-1 rounded">
              <select name="status" class="border p-1 rounded">
                <option <?php if($p['status']=='lost') echo 'selected'; ?> value="lost">lost</option>
                <option <?php if($p['status']=='found') echo 'selected'; ?> value="found">found</option>
                <option <?php if($p['status']=='claimed') echo 'selected'; ?> value="claimed">claimed</option>
              </select>
              <button name="update_post" class="bg-yellow-500 text-white px-2 py-1 rounded">Save</button>
            </form>
            <a class="text-red-600 ml-2" href="../api/posts.php?action=delete&id=<?php echo $p['id']; ?>">Delete</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>
</body>
</html>