<?php
require_once '../../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/Blog/pages/auth/google-callback.php'); // Change to your callback URL
$client->addScope('email');
$client->addScope('profile');

header('Location: ' . $client->createAuthUrl());
exit();
