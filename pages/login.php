<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <form id="loginForm" method="post" action="db/request.php">
        <input type="text" id="email" name="email" placeholder="email" required><br>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="find_user">Login</button> <br>
        <a href="../index.php">Guest</a> <br>
        <a href="register.php">Sign up</a><br>
        <p class="msg" id="res"></p>
    </form>

    <div id="responseMessage"></div>

    <script src="/js/main.js"></script>
</body>

</html>