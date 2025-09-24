<?php
session_start();
require_once 'config.php';

// --- กำหนดโซนที่ถูกเลือก (ถ้าไม่มีการส่งค่ามา ให้เป็น 'central' เป็นค่าเริ่มต้น) ---
$selected_zone = $_GET['zone'] ?? 'central';

// --- เปลี่ยนแปลงคำสั่ง SQL ให้ดึงข้อมูลตามโซน ---
$spot_prefix = 'A%'; // ค่าเริ่มต้นสำหรับ Central
if ($selected_zone === 'bigc') {
    $spot_prefix = 'B%'; // สมมติว่า Big C ใช้ตัวอักษร B
}

$parking_statuses = [];
// --- แก้ไข SQL ให้ใช้ตัวแปร $spot_prefix ---
$stmt = $conn->prepare("SELECT spot_name, status FROM parking_spots WHERE spot_name LIKE ? ORDER BY id");
$stmt->bind_param("s", $spot_prefix);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $parking_statuses[$row['spot_name']] = $row['status'];
    }
}
$stmt->close();
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
            font-family: 'Kanit', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding-top: 50px;
        }
        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            width: 90%;
            max-width: 800px;
        }
        h1, h2 {
            color: #333;
            text-align: left;
            margin-bottom: 20px;
        }

        /* === สไตล์สำหรับโซน === */
        .zone-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 25px;
        }
        .zone-button {
            padding: 10px 20px;
            font-family: 'Kanit', sans-serif;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 25px;
            background-color: #f8f9fa;
            color: #555;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .zone-button:hover {
            background-color: #e2e6ea;
            border-color: #ccc;
        }
        .zone-button.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
            font-weight: bold;
        }
        .zone-button.disabled {
            background-color: #e9ecef;
            color: #adb5bd;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* === Parking Grid & Spots === */
        .parking-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr); 
            gap: 15px;
            margin: 25px 0;
        }
        .spot {
            height: 60px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 500;
            font-size: 16px;
            color: #444;
            transition: all 0.2s ease;
        }
        .spot.available {
            background-color: #e9ecef;
            cursor: pointer;
        }
        .spot.occupied {
            background-color: #e57373;
            color: white;
            cursor: not-allowed;
        }
        .spot.selected {
            background-color: #81c784;
            color: white;
            transform: scale(1.05);
        }

        /* === Header Controls === */
        .header-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .header-controls .username {
            font-weight: bold;
        }
        .logout-button {
            background-color: #e57373;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
        }
        
        /* === สไตล์สำหรับปุ่มจอง === */
        #book-button {
            display: none;
            width: 100%;
            max-width: 300px;
            padding: 15px;
            margin: 25px auto 0 auto;
            font-family: 'Kanit', sans-serif;
            font-size: 18px;
            font-weight: bold;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-align: center;
        }
        #book-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
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
        <h1>เลือกโซนที่จอดรถ</h1>
        <div class="zone-selector">
            <a href="index.php?zone=central" class="zone-button <?php if($selected_zone == 'central') echo 'active'; ?>">
                เซ็นทรัล
            </a>
            <a href="#" class="zone-button disabled" onclick="return false;">
                บิ๊กซี (ยังไม่เปิดให้บริการ)
            </a>
        </div>
        
        <h2>ผังที่จอดรถ: <?php echo ($selected_zone == 'central') ? 'โซน A (เซ็นทรัล)' : 'โซน B (บิ๊กซี)'; ?></h2>
        
        <form action="payment.php" method="post" id="booking-form">
            <div class="parking-grid">
                <?php if (!empty($parking_statuses)): ?>
                    <?php foreach ($parking_statuses as $spot_id => $status): ?>
                        <div class="spot <?php echo $status; ?>" data-spot-id="<?php echo $spot_id; ?>">
                            <?php echo $spot_id; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>ไม่พบที่จอดรถในโซนนี้</p>
                <?php endif; ?>
            </div>
            <input type="hidden" name="selected_spot" id="selected-spot-input">
            <button type="submit" id="book-button">ยืนยันการเลือก</button>
        </form>
    </div>
    
    <script src="script.js"></script>
</body>
</html>