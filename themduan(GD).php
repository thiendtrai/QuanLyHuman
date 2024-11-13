<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'ketnoi.php';
    $tendean = $_POST['txttendean'];
    $ngaybatdau = $_POST['txtngaybatdau'];
    $ngayketthuc = $_POST['txtngayketthuc'];
    $phongban = $_POST['txtphongban'];
    $manv_list = $_POST['manv_list']; 
    $leader = $_POST['txtleader'];
    $ghichu= $_POST['txtghichu'];
    $filemota = $_POST['filemota'];

    if (!empty($tendean) && !empty($ngaybatdau) && !empty($ngayketthuc) && !empty($phongban) && !empty($manv_list) && !empty($leader)) {
        
        date_default_timezone_set(timezoneId: 'Asia/Ho_Chi_Minh');
        $currentDate = new DateTime();
        $startDate = new DateTime(datetime: $ngaybatdau);
        $endDate = new DateTime(datetime: $ngayketthuc);

        if($endDate < $startDate){
            echo "<script>
            alert('Ngày Kết Thúc Không Được Nhỏ Hơn Ngày Bắt Đầu');
           
        window.history.go(-1);
        </script>";
        die();
        }

       
        if ($startDate > $currentDate) {
            $trangthai = "Sắp thực hiện";
        } 
        else{
            $trangthai = "Đang thực hiện";
        }
        if($endDate < $currentDate) {
            $trangthai = "Đã hoàn thành";
        }
        


        
  
        $conn->begin_transaction();
        try {
            
            $sql = "INSERT INTO `duan` (`tenda`, `ngaybatdau`, `ngayhoanthanh`, `phongban`, `leader`, `trangthai`, `ghichu`, `filemota`) 
                    VALUES ('$tendean', '$ngaybatdau', '$ngayketthuc', '$phongban', '$leader', '$trangthai', '$ghichu', '$filemota')";
            if ($conn->query($sql) === true) {
                
                $mada1 = $conn->insert_id;

             $manv_array = explode(',', $manv_list);
                foreach ($manv_array as $manv) {
                    $sql = "INSERT INTO `ctduan` (`manv`, `mada`) VALUES ('$manv', '$mada1')";
                    if (!$conn->query($sql)) {
                        throw new Exception("Lỗi khi thêm nhân viên vào dự án");
                    }
                }

              
                $conn->commit();
                echo "<script>
                        alert('Thêm Dự Án Thành Công!');
                        localStorage.setItem('reload', 'true');
                        window.history.go(-1);
                    
                    </script>";
            } else {
                throw new Exception("Lỗi khi thêm dự án");
            }
        } catch (Exception $e) {
             $conn->rollback();
            echo "Lỗi: " . $e->getMessage();
        }
    } else {
        echo "<script>
            alert('Hãy Nhập Đủ Thông Tin!');
        window.history.go(-1);
                        
        </script>";
    }
}
?>
