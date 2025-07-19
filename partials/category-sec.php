<section class="sec-categories">
    <div class="container">
        <div class="row">
            <div class="all-cats d-flex flex-wrap">
                <?php
                include_once(__DIR__ . '/../config/config.php');

                // ***MODIFICATION 1: Select the new 'slug' column***
                $query = $conn->query("SELECT id, name, image, slug FROM ad_categories WHERE LOWER(status) = 'live' ORDER BY id DESC LIMIT 8");

                while ($cat = $query->fetch_assoc()):
                    $img = !empty($cat['image']) ? $base_url . 'assets/uploads/' . $cat['image'] : $base_url . 'assets/images/cats/default.svg';

                    // Define the new URL. We use $base_url to make it absolute.
                    // ***MODIFICATION 2: Change the href structure***
                    $category_url = $base_url . 'category/' . $cat['slug'];
                ?>
                    <a href="<?= $category_url ?>"
                        class="single-cat d-flex flex-column align-items-center text-decoration-none text-dark">
                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                        <h3 class="fos-16"><?= htmlspecialchars($cat['name']) ?></h3>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>