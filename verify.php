<?php
// /verify.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is not in the verification process, redirect them away.
if (!isset($_SESSION['verification_email'])) {
    header('Location: register.php');
    exit();
}

require_once __DIR__ . '/config/config.php';

$errors = [];
$email = $_SESSION['verification_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if (empty($otp) || !is_numeric($otp) || strlen($otp) !== 6) {
        $errors[] = "Please enter a valid 6-digit OTP.";
    } else {
        // Find the user by their email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'unverified'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            $errors[] = "Invalid account or already verified. Please try logging in.";
        } elseif ($user['verification_otp'] !== $otp) {
            $errors[] = "The OTP you entered is incorrect.";
        } elseif (new DateTime() > new DateTime($user['otp_expires_at'])) {
            $errors[] = "The OTP has expired. Please request a new one by re-entering your email on the registration page.";
        } else {
            // SUCCESS! Activate the account
            $update_stmt = $conn->prepare("UPDATE users SET status = 'active', verification_otp = NULL, otp_expires_at = NULL WHERE id = ?");
            $update_stmt->bind_param("i", $user['id']);
            
            if ($update_stmt->execute()) {
                // Verification successful, log the user in
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                
                unset($_SESSION['verification_email']); // Clean up session
                
                header('Location: ' . $base_url); // Redirect to homepage
                exit();
            } else {
                $errors[] = "A database error occurred. Please try again.";
                error_log("Failed to update user status: " . $update_stmt->error);
            }
        }
    }
}

include 'partials/header.php';
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title text-center">Verify Your Account</h3>
                    <p class="text-center text-muted">An email has been sent to <strong><?= htmlspecialchars($email) ?></strong>. Please enter the 6-digit code below.</p>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error) echo "<p class='mb-1'>$error</p>"; ?>
                        </div>
                    <?php endif; ?>

                    <form action="verify.php" method="POST">
                        <div class="mb-3">
                            <label for="otp" class="form-label">Verification Code (OTP)</label>
                            <input type="text" class="form-control text-center" id="otp" name="otp" required maxlength="6" pattern="\d{6}" style="font-size: 1.5rem; letter-spacing: 0.5rem;">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Verify and Log In</button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a href="register.php">Wrong email? Register again.</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>