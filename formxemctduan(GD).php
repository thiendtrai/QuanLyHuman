<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Dự Án</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <?php
    include 'ketnoi.php'; 
    $mada = $_POST['mada'];
    $sql = "SELECT da.mada,da.trangthai, da.tenda, da.ngaybatdau, da.ngayhoanthanh,pb.mapb, pb.tenpb, nv.manv, nv.hinhanh, nv.hoten, nv.ngaysinh, nv.chucvu, da.leader, da.filemota, da.ghichu, nv.email, nv.trangthai 
            FROM duan da 
            JOIN phongban pb ON da.phongban = pb.mapb 
            JOIN ctduan ctda ON da.mada = ctda.mada 
            JOIN nhanvien nv ON nv.manv = ctda.manv 
            WHERE da.mada = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mada);
    $stmt->execute();
    $result = $stmt->get_result();

    $projectDetails = null;
    $employees = [];
    $leaderInfo = null;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (!$projectDetails) {
                $projectDetails = $row;  
            }
            $employees[] = $row;  
        }

        $leaderId = $projectDetails['leader'];
        if ($leaderId) {
            $leaderSql = "SELECT manv, hoten FROM nhanvien WHERE manv = ?";
            $leaderStmt = $conn->prepare($leaderSql);
            $leaderStmt->bind_param("s", $leaderId);
            $leaderStmt->execute();
            $leaderResult = $leaderStmt->get_result();
            if ($leaderResult->num_rows > 0) {
                $leaderInfo = $leaderResult->fetch_assoc();
            }
            $leaderStmt->close();
        }
    }
    $conn->close();
    ?>
    <form id="formXemThongTin" action="formxemctnv(GD).php" method="POST" >
        <input type="hidden" name="manv" id="manv">
    </form>
    Mã Dự Án:<br>
    <input type="text" name="txtmada" value="<?php echo $projectDetails['mada']; ?>" readonly><br>
    Tên Dự Án: <br>
    <input type="text" name="txttendean" value="<?php echo $projectDetails['tenda']; ?>" placeholder="Nhập Tên Dự Án"><br>
    Trạng Thái: <br>
    <input type="text" name="txttrangthai" id="txttrangthai" value="<?php echo $projectDetails['trangthai']; ?>" readonly><br>
    Ngày Bắt Đầu: <br>
    <input type="date" name="txtngaybatdau" value="<?php echo $projectDetails['ngaybatdau']; ?>"><br>
    Ngày Kết Thúc: <br>
    <input type="date" name="txtngayketthuc" value="<?php echo $projectDetails['ngayhoanthanh']; ?>"><br>
    <span id="error-ngayketthuc" style="color: red; display: none;"></span>

    Phòng Ban: <br>
    <select name="txtphongban" id="phongban">
        <option value="<?php echo $projectDetails['mapb']; ?>" selected><?php echo $projectDetails['tenpb']; ?></option>
    </select><br>

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
                <th>Chức Vụ</th>
                <th>Email</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($employees as $employee) { ?>
        <tr>
            <td class="employee-id"><?php echo $employee['manv']; ?></td>
            <td><img src="<?php echo $employee['hinhanh']; ?>" alt="Hình ảnh nhân viên" width="50" height="50"></td>
            <td><?php echo $employee['hoten']; ?></td>
            <td><?php echo date('d/m/Y', strtotime($employee['ngaysinh'])); ?></td>
            <td><?php echo $employee['chucvu']; ?></td>
            <td><?php echo $employee['email']; ?></td>
            <td><?php echo $employee['trangthai']; ?></td>
            <td>
              
            <button type="button" class="xemttnv" data-id="<?php echo $employee['manv']; ?>">Xem Thông Tin</button>
                 <button type="button" class="delete-btn">Xóa</button>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    Leader: <br>
    <select name="txtleader" id="leader">
        <?php if ($leaderInfo) { ?>
            <option value="" disabled selected>Chọn Nhân Viên</option>
            <option value="<?php echo $leaderInfo['manv']; ?>" selected>
                <?php echo $leaderInfo['hoten'] . " [" . $leaderInfo['manv'] . "]"; ?>
            </option>
        <?php } ?>

        <?php foreach ($employees as $employee) { 
            if ($employee['manv'] !== $leaderInfo['manv']) { ?>
                <option value="<?php echo $employee['manv']; ?>">
                    <?php echo $employee['hoten'] . " [" . $employee['manv'] . "]"; ?>
                </option>
        <?php } } ?>
    </select><br>

    Ghi Chú:<br>
    <input type="text" name="txtghichu" id="txtghichu" value="<?php echo $projectDetails['ghichu']; ?>" placeholder="Điền Ghi Chú"><br>

    File Mô Tả:<br>
    <input type="input" id="filemota" name="filemota" value="<?php echo $projectDetails['filemota']; ?>" ><br>
    <button type="button" id="btnthemduan" name="btnthemduan">Chỉnh Sửa</button>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const nhanvienTable = document.getElementById('nhanvienTable');
    const leaderSelect = document.getElementById('leader');
    const phongbanSelect = document.getElementById('phongban'); 
    const nhanvienSelect = document.getElementById('nhanvien'); 

    const phongbanId = phongbanSelect.value; 
   
    if (phongbanId) {
        // Lấy danh sách mã nhân viên từ bảng
        const existingEmployeeIds = Array.from(nhanvienTable.querySelectorAll('.employee-id'))
            .map(elem => elem.textContent.trim())
            .join(','); // Nối các ID lại thành một chuỗi
        
        // Gọi API để lấy danh sách nhân viên
        fetch(`get_nhanvien4.php?phongban=${phongbanId}&existingIds=${existingEmployeeIds}`)
            .then(response => response.json())
            .then(data => {
                nhanvienSelect.innerHTML = '<option value="" disabled selected>Chọn Nhân Viên</option>'; // Đặt lại danh sách chọn nhân viên
                
                // Thêm các tùy chọn nhân viên vào dropdown
                data.forEach(nhanvien => {
                    const option = document.createElement('option');
                    option.value = nhanvien.value; 
                    option.textContent = nhanvien.name; 
                    nhanvienSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Lỗi khi lấy dữ liệu nhân viên:', error));
    }
    function upadtenv() {
    const phongbanId = phongbanSelect.value; // Lấy giá trị của phongban
    if (phongbanId) {
        // Lấy danh sách mã nhân viên từ bảng
        const existingEmployeeIds = Array.from(nhanvienTable.querySelectorAll('.employee-id'))
            .map(elem => elem.textContent.trim())
            .join(','); // Nối các ID lại thành một chuỗi
        
        // Gọi API để lấy danh sách nhân viên
        fetch(`get_nhanvien4.php?phongban=${phongbanId}&existingIds=${existingEmployeeIds}`)
            .then(response => response.json())
            .then(data => {
                nhanvienSelect.innerHTML = '<option value="" disabled selected>Chọn Nhân Viên</option>';  
                
                 
                data.forEach(nhanvien => {
                    const option = document.createElement('option');
                    option.value = nhanvien.value; 
                    option.textContent = nhanvien.name; 
                    nhanvienSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Lỗi khi lấy dữ liệu nhân viên:', error));
    }
}



document.querySelectorAll('.xemttnv').forEach(button => {
            button.addEventListener('click', function() {
                const employeeId = this.getAttribute('data-id');
                document.getElementById('manv').value = employeeId;  // Set the employee ID
                document.getElementById('formXemThongTin').submit(); // Submit the form
            });
        });

const btnNhanVien = document.getElementById('btnnhanvien');

    btnNhanVien.addEventListener('click', function() {
        const selectedOption = nhanvienSelect.options[nhanvienSelect.selectedIndex];

        if (selectedOption && selectedOption.value) {
            const employeeId = selectedOption.value;

             
            fetch(`get_nhanvien_detail.php?manhanvien=${employeeId}`)
                .then(response => response.json())
                .then(data => {
                    
                    if (data) {
                      
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td class="employee-id">${data.manv}</td>
                            <td><img src="${data.hinhanh}" alt="Hình ảnh nhân viên" width="50" height="50"></td>
                            <td>${data.hoten}</td>
                            <td>${data.ngaysinh}</td>
                            <td>${data.chucvu}</td>
                            <td>${data.email}</td>
                            <td>${data.trangthai}</td>
                            <td><button type="button" class="delete-btn">Xóa</button></td>
                        `;
                        nhanvienTable.querySelector('tbody').appendChild(newRow);
                        const leaderOption = document.createElement('option');
                        leaderOption.value = data.manv;
                        leaderOption.textContent = `${data.hoten} [${data.manv}]`;
                        leaderSelect.appendChild(leaderOption);
                        
                        
                        nhanvienSelect.remove(nhanvienSelect.selectedIndex);
                        upadtenv();
                        nhanvienSelect.innerHTML = '<option value="" disabled selected>Chọn Nhân Viên</option>';  

                    } else {
                        alert("Không tìm thấy thông tin nhân viên.");
                    }
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu chi tiết nhân viên:', error));
        }
    });



    nhanvienTable.addEventListener('click', function(event) {
    if (event.target.classList.contains('delete-btn')) {
        const row = event.target.closest('tr'); 
        const employeeId = row.querySelector('.employee-id').textContent.trim(); 

        const confirmDelete = confirm("Bạn có chắc chắn muốn xóa nhân viên này?");
        if (confirmDelete) {
            row.remove();

             if (leaderSelect.value === employeeId) {
                leaderSelect.selectedIndex = 0;   
            }

             
            for (let i = 0; i < leaderSelect.options.length; i++) {
                if (leaderSelect.options[i].value === employeeId) {
                    leaderSelect.remove(i);
                    break;
                }
            }

            
            upadtenv();
        }
    }

  

});
document.getElementById('btnthemduan').addEventListener('click', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    
    const mada = document.querySelector('input[name="txtmada"]').value.trim();
    const tenda = document.querySelector('input[name="txttendean"]').value.trim();
    const ngaybatdau = document.querySelector('input[name="txtngaybatdau"]').value;
    const ngayketthuc = document.querySelector('input[name="txtngayketthuc"]').value;
    const phongban = document.getElementById('phongban').value;
    const leader = document.getElementById('leader').value;
    const ghichu = document.getElementById('txtghichu').value.trim();
    const filemota = document.getElementById('filemota').value.trim();

    // Hàm lấy mã nhân viên từ bảng nhanvientable
    function getEmployeeIdsFromTable() {
        const nhanvienTable = document.getElementById('nhanvienTable');
        return Array.from(nhanvienTable.querySelectorAll('.employee-id')).map(elem => elem.textContent.trim());
    }

    const nhanvienArray = getEmployeeIdsFromTable();

    // Kiểm tra các trường bắt buộc không được để trống
    if (!tenda || !ngaybatdau || !ngayketthuc || !phongban || nhanvienArray.length === 0 || !leader ) {
        alert("Vui lòng điền đầy đủ các trường bắt buộc.");
        return;
    }

    // Kiểm tra ngày kết thúc phải sau ngày bắt đầu
    if (new Date(ngayketthuc) < new Date(ngaybatdau)) {
        alert("Ngày kết thúc phải sau ngày bắt đầu.");
        return;
    }

    // Yêu cầu người dùng xác nhận
    const confirmSubmission = confirm("Bạn có chắc chắn muốn chỉnh sửa dự án này?");
    if (!confirmSubmission) {
        return; // Hủy gửi nếu người dùng chọn "Cancel"
    }

    // Gửi dữ liệu qua fetch đến capnhatduan.php
    fetch('capnhatduan.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            mada : mada,
            tenda: tenda,
            ngaybatdau: ngaybatdau,
            ngayhoanthanh: ngayketthuc,
            phongban: phongban,
            nhanvien: nhanvienArray, 
            leader: leader,
            ghichu: ghichu,
            filemota: filemota
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Dự án đã được cập nhật thành công!");
            
        } else {
            alert("Có lỗi xảy ra khi cập nhật dự án.");
        }
    })
    .catch(error => {
        console.error('Lỗi khi cập nhật dự án:', error);
        alert("Có lỗi xảy ra khi cập nhật dự án.");
    });


// Gọi fetch để lấy trạng thái dự án
fetch('get_trangthai.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        mada: mada // Truyền mã dự án vào
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Cập nhật ô input trạng thái
        const trangThaiElement = document.getElementById('txttrangthai'); // Lấy input trạng thái
        trangThaiElement.value = data.trangthai; // Cập nhật giá trị
      
    } else {
        alert("Có lỗi xảy ra khi lấy trạng thái dự án: " + data.message);
    }
})
.catch(error => {
    console.error('Lỗi khi lấy trạng thái dự án:', error);
    alert("Có lỗi xảy ra khi lấy trạng thái dự án.");
});

    
});

 
});
</script>
</body>
</html>
