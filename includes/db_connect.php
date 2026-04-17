<?php
$host = 'localhost';
$username = 'root';
$password = ''; // nếu bạn đã cài mật khẩu MySQL, hãy điền vào đây
$database = 'hotel_booking'; // tên CSDL bạn đã tạo

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
