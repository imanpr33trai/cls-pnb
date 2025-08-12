<?php
// /admin/util/delete_blog_category.php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_cat_id'])) {
    $category_id = (int)$_POST['blog_cat_id'];

    if ($category_id > 0) {
        $stmt = $conn->prepare("DELETE FROM blog_categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Blog category deleted successfully!';
        } else {
            if ($conn->errno === 1451) {
                $response['message'] = 'Error: This category cannot be deleted because it is currently in use by one or more blog posts.';
            } else {
                $response['message'] = 'Error deleting blog category: ' . $conn->error;
            }
        }
        $stmt->close();
    } else {
        $response['message'] = 'Invalid Blog Category ID.';
    }
} else {
    $response['message'] = 'Invalid request method or Blog Category ID not provided.';
}

echo json_encode($response);
?>
