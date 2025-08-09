<?php
// /api/v1/users.php

header("Content-Type: application/json");
require_once __DIR__ . '/../../config/config.php';

/**
 * A helper function to send a standardized JSON response and exit.
 */
function send_json_response($status_code, $data) {
    http_response_code($status_code);
    echo json_encode($data);
    exit();
}

// --- SECURITY: Check if an admin is logged in ---
if (session_status() === PHP_SESSION_NONE) session_start();
// Replace 'admin_id' with your actual admin session variable if it's different
if (!isset($_SESSION['admin_id'])) {
    send_json_response(401, ['error' => 'Unauthorized. Please log in as an admin.']);
}

// Get the request method and the user ID from the router
$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_GET['id'] ?? null;

// Use a switch to handle different HTTP methods (GET, DELETE, etc.)
switch ($method) {
    // --- HANDLE GET REQUESTS (View Users) ---
    case 'GET':
        if ($user_id) {
            // --- VIEW FULL DETAILS OF A SINGLE USER ---
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            if (!$stmt) send_json_response(500, ['error' => 'Server Error: Could not prepare statement.']);
            
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user) {
                // Security: NEVER send the password hash to the frontend.
                unset($user['password']);
                send_json_response(200, $user);
            } else {
                send_json_response(404, ['error' => 'User not found.']);
            }
        } else {
            // --- VIEW A LIST OF ALL USERS ---
            $query = "SELECT id, first_name, last_name, email, country, status, auth_provider, created_at 
                      FROM users ORDER BY created_at DESC";
            $result = $conn->query($query);
            $users = $result->fetch_all(MYSQLI_ASSOC);
            send_json_response(200, $users);
        }
        break;

    // --- HANDLE DELETE REQUESTS ---
    case 'DELETE':
        if (!$user_id) {
            send_json_response(400, ['error' => 'Bad Request: Missing user ID for deletion.']);
        }
        
        // Use a secure prepared statement to delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if (!$stmt) send_json_response(500, ['error' => 'Server Error: Could not prepare statement.']);

        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                send_json_response(200, ['message' => 'User deleted successfully.']);
            } else {
                send_json_response(404, ['error' => 'User not found or already deleted.']);
            }
        } else {
            send_json_response(500, ['error' => 'Database error during deletion.']);
        }
        $stmt->close();
        break;

    default:
        // Handle other methods like POST or PUT if you don't support them yet
        send_json_response(405, ['error' => 'Method Not Allowed']);
        break;
}
?>