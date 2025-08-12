<?php
   
   
include_once(__DIR__ . '/../../config/config.php');
include_once(__DIR__ . '/../../partials/header.php');

   
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_id = isset($_GET['cat']) ? $_GET['cat'] : 'all';

   
$sql = "SELECT id, ad_title, description, image, ad_slug, location, city_town_neighbourhood FROM ad_form WHERE status = 'live'";

   
$params = [];
$types = "";

   
if ($query !== '') {
    $sql .= " AND (ad_title LIKE ? OR description LIKE ? OR location LIKE ? OR city_town_neighbourhood LIKE ? OR organisation LIKE ?)";
    $search_term = "%" . $query . "%";
       
    for ($i = 0; $i < 5; $i++) {
        $params[] = &$search_term;
    }
    $types .= "sssss";
}

   
if ($category_id !== 'all' && is_numeric($category_id)) {
    $sql .= " AND category = ?";
    $params[] = &$category_id;
    $types .= "i";
}

$sql .= " ORDER BY created_at DESC";

   
$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $ads = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
       
    $ads = [];
    echo "<p>There was an error preparing the search query.</p>";
}

?>

<main class="container mx-auto my-8">
    <div class="search-results-container">
        <h1 class="text-3xl font-bold mb-4">Search Results</h1>

        <?php if ($query): ?>
            <p class="mb-6 text-lg">You searched for: <strong>"<?= htmlspecialchars($query) ?>"</strong></p>
        <?php endif; ?>

        <?php if (!empty($ads)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($ads as $ad): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                        <a href="/ads/<?= htmlspecialchars($ad['ad_slug']) ?>">
                            <?php 
                                $image_path = !empty($ad['image']) ? $base_url . 'uploads/' . htmlspecialchars($ad['image']) : $base_url . 'assets/images/placeholder.jpg';
                            ?>
                            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($ad['ad_title']) ?>" class="w-full h-48 object-cover">
                        </a>
                        <div class="p-4 flex flex-col flex-grow">
                            <h3 class="text-xl font-semibold mb-2">
                                <a href="/ads/<?= htmlspecialchars($ad['ad_slug']) ?>" class="hover:text-pink-500">
                                    <?= htmlspecialchars($ad['ad_title']) ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-2">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                <?php if (!empty($ad['city_town_neighbourhood'])) echo htmlspecialchars($ad['city_town_neighbourhood']); ?>
                                <?php if (!empty($ad['city_town_neighbourhood']) && !empty($ad['location'])) echo ", "; ?>
                                <?php if (!empty($ad['location'])) echo htmlspecialchars($ad['location']); ?>
                            </p>
                            <p class="text-gray-700 flex-grow">
                                <?= htmlspecialchars(substr($ad['description'], 0, 100)) . '...' ?>
                            </p>
                            <a href="/ads/<?= htmlspecialchars($ad['ad_slug']) ?>" class="text-pink-600 hover:underline mt-4 self-start">Read More</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <p class="text-xl text-gray-500">No ads found matching your criteria.</p>
                <a href="<?= $base_url ?>" class="mt-4 inline-block bg-pink-500 text-white px-6 py-2 rounded-md hover:bg-pink-600">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
   
include_once(__DIR__ . '/footer.php');
?>