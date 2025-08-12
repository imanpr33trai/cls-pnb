<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

    
function redirect($url) {
    header("Location: $url");
    exit();
}

    
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

    
function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

    
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
        
        
        
        
    $words = explode(' ', $title);
        
    $shortenedTitle = implode(' ', array_slice($words, 0, $wordLimit));
        

        
    $slug = strtolower(trim($shortenedTitle));
        
    $slug = preg_replace('/[^\pL\d]+/u', '-', $slug); 
    $slug = preg_replace('/-+/', '-', $slug);     
    $slug = trim($slug, '-');                    

    if (empty($slug)) {
        return 'item-' . uniqid();     
    }

        
    $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    $safeSlugColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $slugColumnName);

    if ($safeTableName !== $tableName || $safeSlugColumn !== $slugColumnName) {
        error_log("Attempted to use invalid characters in table/column name for slug generation.");
        return $slug . '-' . uniqid();     
    }
    
        
    $baseSlug = $slug;
    $counter = 1;
    
    $query = "SELECT id FROM `{$safeTableName}` WHERE `{$safeSlugColumn}` = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        error_log("Slug Check: Failed to prepare statement - " . $conn->error);
        return $baseSlug . '-' . uniqid();     
    }

    while (true) {
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            break;     
        }

            
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    $stmt->close();
    return $slug;
}
?>