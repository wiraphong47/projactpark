<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// ดึงข้อมูลการจองของ user คนนี้
$bookings = [];
$stmt = $conn->prepare("SELECT spot_name FROM parking_spots WHERE booked_by_user = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>การจองของฉัน</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <h1>การจองของฉัน (<?php echo htmlspecialchars($username); ?>)</h1>
        <?php if (!empty($bookings)): ?>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($bookings as $booking): ?>
                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 10px;">
                        <span style="font-size: 18px;">ที่จอด: <strong><?php echo $booking['spot_name']; ?></strong></span>
                        <form action="cancel_booking.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="spot_name" value="<?php echo $booking['spot_name']; ?>">
                            <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer;">ยกเลิกการจอง</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>คุณยังไม่มีรายการจอง</p>
        <?php endif; ?>
        <a href="index.php" style="display:inline-block; margin-top:20px;">กลับสู่หน้าหลัก</a>
    </div>
</body>
</html>