<?php
// booking_process.php
session_start();

// ========================
// 1. Kết nối CSDL
// ========================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "hotel_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// ========================
// 2. Lấy dữ liệu từ form
// ========================
$fullname   = $_POST['full_name'] ?? '';
$email      = $_POST['email'] ?? '';
$phone      = $_POST['phone'] ?? '';
$check_in   = $_POST['check_in'] ?? '';
$check_out  = $_POST['check_out'] ?? '';
$guests     = $_POST['guests'] ?? '';
$room_id    = $_POST['room_id'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$payment_status = "pending";

// Kiểm tra dữ liệu
if (empty($fullname) || empty($email) || empty($check_in) || empty($check_out) || empty($room_id) || empty($payment_method)) {
    die("Vui lòng điền đầy đủ thông tin!");
}

// ========================
// 3. Lấy giá phòng
// ========================
$sql_room = "SELECT price FROM rooms WHERE id = ?";
$stmt_room = $conn->prepare($sql_room);
$stmt_room->bind_param("i", $room_id);
$stmt_room->execute();
$result_room = $stmt_room->get_result();
if ($result_room->num_rows == 0) {
    die("Phòng không tồn tại!");
}
$row_room = $result_room->fetch_assoc();
$price_per_night = $row_room['price'];

// Tính số đêm
$check_in_time  = strtotime($check_in);
$check_out_time = strtotime($check_out);
$nights = max(1, round(($check_out_time - $check_in_time) / (60 * 60 * 24)));

// Tổng tiền
$total_price = $price_per_night * $nights;

// ========================
// 4. Lưu vào bảng bookings
// ========================
// Lấy user_id từ session (đảm bảo user đã đăng nhập)
if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập trước khi đặt phòng.");
}
$user_id = $_SESSION['user_id'];

$sql = "INSERT INTO bookings (user_id, full_name, email, phone, check_in, check_out, guests, room_id, payment_method, payment_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

// i = integer, s = string
$stmt->bind_param("isssssiiss", $user_id, $fullname, $email, $phone, $check_in, $check_out, $guests, $room_id, $payment_method, $payment_status);

if (!$stmt->execute()) {
    die("Lỗi khi lưu booking: " . $stmt->error);
}

$booking_id = $stmt->insert_id; // Lấy ID booking để truyền qua thanh toán

// ========================
// 5. Chuyển hướng thanh toán
// ========================
if ($payment_method === "vnpay") {
    // VNPay Config
    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl = "http://localhost/hotel_booking/payment_success.php";
    $vnp_TmnCode = "YOUR_VNPAY_TMNCODE"; // Mã website của bạn tại VNPay
    $vnp_HashSecret = "YOUR_VNPAY_HASH_SECRET"; // Chuỗi bí mật

    $vnp_TxnRef = $booking_id;
    $vnp_OrderInfo = "Thanh toán booking #$booking_id";
    $vnp_OrderType = "billpayment";
    $vnp_Amount = $total_price * 100; // VNPay tính theo VND * 100
    $vnp_Locale = "vn";
    $vnp_BankCode = "";
    $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef
    );

    ksort($inputData);
    $query = "";
    $hashdata = "";
    foreach ($inputData as $key => $value) {
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
        $hashdata .= urlencode($key) . "=" . urlencode($value) . '&';
    }
    $query = rtrim($query, '&');
    $hashdata = rtrim($hashdata, '&');

    $vnp_Url .= "?" . $query;
    if (isset($vnp_HashSecret)) {
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
    }

    header("Location: $vnp_Url");
    exit;
}

if ($payment_method === "bank") {
    // Chuyển hướng sang trang hiển thị thông tin thanh toán qua ngân hàng
    header("Location: bank_payment.php?booking_id={$booking_id}&amount={$total_price}");
    exit;
}

echo "Invalid payment method!";