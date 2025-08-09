<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Add New Category</h2>
    <div class="bg-white shadow-md rounded-lg p-6">
        <form method="POST" action="index.php" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="category_name" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                <input type="text" id="category_name" name="category_name" placeholder="e.g., Electronics" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div>
                <label for="category_image" class="block text-sm font-medium text-gray-700 mb-1">Category Image</label>
                <input type="file" id="category_image" name="category_image" accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
            </div>
            <div class="flex justify-end">
                <button type="submit" name="add_category" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Category
                </button>
            </div>
        </form>
    </div>
</div>