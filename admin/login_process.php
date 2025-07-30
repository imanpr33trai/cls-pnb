<?php
include '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = $_POST['username'];
$password = $_POST['password'];

$query = $conn->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
$query->bind_param("ss", $username, $username);
$query->execute();
$result = $query->get_result();

echo password_hash("admin123", PASSWORD_DEFAULT);

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['admin'] = $user;
        header("Location: index.php");
    } else {
        echo "Invalid password!";
    }
} else {
    echo "Admin not found!";
}
?>