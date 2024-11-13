<?php
include 'ketnoi.php';

$manhanvien = $_GET['manhanvien'];
$sql = "SELECT * FROM nhanvien WHERE manv = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $manhanvien);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    $row['ngaysinh'] = date(format: "d/m/Y", timestamp: strtotime(datetime: $row['ngaysinh'])); 
    echo json_encode($row);
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
?>
