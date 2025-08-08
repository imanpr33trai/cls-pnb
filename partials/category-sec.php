<section class="sec-categories w-full ">
    <div class="container ">
    <div class="  p-2 xs:min-w-full">
        <div class="row">
            <!-- The parent container uses flexbox and wrapping -->
            <div class="all-cats ">
                <div class=" justify-center flex flex-wrap  gap-2">

                    <?php
                    include_once(__DIR__ . '/../config/config.php');

                    // Get the current URL path
                    $current_url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

                    // Extract the slug from the URL path
                    $current_slug = '';
                    $path_parts = explode('/', trim($current_url_path, '/'));
                    if (count($path_parts) >= 2 && $path_parts[0] === 'category') {
                        $current_slug = $path_parts[1];
                    }

                    $query = $conn->query("SELECT id, name, image, slug FROM ad_categories WHERE LOWER(status) = 'live' ORDER BY id DESC LIMIT 8");

                    while ($cat = $query->fetch_assoc()):
                        $img = !empty($cat['image']) ? $base_url . 'assets/uploads/' . $cat['image'] : $base_url . 'assets/images/cats/default.svg';
                        $category_url = $base_url . 'category/' . $cat['slug'];
                        $active_class = ($cat['slug'] === $current_slug) ? 'active-category' : '';
                    ?>
                        <a href="<?= $category_url ?>"
                            class="single-cat d-flex flex-column justify-between align-items-center text-decoration-none text-dark <?= $active_class ?> responsive-cat-box">
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                            <h3 class="fos-16 text-center mt-2"><?= htmlspecialchars($cat['name']) ?></h3>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>

<!-- Responsive styles for category boxes -->
<!-- <style>
/* Responsive category box grid - added for xs, sm, md, lg, xl breakpoints */
.responsive-cat-box {
    flex: 0 0 100%; /* xs: 1 per row */
    max-width: 48%;
}
@media (min-width: 640px) { /* sm: 2 per row */
    .responsive-cat-box {
        flex: 0 0 50%;
        max-width: 49%;
    }
}
@media (min-width: 768px) { /* md: 3 per row */
    .responsive-cat-box {
        flex: 0 0 33.3333%;
        max-width: 32.3333%;
    }
}
@media (min-width: 1024px) { /* lg: 4 per row */
    .responsive-cat-box {
        flex: 0 0 25%;
        max-width: 25%;
    }
}
@media (min-width: 1280px) { /* xl: show all in one row if 8 or less */
    .responsive-cat-box {
        flex: 0 0 12.5%;
        max-width: 12.5%;
    }
}
</style> -->
<!-- End responsive styles for category boxes -->