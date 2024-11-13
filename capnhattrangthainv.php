<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'ketnoi.php';

    // Lấy dữ liệu từ POST
    $manv = $_POST['manv'];

    // Kiểm tra xem nhân viên có tồn tại không
    $sql = "SELECT * FROM nhanvien WHERE manv = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $manv);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['trangthai'] == 'Đang Làm Việc') {
            
                $trangthai = 'Đã Nghỉ Việc';  
                $update_sql = "UPDATE nhanvien SET trangthai = ? WHERE manv = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('ss', $trangthai, $manv);

                if ($update_stmt->execute()) {
                    $update_sql = "UPDATE taikhoan SET trangthai = ? WHERE manv = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('ss', $trangthai, $manv);

                if ($update_stmt->execute()) {
                    echo "<script>
                    alert('Cập Nhật Nhân Viên Thành Công!');
               
                    window.history.go(-1); 
                </script>";
                }
                } else {
                    echo "<script>
                    alert('Cập Nhật Thất Bại!');
                    window.history.go(-1); 
                </script>";
                }

                $update_stmt->close();
            }
             if($row['trangthai'] == 'Đã Nghỉ Việc') {
                $trangthai = 'Đang Làm Việc';  
                $update_sql = "UPDATE nhanvien SET trangthai = ? WHERE manv = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('ss', $trangthai, $manv);

                if ($update_stmt->execute()) {
                    $update_sql = "UPDATE taikhoan SET trangthai = ? WHERE manv = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('ss', $trangthai, $manv);

                if ($update_stmt->execute()) {
                    echo "<script>
                    alert('Cập Nhật Nhân Viên Thành Công!');
               
                    window.history.go(-1); 
                </script>";
                }
                } else {
                    echo "<script>
                    alert('Cập Nhật Thất Bại!');
                    window.history.go(-1); 
                </script>";
                }

                $update_stmt->close();
            }
        }
    } else {
        echo "<script>
                    alert('Không Thể Tìm Thấy Nhân Viên');
                    window.history.go(-1); 
                </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
