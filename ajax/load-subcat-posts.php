<?php
include_once('../config/config.php');
include_once('../config/functions.php');
if (!isset($_GET['subcat_id']) || !is_numeric($_GET['subcat_id'])) {
    echo '<p>Invalid request.</p>';
    exit();
}

$subcat_id = (int)$_GET['subcat_id'];
$stmt = $conn->prepare("SELECT * FROM ad_form WHERE subcategory = ? AND LOWER(status) = 'live' ORDER BY id DESC");
$stmt->bind_param("i", $subcat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($post = $result->fetch_assoc()) {
        $post_image = !empty($post['image']) ? $base_url . 'assets/uploads/ads_form/' . $post['image'] : $base_url . 'assets/images/default-ad.png';
        $post_url = $base_url . 'ads/' . $post['ad_slug'];        ?>
        <div class="col-12 col-md-6 col-lg-4 mb-4 max-w-[305px]">
            <div class="card h-100">
                <a href="<?= $post_url ?>">
                    <img src="<?= htmlspecialchars($post_image) ?>" class="card-img-top  img-ads" alt="<?= htmlspecialchars($post['ad_title']) ?>">
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
    echo '<div class="w-100  text-center"><p>No posts found in this subcategory yet.</p></div>';
}

$stmt->close(); ?>