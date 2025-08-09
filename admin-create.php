<?php
require_once(__DIR__ . '/config/config.php');

// --- Create a new admin user ---

// Temporary credentials
$temp_username = 'man1';
$temp_email = 'man@example.com';
$temp_password = '135600'; // It's recommended to change this after first login

// Hash the password for security
$hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

// Check if the admin already exists
$check_query = $conn->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
$check_query->bind_param("ss", $temp_username, $temp_email);
$check_query->execute();
$result = $check_query->get_result();

if ($result->num_rows > 0) {
    echo "Admin user with this username or email already exists.";
} else {
    // Insert the new admin into the database
    $insert_query = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $insert_query->bind_param("sss", $temp_username, $temp_email, $hashed_password);

    if ($insert_query->execute()) {
        echo "Successfully created a new admin user.<br>";
        echo "Username: " . htmlspecialchars($temp_username) . "<br>";
        echo "Password: " . htmlspecialchars($temp_password);
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
}

$conn->close();
?>