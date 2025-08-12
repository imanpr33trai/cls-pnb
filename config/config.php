<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/debug.php';






date_default_timezone_set("Asia/Kolkata");


$dotenv = Dotenv::createMutable(__DIR__ . '/../');

$dotenv->load();
$base_url = $_ENV['BASE_URL'] ?? 'http://localhost:8000/';
if (!defined('POST_AD_URL')) {
    define('POST_AD_URL', $base_url . 'ad-form');
}
if (!defined('ARTICLES_URL')) {
    define('ARTICLES_URL', $base_url . 'article');
}
if (!defined('ARTICLES_POST_URL')) {
    define('ARTICLES_POST_URL', $base_url . 'blog-form');
}
if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID']);
}
if (!defined('GOOGLE_CLIENT_SECRET')) {
    define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET']);
}
if (!defined('GOOGLE_REDIRECT_URL')) {
    define('GOOGLE_REDIRECT_URL', $_ENV['GOOGLE_REDIRECT_URL']);
}

if (!defined('GITHUB_CLIENT_ID')) {
    define('GITHUB_CLIENT_ID', $_ENV['GITHUB_CLIENT_ID']);
}
if (!defined('GITHUB_CLIENT_SECRET')) {
    define('GITHUB_CLIENT_SECRET', $_ENV['GITHUB_CLIENT_SECRET']);
}
if (!defined('GITHUB_REDIRECT_URL')) {
    define('GITHUB_REDIRECT_URL', $_ENV['GITHUB_REDIRECT_URL']);
}


$db_host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USER'];
$db_pass = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_NAME'];
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
if (!defined('AJAX_REQUEST')) {
    require_once __DIR__ . '/../handler/DatabaseSessionHandler.php';
    $session_handler = new DatabaseSessionHandler($conn);

    session_set_save_handler($session_handler, true);

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}
include_once(__DIR__ . DIRECTORY_SEPARATOR . 'functions.php');
