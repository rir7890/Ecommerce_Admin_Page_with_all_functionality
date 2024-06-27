<?php 

    require 'db.php';

    if(isset($_GET['term'])){
        
        $searchTerm = isset($_GET['term']) ? $conn->real_escape_string($_GET['term']):'';

        $stmt ="SELECT * FROM categories WHERE Name LIKE '%$searchTerm%'";
        $result=$conn->query($stmt);
        $categories=[];

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $categories[]=$row;
            }
        }
        echo json_encode($categories);
        
    }
?>