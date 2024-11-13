<?php
include 'ketnoi.php'; 


if (isset($_GET['phongban'])) {
    $phongbanId = $_GET['phongban'];

    
    $sql = "SELECT manv, hoten FROM nhanvien WHERE mapb = ? and trangthai='Đang Làm Việc'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $phongbanId); 

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