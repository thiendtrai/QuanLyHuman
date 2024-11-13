<?php
include 'ketnoi.php';
$mapb = $_GET['mapb'];
$sql = "SELECT mapb, tenpb FROM phongban where mapb != '$mapb' trangthai != 'locked'";
$result = $conn->query($sql);

$phongbanList = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $phongbanList[] = [
            'mapb' => $row['mapb'],
            'tenpb' => $row['tenpb'],
        ];
    }
}

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($phongbanList);

$conn->close();
?>
