<?php
require_once __DIR__ . '/../../config/config.php';
?>
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Add New Subcategory</h2>
    <div class="bg-white shadow-md rounded-lg p-6">
        <form method="POST" action="index.php" class="space-y-6">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Select Category</label>
                <select id="category_id" name="category_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                    <option value="">-- Select a Category --</option>
                    <?php
                    $result = $conn->query("SELECT id, name FROM ad_categories ORDER BY name ASC");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="subcategory_title" class="block text-sm font-medium text-gray-700 mb-1">Subcategory Title</label>
                <input type="text" id="subcategory_title" name="subcategory_title" placeholder="e.g., Smartphones" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="flex justify-end">
                <button type="submit" name="add_subcategory" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Subcategory
                </button>
            </div>
        </form>
    </div>
</div>