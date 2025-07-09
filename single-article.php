<?php
// =========================================================================
// PART 1: ALL PHP LOGIC - CONSOLIDATED AND SECURE
// =========================================================================

// CRUCIAL: Must be the very first line of the file.
session_start();
include_once('config/config.php');

// --- 1. INITIALIZE ALL VARIABLES ---
$blog = null;
$table_of_contents = [];
$user = null; // The user VIEWING the page
$related_posts_result = null;
$nextArticle = null;
$prevArticle = null;

// --- 2. VALIDATE THE AD ID FROM THE URL ---
$blog_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

// --- 3. FETCH ALL DATA IF WE HAVE A VALID AD ID ---
if ($blog_id > 0) {
    // --- A. FETCH THE MAIN BLOG POST DETAILS ---
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows === 1) {
        $blog = $result->fetch_assoc();

        // --- B. PARSE DESCRIPTION FOR TABLE OF CONTENTS ---
        if (!empty($blog['description'])) {
            $doc = new DOMDocument();
            // Load the HTML, suppressing errors for any minor issues
            @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $blog['description']);
            
            // Find all h2 and h3 tags to build the TOC
            $headings = array_merge(
                iterator_to_array($doc->getElementsByTagName('h2')),
                iterator_to_array($doc->getElementsByTagName('h3'))
            );
            
            foreach ($headings as $heading) {
                $table_of_contents[] = [
                    'text' => $heading->nodeValue, // The text inside the tag
                    'id' => 'toc-heading-' . uniqid() // A unique ID for scrolling
                ];
            }
        }

        // --- C. FETCH RELATED POSTS ---
        $current_category_id = $blog['category_id'];
        $related_stmt = $conn->prepare("SELECT bp.*, u.first_name, u.last_name FROM blog_posts bp JOIN users u ON bp.author_name = u.first_name WHERE bp.category_id = ? AND bp.id != ? ORDER BY bp.created_at DESC LIMIT 3");
        $related_stmt->bind_param("ii", $current_category_id, $blog_id);
        $related_stmt->execute();
        $related_posts_result = $related_stmt->get_result();
        
        // --- D. FETCH PAGINATION (NEXT/PREVIOUS POSTS) ---
        $next_stmt = $conn->prepare("SELECT id, title FROM blog_posts WHERE id > ? ORDER BY id ASC LIMIT 1");
        $next_stmt->bind_param("i", $blog_id);
        $next_stmt->execute();
        $nextArticle = $next_stmt->get_result()->fetch_assoc();
        $next_stmt->close();
        
        $prev_stmt = $conn->prepare("SELECT id, title FROM blog_posts WHERE id < ? ORDER BY id DESC LIMIT 1");
        $prev_stmt->bind_param("i", $blog_id);
        $prev_stmt->execute();
        $prevArticle = $prev_stmt->get_result()->fetch_assoc();
        $prev_stmt->close();
    }
    $stmt->close();
}

// --- 4. FETCH LOGGED-IN USER INFO (for the comment form) ---
if (isset($_SESSION['user_id'])) {
    $user_id_session = $_SESSION['user_id'];
    $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id_session);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();
    $user_stmt->close();
}

// --- 5. INCLUDE HEADER ---
include_once('partials/header.php');

// =========================================================================
// PART 2: THE HTML STRUCTURE
// =========================================================================
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

<!-- article details -->
<!-- article details -->
<section class="single-article-details pb-100">
    <div class="container">
         <?php if (!$blog): // Handle case where blog is not found ?>
             <div class="text-center mt-5"><h1>Blog Post Not Found</h1><p>The post you are looking for does not exist.</p></div>
        <?php else: // Display the blog post ?>
        <div class="row">
            <div class="col-lg-9 ps-0">
                <div class="col-12 article-top-img mb-30">
                    <?php
                        $images = json_decode($blog['image'], true);
                        $firstImage = !empty($images[0]) ? $base_url . 'assets/uploads/blog_form/' . $images[0] : $base_url . 'assets/images/test-img.png';
                    ?>
                <img src="<?= $firstImage; ?>" alt="<?= htmlspecialchars($blog['title']); ?>" class="w-100" />
                    <div class="user-info d-flex gap-2 align-items-center justify-content-end">
                        <img src="<?= $base_url; ?>assets/images/userimage.png" alt="" />
                        <h1 class="fos-12 poppins-medium m-0 text-white"><?= htmlspecialchars($blog['author_name']); ?></h1>
                        <h1 class="fos-12 poppins-medium m-0 text-white">|</h1>
                        <h1 class="fos-12 poppins-medium m-0 text-white"><?= date('jS F, Y', strtotime($blog['created_at'])); ?></h1>
                    </div>
                </div>
                <div class="col-12">
                    <h1 class="poppins-medium fos-30 mb-20"><?= htmlspecialchars($blog['title']); ?></h1>
                    <!-- DYNAMIC RICH TEXT DESCRIPTION -->
                    <div class="article-content">
                        <?php
                            // Inject IDs into the headings from the description for scrolling
                            if (!empty($blog['description'])) {
                                $doc = new DOMDocument();
                                @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $blog['description']);
                                $headings_to_modify = array_merge(
                                    iterator_to_array($doc->getElementsByTagName('h2')),
                                    iterator_to_array($doc->getElementsByTagName('h3'))
                                );
                                
                                $i = 0;
                                foreach($headings_to_modify as $heading) {
                                    if(isset($table_of_contents[$i])) {
                                        $heading->setAttribute('id', $table_of_contents[$i]['id']);
                                    }
                                    $i++;
                                }
                                // Output the modified HTML, removing temporary wrapper tags
                                // We are NOT using htmlspecialchars() here because we WANT to render the HTML.
                                echo preg_replace('~<(?:!DOCTYPE|/?(?:html|body|xml))[^>]*>\s*~i', '', $doc->saveHTML());
                            }
                        ?>
                    </div>
                    
                    
                    
                    <div class="col mb-40">
                        <hr />
                    </div>
                    <div class="reviews">
    <h1 class="fos-24">Join the conversation</h1>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <h1 class="not-login fos-16">
            You must be logged in to comment. <a href="<?= $base_url; ?>login.php" class="color-pink">Login Here</a>
        </h1>
    <?php else: ?>
        <form id="commentForm">
            <div class="comment-sec d-flex align-items-start gap-3 mb-3">
                <img src="<?php echo $base_url; ?>assets/images/userimage.png" alt="" />
                <textarea name="comment" id="commentbyallusers" rows="5" class="w-100" required></textarea>
            </div>
            <input type="hidden" name="blog_id" value="<?= $_GET['id'] ?>" />
            <input type="hidden" name="user_name" value="<?= $_SESSION['user_name'] ?>" />
            <button type="submit" class="theme-btn">Submit Comment</button>
        </form>
    <?php endif; ?>
</div>

<!-- Comments will be loaded here -->
<div id="all-comments"></div>


                </div>
            </div>
            <div class="col-lg-3 m-0 pe-0">
            <?php
$currentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Try to get the next article by ID
$nextQuery = "SELECT * FROM blog_posts WHERE id > $currentId ORDER BY id ASC LIMIT 1";
$nextResult = mysqli_query($conn, $nextQuery);

if ($nextResult && mysqli_num_rows($nextResult) > 0) {
    $nextArticle = mysqli_fetch_assoc($nextResult);
} else {
    // No next article found, get the first article (loop back)
    $firstQuery = "SELECT * FROM blog_posts ORDER BY id ASC LIMIT 1";
    $firstResult = mysqli_query($conn, $firstQuery);
    $nextArticle = ($firstResult && mysqli_num_rows($firstResult) > 0) ? mysqli_fetch_assoc($firstResult) : null;
}

if ($nextArticle) {
    // Get first image
    $nextImages = json_decode($nextArticle['image'], true);
    $nextImage = !empty($nextImages[0]) ? $base_url . 'assets/uploads/blog_form/' . $nextImages[0] : $base_url . 'assets/images/test-img.png';

    // Shorten description to 15 words
    $descWords = explode(' ', strip_tags($nextArticle['description']));
    $shortDesc = implode(' ', array_slice($descWords, 0, 15)) . (count($descWords) > 15 ? '...' : '');

    echo '<div class="next-post-sidebar mb-40">';
    echo '    <a href="single-article.php?id=' . $nextArticle['id'] . '">';
    echo '        <img src="' . $nextImage . '" class="img-next-post mb-24" alt="" />';
    echo '        <h1 class="poppins-medium fos-20 mb-20">' . htmlspecialchars($nextArticle['title']) . '</h1>';
    echo '    </a>';
    echo '    <p class="fos-14">' . htmlspecialchars($shortDesc) . '</p>';
    echo '</div>';
}
?>


                <div class="share-with-community mb-50">
                    <h1 class="fos-20 poppins-medium mb-24">Share with your community</h1>
                    <div class="social-share d-flex justify-content-between">
    <a href="https://www.instagram.com/?url=<?= urlencode($currentUrl) ?>" target="_blank">
        <img src="<?= $base_url; ?>assets/images/insta.svg" alt="Share on Instagram" />
    </a>
    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>" target="_blank">
        <img src="<?= $base_url; ?>assets/images/tweetr.svg" alt="Share on Twitter" />
    </a>
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank">
        <img src="<?= $base_url; ?>assets/images/facebook.svg" alt="Share on Facebook" />
    </a>
    <a href="https://x.com/intent/post?url=<?= urlencode($currentUrl) ?>" target="_blank">
        <img src="<?= $base_url; ?>assets/images/x.svg" alt="Share on X" />
    </a>
    <!-- WhatsApp -->
<a href="https://wa.me/?text=<?= urlencode($currentUrl) ?>" target="_blank">WhatsApp</a>

<!-- LinkedIn -->
<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($currentUrl) ?>" target="_blank">LinkedIn</a>

</div>

                </div>
                <?php if (!empty($table_of_contents)): ?>
                <div class="inthisarticle">
                    <h1 class="fos-30 poppins-medium mb-30">In this article</h1>
                     <ul class="inthisarticlelist list-unstyled">
                        <?php foreach($table_of_contents as $item): ?>
                            <li class="article-list-item mb-2">
                                <a href="#<?= $item['id'] ?>" class="toc-link text-decoration-none">
                                    <?= htmlspecialchars($item['text']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
         <?php endif; // End of the main if/else for blog existence ?>
    </div>
</section>
<!-- article details -->
<!-- article details -->

<!-- Related blogs -->
<!-- Related blogs -->
<?php
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch current article
$current_query = "SELECT * FROM blog_posts WHERE id = $article_id LIMIT 1";
$current_result = mysqli_query($conn, $current_query);

if (!$current_result || mysqli_num_rows($current_result) == 0) {
    echo "Article not found.";
    exit;
}

$current_article = mysqli_fetch_assoc($current_result);
$current_category = $current_article['category_id']; // or 'cat_id'

$related_query = "SELECT * FROM blog_posts 
                  WHERE category_id = '$current_category' AND id != $article_id 
                  ORDER BY created_at DESC LIMIT 3";

$related_result = mysqli_query($conn, $related_query);
$relatedImages = json_decode($related['image'], true);
$relatedImage = !empty($relatedImages[0]) ? $base_url . 'assets/uploads/blog_form/' . $relatedImages[0] : $base_url . 'assets/images/test-img.png';
?>

<section class="related-blog">
    <div class="container">
        <div class="row">
            <div class="">
                <h1 class="poppins-medium fos-30 mb-30">Related Blog Post</h1>
                <div class="p-0 d-flex flex-wrap">
                    
                   
                <?php if ($related_result && mysqli_num_rows($related_result) > 0) {
    while ($related = mysqli_fetch_assoc($related_result)) {?>
    <div class="col-12 col-sm-6 col-lg-4 mb-4 px-2">
        <div class="article-card position-relative">
            <div class="card-img-blog">
                <a href="<?= $base_url; ?>single-article.php?id=<?= $related['id']; ?>">
                <img src="<?= $relatedImage ?>" class="img-fluid" alt="">
                </a>
            </div>
            <div class="card-body-blog">
                <h1 class="fos-20 poppins-regular mb-20">
                    <a href="<?= $base_url; ?>single-article.php?id=<?= $related['id']; ?>" class="text-dark text-decoration-none">
                        <?= htmlspecialchars($related['title']); ?>
                    </a>
                </h1>
                <p>
                    <?= substr(strip_tags($related['description']), 0, 100); ?>...
                </p>
            </div>
            <div class="card-foot-blog d-flex align-items-center gap-2">
                <img src="<?= $base_url . $related['user_image']; ?>" alt="" class="user-image-blog">
                <h1 class="fos-12 usernameblog m-0"><?= $related['user_name']; ?></h1>
                <h1 class="fos-12 usernameblog m-0">|</h1>
                <h1 class="fos-12 dateblog m-0"><?= date('jS F, Y', strtotime($related['created_at'])); ?></h1>
            </div>
        </div>
    </div>
<?php   
  }
                }

?>


                    
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Related blogs -->
<!-- Related blogs -->

<!-- Pagination -->
<!-- Pagination -->
<?php
$currentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch current article
$currentQuery = "SELECT * FROM blog_posts WHERE id = $currentId LIMIT 1";
$currentResult = mysqli_query($conn, $currentQuery);

if (!$currentResult || mysqli_num_rows($currentResult) == 0) {
    echo "Article not found.";
    exit;
}

$currentArticle = mysqli_fetch_assoc($currentResult);

// Fetch next article
$nextQuery = "SELECT * FROM blog_posts WHERE id > $currentId ORDER BY id ASC LIMIT 1";
$nextResult = mysqli_query($conn, $nextQuery);
$nextArticle = mysqli_fetch_assoc($nextResult);

// Fetch previous article
$prevQuery = "SELECT * FROM blog_posts WHERE id < $currentId ORDER BY id DESC LIMIT 1";
$prevResult = mysqli_query($conn, $prevQuery);
$prevArticle = mysqli_fetch_assoc($prevResult);
?>

<section class="pagination-sec pb-100">
    <div class="container">
        <div class="row">
            <div class="col d-flex align-items-center justify-content-between">
                <?php if ($prevArticle): ?>
                    <a href="single-article.php?id=<?php echo $prevArticle['id']; ?>" class="pagination-btn">&larr; Previous</a>
                <?php else: ?>
                    <span class="pagination-btn disabled">&larr; Previous</span>
                <?php endif; ?>

                <?php if ($nextArticle): ?>
                    <a href="single-article.php?id=<?php echo $nextArticle['id']; ?>" class="pagination-btn">Next &rarr;</a>
                <?php else: ?>
                    <span class="pagination-btn disabled">Next &rarr;</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Pagination -->
<!-- Pagination -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    const blogId = <?= $_GET['id'] ?>;

    function loadComments() {
        $.post('ajax/fetch_comments.php', { blog_id: blogId }, function(data) {
            $('#all-comments').html(data);
        });
    }

    loadComments(); // Initial load

    $('#commentForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax/submit_comment.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                if (response.trim() === "success") {
                    $('#commentbyallusers').val('');
                    loadComments();
                } else {
                    alert(response);
                }
            }
        });
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

<script>
$(document).ready(function () {
    // --- Comment system AJAX (your existing code is good) ---
    // ...

    // --- NEW: SMOOTH SCROLL FOR TABLE OF CONTENTS ---
    $('.toc-link').on('click', function(e) {
        e.preventDefault();
        const targetId = $(this).attr('href');
        const targetElement = $(targetId);
        
        if(targetElement.length) {
            $('html, body').animate({
                scrollTop: targetElement.offset().top - 100 // Adjust -100 to offset for your fixed header
            }, 500); // 500ms animation speed
        }
    });
});
</script>