<?php
// /admin/pages/add-ad-category.php
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">Add New Ad Category</h2>
        </div>
        <div class="mt-8">
            <form action="/admin/add-ad-category" method="POST" enctype="multipart/form-data" class="max-w-lg">
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
</div>
