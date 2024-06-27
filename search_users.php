<?php 

    require 'db.php';

    if(isset($_GET['query'])){
        $query = $conn->real_escape_string($_GET['query']);
        $status= isset($_GET['status'])? $conn->real_escape_string($_GET['status']):'';
        $type= isset($_GET['type'])? $conn->real_escape_string($_GET['type']):'';
        
        $sql = "SELECT * FROM users WHERE (Name LIKE '%$query%' OR Email LIKE '%$query%' OR Phone LIKE '%$query%')";
        
        if($status !== ''){
            $status_value= ($status=='Active')?1:0;
            $sql.= "AND status = $status_value";
        }
        if($type !== ''){
            $type_value= ($type=='Admin')?1:0;
            $sql.= "AND Type = $type_value";
        }

        $result=$conn->query($sql);
        $users=[];

        if($result->num_rows>0){
            while($row=$result->fetch_assoc())
            {
                $users[]=$row;
            }
        }
        header('Content-Type: application/json');
        echo json_encode($users);
    }
    ?>

<?php
// require 'db.php';

// if (isset($_GET['query'])) {
//     $query = $_GET['query'];
//     $searchQuery = '%' . $conn->real_escape_string($query) . '%'; // Safely escape the input

//     $stmt = $conn->prepare("SELECT * FROM users WHERE Name LIKE ?");
//     $stmt->bind_param('s', $searchQuery);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     $users = [];
//     if ($result->num_rows > 0) {
//         while ($row = $result->fetch_assoc()) {
//             $users[] = $row;
//         }
//     }

//     header('Content-Type: application/json'); // Set the correct content type
//     echo json_encode($users);
// }
?>