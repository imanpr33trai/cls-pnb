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
            <a href="/blog-form" class="theme-btn text-decoration-none">Post an Article</a>
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

<section class="articles-blogs">
    <div class="container">


        <div class="article-grid mb-8" id="blog-container">
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
                            <img src="<?= $firstImage ?>" alt="<?= $title ?>" class="img-ads"/>
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
</section>




<section class="pagination-sec pb-12">
    <div class="container">
        <div class="row">
            <div id="pagination-container" class="col d-flex align-items-center justify-content-between">
                
            </div>
        </div>
    </div>
</section>


<script src="https:

<script>
$(document).ready(function() {
    let currentCategory = 0;
    let currentPage = 1;

    function updatePagination(pagination) {
        const { totalPages, currentPage } = pagination;
        const paginationContainer = $('#pagination-container');
        paginationContainer.empty(); 

        if (totalPages <= 1) {
            return; 
        }

        let paginationHTML = '';

        
        if (currentPage > 1) {
            paginationHTML += `<a href="#" class="pagination-btn" data-page="${currentPage - 1}">&larr; Previous</a>`;
        } else {
            paginationHTML += `<span class="pagination-btn disabled">&larr; Previous</span>`;
        }

        
        paginationHTML += '<div class="flex gap-1.5">';
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = (i === currentPage) ? 'active' : '';
            paginationHTML += `<a href="#" class="pagination-nums ${activeClass}" data-page="${i}">${i}</a>`;
        }
        paginationHTML += '</div>';

        
        if (currentPage < totalPages) {
            paginationHTML += `<a href="#" class="pagination-btn" data-page="${currentPage + 1}">Next &rarr;</a>`;
        } else {
            paginationHTML += `<span class="pagination-btn disabled">Next &rarr;</span>`;
        }

        paginationContainer.html(paginationHTML);
    }

    function loadBlogs(categoryId = 0, page = 1) {
        $.ajax({
            url: 'ajax/fetch_blogs_by_category.php',
            method: 'POST',
            data: {
                category_id: categoryId,
                page: page
            },
            dataType: 'json', 
            success: function(response) {
                $('#blog-container').html(response.html);
                updatePagination(response.pagination);
                currentPage = response.pagination.currentPage; 
            },
            error: function() {
                
                $('#blog-container').html('<p class="text-center w-100 py-5 text-danger">Error loading articles. Please try again.</p>');
            }
        });
    }

    
    loadBlogs(currentCategory, currentPage);

    
    $('.blog-category-filter').click(function(e) {
        e.preventDefault();
        $('.blog-category-filter').removeClass('active');
        $(this).addClass('active');
        currentCategory = $(this).data('id');
        loadBlogs(currentCategory, 1); 
    });

    
    $('#pagination-container').on('click', 'a.pagination-btn, a.pagination-nums', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            loadBlogs(currentCategory, page);
        }
    });
});
</script>




<?php
include_once(__DIR__ . '../../../partials/footer.php');
?>

