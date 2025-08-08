<?php
// /partials/search-handler.php (Secure and Debug-Ready)

// Make this the first line to catch all errors


include_once __DIR__ . '/../config/config.php';

// Prepare the final response array which will always contain a debug key
$response = [
    'results' => [],
    'debug' => [] // We will add debugging information here
];

// Start Debugging
$response['debug'][] = "Handler script initiated.";

try {
    // Check for POST data
    $keyword = trim($_POST['keyword'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $response['debug'][] = "Received POST data: keyword='{$keyword}', location='{$location}'";

    if (empty($keyword) && empty($location)) {
        throw new Exception("No search terms provided.");
    }

    // --- SAFE & DYNAMIC SQL QUERY BUILDING ---
    $sql = "
        SELECT 
            ad.id, ad.ad_title, ad.description, ad.location, 
            ad.city_town_neighbourhood, ad.postal_code,
            ad.ad_slug, cat.slug AS category_slug
        FROM ad_form AS ad
        LEFT JOIN ad_categories AS cat ON ad.category = cat.id
        WHERE ad.status = 'live'
    ";

    $params = [];
    $types = '';

    if (!empty($keyword)) {
        $sql .= " AND (LOWER(ad.ad_title) LIKE ? OR LOWER(ad.description) LIKE ?)";
        $keyword_param = '%' . strtolower($keyword) . '%';
        $params[] = $keyword_param;
        $params[] = $keyword_param;
        $types .= 'ss';
    }
    if (!empty($location)) {
        $sql .= " AND (LOWER(ad.location) LIKE ? OR LOWER(ad.city_town_neighbourhood) LIKE ? OR LOWER(ad.postal_code) LIKE ?)";
        $location_param = '%' . strtolower($location) . '%';
        $params[] = $location_param;
        $params[] = $location_param;
        $params[] = $location_param;
        $types .= 'sss';
    }
    $sql .= " LIMIT 20";

    $response['debug']['sql_query'] = $sql;
    $response['debug']['sql_params'] = $params;

    // --- EXECUTE WITH PREPARED STATEMENT ---
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("SQL prepare failed: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $query_result = $stmt->get_result();

    $response['debug']['num_rows_found'] = $query_result ? $query_result->num_rows : 0;

    if ($query_result) {
        while ($row = $query_result->fetch_assoc()) {
            $response['results'][] = $row;
        }
    }
    $stmt->close();
    $response['debug'][] = "Script finished successfully.";
} catch (Exception $e) {
    // If anything fails, log the error into the debug array
    $response['debug']['error'] = $e->getMessage();
    http_response_code(500); // Set a server error status
}

// --- ALWAYS return a valid JSON response ---
header('Content-Type: application/json');
echo json_encode($response);
