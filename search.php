<?php
include_once("config/config.php");

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat = isset($_GET['cat']) ? $_GET['cat'] : 'all';

if (strlen($q) < 2) {
    exit;
}

$q = $conn->real_escape_string($q);
$condition = "WHERE (ad_title LIKE '%$q%' OR description LIKE '%$q%')";


if ($cat !== 'all') {
    $cat = (int)$cat;
    $condition .= " AND category = '$cat'";
}

        $query = "SELECT id, ad_title, image, ad_slug FROM ad_form $condition ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $img = !empty($row['image']) 
            ? $base_url . "assets/uploads/ads_form/" . $row['image'] 
            : $base_url . "assets/images/test-img.png";
        $title = htmlspecialchars($row['ad_title']);
        $slug = $row['ad_slug'];
        echo <<<HTML
            <a href="ads/$slug" class="text-decoration-none">
                <div class="search-result-item">
                    <img src="$img" alt="Ad Image">
                    <h6>$title</h6>
                </div>
                <hr>
            </a>
        HTML;
    }
} else {
    echo "<div class='text-muted text-center'>No results found</div>";
}
?>