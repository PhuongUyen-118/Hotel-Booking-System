<?php
include("../includes/db_connect.php");
session_start();

// Chỉ cho phép admin xóa
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["review_id"])) {
    $review_id = $_POST["review_id"];

    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);
    if ($stmt->execute()) {
        header("Location: manage_review.php");
        exit();
    } else {
        echo "Error deleting review.";
    }
}
?>
