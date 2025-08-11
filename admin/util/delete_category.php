<?php
// /admin/util/delete_category.php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category_id = (int)$_POST['category_id'];

    if ($category_id > 0) {
        // First, get the image filename to delete it from the server
        $stmt = $conn->prepare("SELECT image FROM ad_categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $image_path = __DIR__ . '/../../assets/uploads/' . $row['image'];
            if (!empty($row['image']) && file_exists($image_path)) {
                unlink($image_path);
            }
        }
        $stmt->close();

        // Now, delete the category from the database
        $delete_stmt = $conn->prepare("DELETE FROM ad_categories WHERE id = ?");
        $delete_stmt->bind_param("i", $category_id);
        if ($delete_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Category deleted successfully!';
        } else {
            // Check for foreign key constraint error
            if ($conn->errno === 1451) {
                $response['message'] = 'Error: This category cannot be deleted because it is currently in use by one or more subcategories or ads.';
            } else {
                $response['message'] = 'Error deleting category: ' . $conn->error;
            }
        }
        $delete_stmt->close();
    } else {
        $response['message'] = 'Invalid Category ID.';
    }
} else {
    $response['message'] = 'Invalid request method or Category ID not provided.';
}

echo json_encode($response);
?>