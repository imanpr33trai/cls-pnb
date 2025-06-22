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
                Dashboard / View Categories
            </li>
        </div>
    </div>
</div>
<!--breadcrump End-->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-money fa-fw"></i>
                    View Categories
                </h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Category Id</th>
                                <th>Category Title</th>
                                <th>Status</th>
                                <th>Delete Category</th>
                                <th>Edit Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
            $i=0;
            $get_cats="SELECT * FROM blog_categories";
            $run_cats=mysqli_query($con,$get_cats);

            while ($row_cats=mysqli_fetch_array($run_cats)) {
                $cat_blog_id = $row_cats['id'];
                $cat_blog_title = $row_cats['name'];
				$cat_blog_status = $row_cats['status'];
                $i++;
        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $cat_blog_title; ?></td>
                                <td>
                                    <?php if($cat_blog_status == 'live'): ?>
                                    <span class="badge badge-success">Live</span>
                                    <?php else: ?>
                                    <span class="badge badge-warning">Hold</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?delete_blog_cat=<?php echo $cat_blog_id; ?>">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </td>

                                <td>
                                    <a href="index.php?edit_blog_cat=<?php echo $cat_blog_id; ?>">
                                        <i class="fa fa-pen"></i> Edit
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