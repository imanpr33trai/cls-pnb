<?php
include_once(__DIR__ . '/config/config.php');

     // Homepage

/**
 * Renders a single, static product card.
 * UPDATED: The 'col-*' classes have been REMOVED from the outer div.
 * The component is now just the card itself.
 *
 * @param array $product An associative array of product data.
 * @return string The complete HTML for the product card.
 */
function render_product_card(array $product): string
{
    // ... (all the data preparation and sanitization code is the same) ...
    $defaults = ['name' => 'Ad Title Missing', 'price' => 0.00, 'image' => 'assets/images/test-img.png', 'location' => 'Not specified', 'ad_link' => '#', 'currencySymbol' => ''];
    $product = array_merge($defaults, $product);
    $name = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
    $image = htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8');
    $currencySymbol = htmlspecialchars($product['currencySymbol'], ENT_QUOTES, 'UTF-8');
    $location = htmlspecialchars($product['location'], ENT_QUOTES, 'UTF-8');
    $ad_link = htmlspecialchars($product['ad_link'], ENT_QUOTES, 'UTF-8');
    $price = is_numeric($product['price']) ? (float) $product['price'] : 0;
    $formattedPrice = $currencySymbol . number_format($price, 2);
    $iconHeart = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
    $iconLocation = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>';

    // --- HTML Structure ---
    // The outer div now has NO `col-` classes. It is just a placeholder for the card.
    $html = <<<HTML
    <div class="col">
        <div class="card product-card w-100 h-100 position-relative">
            <div class="ad-tag">Ad</div>
            <div class="product-image-wrapper">
                <a href="{$ad_link}"><img src="{$image}" alt="{$name}" class="product-image img-fluid"></a>
            </div>
            <div class="card-body product-content d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="product-price">{$formattedPrice}</h5>
                    <a href="#" class="wish-heart">{$iconHeart}</a>
                </div>
                <a href="{$ad_link}" class="text-decoration-none"><p class="product-name">{$name}</p></a>
                <hr class="my-2">
                <div class="mt-auto d-flex align-items-center text-muted small">
                    <div class="location-icon">{$iconLocation}</div>
                    <span>{$location}</span>
                </div>
            </div>
        </div>
    </div>
HTML;
    return $html;
}

/**
 * Function 2: The Database API
 * UPGRADED to accept limit and offset for pagination.
 * UPGRADED to use prepared statements for security.
 *
 * @param mysqli $conn The database connection object.
 * @param string $base_url The base URL for constructing paths.
 * @param int $limit The number of ads to fetch.
 * @param int $offset The starting point for the ads (for pagination).
 * @return string HTML content of all the ad cards.
 */
function render_ads_from_database(mysqli $conn, string $base_url, int $limit = 8, int $offset = 0): string
{

    $html = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">';

    // The SQL query is now a template for a prepared statement
    $sql = "SELECT ad_title, asking_price, image, ad_slug, location FROM ad_form WHERE status = 'live' AND expires_at > NOW() ORDER BY id DESC LIMIT ? OFFSET ?";

    // Prepare, bind parameters, and execute. This prevents SQL injection.
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "DEBUG: Prepare failed: " . $conn->error . "<br>";
        return $html . '</div>';
    }
    $stmt->bind_param("ii", $limit, $offset); // "ii" means two integer parameters
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $num_rows = $result->num_rows;
        
        if ($num_rows > 0) {
            while ($ad = $result->fetch_assoc()) {
                // Map the database columns to the keys our component expects
                $card_data = [
                    'name'      => $ad['ad_title'],
                    'price'     => $ad['asking_price'],
                    'location'  => $ad['location'],
                    'image'     => !empty($ad['image']) ? $base_url . 'assets/uploads/ads_form/' . $ad['image'] : $base_url . 'assets/images/test-img.png',
                    'ad_link'   => $base_url . 'ads/' . $ad['ad_slug']
                ];
                // Call the component function for each ad and append the HTML
                $html .= render_product_card($card_data);
            }
        } else {
            // Only show this message if it's the very first batch (offset=0) and no ads are found.
            if ($offset === 0) {
                $html = '<div class="col-12"><p class="text-center">No active ads found.</p></div>';
            }
        }
    } else {
        echo "DEBUG: Query failed: " . $conn->error . "<br>";
    }

    $stmt->close();
    $html .= '</div>';
    return $html;
}

// Call the function to render ads when products.php is accessed directly

