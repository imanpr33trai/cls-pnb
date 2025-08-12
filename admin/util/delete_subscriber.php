<?php
// /admin/util/delete_subscriber.php
require_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');

// Basic security: Check if an admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Get the subscriber ID from the POST request
$subscriber_id = $_POST['subscriber_id'] ?? null;

if (!$subscriber_id || !filter_var($subscriber_id, FILTER_VALIDATE_INT)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Subscriber ID provided.']);
    exit;
}

// Prepare and execute the deletion
$stmt = $conn->prepare("DELETE FROM subscribers WHERE id = ?");
$stmt->bind_param("i", $subscriber_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Subscriber deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Subscriber not found or already deleted.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();