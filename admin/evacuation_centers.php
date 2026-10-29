<?php
// admin/evacuation_centers.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_admin();

$success = '';

// Pag-handle sa CREATE ug UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_center'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $lat = $_POST['latitude'];
    $lng = $_POST['longitude'];
    $contact = trim($_POST['contact']);
    $capacity = (int)$_POST['capacity'];

    if (empty($name) || empty($address) || empty($lat) || empty($lng)) {
        $error = "Please fill in all required fields.";
    } else {
        if ($id > 0) {
            // UPDATE
            $stmt = $pdo->prepare("UPDATE evacuation_centers SET name=?, address=?, latitude=?, longitude=?, contact_person=?, capacity=? WHERE id=?");
            $stmt->execute([$name, $address, $lat, $lng, $contact, $capacity, $id]);
            $success = "Evacuation center updated!";
        } else {
            // CREATE
            $stmt = $pdo->prepare("INSERT INTO evacuation_centers (name, address, latitude, longitude, contact_person, capacity) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $address, $lat, $lng, $contact, $capacity]);
            $success = "New evacuation center added!";
        }
    }
}

// Pag-handle sa DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $deleteId = (int)$_POST['delete_id'];
    $pdo->prepare("DELETE FROM evacuation_centers WHERE id = ?")->execute([$deleteId]);
    $success = "Evacuation center deleted.";
}

// Kuhaon ang tanang evacuation centers
$centers = $pdo->query("SELECT * FROM evacuation_centers ORDER BY name ASC")->fetchAll();

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Evacuation Centers Management</h2>
    <button onclick="showAddForm()" class="btn-primary" style="width: auto; margin-top: 0;">+ Add New Center</button>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Form (Nakatago sa sugod) -->
<div id="center-form" class="auth-box" style="display: none; max-width: 800px; margin: 0 0 2rem 0;">
    <h3 id="form-title" style="margin-bottom: 1.5rem;">Add New Center</h3>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
        <input type="hidden" name="id" id="center-id" value="">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label>Center Name *</label>
                <input type="text" name="name" id="center-name" required>
            </div>
            <div class="form-group">
                <label>Capacity (Number of people)</label>
                <input type="number" name="capacity" id="center-capacity" value="0">
            </div>
        </div>

        <div class="form-group">
            <label>Full Address *</label>
            <input type="text" name="address" id="center-address" required>
        </div>

        <div class="form-group">
            <label>Contact Person / Number</label>
            <input type="text" name="contact" id="center-contact">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label>Latitude *</label>
                <input type="text" name="latitude" id="center-lat" required>
            </div>
            <div class="form-group">
                <label>Longitude *</label>
                <input type="text" name="longitude" id="center-lng" required>
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <small style="color: var(--text-muted);">Tip: I-click ang mapa sa ibabao aron awtomatikong mapuno ang Latitude ug Longitude.</small>
            <div id="map-picker" style="height: 300px; border-radius: 8px; border: 1px solid var(--border); margin-top: 0.5rem; z-index: 1;"></div>
        </div>

        <button type="submit" name="save_center" class="btn-primary">Save Center</button>
        <button type="button" onclick="hideForm()" class="btn-secondary" style="margin-top: 0.5rem;">Cancel</button>
    </form>
</div>

<!-- Table sa mga Centers -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Capacity</th>
                <th>Contact</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($centers)): ?>
                <tr><td colspan="5" style="text-align: center;">No evacuation centers added yet.</td></tr>
            <?php else: ?>
                <?php foreach ($centers as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                    <td><?= htmlspecialchars($c['address']) ?></td>
                    <td><?= htmlspecialchars($c['capacity']) ?> pax</td>
                    <td><?= htmlspecialchars($c['contact_person'] ?? 'N/A') ?></td>
                    <td>
                        <!-- JavaScript para ma-populate ang form inig click sa Edit -->
                        <button class="btn-sm btn-edit" onclick="editCenter(<?= htmlspecialchars(json_encode($c)) ?>)">Edit</button>
                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Delete this center?');">
                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                            <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn-sm btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// ==========================================
// Logic para sa Mini-Map Picker
// ==========================================
let pickerMap, pickerMarker;

document.addEventListener('DOMContentLoaded', function() {
    // Center map sa Pilipinas (Manila)
    pickerMap = L.map('map-picker').setView([14.5995, 120.9842], 11);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(pickerMap);

    pickerMap.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(8);
        const lng = e.latlng.lng.toFixed(8);
        
        document.getElementById('center-lat').value = lat;
        document.getElementById('center-lng').value = lng;

        if (pickerMarker) {
            pickerMap.removeLayer(pickerMarker);
        }
        pickerMarker = L.marker([lat, lng]).addTo(pickerMap);
    });
});

// ==========================================
// Function para ipakita ang Form ug i-fix ang Mapa
// ==========================================
function showAddForm() {
    const form = document.getElementById('center-form');
    form.style.display = 'block';
    
    document.getElementById('form-title').innerText = 'Add New Center';
    form.reset();
    document.getElementById('center-id').value = '';
    
    // Importante: Pugson ang mapa nga mo-resize human ma-display ang form
    setTimeout(() => {
        pickerMap.invalidateSize();
        pickerMap.setView([14.5995, 120.9842], 11);
        if (pickerMarker) { pickerMap.removeLayer(pickerMarker); }
    }, 100);
}

// ==========================================
// Function para tagoan ang Form
// ==========================================
function hideForm() {
    document.getElementById('center-form').style.display = 'none';
}

// ==========================================
// Logic para ma-fill ang form kung i-click ang Edit
// ==========================================
function editCenter(center) {
    document.getElementById('center-form').style.display = 'block';
    document.getElementById('form-title').innerText = 'Edit Evacuation Center';
    
    document.getElementById('center-id').value = center.id;
    document.getElementById('center-name').value = center.name;
    document.getElementById('center-address').value = center.address;
    document.getElementById('center-capacity').value = center.capacity;
    document.getElementById('center-contact').value = center.contact_person || '';
    document.getElementById('center-lat').value = center.latitude;
    document.getElementById('center-lng').value = center.longitude;

    // Pugson ang mapa nga mo-resize human ma-display ang form
    setTimeout(() => {
        const lat = parseFloat(center.latitude);
        const lng = parseFloat(center.longitude);
        
        pickerMap.invalidateSize(); // Kini ang makatangtang sa gray nga background
        pickerMap.setView([lat, lng], 15);
        
        if (pickerMarker) { pickerMap.removeLayer(pickerMarker); }
        pickerMarker = L.marker([lat, lng]).addTo(pickerMap);
    }, 100);
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php include '../includes/footer.php'; ?>