<?php
require_once '../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://yourdomain.com/callback.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // Get user info
    $google_service = new Google_Service_Oauth2($client);
    $userInfo = $google_service->userinfo->get();

    $email = $userInfo->email;
    $name = $userInfo->name;
    $id = $userInfo->id;

    // Store user info in the database
    require 'database.php';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE google_id = ?');
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        // Insert new user
        $stmt = $pdo->prepare('INSERT INTO users (google_id, name, email) VALUES (?, ?, ?)');
        $stmt->execute([$id, $name, $email]);
    }

    $_SESSION['user_id'] = $id;

    header('Location: dashboard.php');
    exit();
}
?>
