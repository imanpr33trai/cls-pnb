<?php
include_once('../../config/config.php');
?>
<h2>Add New Subcategory</h2>
<form method="POST" action="index.php">
    <select name="category_id" required>
        <option value="">Select Category</option>
        <?php
        $result = $conn->query("SELECT id, name FROM ad_categories");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select><br><br>
    <input type="text" name="subcategory_title" placeholder="Subcategory Title" required><br><br>
    <button type="submit" name="add_subcategory">Add Subcategory</button>
</form>