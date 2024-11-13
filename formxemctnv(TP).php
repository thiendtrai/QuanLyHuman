<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    include 'ketnoi.php';

    $manv = $_POST['manv'];
    $sql = "SELECT * FROM nhanvien nv, phongban pb WHERE nv.mapb = pb.mapb and manv = '$manv'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        
        $row['ngaysinh'] = date("Y-m-d", strtotime($row['ngaysinh']));
        $row['ngayvaolam'] = date("Y-m-d", strtotime($row['ngayvaolam']));
        $row['ngaycap'] = date("Y-m-d", strtotime($row['ngaycap']));
    }

    $conn->close();
    ?>
    <title>Mã Nhân Viên: <?php echo $manv; ?></title>
</head>
<body>
    <form action="suanhanvien(GD).php" id="formthemnv" method="POST">
        Mã Nhân Viên:<br>
        <input type="text" name="txtmanv" value="<?php echo $manv; ?>" id="txtmanv" readonly><br>
        
        Họ Và Tên:<br>
        <input type="text" name="txthoten" placeholder="Họ Và Tên" value="<?php echo  $row['hoten']  ?>"><br>
        
        Ngày Sinh:<br>
        <input type="date" name="txtngaysinh" id="ngaysinh" value="<?php echo  $row['ngaysinh']  ?>"><br>
        
        Giới Tính:<br>
        <select name="txtgioitinh" id="gioitinh">
            <option value="" disabled>Chọn Giới Tính</option>
            <option value="Nam" <?php echo (isset($row['gioitinh']) && $row['gioitinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
            <option value="Nữ" <?php echo (isset($row['gioitinh']) && $row['gioitinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
            <option value="Khác" <?php echo (isset($row['gioitinh']) && $row['gioitinh'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
        </select><br>
        
        Quê Quán:<br>
        <input type="text" name="txtquequan" placeholder="Quê Quán" value="<?php echo  $row['quequan']  ?>"><br>
        
        Ngày Vào Làm:<br>
        <input type="date" name="txtngayvaolam" id="ngayvaolam"  value="<?php echo $row['ngayvaolam']  ?>"><br>
        
        Nơi Sinh:<br>
        <input type="text" name="txtnoisinh" placeholder="Nơi Sinh" value="<?php echo  $row['noisinh'] ?>"><br>
        
        Số Điện Thoại:<br>
        <input type="text" name="txtsdt" placeholder="Số Điện Thoại" id="sdt" value="<?php echo $row['sdt'] ?>"><br>
        
        Địa Chỉ Hiện Tại:<br>
        <input type="text" name="txtdiachi" placeholder="Địa Chỉ Hiện Tại" value="<?php echo $row['diachi'] ?>"><br>
        
        Lương Cơ Bản:<br>
        <input type="text" name="txtluong" placeholder="Lương Cơ Bản" value="<?php echo  $row['luong']  ?>"><br>
        
        Căn Cước Công Dân:<br>
        <input type="text" name="txtcccd" placeholder="Căn Cước Công Dân" id="cccd" value="<?php echo  $row['cccd']  ?>"><br>
        
        Ngày Cấp:<br>
        <input type="date" name="txtngaycap" id="ngaycap" value="<?php echo  $row['ngaycap']  ?>"><br>
        
        Nơi Cấp:<br>
        <input type="text" name="txtnoicap" placeholder="Nơi Cấp" value="<?php echo $row['noicap']  ?>"><br>
        
        Chức Vụ:<br>
        <select name="txtchucvu" id="chucvu">
            <option value="<?php echo $row['chucvu']  ?>" ><?php echo $row['chucvu']  ?></option>
           
        </select><br>
        
        Địa Chỉ Email:<br>
        <input type="text" name="txtemail" placeholder="Địa Chỉ Email" id="email" value="<?php echo $row['email'] ?>"><br>
        
        Hình Ảnh:<br>
        <img src="<?php echo $row['hinhanh'] ?>" alt="Hình ảnh" id="hinhanh" name="hinhanh" width="100">
        <input type="hidden" name="filehinhanhhidden" id="filehinhanhhidden" value="<?php echo $row['hinhanh']  ?>"><br>
        
        Phòng Ban:<br>
        <select name="txtphongban" id="phongban">
        <option value="<?php echo $row['mapb']  ?>" ><?php echo $row['tenpb']  ?></option>
    </select><br>

        <button name="uploadButton" type="button" id="uploadButton">Chỉnh Sửa</button>
    </form>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
      



        document.getElementById('uploadButton').addEventListener('click', function (event) {
                if (!validateForm()) {
                    event.preventDefault();
                } else {
                    checkExistence(); 
                }
            });

            async function checkExistence() {
    const manv = document.getElementById('txtmanv').value;
    const email = document.getElementById('email').value;
    const sdt = document.getElementById('sdt').value;
    const cccd = document.getElementById('cccd').value;
    const phongban = document.getElementById('phongban').value;
    const chucvu = document.getElementById('chucvu').value;
    try {
        const response = await fetch('kiemtra_thongtin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `email=${encodeURIComponent(email)}&sdt=${encodeURIComponent(sdt)}&cccd=${encodeURIComponent(cccd)}&manv=${encodeURIComponent(manv)}`
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (data.exists) {
            alert('Email, số điện thoại hoặc căn cước công dân đã tồn tại.');
        } else {
            if (chucvu === "Trưởng Phòng") {
              
              fetch('check_truongphong1.php', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                  },
                  body: JSON.stringify({ phongban, manv }),
              })
              .then(response => response.json())
              .then(data => {
                  if (data.hasTruongPhong) {
                      alert('Phòng ban này đã có trưởng phòng.');
                  } else {
                    const userConfirmed = confirm("Bạn có chắc chắn muốn chỉnh sửa thông tin này không?");
        if (userConfirmed) {
            document.getElementById("formthemnv").submit();        
        }          
                       }
              });
          } else {
            const userConfirmed = confirm("Bạn có chắc chắn muốn chỉnh sửa thông tin này không?");
        if (userConfirmed) {
            document.getElementById("formthemnv").submit();        
        }          
          }
           
        }
    } catch (error) {
        console.error('Lỗi khi kiểm tra dữ liệu:', error);
        alert('Có lỗi xảy ra trong quá trình kiểm tra thông tin. Vui lòng thử lại sau.');
    }
}

            
            function validateField(field, message) {
                if (field.value.trim() === '' || field.value === null) {
                    field.setCustomValidity(message);
                    field.reportValidity();
                } else {
                    field.setCustomValidity('');
                }
            }

            function validateForm() {
                let isValid = true;

                const fields = [
                    { selector: 'select[name="txtphongban"]', message: 'Hãy chọn phòng ban' },
                    { selector: 'input[name="filehinhanh"]', message: 'Hãy thêm hình ảnh' },
                    { selector: 'input[name="txtemail"]', message: 'Hãy nhập địa chỉ email' },
                    { selector: 'select[name="txtchucvu"]', message: 'Hãy chọn chức vụ' },
                    { selector: 'input[name="txtnoicap"]', message: 'Hãy nhập nơi cấp' },
                    { selector: 'input[name="txtngaycap"]', message: 'Hãy chọn ngày cấp' },
                    { selector: 'input[name="txtcccd"]', message: 'Hãy nhập CCCD' },
                    { selector: 'input[name="txtluong"]', message: 'Hãy nhập lương cơ bản' },
                    { selector: 'input[name="txtdiachi"]', message: 'Hãy nhập địa chỉ hiện tại' },
                    { selector: 'input[name="txtsdt"]', message: 'Hãy nhập số điện thoại' },
                    { selector: 'input[name="txtnoisinh"]', message: 'Hãy nhập nơi sinh' },
                    { selector: 'input[name="txtngayvaolam"]', message: 'Hãy chọn ngày vào làm' },
                    { selector: 'input[name="txtquequan"]', message: 'Hãy nhập quê quán' },
                    { selector: 'select[name="txtgioitinh"]', message: 'Hãy chọn giới tính' },
                    { selector: 'input[name="txtngaysinh"]', message: 'Hãy chọn ngày sinh' },
                    { selector: 'input[name="txthoten"]', message: 'Hãy nhập họ tên' }
                ];

                fields.forEach(field => {
                    const element = document.querySelector(field.selector);
                    if (element && (element.value.trim() === '' || element.value === null)) {
                        validateField(element, field.message);
                        isValid = false;
                    }
                });

                if (!validateDateFields()) {
                    isValid = false;
                }

                return isValid;
            }

            function validateDateFields() {
    const today = new Date();
    today.setHours(today.getHours() + 7);
    const todayString = today.toISOString().split('T')[0];

    let isValid = true;

    const ngaySinh = document.getElementById('ngaysinh');
    if (ngaySinh && ngaySinh.value > todayString) {
        ngaySinh.setCustomValidity('Ngày sinh không được lớn hơn ngày hiện tại');
        ngaySinh.reportValidity();
        isValid = false;
    } else {
        ngaySinh.setCustomValidity('');
    }

    const ngayCap = document.getElementById('ngaycap');
    if (ngayCap && ngayCap.value > todayString) {
        ngayCap.setCustomValidity('Ngày cấp không được lớn hơn ngày hiện tại');
        ngayCap.reportValidity();
        isValid = false;
    } else if (ngayCap && ngaySinh && ngayCap.value < ngaySinh.value) {
        ngayCap.setCustomValidity('Ngày cấp không thể trước ngày sinh');
        ngayCap.reportValidity();
        isValid = false;
    } else {
        ngayCap.setCustomValidity('');
    }

    const ngayVaoLam = document.getElementById('ngayvaolam');
    if (ngayVaoLam && ngayVaoLam.value > todayString) {
        ngayVaoLam.setCustomValidity('Ngày vào làm không được lớn hơn ngày hiện tại');
        ngayVaoLam.reportValidity();
        isValid = false;
    } else if (ngayVaoLam && ngaySinh && ngayVaoLam.value < ngaySinh.value) {
        ngayVaoLam.setCustomValidity('Ngày vào làm không thể trước ngày sinh');
        ngayVaoLam.reportValidity();
        isValid = false;
    } else if (ngayVaoLam && ngayCap && ngayVaoLam.value < ngayCap.value) {
        ngayVaoLam.setCustomValidity('Ngày vào làm không thể trước ngày cấp');
        ngayVaoLam.reportValidity();
        isValid = false;
    } else {
        ngayVaoLam.setCustomValidity('');
    }

    return isValid;
}


    });

    window.addEventListener('pageshow', function(event) {
            if (event.persisted || localStorage.getItem('reload') === 'true') {
                localStorage.removeItem('reload');
                location.reload();
            }
        });
</script>
</html>
