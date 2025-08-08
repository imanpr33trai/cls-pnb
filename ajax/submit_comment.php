<?php
define('AJAX_REQUEST', true);
include_once('../config/config.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_id = isset($_POST['blog_id']) ? intval($_POST['blog_id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($blog_id > 0 && $user_id > 0 && !empty($comment)) {
        try {
            // 1. Fetch the user's name from the users table
            $user_stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
            $user_stmt->bind_param("i", $user_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();

            if ($user_row = $user_result->fetch_assoc()) {
                $user_name = trim($user_row['first_name'] . ' ' . $user_row['last_name']);

                // 2. Insert the comment with the fetched user_name
                $insert_stmt = $conn->prepare("INSERT INTO blog_comments (blog_id, user_name, comment) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("iss", $blog_id, $user_name, $comment);

                if ($insert_stmt->execute()) {
                    $response = ['success' => true];
                } else {
                    $response['message'] = 'Failed to save comment to the database.';
                }
                $insert_stmt->close();
            } else {
                $response['message'] = 'Could not find user information.';
            }
            $user_stmt->close();
        } catch (Exception $e) {
            // Log error if needed: error_log($e->getMessage());
            $response['message'] = 'A server error occurred. Please try again later.';
        }
    } else {
        $response['message'] = 'Invalid input. Please make sure all fields are filled.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;