$("#loginForm").on("submit", function (e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way
    var datas = $(this).serializeArray();
    var data_array = {};
    $.map(datas, function (data) {
        data_array[data['name']] = data['value'];
    });

    $.ajax({
        url: '../db/request.php',
        type: 'POST',
        data: {
            'find_user': true,
            ...data_array,
        },
        success: function (data) {
            if (data.includes("Login Success")) {
                $("<div class='success-message'>Logged in successfully!</div>")
                    .appendTo('body')
                    .fadeIn(300)
                    .delay(2000)
                    .fadeOut(500);
                // Optionally redirect or perform other actions
            } else {
                $("#responseMessage").text(data).fadeIn(300).delay(2000).fadeOut(500);
            }
        },
        error: function (error) {
            $("#responseMessage").text("An error occurred!").fadeIn(300).delay(2000).fadeOut(500);
        }
    });
});

// $(document).ready(function() {
//     $("#loginForm").submit(function(event) {
//         event.preventDefault(); // Prevent the form from submitting the traditional way
//         // Get form data
//         var email = $("#email").val();
//         var password = $("#password").val();
//         console.log("Email: ", email); // Log email value
//         console.log("Password: ", password); // Log password value
//         $.ajax({
//             url: '../db/request.php',
//             type: 'POST',
//             data: {
//                 'find_user': true,
//                 'email': email, // Send email field
//                 'password': password // Send password field     
//             },
//             success: function(data) {

//                 $("#res").html(data);
//                 window.location.href = "index.php";
//             },
//             error: function(error) {
//                 $("#responseMessage").text("An error occurred during the login process.");
//             }
//         });
//     });
// });
$("#adminloginForm").on("submit", function (e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way
    var datas = $(this).serializeArray();
    var data_array = {};
    $.map(datas, function (data) {
        data_array[data['name']] = data['value'];
    });

    $.ajax({
        url: '../db/request.php',
        type: 'POST',
        data: {
            'find_user': true,
            ...data_array,
        },
        success: function (data) {
            if (data.includes("Login Success")) {
                $("<div class='success-message'>Logged in successfully!</div>")
                    .appendTo('body')
                    .fadeIn(300)
                    .delay(2000)
                    .fadeOut(500);
                // Optionally redirect or perform other actions
            } else {
                $("#responseMessage").text(data).fadeIn(300).delay(2000).fadeOut(500);
            }
        },
        error: function (error) {
            $("#responseMessage").text("An error occurred!").fadeIn(300).delay(2000).fadeOut(500);
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
        url: "db/request.php",
        method: "POST",
        data: {
            'add_student': true,
            ...data_array,
        },
        success: function (result) {
            $("<div class='success-message'>Student data added successfully!</div>").appendTo('body').fadeIn(300).delay(2000).fadeOut(500);
            window.location.href = "../index.php";
        },
        error: function (error) {
            alert("Something went wrong!");
        }
    });
    // Reset search after updating the table
    resetSearch();
});


