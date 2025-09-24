<?php
session_start();
require_once 'config.php';

// --- ส่วนที่ 1: จัดการการอัปเดตฐานข้อมูล (เมื่อฟอร์มถูกส่งกลับมาที่หน้านี้) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    $booked_spot = $_POST['booked_spot'];
    $username = $_SESSION['username'] ?? 'guest'; // เก็บชื่อผู้ใช้ (ถ้ามี)

    // อัปเดตสถานะในฐานข้อมูลเป็น 'occupied'
    $stmt = $conn->prepare("UPDATE parking_spots SET status = 'occupied' WHERE spot_name = ? AND status = 'available'");
    $stmt->bind_param("s", $booked_spot);
    $stmt->execute();

    $is_success = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();

    // --- ส่วนแสดงผลหลังจากการจอง ---
    echo '
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <title>สถานะการจอง</title>
        <style>
            body { font-family: "Kanit", sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
            .container { background-color: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }
            h1 { font-size: 28px; margin-bottom: 15px; }
            p { font-size: 18px; color: #555; }
            .back-button { display: inline-block; margin-top: 25px; padding: 12px 25px; background-color: #007bff; color: white; text-decoration: none; border-radius: 25px; font-size: 16px; transition: background-color 0.3s; }
            .back-button:hover { background-color: #0056b3; }
        </style>
    </head>
    <body>
        <div class="container">';
    
    if ($is_success) {
        echo '<h1 style="color: #28a745;">จองสำเร็จ!</h1>';
        echo "<p>ที่จอด <strong>$booked_spot</strong> เป็นของคุณแล้ว</p>";
    } else {
        echo '<h1 style="color: #dc3545;">เกิดข้อผิดพลาด!</h1>';
        echo "<p>ขออภัย ที่จอด <strong>$booked_spot</strong> อาจถูกจองไปแล้ว</p>";
    }

    echo '<a href="index.php" class="back-button">กลับสู่หน้าหลัก</a>';
    echo '
        </div>
    </body>
    </html>';

    exit(); // หยุดการทำงานส่วนที่เหลือ
}


// --- ส่วนที่ 2: แสดงหน้ายืนยันการจอง (เมื่อมาจากหน้า index) ---
if (isset($_POST['selected_spot']) && !empty($_POST['selected_spot'])) {
    $selected_spot = htmlspecialchars($_POST['selected_spot']);
} else {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ยืนยันการจองและชำระเงิน</title>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 500px;
            text-align: center;
        }
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 25px;
        }
        .info {
            font-size: 20px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .info strong {
            color: #007bff;
            font-size: 22px;
        }
        #confirm-button {
            width: 100%;
            max-width: 300px;
            height: 70px;
            border: none;
            border-radius: 15px;
            background-color: #81c784; /* สีเขียว */
            color: white;
            font-family: 'Kanit', sans-serif;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #confirm-button:hover {
            background-color: #66bb6a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ยืนยันและชำระเงิน</h1>
        <div class="info">
            คุณกำลังจะจองที่จอด:<br>
            <strong><?php echo $selected_spot; ?> (โซน A)</strong>
            <p style="font-size: 18px; margin-top: 20px;">ค่าบริการ: 50 บาท</p>
        </div>
        
        <form action="payment.php" method="POST">
            <input type="hidden" name="booked_spot" value="<?php echo $selected_spot; ?>">
            <button type="submit" name="confirm_booking" id="confirm-button">ยืนยันการจอง</button>
        </form>
    </div>
</body>
</html>