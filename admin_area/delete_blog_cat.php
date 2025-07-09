<?php  

if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
?>

<?php
if (isset($_GET['delete_blog_cat'])) {
    $delete_blog_id = $_GET['delete_blog_cat'];
    $delete_blog_cat = "DELETE FROM blog_categories WHERE id = '$delete_blog_id'";
    $run_blog_delete = mysqli_query($con, $delete_blog_cat);

    if ($run_blog_delete) {
        echo "<script>alert('Your Category Has Been Deleted Successfully');</script>";
        echo "<script>window.open('index.php?view_blog_cat','_self');</script>";
    } else {
        echo "<script>alert('Failed to delete the category');</script>";
    }
}
?>

<?php } ?>