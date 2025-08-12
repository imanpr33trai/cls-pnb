<?php
  

  
  
include_once(__DIR__ . '/../../partials/header.php');
include_once(__DIR__ . '/../../config/config.php');
include_once(__DIR__ . '/../../config/functions.php');
require_once(__DIR__ . '/../../partials/products.php');   

  
  
    
  
  
  

  
    
  
  

  

  
include(__DIR__ . '/../../partials/hero-sec.php');
include(__DIR__ . '/../../partials/section-1.php');
include(__DIR__ . '/../../partials/category-sec.php');
include(__DIR__ . '/../../partials/search-sec.php');
?>

  <section class=" container">
    <div class="section-4 xs:min-w-full">
        <div class="">
            <?php
              
            echo render_ads_from_database($conn, $base_url, 8, 0);
            ?>
        </div>

                  <div class="row container m-auto">
            <div class="section-1-advertisements mt-5 mb-5 flex justify-center gap-3">
                <div class="image-1 image-secs">
                    <img src="<?= $base_url ?>assets/images/test-image.jpg" alt="Advertisement" class="2xl:min-w-full 2xl:max-h-full">
                </div>
                <div class="image-2 image-secs d-none d-md-block">
                    <img src="<?= $base_url ?>assets/images/test-image-2.jpg" alt="Advertisement" class="2xl:min-w-full 2xl:max-h-full">
                </div>
            </div>
        </div>

                  <div class="mb-7">
            <?php
              
            echo render_ads_from_database($conn, $base_url, 8, 8);
            ?>
        </div>
    </div>
</section>

<?php
include_once(__DIR__ . '/../../partials/footer.php');
?>