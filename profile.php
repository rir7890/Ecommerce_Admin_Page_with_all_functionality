<?php 
    session_start();
    require 'db.php';

     
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }

    $user_id=$_SESSION['user_id'];

    $stmt=$conn->prepare("SELECT ID,Name,Email,Address,Phone,Photo,created FROM users WHERE ID=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $result=$stmt->get_result();

    if($result->num_rows>0){
        $user=$result->fetch_assoc();
    }else{
        $_SESSION['error']='no user data found.';
        exit();
    }

    $stmt->close();
    $conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <img src="uploads/<?php echo htmlspecialchars($user['Photo']); ?>" class="img-thumbnail"
                    alt="Profile Photo">
            </div>
            <div class="col-md-8">
                <h2>Profile</h2>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <td><?php echo htmlspecialchars($user['Name']); ?></td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($user['Email']); ?></td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td><?php echo htmlspecialchars($user['Phone']); ?></td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><?php echo htmlspecialchars($user['Address']); ?></td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td><?php echo htmlspecialchars($user['created']); ?></td>
                        </tr>
                    </tbody>
                </table>
                <a href="logout.php" class="btn btn-danger">Logout</a>
                <a href="edit_data_user.php?id=<?php echo $user['ID'];?>" class="btn btn-success">Edit Profile</a>
            </div>
        </div>
    </div>
</body>

</html>