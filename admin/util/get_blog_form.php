<?php
// /admin/util/get_blog_form.php
require_once __DIR__ . '/../../config/config.php';

$blog_id = isset($_GET['blog_id']) ? (int)$_GET['blog_id'] : 0;

if ($blog_id > 0) {
    // Fetch blog post
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();
    $stmt->close();

    // Fetch categories
    $cat_stmt = $conn->prepare("SELECT id, name FROM blog_categories ORDER BY name ASC");
    $cat_stmt->execute();
    $categories = $cat_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $cat_stmt->close();

    if ($blog) {
?>
<form id="edit-blog-form" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
        </div>
        <div class="mb-4">
            <label for="author_name" class="block text-gray-700 text-sm font-bold mb-2">Author Name:</label>
            <input type="text" name="author_name" value="<?php echo htmlspecialchars($blog['author_name']); ?>" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
        </div>
        <div class="mb-4">
            <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category:</label>
            <select name="category_id" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($blog['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mb-4">
        <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Content:</label>
        <textarea name="content" rows="10" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"><?php echo htmlspecialchars($blog['content']); ?></textarea>
    </div>

    <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2">Current Image:</label>
        <?php if(!empty($blog['image'])): ?>
            <img src="../assets/uploads/<?php echo htmlspecialchars($blog['image']); ?>" alt="Blog Image" class="w-32 h-32 rounded-md object-cover">
        <?php else: ?>
            <p>No image uploaded.</p>
        <?php endif; ?>
    </div>

    <div class="mb-6">
        <label for="blog_image" class="block text-gray-700 text-sm font-bold mb-2">New Image (optional):</label>
        <input type="file" name="blog_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
    </div>

    <div class="flex items-center justify-end">
        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Update Post
        </button>
    </div>
</form>
<?php
    } else {
        echo "Blog post not found.";
    }
} else {
    echo "Invalid Blog ID.";
}
?>