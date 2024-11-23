<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <style>
        /* Reset basic styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #4CAF50, #2F8F50);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 400px;
            background-color: white;
            border-radius: 15px;
            padding: 20px 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4CAF50;
        }

        form {
            text-align: center;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-bottom: 15px;
        }

        button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        button:active {
            background-color: #3e8e41;
            transform: translateY(0);
        }

        a {
            display: inline-block;
            margin-top: 10px;
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #45a049;
            text-decoration: underline;
        }

        .social-login {
            margin-top: 20px;
            text-align: center;
        }

        .social-login button {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .facebook-login-button {
            background-color: #4267B2;
            color: white;
            border: none;
        }

        .facebook-login-button:hover {
            background-color: #365899;
        }

        .google-login-button {
            background-color: #DB4437;
            color: white;
            border: none;
        }

        .google-login-button:hover {
            background-color: #C23321;
        }

        #responseMessage {
            margin-top: 20px;
            font-size: 16px;
            color: #4CAF50;
        }

        .hidden {
            display: none;
        }
    </style>

    <script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/en_US/sdk.js"></script>

    <script src="https://accounts.google.com/gsi/client" async defer></script>

</head>

<body>
    <div class="container">
        <h2>User Login</h2>
        <form id="loginForm" method="post" action="/Blog/db/request.php">
            <input type="text" id="email" name="email" placeholder="Email" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <div id="responseMessage" style="display: none;"></div>
            <div id="status"></div>
            <button type="submit" name="find_user">Login</button><br>
            <a href="/Blog/pages/auth/logout.php">Guest |</a>
            <a href="/Blog/pages/auth/register.php">Sign up</a>
        </form>

        <div class="social-login">
            <!-- Custom Facebook Login Button -->
            <button id="customFacebookLogin" class="facebook-login-button">
                Login with Facebook
            </button>
           
            <!-- Sign In With Google button with HTML data attributes API -->
            <div id="g_id_onload"
                data-client_id="624367051637-26ok23b3casi34oeo728ee1jltjb04bo.apps.googleusercontent.com"
                data-context="signin"
                data-ux_mode="popup"
                data-callback="handleCredentialResponse"
                data-auto_prompt="false">
            </div>

            <div class="g_id_signin"
                data-type="standard"
                data-shape="rectangular"
                data-theme="outline"
                data-text="signin_with"
                data-size="large"
                data-logo_alignment="left">
            </div>
            <!-- User Profile Display -->

            <div class="pro-data hidden"></div>
        </div>
    </div>

    <script src="/Blog/js/main.js"></script>
</body>

</html>