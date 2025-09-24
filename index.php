<?php
session_start();
require_once 'config.php';

// --- 1. สุ่มสถานะที่จอดรถในฐานข้อมูลใหม่ทุกครั้งที่รีเฟรช ---
// หมายเหตุ: บรรทัดนี้จะเปลี่ยนข้อมูลใน Database จริงๆ ทุกครั้งที่เข้ามาหน้านี้
$conn->query("UPDATE parking_spots SET status = IF(RAND() > 0.5, 'occupied', 'available') WHERE spot_name LIKE 'A%'");


// --- 2. ดึงข้อมูลสถานะล่าสุดและนับจำนวน ---
$parking_statuses = [];
$available_count = 0;
$occupied_count = 0;

$result = $conn->query("SELECT spot_name, status FROM parking_spots WHERE spot_name LIKE 'A%' ORDER BY id");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $parking_statuses[$row['spot_name']] = $row['status'];
        // นับจำนวนสถานะ
        if ($row['status'] == 'available') {
            $available_count++;
        } else {
            $occupied_count++;
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองที่จอดรถออนไลน์</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif; background-color: #f4f4f9; display: flex;
            justify-content: center; align-items: flex-start; min-height: 100vh; margin: 0; padding-top: 50px;
        }
        .container {
            background-color: white; padding: 30px 40px; border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 90%; max-width: 800px;
        }
        h1 { color: #333; text-align: left; margin-bottom: 20px; }

        /* === สไตล์สำหรับสรุปสถานะ === */
        .status-summary {
            display: flex; gap: 20px; padding: 15px; background-color: #f8f9fa;
            border-radius: 8px; margin-bottom: 25px; border: 1px solid #e9ecef;
        }
        .summary-item { display: flex; align-items: center; gap: 8px; font-size: 16px; }
        .summary-item .color-box { width: 20px; height: 20px; border-radius: 4px; }
        .summary-item .available-box { background-color: #28a745; }
        .summary-item .occupied-box { background-color: #dc3545; }
        .summary-item span { font-weight: bold; font-size: 18px; }

        .parking-grid {
            display: grid; grid-template-columns: repeat(5, 1fr); 
            gap: 15px; margin: 25px 0;
        }
        .spot {
            height: 60px; border-radius: 10px; display: flex; justify-content: center;
            align-items: center; font-weight: 500; font-size: 16px; color: white;
            transition: all 0.2s ease;
        }
        .spot.available { background-color: #28a745; cursor: pointer; }
        .spot.occupied { background-color: #dc3545; cursor: not-allowed; }
        .spot.selected { background-color: #ffc107; color: #333; transform: scale(1.1); border: 2px solid #e0a800;}
        
        .header-controls { position: absolute; top: 20px; right: 20px; display: flex; align-items: center; gap: 15px; }
        .header-controls .username { font-weight: bold; }
        .logout-button {
            background-color: #e57373; color: white; padding: 8px 15px;
            text-decoration: none; border-radius: 20px; font-size: 14px;
        }
        #book-button {
            display: none; width: 100%; max-width: 300px; padding: 15px; margin: 25px auto 0 auto;
            font-family: 'Kanit', sans-serif; font-size: 18px; font-weight: bold; color: white;
            background-color: #007bff; border: none; border-radius: 30px; cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease; text-align: center;
        }
        #book-button:hover { background-color: #0056b3; transform: scale(1.05); }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['username'])): ?>
        <div class="header-controls">
            <span class="username">สวัสดี, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="logout-button">ออกจากระบบ</a>
        </div>
    <?php endif; ?>

    <div class="container">
        <h1>ผังที่จอดรถ</h1>
        
        <div class="status-summary">
            <div class="summary-item">
                <div class="color-box available-box"></div>
                ว่าง: <span><?php echo $available_count; ?></span> ช่อง
            </div>
            <div class="summary-item">
                <div class="color-box occupied-box"></div>
                ไม่ว่าง: <span><?php echo $occupied_count; ?></span> ช่อง
            </div>
        </div>
        
        <form action="payment.php" method="post" id="booking-form">
            <div class="parking-grid">
                <?php foreach ($parking_statuses as $spot_id => $status): ?>
                    <div class="spot <?php echo $status; ?>" data-spot-id="<?php echo $spot_id; ?>">
                        <?php echo $spot_id; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="selected_spot" id="selected-spot-input">
            <button type="submit" id="book-button">ยืนยันการเลือก</button>
        </form>
    </div>
    
    <script src="script.js"></script>
</body>
</html>