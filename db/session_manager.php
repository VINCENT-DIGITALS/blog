<?php

session_start();



// Function to store user data in session
function loginUserSession($userData, $role)
{
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['email'] = $userData['email'];
    $_SESSION['role'] = $role; // Store the user's role (admin/user)
    
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
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session completely
    header("Location: ../../index.php"); // Redirect to the homepage or a login page
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
