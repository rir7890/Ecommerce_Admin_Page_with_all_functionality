<?php

    require 'db.php';

    if(isset($_GET['term'])){
        $searchTerm = isset($_GET['term'])?$conn->real_escape_string($_GET['term']) :'';

        $stmt = "SELECT * FROM brand WHERE Name LIKE '%$searchTerm%'";
        $brands=[];
        $result=$conn->query($stmt);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $brands[] = $row;
            }
        }
        echo json_encode($brands);
    }
    $conn->close();
?>