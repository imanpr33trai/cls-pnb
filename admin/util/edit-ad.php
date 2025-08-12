<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ad'])) {
    $ad_id = isset($_POST['ad_id']) ? (int)$_POST['ad_id'] : 0;

    if ($ad_id > 0) {
               $ad_title = htmlspecialchars($_POST['ad_title']);
        $category = (int)$_POST['category'];
        $subcategory = (int)$_POST['subcategory'];
        $asking_price = !empty($_POST['asking_price']) ? (float)$_POST['asking_price'] : null;
        $description = htmlspecialchars($_POST['description']);
        $user_name = htmlspecialchars($_POST['user_name']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $phone = htmlspecialchars($_POST['phone']);
        $location = htmlspecialchars($_POST['location']);
        $status = htmlspecialchars($_POST['status']);

               $update_stmt = $conn->prepare("
            UPDATE ad_form 
            SET 
                ad_title = ?, category = ?, subcategory = ?, asking_price = ?, 
                description = ?, user_name = ?, email = ?, phone = ?, 
                location = ?, status = ?
            WHERE id = ?
        ");
        $update_stmt->bind_param(
            "siidssssssi",
            $ad_title, $category, $subcategory, $asking_price,
            $description, $user_name, $email, $phone,
            $location, $status, $ad_id
        );

        if ($update_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Ad updated successfully!';
        } else {
            $response['message'] = 'Error updating ad: ' . $conn->error;
        }
        $update_stmt->close();
    } else {
        $response['message'] = 'Invalid Ad ID.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>