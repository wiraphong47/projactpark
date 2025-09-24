<?php
// --- ส่วนจำลองข้อมูลจากฐานข้อมูล ---
// ในการใช้งานจริง ข้อมูลนี้ควรจะดึงมาจาก Database เช่น MySQL
// key คือรหัสที่จอด, value คือสถานะ ('available' = ว่าง, 'occupied' = ไม่ว่าง)
$parking_statuses = [
    'A01' => 'occupied',
    'A02' => 'available',
    'A03' => 'available',
    'A04' => 'occupied',
    'A05' => 'available',
    'A06' => 'available',
    'A07' => 'available',
    'A08' => 'available',
    'B01' => 'occupied',
    'B02' => 'occupied',
    'B03' => 'available',
    'B04' => 'available',
];
// --- จบส่วนจำลองข้อมูล ---
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองที่จอดรถออนไลน์</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>เลือกโซนที่จอดรถ</h1>

        <div class="location-selector">
            <button class="pin" data-location="lot-a">โซน A</button>
            <button class="pin" data-location="lot-b">โซน B (ยังไม่เปิด)</button>
        </div>

        <hr>

        <div id="parking-lot-a" class="parking-lot-container">
            <h2>ผังที่จอดรถ โซน A</h2>
            
            <form action="payment.php" method="post" id="booking-form">
                
                <div class="parking-grid">
                    <?php foreach ($parking_statuses as $spot_id => $status): ?>
                        <div 
                            class="spot <?php echo $status; // 'available' หรือ 'occupied' ?>" 
                            data-spot-id="<?php echo $spot_id; ?>">
                            <?php echo $spot_id; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="selected_spot" id="selected-spot-input">
                
                <button type="submit" id="book-button" style="display: none;">จองที่จอด</button>

            </form>
            
            <div class="legend">
                <div class="legend-item"><span class="spot occupied"></span> ไม่ว่าง</div>
                <div class="legend-item"><span class="spot available"></span> ว่าง</div>
                <div class="legend-item"><span class="spot selected"></span> ที่คุณเลือก</div>
            </div>
        </div>

    </div>

    <script src="script.js"></script>
</body>
</html>