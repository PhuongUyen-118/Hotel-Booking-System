<?php
// payment_cancel.php

// 1. Kết nối CSDL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// 2. Lấy booking_id từ URL
$booking_id = $_GET['booking_id'] ?? '';
if (empty($booking_id)) {
    die("Không tìm thấy booking ID.");
}

// 3. Cập nhật trạng thái thanh toán
$sql = "UPDATE bookings SET payment_status = 'Cancelled' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    echo "<h2>Thanh toán đã bị hủy!</h2>";
    echo "<p>Đặt phòng của bạn chưa được xác nhận. Bạn có thể thử thanh toán lại.</p>";
} else {
    echo "Lỗi: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
