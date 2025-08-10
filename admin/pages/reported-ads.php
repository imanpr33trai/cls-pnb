<?php
// /admin/pages/reported-ads.php
require_once __DIR__ . '/../../config/config.php';

// Fetch all ads with 'reported' status
$stmt = $conn->prepare("
    SELECT 
        a.id, 
        a.ad_title,
        a.ad_slug,
        r.reason,
        r.created_at AS reported_at,
        u.email AS reporter_email
    FROM 
        reported_ads r
    JOIN 
        ad_form a ON r.ad_id = a.id
    LEFT JOIN
        users u ON r.user_id = u.id
    WHERE
        a.status = 'reported'
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
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
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
                                Date
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
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['ad_title']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['reason']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($ad['reporter_email'] ?? 'N/A'); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo date("F j, Y", strtotime($ad['reported_at'])); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center">
                                            <form action="/admin/pages/pending-ads.php" method="POST" class="inline-block">
                                                <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                                <button type="submit" name="status" value="live" class="text-green-600 hover:text-green-900 mr-3 font-semibold">Dismiss</button>
                                            </form>
                                            <form action="/admin/pages/pending-ads.php" method="POST" class="inline-block">
                                                <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                                <button type="submit" name="status" value="rejected" class="text-red-600 hover:text-red-900 mr-4 font-semibold">Remove Ad</button>
                                            </form>
                                            
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <a href="/ads/<?php echo htmlspecialchars($ad['ad_slug'] ?? $ad['id']); ?>" target="_blank" class="text-gray-500 hover:text-gray-700" title="View Ad">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
