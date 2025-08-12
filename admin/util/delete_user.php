<?php
// /admin/util/delete_user.php
require_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');

// Basic security: Check if an admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Get the user ID from the POST request
$user_id = $_POST['user_id'] ?? null;

if (!$user_id || !filter_var($user_id, FILTER_VALIDATE_INT)) {
    echo json_encode(['success' => false, 'message' => 'Invalid User ID provided.']);
    exit;
}

// Critical safety check: Prevent the main admin (ID 1) from being deleted.
// You might have more sophisticated role-based checks here.
if ($user_id == 1) {
    echo json_encode(['success' => false, 'message' => 'The primary admin account cannot be deleted.']);
    exit;
}

// Another safety check: Prevent an admin from deleting themselves.
if ($user_id == $_SESSION['admin_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
    exit;
}

// Prepare and execute the deletion
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found or already deleted.']);
    }
} else {
    // Check for foreign key constraint errors if any
    if ($conn->errno === 1451) { // Cannot delete or update a parent row
         echo json_encode(['success' => false, 'message' => 'Cannot delete user. They may have active ads or other associated data. Please reassign or delete their content first.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
}

$stmt->close();
$conn->close();