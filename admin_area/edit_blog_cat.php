<?php  
if (!isset($_SESSION['admin_email'])){

  echo "<script>window.open('login.php','_self')</script>";
  }else{


?>
<?php
if (isset($_GET['edit_blog_cat'])) {
    $edit_blog_id = $_GET['edit_blog_cat'];
    $get_blog_cat = "SELECT * FROM blog_categories WHERE id = '$edit_blog_id'";
    $run_blog_cat = mysqli_query($con, $get_blog_cat);
    $row_blog_cat = mysqli_fetch_array($run_blog_cat);

    $c_blog_id = $row_blog_cat['id'];
    $c_blog_title = $row_blog_cat['name'];
    // $c_blog_image = $row_blog_cat['image'];
    $c_blog_status = $row_blog_cat['status'];
}

if (isset($_POST['update'])) {
    $cat_blog_title = $_POST['blog_cat_titles'];
    $cat_blog_status = $_POST['blog_cat_statuss'];

   

    $update_blog_cat = "UPDATE blog_categories 
                   SET name='$cat_blog_title', status='$cat_blog_status' 
                   WHERE id='$c_blog_id'";
    $run_blog_cat = mysqli_query($con, $update_blog_cat);

    if ($run_blog_cat) {
        echo "<script>alert('Blog Category has been updated successfully');</script>";
        echo "<script>window.open('index.php?view_blog_cat','_self');</script>";
    } else {
        echo "<script>alert('Category update failed');</script>";
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
                    <i class="fa fa-money fa-fw"> Insert Category for blog </i>
                </h3>
            </div>
            <!--panel-heading End-->
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Title</label>
                        <div class="col-md-6">
                            <input type="text" name="blog_cat_titles" class="form-control" required
                                value="<?php echo $c_blog_title; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Status</label>
                        <div class="col-md-6">
                            <select name="blog_cat_statuss" class="form-control" required>
                                <option value="live" <?php if($c_blog_status == 'live') echo 'selected'; ?>>Live
                                </option>
                                <option value="hold" <?php if($c_blog_status == 'hold') echo 'selected'; ?>>Hold
                                </option>
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






<?php } ?>