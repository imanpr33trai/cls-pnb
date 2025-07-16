<?php

// router.php - Handles all routing for the application

// Include essential configuration and functions first
include_once(__DIR__ . '/config/config.php');
include_once(__DIR__ . '/config/functions.php');

// Get the request URI and remove the query string
$request_uri = strtok($_SERVER['REQUEST_URI'], '?');

// Define routes
switch ($request_uri) {
    case '/':
        include __DIR__ . '/app/pages/home.php';

        // This file now contains the render_ads_from_database function

        break;

    case '/products':
        echo "DEBUG: Reached /products route.<br>";
        // Products listing (now ads listing)   
        include __DIR__ . '/products.php';
        break;

    case '/articles':
        // Articles listing
        include __DIR__ . '/app/pages/articles.php';
        break;

    default:
        // Handle /category/slug
        if (preg_match('/^\/category\/([a-zA-Z0-9-]+)$/', $request_uri, $matches)) {
            $_GET['slug'] = $matches[1];
            include __DIR__ . '/single-category.php';
        }
        // Handle direct .php file requests (e.g., /ad-form.php, /login.php)
        elseif (file_exists(__DIR__ . $request_uri . '.php')) {
            include __DIR__ . $request_uri . '.php';
        }
        // 404 Not Found
        else {
            header("HTTP/1.0 404 Not Found");
            include __DIR__ . '/partials/header.php'; // Include header for 404 page
            echo '<div class="container text-center my-5"><h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p><a href="/" class="theme-btn">Go to Homepage</a></div>';
            include __DIR__ . '/partials/footer.php'; // Include footer for 404 page
        }
        break;
}
