<?php
// /admin/pages/site-settings.php
require_once __DIR__ . '/../../config/config.php';

// Fetch the current site settings
$stmt = $conn->prepare("SELECT * FROM site_settings WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $site_name = trim($_POST['site_name']);
    $admin_phone = trim($_POST['admin_phone']);
    $admin_details = trim($_POST['admin_details']);
    
    $header_logo = $settings['header_logo'];
    $footer_logo = $settings['footer_logo'];

    $upload_dir = __DIR__ . '/../../assets/images/';

    // Handle header logo upload
    if (!empty($_FILES['header_logo']['name'])) {
        // Delete old logo if it exists
        if (!empty($header_logo) && file_exists($upload_dir . $header_logo)) {
            unlink($upload_dir . $header_logo);
        }
        $header_logo = time() . '_header_' . basename($_FILES['header_logo']['name']);
        move_uploaded_file($_FILES['header_logo']['tmp_name'], $upload_dir . $header_logo);
    }

    // Handle footer logo upload
    if (!empty($_FILES['footer_logo']['name'])) {
        // Delete old logo if it exists
        if (!empty($footer_logo) && file_exists($upload_dir . $footer_logo)) {
            unlink($upload_dir . $footer_logo);
        }
        $footer_logo = time() . '_footer_' . basename($_FILES['footer_logo']['name']);
        move_uploaded_file($_FILES['footer_logo']['tmp_name'], $upload_dir . $footer_logo);
    }

    $update_stmt = $conn->prepare("
        UPDATE site_settings 
        SET site_name = ?, header_logo = ?, footer_logo = ?, admin_phone = ?, admin_details = ?
        WHERE id = 1
    ");
    $update_stmt->bind_param("sssss", $site_name, $header_logo, $footer_logo, $admin_phone, $admin_details);
    
    if ($update_stmt->execute()) {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Settings updated successfully!</div>";
        // Refresh settings after update
        $stmt = $conn->prepare("SELECT * FROM site_settings WHERE id = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $settings = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error updating settings.</div>";
    }
    $update_stmt->close();
}
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Site Settings</h2>
        <p class="mt-2 text-gray-600">Manage general site information and branding.</p>

        <div class="mt-8 p-6 bg-white rounded-lg shadow">
            <form action="" method="POST" enctype="multipart/form-data" class="max-w-2xl">
                <div class="mb-4">
                    <label for="site_name" class="block text-gray-700 text-sm font-bold mb-2">Site Name:</label>
                    <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label for="header_logo" class="block text-gray-700 text-sm font-bold mb-2">Header Logo:</label>
                    <?php if (!empty($settings['header_logo'])): ?>
                        <img src="../assets/images/<?php echo htmlspecialchars($settings['header_logo']); ?>" alt="Header Logo" class="h-12 mb-2">
                    <?php endif; ?>
                    <input type="file" id="header_logo" name="header_logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-1">Upload a new logo to replace the current one.</p>
                </div>

                <div class="mb-6">
                    <label for="footer_logo" class="block text-gray-700 text-sm font-bold mb-2">Footer Logo:</label>
                    <?php if (!empty($settings['footer_logo'])): ?>
                        <img src="../assets/images/<?php echo htmlspecialchars($settings['footer_logo']); ?>" alt="Footer Logo" class="h-12 mb-2">
                    <?php endif; ?>
                    <input type="file" id="footer_logo" name="footer_logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="mb-4">
                    <label for="admin_phone" class="block text-gray-700 text-sm font-bold mb-2">Admin Contact Phone:</label>
                    <input type="text" id="admin_phone" name="admin_phone" value="<?php echo htmlspecialchars($settings['admin_phone'] ?? ''); ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label for="admin_details" class="block text-gray-700 text-sm font-bold mb-2">Admin Details / Address:</label>
                    <textarea id="admin_details" name="admin_details" rows="4"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($settings['admin_details'] ?? ''); ?></textarea>
                </div>

                <div class="flex items-center">
                    <button type="submit" name="update_settings"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
