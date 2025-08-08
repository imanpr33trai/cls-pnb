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
// /config/functions.php

/**
 * Creates a clean, URL-friendly, and unique slug from a string, using only the first few words.
 *
 * @param mysqli $conn The database connection object.
 * @param string $title The full title to convert into a slug.
 * @param string $tableName The name of the database table to check for uniqueness.
 * @param string $slugColumnName The name of the slug column in that table.
 * @param int $wordLimit The number of words to use for the base slug. Defaults to 5.
 * @return string The unique, URL-friendly slug.
 */
function create_unique_slug(
    mysqli $conn, 
    string $title, 
    string $tableName, 
    string $slugColumnName, 
    int $wordLimit = 5
): string {
    // ==========================================================
    //  NEW: Truncate the title to the specified word limit
    // ==========================================================
    // Break the title into an array of words
    $words = explode(' ', $title);
    // Take a slice of the array from the beginning up to the word limit
    $shortenedTitle = implode(' ', array_slice($words, 0, $wordLimit));
    // ==========================================================

    // 1. Create the basic slug from the shortened title
    $slug = strtolower(trim($shortenedTitle));
    // Replace non-alphanumeric characters (including Unicode) with a hyphen
    $slug = preg_replace('/[^\pL\d]+/u', '-', $slug); 
    $slug = preg_replace('/-+/', '-', $slug); // Collapse multiple hyphens
    $slug = trim($slug, '-');                // Remove leading/trailing hyphens

    if (empty($slug)) {
        return 'item-' . uniqid(); // Fallback for empty or symbol-only titles
    }

    // 2. Security: Sanitize table and column names
    $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    $safeSlugColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $slugColumnName);

    if ($safeTableName !== $tableName || $safeSlugColumn !== $slugColumnName) {
        error_log("Attempted to use invalid characters in table/column name for slug generation.");
        return $slug . '-' . uniqid(); // Fallback to a guaranteed unique slug
    }
    
    // --- 3. Check for uniqueness and append a counter if necessary ---
    $baseSlug = $slug;
    $counter = 1;
    
    $query = "SELECT id FROM `{$safeTableName}` WHERE `{$safeSlugColumn}` = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        error_log("Slug Check: Failed to prepare statement - " . $conn->error);
        return $baseSlug . '-' . uniqid(); // Fallback
    }

    while (true) {
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            break; // This slug is unique
        }

        // If the slug exists, append the counter and try again
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    $stmt->close();
    return $slug;
}
?>