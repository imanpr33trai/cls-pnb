

<!-- Category Form -->
<div class="container mt-4">
    <h2>Add Blog Category</h2>
    
    <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

    <form method="POST" action="index.php">
        <div class="form-group mb-3">
            <label for="category_name">Category Name</label>
            <input type="text" class="form-control" name="category_name_blog" id="category_name" required>
        </div>
        <button type="submit" name="add_category_1" class="btn btn-primary">Add Category</button>
    </form>
</div>
