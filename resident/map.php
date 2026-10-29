<?php
// resident/map.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_login();

// Kuhaon ang mga Evacuation Centers
$centers = $pdo->query("SELECT * FROM evacuation_centers")->fetchAll();

include '../includes/header.php';
?>

<h2>Interactive Evacuation Map</h2>
<p style="margin-bottom: 1rem; color: var(--text-muted);">View nearby evacuation centers and reported hazards. Map data &copy; OpenStreetMap contributors.</p>

<div id="main-map" style="height: 600px; border-radius: var(--radius-lg); border: 1px solid var(--border); z-index: 1;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. I-initialize ang Mapa (Center sa Manila)
    const map = L.map('main-map').setView([14.5995, 120.9842], 11);
    
    // 2. Ibutang ang OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // 3. Custom Icon para sa Evacuation Center (Green)
    const centerIcon = L.divIcon({
        className: 'custom-icon',
        html: '<div style="background-color: #10b981; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    // 4. I-loop ang mga Evacuation Centers gikan sa Database ug ibutang sa Mapa
    const centers = <?= json_encode($centers) ?>;
    
    if (centers.length === 0) {
        alert("No evacuation centers mapped yet. Admin can add them in the dashboard.");
    }

    centers.forEach(center => {
        const lat = parseFloat(center.latitude);
        const lng = parseFloat(center.longitude);
        
        if (!isNaN(lat) && !isNaN(lng)) {
            const marker = L.marker([lat, lng], { icon: centerIcon }).addTo(map);
            
            // Popup Content
            const popupContent = `
                <div style="font-family: 'Inter', sans-serif;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #0f172a;">${center.name}</h4>
                    <p style="margin: 0; font-size: 0.85rem; color: #475569;">${center.address}</p>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem;"><strong>Capacity:</strong> ${center.capacity} pax</p>
                    ${center.contact_person ? `<p style="margin: 0; font-size: 0.85rem;"><strong>Contact:</strong> ${center.contact_person}</p>` : ''}
                </div>
            `;
            marker.bindPopup(popupContent);
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>