<?php
// /admin/pages/sub-cat.php
require_once __DIR__ . '/../../config/config.php';

// Handle subcategory deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_subcategory'])) {
    $subcategory_id = (int)$_POST['subcategory_id'];
    $stmt = $conn->prepare("DELETE FROM ad_subcategories WHERE id = ?");
    $stmt->bind_param("i", $subcategory_id);
    if ($stmt->execute()) {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Subcategory deleted successfully!</div>";
    } else {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error deleting subcategory.</div>";
    }
    $stmt->close();
}

// Fetch all subcategories with their parent category name
$stmt = $conn->prepare("
    SELECT 
        sc.id, 
        sc.title, 
        ac.name AS category_name,
        sc.created_at
    FROM 
        ad_subcategories sc
    JOIN 
        ad_categories ac ON sc.category_id = ac.id
    ORDER BY 
        ac.name, sc.title
");
$stmt->execute();
$result = $stmt->get_result();
$subcategories = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch all parent categories for the "Add New" form
$category_stmt = $conn->prepare("SELECT id, name FROM ad_categories ORDER BY name");
$category_stmt->execute();
$category_result = $category_stmt->get_result();
$categories = $category_result->fetch_all(MYSQLI_ASSOC);
$category_stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold leading-tight">Ad Subcategories</h2>
            <a href="/admin/add-ad-category" data-page="add-ad-category" class="tab-link bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Add New Subcategory
            </a>
        </div>
        
        <!-- Subcategories List -->
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Subcategory Title
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Parent Category
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subcategories)): ?>
                            <tr>
                                <td colspan="3" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No subcategories found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subcategories as $sub): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($sub['title']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($sub['category_name']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this subcategory?');">
                                            <input type="hidden" name="subcategory_id" value="<?php echo $sub['id']; ?>">
                                            <button type="submit" name="delete_subcategory" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
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
