<?php

require_once __DIR__ . '/../../config/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ad_id'], $_POST['status'])) {
    $ad_id = (int)$_POST['ad_id'];
    $status = $_POST['status'];

    if (in_array($status, ['live', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE ad_form SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $ad_id);
        if ($stmt->execute()) {
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Ad status updated successfully!</div>";
        } else {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error updating status.</div>";
        }
        $stmt->close();
    }
}



$stmt = $conn->prepare("
    SELECT 
        a.id, 
        a.ad_title, 
        a.ad_slug,
        ac.name AS category_name, 
        a.asking_price, 
        a.user_name, 
        a.created_at
    FROM 
        ad_form a
    LEFT JOIN 
        ad_categories ac ON a.category = ac.id
    WHERE 
        a.status = 'pending'
    ORDER BY 
        a.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$pending_ads = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">Pending Ads for Approval</h2>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
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
                                Submitted On
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pending_ads)): ?>
                            <tr>
                                <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No pending ads found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pending_ads as $ad): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['ad_title']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['category_name'] ?? 'N/A'); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo date("F j, Y, g:i a", strtotime($ad['created_at'])); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center">
                                            <form action="" method="POST" class="inline-block">
                                                <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                                <button type="submit" name="status" value="live" class="text-green-600 hover:text-green-900 mr-3 font-semibold">Approve</button>
                                            </form>
                                            <form action="" method="POST" class="inline-block">
                                                <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                                <button type="submit" name="status" value="rejected" class="text-red-600 hover:text-red-900 mr-4 font-semibold">Reject</button>
                                            </form>
                                            
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <a href="/ads/<?php echo htmlspecialchars($ad['ad_slug'] ?? $ad['id']); ?>" target="_blank" class="text-gray-500 hover:text-gray-700" title="View Ad">
                                                <svg xmlns="http:
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
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
