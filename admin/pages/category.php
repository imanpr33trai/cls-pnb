<h2>Add New Category</h2>
<!-- <form method="POST" action="pages/save_category.php" enctype="multipart/form-data"> -->
<form method="POST" action="index.php" enctype="multipart/form-data">
    <input type="text" name="category_name" placeholder="Category Name" required><br><br>
    <input type="file" name="category_image" accept="image/*" required><br><br>
    <button type="submit" name="add_category">Add Category</button>
</form>