<?php
session_start();
require_once 'config.php';

// ตรวจสอบว่าเป็น Admin หรือไม่
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php?error=กรุณาเข้าสู่ระบบสำหรับผู้ดูแล');
    exit();
}

// ดึงข้อมูลที่จอดรถทั้งหมด
$spots = [];
$result = $conn->query("SELECT spot_name, status, booked_by_user FROM parking_spots ORDER BY id");
if ($result) { while($row = $result->fetch_assoc()) { $spots[] = $row; } }
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .reset-button {
            background-color: #ffc107; color: black; border: none;
            padding: 5px 10px; border-radius: 5px; cursor: pointer;
            font-size: 12px;
        }
        .spot { flex-direction: column; } /* ให้ปุ่มอยู่ใต้ชื่อช่องจอด */
    </style>
</head>
<body>
    <div class="container" style="max-width: 900px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>แผงควบคุมผู้ดูแลระบบ</h1>
            <a href="logout.php">ออกจากระบบ</a>
        </div>
        <p>คุณกำลังล็อกอินในฐานะ: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        
        <div class="parking-grid" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));">
            <?php foreach ($spots as $spot): ?>
                <div class="spot <?php echo $spot['status']; ?>" title="จองโดย: <?php echo htmlspecialchars($spot['booked_by_user'] ?? 'N/A'); ?>">
                    <?php echo $spot['spot_name']; ?>
                    <?php if ($spot['status'] === 'occupied'): ?>
                        <form action="admin_reset_spot.php" method="POST" style="margin-top: 5px;">
                            <input type="hidden" name="spot_name" value="<?php echo $spot['spot_name']; ?>">
                            <button type="submit" class="reset-button">รีเซ็ต</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>