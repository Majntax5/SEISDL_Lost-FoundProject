<?php
require_once __DIR__ . '/../../inc/functions.php';
require_login();
$user = current_user();

// load rooms
$rooms = $pdo->query("SELECT * FROM chat_rooms ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$selected_room = intval($_GET['room'] ?? ($rooms[0]['id'] ?? 0));

// messages (last 100)
$ms = $pdo->prepare("SELECT cm.*, u.name FROM chat_messages cm JOIN users u ON u.id = cm.user_id WHERE cm.room_id = ? ORDER BY cm.created_at ASC LIMIT 200");
$ms->execute([$selected_room]);
$messages = $ms->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Chat Rooms - RPSU Lost & Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // simple polling to refresh chat
    let roomId = <?php echo json_encode($selected_room); ?>;
    function fetchMessages(){
      fetch('chat_api.php?room=' + roomId)
        .then(r=>r.json())
        .then(data=>{
          const box = document.getElementById('messages');
          box.innerHTML = '';
          data.forEach(m=>{
            const div = document.createElement('div');
            div.className = 'mb-2';
            div.innerHTML = '<div class="text-xs text-gray-500">'+m.name+' • '+m.created_at+'</div>'
                          + '<div class="bg-gray-100 p-2 rounded">'+m.content+'</div>';
            box.appendChild(div);
          });
          box.scrollTop = box.scrollHeight;
        });
    }
    setInterval(fetchMessages, 3000);
    window.onload = function(){ fetchMessages(); }
    function sendMessage(e){
      e.preventDefault();
      const f = document.getElementById('sendForm');
      const data = new FormData(f);
      fetch('chat_api.php', { method:'POST', body:data }).then(()=>{ f.message.value=''; fetchMessages(); });
    }
    function changeRoom(id){
      window.location = '?room=' + id;
    }
  </script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-600 text-white p-4">
  <div class="container mx-auto flex justify-between">
    <div>Chat Rooms</div>
    <div><a href="../dashboard.php">Dashboard</a></div>
  </div>
</nav>

<main class="container mx-auto p-6">
  <div class="grid md:grid-cols-4 gap-4">
    <aside class="md:col-span-1 bg-white p-4 rounded shadow">
      <h3 class="font-semibold mb-3">Rooms</h3>
      <?php foreach ($rooms as $r): ?>
        <div class="mb-2">
          <button onclick="changeRoom(<?php echo $r['id']; ?>)" class="w-full text-left p-2 <?php echo $r['id']==$selected_room?'bg-blue-100':''; ?> rounded"><?php echo h($r['name']); ?></button>
        </div>
      <?php endforeach; ?>
      <form method="post" action="chat_api.php" class="mt-4">
        <input type="hidden" name="action" value="create_room">
        <input name="room_name" class="w-full p-2 border rounded" placeholder="New room name">
      </form>
    </aside>

    <section class="md:col-span-3">
      <div class="bg-white p-4 rounded shadow h-[60vh] flex flex-col">
        <div id="messages" class="flex-1 overflow-auto p-2">
          <?php foreach ($messages as $m): ?>
            <div class="mb-2">
              <div class="text-xs text-gray-500"><?php echo h($m['name']); ?> • <?php echo h($m['created_at']); ?></div>
              <div class="bg-gray-100 p-2 rounded"><?php echo nl2br(h($m['content'])); ?></div>
            </div>
          <?php endforeach; ?>
        </div>

        <form id="sendForm" onsubmit="sendMessage(event)" class="mt-3 flex gap-2">
          <input type="hidden" name="room_id" value="<?php echo $selected_room; ?>">
          <input name="message" class="flex-1 p-2 border rounded" placeholder="Write a message..." required>
          <button class="bg-blue-600 text-white p-2 rounded">Send</button>
        </form>
      </div>
    </section>
  </div>
</main>

</body>
</html>