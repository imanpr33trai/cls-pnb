<?php
// /admin/util/delete_ad.php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ad_id'])) {
    $ad_id = (int)$_POST['ad_id'];

    if ($ad_id > 0) {
        // First, get the image filename to delete it from the server
        $stmt = $conn->prepare("SELECT image FROM ad_form WHERE id = ?");
        $stmt->bind_param("i", $ad_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $image_path = __DIR__ . '/../../assets/uploads/' . $row['image'];
            if (!empty($row['image']) && file_exists($image_path)) {
                unlink($image_path);
            }
        }
        $stmt->close();

        // Now, delete the ad from the database
        $delete_stmt = $conn->prepare("DELETE FROM ad_form WHERE id = ?");
        $delete_stmt->bind_param("i", $ad_id);
        if ($delete_stmt->execute()) {
            $response['success'] = true;
            
        } else {
            $response['message'] = 'Error deleting ad: ' . $conn->error;
        }
        $delete_stmt->close();
    } else {
        $response['message'] = 'Invalid Ad ID.';
    }
} else {
    $response['message'] = 'Invalid request method or Ad ID not provided.';
}

echo json_encode($response);
?>



