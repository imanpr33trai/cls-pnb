<?php

// Include essential configuration and functions first
include_once(__DIR__ . '/config/config.php');
include_once(__DIR__ . '/config/functions.php');

// Get the request URI and remove the query string
$request_uri = strtok($_SERVER['REQUEST_URI'], '?');

// Define routes
switch ($request_uri) {
    case '/':
        // Homepage
        include __DIR__ . '/partials/header.php';
        include __DIR__ . '/partials/hero-sec.php';
        include __DIR__ . '/partials/section-1.php';
        include __DIR__ . '/partials/category-sec.php';
        include __DIR__ . '/partials/search-sec.php';
        // Include the products listing (which is now ads)
        include __DIR__ . '/products.php'; // This file now contains the render_ads_from_database function
        include __DIR__ . '/partials/footer.php';
        break;

    case '/products':
        echo "DEBUG: Reached /products route.<br>";
        // Products listing (now ads listing)   
        include __DIR__ . '/partials/header.php';
        include __DIR__ . '/products.php';
        include __DIR__ . '/partials/footer.php';
        break;

    case '/articles':
        // Articles listing
        include __DIR__ . '/articles.php';
        break;

    // Dynamic routes with slugs
    default:
        // Handle /ads/slug
        if (preg_match('/^\/ads\/([a-zA-Z0-9-]+)$/', $request_uri, $matches)) {
            $_GET['slug'] = $matches[1];
            include __DIR__ . '/single-ad.php';
        }
        // Handle /articles/slug
        else if (preg_match('/^\/articles\/([a-zA-Z0-9-]+)$/', $request_uri, $matches)) {
            $_GET['slug'] = $matches[1];
            include __DIR__ . '/single-article.php';
        }
        // Handle /category/slug
        else if (preg_match('/^\/category\/([a-zA-Z0-9-]+)$/', $request_uri, $matches)) {
            $_GET['slug'] = $matches[1];
            include __DIR__ . '/single-category.php';
        }
        // Handle direct .php file requests (e.g., /ad-form.php, /login.php)
        else if (file_exists(__DIR__ . $request_uri . '.php')) {
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

?>