<?php
include 'ketnoi.php';
header(header: 'Content-Type: application/json');

$sql = "SELECT * FROM phongban where trangthai != 'locked'";
$result = $conn->query(query: $sql);

$chucvus = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $chucvus[] = ['value' => $row['mapb'], 'name' => $row['tenpb']];
    }
}

$conn->close();

echo json_encode($chucvus);
?>
