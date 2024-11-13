<?php
include 'ketnoi.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data
    $manv = $_POST['txtmanv'];
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
    $phongban = $_POST['txtphongban'];
    $hinhanh = $_POST['filehinhanhhidden'];
    $chucvu = $_POST['txtchucvu'];

    // Check required fields
    if (!empty($hoten) && !empty($ngaysinh) && !empty($gioitinh) && !empty($quequan) && !empty($ngayvaolam) && !empty($noisinh) && !empty($sdt) && !empty($diachi) && !empty($luong) && !empty($phongban) && !empty($hinhanh) && !empty($cccd) && !empty($ngaycap) && !empty($noicap) && !empty($email)) {

        $conn->begin_transaction();
        
        try {
            // Prepare SQL for inserting employee details
            $stmt_nhanvien = $conn->prepare("INSERT INTO nhanvien (hoten, ngaysinh, gioitinh, quequan, ngayvaolam, noisinh, sdt, diachi, luong, mapb, hinhanh, trangthai, cccd, ngaycap, noicap, email, chucvu) 
                                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Đang Làm Việc', ?, ?, ?, ?, ?)");
            $stmt_nhanvien->bind_param("ssssssssssssssss", $hoten, $ngaysinh, $gioitinh, $quequan, $ngayvaolam, $noisinh, $sdt, $diachi, $luong, $phongban, $hinhanh, $cccd, $ngaycap, $noicap, $email, $chucvu);

            if ($stmt_nhanvien->execute()) {
                // Prepare SQL for creating user account
                $stmt_taikhoan = $conn->prepare("INSERT INTO taikhoan (sdt, matkhau, manv, trangthai, chucvu) VALUES (?, '1111', ?, 'Đang Làm Việc', ?)");
                $stmt_taikhoan->bind_param("sss", $sdt, $manv, $chucvu);

                if ($stmt_taikhoan->execute()) {
                    $conn->commit(); // Commit transaction
                    echo "<script>
                            alert('Thêm Nhân Viên Thành Công!');
                            localStorage.setItem('reload', 'true');
                            window.history.go(-1);
                          </script>";
                } else {
                    throw new Exception("Lỗi khi tạo tài khoản");
                }
            } else {
                throw new Exception("Lỗi khi thêm nhân viên");
            }
            
        } catch (Exception $e) {
            $conn->rollback(); // Rollback on error
            echo "Lỗi: " . $e->getMessage();
        }
        
        $stmt_nhanvien->close();
        $stmt_taikhoan->close();
        
    } else {
        echo "<script>
                alert('Hãy Nhập Đủ Thông Tin');
                window.history.go(-1);
              </script>";
    }
} else {
    echo "<script>
            alert('Yêu cầu không hợp lệ');
            window.history.go(-1);
          </script>";
}

$conn->close();
?>
