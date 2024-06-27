<?php 
    session_start();
    require 'db.php';

    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION["user_id"];
    // echo gettype($user_id);//integer value

    $stmt = $conn->prepare("SELECT * FROM users WHERE ID=?");
    $stmt->bind_param('i',$user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    
    if($result->num_rows==1){
        $user_data=$result->fetch_assoc();
        // echo $user_data['Name'];
        if($user_data['Type']!=1){
            header("Location: profile.php");
            exit();
        }
        else{
            // $_SESSION['success']="Admin is Ready to Profile view";

            $stmt1=$conn->prepare("SELECT * FROM users");
            $stmt1->execute();
            $result1 = $stmt1->get_result();

            $all_users=[];
            if($result1->num_rows>0){
                // $all_user=$result->fetch_assoc();
                while($row = $result1->fetch_assoc()) {
                   $all_users[] = $row;
                }
                // print_r($all_users);
            }else{
                $_SESSION['error']='error in fetching the data';
                header("Location: login.php");
                exit();
            }
        }
    }else{
        header('Location: login.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
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
                    <a class="nav-link" href="products.php">product</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>


    <div class="container w-100 d-flex flex-row gap-2">
        <div class="input-group w-50 mt-5">
            <input type="text" class="form-control search-input" placeholder="Search" onkeyup="searchfunction()">
        </div>

        <!-- Type Selection Input -->
        <div class="input-group mt-5 w-25">
            <label class="input-group-text" for="typeSelect">Type</label>
            <select class="form-select" id="typeSelect" onchange="searchfunction()">
                <option value="">All</option>
                <option value="Admin">Admin</option>
                <option value="User">User</option>
            </select>
        </div>

        <!-- Status Selection Input -->
        <div class="input-group mt-5 w-25">
            <label class="input-group-text" for="statusSelect">Status</label>
            <select class="form-select" id="statusSelect" onchange="searchfunction()">
                <option value="">All</option>
                <option value="Active">Active</option>
                <option value="InActive">Inactive</option>
            </select>
        </div>
    </div>



    <div class="container-fluid mt-5">
        <h2>User Management</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Joined</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if(!empty($all_users)){
                        foreach($all_users as $user){
                            echo "<tr>";
                            echo "<td>".$user['Name']."</td>";
                            echo "<td>" . $user['Email'] . "</td>";
                            echo "<td>" . $user['Address'] . "</td>";
                            echo "<td>" . $user['Phone'] . "</td>";
                            echo "<td>". $user["created"] . "</td>";
                            $user_Type= ($user['Type']==1)?"Admin":"User";
                            echo "<td>" . $user_Type. "</td>";
                            $user_status= ($user['status']==1)?"Active":"InActive";
                            echo "<td>" . $user_status . "</td>";
                            echo "<td>";
                            echo '<a href="edit_user.php?id=' . $user['ID'] . '" class="btn btn-warning btn-sm">Edit</a> ';
                            echo '<a href="delete_user.php?id=' . $user['ID'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this user?\');">Delete</a>';
                            echo "</td>";
                            echo "</tr>";
                        }
                    }else{
                        echo "<tr><td colspan='8'>No results found</td></tr>";
                    }
                ?>
            </tbody>
        </table>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
        function searchfunction() {
            const searchValue = document.querySelector('.search-input').value;
            const typeSelect = document.querySelector('#typeSelect').value;
            const statusSelect = document.querySelector('#statusSelect').value;
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `search_users.php?query=${searchValue}&type=${typeSelect}&status=${statusSelect}`, true);
            xhr.send(); // Send the request before defining the onload function
            xhr.onload = function() {
                if (this.status === 200) {
                    let users;
                    try {
                        users = JSON.parse(this.responseText);
                    } catch (e) {
                        console.error("Failed to parse JSON response:", e);
                        return;
                    }

                    // Ensure that users is an array
                    if (!Array.isArray(users)) {
                        console.error("Unexpected response format:", users);
                        return;
                    }

                    // console.log(users);
                    const userTable = document.querySelector('.table tbody');
                    userTable.innerHTML = '';

                    users.forEach(function(user) { // Corrected to forEach
                        const row = userTable.insertRow();
                        row.innerHTML = `
                        <td>${user.Name}</td>
                        <td>${user.Email}</td>
                        <td>${user.Address}</td>
                        <td>${user.Phone}</td>
                        <td>${user.created}</td>
                        <td>${(user.Type == 1) ? "Admin" : "User"}</td> <!-- Corrected user.Types to user.Type -->
                        <td>${(user.status == 1) ? "Active" : "Inactive"}</td>
                        <td>
                            <a href="edit_user.php?id=${user.ID}" class="btn btn-warning btn-sm">Edit</a> 
                            <a href="delete_user.php?id=${user.ID}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    `;
                    });
                }
            };
        }
        </script>
</body>

</html>