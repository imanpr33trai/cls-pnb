<?php
include_once(__DIR__ . '/../../config/config.php');
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("HTTP/1.0 404 Not Found");
    include_once(__DIR__ . '/../../partials/header.php');
    exit();
}

$categorySlug = $_GET['slug'];
$stmt = $conn->prepare("SELECT id, name FROM ad_categories WHERE slug = ? AND LOWER(status) = 'live' LIMIT 1");
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("HTTP/1.0 404 Not Found");
    include_once(__DIR__ . '/../../partials/header.php');
    exit();
}

$category = $result->fetch_assoc();
$category_id = $category['id'];
$category_name = $category['name'];
$stmt->close();
include_once(__DIR__ . '/../../partials/header.php');
?>


<?php include 'partials/hero-sec.php'; ?>
<?php include 'partials/section-1.php'; ?>
<?php include 'partials/category-sec.php'; ?>
<?php include __DIR__ . '/../../partials/search-sec.php'; ?>


<section class="section-4 pb-100">
    <?php

    $subQuery = $conn->prepare("SELECT id, title FROM ad_subcategories WHERE category_id = ? ORDER BY id ASC");
    $subQuery->bind_param("i", $category_id);
    $subQuery->execute();
    $subResult = $subQuery->get_result();

    $subcategories = [];
    while ($sub = $subResult->fetch_assoc()) {
        $subcategories[] = $sub;
    }
    $subQuery->close();
    ?>
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 mb-md-0">
                <div class="sidebar-category">

                    <h1 class="poppins-regular fos-20 mb-7">Subcategories of <?= htmlspecialchars($category_name) ?></h1>
                    <div class="col fos-16 subcats-fetched">

                        <?php if (!empty($subcategories)): ?>
                            <?php foreach ($subcategories as $index => $sub): ?>
                                <a href="#"
                                    class="<?= $index === 0 ? 'active' : '' ?>"
                                    data-subid="<?= $sub['id'] ?>">
                                    <?= htmlspecialchars($sub['title']) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No subcategories found.</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-8 col-lg-9 d-flex gap-3 flex-wrap" id="subcat-posts">

                <div class="w-100 text-center">
                    <p>Loading posts...</p>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        const subcatLinks = document.querySelectorAll('.subcats-fetched a');
        const postContainer = document.getElementById('subcat-posts');

        function loadPosts(subcatID) {
            postContainer.innerHTML = '<div class="w-100 text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

            fetch(`<?php echo $base_url; ?>ajax/load-subcat-posts.php?subcat_id=${subcatID}`)
                .then(response => response.text())
                .then(html => {
                    postContainer.innerHTML = html;
                })
                .catch(err => {
                    console.error("Error:", err);
                    postContainer.innerHTML = "<p class='text-danger'>Error loading posts. Please try again later.</p>";
                });
        }

        const firstActive = document.querySelector('.subcats-fetched a.active');
        if (firstActive) {
            loadPosts(firstActive.dataset.subid);
        } else {
            postContainer.innerHTML = "<div class='w-100'><p>Select a subcategory to view ads, or create one if none exist.</p></div>";
        }

        subcatLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                subcatLinks.forEach(el => el.classList.remove('active'));
                this.classList.add('active');
                const subID = this.dataset.subid;
                loadPosts(subID);
            });
        });
    });
</script>

<?php
include_once(__DIR__ . '/../../partials/footer.php');
?>