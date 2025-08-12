<?php
require_once __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Oauth2;

if (session_status() === PHP_SESSION_NONE) {
}


try {
    require_once __DIR__ . '/config/config.php';
    

       $google_client = new Client();
    $google_client->setClientId(GOOGLE_CLIENT_ID);
    $google_client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $google_client->setRedirectUri(GOOGLE_REDIRECT_URL);
    $google_client->addScope('email');
    $google_client->addScope('profile');
    
    if (!isset($_GET['code'])) {
        throw new Exception("Authorization 'code' not found.");
    }

    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        throw new Exception("Token Error: " . ($token['error_description'] ?? 'Unknown'));
    }

    $google_client->setAccessToken($token['access_token']);

       $google_service = new Oauth2($google_client);
    $data = $google_service->userinfo->get();

       $google_id = $data->getId();
    $user_email = $data->getEmail();
    $user_first_name = $data->getGivenName();
    $user_last_name = $data->getFamilyName();

    $google_id = $data->getId();
    $user_email = $data->getEmail();
    $user_first_name = $data->getGivenName() ?? 'User';    $user_last_name = $data->getFamilyName() ?? '';

    $user = null;
       $stmt = $conn->prepare("SELECT * FROM users WHERE google_id = ?");
    $stmt->bind_param("s", $google_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
               $user = $result->fetch_assoc();
    } else {
               $stmt_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt_email->bind_param("s", $user_email);
        $stmt_email->execute();
        $result_email = $stmt_email->get_result();

        if ($result_email->num_rows > 0) {
                       $user = $result_email->fetch_assoc();
            $update_stmt = $conn->prepare("UPDATE users SET google_id = ?, auth_provider = 'google' WHERE email = ?");
            $update_stmt->bind_param("ss", $google_id, $user_email);
            $update_stmt->execute();
        } else {
                       $insert_stmt = $conn->prepare(
                "INSERT INTO users (first_name, last_name, email, auth_provider, google_id) 
                 VALUES (?, ?, ?, 'google', ?)"
            );
            $insert_stmt->bind_param("ssss", $user_first_name, $user_last_name, $user_email, $google_id);
            $insert_stmt->execute();

                       $new_user_id = $conn->insert_id;
            $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $user_stmt->bind_param("i", $new_user_id);
            $user_stmt->execute();
            $user = $user_stmt->get_result()->fetch_assoc();
        }
    }


      
   
       $stmt = $conn->prepare("SELECT * FROM users WHERE google_id = ? OR email = ?");
    $stmt->bind_param("ss", $google_id, $user_email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

       session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_email'] = $user['email'];

    session_write_close();
    header('Location: ' . $base_url);
    exit();
} catch (Exception $e) {
    session_write_close();
    header('Location: login.php?error=google_auth_failed');
    exit();
}
