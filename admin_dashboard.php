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
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* === General Styles === */
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 20px;
        }

        /* === Main Container === */
        .container {
            max-width: 900px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* === Header Section === */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header-section h1 {
            font-size: 28px;
            color: #007bff;
            margin: 0;
        }

        .header-section a {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .header-section a:hover {
            background-color: #c82333;
        }

        .welcome-message {
            margin-bottom: 25px;
            font-size: 16px;
            color: #6c757d;
        }

        /* === Parking Grid === */
        .parking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 20px;
        }

        .spot {
            height: 80px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 16px;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            position: relative;
            overflow: hidden;
        }

        .spot:hover {
            transform: translateY(-5px);
        }

        .spot.available {
            background-color: #6c757d; /* สีเทาเข้มสำหรับช่องว่าง */
        }

        .spot.occupied {
            background-color: #e57373; /* สีแดงสำหรับช่องที่ถูกจอง */
        }

        .spot-name {
            margin-bottom: 5px;
        }

        .booked-by {
            font-size: 11px;
            font-weight: normal;
            position: absolute;
            bottom: 5px;
            color: rgba(255, 255, 255, 0.8);
        }

        /* === Reset Button === */
        .reset-button {
            background-color: #ffc107;
            color: #212529;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            margin-top: 8px;
            transition: background-color 0.3s;
        }

        .reset-button:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <h1>แผงควบคุมผู้ดูแลระบบ</h1>
            <a href="logout.php">ออกจากระบบ</a>
        </div>
        <p class="welcome-message">ยินดีต้อนรับ, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        
        <div class="parking-grid">
            <?php foreach ($spots as $spot): ?>
                <div class="spot <?php echo $spot['status']; ?>">
                    <span class="spot-name"><?php echo $spot['spot_name']; ?></span>
                    <?php if ($spot['status'] === 'occupied'): ?>
                        <span class="booked-by">จองโดย: <?php echo htmlspecialchars($spot['booked_by_user']); ?></span>
                        <form action="admin_reset_spot.php" method="POST" style="margin:0;">
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