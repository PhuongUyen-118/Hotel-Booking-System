<?php
session_start();
require_once '../config/db_connect.php';

// Check if admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Handle hotel deletion
if (isset($_GET['delete'])) {
    $hotel_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    header("Location: manage_hotels.php");
    exit();
}

// Handle hotel addition
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $location = trim($_POST["location"]);
    $price = floatval($_POST["price"]);
    $description = trim($_POST["description"]);
    $image = trim($_POST["image"]);

    $stmt = $conn->prepare("INSERT INTO hotels (name, location, price, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $location, $price, $description, $image);
    $stmt->execute();
    header("Location: manage_hotels.php");
    exit();
}

// Get hotel list
$result = $conn->query("SELECT * FROM hotels ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Hotels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-primary mb-4">Hotel Management</h2>

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <!-- Add Hotel Form -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-success text-white">Add New Hotel</div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label>Hotel Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Price per Night ($)</label>
                    <input type="number" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Image URL</label>
                    <input type="text" name="image" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Add Hotel</button>
            </form>
        </div>
    </div>

    <!-- Hotel List -->
    <table class="table table-bordered table-striped shadow">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Location</th>
                <th>Price</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($hotel = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $hotel['id']; ?></td>
                <td><?= htmlspecialchars($hotel['name']); ?></td>
                <td><?= htmlspecialchars($hotel['location']); ?></td>
                <td>$<?= number_format($hotel['price'], 2); ?></td>
                <td><?= htmlspecialchars($hotel['description']); ?></td>
                <td><img src="<?= htmlspecialchars($hotel['image']); ?>" alt="Hotel" style="width: 80px;"></td>
                <td>
                    <a href="?delete=<?= $hotel['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this hotel?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
