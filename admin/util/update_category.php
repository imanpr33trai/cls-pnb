<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category_id = (int)$_POST['category_id'];
    $category_name = htmlspecialchars(trim($_POST['category_name']));
    $status = htmlspecialchars(trim($_POST['status']));
    
       $stmt = $conn->prepare("SELECT image FROM ad_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_category = $result->fetch_assoc();
    $new_image_name = $existing_category['image'];
    $stmt->close();

       if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
        $upload_dir = __DIR__ . '/../../assets/uploads/';
               if (!empty($new_image_name) && file_exists($upload_dir . $new_image_name)) {
            unlink($upload_dir . $new_image_name);
        }
               $new_image_name = time() . '_' . basename($_FILES['category_image']['name']);
        $target_file = $upload_dir . $new_image_name;
        if (!move_uploaded_file($_FILES['category_image']['tmp_name'], $target_file)) {
            $response['message'] = 'Error uploading new image.';
            echo json_encode($response);
            exit;
        }
    }

    $update_stmt = $conn->prepare("UPDATE ad_categories SET name = ?, image = ?, status = ? WHERE id = ?");
    $update_stmt->bind_param("sssi", $category_name, $new_image_name, $status, $category_id);

    if ($update_stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Category updated successfully!';
    } else {
        $response['message'] = 'Error updating category: ' . $conn->error;
    }
    $update_stmt->close();
} else {
    $response['message'] = 'Invalid request method or missing data.';
}

echo json_encode($response);
?>