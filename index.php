
<?php
// index.php
require_once 'includes/auth.php';

if (is_logged_in()) {
    if (is_admin()) {
        header('Location: /admin/dashboard.php');
    } else {
        header('Location: /resident/dashboard.php');
    }
    exit;
}

include 'includes/header.php';
?>

<!-- ===== Hero ===== -->
<div class="hero">
    <h1>Disaster<span>Ready</span> PH</h1>
    <p>Your centralized platform for weather updates, earthquake monitoring, and emergency reporting.</p>

    <div class="hero-buttons">
        <a href="/login.php" class="btn-primary">Log In</a>
        <a href="/register.php" class="btn-secondary">Register as Resident</a>
    </div>

    <ul class="trust-badges">
        <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Free for residents</li>
        <li><i class="fa-solid fa-clock" aria-hidden="true"></i> Real-time data</li>
        <li><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Secure accounts</li>
    </ul>
</div>

<!-- ===== Highlights Strip ===== -->
<div class="highlights">
    <div class="highlight-item">
        <span class="highlight-value">24/7</span>
        <span class="highlight-label">Monitoring</span>
    </div>

    <div class="highlight-item">
        <span class="highlight-value">Live</span>
        <span class="highlight-label">Weather &amp; Quake Data</span>
    </div>

    <div class="highlight-item">
        <span class="highlight-value">4</span>
        <span class="highlight-label">Report Status Stages</span>
    </div>

    <div class="highlight-item">
        <span class="highlight-value">1</span>
        <span class="highlight-label">Central Dashboard</span>
    </div>
</div>

<!-- ===== Features ===== -->
<h2 class="section-title">Everything you need to stay prepared</h2>
<p class="section-subtitle">
    Reliable information and fast reporting, all in one place.
</p>

<div class="features-grid">
    <div class="feature-card">
        <h3>
            <i class="fa-solid fa-cloud-sun feature-icon" aria-hidden="true"></i>
            Live Weather
        </h3>
        <p>Real-time weather forecasts via Open-Meteo.</p>
    </div>

    <div class="feature-card">
        <h3>
            <i class="fa-solid fa-house-crack feature-icon" aria-hidden="true"></i>
            Earthquake Alerts
        </h3>
        <p>Recent seismic activity from USGS.</p>
    </div>

    <div class="feature-card">
        <h3>
            <i class="fa-solid fa-bullhorn feature-icon" aria-hidden="true"></i>
            Emergency Reporting
        </h3>
        <p>Report local hazards to authorities.</p>
    </div>

    <div class="feature-card">
        <h3>
            <i class="fa-solid fa-location-dot feature-icon" aria-hidden="true"></i>
            Report Tracking
        </h3>
        <p>Follow your report from Pending to Resolved at any time.</p>
    </div>

    <div class="feature-card">
        <h3>
            <i class="fa-solid fa-shield-halved feature-icon" aria-hidden="true"></i>
            Admin Verification
        </h3>
        <p>Authorities review and verify reports to keep information trustworthy.</p>
    </div>

    <div class="feature-card">
        <h3>
            <i class="fa-solid fa-chart-line feature-icon" aria-hidden="true"></i>
            Central Dashboard
        </h3>
        <p>A clear overview of incidents, statuses, and activity.</p>
    </div>
</div>

<!-- ===== How It Works ===== -->
<h2 class="section-title">How it works</h2>
<p class="section-subtitle">
    Reporting an emergency takes just three steps.
</p>

<div class="steps-grid">
    <div class="step-card">
        <span class="step-number">1</span>
        <h3>Create an account</h3>
        <p>Register as a resident to access reporting and monitoring tools.</p>
    </div>

    <div class="step-card">
        <span class="step-number">2</span>
        <h3>Submit a report</h3>
        <p>Describe the hazard or incident in your area so responders can act.</p>
    </div>

    <div class="step-card">
        <span class="step-number">3</span>
        <h3>Track the response</h3>
        <p>Watch your report move through verification until it is resolved.</p>
    </div>
</div>

<!-- ===== Status Legend ===== -->
<div class="info-panel">
    <h3>Understanding report statuses</h3>

    <ul class="status-legend">
        <li>
            <span class="status-badge status-pending">Pending</span>
            Received and waiting for review.
        </li>

        <li>
            <span class="status-badge status-verified">Verified</span>
            Confirmed by an administrator.
        </li>

        <li>
            <span class="status-badge status-inprogress">In Progress</span>
            Responders are working on it.
        </li>

        <li>
            <span class="status-badge status-resolved">Resolved</span>
            The incident has been addressed.
        </li>
    </ul>
</div>

<!-- ===== Preparedness + Hotlines ===== -->
<div class="two-col">
    <div class="info-panel">
        <h3>
            <i class="fa-solid fa-briefcase feature-icon" aria-hidden="true"></i>
            Go-Bag Checklist
        </h3>

        <ul class="checklist">
            <li>Drinking water and ready-to-eat food (3 days)</li>
            <li>First-aid kit and personal medicines</li>
            <li>Flashlight, extra batteries, and power bank</li>
            <li>Whistle and battery-powered radio</li>
            <li>Copies of IDs and important documents</li>
            <li>Cash, face masks, and hygiene items</li>
        </ul>
    </div>

    <div class="info-panel hotline-panel">
        <h3>
            <i class="fa-solid fa-phone-volume feature-icon" aria-hidden="true"></i>
            Emergency Hotlines
        </h3>

        <ul class="hotline-list">
            <li>
                <span>National Emergency Hotline</span>
                <strong>911</strong>
            </li>

            <li>
                <span>Philippine Red Cross</span>
                <strong>143</strong>
            </li>
        </ul>

        <p class="panel-note">
            In a life-threatening emergency, call first.
            Use this system to report and track local hazards.
        </p>
    </div>
</div>

<!-- ===== Call to Action ===== -->
<div class="cta-banner">
    <h2>Be ready before disaster strikes</h2>
    <p>Join DisasterReady PH today and help keep your community safe.</p>

    <a href="/register.php" class="btn-primary">
        Get Started
    </a>
</div>

<?php include 'includes/footer.php'; ?>
