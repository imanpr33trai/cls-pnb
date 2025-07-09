<?php
include_once("config/config.php");

if (isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);
    $query = "SELECT * FROM ad_subcategories WHERE category_id = $category_id AND status = 'live'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo '<option value="">Select Subcategory</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';

        }
    } else {
        echo '<option value="">No subcategories found</option>';
    }
}
?>