
               <?php
include_once(__DIR__ . '/../config/config.php'); // Adjust path as needed
$query = $conn->query("SELECT * FROM ad_categories WHERE LOWER(status) = 'live' ORDER BY id DESC LIMIT 6");

while ($cat = $query->fetch_assoc()):
    $img = !empty($cat['image']) ? $base_url . 'assets/uploads/' . $cat['image'] : $base_url . 'assets/images/cats/default.svg';
?>
                <a href="single-category.php?category=<?= $cat['id'] ?>"
                   >
                
                    <span class=""><?= htmlspecialchars($cat['name']) ?></span>
                </a>
                <?php endwhile; ?>

