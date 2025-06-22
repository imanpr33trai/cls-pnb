<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        font-family: Arial;
        margin: 0;
        display: flex;
    }

    .sidebar {
        width: 200px;
        background: #333;
        color: white;
        height: 100vh;
        padding: 20px;
    }

    .sidebar h3 {
        margin-bottom: 20px;
    }

    .sidebar ul {
        list-style: none;
        padding-left: 0;
    }

    .sidebar ul li {
        margin-bottom: 10px;
    }

    .sidebar ul li a {
        color: white;
        text-decoration: none;
        display: block;
    }

    .sidebar ul li a.active {
        font-weight: bold;
        background: #444;
        padding: 5px;
        border-radius: 5px;
    }

    #content-area {
        padding: 20px;
        flex-grow: 1;
    }
    </style>
</head>

<body>
    <aside class="sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="#" data-page="dashboard" class="tab-link active">Dashboard</a></li>
            <li><a href="#" data-page="category" class="tab-link">Category</a></li>
            <li><a href="#" data-page="sub-cat" class="tab-link">Sub-category</a></li>
            <li><a href="#" data-page="blog-cat" class="tab-link">Blog-category</a></li>
            <li><a href="#" data-page="about" class="tab-link">About</a></li>
        </ul>
    </aside>
    <main id="content-area">
        <!-- Dynamic content will be loaded here -->
        <?php
include_once('../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    $image_name = '';

    if (!empty($_FILES['category_image']['name'])) {
        $upload_dir = '../assets/uploads/';
        $image_name = time() . '_' . basename($_FILES['category_image']['name']);
        $target_path = $upload_dir . $image_name;
        move_uploaded_file($_FILES['category_image']['tmp_name'], $target_path);
    }

    $stmt = $conn->prepare("INSERT INTO ad_categories (name, image) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $image_name);
    $stmt->execute();

    echo "✅ Category added!";
}
?>
        <?php
include_once('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subcategory'])) {
    $category_id = $_POST['category_id'];
    $title = trim($_POST['subcategory_title']);

    $stmt = $conn->prepare("INSERT INTO ad_subcategories (category_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $category_id, $title);
    $stmt->execute();

    echo "✅ Subcategory added!";
}
?>
<?php
include_once('../../config/config.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category_1'])) {
    $category_namess = trim($_POST['category_name_blog']);

    if (!empty($category_namess)) {
        $stmt = $conn->prepare("INSERT INTO blog_categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_namess);
        if ($stmt->execute()) {
            $success = "Category added successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Please enter a category name.";
    }
}
?>
        <!-- <a href="javascript:history.back()">Go back</a> -->
    </main>

    <script>
    const links = document.querySelectorAll('.tab-link');
    const contentArea = document.getElementById('content-area');

    function loadPage(page) {
        fetch(`pages/${page}.php`)
            .then(res => res.text())
            .then(data => {
                contentArea.innerHTML = data;

                // Dynamically attach event handlers based on the loaded page
                if (page === 'dashboard') {
                    attachDashboardHandlers(); // Call this to bind handlers for the modal
                }
            });
    }



    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            links.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            const page = this.getAttribute('data-page');
            loadPage(page);
        });
    });

    // Initial page load
    loadPage('dashboard');
    </script>



</body>

</html>