<?php  
if (!isset($_SESSION['admin_id'])){

  echo "<script>window.open('login.php','_self')</script>";
  }else{

?>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Punjab Classified</h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-bar-chart"></i> Details </li>
        </ol>
    </div>


    <div class="row1">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row2">
                        <div class="col-xs-3">
                            <i class="fa fa-tasks fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"> <?php echo $count_pro ?> </div>
                            <div>All Ads</div>
                        </div>
                    </div>
                </div>
                <a href="index.php?view_product">
                    <div class="panel-footer">
                        <span class="pull-left"> View Details </span>
                        <span class="pull-right"> <i class="fa fa-arrow-circle-right"> </i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row2">
                        <div class="col-xs-3">
                            <i class="fa fa-comments fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"> <?php echo $count_cust ?> </div>
                            <div>All Users </div>
                        </div>
                    </div>
                </div>
                <a href="index.php?view_customer">
                    <div class="panel-footer">
                        <span class="pull-left"> View Details </span>
                        <span class="pull-right"> <i class="fa fa-arrow-circle-right"></i> </span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    <div class="row2">
                        <div class="col-xs-3"> <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"> <?php echo $count_p_cat ?> </div>
                            <div>Ads All Categories</div>
                        </div>
                    </div>
                </div>
                <a href="index.php?view_product_cat">

                    <div class="panel-footer">
                        <span class="pull-left"> View Detials </span>
                        <span class="pull-right"> <i class="fa fa-arrow-circle-right"></i> </span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>


    </div>
    <div class="row1">


        <div class="col-lg-3 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading">

                    <div class="row2">
                        <div class="col-xs-3"><i class="fa fa-support fa-5x"></i>

                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"> <?php echo $count_order ?> </div>
                            <div> Ads All Subcategories </div>
                        </div>
                    </div>
                </div>
                <a href="index.php?view_order">
                    <div class="panel-footer">
                        <span class="pull-left"> View Detials </span>
                        <span class="pull-right"> <i class="fa fa-arrow-circle-right"> </i> </span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row2">
                        <div class="col-xs-3">
                            <i class="fa fa-comments fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"> <?php echo $count_blogs ?> </div>
                            <div>All articles </div>
                        </div>
                    </div>
                </div>
                <a href="index.php?view_customer">
                    <div class="panel-footer">
                        <span class="pull-left"> View Details </span>
                        <span class="pull-right"> <i class="fa fa-arrow-circle-right"></i> </span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading">

                    <div class="row2">
                        <div class="col-xs-3"><i class="fa fa-support fa-5x"></i>

                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"> <?php echo $count_blog_cat ?> </div>
                            <div> Blog Categories </div>
                        </div>
                    </div>
                </div>
                <a href="index.php?view_order">
                    <div class="panel-footer">
                        <span class="pull-left"> View Detials </span>
                        <span class="pull-right"> <i class="fa fa-arrow-circle-right"> </i> </span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

    </div>


    <div class="row1" style="width:100%;">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-money fa-fw"></i> New ads
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ad Title</th>
                                    <th>User Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Location</th>
                                    <th>Posted Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                $i = 0;
                $get_ads = "SELECT * FROM ad_form ORDER BY id DESC LIMIT 0,5";
                $run_ads = mysqli_query($con, $get_ads);
                
                while ($row = mysqli_fetch_assoc($run_ads)) {
                    $i++;
                    $title = $row['ad_title'];
                    $name = $row['user_name'];
                    $phone = $row['phone'];
                    $email = $row['email'];
                    $location = $row['city_town_neighbourhood'];
                    $date = $row['created_at']; // or whatever column stores the post date
                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $title; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo $phone; ?></td>
                                    <td><?php echo $email; ?></td>
                                    <td><?php echo $location; ?></td>
                                    <td><?php echo $date; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <a href="index.php?view_ads">View all ads
                            <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row1">
        <div class="col-lg-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-money fa-fw"></i> New Articles
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ad Title</th>
                                    <th>User Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Location</th>
                                    <th>Posted Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                $i = 0;
                $get_ads = "SELECT * FROM blog_posts ORDER BY id DESC LIMIT 0,5";
                $run_ads = mysqli_query($con, $get_ads);
                
                while ($row = mysqli_fetch_assoc($run_ads)) {
                    $i++;
                    $title = $row['title'];
                    $name = $row['author_name'];
                    $phone = $row['phone'];
                    $email = $row['email'];
                    $location = $row['category_id'];
                    $date = $row['created_at']; // or whatever column stores the post date
                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $title; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo $phone; ?></td>
                                    <td><?php echo $email; ?></td>
                                    <td><?php echo $location; ?></td>
                                    <td><?php echo $date; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <a href="index.php?view_ads">View all ads
                            <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel">
                <div class="panel-body">
                    <div class="thumb-info mb-md">
                        <img src="admin_images/<?php echo $admin_image ?>" class="rounded img-responsive" width="250"
                            height="">
                        <div class="thumb-info-title">
                            <span class="thumb-info-inner"><?php echo $admin_name; ?></span><br>
                        </div>
                    </div>
                    <div class="mb-md">
                        <div class="widget-content-expanded">
                            <i class="fa fa-user"></i> <span>Email : </span> <?php echo $admin_email ?> <br>
                            <i class="fa fa-user"></i> <span>Contact : </span> <?php echo $admin_phone?> <br>
                        </div>

                        <hr class="dotted short">
                        <h5 class="text-muted">About</h5>
                        <p><?php echo $admin_about ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php } ?>