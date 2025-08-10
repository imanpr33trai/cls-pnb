<?php
// /admin/login.php (Combined and Corrected)

// --- 1. CONFIGURATION AND SESSION START ---
// The path must go up one level to find the config folder.
require_once __DIR__ . '/../config/config.php';

// Start session if not already started. This must be at the very top.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If admin is already logged in, redirect to the dashboard
if (isset($_SESSION['admins_id'])) {
    // Redirect to the canonical admin URL, which the router will handle.
    header("Location: /admin"); 
    exit();
}

// --- 2. FORM PROCESSING LOGIC ---
$errors = [];
// This block will ONLY run when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required.";
    }

    if (empty($errors)) {
        // Prepare a secure statement to find the admin by username OR email
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($admin = $result->fetch_assoc()) {
            // User found, now verify the password
            if (password_verify($password, $admin['password'])) {
                // --- LOGIN SUCCESSFUL ---
                session_regenerate_id(true); // Security: prevent session fixation
                $_SESSION['admins_id'] = $admin['id'];
                $_SESSION['admins_username'] = $admin['username'];
                
                // Redirect to the admin dashboard
                header("Location: " . $base_url . 'admin');
                exit();
            } else {
                // Password incorrect
                $errors[] = "Invalid username or password.";
            }
        } else {
            // No user found with that username/email
            $errors[] = "The Email or username is not exist.";
        }
        $stmt->close();
    }
    
    // If we are here, login failed. Store the error to display it.
    // The session variable is more reliable across redirects if needed, but for a single file it's fine.
    $_SESSION['login_error'] = implode('<br>', $errors);

    // Redirect back to the login page itself to prevent form resubmission
    header("Location: /admin/login");
    exit();
}

// --- 3. DISPLAY LOGIC (This runs on a normal page load) ---
// Get any errors from the session (set by the POST block above)
$login_error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']); // Clear the error so it doesn't show again
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <!-- The base_url from config.php ensures the path to CSS is always correct -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>assets/css/output.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-lg">
        
        <!-- ========================================================== -->
        <!-- THE FIX: The form action is now empty, so it submits to itself. -->
        <!-- ========================================================== -->
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