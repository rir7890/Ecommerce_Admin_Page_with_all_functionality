<?php 
session_start();

$email=$password='';
$emailErr=$passwordErr='';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if($_SERVER['REQUEST_METHOD'] =='POST'){
    

    $email=sanitizeInput($_POST['email']);
    $hashpassword=md5($_POST['password']);
    // $_SESSION['error1'] = md5($password);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $emailErr= 'Invalid email format.';
    }

    if(!$emailErr){
        require 'db.php';

        $stmt= $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows==1){
            $user=$result->fetch_assoc();
            // $_SESSION['error2']=$hashpassword;
            if($hashpassword === $user['password']){
                $_SESSION['user_id'] = $user['ID'];
                header("Location: profile.php");
                exit();
            }else{
                $_SESSION['error']='No Data Found about this user';
                header("Location: login.php");
                exit();
            }
        }else{
            $_SESSION["error"]= "User not found , Register yourself!";
            header("Location: index.php");
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light p-4">
        <a class="navbar-brand" href="#">MyWebsite</a>
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
    <h1 class="mt-5 text-center">Login Form</h1>
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>
    <div class="container p-5">
        <form action="" method="post">
            <div class="form-group m-3">
                <input type="email" name="email" class="form-control" aria-describedby="emailHelp"
                    placeholder="Enter email Address..">
                <span class="error" style="<?php echo $emailErr?"display:visible;":"display:none;"?>">
                    <?php echo "$emailErr"?>
                </span>
            </div>
            <div class="form-group m-3">
                <input type="password" name="password" class="form-control" placeholder="Enter Password..">
                <span class="error" style="<?php echo $passwordErr?"display:visible;":"display:none;"?>">
                    <?php echo "$passwordErr"?>
                </span>
            </div>
            <button type="submit" class="btn btn-primary m-3">Submit</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>