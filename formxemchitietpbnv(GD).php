<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhân Viên</title>
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
$mapb = $_POST['mapb'];



?>


<?php
include 'ketnoi.php';

$sql = "SELECT * FROM phongban WHERE mapb = '$mapb'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mapb = $row['mapb'];
        $tenpb = $row['tenpb'];
        $mota = $row['mota'];
        $ngaytao = date('Y-m-d', strtotime($row['ngaytao']));

        echo "Mã Phòng Ban: ";
        echo "<input type='text' name='txtmapb' id='txtmapb' value='$mapb' readonly>";
        echo "Tên Phòng Ban: ";
        echo "<input type='text' name='txttenpb' id='txttenpb' value='$tenpb'>";
        echo "Mô Tả: ";
        echo "<input type='text' name='txtmota' id='txtmota' value='$mota'>";
        echo "Ngày Tạo: ";
        echo "<input type='date' name='txtngaytao' id='txtngaytao' value='$ngaytao'>";
    }
}

$conn->close();
?>



<input type="hidden" name="txttrangthai" id="trangthai"  value="Đang Làm Việc">
   <br>

<input type="text" name="txttimkiem" id="txttimkiem" placeholder="Tìm Kiếm...">

<button type="button" name="btnluutt" id="btnluuttt" >Lưu Thông Tin Phòng Ban</button>

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

<input type="hidden" name="txtkhoapb" id="txtkhoapb" value="<?php echo $mapb;  ?>" >
<button type="button" id="btnkhoapb" name="btnkhoapb" > Khóa </button>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phongbanSelect = document.getElementById('txtmapb');
    const timkiemInput = document.getElementById('txttimkiem');
    const timkiemButton = document.getElementById('btntimkiem');
    const trangthaiSelect = document.getElementById('trangthai');


    document.getElementById('btnkhoapb').addEventListener('click', function() {
    // Lấy giá trị từ input ẩn
    const mapb = document.getElementById('txtkhoapb').value;

    // Gửi dữ liệu qua fetch đến capnhattrangthaipb.php
    fetch('capnhattrangthaipb.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ mapb: mapb })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Trạng thái phòng ban đã được cập nhật thành công!");
        } else {
            alert("" + data.message);
        }
    })
    .catch(error => {
        console.error('Lỗi khi cập nhật trạng thái phòng ban:', error);
        alert("Có lỗi xảy ra khi cập nhật trạng thái phòng ban.");
    });
});


    function loadNhanVien() {
        const phongbanId = phongbanSelect.value;
        const trangthai = trangthaiSelect.value;
        const tableBody = document.getElementById('nhanvienTable').getElementsByTagName('tbody')[0];
        tableBody.innerHTML = "";  

        if (phongbanId && trangthai) {
            fetch(`get_nhanvien3.php?phongban=${phongbanId}&trangthai=${trangthai}`)
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

                           
                            const phongbanDropdown = document.createElement('select');
                            const currentPhongBan = phongbanSelect.value; 
                            fillPhongBanDropdown(phongbanDropdown, currentPhongBan); 
                            actionCell.appendChild(phongbanDropdown);
                            const detailButton = document.createElement('button');
detailButton.textContent = 'Chuyển Phòng Ban';
detailButton.onclick = function() {
    const chucvu = nhanvien.chucvu;  
    const phongbanDropdown = actionCell.querySelector('select');  
    
    
    checkTruongPhong(phongbanDropdown, chucvu, nhanvien.manv);
};
actionCell.appendChild(detailButton);


                            const updateStatusButton = document.createElement('button');
                            updateStatusButton.textContent = 'Chọn Làm Trưởng Phòng';
                            updateStatusButton.onclick = function() {
                                capNhatTruongphong(nhanvien.manv);
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

    function fillPhongBanDropdown(dropdown, currentPhongBan) {
    fetch(`get_phongban2.php?mapb=${currentPhongBan}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Không thể lấy danh sách phòng ban');
            }
            return response.json();
        })
        .then(phongbanList => {
            dropdown.innerHTML = '';
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.text = 'Chọn phòng ban';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            dropdown.appendChild(defaultOption);

            phongbanList.forEach(phongban => {
                const option = document.createElement('option');
                option.value = phongban.mapb;  
                option.text = phongban.tenpb;  
                dropdown.appendChild(option);
            });
        })
        .catch(error => console.error('Lỗi khi lấy danh sách phòng ban:', error));
}


timkiemInput.addEventListener('input', function() {
  
    const tenNhanVien = timkiemInput.value.trim();
    const phongbanId = phongbanSelect.value;

if(tenNhanVien==""){

loadNhanVien();

}

    if (tenNhanVien !="") {
        fetch(`timkiem_nhanvien3.php?tennv=${tenNhanVien}&phongban=${phongbanId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không thể tìm kiếm');
                }
                return response.json();
            })
            .then(data => {
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
                        const phongbanDropdown = document.createElement('select');
                        fillPhongBanDropdown(phongbanDropdown);  
                        actionCell.appendChild(phongbanDropdown);

                        const detailButton = document.createElement('button');
                        detailButton.textContent = 'Chuyển Phòng Ban';
                        detailButton.onclick = function() {
                            const chucvu = nhanvien.chucvu; 
                            const phongbanDropdown = actionCell.querySelector('select');  
                            checkTruongPhong(phongbanDropdown, chucvu, nhanvien.manv);
                        };
                        actionCell.appendChild(detailButton);

                        const updateStatusButton = document.createElement('button');
                        updateStatusButton.textContent = 'Chọn Làm Trưởng Phòng';
                        updateStatusButton.onclick = function() {
                            capNhatTruongphong(nhanvien.manv);
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


    function checkTruongPhong(phongbanDropdown, chucvu, manv) {
   
    if (chucvu !== 'Trưởng Phòng') {
        const mapb = phongbanDropdown.value;

        if (!mapb) {
            alert('Vui lòng chọn phòng ban.');
            return;
        }

        const confirmChange = confirm('Bạn có chắc chắn muốn chuyển nhân viên này sang phòng ban mới không?');
    if (!confirmChange) {
        return; 
    }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'capnhatphongban.php';

        const inputManv = document.createElement('input');
        inputManv.type = 'hidden';
        inputManv.name = 'manv';
        inputManv.value = manv;
        form.appendChild(inputManv);

        const inputMapb = document.createElement('input');
        inputMapb.type = 'hidden';
        inputMapb.name = 'mapb';
        inputMapb.value = mapb;
        form.appendChild(inputMapb);

        document.body.appendChild(form);
        form.submit();
        return; 
    }

    if(chucvu == 'Trưởng Phòng'){
        const phongban = phongbanDropdown.value;
    if (!phongban) {
        alert('Vui lòng chọn phòng ban.');
        return;
    }


    
    fetch('check_truongphong3.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ phongban }),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Lỗi khi kiểm tra trưởng phòng.');
        }
        return response.json();
    })
    .then(data => {
        if (data.hasTruongPhong) {
            alert('Phòng ban này đã có trưởng phòng.');
        } else {
            const confirmChange = confirm('Bạn có chắc chắn muốn chuyển nhân viên này sang phòng ban mới không?');
    if (!confirmChange) {
        return; }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'capnhatphongban.php';

            const inputManv = document.createElement('input');
            inputManv.type = 'hidden';
            inputManv.name = 'manv';
            inputManv.value = manv;
            form.appendChild(inputManv);

            const inputMapb = document.createElement('input');
            inputMapb.type = 'hidden';
            inputMapb.name = 'mapb';
            inputMapb.value = phongban;
            form.appendChild(inputMapb);

            document.body.appendChild(form);
            form.submit();
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        alert('Đã có lỗi xảy ra. Vui lòng thử lại.');
    });

    }
   
}

function capNhatTruongphong(manv) {
    if (!manv) {
        alert("Mã nhân viên không hợp lệ!");
        return;
    }
    const phongbanSelect = document.getElementById('txtmapb');
    const phongbanId = phongbanSelect.value; 
    if (!phongbanId) {
        alert("Vui lòng chọn phòng ban.");
        return;
    }

    const confirmUpdate = confirm("Bạn có chắc chắn muốn chọn nhân viên này làm trưởng phòng không?");
    if (confirmUpdate) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'capnhattruongphong.php';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'manv';
        input.value = manv;
        form.appendChild(input);

        const input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'mapb';
        input1.value = phongbanId; 
        form.appendChild(input1);

        document.body.appendChild(form);
        form.submit();
    }
}

    loadNhanVien();
});

window.addEventListener('pageshow', function(event) {
        if (event.persisted || localStorage.getItem('reload') === 'true') {
            localStorage.removeItem('reload');
            location.reload();
        }
    });


    document.getElementById('btnluuttt').addEventListener('click', function() {
    
    const mapb = document.getElementById('txtmapb').value;
    const tenpb = document.getElementById('txttenpb').value;
    const mota = document.getElementById('txtmota').value;
    const ngaytao = document.getElementById('txtngaytao').value;

    
    const vietnamDate = new Date();
    vietnamDate.setHours(vietnamDate.getHours() + 7);
    const todayVN = vietnamDate.toISOString().split('T')[0];

    
    if (ngaytao > todayVN) {
        alert("Ngày tạo không được lớn hơn hôm nay. Vui lòng chọn lại.");
        return;  
    }

   
    if (confirm("Bạn có chắc chắn muốn lưu thông tin phòng ban?")) {
        
        const data = {
            mapb: mapb,
            tenpb: tenpb,
            mota: mota,
            ngaytao: ngaytao,
        };
 
        fetch('suattpb.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(responseData => {
             if (responseData.status === "success") {
                alert("Thông Tin Phòng Ban Đã Được Cập Nhật Thành Công");
            } else {
                alert("Có lỗi xảy ra: " + responseData.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Có lỗi xảy ra trong quá trình cập nhật thông tin phòng ban.");
        });
    }
});


</script>

</body>
</html>
