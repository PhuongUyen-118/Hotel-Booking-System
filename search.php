<?php
include 'includes/db_connect.php';

// Lấy dữ liệu từ URL
$address = $_GET['location'] ?? '';
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';

// Nếu thiếu địa chỉ, không thực hiện tìm kiếm
if (!$address) {
    echo "No address provided.";
    exit;
}

// Chuẩn hóa chuỗi để tránh lỗi SQL injection
$address_safe = mysqli_real_escape_string($conn, $address);

// Truy vấn 1 khách sạn duy nhất theo địa chỉ chính xác
$sql = "SELECT hotels.*, images.image_path 
        FROM hotels 
        LEFT JOIN (
            SELECT hotel_id, MIN(image_path) AS image_path 
            FROM hotel_images 
            GROUP BY hotel_id
        ) AS images ON hotels.id = images.hotel_id
        WHERE hotels.address LIKE '%$address_safe%' 
        LIMIT 1";

$result = mysqli_query($conn, $sql);
$hotel = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Hotels</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
        }

        .search-summary {
            text-align: center;
            padding: 40px 20px 10px;
        }

        .search-summary h1 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .search-summary p {
            font-size: 18px;
            color: #555;
        }

        .hotel-list {
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .hotel-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 400px;
            transition: 0.3s ease;
        }

        .hotel-card:hover {
            transform: translateY(-5px);
        }

        .hotel-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .hotel-card h3 {
            margin: 15px;
            font-size: 22px;
            color: #2f3640;
        }

        .hotel-card p {
            margin: 0 15px 15px;
            color: #7f8c8d;
        }

        .hotel-card a {
            display: block;
            margin: 0 15px 20px;
            padding: 10px;
            text-align: center;
            background-color: #f39c12;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .hotel-card a:hover {
            background-color: #e67e22;
        }
    </style>
</head>
<body>

    <div class="search-summary">
        <h1>Available Hotel</h1>
        <p>
            You searched for "<strong><?php echo htmlspecialchars($address); ?></strong>" 
            from <strong><?php echo htmlspecialchars($checkin); ?></strong> 
            to <strong><?php echo htmlspecialchars($checkout); ?></strong>.
        </p>
    </div>

    <div class="hotel-list">
        <?php if ($hotel): ?>
            <div class="hotel-card">
                <img src="uploads/<?php echo $hotel['image_path'] ?? 'default.jpg'; ?>" alt="Hotel Image">
                <h3><?php echo htmlspecialchars($hotel['name']); ?></h3>
                <p><?php echo htmlspecialchars($hotel['address']); ?></p>
                <a href="hotel_detail.php?id=<?php echo $hotel['id']; ?>&checkin=<?php echo $checkin; ?>&checkout=<?php echo $checkout; ?>">View Details</a>
            </div>
        <?php else: ?>
            <p style="text-align: center;">No hotel found for "<?php echo htmlspecialchars($address); ?>".</p>
        <?php endif; ?>
    </div>

</body>
</html>
