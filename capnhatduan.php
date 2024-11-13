<?php
include 'ketnoi.php';

// Thiết lập header cho JSON
header('Content-Type: application/json');

// Lấy dữ liệu từ fetch và giải mã JSON
$data = json_decode(file_get_contents('php://input'), true);

$tenda = $data['tenda'];
$ngaybatdau = $data['ngaybatdau'];
$ngayhoanthanh = $data['ngayhoanthanh'];
$phongban = $data['phongban'];
$nhanvienArray = $data['nhanvien'];
$leader = $data['leader'];
$ghichu = $data['ghichu'];
$filemota = $data['filemota'];
$mada = $data['mada']; // Lấy mã dự án từ dữ liệu JSON


date_default_timezone_set(timezoneId: 'Asia/Ho_Chi_Minh');
$currentDate = new DateTime();
$startDate = new DateTime(datetime: $ngaybatdau);
$endDate = new DateTime(datetime: $ngayhoanthanh);


if ($startDate > $currentDate) {
    $trangthai = "Sắp thực hiện";
} 
else{
    $trangthai = "Đang thực hiện";
}
if($endDate < $currentDate) {
    $trangthai = "Đã hoàn thành";
}


// Cập nhật bảng dự án
$sqlUpdateDuan = "UPDATE duan SET tenda = ?, ngaybatdau = ?, ngayhoanthanh = ?, phongban = ?, leader = ?, ghichu = ?, filemota = ?, trangthai = ? WHERE mada = ?";
$stmt = $conn->prepare($sqlUpdateDuan);
$stmt->bind_param('ssssssssi', $tenda, $ngaybatdau, $ngayhoanthanh, $phongban, $leader, $ghichu, $filemota,$trangthai, $mada);

if ($stmt->execute()) {
    // Xóa danh sách nhân viên hiện tại trong dự án
    $sqlDeleteNhanvien = "DELETE FROM ctduan WHERE mada = ?";
    $stmtDelete = $conn->prepare($sqlDeleteNhanvien);
    $stmtDelete->bind_param('i', $mada);
    $stmtDelete->execute();
    $stmtDelete->close();

    // Thêm lại danh sách nhân viên mới vào dự án
    $sqlInsertNhanvien = "INSERT INTO ctduan (mada, manv) VALUES (?, ?)";
    $stmtInsert = $conn->prepare($sqlInsertNhanvien);

    foreach ($nhanvienArray as $manv) {
        $stmtInsert->bind_param('is', $mada, $manv);
        $stmtInsert->execute();
    }

    $stmtInsert->close();
    echo json_encode(['success' => true, 'message' => 'Dự án đã được cập nhật thành công.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật dự án.']);
}

$stmt->close();
$conn->close();
?>
