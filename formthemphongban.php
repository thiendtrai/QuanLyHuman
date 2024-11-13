<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form id="formthemphongban">
        <input name="tenphongban" id="tenphongban" type="text" placeholder="Tên phòng ban">
        <input name="mota" id="mota" type="text" placeholder="Mô tả">
        <button type="button" id="btnthempb">THÊM</button>
    </form> 

    <script>
        document.getElementById('btnthempb').addEventListener('click', function() {
            const tenphongban = document.getElementById('tenphongban').value;
            const mota = document.getElementById('mota').value;

            fetch('themphongban.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `tenphongban=${encodeURIComponent(tenphongban)}&mota=${encodeURIComponent(mota)}`
            })
            .then(response => response.text())
            .then(data => {
                console.log(data); 
                alert("Phòng ban đã được thêm thành công!");
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Đã xảy ra lỗi!");
            });
        });
    </script>
</body>
</html>
