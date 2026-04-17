<?php
session_start();
require_once('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$userId = intval($_SESSION['user_id']);

// Cancel booking (GET for simplicity; in production use POST with a CSRF token)
if (isset($_GET['cancel']) && isset($_GET['id'])) {
    $bookingId = intval($_GET['id']);
    // Only allow canceling own bookings which are still pending/confirmed
    $sqlCancel = "UPDATE bookings SET status='cancelled' WHERE id=? AND user_id=? AND status IN ('pending','confirmed')";
    $stmtCancel = $conn->prepare($sqlCancel);
    $stmtCancel->bind_param('ii', $bookingId, $userId);
    $stmtCancel->execute();
    header('Location: my_booking.php?cancelled=' . ($stmtCancel->affected_rows > 0 ? '1' : '0'));
    exit;
}

// Fetch user's bookings
$sql = "
    SELECT b.id, b.check_in, b.check_out, b.status,
           r.type AS room_type, r.id AS room_id,
           h.name AS hotel_name, h.id AS hotel_id
    FROM bookings b
    JOIN rooms r ON r.id = b.room_id
    JOIN hotels h ON h.id = r.hotel_id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$bookings = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background:#f6f7fb; margin:0; }
        .container { max-width: 980px; margin: 32px auto; background:#fff; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.08); padding: 24px 28px; }
        h1 { margin: 0 0 12px; }
        .notice { padding: 12px 14px; border-radius:8px; margin-bottom:16px; }
        .success { background:#e8f7ee; color:#0f8a3b; border:1px solid #bfe8cf; }
        .error { background:#ffecec; color:#c20d0d; border:1px solid #ffc4c4; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:12px 10px; border-bottom:1px solid #eee; text-align:left; }
        th { background:#fafafa; }
        .badge { padding:4px 8px; border-radius: 999px; font-size:12px; font-weight:600; }
        .pending { background:#fff5e6; color:#b26a00; }
        .confirmed { background:#e6f4ff; color:#0b66c3; }
        .cancelled { background:#f3f4f6; color:#6b7280; }
        .btn { display:inline-block; padding:8px 12px; background:#ef4444; color:#fff; border-radius:6px; text-decoration:none; font-weight:600; }
        .btn:hover { background:#dc2626; }
        .actions { white-space: nowrap; }
        .topbar { display:flex; align-items:center; justify-content:space-between; margin-bottom: 16px; }
        .topbar a { text-decoration:none; color:#0b66c3; }
    </style>
    <script>
        function confirmCancel(id) {
            if (confirm('Are you sure you want to cancel booking #' + id + '?')) {
                window.location.href = 'my_booking.php?cancel=1&id=' + id;
            }
        }
    </script>
    </head>
<body>
    <div class="container">
        <div class="topbar">
            <h1>My Bookings</h1>
            <a href="../index.php">← Back to Home</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="notice success">✅ Booking created successfully! ID: #<?php echo intval($_GET['booking_id'] ?? 0); ?><?php if(isset($_GET['total'])) { echo ' • Total: ' . number_format((float)$_GET['total'], 2) . ' • Nights: ' . intval($_GET['nights']); } ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['cancelled'])): ?>
            <?php if ($_GET['cancelled'] === '1'): ?>
                <div class="notice success">✅ Booking cancelled successfully.</div>
            <?php else: ?>
                <div class="notice error">❌ Unable to cancel booking (it may have been cancelled already).</div>
            <?php endif; ?>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hotel</th>
                    <th>Room Type</th>
                    <th>Check-in - Check-out</th>
                    <th>Status</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($bookings->num_rows > 0): ?>
                    <?php while ($bk = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $bk['id']; ?></td>
                            <td><a href="../hotel/detail.php?id=<?php echo $bk['hotel_id']; ?>"><?php echo htmlspecialchars($bk['hotel_name']); ?></a></td>
                            <td><?php echo htmlspecialchars($bk['room_type']); ?></td>
                            <td><?php echo htmlspecialchars($bk['check_in']); ?> → <?php echo htmlspecialchars($bk['check_out']); ?></td>
                            <td>
                                <?php
                                    $cls = $bk['status'];
                                    echo '<span class="badge ' . $cls . '">' . strtoupper($bk['status']) . '</span>';
                                ?>
                            </td>
                            <td class="actions">
                                <?php if (in_array($bk['status'], ['pending','confirmed'])): ?>
                                    <a href="javascript:void(0)" class="btn" onclick="confirmCancel(<?php echo $bk['id']; ?>)">Cancel</a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">You have no bookings yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>


