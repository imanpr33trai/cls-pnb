<?php

require_once __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['admins_id'])) {
       header("Location: /admin/dashboard"); 
    exit();
}
$errors = [];if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required.";
    }

    if (empty($errors)) {
               $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($admin = $result->fetch_assoc()) {
                       if (password_verify($password, $admin['password'])) {
                               session_regenerate_id(true);                $_SESSION['admins_id'] = $admin['id'];
                $_SESSION['admins_username'] = $admin['username'];
                
                               header("Location: " . $base_url . 'admin');
                exit();
            } else {
                               $errors[] = "Invalid username or password.";
            }
        } else {
                       $errors[] = "The Email or username is not exist.";
        }
        $stmt->close();
    }
    
          $_SESSION['login_error'] = implode('<br>', $errors);

       header("Location: /admin/login");
    exit();
}$login_error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>assets/css/output.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-lg">
        
        
        
        
        <form method="POST" action="" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Admin Login</h2>

            <?php if ($login_error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $login_error; ?></span>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username or Email
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" name="username" placeholder="Username or Email" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" name="password" placeholder="******************" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Sign In
                </button>
            </div>
        </form>
        <p class="text-center text-gray-500 text-xs">
            &copy;2025 Punjab Classified Corp. All rights reserved.
        </p>
    </div>
</body>
</html>