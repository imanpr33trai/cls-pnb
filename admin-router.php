<?php
// /admin-router.php (Final version with clean URL support)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if an admin is logged in
function isAdminAuthenticated() {
    return isset($_SESSION['admin_id']); // Use your actual admin session variable
}

$request_uri = strtok($_SERVER['REQUEST_URI'], '?');

// --- Main Admin Routing Logic ---
switch (true) {
    // 1. Handle the login page
    case preg_match('#^/admin/login/?$#', $request_uri):
        if (isAdminAuthenticated()) {
            header('Location: /admin');
            exit;
        }
        include __DIR__ . '/admin/login.php';
        break;

    // 2. Handle AJAX requests for page content (e.g., /admin/pages/category)
    case preg_match('#^/admin/pages/([a-zA-Z0-9-_]+)$#', $request_uri, $matches):
        if (!isAdminAuthenticated()) {
            http_response_code(401);
            echo 'Authentication required.';
            exit;
        }
        
        $page = $matches[1];
        $page_file = __DIR__ . '/admin/pages/' . $page . '.php';

        // Security: Whitelist allowed pages

$allowed_pages = [
    'dashboard', 'view-ads', 'pending-ads', 'reported-ads', 'category', 'add-ad-category', 'sub-cat',
    'view-blogs', 'pending-blogs', 'blog-cat', 'users/view_users',
    'view-subscribers', 'send-newsletter', 'site-settings', 'email-settings',
    'security-roles', 'analytics', 'backup-restore', 'export-data'
];

        
        if (in_array($page, $allowed_pages) && file_exists($page_file)) {
            include $page_file; // Respond with just the content
        } else {
            http_response_code(404);
            echo 'Page content not found.';
        }
        break;
        
    // =================================================================
    // 3. NEW: Handle clean admin URLs like /admin/category or just /admin
    // This is the primary router for all admin page views.
    // =================================================================
    case preg_match('#^/admin(/([a-zA-Z0-9-_]+))?/?$#', $request_uri, $matches):
        if (!isAdminAuthenticated()) {
            header('Location: /admin/login');
            exit;
        }
        
        // The second part of the URL ($matches[2]) is our page name.
        // If it's not present (i.e., the user visited just /admin), default to 'dashboard'.
        $page = $matches[2] ?? 'dashboard';
        
        // Pass the page name to the admin shell via a $_GET variable.
        // The JavaScript will use this to know which content to load initially.
        $_GET['page'] = $page;

        // Always load the main admin "shell" page.
        include __DIR__ . '/admin/admin-page.php';
        break;
        
    default:
        http_response_code(404);
        include __DIR__ . '/admin/pages/404.php'; // A proper 404 page
        break;
}
?>