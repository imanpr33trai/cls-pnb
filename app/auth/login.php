<?php
// FOR DEBUGGING - REMOVE IN PRODUCTION
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



include_once(__DIR__ . '/../../config/config.php');
include(__DIR__ . '/../../partials/github_login.php');
include(__DIR__ . '/../../partials/google-login.php');

include_once(__DIR__ . '/../../config/functions.php');



// USE $errors array to match your HTML block
$errors = [];

// If user is already logged in, redirect them away
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $accepted = isset($_POST['accept-login']);

    // --- Validate all inputs first ---
    if (empty($email)) {
        $errors[] = "Email address is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if (!$accepted) {
        $errors[] = "You must agree to the Terms of Use to log in.";
    }

    // --- If no basic errors, check the database ---
    if (empty($errors)) {
        // Use your working "SELECT *" logic
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Login is successful! Set session variables.
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_image'] = $user['image'];

                // Redirect to index.php
                header("Location: index.php");
                exit; // Crucial to stop the script here
            } else {
                // **Security Improvement**: Use a generic error
                $errors[] = "Invalid email or password.";
            }
        } else {
            // **Security Improvement**: Use the same generic error
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>

<?php include_once('partials/header.php'); ?>
<!-- login page Start -->
<!-- login page Start -->
<section class=" container">
    <div class="account-main">

        <div class="inner-section form-section d-flex flex-lg-row flex-column">
            <div class="col-lg-7">
                <img src="<?php echo $base_url; ?>assets/images/login-2.jpg" alt="" class="" />
            </div>
            <div class="col-lg-5 text-center login-sec-2">
                <h1 class="fos-8 poppins-medium">Sign In</h1>
                <h6 class="fos-16">Welcome back enter your details below.</h6>




                <!-- THE NEW, CORRECTED BLOCK GOES HERE -->
                <?php
                if (!empty($errors)) {
                    echo '<div class="alert alert-danger">';
                    foreach ($errors as $err) {
                        echo '<p class="mb-0">' . htmlspecialchars($err) . '</p>';
                    }
                    echo '</div>';
                }
                ?>
                <!-- END OF CORRECTED BLOCK -->





                <form action="login.php" method="POST" class="text-start xs:text-xs sm:text-sm mt-8">

                    <div class="mb-6">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control"
                            placeholder="Enter Your Registered Email" required />
                    </div>

                    <div class="mb-6">
                        <div class="password-options d-flex justify-content-between">
                            <label class="form-label">Password</label>
                            <div class="hide">
                                <a href="#" class="text-decoration-none d-flex gap-2 align-items-center">
                                    <img src="<?php echo $base_url; ?>assets/images/hide.svg" alt="" />
                                    <h6 class="fos-18 m-0">Hide</h6>
                                </a>
                            </div>
                        </div>
                        <input type="password" name="password" id="inputPassword6" class="form-control"
                            placeholder="Enter Your Password" required />
                    </div>

                    <div class="mb-6 text-end">
                        <a class="poppins-medium color-pink">Forget Password</a>
                    </div>

                    <div class="mb-6 d-flex gap-2">
                        <input type="checkbox" name="accept-login" id="accept-login" />
                        <label for="accept-login">By logging in, I agree to the Terms of Use and Privacy
                            Policy</label>
                    </div>

                    <div class="d-flex gap-2 mb-8 justify-center">
                        <button type="submit" class="w-100 theme-btn text-center">Log in</button>
                    </div>

                    <div class="mb-8 d-flex align-items-center gap-2">
                        <div class="divider"></div>
                        <h6 class="m-0">OR</h6>
                        <div class="divider"></div>
                    </div>

                    <div class="mb-8 flex gap-2 justify-center">
                        <a href="<?php echo htmlspecialchars($google_login_url); ?>"
                            class="w-100 theme-btn text-decoration-none text-center">Continue with
                            Google</a>
                    </div>

                    <div class="mb-8 flex flex-column justify-center align-items-center">
                        <h6 class="poppins-medium">Sign in With</h6>
                        <a href="<?php echo htmlspecialchars($github_login_url); ?>">
                            <img src="<?php echo $base_url; ?>assets/images/sign-in-with.svg" alt="" />
                        </a>
                    </div>

                    <div class="text-center">
                        <h1 class="fos-16">Don’t have an account?
                            <a href="./register.php"
                                class="color-pink text-decoration-none">Signup</a>
                        </h1>
                    </div>
                </form>
            </div>


        </div>
    </div>

</section>

<!-- login page End -->
<!-- login page End -->

<!-- footer -->
<!-- footer -->
<?php
include_once('partials/footer.php');
?>
<!-- footer -->
<!-- footer -->