<?php
include 'ketnoi.php';
$sdt = $_POST['txtsdt'];
$pass = $_POST['txtpass'];
if (!empty($sdt) && !empty($pass)){
    $sql = "SELECT * FROM taikhoan where sdt = '$sdt' and matkhau = '$pass'";
    $result = $conn->query(query: $sql);
    if ($result->num_rows > 0) {
       
        while ($row = $result->fetch_assoc()) {
            if($row['trangthai']==='Đã Nghỉ Việc'){
                echo "<script>
                alert('Tài Khoản Của Bạn Đã Bị Khóa!');
                window.history.go(-1);
            </script>";
            die();
            }
            if($row['chucvu'] === 'Giám Đốc'){
                header("Location: formthemnv(GD).php?sdt1=" .$sdt);           
                      die();
            }
            if($row['chucvu']==='Trưởng Phòng'){
                header("Location: formdsnhanvien(TP).php?sdt1=" .$sdt);
                die();
            }
            if($row['chucvu']==='Nhân Viên'){
                header("Location: formtrangchu(NV).php?sdt1=" .$sdt);
                die();
            }
        }
        
    }else{
        echo "<script>
        alert('Tên Đăng Nhập Hoặc Mật Khẩu Không Đúng!');
        window.history.go(-1);
    </script>";
    } 
}
else{
    echo "<script>
        alert('Hãy Nhập Đủ Thông Tin');
        window.history.go(-1);
    </script>";
}

?>