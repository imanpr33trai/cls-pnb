<?php
include_once('../config/config.php'); // Adjust path to config file

// Check if subcat_id is provided and is a number
if (!isset($_GET['subcat_id']) || !is_numeric($_GET['subcat_id'])) {
    echo '<p>Invalid request.</p>';
    exit();
}

$subcat_id = (int)$_GET['subcat_id'];

// Use prepared statements to fetch posts securely
// I'm assuming your ads table is named 'ads' and has a 'subcategory_id' column
$stmt = $conn->prepare("SELECT * FROM ad_form WHERE subcategory = ? AND LOWER(status) = 'live' ORDER BY id DESC");
$stmt->bind_param("i", $subcat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Loop through the results and generate the HTML for each post
    while ($post = $result->fetch_assoc()) {
        // Build your HTML for a single post card here. This is just an example.
        $post_image = !empty($post['image']) ? $base_url . 'assets/uploads/ads_form/' . $post['image'] : $base_url . 'assets/images/default-ad.png';
        $post_url = $base_url . 'ads/' . $post['ad_slug']; // Use ad_slug for pretty URL
        ?>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <a href="<?= $post_url ?>">
                    <img src="<?= htmlspecialchars($post_image) ?>" class="card-img-top" alt="<?= htmlspecialchars($post['ad_title']) ?>">
                </a>
                <div class="card-body">
                    <h5 class="card-title"><a href="<?= $post_url ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($post['ad_title']) ?></a></h5>
                    <p class="card-text text-muted"><?= htmlspecialchars(substr($post['description'], 0, 80)) ?>...</p>
                    <p class="card-text fw-bold">Price: $<?= htmlspecialchars($post['asking_price']) ?></p>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    // If no posts are found in this subcategory
    echo '<div class="w-100 text-center"><p>No posts found in this subcategory yet.</p></div>';
}

$stmt->close();
$conn->close();
?>