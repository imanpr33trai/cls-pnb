<?php

include_once('partials/header.php');
include_once('config/config.php'); // always load this first
include_once('config/functions.php'); // load functions after config

require_once 'products.php'; // load products after functions

?>

<!-- hero section -->
<!-- hero section -->

<?php include 'partials/hero-sec.php'; ?>

<!-- hero section -->
<!-- hero section -->

<!-- section-1 -->
<!-- section-1 -->

<?php include 'partials/section-1.php'; ?>

<!-- section-1 -->
<!-- section-1 -->

<!-- section-2 categories -->
<!-- section-2 categories -->

<?php include 'partials/category-sec.php'; ?>

<!-- section-2 categories -->
<!-- section-2 categories -->

<!-- section-3 search -->
<!-- section-3 search -->

<?php include 'partials/search-sec.php'; ?>

<!-- section-3 search -->
<!-- section-3 search -->



<?php
// ... (your includes at the top)
require_once 'product.php';
?>

<!-- ... (your other sections: hero, categories, etc.) ... -->

<!-- ADS SECTION -->
<section class="section-4">
    <div class="container">
        <!-- 
            RESPONSIVE GRID LOGIC IS NOW HERE:
            - row-cols-1: 1 column on extra-small screens (default)
            - row-cols-sm-2: 2 columns on small screens
            - row-cols-md-3: 3 columns on medium screens
            - row-cols-lg-4: 4 columns on large screens
            - g-4: Sets a consistent gap (gutter) of 1.5rem between all items
        -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php
            // Fetch and display the first 8 ads. The function now just returns cards.
            // The row above handles the layout.
            echo render_ads_from_database($conn, $base_url, 8, 0);
            ?>
        </div>

        <!-- Advertisement Images (This part is already responsive) -->
        <div class="row">
            <div class="section-1-advertisements mt-5 mb-5 d-flex gap-3">
                <div class="image-1 image-secs">
                    <img src="<?= $base_url ?>assets/images/test-image.jpg" alt="" class="w-100">
                </div>
                <div class="image-2 image-secs d-none d-md-block">
                    <img src="<?= $base_url ?>assets/images/test-image-2.jpg" alt="" class="w-100">
                </div>
            </div>
        </div>

        <!-- You can repeat the same responsive grid for the next set of ads -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php
            // Fetch and display the next 8 ads
            echo render_ads_from_database($conn, $base_url, 8, 8);
            ?>
        </div>

    </div>
</section>

<!-- ... (your footer) ... -->
<!-- footer -->
<!-- footer -->
<?php
include_once('partials/footer.php');
?>
<!-- footer -->
<!-- footer -->