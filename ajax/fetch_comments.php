<?php
include_once('../config/config.php');

$blog_id = intval($_POST['blog_id']);

$query = "SELECT * FROM blog_comments WHERE blog_id = $blog_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $date = date('jS F, Y', strtotime($row['created_at']));
        echo '<div class="comment-box mb-3 d-flex items-center gap-3 mt-3">
               
                <img class="max-w-[50px] max-h-[50px]" src="' . $base_url . $row['user_image'] . ' "/>
                <div>
                    <div class="flex justify-between gap-6 items-center"><h5 class="m-0">' . htmlspecialchars($row['user_name']) . '</h5>
                    <small class="text-muted">' . $date . '</small></div>
                    <p>' . nl2br(htmlspecialchars($row['comment'])) . '</p>
                </div>
              </div>';
    }
} else {
    echo '<p>No comments yet.</p>';
}
?>