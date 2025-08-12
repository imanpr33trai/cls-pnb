<?php


// Include essential configuration files.
include_once(__DIR__ . '/../../config/config.php');

// Redirect logged-in users to the homepage.
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url);
    exit();
}

// Include Composer's autoloader for packages like PHPMailer.
include_once(__DIR__ . '/../../vendor/autoload.php');

// Bring PHPMailer classes into the global namespace.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//======================================================================
// 2. PROCESS FORM SUBMISSION (POST REQUEST)
// This block only runs when the form is submitted.
//======================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // --- Step 2a: Sanitize and Validate Input ---
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = empty(trim($_POST['phone'])) ? NULL : trim($_POST['phone']);
    $country = empty(trim($_POST['country'])) ? NULL : trim($_POST['country']);

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = 'All required fields must be filled.';
    }
    if (!$email) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    // --- Step 2b: If validation fails, redirect back with errors ---
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: ' . $base_url . 'register.php');
        exit();
    }

    // --- Step 2c: Check for Duplicate Email ---
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "An account with this email already exists. Please <a href='login.php'>log in</a>.";
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        $stmt->close(); // Close statement here
        header('Location: ' . $base_url . 'register.php');
        exit();
    }
    $stmt->close();

    // --- Step 2d: Create User, OTP, and Send Email ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $otp = rand(100000, 999999);
    $otp_expires_at = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, country, email, phone, password, auth_provider, status, verification_otp, otp_expires_at) VALUES (?, ?, ?, ?, ?, ?, 'local', 'unverified', ?, ?)");
    $stmt->bind_param("ssssssss", $first_name, $last_name, $country, $email, $phone, $hashed_password, $otp, $otp_expires_at);

    if ($stmt->execute()) {
        $stmt->close(); // Close statement here
        $mail = new PHPMailer(true);
        try {
            // SMTP settings from your config file
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure =$_ENV['SMTP_SECURE'];
            $mail->Port       = $_ENV['SMTP_PORT'];
            $mail->setFrom($_ENV['SMTP_USERNAME'], 'Punjab Classified');
$mail->addAddress($email, "{$first_name} {$last_name}");
$mail->isHTML(true);
$mail->Subject = 'Verify Your Account';

$mail->Body = "
    <div style='font-family: Arial, sans-serif; font-size: 15px; color: #333;'>
        <p>Hi <strong>{$first_name}</strong>,</p>
        
        <p>Thank you for signing up with <strong>Punjab Classified</strong>! To complete your registration, please use the verification code below:</p>
        
        <p style='font-size: 18px; font-weight: bold; color: #2b6cb0; background: #f1f5f9; padding: 10px; border-radius: 5px; text-align: center;'>
            {$otp}
        </p>
        
        <p>This code will expire in <strong>15 minutes</strong>. If you didn’t request this, you can safely ignore this email.</p>
        
        <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
        
        <p style='font-size: 13px; color: #777;'>
            Regards,<br>
            The Punjab Classified Team
        </p>
    </div>
";

            $mail->send();

            $_SESSION['verification_email'] = $email;
            header('Location: ' . $base_url . 'verify.php');
            exit();
        } catch (Exception $e) {
            $errors[] = "Your account was created, but the verification email could not be sent. Please contact support.";
            error_log("PHPMailer Error for user {$email}: " . $mail->ErrorInfo);
            $_SESSION['form_errors'] = $errors;
            header('Location: ' . $base_url . 'register.php');
            exit();
        }
    } else {
        $errors[] = "A critical error occurred with the database. Please try again later.";
        error_log("User registration INSERT failed for email {$email}: " . $stmt->error);
        $stmt->close(); // Close statement here
        $_SESSION['form_errors'] = $errors;
        header('Location: ' . $base_url . 'register.php');
        exit();
    }
}

//======================================================================
// 3. PREPARE VIEW (This runs on a normal page load)
//======================================================================

// Get any errors or old input from the session to display on the page
$errors = $_SESSION['form_errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];
// Clear them from the session so they don't appear again on refresh
unset($_SESSION['form_errors'], $_SESSION['old_input']);

// Helper function to safely repopulate form fields
function oldValue(string $field, array $data): string {
    return htmlspecialchars($data[$field] ?? '', ENT_QUOTES, 'UTF-8');
}

// Include header and social login generators
include_once(__DIR__ . '/../../partials/header.php');
include_once(__DIR__ . '/../../partials/google-login.php');
include_once(__DIR__ . '/../../partials/github_login.php');
?>

<!-- The HTML <section> element starts after this point -->

<!-- login page Start -->
<section class="account-main sm:py-14">
    <div class="container">
        <div class="">
            <div class="inner-section form-section d-flex flex-lg-row flex-column">
                <div class="col-lg-7">
                    <img src="<?php echo $base_url; ?>assets/images/login-2.jpg" alt="" class="" />
                </div>
                <div class="col-lg-5 items-center login-sec-2">
                    <h1 class="fos-7 text-center poppins-medium">Sign up now</h1>
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

                    <form action="" method="POST" class="text-start mt-10 xs:text-xs sm:text-sm" id="registerForm">
                        <div class="mb-6">
                            <label for="firstnameid" class="form-label">First name*</label>
                            <input type="text" name="first_name" class="form-control" id="firstnameid"
                                placeholder="Enter Your Name" required />
                        </div>
                        <div class="mb-6 ">
                            <label for="lastnameid" class="form-label">Last name*</label>
                            <input type="text" name="last_name" class="form-control" id="lastnameid"
                                placeholder="Enter Your Last Name" required />
                        </div>

                        <div class="mb-6">
                            <label for="emailid" class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" id="emailid"
                                placeholder="Enter Your Email" required />
                        </div>
                        <div class="mb-6">
                            <label for="phonenumbid" class="form-label">Phone Number (Optional)</label>
                            <input type="tel" name="phone" class="form-control" id="phonenumbid"
                                placeholder="Enter Your Phone Number" />
                            <!-- This is the new hidden field. It will store the country name. -->
                            <input type="hidden" name="country" id="country_name_hidden">

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
                        <div class="mb-6 d-flex gap-2">
                            <input type="checkbox" required id="accept-login" />
                            <label for="accept-login">By creating an account, I agree to our Terms of use and Privacy
                                Policy </label>
                        </div>
                        <div class="flex justify-center gap-2 mb-7">
                            <button type="submit"
                                class="w-100 theme-btn text-decoration-none text-center">Sign
                                Up</button>
                        </div>
                        <div class="mb-7 flex items-center gap-2">
                            <div class="divider"></div>
                            <h6 class="m-0">OR</h6>
                            <div class="divider"></div>
                        </div>
                        <div class="mb-7 flex gap-2 justify-center">
                            <a href="<?php echo htmlspecialchars($google_login_url); ?>"
                                class="w-100 theme-btn text-decoration-none text-center">Continue with
                                Google</a>
                        </div>
                        <div class="mb-7 flex flex-column justify-center items-center">
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

<!-- footer -->
<?php
include_once(__DIR__ . '/../../partials/footer.php');
?>
<!-- footer -->