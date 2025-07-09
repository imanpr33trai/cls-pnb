<?php  
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
?>

<?php
if (isset($_GET['delete_cat'])) {
    $delete_id = $_GET['delete_cat'];
    $delete_cat = "DELETE FROM ad_categories WHERE id = '$delete_id'";
    $run_delete = mysqli_query($con, $delete_cat);

    if ($run_delete) {
        echo "<script>alert('Your Category Has Been Deleted Successfully');</script>";
        echo "<script>window.open('index.php?view_categories','_self');</script>";
    } else {
        echo "<script>alert('Failed to delete the category');</script>";
    }
}
?>

<?php } ?>