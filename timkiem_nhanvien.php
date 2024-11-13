<?php
include 'ketnoi.php';

if (isset($_GET['tennv']) && !empty($_GET['tennv'])) {
    $tennv = $_GET['tennv'];

    // Sửa câu lệnh SQL để kết nối đúng bảng và tìm kiếm theo nhiều tiêu chí
    $sql = "SELECT nv.*, pb.tenpb 
            FROM nhanvien nv 
            JOIN phongban pb ON nv.mapb = pb.mapb 
            WHERE nv.hoten LIKE ? OR nv.sdt LIKE ? OR nv.manv LIKE ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $searchTerm = "%" . $tennv . "%";

        // Gắn ba tham số vào câu truy vấn (tìm kiếm trên hoten, sdt, và manv)
        $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        // Tạo mảng để chứa danh sách nhân viên
        $nhanvienList = [];
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

        // Trả về dữ liệu dưới dạng JSON
        header('Content-Type: application/json');
        echo json_encode($nhanvienList);

        // Đóng statement và kết nối
        $stmt->close();
    } else {
        // Trường hợp không thể chuẩn bị truy vấn, trả về lỗi
        echo json_encode(['error' => 'Không thể thực hiện tìm kiếm']);
    }
    
    $conn->close();
} else {
    // Nếu không có tham số tìm kiếm, trả về một mảng rỗng
    echo json_encode([]);
}
?>
