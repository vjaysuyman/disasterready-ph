<?php
// resident/news.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_login();

// Kuhaon ang mga published announcements
$announcements = $pdo->query("SELECT * FROM announcements WHERE is_published = 1 ORDER BY created_at DESC LIMIT 5")->fetchAll();

include '../includes/header.php';
?>

<div class="charts-grid">
    <!-- Official Announcements -->
    <div class="chart-card">
        <h3> Official Announcements</h3>
        <div style="max-height: 400px; overflow-y: auto; padding-right: 0.5rem;">
            <?php if (empty($announcements)): ?>
                <p style="color: var(--text-muted);">No new announcements.</p>
            <?php else: ?>
                <?php foreach ($announcements as $a): ?>
                    <div style="border-bottom: 1px solid var(--border); padding: 1rem 0;">
                        <h4 style="color: var(--primary); margin-bottom: 0.25rem;"><?= htmlspecialchars($a['title']) ?></h4>
                        <small style="color: var(--text-muted);"><?= date('M d, Y', strtotime($a['created_at'])) ?></small>
                        <p style="margin-top: 0.5rem; font-size: 0.95rem;"><?= nl2br(htmlspecialchars($a['content'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Currents API News -->
    <div class="chart-card">
        <h3> Disaster News (Live)</h3>
        <div id="news-feed" style="max-height: 400px; overflow-y: auto; padding-right: 0.5rem;">
            <p style="color: var(--text-muted); text-align: center;">Loading news...</p>
        </div>
    </div>
</div>

<script>
// Fetch News from Currents API
fetch('/api/news.php')
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('news-feed');
        let html = '';

        if (data.status === 'ok' && data.news.length > 0) {
            data.news.slice(0, 5).forEach(article => {
                html += `
                    <div style="border-bottom: 1px solid var(--border); padding: 1rem 0;">
                        <h4 style="margin-bottom: 0.25rem;">
                            <a href="${article.url}" target="_blank" style="color: var(--accent); text-decoration: none;">${article.title}</a>
                        </h4>
                        <small style="color: var(--text-muted);">${article.author || 'News'}</small>
                        <p style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--text-main);">${article.description || 'Click to read more...'}</p>
                    </div>
                `;
            });
        } else {
            html = '<p style="color: var(--text-muted); text-align: center;">No news available at the moment.</p>';
        }
        container.innerHTML = html;
    })
    .catch(err => {
        document.getElementById('news-feed').innerHTML = '<p style="color: red; text-align: center;">Failed to load news.</p>';
    });
</script>

<?php include '../includes/footer.php'; ?>