<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard • RPSU Lost & Found</title>
  <style>
    :root {
      --header-bg: rgba(19, 30, 60, 0.95);
      --menu-bg: rgba(19, 30, 60, 0.98);
      --card-bg: rgba(34, 51, 88, 0.7);
      --text-light: #ffffff;
      --accent: #4da3ff;
    }

    html, body {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #0f1c3f 0%, #1a2a4f 100%);
      color: var(--text-light);
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 20px;
      background: var(--header-bg);
      color: var(--text-light);
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 6px rgba(0,0,0,0.4);
    }

    header h1 { margin: 0; font-size: 20px; }

    .desktop-links a {
      color: var(--text-light);
      text-decoration: none;
      margin-left: 20px;
      font-weight: 600;
    }
    .desktop-links a:hover { color: var(--accent); }

    .hamburger {
      display: none;
      flex-direction: column;
      cursor: pointer;
      gap: 4px;
    }
    .hamburger span {
      width: 25px;
      height: 3px;
      background: var(--text-light);
      border-radius: 2px;
    }

    .mobile-menu {
      display: none;
      flex-direction: column;
      position: absolute;
      top: 50px;
      right: 10px;
      background: var(--menu-bg);
      padding: 16px;
      border-radius: 8px;
      z-index: 101;
    }
    .mobile-menu a {
      color: var(--text-light);
      text-decoration: none;
      margin: 10px 0;
      padding: 8px 12px;
      font-size: 16px;
      font-weight: 600;
    }

    .container {
      display: grid;
      grid-template-columns: 240px 1fr;
      gap: 20px;
      padding: 20px;
    }

    .sidebar {
      background: var(--card-bg);
      backdrop-filter: blur(6px);
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.4);
      padding: 16px;
      height: fit-content;
      position: sticky;
      top: 82px;
    }
    .sidebar h2 {
      margin: 0 0 12px 0;
      font-size: 18px;
      color: var(--accent);
    }
    .sidebar a {
      display: block;
      padding: 10px 12px;
      border-radius: 8px;
      color: var(--text-light);
      text-decoration: none;
      font-weight: 600;
      margin-bottom: 6px;
      background: rgba(255, 255, 255, 0.05);
      transition: background 0.2s;
    }
    .sidebar a:hover {
      background: rgba(255, 255, 255, 0.12);
    }

    .content { min-width: 0; }
    .card {
      background: var(--card-bg);
      backdrop-filter: blur(6px);
      padding: 16px;
      border-radius: 12px;
      margin-bottom: 16px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.4);
    }
    .card h3 {
      margin: 0 0 12px 0;
      font-size: 20px;
      color: var(--accent);
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(4, minmax(120px, 1fr));
      gap: 12px;
      margin-bottom: 16px;
    }
    .stat {
      background: rgba(255,255,255,0.08);
      border-radius: 10px;
      padding: 12px;
      text-align: center;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.05), 0 1px 4px rgba(0,0,0,0.4);
    }
    .stat .num { font-size: 22px; font-weight: 700; color: #fff; }
    .stat .label { font-size: 12px; color: #ccc; }

    table {
      width: 100%;
      border-collapse: collapse;
    }
    thead th {
      text-align: left;
      background: rgba(255,255,255,0.1);
      color: #fff;
      padding: 10px;
      font-size: 14px;
    }
    tbody td {
      background: rgba(255,255,255,0.05);
      padding: 10px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      vertical-align: top;
      font-size: 14px;
    }

    .btn {
      border: none;
      border-radius: 6px;
      padding: 6px 10px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      color: #fff;
      margin-right: 6px;
    }
    .btn-view { background: #4da3ff; }
    .btn-edit { background: #2ecc71; }
    .btn-del  { background: #e74c3c; }

    .status-select {
      padding: 6px 8px;
      border-radius: 6px;
      border: 1px solid rgba(255,255,255,0.3);
      background: rgba(255,255,255,0.1);
      color: #fff;
      font-size: 13px;
    }

    .dark-overlay {
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.7);
      display: none;
      z-index: 1000;
    }
    .dark-overlay.active { display: block; }

    .side-panel {
      position: fixed;
      top: 0; right: -100%;
      width: 90%; max-width: 420px; height: 100%;
      background: rgba(19,30,60,0.98);
      color: #fff;
      padding: 20px;
      box-shadow: -4px 0 10px rgba(0,0,0,0.6);
      transition: right 0.3s ease;
      z-index: 1001;
      overflow-y: auto;
    }
    .side-panel.active { right: 0; }
    .side-panel img {
      width: 100%; border-radius: 8px; margin-bottom: 10px;
      background: #222;
    }
    .side-panel button {
      margin-top: 10px; padding: 8px 12px;
      border: none; border-radius: 6px;
      background: #e74c3c; color: #fff; cursor: pointer;
    }

    @media (max-width: 900px) {
      .container { grid-template-columns: 1fr; }
      .sidebar { position: static; top: auto; }
    }
    @media (max-width: 768px) {
      .desktop-links { display: none; }
      .hamburger { display: flex; }
      .stats { grid-template-columns: repeat(2, 1fr); }
    }
  </style>
</head>
<body>
  <!-- Header / Navbar -->
  <header>
    <h1>Admin Dashboard</h1>
    <div class="desktop-links">
      <a href="Main.html">Home</a>
      <a href="Allpost.html">Issues</a>
      <a href="report.html">Report</a>
      <a href="index.html" onclick="logout()">Logout</a>
    </div>

    <div class="hamburger" onclick="toggleMenu()">
      <span></span><span></span><span></span>
    </div>
    <div class="mobile-menu" id="mobileMenu">
      <a href="Main.html">Home</a>
      <a href="Allpost.html">Issues</a>
      <a href="report.html">Report</a>
      <a href="index.html" onclick="logout()">Logout</a>
    </div>
  </header>

  <!-- Main layout -->
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Menu</h2>
      <a href="#" onclick="filterStatus('all')">📌 All Issues</a>
      <a href="#" onclick="filterStatus('Pending')">⏳ Pending</a>
      <a href="#" onclick="filterStatus('Approved')">✅ Approved</a>
      <a href="#" onclick="filterStatus('Resolved')">✔ Resolved</a>
      <a href="#" onclick="filterStatus('Rejected')">❌ Rejected</a>
    </aside>

    <!-- Content -->
    <section class="content">
      <!-- Quick stats -->
      <div class="stats">
        <div class="stat">
          <div class="num" id="countAll">0</div>
          <div class="label">Total Issues</div>
        </div>
        <div class="stat">
          <div class="num" id="countPending">0</div>
          <div class="label">Pending</div>
        </div>
        <div class="stat">
          <div class="num" id="countApproved">0</div>
          <div class="label">Approved</div>
        </div>
        <div class="stat">
          <div class="num" id="countResolved">0</div>
          <div class="label">Resolved</div>
        </div>
      </div>

      <!-- Issues table -->
      <div class="card">
        <h3>Manage Issues</h3>
        <table>
          <thead>
            <tr>
              <th style="width:70px;">ID</th>
              <th style="width:140px;">Reported By</th>
              <th>Description</th>
              <th style="width:160px;">Status</th>
              <th style="width:220px;">Actions</th>
            </tr>
          </thead>
          <tbody id="issuesTableBody"></tbody>
        </table>
      </div>

      <!-- Notifications placeholder -->
      <div class="card">
        <h3>System Notifications</h3>
        <p>No new notifications at this moment.</p>
      </div>
    </section>
  </div>

  <!-- View Panel -->
  <div class="dark-overlay" id="darkOverlay" onclick="closePanel()"></div>
  <div class="side-panel" id="sidePanel">
    <img id="panelImage" alt="Issue Image">
    <h2 id="panelTitle"></h2>
    <p><b>Place:</b> <span id="panelPlace"></span></p>
    <p><b>Description:</b> <span id="panelDesc"></span></p>
    <p><i>Reported by: <span id="panelUser"></span></i></p>
    <p><i>Status: <span id="panelStatus"></span></i></p>
    <button onclick="closePanel()">⬅ Back</button>
  </div>

  <script>
async function fetchIssues() {
  try {
    const res = await fetch('fetch_issues.php');
    const issues = await res.json();
    renderTable(issues);
  } catch (err) {
    console.error("Error fetching issues:", err);
  }
}

function renderTable(issues) {
  const tbody = document.getElementById("issuesTableBody");
  tbody.innerHTML = "";

  issues.forEach(issue => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>#${issue.id}</td>
      <td>${issue.username}</td>
      <td>
        <b>${issue.title}</b><br>
        <small><b>Place:</b> ${issue.place}</small><br>
        <small>${issue.description}</small>
      </td>
      <td>
        <select class="status-select" onchange="updateStatus(${issue.id}, this.value)">
          ${["Pending","Approved","Resolved","Rejected"].map(s => `<option value="${s}" ${issue.status===s?"selected":""}>${s}</option>`).join("")}
        </select>
      </td>
      <td>
        <button class="btn btn-view" onclick='viewIssue(${JSON.stringify(issue)})'>View</button>
        <button class="btn btn-del" onclick="deleteIssue(${issue.id})">Delete</button>
      </td>
    `;
    tbody.appendChild(tr);
  });

  updateStats(issues);
}

function updateStats(issues) {
  document.getElementById("countAll").innerText = issues.length;
  document.getElementById("countPending").innerText = issues.filter(i => i.status==="Pending").length;
  document.getElementById("countApproved").innerText = issues.filter(i => i.status==="Approved").length;
  document.getElementById("countResolved").innerText = issues.filter(i => i.status==="Resolved").length;
}

function viewIssue(issue) {
  document.getElementById("panelImage").src = issue.image_path || "images/noimage.jpg";
  document.getElementById("panelTitle").innerText = issue.title;
  document.getElementById("panelPlace").innerText = issue.place;
  document.getElementById("panelDesc").innerText = issue.description;
  document.getElementById("panelUser").innerText = issue.username;
  document.getElementById("panelStatus").innerText = issue.status;
  document.getElementById("darkOverlay").classList.add("active");
  document.getElementById("sidePanel").classList.add("active");
}

function closePanel() {
  document.getElementById("darkOverlay").classList.remove("active");
  document.getElementById("sidePanel").classList.remove("active");
}

async function updateStatus(id, status) {
  await fetch("update_status.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `id=${id}&status=${status}`
  });
  fetchIssues(); // reload updated data
}

async function deleteIssue(id) {
  if (!confirm("Delete this issue?")) return;
  await fetch("delete_issue.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `id=${id}`
  });
  fetchIssues();
}

fetchIssues();
</script>

</body>
</html>
