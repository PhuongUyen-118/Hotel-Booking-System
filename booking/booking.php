<?php
require_once('../config/db_connect.php');

// Kiểm tra kết nối database
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Lấy và validate dữ liệu đầu vào
$errors = [];

$hotel_id = filter_input(INPUT_GET, 'hotel_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$hotel_id) {
    $errors[] = 'Invalid or missing hotel_id';
}

$room_id = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$room_id) {
    // Thử với room_type_id nếu room_id không có
    $room_id = filter_input(INPUT_GET, 'room_type_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$room_id) {
        $errors[] = 'Invalid or missing room_id/room_type_id';
    }
}

$check_in = filter_input(INPUT_GET, 'checkin', FILTER_SANITIZE_STRING);
if (!$check_in || !DateTime::createFromFormat('Y-m-d', $check_in)) {
    $errors[] = 'Invalid check-in date format (YYYY-MM-DD required)';
}

$check_out = filter_input(INPUT_GET, 'checkout', FILTER_SANITIZE_STRING);
if (!$check_out || !DateTime::createFromFormat('Y-m-d', $check_out)) {
    $errors[] = 'Invalid check-out date format (YYYY-MM-DD required)';
}

$guests = filter_input(INPUT_GET, 'guests', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$guests) {
    $errors[] = 'Invalid number of guests (minimum 1)';
}

// Nếu có lỗi thì trả về
if (!empty($errors)) {
    die(json_encode(['status' => 'error', 'message' => implode(', ', $errors)]));
}

// Kiểm tra ngày hợp lệ
$today = new DateTime();
$checkInDate = new DateTime($check_in);
$checkOutDate = new DateTime($check_out);

if ($checkInDate < $today) {
    die(json_encode(['status' => 'error', 'message' => 'Check-in date cannot be in the past']));
}

if ($checkOutDate <= $checkInDate) {
    die(json_encode(['status' => 'error', 'message' => 'Check-out date must be after check-in date']));
}

// Lấy thông tin phòng với xử lý lỗi chi tiết
$sql = "SELECT r.id, r.type, r.price, r.room_number, h.name AS hotel_name 
        FROM rooms r 
        JOIN hotels h ON r.hotel_id = h.id
        WHERE r.id = ? AND r.hotel_id = ? 
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]));
}

if (!$stmt->bind_param("ii", $room_id, $hotel_id)) {
    die(json_encode(['status' => 'error', 'message' => 'Bind failed: ' . $stmt->error]));
}

if (!$stmt->execute()) {
    die(json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]));
}

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die(json_encode(['status' => 'error', 'message' => 'Room not found or not available']));
}

$room = $result->fetch_assoc();

// Tính số đêm
$nights = $checkOutDate->diff($checkInDate)->days;
$total_amount = $nights * $room['price'];

// Trả về thông tin thành công
$response = [
    'status' => 'success',
    'data' => [
        'room' => [
            'id' => $room['id'],
            'type' => $room['type'],
            'price' => $room['price'],
            'room_number' => $room['room_number'],
            'hotel_name' => $room['hotel_name']
        ],
        'check_in' => $check_in,
        'check_out' => $check_out,
        'nights' => $nights,
        'guests' => $guests,
        'total_amount' => $total_amount
    ]
];
$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Booking Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }

        .booking-container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 15px;
        }

        .room-summary {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .room-summary img {
            width: 200px;
            height: auto;
            border-radius: 6px;
        }

        .room-details p {
            margin: 4px 0;
            color: #555;
        }

        .form-group {
            margin-bottom: 12px;
        }

        label {
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background: #218838;
        }
    </style>
</head>

<body>

    <div class="booking-container">
        <h2>Confirm Your Booking</h2>

        <div class="room-summary">
         <img src="../image/<?php echo strtolower(htmlspecialchars($room['type'])); ?>.jpg"
                            alt="<?php echo htmlspecialchars($room['type']); ?> Room"
                            onerror="this.src='../image/default.jpg';">
            <div class="room-details">
                <p><strong>Hotel:</strong> <?php echo htmlspecialchars($room['hotel_name']); ?></p>
                <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['type']); ?></p>
                <p><strong>Check-in:</strong> <?php echo htmlspecialchars($check_in); ?></p>
                <p><strong>Check-out:</strong> <?php echo htmlspecialchars($check_out); ?></p>
                <p><strong>Nights:</strong> <?php echo $nights; ?></p>
                <p><strong>Guests:</strong> <?php echo $guests; ?></p>
                <p><strong>Total:</strong> <?php echo number_format($total_amount, 0, ',', '.'); ?> VND</p>
            </div>
        </div>

        <form action="booking_process.php" method="POST">
            <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
            <input type="hidden" name="check_in" value="<?php echo $check_in; ?>">
            <input type="hidden" name="check_out" value="<?php echo $check_out; ?>">
            <input type="hidden" name="guests" value="<?php echo $guests; ?>">
            <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">

            <h3>Contact Information</h3>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" required>
            </div>

            <h3>Payment Information</h3>
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="">-- Select --</option>
                    <option value="vnpay">VNPay</option>
                    <option value="bank">Bank Transfer</option>
                    <option value="hotel">Pay at Hotel</option>
                </select>
            </div>


            <button type="submit" class="btn">Confirm Booking</button>
        </form>
    </div>

</body>

</html>