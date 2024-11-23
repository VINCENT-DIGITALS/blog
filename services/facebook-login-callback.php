<?php
require_once __DIR__ . '/vendor/autoload.php';

use Facebook\Facebook;

session_start();

$fb = new Facebook([
    'app_id' => 'YOUR_FACEBOOK_APP_ID',
    'app_secret' => 'YOUR_FACEBOOK_APP_SECRET',
    'default_graph_version' => 'v12.0',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
    if (!isset($accessToken)) {
        throw new Exception('No access token received.');
    }

    $response = $fb->get('/me?fields=id,name,email', $accessToken);
    $user = $response->getGraphUser();

    // Save user to database
    $conn = new mysqli('localhost', 'root', '', 'your_database');
    $stmt = $conn->prepare("INSERT INTO users (name, email, provider, provider_id) VALUES (?, ?, 'facebook', ?) ON DUPLICATE KEY UPDATE name=?, email=?");
    $stmt->bind_param('sssss', $user['name'], $user['email'], $user['id'], $user['name'], $user['email']);
    $stmt->execute();

    header("Location: /Blog/index.php?message=Facebook login successful");
} catch (Exception $e) {
    header("Location: /Blog/pages/auth/login.php?error=" . urlencode($e->getMessage()));
}
