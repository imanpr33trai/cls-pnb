<?php

require_once __DIR__ . '/../../config/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'], $_POST['action'])) {
    $report_id = (int)$_POST['report_id'];
    $action = $_POST['action'];

    if ($action === 'dismiss') {
        
        $stmt = $conn->prepare("DELETE FROM reported_ads WHERE id = ?");
        $stmt->bind_param("i", $report_id);
        if ($stmt->execute()) {
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Report dismissed successfully.</div>";
        } else {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error dismissing report.</div>";
        }
        $stmt->close();
    } elseif ($action === 'delete_ad' && isset($_POST['ad_id'])) {
        $ad_id = (int)$_POST['ad_id'];
        
        
        $stmt = $conn->prepare("DELETE FROM ad_form WHERE id = ?");
        $stmt->bind_param("i", $ad_id);
        if ($stmt->execute()) {
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Ad deleted successfully.</div>";
        } else {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error deleting ad.</div>";
        }
        $stmt->close();
    }
}


$stmt = $conn->prepare("
    SELECT 
        r.id AS report_id,
        r.ad_id,
        r.reason,
        r.created_at AS reported_at,
        a.ad_title,
        a.ad_slug,
        u.email AS reporter_email
    FROM 
        reported_ads r
    JOIN 
        ad_form a ON r.ad_id = a.id
    LEFT JOIN
        users u ON r.user_id = u.id
    ORDER BY 
        r.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$reported_ads = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">Reported Ads</h2>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-aut">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ad Title
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Reason for Report
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Reported By
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Reported On
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reported_ads)): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No reported ads found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reported_ads as $ad): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <a href="/ads/<?php echo htmlspecialchars($ad['ad_slug'] ?? $ad['ad_id']); ?>" target="_blank" class="text-blue-500 hover:underline">
                                            <?php echo htmlspecialchars($ad['ad_title']); ?>
                                        </a>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['reason']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['reporter_email'] ?? 'Guest'); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo date("F j, Y, g:i a", strtotime($ad['reported_at'])); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center">
                                            <form action="" method="POST" class="inline-block mr-2">
                                                <input type="hidden" name="report_id" value="<?php echo $ad['report_id']; ?>">
                                                <button type="submit" name="action" value="dismiss" class="text-green-600 hover:text-green-900 font-semibold">Dismiss</button>
                                            </form>
                                            <form action="" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this ad permanently?');">
                                                <input type="hidden" name="report_id" value="<?php echo $ad['report_id']; ?>">
                                                <input type="hidden" name="ad_id" value="<?php echo $ad['ad_id']; ?>">
                                                <button type="submit" name="action" value="delete_ad" class="text-red-600 hover:text-red-900 font-semibold">Delete Ad</button>
                                            </form>
                                        </div>
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