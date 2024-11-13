<?php
include 'ketnoi.php';

// Thiết lập header cho JSON
header('Content-Type: application/json');

// Lấy dữ liệu từ fetch và giải mã JSON
$data = json_decode(file_get_contents('php://input'), true);
$mapb = $data['mapb'];

// Kiểm tra mã phòng ban có tồn tại không
if (!$mapb) {
    echo json_encode(['success' => false, 'message' => 'Mã phòng ban không hợp lệ.']);
    exit;
}

// Bước 1: Kiểm tra xem có nhân viên nào thuộc phòng ban này không
$sql_check_nhanvien = "SELECT COUNT(*) FROM nhanvien WHERE mapb = ?";
$stmt_check = $conn->prepare($sql_check_nhanvien);
$stmt_check->bind_param('i', $mapb);
$stmt_check->execute();
$stmt_check->bind_result($nhanvien_count);
$stmt_check->fetch();
$stmt_check->close();

if ($nhanvien_count > 0) {
   
    echo json_encode(['success' => false, 'message' => 'Phòng ban này vẫn còn nhân viên, không thể khóa.']);
    exit;
}

// Bước 2: Cập nhật trạng thái phòng ban
$sql = "UPDATE phongban SET trangthai = 'locked' WHERE mapb = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $mapb);

if ($stmt->execute()) {
    // Kiểm tra xem có hàng nào bị ảnh hưởng không
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Trạng thái phòng ban đã được cập nhật.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy phòng ban hoặc trạng thái đã được khóa.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật trạng thái phòng ban.']);
}

$stmt->close();
$conn->close();
?>
