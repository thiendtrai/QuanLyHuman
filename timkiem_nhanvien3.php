<?php
include 'ketnoi.php'; // Kết nối đến cơ sở dữ liệu

if (isset($_GET['tennv']) && isset($_GET['phongban'])) {
    $tennv = $_GET['tennv'];
    $phongban = $_GET['phongban'];

    // Câu truy vấn để tìm kiếm nhân viên theo tên, sdt, hoặc mã nhân viên trong phòng ban cụ thể và đang làm việc
    $sql = "SELECT nv.*, pb.tenpb 
            FROM nhanvien nv 
            JOIN phongban pb ON nv.mapb = pb.mapb 
            WHERE (nv.hoten LIKE ? OR nv.sdt LIKE ? OR nv.manv LIKE ?) 
            AND nv.mapb = ? AND nv.trangthai = 'Đang Làm Việc'";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $searchTerm = "%" . $tennv . "%";

        // Gắn các tham số cho câu truy vấn
        $stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $phongban);
        $stmt->execute();
        $result = $stmt->get_result();

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

        // Trả về dữ liệu dưới dạng JSON
        header('Content-Type: application/json');
        echo json_encode($nhanvienList);

        // Đóng statement và kết nối
        $stmt->close();
    } else {
        // Trường hợp không thể chuẩn bị truy vấn, trả về lỗi
        echo json_encode(['error' => 'Không thể thực hiện truy vấn']);
    }

    $conn->close();
} else {
    // Trường hợp không nhận được tham số cần thiết, trả về lỗi
    echo json_encode(['error' => 'Thiếu tham số tennv hoặc phongban']);
}
?>
