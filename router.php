<?php

// router.php - Handles all routing for the application

// Include essential configuration and functions first
include_once(__DIR__ . '/config/config.php');
include_once(__DIR__ . '/config/functions.php');





// Get the request URI and remove the query string
$request_uri = strtok($_SERVER['REQUEST_URI'], '?');



$path_segments = $request_uri ? explode('/', $request_uri) : [];
$segment_count = count($path_segments);


if ($segment_count === 0) {
    require 'home.php';
    exit();
}
// Define routes using a switch for static routes and regex for dynamic ones
switch (true) {
    // --- Static Routes ---
    case $request_uri === '/':

        include __DIR__ . '/app/pages/home.php';
        break;

    case $request_uri === '/products':

        include __DIR__ . '/products.php';
        break;

    case $request_uri === '/articles':

        include __DIR__ . '/app/pages/articles.php';
        break;

    case $request_uri === '/login':
        include __DIR__ . '/app/auth/login.php';
        break;

    case $request_uri === '/register':

        include __DIR__ . '/app/auth/register.php';
        break;

    case $request_uri === '/logout':
        include __DIR__ . '/app/auth/logout.php';
        break;
    case $request_uri === '/ad-form':

        include __DIR__ . '/app/pages/ad-form.php';
        break;
    case $request_uri === '/blog-form':

        include __DIR__ . '/app/pages/blog-form.php';
        break;
    case $request_uri === '/verify':
        include __DIR__ . '/app/auth/verify.php';
        break;

    case $request_uri === '/test':
        include __DIR__ . '/partials/header.php';
        break;

    case $request_uri === '/search-results':
        include __DIR__ . '/app/pages/search-results.php';
        break;

    // --- AJAX/API Handlers ---
    case $request_uri === '/search':
        // This route should only include the backend logic file.
        // Ensure search.php is in your project's root directory.
        include __DIR__ . 'ajax/search.php';
        break;




// --- Admin Panel Routes ---
// If the request starts with /admin, delegate to the admin-router.
case preg_match('/^\/admin/', $request_uri):
    // The admin-router is designed to handle the full URI starting with /admin,
    // so we pass the request URI to it without modification.
    include __DIR__ . '/admin-router.php';
    exit; // Important: exit after handling admin routes




    // Handles URLs like /article/some-cool-slug
    case preg_match('/^\/article\/([a-zA-Z0-9-_]+)\/?$/', $request_uri, $matches):


        $_GET['slug'] = $matches[1]; // Extract slug from URL
        include __DIR__ . '/app/pages/single-article.php';
        break;

    // Handles URLs like /category/gadgets
    case preg_match('/^\/category\/([a-zA-Z0-9-_]+)\/?$/', $request_uri, $matches):


        $_GET['slug'] = $matches[1]; // Extract slug from URL
        include __DIR__ . '/app/pages/single-category.php';
        break;

    // Handles URLs like /ad/my-awesome-product
    case preg_match('/^\/ads\/([a-zA-Z0-9-_]+)\/?$/', $request_uri, $matches):


        $_GET['slug'] = $matches[1]; // Extract slug from URL
        include __DIR__ . '/app/pages/single-ad.php';
        break;


    // --- 404 Not Found ---
    default:
       
        header("HTTP/1.0 404 Not Found");
        include __DIR__ . '/partials/header.php';

        echo '<div class="container text-center flex h-[75vh] my-5"><div  class="my-auto mx-auto"><h1>404 - Page Not Found</h1><p>The page you are looking for ddsfoes not exist.</p><a href="/" class="theme-btn">Go to Homepage</a></div></div>';
        include __DIR__ . '/partials/footer.php';
        break;
}
