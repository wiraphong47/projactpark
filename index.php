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
        :root {
            --primary-color: #007bff;
            --primary-dark: #0056b3;
            --secondary-color: #6c757d;
            --background-light: #f8f9fa;
            --background-dark: #e9ecef;
            --text-color: #343a40;
            --text-secondary: #495057;
            --card-background: #ffffff;
            --border-color: #dee2e6;
            --shadow-color: rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--background-light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            color: var(--text-color);
        }

        .container {
            background-color: var(--card-background);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 30px var(--shadow-color);
            width: 100%;
            max-width: 800px;
        }
        
        h1 {
            color: var(--primary-dark);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }

        /* === สไตล์สำหรับส่วน Header (ชื่อผู้ใช้และปุ่มออกจากระบบ) === */
        .header-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
        }

        .header-controls .username {
            font-weight: 700;
            color: var(--text-secondary);
        }

        .logout-button {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #c82333;
        }
        
        /* === สไตล์สำหรับปุ่มเลือกโซน === */
        .zone-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            justify-content: center;
        }
        
        .zone-button {
            padding: 12px 25px;
            font-family: 'Kanit', sans-serif;
            font-size: 16px;
            border: 1px solid var(--border-color);
            border-radius: 25px;
            background-color: var(--background-light);
            color: var(--text-secondary);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .zone-button:hover {
            background-color: var(--background-dark);
            border-color: #ccc;
        }
        
        .zone-button.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.25);
        }
        
        /* === สไตล์สำหรับฟอร์มค้นหา === */
        .search-criteria {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .search-criteria .form-group {
            display: flex;
            flex-direction: column;
        }

        .search-criteria .form-group label {
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--text-color);
        }
        
        .search-criteria .form-group input,
        .search-criteria .form-group select {
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .search-criteria .form-group input:focus,
        .search-criteria .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }
        
        #search-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 30px;
            background-color: var(--primary-color);
            color: white;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        
        #search-button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* สำหรับหน้าจอขนาดเล็ก */
        @media (min-width: 600px) {
            .search-criteria .form-group {
                flex-direction: row;
                align-items: center;
                gap: 15px;
            }
            .search-criteria .form-group label {
                flex-basis: 150px;
                margin-bottom: 0;
            }
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
        
        <form action="park2.php" method="get" id="search-form">
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
