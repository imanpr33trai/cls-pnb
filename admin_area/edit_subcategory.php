<?php
if (!isset($_SESSION['admin_email'])){

  echo "<script>window.open('login.php','_self')</script>";
  }else{
?>

<?php
// Check if we have the category id and subcategory id
if (isset($_GET['edit_subcat'])) {
    $subcat_id = $_GET['edit_subcat'];
    $cat_id = $_GET['cat_id']; // Category ID from the URL
    
    // Fetch the subcategory data from the database
    $get_subcat = "SELECT * FROM ad_subcategories WHERE id = '$subcat_id' AND category_id = '$cat_id'";
    $run_subcat = mysqli_query($con, $get_subcat);
    $subcat_data = mysqli_fetch_array($run_subcat);

    if ($subcat_data) {
        $subcat_title = $subcat_data['title'];
        $subcat_status = $subcat_data['status'];
    }
}

// Update Subcategory
if (isset($_POST['update_subcat'])) {
    $subcat_title = $_POST['subcat_title'];
    $subcat_status = $_POST['subcat_status'];
    
    // Update the subcategory in the database
    $update_subcat = "UPDATE ad_subcategories SET title = '$subcat_title', status = '$subcat_status' WHERE id = '$subcat_id' AND category_id = '$cat_id'";
    $run_update = mysqli_query($con, $update_subcat);

    if ($run_update) {
        echo "<script>alert('Subcategory updated successfully')</script>";
        echo "<script>window.open('index.php?edit_cat=$cat_id', '_self')</script>";
    } else {
        echo "<script>alert('Error updating subcategory')</script>";
    }
}
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
                <form method="POST" action="">
                    <div>
                        <label>Subcategory Title:</label>
                        <input type="text" name="subcat_title" value="<?php echo $subcat_title; ?>" required />
                    </div>
                    <div>
                        <label>Status:</label>
                        <select name="subcat_status" required>
                            <option value="live" <?php echo ($subcat_status == 'live') ? 'selected' : ''; ?>>Live
                            </option>
                            <option value="hold" <?php echo ($subcat_status == 'hold') ? 'selected' : ''; ?>>Hold
                            </option>
                        </select>
                    </div>
                    <button type="submit" name="update_subcat">Update Subcategory</button>
                </form>

            </div>

        </div>
    </div>
</div>






<?php } ?>