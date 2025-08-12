<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../config/config.php';


function send_json_response($status_code, $data)
{
    http_response_code($status_code);
    echo json_encode($data);
    exit();
}
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    send_json_response(401, ['error' => 'Unauthorized. Please log in as an admin.']);
}
$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_GET['id'] ?? null;
switch ($method) {
    case 'GET':
        if ($user_id) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            if (!$stmt) send_json_response(500, ['error' => 'Server Error: Could not prepare statement.']);

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user) {
                unset($user['password']);
                send_json_response(200, $user);
            } else {
                send_json_response(404, ['error' => 'User not found.']);
            }
        } else {
            $query = "SELECT id, first_name, last_name, email, country, status, auth_provider, created_at 
                      FROM users ORDER BY created_at DESC";
            $result = $conn->query($query);
            $users = $result->fetch_all(MYSQLI_ASSOC);
            send_json_response(200, $users);
        }
        break;

    case 'DELETE':
        if (!$user_id) {
            send_json_response(400, ['error' => 'Bad Request: Missing user ID for deletion.']);
        }

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
        send_json_response(405, ['error' => 'Method Not Allowed']);
        break;
}
