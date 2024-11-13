<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'ketnoi.php';  

   
    $manv = $_POST['manv'];
    $mapb = $_POST['mapb'];

    $sql = "UPDATE nhanvien SET mapb = ? WHERE manv = ?";
    
     $stmt = $conn->prepare($sql);

     $stmt->bind_param('ss', $mapb, $manv);

     if ($stmt->execute()) {
        echo "<script>
                alert('Chuyển phòng ban thành công.');
                  localStorage.setItem('reload', 'true');
                 window.history.go(-1);
              </script>";
    } else {
        echo "<script>
                alert('Lỗi khi chuyển phòng ban.');
                window.history.go(-1); 
              </script>";
    }

     $stmt->close();
    $conn->close();
}
?>
