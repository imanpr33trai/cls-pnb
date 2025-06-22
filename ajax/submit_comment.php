<?php
include_once('../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_id = intval($_POST['blog_id']);
    $user_name = trim($_POST['user_name']);
    $comment = trim($_POST['comment']);

    if (!empty($user_name) && !empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO blog_comments (blog_id, user_name, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $blog_id, $user_name, $comment);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error: " . $stmt->error;
        }
    } else {
        echo "error: fields required";
    }
}
?>
