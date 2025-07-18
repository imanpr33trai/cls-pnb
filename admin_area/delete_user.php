<?php
session_start();
include_once(__DIR__ . '/../config/config.php'); // Adjust path as needed

// Basic security check: Only allow if an admin is logged in
// You might need to adjust 'admin_id' based on your actual admin session variable
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirect to admin login page
    exit();
}

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    if ($user_id <= 0) {
        $response['message'] = 'Invalid user ID provided.';
    } else {
        // Prepare a delete statement
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'User deleted successfully.';
            } else {
                $response['message'] = 'User not found or already deleted.';
            }
        } else {
            $response['message'] = 'Database error: ' . $stmt->error;
            error_log("Error deleting user ID {$user_id}: " . $stmt->error);
        }
        $stmt->close();
    }
} else {
    $response['message'] = 'No user ID specified for deletion.';
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>