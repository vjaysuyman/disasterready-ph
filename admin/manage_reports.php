<?php
// admin/manage_reports.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_admin(); // Tanging admin lang ang makasulod

$success = '';

// Pag-handle sa Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $reportId = (int)$_POST['report_id'];
    $newStatus = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE emergency_reports SET status = ? WHERE id = ?");
    if ($stmt->execute([$newStatus, $reportId])) {
        $success = "Report status updated to $newStatus.";
    }
}

// Kuhaon ang tanang reports (Apil ang ngan sa residente pinaagi sa JOIN)
$sql = "SELECT r.*, u.full_name 
        FROM emergency_reports r 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.reported_at DESC";
$reports = $pdo->query($sql)->fetchAll();

include '../includes/header.php';
?>

<h2>Manage All Emergency Reports</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reporter</th>
                <th>Title & Details</th>
                <th>Location</th>
                <th>Current Status</th>
                <th>Action (Update Status)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No reports submitted yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reports as $report): ?>
                <tr>
                    <td style="white-space: nowrap;"><?= date('M d, Y', strtotime($report['reported_at'])) ?></td>
                    <td><?= htmlspecialchars($report['full_name']) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($report['title']) ?></strong> <br>
                        <small style="color: var(--text-muted);"><?= htmlspecialchars($report['category']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($report['location']) ?></td>
                    <td>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '', $report['status'])) ?>">
                            <?= htmlspecialchars($report['status']) ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" style="display: flex; gap: 0.5rem;">
                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                            <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                            
                            <select name="status" style="padding: 0.3rem; border-radius: 4px; border: 1px solid var(--border);">
                                <option value="Pending" <?= $report['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Verified" <?= $report['status'] === 'Verified' ? 'selected' : '' ?>>Verified</option>
                                <option value="In Progress" <?= $report['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Resolved" <?= $report['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                            </select>
                            
                            <button type="submit" name="update_status" class="btn-sm btn-primary" style="margin-top: 0; padding: 0.3rem 0.8rem;">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>