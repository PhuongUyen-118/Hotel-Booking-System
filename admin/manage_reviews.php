<?php
include("../includes/db_connect.php");
include("admin_header.php");

// Kiểm tra đăng nhập admin
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Lấy danh sách đánh giá
$sql = "SELECT reviews.id, users.name AS user_name, hotels.name AS hotel_name, reviews.rating, reviews.comment, reviews.created_at 
        FROM reviews 
        JOIN users ON reviews.user_id = users.id 
        JOIN hotels ON reviews.hotel_id = hotels.id 
        ORDER BY reviews.created_at DESC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2>Manage Reviews</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Hotel</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["id"] ?></td>
                        <td><?= htmlspecialchars($row["user_name"]) ?></td>
                        <td><?= htmlspecialchars($row["hotel_name"]) ?></td>
                        <td><?= $row["rating"] ?>/5</td>
                        <td><?= htmlspecialchars($row["comment"]) ?></td>
                        <td><?= $row["created_at"] ?></td>
                        <td>
                            <form method="post" action="delete_review.php" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No reviews found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("admin_footer.php"); ?>
