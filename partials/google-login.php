<?php
// /partials/google_login.php (Correct for Google API Client v2.0)

require_once __DIR__ . '/../vendor/autoload.php';

use Google\Client;


// Use the non-namespaced v2.0 class name
$google_client = new Client();

$google_client->setClientId(GOOGLE_CLIENT_ID);
$google_client->setClientSecret(GOOGLE_CLIENT_SECRET);
$google_client->setRedirectUri(GOOGLE_REDIRECT_URL);
$google_client->addScope('email');
$google_client->addScope('profile');

// Generate the final login URL
$google_login_url = $google_client->createAuthUrl();
?>