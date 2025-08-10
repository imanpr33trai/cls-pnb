<?php
// /admin-router.php
// Get the request URI and remove the query string
$request_uri = strtok($_SERVER['REQUEST_URI'], '?');

// Add authentication check function at the top
function isAdminAuthenticated() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['admins_id']) && $_SESSION['admins_id'] !== null;
}

// Define the list of valid admin pages that can be loaded.
// This prevents loading arbitrary files.
$allowed_pages = [
    'dashboard', 'view-ads', 'pending-ads', 'reported-ads', 'category', 'add-ad-category', 'sub-cat',
    'view-blogs', 'pending-blogs', 'blog-cat', 'users/view_users',
    'view-subscribers', 'send-newsletter', 'site-settings', 'email-settings',
    'security-roles', 'analytics', 'backup-restore', 'export-data'
];

// The main router has already confirmed the URL starts with /admin.
// We can now handle the specific admin routes.
switch (true) {
    // Route for the admin login page (don't require authentication)
    case preg_match('/^\/admin\/login(\.php)?\/?$/', $request_uri):
        if (isAdminAuthenticated()) {
            header('Location: /admin');
            exit;
        }
        include __DIR__ . '/admin/login.php';
        break;
        
    // Handle root admin path, which is the dashboard
    case in_array($request_uri, ['/admin', '/admin/']):
        if (!isAdminAuthenticated()) {
            header('Location: /admin/login');
            exit;
        }
        include __DIR__ . '/admin/admin-page.php';
        break;

    // Route for admin pages loaded via AJAX (e.g., /admin/pages/dashboard)
    case preg_match('/^\/admin\/pages\/([a-zA-Z0-9-_\/]+)(?:\.php)?$/', $request_uri, $matches):
        if (!isAdminAuthenticated()) {
            // AJAX requests should already be authenticated, but check just in case
            header('HTTP/1.1 401 Unauthorized');
            echo '401 Unauthorized';
            exit;
        }
        $page = $matches[1];
        // Security: Prevent directory traversal and check against allowed pages
        if (strpos($page, '..') !== false || !in_array($page, $allowed_pages)) {
            header("HTTP/1.0 400 Bad Request");
            echo '400 Bad Request';
            break;
        }
        $page_file = __DIR__ . '/admin/pages/' . $page . '.php';
        if (file_exists($page_file)) {
            include $page_file;
        } else {
            header("HTTP/1.0 404 Not Found");
            echo '404 Not Found';
        }
        break;

    // Route for "clean" admin URLs (e.g., /admin/view-ads)
    // These should load the main admin page, and JS will handle fetching the content.
    case preg_match('/^\/admin\/([a-zA-Z0-9-_\/]+)$/', $request_uri, $matches):
        if (!isAdminAuthenticated()) {
            header('Location: /admin/login');
            exit;
        }
        $page = $matches[1];
        if (in_array($page, $allowed_pages)) {
            include __DIR__ . '/admin/admin-page.php';
        } else {
            header("HTTP/1.0 404 Not Found");
            echo '404 Not Found - Admin Page';
        }
        break;

    // If no route matches, show a 404
    default:
        header("HTTP/1.0 404 Not Found");
        echo '404 Not Found - Admin Page';
        break;
}