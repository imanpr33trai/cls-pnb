<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = isset($_POST['blog_category_id']) ? (int)$_POST['blog_category_id'] : 0;
    $name = trim($_POST['blog_category_name'] ?? '');
    $status = trim($_POST['status'] ?? 'paused');

    if ($category_id > 0 && !empty($name)) {
        $stmt = $conn->prepare("UPDATE blog_categories SET name = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $status, $category_id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Blog category updated successfully!';
        } else {
            $response['message'] = 'Error updating blog category: ' . $conn->error;
        }
        $stmt->close();
    } else {
        $response['message'] = 'Invalid data provided.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
