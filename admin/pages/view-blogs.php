<?php
// /admin/pages/view-blogs.php
require_once __DIR__ . '/../../config/config.php';

// Handle blog post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_blog'])) {
    $blog_id = (int)$_POST['blog_id'];

    // Optional: Delete associated image if it exists
    $stmt = $conn->prepare("SELECT image FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['image'])) {
            $image_path = __DIR__ . '/../../assets/uploads/' . $row['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }
    $stmt->close();

    // Delete the blog post
    $delete_stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
    $delete_stmt->bind_param("i", $blog_id);
    if ($delete_stmt->execute()) {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Blog post deleted successfully!</div>";
    } else {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error deleting blog post.</div>";
    }
    $delete_stmt->close();
}

// Fetch all blog posts
$stmt = $conn->prepare("
    SELECT 
        p.id, 
        p.title, 
        p.author_name,
        c.name AS category_name,
        p.created_at
    FROM 
        blog_posts p
    LEFT JOIN 
        blog_categories c ON p.category_id = c.id
    ORDER BY 
        p.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$blogs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">All Blog Posts</h2>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Title
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Author
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Published On
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($blogs)): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No blog posts found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($blogs as $blog): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($blog['title']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($blog['author_name']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($blog['category_name'] ?? 'N/A'); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo date("F j, Y", strtotime($blog['created_at'])); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                            <button type="submit" name="delete_blog" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
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
