<?php
   
   
   

   

include_once(__DIR__ . '/../../partials/header.php');
include_once(__DIR__ . '/../../config/config.php');
include_once(__DIR__ . '/../../config/functions.php');

   
$ad = null;
$platforms = [];
$user = null;    
$initial_reviews = [];
$total_reviews = 0;
$reviews_per_page = 5;
$related_ads_result = null;

   
$ad_slug = isset($_GET['slug']) ? $_GET['slug'] : '';

   
if (!empty($ad_slug)) {

       
    $stmt = $conn->prepare("SELECT * FROM ad_form WHERE ad_slug = ?");
    $stmt->bind_param("s", $ad_slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $ad = $result->fetch_assoc();
        $ad_id = $ad['id'];    

           
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

           
if (!empty($ad['platforms']) && is_string($ad['platforms'])) {
    $decoded_platforms = json_decode($ad['platforms'], true);
    if (is_array($decoded_platforms)) {
        $platforms = $decoded_platforms;
    }
}

           
           
        $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM ad_reviews WHERE ad_id = ?");
        $count_stmt->bind_param("i", $ad_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        if ($count_result) {
            $total_reviews = $count_result->fetch_assoc()['total'];
        }
        $count_stmt->close();

           
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
           

           
        if ($ad['status'] === 'live') {
            $current_category = $ad['category'];    

            $related_stmt = $conn->prepare("SELECT * FROM ad_form WHERE category = ? AND id != ? AND status = 'live' AND expires_at > NOW() ORDER BY created_at DESC LIMIT 8");

               
               
            $related_stmt->bind_param("ii", $current_category, $ad_id);

            $related_stmt->execute();
            $related_ads_result = $related_stmt->get_result();
               
        }
    }
    $stmt->close();
}


   
if (isset($_SESSION['user_id'])) {
       
    $user_id_session = $_SESSION['user_id'];

    $stmt_user = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE id = ?");
       
    $stmt_user->bind_param("i", $user_id_session);    

    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    if ($user_result && $user_result->num_rows === 1) {
        $user = $user_result->fetch_assoc();
    }
    $stmt_user->close();
}

   


   
   
   
?>
      <section class="breadcrump py-2.5">
    <div class="container">
        <div class="row">
            <div class="d-flex gap-2">
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-1">Home >></a>
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-2">Post Ad</a>
            </div>
        </div>
    </div>
</section>
      
      <section class="single-article-details">
    <div class="container">
        <?php if (!$ad):    
                ?>
            <div class="text-center mt-5 mb-5">
                <h1>Ad Not Found</h1>
                <p>The ad you are looking for does not exist or may have been removed.</p>
                <a href="<?= $base_url ?>index.php" class="theme-btn">Back to Homepage</a>
            </div>
        <?php elseif ($ad['status'] === 'expired'):    
                ?>
            <div class="text-center mt-5 mb-5">
                <h1 class="text-danger">This Ad Has Expired</h1>
                <p>This listing is no longer available.</p>
                <a href="<?= $base_url ?>index.php" class="theme-btn">Back to Homepage</a>
            </div>
        <?php else:    
                ?>
            <div class="row">
                <div class="col-lg-8">
                    <?php
                       
                    $ad_image = !empty($ad['image']) ? $base_url . 'assets/uploads/ads_form/' . $ad['image'] : $base_url . 'assets/images/placeholder-ad.png';
                    $platforms = json_decode($ad['platforms'], true);
                    ?>
                    <div class="user-infos d-flex align-items-center mb-7 gap-3 d-block d-lg-none">
                        <img src="<?= $base_url ?>assets/images/userimage.png" alt="">
                        <h1 class="fos-20 poppins-regular"><?= htmlspecialchars($ad['user_name']) ?></h1>
                    </div>
                    <div>
                        <h1 class=" single-post-title fos-24 poppins-regular mb-5 d-block d-lg-none">
                            <?= htmlspecialchars($ad['ad_title']) ?>
                        </h1>

                        <img src="<?= $ad_image ?>" class="w-full max-h-[450px] mb-3 post-image-single" alt="">
                    </div>

                    <div class="loc d-flex mb-2.5 d-block d-lg-none">
                        <img src="<?php echo $base_url; ?>assets/images/location-black.svg" alt="" class="me-3">
                        <h1 class="fos-16 poppins-regular m-0"><?= htmlspecialchars($ad['location']) ?></h1>
                    </div>


                    <div class="price-div mb-2.5 d-block d-lg-none">
                        <h1 class="fos-40 poppins-medium color-pink">$<?= htmlspecialchars($ad['asking_price']) ?>
                        </h1>
                    </div>

                    <div class="contact-btn-div mb-12 d-block d-lg-none">
                        <a href="#" class="theme-btn text-decoration-none mb-12">Contact Us</a>
                    </div>





                    <div class="details-ad">
                        <h1 class="single-ad-email fos-16 poppins-bold">📧 <?= htmlspecialchars($ad['email']) ?></h1>

                                                   <?php
                           
                        $platform_names = !empty($platforms) ? array_column($platforms, 'platform') : [];
                           
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
                                                   <?php if (!empty($platforms)): ?>
                            <div class="single-ad-social fos-16 poppins-bold mb-7">
                                <span class="poppins-regular">My Social Media Accounts:</span>
                                <?php foreach ($platforms as $platform_item): ?>
                                    <a href="<?= htmlspecialchars($platform_item['link']) ?>" target="_blank"
                                        class="social-link-tag">
                                        <?= htmlspecialchars($platform_item['platform']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                                                   <?php if (!empty($platforms)): ?>
                            <?php foreach ($platforms as $platform_item): ?>
                                <h1 class="single-ad-platform-link fos-16 poppins-bold">Link:
                                    <span class="poppins-regular color-pink">
                                        <a href="<?= htmlspecialchars($platform_item['link']) ?>" target="_blank"
                                            class="text-decoration-none color-pink">
                                            <?= htmlspecialchars($platform_item['link']) ?>
                                        </a>
                                    </span>
                                </h1>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="user-infos d-lg-flex align-items-center mb-4 gap-3 d-none">
                        <img src="<?php echo $base_url; ?>assets/images/userimage.png" class="" alt="">
                        <h1 class="fos-20 poppins-regular"><?= htmlspecialchars($ad['user_name']) ?></h1>
                    </div>
                    <h1 class="fos-40 poppins-regular mb-5 d-none d-lg-block"><?= htmlspecialchars($ad['ad_title']) ?></h1>
                    <div class="loc d-lg-flex mb-5 d-none">
                        <img src="<?php echo $base_url; ?>assets/images/location-black.svg" alt="" class="me-3">
                        <h1 class="fos-16 poppins-regular m-0"><?= htmlspecialchars($ad['location']) ?></h1>
                    </div>
                    <div class="price-div mb-5 d-none d-lg-block">
                        <h1 class="fos-40 poppins-medium mb-5 color-pink">$<?= htmlspecialchars($ad['asking_price']) ?>
                        </h1>
                    </div>
                    <div class="contact-btn-div mb-7 d-none d-lg-block">
                        <a href="#" class="theme-btn text-decoration-none mb-7">Contact Us</a>
                    </div>


                                              
                    <?php if (!$user): ?>
                        <div class="text-center mt-2">
                            <p>You must be logged in to write a review.</p>
                            <a href="/login" class="theme-btn">Login to Continue</a>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <h4 class="poppins-medium">Welcome,
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!
                            </h4>
                        </div>

                                                   <div class="reviews-col">
                            <div class="reviews-col-text mb-4">
                                <h1 class="poppins-medium fos-20">How would you rate the overall user experience of our App?
                                </h1>
                                <h1 class="fos-14 poppins-regular mb-4">Do you find the app easy to use?</h1>

                                                                   <div class="review-stars d-flex justify-content-between mb-4" id="starContainer"
                                    style="gap: 10px;">
                                    <input type="hidden" name="rating" id="ratingInput" value="0">
                                    <i class="fa fa-star-o star" data-value="1"></i>
                                    <i class="fa fa-star-o star" data-value="2"></i>
                                    <i class="fa fa-star-o star" data-value="3"></i>
                                    <i class="fa fa-star-o star" data-value="4"></i>
                                    <i class="fa fa-star-o star" data-value="5"></i>
                                </div>

                                                                   <form action="" method="POST" id="reviewForm">
                                    <input type="hidden" name="ad_id" value="<?php echo $ad_id; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="rating" id="ratingHidden" value="0">

                                    <div class="review-comment">
                                                                                   <textarea name="comment" class="mb-4 w-full" rows="5" required
                                            id="reviewCommentTextarea"></textarea>

                                                                                   <div class="d-flex justify-content-between">
                                            <button type="submit" class="theme-btn" id="submitReviewBtn"
                                                disabled>Submit</button>

                                            <a href="#" class="theme-btn text-decoration-none" id="cancelReviewBtn"
                                                style="display: none;">Cancel</a>
                                        </div>

                                                                               </div>
                                </form>
                                <div id="reviewMsg" class="mt-2 text-success"></div>

                            </div>
                        </div>
                    <?php endif; ?>






                                              
                </div>
            </div>
        <?php endif;    
            ?>
    </div>
</section>
      

      

      <?php if ($ad && $ad['status'] === 'live'):    
        ?>
    <section id="reviews-section" class="pb-6">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h3 class="mb-4">User Reviews (<?= $total_reviews ?>)</h3>
                    <div id="reviews-list">
                        <?php if (!empty($initial_reviews)): ?>
                            <?php foreach ($initial_reviews as $review): ?>
                                <div class="review-item border-bottom pb-3 mb-3">
                                    <div class="review-header d-flex justify-content-between align-items-center">
                                        <h5 class="poppins-medium m-0">
                                            <?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?>
                                        </h5>
                                        <div class="review-stars-display">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fa <?= ($i <= $review['rating']) ? 'fa-star' : 'fa-star-o' ?>"
                                                    style="color:    
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

                                           <?php if ($total_reviews > $reviews_per_page): ?>
                        <div id="see-more-container" class="text-center mt-4">
                            <a href="#" id="see-more-reviews" class="color-pink poppins-medium text-decoration-none"
                                data-ad-id="<?= $ad_id ?>">See More Reviews</a>
                            <div id="loading-spinner" style="display: none;">Loading...</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>


         





      <?php include_once(__DIR__ . '/../../partials/ad.php'); ?>
      
         <section>
    <div class="container">
        <div class="row">
            <h1 class="fos-40 playfair-regular text-center text-md-start mb-7">More Related Ads</h1>
            <?php if ($related_ads_result && $related_ads_result->num_rows > 0): ?>
                <div class="row mt-5 mx-auto">

                    <?php while ($related = $related_ads_result->fetch_assoc()):
                           
                        $related_image = !empty($related['image'])
                            ? $base_url . 'assets/uploads/ads_form/' . $related['image']
                            : $base_url . 'assets/images/test-img.png';
                        $related_title = htmlspecialchars($related['ad_title']);
                        $related_price = htmlspecialchars($related['asking_price']);
                        $related_location = htmlspecialchars($related['location']);
                        $related_link = $base_url . "ads/" . $related['ad_slug'];
                           
                        ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <a href="<?= $related_link ?>" class="text-decoration-none text-dark">
                                <div class="card position-relative h-100">
                                    <div class="ad-tag absolute top-2.5 left-2.5 text-white px-3 py-1 rounded-sm text-sm z-10">
                                        Ad</div>
                                    <div class="card-img-ad">
                                        <img src="<?= $related_image ?>" class="img-ads" alt="<?= $related_title ?>">
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
                                    <button
                                        class="position-absolute top-0 end-0 bg-white border-0 m-2 rounded-circle shadow-sm p-1">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <p>No related ads found in this category.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
         










      <?php
include_once(__DIR__ . '/../../partials/footer.php');

?>
      
<script>
    function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<i class="fa ${i <= rating ? 'fa-star' : 'fa-star-o'}" 
                    style="color:    
    }
    return stars;
}

function formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}
       
    document.addEventListener("DOMContentLoaded", function () {

           
        const reviewForm = document.getElementById("reviewForm");

           
        if (reviewForm) {
            const starContainer = document.getElementById("starContainer");

               
            const stars = starContainer.querySelectorAll(".star");

            const ratingInput = document.getElementById("ratingHidden");
            const msgBox = document.getElementById("reviewMsg");
            let currentRating = 0;    

               
            function updateStars(rating) {
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
            }

               
            stars.forEach((star) => {
                   
                star.addEventListener("mouseover", () => {
                    const rating = parseInt(star.getAttribute("data-value"));
                    updateStars(rating);
                });

                   
                star.addEventListener("mouseout", () => {
                    updateStars(currentRating);
                });

                   
                star.addEventListener("click", () => {
                    const rating = parseInt(star.getAttribute("data-value"));
                    currentRating = rating;    
                    ratingInput.value = currentRating;    
                    updateStars(currentRating);
                });
            });

               
            reviewForm.addEventListener("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(reviewForm);
                msgBox.textContent = 'Submitting...';
                msgBox.style.color = "blue";

                fetch('/ajax/submit_review_ajax.php', {
                    method: "POST",
                    body: formData
                })
                    .then(response => {
                           
                        if (response.ok) {
                               
                            return response.json();
                        } else {
                               
                               
                            return response.json().then(errorData => {
                                   
                                   
                                let err = new Error(errorData.message || 'Server returned an error.');
                                err.data = errorData;
                                throw err;
                            });
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            msgBox.textContent = "Review submitted successfully!";
                            msgBox.style.color = "green";
                            
                               
                            const newReview = `
                                <div class="review-item border-bottom pb-3 mb-3">
                                    <div class="review-header d-flex justify-content-between align-items-center">
                                        <h5 class="poppins-medium m-0">
                                            ${data.reviewData.user_name}
                                        </h5>
                                        <div class="review-stars-display">
                                            ${generateStars(data.reviewData.rating)}
                                        </div>
                                    </div>
                                    <small class="text-muted">${formatDate(new Date())}</small>
                                    <p class="mt-2">${data.reviewData.comment}</p>
                                </div>
                            `;
                            
                               
                            document.getElementById('reviews-list').insertAdjacentHTML('afterbegin', newReview);
                            
                               
                            const reviewsCount = document.querySelector('h3.mb-4');
                            const currentCount = parseInt(reviewsCount.textContent.match(/\d+/)[0]);
                            reviewsCount.textContent = reviewsCount.textContent.replace(
                                /\d+/, 
                                currentCount + 1
                            );

                               
                            reviewForm.reset();
                            currentRating = 0;
                            ratingInput.value = 0;
                            updateStars(0);
                        } else {
                            msgBox.textContent = data.message || "An unknown error occurred.";
                            msgBox.style.color = "red";
                        }
                    })
                    .catch(err => {
                           
                           
                        console.error('Fetch Error:', err);
                        msgBox.textContent = err.message || "A network error occurred. Please try again.";
                        msgBox.style.color = "red";
                    });
            });
        }


           
           
        if (typeof jQuery !== 'undefined') {
                let currentPage = 1;
                $('#see-more-reviews').on('click', function (e) {
                    e.preventDefault();

                    currentPage++;
                    const adId = $(this).data('ad-id');
                    const seeMoreBtn = $(this);
                    const loadingSpinner = $('#loading-spinner');

                    $.ajax({
                        url: '/ajax/load_more_reviews.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ad_id: adId,
                            page: currentPage
                        },
                        beforeSend: function () {
                            seeMoreBtn.hide();
                            loadingSpinner.show();
                        },
                        success: function (response) {
                            if (response.html) {
                                   
                                $('#reviews-list').append(response.html);
                            }

                               
                            if (!response.hasMore) {
                                $('#see-more-container').hide();
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            alert('An error occurred while loading more reviews. Please try again.');
                            $('#see-more-container').hide();
                        },
                        complete: function () {
                            loadingSpinner.hide();
                               
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
           
        const textarea = document.getElementById('reviewCommentTextarea');
        const submitBtn = document.getElementById('submitReviewBtn');
        const cancelBtn = document.getElementById('cancelReviewBtn');

           
        if (!textarea || !submitBtn || !cancelBtn) {
            return;
        }

           
        function updateButtonStates() {
               
            const hasText = textarea.value.trim().length > 0;

               
            submitBtn.disabled = !hasText;

               
            cancelBtn.style.display = hasText ? 'flex' : 'none';
        }

           
           
        textarea.addEventListener('input', updateButtonStates);

           
        cancelBtn.addEventListener('click', function (event) {
            event.preventDefault();    

               
            textarea.value = '';

               
            updateButtonStates();
        });
    });
</script>