<?php
// admin/announcements.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_admin();

$success = '';

// CREATE ug UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_announcement'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=?, is_published=? WHERE id=?");
            $stmt->execute([$title, $content, $is_published, $id]);
            $success = "Announcement updated!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO announcements (admin_id, title, content, is_published) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $content, $is_published]);
            $success = "Announcement published!";
        }
    }
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $deleteId = (int)$_POST['delete_id'];
    $pdo->prepare("DELETE FROM announcements WHERE id = ?")->execute([$deleteId]);
    $success = "Announcement deleted.";
}

$announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll();

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Safety Announcements</h2>
    <button onclick="showForm()" class="btn-primary" style="width: auto; margin-top: 0;">+ Create Announcement</button>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Form -->
<div id="announcement-form" class="auth-box" style="display: none; max-width: 800px; margin: 0 0 2rem 0;">
    <h3 id="form-title" style="margin-bottom: 1.5rem;">Create Announcement</h3>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
        <input type="hidden" name="id" id="ann-id" value="">
        
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" id="ann-title" required>
        </div>
        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" id="ann-content" rows="5" required></textarea>
        </div>
        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="checkbox" name="is_published" id="ann-published" checked style="width: auto;">
            <label for="ann-published" style="margin: 0;">Publish this announcement to residents</label>
        </div>

        <button type="submit" name="save_announcement" class="btn-primary">Save Announcement</button>
        <button type="button" onclick="hideForm()" class="btn-secondary" style="margin-top: 0.5rem;">Cancel</button>
    </form>
</div>

<!-- List -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($announcements)): ?>
                <tr><td colspan="4" style="text-align: center;">No announcements yet.</td></tr>
            <?php else: ?>
                <?php foreach ($announcements as $a): ?>
                <tr>
                    <td><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
                    <td><strong><?= htmlspecialchars($a['title']) ?></strong></td>
                    <td>
                        <span class="status-badge <?= $a['is_published'] ? 'status-resolved' : 'status-pending' ?>">
                            <?= $a['is_published'] ? 'Published' : 'Draft' ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn-sm btn-edit" onclick="editAnnouncement(<?= htmlspecialchars(json_encode($a)) ?>)">Edit</button>
                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Delete announcement?');">
                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                            <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                            <button type="submit" class="btn-sm btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function showForm() {
    document.getElementById('announcement-form').style.display = 'block';
    document.getElementById('form-title').innerText = 'Create Announcement';
    document.getElementById('announcement-form').querySelector('form').reset();
    document.getElementById('ann-id').value = '';
}

function hideForm() {
    document.getElementById('announcement-form').style.display = 'none';
}

function editAnnouncement(ann) {
    document.getElementById('announcement-form').style.display = 'block';
    document.getElementById('form-title').innerText = 'Edit Announcement';
    document.getElementById('ann-id').value = ann.id;
    document.getElementById('ann-title').value = ann.title;
    document.getElementById('ann-content').value = ann.content;
    document.getElementById('ann-published').checked = ann.is_published == 1;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php include '../includes/footer.php'; ?>