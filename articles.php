<?php
include_once('config/config.php'); // always load this first
include_once('partials/header.php');
?>
<!-- Breadcrump -->
<!-- Breadcrump -->
<section class="breadcrump">
    <div class="container">
        <div class="row">
            <div class="d-flex gap-2">
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-1">Home >></a>
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-2">Post Ad</a>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrump -->
<!-- Breadcrump -->

<!-- article page top cats and button -->
<!-- article page top cats and button-->
<section class="mb-50">
    <div class="container">
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-30">
                <h1 class="fos-40 playfair-medium m-0">Create Your Free ads</h1>
                <a href="<?= ARTICLES_POST_URL ?>" class="theme-btn text-decoration-none">Post Articles</a>
            </div>
            <div class="col-12 all-blog-cats">
            <?php
$query = "SELECT id, name FROM blog_categories ORDER BY name ASC";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($cat = mysqli_fetch_assoc($result)) {
        echo '<a href="#" class="blog-category-filter" data-id="' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a>';
    }
} else {
    echo '<p>No categories found.</p>';
}
?>
            </div>

        </div>
    </div>
</section>
<!-- article page top cats and button-->
<!-- article page top cats and button-->

<!-- atricles -->
<!-- atricles -->
<section class="articles-blogs">
    <div class="container">
        <div class="row" id="blog-container">
            <!-- Card Start -->
            

<?php
$limit = 12;
$offset = 0; // Default for first page

$query = "SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($blog = mysqli_fetch_assoc($result)) {
        $images = json_decode($blog['image'], true);
        $firstImage = !empty($images[0]) ? $base_url . 'assets/uploads/blog_form/' . $images[0] : $base_url . 'assets/images/test-img.png';

        $descWords = explode(' ', strip_tags($blog['description']));
        $excerpt = implode(' ', array_slice($descWords, 0, 20)) . (count($descWords) > 20 ? '...' : '');

        $createdDate = date('jS F, Y', strtotime($blog['created_at']));

        echo '<div class="col-12 col-sm-6 col-lg-4 mb-4">
                <div class="article-card position-relative">
                    <div class="card-img-blog">
                    <a href="single-article.php?id=' . $blog['id'] . '">
                        <img src="' . $firstImage . '" class="img-fluid" alt="" />
                          </a>
                    </div>
                    <div class="card-body-blog">
                          <h1 class="fos-20 poppins-regular mb-20">
        <a href="single-article.php?id=' . $blog['id'] . '" class="text-dark text-decoration-none">'
            . htmlspecialchars($blog['title']) .
        '</a>
    </h1>
                        <p>' . htmlspecialchars($excerpt) . '</p>
                    </div>
                    <div class="card-foot-blog d-flex align-items-center gap-2">
                        <img src="' . $base_url . 'assets/images/userimage.png" alt="" class="user-image-blog" />
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
?>



        </div>
    </div>
</section>
<!-- atricles -->
<!-- atricles -->

<!-- Pagination -->
<!-- Pagination -->
<section class="pagination-sec pb-100">
    <div class="container">
        <div class="row">
            <div class="col d-flex align-items-center justify-content-between">
                <a href="#" class="pagination-btn">&larr; Previous</a>
                <div>
                    <a href="#" class="pagination-nums">1</a>
                    <a href="#" class="pagination-nums">2</a>
                    <a href="#" class="pagination-nums">3</a>
                    <a href="#" class="pagination-nums">4</a>
                    <a href="#" class="pagination-nums">5</a>
                </div>
                <a href="#" class="pagination-btn">Next &rarr;</a>
            </div>
        </div>
    </div>
</section>
<!-- Pagination -->
<!-- Pagination -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    let currentCategory = 0;
    let currentPage = 1;

    function loadBlogs(categoryId = 0, page = 1) {
        $.ajax({
            url: 'ajax/fetch_blogs_by_category.php',
            method: 'POST',
            data: { category_id: categoryId, page: page },
            success: function (data) {
                $('#blog-container').html(data);
            },
            error: function () {
                alert('Something went wrong while fetching blogs.');
            }
        });
    }

    // Load default blogs on page load
    loadBlogs();

    $('.blog-category-filter').click(function (e) {
        e.preventDefault();
        currentCategory = $(this).data('id');
        currentPage = 1;
        loadBlogs(currentCategory, currentPage);
    });

    $('.pagination-nums').click(function (e) {
        e.preventDefault();
        currentPage = parseInt($(this).text());
        loadBlogs(currentCategory, currentPage);
    });

    $('.pagination-btn').click(function (e) {
        e.preventDefault();
        if ($(this).text().includes('Previous')) {
            if (currentPage > 1) {
                currentPage--;
            }
        } else {
            currentPage++;
        }
        loadBlogs(currentCategory, currentPage);
    });
});
</script>


<!-- footer -->
<!-- footer -->
<?php
 include_once('partials/footer.php');
 ?>
<!-- footer -->
<!-- footer -->