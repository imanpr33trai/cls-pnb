<?php
include_once(__DIR__ . '/../config/config.php');

    

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

        
    $html = <<<HTML
    <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/5 p-2">
        <div class="bg-white shadow-md overflow-hidden product-card h-full flex flex-col">
            <div class="relative">
                <div class="ad-tag absolute top-2.5 left-2.5 text-white px-3 py-1 rounded-sm text-sm z-10">Ad</div>
                <div class="product-image-wrapper overflow-hidden">
                    <a href="{$ad_link}"><img src="{$image}" alt="{$name}" class="product-image img-ads w-full h-48 object-cover transition-transform duration-300 ease-in-out"></a>
                </div>
            </div>
            <div class="p-4 flex flex-col flex-grow">
                <div class="flex justify-between items-center mb-2">
                    <h5 class="product-price text-lg font-bold">{$formattedPrice}</h5>
                    <a href="#" class="wish-heart text-black hover:text-pink duration-200 p-2 rounded-full"><span class="icon">{$iconHeart}</span></a>
                </div>
                <a href="{$ad_link}" class="text-decoration-none "><p class="product-name text-gray-800 hover:text-pink transition-colors duration-200">{$name}</p></a>
                <hr class="my-3">
                <div class="mt-auto flex items-center text-gray-500 text-sm">
                    <div class="location-icon mr-1">{$iconLocation}</div>
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

    $html = '<div class="flex flex-wrap -mx-2">';

        
    $sql = "SELECT ad_title, asking_price, image, ad_slug, location FROM ad_form WHERE status = 'live' AND expires_at > NOW() ORDER BY id DESC LIMIT ? OFFSET ?";

        
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "DEBUG: Prepare failed: " . $conn->error . "<br>";
        return $html . '</div>';
    }
    $stmt->bind_param("ii", $limit, $offset);     
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $num_rows = $result->num_rows;

        if ($num_rows > 0) {
            while ($ad = $result->fetch_assoc()) {
                    
                $card_data = [
                    'name' => $ad['ad_title'],
                    'price' => $ad['asking_price'],
                    'location' => $ad['location'],
                    'image' => !empty($ad['image']) ? $base_url . 'assets/uploads/ads_form/' . $ad['image'] : $base_url . 'assets/images/test-img.png',
                    'ad_link' => $base_url . 'ads/' . $ad['ad_slug']
                ];
                    
                $html .= render_product_card($card_data);
            }
        } else {
                
            if ($offset === 0) {
                $html = '<div class="w-full"><p class="text-center text-gray-500">No active ads found.</p></div>';
            }
        }
    } else {
        echo "DEBUG: Query failed: " . $conn->error . "<br>";
    }

    $stmt->close();
    $html .= '</div>';
    return $html;
}

    
