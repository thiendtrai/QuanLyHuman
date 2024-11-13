<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'ketnoi.php';  

    $manv = $_POST['manv']; // The employee ID to be set as "Trưởng Phòng"
    $mapb = $_POST['mapb']; // The department ID

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Step 1: Update all employees in the specified department to 'Nhân Viên'
        $sql = "UPDATE nhanvien SET chucvu = 'Nhân Viên' WHERE mapb = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $mapb);
        
        // Execute Step 1
        if ($stmt->execute()) {
            // Step 2: Update the specified employee to 'Trưởng Phòng'
            $sql = "UPDATE nhanvien SET chucvu = 'Trưởng Phòng' WHERE mapb = ? AND manv = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $mapb, $manv);
            
            // Execute Step 2
            if ($stmt->execute()) {
                // Step 3: Update the `taikhoan` table for all employees in the department
                $sql = "SELECT manv FROM nhanvien WHERE mapb = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $mapb);
                $stmt->execute();
                $result = $stmt->get_result();

                // Update `taikhoan` for all employees in the department
                while ($row = $result->fetch_assoc()) {
                    $manv_emp = $row['manv'];
                    $sql = "UPDATE taikhoan SET chucvu = 'Nhân Viên' WHERE manv = ?";
                    $stmt_taikhoan = $conn->prepare($sql);
                    $stmt_taikhoan->bind_param('s', $manv_emp);
                    if (!$stmt_taikhoan->execute()) {
                        throw new Exception('Lỗi khi cập nhật tài khoản cho nhân viên.');
                    }
                }

                
                $sql = "UPDATE taikhoan SET chucvu = 'Trưởng Phòng' WHERE  manv = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $manv);
                if ($stmt->execute()) {
                    $conn->commit();
                    echo "<script>
                        alert('Cập nhật thành công.');
                        localStorage.setItem('reload', 'true');
                        window.history.go(-1);
                    </script>";
                }

               

            } else {
                throw new Exception('Lỗi khi cập nhật trưởng phòng.');
            }

        } else {
            throw new Exception('Lỗi khi cập nhật trạng thái nhân viên.');
        }

    } catch (Exception $e) {
        // If there is an error, roll back the transaction
        $conn->rollback();
        echo "<script>
            alert('".$e->getMessage()."');
            window.history.go(-1);
        </script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
