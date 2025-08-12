<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = htmlspecialchars(trim($_POST['category_name']));
    $image_name = '';
    $ad_slug = create_unique_slug($conn, $category_name, 'ad_categories', 'slug');
    if (!empty($category_name) && isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
        $upload_dir = __DIR__ . '/../../assets/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $image_name = time() . '_' . basename($_FILES['category_image']['name']);
        $target_file = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['category_image']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO ad_categories (name, slug, image, status) VALUES (?, ?, ?, 'live')");
            $stmt->bind_param("sss", $category_name, $ad_slug, $image_name);
            if ($stmt->execute()) {
                echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Category added successfully!</div>";
            } else {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error adding category.</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error uploading image.</div>";
        }
    } else {
        echo "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4'>⚠️ Please fill in all fields and select an image.</div>";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subcategory'])) {
    $category_id = (int)$_POST['category_id'];
    $subcategory_title = htmlspecialchars(trim($_POST['subcategory_title']));
    $sub_cat_slug = create_unique_slug($conn, $subcategory_title, 'ad_subcategories', 'slug');

    if (!empty($category_id) && !empty($subcategory_title)) {
        $stmt = $conn->prepare("INSERT INTO ad_subcategories (category_id, title, slug) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $category_id, $subcategory_title, $sub_cat_slug);
        if ($stmt->execute()) {
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Subcategory added successfully!</div>";
        } else {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error adding subcategory.</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4'>⚠️ Please fill in all fields.</div>";
    }
}
$category_stmt = $conn->prepare("SELECT id, name FROM ad_categories ORDER BY name");
$category_stmt->execute();
$category_result = $category_stmt->get_result();
$categories = $category_result->fetch_all(MYSQLI_ASSOC);
$category_stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">

        <div class="mb-10">
            <h2 class="text-2xl font-semibold leading-tight">Add New Ad Category</h2>
            <div class="mt-4 p-6 bg-white rounded-lg shadow">
                <form action="" method="POST" enctype="multipart/form-data" class="max-w-lg">
                    <div class="mb-4">
                        <label for="category_name" class="block text-gray-700 text-sm font-bold mb-2">Category Name:</label>
                        <input type="text" id="category_name" name="category_name" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-6">
                        <label for="category_image" class="block text-gray-700 text-sm font-bold mb-2">Category Image:</label>
                        <input type="file" id="category_image" name="category_image" accept="image/*" required
                            class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" name="add_category"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Add Category
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <div>
            <h2 class="text-2xl font-semibold leading-tight">Add New Subcategory</h2>
            <div class="mt-4 p-6 bg-white rounded-lg shadow">
                <form action="" method="POST" class="max-w-lg">
                    <div class="mb-4">
                        <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Parent Category:</label>
                        <select id="category_id" name="category_id" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select a Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="subcategory_title" class="block text-gray-700 text-sm font-bold mb-2">Subcategory Title:</label>
                        <input type="text" id="subcategory_title" name="subcategory_title" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center">
                        <button type="submit" name="add_subcategory"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Add Subcategory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
