<?php
require_once '../../db/session_manager.php';
requireAdmin();
require_once '../../db/your_database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $content = $_POST['content'];

    $mydb->insert('blogs', [
        'title' => $title,
        'author' => $author,
        'content' => $content,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    header('Location: /pages/admin/blog_dashboard.php');
    exit();
}
?>
