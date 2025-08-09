<?php
require_once __DIR__ . '/../../config/config.php';

// --- Fetching Data ---

// Site Settings
$setting = $conn->query("SELECT * FROM site_settings WHERE id = 1")->fetch_assoc();

// Total Counts
$total_ads = $conn->query("SELECT COUNT(*) as count FROM ad_form")->fetch_assoc()['count'];
$total_blogs = $conn->query("SELECT COUNT(*) as count FROM blog_posts")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$ad_categories_count = $conn->query("SELECT COUNT(*) as count FROM ad_categories")->fetch_assoc()['count'];
$blog_categories_count = $conn->query("SELECT COUNT(*) as count FROM blog_categories")->fetch_assoc()['count'];

// Counts for "Today"
$today_date = date('Y-m-d');
$ads_today = $conn->query("SELECT COUNT(*) as count FROM ad_form WHERE DATE(created_at) = '$today_date'")->fetch_assoc()['count'];
$blogs_today = $conn->query("SELECT COUNT(*) as count FROM blog_posts WHERE DATE(created_at) = '$today_date'")->fetch_assoc()['count'];
$pending_ads = $conn->query("SELECT COUNT(*) as count FROM ad_form WHERE status = 'pending'")->fetch_assoc()['count'];

// Mock Data for Subscribers
$newsletter_subscribers = [
    ['name' => 'Alice', 'email' => 'alice@example.com', 'date' => '2025-08-08'],
    ['name' => 'Bob', 'email' => 'bob@example.com', 'date' => '2025-08-08'],
    ['name' => 'Charlie', 'email' => 'charlie@example.com', 'date' => '2025-08-07'],
];
$total_subscribers = count($newsletter_subscribers);
$subscribers_today = count(array_filter($newsletter_subscribers, fn($sub) => $sub['date'] === $today_date));

?>

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Dashboard</h2>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="lg:col-span-4 bg-white shadow-lg rounded-lg p-6 flex items-center">
            <?php if (!empty($setting['header_logo'])): ?>
                <img src="../admin/<?= htmlspecialchars($setting['header_logo']) ?>" alt="Site Logo" class="h-20 w-20 mr-6 rounded-full border border-gray-200">
            <?php endif; ?>
            <div>
                <h3 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($setting['site_name'] ?? 'Site Name') ?></h3>
                <p class="text-gray-600">Welcome to the admin panel.</p>
            </div>
        </div>
        <!-- Total Ads -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h4 class="text-gray-500 font-medium">Total Ads</h4>
                    <svg class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= $total_ads ?> Active Ads</p>
            </div>
            <p class="text-sm text-gray-400 mt-4">+120 this week</p>
        </div>
        <!-- Total Blogs -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h4 class="text-gray-500 font-medium">Total Blogs</h4>
                    <svg class="h-6 w-6 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747l9 5.747 9-5.747-9-5.747-9 5.747z"/></svg>
                </div>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= $total_blogs ?> Blogs Published</p>
            </div>
            <p class="text-sm text-gray-400 mt-4">+6 this week</p>
        </div>
        <!-- Total Users -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h4 class="text-gray-500 font-medium">Total Users</h4>
                    <svg class="h-6 w-6 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= $total_users ?> Registered Users</p>
            </div>
            <p class="text-sm text-gray-400 mt-4">+79 this week</p>
        </div>
        <!-- Total Subscribers -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h4 class="text-gray-500 font-medium">Total Subscribers</h4>
                    <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= $total_subscribers ?> Subscribers</p>
            </div>
            <p class="text-sm text-gray-400 mt-4">+34 new</p>
        </div>
    </div>

    <!-- Actionable Items Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- New Ad Posts -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h4 class="text-gray-500 font-medium mb-2">New Ad Posts (Today)</h4>
            <p class="text-2xl font-bold text-gray-800">➕ <?= $ads_today ?> New Ads Posted</p>
            <button class="mt-4 w-full bg-indigo-500 text-white py-2 rounded-lg hover:bg-indigo-600 transition-colors">Review Pending Ads</button>
        </div>
        <!-- New Blog Posts -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h4 class="text-gray-500 font-medium mb-2">New Blog Posts (Today)</h4>
            <p class="text-2xl font-bold text-gray-800">📝 <?= $blogs_today ?> New Blogs Submitted</p>
            <button class="mt-4 w-full bg-teal-500 text-white py-2 rounded-lg hover:bg-teal-600 transition-colors">Approve Blogs</button>
        </div>
        <!-- New Subscribers -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h4 class="text-gray-500 font-medium mb-2">New Subscribers (Today)</h4>
            <p class="text-2xl font-bold text-gray-800">📬 <?= $subscribers_today ?> New Newsletter Subscribed</p>
            <button class="mt-4 w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition-colors">View Emails</button>
        </div>
        <!-- Ad Categories -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h4 class="text-gray-500 font-medium mb-2">Ad Categories</h4>
            <p class="text-2xl font-bold    
            
            text-gray-800">🏷️ <?= $ad_categories_count ?> Categories</p>
            <button class="mt-4 w-full bg-gray-700 text-white py-2 rounded-lg hover:bg-gray-800 transition-colors">Manage Ad Categories</button>
        </div>
        <!-- Blog Categories -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h4 class="text-gray-500 font-medium mb-2">Blog Categories</h4>
            <p class="text-2xl font-bold text-gray-800">📚 <?= $blog_categories_count ?> Categories</p>
            <button class="mt-4 w-full bg-gray-700 text-white py-2 rounded-lg hover:bg-gray-800 transition-colors">Edit Blog Categories</button>
        </div>
        <!-- Pending Ad Approvals -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h4 class="text-gray-500 font-medium mb-2">Pending Ad Approvals</h4>
            <p class="text-2xl font-bold text-gray-800">⏳ <?= $pending_ads ?> Ads Awaiting Review</p>
            <button class="mt-4 w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition-colors">Go to Moderation</button>
        </div>
    </div>
</div>