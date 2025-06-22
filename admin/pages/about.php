<?php
include_once('../../config/config.php');

// Show all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Folder for logo upload
$upload_dir = '../../admin/upload/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Fetch existing settings
$setting = $conn->query("SELECT * FROM site_settings WHERE id = 1")->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'] ?? '';
    $admin_phone = $_POST['admin_phone'] ?? '';
    $admin_details = $_POST['admin_details'] ?? '';
    
    $header_logo = $setting['header_logo'] ?? '';
    $footer_logo = $setting['footer_logo'] ?? '';

    // Upload header logo
    if (!empty($_FILES['header_logo']['name'])) {
        $header_logo_file = time() . '_' . basename($_FILES['header_logo']['name']);
        if (move_uploaded_file($_FILES['header_logo']['tmp_name'], $upload_dir . $header_logo_file)) {
            $header_logo = 'upload/' . $header_logo_file;
        }
    }

    // Upload footer logo
    if (!empty($_FILES['footer_logo']['name'])) {
        $footer_logo_file = time() . '_' . basename($_FILES['footer_logo']['name']);
        if (move_uploaded_file($_FILES['footer_logo']['tmp_name'], $upload_dir . $footer_logo_file)) {
            $footer_logo = 'upload/' . $footer_logo_file;
        }
    }

    // Check if row exists
    $exists = $conn->query("SELECT id FROM site_settings WHERE id = 1")->num_rows > 0;

    if ($exists) {
        $stmt = $conn->prepare("UPDATE site_settings SET site_name=?, header_logo=?, footer_logo=?, admin_phone=?, admin_details=? WHERE id=1");
    } else {
        $stmt = $conn->prepare("INSERT INTO site_settings (id, site_name, header_logo, footer_logo, admin_phone, admin_details) VALUES (1, ?, ?, ?, ?, ?)");
    }

    if ($stmt) {
        $stmt->bind_param("sssss", $site_name, $header_logo, $footer_logo, $admin_phone, $admin_details);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('✅ Site settings saved!'); window.location.href='about.php';</script>";
        exit;
    } else {
        echo "❌ DB Error: " . $conn->error;
        exit;
    }
}

// Re-fetch in case updated
$setting = $conn->query("SELECT * FROM site_settings WHERE id = 1")->fetch_assoc();
?>

<h2>Site Settings</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Site Name:</label><br>
    <input type="text" name="site_name" value="<?= htmlspecialchars($setting['site_name'] ?? '') ?>"><br><br>

    <label>Header Logo:</label><br>
    <?php if (!empty($setting['header_logo'])): ?>
    <img src="../../admin/<?= $setting['header_logo'] ?>" width="100"><br>
    <?php endif; ?>
    <input type="file" name="header_logo"><br><br>

    <label>Footer Logo:</label><br>
    <?php if (!empty($setting['footer_logo'])): ?>
    <img src="../../admin/<?= $setting['footer_logo'] ?>" width="100"><br>
    <?php endif; ?>
    <input type="file" name="footer_logo"><br><br>

    <label>Admin Phone:</label><br>
    <input type="text" name="admin_phone" value="<?= htmlspecialchars($setting['admin_phone'] ?? '') ?>"><br><br>

    <label>Admin Details:</label><br>
    <textarea name="admin_details"><?= htmlspecialchars($setting['admin_details'] ?? '') ?></textarea><br><br>

    <button type="submit">Save Settings</button>
</form>