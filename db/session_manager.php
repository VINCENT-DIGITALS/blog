<?php

session_start();

// Check if visitor_id is not set in the session, and initialize it if needed
if (!isset($_SESSION['visitor_id'])) {
    $_SESSION['visitor_id'] = uniqid('visitor_', true); // Generate a unique visitor ID
}
// Function to store user data in session
function loginUserSession($userData, $role)
{
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['email'] = $userData['email'];
    $_SESSION['role'] = $role; // Store the user's role (admin/user)
    unset($_SESSION['visitor_id']); // Clear visitor ID when the user logs in
}

// Function to check if the user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Function to check if the logged-in user is an admin
function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to destroy session (logout)
function logoutUser()
{
    // Start the session if it hasn't been started already
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Unset all session variables
    $_SESSION = []; // More explicit than session_unset()

    // Destroy the session data on the server
    session_destroy();

    // Remove the session cookie (if any exists)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Redirect to the homepage or a login page
    header("Location: ../../index.php");
    exit();
}



// Function to restrict access to admin-only pages
function requireAdmin()
{
    if (!isAdmin()) {
        header('Location: ../../pages/auth/choose_login.php'); // Redirect non-admins to login page
        exit();
    }
}

// Function to restrict access to logged-in users
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ../../pages/auth/choose_login.php'); // Redirect if not logged in
        exit();
    }
}
