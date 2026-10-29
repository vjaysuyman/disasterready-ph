<?php
// resident/report_edit.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_login();

$reportId = (int)($_GET['id'] ?? 0);
$userId = $_SESSION['user_id'];
$error = '';

// Kuhaon ang report (Siguroha nga iya kini)
$stmt = $pdo->prepare("SELECT * FROM emergency_reports WHERE id = ? AND user_id = ?");
$stmt->execute([$reportId, $userId]);
$report = $stmt->fetch();

if (!$report) {
    die("Report not found or you do not have permission to edit it.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $title       = trim($_POST['title']);
    $category    = $_POST['category'];
    $description = trim($_POST['description']);
    $location    = trim($_POST['location']);
    $reported_at = $_POST['reported_at'];

    if (empty($title) || empty($category) || empty($description) || empty($location) || empty($reported_at)) {
        $error = "All fields are required.";
    } else {
        $sql = "UPDATE emergency_reports 
                SET title = ?, category = ?, description = ?, location = ?, reported_at = ? 
                WHERE id = ? AND user_id = ?";
        
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$title, $category, $description, $location, $reported_at, $reportId, $userId])) {
            header('Location: my_reports.php?success=updated');
            exit;
        } else {
            $error = "Failed to update report.";
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-box" style="max-width: 600px;">
    <h2>Edit Report</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
        
        <div class="form-group">
            <label>Report Title</label>
            <input type="text" name="title" required value="<?= htmlspecialchars($report['title']) ?>">
        </div>

        <div class="form-group">
            <label>Disaster / Hazard Category</label>
            <select name="category" required>
                <?php 
                $categories = ['Flood', 'Earthquake', 'Typhoon', 'Fire', 'Landslide', 'Other'];
                foreach ($categories as $cat): 
                ?>
                    <option value="<?= $cat ?>" <?= $report['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" required value="<?= htmlspecialchars($report['location']) ?>">
        </div>

        <div class="form-group">
            <label>Date and Time of Incident</label>
            <input type="datetime-local" name="reported_at" required value="<?= date('Y-m-d\TH:i', strtotime($report['reported_at'])) ?>">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4" required style="width: 100%; padding: 0.8rem; border: 2px solid var(--border); border-radius: 8px; font-family: inherit;"><?= htmlspecialchars($report['description']) ?></textarea>
        </div>
        
        <button type="submit" class="btn-primary">Update Report</button>
        <a href="my_reports.php" style="display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); text-decoration: none;">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>