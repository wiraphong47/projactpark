<?php
require_once 'config.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลสถานะที่จอดรถทั้งหมดจากฐานข้อมูล
$parking_statuses = [];
$result = $conn->query("SELECT spot_name, status FROM parking_spots WHERE spot_name LIKE 'A%' ORDER BY id");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $parking_statuses[$row['spot_name']] = $row['status'];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองที่จอดรถออนไลน์</title>
    
    <style>
        /* General Body Styles */
        body {
            font-family: 'Kanit', sans-serif; /* แนะนำให้เพิ่ม Google Font 'Kanit' เพื่อความสวยงาม */
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding-top: 50px;
        }

        /* Main Container */
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

        h1 {
            font-size: 28px;
        }

        h2 {
            font-size: 22px;
            font-weight: normal;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        /* Location Pins */
        .location-selector {
            margin-bottom: 25px;
        }

        .pin {
            background-color: #f0f0f0;
            color: #555;
            border: 1px solid #ddd;
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 16px;
            font-family: 'Kanit', sans-serif;
            cursor: pointer;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .pin.active, .pin:hover {
            background-color: #d8d8d8;
            border-color: #ccc;
        }

        /* Parking Grid Layout */
        .parking-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr); 
            gap: 15px;
            margin: 25px 0;
        }

        /* Parking Spots */
        .spot {
            height: 60px;
            border: none;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 500;
            font-size: 16px;
            color: #444;
            transition: all 0.2s ease;
        }

        /* Spot Status Colors */
        .spot.available {
            background-color: #e9ecef; /* สีเทา = ว่าง */
            cursor: pointer;
        }

        .spot.available:hover {
            background-color: #ced4da;
            transform: translateY(-2px);
        }

        .spot.occupied {
            background-color: #e57373; /* สีแดง = ไม่ว่าง */
            color: white;
            cursor: not-allowed;
        }

        .spot.selected {
            background-color: #81c784; /* สีเขียว = ที่เราเลือก */
            color: white;
            transform: scale(1.05);
        }

        /* Booking Button */
        #book-button {
            width: 167px;
            height: 74px;
            border: none;
            border-radius: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            color: #333;
            font-family: 'Kanit', sans-serif;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        #book-button:hover {
            background-color: #d8d8d8;
        }

        
        /* ... (โค้ด CSS เดิมทั้งหมดของคุณ) ... */

        /* ▼▼▼ ส่วนที่ 2: เพิ่มโค้ด CSS สำหรับปุ่ม Logout ▼▼▼ */
        .header-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 15px; /* ระยะห่างระหว่างชื่อกับปุ่ม */
        }
        .header-controls .username {
            font-weight: bold;
            color: #555;
        }
        .logout-button {
            background-color: #e57373; /* สีแดง */
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
            font-family: 'Kanit', sans-serif;
            transition: background-color 0.3s;
        }
        .logout-button:hover {
            background-color: #ef5350;
        }
    
    </style>
</head>
<body>
    <div class="container">

    <?php if (isset($_SESSION['username'])): ?>
        <div class="header-controls">
            <span class="username">สวัสดี, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="logout-button">ออกจากระบบ</a>
        </div>
    <?php endif; ?>

        <h1>เลือกโซนที่จอดรถ</h1>

        <div class="location-selector">
            <button class="pin active">เซ็นทรัล</button>
            <button class="pin">บิ๊กซี (ยังไม่เปิด)</button>
        </div>

        <h2>ผังที่จอดรถ โซน A</h2>
        
        <form action="payment.php" method="post" id="booking-form">
            <div class="parking-grid">
                <?php foreach ($parking_statuses as $spot_id => $status): ?>
                    <div class="spot <?php echo $status; ?>" data-spot-id="<?php echo $spot_id; ?>">
                        <?php echo $spot_id; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <input type="hidden" name="selected_spot" id="selected-spot-input">
            
            <button type="submit" id="book-button" style="display: none;">จอง</button>
        </form>
    </div>
    
    <script src="script.js"></script>
</body>
</html>