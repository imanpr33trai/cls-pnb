<?php
if (!isset($_SESSION['admin_email'])){

  echo "<script>window.open('login.php','_self')</script>";
  }else{
?>



<div class="row">
    <!--breadcrump start-->
    <div class="col-lg-12">
        <div class="breadcrump">
            <li class="active">
                <i class="fa fa-bar-chart"></i>
                Dashboard / Insert Category
            </li>
        </div>
    </div>
</div>
<!--breadcrump End-->
<div class="row">
    <div class="col-lg-3">

    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!--panel-heading start-->
                <h3 class="panel-title">
                    <i class="fa fa-money fa-fw"> Insert Category </i>
                </h3>
            </div>
            <!--panel-heading End-->
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Title</label>
                        <div class="col-md-6">
                            <input type="text" name="cat_title" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Image</label>
                        <div class="col-md-6">
                            <input type="file" name="category_image" accept="image/*" required><br><br>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Status</label>
                        <div class="col-md-6">
                            <select name="cat_status" class="form-control" required>
                                <option value="live">Live</option>
                                <option value="hold">Hold</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                            <input type="submit" name="submit" value="Insert Category"
                                class="btn btn-primary form-control">
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">

    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!--panel-heading start-->
                <h3 class="panel-title">
                    <i class="fa fa-money fa-fw"> Insert Sub Category </i>
                </h3>
            </div>
            <!--panel-heading End-->
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Select Category</label>
                        <div class="col-md-6">
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php
                $get_categories = "SELECT * FROM ad_categories WHERE status = 'live'";
                $run_categories = mysqli_query($con, $get_categories);
                while($row_cat = mysqli_fetch_array($run_categories)){
                    echo "<option value='".$row_cat['id']."'>".$row_cat['name']."</option>";
                }
                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Sub Category Title</label>
                        <div class="col-md-6">
                            <input type="text" name="subcat_title" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Status</label>
                        <div class="col-md-6">
                            <select name="subcat_status" class="form-control" required>
                                <option value="live">Live</option>
                                <option value="hold">Hold</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                            <input type="submit" name="insert_subcat" value="Insert Sub Category"
                                class="btn btn-success form-control">
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>

<?php
if (isset($_POST['submit'])) {
    $cat_title = $_POST['cat_title'];
    $cat_status = $_POST['cat_status'];

    $image_name = '';
    if (!empty($_FILES['category_image']['name'])) {
        $upload_dir = '../assets/uploads/';
        $image_name = time() . '_' . basename($_FILES['category_image']['name']);
        $target_path = $upload_dir . $image_name;
        move_uploaded_file($_FILES['category_image']['tmp_name'], $target_path);
    }

    $insert_cat = "INSERT INTO ad_categories (name, image, status, created_at) 
                   VALUES ('$cat_title', '$image_name', '$cat_status', NOW())";

    $run_cat = mysqli_query($con, $insert_cat);

    if ($run_cat) {
        echo "<script>alert('New Category has been Inserted successfully');</script>";
        echo "<script>window.open('index.php?view_categories','_self');</script>";
    }
}
?>
<?php
if (isset($_POST['insert_subcat'])) {
    $category_id = $_POST['category_id'];
    $subcat_title = $_POST['subcat_title'];
    $subcat_status = $_POST['subcat_status'];
    $created_at = date('Y-m-d H:i:s');

    $insert_subcat = "INSERT INTO ad_subcategories (category_id, title, created_at, status)
                      VALUES ('$category_id', '$subcat_title', '$created_at', '$subcat_status')";

    $run_subcat = mysqli_query($con, $insert_subcat);

    if ($run_subcat) {
        echo "<script>alert('New Sub Category has been Inserted successfully')</script>";
        echo "<script>window.open('index.php?view_categories','_self')</script>";
    } else {
        echo "<script>alert('Failed to Insert Sub Category')</script>";
    }
}
?>




<?php } ?>