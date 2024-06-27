<?php

    session_start();
    require 'db.php';

    // Directory where the uploaded files will be saved
    $uploadDir = 'uploads/';

    $name=$email=$password=$address=$file=$phonenumber='';
    $nameErr=$emailErr=$passwordErr=$fileErr=$phonenumberErr='';

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        
        // previously i am using this method
        // $name=$_POST['name'];
        // $email=$_POST['email'];
        // $password=$_POST['password'];
        // $address=$_POST['address'];
        // $file=$_FILES['file'];
        // $phonenumber=$_POST['phonenumber'];

        ////handling the input data of name
        if (empty($_POST['name'])) {
            $nameErr = 'Name is required.';
        } else {
            $name = input_data($_POST['name']);
            if (!preg_match('/^[a-zA-Z\s]*$/', $name)) {
                $nameErr = 'Only alphabets and spaces are allowed.';
            }
        }


         ////handling the input data of email
        if (empty($_POST['email'])) {
            $emailErr = 'Email is required.';
        } else {
            $email = input_data($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = 'Invalid email format.';
            }
        }


         ////handling the input data of password
        if(empty($_POST['password'])){
            $passwordErr= 'password is required.';
        }else{
            $password=md5($_POST['password']);
        }

         ////handling the input data of address
        if(empty($_POST['address'])){
            $addressErr= 'address is empty.';
        }else{
            $address=$_POST['address'];
        }

         ////handling the input data of phone number
        if(empty($_POST['phonenumber'])){
            $phonenumberErr='Mobile no. is required.';
        }else{
            $phonenumber=input_data($_POST['phonenumber']);

            if(!preg_match("/^[0-9]*$/",$phonenumber)){
                $phonenumberErr= "only numeric numbers are allowed.";
            }
            if(strlen($phonenumber)!=10){
                $phonenumberErr= "Mobile no. must be eqaul to 10 digits.";
            }
        }

         ////handling the input data of file
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Sanitize file name
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Create the uploads directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Directory where the uploaded file will be moved
            $destPath = $uploadDir . $newFileName;
            $file=$newFileName;

            // Move the file to the destination directory
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $_SESSION['message'] = 'File is successfully uploaded.';
            } else {
                $fileErr = 'There was some error moving the file to upload directory.';
            }
        } else {
            $fileErr = 'There was some error in the file upload. Error code: ' . $_FILES['file']['error'];
        }

        $created=date("Y-m-d H:i:s");

        $sql="INSERT INTO users (Name,Email,password,Address,Phone,Photo,created) VALUES ('$name', '$email', '$password', '$address', '$phonenumber','$file','$created')";

        ////it checks error if their is error then no login page will be shown after registration
        if($nameErr || $passwordErr|| $fileErr || $phonenumberErr ||$emailErr ){
            $_SESSION['error']= 'Error occurred while creatting the user.';
        }else{
            if($conn->query($sql)===TRUE){
                $_SESSION['success']='Successfully User Created.';
                header("Location: login.php");
                exit();
            }
        }
        
        
    }
    function input_data($data) {  
        $data = trim($data);  
        $data = stripslashes($data);  
        $data = htmlspecialchars($data);  
        return $data;  
    } 

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light p-4">
        <a class="navbar-brand" href="index.php">MyWebsite</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Register</a>
                </li>
            </ul>
        </div>
    </nav>
    <?php 
        //echo "<p>".(($_SESSION['error'])?($_SESSION['error']):(""))."</p>";
    ?>
    <!-- form html -->
    <h1 class="text-center mt-5">Registration Form</h1>
    <div class="container p-5">
        <form method="post" action="" enctype="multipart/form-data">

            <div class="form-group mb-3">
                <label for="exampleInputName">Name</label>
                <input type="text" name="name" class="form-control mt-1" id="exampleInputName"
                    aria-describedby="NameHelp" placeholder="Enter Name.." required>
                <span class="error" style="<?php echo $nameErr?"display:visible;":"display:none;"?>">
                    <?php echo "$nameErr"?>
                </span>
            </div>

            <div class="form-group mb-3">
                <label for="exampleInputEmail">Email</label>
                <input type="email" name="email" class="form-control mt-1" id="exampleInputEmail"
                    placeholder="Email Address.." required>
                <span class="error" style="<?php echo $emailErr?"display:visible;color:red;":"display:none;"?>">
                    <?php echo "$emailErr"?>
                </span>
            </div>

            <div class="form-group mb-3">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" name="password" class="form-control mt-1" id="exampleInputPassword"
                    placeholder="Password.." required>
                <span class="error" style="<?php echo $passwordErr?"display:visible;color:red;":"display:none;"?>">
                    <?php echo "$passwordErr"?>
                </span>
            </div>

            <div class="form-group mb-3">
                <label for="exampleFormControlTextarea3">Address</label>
                <textarea name="address" class="form-control mt-1" id="exampleFormControlAddress" rows="7"></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="exampleInputPhoneNumber">Phone Number</label>
                <input type="tel" name="phonenumber" class="form-control mt-1" id="exampleInputPhoneNumber"
                    placeholder="Phone Number.." required>
                <span class="error" style="<?php echo $phonenumberErr?"display:visible;color:red;":"display:none;"?>">
                    <?php echo "$phonenumberErr"?>
                </span>
            </div>

            <div class="form-group mb-3 mt-4">
                <label for="exampleFormControlFile1">file input</label>
                <input type="file" name="file" class="form-control-file mt-1" id="exampleFormControlFile1" required>
                <span class="error" style="<?php echo $fileErr?"display:visible;color:red;":"display:none;"?>">
                    <?php echo "$fileErr"?>
                </span>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Save & Register</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>