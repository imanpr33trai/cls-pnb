<?php  
if (!isset($_SESSION['admin_email'])){

  echo "<script>window.open('login.php','_self')</script>";
  }else{

?>

<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrump">
            <li class="active">
                <i class="fa fa-bar-chart"> </i>
                Dashboard / Insert User
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-money fa-fw"></i>
                    Insert User
                </h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> User First Name: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_fname" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> User Last Name: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_lname" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> User Email: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_email" class="form-control" required="">
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> User Password: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_pass" class="form-control" required="">
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> User Contact: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_contact" class="form-control" required="">
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                            <input type="submit" name="submit" value="Insert User" class="btn btn-primary form-control">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
if (isset($_POST['submit'])) {
	$admin_fname=$_POST['admin_fname'];
	$admin_lname=$_POST['admin_lname'];
	$admin_email=$_POST['admin_email'];
	$admin_pass=$_POST['admin_pass'];
	$admin_contact=$_POST['admin_contact'];

	move_uploaded_file($temp_admin_image,"admin_images/$admin_image");

	$insert_admin="INSERT INTO users (first_name,last_name,email,phone,password) VALUES ('$admin_fname','$admin_lname','$admin_email','$admin_contact','$admin_pass')";
	$run_admin=mysqli_query($con,$insert_admin);
	if ($run_admin) {
		echo "<script>alert('One New User has been inserted')</script>";
		echo "<script>window.open('index.php?view_user','_self')</script>";
	}
}


 ?>

<?php } ?>