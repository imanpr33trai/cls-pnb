<?php
include_once("../config/config.php");


$query_string = trim($_GET['q'] ?? '');
$category_id = $_GET['cat'] ?? 'all';

if (strlen($query_string) < 2) {
    exit();
}


$sql_params = [];
$sql_param_types = '';


$sql = "SELECT ad_title, image, ad_slug FROM ad_form WHERE ad_title LIKE ?";
$sql_params[] = "%" . $query_string . "%";
$sql_param_types .= 's';


if ($category_id !== 'all' && is_numeric($category_id)) {
    
    
    
    $sql .= " AND category = ?";

    $sql_params[] = (int)$category_id;
    $sql_param_types .= 'i';
}

$sql .= " ORDER BY created_at DESC LIMIT 10";


$stmt = $conn->prepare($sql);

if ($stmt) {
    
    $stmt->bind_param($sql_param_types, ...$sql_params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        while ($row = $result->fetch_assoc()) {
            $img_url = !empty($row['image'])
                ? $base_url . "assets/uploads/ads_form/" . htmlspecialchars($row['image'])
                : $base_url . "assets/images/test-img.png";

            $ad_title = htmlspecialchars($row['ad_title']);
            $ad_slug = htmlspecialchars($row['ad_slug']);
            $link_url = $base_url . 'ads/' . $ad_slug;

            echo <<<HTML
                <a href="{$link_url}" class="text-decoration-none search-result-item">
                    <img src="{$img_url}" alt="Ad Image">
                    <h6>{$ad_title}</h6>
                </a>
            HTML;
        }
    } else {
        echo "<div class='text-muted text-center p-2'>No results found</div>";
    }
    $stmt->close();
} else {
    
    
    error_log("Search query failed to prepare: " . $conn->error);
    echo "<div class='text-danger text-center p-2'>A database error occurred.</div>";
}
