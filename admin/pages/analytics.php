<?php
// /admin/pages/analytics.php
require_once __DIR__ . '/../../config/config.php';

// Fetch various statistics
$stats = [];

// Total Users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Total Ads
$result = $conn->query("SELECT COUNT(*) as count FROM ad_form");
$stats['total_ads'] = $result->fetch_assoc()['count'];

// Pending Ads
$result = $conn->query("SELECT COUNT(*) as count FROM ad_form WHERE status = 'pending'");
$stats['pending_ads'] = $result->fetch_assoc()['count'];

// Total Blog Posts
$result = $conn->query("SELECT COUNT(*) as count FROM blog_posts");
$stats['total_blogs'] = $result->fetch_assoc()['count'];

// Total Subscribers
$result = $conn->query("SELECT COUNT(*) as count FROM subscribers");
$stats['total_subscribers'] = $result->fetch_assoc()['count'];

// Total Ad Categories
$result = $conn->query("SELECT COUNT(*) as count FROM ad_categories");
$stats['total_ad_categories'] = $result->fetch_assoc()['count'];

?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Site Analytics</h2>
        <p class="mt-2 text-gray-600">An overview of your website's data.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <!-- Total Users -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Total Users</h3>
                <p class="text-4xl font-bold text-indigo-600 mt-2"><?php echo number_format($stats['total_users']); ?></p>
            </div>

            <!-- Total Ads -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Total Ads</h3>
                <p class="text-4xl font-bold text-indigo-600 mt-2"><?php echo number_format($stats['total_ads']); ?></p>
            </div>

            <!-- Pending Ads -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Pending Ads</h3>
                <p class="text-4xl font-bold text-yellow-600 mt-2"><?php echo number_format($stats['pending_ads']); ?></p>
            </div>

            <!-- Total Blog Posts -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Total Blog Posts</h3>
                <p class="text-4xl font-bold text-indigo-600 mt-2"><?php echo number_format($stats['total_blogs']); ?></p>
            </div>

            <!-- Total Subscribers -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Subscribers</h3>
                <p class="text-4xl font-bold text-indigo-600 mt-2"><?php echo number_format($stats['total_subscribers']); ?></p>
            </div>

            <!-- Total Ad Categories -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Ad Categories</h3>
                <p class="text-4xl font-bold text-indigo-600 mt-2"><?php echo number_format($stats['total_ad_categories']); ?></p>
            </div>
        </div>
    </div>
</div>
