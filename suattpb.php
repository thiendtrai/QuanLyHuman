<?php
include 'ketnoi.php';

 
$data = json_decode(file_get_contents('php://input'), true);

 
$mapb = $data['mapb'];
$tenpb = $data['tenpb'];
$mota = $data['mota'];
$ngaytao = $data['ngaytao'];


 
if (!empty($mapb) && !empty($tenpb) && !empty($ngaytao)) {
     $sql = "UPDATE phongban SET tenpb = ?, mota = ?, ngaytao = ? WHERE mapb = ?";
    $stmt = $conn->prepare($sql);

     $stmt->bind_param("ssss", $tenpb, $mota, $ngaytao,  $mapb);

     if ($stmt->execute()) {
         echo json_encode(["status" => "success", "message" => "Cập nhật thành công"]);
    } else {
         echo json_encode(["status" => "error", "message" => "Lỗi: " . $stmt->error]);
    }

     $stmt->close();
} else {
     echo json_encode(["status" => "error", "message" => "Vui lòng điền đầy đủ thông tin"]);
}

 $conn->close();
?>
