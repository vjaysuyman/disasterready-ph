<?php
// resident/my_reports.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_login(); // Siguroha nga naka-login

$userId = $_SESSION['user_id'];
$success = $_GET['success'] ?? '';

// Pag-handle sa Delete Request (Kung naay mo-click sa Delete button)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $deleteId = (int)$_POST['delete_id'];

    // Siguroha nga ang report kay iya sa residente bag-o i-delete (Security)
    $stmt = $pdo->prepare("DELETE FROM emergency_reports WHERE id = ? AND user_id = ?");
    $stmt->execute([$deleteId, $userId]);

    header('Location: my_reports.php?success=deleted');
    exit;
}

// Kuhaon ang tanang reports sa residente
$stmt = $pdo->prepare("SELECT * FROM emergency_reports WHERE user_id = ? ORDER BY reported_at DESC");
$stmt->execute([$userId]);
$reports = $stmt->fetchAll();

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>My Emergency Reports</h2>
    <a href="report_create.php" class="btn-primary" style="width: auto; margin-top: 0;">+ Submit New Report</a>
</div>

<?php if ($success === 'created'): ?>
    <div class="alert alert-success">Report submitted successfully!</div>
<?php elseif ($success === 'updated'): ?>
    <div class="alert alert-success">Report updated successfully!</div>
<?php elseif ($success === 'deleted'): ?>
    <div class="alert alert-success">Report deleted successfully!</div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Location</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No reports found. Click "Submit New Report" to create one.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reports as $report): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($report['title']) ?></strong></td>
                    <td><?= htmlspecialchars($report['category']) ?></td>
                    <td><?= htmlspecialchars($report['location']) ?></td>
                    <td>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '', $report['status'])) ?>">
                            <?= htmlspecialchars($report['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y h:i A', strtotime($report['reported_at'])) ?></td>
                    <td>
                        <!-- Edit Link -->
                        <a href="report_edit.php?id=<?= $report['id'] ?>" class="btn-sm btn-edit">Edit</a>
                        
                        <!-- Delete Form (Naa sa sulod sa table) -->
                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this report?');">
                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                            <input type="hidden" name="delete_id" value="<?= $report['id'] ?>">
                            <button type="submit" class="btn-sm btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>