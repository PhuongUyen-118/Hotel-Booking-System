<?php
require_once 'config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        $status = "success";
    } else {
        $status = "error";
    }
} else {
    header("Location: contact.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width:700px;margin:50px auto;text-align:center;padding:20px;background:#fff;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
    <?php if ($status == "success"): ?>
        <div style="padding:20px;background:#d4edda;color:#155724;border-radius:8px;margin-bottom:20px;">
            🎉 <strong>Phản hồi của bạn đã được gửi thành công!</strong><br>
            Chúng tôi trân trọng ý kiến đóng góp của bạn và sẽ liên hệ lại sớm nhất.
        </div>
        <a href="index.php" style="padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;">🏠 Quay lại trang chủ</a>
        <a href="contact.php" style="padding:10px 20px;background:#17a2b8;color:white;text-decoration:none;border-radius:5px;margin-left:10px;">✏️ Gửi phản hồi mới</a>
    <?php else: ?>
        <div style="padding:20px;background:#f8d7da;color:#721c24;border-radius:8px;margin-bottom:20px;">
            ⚠️ <strong>Rất tiếc!</strong> Đã xảy ra lỗi khi gửi phản hồi. Vui lòng thử lại sau.
        </div>
        <a href="contact.php" style="padding:10px 20px;background:#dc3545;color:white;text-decoration:none;border-radius:5px;">🔄 Thử lại</a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
