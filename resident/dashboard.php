<?php
// resident/dashboard.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_login();

include '../includes/header.php';
?>

<!-- Hero Banner -->
<div class="hero" style="padding: 3rem 2rem; margin-bottom: 2rem; text-align: left; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, var(--primary) 0%, #1e293b 100%);">
    <div>
        <h1 style="font-size: 2.2rem; margin-bottom: 0.5rem; color: white;">Hello, <?= htmlspecialchars($_SESSION['full_name']) ?>! </h1>
        <p style="color: #cbd5e1; margin: 0; font-size: 1.1rem;">Stay updated with real-time disaster information and AI guidance.</p>
    </div>
    <div>
        <a href="/disasterready/resident/report_create.php" class="btn-primary" style="background: var(--accent); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block;">+ Report Emergency</a>
    </div>
</div>

<!-- Dashboard Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    
    <!-- Weather Card -->
    <div class="chart-card" style="padding: 2rem; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.2rem;"> Live Weather (Manila)</h3>
        <div id="weather-data" style="width: 100%;">
            <p style="color: var(--text-muted);">Loading weather data...</p>
        </div>
    </div>

    <!-- Earthquake Card -->
    <div class="chart-card" style="padding: 2rem;">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.2rem;"> Recent Earthquakes</h3>
        <div id="earthquake-data" style="max-height: 250px; overflow-y: auto; padding-right: 0.5rem;">
            <p style="color: var(--text-muted); text-align: center;">Loading earthquake data...</p>
        </div>
    </div>
</div>

<!-- AI Assistant Card (Full Width) -->
<div class="chart-card" style="padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
        <div>
            <h3 style="color: var(--primary); font-size: 1.2rem; margin-bottom: 0.5rem;">🤖 AI Preparedness Assistant</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">
                <em>Disclaimer: This AI provides general guidance. Always follow official government advisories.</em>
            </p>
        </div>
        <span style="background: #e0e7ff; color: #3730a3; padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700;">Beta</span>
    </div>
    
    <!-- Chat Box -->
    <div id="chat-box" style="height: 250px; overflow-y: auto; background: #f9fafb; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1rem; display: flex; flex-direction: column; gap: 1rem;">
        <!-- Default AI Message -->
        <div style="background: white; border: 1px solid var(--border); padding: 1rem; border-radius: 0 12px 12px 12px; max-width: 80%; box-shadow: var(--shadow-sm);">
            <strong style="color: var(--primary); font-size: 0.85rem;">DisasterReady AI</strong>
            <p style="margin-top: 0.5rem; font-size: 0.95rem; color: var(--text-main);">Hello! I am your preparedness assistant. Ask me for an emergency checklist or safety tips.</p>
        </div>
    </div>
    
    <!-- Chat Input -->
    <div style="display: flex; gap: 0.75rem;">
        <input type="text" id="ai-input" placeholder="e.g., What should I pack in an emergency go-bag?" style="flex: 1; padding: 1rem; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.95rem; outline: none; transition: var(--transition);">
        <button id="ai-btn" class="btn-primary" style="width: auto; margin-top: 0; padding: 0 2rem; border-radius: var(--radius-md);">Send</button>
    </div>
</div>

<!-- Latest Announcements -->
<div class="chart-card" style="padding: 2rem; margin-top: 1.5rem;">
    <h3 style="color: var(--primary); font-size: 1.2rem; margin-bottom: 1rem;"> Latest Announcements</h3>
    <?php
    // Kuhaon ang pinakabag-o nga announcements
    $latestAnn = $pdo->query("SELECT * FROM announcements WHERE is_published = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();
    ?>
    <?php if (empty($latestAnn)): ?>
        <p style="color: var(--text-muted);">No announcements yet.</p>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($latestAnn as $a): ?>
                <div style="background: #f8fafc; border-left: 4px solid var(--accent); padding: 1rem; border-radius: 4px;">
                    <h4 style="color: var(--primary); margin-bottom: 0.25rem;"><?= htmlspecialchars($a['title']) ?></h4>
                    <p style="font-size: 0.9rem;"><?= nl2br(htmlspecialchars($a['content'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// ==========================================
// 1. Fetch Weather
// ==========================================
fetch('/disasterready/api/weather.php')
    .then(res => res.json())
    .then(data => {
        const temp = data.current.temperature_2m;
        const wind = data.current.wind_speed_10m;
        const humidity = data.current.relative_humidity_2m;
        
        document.getElementById('weather-data').innerHTML = `
            <div style="font-size: 4rem; font-weight: 800; color: var(--accent); line-height: 1;">${temp}°C</div>
            <div style="font-size: 1rem; color: var(--text-muted); margin-bottom: 1.5rem;">Current Temperature</div>
            <div style="display: flex; justify-content: center; gap: 2rem; font-size: 1rem; background: #f1f5f9; padding: 1rem; border-radius: 8px;">
                <span>💨 <strong>${wind}</strong> km/h</span>
                <span>💧 <strong>${humidity}%</strong></span>
            </div>
            <div style="margin-top: 1rem; font-size: 0.75rem; color: #9ca3af;">Source: Open-Meteo</div>
        `;
    })
    .catch(err => {
        document.getElementById('weather-data').innerHTML = '<p style="color: red;">Failed to load weather data.</p>';
    });

// ==========================================
// 2. Fetch Earthquakes
// ==========================================
fetch('/disasterready/api/earthquake.php')
    .then(res => res.json())
    .then(data => {
        const features = data.features;
        let html = '';
        
        if (features.length === 0) {
            html = '<p style="text-align: center; color: var(--text-muted);">No recent significant earthquakes.</p>';
        } else {
            features.slice(0, 5).forEach(eq => {
                const mag = eq.properties.mag;
                const place = eq.properties.place;
                const time = new Date(eq.properties.time).toLocaleString();
                
                // Color coding based on magnitude
                let magColor = '#10b981'; // Green for low
                if (mag >= 5.0) magColor = 'var(--accent)'; // Red for high
                else if (mag >= 4.0) magColor = '#f59e0b'; // Orange for medium

                html += `
                    <div style="border-bottom: 1px solid var(--border); padding: 1rem 0; display: flex; gap: 1rem; align-items: center;">
                        <div style="background: ${magColor}; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 1.1rem; flex-shrink: 0;">
                            ${mag}
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--primary); font-size: 0.95rem;">${place}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">${time}</div>
                        </div>
                    </div>
                `;
            });
        }
        document.getElementById('earthquake-data').innerHTML = html;
    })
    .catch(err => {
        document.getElementById('earthquake-data').innerHTML = '<p style="color: red;">Failed to load earthquake data.</p>';
    });

// ==========================================
// 3. AI Assistant
// ==========================================
document.getElementById('ai-btn').addEventListener('click', function() {
    const input = document.getElementById('ai-input').value;
    if (!input) return;

    const chatBox = document.getElementById('chat-box');
    
    // User Message Style
    chatBox.innerHTML += `
        <div style="background: var(--primary); color: white; padding: 1rem; border-radius: 12px 0 12px 12px; max-width: 80%; align-self: flex-end; margin-left: auto;">
            <p style="font-size: 0.95rem;">${input}</p>
        </div>
    `;
    
    // Loading Message
    chatBox.innerHTML += `
        <div id="ai-loading" style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;">
            AI is typing...
        </div>
    `;
    
    chatBox.scrollTop = chatBox.scrollHeight;
    
    document.getElementById('ai-input').value = '';
    document.getElementById('ai-btn').disabled = true;

    // Ilisan ang URL sa fetch
    fetch('/disasterready/api/ai.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt: input })
    })
    .then(res => res.json())
    .then(data => {
        const loadingEl = document.getElementById('ai-loading');
        if (loadingEl) loadingEl.remove();
        
        // Groq/OpenAI format: data.choices[0].message.content
        const reply = data.choices[0].message.content;
        const formattedReply = reply.replace(/\n/g, '<br>');
        
        chatBox.innerHTML += `
            <div style="background: white; border: 1px solid var(--border); padding: 1rem; border-radius: 0 12px 12px 12px; max-width: 80%; box-shadow: var(--shadow-sm);">
                <strong style="color: var(--primary); font-size: 0.85rem;">DisasterReady AI</strong>
                <p style="margin-top: 0.5rem; font-size: 0.95rem; color: var(--text-main);">${formattedReply}</p>
            </div>
        `;
        chatBox.scrollTop = chatBox.scrollHeight;
        document.getElementById('ai-btn').disabled = false;
    })
    .catch(err => {
        const loadingEl = document.getElementById('ai-loading');
        if (loadingEl) loadingEl.remove(); // Check kung naa ba ang element
        chatBox.innerHTML += `<p style="color: red; font-size: 0.9rem;">AI is temporarily unavailable. Please try again in a few moments.</p>`;
        document.getElementById('ai-btn').disabled = false;
    });
});
</script>

<?php include '../includes/footer.php'; ?>