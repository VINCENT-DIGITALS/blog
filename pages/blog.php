<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/Blog/css/index_style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> <!-- Add jQuery -->
</head>
<body>

    <?php include '../includes/nav_bar.php'; 
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $isLoggedIn = true; // User is logged in if 'user_id' is set
        $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'admin' : null; // Check if user is admin
        $visitor_id = null;
    } else {
        $user_id = null;
        $isLoggedIn = false; // Not logged in if 'user_id' is not set
        $isAdmin = null; // Not an admin if not logged in
        $visitor_id = random_int(1000000, 9999999999); // Generates a random integer in the specified range

    }

    ?>
    <script>
        var userId = <?php echo json_encode($user_id); ?>; // Pass PHP user_id to JavaScript
        var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>; // Pass PHP variable to JavaScript
        var isAdmin = <?php echo json_encode($isAdmin); ?>;
        var visitorid = <?php echo json_encode($visitor_id); ?>;
    </script>
    <main>
        <div id="gridBlogs">
            <!-- The blog post content will go here, dynamically loaded by the script -->
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <!-- JS goes here -->
    <script src="/Blog/js/index.js">
    </script>
</body>
</html>
