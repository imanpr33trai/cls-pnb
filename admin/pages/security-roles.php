<?php

require_once __DIR__ . '/../../config/config.php';
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
                                                     src="<?php echo htmlspecialchars($admin['profile_image'] ?? '/assets/images/userimage.png'); ?>"
                                                     alt="<?php echo htmlspecialchars($admin['full_name']); ?>">
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