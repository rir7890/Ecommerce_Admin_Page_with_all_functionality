<?php

    session_start();
    require 'db.php';

    if(!isset($_SESSION['user_id']) && !isset($_GET['id'])){
        header("Location: login.php");
        exit();
    }

    if(!isset($_GET['id'])){
        header("Location: admin.php");
        exit();
    }

    $id = $_GET["id"];

    $stmt= $conn->prepare("DELETE FROM users WHERE ID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo "<h1>User Deleted Successfully</h1>";
    echo "<a href='admin.php'>Go Back</a>";
?>