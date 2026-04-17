<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/db_connect.php';

// Fetch admin info
$admin_id = $_SESSION["user_id"];
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-primary">Admin Dashboard</h2>

        <p><strong>Welcome,</strong> <?php echo htmlspecialchars($admin['name']); ?> (<?php echo htmlspecialchars($admin['email']); ?>)</p>

        <div class="mt-4 d-grid gap-3">
            <a href="manage_users.php" class="btn btn-outline-primary">Manage Users</a>
            <a href="manage_hotels.php" class="btn btn-outline-success">Manage Hotels</a>
            <a href="manage_rooms.php" class="btn btn-outline-info">Manage Rooms</a>
            <a href="manage_bookings.php" class="btn btn-outline-warning">Manage Bookings</a>
            <a href="manage_reviews.php" class="btn btn-outline-secondary">Manage Reviews</a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

</body>
</html>
