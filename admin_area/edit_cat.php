<?php  
if (!isset($_SESSION['admin_email'])){

  echo "<script>window.open('login.php','_self')</script>";
  }else{


if (isset($_GET['edit_cat'])) {
    $cat_id = $_GET['edit_cat'];
}
?>
<?php
if (isset($_GET['edit_cat'])) {
    $edit_id = $_GET['edit_cat'];
    $get_cat = "SELECT * FROM ad_categories WHERE id = '$edit_id'";
    $run_cat = mysqli_query($con, $get_cat);
    $row_cat = mysqli_fetch_array($run_cat);

    $c_id = $row_cat['id'];
    $c_title = $row_cat['name'];
    $c_image = $row_cat['image'];
    $c_status = $row_cat['status'];
}

if (isset($_POST['update'])) {
    $cat_title = $_POST['cat_title'];
    $cat_status = $_POST['cat_status'];

    $cat_image = $c_image; // Default to old image

    if (!empty($_FILES['category_image']['name'])) {
        $upload_dir = '../assets/uploads/';
        $new_image_name = time() . '_' . basename($_FILES['category_image']['name']);
        $target_path = $upload_dir . $new_image_name;

        if (move_uploaded_file($_FILES['category_image']['tmp_name'], $target_path)) {
            $cat_image = $new_image_name;
        }
    }

    $update_cat = "UPDATE ad_categories 
                   SET name='$cat_title', image='$cat_image', status='$cat_status' 
                   WHERE id='$c_id'";
    $run_cat = mysqli_query($con, $update_cat);

    if ($run_cat) {
        echo "<script>alert('Category has been updated successfully');</script>";
        echo "<script>window.open('index.php?view_categories','_self');</script>";
    } else {
        echo "<script>alert('Category update failed');</script>";
    }
}
?>
<?php
// Fetch category info (already there)

// Fetch Subcategories
$subcategories = [];
if (isset($_GET['edit_cat'])) {
    $c_id = $_GET['edit_cat'];

    $get_subcats = "SELECT * FROM ad_subcategories WHERE category_id = '$c_id'";
    $run_subcats = mysqli_query($con, $get_subcats);

    while ($row_subcat = mysqli_fetch_array($run_subcats)) {
        $subcategories[] = $row_subcat;
    }
}
?>


<div class="row">
    <!--breadcrump start-->
    <div class="col-lg-12">
        <div class="breadcrump">
            <li class="active">
                <i class="fa fa-bar-chart"></i>
                Dashboard / Edit Cetegory
            </li>
        </div>
    </div>
</div>
<!--breadcrump End-->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!--panel-heading start-->
                <h3 class="panel-title">
                    <i class="fa fa-money fa-fw"> Edit Category</i>
                </h3>
            </div>
            <!--panel-heading End-->
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Title</label>
                        <div class="col-md-6">
                            <input type="text" name="cat_title" class="form-control" required
                                value="<?php echo $c_title; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Image</label>
                        <div class="col-md-6">
                            <input type="file" name="category_image" accept="image/*"><br>
                            <img src="../assets/uploads/<?php echo $c_image; ?>" width="70" height="70"
                                style="margin-top:10px; object-fit:cover;">
                            <p style="color:gray; font-size:12px;">(Leave blank if you don't want to change the image)
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Status</label>
                        <div class="col-md-6">
                            <select name="cat_status" class="form-control" required>
                                <option value="live" <?php if($c_status == 'live') echo 'selected'; ?>>Live</option>
                                <option value="hold" <?php if($c_status == 'hold') echo 'selected'; ?>>Hold</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                            <input type="submit" name="update" value="Update Category"
                                class="btn btn-primary form-control">
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!--panel-heading start-->
                <h3 class="panel-title">
                    <i class="fa fa-money fa-fw"> Edit Category</i>
                </h3>
            </div>
            <!--panel-heading End-->
            <div class="panel-body">
                <!-- <hr> -->
                <!-- <h3 class="text-center">Sub Categories of this Category</h3> -->

                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sub Category Title</th>
                            <th>Status</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
        $i = 0;
        foreach ($subcategories as $subcat) { 
            $i++;
        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $subcat['title']; ?></td>
                            <td>
                                <?php echo ucfirst($subcat['status']); ?>
                            </td>
                            <td>
                                <a href="index.php?edit_subcat=<?php echo $subcat['id']; ?>&cat_id=<?php echo $c_id; ?>"
                                    class="btn btn-sm btn-primary">
                                    <i class="fa fa-pen"></i> Edit
                                </a>
                            </td>
                            <td>
                                <a
                                    href="delete_subcat.php?delete_subcat=<?php echo $subcat['id']; ?>&cat_id=<?php echo $cat_id; ?>">
                                    <i class="fa fa-trash"></i> Delete
                                </a>


                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>



<?php } ?>