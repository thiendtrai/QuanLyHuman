<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Phòng Ban</title>
</head>
<body>
    <?php
    include 'ketnoi.php';
    $sql = "SELECT * FROM phongban where trangthai != 'locked'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            
            echo "<div class='phongban'>";
            echo "<form id='thongtinkh'>";
            echo "<p>" . $row['tenpb'] . "</p>";   
            echo "</form>";

            echo "<form action='formxemchitietpbnv(GD).php' method='POST'>";
            echo "<input type='hidden' name='mapb' value='" . $row['mapb'] . "'>";   
            echo "<button type='submit'>Xem</button>";   
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "Không có dữ liệu.";
    }

    $conn->close();
    ?>
    
</body>
</html>
