<?php
session_start();
include("includes/db.php"); 

if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
    exit();
} else {
    if (isset($_GET['delete_subcat']) && isset($_GET['cat_id'])) {
        $subcat_id = $_GET['delete_subcat'];
        $cat_id = $_GET['cat_id'];

        // Delete subcategory
        $delete_subcat = "DELETE FROM ad_subcategories WHERE id = '$subcat_id'";
        $run_delete = mysqli_query($con, $delete_subcat);

        if ($run_delete) {
            echo "<script>alert('Subcategory deleted successfully');</script>";
            echo "<script>window.open('index.php?edit_cat=$cat_id','_self');</script>";
        } else {
            echo "<script>alert('Error deleting subcategory');</script>";
        }
    }
}
?>