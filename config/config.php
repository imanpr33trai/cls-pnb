<?php
// ==========================================
// ✅ Basic Site Config for Punjab Classified
// ==========================================

// Start session globally

use Dotenv\Dotenv;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



// Set default timezone (optional)
date_default_timezone_set("Asia/Kolkata");

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createMutable(__DIR__. '/../');

$dotenv->load();

// ==========================================
// ✅ Base URL (adjust this if you're in subfolder)
// ==========================================
$base_url = $_ENV['BASE_URL'] ?? 'http://localhost:8000/';
define('POST_AD_URL', $base_url . 'ad-form.php');
define('ARTICLES_URL', $base_url . 'articles.php');
define('ARTICLES_POST_URL', $base_url . 'Blog-form.php');

//google api
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID']);
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET']);
define('GOOGLE_REDIRECT_URL', $_ENV['GOOGLE_REDIRECT_URL']);

define('GITHUB_CLIENT_ID', $_ENV['GITHUB_CLIENT_ID']);
define('GITHUB_CLIENT_SECRET', $_ENV['GITHUB_CLIENT_SECRET']);
define('GITHUB_REDIRECT_URL', $_ENV['GITHUB_REDIRECT_URL']);



// ← change as per your Laragon folder name


// ==========================================
// ✅ Database Configuration
// ==========================================
$db_host = $_ENV['DB_HOST'];       // Usually localhost
$db_user = $_ENV['DB_USER'];            // Default user in Laragon/XAMPP
$db_pass = $_ENV['DB_PASSWORD'];                // Leave empty in Laragon/XAMPP
$db_name = $_ENV['DB_NAME']; // Your DB name

// Connect to MySQL
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Optional: Set charset
$conn->set_charset("utf8mb4");


// ✅ Fix path to functions.php
include_once(__DIR__ . DIRECTORY_SEPARATOR . 'functions.php');

?>