<?php
// /admin/util/toggle_user_status.php
require_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');

// Check if user is admin (you might have a more robust check)
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    exit;
}

// Can't suspend the main admin or oneself
// In a real app, you'd have a better way to identify the super admin
if ($user_id == 1 || $user_id == $_SESSION['admin_id']) {
    echo json_encode(['success' => false, 'message' => 'This user cannot be suspended.']);
    exit;
}

// Get current status
$stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// Determine the new status
$new_status = $user['status'] === 'active' ? 'suspended' : 'active';

// Update the status
$stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'new_status' => $new_status]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed.']);
}

$stmt->close();
$conn->close();