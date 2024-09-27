<?php
require "db.php";
$mydb = new myDB();



if (isset($_POST['find_user'])) {

    $mydb->loginUser('users', $_POST['email'], $_POST['password']);
}

if (isset($_POST['add_post'])) {
    unset($_POST['add_post']);
    $mydb->insert('posts', [...$_POST]);
    header("location: ../");
}

if (isset($_POST['get_post'])) {

    $mydb->select('posts');
    $datas = [];
    while ($row = $mydb->res->fetch_assoc()) {
        array_push($datas, $row);
    }
    echo json_encode($datas);
}

if (isset($_POST['update_post'])) {
    unset($_POST['update_student']); // Remove the action identifier from data

    $id = $_POST['id']; // Get the ID
    unset($_POST['id']); // Remove 'id' from data as it will be used in the WHERE condition

    $data = $_POST; // Remaining fields are the data to be updated
    $where = ['id' => $id]; // Condition for which row to update

    $mydb->update('posts', $data, $where);
    header("location: ../");
}

if (isset($_POST['delete_post'])) {
    $where = ['id' => $_POST['id']];
    $mydb->delete('posts', $where);
    header("location: ../");
}
