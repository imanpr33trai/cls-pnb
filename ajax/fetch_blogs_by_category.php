<?php

include_once(__DIR__ . '../../config/config.php');
include_once(__DIR__ . '../../config/functions.php');

$categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;

$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$sql = "SELECT * FROM blog_posts";
if ($categoryId > 0) {
    $sql .= " WHERE category_id = $categoryId";
}
$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($blog = mysqli_fetch_assoc($result)) {
        $images = json_decode($blog['image'], true);
        $firstImage = !empty($images[0]) ? '../assets/uploads/blog_form/' . $images[0] : '../assets/images/test-img.png';

        $descWords = explode(' ', strip_tags($blog['description']));
        $excerpt = implode(' ', array_slice($descWords, 0, 20)) . (count($descWords) > 20 ? '...' : '');

        $createdDate = date('jS F, Y', strtotime($blog['created_at']));

        echo '<div class="col-12 col-sm-6 col-lg-4 mb-4">
                <div class="article-card position-relative">
                    <div class="card-img-blog">
                         <a href="article/' . $blog['blog_slug'] . '">
        <img src="' . $firstImage . '" class="img-fluid" alt="" />
    </a>
                    </div>
                    <div class="card-body-blog">
                         <h1 class="fos-20 poppins-regular mb-20">
        <a href="article/' . $blog['blog_slug'] . '" class="text-dark text-decoration-none">'
            . htmlspecialchars($blog['title']) .
            '</a>
    </h1>
                        <p>' . htmlspecialchars($excerpt) . '</p>
                    </div>
                    <div class="card-foot-blog d-flex align-items-center gap-2">
                        <img src="../assets/images/userimage.png" alt="" class="user-image-blog" />
                        <h1 class="fos-12 usernameblog m-0">' . htmlspecialchars($blog['author_name']) . '</h1>
                        <h1 class="fos-12 usernameblog m-0">|</h1>
                        <h1 class="fos-12 dateblog m-0">' . $createdDate . '</h1>
                    </div>
                </div>
            </div>';
    }
} else {
    echo '<p>No blog posts found.</p>';
}
