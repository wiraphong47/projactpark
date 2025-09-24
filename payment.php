<?php
session_start();
require_once 'config.php';

// --- ส่วนที่ 1: จัดการการอัปโหลดสลิปและยืนยันการจอง ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    
    $booked_spot = $_POST['booked_spot'];
    $username = $_SESSION['username'] ?? 'guest';
    $is_upload_success = false;

    // --- ตรวจสอบและจัดการไฟล์ที่อัปโหลด ---
    if (isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
        
        $file_tmp_path = $_FILES['payment_slip']['tmp_name'];
        $file_name = $_FILES['payment_slip']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // --- สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน ---
        $new_file_name = uniqid('slip_', true) . '.' . $file_extension;
        $upload_dir = 'uploads/';
        $dest_path = $upload_dir . $new_file_name;

        // --- ย้ายไฟล์ไปยังโฟลเดอร์ uploads ---
        if (move_uploaded_file($file_tmp_path, $dest_path)) {
            $is_upload_success = true;
        }
    }

    // --- อัปเดตฐานข้อมูลก็ต่อเมื่ออัปโหลดสลิปสำเร็จ ---
    if ($is_upload_success) {
        $stmt = $conn->prepare("UPDATE parking_spots SET status = 'occupied', booked_by_user = ? WHERE spot_name = ? AND status = 'available'");
        $stmt->bind_param("ss", $username, $booked_spot);
        $stmt->execute();
        $is_booking_success = $stmt->affected_rows > 0;
        $stmt->close();
    } else {
        $is_booking_success = false;
    }
    
    $conn->close();

    // --- ส่วนแสดงผลหลังจากการจอง ---
    echo '
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <title>สถานะการจอง</title>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
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
    
    if ($is_booking_success) {
        echo '<h1 style="color: #28a745;">จองสำเร็จ!</h1>';
        echo "<p>ระบบได้บันทึกการจองที่จอด <strong>$booked_spot</strong> ของคุณแล้ว</p>";
        echo "<p style='font-size:14px; color:#6c757d;'>ขอบคุณสำหรับสลิปการโอนเงิน</p>";
    } else {
        echo '<h1 style="color: #dc3545;">เกิดข้อผิดพลาด!</h1>';
        echo "<p>ขออภัย ที่จอด <strong>$booked_spot</strong> อาจถูกจองไปแล้ว หรือเกิดปัญหาในการอัปโหลดสลิป</p>";
    }

    echo '<a href="index.php" class="back-button">กลับสู่หน้าหลัก</a>';
    echo '
        </div>
    </body>
    </html>';

    exit(); 
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
    <title>ยืนยันและชำระเงิน</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
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
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .info strong {
            color: #007bff;
            font-size: 22px;
        }
        .payment-section {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 30px;
        }
        .payment-section h2 {
            font-size: 22px;
            margin-bottom: 20px;
        }
        .qr-code {
            max-width: 200px; /* ปรับขนาด QR Code ที่นี่ */
            margin-bottom: 20px;
        }
        .slip-upload-label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        #payment_slip {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        #confirm-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background-color: #28a745;
            color: white;
            font-family: 'Kanit', sans-serif;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #confirm-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ยืนยันการจอง</h1>
        <div class="info">
            คุณกำลังจะจองที่จอด:<br>
            <strong><?php echo $selected_spot; ?> (โซน A)</strong>
            <p style="font-size: 18px; margin-top: 20px;">ค่าบริการ: 50 บาท</p>
        </div>

        <div class="payment-section">
            <h2>ชำระเงินผ่าน QR Code</h2>
            <img src="qr_code.png" alt="QR Code สำหรับชำระเงิน" class="qr-code">
            
            <form action="payment.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="booked_spot" value="<?php echo $selected_spot; ?>">
                
                <label for="payment_slip" class="slip-upload-label">แนบสลิปการโอนเงิน</label>
                <input type="file" id="payment_slip" name="payment_slip" accept="image/*" required>
                
                <button type="submit" name="confirm_booking" id="confirm-button">ยืนยันการจองและแจ้งชำระเงิน</button>
            </form>
        </div>
    </div>
</body>
</html>