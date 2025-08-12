<?php
// /admin/util/get_blog_cat_form.php
require_once __DIR__ . '/../../config/config.php';

$category_id = isset($_GET['blog_category_id']) ? (int)$_GET['blog_category_id'] : 0;

if ($category_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM blog_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();

    if ($category) {
?>
<form id="edit-blog-cat-form" method="POST">
    <input type="hidden" name="blog_category_id" value="<?php echo $category['id']; ?>">
    
    <div class="mb-4">
        <label for="blog_category_name" class="block text-gray-700 text-sm font-bold mb-2">Category Name:</label>
        <input type="text" id="blog_category_name" name="blog_category_name" value="<?php echo htmlspecialchars($category['name']); ?>" required
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    </div>

    <div class="mb-6">
        <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
        <select id="status" name="status" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
            <option value="live" <?php echo ($category['status'] === 'live') ? 'selected' : ''; ?>>Live</option>
            <option value="paused" <?php echo ($category['status'] === 'paused') ? 'selected' : ''; ?>>Paused</option>
        </select>
    </div>

    <div class="flex items-center justify-end">
        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Update Category
        </button>
    </div>
</form>
<?php
    } else {
        echo "Blog category not found.";
    }
} else {
    echo "Invalid Blog Category ID.";
}
?>
