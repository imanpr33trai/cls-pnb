<?php

require_once __DIR__ . '/../../config/config.php';
$stmt = $conn->prepare("SELECT smtp_host, smtp_port, smtp_secure, smtp_user, smtp_pass, smtp_from_email, smtp_from_name FROM site_settings WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_email_settings'])) {
    $smtp_host = trim($_POST['smtp_host']);
    $smtp_port = (int)$_POST['smtp_port'];
    $smtp_secure = trim($_POST['smtp_secure']);
    $smtp_user = trim($_POST['smtp_user']);
    $smtp_from_email = trim($_POST['smtp_from_email']);
    $smtp_from_name = trim($_POST['smtp_from_name']);
    
       $smtp_pass = !empty(trim($_POST['smtp_pass'])) ? trim($_POST['smtp_pass']) : $settings['smtp_pass'];

    $update_stmt = $conn->prepare("
        UPDATE site_settings 
        SET smtp_host = ?, smtp_port = ?, smtp_secure = ?, smtp_user = ?, smtp_pass = ?, smtp_from_email = ?, smtp_from_name = ?
        WHERE id = 1
    ");
    $update_stmt->bind_param("sisssss", $smtp_host, $smtp_port, $smtp_secure, $smtp_user, $smtp_pass, $smtp_from_email, $smtp_from_name);
    
    if ($update_stmt->execute()) {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Email settings updated successfully!</div>";
               $stmt = $conn->prepare("SELECT smtp_host, smtp_port, smtp_secure, smtp_user, smtp_pass, smtp_from_email, smtp_from_name FROM site_settings WHERE id = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $settings = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error updating email settings.</div>";
    }
    $update_stmt->close();
}
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Email Settings (SMTP)</h2>
        <p class="mt-2 text-gray-600">Configure the settings for sending emails from the application.</p>

        <div class="mt-8 p-6 bg-white rounded-lg shadow">
            <form action="" method="POST" class="max-w-2xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label for="smtp_host" class="block text-gray-700 text-sm font-bold mb-2">SMTP Host:</label>
                        <input type="text" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                    <div class="mb-4">
                        <label for="smtp_port" class="block text-gray-700 text-sm font-bold mb-2">SMTP Port:</label>
                        <input type="number" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? ''); ?>"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                    <div class="mb-4">
                        <label for="smtp_secure" class="block text-gray-700 text-sm font-bold mb-2">Encryption:</label>
                        <select id="smtp_secure" name="smtp_secure" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="tls" <?php echo ($settings['smtp_secure'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?php echo ($settings['smtp_secure'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                            <option value="" <?php echo empty($settings['smtp_secure']) ? 'selected' : ''; ?>>None</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="smtp_user" class="block text-gray-700 text-sm font-bold mb-2">SMTP Username:</label>
                        <input type="text" id="smtp_user" name="smtp_user" value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                    <div class="mb-4">
                        <label for="smtp_pass" class="block text-gray-700 text-sm font-bold mb-2">SMTP Password:</label>
                        <input type="password" id="smtp_pass" name="smtp_pass" placeholder="Leave blank to keep current password"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                    <div class="mb-4">
                        <label for="smtp_from_email" class="block text-gray-700 text-sm font-bold mb-2">From Email:</label>
                        <input type="email" id="smtp_from_email" name="smtp_from_email" value="<?php echo htmlspecialchars($settings['smtp_from_email'] ?? ''); ?>"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                    <div class="mb-4">
                        <label for="smtp_from_name" class="block text-gray-700 text-sm font-bold mb-2">From Name:</label>
                        <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?php echo htmlspecialchars($settings['smtp_from_name'] ?? ''); ?>"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                </div>
                <div class="flex items-center mt-6">
                    <button type="submit" name="update_email_settings"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Save Email Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
