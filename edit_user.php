<?php 

    session_start();
    require 'db.php';

    if(!isset($_SESSION['user_id'])){
        header('Location: login.php');
        exit();
    }

    if(!isset($_GET['id'])){
        header('Location: admin.php');
        exit();
    }

    $id=intval($_GET['id']);

    $errors = [];
    $name = $email = $address = $phone = '';

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        
        // Validate name
        if (empty($_POST['name'])) {
            $errors['name'] = "Name is required.";
        } elseif (!preg_match("/^[a-zA-Z ]*$/", $_POST['name'])) {
            $errors['name'] = "Only letters and spaces are allowed.";
        } else {
            $name = $conn->real_escape_string($_POST['name']);
        }

        // Validate email
        if (empty($_POST['email'])) {
            $errors['email'] = "Email is required.";
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format.";
        } else {
            $email = $conn->real_escape_string($_POST['email']);
        }

        // Validate address
        if (empty($_POST['address'])) {
            $errors['address'] = "Address is required.";
        } else {
            $address = $conn->real_escape_string($_POST['address']);
        }

        // Validate phone
        if (empty($_POST['phone'])) {
            $errors['phone'] = "Phone number is required.";
        } elseif (!preg_match("/^\d{10}$/", $_POST['phone'])) {
            $errors['phone'] = "Phone number must be 10 digits.";
        } else {
            $phone = $conn->real_escape_string($_POST['phone']);
        }

        // If no errors, update the user's information
        if (empty($errors)) {
            $stmt = $conn->prepare('UPDATE users SET Name=?, Email=?, Address=?, Phone=? WHERE Id=?');
            $stmt->bind_param("ssssi", $name, $email, $address, $phone, $id);
            $stmt->execute();
            $stmt->close();
            header("Location: admin.php");
            exit();
        }

    }else{
        // echo gettype($id);
        $stmt= $conn->prepare("SELECT Name,Email,Address,Phone FROM users WHERE ID=?");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $stmt->bind_result($name,$email,$address,$phone);
        $stmt->fetch();
        $stmt->close();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light p-4">
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

    <form class="p-4" action="" method="post">

        <div class="form-group m-3">
            <label for="exampleInputName">Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name);?>" class="form-control"
                id="exampleInputName" aria-describedby="NameHelp" placeholder="Enter Name..">
            <?php if (isset($errors['name'])): ?>
            <div class="alert alert-danger mt-2"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group m-3">
            <label for="exampleInputEmail1">Email address</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email);?>" class="form-control"
                id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Email..">
            <?php if (isset($errors['email'])): ?>
            <div class="alert alert-danger mt-2"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group m-3">
            <label for="exampleInputAddress">Address</label>
            <textarea name="address" class="form-control " id="exampleInputAddress">
                <?php echo htmlspecialchars(trim($address));?>
            </textarea>
            <?php if (isset($errors['address'])): ?>
            <div class="alert alert-danger mt-2"><?php echo $errors['address']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group m-3">
            <label for="exampleInputPhone">Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($phone)?>" class="form-control"
                id="exampleInputPhone" aria-describedby="PhoneHelp" placeholder="Enter PhoneNumber..">
            <?php if (isset($errors['phone'])): ?>
            <div class="alert alert-danger mt-2"><?php echo $errors['phone']; ?></div>
            <?php endif; ?>
        </div>
        <!-- <div class="form-group">
            <label for="exampleFormControlFile1">Example file input</label>
            <input type="file" class="form-control-file" id="exampleFormControlFolFile1">
        </div> -->
        <button type="submit" class="btn btn-primary m-3">Submit</button>
    </form>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>