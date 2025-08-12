<?php
include_once("../config/config.php");

if (isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);
    
    $stmt = $conn->prepare("SELECT id, title FROM ad_subcategories WHERE category_id = ? AND status = 'live'");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<option value="">Select Subcategory</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['title']) . '</option>';
        }
    } else {
        echo '<option value="">No subcategories found</option>';
    }
    $stmt->close();
}
