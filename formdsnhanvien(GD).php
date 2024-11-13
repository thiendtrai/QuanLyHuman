<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhân Viên</title>
</head>
<body>
    <select name="txtphongban" id="phongban">
        <option value="" disabled selected>Chọn Phòng Ban</option>
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
          

             fetch('get_phongban.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    data.forEach(phongban => {
                        const option = document.createElement('option');
                        option.value = phongban.value;
                        option.textContent = phongban.name;
                        phongbanSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu phòng ban:', error));


const trangthaiSelect = document.getElementById('trangthai');

phongbanSelect.addEventListener('change', fetchNhanVienData);
trangthaiSelect.addEventListener('change', fetchNhanVienData);

function fetchNhanVienData() {
    const phongbanId = phongbanSelect.value;
    const trangthai = trangthaiSelect.value;
    timkiemInput.value = "";
    const tableBody = document.getElementById('nhanvienTable').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = "";  

    if (phongbanId && trangthai) {
         fetch(`get_nhanvien1.php?phongbanId=${phongbanId}&trangthai=${trangthai}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không thể lấy dữ liệu');
                }
                return response.json();
            })
            .then(data => {
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

                        const detailButton = document.createElement('button');
                        detailButton.textContent = 'Xem Chi Tiết';
                        detailButton.onclick = function() {
                            xemChiTietNhanVien(nhanvien.manv);
                        };
                        actionCell.appendChild(detailButton);

                       
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
                    cell.textContent = 'Không có nhân viên';
                    cell.style.textAlign = "center";  
                }
            })
            .catch(error => console.error('Lỗi khi lấy dữ liệu nhân viên:', error));
    } else {
      
        const row = tableBody.insertRow();
        const cell = row.insertCell(0);
        cell.colSpan = 10;
        cell.textContent = 'Vui lòng chọn Phòng ban và Trạng thái';
        cell.style.textAlign = "center";  
    }
}

             timkiemInput.addEventListener('input', function() {
                const tenNhanVien = timkiemInput.value.trim();



                if(tenNhanVien==""){

                    fetchNhanVienData();
                }



                if (tenNhanVien!="") {
                    fetch(`timkiem_nhanvien.php?tennv=${tenNhanVien}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Không thể tìm kiếm');
                            }
                            return response.json();
                        })
                        .then(data => {
                            phongbanSelect.selectedIndex = 0;
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


                                    const detailButton = document.createElement('button');
                                    detailButton.textContent = 'Xem Chi Tiết';
                                    detailButton.onclick = function() {
                                        xemChiTietNhanVien(nhanvien.manv);
                                    };
                                    actionCell.appendChild(detailButton);

                                
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
                                cell.textContent = 'Không tìm thấy nhân viên';
                            }
                        })
                        .catch(error => console.error('Lỗi khi tìm kiếm nhân viên:', error));
                } 
            });

         
            function xemChiTietNhanVien(manv) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'formxemctnv(GD).php';

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
