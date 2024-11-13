<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhân Viên</title>
</head>
<body>
     

<?php
include 'ketnoi.php';
 $sdt1 = $_GET['sdt1'];

$sql = "SELECT * FROM phongban pb, nhanvien nv where pb.mapb = nv.mapb and nv.sdt = '$sdt1'";
$result = $conn->query(query: $sql);


if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $mapb = $row['mapb'];
        $tenpb = $row['tenpb'];
    }
}

$conn->close();
 

    ?>
    <select name="txtphongban" id="phongban">
         
        <option value="<?php echo $mapb; ?>" ><?php echo $tenpb; ?></option>
    </select>
    <select name="txttrangthai" id="trangthai">
    <option value="" disabled selected>Chọn Trạng Thái</option>
        <option value="Đang Làm Việc" >Đang Làm Việc</option>
        <option value="Đã Nghỉ Việc" >Đã Nghỉ Việc</option>
    </select><br>

    <input type="text" name="txttimkiem" id="txttimkiem" placeholder="Tìm Kiếm..." >
    

    <table id="nhanvienTable">
        <thead>
            <tr>
                <th>Mã Nhân Viên</th>
                <th>Hình Ảnh</th>
                <th>Họ Tên</th>
                <th>Ngày Sinh</th>
                <th>Giới Tính</th>
                <th>Chức Vụ</th>
                <th>Phòng Ban</th>
                <th>Email</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
    const phongbanSelect = document.getElementById('phongban');
    const timkiemInput = document.getElementById('txttimkiem');
    const timkiemButton = document.getElementById('btntimkiem');
   const trangthaiSelect = document.getElementById('trangthai');


    const phongbanId = phongbanSelect.value;
    const trangthai = trangthaiSelect.value;
    const tableBody = document.getElementById('nhanvienTable').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = "";  // Xóa nội dung bảng cũ

    if (phongbanId && trangthai) {
        
        fetch(`get_nhanvien2.php?phongban=${phongbanId}&trangthai=${trangthai}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không thể lấy dữ liệu');
                }
                return response.json();
            })
            .then(data => {
                // Kiểm tra và hiển thị dữ liệu
                if (data.length > 0) {
                    data.forEach(nhanvien => {
                        const row = tableBody.insertRow();
                        row.insertCell(0).textContent = nhanvien.manv;
                        row.insertCell(1).innerHTML = `<img src="${nhanvien.hinhanh}" alt="Hình ảnh" width="50">`;
                        row.insertCell(2).textContent = nhanvien.hoten;
                        row.insertCell(3).textContent = nhanvien.ngaysinh;
                        row.insertCell(4).textContent = nhanvien.gioitinh;
                        row.insertCell(5).textContent = nhanvien.chucvu;
                        row.insertCell(6).textContent = nhanvien.tenpb;
                        row.insertCell(7).textContent = nhanvien.email;
                        row.insertCell(8).textContent = nhanvien.trangthai;

                        const actionCell = row.insertCell(9);

                        // Tạo nút 'Xem Chi Tiết'
                        const detailButton = document.createElement('button');
                        detailButton.textContent = 'Xem Chi Tiết';
                        detailButton.onclick = function() {
                            xemChiTietNhanVien(nhanvien.manv);
                        };
                        actionCell.appendChild(detailButton);

                        // Tạo nút 'Cập Nhật Trạng Thái'
                        const updateStatusButton = document.createElement('button');
                        updateStatusButton.textContent = 'Cập Nhật Trạng Thái';
                        updateStatusButton.onclick = function() {
                            capNhatTrangThai(nhanvien.manv);
                        };
                        actionCell.appendChild(updateStatusButton);
                    });
                } else {
                    // Khi không có nhân viên
                    const row = tableBody.insertRow();
                    const cell = row.insertCell(0);
                    cell.colSpan = 10;
                    cell.textContent = 'Không có nhân viên';
                    cell.style.textAlign = "center";  // Canh giữa thông báo
                }
            })
            .catch(error => console.error('Lỗi khi lấy dữ liệu nhân viên:', error));
    } else {
        // Nếu chưa chọn đủ Phòng ban và Trạng thái, hiển thị thông báo
        const row = tableBody.insertRow();
        const cell = row.insertCell(0);
        cell.colSpan = 10;
        cell.textContent = 'Vui lòng chọn Phòng ban và Trạng thái';
        cell.style.textAlign = "center";  // Canh giữa thông báo
    }


   
 trangthaiSelect.addEventListener('change', fetchNhanVienData);

function fetchNhanVienData() {
    const phongbanId = phongbanSelect.value;
    const trangthai = trangthaiSelect.value;
    timkiemInput.value ="";
    const tableBody = document.getElementById('nhanvienTable').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = "";  

    if (phongbanId && trangthai) {
       
        fetch(`get_nhanvien2.php?phongban=${phongbanId}&trangthai=${trangthai}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không thể lấy dữ liệu');
                }
                return response.json();
            })
            .then(data => {
                // Kiểm tra và hiển thị dữ liệu
                if (data.length > 0) {
                    data.forEach(nhanvien => {
                        const row = tableBody.insertRow();
                        row.insertCell(0).textContent = nhanvien.manv;
                        row.insertCell(1).innerHTML = `<img src="${nhanvien.hinhanh}" alt="Hình ảnh" width="50">`;
                        row.insertCell(2).textContent = nhanvien.hoten;
                        row.insertCell(3).textContent = nhanvien.ngaysinh;
                        row.insertCell(4).textContent = nhanvien.gioitinh;
                        row.insertCell(5).textContent = nhanvien.chucvu;
                        row.insertCell(6).textContent = nhanvien.tenpb;
                        row.insertCell(7).textContent = nhanvien.email;
                        row.insertCell(8).textContent = nhanvien.trangthai;

                        const actionCell = row.insertCell(9);

                        // Tạo nút 'Xem Chi Tiết'
                        const detailButton = document.createElement('button');
                        detailButton.textContent = 'Xem Chi Tiết';
                        detailButton.onclick = function() {
                            xemChiTietNhanVien(nhanvien.manv);
                        };
                        actionCell.appendChild(detailButton);

                        // Tạo nút 'Cập Nhật Trạng Thái'
                        const updateStatusButton = document.createElement('button');
                        updateStatusButton.textContent = 'Cập Nhật Trạng Thái';
                        updateStatusButton.onclick = function() {
                            capNhatTrangThai(nhanvien.manv);
                        };
                        actionCell.appendChild(updateStatusButton);
                    });
                } else {
                    // Khi không có nhân viên
                    const row = tableBody.insertRow();
                    const cell = row.insertCell(0);
                    cell.colSpan = 10;
                    cell.textContent = 'Không có nhân viên';
                    cell.style.textAlign = "center";  // Canh giữa thông báo
                }
            })
            .catch(error => console.error('Lỗi khi lấy dữ liệu nhân viên:', error));
    } else {
        // Nếu chưa chọn đủ Phòng ban và Trạng thái, hiển thị thông báo
        const row = tableBody.insertRow();
        const cell = row.insertCell(0);
        cell.colSpan = 10;
        cell.textContent = 'Vui lòng chọn Phòng ban và Trạng thái';
        cell.style.textAlign = "center";  // Canh giữa thông báo
    }
}


// Thay sự kiện click bằng change
timkiemInput.addEventListener('input', function() {
    const tenNhanVien = timkiemInput.value.trim();
    const phongbanId = phongbanSelect.value;
    

if(tenNhanVien==""){
    fetchNhanVienData();

}



if (tenNhanVien !="") {
        fetch(`timkiem_nhanvien2.php?tennv=${tenNhanVien}&phongban=${phongbanId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không thể tìm kiếm');
                }
                return response.json();
            })
            .then(data => {
                trangthaiSelect.selectedIndex = 0;

                const tableBody = document.getElementById('nhanvienTable').getElementsByTagName('tbody')[0];
                tableBody.innerHTML = "";
                if (data.length > 0) {
                    data.forEach(nhanvien => {
                        const row = tableBody.insertRow();
                        row.insertCell(0).textContent = nhanvien.manv;
                        row.insertCell(1).innerHTML = `<img src="${nhanvien.hinhanh}" alt="Hình ảnh" width="50">`;
                        row.insertCell(2).textContent = nhanvien.hoten;
                        row.insertCell(3).textContent = nhanvien.ngaysinh;
                        row.insertCell(4).textContent = nhanvien.gioitinh;
                        row.insertCell(5).textContent = nhanvien.chucvu;
                        row.insertCell(6).textContent = nhanvien.tenpb;
                        row.insertCell(7).textContent = nhanvien.email;
                        row.insertCell(8).textContent = nhanvien.trangthai;

                        const actionCell = row.insertCell(9);

                        // Nút xem chi tiết
                        const detailButton = document.createElement('button');
                        detailButton.textContent = 'Xem Chi Tiết';
                        detailButton.onclick = function() {
                            xemChiTietNhanVien(nhanvien.manv);
                        };
                        actionCell.appendChild(detailButton);

                        // Nút cập nhật trạng thái
                        const updateStatusButton = document.createElement('button');
                        updateStatusButton.textContent = 'Cập Nhật Trạng Thái';
                        updateStatusButton.onclick = function() {
                            capNhatTrangThai(nhanvien.manv);
                        };
                        actionCell.appendChild(updateStatusButton);
                    });
                } else {
                    const row = tableBody.insertRow();
                    const cell = row.insertCell(0);
                    cell.colSpan = 10;
                    cell.classList.add('no-result');
                    cell.textContent = 'Không tìm thấy nhân viên';
                }
            })
            .catch(error => {
                console.error('Lỗi khi tìm kiếm nhân viên:', error);
                alert("Đã có lỗi xảy ra trong quá trình tìm kiếm. Vui lòng thử lại.");
            });
    } 
});



     function xemChiTietNhanVien(manv) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'formxemctnv(TP).php';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'manv';
        input.value = manv;
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();
    }
  function capNhatTrangThai(manv) {
        if (!manv) {
            alert("Mã nhân viên không hợp lệ!");
            return;
        }

        const confirmUpdate = confirm("Bạn có chắc chắn muốn cập nhật trạng thái cho nhân viên này không?");
        if (confirmUpdate) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'capnhattrangthainv.php';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'manv';
            input.value = manv;
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }
    }
});

    </script>
</body>
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
</html>
