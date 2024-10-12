<?php
ini_set('display_errors', 0);  // Prevent errors from displaying in the output
ini_set('log_errors', 1);      // Enable logging errors
error_reporting(E_ALL);        // Report all PHP errors

include 'session.php';
require "db.php";
$mydb = new myDB();
include 'session_manager.php'; // Include session management


// Function to validate if email is a Gmail account
function isValidGmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) && strpos($email, '@gmail.com') !== false;
}


if (isset($_POST['find_user'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $mydb->loginUser('users', '*', ['email' => $email]);

    // Check if the query returned a result
    if ($result && password_verify($password, $result['password'])) {
        loginUserSession($result, 'user'); // Store as 'user'
        echo "Login Success";
    } else {
        echo "Invalid email or password";
    }
}

if (isset($_POST['find_admin'])) { //Login
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $mydb->loginUser('admin', '*', ['email' => $email]);

    // Check if the query returned a result
    if ($result && password_verify($password, $result['password'])) {
        loginUserSession($result, 'admin'); // Store as 'admin'
        echo "Login Success";
    } else {
        echo "Invalid email or password";
    }
    exit();
}

if (isset($_POST['add_user'])) {
    // Store passwords for comparison
    $password = $_POST['password'];
    $confirmPassword = $_POST['Cpassword'];
    $email = $_POST['email'];  // Assuming 'email' is passed in the POST request

    // Check if email already exists
    if ($mydb->email_exists('users', $email)) {
        echo "Email already exists";  // Send error message
        exit();  // Stop further execution
    } elseif (!isValidGmail($email)) {
        echo "Not a valid Gmail address";
        exit();
    }
    // Check if password and confirm password match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match";  // Send error message if passwords don't match
        exit();  // Stop further execution

    } elseif ($password == $confirmPassword) {
        unset($_POST['add_user']);  // Remove 'add_user' from the POST array
        unset($_POST['Cpassword']); // Explicitly remove 'Cpassword' from the POST array
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Replace the plain password with the hashed password in the POST array
        $_POST['password'] = $hashedPassword;
        // Insert user data into the database
        $success =  $mydb->add_user('users', $_POST);
        // Check if the registration was successful
        if ($success) {
            echo "Registration Successful";  // Send success message to the front-end
        } else {
            echo "Registration Failed";  // Send failure message
        }
        exit(); // Stop further script execution
    }
}

if (isset($_POST['add_admin'])) {
    // Store passwords for comparison
    $password = $_POST['password'];
    $confirmPassword = $_POST['Cpassword'];
    $email = $_POST['email'];  // Assuming 'email' is passed in the POST request

    // Check if email already exists
    if ($mydb->email_exists('admin', $email)) {
        echo "Email already exists";  // Send error message
        exit();  // Stop further execution
    } elseif (!isValidGmail($email)) {
        echo "Not a valid Gmail address";
        exit();
    }
    // Check if password and confirm password match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match";  // Send error message if passwords don't match
        exit();  // Stop further execution

    } elseif ($password == $confirmPassword) {
        unset($_POST['add_admin']);  // Remove 'add_user' from the POST array
        unset($_POST['Cpassword']); // Explicitly remove 'Cpassword' from the POST array
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Replace the plain password with the hashed password in the POST array
        $_POST['password'] = $hashedPassword;
        // Insert user data into the database
        $success =  $mydb->add_user('admin', $_POST);
        // Check if the registration was successful
        if ($success) {
            echo "Registration Successful";  // Send success message to the front-end
        } else {
            echo "Registration Failed";  // Send failure message
        }
        exit(); // Stop further script execution
    }
}


if (isset($_POST['add_post'])) {
    unset($_POST['add_post']);
    $mydb->insert('posts', [...$_POST]);
    header("location: ../");
}

if (isset($_POST['get_post'])) {
    try {
        $search = isset($_POST['search']) ? trim($_POST['search']) : '';

        // Use search term if provided, otherwise get all posts
        if (!empty($search)) {
            $mydb->select('posts', '*', NULL, ['title' => $search, 'content' => $search]);
        } else {
            $mydb->select('posts');
        }

        $datas = [];
        while ($row = $mydb->res->fetch_assoc()) {
            $datas[] = $row;
        }
        echo json_encode($datas);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}

if (isset($_POST['submit_comment'])) {
    try {
        $mydb->add_comment('comments', $_POST);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}

if (isset($_POST['load_comments'])) {
    $postId = $_POST['post_id'];
    $comments = $mydb->fetchComments('comments', $postId);

    if (isset($comments['error'])) {
        echo json_encode(["error" => $comments['error']]);
    } else {
        echo json_encode($comments);
    }
}




if (isset($_POST['update_post'])) {
    unset($_POST['update_student']); // Remove the action identifier from data

    $id = $_POST['id']; // Get the ID
    unset($_POST['id']); // Remove 'id' from data as it will be used in the WHERE condition

    $data = $_POST; // Remaining fields are the data to be updated
    $where = ['id' => $id]; // Condition for which row to update

    $mydb->update('posts', $data, $where);
    header("location: ../");
}

if (isset($_POST['delete_post'])) {
    $where = ['id' => $_POST['id']];
    $mydb->delete('posts', $where);
    header("location: ../");
}
