<?php
session_start();
require_once '../config/db_connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Xử lý thêm phòng
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hotel_id = $_POST['hotel_id'];
    $room_name = $_POST['room_name'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO rooms (hotel_id, name, type, price, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issds", $hotel_id, $room_name, $room_type, $price, $status);
    $stmt->execute();
}

// Xử lý xoá phòng
if (isset($_GET['delete'])) {
    $room_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    header("Location: manage_rooms.php");
    exit();
}

// Lấy danh sách phòng
$sql = "SELECT rooms.id, rooms.name AS room_name, rooms.type, rooms.price, rooms.status, hotels.name AS hotel_name
        FROM rooms
        JOIN hotels ON rooms.hotel_id = hotels.id
        ORDER BY rooms.id DESC";
$result = $conn->query($sql);

// Lấy danh sách khách sạn để chọn khi thêm phòng
$hotels = $conn->query("SELECT id, name FROM hotels");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-primary mb-4">Room Management</h2>

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <!-- Add Room Form -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">Add New Room</div>
        <div class="card-body">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col">
                        <label>Hotel</label>
                        <select name="hotel_id" class="form-control" required>
                            <option value="">-- Select Hotel --</option>
                            <?php while ($hotel = $hotels->fetch_assoc()) : ?>
                                <option value="<?= $hotel['id']; ?>"><?= htmlspecialchars($hotel['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col">
                        <label>Room Name</label>
                        <input type="text" name="room_name" class="form-control" required>
                    </div>
                    <div class="col">
                        <label>Room Type</label>
                        <input type="text" name="room_type" class="form-control" placeholder="e.g. Deluxe, Suite" required>
                    </div>
                    <div class="col">
                        <label>Price ($)</label>
                        <input type="number" name="price" step="0.01" class="form-control" required>
                    </div>
                    <div class="col">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-success" type="submit">Add Room</button>
            </form>
        </div>
    </div>

    <!-- Room List -->
    <table class="table table-bordered table-striped shadow">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Hotel</th>
                <th>Room Name</th>
                <th>Type</th>
                <th>Price ($)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['hotel_name']); ?></td>
                <td><?= htmlspecialchars($row['room_name']); ?></td>
                <td><?= htmlspecialchars($row['type']); ?></td>
                <td>$<?= number_format($row['price'], 2); ?></td>
                <td><?= htmlspecialchars($row['status']); ?></td>
                <td>
                    <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this room?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
