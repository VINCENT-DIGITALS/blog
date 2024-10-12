

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
            }else if (data.includes("Email already exists")) {
                showResponseMessage("Email already exists!", "error");
            }else if (data.includes("Not a valid Gmail addres")) {
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
            }else if (data.includes("Email already exists")) {
                showResponseMessage("Email already exists!", "error");
            }else if (data.includes("Not a valid Gmail addres")) {
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


