<?php
// admin/dashboard.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_admin();

// 1. Kuhaon ang Summary Stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'resident'")->fetchColumn();
$totalReports = $pdo->query("SELECT COUNT(*) FROM emergency_reports")->fetchColumn();
$pendingReports = $pdo->query("SELECT COUNT(*) FROM emergency_reports WHERE status = 'Pending'")->fetchColumn();
$totalCenters = $pdo->query("SELECT COUNT(*) FROM evacuation_centers")->fetchColumn();

// 2. Data para sa Doughnut Chart (Reports by Category)
$catSql = "SELECT category, COUNT(*) as count FROM emergency_reports GROUP BY category";
$catStmt = $pdo->query($catSql);
$categories = [];
$categoryCounts = [];
while ($row = $catStmt->fetch()) {
    $categories[] = $row['category'];
    $categoryCounts[] = $row['count'];
}

// 3. Data para sa Bar Chart (Reports by Status)
$statSql = "SELECT status, COUNT(*) as count FROM emergency_reports GROUP BY status";
$statStmt = $pdo->query($statSql);
$statuses = [];
$statusCounts = [];
while ($row = $statStmt->fetch()) {
    $statuses[] = $row['status'];
    $statusCounts[] = $row['count'];
}

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2>Dashboard Overview</h2>
        <p style="color: var(--text-muted);">Welcome back, Admin. Here is the current situation.</p>
    </div>
    <a href="manage_reports.php" class="btn-primary" style="width: auto; margin-top: 0;">View All Reports</a>
</div>

<!-- Summary Cards -->
<div class="stats-grid">
    <div class="stat-card" style="border-left-color: #3b82f6;">
        <h3>Total Residents</h3>
        <div class="stat-number"><?= $totalUsers ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--accent);">
        <h3>Total Reports</h3>
        <div class="stat-number"><?= $totalReports ?></div>
    </div>
    <div class="stat-card" style="border-left-color: #eab308;">
        <h3>Pending Reports</h3>
        <div class="stat-number"><?= $pendingReports ?></div>
    </div>
    <div class="stat-card" style="border-left-color: #22c55e;">
        <h3>Evacuation Centers</h3>
        <div class="stat-number"><?= $totalCenters ?></div>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <div class="chart-card">
        <h3>Reports by Disaster Category</h3>
        <?php if (empty($categories)): ?>
            <p style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No data available yet.</p>
        <?php else: ?>
            <div style="position: relative; height:300px; width:100%;">
                <canvas id="categoryChart"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <div class="chart-card">
        <h3>Reports by Status</h3>
        <?php if (empty($statuses)): ?>
            <p style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No data available yet.</p>
        <?php else: ?>
            <div style="position: relative; height:300px; width:100%;">
                <canvas id="statusChart"></canvas>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Kani nga script maoy mopadagan sa Chart.js -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Doughnut Chart (Categories)
    <?php if (!empty($categories)): ?>
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($categories) ?>,
            datasets: [{
                data: <?= json_encode($categoryCounts) ?>,
                backgroundColor: [
                    '#3b82f6', // Blue
                    '#ef4444', // Red
                    '#f59e0b', // Yellow
                    '#10b981', // Green
                    '#8b5cf6', // Purple
                    '#6b7280'  // Gray
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    <?php endif; ?>

    // 2. Bar Chart (Status)
    <?php if (!empty($statuses)): ?>
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'bar',
        data: {
            labels: <?= json_encode($statuses) ?>,
            datasets: [{
                label: 'Number of Reports',
                data: <?= json_encode($statusCounts) ?>,
                backgroundColor: [
                    '#f59e0b', // Pending (Yellow)
                    '#3b82f6', // Verified (Blue)
                    '#f97316', // In Progress (Orange)
                    '#10b981'  // Resolved (Green)
                ],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
    <?php endif; ?>
});
</script>

<?php include '../includes/footer.php'; ?>