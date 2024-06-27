<?php

    session_start();
    require 'db.php';

    if(!isset($_SESSION['user_id'])){
        header('Location: login.php');
        exit();
    }else{
        $user_id = $conn->real_escape_string($_SESSION['user_id']);
        $sql= "SELECT * FROM users WHERE ID = $user_id";
        $result = $conn->query($sql);

        if($result->num_rows>0){
            $user_check=$result->fetch_assoc();
            if($user_check['Type']!=1){
                header('Location: profile.php');
                exit();
            }
        }
    }

    if(!isset($_GET['id'])){
        header('Location: products.php');
        exit();
    }
    
    $product_id=$conn->real_escape_string($_GET['id']);
    $stmt=$conn->prepare("DELETE FROM categories WHERE ID=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    echo "<h1>Product Deleted Successfully</h1>";
    echo "<a href='products.php'>Go Back</a>";

?>