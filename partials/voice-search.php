<?php
include_once("../config/config.php");

$base_url = "http://punjab-classified-site-php.test/";
$q = isset($_GET['q']) ? trim($conn->real_escape_string($_GET['q'])) : '';
if ($q === '') {
    echo "<p>No search term provided.</p>";
    exit;
}

$normalized_q = strtolower(str_replace(' ', '', $q));
function searchAds($term, $conn)
{
    $term = strtolower(str_replace(' ', '', $term));
    $sql = "SELECT id, ad_title, location, image FROM ad_form
            WHERE 
                REPLACE(LOWER(ad_title), ' ', '') LIKE '%$term%' OR
                REPLACE(LOWER(description), ' ', '') LIKE '%$term%' OR
                REPLACE(LOWER(email), ' ', '') LIKE '%$term%' OR
                REPLACE(LOWER(phone), ' ', '') LIKE '%$term%' OR
                REPLACE(LOWER(user_name), ' ', '') LIKE '%$term%' OR
                REPLACE(LOWER(asking_price), ' ', '') LIKE '%$term%' OR
                REPLACE(LOWER(category), ' ', '') LIKE '%$term%' OR
                REPLACE(LOWER(subcategory), ' ', '') LIKE '%$term%'
            LIMIT 10";
    return $conn->query($sql);
}
$exactResults = searchAds($q, $conn);
echo "<div style='display: flex; gap: 40px;'>";
echo "<div style='flex: 1;'>";
echo "<h4 style='color: white;'>Exact Match</h4>";
if ($exactResults->num_rows > 0) {
    while ($row = $exactResults->fetch_assoc()) {
        $link = $base_url . "single-ad.php?id=" . $row['id'];
        echo "<div class='search-result-item'>";
        echo "<a href='$link' style='color: white; display: block; margin: 10px 0;'>";
        echo "<strong>" . htmlspecialchars($row['ad_title']) . "</strong><br>";
        echo "<small>" . htmlspecialchars($row['location']) . "</small>";
        echo "</a></div>";
    }
} else {
    echo "<p style='color: white;'>No exact match found for \"$q\".</p>";
}
echo "</div>";
$keywords = explode(' ', $q);
if (count($keywords) > 1) {
    echo "<div style='flex: 1;'>";
    echo "<h4 style='color: white;'>Related to \"" . htmlspecialchars($keywords[0]) . "\"</h4>";
    $word1Results = searchAds($keywords[0], $conn);
    if ($word1Results->num_rows > 0) {
        while ($row = $word1Results->fetch_assoc()) {
            $link = $base_url . "single-ad.php?id=" . $row['id'];
            echo "<div class='search-result-item'>";
            echo "<a href='$link' style='color: white; display: block; margin: 10px 0;'>";
            echo "<strong>" . htmlspecialchars($row['ad_title']) . "</strong><br>";
            echo "<small>" . htmlspecialchars($row['location']) . "</small>";
            echo "</a></div>";
        }
    } else {
        echo "<p style='color: white;'>No related results for \"" . htmlspecialchars($keywords[0]) . "\".</p>";
    }
    echo "</div>";

    echo "<div style='flex: 1;'>";
    echo "<h4 style='color: white;'>Related to \"" . htmlspecialchars($keywords[1]) . "\"</h4>";
    $word2Results = searchAds($keywords[1], $conn);
    if ($word2Results->num_rows > 0) {
        while ($row = $word2Results->fetch_assoc()) {
            $link = $base_url . "single-ad.php?id=" . $row['id'];
            echo "<div class='search-result-item'>";
            echo "<a href='$link' style='color: white; display: block; margin: 10px 0;'>";
            echo "<strong>" . htmlspecialchars($row['ad_title']) . "</strong><br>";
            echo "<small>" . htmlspecialchars($row['location']) . "</small>";
            echo "</a></div>";
        }
    } else {
        echo "<p style='color: white;'>No related results for \"" . htmlspecialchars($keywords[1]) . "\".</p>";
    }
    echo "</div>";
}

echo "</div>";
echo "<hr style='border-top: 1px solid white; margin-top: 20px;'>";
