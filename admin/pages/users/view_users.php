<?php
// /admin/pages/users/view_users.php
require_once __DIR__ . '/../../../config/config.php';

// Handle user actions (status change, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'], $_POST['action'])) {
        $user_id = (int)$_POST['user_id'];
        $action = $_POST['action'];

        if ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ User deleted successfully!</div>";
            } else {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error deleting user.</div>";
            }
            $stmt->close();
        } elseif (in_array($action, ['active', 'suspended'])) {
            $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $action, $user_id);
            if ($stmt->execute()) {
                echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ User status updated!</div>";
            } else {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error updating user status.</div>";
            }
            $stmt->close();
        }
    }
}

// Fetch all users
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, status, auth_provider, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">All Users</h2>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
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
                                Status
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Registered On
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="relative inline-block px-3 py-1 font-semibold leading-tight 
                                            <?php if ($user['status'] === 'active') echo 'text-green-900';
                                                  elseif ($user['status'] === 'suspended') echo 'text-red-900';
                                                  else echo 'text-yellow-900'; ?>">
                                            <span aria-hidden class="absolute inset-0 opacity-50 rounded-full 
                                                <?php if ($user['status'] === 'active') echo 'bg-green-200';
                                                      elseif ($user['status'] === 'suspended') echo 'bg-red-200';
                                                      else echo 'bg-yellow-200'; ?>"></span>
                                            <span class="relative"><?php echo htmlspecialchars(ucfirst($user['status'])); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo date("F j, Y", strtotime($user['created_at'])); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <form action="" method="POST" class="inline-block">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <?php if ($user['status'] === 'suspended'): ?>
                                                <button type="submit" name="action" value="active" class="text-green-600 hover:text-green-900 mr-3 font-semibold">Activate</button>
                                            <?php else: ?>
                                                <button type="submit" name="action" value="suspended" class="text-yellow-600 hover:text-yellow-900 mr-3 font-semibold">Suspend</button>
                                            <?php endif; ?>
                                            <button type="submit" name="action" value="delete" class="text-red-600 hover:text-red-900 font-semibold" onclick="return confirm('Are you sure you want to permanently delete this user?');">Delete</button>
                                        </form>
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