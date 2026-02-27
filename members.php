<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['username'])) { echo '<p class="text-danger p-3">Not authenticated.</p>'; exit(); }
include_once 'partials/_dbconnect2.php';

$admin_name = $_SESSION['username'];
$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'add_member') {
        $name    = trim($_POST['name'] ?? '');
        $number  = trim($_POST['number'] ?? '');
        $type    = trim($_POST['type'] ?? 'General');
        $address = trim($_POST['address'] ?? '');
        if ($name !== '') {
            $stmt = $conn->prepare("INSERT INTO member (name, number, type, address, amendedby) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $number, $type, $address, $admin_name);
            $message = $stmt->execute() ? "Member \"".htmlspecialchars($name)."\" added successfully." : "Failed: ".$stmt->error;
            $stmt->close();
        } else { $error = "Name is required."; }
    }

    elseif ($_POST['action'] === 'edit_member') {
        $id      = (int)($_POST['edit_id'] ?? 0);
        $name    = trim($_POST['edit_name'] ?? '');
        $number  = trim($_POST['edit_number'] ?? '');
        $type    = trim($_POST['edit_type'] ?? 'General');
        $address = trim($_POST['edit_address'] ?? '');
        if ($id > 0 && $name !== '') {
            $stmt = $conn->prepare("UPDATE member SET name=?, number=?, type=?, address=?, amendedby=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $number, $type, $address, $admin_name, $id);
            $message = $stmt->execute() ? "Member updated successfully." : "Failed: ".$stmt->error;
            $stmt->close();
        } else { $error = "Invalid data."; }
    }

    elseif ($_POST['action'] === 'delete_member') {
        $id = (int)($_POST['del_id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM member WHERE id=?");
            $stmt->bind_param("i", $id);
            $message = $stmt->execute() ? "Member deleted." : "Delete failed: ".$stmt->error;
            $stmt->close();
        }
    }
}

$search  = trim($_GET['q'] ?? '');
$edit_id = (int)($_GET['edit'] ?? 0);

if ($search !== '') {
    $like = "%$search%";
    $stmt = $conn->prepare("SELECT * FROM member WHERE name LIKE ? OR number LIKE ? ORDER BY name ASC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $members_result = $stmt->get_result();
    $stmt->close();
} else {
    $members_result = $conn->query("SELECT * FROM member ORDER BY name ASC");
}

$members = [];
if ($members_result) { while ($r = $members_result->fetch_assoc()) $members[] = $r; }

$edit_member = null;
if ($edit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM member WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_member = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$total_count     = count($members);
$general_count   = 0;
$permanent_count = 0;
foreach ($members as $m) {
    $t = strtolower(trim($m['type'] ?? ''));
    if ($t === 'permanent') $permanent_count++;
    else $general_count++; // empty or 'general' both count as general
}
?>
<style>
.mem-wrap { padding: 4px 0 40px; }
.mem-page-header {
  background: linear-gradient(135deg,#192F59,#213F7A); color:#fff;
  border-radius:12px; padding:18px 22px; margin-bottom:16px;
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
}
.mem-page-header h4 { margin:0; font-size:1.2rem; font-weight:700; }
.mem-page-header small { opacity:.75; font-size:.8rem; display:block; margin-top:2px; }

.mem-stats { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.mem-stat { flex:1; min-width:110px; background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.07); padding:12px 16px; display:flex; align-items:center; gap:10px; }
.mem-stat-icon { width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:1rem; color:#fff; flex-shrink:0; }
.mem-stat-icon.blue   { background:#192F59; }
.mem-stat-icon.green  { background:#10b981; }
.mem-stat-icon.purple { background:#8b5cf6; }
.mem-stat-num   { font-size:1.2rem; font-weight:700; color:#111; line-height:1; }
.mem-stat-label { font-size:.72rem; color:#6b7280; margin-top:2px; }

.mem-alert { border-radius:10px; padding:11px 15px; font-size:.88rem; font-weight:500; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
.mem-alert.success { background:#d1fae5; color:#065f46; border-left:4px solid #10b981; }
.mem-alert.danger  { background:#fee2e2; color:#991b1b; border-left:4px solid #ef4444; }

.mem-card { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08); margin-bottom:16px; overflow:hidden; }
.mem-card-head { background:#192F59; color:#fff; padding:13px 18px; font-weight:600; font-size:.92rem; display:flex; align-items:center; gap:8px; }
.mem-card-head.edit-head { background:#1a5c3a; }
.mem-card-body { padding:18px; }

.mem-form-row { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:14px; }
.mem-form-group { flex:1; min-width:160px; display:flex; flex-direction:column; }
.mem-form-group label { font-weight:600; font-size:.8rem; color:#374151; margin-bottom:4px; }
.mem-form-group input,
.mem-form-group select {
  border:1.5px solid #d0d7e3; border-radius:8px; padding:8px 12px;
  font-size:.88rem; outline:none; transition:border-color .2s; background:#fff;
}
.mem-form-group input:focus,
.mem-form-group select:focus { border-color:#192F59; box-shadow:0 0 0 3px rgba(25,47,89,.12); }

.mem-btn-primary { background:#192F59; color:#fff; border:none; border-radius:8px; padding:9px 18px; font-size:.87rem; font-weight:600; cursor:pointer; transition:background .2s; }
.mem-btn-primary:hover { background:#213F7A; }
.mem-btn-success { background:#1a5c3a; color:#fff; border:none; border-radius:8px; padding:9px 18px; font-size:.87rem; font-weight:600; cursor:pointer; transition:background .2s; }
.mem-btn-success:hover { background:#145230; }
.mem-btn-cancel  { background:#6c757d; color:#fff; border:none; border-radius:8px; padding:9px 18px; font-size:.87rem; font-weight:600; cursor:pointer; }
.mem-btn-cancel:hover  { background:#5a6268; }
.mem-btn-edit   { background:#213F7A; color:#fff; border:none; border-radius:6px; padding:5px 11px; font-size:.78rem; font-weight:600; cursor:pointer; white-space:nowrap; }
.mem-btn-edit:hover   { background:#192F59; }
.mem-btn-delete { background:#dc3545; color:#fff; border:none; border-radius:6px; padding:5px 11px; font-size:.78rem; font-weight:600; cursor:pointer; white-space:nowrap; }
.mem-btn-delete:hover { background:#b02a37; }
.mem-btns-row { display:flex; gap:8px; flex-wrap:wrap; margin-top:4px; }

.mem-search { background:#fff; border-radius:10px; box-shadow:0 1px 6px rgba(0,0,0,.07); padding:12px 16px; margin-bottom:14px; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.mem-search input { flex:1; min-width:140px; border:1.5px solid #d0d7e3; border-radius:8px; padding:7px 12px; font-size:.88rem; outline:none; }
.mem-search input:focus { border-color:#192F59; }
.mem-search-count { font-size:.8rem; color:#6b7280; white-space:nowrap; }

.mem-table-wrap { overflow-x:auto; }
.mem-table { width:100%; border-collapse:collapse; font-size:.875rem; min-width:560px; }
.mem-table thead { background:#192F59; color:#fff; }
.mem-table thead th { padding:12px 13px; font-weight:600; white-space:nowrap; }
.mem-table tbody tr { border-bottom:1px solid #e8ecf1; transition:background .15s; }
.mem-table tbody tr:hover { background:#f5f7ff; }
.mem-table tbody td { padding:10px 13px; vertical-align:middle; color:#374151; }
.mem-name { font-weight:600; color:#192F59; }
.mem-by-badge { display:inline-block; font-size:.68rem; color:#6b7280; background:#eef2ff; border-radius:20px; padding:2px 7px; margin-left:5px; white-space:nowrap; }
.mem-type-general   { display:inline-block; background:#e0e7ff; color:#3730a3; border-radius:20px; padding:2px 9px; font-size:.73rem; font-weight:600; }
.mem-type-permanent { display:inline-block; background:#d1fae5; color:#065f46; border-radius:20px; padding:2px 9px; font-size:.73rem; font-weight:600; }
.mem-action-cell { display:flex; gap:6px; flex-wrap:wrap; }
.mem-empty { text-align:center; padding:36px 20px; color:#9ca3af; font-size:.9rem; }

@media (max-width:767px) {
  .mem-desktop { display:none !important; }
  .mem-mobile  { display:block !important; }
  .mem-form-group { min-width:100% !important; }
  .mem-page-header h4 { font-size:1rem; }
  .mem-stat-num { font-size:1rem; }
}
@media (min-width:768px) {
  .mem-desktop { display:block !important; }
  .mem-mobile  { display:none !important; }
}

.mem-mobile-card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.08); margin-bottom:11px; overflow:hidden; }
.mem-mobile-card-head { background:linear-gradient(90deg,#192F59,#213F7A); color:#fff; padding:11px 13px; display:flex; align-items:center; justify-content:space-between; }
.mem-mobile-card-head .mname { font-weight:700; font-size:.92rem; }
.mem-mobile-card-head .mby   { font-size:.7rem; opacity:.75; margin-top:2px; }
.mem-mobile-card-body { padding:10px 13px; }
.mem-mobile-row { display:flex; justify-content:space-between; align-items:flex-start; padding:5px 0; border-bottom:1px solid #f3f4f6; font-size:.83rem; }
.mem-mobile-row:last-child { border-bottom:none; }
.mem-mobile-row .ml { color:#6b7280; font-weight:600; flex-shrink:0; margin-right:8px; }
.mem-mobile-row .mv { text-align:right; word-break:break-word; max-width:200px; }
.mem-mobile-card-foot { padding:9px 13px; background:#f9fafb; display:flex; gap:8px; }
.mem-mobile-card-foot .mem-btn-edit,
.mem-mobile-card-foot .mem-btn-delete { flex:1; text-align:center; padding:8px 4px; font-size:.82rem; }
</style>

<div class="mem-wrap">

  <div class="mem-page-header">
    <div>
      <h4>&#128101; Member Management</h4>
      <small>Add, edit, and delete member records</small>
    </div>
  </div>

  <?php if ($message): ?>
    <div class="mem-alert success" id="memAlert">&#10003; <?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="mem-alert danger" id="memAlert">&#9888; <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="mem-stats">
    <div class="mem-stat">
      <div class="mem-stat-icon blue">&#128101;</div>
      <div><div class="mem-stat-num"><?= $total_count ?></div><div class="mem-stat-label">Total Members</div></div>
    </div>
    <div class="mem-stat">
      <div class="mem-stat-icon purple">&#128100;</div>
      <div><div class="mem-stat-num"><?= $general_count ?></div><div class="mem-stat-label">General Members</div></div>
    </div>
    <div class="mem-stat">
      <div class="mem-stat-icon green">&#11088;</div>
      <div><div class="mem-stat-num"><?= $permanent_count ?></div><div class="mem-stat-label">Permanent Members</div></div>
    </div>
  </div>

  <!-- Add / Edit Form -->
  <div class="mem-card">
    <div class="mem-card-head <?= $edit_member ? 'edit-head' : '' ?>">
      <?= $edit_member ? '&#9998; Editing: ' . htmlspecialchars($edit_member['name']) : '&#43; Add New Member' ?>
    </div>
    <div class="mem-card-body">

      <?php if ($edit_member): ?>
        <form id="memEditForm" method="POST">
          <input type="hidden" name="action" value="edit_member">
          <input type="hidden" name="edit_id" value="<?= $edit_member['id'] ?>">
          <div class="mem-form-row">
            <div class="mem-form-group" style="min-width:200px;">
              <label>Full Name *</label>
              <input type="text" name="edit_name" value="<?= htmlspecialchars($edit_member['name']) ?>" required placeholder="Full name">
            </div>
            <div class="mem-form-group" style="min-width:150px;">
              <label>Phone Number</label>
              <input type="text" name="edit_number" value="<?= htmlspecialchars($edit_member['number'] ?? '') ?>" placeholder="01700000000">
            </div>
            <div class="mem-form-group" style="min-width:140px;">
              <label>Member Type</label>
              <select name="edit_type">
                <option value="General"   <?= (strtolower(trim($edit_member['type'] ?? '')) !== 'permanent') ? 'selected' : '' ?>>General</option>
                <option value="Permanent" <?= (strtolower(trim($edit_member['type'] ?? '')) === 'permanent') ? 'selected' : '' ?>>Permanent</option>
              </select>
            </div>
            <div class="mem-form-group" style="min-width:200px;">
              <label>Address</label>
              <input type="text" name="edit_address" value="<?= htmlspecialchars($edit_member['address'] ?? '') ?>" placeholder="Enter address">
            </div>
          </div>
          <div class="mem-btns-row">
            <button type="submit" class="mem-btn-success">&#10003; Save Changes</button>
            <button type="button" class="mem-btn-cancel" onclick="memLoad('members.php')">&#215; Cancel</button>
          </div>
        </form>

      <?php else: ?>
        <form id="memAddForm" method="POST">
          <input type="hidden" name="action" value="add_member">
          <div class="mem-form-row">
            <div class="mem-form-group" style="min-width:200px;">
              <label>Full Name *</label>
              <input type="text" name="name" required placeholder="Enter full name">
            </div>
            <div class="mem-form-group" style="min-width:150px;">
              <label>Phone Number</label>
              <input type="text" name="number" placeholder="01700000000">
            </div>
            <div class="mem-form-group" style="min-width:140px;">
              <label>Member Type</label>
              <select name="type">
                <option value="General" selected>General</option>
                <option value="Permanent">Permanent</option>
              </select>
            </div>
            <div class="mem-form-group" style="min-width:200px;">
              <label>Address</label>
              <input type="text" name="address" placeholder="Enter address">
            </div>
          </div>
          <div class="mem-btns-row">
            <button type="submit" class="mem-btn-primary">&#43; Add Member</button>
          </div>
        </form>
      <?php endif; ?>

    </div>
  </div>

  <!-- Search -->
  <div class="mem-search">
    <span style="color:#192F59;">&#128269;</span>
    <input type="text" id="memSearchInput" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name or phone…">
    <button type="button" class="mem-btn-primary" style="padding:7px 14px;" onclick="memSearch()">Search</button>
    <?php if ($search): ?>
      <button type="button" class="mem-btn-cancel" style="padding:7px 12px;" onclick="memLoad('members.php')">Clear</button>
    <?php endif; ?>
    <span class="mem-search-count"><?= $total_count ?> result<?= $total_count !== 1 ? 's' : '' ?></span>
  </div>

  <!-- Desktop Table -->
  <div class="mem-card mem-desktop">
    <div class="mem-card-head">&#128203; All Members</div>
    <div class="mem-table-wrap">
      <?php if (empty($members)): ?>
        <div class="mem-empty">No members found<?= $search ? ' for &ldquo;'.htmlspecialchars($search).'&rdquo;' : '' ?>.</div>
      <?php else: ?>
        <table class="mem-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Phone</th>
              <th>Type</th>
              <th>Address</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($members as $m): ?>
            <?php $isPermanent = strtolower(trim($m['type'] ?? '')) === 'permanent'; ?>
            <tr>
              <td style="color:#9ca3af;font-size:.78rem;"><?= $m['id'] ?></td>
              <td>
                <span class="mem-name"><?= htmlspecialchars($m['name']) ?></span>
                <?php if (!empty($m['amendedby'])): ?>
                  <span class="mem-by-badge">by <?= htmlspecialchars($m['amendedby']) ?></span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($m['number'] ?? '') ?: '&mdash;' ?></td>
              <td>
                <span class="<?= $isPermanent ? 'mem-type-permanent' : 'mem-type-general' ?>">
                  <?= $isPermanent ? 'Permanent' : 'General' ?>
                </span>
              </td>
              <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($m['address'] ?? '') ?>">
                <?= htmlspecialchars($m['address'] ?? '') ?: '&mdash;' ?>
              </td>
              <td>
                <div class="mem-action-cell">
                  <button class="mem-btn-edit"   onclick="memLoad('members.php?edit=<?= $m['id'] ?>')">&#9998; Edit</button>
                  <button class="mem-btn-delete" onclick="memDelete(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['name'])) ?>')">&#128465; Delete</button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- Mobile Cards -->
  <div class="mem-mobile">
    <?php if (empty($members)): ?>
      <div class="mem-empty" style="background:#fff;border-radius:10px;padding:36px 20px;">No members found<?= $search ? ' for &ldquo;'.htmlspecialchars($search).'&rdquo;' : '' ?>.</div>
    <?php else: ?>
      <?php foreach ($members as $m): ?>
        <?php $isPermanent = strtolower(trim($m['type'] ?? '')) === 'permanent'; ?>
        <div class="mem-mobile-card">
          <div class="mem-mobile-card-head">
            <div>
              <div class="mname"><?= htmlspecialchars($m['name']) ?></div>
              <?php if (!empty($m['amendedby'])): ?>
                <div class="mby">by <?= htmlspecialchars($m['amendedby']) ?></div>
              <?php endif; ?>
            </div>
            <span style="background:rgba(255,255,255,.2);color:#fff;border-radius:20px;padding:3px 9px;font-size:.72rem;font-weight:600;">
              <?= $isPermanent ? 'Permanent' : 'General' ?>
            </span>
          </div>
          <div class="mem-mobile-card-body">
            <div class="mem-mobile-row">
              <span class="ml">Phone</span>
              <span class="mv"><?= htmlspecialchars($m['number'] ?? '') ?: '&mdash;' ?></span>
            </div>
            <div class="mem-mobile-row">
              <span class="ml">Address</span>
              <span class="mv"><?= htmlspecialchars($m['address'] ?? '') ?: '&mdash;' ?></span>
            </div>
            <div class="mem-mobile-row">
              <span class="ml">ID</span>
              <span class="mv" style="color:#9ca3af;">#<?= $m['id'] ?></span>
            </div>
          </div>
          <div class="mem-mobile-card-foot">
            <button class="mem-btn-edit"   onclick="memLoad('members.php?edit=<?= $m['id'] ?>')">&#9998; Edit</button>
            <button class="mem-btn-delete" onclick="memDelete(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['name'])) ?>')">&#128465; Delete</button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div><!-- /mem-wrap -->

<!-- Hidden delete form -->
<form id="memDeleteForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="delete_member">
  <input type="hidden" name="del_id" id="memDeleteId">
</form>

<script>
// ── Core loader: fetch any members.php URL into #content ──
function memLoad(url) {
  var el = document.getElementById('content');
  if (!el) { window.location.href = url; return; }
  fetch(url)
    .then(function(r){ if(!r.ok) throw new Error('bad'); return r.text(); })
    .then(function(h){ el.innerHTML = h; })
    .catch(function(){ el.innerHTML = '<p class="text-danger p-3">Failed to load.</p>'; });
}

// ── Core poster: POST formData to members.php, replace #content ──
function memPost(fd) {
  var el = document.getElementById('content');
  if (!el) return;
  fetch('members.php', { method:'POST', body:fd })
    .then(function(r){ return r.text(); })
    .then(function(h){ el.innerHTML = h; })
    .catch(function(){ alert('Action failed. Please try again.'); });
}

// ── Add form ──
(function(){
  var f = document.getElementById('memAddForm');
  if (!f) return;
  f.addEventListener('submit', function(e){
    e.preventDefault();
    var n = f.querySelector('[name="name"]');
    if (n && n.value.trim() === '') { alert('Name is required.'); n.focus(); return; }
    memPost(new FormData(f));
  });
})();

// ── Edit form ──
(function(){
  var f = document.getElementById('memEditForm');
  if (!f) return;
  f.addEventListener('submit', function(e){
    e.preventDefault();
    var n = f.querySelector('[name="edit_name"]');
    if (n && n.value.trim() === '') { alert('Name is required.'); n.focus(); return; }
    memPost(new FormData(f));
  });
})();

// ── Delete ──
function memDelete(id, name) {
  if (!confirm('Delete "' + name + '"?\nThis cannot be undone.')) return;
  document.getElementById('memDeleteId').value = id;
  memPost(new FormData(document.getElementById('memDeleteForm')));
}

// ── Search ──
function memSearch() {
  var q = (document.getElementById('memSearchInput') || {}).value || '';
  memLoad('members.php' + (q.trim() ? '?q=' + encodeURIComponent(q.trim()) : ''));
}
(function(){
  var si = document.getElementById('memSearchInput');
  if (si) si.addEventListener('keydown', function(e){ if(e.key==='Enter'){e.preventDefault();memSearch();} });
})();

// ── Auto-dismiss alert ──
(function(){
  var a = document.getElementById('memAlert');
  if (!a) return;
  setTimeout(function(){
    a.style.transition='opacity .5s'; a.style.opacity='0';
    setTimeout(function(){ if(a.parentNode) a.parentNode.removeChild(a); }, 500);
  }, 4000);
})();
</script>
