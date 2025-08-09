<?php
// /admin-router.php

// This router handles all requests that begin with /admin

// The main router.php has already included the config, so we don't need to do it again.

// Get the request URI and remove the query string
$request_uri = strtok($_SERVER['REQUEST_URI'], '?');

// The main router has already confirmed the URL starts with /admin.
// We can now handle the specific admin routes.
switch (true) {
    // Route for the main admin dashboard. Handles /admin, /admin/, /admin/index, etc.
    case preg_match('/^\/admin(\/|\/index(\.php)?)?\/?$/', $request_uri):
        include __DIR__ . '/admin/admin-page.php';
        break;

    // Route for the admin login page
    case preg_match('/^\/admin\/login(\.php)?\/?$/', $request_uri):
        include __DIR__ . '/admin/login.php';
        break;

    // Route for admin pages loaded via AJAX (e.g., /admin/pages/users/view_users)
    case preg_match('/^\/admin\/pages\/([a-zA-Z0-9-_\/]+)(?:\.php)?$/', $request_uri, $matches):
        $page = $matches[1];
        // Security: Prevent directory traversal
        if (strpos($page, '..') !== false) {
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

    // If no admin route matches, show a 404
    default:
        header("HTTP/1.0 404 Not Found");
        echo '404 Not Found - Admin Page';
        break;
}
