<?php

require_once __DIR__ . '/../../config/config.php';

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($category_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM ad_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();

    if ($category) {
?>
<form id="edit-category-form" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
    
    <div class="mb-4">
        <label for="category_name" class="block text-gray-700 text-sm font-bold mb-2">Category Name:</label>
        <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category['name']); ?>" required
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    </div>

    <div class="mb-6">
        <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
        <select id="status" name="status" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
            <option value="live" <?php echo ($category['status'] === 'live') ? 'selected' : ''; ?>>Live</option>
            <option value="paused" <?php echo ($category['status'] === 'paused') ? 'selected' : ''; ?>>Paused</option>
        </select>
    </div>

    <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2">Current Image:</label>
        <img src="../assets/uploads/<?php echo htmlspecialchars($category['image']); ?>" alt="Category Image" class="w-24 h-24 rounded-md object-cover">
    </div>

    <div class="mb-6">
        <label for="category_image" class="block text-gray-700 text-sm font-bold mb-2">New Image (optional):</label>
        <input type="file" id="category_image" name="category_image" accept="image/*"
               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
    </div>

    <div class="flex items-center justify-end">
        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Update Category
        </button>
    </div>
</form>
<?php
    } else {
        echo "Category not found.";
    }
} else {
    echo "Invalid Category ID.";
}
?>