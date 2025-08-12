<?php
// /admin/pages/security-roles.php
require_once __DIR__ . '/../../config/config.php';

// Fetch all admins
$stmt = $conn->prepare("SELECT id, full_name, email, role, profile_image FROM admins ORDER BY full_name ASC");
$stmt->execute();
$result = $stmt->get_result();
$admins = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Roles & Permissions</h2>
        <p class="mt-2 text-gray-600">Manage administrator accounts and their assigned roles.</p>

        <div class="mt-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
            <p class="font-bold">Feature Coming Soon!</p>
            <p>Full role and permission management is currently under development. For now, you can view existing administrators.</p>
        </div>

        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto mt-8">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Role
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($admins)): ?>
                            <tr>
                                <td colspan="3" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No administrators found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10">
                                                <img class="w-full h-full rounded-full"
                                                     <?php
// /admin/pages/security-roles.php
require_once __DIR__ . '/../../config/config.php';

// --- Handle Form Submissions ---

// Add/Edit Role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_role'])) {
    $role_id = $_POST['role_id'] ?? null;
    $role_name = trim($_POST['role_name']);
    $permissions = $_POST['permissions'] ?? [];
    $permissions_json = json_encode($permissions);

    if (empty($role_name) || empty($permissions)) {
        $error_message = "Role name and at least one permission are required.";
    } else {
        if ($role_id) {
            // Update existing role
            $stmt = $conn->prepare("UPDATE security_roles SET role_name = ?, permissions = ? WHERE id = ?");
            $stmt->bind_param("ssi", $role_name, $permissions_json, $role_id);
        } else {
            // Insert new role
            $stmt = $conn->prepare("INSERT INTO security_roles (role_name, permissions) VALUES (?, ?)");
            $stmt->bind_param("ss", $role_name, $permissions_json);
        }

        if (!$stmt->execute()) {
            $error_message = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Assign Role to Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_role'])) {
    $admin_id = $_POST['admin_id'];
    $role_id = $_POST['role_id'];

    // Prevent changing role for admin ID 1
    if ($admin_id != 1) {
        $stmt = $conn->prepare("UPDATE admins SET role_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $role_id, $admin_id);
        if (!$stmt->execute()) {
            $error_message = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Delete Role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_role'])) {
    $role_id = $_POST['role_id'];
    // Add safety check: don't delete role ID 1 (super admin)
    if ($role_id != 1) {
        // Set users with this role to a default role or null
        $update_admins = $conn->prepare("UPDATE admins SET role_id = NULL WHERE role_id = ?");
        $update_admins->bind_param("i", $role_id);
        $update_admins->execute();
        $update_admins->close();

        // Delete the role
        $stmt = $conn->prepare("DELETE FROM security_roles WHERE id = ?");
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        $stmt->close();
    }
}


// --- Fetch Data for Display ---
$roles_stmt = $conn->prepare("SELECT * FROM security_roles ORDER BY role_name ASC");
$roles_stmt->execute();
$roles_result = $roles_stmt->get_result();
$roles = $roles_result->fetch_all(MYSQLI_ASSOC);
$roles_stmt->close();

$admins_stmt = $conn->prepare("
    SELECT a.id, a.full_name, a.email, a.profile_image, sr.role_name 
    FROM admins a
    LEFT JOIN security_roles sr ON a.role_id = sr.id
    ORDER BY a.full_name ASC
");
$admins_stmt->execute();
$admins_result = $admins_stmt->get_result();
$admins = $admins_result->fetch_all(MYSQLI_ASSOC);
$admins_stmt->close();

// Define available permissions
$available_permissions = [
    'manage_ads' => 'Manage Ads',
    'manage_users' => 'Manage Users',
    'manage_blogs' => 'Manage Blogs',
    'manage_categories' => 'Manage Categories',
    'manage_settings' => 'Manage Site Settings',
    'manage_roles' => 'Manage Security Roles'
];

?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Roles & Permissions</h2>
        <p class="mt-2 text-gray-600">Manage administrator accounts and their assigned roles.</p>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded my-4">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Role Form -->
        <div class="mt-8 p-6 bg-white rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Add New Role</h3>
            <form action="" method="POST">
                <div class="mb-4">
                    <label for="role_name" class="block text-gray-700 text-sm font-bold mb-2">Role Name:</label>
                    <input type="text" id="role_name" name="role_name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Permissions:</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($available_permissions as $key => $label): ?>
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="permissions[]" value="<?php echo $key; ?>" class="form-checkbox h-5 w-5 text-indigo-600">
                                    <span class="ml-2 text-gray-700"><?php echo $label; ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" name="save_role" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Save Role
                </button>
            </form>
        </div>

        <!-- Existing Roles Table -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-4">Existing Roles</h3>
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role Name</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Permissions</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($role['role_name']); ?></td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?php 
                                    $permissions = json_decode($role['permissions'], true);
                                    if (is_array($permissions)) {
                                        echo implode(', ', array_map('ucfirst', str_replace('_', ' ', $permissions)));
                                    }
                                    ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <!-- Edit and Delete buttons would go here -->
                                     <form action="" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                                        <input type="hidden" name="role_id" value="<?php echo $role['id']; ?>">
                                        <button type="submit" name="delete_role" class="text-red-600 hover:text-red-900 font-semibold" <?php if($role['id'] == 1) echo 'disabled'; ?>>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Admins List -->
        <div class="mt-8">
             <h3 class="text-lg font-semibold mb-4">Administrator Accounts</h3>
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                     <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Assigned Role
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($admin['full_name']); ?></p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($admin['email']); ?></p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <form action="" method="POST" class="inline-flex items-center">
                                        <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                        <select name="role_id" class="shadow border rounded py-1 px-2 text-gray-700" <?php if($admin['id'] == 1) echo 'disabled'; ?>>
                                            <option value="">No Role</option>
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?php echo $role['id']; ?>" <?php echo ($admin['role_name'] == $role['role_name']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="assign_role" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-2 rounded text-xs" <?php if($admin['id'] == 1) echo 'disabled'; ?>>
                                            Assign
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    <?php echo htmlspecialchars($admin['full_name']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($admin['email']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars(ucfirst($admin['role'])); ?></p>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
