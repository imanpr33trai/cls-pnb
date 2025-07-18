<?php

// delete_test_user.php - For development/testing ONLY

// Include database configuration
include_once(__DIR__ . '/config/config.php');

// Check for email and confirmation parameter
if (isset($_GET['email']) && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $email_to_delete = trim($_GET['email']);

    if (empty($email_to_delete)) {
        echo "<p style=\"color: red;\">Error: Email parameter is empty.</p>";
    } else {
        // Prepare a delete statement
        $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
        $stmt->bind_param("s", $email_to_delete);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<p style=\"color: green;\">User with email '" . htmlspecialchars($email_to_delete) . "' deleted successfully.</p>";
            } else {
                echo "<p style=\"color: orange;\">User with email '" . htmlspecialchars($email_to_delete) . "' not found.</p>";
            }
        } else {
            echo "<p style=\"color: red;\">Database error: " . htmlspecialchars($stmt->error) . "</p>";
            error_log("Error deleting user '{$email_to_delete}': " . $stmt->error);
        }
        $stmt->close();
    }
} else {
    echo "<p>To delete a user, provide their email and add '&confirm=yes' to the URL.</p>";
    echo "<p>Example: <code>" . htmlspecialchars($base_url) . "delete_test_user.php?email=test@example.com&confirm=yes</code></p>";
}

$conn->close();

?>