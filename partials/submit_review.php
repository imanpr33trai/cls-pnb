<?php


include_once('../config/config.php'); // always load this first
include_once('../partials/header.php');

$ad_id = isset($_POST['ad_id']) ? intval($_POST['ad_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if ($ad_id && $user_id && $rating >= 1 && $rating <= 5 && !empty($comment)) {
    $stmt = $conn->prepare("INSERT INTO ad_reviews (ad_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $ad_id, $user_id, $rating, $comment);

    if ($stmt->execute()) {
        header("Location: single-ad.php?id=$ad_id&review=success");
        exit;
    } else {
        header("Location: single-ad.php?id=$ad_id&review=error");
        exit;
    }
} else {
    header("Location: single-ad.php?id=$ad_id&review=invalid");
    exit;
}
