<?php
// Bật báo lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../config/db_connect.php');

// Lấy hotel_id từ URL
$hotel_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($hotel_id <= 0) {
    die("Hotel not found.");
}

// Lấy thông tin khách sạn
$stmt = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Hotel not found.");
}
$hotel = $result->fetch_assoc();
$stmt->close();

// Xác định hình ảnh khách sạn
$hotelImage = !empty($hotel['image']) ? $hotel['image'] : 'hotel1.jpg';

// Lấy danh sách loại phòng có sẵn
$sql_room_types = "
    SELECT 
        id,
        room_number,
        type,
        price,
        description
    FROM rooms
    WHERE hotel_id = ? AND status = 'available'
    ORDER BY type, price
";
$stmt_room_types = $conn->prepare($sql_room_types);
if (!$stmt_room_types) {
    die("Prepare failed for room types: (" . $conn->errno . ") " . $conn->error);
}
$stmt_room_types->bind_param("i", $hotel_id);
if (!$stmt_room_types->execute()) {
    die("Execute failed for room types: (" . $stmt_room_types->errno . ") " . $stmt_room_types->error);
}
$room_result = $stmt_room_types->get_result();
$stmt_room_types->close();

// Tính giá thấp nhất
$stmt_min_price = $conn->prepare("SELECT MIN(price) AS min_price FROM rooms WHERE hotel_id = ? AND status = 'available'");
if (!$stmt_min_price) {
    die("Prepare failed for min price: (" . $conn->errno . ") " . $conn->error);
}
$stmt_min_price->bind_param("i", $hotel_id); // Chỉ một lần bind_param
if (!$stmt_min_price->execute()) {
    die("Execute failed for min price: (" . $stmt_min_price->errno . ") " . $stmt_min_price->error);
}
$min_price_result = $stmt_min_price->get_result();
if ($min_price_result === false) {
    die("Get result failed for min price: (" . $stmt_min_price->errno . ") " . $stmt_min_price->error);
}
$row_min = $min_price_result->fetch_assoc();
$starting_price = ($row_min && $row_min['min_price'] !== null) ? (float)$row_min['min_price'] : null;
$stmt_min_price->close();

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($hotel['name']); ?> - Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .hotel-top {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .hotel-image img {
            width: 100%;
            max-width: 400px;
            height: 250px;
            border-radius: 10px;
            object-fit: cover;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            color: #d32f2f;
        }

        .room-list {
            margin-top: 30px;
        }

        .room-card {
            display: flex;
            gap: 15px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fafafa;
            margin-bottom: 15px;
        }

        .room-card.selected {
            border-color: #28a745;
            background: #e8f5e8;
        }

        .room-card img {
            width: 180px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }

        .btn-select {
            display: inline-block;
            padding: 8px 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-select:hover {
            background: #1e7e34;
        }

        .btn-select.selected {
            background: #dc3545;
        }

        .btn-book {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
        }

        .btn-book:hover {
            background: #0056b3;
        }

        .booking-form {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .booking-form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        .booking-form input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 15px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background: #545b62;
        }
    </style>
</head>

<body>

    <div class="container">
        <a href="../index.php" class="back-btn">← Back to Hotels</a>

        <div class="hotel-top">
            <div class="hotel-image" style="flex:1; min-width:300px;">
                <!-- Sửa đường dẫn ảnh -->
                <img src="../image/<?php echo htmlspecialchars($hotelImage); ?>"
                    alt="<?php echo htmlspecialchars($hotel['name']); ?>"
                    onerror="this.src='../image/default.jpg';">
            </div>
            <div class="hotel-info" style="flex:1; min-width:300px;">
                <h1><?php echo htmlspecialchars($hotel['name']); ?></h1>
                <p class="price">
                    Starting Price:
                    <?php echo $starting_price !== null ? number_format($starting_price, 0, ',', '.') . ' VND / night' : 'N/A'; ?>
                </p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($hotel['address']); ?></p>
                <p><?php echo htmlspecialchars($hotel['description']); ?></p>
            </div>

            <div class="room-simple-info" style="margin-top:20px; padding:15px; background-color:#f8f9fa; border:1px solid #ddd; border-radius:8px; font-family:Arial, sans-serif;">
                <h3 style="font-size:16px; font-weight:bold; margin-bottom:8px; color:#333;">Room Information</h3>
                <div class="room-details" style="margin-bottom:12px;">
                    <p style="font-size:14px; color:#555; margin:3px 0;">20.0 m²</p>
                    <p style="font-size:14px; color:#555; margin:3px 0;">2 guests</p>
                </div>

                <h3 style="font-size:16px; font-weight:bold; margin-bottom:8px; color:#333;">Features You Will Love</h3>
                <ul style="list-style:none; padding:0; margin:0;">
                    <li style="font-size:14px; color:#444; padding-left:20px; position:relative; margin:5px 0;">
                        ✔ Shower
                    </li>
                    <li style="font-size:14px; color:#444; padding-left:20px; position:relative; margin:5px 0;">
                        ✔ Air Conditioning
                    </li>
                    <li style="font-size:14px; color:#444; padding-left:20px; position:relative; margin:5px 0;">
                        ✔ Seating Area
                    </li>
                    <li style="font-size:14px; color:#444; padding-left:20px; position:relative; margin:5px 0;">
                        ✔ Refrigerator
                    </li>
                </ul>
            </div>

        </div>

        <div class="room-list">
            <h3>Available Room Types</h3>
            <?php if ($room_result->num_rows > 0): ?>
                <?php while ($room = $room_result->fetch_assoc()): ?>
                    <div class="room-card" id="room-<?php echo $room['id']; ?>">
                        <!-- SỬA ĐƯỜNG DẪN ẢNH PHÒNG -->
                        <img src="../image/<?php echo strtolower(htmlspecialchars($room['type'])); ?>.jpg"
                            alt="<?php echo htmlspecialchars($room['type']); ?> Room"
                            onerror="this.src='../image/default.jpg';">
                        <div class="room-info">
                            <h4><?php echo htmlspecialchars($room['type']); ?> Room - <?php echo htmlspecialchars($room['room_number']); ?></h4>
                            <p><?php echo htmlspecialchars($room['description']); ?></p>
                            <p class="room-price"><?php echo number_format($room['price'], 0, ',', '.'); ?> VND / night</p>
                            <button class="btn-select" data-room-id="<?php echo $room['id']; ?>" data-room-price="<?php echo $room['price']; ?>">
                                Select this room
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>

                <!-- Booking Form -->
                <div class="booking-form">
                    <h3>Booking Details</h3>
                    <div id="selected-room-info" style="display:none; background:#e8f5e8; padding:10px; margin-bottom:15px; border-radius:5px;">
                        <strong>Selected Room:</strong> <span id="selected-room-type"></span><br>
                        <strong>Price:</strong> <span id="selected-room-price"></span> VND / night
                    </div>

                    <label for="checkin">Check-in Date:</label>
                    <input type="date" id="checkin" required>

                    <label for="checkout">Check-out Date:</label>
                    <input type="date" id="checkout" required>

                    <label for="guests">Guests:</label>
                    <input type="number" id="guests" min="1" max="4" value="1" required>

                    <div style="text-align: center;">
                        <button id="book-now" class="btn-book">Book Now</button>
                    </div>
                </div>

            <?php else: ?>
                <p>No available rooms at this hotel.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let selectedRoomId = null;
        let selectedRoomPrice = 0;

        // Set minimum dates (today for check-in, tomorrow for check-out)
        const today = new Date().toISOString().split('T')[0];
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];

        document.getElementById('checkin').min = today;
        document.getElementById('checkout').min = tomorrowStr;
        document.getElementById('checkin').value = today;
        document.getElementById('checkout').value = tomorrowStr;

        // Update checkout min date when checkin changes
        document.getElementById('checkin').addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            checkinDate.setDate(checkinDate.getDate() + 1);
            const minCheckout = checkinDate.toISOString().split('T')[0];
            document.getElementById('checkout').min = minCheckout;

            if (document.getElementById('checkout').value <= this.value) {
                document.getElementById('checkout').value = minCheckout;
            }
        });

        // Chọn phòng
        document.querySelectorAll('.btn-select').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove previous selection
                document.querySelectorAll('.room-card').forEach(card => card.classList.remove('selected'));
                document.querySelectorAll('.btn-select').forEach(b => {
                    b.classList.remove('selected');
                    b.textContent = 'Select this room';
                });

                // Add current selection
                selectedRoomId = this.getAttribute('data-room-id');
                selectedRoomPrice = this.getAttribute('data-room-price');

                const roomCard = document.getElementById('room-' + selectedRoomId);
                roomCard.classList.add('selected');

                this.classList.add('selected');
                this.textContent = 'Selected';

                // Show selected room info
                const roomType = roomCard.querySelector('h4').textContent;
                document.getElementById('selected-room-type').textContent = roomType;
                document.getElementById('selected-room-price').textContent = new Intl.NumberFormat('vi-VN').format(selectedRoomPrice);
                document.getElementById('selected-room-info').style.display = 'block';
            });
        });

        // Booking form
        document.getElementById('book-now').addEventListener('click', function() {
            let checkin = document.getElementById('checkin').value;
            let checkout = document.getElementById('checkout').value;
            let guests = document.getElementById('guests').value;

            if (!selectedRoomId) {
                alert("Please select a room before booking!");
                return;
            }
            if (!checkin || !checkout || guests <= 0) {
                alert("Please fill in all booking details.");
                return;
            }
            if (new Date(checkout) <= new Date(checkin)) {
                alert("Check-out date must be after check-in date.");
                return;
            }

            // Calculate total nights and price
            const checkinDate = new Date(checkin);
            const checkoutDate = new Date(checkout);
            const nights = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));
            const totalPrice = nights * selectedRoomPrice;

            const confirmMsg = `Booking Summary:
Room: ${document.getElementById('selected-room-type').textContent}
Check-in: ${checkin}
Check-out: ${checkout}
Nights: ${nights}
Guests: ${guests}
Total Price: ${new Intl.NumberFormat('vi-VN').format(totalPrice)} VND

Proceed with booking?`;

            if (confirm(confirmMsg)) {
                let url = "../booking/booking.php?hotel_id=<?php echo $hotel['id']; ?>" +
                    "&room_id=" + encodeURIComponent(selectedRoomId) +
                    "&checkin=" + encodeURIComponent(checkin) +
                    "&checkout=" + encodeURIComponent(checkout) +
                    "&guests=" + encodeURIComponent(guests) +
                    "&total_price=" + encodeURIComponent(totalPrice);

                window.location.href = url;
            }
        });
    </script>

</body>

</html>