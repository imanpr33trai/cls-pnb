<?php
include_once(__DIR__ . '/../../partials/header.php');
include_once(__DIR__ . '/../../config/config.php');
include_once(__DIR__ . '/../../config/functions.php');
?>

<section class="breadcrump">
    <div class="container">
        <div class="row">
            <div class="d-flex gap-2 py-2">
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-1">Home >></a>
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-2">Post Ad</a>
            </div>
        </div>
    </div>
</section>

<section class="page-header">
    <div class="container">


        <div class="page-header-top">
            <h1 class="page-title">Our Latest Articles</h1>
            <a href="http://localhost:8000/blog-form" class="theme-btn text-decoration-none">Post an Article</a>
        </div>


        <div class="category-filters-container">

            <a href="#" class="blog-category-filter hover:text-pink active text-black" data-id="0">All</a>

            <?php

            $query = "SELECT id, name FROM blog_categories ORDER BY name ASC";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($cat = mysqli_fetch_assoc($result)) {
                    echo '<a href="' . htmlspecialchars($cat['name']) . '" class="blog-category-filter text-black hover:text-pink" data-id="' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a>';
                }
            }
            ?>
        </div>

    </div>
</section>

<section class="articles-blogs
">
    <div class="container">


        <div class="article-grid" id="blog-container">
            <?php

            $limit = 12;
            $offset = 0;

            $query = "SELECT bp.*, bc.name as category_name FROM blog_posts bp JOIN blog_categories bc ON bp.category_id = bc.id ORDER BY bp.created_at DESC LIMIT $limit OFFSET $offset";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($blog = mysqli_fetch_assoc($result)) {

                    $images = json_decode($blog['image'], true);
                    $firstImage = !empty($images[0]) ? $base_url . 'assets/uploads/blog_form/' . rawurlencode($images[0]) : $base_url . 'assets/images/test-img.png';

                    $title = htmlspecialchars($blog['title']);
                    $short_title = mb_strimwidth($title, 0, 60, "...");

                    $description = strip_tags($blog['description']);
                    $excerpt = mb_strimwidth($description, 0, 120, "...");

                    $createdDate = date('jS F, Y', strtotime($blog['created_at']));
                    $article_url = $base_url . 'article/' . $blog['blog_slug'];
            ?>

                    <div class="article-card">
                        <a href="<?= $article_url ?>" class="card-img-blog">
                            <img src="<?= $firstImage ?>" alt="<?= $title ?>" />
                        </a>
                        <div class="card-body-blog">
                            <span class="inline-block bg-gray-200 text-gray-800 px-2 py-1 rounded-xs text-xs font-semibold mb-2"><?= htmlspecialchars($blog['category_name']) ?></span>
                            <a href="<?= $article_url ?>" class="card-title text-decoration-none">
                                <?= $short_title ?>
                            </a>
                            <p class="card-excerpt"><?= $excerpt ?></p>
                        </div>
                        <div class="card-foot-blog">
                            <img src="<?= $base_url ?>assets/images/userimage.png" alt="Author" class="user-image-blog" />
                            <span class="author-name"><?= htmlspecialchars($blog['author_name']) ?></span>
                            <span>|</span>
                            <span class="date"><?= $createdDate ?></span>
                        </div>
                    </div>

            <?php
                }
            } else {
                echo '<p>No blog posts found.</p>';
            }
            ?>
        </div>
    </div>
</section




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
    // In your existing $(document).ready() function...

    $('.blog-category-filter').click(function(e) {
        e.preventDefault();

        // --- ADD THIS PART ---
        // Remove 'active' class from all filters
        $('.blog-category-filter').removeClass('active');
        // Add 'active' class to the one that was just clicked
        $(this).addClass('active');
        // --- END OF ADDED PART ---

        currentCategory = $(this).data('id');
        currentPage = 1;
        loadBlogs(currentCategory, currentPage);
    });

    $(document).ready(function() {
        let currentCategory = 0;
        let currentPage = 1;

        function loadBlogs(categoryId = 0, page = 1) {
            $.ajax({
                url: 'ajax/fetch_blogs_by_category.php',
                method: 'POST',
                data: {
                    category_id: categoryId,
                    page: page
                },
                success: function(data) {
                    $('#blog-container').html(data);
                },
                error: function() {
                    alert('Something went wrong while fetching blogs.');
                }
            });
        }

        // Load default blogs on page load
        loadBlogs();

        $('.blog-category-filter').click(function(e) {
            e.preventDefault();
            currentCategory = $(this).data('id');
            currentPage = 1;
            loadBlogs(currentCategory, currentPage);
        });

        $('.pagination-nums').click(function(e) {
            e.preventDefault();
            currentPage = parseInt($(this).text());
            loadBlogs(currentCategory, currentPage);
        });

        $('.pagination-btn').click(function(e) {
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
include_once(__DIR__ . '../../../partials/footer.php');
?>
<!-- footer -->
<!-- footer -->