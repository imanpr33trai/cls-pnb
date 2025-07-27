<?php
// =========================================================================
// PART 1: ALL PHP LOGIC - CONSOLIDATED AND SECURE
// =========================================================================

// CRUCIAL: Must be the very first line of the file.
session_start();
include_once(__DIR__ . '/../../config/config.php');
include_once(__DIR__ . '/../../config/functions.php');
require __DIR__ . '/../../config/whoops.php';

// --- 1. INITIALIZE ALL VARIABLES ---
$blog = null;
$table_of_contents = [];
$user = null; // The user VIEWING the page
$related_posts = [];
$nextArticle = null;
$prevArticle = null;
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


// --- 2. VALIDATE THE BLOG SLUG FROM THE URL ---
$blog_slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

// --- 3. FETCH ALL DATA IF WE HAVE A VALID BLOG SLUG ---
if (!empty($blog_slug)) {
    // --- A. FETCH THE MAIN BLOG POST DETAILS ---
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE blog_slug = ?");
    $stmt->bind_param("s", $blog_slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $blog = $result->fetch_assoc();
        $blog_id = $blog['id']; // Get ID for other queries
        $blog_created_at = $blog['created_at'];
        $blog_category_id = $blog['category_id'];

        // --- B. PARSE DESCRIPTION FOR TABLE OF CONTENTS ---
        if (!empty($blog['description'])) {
            $doc = new DOMDocument();
            // Load the HTML, suppressing errors for any minor issues
            @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $blog['description']);

            $headings = $doc->getElementsByTagName('h2');
            foreach ($headings as $index => $heading) {
                $id = 'toc-heading-' . $index;
                $heading->setAttribute('id', $id);
                $table_of_contents[] = [
                    'text' => $heading->nodeValue,
                    'id' => $id
                ];
            }
            // Save the modified HTML back to the blog description
            $blog['description'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|body|xml))[^>]*>\s*~i', '', $doc->saveHTML());
        }

        // --- C. FETCH RELATED POSTS ---
        $related_stmt = $conn->prepare("SELECT * FROM blog_posts WHERE category_id = ? AND id != ? ORDER BY created_at DESC LIMIT 3");
        $related_stmt->bind_param("ii", $blog_category_id, $blog_id);
        $related_stmt->execute();
        $related_posts_result = $related_stmt->get_result();
        while ($row = $related_posts_result->fetch_assoc()) {
            $related_posts[] = $row;
        }
        $related_stmt->close();

        // --- D. FETCH PAGINATION (NEXT/PREVIOUS POSTS) ---
        $next_stmt = $conn->prepare("SELECT title, blog_slug FROM blog_posts WHERE created_at > ? ORDER BY created_at ASC LIMIT 1");
        $next_stmt->bind_param("s", $blog_created_at);
        $next_stmt->execute();
        $nextArticle = $next_stmt->get_result()->fetch_assoc();
        $next_stmt->close();

        $prev_stmt = $conn->prepare("SELECT title, blog_slug FROM blog_posts WHERE created_at < ? ORDER BY created_at DESC LIMIT 1");
        $prev_stmt->bind_param("s", $blog_created_at);
        $prev_stmt->execute();
        $prevArticle = $prev_stmt->get_result()->fetch_assoc();
        $prev_stmt->close();
    }
    $stmt->close();
}

// --- 4. FETCH LOGGED-IN USER INFO (for the comment form) ---
if (isset($_SESSION['user_id'])) {
    $user_id_session = $_SESSION['user_id'];
    $user_stmt = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id_session);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();
    $user_stmt->close();
}

// --- 5. INCLUDE HEADER ---
include_once(__DIR__ . '/../../partials/header.php');

// =========================================================================
// PART 2: THE HTML STRUCTURE (UNCHANGED DESIGN)
// =========================================================================
?>

<!-- Breadcrump -->
<section class="breadcrump sm:py-3">
    <div class="container">
        <div class="">
            <div class="d-flex gap-2">
                <a href="<?= $base_url ?>" class="text-decoration-none breadcrump-links breadcrump-link-1">Home >></a>
                <a href="<?= $base_url ?>articles" class="text-decoration-none breadcrump-links breadcrump-link-2">Articles</a>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrump -->

<!-- article details -->
<section class="single-article-details pb-20">
    <div class="container">
        <?php if (!$blog): // Handle case where blog is not found 
        ?>
            <div class="text-center mt-5">
                <h1>Blog Post Not Found</h1>
                <p>The post you are looking for does not exist or may have been moved.</p>
                <a href="<?= $base_url ?>articles" class="theme-btn">Back to Articles</a>
            </div>
        <?php else: // Display the blog post 
        ?>
            <div class="lg:flex gap-7">
                <div class="col-lg-9 article-body ps-0">
                    <div class="col-12 article-top-img mb-7">
                        <?php
                        $images = json_decode($blog['image'], true);
                        $firstImage = !empty($images[0]) ? $base_url . 'assets/uploads/blog_form/' . $images[0] : $base_url . 'assets/images/placeholder-blog.png';
                        ?>
                        <img src="<?= $firstImage; ?>" alt="<?= htmlspecialchars($blog['title']); ?>" class="article-img" />
                        <div class="user-info d-flex gap-2 align-items-center justify-content-end">
                            <img src="<?= $base_url; ?>assets/images/userimage.png" alt="" />
                            <h1 class="fos-12 poppins-medium m-0 text-white"><?= htmlspecialchars($blog['author_name']); ?></h1>
                            <h1 class="fos-12 poppins-medium m-0 text-white">|</h1>
                            <h1 class="fos-12 poppins-medium m-0 text-white"><?= date('jS F, Y', strtotime($blog['created_at'])); ?></h1>
                        </div>
                    </div>
                    <div class="col-12">
                        <h1 class="poppins-medium fos-30 mb-5"><?= htmlspecialchars($blog['title']); ?></h1>
                        <div class="article-content">
                            <?= $blog['description']; // This is now pre-processed and safe 
                            ?>
                        </div>

                        <div class="col mb-10">
                            <hr />
                        </div>
                        <div class="reviews">
                            <h1 class="fos-24">Join the conversation</h1>
                            <?php if (!$user): ?>
                                <h1 class="not-login fos-16">
                                    You must be logged in to comment. <a href="<?= $base_url; ?>login" class="color-pink">Login Here</a>
                                </h1>
                            <?php else: ?>
                                <form id="commentForm">
                                    <div class="comment-sec d-flex align-items-start gap-3 mb-0.5">
                                        <img src="<?= $base_url; ?>assets/images/userimage.png" alt="" />
                                        <textarea name="comment" id="commentbyallusers" rows="5" class="w-100" required></textarea>
                                    </div>
                                    <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>" />
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
                                    <button type="submit" class="theme-btn">Submit Comment</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <div id="all-comments" class="mt-1"></div>
                    </div>
                </div>
                <div class="col-lg-3 m-0 pe-0">
                    <?php if ($nextArticle): ?>
                        <div class="next-post-sidebar mb-10">
                            <?php
                            $nextImages = json_decode($nextArticle['image'] ?? '[]', true);
                            $nextImage = !empty($nextImages[0]) ? $base_url . 'assets/uploads/blog_form/' . $nextImages[0] : $base_url . 'assets/images/placeholder-blog.png';
                            $descWords = explode(' ', strip_tags($nextArticle['description'] ?? ''));
                            $shortDesc = implode(' ', array_slice($descWords, 0, 15)) . (count($descWords) > 15 ? '...' : '');
                            ?>
                            <a href="<?= $base_url ?>article/<?= $nextArticle['blog_slug'] ?>">
                                <img src="<?= $nextImage ?>" class="img-next-post mb-6" alt="" />
                                <h1 class="poppins-medium fos-20 mb-5"><?= htmlspecialchars($nextArticle['title']) ?></h1>
                            </a>
                            <p class="fos-14"><?= htmlspecialchars($shortDesc) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="share-with-community mb-12">
                        <h1 class="fos-20 poppins-medium mb-6">Share with your community</h1>
                        <div class="social-share d-flex justify-content-between">
                            <a href="https://www.instagram.com/?url=<?= urlencode($currentUrl) ?>" target="_blank"><img src="<?= $base_url; ?>assets/images/insta.svg" alt="Instagram" /></a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>" target="_blank"><img src="<?= $base_url; ?>assets/images/tweetr.svg" alt="Twitter" /></a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank"><img src="<?= $base_url; ?>assets/images/facebook.svg" alt="Facebook" /></a>
                            <a href="https://x.com/intent/post?url=<?= urlencode($currentUrl) ?>" target="_blank"><img src="<?= $base_url; ?>assets/images/x.svg" alt="X" /></a>
                            <a href="https://wa.me/?text=<?= urlencode($currentUrl) ?>" target="_blank">WhatsApp</a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($currentUrl) ?>" target="_blank">LinkedIn</a>
                        </div>
                    </div>

                    <?php if (!empty($table_of_contents)):
                    ?>
                        <div class="inthisarticle">
                            <h1 class="fos-30 poppins-medium mb-7">In this article</h1>
                            <ul class="inthisarticlelist list-unstyled">
                                <?php foreach ($table_of_contents as $item): ?>
                                    <li class="article-list-item mb-1">
                                        <a href="#<?= $item['id'] ?>" class="toc-link text-decoration-none"><?= htmlspecialchars($item['text']) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<!-- article details -->

<!-- Related blogs -->
<?php if (!empty($related_posts)): ?>
    <section class="related-blog bg-gray-50 py-12 md:py-16">
        <div class="container mx-auto">

            <!-- Section Header -->
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Related Blog Posts</h2>

            <!-- Responsive Grid Container -->
            <!--
            - Default: 1 column (mobile)
            - sm (640px+): 2 columns
            - lg (1024px+): 3 columns
        -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">

                <?php foreach ($related_posts as $related): ?>
                    <?php
                    // --- Prepare data inside the loop for cleaner HTML ---
                    $relatedImages = json_decode($related['image'], true);
                    $relatedImage = !empty($relatedImages[0])
                        ? $base_url . 'assets/uploads/blog_form/' . rawurlencode($relatedImages[0])
                        : $base_url . 'assets/images/placeholder-blog.png';

                    $title = htmlspecialchars($related['title']);
                    $short_title = mb_strimwidth($title, 0, 60, "...");

                    $description = strip_tags($related['description']);
                    $excerpt = mb_strimwidth($description, 0, 100, "...");

                    $author_name = htmlspecialchars($related['author_name']);
                    $created_date = date('jS F, Y', strtotime($related['created_at']));
                    $article_url = $base_url . 'article/' . htmlspecialchars($related['blog_slug']);
                    ?>

                    <!-- Card Start: Styled entirely with Tailwind CSS -->
                    <div class="group flex flex-col bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden mb-1">

                        <!-- Image container with a fixed aspect ratio for uniformity -->
                        <a href="<?= $article_url ?>" class="block overflow-hidden">
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="<?= $relatedImage ?>"
                                    alt="<?= $title ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            </div>
                        </a>

                        <!-- Card Body -->
                        <div class="p-5 flex flex-col flex-grow">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 leading-tight">
                                <a href="<?= $article_url ?>" class="hover:text-blue-700 transition-colors duration-200 no-underline">
                                    <?= $short_title ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm flex-grow">
                                <?= $excerpt ?>
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center gap-3">
                            <img src="<?= $base_url; ?>assets/images/userimage.png" alt="Author: <?= $author_name ?>" class="w-9 h-9 rounded-full">
                            <div>
                                <p class="text-sm font-medium text-gray-800"><?= $author_name ?></p>
                                <p class="text-xs text-gray-500"><?= $created_date ?></p>
                            </div>
                        </div>

                    </div>
                    <!-- Card End -->

                <?php endforeach; ?>

            </div> <!-- End Grid -->
        </div>
    </section>
<?php endif; ?>
<!-- Related blogs -->

<!-- Pagination -->
<!-- <section class="pagination-sec pb-24">
    <div class="container py-3">
        <div class="">
            <div class="col d-flex align-items-center justify-content-between">
                <?php if ($prevArticle): ?>
                    <a href="<?= $base_url ?>article/<?= $prevArticle['blog_slug']; ?>" class="pagination-btn">&larr; Previous</a>
                <?php else: ?>
                    <span class="pagination-btn disabled">&larr; Previous</span>
                <?php endif; ?>

                <?php if ($nextArticle): ?>
                    <a href="<?= $base_url ?>article/<?= $nextArticle['blog_slug']; ?>" class="pagination-btn">Next &rarr;</a>
                <?php else: ?>
                    <span class="pagination-btn disabled">Next &rarr;</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section> -->
<!-- Pagination -->

<?php include_once(__DIR__ . '/../../partials/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        <?php if ($blog): ?>
            const blogId = <?= $blog['id'] ?>;

            function loadComments() {
                $.post('<?= $base_url ?>ajax/fetch_comments.php', {
                    blog_id: blogId
                }, function(data) {
                    $('#all-comments').html(data);
                }).fail(function() {
                    $('#all-comments').html('<p>Error loading comments.</p>');
                });
            }

            loadComments();

            $('#commentForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '<?= $base_url ?>ajax/submit_comment.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#commentbyallusers').val('');
                            loadComments();
                        } else {
                            alert(response.message || 'An error occurred.');
                        }
                    },
                    error: function() {
                        alert('Could not submit comment. Please try again.');
                    }
                });
            });
        <?php endif; ?>

        // Smooth scroll for Table of Contents
        $('.toc-link').on('click', function(e) {
            e.preventDefault();
            const targetId = $(this).attr('href');
            const targetElement = $(targetId);
            if (targetElement.length) {
                $('html, body').animate({
                    scrollTop: targetElement.offset().top - 100
                }, 500);
            }
        });
    });
</script>