<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Nhân Viên</title>
</head>

<body>
    <form action="themnhanvien(GD).php" id="formthemnv" method="POST" enctype="multipart/form-data">
        <?php
        include 'ketnoi.php';
        $sql = "SELECT manv FROM nhanvien ORDER BY manv DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $manv = $row['manv'] + 1;
            }
        } else {
            $manv = 1;
        }

        $conn->close();
        ?>
        Mã Nhân Viên:<br>
        <input type="text" name="txtmanv" id="manv" value="<?php echo $manv; ?>" readonly><br>
        Họ Và Tên:<br>
        <input type="text" name="txthoten" placeholder="Họ Và Tên" required><br>
        Ngày Sinh:<br>
        <input type="date" name="txtngaysinh" id="ngaysinh" required><br>
        Giới Tính:<br>
        <select name="txtgioitinh" id="gioitinh" required>
            <option value="" disabled selected>Chọn Giới Tính</option>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Khác">Khác</option>
        </select><br>
        Quê Quán:<br>
        <input type="text" name="txtquequan" placeholder="Quê Quán" required><br>
        Ngày Vào Làm:<br>
        <input type="date" name="txtngayvaolam" id="ngayvaolam" required><br>
        Nơi Sinh:<br>
        <input type="text" name="txtnoisinh" placeholder="Nơi Sinh" required><br>
        Số Điện Thoại:<br>
        <input type="text" name="txtsdt" placeholder="Số Điện Thoại" id="sdt" required><br>
        Địa Chỉ Hiện Tại:<br>
        <input type="text" name="txtdiachi" placeholder="Địa Chỉ Hiện Tại" required><br>
        Lương Cơ Bản:<br>
        <input type="text" name="txtluong" placeholder="Lương Cơ Bản" required><br>
        Căn Cước Công Dân:<br>
        <input type="text" name="txtcccd" placeholder="Căn Cước Công Dân" id="cccd" required><br>
        Ngày Cấp:<br>
        <input type="date" name="txtngaycap" id="ngaycap" required><br>
        Nơi Cấp:<br>
        <input type="text" name="txtnoicap" placeholder="Nơi Cấp" required><br>
        Chức Vụ:<br>
        <select name="txtchucvu" id="chucvu" required>
            <option value="" disabled selected>Chọn Chức Vụ</option>
            <option value="Nhân Viên">Nhân Viên</option>
            <option value="Trưởng Phòng">Trưởng Phòng</option>
        </select><br>
        Địa Chỉ Email:<br>
        <input type="email" name="txtemail" placeholder="Địa Chỉ Email" id="email" required><br>
        Hình Ảnh:<br>
        <input type="file" name="filehinhanh" placeholder="Hình Ảnh" id="filehinhanh" accept="image/*" required><br>
        <input type="hidden" name="filehinhanhhidden" id="filehinhanhhidden">
        Phòng Ban:<br>
        <select name="txtphongban" id="phongban" required>
            <option value="" disabled selected>Chọn Phòng Ban</option>
        </select><br>
        <button name="uploadButton" type="button" id="uploadButton">THÊM</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
             
            fetch('get_phongban.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('phongban');
                    data.forEach(chucvu => {
                        const option = document.createElement('option');
                        option.value = chucvu.value;
                        option.textContent = chucvu.name;
                        select.appendChild(option);
                    });
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu:', error));

                document.getElementById('uploadButton').addEventListener('click', function (event) {
            if (validateForm()) {
                checkExistenceAndSubmit();
            } else {
                event.preventDefault();
            }
        });

        function checkExistenceAndSubmit() {
            const email = document.getElementById('email').value;
            const sdt = document.getElementById('sdt').value;
            const cccd = document.getElementById('cccd').value;
            const phongban = document.getElementById('phongban').value;
            const chucvu = document.getElementById('chucvu').value;

          
            fetch('check_existence.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, sdt, cccd }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    alert('Email, Số điện thoại hoặc CCCD đã tồn tại trong CSDL.');
                } else{
                    if (chucvu === "Trưởng Phòng") {
              
                    fetch('check_truongphong.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ phongban }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.hasTruongPhong) {
                            alert('Phòng ban này đã có trưởng phòng.');
                        } else {
                            submitForm();
                        }
                    });
                } else {
                    submitForm();
                }
                }
            });
        }

        function submitForm() {
            uploadImage();  // Gọi hàm upload ảnh
        }

           
function uploadImage() {

var formData = new FormData();
formData.append("image", document.getElementById("filehinhanh").files[0]);

fetch('https://api.imgbb.com/1/upload?key=4492cd17ce112885a8956a72bf05b2a3', {
    method: 'POST',
    body: formData,
})
.then(response => response.json())
.then(data => {
 
    var imageUrl = data.data.url;


    saveImageToDatabase(imageUrl);
})
.catch(error => {
    console.error('Lỗi:', error);
});

}

function saveImageToDatabase(idh) {
var xhr = new XMLHttpRequest();
xhr.open("POST", "themnhanvien(GD).php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.onreadystatechange = function() {
if (this.readyState === XMLHttpRequest.DONE) {
if (this.status === 200) {
    console.log("Ảnh đã được lưu trữ vào cơ sở dữ liệu.");
    


document.getElementById("filehinhanhhidden").value = idh;

    document.getElementById("formthemnv").submit();
    
} else {
    
    console.error("Lỗi khi lưu trữ ảnh vào cơ sở dữ liệu.");
}
}
};
xhr.send("idh=" + idh);
}

            
            function validateField(field, message) {
                if (field.value.trim() === '' || field.value === null) {
                    field.setCustomValidity(message);
                    field.reportValidity();
                } else {
                    field.setCustomValidity('');
                }
            }

            // Xác thực form
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
                    { selector: 'input[name="txtngaysinh"]', message: 'Hãy chọn ngày sinh' }
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

        // Xử lý lại trang
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || localStorage.getItem('reload') === 'true') {
                localStorage.removeItem('reload');
                location.reload();
            }
        });
    </script>
</body>

</html>
