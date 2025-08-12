<?php
define('AJAX_REQUEST', true);



header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../config/config.php');


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$response = [];

$ad_id = isset($_POST['ad_id']) ? intval($_POST['ad_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if ($ad_id && $user_id && $rating >= 1 && $rating <= 5 && !empty($comment)) {
    
    $user_stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();
    $user_stmt->close();

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    
    $stmt = $conn->prepare("INSERT INTO ad_reviews (ad_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $ad_id, $user_id, $rating, $comment);
    if ($stmt->execute()) {
        
        $user_name = $user['first_name'] . ' ' . $user['last_name'];

        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully',
            'reviewData' => [
                'user_name' => $user_name,
                'rating' => $rating,
                'comment' => $comment,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
        exit;
    } else {
        $response = ["success" => false, "message" => "Database error occurred while saving your review."];
    }
    $stmt->close();
} else {
    $response = ["success" => false, "message" => "Invalid input. Please ensure all fields are filled correctly."];
}


echo json_encode($response);
exit;