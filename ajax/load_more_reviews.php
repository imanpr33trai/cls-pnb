<?php

include_once('../config/config.php');
if (!isset($_POST['ad_id']) || !isset($_POST['page'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request.']);
    exit();
}

$ad_id = intval($_POST['ad_id']);
$page = intval($_POST['page']);
$reviews_per_page = 5;
$offset = ($page - 1) * $reviews_per_page;
$sql = "SELECT r.*, u.first_name, u.last_name 
        FROM ad_reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.ad_id = ?
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $ad_id, $reviews_per_page, $offset);
$stmt->execute();
$reviews_result = $stmt->get_result();
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM ad_reviews WHERE ad_id = ?");
$count_stmt->bind_param("i", $ad_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_reviews = 0;
if ($count_result) {
    $total_reviews = $count_result->fetch_assoc()['total'];
}
$count_stmt->close();

ob_start();
if ($reviews_result && $reviews_result->num_rows > 0) {
    while ($review = $reviews_result->fetch_assoc()) {
?>
        <div class="review-item border-bottom pb-3 mb-3">
            <div class="review-header d-flex justify-content-between align-items-center">
                <h5 class="poppins-medium m-0"><?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?></h5>
                <div class="review-stars-display">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa <?= ($i <= $review['rating']) ? 'fa-star' : 'fa-star-o' ?>" style="color: #FFA500;"></i>
                    <?php endfor; ?>
                </div>
            </div>
            <small class="text-muted"><?= date('F j, Y', strtotime($review['created_at'])) ?></small>
            <p class="mt-2"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
        </div>
<?php
    }
}
$html = ob_get_clean();
$stmt->close();
$has_more = ($total_reviews > ($page * $reviews_per_page));
header('Content-Type: application/json');
echo json_encode(['html' => $html, 'hasMore' => $has_more]);
