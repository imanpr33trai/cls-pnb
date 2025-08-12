<?php
require_once __DIR__ . '/../../config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);$setting = $conn->query("SELECT * FROM site_settings WHERE id = 1")->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'] ?? '';
    $admin_phone = $_POST['admin_phone'] ?? '';
    $admin_details = $_POST['admin_details'] ?? '';
    
    $header_logo = $setting['header_logo'] ?? '';
    $footer_logo = $setting['footer_logo'] ?? '';

       if (!empty($_FILES['header_logo']['name'])) {
        $header_logo_file = time() . '_' . basename($_FILES['header_logo']['name']);
        if (move_uploaded_file($_FILES['header_logo']['tmp_name'], $upload_dir . $header_logo_file)) {
            $header_logo = 'upload/' . $header_logo_file;
        }
    }

       if (!empty($_FILES['footer_logo']['name'])) {
        $footer_logo_file = time() . '_' . basename($_FILES['footer_logo']['name']);
        if (move_uploaded_file($_FILES['footer_logo']['tmp_name'], $upload_dir . $footer_logo_file)) {
            $footer_logo = 'upload/' . $footer_logo_file;
        }
    }

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
$setting = $conn->query("SELECT * FROM site_settings WHERE id = 1")->fetch_assoc();
?>

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Site Settings</h2>
    <div class="bg-white shadow-md rounded-lg p-6">
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name:</label>
                <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($setting['site_name'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="header_logo" class="block text-sm font-medium text-gray-700 mb-1">Header Logo:</label>
                    <?php if (!empty($setting['header_logo'])): ?>
                    <div class="mt-2">
                        <img src="../../admin/<?= $setting['header_logo'] ?>" class="h-16 w-auto rounded-md border border-gray-200">
                    </div>
                    <?php endif; ?>
                    <input type="file" id="header_logo" name="header_logo" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div>
                    <label for="footer_logo" class="block text-sm font-medium text-gray-700 mb-1">Footer Logo:</label>
                    <?php if (!empty($setting['footer_logo'])): ?>
                    <div class="mt-2">
                        <img src="../../admin/<?= $setting['footer_logo'] ?>" class="h-16 w-auto rounded-md border border-gray-200">
                    </div>
                    <?php endif; ?>
                    <input type="file" id="footer_logo" name="footer_logo" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>

            <div>
                <label for="admin_phone" class="block text-sm font-medium text-gray-700 mb-1">Admin Phone:</label>
                <input type="text" id="admin_phone" name="admin_phone" value="<?= htmlspecialchars($setting['admin_phone'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="admin_details" class="block text-sm font-medium text-gray-700 mb-1">Admin Details:</label>
                <textarea id="admin_details" name="admin_details" rows="4" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?= htmlspecialchars($setting['admin_details'] ?? '') ?></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>