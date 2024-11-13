<?php
include 'ketnoi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mapb = $_POST['mapb'];
    
     
    $sql = "SELECT mapb, tenpb FROM phongban WHERE mapb != '$mapb' trangthai != 'locked'";
    $result = $conn->query($sql);

    $phongbans = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $phongbans[] = $row;
        }
    }

  
    echo json_encode($phongbans);
}

$conn->close();
?>
