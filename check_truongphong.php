<?php
include 'ketnoi.php';
$data = json_decode(file_get_contents("php://input"));
$phongban = $data->phongban;

$sql = "SELECT COUNT(*) as count FROM nhanvien WHERE mapb='$phongban' AND chucvu='Trưởng Phòng'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode(['hasTruongPhong' => $row['count'] > 0]);

$conn->close();
?>
