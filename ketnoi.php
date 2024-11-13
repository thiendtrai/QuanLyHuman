<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "quanlynhansu";

$conn = new mysqli(hostname: $host, username: $username, password: $password, database: $dbname);


if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

?>