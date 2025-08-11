<?php
// /admin/util/delete_ad.php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ad_id'])) {
    $ad_id = (int)$_POST['ad_id'];

    if ($ad_id > 0) {
        // First, get the image filename to delete it from the server
        $stmt = $conn->prepare("SELECT image FROM ad_form WHERE id = ?");
        $stmt->bind_param("i", $ad_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $image_path = __DIR__ . '/../../assets/uploads/' . $row['image'];
            if (!empty($row['image']) && file_exists($image_path)) {
                unlink($image_path);
            }
        }
        $stmt->close();

        // Now, delete the ad from the database
        $delete_stmt = $conn->prepare("DELETE FROM ad_form WHERE id = ?");
        $delete_stmt->bind_param("i", $ad_id);
        if ($delete_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Ad deleted successfully!';
        } else {
            $response['message'] = 'Error deleting ad: ' . $conn->error;
        }
        $delete_stmt->close();
    } else {
        $response['message'] = 'Invalid Ad ID.';
    }
} else {
    $response['message'] = 'Invalid request method or Ad ID not provided.';
}

echo json_encode($response);
?>



<?php
// /admin/admin-page.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Ensure an admin is logged in.
// Replace 'admin_id' with your actual session variable if it's different.
if (!isset($_SESSION['admin_id'])) { 
    header("Location: /admin/login");
    exit;
}

// The router has already included config.php, but it's safe to include again.
include_once(__DIR__ . '/../config/config.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/output.css">
    <style>
        .submenu { max-height: 0; overflow: hidden; opacity: 0; visibility: hidden; transition: max-height 0.3s ease-in-out, opacity 0.2s ease-in-out, visibility 0s linear 0.3s; }
        .submenu.open { max-height: 500px; opacity: 1; visibility: visible; transition: max-height 0.5s ease-in-out, opacity 0.3s ease-in-out, visibility 0s linear; }
        .rotate-180 { transform: rotate(180deg); transition: transform 0.3s ease-in-out; }
        
        /* Ensure main content has a base z-index */
        #main-content { position: relative; z-index: 10; }
        #main-content.blur { filter: blur(4px); transition: filter 0.3s ease; }
        
        /* Sidebar needs to be above main content */
        #sidebar { z-index: 40; }
        
        /* Modals need to be on top of EVERYTHING */
        #edit-ad-modal, #delete-ad-modal { z-index: 50; }
        
        body.modal-open { overflow: hidden; }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar HTML (Unchanged and Correct) -->
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
                    
                    <!-- Ads -->
                    <!-- Ads -->
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

                    <!-- Ad Categories -->
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">🏷️</span> Ad Categories</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="category" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View Categories</a></li>
                            <li><a href="#" data-page="add-ad-category" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">Add New</a></li>
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
                        </ul>
                    </li>

                    <!-- Users -->
                    <li>
                        <button class="menu-toggle w-full flex justify-between items-center px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-left">
                            <span class="flex items-center"><span class="mr-3">👥</span> Users</span>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="submenu pl-8 mt-2 space-y-2">
                            <li><a href="#" data-page="view_users" class="tab-link block px-4 py-2 rounded-md hover:bg-gray-700">View All Users</a></li>
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
    
    <!-- Edit Ad Modal -->
    <div id="edit-ad-modal" class="fixed inset-0  bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold">Edit Ad</p>
                <div id="close-edit-modal" class="cursor-pointer z-50">&times;</div>
            </div>
            <div id="edit-ad-modal-content">Loading form...</div>
        </div>
    </div>

    <!-- Delete Ad Modal -->
    <div id="delete-ad-modal" class="fixed inset-0  bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-1/4 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Ad</h3>
                <div class="mt-2 px-7 py-3"><p class="text-sm text-gray-500">Are you sure? This action cannot be undone.</p></div>
                <div class="items-center px-4 py-3">
                    <button id="confirm-delete-btn" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-red-700">Delete</button>
                    <button id="cancel-delete-btn" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-auto ml-2 shadow-sm hover:bg-gray-300">Cancel</button>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- CACHE DOM ELEMENTS ---
    const sidebar = document.getElementById("sidebar");
    const openSidebarBtn = document.getElementById("open-sidebar");
    const closeSidebarBtn = document.getElementById("close-sidebar");
    const contentArea = document.getElementById("content-area");
    const mainContent = document.getElementById("main-content");
    const menuToggles = document.querySelectorAll(".menu-toggle");
    const activeClass = "bg-gray-700";

    const editAdModal = document.getElementById("edit-ad-modal");
    const editAdModalContent = document.getElementById("edit-ad-modal-content");
    const closeEditModalBtn = document.getElementById("close-edit-modal");
    const deleteAdModal = document.getElementById("delete-ad-modal");
    const confirmDeleteBtn = document.getElementById("confirm-delete-btn");
    const cancelDeleteBtn = document.getElementById("cancel-delete-btn");
    let adToDeleteId = null;

    // --- SIDEBAR & MENU LOGIC ---
    if (openSidebarBtn) openSidebarBtn.addEventListener("click", () => sidebar.classList.remove("-translate-x-full"));
    if (closeSidebarBtn) closeSidebarBtn.addEventListener("click", () => sidebar.classList.add("-translate-x-full"));
    menuToggles.forEach(clickedToggle => {
        clickedToggle.addEventListener("click", () => {
            const clickedSubmenu = clickedToggle.nextElementSibling;
            if (!clickedSubmenu || !clickedSubmenu.classList.contains('submenu')) return;
            menuToggles.forEach(otherToggle => {
                if (otherToggle !== clickedToggle) {
                    const otherSubmenu = otherToggle.nextElementSibling;
                    if (otherSubmenu && otherSubmenu.classList.contains('open')) {
                        otherSubmenu.classList.remove('open');
                        otherToggle.querySelector('svg').classList.remove('rotate-180');
                    }
                }
            });
            clickedSubmenu.classList.toggle("open");
            clickedToggle.querySelector("svg").classList.toggle("rotate-180");
        });
    });
    
    // --- PAGE LOADING LOGIC ---
    async function loadPage(page, pushState = true) {
        // =================================================================
        //  NEW: CLEANUP LOGIC - Close any open modals before loading a new page
        // =================================================================
        allModals.forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                closeModal(modal);
            }
        });
        const fetchUrl = `/admin/pages/${page}`;
        contentArea.innerHTML = '<div class="text-center p-8">Loading...</div>';
        try {
            const response = await fetch(`<?php echo $base_url; ?>admin/pages/${page}`);
            if (!response.ok) throw new Error(`Error ${response.status}: Page not found.`);
            const html = await response.text();
            contentArea.innerHTML = html;
            if (pushState) {
                const newUrl = page === "dashboard" ? `<?php echo $base_url; ?>admin` : `<?php echo $base_url; ?>admin/${page}`;
                history.pushState({ page }, '', newUrl);
            }
            updateActiveLink(page);
        } catch (error) {
            contentArea.innerHTML = `<div class="p-4 bg-red-100 text-red-700 rounded">${error.message}</div>`;
        }
    }

    
    function updateActiveLink(page) { /* ... same as before ... */ }
    sidebar.addEventListener('click', (e) => {
        const link = e.target.closest('.tab-link');
        if (link) { e.preventDefault(); loadPage(link.dataset.page); }
    });
    window.addEventListener('popstate', (e) => loadPage(e.state?.page || 'dashboard', false));
    
    // =================================================================
    //  EVENT DELEGATION FOR DYNAMIC CONTENT
    // =================================================================
    mainContent.addEventListener('click', function(event) {
        const target = event.target;
        
        // --- HANDLE EDIT MODAL BUTTON CLICK ---
        if (target.matches('.open-edit-modal')) {
            openEditModal(target.dataset.adId);
        }

        // --- HANDLE DELETE MODAL BUTTON CLICK ---
        if (target.matches('.open-delete-modal')) {
            openDeleteModal(target.dataset.adId);
        }

        // --- HANDLE EDIT FORM SUBMISSION ---
        if (target.matches('#edit-ad-form button[type="submit"]')) {
            const form = target.closest('#edit-ad-form');
            if (form) {
                event.preventDefault();
                submitEditForm(form);
            }
        }
    });

    // --- MODAL FUNCTIONS ---
    function openModal(modal) {
        modal.classList.remove("hidden");
        mainContent.classList.add("blur");
        document.body.classList.add("modal-open");
    }
    function closeModal(modal) {
        modal.classList.add("hidden");
        mainContent.classList.remove("blur");
        document.body.classList.remove("modal-open");
    }

    async function openEditModal(adId) {
        if (!adId) return;
        editAdModalContent.innerHTML = "Loading form...";
        openModal(editAdModal);
        try {
            const response = await fetch(`<?php echo $base_url; ?>admin/util/get_ad_form.php?ad_id=${adId}`);
            if (!response.ok) throw new Error('Failed to load edit form.');
            editAdModalContent.innerHTML = await response.text();
        } catch (error) { editAdModalContent.innerHTML = `<p class="text-red-500">${error.message}</p>`; }
    }

    if (closeEditModalBtn) closeEditModalBtn.addEventListener("click", () => closeModal(editAdModal));

    async function submitEditForm(form) {
        try {
            const response = await fetch('<?php echo $base_url; ?>admin/util/edit-ad.php', { method: 'POST', body: new FormData(form) });
            const result = await response.json();
            if (result.success) {
                alert('Ad updated successfully!');
                closeModal(editAdModal);
                loadPage(new URLSearchParams(window.location.search).get('page') || 'view-ads');
            } else { throw new Error(result.message || 'Failed to update ad.'); }
        } catch(error) { alert(error.message); }
    }

    function openDeleteModal(adId) { adToDeleteId = adId; openModal(deleteAdModal); }
    if (cancelDeleteBtn) cancelDeleteBtn.addEventListener("click", () => closeModal(deleteAdModal));

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener("click", async () => {
            if (!adToDeleteId) return;
            try {
                const formData = new FormData();
                formData.append('ad_id', adToDeleteId);
                const response = await fetch(`<?php echo $base_url; ?>admin/util/delete_ad.php`, { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    alert('Ad deleted successfully!');
                    closeModal(deleteAdModal);
                    loadPage(new URLSearchParams(window.location.search).get('page') || 'view-ads');
                } else { throw new Error(result.message || 'Failed to delete ad.'); }
            } catch(error) { alert(error.message); }
        });
    }

    // --- INITIAL PAGE LOAD ---
    const initialPage = '<?php echo htmlspecialchars($_GET["page"] ?? "dashboard"); ?>';
    updateActiveLink(initialPage);
});
</script>
</body>
</html>