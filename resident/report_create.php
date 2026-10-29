<?php
// resident/report_create.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $title       = trim($_POST['title']);
    $category    = $_POST['category'];
    $description = trim($_POST['description']);
    $location    = trim($_POST['location']);
    $reported_at = $_POST['reported_at'];

    // Basic Validation
    if (empty($title) || empty($category) || empty($description) || empty($location) || empty($reported_at)) {
        $error = "All fields are required.";
    } else {
        // I-save sa database
        $sql = "INSERT INTO emergency_reports (user_id, title, category, description, location, reported_at, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$_SESSION['user_id'], $title, $category, $description, $location, $reported_at])) {
            header('Location: my_reports.php?success=created');
            exit;
        } else {
            $error = "Failed to submit report. Please try again.";
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-box" style="max-width: 600px;">
    <h2>Submit Emergency Report</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
        
        <div class="form-group">
            <label>Report Title</label>
            <input type="text" name="title" required placeholder="e.g., Baha sa may Barangay Hall">
        </div>

        <div class="form-group">
            <label>Disaster / Hazard Category</label>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="Flood">Flood</option>
                <option value="Earthquake">Earthquake</option>
                <option value="Typhoon">Typhoon</option>
                <option value="Fire">Fire</option>
                <option value="Landslide">Landslide</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" required placeholder="e.g., Purok 3, Brgy. San Isidro">
        </div>

        <div class="form-group">
            <label>Date and Time of Incident</label>
            <input type="datetime-local" name="reported_at" required value="<?= date('Y-m-d\TH:i') ?>">
        </div>

        <div class="form-group">
            <label>Description (Details of the incident)</label>
            <textarea name="description" rows="4" required placeholder="Describe the situation..." style="width: 100%; padding: 0.8rem; border: 2px solid var(--border); border-radius: 8px; font-family: inherit;"></textarea>
        </div>
        
        <button type="submit" class="btn-primary">Submit Report</button>
        <a href="my_reports.php" style="display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); text-decoration: none;">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>