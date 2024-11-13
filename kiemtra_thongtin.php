<?php
include 'ketnoi.php';

$email = $_POST['email'];
$sdt = $_POST['sdt'];
$cccd = $_POST['cccd'];
$manv = $_POST['manv'];

$sql = "SELECT COUNT(*) as count FROM nhanvien WHERE (email = '$email' OR sdt = '$sdt' OR cccd = '$cccd') AND manv != '$manv'";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['exists' => $row['count'] > 0]);
} else {
    echo json_encode(['exists' => false, 'error' => 'Database query failed']);
}

$conn->close();
?>
