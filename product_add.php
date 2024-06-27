<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $conn->real_escape_string($_SESSION['user_id']);
$sql = "SELECT * FROM users WHERE ID = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user_check = $result->fetch_assoc();
    if ($user_check['Type'] != 1) {
        header('Location: profile.php');
        exit();
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    
    $productName = trim($conn->real_escape_string($_POST['productname']));
    $content = trim($conn->real_escape_string($_POST['content']));
    $stock = trim($conn->real_escape_string($_POST['stock']));
    $price = trim($conn->real_escape_string($_POST['price']));
    $salePrice = trim($conn->real_escape_string($_POST['saleprice']));
    $category = trim($conn->real_escape_string($_POST['category']));
    $brand = trim($conn->real_escape_string($_POST['brand']));
    $status = trim($conn->real_escape_string($_POST['status']));

    // Validate inputs
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
    if (empty($category)) {
        $errors[] = 'Category is required.';
    }
    if (empty($brand)) {
        $errors[] = 'Brand is required.';
    }
    if ($status !== '0' && $status !== '1') {
        $errors[] = 'Status must be either 0 or 1.';
    }
    
    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        $image = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    
    // If no validation errors, proceed with database insertion
    if (empty($errors)) {
        $sql = "INSERT INTO products (Name, Content, image, STU, Price, sale_price, category_id, brand_id, user_id, status) 
                VALUES ('$productName', '$content', '$image', '$stock', '$price', '$salePrice', '$category', '$brand', '$user_id', '$status')";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = 'Product added successfully.';
            header('Location: product_add.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error adding product: ' . $conn->error;
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
    <title>Add Product</title>
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

    <!-- Add Product Form -->
    <div class="container mt-5 p-5">
        <h2>Add Product</h2>

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
            <div class="mb-3">
                <label for="productname" class="form-label">Product Name</label>
                <input type="text" class="form-control" name="productname" placeholder="Enter product name.." required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" name="content" rows="3" placeholder="Enter content.."
                    required></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" name="image">
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" name="stock" placeholder="Enter Stock.." required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="float" class="form-control" name="price" placeholder="Enter price.." required>
            </div>
            <div class="mb-3">
                <label for="saleprice" class="form-label">Sale Price</label>
                <input type="float" class="form-control" name="saleprice" placeholder="Enter sale price..">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" name="category" required>
                    <option value="" disabled selected>Select a category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['ID']; ?>"><?php echo $category['Name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="brand" class="form-label">Brand</label>
                <select class="form-select" name="brand" required>
                    <option value="" disabled selected>Select a brand</option>
                    <?php foreach ($brands as $brand): ?>
                    <option value="<?php echo $brand['ID']; ?>"><?php echo $brand['Name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>