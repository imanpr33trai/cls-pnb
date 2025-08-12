<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function isAdminAuthenticated()
{
    return isset($_SESSION['admins_id']);
}

$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
switch (true) {
    case preg_match('#^/admin/login/?$#', $request_uri):
        if (isAdminAuthenticated()) {
            header('Location: /admin');
            exit;
        }
        include __DIR__ . '/admin/login.php';
        break;

    case preg_match('#^/admin/pages/([a-zA-Z0-9-_]+)$#', $request_uri, $matches):
        if (!isAdminAuthenticated()) {
            http_response_code(401);
            echo 'Authentication required.';
            exit;
        }

        $page = $matches[1];
        $page_file = __DIR__ . '/admin/pages/' . $page . '.php';


        $allowed_pages = [
            'dashboard',
            'view-ads',
            'pending-ads',
            'reported-ads',
            'category',
            'add-ad-category',
            'sub-cat',
            'view-blogs',
            'pending-blogs',
            'blog-cat',
            'view_users',
            'view-subscribers',
            'send-newsletter',
            'site-settings',
            'email-settings',
            'security-roles'
        ];


        if (in_array($page, $allowed_pages) && file_exists($page_file)) {
            include $page_file;
        } else {
            http_response_code(404);
            echo 'Page content not found.';
        }
        break;

    case preg_match('#^/admin(/([a-zA-Z0-9-_]+))?/?$#', $request_uri, $matches):
        if (!isAdminAuthenticated()) {
            header('Location: /admin/login');
            exit;
        }

        $page = $matches[2] ?? 'dashboard';

        $_GET['page'] = $page;

        include __DIR__ . '/admin/admin-page.php';
        break;

    default:
        http_response_code(404);
        include __DIR__ . '/admin/pages/404.php';
        break;
}
