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
                    <i class="fa fa-money fa-fw"> Insert Category for blog </i>
                </h3>
            </div>
            <!--panel-heading End-->
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Title</label>
                        <div class="col-md-6">
                            <input type="text" name="blog_cat_title" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Category Status</label>
                        <div class="col-md-6">
                            <select name="blog_cat_status" class="form-control" required>
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

<?php
if (isset($_POST['submit'])) {
    $cat_title = $_POST['blog_cat_title'];
    $cat_status = $_POST['blog_cat_status'];


    $insert_cat = "INSERT INTO blog_categories (name, status, created_at) 
                   VALUES ('$cat_title', '$cat_status', NOW())";

    $run_cat = mysqli_query($con, $insert_cat);

    if ($run_cat) {
        echo "<script>alert('New Blog  Category has been Inserted successfully');</script>";
        echo "<script>window.open('index.php?view_blog_cat','_self');</script>";
    }
}
?>





<?php } ?>