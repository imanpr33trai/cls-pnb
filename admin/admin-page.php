<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admins_id'])) {
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
    <!-- <script defer src="js/script.js"></script> -->
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

    <div id="edit-ad-modal" class="fixed  inset-0 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Ad</h3>
                <div id="edit-ad-modal-content" class="mt-2 px-7 py-3">
                    <!-- Form will be loaded here -->
                </div>
                <div class="items-center px-4 py-3">
                    <button id="close-edit-modal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Ad Modal -->
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

    <!-- Initialize the admin panel -->
<!-- This is the <script> block in /admin/admin-page.php -->

<!-- This is the <script> block in /admin/admin-page.php -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById("sidebar");
    const openSidebarBtn = document.getElementById("open-sidebar");
    const closeSidebarBtn = document.getElementById("close-sidebar");
    const contentArea = document.getElementById("content-area");
    const tabLinks = document.querySelectorAll(".tab-link");
    const menuToggles = document.querySelectorAll(".menu-toggle");
    const activeClass = "bg-gray-700";
const mainContent = document.getElementById("main-content");
        const editAdModal = document.getElementById("edit-ad-modal");
    const editAdModalContent = document.getElementById("edit-ad-modal-content");
    const closeEditModalBtn = document.getElementById("close-edit-modal");
    const deleteAdModal = document.getElementById("delete-ad-modal");
    const confirmDeleteBtn = document.getElementById("confirm-delete-btn"); 
    const cancelDeleteBtn = document.getElementById("cancel-delete-btn");
    let adToDeleteId = null;


    // Sidebar and Menu toggle logic (your existing code is good)
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

    /**
     * Main function to load content into the dashboard dynamically.
     */
    async function loadPage(page, pushState = true) {
        contentArea.innerHTML = '<div class="text-center p-8">Loading...</div>';
        
        // THE FIX: Fetch from the dedicated AJAX endpoint your router now understands
        const fetchUrl = `/admin/pages/${page}`;

        try {
            const response = await fetch(fetchUrl);
            if (!response.ok) throw new Error(`Error: ${await response.text() || 'Page content not found'}`);

            const html = await response.text();
            contentArea.innerHTML = html;

            if (pushState) {
                // THE FIX: Push a clean user-facing URL to the browser history
                const newUrl = page === "dashboard" ? `/admin` : `/admin/${page}`;
                history.pushState({ page: page }, '', newUrl);
            }
            document.title = `Admin | ${page.charAt(0).toUpperCase() + page.slice(1).replace('-', ' ')}`;
            updateActiveLink(page);
        } catch (error) {
            console.error("Error loading page:", error);
            contentArea.innerHTML = `<div class="p-4 bg-red-100 text-red-700 rounded">${error.message}</div>`;
        }
    }

    /**
     * Updates the active state on sidebar links.
     */
    function updateActiveLink(page) {
        tabLinks.forEach(link => link.classList.remove(activeClass));
        const activeLink = document.querySelector(`.tab-link[data-page="${page}"]`);
        if (activeLink) {
            activeLink.classList.add(activeClass);
            const parentSubmenu = activeLink.closest('.submenu');
            if (parentSubmenu && !parentSubmenu.classList.contains('open')) {
                parentSubmenu.previousElementSibling.click();
            }
        }
    }

    // --- Main Event Listener for all sidebar links ---
  sidebar.addEventListener('click', (e) => {
        const link = e.target.closest('.tab-link');
        if (link) { e.preventDefault(); loadPage(link.dataset.page); }
    });

    // --- Browser Back/Forward Button Support ---
 window.addEventListener('popstate', (e) => loadPage(e.state?.page || 'dashboard', false));

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


    // --- Load Initial Page Content ---
    // The PHP router now passes the initial page name via $_GET['page']
    const initialPage = '<?php echo htmlspecialchars($_GET["page"] ?? "dashboard"); ?>';
    updateActiveLink(initialPage);
    
    // The initial HTML is already loaded by the PHP include at the top of the page,
    // so we don't need to call loadPage() on the first visit.
});
</script>
</body>

</html>