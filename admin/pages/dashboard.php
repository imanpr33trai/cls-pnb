<?php
include_once('../../config/config.php');
?>
<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $id = $_POST['edit_id'];
    $name = trim($_POST['edit_name']);
    
    // Get current image
    $res = $conn->query("SELECT image FROM ad_categories WHERE id = $id");
    $row = $res->fetch_assoc();
    $image_name = $row['image'];

    if (!empty($_FILES['edit_image']['name'])) {
        $upload_dir = '../../assets/uploads/';
        $image_name = time() . '_' . basename($_FILES['edit_image']['name']);
        $target_path = $upload_dir . $image_name;
        move_uploaded_file($_FILES['edit_image']['tmp_name'], $target_path);
    }

    $stmt = $conn->prepare("UPDATE ad_categories SET name = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $image_name, $id);
    $stmt->execute();

    echo "<script>alert('Category updated successfully!'); location.reload();</script>";
}

?>
<h2>All Categories</h2>

<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; margin-bottom: 40px;">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Status</th>
            <th>Subcategories</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $categories = $conn->query("SELECT * FROM ad_categories ORDER BY id DESC");
        while ($cat = $categories->fetch_assoc()):
            $subcats = $conn->query("SELECT title, status FROM ad_subcategories WHERE category_id = {$cat['id']}");
        ?>
        <tr>
            <td>
                <img src="../../assets/uploads/<?= $cat['image'] ?>" width="60" height="60" style="object-fit:cover;">
            </td>
            <td><?= $cat['name'] ?></td>
            <td>
                <span style="color:<?= $cat['status'] === 'live' ? 'green' : 'gray' ?>;">
                    <?= ucfirst($cat['status']) ?>
                </span>
            </td>
            <td>
                <?php while ($sub = $subcats->fetch_assoc()): ?>
                <?= $sub['title'] ?>
                <small style="color:<?= $sub['status'] === 'live' ? 'green' : 'gray' ?>;">
                    (<?= $sub['status'] ?>)
                </small><br>
                <?php endwhile; ?>
            </td>
            <td>
                <a href="pages/update-category.php?id=<?= $cat['id'] ?>">
                    <button>Edit</button>
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>


<!-- Edit Category Modal -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background:white; padding:20px; border-radius:10px; width:400px; position:relative;">
        <h3>Edit Category</h3>
        <form method="POST" enctype="multipart/form-data" id="editForm">
            <input type="hidden" name="edit_id" id="edit_id">
            <div>
                <label>Category Name:</label>
                <input type="text" name="edit_name" id="edit_name" required>
            </div>
            <div>
                <label>Category Image:</label>
                <input type="file" name="edit_image">
                <br>
                <img id="preview_img" src="" width="80" style="margin-top:10px;">
            </div>
            <div style="margin-top:15px;">
                <button type="submit" name="update_category">Update</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>