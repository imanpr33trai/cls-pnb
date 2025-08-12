<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

if (session_status() === PHP_SESSION_NONE) {
    
}


$provider = new Github([
    'clientId'          => GITHUB_CLIENT_ID,
    'clientSecret'      => GITHUB_CLIENT_SECRET,
    'redirectUri'       => GITHUB_REDIRECT_URL,
]);

try {
       if (empty($_GET['state']) || empty($_SESSION['oauth2state']) || $_GET['state'] !== $_SESSION['oauth2state']) {
        unset($_SESSION['oauth2state']);
        throw new Exception("Invalid state parameter, possible CSRF attack.");
    }
    unset($_SESSION['oauth2state']);


    if (empty($_GET['code'])) {
        throw new Exception("Authorization 'code' not found.");
    }

       $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

       $githubUser = $provider->getResourceOwner($token);
    $githubUserData = $githubUser->toArray();

    
             
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/user/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token->getToken(),
        'User-Agent: PNB-Classifieds-App'    ]);
    $emails_response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_status !== 200) {
        throw new Exception('Failed to fetch user emails from GitHub API. Response: ' . $emails_response);
    }
    
    $emails = json_decode($emails_response, true);


    $primary_email = '';
    foreach ($emails as $email_info) {
        if ($email_info['primary'] && $email_info['verified']) {
            $primary_email = $email_info['email'];
            break;
        }
    }
       if (empty($primary_email) && !empty($githubUserData['email'])) {
        $primary_email = $githubUserData['email'];
    }
    if (empty($primary_email)) {
        throw new Exception('Could not find a usable email for this GitHub account.');
    }


         
       $github_id = $githubUserData['id'];
    $name = $githubUserData['name'] ?? $githubUserData['login'];
    $name_parts = explode(' ', $name, 2);
    $first_name = $name_parts[0];
    $last_name = $name_parts[1] ?? '';

       $user = null;

    $stmt = $conn->prepare("SELECT * FROM users WHERE github_id = ?");
    $stmt->bind_param("s", $github_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

    } else {
        $stmt_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt_email->bind_param("s", $primary_email);
        $stmt_email->execute();
        $result_email = $stmt_email->get_result();

        if ($result_email->num_rows > 0) {
            $user = $result_email->fetch_assoc();
            $update_stmt = $conn->prepare("UPDATE users SET github_id = ?, auth_provider = 'github' WHERE email = ?");
            $update_stmt->bind_param("ss", $github_id, $primary_email);
            $update_stmt->execute();

        } else {
            $insert_stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, auth_provider, github_id) VALUES (?, ?, ?, 'github', ?)");
            $insert_stmt->bind_param("ssss", $first_name, $last_name, $primary_email, $github_id);
            $insert_stmt->execute();
            
            $new_user_id = $conn->insert_id;
            $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $user_stmt->bind_param("i", $new_user_id);
            $user_stmt->execute();
            $user = $user_stmt->get_result()->fetch_assoc();
            
        }
    }

       session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_email'] = $user['email'];
   

    session_write_close();
    header('Location: ' . $base_url);
    exit();

} catch (IdentityProviderException | Exception $e) {
 
    error_log("GitHub Auth Error: " . $e->getMessage());
    session_write_close();
    header('Location: login.php?error=github_auth_failed');
    exit();
}