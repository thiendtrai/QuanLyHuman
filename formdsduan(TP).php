<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Dự Án</title>
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
    <select name="txtphongban" id="phongban">
        <option value="" disabled selected>Chọn Phòng Ban</option>
    </select>
    <select name="txttrangthai" id="trangthai">
        <option value="" disabled selected>Chọn Trạng Thái</option>
        <option value="Sắp thực hiện">Sắp thực hiện</option>
        <option value="Đang thực hiện">Đang thực hiện</option>
        <option value="Đã hoàn thành">Đã hoàn thành</option>
    </select><br>
    <input type="text" name="txttimkiem" id="txttimkiem" placeholder="Tìm Kiếm...">

    <table id="duanTable">
        <thead>
            <tr>
                <th>Mã Dự Án</th>
                <th>Tên Dự Án</th>
                <th>Phòng Ban</th>
                <th>Ngày Bắt Đầu</th>
                <th>Ngày Hoàn Thành</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timkiemInput = document.getElementById('txttimkiem');
            const phongbanSelect = document.getElementById('phongban');
            const trangthaiSelect = document.getElementById('trangthai');
            const tableBody = document.getElementById('duanTable').getElementsByTagName('tbody')[0];

             fetch('get_phongban.php')
                .then(response => response.json())
                .then(data => {
                    data.forEach(phongban => {
                        const option = document.createElement('option');
                        option.value = phongban.value;
                        option.textContent = phongban.name;
                        phongbanSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu phòng ban:', error));

       
            phongbanSelect.addEventListener('change', fetchDuAnData);
            trangthaiSelect.addEventListener('change', fetchDuAnData);

            function fetchDuAnData() {
                const phongbanId = phongbanSelect.value;
                const trangthai = trangthaiSelect.value;
                tableBody.innerHTML = "";  
                if (phongbanId && trangthai) {
                    fetch(`get_duan.php?phongban=${phongbanId}&trangthai=${trangthai}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log(data); 

                            if (data.length > 0) {
                                data.forEach(duan => {
                                    const row = tableBody.insertRow();
                                    row.insertCell(0).textContent = duan.mada;  
                                    row.insertCell(1).textContent = duan.tenda;  
                                    row.insertCell(2).textContent = duan.tenpb;  
                                    row.insertCell(3).textContent = duan.ngaybatdau;  
                                    row.insertCell(4).textContent = duan.ngayhoanthanh;  
                                    row.insertCell(5).textContent = duan.trangthai;  

                                    const actionCell = row.insertCell(6);
                                    const detailButton = document.createElement('button');
                                    detailButton.textContent = 'Xem Chi Tiết';
                                    detailButton.onclick = () => xemChiTietDuAn(duan.mada);
                                    actionCell.appendChild(detailButton);
                                });
                            } else {
                                const row = tableBody.insertRow();
                                const cell = row.insertCell(0);
                                cell.colSpan = 7; 
                                cell.textContent = 'Không có dự án nào';
                                cell.style.textAlign = "center";
                            }
                        })
                        .catch(error => console.error('Lỗi khi lấy dữ liệu dự án:', error));
                } else {
                    const row = tableBody.insertRow();
                    const cell = row.insertCell(0);
                    cell.colSpan = 7; 
                    cell.textContent = 'Vui lòng chọn Phòng ban và Trạng thái';
                    cell.style.textAlign = "center";
                }
            }

                
             timkiemInput.addEventListener('input', function() {
                const tenduan = timkiemInput.value.trim();
                if (tenduan!="") {
                    tableBody.innerHTML = "";  
                    phongbanSelect.selectedIndex = 0;
                    trangthaiSelect.selectedIndex = 0;
               
                    fetch(`timkiem_duan.php?tenduan=${tenduan}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log(data); 

                            if (data.length > 0) {
                                data.forEach(duan => {
                                    const row = tableBody.insertRow();
                                    row.insertCell(0).textContent = duan.mada;  
                                    row.insertCell(1).textContent = duan.tenda;  
                                    row.insertCell(2).textContent = duan.tenpb;  
                                    row.insertCell(3).textContent = duan.ngaybatdau;  
                                    row.insertCell(4).textContent = duan.ngayhoanthanh;  
                                    row.insertCell(5).textContent = duan.trangthai;  

                                    const actionCell = row.insertCell(6);
                                    const detailButton = document.createElement('button');
                                    detailButton.textContent = 'Xem Chi Tiết';
                                    detailButton.onclick = () => xemChiTietDuAn(duan.mada);
                                    actionCell.appendChild(detailButton);
                                });
                            } else {
                                const row = tableBody.insertRow();
                                const cell = row.insertCell(0);
                                cell.colSpan = 7; 
                                cell.textContent = 'Không có dự án nào';
                                cell.style.textAlign = "center";
                            }
                        })
                        .catch(error => console.error('Lỗi khi lấy dữ liệu dự án:', error));
               
                } 
                if(tenduan==""){

                    fetchDuAnData();
                }
            });



            function xemChiTietDuAn(mada) {
               
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'formxemctduan(GD).php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'mada';
                input.value = mada;
                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            }






        });
    </script>
</body>
</html>
