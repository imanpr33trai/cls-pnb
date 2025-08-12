<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admins_id'])) {
       header("Location: /admin/login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <link rel="stylesheet" href="../assets/css/output.css">
    <script defer src="/admin/js/script.js"></script>
    <style>
        .submenu { max-height: 0; overflow: hidden; opacity: 0; visibility: hidden; transition: max-height 0.3s ease-in-out, opacity 0.2s ease-in-out, visibility 0s linear 0.3s; }
        .submenu.open { max-height: 500px; opacity: 1; visibility: visible; transition: max-height 0.5s ease-in-out, opacity 0.3s ease-in-out, visibility 0s linear; }
        .rotate-180 { transform: rotate(180deg); transition: transform 0.3s ease-in-out; }
        
        
        #main-content { position: relative; z-index: 10; }
        #main-content.blur { filter: blur(4px); transition: filter 0.3s ease; }
        
        
        #sidebar { z-index: 40; }
        
        
        #edit-ad-modal, #delete-ad-modal, #edit-category-modal, #edit-blog-modal, #edit-blog-cat-modal{ z-index: 50; }
        
        body.modal-open { overflow: hidden; }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div  class="flex min-h-screen">
        <aside id="sidebar" class="bg-gray-800 text-white w-64 p-4 space-y-6 fixed top-0 left-0 h-full z-30 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold">Admin Panel</h3>
                <button class="md:hidden p-2" id="close-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto">
                <ul class="space-y-2">
                    <li>
                        <a href="/admin/admin-page.php" data-page="dashboard" class="tab-link flex items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                            <span class="mr-3">📊</span> Dashboard
                        </a>
                    </li>
                    
                        
                    
                    <li>
                        <button type="button" class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">📢</span> Ads</span>
                            <svg class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-0 space-y-1 overflow-hidden">
                            <li><a href="/admin/admin-page.php?page=view-ads" data-page="view-ads" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">View All Ads</a></li>
                            <li><a href="/admin/admin-page.php?page=pending-ads" data-page="pending-ads" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">Pending Ads</a></li>
                            <li><a href="/admin/admin-page.php?page=reported-ads" data-page="reported-ads" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">⚠️ Reported Ads</a></li>
                        </ul>
                    </li>

                    
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">🏷️</span> Ad Categories</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="category" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View Categories</a></li>
                            <li><a href="#" data-page="sub-cat" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View Subcategories</a></li>
                            <li><a href="#" data-page="add-ad-category" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Add New Category</a></li>
                        </ul>
                    </li>

                    
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">📝</span> Blogs</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="view-blogs" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View All Blogs</a></li>
                            <li><a href="#" data-page="pending-blogs" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Pending Blogs</a></li>
                        </ul>
                    </li>

                    
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">📚</span> Blog Categories</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="blog-cat" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View Categories</a></li>
                        </ul>
                    </li>

                    
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">👥</span> Users</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="view_users" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View All Users</a></li>
                        </ul>
                    </li>

                    
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">📧</span> Subscribers</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="view-subscribers" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View All Subscribers</a></li>
                            <li><a href="#" data-page="send-newsletter" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Send Newsletter</a></li>
                        </ul>
                    </li>

                    
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">⚙️</span> Settings</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="site-settings" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Site Settings</a></li>
                            <li><a href="#" data-page="email-settings" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Email Settings</a></li>
                            <li><a href="#" data-page="security-roles" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Roles & Permissions</a></li>
                        </ul>
                    </li>

                    
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">🛠️</span> Tools</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="analytics" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Analytics</a></li>
                            <li><a href="#" data-page="backup-restore" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">️ Backup</a></li>
                            <li><a href="#" data-page="export-data" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Export Data</a></li>
                        </ul>
                    </li>

                    
                    <li class="pt-4 border-t border-gray-700">
                        <a href="logout.php" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                           <span class="mr-3">🚪</span> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <div id="main-content" class="flex-1 md:ml-64 transition-all duration-300 ease-in-out">
            <header class="bg-white shadow p-4 flex justify-between items-center md:hidden sticky top-0 z-20">
                <h1 class="text-xl font-bold">Admin Dashboard</h1>
                <button id="open-sidebar" class="p-2">...</button>
            </header>
            <main id="content-area" class="p-8">
                <?php
                $page = $_GET['page'] ?? 'dashboard';
                $page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
                $pagePath = __DIR__ . '/pages/' . $page . '.php';
                if (file_exists($pagePath)) {
                    include $pagePath;
                } else {
                    include __DIR__ . '/pages/dashboard.php';
                }
                ?>
            </main>
        </div>
    </div>

    <div id="edit-ad-modal" class="fixed  inset-0 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Ad</h3>
                <div id="edit-ad-modal-content" class="mt-2 px-7 py-3">
                    
                </div>
                <div class="items-center px-4 py-3">
                    <button id="close-edit-modal" class="close-edit-modal px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    
<div id="edit-category-modal" class="fixed inset-0 mx-auto overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">Edit Category</p>
            <button id="close-edit-category-modal" class="cursor-pointer text-2xl font-bold close-edit-modal">&times;</button>
        </div>
        <div id="edit-category-modal-content">Loading form...</div>
    </div>
</div>


<div id="edit-blog-modal" class="fixed inset-0 mx-auto overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">Edit Blog Post</p>
            <button id="close-edit-blog-modal" class="cursor-pointer text-2xl font-bold close-edit-modal">&times;</button>
        </div>
        <div id="edit-blog-modal-content">Loading form...</div>
    </div>
</div>


<div id="edit-blog-cat-modal" class="fixed inset-0 mx-auto overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">Edit Blog Category</p>
            <button id="close-edit-blog-cat-modal" class="cursor-pointer text-2xl font-bold close-edit-modal">&times;</button>
        </div>
        <div id="edit-blog-cat-modal-content">Loading form...</div>
    </div>
</div>

    
    <div id="delete-ad-modal" class="fixed inset-0 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Delete Ad</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this ad? This action cannot be undone.</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Delete

                    </button>
                    <button id="cancel-delete-btn" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-auto shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 ml-3">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    




    






</body>

</html>