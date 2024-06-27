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

    $stmt = "
    SELECT products.*, users.Name as user_name, brand.Name as brand_name, categories.Name as category_name
    FROM products
    JOIN users ON products.user_id = users.ID
    JOIN brand ON products.brand_id = brand.ID
    JOIN categories ON products.category_id = categories.ID  
    ";
    
    $products=[];
    $result=$conn->query($stmt);

    if($result){
        if($result->num_rows>0){
        while($row=$result->fetch_assoc()){
            $products[]=$row;
        }
    }
    }else{
        echo "Error: " . $conn->error;
    }
    
    
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

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
    <div class="container-fluid mt-3">

        <div class="container mb-5 text-center">
            <a href="product_add.php" class="btn btn-primary w-25">Add New Product</a>
        </div>

        <h2 class="text-center">Product Index</h2>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Image</th>
                    <th>Category Name</th>
                    <th>Brand Name</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Stock</th>
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody>
                <?php 

                    if(!empty($products)){
                        foreach($products as $product){
                            echo "<tr>";
                            echo "<td>". $product['Name'] ."</td>";
                            echo "<th><img src='".$product['image'] ."' style='width:50px;height:50px;' alt='' /></th>";
                            echo "<td>" . $product['category_name'] . "</td>";
                            echo "<td>" . $product['brand_name'] . "</td>";
                            echo "<td>" . $product['user_name'] . "</td>";
                            $product_status=($product['status']==1)?"Active":"InActive";
                            echo "<td>" . $product_status . "</td>";
                            $stock_status=($product['STU']>=1)?"In Stock":"Not In Stock";
                            echo "<td>". $stock_status . "</td>";
                            echo "<td>";
                            echo '<a href="edit_product.php?id=' . $product['ID'] . '" class="btn btn-warning btn-sm">Edit</a> ';
                            echo '<a href="delete_product.php?id=' . $product['ID'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this user?\');">Delete</a>';
                            echo "</td>";
                            echo "</tr>";
                        }
                    }else{
                        echo "<tr><td colspan='7'>No results found</td></tr>";
                    }
                    
                ?>
            </tbody>
        </table>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>