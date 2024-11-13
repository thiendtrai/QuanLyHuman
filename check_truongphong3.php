<?php
include 'ketnoi.php';

// Kiểm tra xem có dữ liệu đầu vào không
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['phongban'])) {
    echo json_encode(['error' => 'Thiếu thông tin phòng ban']);
    exit;
}

$phongban = $data['phongban'];

$sql = "SELECT COUNT(*) as count FROM nhanvien WHERE mapb='$phongban' AND chucvu='Trưởng Phòng'"; 
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode(['hasTruongPhong' => $row['count'] > 0]);
$conn->close();
?>
