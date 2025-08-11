<?php
// /admin/util/delete_blog.php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_id'])) {
    $blog_id = (int)$_POST['blog_id'];

    if ($blog_id > 0) {
        // First, get the image filename to delete it from the server
        $stmt = $conn->prepare("SELECT image FROM blog_posts WHERE id = ?");
        $stmt->bind_param("i", $blog_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (!empty($row['image'])) {
                $image_path = __DIR__ . '/../../assets/uploads/' . $row['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        $stmt->close();

        // Now, delete the blog post from the database
        $delete_stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
        $delete_stmt->bind_param("i", $blog_id);
        if ($delete_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Blog post deleted successfully!';
        } else {
            $response['message'] = 'Error deleting post: ' . $conn->error;
        }
        $delete_stmt->close();
    } else {
        $response['message'] = 'Invalid Blog ID.';
    }
} else {
    $response['message'] = 'Invalid request method or Blog ID not provided.';
}

echo json_encode($response);
?>