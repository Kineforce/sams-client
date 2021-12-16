<?php

require '../../vendor/autoload.php';

use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Auth\OAuth2;

session_start();

$redirectURI = "http://localhost:8000/src/controllers/auth.php";
$scopes = "https://www.googleapis.com/auth/photoslibrary.readonly";

$clientSecretJson = json_decode(
    file_get_contents('../../credentials.json'),
    true
)['web'];
$clientId = $clientSecretJson['client_id'];
$clientSecret = $clientSecretJson['client_secret'];

$oauth2 = new OAuth2([
    'clientId' => $clientId,
    'clientSecret' => $clientSecret,
    'authorizationUri' => 'https://accounts.google.com/o/oauth2/auth',
    'redirectUri' => $redirectURI,
    'tokenCredentialUri' => 'https://oauth2.googleapis.com/token',
    'scope' => $scopes,
]);


if (!isset($_GET['code'])) {
    $authenticationUrl = $oauth2->buildFullAuthorizationUri(['access_type' => 'offline']);
    header("Location: " . $authenticationUrl);
} else {
    $oauth2->setCode($_GET['code']);
    $authToken = $oauth2->fetchAuthToken();
    $refreshToken = $authToken['access_token'];

    $_SESSION['credentials'] = new UserRefreshCredentials(
        $scopes,
        [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken
        ]
    );

    header("Location: http://localhost:8000/src/pages/home.php");
}