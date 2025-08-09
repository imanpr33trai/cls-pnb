<?php
require_once __DIR__ . '/../../config/config.php';

if (!isset($_GET['id'])) {
    echo "Category ID missing!";
    exit;
}

$id = $_GET['id'];
$cat = $conn->query("SELECT * FROM ad_categories WHERE id = $id")->fetch_assoc();

if (!$cat) {
    echo "Category not found!";
    exit;
}

// ✅ Handle category update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $name = trim($_POST['name']);
    $status = $_POST['status'];
    $image_name = $cat['image'];

    if (!empty($_FILES['image']['name'])) {
        $upload_dir = '../../assets/uploads/';
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
    }

    $stmt = $conn->prepare("UPDATE ad_categories SET name = ?, image = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $image_name, $status, $id);
    $stmt->execute();

    echo "<script>alert('✅ Category updated!'); window.location.href='../index.php';</script>";
    exit;
}

// ✅ Handle subcategory updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_subcategories'])) {
    foreach ($_POST['sub_id'] as $index => $subId) {
        $subTitle = trim($_POST['sub_title'][$index]);
        $subStatus = $_POST['sub_status'][$index];

        $stmt = $conn->prepare("UPDATE ad_subcategories SET title = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssi", $subTitle, $subStatus, $subId);
        $stmt->execute();
    }

    echo "<script>alert('✅ Subcategories updated!'); window.location.href='update-category.php?id=$id';</script>";
    exit;
}

// ✅ Fetch subcategories
$subcategories = $conn->query("SELECT * FROM ad_subcategories WHERE category_id = $id");
?>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Edit Category Section -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Category</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Category Name:</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($cat['name']) ?>" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status:</label>
                    <select id="status" name="status" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="live" <?= $cat['status'] === 'live' ? 'selected' : '' ?>>Live</option>
                        <option value="hold" <?= $cat['status'] === 'hold' ? 'selected' : '' ?>>Hold</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Image:</label>
                    <img src="../assets/uploads/<?= htmlspecialchars($cat['image']) ?>" class="mt-2 w-24 h-24 rounded-md object-cover border border-gray-200">
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Change Image (optional):</label>
                    <input type="file" id="image" name="image" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="flex justify-end">
                    <button type="submit" name="update_category" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Category
                    </button>
                </div>
            </form>
        </div>

        <!-- Edit Subcategories Section -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Subcategories</h2>
            <form method="POST" class="space-y-4">
                <?php if ($subcategories->num_rows > 0): ?>
                    <?php while ($sub = $subcategories->fetch_assoc()): ?>
                        <div class="p-4 border border-gray-200 rounded-md">
                            <input type="hidden" name="sub_id[]" value="<?= $sub['id'] ?>">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="sub_title_<?= $sub['id'] ?>" class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text" id="sub_title_<?= $sub['id'] ?>" name="sub_title[]" value="<?= htmlspecialchars($sub['title']) ?>" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label for="sub_status_<?= $sub['id'] ?>" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select id="sub_status_<?= $sub['id'] ?>" name="sub_status[]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="live" <?= $sub['status'] === 'live' ? 'selected' : '' ?>>Live</option>
                                        <option value="hold" <?= $sub['status'] === 'hold' ? 'selected' : '' ?>>Hold</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <div class="flex justify-end pt-4">
                        <button type="submit" name="update_subcategories" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Update Subcategories
                        </button>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No subcategories found for this category.</p>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="mt-8">
        <a href="../" class="text-indigo-600 hover:text-indigo-900 font-medium">&larr; Back to Dashboard</a>
    </div>
</div>