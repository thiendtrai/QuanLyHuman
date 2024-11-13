<?php
include 'ketnoi.php'; 

if (isset($_GET['phongban'])) {
    $phongbanId = $_GET['phongban'];

    // Lấy danh sách mã nhân viên đã có
    $existingIds = isset($_GET['existingIds']) ? explode(',', $_GET['existingIds']) : [];

    // Tạo điều kiện cho danh sách existingIds
    $placeholders = implode(',', array_fill(0, count($existingIds), '?'));
    
    $sql = "SELECT manv, hoten FROM nhanvien WHERE mapb = ? AND trangthai = 'Đang Làm Việc'";
    
    // Chỉ thêm điều kiện cho existingIds nếu có
    if (!empty($existingIds)) {
        $sql .= " AND manv NOT IN ($placeholders)";
    }

    $stmt = $conn->prepare($sql);
    
    // Kết hợp phongbanId và existingIds để bind tham số
    if (!empty($existingIds)) {
        $stmt->bind_param(str_repeat('s', count($existingIds) + 1), $phongbanId, ...$existingIds);
    } else {
        $stmt->bind_param('s', $phongbanId);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $nhanvienList = [];

        while ($row = $result->fetch_assoc()) {
            $nhanvienList[] = [
                'value' => $row['manv'], 
                'name' => $row['hoten'] . ' [' . $row['manv'] . ']' 
            ];
        }

        echo json_encode($nhanvienList);
    } else {
        echo json_encode([]);
    }

    $stmt->close();
} else {
    echo json_encode([]);
}

$conn->close();
?>
