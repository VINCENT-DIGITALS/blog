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
        <button type="submit" name="find_user">Login</button>
        <a href="index.php">Guest</a>
        <p class="msg" id="res"></p>
    </form>

    <div id="responseMessage"></div>

    <script>
        $(document).ready(function() {
            $("#loginForm").submit(function(event) {
                event.preventDefault(); // Prevent the form from submitting the traditional way
                // Get form data
                var email = $("#email").val();
                var password = $("#password").val();
                console.log("Email: ", email); // Log email value
                console.log("Password: ", password); // Log password value
                $.ajax({
                    url: 'db/request.php',
                    type: 'POST',
                    data: {
                        'find_user': true,
                        'email': email, // Send email field
                        'password': password // Send password field     
                    },
                    success: function(data) {

                       $("#res").html(data);
                    //    window.location.href = "index.php";
                    },
                    error: function(error) {
                        $("#responseMessage").text("An error occurred during the login process.");
                    }
                });
            });
        });
    </script>

</body>

</html>