<section class="hero-sec d-flex justify-content-center align-items-center">
    <div class="container">
        <div class="row">
            <div class="">
                <h1 class="playfair-medium text-white fos-90 text-md-center"><span
                        class="playfair-medium hide-text">Free</span> Classified Ads</h1>
                <h6 class="text-md-center  mt-4 mb-md-3 mb-5 text-white text-9xl">8,096,606 listings across 5,921 sites
                </h6>
                <div
                    class="d-flex justify-content-center head-searching-content align-items-md-center align-items-start flex-column flex-md-row" id="search-form">
                    <input type="text" id="keyword" placeholder="Enter keyword">
                    <input type="text" id="location" placeholder="City or Postal Code">
                    <a href="#" id="search-btn" class="theme-btn d-flex">
                        <img src="<?php echo $base_url; ?>assets/images/search-icon.svg" alt="" class="me-2">
                        Search
                    </a>
                
                </div>
          <!-- Overlay Result Area -->
            <div id="overlay" class="voice-overlay">
              <div class="close-btn" onclick="document.getElementById('overlay').style.display='none';">&times;</div>
              <div class="loader" id="loader">
                <img src="<?php echo $base_url; ?>assets/images/loader.svg" alt="Loading...">
              </div>
              <div id="results"></div>
            </div>

            </div>
        </div>
    </div>
</section>