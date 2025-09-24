<?php
session_start();
require_once 'config.php';

// --- ดึงข้อมูลโซนทั้งหมดจากฐานข้อมูล ---
$zones = [];
$zone_result = $conn->query("SELECT * FROM zones ORDER BY id");
if ($zone_result) {
    while($row = $zone_result->fetch_assoc()) {
        $zones[] = $row;
    }
}

// --- กำหนดโซนที่ถูกเลือก (ถ้าไม่มี ให้เลือกโซนแรกเป็นค่าเริ่มต้น) ---
$selected_zone_id = $_GET['zone'] ?? ($zones[0]['id'] ?? 0);

// ปิดการเชื่อมต่อชั่วคราว
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

        /* === สไตล์สำหรับปุ่มเลือกโซน === */
        .zone-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #eee;
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
        
        /* === สไตล์สำหรับฟอร์มค้นหาใหม่ === */
        .search-criteria {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 25px;
        }
        .search-criteria .form-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-criteria .form-group label {
            flex-basis: 120px;
        }
        .search-criteria .form-group input,
        .search-criteria .form-group select {
            flex-grow: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        #search-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 30px;
            background-color: #007bff;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #search-button:hover {
            background-color: #0056b3;
        }
        .header-controls { position: absolute; top: 20px; right: 20px; display: flex; align-items: center; gap: 15px; }
        .header-controls .username { font-weight: bold; }
        .logout-button {
            background-color: #e57373; color: white; padding: 8px 15px;
            text-decoration: none; border-radius: 20px; font-size: 14px;
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
        <h1>ค้นหาที่จอดรถ</h1>
        
        <div class="zone-selector">
            <?php if (!empty($zones)): ?>
                <?php foreach ($zones as $zone): ?>
                    <a href="index.php?zone=<?php echo $zone['id']; ?>" 
                       class="zone-button <?php if($zone['id'] == $selected_zone_id) echo 'active'; ?>">
                        <?php echo htmlspecialchars($zone['name']); ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>ยังไม่มีโซนที่จอดรถให้บริการ</p>
            <?php endif; ?>
        </div>
        
        <form action="park.php" method="get" id="search-form">
            <input type="hidden" name="zone" value="<?php echo $selected_zone_id; ?>">
            <div class="search-criteria">
                <div class="form-group">
                    <label for="spot_type">ประเภทที่จอด:</label>
                    <select id="spot_type" name="spot_type">
                        <option value="all">ทั้งหมด</option>
                        <option value="car">รถยนต์</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_time">เวลาเข้า:</label>
                    <input type="datetime-local" id="start_time" name="start_time" required>
                </div>
                <div class="form-group">
                    <label for="end_time">เวลาออก:</label>
                    <input type="datetime-local" id="end_time" name="end_time" required>
                </div>
                <button type="submit" id="search-button">ค้นหา</button>
            </div>
        </form>
    </div>
</body>
</html>