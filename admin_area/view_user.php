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
                Dashboard / View Users
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
                    View Users
                </h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>

                                <th>Id in DB </th>
                                <th>User First Name: </th>
                                <th>User Last Name: </th>
                                <th>User Email: </th>
                                <th>User Mobile: </th>

                                <th>Register Date </th>
                                <!-- <th>User Job: </th> -->

                                <th>User Delete: </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 

                           
                            $get_users="select * from users";
                            $run_users=mysqli_query($con, $get_users);

                            while ($row_users=mysqli_fetch_array($run_users)) {

                                $admin_id=$row_users['id'];
                                $admin_fname=$row_users['first_name'];
                                $admin_lname=$row_users['last_name'];
                                $admin_email=$row_users['email'];
                                $admin_phone=$row_users['phone'];
                                $admin_date=$row_users['created_at'];

                                // $admin_country=$row_users['admin_country'];

                                // $admin_job=$row_users['admin_job'];

                              
                                 ?>
                            <tr>

                                <td><?php echo $admin_id; ?></td>
                                <td><?php echo $admin_fname; ?></td>
                                <td><?php echo $admin_lname; ?></td>
                                <!-- <td><img src="admin_images/<?php echo $admin_image; ?>" width="60" height="60"></td> -->

                                <td><?php echo $admin_email; ?></td>
                                <td><?php echo $admin_phone ?></td>
                                <td><?php echo $admin_date ?></td>

                                <td>
                                    <a href="index.php?user_delete=<?php echo $admin_id; ?>"> <i
                                            class="fa fa-trash"></i> Delete </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php } ?>