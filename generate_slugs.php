<?php
include_once(__DIR__ . '/config/config.php');

// This line is crucial to make sure mysqli throws exceptions instead of warnings
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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

// --- Generate slugs for ad_categories ---
echo "Generating slugs for ad_categories...\n<br>";
$query_categories = $conn->query("SELECT id, name FROM ad_categories WHERE slug IS NULL OR slug = ''");

if ($query_categories->num_rows > 0) {
    while ($cat = $query_categories->fetch_assoc()) {
        $id = $cat['id'];
        $name = $cat['name'];
        $slug = generateSlug($name);
        $stmt = null; // Initialize stmt to null

        try {
            $stmt = $conn->prepare("UPDATE ad_categories SET slug = ? WHERE id = ?");
            $stmt->bind_param("si", $slug, $id);
            $stmt->execute();
            echo "SUCCESS: Updated category '{$name}' (ID: {$id}) with slug '{$slug}'\n<br>";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // 1062 is the error code for a duplicate entry
                $newSlug = $slug . '-' . $id;
                $stmt_unique = $conn->prepare("UPDATE ad_categories SET slug = ? WHERE id = ?");
                $stmt_unique->bind_param("si", $newSlug, $id);
                $stmt_unique->execute();
                echo "NOTICE: Slug '{$slug}' existed. Created unique slug '{$newSlug}' for ID: {$id}\n<br>";
                $stmt_unique->close();
            } else {
                echo "ERROR: Could not update category ID {$id}. Reason: " . $e->getMessage() . "\n<br>";
            }
        } finally {
            if ($stmt) $stmt->close();
        }
    }
} else {
    echo "No ad_categories needed updating.\n<br>";
}

// --- Generate slugs for ad_form (ads) ---
echo "Generating slugs for ad_form...\n<br>";
$query_ads = $conn->query("SELECT id, ad_title FROM ad_form WHERE ad_slug IS NULL OR ad_slug = ''");

if ($query_ads->num_rows > 0) {
    while ($ad = $query_ads->fetch_assoc()) {
        $id = $ad['id'];
        $ad_title = $ad['ad_title'];
        $slug = generateSlug($ad_title);
        $stmt = null;

        try {
            $stmt = $conn->prepare("UPDATE ad_form SET ad_slug = ? WHERE id = ?");
            $stmt->bind_param("si", $slug, $id);
            $stmt->execute();
            echo "SUCCESS: Updated ad '{$ad_title}' (ID: {$id}) with slug '{$slug}'\n<br>";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $newSlug = $slug . '-' . $id;
                $stmt_unique = $conn->prepare("UPDATE ad_form SET ad_slug = ? WHERE id = ?");
                $stmt_unique->bind_param("si", $newSlug, $id);
                $stmt_unique->execute();
                echo "NOTICE: Ad slug '{$slug}' existed. Created unique slug '{$newSlug}' for ID: {$id}\n<br>";
                $stmt_unique->close();
            } else {
                echo "ERROR: Could not update ad ID {$id}. Reason: " . $e->getMessage() . "\n<br>";
            }
        } finally {
            if ($stmt) $stmt->close();
        }
    }
} else {
    echo "No ad_form entries needed updating.\n<br>";
}

// --- Generate slugs for blog_posts ---
echo "Generating slugs for blog_posts...\n<br>";
$query_blogs = $conn->query("SELECT id, title FROM blog_posts WHERE blog_slug IS NULL OR blog_slug = ''");

if ($query_blogs->num_rows > 0) {
    while ($blog = $query_blogs->fetch_assoc()) {
        $id = $blog['id'];
        $title = $blog['title'];
        $slug = generateSlug($title);
        $stmt = null;

        try {
            $stmt = $conn->prepare("UPDATE blog_posts SET blog_slug = ? WHERE id = ?");
            $stmt->bind_param("si", $slug, $id);
            $stmt->execute();
            echo "SUCCESS: Updated blog post '{$title}' (ID: {$id}) with slug '{$slug}'\n<br>";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $newSlug = $slug . '-' . $id;
                $stmt_unique = $conn->prepare("UPDATE blog_posts SET blog_slug = ? WHERE id = ?");
                $stmt_unique->bind_param("si", $newSlug, $id);
                $stmt_unique->execute();
                echo "NOTICE: Blog slug '{$slug}' existed. Created unique slug '{$newSlug}' for ID: {$id}\n<br>";
                $stmt_unique->close();
            } else {
                echo "ERROR: Could not update blog post ID {$id}. Reason: " . $e->getMessage() . "\n<br>";
            }
        } finally {
            if ($stmt) $stmt->close();
        }
    }
} else {
    echo "No blog_posts needed updating.\n<br>";
}

echo "Slug generation complete.\n<br>";

$conn->close();
?>