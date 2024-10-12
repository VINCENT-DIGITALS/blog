<?php
require_once '../../db/session_manager.php';
requireAdmin();
require_once '../../db/your_database.php';

$blog_id = $_GET['id'];
$blog = $mydb->getOne('blogs', ['id' => $blog_id]); // Fetch the specific blog

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $content = $_POST['content'];

    $mydb->update('blogs', [
        'title' => $title,
        'author' => $author,
        'content' => $content
    ], ['id' => $blog_id]);

    header('Location: /pages/admin/blog_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
</head>
<body>
    <h1>Edit Blog</h1>
    <form action="" method="post">
        <label for="title">Blog Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($blog['title']); ?>" required>

        <label for="author">Author:</label>
        <input type="text" name="author" value="<?= htmlspecialchars($blog['author']); ?>" required>

        <label for="content">Content:</label>
        <textarea name="content" rows="5" required><?= htmlspecialchars($blog['content']); ?></textarea>

        <button type="submit">Update Blog</button>
    </form>
</body>
</html>
