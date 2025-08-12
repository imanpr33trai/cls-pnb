<?php
include '../config/config.php';

$keyword = isset($_POST['keyword']) ? mysqli_real_escape_string($conn, $_POST['keyword']) : '';
$location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';

$results = [];

    
$ad_query = "SELECT 'ad' AS type, id, ad_title AS title, description, phone, email, location, created_at, ad_slug 
             FROM ad_form 
             WHERE (
                 ad_title LIKE '%$keyword%' OR
                 description LIKE '%$keyword%' OR
                 phone LIKE '%$keyword%' OR
                 email LIKE '%$keyword%' OR
                 location LIKE '%$keyword%' OR
                 city_town_neighbourhood LIKE '%$keyword%' OR
                 organisation LIKE '%$keyword%'
             )";

if (!empty($location)) {
    $ad_query .= " AND (postal_code LIKE '%$location%' OR city_town_neighbourhood LIKE '%$location%')";
}

    
$blog_query = "SELECT 'blog' AS type, id, title, description, phone, email, created_at, blog_slug 
               FROM blog_posts 
               WHERE (
                   title LIKE '%$keyword%' OR
                   description LIKE '%$keyword%' OR
                   phone LIKE '%$keyword%' OR
                   email LIKE '%$keyword%' OR
                   author_name LIKE '%$keyword%'
               )";

if (!empty($location)) {
    $blog_query .= " AND (title LIKE '%$location%' OR category_id LIKE '%$location%')";
}

    
$ads_result = mysqli_query($conn, $ad_query);
$blogs_result = mysqli_query($conn, $blog_query);

if ($ads_result && mysqli_num_rows($ads_result) > 0) {
    while ($row = mysqli_fetch_assoc($ads_result)) {
        $results[] = $row;
    }
}

if ($blogs_result && mysqli_num_rows($blogs_result) > 0) {
    while ($row = mysqli_fetch_assoc($blogs_result)) {
        $results[] = $row;
    }
}

if (empty($results)) {
    echo "<p class='text-white text-center'>No results found.</p>";
} else {
    foreach ($results as $item) {
        $type = $item['type'];
        $title = htmlspecialchars($item['title']);
        $desc = htmlspecialchars(substr($item['description'], 0, 100));
        $id = $item['id'];

            
        $url = ($type === 'ad') ? "ads/" . $item['ad_slug'] : "articles/" . $item['blog_slug'];

        echo "<div class='result-item mb-3 p-3 bg-light rounded'>";
        echo "<a href='$url' class='text-dark text-decoration-none'>";
        echo "<strong>" . strtoupper($type) . "</strong>: <h5>$title</h5>";
        echo "<p>$desc...</p>";
        echo "<small>Posted on: " . htmlspecialchars($item['created_at']) . "</small>";
        echo "</a>";
        echo "</div>";
    }
}
?>