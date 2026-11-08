<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DisasterReady PH</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Gikuha ang / diri -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- Leaflet CSS & JS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body>

<?php if (is_logged_in()): ?>

    <?php if (is_admin()): ?>

        <!-- ADMIN LAYOUT -->
        <div class="admin-layout">

            <!-- Admin Sidebar -->
            <aside class="sidebar" id="admin-sidebar">

                <div class="sidebar-brand">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    DisasterReady
                </div>

                <nav class="sidebar-nav">
                    <a href="/admin/dashboard.php">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <a href="/admin/manage_reports.php">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Manage Reports
                    </a>

                    <a href="/admin/evacuation_centers.php">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Evacuation Centers
                    </a>

                    <a href="/admin/manage_users.php">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Manage Users
                    </a>

                    <a href="/admin/announcements.php">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        Announcements
                    </a>
                </nav>
            </aside>

            <!-- Admin Main Content -->
            <div class="main-content">
                <header class="top-navbar">
                    <!-- Hamburger Button (Visible on Mobile) -->
                    <button class="menu-toggle" id="menu-toggle" type="button" aria-label="Toggle navigation menu" aria-controls="admin-sidebar" aria-expanded="false" style="display: none; background: none; border: none; cursor: pointer; color: var(--primary);">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="top-navbar-right">
                        <span style="font-weight: 500; font-size: 0.9rem;">
                            Hello, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong>
                        </span>
                        <a href="/logout.php" class="btn-logout">Logout</a>
                    </div>
                </header>

                <div class="container">

    <?php else: ?>

        <!-- RESIDENT LAYOUT -->
        <nav class="navbar">
            <div class="nav-brand">
                <a href="/resident/dashboard.php">
                    <svg width="24" height="24" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    DisasterReady PH
                </a>
            </div>

            <!-- Hamburger Button para sa Resident (Tago sa desktop) -->
            <button class="resident-menu-toggle" id="resident-menu-toggle" type="button" aria-label="Toggle navigation">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <div class="nav-links" id="resident-nav-links">
                <a href="/resident/dashboard.php">Dashboard</a>
                <a href="/resident/my_reports.php">My Reports</a>
                <a href="/resident/weather.php">Weather</a>
                <a href="/resident/news.php">News</a>
                <a href="/resident/map.php">Map</a>

                <span>
                    Hello, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong>
                </span>

                <a href="/logout.php" class="btn-logout">Logout</a>
            </div>
        </nav>
        <div class="container">

    <?php endif; ?>

<?php else: ?>

    <!-- PUBLIC LAYOUT -->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="/index.php">
                <svg width="24" height="24" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                DisasterReady PH
            </a>
        </div>

        <div class="nav-links">
            <a href="/login.php">Login</a>
            <a href="/register.php" class="btn-logout">Register</a>
        </div>
    </nav>

    <div class="container">

<?php endif; ?>