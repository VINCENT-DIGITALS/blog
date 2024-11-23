<?php
// Get the JSON payload
$data = json_decode(file_get_contents("php://input"), true);

// Access user data
$userId = $data['id'];
$userName = $data['name'];
$userEmail = $data['email'];

// Example: Store user data in the database
$mysqli = new mysqli("localhost", "username", "password", "database");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("INSERT INTO users (provider_id, name, email) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?");
$stmt->bind_param("sssss", $userId, $userName, $userEmail, $userName, $userEmail);
$stmt->execute();

echo json_encode(["status" => "success"]);
?>
