<?php

/**
 * Fetches and renders a grid of ad cards from the database.
 *
 * This function queries for 'live' and non-expired ads,
 * sanitizes the output, and returns the HTML for multiple ad cards.
 *
 * @param mysqli $conn The active database connection.
 * @param string $base_url The base URL for constructing links and image paths.
 * @param int $limit The maximum number of ads to retrieve.
 * @param int $offset The starting offset for the ads to retrieve.
 * @return string The complete HTML for the ad cards grid.
 */
function render_ad_cards(mysqli $conn, string $base_url, int $limit, int $offset = 0): string
{
    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM ad_form WHERE status = 'live' AND expires_at > NOW() ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return '<p class="text-danger">Error: Could not prepare to fetch ads.</p>';
    }

    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $html = '';

    if ($result->num_rows > 0) {
        while ($ad = $result->fetch_assoc()) {
            // Sanitize and prepare data for display
            $img = !empty($ad['image']) ? htmlspecialchars($base_url . 'assets/uploads/ads_form/' . $ad['image'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($base_url . 'assets/images/test-img.png', ENT_QUOTES, 'UTF-8');
            $price = htmlspecialchars($ad['asking_price'], ENT_QUOTES, 'UTF-8');
            $title = htmlspecialchars($ad['ad_title'], ENT_QUOTES, 'UTF-8');
            $location = htmlspecialchars($ad['location'], ENT_QUOTES, 'UTF-8');
            $ad_id = (int)$ad['id'];
            $ad_link = htmlspecialchars($base_url . 'single-ad.php?id=' . $ad_id, ENT_QUOTES, 'UTF-8');
            $heart_icon_link = htmlspecialchars($base_url . 'assets/images/single-ad/heart-icon.svg', ENT_QUOTES, 'UTF-8');
            $location_icon_link = htmlspecialchars($base_url . 'assets/images/location-black.svg', ENT_QUOTES, 'UTF-8');

            // Using HEREDOC for cleaner HTML structure
            $html .= <<<HTML
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card position-relative h-100">
                    <div class="ad-tag poppins-regular">Ad</div>
                    <div class="card-img-ad">
                        <a href="{$ad_link}">
                            <img src="{$img}" class="img-fluid" alt="{$title}">
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="card-price-det">
                            <h5 class="poppins-bold price-post">\${$price}</h5>
                            <a href="#" class="wish-heart">
                                <img src="{$heart_icon_link}" alt="Add to wishlist">
                            </a>
                        </div>
                        <a href="{$ad_link}">
                            <p class="Post-title fos-16 poppins-regular">{$title}</p>
                        </a>
                        <hr>
                        <div class="d-flex align-items-start poppins-regular fos-14">
                            <img src="{$location_icon_link}" alt="location" class="me-2">
                            <small>{$location}</small>
                        </div>
                    </div>
                </div>
            </div>
HTML;
        }
    }

    $stmt->close();
    return $html;
}