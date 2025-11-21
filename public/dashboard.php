<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
$user = current_user();

// handle create post
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $loc = trim($_POST['location'] ?? '');
    $status = in_array($_POST['status'] ?? 'lost', ['lost','found']) ? $_POST['status'] : 'lost';
    $image = upload_image($_FILES['image'] ?? null);
    if (!$title) $errors[] = 'Title required';
    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, description, image_path, location, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user['id'], $title, $desc, $image, $loc, $status]);
        header('Location: ' . BASE_URL . 'public/dashboard.php');
        exit;
    }
}

// list user's posts
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$my_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// all posts for quick admin or browsing:
$all_stmt = $pdo->query("SELECT p.*, u.name FROM posts p JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC LIMIT 20");
$recent_posts = $all_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Dashboard - RPSU Lost & Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-600 text-white p-4">
  <div class="container mx-auto flex justify-between">
    <div class="font-semibold">Dashboard</div>
    <div>
      <a class="mr-4" href="index.php">Home</a>
      <a href="profile.php">Profile</a>
      <a class="ml-4" href="auth/logout.php">Logout</a>
    </div>
  </div>
</nav>

<main class="container mx-auto p-6">
  <div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-1 bg-white p-4 rounded shadow">
      <h3 class="font-bold mb-2">Create Post</h3>
      <?php if ($errors): ?>
        <div class="bg-red-100 text-red-700 p-2 rounded mb-2">
          <?php foreach ($errors as $e) echo "<div>" . h($e) . "</div>"; ?>
        </div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <label class="block mb-2">Title<input name="title" class="w-full p-2 border rounded" required></label>
        <label class="block mb-2">Description<textarea name="description" class="w-full p-2 border rounded"></textarea></label>
        <label class="block mb-2">Location<input name="location" class="w-full p-2 border rounded"></label>
        <label class="block mb-2">Status
          <select name="status" class="w-full p-2 border rounded">
            <option value="lost">Lost</option>
            <option value="found">Found</option>
          </select>
        </label>
        <label class="block mb-4">Image
          <input type="file" name="image" accept="image/*" id="imageInput" class="w-full p-2">
        </label>

        <!-- image preview (object-contain so preview shows whole image) -->
        <img id="imgPreview" class="hidden w-full max-h-48 object-contain rounded mt-2 bg-gray-100" src="" alt="Preview">

        <button name="create_post" class="w-full bg-green-600 text-white p-2 rounded mt-4">Post</button>
      </form>

      <script>
      document.getElementById('imageInput')?.addEventListener('change', function(e){
        const file = e.target.files[0];
        const img = document.getElementById('imgPreview');
        if (!file) { img.src = ''; img.classList.add('hidden'); return; }
        const reader = new FileReader();
        reader.onload = function(ev){
          img.src = ev.target.result;
          img.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      });
      </script>
    </div>

    <div class="md:col-span-2">
      <div class="bg-white p-4 rounded shadow mb-4">
        <h3 class="font-semibold">My Posts</h3>
        <?php foreach ($my_posts as $p): ?>
          <div class="border-b py-3">
            <div class="flex justify-between">
              <div>
                <a class="font-medium" href="post_view.php?id=<?php echo $p['id']; ?>"><?php echo h($p['title']); ?></a>
                <div class="text-xs text-gray-500"><?php echo h($p['status']); ?> • <?php echo h($p['location']); ?></div>
              </div>
              <div class="text-sm">
                <a class="text-blue-600 mr-2" href="post_view.php?id=<?php echo $p['id']; ?>">View</a>
                <a class="text-red-600" href="api/posts.php?action=delete&id=<?php echo $p['id']; ?>">Delete</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Recent Posts</h3>
        <div class="grid md:grid-cols-2 gap-3 mt-3">
          <?php foreach ($recent_posts as $rp): ?>
            <div class="border p-2 rounded">
              <?php if ($rp['image_path']): ?>
                <!-- thumbnail uses object-contain so whole image shows in the card -->
                <div class="bg-gray-100 rounded overflow-hidden">
                  <img class="w-full h-32 object-contain" src="<?php echo h($rp['image_path']); ?>">
                </div>
              <?php endif; ?>
              <div class="font-medium mt-1"><?php echo h($rp['title']); ?></div>
              <div class="text-xs text-gray-500"><?php echo h(substr($rp['description'],0,80)); ?></div>
              <div class="mt-2"><a class="text-blue-600" href="post_view.php?id=<?php echo $rp['id']; ?>">View</a></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</main>

</body>
</html>