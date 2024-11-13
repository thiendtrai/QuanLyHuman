
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Dự Án</title>
</head>
<body>
<form action="themduan(GD).php" id="formthemduan" method="post">
    <?php
    include 'ketnoi.php'; 
    $sql = "SELECT mada FROM duan ORDER BY mada DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $mada = $row['mada'] + 1;
        }
    } else {
        $mada = 1;
    }

    $conn->close();
    ?>
    Mã Dự Án:<br>
    <input type="text" name="txtmada" value="<?php echo $mada; ?>" readonly><br>
    Tên Dự Án: <br>
    <input type="text" name="txttendean" placeholder="Nhập Tên Dự Án"><br>
    Ngày Bắt Đầu: <br>
    <input type="date" name="txtngaybatdau" ><br>
    Ngày Kết Thúc: <br>
    <input type="date" name="txtngayketthuc"><br>
    <span id="error-ngayketthuc" style="color: red; display: none;"></span>  
    <?php
    $sdt1 = $_GET['sdt1'];
    include 'ketnoi.php'; 
    $sql = "SELECT nv.mapb as ma, pb.tenpb as ten FROM phongban pb, nhanvien nv where nv.mapb = pb.mapb
    and nv.sdt = $sdt1 limit 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tenpb = $row['ten'];
            $mapb = $row['ma'];
        }
    }

    $conn->close();
    ?>
    Phòng Ban: <br>
    <select name="txtphongban" id="phongban">
        <option value="" disabled selected>Chọn Phòng Ban</option>
        <option value="<?php echo $mapb; ?>"> <?php echo $tenpb; ?></option>
    </select><br>

    
    <input type="hidden" name="txtphongban" id="txtphongban" value="<?php echo $mapb; ?>">
    <input type="hidden" name="manv_list" id="manv_list">
   
   

    Nhân Viên: <br>
    <select name="txtnhanvien" id="nhanvien">
        <option value="" disabled selected>Chọn Nhân Viên</option>
    </select>
    <button type="button" id="btnnhanvien" name="btnnhanvien">Chọn</button><br>


    <table id="nhanvienTable">
        <thead>
            <tr>
            <th>Mã Nhân Viên</th>
                <th>Hình Ảnh</th>
                <th>Họ Tên</th>
                <th>Ngày Sinh</th>
                <th>Giới Tính</th>
                <th>Chức Vụ</th>
                <th>Email</th>
                <th>Hành Động</th>
                
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    Leader: <br>
    <select name="txtleader" id="leader">
        <option value="" disabled selected>Chọn Nhân Viên</option>
    </select><br>

    Ghi Chú:<br>
    <input type="text" name="txtghichu" id="txtghichu" placeholder="Điền Ghi Chú"><br>

    File Mô Tả:
    <input type="input" id="filemota" name="filemota"  ><br>

 
    <button type="button" id="btnthemduan" name="btnthemduan">Thêm Dự Án</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addedNhanVienIds = new Set(); 
    const leaderSelect = document.getElementById('leader');
    const phongbanSelect = document.getElementById('phongban'); 
    let phongbanSelectedValue = null;  

    

        function disableOtherOptions() {
        for (let i = 0; i < phongbanSelect.options.length; i++) {
            if (phongbanSelect.options[i].value !== phongbanSelectedValue) {
                phongbanSelect.options[i].disabled = true;   
            }
        }
    }

    
    function checkTableData() {
        const table = document.getElementById('nhanvienTable').getElementsByTagName('tbody')[0];
        if (table.rows.length > 0) {
            phongbanSelectedValue = phongbanSelect.value;   
            disableOtherOptions();   
        } else {
            enableAllOptions();   
        }
    }

    
    function enableAllOptions() {
        for (let i = 0; i < phongbanSelect.options.length; i++) {
            phongbanSelect.options[i].disabled = false;  
        }
    }
    phongbanSelect.addEventListener('change', function() {
        const phongbanId = this.value;
        const nhanvienSelect = document.getElementById('nhanvien');
        nhanvienSelect.innerHTML = '<option value="" disabled selected>Chọn Nhân Viên</option>'; 

        if (phongbanId) {
            fetch(`get_nhanvien.php?phongban=${phongbanId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    data.forEach(nhanvien => {
                        const option = document.createElement('option');
                        option.value = nhanvien.value;
                        option.textContent = nhanvien.name;
                        nhanvienSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu nhân viên:', error));
        }
    });

    document.getElementById('btnnhanvien').addEventListener('click', function() {
        const selectedNhanVienId = document.getElementById('nhanvien').value;

        if (selectedNhanVienId) {
            fetch(`get_nhanvien_detail.php?manhanvien=${selectedNhanVienId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(nhanvienInfo => {
                    const table = document.getElementById('nhanvienTable').getElementsByTagName('tbody')[0];
                    const row = table.insertRow();
                    
                    const cell0 = row.insertCell(0);  
                    const cell1 = row.insertCell(1); 
                    const cell2 = row.insertCell(2);  
                    const cell3 = row.insertCell(3); 
                    const cell4 = row.insertCell(4); 
                    const cell5 = row.insertCell(5); 
                    const cell6 = row.insertCell(6);  
                    const cell7 = row.insertCell(7);  

                    cell0.textContent = nhanvienInfo.manv; 
                    cell1.innerHTML = `<img src="${nhanvienInfo.hinhanh}" alt="Hình ảnh" width="50">`; 
                    cell2.textContent = nhanvienInfo.hoten; 
                    cell3.textContent = nhanvienInfo.ngaysinh;
                    cell4.textContent = nhanvienInfo.gioitinh; 
                    cell5.textContent = nhanvienInfo.chucvu; 
                    cell6.textContent = nhanvienInfo.email; 

                    const deleteBtn = document.createElement('button');
                    deleteBtn.textContent = 'Xóa';
                    deleteBtn.addEventListener('click', function() {
                        row.remove();  
                        addedNhanVienIds.delete(selectedNhanVienId); 
                        updateNhanVienSelect();  
                        updateLeaderSelect(); 
                        checkTableData();
                    });
                    cell7.appendChild(deleteBtn);

                    addedNhanVienIds.add(selectedNhanVienId);
                    document.getElementById('nhanvien').selectedIndex = 0; 
                    updateNhanVienSelect();
                    updateLeaderSelect();
                    checkTableData();
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu chi tiết nhân viên:', error));
        }
    });

    
    checkTableData();

   
    function updateNhanVienSelect() {
        const nhanvienSelect = document.getElementById('nhanvien');
        const options = nhanvienSelect.options;

        for (let i = 1; i < options.length; i++) { 
            options[i].disabled = addedNhanVienIds.has(options[i].value); 
        }
    }

    function updateLeaderSelect() {
        leaderSelect.innerHTML = '<option value="" disabled selected>Chọn Nhân Viên</option>'; 
        addedNhanVienIds.forEach(nhanvienId => {
            fetch(`get_nhanvien_detail.php?manhanvien=${nhanvienId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(nhanvienInfo => {
                    const option = document.createElement('option');
                    option.value = nhanvienInfo.manv;
                    option.textContent = nhanvienInfo.hoten + ' [' + nhanvienInfo.manv + ']';
                    leaderSelect.appendChild(option);
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu chi tiết nhân viên:', error));
        });
    }

    
 


 
const form = document.getElementById('formthemduan');
    const fields = [
        { selector: 'select[name="txtleader"]', message: 'Hãy chọn Leader' },
        { selector: 'select[name="txtphongban"]', message: 'Hãy chọn Phòng Ban' },
        { selector: 'input[name="txtngayketthuc"]', message: 'Hãy chọn Ngày Kết Thúc' },
        { selector: 'input[name="txtngaybatdau"]', message: 'Hãy chọn Ngày Bắt Đầu' },
        { selector: 'input[name="txttendean"]', message: 'Hãy nhập Tên Dự Án' },
      
      
        
        
    ];

  
    fields.forEach(field => {
        const element = document.querySelector(field.selector);
        if (element) {
            element.addEventListener('input', () => validateField(element, field.message));
            element.addEventListener('change', () => validateField(element, field.message));
        }
    });

 
    document.getElementById('btnthemduan').addEventListener('click', function(event) {
     
        if (!validateForm()) {
            event.preventDefault();
        } else {
            const manvList = Array.from(addedNhanVienIds).join(',');
            document.getElementById('manv_list').value = manvList;
            form.submit();
        }
    });
 
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

    fields.forEach(field => {
        const element = document.querySelector(field.selector);
        if (element && (element.value.trim() === '' || element.value === null)) {
            validateField(element, field.message);
            isValid = false;
        }
    });

     
    const ngayBatDau = document.querySelector('input[name="txtngaybatdau"]');
    const ngayKetThuc = document.querySelector('input[name="txtngayketthuc"]');
    const errorNgayKetThuc = document.getElementById('error-ngayketthuc');  

    if (ngayBatDau.value && ngayKetThuc.value) {
        if (new Date(ngayKetThuc.value) < new Date(ngayBatDau.value)) {
            errorNgayKetThuc.textContent = 'Ngày kết thúc không được nhỏ hơn ngày bắt đầu.';
            errorNgayKetThuc.style.display = 'block';  
            isValid = false;
        } else {
            errorNgayKetThuc.textContent = '';  
            errorNgayKetThuc.style.display = 'none'; 
        }
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
</body>
</html>

<style>
table, th, td {
  border:1px solid black;
}
</style>
