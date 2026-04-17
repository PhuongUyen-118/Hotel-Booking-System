<?php
session_start();

// Check if user is logged in and is a normal user
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "user") {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/db_connect.php';

// Fetch user info from DB (optional)
$user_id = $_SESSION["user_id"];
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
        
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

        <div class="mt-4">
            <a href="../hotel/list.php" class="btn btn-primary">Browse Hotels</a>
            <a href="my_bookings.php" class="btn btn-success">My Bookings</a>
            <a href="edit_profile.php" class="btn btn-secondary">Edit Profile</a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

</body>
</html>
