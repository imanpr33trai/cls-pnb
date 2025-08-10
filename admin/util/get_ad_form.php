<?php
// /admin/util/get_ad_form.php
require_once __DIR__ . '/../../config/config.php';

// Function to fetch categories from the database
function getCategories($conn) {
    $categories = [];
    $result = $conn->query("SELECT id, name FROM ad_categories ORDER BY name ASC");
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}

// Function to fetch subcategories for a given category
function getSubcategories($conn, $categoryId) {
    $subcategories = [];
    $stmt = $conn->prepare("SELECT id, title FROM ad_subcategories WHERE category_id = ? ORDER BY title ASC");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $subcategories[] = $row;
    }
    $stmt->close();
    return $subcategories;
}

$ad_id = isset($_GET['ad_id']) ? (int)$_GET['ad_id'] : 0;

if ($ad_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM ad_form WHERE id = ?");
    $stmt->bind_param("i", $ad_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ad = $result->fetch_assoc();
    $stmt->close();

    if ($ad) {
        $categories = getCategories($conn);
        $subcategories = getSubcategories($conn, $ad['category']);
?>
<form id="edit-ad-form" method="POST" action="/admin/util/edit-ad.php">
    <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
    <input type="hidden" name="update_ad" value="1">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Ad Title -->
        <div class="mb-4">
            <label for="ad_title" class="block text-gray-700 text-sm font-bold mb-2">Ad Title</label>
            <input type="text" id="ad_title" name="ad_title" value="<?php echo htmlspecialchars($ad['ad_title']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Category -->
        <div class="mb-4">
            <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
            <select id="category" name="category" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($ad['category'] == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Subcategory -->
        <div class="mb-4">
            <label for="subcategory" class="block text-gray-700 text-sm font-bold mb-2">Subcategory</label>
            <select id="subcategory" name="subcategory" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <?php foreach ($subcategories as $subcat): ?>
                    <option value="<?php echo $subcat['id']; ?>" <?php echo ($ad['subcategory'] == $subcat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($subcat['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Asking Price -->
        <div class="mb-4">
            <label for="asking_price" class="block text-gray-700 text-sm font-bold mb-2">Asking Price</label>
            <input type="number" step="0.01" id="asking_price" name="asking_price" value="<?php echo htmlspecialchars($ad['asking_price']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- User Name -->
        <div class="mb-4">
            <label for="user_name" class="block text-gray-700 text-sm font-bold mb-2">User Name</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($ad['user_name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($ad['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($ad['phone']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Location -->
        <div class="mb-4">
            <label for="location" class="block text-gray-700 text-sm font-bold mb-2">Location</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($ad['location']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Status -->
        <div class="mb-4">
            <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
            <select id="status" name="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="live" <?php echo ($ad['status'] == 'live') ? 'selected' : ''; ?>>Live</option>
                <option value="pending" <?php echo ($ad['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="expired" <?php echo ($ad['status'] == 'expired') ? 'selected' : ''; ?>>Expired</option>
                <option value="deleted" <?php echo ($ad['status'] == 'deleted') ? 'selected' : ''; ?>>Deleted</option>
            </select>
        </div>
    </div>

    <!-- Description -->
    <div class="mb-6">
        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
        <textarea id="description" name="description" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($ad['description']); ?></textarea>
    </div>

    <div class="flex items-center justify-end">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Update Ad
        </button>
    </div>
</form>
<?php
    } else {
        echo "Ad not found.";
    }
} else {
    echo "Invalid Ad ID.";
}
?>
