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
    }else{
        $product_id=$conn->real_escape_string($_GET['id']);
    
        $sql="SELECT * FROM products WHERE ID=$product_id";
        $result = $conn->query($sql);
        // print_r($result);
        if($result && $result->num_rows == 1){
            $product_data=$result->fetch_assoc();
        }else{
            die('Something went wrong in the database fetching in single product data.');
        }
    }

    // Fetch categories and brands
    $categories = [];
    $brands = [];

    $cat_result = $conn->query("SELECT * FROM categories");
    if ($cat_result->num_rows > 0) {
        while ($row = $cat_result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    $brand_result = $conn->query("SELECT * FROM brand");
    if ($brand_result->num_rows > 0) {
        while ($row = $brand_result->fetch_assoc()) {
            $brands[] = $row;
        }
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
        $errors = [];
    
        $productName = $conn->real_escape_string(trim($_POST['productname']));
        $content = $conn->real_escape_string(trim($_POST['content']));
        $stock = $conn->real_escape_string(trim($_POST['stock']));
        $price = $conn->real_escape_string(trim($_POST['price']));
        $salePrice = $conn->real_escape_string(trim($_POST['saleprice']));
        $category = $conn->real_escape_string(trim($_POST['category']));
        $brand = $conn->real_escape_string(trim($_POST['brand']));
        $status = $conn->real_escape_string(trim($_POST['status']));

        if (empty($productName)) {
            $errors[] = 'Product name is required.';
        }
        if (empty($content)) {
            $errors[] = 'Content is required.';
        }
        if (!is_numeric($stock) || $stock < 0) {
            $errors[] = 'Stock must be a non-negative number.';
        }
        if (!is_numeric($price) || $price < 0) {
            $errors[] = 'Price must be a non-negative number.';
        }
        if (!is_numeric($salePrice) || $salePrice < 0) {
            $errors[] = 'Sale price must be a non-negative number.';
        }
        if (!is_numeric($category)) {
            $errors[] = 'Invalid category selected.';
        }
        if (!is_numeric($brand)) {
            $errors[] = 'Invalid brand selected.';
        }
        if (!in_array($status, ['0', '1'])) {
            $errors[] = 'Invalid status selected.';
        }
        
        // Handle file upload
        $image = $product_data['image']; // Retain existing image path by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "uploads/";
            $image = $targetDir . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);
        }

        // Update query
        if (empty($errors)) {
            $sql = "UPDATE products 
                    SET Name='$productName', Content='$content', image='$image', STU='$stock', Price='$price', sale_price='$salePrice', category_id='$category', brand_id='$brand', status='$status'
                    WHERE ID='$product_id'";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['success'] = 'Product updated successfully.';
                header('Location: products.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error updating product.';
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errors);
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <!-- Navbar -->
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

    <div class="container mt-5 p-5">
        <h2 class="text-center">Edit Product</h2>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">

            <div class="mb-3 text-center">
                <img src="<?php echo htmlspecialchars($product_data['image']);?>" style="height:200px;width:200px;"
                    alt="product_image">
            </div>

            <div class="mb-3">
                <label for="productname" class="form-label">Product Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($product_data['Name']);?>"
                    name="productname" placeholder="Enter product name.." required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" name="content" rows="3" placeholder="Enter content.." required>
                <?php echo htmlspecialchars(trim($product_data['Content']));?>
                </textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" name="image">
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" name="stock"
                    value="<?php echo htmlspecialchars($product_data['STU']);?>" placeholder="Enter Stock Unit.."
                    required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="float" class="form-control" value="<?php echo htmlspecialchars($product_data['Price']);?>"
                    name="price" placeholder="Enter price.." required>
            </div>

            <div class="mb-3">
                <label for="saleprice" class="form-label">Sale Price</label>
                <input type="float" class="form-control"
                    value="<?php echo htmlspecialchars($product_data['sale_price']);?>" name="saleprice"
                    placeholder="Enter sale price..">
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" name="category" required>
                    <?php foreach ($categories as $category): ?>
                    <option <?php 
                        $selectedoption=($category['ID']==$product_data['category_id'])?"selected":'';
                    ?> value="<?php echo $category['ID']; ?>"><?php echo $category['Name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="brand" class="form-label">Brand</label>
                <select class="form-select" name="brand" required>
                    <?php foreach ($brands as $brand): ?>
                    <option <?php 
                        $selectedoption=($brand['ID']==$product_data['brand_id'])?"selected":'';
                    ?> value="<?php echo $brand['ID']; ?>"><?php echo $brand['Name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <option <?php 
                        $selectedoption=(1==$product_data['status'])?"selected":'';
                    ?> value="1">Active</option>
                    <option <?php 
                        $selectedoption=(0==$product_data['status'])?"selected":'';
                    ?> value="0">Inactive</option>
                </select>
            </div>

            <div class="mb-3 text-center">
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>