<?php
include 'ketnoi.php';
$hoten = $_POST['txthoten'];
$ngaysinh = $_POST['txtngaysinh'];
$gioitinh = $_POST['txtgioitinh'];
$quequan = $_POST['txtquequan'];
$ngayvaolam = $_POST['txtngayvaolam'];
$noisinh = $_POST['txtnoisinh'];
$sdt = $_POST['txtsdt'];
$diachi = $_POST['txtdiachi'];
$luong = $_POST['txtluong'];
$cccd = $_POST['txtcccd'];
$ngaycap = $_POST['txtngaycap'];
$noicap = $_POST['txtnoicap'];
$email = $_POST['txtemail'];
$hinhanh = $_POST['filehinhanhhidden'];
$sdt1 = $_POST['sdt1'];
$chucvu = 'Nhân Viên';
if (!empty($hoten) && !empty($ngaysinh) && !empty($gioitinh) && !empty($quequan) && !empty($ngayvaolam) && !empty($noisinh) && !empty($sdt) && !empty($diachi) && !empty($luong) &&  !empty($hinhanh) && !empty($cccd) && !empty($ngaycap) && !empty($noicap) && !empty($email)) {
    $sql = "SELECT * FROM nhanvien where sdt = '$sdt'";
    $result = $conn->query(query: $sql);

    $sql = "SELECT * FROM nhanvien where sdt = '$sdt1' ";
    $result = $conn->query(query: $sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $conn->begin_transaction();
            try {
                $sql = "INSERT INTO `nhanvien` (`hoten`, `ngaysinh`, `gioitinh`, `quequan`, `ngayvaolam`,`noisinh`,`sdt`, `diachi`, `luong`, `mapb`, `hinhanh`, `trangthai`, `cccd`, `ngaycap`,`noicap`,`email`,`chucvu`) 
                        VALUES ('$hoten', '$ngaysinh', '$gioitinh','$quequan', '$ngayvaolam','$noisinh', '$sdt', '$diachi', '$luong', '".$row['mapb']."', '$hinhanh', 'Đang Làm Việc', '$cccd', '$ngaycap','$noicap','$email','$chucvu')";
                if ($conn->query(query: $sql) === true) { 
                    
                    $sql = "INSERT INTO `taikhoan` (`sdt`, `matkhau`, `trangthai`, `chucvu`)
                            VALUES ('$sdt', '1111', 'Đang Làm Việc','$chucvu')";
                    if ($conn->query(query: $sql) === true) {
                        $conn->commit();
                        echo "<script>
                            alert('Thêm Nhân Viên Thành Công!');
                            localStorage.setItem('reload', 'true');
                            window.history.go(-1);
                        </script>";
                    } else {
                        throw new Exception(message: "Lỗi khi tạo tài khoản");
                    }
                } else {
                    throw new Exception(message: "Lỗi khi thêm nhân viên");
                }
            } catch (Exception $e) {
                $conn->rollback();
                echo "Lỗi: " . $e->getMessage();
            }
        }
        
    }

} else {
    echo "<script>
        alert('Hãy Nhập Đủ Thông Tin');
        window.history.go(-1);
    </script>";
}
$conn->close();
?>