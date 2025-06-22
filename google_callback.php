<?php
// /google-callback.php (For Google API Client v3+ with Namespaces)

// Include debug tools first
require_once __DIR__ . '/config/debug.php';

// The autoloader now knows where to find the namespaced classes
require_once __DIR__ . '/vendor/autoload.php';

// Import the classes at the top for cleaner code
use Google\Client;
use Google\Service\Oauth2;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
debug_to_session(null, "--- Google Callback Initiated ---");

try {
    require_once __DIR__ . '/config/config.php';
    debug_to_session('Config loaded.', 'Init');

    // Initialize the namespaced Google Client
    $google_client = new Client();
    $google_client->setClientId(GOOGLE_CLIENT_ID);
    $google_client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $google_client->setRedirectUri(GOOGLE_REDIRECT_URL);
    $google_client->addScope('email');
    $google_client->addScope('profile');
    debug_to_session('Google Client Initialized (Namespaced).', 'Setup');

    if (!isset($_GET['code'])) {
        throw new Exception("Authorization 'code' not found.");
    }
    debug_to_session($_GET['code'], "Auth Code Received");

    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
    debug_to_session($token, 'Token from Google');
    if (isset($token['error'])) {
        throw new Exception("Token Error: " . ($token['error_description'] ?? 'Unknown'));
    }

    $google_client->setAccessToken($token['access_token']);

    // Initialize the namespaced Oauth2 Service
    $google_service = new Oauth2($google_client);
    $data = $google_service->userinfo->get();
    debug_to_session($data, 'User Data from Google');

    // Your existing user processing logic...
    $google_id = $data->getId();
    $user_email = $data->getEmail();
    $user_first_name = $data->getGivenName();
    $user_last_name = $data->getFamilyName();

    $google_id = $data->getId();
    $user_email = $data->getEmail();
    $user_first_name = $data->getGivenName() ?? 'User'; // Fallback if name is empty
    $user_last_name = $data->getFamilyName() ?? '';

    $user = null; // This variable will hold the final user record

    // SCENARIO 1: Check if a user with this Google ID already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE google_id = ?");
    $stmt->bind_param("s", $google_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User is a returning Google user. Fetch their data.
        $user = $result->fetch_assoc();
        debug_to_session($user, 'Result: Found existing user by Google ID');
    } else {
        // SCENARIO 2: No user with this Google ID. Check if the email is already registered.
        $stmt_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt_email->bind_param("s", $user_email);
        $stmt_email->execute();
        $result_email = $stmt_email->get_result();

        if ($result_email->num_rows > 0) {
            // User exists as a 'local' user. Link their account by adding the Google ID.
            $user = $result_email->fetch_assoc();
            $update_stmt = $conn->prepare("UPDATE users SET google_id = ?, auth_provider = 'google' WHERE email = ?");
            $update_stmt->bind_param("ss", $google_id, $user_email);
            $update_stmt->execute();
            debug_to_session($user, 'Result: Linked local user to Google account');
        } else {
            // SCENARIO 3: This is a brand new user. Create their account.
            $insert_stmt = $conn->prepare(
                "INSERT INTO users (first_name, last_name, email, auth_provider, google_id) 
                 VALUES (?, ?, ?, 'google', ?)"
            );
            $insert_stmt->bind_param("ssss", $user_first_name, $user_last_name, $user_email, $google_id);
            $insert_stmt->execute();

            // Get the full record for the user we just created
            $new_user_id = $conn->insert_id;
            $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $user_stmt->bind_param("i", $new_user_id);
            $user_stmt->execute();
            $user = $user_stmt->get_result()->fetch_assoc();
            debug_to_session($user, 'Result: Created new user account');
        }
    }


    // ... The rest of your user database logic (find, link, create) is correct ...
    // ... and does not need to be changed. ...

    // (For brevity, I'm omitting the user DB logic here as it's unchanged)

    // Example of finding the user after the logic
    $stmt = $conn->prepare("SELECT * FROM users WHERE google_id = ? OR email = ?");
    $stmt->bind_param("ss", $google_id, $user_email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // --- Final Session Creation ---
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_email'] = $user['email'];
    debug_to_session($_SESSION, 'Final Session State');

    session_write_close();
    header('Location: ' . $base_url);
    exit();

} catch (Exception $e) {
    debug_to_session($e->getMessage(), '!!! SCRIPT FAILED !!!');
    session_write_close();
    header('Location: login.php?error=google_auth_failed');
    exit();
}