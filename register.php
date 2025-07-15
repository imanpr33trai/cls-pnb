<?php
if (session_status() === PHP_SESSION_NONE) {
    
}

// Redirect logged-in users to the homepage
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/vendor/autoload.php'; 
require_once __DIR__ . '/config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Dotenv\Dotenv;


$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get form values
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone']); // Getting the number from the visible input
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';

        // Check for duplicate email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "An account with this email already exists.";
        }
        $stmt->close();
    }

    // --- NEW: Get the country name from the hidden input ---

    // Basic validations
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $errors[] = 'All required fields must be filled.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }




    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

         // Generate OTP and expiration
        $otp = rand(100000, 999999);
        $otp_expires_at = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');
        
        // --- NEW: Update the database INSERT command ---
        // We add the `country` column to the query
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, country, email, phone, password,auth_provider, status, verification_otp, otp_expires_at) VALUES (?, ?, ?, ?, ?, ?, 'local', 'unverified', ?, ?)");

        // Add the $country variable and update the types to "ssssss" (6 strings)
        $stmt->bind_param("ssssssss", $first_name, $last_name, $country, $email, $phone, $hashed_password, $otp, $otp_expires_at);

        if ($stmt->execute()) {
            $success = "Account created successfully! You can now log in.";
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = $_ENV['SMTP_HOST'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['SMTP_USERNAME'];
                $mail->Password   = $_ENV['SMTP_PASSWORD'];
                $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
                $mail->Port       = $_ENV['SMTP_PORT'];

                // Recipients
                $mail->setFrom($_ENV['SMTP_USERNAME'], 'Manpreet Singh');
                $mail->addAddress($email, "{$first_name} {$last_name}");

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Account - PNB Classifieds';
                $mail->Body    = "Hello {$first_name},<br><br>Thank you for registering. Your verification code is: <b>{$otp}</b><br><br>This code will expire in 10 minutes.";
                $mail->AltBody = "Your verification code is: {$otp}";
                $mail->send();

                // Store email in session and redirect to the verification page
                $_SESSION['verification_email'] = $email;
                header('Location: verify.php');
                exit();
                
            } catch (Exception $e) {
                $errors[] = "Registration was successful, but the verification email could not be sent. Please contact support. Mailer Error: {$mail->ErrorInfo}";
            }
            // header("Location: login.php"); // Redirect to login page
        }else {
            $errors[] = "Database error: Could not register the account.";
            error_log("User registration failed: " . $stmt->error);
        }

        $stmt->close();
    }
}
?>
<?php
include_once('partials/header.php');
include('partials/google-login.php'); // Include Google login logic
include'partials/github_login.php'; // Include GitHub login logic
?>


<!-- login page Start -->
<!-- login page Start -->
<section class="account-main">
    <div class="container">
        <div class="row">
            <div class="inner-section form-section d-flex flex-lg-row flex-column">
                <div class="col-lg-7">
                    <img src="<?php echo $base_url; ?>assets/images/login-2.jpg" alt="" class="w-100" />
                </div>
                <div class="col-lg-5 login-sec-2">
                    <h1 class="fos-32 poppins-medium">Sign up now</h1>
                    <!-- <h6 class="fos-16">Welcome to Punjab Classified enter your details below.</h6> -->

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $err)
                                echo "<p>$err</p>"; ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="text-start mt-40" id="registerForm">
                        <div class="row mb-24">
                            <div class="col-lg-6">
                                <label for="firstnameid" class="form-label">First name*</label>
                                <input type="text" name="first_name" class="form-control" id="firstnameid"
                                    placeholder="Enter Your Name" required />
                            </div>
                            <div class="col-lg-6">
                                <label for="lastnameid" class="form-label">Last name*</label>
                                <input type="text" name="last_name" class="form-control" id="lastnameid"
                                    placeholder="Enter Your Last Name" required />
                            </div>
                        </div>
                        <div class="mb-24">
                            <label for="emailid" class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" id="emailid"
                                placeholder="Enter Your Email" required />
                        </div>
                        <div class="mb-24">
                            <label for="phonenumbid" class="form-label">Phone Number (Optional)</label>
                            <input type="tel" name="phone" class="form-control" id="phonenumbid"
                                placeholder="Enter Your Phone Number" />
                            <!-- This is the new hidden field. It will store the country name. -->
                            <input type="hidden" name="country" id="country_name_hidden">

                        </div>
                        <div class="mb-24">
                            <div class="password-options d-flex justify-content-between">
                                <label class="form-label">Password</label>
                                <div class="hide">
                                    <a href="#" class="text-decoration-none d-flex gap-2 align-items-center">
                                        <img src="<?php echo $base_url; ?>assets/images/hide.svg" alt="" />
                                        <h6 class="fos-18 m-0">Hide</h6>
                                    </a>
                                </div>
                            </div>
                            <input type="password" name="password" id="inputPassword5" class="form-control"
                                placeholder="Enter Your Password" required />
                            <div class="password-strength"></div>
                            <div class="form-text">Use 8 or more characters with a mix of letters, numbers & symbols
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                        <div class="mb-24 d-flex gap-2">
                            <input type="checkbox" required id="accept-login" />
                            <label for="accept-login">By creating an account, I agree to our Terms of use and Privacy
                                Policy </label>
                        </div>
                        <div class="d-flex gap-2 mb-32">
                            <button type="submit"
                                class="w-100 theme-btn text-decoration-none text-center btn btn-primary">Sign
                                Up</button>
                        </div>
                        <div class="mb-32 d-flex align-items-center gap-2">
                            <div class="divider"></div>
                            <h6 class="m-0">OR</h6>
                            <div class="divider"></div>
                        </div>
                        <div class="mb-32 d-flex gap-2">
                            <a href="<?php echo htmlspecialchars($google_login_url); ?>"
                                class="w-100 theme-btn text-decoration-none text-center">Continue with
                                Google</a>
                        </div>
                        <div class="mb-32 d-flex flex-column justify-content-center align-items-center">
                            <h6 class="poppins-medium">Sign in With</h6>
                            <a href="<?php echo htmlspecialchars($github_login_url); ?>">
                                <img src="<?php echo $base_url; ?>assets/images/sign-in-with.svg" alt="" />
                            </a>
                        </div>
                        <div class="text-center">
                            <h1 class="fos-16">Already have an ccount?
                                <a href="<?php echo $base_url; ?>login.php" class="color-pink text-decoration-none">Log
                                    in</a>
                            </h1>
                        </div>
                    </form>
                </div>
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
