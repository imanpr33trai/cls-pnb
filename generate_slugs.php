<?php
include_once(__DIR__ . '/config/config.php');

// A function to create a URL-friendly slug from a string
function generateSlug($text) {
    // Convert to lowercase
    $text = strtolower($text);
    // Replace non-letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Check if empty
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

echo "Starting slug generation...\n<br>";

// Fetch all categories that don't have a slug yet
$query = $conn->query("SELECT id, name FROM ad_categories WHERE slug IS NULL OR slug = ''");

if ($query->num_rows > 0) {
    while ($cat = $query->fetch_assoc()) {
        $id = $cat['id'];
        $name = $cat['name'];
        $slug = generateSlug($name);

        // Prepare an update statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE ad_categories SET slug = ? WHERE id = ?");
        $stmt->bind_param("si", $slug, $id);
        
        if ($stmt->execute()) {
            echo "SUCCESS: Updated category '{$name}' (ID: {$id}) with slug '{$slug}'\n<br>";
        } else {
            // If the slug already exists (due to UNIQUE constraint), make it unique
            $newSlug = $slug . '-' . $id;
            $stmt_unique = $conn->prepare("UPDATE ad_categories SET slug = ? WHERE id = ?");
            $stmt_unique->bind_param("si", $newSlug, $id);
            $stmt_unique->execute();
             echo "NOTICE: Slug '{$slug}' existed. Created unique slug '{$newSlug}' for ID: {$id}\n<br>";
            $stmt_unique->close();
        }
        $stmt->close();
    }
} else {
    echo "No categories needed updating.\n<br>";
}

echo "Slug generation complete.\n<br>";

$conn->close();
?>