<?php

require_once __DIR__ . '/../vendor/autoload.php';

use League\OAuth2\Client\Provider\Github;

$provider = new Github([
    'clientId'          => GITHUB_CLIENT_ID,
    'clientSecret'      => GITHUB_CLIENT_SECRET,
    'redirectUri'       => GITHUB_REDIRECT_URL,
]);
$options = [
    'scope' => ['read:user', 'user:email']
];
$github_login_url = $provider->getAuthorizationUrl($options);
$_SESSION['oauth2state'] = $provider->getState();
