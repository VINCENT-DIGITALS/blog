<?php
ini_set('display_errors', 0);  // Prevent errors from displaying in the output
ini_set('log_errors', 1);      // Enable logging errors
error_reporting(E_ALL);        // Report all PHP errors
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json");

require "db.php";
$mydb = new myDB();
include 'session_manager.php'; // Include session management



// header('Content-Type: application/json'); // Always respond with JSON

if (isset($_POST['facebook_login'])) {
    $userId = $_POST['id'] ?? null;
    $userName = $_POST['username'] ?? null;
    $userEmail = $_POST['email'] ?? null;

    if (!$userId || !$userName || !$userEmail) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    // Example user login/registration logic
    $existingUser = $mydb->loginUser('users', '*', ['provider_id' => $userId]);

    if ($existingUser) {
        loginUserSession($existingUser, 'user'); // Store as 'user'
        echo "Login Success";
    } else {
        $newUser = [
            'provider' => 'facebook',
            'provider_id' => $userId,
            'username' => $userName,
            'email' => $userEmail,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $isAdded = $mydb->add_user('users', $newUser);
        // loginUserSession($newUser, 'user'); // Store as 'user'
        if ($isAdded) {
            // Example user login/registration logic
            $existingUser = $mydb->loginUser('users', '*', ['provider_id' => $userId]);

            if ($existingUser) {
                loginUserSession($existingUser, 'user'); // Store as 'user'
                echo "Login Success";
            }
            echo "New Login Success";
        } else {
            echo "Something Went Wrong";
        }
    }
}


if (isset($_POST['google_login'])) {
    $credential = $_POST['credential'] ?? null;

    if (!$credential) {
        echo json_encode(["status" => "error", "message" => "Missing credential"]);
        exit;
    }

    // Verify the credential token with Google's API
    $google_api_url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($credential);
    $response = file_get_contents($google_api_url);
    $userInfo = json_decode($response, true);

    if (isset($userInfo['sub'])) {
        // Extract user data
        $userId = $userInfo['sub'];
        $userName = $userInfo['name'] ?? "Unknown";
        $userEmail = $userInfo['email'] ?? "Unknown";
        $userPicture = $userInfo['picture'] ?? "";

        // Example user login/registration logic
        $existingUser = $mydb->loginUser('users', '*', ['provider_id' => $userId]);

        if ($existingUser) {
            // Log in existing user
            loginUserSession($existingUser, 'user'); // Store as 'user'
            echo "Login Success";
        } else {
            // Register new user
            $newUser = [
                'provider' => 'google',
                'provider_id' => $userId,
                'username' => $userName,
                'email' => $userEmail,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $isAdded = $mydb->add_user('users', $newUser);
            // loginUserSession($newUser, 'user'); // Store as 'user'

            if ($isAdded) {
                // Example user login/registration logic
                $existingUser = $mydb->loginUser('users', '*', ['provider_id' => $userId]);

                if ($existingUser) {
                    // Log in existing user
                    loginUserSession($existingUser, 'user'); // Store as 'user'
                    echo "Login Success";
                }
                echo "New Login Success";
            } else {
                echo "Something Went Wrong";
            }
        }
    } else {
        // Invalid token
        echo json_encode(["status" => "error", "message" => "Invalid token"]);
    }
}




// if (isset($_POST['google_login'])) {
//     $id = $_POST['id'];
//     $username = $_POST['username'];
//     $email = $_POST['email'];
//     if (!$userId || !$userName || !$userEmail) {
//         echo json_encode(["status" => "error", "message" => "Missing required fields"]);
//         exit;
//     }
//     // Example user login/registration logic
//     $existingUser = $mydb->loginUser('users', '*', ['provider_id' => $userId]);

//     if ($existingUser) {
//         loginUserSession($existingUser, 'user'); // Store as 'user'
//         echo "Login Success";
//     } else {
//         $newUser = [
//             'provider' => 'google',
//             'provider_id' => $userId,
//             'username' => $userName,
//             'email' => $userEmail,
//             'created_at' => date('Y-m-d H:i:s')
//         ];

//         $isAdded = $mydb->add_user('users', $newUser);
//         loginUserSession($newUser, 'user'); // Store as 'user'
//         if ($isAdded) {
//             echo "New Login Success";
//         } else {
//             echo "Something Went Wrong";
//         }
//     }
// } 

function isValidGmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) && strpos($email, '@gmail.com') !== false;
}
if (isset($_POST['update_post'])) {
    unset($_POST['update_post']); // Remove the action identifier from data

    $id = $_POST['id']; // Get the ID
    unset($_POST['id']); // Remove 'id' from data as it will be used in the WHERE condition

    $data = $_POST; // Remaining fields are the data to be updated
    $where = ['id' => $id]; // Condition for which row to update

    $mydb->update('posts', $data, $where);
    header("location: ../");
}


if (isset($_POST['like_post'])) {
    $postId = $_POST['post_id'];
    $userId = $_POST['user_id'] ?? null;
    $visitorId = $_POST['visitor_id'] ?? null;

    // Check if the user is authenticated or is a visitor with a valid session
    if ($userId || $visitorId) {
        // Ensure at least one identifier (userId or visitorId) is not null or empty before proceeding
        if ($userId === null && $visitorId === null) {
            echo json_encode(['success' => false, 'error' => 'You must be logged in or have a valid session to like posts.']);
            exit;
        }

        // Fetch the like record based on whether the user is authenticated or a visitor
        if ($userId) {
            $mydb->select('likes', '*', ['post_id' => $postId, 'user_id' => $userId]);
        } else if ($visitorId) {
            $mydb->select('likes', '*', ['post_id' => $postId, 'visitor_id' => $visitorId]);
        }

        $likeExists = $mydb->res->num_rows > 0;  // Check if like exists

        if ($likeExists) {
            // Unlike the post (delete the existing like entry)
            if ($userId && !$visitorId) {
                $mydb->delete('likes', ['post_id' => $postId, 'user_id' => $userId]);
            } else if ($visitorId && !$userId) {
                $mydb->delete('likes', ['post_id' => $postId, 'visitor_id' => $visitorId]);
            }
            $mydb->updateLikes($postId, 'likes_count - 1');
        } else {
            // Like the post (insert a new like entry)
            $data = ['post_id' => $postId];
            if ($userId) {
                $data['user_id'] = $userId;
            } else if ($visitorId) {
                $data['visitor_id'] = $visitorId;
            }
            $mydb->insert('likes', $data);

            $mydb->updateLikes($postId, 'likes_count + 1');  // Increase the like count
        }

        // Fetch the updated likes count
        $mydb->select('posts', 'likes_count', ['id' => $postId]);
        $row = $mydb->res->fetch_assoc();
        $newLikes = isset($row['likes_count']) ? (int)$row['likes_count'] : 0;

        echo json_encode(['success' => true, 'new_likes' => $newLikes]);
    } else {
        echo json_encode(['success' => false, 'error' => 'You must be logged in or have a valid session to like posts.']);
    }
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
    $result = $mydb->insert('posts', [...$_POST]);

    if ($result) {
        echo "Success";
    } else {
        echo "Error";
    }

    //    header("location: ../");
}

if (isset($_POST['get_post'])) {
    try {
        $search = isset($_POST['search']) ? trim($_POST['search']) : '';
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $url_post_id = isset($_POST['url_post_id']) ? intval(trim($_POST['url_post_id'])) : null; // Ensure it's an integer

        // Prepare filter conditions
        $filters = [];
        if (!empty($category)) {
            $filters['category'] = $category;
        }

        // If post ID is present, fetch only the post with this ID
        if (!empty($url_post_id)) {
            $filters['id'] = $url_post_id;  // Prioritize the post ID
            $mydb->select('posts', '*', $filters);  // Fetch the post with specific ID
        } else {
            // If no post ID is provided, proceed with search and category filters
            if (!empty($search)) {
                // Handle both filters and search terms
                $mydb->select('posts', '*', $filters, ['title' => $search, 'content' => $search]);
            } else {
                // Handle only category filter
                $mydb->select('posts', '*', $filters);
            }
        }

        $datas = [];
        while ($row = $mydb->res->fetch_assoc()) {
            $datas[] = $row;
        }

        // Log and return the result
        error_log(print_r($datas, true));  // Log to check the result
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


if (isset($_POST['delete_post'])) {
    $where = ['id' => $_POST['id']];
    $mydb->delete('posts', $where);
    header("location: ../");
}
