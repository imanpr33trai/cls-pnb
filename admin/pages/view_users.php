<?php
// /admin/pages/view_users.php
require_once __DIR__ . '/../../config/config.php';

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
                            <?php foreach ($users as $user): 
                                $isSuspended = $user['status'] === 'suspended';
                            ?>
                                <tr class="<?php echo $isSuspended ? 'bg-red-50 opacity-75' : 'bg-white'; ?>">
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <span class="relative inline-block px-3 py-1 font-semibold leading-tight <?php echo $isSuspended ? 'text-red-900' : 'text-green-900'; ?>">
                                            <span aria-hidden class="absolute inset-0 opacity-50 rounded-full <?php echo $isSuspended ? 'bg-red-200' : 'bg-green-200'; ?>"></span>
                                            <span class="relative"><?php echo htmlspecialchars(ucfirst($user['status'])); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo date("F j, Y", strtotime($user['created_at'])); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <button class="font-semibold toggle-user-status-btn <?php echo $isSuspended ? 'text-green-600 hover:text-green-900' : 'text-yellow-600 hover:text-yellow-900'; ?>" data-user-id="<?php echo $user['id']; ?>">
                                            <?php echo $isSuspended ? 'Activate' : 'Suspend'; ?>
                                        </button>
                                        <button class="ml-3 font-semibold text-red-600 hover:text-red-900 open-delete-user-modal" data-user-id="<?php echo $user['id']; ?>">
                                            Delete
                                        </button>
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