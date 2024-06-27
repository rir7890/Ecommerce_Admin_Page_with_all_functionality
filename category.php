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

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['categoryname'])){
        $categoryname = $conn->real_escape_string($_POST['categoryname']);
        $sql="INSERT INTO categories (Name,user_id) VALUES ('$categoryname','$user_id')";

        if ($conn->query($sql) === true) {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            die("insertion error of category.");
        }
    }
    
    $stmt = "SELECT * FROM categories";
    $categories=[];

    $result=$conn->query($stmt);

    if($result->num_rows>0){
        while($row=$result->fetch_assoc()){
            $categories[]=$row;
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    var $j = jQuery.noConflict();
    </script>
</head>

<body>

    <!--Navbar-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light p-4 w-100">
        <a class="navbar-brand" href="admin.php">Admin</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="brand.php">Brand</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="category.php">Category</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Add Category</h2>
        <form action="" method="post">
            <div class="mb-3">
                <input type="text" class="form-control" name="categoryname" placeholder="Enter category name" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <div class="container-fluid mt-5 px-5">
        <input type="text" id="search" class="form-control mb-3" placeholder="Search Category Name...">
        <h2 class="text-center">Category Index</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>User Id</th>
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody id="bodyTable">
                <?php 

                    if(!empty($categories)){
                        foreach($categories as $category){
                            echo "<tr>";
                            echo "<td>".$category['Name']."</td>";
                            echo "<td>" . $category['user_id'] . "</td>";
                            echo "<td>";
                            echo '<a href="edit_category.php?id=' . $category['ID'] . '" class="btn btn-warning btn-sm">Edit</a> ';
                            echo '<a href="delete_category.php?id=' . $category['ID'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this user?\');">Delete</a>';
                            echo "</td>";
                            echo "</tr>";
                        }
                    }else{
                        echo "<tr><td colspan='3'>No results found</td></tr>";
                    }
                    
                ?>
            </tbody>
        </table>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
        $j(document).ready(function() {
            $j('#search').on('input', function() {
                let searchTerm = $j(this).val();
                $j.ajax({
                    url: 'fetch_categorys.php',
                    type: 'GET',
                    data: {
                        term: searchTerm,
                    },
                    dataType: 'json',
                    success: function(data) {
                        const bodyTable = $j('#bodyTable');
                        bodyTable.empty();

                        if (data.length > 0) {

                            data.forEach(function(category) {

                                let row = "<tr>" +
                                    "<td>" + category.Name + "</td>" +
                                    "<td>" + category.user_id + "</td>" +
                                    "<td>" +
                                    '<a href="edit_category.php?id=' + category.ID +
                                    '" class="btn btn-warning btn-sm">Edit</a> ' +
                                    '<a href="delete_category.php?id=' + category
                                    .ID +
                                    '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this user?\');">Delete</a>' +
                                    "</td>" +
                                    "</tr>";

                                bodyTable.append(row);
                            })
                        } else {
                            bodyTable.append(
                                "<tr><td colspan='3'>No results found</td></tr>"
                            );
                        }
                    }
                })
            })
        })
        </script>
</body>

</html>