<?php
include_once('../config/config.php');
header('Content-Type: application/json');

$keyword = strtolower(trim($_POST['keyword'] ?? ''));
$location = strtolower(trim($_POST['location'] ?? ''));

$results = [];

if ($keyword || $location) {
    $sql = "SELECT * FROM ad_form WHERE 1=1";

    if (!empty($keyword)) {
        $keyword_safe = $conn->real_escape_string($keyword);
        $sql .= " AND (LOWER(ad_title) LIKE '%$keyword_safe%' OR LOWER(description) LIKE '%$keyword_safe%')";
    }

    if (!empty($location)) {
        $location_safe = $conn->real_escape_string($location);
        $sql .= " AND (LOWER(postal_code) LIKE '%$location_safe%' OR LOWER(city_town_neighbourhood) LIKE '%$location_safe%')";
    }

    $query = $conn->query($sql);

    if ($query && $query->num_rows > 0) {
        while ($row = $query->fetch_assoc()) {
            $results[] = [
                'id' => $row['id'],
                'ad_title' => $row['ad_title'],
                'description' => $row['description'],
                'postal_code' => $row['postal_code'],
                'city_town_neighbourhood' => $row['city_town_neighbourhood'],
                'ad_slug' => $row['ad_slug']
            ];
        }
    }
}

echo json_encode($results);
