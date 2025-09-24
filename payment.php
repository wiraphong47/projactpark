<?php
// เริ่มต้น session เพื่อเข้าถึงข้อมูล session ของผู้ใช้
session_start();

// --- 1. ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือยัง ---
// ถ้าไม่มี session 'username' แสดงว่ายังไม่ได้ล็อกอิน ให้ส่งกลับไปหน้า login
if (!isset($_SESSION['username'])) {
    // ส่งข้อความ error กลับไปด้วย
    header('Location: login.php?error=กรุณาเข้าสู่ระบบก่อนทำการจอง');
    exit(); // จบการทำงานทันที
}

// --- 2. ตรวจสอบว่ามีการส่งข้อมูลที่จอดรถมาหรือไม่ ---
// เช็คว่ามีข้อมูล 'selected_spot' ส่งมาด้วยวิธี POST หรือไม่
if (isset($_POST['selected_spot']) && !empty($_POST['selected_spot'])) {
    
    // ดึงข้อมูลที่จำเป็นมาเก็บในตัวแปร
    $username = $_SESSION['username'];
    $booked_spot = htmlspecialchars($_POST['selected_spot']); // ป้องกัน XSS

} else {
    // ถ้าไม่มีข้อมูลส่งมา ให้ส่งกลับไปหน้าแรก
    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการจองและชำระเงิน</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* เพิ่มสไตล์เฉพาะสำหรับหน้านี้ */
        .payment-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .payment-container h1 {
            color: #28a745; /* สีเขียว */
        }
        .info {
            font-size: 1.2rem;
            margin: 20px 0;
            text-align: left;
            line-height: 1.8;
        }
        .info strong {
            display: inline-block;
            width: 150px;
        }
        /* ปุ่มยืนยัน */
        .confirm-button {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 20px;
            font-family: 'Kanit', sans-serif;
        }
        .confirm-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>✔️ ยืนยันการจอง</h1>
        <div class="info">
            <p><strong>ผู้ใช้งาน:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>ที่จอดที่เลือก:</strong> <?php echo $booked_spot; ?></p>
            <p><strong>ค่าบริการ:</strong> 50 บาท</p>
        </div>
        
        <hr>

        <p>ส่วนนี้คือตัวอย่างการเชื่อมต่อระบบชำระเงิน</p>
                
        <form action="booking_success.php" method="POST">
             <input type="hidden" name="booked_spot" value="<?php echo $booked_spot; ?>">
             <button type="submit" class="confirm-button">ยืนยันการชำระเงิน</button>
        </form>

    </div>
</body>
</html>