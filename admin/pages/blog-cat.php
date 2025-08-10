<?php
// /admin/pages/blog-cat.php
require_once __DIR__ . '/../../config/config.php';

// Handle category deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $category_id = (int)$_POST['category_id'];
    $stmt = $conn->prepare("DELETE FROM blog_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    if ($stmt->execute()) {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Category deleted successfully!</div>";
    } else {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error deleting category. It might be in use.</div>";
    }
    $stmt->close();
}

// Fetch all blog categories
$stmt = $conn->prepare("SELECT id, name, status, created_at FROM blog_categories ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Blog Categories</h2>

        <!-- Add New Category Form -->
        <div class="mt-8 mb-10 p-6 bg-white rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Add New Blog Category</h3>
            <form action="/admin/admin-page.php" method="POST" class="max-w-lg">
                <div class="mb-4">
                    <label for="category_name_blog" class="block text-gray-700 text-sm font-bold mb-2">Category Name:</label>
                    <input type="text" id="category_name_blog" name="category_name_blog" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center">
                    <button type="submit" name="add_category_1"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Add Category
                    </button>
                </div>
            </form>
        </div>

        <!-- Categories List -->
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="3" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No categories found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($category['name']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="relative inline-block px-3 py-1 font-semibold leading-tight <?php echo $category['status'] === 'live' ? 'text-green-900' : 'text-yellow-900'; ?>">
                                            <span aria-hidden class="absolute inset-0 opacity-50 rounded-full <?php echo $category['status'] === 'live' ? 'bg-green-200' : 'bg-yellow-200'; ?>"></span>
                                            <span class="relative"><?php echo htmlspecialchars(ucfirst($category['status'])); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                            <button type="submit" name="delete_category" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>