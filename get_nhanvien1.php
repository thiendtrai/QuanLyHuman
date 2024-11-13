<?php
include 'ketnoi.php'; // Kết nối đến cơ sở dữ liệu

// Lấy mã phòng ban và trạng thái từ tham số GET
$phongbanId = $_GET['phongbanId'];
$trangthai = $_GET['trangthai'];

// Sử dụng Prepared Statements để ngăn chặn SQL Injection
$stmt = $conn->prepare("SELECT nv.*, pb.tenpb 
FROM nhanvien nv 
JOIN phongban pb ON nv.mapb = pb.mapb 
WHERE nv.mapb = ? AND nv.trangthai = ? 
ORDER BY 
    CASE 
        WHEN nv.chucvu = 'Trưởng Phòng' THEN 0 
        ELSE 1 
    END, nv.chucvu ASC"); 

// Gắn giá trị tham số vào truy vấn
$stmt->bind_param("ss", $phongbanId, $trangthai); // Gắn giá trị cho mapb và trangthai

$stmt->execute();
$result = $stmt->get_result();

// Tạo mảng để chứa danh sách nhân viên
$nhanvienList = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Định dạng lại ngày sinh sang DD/MM/YYYY
        $ngaysinh = date('d/m/Y', strtotime($row['ngaysinh']));
        
        $nhanvienList[] = [
            'manv' => $row['manv'],
            'hinhanh' => $row['hinhanh'],
            'hoten' => $row['hoten'],
            'ngaysinh' => $ngaysinh, // Ngày sinh đã được định dạng
            'gioitinh' => $row['gioitinh'],
            'chucvu' => $row['chucvu'],
            'email' => $row['email'],
            'trangthai' => $row['trangthai'],
            'tenpb' => $row['tenpb'], // Tên phòng ban
        ];
    }
}

// Đóng statement và kết nối
$stmt->close();
$conn->close();

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($nhanvienList);
?>