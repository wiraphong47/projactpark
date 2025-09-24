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

// --- กำหนดโซนที่ถูกเลือก ---
$selected_zone_id = $_GET['zone'] ?? ($zones[0]['id'] ?? 0);
$selected_zone_name = '';
foreach ($zones as $zone) {
    if ($zone['id'] == $selected_zone_id) {
        $selected_zone_name = $zone['name'];
        break;
    }
}


// --- ส่วนจำลองการสุ่มสถานะ (สำหรับทดสอบ) เพื่ออัปเดตทุกครั้งที่รีเฟรช ---
// $conn->query("UPDATE parking_spots SET status = IF(RAND() > 0.5, 'occupied', 'available') WHERE zone_id = '$selected_zone_id'");

// --- รับค่าจากการค้นหา ---
$spot_type = $_GET['spot_type'] ?? 'all';
$start_time = $_GET['start_time'] ?? null;
$end_time = $_GET['end_time'] ?? null;

// --- ดึงข้อมูลที่จอดรถตามเกณฑ์การค้นหา ---
$parking_statuses = [];
$available_count = 0;
$occupied_count = 0;

if ($selected_zone_id > 0) {
    $sql = "SELECT spot_name, status FROM parking_spots WHERE zone_id = ?";
    if ($spot_type !== 'all') {
        $sql .= " AND spot_type = ?";
    }
    $sql .= " ORDER BY id";
    
    $stmt = $conn->prepare($sql);
    if ($spot_type !== 'all') {
        $stmt->bind_param("is", $selected_zone_id, $spot_type);
    } else {
        $stmt->bind_param("i", $selected_zone_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $parking_statuses[$row['spot_name']] = $row['status'];
            if ($row['status'] == 'available') {
                $available_count++;
            } else {
                $occupied_count++;
            }
        }
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลการค้นหา - ระบบจองที่จอดรถออนไลน์</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #66f3d5ff;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding-top: 50px;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            width: 90%;
            max-width: 800px;
            text-align: center;
        }
        h1, h2 {
            color: #333;
            margin-top: 0;
        }
        .summary {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 25px;
        }
        .summary-item {
            display: flex;
            align-items: center;
            font-size: 16px;
        }
        .color-box {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            border-radius: 4px;
        }
        .available-box { background-color: #28a745; }
        .occupied-box { background-color: #dc3545; }
        .parking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 15px;
            padding: 20px 0;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .spot {
            background-color: #f8f9fa;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px 5px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .spot.available {
            background-color: #e6f5e6;
            border-color: #28a745;
            color: #28a745;
        }
        .spot.occupied {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #dc3545;
            cursor: not-allowed;
        }
        .spot.selected {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.7);
            transform: scale(1.05);
        }
        #book-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background-color: #007bff;
            color: white;
            font-family: 'Kanit', sans-serif;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        #book-button:hover {
            background-color: #0056b3;
        }
        #map-container {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            width: auto;
            height: auto;
            background-color: #eee;
            margin-top: 20px;
            border-radius: 10px;
        }
        #map-container.fade-in {
            opacity: 1;
        }
        #map-placeholder {
            padding: 20px;
            color: #6c757d;
        }
        #map-container img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border: 2px solid black;
        }
        
        /* CSS สำหรับส่วน User Info และปุ่มกลับหน้าหลัก */
        .header-controls {
            position: fixed;
            top: 15px;
            right: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Kanit', sans-serif;
        }
        .user-greeting {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .logout-button {
            padding: 8px 16px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }
        .logout-button:active {
            background-color: #bd2130;
            transform: scale(0.98);
        }
        
        /* CSS สำหรับปุ่ม "กลับหน้าหลัก" */
        .home-button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-family: 'Kanit', sans-serif;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .home-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .home-button:active {
            background-color: #003d80;
            transform: scale(0.98);
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="header-controls">
        <?php if (isset($_SESSION['username'])): ?>
        <div class="user-info">
            <span class="user-greeting">สวัสดี, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="logout-button">ออกจากระบบ</a>
        </div>
        <?php endif; ?>
        <a href="index.php" class="home-button">กลับหน้าหลัก</a>
    </div>

    <div class="container">
        <h1>เลือกที่จอดรถ</h1>
        <h2>โซน: <?php echo htmlspecialchars($selected_zone_name); ?></h2>

        <div class="summary">
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
                <?php if (!empty($parking_statuses)): ?>
                    <?php foreach ($parking_statuses as $spot_id => $status): ?>
                        <div class="spot <?php echo $status; ?>" data-spot-id="<?php echo $spot_id; ?>">
                            <?php echo $spot_id; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>ไม่พบที่จอดที่ว่างตามเงื่อนไขที่ระบุ</p>
                <?php endif; ?>
            </div>
            <input type="hidden" name="selected_spot" id="selected-spot-input">
            <input type="hidden" name="start_time" value="<?php echo htmlspecialchars($start_time); ?>">
            <input type="hidden" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>">
            <button type="submit" id="book-button" disabled>ยืนยันการเลือก</button>
        </form>

        <div id="map-container">
            <div id="map-placeholder">เลือกที่จอดเพื่อดูแผนที่...</div>
        </div>
    </div>
    
   <script>
    document.addEventListener('DOMContentLoaded', function() {
        const spots = document.querySelectorAll('.spot');
        const selectedSpotInput = document.getElementById('selected-spot-input');
        const bookButton = document.getElementById('book-button');
        const mapContainer = document.getElementById('map-container');
        
        const selectedZoneName = "<?php echo htmlspecialchars($selected_zone_name); ?>";
        
        bookButton.disabled = true;
        bookButton.style.backgroundColor = '';
        bookButton.innerText = 'ยืนยันการเลือก';
        
        function showMap(spotId) {
            mapContainer.innerHTML = `
                <h2>แผนที่แสดงตำแหน่งที่จอด</h2>
                <p><strong>ตำแหน่งที่เลือก: ${spotId}</strong></p>
                <p><strong>สถานที่: ${selectedZoneName}</strong></p>
                <img src="map_bigc_udon.png" alt="Map of Big C Udon Parking">
            `;
            mapContainer.classList.add('fade-in');
        }

        function hideMap() {
            mapContainer.classList.remove('fade-in');
            setTimeout(() => {
                mapContainer.innerHTML = `<div id="map-placeholder">เลือกที่จอดเพื่อดูแผนที่...</div>`;
            }, 500);
        }
        
        spots.forEach(spot => {
            spot.addEventListener('click', function() {
                if (this.classList.contains('available')) {
                    const isAlreadySelected = this.classList.contains('selected');
                    
                    spots.forEach(s => s.classList.remove('selected'));
                    
                    if (isAlreadySelected) {
                        selectedSpotInput.value = '';
                        bookButton.disabled = true;
                        bookButton.style.backgroundColor = '';
                        bookButton.innerText = 'ยืนยันการเลือก';
                        hideMap();
                    } else {
                        this.classList.add('selected');
                        const selectedSpotId = this.dataset.spotId;
                        selectedSpotInput.value = selectedSpotId;

                        bookButton.disabled = false;
                        bookButton.style.backgroundColor = '#1e7e34';
                        bookButton.innerText = `จองที่จอด ${selectedSpotId}`;
                        
                        showMap(selectedSpotId);
                    }
                }
            });
        });

        bookButton.addEventListener('click', function(event) {
            if (!selectedSpotInput.value) {
                alert('กรุณาเลือกช่องจอดที่ว่างก่อน');
                event.preventDefault();
            } else {
                const confirmation = confirm(`ยืนยันการจองที่จอด ${selectedSpotInput.value} ?`);
                if (!confirmation) {
                    event.preventDefault();
                }
            }
        });
    });
</script>
</body>
</html>
