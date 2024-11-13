<?php
include 'ketnoi.php'; // Kết nối đến cơ sở dữ liệu

// Lấy dữ liệu từ yêu cầu GET
$tenduan = isset($_GET['tenduan']) ? trim($_GET['tenduan']) : '';

// Nếu không có giá trị thì không thực hiện truy vấn
if (empty($tenduan)) {
    echo json_encode([]);
    exit;
}

// Update trạng thái của các dự án dựa trên ngày hiện tại
$today = date('Y-m-d');
$updateSql = "
    UPDATE duan 
    SET trangthai = CASE
        WHEN ngayhoanthanh < ? THEN 'Đã hoàn thành'
        WHEN ngayhoanthanh > ? AND ngaybatdau < ? THEN 'Đang thực hiện'
        WHEN ngaybatdau > ? THEN 'Sắp thực hiện'
    END
    WHERE (trangthai != 'Đã hoàn thành' AND ngayhoanthanh < ?)
       OR (trangthai != 'Đang thực hiện' AND ngayhoanthanh > ? AND ngaybatdau < ?)
       OR (trangthai != 'Sắp thực hiện' AND ngaybatdau > ?)
";

$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("ssssssss", $today, $today, $today, $today, $today, $today, $today, $today);
$updateStmt->execute();
$updateStmt->close();

// Truy vấn lấy dữ liệu dự án dựa trên `mada` hoặc `tenda`
$sql = "SELECT da.mada, da.tenda, pb.tenpb, da.ngaybatdau, da.ngayhoanthanh, da.trangthai 
        FROM duan da
        JOIN phongban pb ON da.phongban = pb.mapb
        WHERE da.mada = ? OR da.tenda LIKE ?";
$stmt = $conn->prepare($sql);

// Chuẩn bị tham số để truyền vào
$mada = intval($tenduan); // Chuyển thành số nguyên
$searchTerm = "%" . $tenduan . "%"; // Thêm ký tự `%` để tìm kiếm chính xác hơn

$stmt->bind_param("is", $mada, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$duanData = [];

// Lấy dữ liệu từ kết quả truy vấn
while ($row = $result->fetch_assoc()) {
    // Định dạng lại ngày bắt đầu và ngày hoàn thành sang DD/MM/YYYY
    $ngaybatdau = date('d/m/Y', strtotime($row['ngaybatdau']));
    $ngayhoanthanh = date('d/m/Y', strtotime($row['ngayhoanthanh']));
    
    $duanData[] = [
        'mada' => $row['mada'],
        'tenda' => $row['tenda'],
        'tenpb' => $row['tenpb'],
        'ngaybatdau' => $ngaybatdau,
        'ngayhoanthanh' => $ngayhoanthanh,
        'trangthai' => $row['trangthai'],
    ];
}

// Trả về kết quả dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($duanData);

// Đóng kết nối
$stmt->close();
$conn->close();
?>
