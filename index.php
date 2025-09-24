<?php
session_start();
require_once 'config.php';

// ดึงข้อมูลสถานะที่จอดรถทั้งหมดจากฐานข้อมูล
$parking_statuses = [];
$result = $conn->query("SELECT spot_name, status FROM parking_spots WHERE spot_name LIKE 'A%' ORDER BY id");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $parking_statuses[$row['spot_name']] = $row['status'];
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
        }
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