<?php
// app/pages/home.php

// Since this file is now in app/pages/, we adjust the paths to go up two levels.
// Using __DIR__ makes the paths absolute and more reliable.
include_once(__DIR__ . '/../../partials/header.php');
include_once(__DIR__ . '/../../config/config.php');
include_once(__DIR__ . '/../../config/functions.php');
require_once(__DIR__ . '/../../partials/products.php'); // Contains the render_ads_from_database function

// Page content sections
include(__DIR__ . '/../../partials/hero-sec.php');
include(__DIR__ . '/../../partials/section-1.php');
include(__DIR__ . '/../../partials/category-sec.php');
include(__DIR__ . '/../../partials/search-sec.php');
?>

<!-- ADS SECTION -->
<section class="section-4">
    <div class="container">
        <div class="">
            <?php
            // Fetch and display the first 8 ads.
            echo render_ads_from_database($conn, $base_url, 8, 0);
            ?>
        </div>

        <!-- Advertisement Images -->
        <div class="row">
            <div class="section-1-advertisements mt-5 mb-5 d-flex gap-3">
                <div class="image-1 image-secs">
                    <img src="<?= $base_url ?>assets/images/test-image.jpg" alt="Advertisement" class="w-100">
                </div>
                <div class="image-2 image-secs d-none d-md-block">
                    <img src="<?= $base_url ?>assets/images/test-image-2.jpg" alt="Advertisement" class="w-100">
                </div>
            </div>
        </div>

        <!-- More Ads -->
        <div class_="">
            <?php
            // Fetch and display the next 8 ads
            echo render_ads_from_database($conn, $base_url, 8, 8);
            ?>
        </div>
    </div>
</section>

<?php
include_once(__DIR__ . '/../../partials/footer.php');
?>