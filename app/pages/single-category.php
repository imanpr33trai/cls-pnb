<?php
include_once(__DIR__ . '/config/config.php'); // always load this first
require __DIR__ . '/../../config/whoops.php';
// --- NEW LOGIC TO HANDLE SLUG-BASED URLS ---

// 1. Get the slug from the URL provided by .htaccess
// The URL is now .../category/some-slug, which our .htaccess turns into .../single-category.php?slug=some-slug
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    // If no slug is provided, it's a bad request. Show a 404 error.
    header("HTTP/1.0 404 Not Found");
    include_once(__DIR__ . '/../../partials/header.php');
    exit();
}

$categorySlug = $_GET['slug'];

// 2. Find the category's ID and Name from its SLUG
// We use a prepared statement for security against SQL injection.
$stmt = $conn->prepare("SELECT id, name FROM ad_categories WHERE slug = ? AND LOWER(status) = 'live' LIMIT 1");
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no category is found with this slug, it's a 404 error.
    header("HTTP/1.0 404 Not Found");
    include_once(__DIR__ . '/../../partials/header.php');
    exit();
}

$category = $result->fetch_assoc();
$category_id = $category['id'];       // THIS IS THE ID WE NEED FOR THE REST OF THE PAGE
$category_name = $category['name']; // We can use this for titles, etc.
$stmt->close();

// --- END OF NEW LOGIC ---

// Now the rest of your page can proceed, because we have the correct $category_id
include_once(__DIR__ . '/../../partials/header.php');
?>

<!-- Your other partials can be included as before -->
<?php include 'partials/hero-sec.php'; ?>
<?php include 'partials/section-1.php'; ?>
<?php include 'partials/category-sec.php'; ?>
<?php include 'partials/search-sec.php'; ?>

<!-- section-4 posts -->
<section class="section-4 pb-100">
    <?php
    // This part of the logic remains almost the same, as it now has the correct $category_id

    // Fetch all subcategories of this category using the ID we found earlier
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
                    <!-- Display the dynamic category name we fetched -->
                    <h1 class="poppins-regular fos-20 mb-30">Subcategories of <?= htmlspecialchars($category_name) ?></h1>
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
            <div class="col-12 col-sm-6 col-md-8 col-lg-9 d-flex justify-content-between flex-wrap" id="subcat-posts">
                <!-- Initial loading message, JavaScript will replace this -->
                <div class="w-100 text-center">
                    <p>Loading posts...</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- section-4 posts -->

<!-- The JavaScript does NOT need to be changed, it's already perfect! -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const subcatLinks = document.querySelectorAll('.subcats-fetched a');
        const postContainer = document.getElementById('subcat-posts');

        function loadPosts(subcatID) {
            // Show loading state
            postContainer.innerHTML = '<div class="w-100 text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

            // The AJAX call remains the same, as it correctly uses the subcategory ID
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

        // Auto-load posts for the first subcategory, if it exists
        const firstActive = document.querySelector('.subcats-fetched a.active');
        if (firstActive) {
            loadPosts(firstActive.dataset.subid);
        } else {
            // If there are no subcategories, show a message
            postContainer.innerHTML = "<div class='w-100'><p>Select a subcategory to view ads, or create one if none exist.</p></div>";
        }

        // Add click event listeners for other subcategories
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