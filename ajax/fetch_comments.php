<?php
include_once('../config/config.php');

$blog_id = intval($_POST['blog_id']);

$query = "SELECT * FROM blog_comments WHERE blog_id = $blog_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $date = date('jS F, Y', strtotime($row['created_at']));
        echo '<div class="comment-box mb-3 d-flex gap-3 mt-3">
                <img src="' . $base_url . $row['user_image'] . '" width="50px" " height="50px" />
                <div>
                    <h5 class="m-0">' . htmlspecialchars($row['user_name']) . '</h5>
                    <small class="text-muted">' . $date . '</small>
                    <p>' . nl2br(htmlspecialchars($row['comment'])) . '</p>
                </div>
              </div>';
    }
} else {
    echo '<p>No comments yet.</p>';
}
?>
