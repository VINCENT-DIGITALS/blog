


$("#loginForm").on("submit", function (e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way
    var datas = $(this).serializeArray();
    var data_array = {};
    $.map(datas, function (data) {
        data_array[data['name']] = data['value'];
    });

    $.ajax({
        url: '../../db/request.php',
        type: 'POST',
        data: {
            'find_user': true,
            ...data_array,
        },
        success: function (data) {
            if (data.includes("Login Success")) {
                // Pass the redirect URL to the success message
                showResponseMessage("Login Success!", "success", "../../index.php");

            } else if (data.includes("Invalid email or password")) {
                showResponseMessage("Invalid email or password!", "error");
            }
        },
        error: function (error) {
            text("An error occurred!").fadeIn(300).delay(2000).fadeOut(500);
        }
    });
});

document.getElementById('customFacebookLogin').addEventListener('click', function () {
    FB.login(function (response) {
        if (response.status === 'connected') {
            // User logged in, handle the response
            checkLoginState();
        } else {
            console.log('User did not log in');
        }
    }, { scope: 'public_profile,email' }); // Request the necessary permissions
});


// Load the Facebook SDK asynchronously
window.fbAsyncInit = function () {
    FB.init({
        appId: '3880290302185411', // Replace with your Facebook App ID
        cookie: true,          // Enable cookies to allow server-side processing
        xfbml: true,          // Parse social plugins like the login button
        version: 'v16.0'        // Use the latest API version
    });

    // Check login status when the page loads
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
};

(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) { return; }
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Handle login status response
function statusChangeCallback(response) {
    console.log('statusChangeCallback', response);

    if (response.status === 'connected') {
        // Fetch user info and send to the backend
        fetchUserInfo(response.authResponse);
    } else if (response.status === 'not_authorized') {
        document.getElementById('status').innerHTML = '<p>Please authorize the app in Facebook.</p>';
    } else {
        document.getElementById('status').innerHTML = '<p>Please log in to Facebook.</p>';
    }
}

// Triggered by the Facebook login button
function checkLoginState() {
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
}

// Fetch user info from Facebook and send it to the backend
function fetchUserInfo(authResponse) {
    FB.api('/me', { fields: 'id,name,email' }, function (userResponse) {
        console.log('User info:', userResponse);

        // Prepare serialized data
        var datas = [
            { name: 'facebook_login', value: true },
            { name: 'id', value: userResponse.id },
            { name: 'username', value: userResponse.name },
            { name: 'email', value: userResponse.email }
        ];

        var data_array = {};
        $.map(datas, function (data) {
            data_array[data['name']] = data['value'];
        });

        // AJAX request
        $.ajax({
            url: '../../db/request.php',
            type: 'POST',
            data: {
                'facebook_login': true, // Custom key to identify the action
                ...data_array,
            },
            success: function (data) {
                if (data.includes("Login Success")) {
                    // Pass the redirect URL to the success message
                    showResponseMessage("Login Success!", "success", "../../index.php");

                } else if (data.includes("Something Went Wrong")) {
                    showResponseMessage("Something Went Wrong", "error");
                } else if (data.includes("New Login Success")) {
                    showResponseMessage("New account Created!, Login Success!", "success", "../../index.php");
                }
                else {
                    showResponseMessage("Unexpected response!", "error");
                }
            },
            error: function (error) {
                showResponseMessage("An error occurred!", "error");
            }
        });
    });
}



// Google Login
$("#googleLogin").on("click", function () {
    window.location.href = "/Blog/services/google-login-callback.php"; // Redirect to Google login handler
});


$("#adminloginForm").on("submit", function (e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way
    var datas = $(this).serializeArray();
    var data_array = {};
    $.map(datas, function (data) {
        data_array[data['name']] = data['value'];
    });

    $.ajax({
        url: '../../db/request.php',
        type: 'POST',
        data: {
            'find_admin': true,
            ...data_array,
        },
        success: function (data) {
            if (data.includes("Login Success")) {
                // Pass the redirect URL to the success message
                showResponseMessage("Login Success!", "success", "../../index.php");

            } else if (data.includes("Invalid email or password")) {
                showResponseMessage("Invalid email or password!", "error");
            }
        },
        error: function (error) {
            text("An error occurred!").fadeIn(300).delay(2000).fadeOut(500);
        }
    });
});


// Add student using AJAX
$("#register_form").on("submit", function (e) {
    e.preventDefault();
    var datas = $(this).serializeArray();
    var data_array = {};
    $.map(datas, function (data) {
        data_array[data['name']] = data['value'];
    });

    $.ajax({
        url: "../../db/request.php",
        method: "POST",
        data: {
            'add_user': true,
            ...data_array,
        },
        success: function (data) {
            if (data.includes("Registration Successful")) {
                // Pass the redirect URL to the success message
                showResponseMessage("Registration Successful!", "success", "../../index.php");

            } else if (data.includes("Registration Failed")) {
                showResponseMessage("Registration Failed!", "error");
            }
            else if (data.includes("Passwords do not match")) {
                showResponseMessage("Passwords do not match!", "error");
            } else if (data.includes("Email already exists")) {
                showResponseMessage("Email already exists!", "error");
            } else if (data.includes("Not a valid Gmail addres")) {
                showResponseMessage("Please use a valid Gmail addres", "error");
            }

        },
        error: function (error) {
            alert("Something went wrong!");
        }
    });

});

// Add student using AJAX
$("#admin_register_form").on("submit", function (e) {
    e.preventDefault();
    var datas = $(this).serializeArray();
    var data_array = {};
    $.map(datas, function (data) {
        data_array[data['name']] = data['value'];
    });

    $.ajax({
        url: "../../db/request.php",
        method: "POST",
        data: {
            'add_admin': true,
            ...data_array,
        },
        success: function (data) {
            if (data.includes("Registration Successful")) {
                // Pass the redirect URL to the success message
                showResponseMessage("Registration Successful!", "success", "../pages/auth/admin_login.php");

            } else if (data.includes("Registration Failed")) {
                showResponseMessage("Registration Failed!", "error");
            }
            else if (data.includes("Passwords do not match")) {
                showResponseMessage("Passwords do not match!", "error");
            } else if (data.includes("Email already exists")) {
                showResponseMessage("Email already exists!", "error");
            } else if (data.includes("Not a valid Gmail addres")) {
                showResponseMessage("Please use a valid Gmail addres", "error");
            }

        },
        error: function (error) {
            alert("Something went wrong!");
        }
    });

});

function showResponseMessage(message, messageType, redirectUrl = null) {
    // Show the response message container
    $("#responseMessage").show();

    // Create a new message div with the specified message and message type
    var messageClass = messageType === 'success' ? 'success-message' : 'error-message';
    $("<div class='" + messageClass + "'>" + message + "</div>")
        .appendTo('#responseMessage')
        .fadeIn(300)
        .delay(2000)
        .fadeOut(500, function () {
            // Clear the message container after fade-out
            $("#responseMessage").html("");

            // Check if redirectUrl is provided
            if (redirectUrl) {
                // Append a loading spinner before redirect
                $("#responseMessage").html("<div class='loading'>Loading...</div>");

                // Optionally delay before redirect
                setTimeout(function () {
                    window.location.href = redirectUrl;
                }, 1000); // Adjust delay as needed
            }
        });
}


