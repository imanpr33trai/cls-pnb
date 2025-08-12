<?php

require_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');


if (!isset($_SESSION['admins_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    exit;
}


if ($user_id == 1 || $user_id == $_SESSION['admin_id']) {
    echo json_encode(['success' => false, 'message' => 'This user cannot be suspended.']);
    exit;
}


$stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}


$new_status = $user['status'] === 'active' ? 'suspended' : 'active';


$stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'new_status' => $new_status]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed.']);
}

$stmt->close();
