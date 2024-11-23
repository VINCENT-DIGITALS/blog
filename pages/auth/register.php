<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Reset some basic styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
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

        .guest{
            display: inline-block;
            margin-top: 10px;
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }
        .footer{
            display: inline-block;
            margin-top: 10px;
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }
        .footer a{
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #45a049;
            text-decoration: underline;
        }


        .msg {
            color: red;
            font-size: 14px;
        }

        #responseMessage {
            margin-top: 20px;
            font-size: 16px;
            color: #4CAF50;
        }
    </style>
</head>

<body>
    <form id="register_form" method="post" action="/Blog/db/request.php">
        <h2>User Sign Up</h2>
        <input type="text" id="Username" name="Username" placeholder="Username" required><br>
        <input type="text" id="email" name="email" placeholder="Email" required><br>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <input type="password" id="password" name="Cpassword" placeholder="Confirm Password" required><br>
        <div id="responseMessage" style="display: none;"></div>
        <button type="submit" name="add_user">Login</button><br>
        <div>
    <button id="facebookLogin" type="button">Sign up with Facebook</button><br>
    <button id="googleLogin" type="button">Sign up with Google</button><br>
</div>

        <a class="guest" href="/Blog/pages/auth/logout.php">Guest</a><br>
        <div class="footer">Already have an account? <a  href="/Blog/pages/auth/login.php">Login here</a></div>
    </form>



    <script src="/Blog/js/main.js"></script>
</body>

</html>