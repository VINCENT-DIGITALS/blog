<?php
require_once 'db/db.php';

$mydb = new myDB;
$blogs = $mydb->select('posts');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Dashboard</title>
    <link rel="stylesheet" href="/css/index_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
    <?php include 'includes/nav_bar.php';
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $isLoggedIn = true; // User is logged in if 'user_id' is set
        $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'admin' : null; // Check if user is admin
    } else {
        $user_id = null;
        $isLoggedIn = false; // Not logged in if 'user_id' is not set
        $isAdmin = null; // Not an admin if not logged in
    }
    
    ?>
    <script>
        var userId = <?php echo json_encode($user_id); ?>; // Pass PHP user_id to JavaScript
        var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>; // Pass PHP variable to JavaScript
        var isAdmin = <?php echo json_encode($isAdmin); ?>; 
    </script>

    <section class="search-container">
        <input type="text" id="searchInput" placeholder="Search for a blog..." />
    </section>

    <main>
        

        <!-- Blog List -->
        <section>
            <div id="gridBlogs">
                <!-- Blog cards will be populated here by JavaScript -->
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="/js/index.js"></script>
</body>

</html>