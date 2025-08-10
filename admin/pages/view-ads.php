<?php
// /admin/pages/view-ads.php
require_once __DIR__ . '/../../config/config.php';

// Fetch all ads from the database
$stmt = $conn->prepare("
    SELECT 
        a.id, 
        a.ad_title, 
        a.ad_slug,
        ac.name AS category_name, 
        a.asking_price, 
        a.user_name, 
        a.status,
        a.created_at
    FROM 
        ad_form a
    LEFT JOIN 
        ad_categories ac ON a.category = ac.id
    ORDER BY 
        a.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$ads = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">All Ads</h2>
        </div>
        <div class="-mx-4 sm:-mx-8  sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Title
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Price
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ads)): ?>
                            <tr>
                                <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No ads found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ads as $ad): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['ad_title']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['category_name'] ?? 'N/A'); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            <?php echo $ad['asking_price'] ? '$' . number_format($ad['asking_price'], 2) : 'N/A'; ?>
                                        </p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['user_name']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                            <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                            <span class="relative"><?php echo htmlspecialchars($ad['status']); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
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
