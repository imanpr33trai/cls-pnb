<?php

include_once('partials/header.php');
include_once('config/config.php'); // always load this first
include_once('config/functions.php'); // load functions after config
require_once __DIR__ . '/config/debug.php';
echo '
<pre>';
print_r($_SESSION);
echo '</pre>';

if (is_loggedin()) {
    echo "User is logged in";
} else {
    echo "User is logged out";
}
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





<!-- section test posts  -->
<!-- section test posts  -->
<!-- section test posts  -->

<section class="section-4">
    <div class="container">
        <!-- First 8 Ads -->
        <div class="row">
            <?php
            // --- UPDATED QUERY #1 ---
            // Added the WHERE clause to filter for live and non-expired ads.
            $sql1 = "SELECT * FROM ad_form WHERE status = 'live' AND expires_at > NOW() ORDER BY id DESC LIMIT 8";
            $query1 = $conn->query($sql1);

            while ($ad = $query1->fetch_assoc()):
                $img = !empty($ad['image']) ? $base_url . 'assets/uploads/ads_form/' . $ad['image'] : $base_url . 'assets/images/test-img.png';
                $price = htmlspecialchars($ad['asking_price']);
                $title = htmlspecialchars($ad['ad_title']);
                $location = htmlspecialchars($ad['location']);
                ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card position-relative h-100">
                    <div class="ad-tag poppins-regular">Ad</div>
                    <div class="card-img-ad">
                        <a href="single-ad.php?id=<?= $ad['id'] ?>">
                            <img src="<?= $img ?>" class="img-fluid" alt="">
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="card-price-det">
                            <h5 class="poppins-bold price-post">$<?= $price ?></h5>
                            <a href="#" class="wish-heart">
                                <img src="<?= $base_url ?>assets/images/single-ad/heart-icon.svg" alt="">
                            </a>
                        </div>
                        <a href="single-ad.php?id=<?= $ad['id'] ?>">
                            <p class="Post-title fos-16 poppins-regular"><?= $title ?></p>
                        </a>
                        <hr>
                        <div class="d-flex align-items-start poppins-regular fos-14">
                            <img src="<?= $base_url ?>assets/images/location-black.svg" alt="" class="me-2">
                            <small><?= $location ?></small>
                        </div>
                    </div>
                    <button class="position-absolute top-0 end-0 bg-white border-0 m-2 rounded-circle shadow-sm p-1">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Advertisement Images -->
        <div class="row">
            <div class="section-1-advertisements mt-3 mb-5 d-flex gap-3">
                <div class="image-1 image-secs">
                    <img src="<?= $base_url ?>assets/images/test-image.jpg" alt="" class="w-100">
                </div>
                <div class="image-2 image-secs d-none d-md-block">
                    <img src="<?= $base_url ?>assets/images/test-image-2.jpg" alt="" class="w-100">
                </div>
            </div>
        </div>

        <!-- Next 8 Ads -->
        <div class="row">
            <?php
            // --- UPDATED QUERY #2 ---
            // Added the SAME WHERE clause here as well.
            $sql2 = "SELECT * FROM ad_form WHERE status = 'live' AND expires_at > NOW() ORDER BY id DESC LIMIT 8 OFFSET 8";
            $query2 = $conn->query($sql2);

            while ($ad = $query2->fetch_assoc()):
                $img = !empty($ad['image']) ? $base_url . 'assets/uploads/ads_form/' . $ad['image'] : $base_url . 'assets/images/test-img.png';
                $price = htmlspecialchars($ad['asking_price']);
                $title = htmlspecialchars($ad['ad_title']);
                $location = htmlspecialchars($ad['location']);
                ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card position-relative h-100">
                    <div class="ad-tag poppins-regular">Ad</div>
                    <div class="card-img-ad">
                        <a href="single-ad.php?id=<?= $ad['id'] ?>">
                            <img src="<?= $img ?>" class="img-fluid" alt="">
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="card-price-det">
                            <h5 class="poppins-bold price-post">$<?= $price ?></h5>
                            <a href="#" class="wish-heart">
                                <img src="<?= $base_url ?>assets/images/single-ad/heart-icon.svg" alt="">
                            </a>
                        </div>
                        <a href="single-ad.php?id=<?= $ad['id'] ?>">
                            <p class="Post-title fos-16 poppins-regular"><?= $title ?></p>
                        </a>
                        <hr>
                        <div class="d-flex align-items-start poppins-regular fos-14">
                            <img src="<?= $base_url ?>assets/images/location-black.svg" alt="" class="me-2">
                            <small><?= $location ?></small>
                        </div>
                    </div>
                    <button class="position-absolute top-0 end-0 bg-white border-0 m-2 rounded-circle shadow-sm p-1">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- section test posts  -->
<!-- section test posts  -->






<!-- footer -->
<!-- footer -->
<?php
include_once('partials/footer.php');
?>
<!-- footer -->
<!-- footer -->