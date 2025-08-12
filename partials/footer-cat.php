<?php
include_once(__DIR__ . '/../config/config.php');
$query = $conn->query("SELECT id, name, image, slug FROM ad_categories WHERE LOWER(status) = 'live' ORDER BY id DESC LIMIT 8");

while ($cat = $query->fetch_assoc()):


    $category_url = $base_url . 'category/' . $cat['slug'];
?>
    <a href="<?= $category_url ?>">

        <span class=""><?= htmlspecialchars($cat['name']) ?></span>
    </a>
<?php endwhile; ?>