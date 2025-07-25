<?php
// ajax/fetch_blogs_by_category.php

// Adjust the path to your config file. It's one level up from the /ajax/ directory.
include_once(__DIR__ . '/../config/config.php');
include_once(__DIR__ . '/../config/functions.php'); // Optional, if it has helper functions

// --- 1. GET AND VALIDATE INPUT ---
// Safely get the category ID and page number
$categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;

// Define pagination
$limit = 12;
$offset = ($page > 0) ? ($page - 1) * $limit : 0;

// --- 2. BUILD QUERY SECURELY WITH PREPARED STATEMENTS ---
$sql_params = [];
$sql_param_types = '';

// Base query
$sql = "SELECT bp.*, bc.name as category_name FROM blog_posts bp JOIN blog_categories bc ON bp.category_id = bc.id";

// Add category filter ONLY if a valid category is selected
if ($categoryId > 0) {
    $sql .= " WHERE category_id = ?"; // Use your actual category column name
    $sql_params[] = $categoryId;
    $sql_param_types .= 'i';
}

// Add ordering and pagination
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$sql_params[] = $limit;
$sql_params[] = $offset;
$sql_param_types .= 'ii';

// --- 3. EXECUTE THE QUERY ---
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind parameters if any exist
    if (!empty($sql_param_types)) {
        $stmt->bind_param($sql_param_types, ...$sql_params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // --- 4. LOOP AND ECHO THE CORRECT HTML STRUCTURE ---
        while ($blog = $result->fetch_assoc()) {
            // Prepare data exactly as in articles.php
            $images = json_decode($blog['image'], true);
            $firstImage = !empty($images[0]) ? $base_url . 'assets/uploads/blog_form/' . rawurlencode($images[0]) : $base_url . 'assets/images/test-img.png';

            $title = htmlspecialchars($blog['title']);
            $short_title = mb_strimwidth($title, 0, 60, "..."); // Use better truncation

            $description = strip_tags($blog['description']);
            $excerpt = mb_strimwidth($description, 0, 120, "...");

            $createdDate = date('jS F, Y', strtotime($blog['created_at']));
            $article_url = $base_url . 'article/' . $blog['blog_slug'];
?>
            <!-- 
                THIS HTML MUST BE IDENTICAL TO THE CARD HTML IN articles.php
                It uses the custom CSS classes (.article-card) we created.
            -->
            <div class="article-card">
                <a href="<?= $article_url ?>" class="card-img-blog">
                    <img src="<?= $firstImage ?>" alt="<?= $title ?>" />
                </a>
                <div class="card-body-blog">
                    <span class="inline-block bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs font-semibold mb-2"><?= htmlspecialchars($blog['category_name']) ?></span>
                    <a href="<?= $article_url ?>" class="card-title text-decoration-none">
                        <?= $short_title ?>
                    </a>
                    <p class="card-excerpt"><?= $excerpt ?></p>
                </div>
                <div class="card-foot-blog">
                    <img src="<?= $base_url ?>assets/images/userimage.png" alt="Author" class="user-image-blog" />
                    <span class="author-name"><?= htmlspecialchars($blog['author_name']) ?></span>
                    <span>|</span>
                    <span class="date"><?= $createdDate ?></span>
                </div>
            </div>

<?php
        } // End of while loop
    } else {
        // Provide a message if no posts are found for the filter
        echo '<p class="text-center w-100 py-5 text-muted">No blog posts found for this selection.</p>';
    }
    $stmt->close();
}
