<?php
session_start();
require_once('../config/db_connect.php');

if (!isset($_GET['id'])) {
    die('Invalid invoice id.');
}
$bookingId = intval($_GET['id']);

$sql = "
    SELECT b.id, b.check_in, b.check_out, b.status, b.created_at, b.total_price,
           r.type AS room_type, r.price AS nightly_price,
           h.name AS hotel_name, h.address AS hotel_address,
           u.name AS user_name, u.email AS user_email
    FROM bookings b
    JOIN rooms r ON r.id = b.room_id
    JOIN hotels h ON h.id = r.hotel_id
    JOIN users u ON u.id = b.user_id
    WHERE b.id = ?
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $bookingId);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die('Invoice not found.');
}
$data = $res->fetch_assoc();

$nights = max(0, (int)date_diff(date_create($data['check_in']), date_create($data['check_out']))->days);
$nightly = (float)$data['nightly_price'];
$total = $data['total_price'] !== null ? (float)$data['total_price'] : $nights * $nightly;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $data['id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#f6f7fb; }
        .container { max-width: 800px; margin: 28px auto; background:#fff; padding: 28px; border-radius: 10px; box-shadow:0 8px 24px rgba(0,0,0,.08); }
        .header { display:flex; justify-content: space-between; align-items:center; margin-bottom:16px; }
        .muted { color:#6b7280; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        table { width:100%; border-collapse: collapse; margin-top: 16px; }
        th,td { padding: 10px; border-bottom:1px solid #eee; text-align:left; }
        .right { text-align:right; }
        .total { font-weight: 700; }
        .btn { display:inline-block; padding:10px 14px; background:#0b66c3; color:#fff; text-decoration:none; border-radius:6px; }
        .btn:hover { background:#0a57a4; }
        .toolbar { display:flex; justify-content: space-between; margin-top: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Invoice</h2>
                <div class="muted">#<?php echo $data['id']; ?> • <?php echo htmlspecialchars($data['status']); ?> • <?php echo $data['created_at']; ?></div>
            </div>
            <a href="javascript:window.print()" class="btn">Print</a>
        </div>

        <div class="grid">
            <div>
                <h3>Bill To</h3>
                <div><?php echo htmlspecialchars($data['user_name']); ?></div>
                <div class="muted"><?php echo htmlspecialchars($data['user_email']); ?></div>
            </div>
            <div>
                <h3>Hotel</h3>
                <div><?php echo htmlspecialchars($data['hotel_name']); ?></div>
                <div class="muted"><?php echo htmlspecialchars($data['hotel_address']); ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="right">Nights</th>
                    <th class="right">Nightly</th>
                    <th class="right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Room: <?php echo htmlspecialchars($data['room_type']); ?> (Check-in: <?php echo $data['check_in']; ?>, Check-out: <?php echo $data['check_out']; ?>)</td>
                    <td class="right"><?php echo $nights; ?></td>
                    <td class="right"><?php echo number_format($nightly, 2); ?></td>
                    <td class="right"><?php echo number_format($nightly * $nights, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="right total">Total</td>
                    <td class="right total"><?php echo number_format($total, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="toolbar">
            <a class="btn" href="my_booking.php">Back to My Bookings</a>
            <a class="btn" href="../hotel/detail.php?id=<?php echo isset($_GET['hotel']) ? intval($_GET['hotel']) : 1; ?>">Book Another Room</a>
        </div>
    </div>
</body>
</html>


