<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect to another page
function redirect($url) {
    header("Location: $url");
    exit();
}

// Sanitize input (e.g., form inputs)
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in (for admin)
function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Flash message (save and show alerts)
function set_flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function get_flash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}


function is_loggedin() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function generate_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/\s+/', '-', $string); // Replace spaces with hyphens
    $string = preg_replace('/[^a-z0-9-]/i', '', $string); // Remove non-alphanumeric characters except hyphens
    $string = preg_replace('/-+/', '-', $string); // Replace multiple hyphens with a single hyphen
    $string = trim($string, '-'); // Trim hyphens from the beginning and end
    return $string;
}

?>