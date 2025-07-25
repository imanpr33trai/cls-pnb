<section class="sec-categories">
    <div class="container p-2">
        <div class="row">
            <div class="all-cats flex gap-2 flex-wrap">
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

                // ***MODIFICATION 1: Select the new 'slug' column***
                $query = $conn->query("SELECT id, name, image, slug FROM ad_categories WHERE LOWER(status) = 'live' ORDER BY id DESC LIMIT 8");

                while ($cat = $query->fetch_assoc()):
                    $img = !empty($cat['image']) ? $base_url . 'assets/uploads/' . $cat['image'] : $base_url . 'assets/images/cats/default.svg';

                    // Define the new URL. We use $base_url to make it absolute.
                    // ***MODIFICATION 2: Change the href structure***
                    $category_url = $base_url . 'category/' . $cat['slug'];

                    // Add active-category class if the slug matches
                    $active_class = ($cat['slug'] === $current_slug) ? 'active-category' : '';
                ?>
                    <a href="<?= $category_url ?>"
                        class="single-cat d-flex flex-column align-items-center text-decoration-none text-dark <?= $active_class ?>">
                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                        <h3 class="fos-16"><?= htmlspecialchars($cat['name']) ?></h3>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>