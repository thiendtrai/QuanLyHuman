<?php
include 'ketnoi.php';

// Thiết lập header cho JSON
header('Content-Type: application/json');

// Lấy dữ liệu từ fetch và giải mã JSON
$data = json_decode(file_get_contents('php://input'), true);

$mada = $data['mada']; // Lấy mã dự án từ dữ liệu JSON

// Truy vấn để lấy trạng thái của dự án
$sql = "SELECT trangthai FROM duan WHERE mada = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $mada);
$stmt->execute();
$stmt->bind_result($trangthai);
$stmt->fetch();

if ($trangthai) {
    echo json_encode(['success' => true, 'trangthai' => $trangthai]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy dự án.']);
}

$stmt->close();
$conn->close();
?>
