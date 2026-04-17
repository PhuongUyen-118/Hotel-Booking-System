<?php
// Khởi tạo session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
$isLoggedIn = !empty($_SESSION['user_id']);

require_once 'config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // ✅ Lưu thông tin user vào session ngay tại đây
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        header("Location: ../index.php"); // chuyển về trang chủ
        exit;
    } else {
        echo "Email hoặc mật khẩu không đúng";
    }
}

// Lấy danh sách khách sạn
$sql = "SELECT id, name, address, price, image FROM hotels";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Booking.com</title>
    <link rel="stylesheet" href="assets/image/css/index.css"> <!-- Tạo file style riêng nếu cần -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <!-- Header -->
    <header style="background-color:#003580; color:white; padding:10px 20px; display:flex; align-items:center;">
        <div style="font-size:20px; font-weight:bold;">Booking.com</div>
        <div style="flex:1;">
            <nav>
                <ul style="font-size:18px; list-style: none; display: flex; justify-content: center; align-items: center; gap: 32px; margin: 0; padding: 0;">
                    <li><a href="/hotel_booking/index.php" style="color: white; text-decoration: none; font-weight: bold;">Home</a></li>
                    <li><a href="/hotel_booking/contact.php" style="color: white; text-decoration: none; font-weight: bold;">Contact</a></li>

                    <?php if ($isLoggedIn): ?>
                        <li style="color: white; font-weight: bold;">
                            Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
                        </li>
                        <li>
                            <a href="/hotel_booking/auth/logout.php" style="color: white; text-decoration: none; font-weight: bold;">Logout</a>
                        </li>
                    <?php else: ?>
                        <li><a href="/hotel_booking/auth/login.php" style="color: white; text-decoration: none; font-weight: bold;">Login</a></li>
                        <li><a href="/hotel_booking/auth/register.php" style="color: white; text-decoration: none; font-weight: bold;">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <!-- Banner + Search -->
    <section style="background: url('assets/image/image/banner.png') center center/cover no-repeat; color:white; padding:80px 20px; text-align:center; position:relative;">
        <div style="position:absolute; inset:0; z-index:1; border-radius:16px;"></div>
        <div style="position:relative; z-index:2;">
            <h1 style="font-size:60px; color:white; text-align:left;">Find your next stay</h1>
            <p style="font-size:20px;color:white; text-align:left;"><b>Search low prices on hotels, homes and much more...</b></p>

            <form action="search.php" method="GET" style="margin-top:40px; display:flex; justify-content:center; gap:10px; flex-wrap:wrap; position:relative;">

                <!-- Ô địa điểm -->
                <div style="position:relative;">
                    <input type="text" name="location" id="locationInput" placeholder="Where are you going?" autocomplete="off"
                        style="padding:10px 10px 10px 35px; width:220px; border:2px solid orange; border-radius:8px; color:black;">
                    <i class="fa fa-bed" style="position:absolute; top:10px; left:10px; color:gray;"></i>

                    <!-- Gợi ý địa điểm -->
                    <div id="suggestions" style="position:absolute; top:45px; left:0; width:100%; background:white; border:1px solid #ccc; display:none; z-index:1000;">
                        <div style="font-weight:bold; padding:10px; color:black;">Trending destinations</div>
                        <?php
                        $locations = ["Nha Trang", "Da Lat", "Da Nang", "Vung Tau", "Hoi An",];
                        foreach ($locations as $loc) {
                            echo '<div class="suggestion-item" style="padding:10px; cursor:pointer; border-top:1px solid #eee; color:black;">
                        <i class="fa fa-map-marker-alt" style="margin-right:8px;"></i>' . $loc . '<br><small style="color:black;">Vietnam</small>
                      </div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Ngày -->
                <!-- Ngày -->
                <input type="text" name="checkin" id="checkin" placeholder="Check-in date" required
                    onfocus="(this.type='date')" onblur="if(this.value==''){this.type='text'}"
                    style="padding:10px; border:2px solid orange; border-radius:8px; color:black;">
                <input type="text" name="checkout" id="checkout" placeholder="Check-out date" required
                    onfocus="(this.type='date')" onblur="if(this.value==''){this.type='text'}"
                    style="padding:10px; border:2px solid orange; border-radius:8px; color:black;">

                <!-- Người/phòng -->
                <div style="position:relative;">
                    <input type="text" name="guests" id="guestsInput" readonly value="1 adult · 0 children · 1 room"
                        style="padding:10px 10px 10px 35px; width:250px; border:2px solid orange; border-radius:8px; cursor:pointer; color:black;">
                    <i class="fa fa-user" style="position:absolute; top:10px; left:10px; color:gray;"></i>

                    <!-- Dropdown chi tiết -->
                    <div id="guestDropdown" style="display:none; position:absolute; top:45px; left:0; background:white; border:1px solid #ccc; width:100%; padding:10px; z-index:999; color:black;">
                        <div style="display:flex; justify-content:space-between; margin:10px 0;">
                            <span style="color:black;">Adults</span>
                            <div>
                                <button type="button" onclick="adjustGuest('adults', -1)">-</button>
                                <span id="adultsCount" style="color:black;">1</span>
                                <button type="button" onclick="adjustGuest('adults', 1)">+</button>
                            </div>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin:10px 0;">
                            <span style="color:black;">Children</span>
                            <div>
                                <button type="button" onclick="adjustGuest('children', -1)">-</button>
                                <span id="childrenCount" style="color:black;">0</span>
                                <button type="button" onclick="adjustGuest('children', 1)">+</button>
                            </div>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin:10px 0;">
                            <span style="color:black;">Rooms</span>
                            <div>
                                <button type="button" onclick="adjustGuest('rooms', -1)">-</button>
                                <span id="roomsCount" style="color:black;">1</span>
                                <button type="button" onclick="adjustGuest('rooms', 1)">+</button>
                            </div>
                        </div>
                        <button type="button" onclick="closeGuestDropdown()" style="margin-top:10px;">Done</button>
                    </div>
                </div>

                <button type="submit" style="padding:10px 20px; background-color:#0071c2; color:white; border:none; cursor:pointer;">Search</button>
            </form>
    </section>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <title>Hotel Booking</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f8f8f8;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 1100px;
                margin: 40px auto;
                padding: 0 20px;
            }

            .hotel-list {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 24px;
            }

            .hotel-card {
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                transition: transform 0.2s ease;
            }

            .hotel-card:hover {
                transform: scale(1.02);
            }

            .hotel-card img {
                width: 100%;
                height: 180px;
                object-fit: cover;
            }

            .hotel-info {
                padding: 16px;
            }

            .hotel-info h2 {
                margin: 0 0 8px 0;
                font-size: 1.2rem;
                color: #222;
            }

            .hotel-info p {
                margin: 6px 0;
                color: #666;
            }

            .hotel-info .price {
                color: #0071c2;
                font-weight: bold;
                margin-top: 12px;
            }

            .btn-book {
                display: inline-block;
                margin-top: 12px;
                padding: 10px 20px;
                background-color: #0071c2;
                color: #fff;
                border: none;
                border-radius: 4px;
                text-decoration: none;
                font-weight: bold;
            }

            .btn-book:hover {
                background-color: #005b9f;
            }

            h1.title {
                text-align: center;
                margin-bottom: 40px;
                color: #333;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <h1 class="title">Available Hotels</h1>
            <div class="hotel-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="hotel-card">
                        <img src="image/<?php echo htmlspecialchars($row['image']); ?>" alt="Hotel Image">
                        <div class="hotel-info">
                            <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                            <p><?php echo htmlspecialchars($row['address']); ?></p>
                            <p class="price">
                                <?php echo number_format($row['price'], 0, ',', '.'); ?> VND / night
                            </p>
                            <a href="hotel/detail.php?id=<?php echo $row['id']; ?>" class="btn-book">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

    </body>

    </html>

    <!-- Footer -->
    <!-- Footer -->
    <footer style="background-color: #003580; color: #fff; padding: 40px 0; font-family: Arial, sans-serif;">
        <div style="max-width: 1200px; margin: auto; display: flex; flex-wrap: wrap; justify-content: space-between;">

            <!-- Giới thiệu -->
            <div style="flex: 1 1 250px; margin: 10px;">
                <h3 style="border-bottom: 2px solid #ffcc00; padding-bottom: 10px;">Booking.com</h3>
                <p>Book hotel rooms quickly, conveniently and at the best prices. Bring you a great travel experience.</p>
            </div>

            <!-- Liên hệ -->
            <div style="flex: 1 1 250px; margin: 10px;">
                <h4 style="border-bottom: 2px solid #ffcc00; padding-bottom: 10px;">Liên hệ</h4>
                <p>Email: support@hotelbooking.com</p>
                <p>Phone: +84 123 456 789</p>
                <p>Address: 123 Nguyen Hue, District 1, Ho Chi Minh City</p>
            </div>

            <!-- Liên kết nhanh -->
            <div style="flex: 1 1 250px; margin: 10px;">
                <h4 style="border-bottom: 2px solid #ffcc00; padding-bottom: 10px;">Quick Links</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="index.php" style="color: #fff; text-decoration: none;">Home</a></li>
                    <li><a href="search.php" style="color: #fff; text-decoration: none;">Search Hotels</a></li>
                    <li><a href="cart.php" style="color: #fff; text-decoration: none;">Cart</a></li>
                    <li><a href="auth/login.php" style="color: #fff; text-decoration: none;">Login</a></li>
                </ul>
            </div>

            <!-- Mạng xã hội -->
            <div style="flex: 1 1 250px; margin: 10px;">
                <h4 style="border-bottom: 2px solid #ffcc00; padding-bottom: 10px;">Connect with Us</h4>
                <a href="#" style="color: #fff; margin-right: 15px; font-size: 16px; text-decoration: none;">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a><br>
                <a href="#" style="color: #fff; margin-right: 15px; font-size: 16px; text-decoration: none;">
                    <i class="fab fa-instagram"></i> Instagram
                </a><br>
                <a href="#" style="color: #fff; font-size: 16px; text-decoration: none;">
                    <i class="fab fa-tiktok"></i> TikTok
                </a>
            </div>


            <!-- Copyright -->
        <div style="
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            position: relative;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;">
            <p style="
                margin: 0;
                padding: 0;
                font-size: 14px;
                color: inherit;
                line-height: 1.5;
                ">&copy; 2025 B. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="assets/image/js/script.js"></script>
</body>

</html>