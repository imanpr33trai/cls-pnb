<?php
    
    
    

    

include_once(__DIR__ . '/../../config/config.php');
include_once(__DIR__ . '/../../config/functions.php');


    
$blog = null;
$table_of_contents = [];
$user = null;     
$related_posts = [];
$nextArticle = null;
$prevArticle = null;
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


    
$blog_slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

    
if (!empty($blog_slug)) {
        
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE blog_slug = ?");
    $stmt->bind_param("s", $blog_slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $blog = $result->fetch_assoc();
        $blog_id = $blog['id'];     
        $blog_created_at = $blog['created_at'];
        $blog_category_id = $blog['category_id'];

            
        if (!empty($blog['description'])) {
            $doc = new DOMDocument();
                
            @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $blog['description']);

            $headings = $doc->getElementsByTagName('h2');
            foreach ($headings as $index => $heading) {
                if ($heading instanceof DOMElement) {
                    $id = "toc-heading-$index";
                    $heading->setAttribute('id', $id);
                    $table_of_contents[] = [
                        'text' => $heading->nodeValue,
                        'id' => $id
                    ];
                }
            }
                
            $blog['description'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|body|xml))[^>]*>\s*~i', '', $doc->saveHTML());
        }

            
        $related_stmt = $conn->prepare("SELECT * FROM blog_posts WHERE category_id = ? AND id != ? ORDER BY created_at DESC LIMIT 3");
        $related_stmt->bind_param("ii", $blog_category_id, $blog_id);
        $related_stmt->execute();
        $related_posts_result = $related_stmt->get_result();
        while ($row = $related_posts_result->fetch_assoc()) {
            $related_posts[] = $row;
        }
        $related_stmt->close();

            
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

    
if (isset($_SESSION['user_id'])) {
    $user_id_session = $_SESSION['user_id'];
    $user_stmt = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id_session);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();
    $user_stmt->close();
}

    
include_once(__DIR__ . '/../../partials/header.php');

    
    
    
?>

    <section class="breadcrump sm:py-3">
    <div class="container">
        <div class="">
            <div class="d-flex gap-2">
                <a href="<?= $base_url ?>" class="text-decoration-none breadcrump-links breadcrump-link-1">Home >></a>
                <a href="<?= $base_url ?>articles"
                    class="text-decoration-none breadcrump-links breadcrump-link-2">Articles</a>
            </div>
        </div>
    </div>
</section>
    
    <section class="single-article-details pb-20">
    <div class="container">
        <?php if (!$blog):     
                ?>
            <div class="text-center mt-5">
                <h1>Blog Post Not Found</h1>
                <p>The post you are looking for does not exist or may have been moved.</p>
                <a href="<?= $base_url ?>articles" class="theme-btn">Back to Articles</a>
            </div>
        <?php else:     
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
                            <h1 class="fos-12 poppins-medium m-0 text-white"><?= htmlspecialchars($blog['author_name']); ?>
                            </h1>
                            <h1 class="fos-12 poppins-medium m-0 text-white">|</h1>
                            <h1 class="fos-12 poppins-medium m-0 text-white">
                                <?= date('jS F, Y', strtotime($blog['created_at'])); ?></h1>
                        </div>
                    </div>
                    <div class="col-12">
                        <h1 class="poppins-medium fos-30 mb-5"><?= htmlspecialchars($blog['title']); ?></h1>
                        <div class="article-content">
                            <?= $blog['description'];     
                                ?>
                        </div>

                        <div class="col mb-10">
                            <hr />
                        </div>
                        <div class="reviews">
                            <h1 class="fos-24">Join the conversation</h1>
                            <?php if (!$user): ?>
                                <h1 class="not-login fos-16">
                                    You must be logged in to comment. <a href="<?= $base_url; ?>login" class="color-pink">Login
                                        Here</a>
                                </h1>
                            <?php else: ?>
                                <form id="commentForm">
                                    <div class="comment-sec d-flex align-items-start gap-3 mb-0.5">
                                        <img src="<?= $base_url; ?>assets/images/userimage.png" alt="" />
                                        <textarea name="comment" id="commentbyallusers" rows="5" class="w-100"
                                            required></textarea>
                                        <button type="submit" class="theme-btn">Submit Comment</button>
                                    </div>
                                    <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>" />
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />

                                </form>
                            <?php endif; ?>
                        </div>
                        <div id="all-comments" class="mt-1"></div>
                    </div>
                </div>
                <div class="col-lg-3 m-0 pe-0">
                   
                    <div class="share-with-community mb-12 text-white">
                        <h1 class="fos-20 poppins-medium mb-6">Share with your community</h1>
                        <div class="social-share d-flex justify-content-between">
                            <a href="https://www.instagram.com/?url=<?= urlencode($currentUrl) ?>" target="_blank"><img
                                    src="<?= $base_url; ?>assets/images/insta.svg" alt="Instagram" /></a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>"
                                target="_blank"><img src="<?= $base_url; ?>assets/images/tweetr.svg" alt="Twitter" /></a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>"
                                target="_blank"><img src="<?= $base_url; ?>assets/images/facebook.svg" alt="Facebook" /></a>
                            <a href="https://x.com/intent/post?url=<?= urlencode($currentUrl) ?>" target="_blank"><img
                                    src="<?= $base_url; ?>assets/images/x.svg" alt="X" /></a>
                            <a class="text-white" href="https://wa.me/?text=<?= urlencode($currentUrl) ?>"
                                target="_blank">WhatsApp</a>
                            <a class="text-white"
                                href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($currentUrl) ?>"
                                target="_blank">LinkedIn</a>
                        </div>
                    </div>

                    <?php if (!empty($table_of_contents)):
                        ?>
                        <div class="inthisarticle">
                            <h1 class="fos-30 poppins-medium mb-7">In this article</h1>
                            <ul class="inthisarticlelist list-unstyled">
                                <?php foreach ($table_of_contents as $item): ?>
                                    <li class="article-list-item mb-1">
                                        <a href="#<?= $item['id'] ?>"
                                            class="toc-link text-decoration-none"><?= htmlspecialchars($item['text']) ?></a>
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
    
    <section class="pagination-sec container pb-24">
    <div class="py-3">
        <div class="">
            <div class="col d-flex align-items-center justify-content-between">
                <?php if ($prevArticle): ?>
                    <div class="pagination-btn">
                    
                    <a href="<?= $base_url ?>article/<?= $prevArticle['blog_slug']; ?>" > Previous</a>
                </div>
                    <?php else: ?>
                    <div class="pagination-btn disabled">
                    
                    <span>Previous</span>
                </div>
                    <?php endif; ?>

                <?php if ($nextArticle): ?>
                    <div  class="pagination-btn">
                    <a href="<?= $base_url ?>article/<?= $nextArticle['blog_slug']; ?>">Next </a>
                </div>
                    <?php else: ?>
                        <div class="pagination-btn disabled">
                    <span >Next</span>
                </div>
                    <?php endif; ?>
            </div>
        </div>
    </div>
</section>
        <?php if (!empty($related_posts)): ?>
    <section class="related-blog bg-gray-50 py-12 md:py-16">
        <div class="container mx-auto">

                            <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Related Blog Posts</h2>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">

                <?php foreach ($related_posts as $related): ?>
                    <?php
                        
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

                                            <div
                        class="group flex flex-col bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden mb-1">

                                                    <a href="<?= $article_url ?>" class="block overflow-hidden">
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="<?= $relatedImage ?>" alt="<?= $title ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 img-ads" />
                            </div>
                        </a>

                                                    <div class="p-5 flex flex-col flex-grow">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 leading-tight">

                                <a href="<?= $article_url ?>"
                                    class="hover:text-gray-900 text-gray-700 transition-colors duration-200 no-underline">
                                    <?= $short_title ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm flex-grow">
                                <?= $excerpt ?>
                            </p>
                        </div>

                                                    <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center gap-3">
                            <img src="<?= $base_url; ?>assets/images/userimage.png" alt="Author: <?= $author_name ?>"
                                class="w-9 h-9 rounded-full">
                            <div>
                                <p class="text-sm font-medium text-gray-800"><?= $author_name ?></p>
                                <p class="text-xs text-gray-500"><?= $created_date ?></p>
                            </div>
                        </div>

                    </div>
                        
                <?php endforeach; ?>

            </div>             </div>
    </section>
<?php endif; ?>
    


<?php include_once(__DIR__ . '/../../partials/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        <?php if ($blog): ?>
            const blogId = <?= $blog['id'] ?>;

            function loadComments() {
                $.post('<?= $base_url ?>ajax/fetch_comments.php', {
                    blog_id: blogId
                }, function (data) {
                    $('#all-comments').html(data);
                }).fail(function () {
                    $('#all-comments').html('<p>Error loading comments.</p>');
                });
            }

            loadComments();

            $('#commentForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '<?= $base_url ?>ajax/submit_comment.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#commentbyallusers').val('');
                            loadComments();
                        } else {
                            alert(response.message || 'An error occurred.');
                        }
                    },
                    error: function () {
                        alert('Could not submit comment. Please try again.');
                    }
                });
            });
        <?php endif; ?>

            
        $('.toc-link').on('click', function (e) {
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