<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    // Use an absolute path for the redirect
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
    <style>
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0.25, 0.1, 0.25, 1);
        }
        .submenu.open {
            max-height: 1000px; /* Adjust as needed */
        }
        .rotate-180 {
            transform: rotate(180deg);
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
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
                        <a href="#" data-page="dashboard" class="tab-link flex items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                            <span class="mr-3">📊</span> Dashboard
                        </a>
                    </li>
                    
                    <!-- Ads -->
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">📢</span> Ads</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="view-ads" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View All Ads</a></li>
                            <li><a href="#" data-page="pending-ads" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Pending Ads</a></li>
                            <li><a href="#" data-page="reported-ads" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">⚠️ Reported Ads</a></li>
                        </ul>
                    </li>

                    <!-- Ad Categories -->
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">🏷️</span> Ad Categories</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="category" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View Categories</a></li>
                            <li><a href="#" data-page="sub-cat" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View Subcategories</a></li>
                        </ul>
                    </li>

                    <!-- Blogs -->
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

                    <!-- Blog Categories -->
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">📚</span> Blog Categories</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="blog-cat" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View Categories</a></li>
                            <li><a href="#" data-page="blog-sub-cat" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View Subcategories</a></li>
                        </ul>
                    </li>

                    <!-- Users -->
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">👥</span> Users</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="users/view_users" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View All Users</a></li>
                            <li><a href="#" data-page="reported-users" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">️ Reported Users</a></li>
                        </ul>
                    </li>

                    <!-- Subscribers -->
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

                    <!-- Settings -->
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

                    <!-- Tools -->
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

                    <!-- Logout -->
                    <li class="pt-4 border-t border-gray-700">
                        <a href="logout.php" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                           <span class="mr-3">🚪</span> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <div class="flex-1 md:ml-64 transition-all duration-300 ease-in-out">
             <header class="bg-white shadow p-4 flex justify-between items-center md:hidden sticky top-0 z-20">
                <h1 class="text-xl font-bold">Admin Dashboard</h1>
                <button id="open-sidebar" class="p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </header>

            <main id="content-area" class="p-8">
            <!-- Dynamic content will be loaded here -->
            <?php
            // These blocks handle form submissions from the loaded pages.
            require_once __DIR__ . '/../config/config.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
                $name = trim($_POST['category_name']);
                $image_name = '';

                if (!empty($_FILES['category_image']['name'])) {
                    $upload_dir = '../assets/uploads/';
                    $image_name = time() . '_' . basename($_FILES['category_image']['name']);
                    $target_path = $upload_dir . $image_name;
                    move_uploaded_file($_FILES['category_image']['tmp_name'], $target_path);
                }

                $stmt = $conn->prepare("INSERT INTO ad_categories (name, image) VALUES (?, ?)");
                $stmt->bind_param("ss", $name, $image_name);
                if ($stmt->execute()) {
                    echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Category added!</div>";
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subcategory'])) {
                $category_id = $_POST['category_id'];
                $title = trim($_POST['subcategory_title']);

                $stmt = $conn->prepare("INSERT INTO ad_subcategories (category_id, title) VALUES (?, ?)");
                $stmt->bind_param("is", $category_id, $title);
                if ($stmt->execute()) {
                    echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Subcategory added!</div>";
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category_1'])) {
                $category_namess = trim($_POST['category_name_blog']);

                if (!empty($category_namess)) {
                    $stmt = $conn->prepare("INSERT INTO blog_categories (name) VALUES (?)");
                    $stmt->bind_param("s", $category_namess);
                    if ($stmt->execute()) {
                        $success = "Category added successfully!";
                        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ $success</div>";
                    } else {
                        $error = "Error: " . $stmt->error;
                        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ $error</div>";
                    }
                    $stmt->close();
                } else {
                    $error = "Please enter a category name.";
                    echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ $error</div>";
                }
            }
            ?>
        </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const openSidebarBtn = document.getElementById('open-sidebar');
            const closeSidebarBtn = document.getElementById('close-sidebar');
            const contentArea = document.getElementById('content-area');
            const links = document.querySelectorAll('.tab-link');
            const menuToggles = document.querySelectorAll('.menu-toggle');
            const activeClass = 'bg-gray-700';

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
            }

            if (openSidebarBtn) {
                openSidebarBtn.addEventListener('click', openSidebar);
            }
            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', closeSidebar);
            }

            menuToggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const submenu = toggle.nextElementSibling;
                    const icon = toggle.querySelector('svg');
                    
                    // Close other open submenus
                    document.querySelectorAll('.submenu.open').forEach(openSubmenu => {
                        if (openSubmenu !== submenu) {
                            openSubmenu.classList.remove('open');
                            openSubmenu.previousElementSibling.querySelector('svg').classList.remove('rotate-180');
                        }
                    });

                    submenu.classList.toggle('open');
                    icon.classList.toggle('rotate-180');
                });
            });

            // Placeholder for function that might be loaded with a page
            let attachDashboardHandlers = () => {};

            function loadPage(page) {
                // Clear previous content and show loading indicator
                contentArea.innerHTML = '<div class="text-center py-10 text-gray-500">Loading...</div>';
                
                fetch(`pages/${page}.php`)
                    .then(res => {
                        if (!res.ok) throw new Error(`Network response was not ok: ${res.statusText}`);
                        return res.text();
                    })
                    .then(data => {
                        // Prepend existing form submission messages to the new content
                        const existingMessages = document.querySelectorAll('#content-area > .bg-green-100, #content-area > .bg-red-100');
                        let messagesHTML = '';
                        existingMessages.forEach(msg => messagesHTML += msg.outerHTML);
                        
                        contentArea.innerHTML = messagesHTML + data;

                        if (page === 'dashboard' && typeof attachDashboardHandlers === 'function') {
                            attachDashboardHandlers();
                        }
                    })
                    .catch(error => {
                        contentArea.innerHTML = `<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                                                    <strong>Error:</strong> Failed to load page content. Please check the console for details.
                                                 </div>`;
                        console.error('Error loading page:', error);
                    });
            }

            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    links.forEach(l => l.classList.remove(activeClass));
                    this.classList.add(activeClass);
                    const page = this.getAttribute('data-page');
                    if(page) {
                        loadPage(page);
                    }
                    
                    if (window.innerWidth < 768) {
                        closeSidebar();
                    }
                });
            });

            // Initial page load
            const initialLink = document.querySelector('.tab-link[data-page="dashboard"]');
            if (initialLink) {
                initialLink.classList.add(activeClass);
                loadPage('dashboard');
            }
        });
    </script>
</body>

</html>
        