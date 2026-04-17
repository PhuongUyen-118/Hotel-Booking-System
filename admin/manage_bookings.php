<?php
session_start();
require_once '../config/db_connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Xử lý xóa booking
if (isset($_GET['delete'])) {
    $booking_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    header("Location: manage_bookings.php");
    exit();
}

// Lấy danh sách bookings
$sql = "SELECT 
            bookings.id,
            users.fullname AS user_name,
            hotels.name AS hotel_name,
            bookings.check_in,
            bookings.check_out,
            bookings.total_price,
            bookings.status
        FROM bookings
        JOIN users ON bookings.user_id = users.id
        JOIN hotels ON bookings.hotel_id = hotels.id
        ORDER BY bookings.created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-primary mb-4">Booking Management</h2>

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <!-- Booking List -->
    <table class="table table-bordered table-striped shadow">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Hotel</th>
                <th>User</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Total ($)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['hotel_name']); ?></td>
                <td><?= htmlspecialchars($row['user_name']); ?></td>
                <td><?= $row['check_in']; ?></td>
                <td><?= $row['check_out']; ?></td>
                <td>$<?= number_format($row['total_price'], 2); ?></td>
                <td><?= htmlspecialchars($row['status']); ?></td>
                <td>
                    <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this booking?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
