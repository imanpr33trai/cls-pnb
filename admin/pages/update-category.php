<?php
include_once('../../config/config.php');

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

<h2>Edit Category</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Category Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" required><br><br>

    <label>Status:</label><br>
    <select name="status" required>
        <option value="live" <?= $cat['status'] === 'live' ? 'selected' : '' ?>>Live</option>
        <option value="hold" <?= $cat['status'] === 'hold' ? 'selected' : '' ?>>Hold</option>
    </select><br><br>

    <label>Current Image:</label><br>
    <img src="../../assets/uploads/<?= $cat['image'] ?>" width="80"><br><br>

    <label>Change Image (optional):</label><br>
    <input type="file" name="image"><br><br>

    <button type="submit" name="update_category">Update Category</button>
</form>

<hr>

<h2>Edit Subcategories</h2>
<form method="POST">
    <?php if ($subcategories->num_rows > 0): ?>
    <?php while ($sub = $subcategories->fetch_assoc()): ?>
    <div style="margin-bottom: 10px;">
        <input type="hidden" name="sub_id[]" value="<?= $sub['id'] ?>">
        <input type="text" name="sub_title[]" value="<?= htmlspecialchars($sub['title']) ?>" required>

        <select name="sub_status[]">
            <option value="live" <?= $sub['status'] === 'live' ? 'selected' : '' ?>>Live</option>
            <option value="hold" <?= $sub['status'] === 'hold' ? 'selected' : '' ?>>Hold</option>
        </select>
    </div>
    <?php endwhile; ?>
    <button type="submit" name="update_subcategories">Update Subcategories</button>
    <?php else: ?>
    <p>No subcategories found.</p>
    <?php endif; ?>
</form>

<br>
<a href="../index.php">&larr; Back to Dashboard</a>