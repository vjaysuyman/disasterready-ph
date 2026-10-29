<?php
// admin/manage_users.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_admin();

$success = '';

// Pag-handle sa Activate/Deactivate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $userId = (int)$_POST['user_id'];
    $currentStatus = (int)$_POST['current_status'];
    $newStatus = $currentStatus === 1 ? 0 : 1;

    // Dili pwede ma-deactivate ang kaugalingon nga account
    if ($userId === $_SESSION['user_id']) {
        $error = "You cannot deactivate your own account.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);
        $success = "User status updated successfully.";
    }
}

// Kuhaon ang tanang users gawas sa naka-login nga admin
$stmt = $pdo->prepare("SELECT id, full_name, email, role, is_active, created_at FROM users WHERE id != ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2>Manage Users</h2>
        <p style="color: var(--text-muted);">View and manage resident accounts.</p>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Date Registered</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="status-badge" style="background: #e0e7ff; color: #3730a3;">
                            <?= ucfirst(htmlspecialchars($u['role'])) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <span class="status-badge <?= $u['is_active'] ? 'status-resolved' : 'status-pending' ?>">
                            <?= $u['is_active'] ? 'Active' : 'Deactivated' ?>
                        </span>
                    </td>
                    <td>
                        <!-- Form para sa Toggle Status -->
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="current_status" value="<?= $u['is_active'] ?>">
                            
                            <?php if ($u['is_active']): ?>
                                <button type="submit" name="toggle_status" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to deactivate this user?');">Deactivate</button>
                            <?php else: ?>
                                <button type="submit" name="toggle_status" class="btn-sm btn-edit" style="background: #10b981; color: white; border: none;" onclick="return confirm('Are you sure you want to activate this user?');">Activate</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>