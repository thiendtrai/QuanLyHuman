<?php
include 'ketnoi.php';

// Retrieve data from POST request
$tenpb = isset($_POST['tenphongban']) ? trim($_POST['tenphongban']) : '';
$mota = isset($_POST['mota']) ? trim($_POST['mota']) : ''; 

// Get current date
$ngaytao = date('Y-m-d');

if (!empty($tenpb)) {
    // Prepare SQL query to insert the data
    $sql = "INSERT INTO `phongban` (`tenpb`, `mota`, `ngaytao`, `trangthai`) VALUES (?, ?, ?, 'on')";

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $tenpb, $mota, $ngaytao);

    if ($stmt->execute()) {
        // Return success response
        echo json_encode(["status" => "success", "message" => "Thêm Phòng Ban Thành Công"]);
    } else {
        // Return error response
        echo json_encode(["status" => "error", "message" => "Lỗi: " . $conn->error]);
    }

    // Close statement
    $stmt->close();
} else {
    // Return error response if required fields are missing
    echo json_encode(["status" => "error", "message" => "Hãy Nhập Đủ Thông Tin"]);
}

// Close the database connection
$conn->close();
?>
