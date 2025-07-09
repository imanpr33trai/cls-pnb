<?php
include 'config/config.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get form values
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']); // Getting the number from the visible input

    // --- NEW: Get the country name from the hidden input ---
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';

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


    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // --- NEW: Update the database INSERT command ---
        // We add the `country` column to the query
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, country, email, phone, password) VALUES (?, ?, ?, ?, ?, ?)");

        // Add the $country variable and update the types to "ssssss" (6 strings)
        $stmt->bind_param("ssssss", $first_name, $last_name, $country, $email, $phone, $hashed_password);

        if ($stmt->execute()) {
            $success = "Account created successfully! You can now log in.";
            // header("Location: login.php"); // Redirect to login page
        } else {
            $errors[] = "An error occurred during registration. Please try again.";
        }

        $stmt->close();
    }
}
?>
<?php
include_once('partials/header.php');
include('partials/google-login.php'); // Include Google login logic
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
                        </div>s
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