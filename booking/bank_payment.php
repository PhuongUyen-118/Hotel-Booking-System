<?php
session_start();

// Lấy thông tin booking từ session hoặc database
$booking_id = $_GET['booking_id'] ?? '';
$total_price = $_GET['amount'] ?? 0;
$customer_name = $_SESSION['user_name'] ?? 'Guest';
$bank_name = "Vietcombank";
$bank_account = "0123456789";
$bank_owner = "Nguyen Van A";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bank Transfer Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
        }

        .invoice {
            max-width: 600px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .invoice h2 {
            text-align: center;
            color: #003580;
        }

        .invoice table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .invoice table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .total {
            font-weight: bold;
            color: #e63946;
            font-size: 18px;
        }

        .bank-info {
            background: #f0f4ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .bank-info p {
            margin: 5px 0;
        }
    </style>
</head>

<body>

    <div class="invoice">
        <h2>Bank Transfer Payment</h2>
        <p style="text-align:center;">Please transfer to the bank account below</p>

        <table>
            <tr>
                <td>Booking ID:</td>
                <td><?php echo htmlspecialchars($booking_id); ?></td>
            </tr>
            <tr>
                <td>Customer Name:</td>
                <td><?php echo htmlspecialchars($customer_name); ?></td>
            </tr>
            <tr>
                <td>Total Amount:</td>
                <td class="total"><?php echo number_format($total_price, 0, ',', '.'); ?> VND</td>
            </tr>
        </table>

        <div class="bank-info">
            <p><strong>Bank Name:</strong> <?php echo $bank_name; ?></p>
            <p><strong>Account Number:</strong> <?php echo $bank_account; ?></p>
            <p><strong>Account Holder:</strong> <?php echo $bank_owner; ?></p>
            <p><strong>Transfer Note:</strong> BOOKING <?php echo htmlspecialchars($booking_id); ?></p>
        </div>

        <!-- Thêm mã QR ở đây -->
        <div style="text-align:center; margin-top:20px;">
            <p>Scan the QR code to make payment faster:</p>
            <img src="/hotel_booking/assets/image/image/QR_bank.jpg"  alt="QR Code"
                style="width:200px;height:200px;border:1px solid #ccc;padding:5px;border-radius:8px;">
        </div>


        <p style="text-align:center; margin-top:20px;">After transfer, please wait for confirmation.</p>
    </div>

    <script>
    // Giả lập thanh toán thành công sau khi người dùng quét QR
    function paymentSuccess() {
        alert("Payment successful! Thank you for your booking.");
        // Chuyển về trang chủ sau 2 giây
        setTimeout(function() {
            window.location.href = "/hotel_booking/index.php"; // Đường dẫn trang chủ
        }, 2000);
    }

    // Giả lập: Khi người dùng nhấn vào QR code, gọi hàm paymentSuccess
    const qrImage = document.querySelector("img[alt='QR Code']");
    qrImage.addEventListener("click", function() {
        paymentSuccess();
    });
</script>

</body>

</html>