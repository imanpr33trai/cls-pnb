<?php
// /admin/util/update_blog.php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_id'])) {
    $blog_id = (int)$_POST['blog_id'];
    $title = htmlspecialchars(trim($_POST['title']));
    $author_name = htmlspecialchars(trim($_POST['author_name']));
    $category_id = (int)$_POST['category_id'];
    $content = trim($_POST['content']);

    // Fetch current image
    $stmt = $conn->prepare("SELECT image FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_blog = $result->fetch_assoc();
    $new_image_name = $existing_blog['image'];
    $stmt->close();

    // Handle file upload
    if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] == 0) {
        $upload_dir = __DIR__ . '/../../assets/uploads/';
        if (!empty($new_image_name) && file_exists($upload_dir . $new_image_name)) {
            unlink($upload_dir . $new_image_name);
        }
        $new_image_name = time() . '_' . basename($_FILES['blog_image']['name']);
        $target_file = $upload_dir . $new_image_name;
        if (!move_uploaded_file($_FILES['blog_image']['tmp_name'], $target_file)) {
            $response['message'] = 'Error uploading new image.';
            echo json_encode($response);
            exit;
        }
    }

    $update_stmt = $conn->prepare("UPDATE blog_posts SET title = ?, author_name = ?, category_id = ?, description = ?, image = ? WHERE id = ?");
    $update_stmt->bind_param("ssissi", $title, $author_name, $category_id, $content, $new_image_name, $blog_id);

    if ($update_stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Blog post updated successfully!';
    } else {
        $response['message'] = 'Error updating post: ' . $conn->error;
    }
    $update_stmt->close();
} else {
    $response['message'] = 'Invalid request method or missing data.';
}

echo json_encode($response);
?>