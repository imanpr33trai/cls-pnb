<?php

require_once __DIR__ . '/../../config/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $category_id = (int)$_POST['category_id'];

    
    $stmt = $conn->prepare("SELECT image FROM ad_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $image_path = __DIR__ . '/../../assets/uploads/' . $row['image'];
        if (!empty($row['image']) && file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $stmt->close();

    
    $delete_stmt = $conn->prepare("DELETE FROM ad_categories WHERE id = ?");
    $delete_stmt->bind_param("i", $category_id);
    if ($delete_stmt->execute()) {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Category deleted successfully!</div>";
    } else {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error deleting category. It might be in use.</div>";
    }
    $delete_stmt->close();
}



$stmt = $conn->prepare("SELECT id, name, image, status, created_at FROM ad_categories ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold leading-tight">Ad Categories</h2>
            <a href="/admin/add-ad-category" data-page="add-ad-category" class="tab-link bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Add New Category
            </a>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Image
                            </th>
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
                                <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No categories found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex-shrink-0 w-16 h-16">
                                            <img class="w-full h-full rounded-md object-cover"
                                                 src="../assets/uploads/<?php echo htmlspecialchars($category['image']); ?>"
                                                 alt="Category Image" />
                                        </div>
                                    </td>
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
                                         <button class="text-indigo-600 hover:text-indigo-900 mr-3 open-edit-category-modal" data-category-id="<?php echo $category['id']; ?>">Edit</button>
                                        <button class="text-red-600 hover:text-red-900 font-semibold open-delete-category-modal" data-category-id="<?php echo $category['id']; ?>">Delete</button>
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

