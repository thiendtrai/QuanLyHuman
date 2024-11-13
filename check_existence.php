<?php
include 'ketnoi.php';
$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$sdt = $data->sdt;
$cccd = $data->cccd;

$sql = "SELECT COUNT(*) as count FROM nhanvien WHERE email='$email' OR sdt='$sdt' OR cccd='$cccd'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode(['exists' => $row['count'] > 0]);

$conn->close();
?>
