<?php
// =========================================================================
// PART 1: FINAL, CORRECTED PHP LOGIC
// =========================================================================

// CRUCIAL: Must be the very first line of the file.
session_start();
include_once('config/config.php');

// --- 1. INITIALIZE ALL VARIABLES ---
$ad = null;
$platforms = [];
$user = null; // The user VIEWING the page
$initial_reviews = [];
$total_reviews = 0;
$reviews_per_page = 5;
$related_ads_result = null;

// --- 2. VALIDATE THE AD ID FROM THE URL ---
$ad_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

// --- 3. FETCH ALL DATA IF WE HAVE A VALID AD ID ---
if ($ad_id > 0) {
    // --- A. FETCH THE MAIN AD DETAILS ---
    $stmt = $conn->prepare("SELECT * FROM ad_form WHERE id = ?");
    $stmt->bind_param("i", $ad_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows === 1) {
        $ad = $result->fetch_assoc();

        // --- B. EXPIRATION CHECK ---
        if (isset($ad['expires_at'], $ad['status'])) {
            $is_expired = new DateTime() > new DateTime($ad['expires_at']);
            if ($ad['status'] === 'live' && $is_expired) {
                $update_stmt = $conn->prepare("UPDATE ad_form SET status = 'expired' WHERE id = ?");
                $update_stmt->bind_param("i", $ad_id);
                $update_stmt->execute();
                $update_stmt->close();
                $ad['status'] = 'expired';
            }
        }

        // --- C. DECODE PLATFORM DATA ---
        if (!empty($ad['platforms'])) {
            $decoded_platforms = json_decode($ad['platforms'], true);
            if (is_array($decoded_platforms)) {
                $platforms = $decoded_platforms;
            }
        }

        // --- D. FETCH REVIEWS (Safely, with CORRECT table name) ---
        // Get total count from ad_reviews table
        $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM ad_reviews WHERE ad_id = ?");
        $count_stmt->bind_param("i", $ad_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        if ($count_result) {
            $total_reviews = $count_result->fetch_assoc()['total'];
        }
        $count_stmt->close();
        
        // Get the first 5 reviews from ad_reviews table
        $review_stmt = $conn->prepare("SELECT r.*, u.first_name, u.last_name FROM ad_reviews r JOIN users u ON r.user_id = u.id WHERE r.ad_id = ? ORDER BY r.created_at DESC LIMIT ?");
        $review_stmt->bind_param("ii", $ad_id, $reviews_per_page);
        $review_stmt->execute();
        $initial_reviews_result = $review_stmt->get_result();
        if ($initial_reviews_result) {
            while ($row = $initial_reviews_result->fetch_assoc()) {
                $initial_reviews[] = $row;
            }
        }
        $review_stmt->close();
        // --- END OF REVIEW FIX ---

        // --- E. FETCH RELATED ADS (Safely) ---
        if ($ad['status'] === 'live') {
            $current_category = $ad['category']; // This holds the category ID, e.g., 5

            $related_stmt = $conn->prepare("SELECT * FROM ad_form WHERE category = ? AND id != ? AND status = 'live' AND expires_at > NOW() ORDER BY created_at DESC LIMIT 8");
            
            // *** THIS IS THE FIX ***
            // Change "si" to "ii" because both category and ID are integers.
            $related_stmt->bind_param("ii", $current_category, $ad_id);
            
            $related_stmt->execute();
            $related_ads_result = $related_stmt->get_result(); 
            // We do NOT close the statement here. The HTML loop needs it.
        }
    }
    $stmt->close();
}


// --- 4. FETCH LOGGED-IN USER INFO (for the review form) ---
if (isset($_SESSION['user_id'])) {
       // Here, the variable is named $user_id_session
    $user_id_session = $_SESSION['user_id']; 
    
    $stmt_user = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE id = ?");
    // But here, it's still trying to use the old variable name from a previous version
    $stmt_user->bind_param("i", $user_id_session); // This line is correct now.
    
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    if ($user_result && $user_result->num_rows === 1) {
        $user = $user_result->fetch_assoc();
    }
    $stmt_user->close();
}

// --- 5. INCLUDE HEADER ---
include_once('partials/header.php');

// =========================================================================
// PART 2: THE HTML STRUCTURE
// =========================================================================
?>
<!-- Breadcrump -->
<!-- Breadcrump -->
<section class="breadcrump">
    <div class="container">
        <div class="row">
            <div class="d-flex gap-2">
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-1">Home >></a>
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-2">Post Ad</a>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrump -->
<!-- Breadcrump -->

<!-- article details -->
<!-- article details -->
<section class="single-article-details pb-100 mb-50">
    <div class="container">
        <?php if (!$ad): // Case 1: Ad ID was invalid or ad not found ?>
            <div class="text-center mt-5 mb-5">
                <h1>Ad Not Found</h1>
                <p>The ad you are looking for does not exist or may have been removed.</p>
                <a href="index.php" class="theme-btn">Back to Homepage</a>
            </div>
        <?php elseif ($ad['status'] === 'expired'): // Case 2: Ad was found but is expired ?>
            <div class="text-center mt-5 mb-5">
                <h1 class="text-danger">This Ad Has Expired</h1>
                <p>This listing is no longer available.</p>
                <a href="index.php" class="theme-btn">Back to Homepage</a>
            </div>
        <?php else: // Case 3: Ad is live and ready to display ?>
        <div class="row">
            <div class="col-lg-8">
                   <?php
                        // Prepare variables for clean display
                        $ad_image = !empty($ad['image']) ? $base_url . 'assets/uploads/ads_form/' . $ad['image'] : $base_url . 'assets/images/placeholder-ad.png';
                        $platforms = json_decode($ad['platforms'], true);
                    ?>
                <div class="user-infos d-flex align-items-center mb-30 gap-3 d-block d-lg-none">
                    <img src="<?= $base_url ?>assets/images/userimage.png" alt="">
                    <h1 class="fos-20 poppins-regular"><?= htmlspecialchars($ad['user_name']) ?></h1>
                </div>

                <h1 class=" single-post-title fos-24 poppins-regular mb-20 d-block d-lg-none">
                    <?= htmlspecialchars($ad['ad_title']) ?></h1>

                <img src="<?= $ad_image ?>" class="w-100 mb-60 post-image-single" alt="">


                <div class="loc d-flex mb-40 d-block d-lg-none">
                    <img src="<?php echo $base_url; ?>assets/images/location-black.svg" alt="" class="me-3">
                    <h1 class="fos-16 poppins-regular m-0"><?= htmlspecialchars($ad['location']) ?></h1>
                </div>


                <div class="price-div mb-40 d-block d-lg-none">
                    <h1 class="fos-40 poppins-medium mb-40 color-pink">$<?= htmlspecialchars($ad['asking_price']) ?>
                    </h1>
                </div>

                <div class="contact-btn-div mb-50 d-block d-lg-none">
                    <a href="#" class="theme-btn text-decoration-none mb-50">Contact Us</a>
                </div>





                <div class="details-ad">
                    <h1 class="single-ad-email fos-16 poppins-bold">📧 <?= htmlspecialchars($ad['email']) ?></h1>
                    
                     <!-- ** DYNAMIC PLATFORM LIST + USERNAME ** -->
                        <?php
                            // Get just the platform names into a new array
                            $platform_names = !empty($platforms) ? array_column($platforms, 'platform') : [];
                            // Join them into a comma-separated string
                            $platform_list_string = implode(', ', $platform_names);
                        ?>
                        <?php if (!empty($platform_list_string)): ?>
                            <h1 class="single-ad-plateform fos-16 poppins-bold">
                                <span class="poppins-regular"><?= htmlspecialchars($platform_list_string) ?>:</span>
                                <?= htmlspecialchars($ad['user_name']) ?>
                            </h1>
                        <?php endif; ?>
                    <h1 class="single-ad-whatsapp fos-16 poppins-bold"><span class="poppins-regular"> WhatsApp
                            No:</span> <?= htmlspecialchars($ad['phone']) ?>
                    </h1>
                    <hr>
                    <div class="services-single-ad">
                        
                        <h1 class="fos-16 poppins-regular"><?= nl2br(htmlspecialchars($ad['description'])) ?></h1>
                    </div>
                    <hr>
                    <h1 class="single-ad-portfolio fos-16 poppins-bold"><span class="poppins-regular"> Follow My other
                            portfolio:</span> <?= htmlspecialchars($ad['organisation']) ?>
                    </h1>
                    <!-- ** DYNAMIC CLICKABLE SOCIAL MEDIA ACCOUNTS ** -->
                        <?php if (!empty($platforms)): ?>
                            <div class="single-ad-social fos-16 poppins-bold mb-30">
                                <span class="poppins-regular">My Social Media Accounts:</span>
                                <?php foreach ($platforms as $platform_item): ?>
                                    <a href="<?= htmlspecialchars($platform_item['link']) ?>" target="_blank" class="social-link-tag">
                                        <?= htmlspecialchars($platform_item['platform']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- ** DYNAMIC LIST OF LINKS ** -->
                        <?php if (!empty($platforms)): ?>
                            <?php foreach ($platforms as $platform_item): ?>
                                <h1 class="single-ad-platform-link fos-16 poppins-bold">Link: 
                                    <span class="poppins-regular color-pink">
                                        <a href="<?= htmlspecialchars($platform_item['link']) ?>" target="_blank" class="text-decoration-none color-pink">
                                            <?= htmlspecialchars($platform_item['link']) ?>
                                        </a>
                                    </span>
                                </h1>
                            <?php endforeach; ?>
                        <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="user-infos d-lg-flex align-items-center mb-30 gap-3 d-none">
                    <img src="<?php echo $base_url; ?>assets/images/userimage.png" class="" alt="">
                    <h1 class="fos-20 poppins-regular"><?= htmlspecialchars($ad['user_name']) ?></h1>
                </div>
                <h1 class="fos-40 poppins-regular mb-40 d-none d-lg-block"><?= htmlspecialchars($ad['ad_title']) ?></h1>
                <div class="loc d-lg-flex mb-40 d-none">
                    <img src="<?php echo $base_url; ?>assets/images/location-black.svg" alt="" class="me-3">
                    <h1 class="fos-16 poppins-regular m-0"><?= htmlspecialchars($ad['location']) ?></h1>
                </div>
                <div class="price-div mb-40 d-none d-lg-block">
                    <h1 class="fos-40 poppins-medium mb-40 color-pink">$<?= htmlspecialchars($ad['asking_price']) ?>
                    </h1>
                </div>
                <div class="contact-btn-div mb-50 d-none d-lg-block">
                    <a href="#" class="theme-btn text-decoration-none mb-50">Contact Us</a>
                </div>


                <!-- review system -->
                <!-- review system -->

                <?php if (!$user): ?>
                <div class="text-center mt-5">
                    <p>You must be logged in to write a review.</p>
                    <a href="login.php" class="theme-btn">Login to Continue</a>
                </div>
                <?php else: ?>
                <div class="mb-3">
                    <h4 class="poppins-medium">Welcome,
                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!</h4>
                </div>

                <!-- Review Form -->
                <div class="reviews-col">
                    <div class="reviews-col-text mb-4">
                        <h1 class="poppins-medium fos-20">How would you rate the overall user experience of our App?
                        </h1>
                        <h1 class="fos-14 poppins-regular mb-4">Do you find the app easy to use?</h1>

                        <!-- Star Rating -->
                        <div class="review-stars d-flex justify-content-between mb-4" id="starContainer"
                            style="gap: 10px;">
                            <input type="hidden" name="rating" id="ratingInput" value="0">
                            <i class="fa fa-star-o star" data-value="1"></i>
                            <i class="fa fa-star-o star" data-value="2"></i>
                            <i class="fa fa-star-o star" data-value="3"></i>
                            <i class="fa fa-star-o star" data-value="4"></i>
                            <i class="fa fa-star-o star" data-value="5"></i>
                        </div>

                        <!-- Review Form -->
                        <form action="" method="POST" id="reviewForm">
                            <input type="hidden" name="ad_id" value="<?php echo $ad_id; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="hidden" name="rating" id="ratingHidden" value="0">

                            <div class="review-comment">
                                <textarea name="comment" class="w-100 mb-4" rows="5" required></textarea>
                                <button type="submit" class="theme-btn">Submit</button>
                                <a href="#" class="theme-btn text-decoration-none">Cancel</a>
                            </div>
                        </form>
                        <div id="reviewMsg" class="mt-2 text-success"></div>

                    </div>
                </div>
                <?php endif; ?>






                <!-- review system -->
                <!-- review system -->

            </div>
        </div>
           <?php endif; // End of the main if/elseif/else block ?>
    </div>
</section>
<!-- article details -->
<!-- article details -->


<!-- review view -->
<!-- review view -->


<!-- NEW SECTION FOR DISPLAYING REVIEWS (Place this after the main ad section) -->
<!-- REVIEW VIEW SECTION -->
<?php if ($ad && $ad['status'] === 'live'): // Only show this section if the ad is live ?>
<section id="reviews-section" class="pb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h3 class="mb-4">User Reviews (<?= $total_reviews ?>)</h3>
                <div id="reviews-list">
                    <?php if (!empty($initial_reviews)): ?>
                        <?php foreach ($initial_reviews as $review): ?>
                            <div class="review-item border-bottom pb-3 mb-3">
                                <div class="review-header d-flex justify-content-between align-items-center">
                                    <h5 class="poppins-medium m-0"><?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?></h5>
                                    <div class="review-stars-display">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa <?= ($i <= $review['rating']) ? 'fa-star' : 'fa-star-o' ?>" style="color: #FFA500;"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <small class="text-muted"><?= date('F j, Y', strtotime($review['created_at'])) ?></small>
                                <p class="mt-2"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Be the first to leave a review for this ad!</p>
                    <?php endif; ?>
                </div>

                <!-- "See More" Button Container -->
                <?php if ($total_reviews > $reviews_per_page): ?>
                    <div id="see-more-container" class="text-center mt-4">
                        <a href="#" id="see-more-reviews" class="color-pink poppins-medium text-decoration-none" data-ad-id="<?= $ad_id ?>">See More Reviews</a>
                        <div id="loading-spinner" style="display: none;">Loading...</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- review view -->
<!-- review view -->
<!-- review view -->






<!-- advertisements -->
<!-- advertisements -->
<section class="mb-50">
    <div class="container">
        <div class="row">
            <div class="section-1-advertisements d-flex gap-3">
                <div class="image-1 image-secs"><img src="<?php echo $base_url; ?>assets/images/test-image.jpg" alt=""
                        class="w-100"></div>
                <div class="image-2 image-secs d-none d-md-block"><img
                        src="<?php echo $base_url; ?>assets/images/test-image-2.jpg" alt="" class="w-100"></div>
            </div>
        </div>
    </div>
</section>
<!-- advertisements -->
<!-- advertisements -->

<!-- related posts -->
<!-- related posts -->
<!-- related posts -->
<section>
    <div class="container">
        <div class="row">
            <h1 class="fos-40 playfair-regular text-center text-md-start mb-30">More Related Ads</h1>
            <?php if ($related_ads_result && $related_ads_result->num_rows > 0): ?>
                <div class="row mt-5">
                    
                    <?php while ($related = $related_ads_result->fetch_assoc()): 
                        // --- THIS IS THE MISSING BLOCK THAT FIXES THE ERROR ---
                        $related_image = !empty($related['image']) 
                            ? $base_url . 'assets/uploads/ads_form/' . $related['image'] 
                            : $base_url . 'assets/images/test-img.png';
                        $related_title = htmlspecialchars($related['ad_title']);
                        $related_price = htmlspecialchars($related['asking_price']);
                        $related_location = htmlspecialchars($related['location']);
                        $related_link = $base_url . "single-ad.php?id=" . $related['id'];
                        // --- END OF FIX ---
                    ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                        <a href="<?= $related_link ?>" class="text-decoration-none text-dark">
                            <div class="card position-relative h-100">
                                <div class="ad-tag poppins-regular">Ad</div>
                                <div class="card-img-ad">
                                    <img src="<?= $related_image ?>" class="img-fluid" alt="<?= $related_title ?>">
                                </div>
                                <div class="card-body">
                                    <div class="card-price-det">
                                        <h5 class="poppins-bold price-post">$<?= $related_price ?></h5>
                                        <a href="#" class="wish-heart">
                                            <img src="<?= $base_url ?>assets/images/single-ad/heart-icon.svg" alt="">
                                        </a>
                                    </div>
                                    <p class="Post-title fos-16 poppins-regular"><?= $related_title ?></p>
                                    <hr>
                                    <div class="d-flex align-items-start poppins-regular fos-14">
                                        <img src="<?= $base_url ?>assets/images/location-black.svg" alt="" class="me-2">
                                        <small><?= $related_location ?></small>
                                    </div>
                                </div>
                                <button class="position-absolute top-0 end-0 bg-white border-0 m-2 rounded-circle shadow-sm p-1">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center"><p>No related ads found in this category.</p></div>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- related posts -->
<!-- related posts -->
<!-- related posts -->











<!-- footer -->
<!-- footer -->
<?php
 include_once('partials/footer.php');
 ?>
<!-- footer -->
<!-- footer -->

<script>
// This function ensures all HTML is loaded before the scripts run.
document.addEventListener("DOMContentLoaded", function() {

    // --- LOGIC FOR THE REVIEW SUBMISSION FORM ---
    const reviewForm = document.getElementById("reviewForm");
    
    // Only run this part if the review form actually exists on the page
    if (reviewForm) {
        const stars = document.querySelectorAll(".star");
        const ratingInput = document.getElementById("ratingHidden");
        const msgBox = document.getElementById("reviewMsg");

        // Star rating click handler
        stars.forEach((star) => {
            star.addEventListener("click", () => {
                const rating = parseInt(star.getAttribute("data-value"));
                ratingInput.value = rating;

                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.remove("fa-star-o");
                        s.classList.add("fa-star");
                        s.style.color = "#FFA500";
                    } else {
                        s.classList.remove("fa-star");
                        s.classList.add("fa-star-o");
                        s.style.color = "#000";
                    }
                });
            });
        });

        // AJAX form submission handler
        reviewForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(reviewForm);
            msgBox.textContent = 'Submitting...';
            msgBox.style.color = "blue";

            fetch("ajax/submit_review_ajax.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msgBox.textContent = "Review submitted successfully!";
                    msgBox.style.color = "green";
                    reviewForm.reset();
                    ratingInput.value = 0;
                    stars.forEach(star => {
                        star.classList.remove("fa-star");
                        star.classList.add("fa-star-o");
                        star.style.color = "#000";
                    });
                    // Optional: You could reload the page or dynamically add the new review to the list here.
                } else {
                    msgBox.textContent = data.message || "Submission failed.";
                    msgBox.style.color = "red";
                }
            })
            .catch(err => {
                console.error('Fetch Error:', err);
                msgBox.textContent = "Network error. Please try again.";
                msgBox.style.color = "red";
            });
        });
    }


    // --- LOGIC FOR THE "SEE MORE REVIEWS" BUTTON (using jQuery) ---
    // This part requires jQuery to be loaded.
    if (typeof jQuery !== 'undefined') {
        let currentPage = 1;
        $('#see-more-reviews').on('click', function(e) {
            e.preventDefault();
            
            currentPage++;
            const adId = $(this).data('ad-id');
            const seeMoreBtn = $(this);
            const loadingSpinner = $('#loading-spinner');

            $.ajax({
                url: 'ajax/load_more_reviews.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    ad_id: adId,
                    page: currentPage
                },
                beforeSend: function() {
                    seeMoreBtn.hide();
                    loadingSpinner.show();
                },
                success: function(response) {
                    if (response.html) {
                        // Append new reviews to the list
                        $('#reviews-list').append(response.html);
                    }
                    
                    // Hide the "See More" button if the server says there are no more
                    if (!response.hasMore) {
                        $('#see-more-container').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    alert('An error occurred while loading more reviews. Please try again.');
                    $('#see-more-container').hide();
                },
                complete: function() {
                    loadingSpinner.hide();
                    // Only show the button again if there are still more pages
                    if ($('#see-more-container').is(':visible')) {
                        seeMoreBtn.show();
                    }
                }
            });
        });
    } else {
        console.error("jQuery is not loaded. 'See More' functionality will not work.");
    }
});
</script>