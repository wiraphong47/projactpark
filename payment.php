<?php
session_start();
require_once 'config.php';

// ฟังก์ชันสำหรับคำนวณค่าบริการ
function calculate_cost($start, $end) {
    $start_datetime = new DateTime($start);
    $end_datetime = new DateTime($end);
    $interval = $start_datetime->diff($end_datetime);
    $hours = $interval->h;
    $days = $interval->d;
    $minutes = $interval->i;
    if ($minutes > 0) {
        $hours += 1;
    }
    $total_hours = ($days * 24) + $hours;
    return $total_hours * 50; 
}

// --- ส่วนที่ 1: จัดการการอัปโหลดสลิปและยืนยันการจอง ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    
    $booked_spot = $_POST['booked_spot'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $total_cost = calculate_cost($start_time, $end_time);
    $username = $_SESSION['username'] ?? 'guest';
    $payment_method = $_POST['payment_method'];
    $is_booking_success = false;

    // --- ตรวจสอบวิธีการชำระเงิน ---
    if ($payment_method === 'qr_code') {
        // โค้ดเดิมสำหรับ QR Code (อัปโหลดสลิป)
        $is_upload_success = false;
        if (isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES['payment_slip']['tmp_name'];
            $file_name = $_FILES['payment_slip']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_file_name = uniqid('slip_', true) . '.' . $file_extension;
            $upload_dir = 'uploads/';
            $dest_path = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                $is_upload_success = true;
            }
        }
        if ($is_upload_success) {
            $stmt = $conn->prepare("UPDATE parking_spots SET status = 'occupied', booked_by_user = ? WHERE spot_name = ? AND status = 'available'");
            $stmt->bind_param("ss", $username, $booked_spot);
            $stmt->execute();
            $is_booking_success = $stmt->affected_rows > 0;
            $stmt->close();
        }

    } elseif ($payment_method === 'credit_card') {
        // โค้ดจำลองสำหรับการประมวลผลบัตรเครดิต
        // ในระบบจริงจะมีการเชื่อมต่อกับ Payment Gateway API
        $is_payment_successful = true; // สมมติว่าสำเร็จ
        if ($is_payment_successful) {
            $stmt = $conn->prepare("UPDATE parking_spots SET status = 'occupied', booked_by_user = ? WHERE spot_name = ? AND status = 'available'");
            $stmt->bind_param("ss", $username, $booked_spot);
            $stmt->execute();
            $is_booking_success = $stmt->affected_rows > 0;
            $stmt->close();
        }
    }
    
    $conn->close();

    // หากการจองสำเร็จ ให้เก็บข้อมูลลง session และเปลี่ยนเส้นทางไปหน้าใบเสร็จ
    if ($is_booking_success) {
        $_SESSION['receipt_data'] = [
            'spot' => $booked_spot,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'cost' => $total_cost,
            'username' => $username,
            'transaction_date' => date('Y-m-d H:i:s'),
            'payment_method' => $payment_method
        ];
        header('Location: receipt.php');
        exit();
    }
    // หากไม่สำเร็จ ให้แสดงหน้าผิดพลาด
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
        <div class="container">
            <h1 style="color: #dc3545;">เกิดข้อผิดพลาด!</h1>
            <p>ขออภัย ที่จอด <strong>' . htmlspecialchars($booked_spot) . '</strong> อาจถูกจองไปแล้ว หรือเกิดปัญหาในการอัปโหลดสลิป</p>
            <a href="index.php" class="back-button">กลับสู่หน้าหลัก</a>
        </div>
    </body>
    </html>';

    exit(); 
}

// --- ส่วนที่ 2: แสดงหน้ายืนยันการจอง (เมื่อมาจากหน้า index หรือ park) ---
if (isset($_POST['selected_spot']) && !empty($_POST['selected_spot'])) {
    $selected_spot = htmlspecialchars($_POST['selected_spot']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $total_cost = calculate_cost($start_time, $end_time);
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
            max-width: 200px;
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
        .payment-method-selector {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .payment-method-selector button {
            padding: 12px 25px;
            border-radius: 25px;
            border: 1px solid #007bff;
            background-color: white;
            color: #007bff;
            font-family: 'Kanit', sans-serif;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method-selector button.active,
        .payment-method-selector button:hover {
            background-color: #007bff;
            color: white;
        }
        .payment-form-section {
            display: none;
        }
        .payment-form-section.active {
            display: block;
        }
        .credit-card-form .form-group {
            text-align: left;
            margin-bottom: 15px;
        }
        .credit-card-form .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .credit-card-form .form-group input {
            width: 95%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .credit-card-form .form-row {
            display: flex;
            gap: 15px;
        }
        .credit-card-form .form-row .form-group {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ยืนยันและชำระเงิน</h1>
        <div class="info">
            คุณกำลังจะจองที่จอด:<br>
            <strong><?php echo $selected_spot; ?></strong>
            <p style="font-size: 18px; margin-top: 20px;">
                วันที่เข้า: <?php echo date('d/m/Y', strtotime($start_time)); ?>
                <br>
                เวลาเข้า: <?php echo date('H:i', strtotime($start_time)); ?> น.
                <br>
                วันที่ออก: <?php echo date('d/m/Y', strtotime($end_time)); ?>
                <br>
                เวลาออก: <?php echo date('H:i', strtotime($end_time)); ?> น.
            </p>
            <p style="font-size: 24px;">ค่าบริการ: <strong><?php echo number_format($total_cost, 2); ?> บาท</strong></p>
        </div>

        <div class="payment-method-selector">
            <button type="button" class="method-btn active" data-method="qr_code">QR Code</button>
            <button type="button" class="method-btn" data-method="credit_card">บัตรเครดิต</button>
        </div>

        <div class="payment-section">
            
            <form action="payment.php" method="POST" enctype="multipart/form-data" id="qr_code-form" class="payment-form-section active">
                <h2>ชำระเงินผ่าน QR Code</h2>
                <img src="qr_code.png" alt="QR Code สำหรับชำระเงิน" class="qr-code">
                <input type="hidden" name="payment_method" value="qr_code">
                <input type="hidden" name="booked_spot" value="<?php echo $selected_spot; ?>">
                <input type="hidden" name="start_time" value="<?php echo htmlspecialchars($start_time); ?>">
                <input type="hidden" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>">
                
                <label for="payment_slip" class="slip-upload-label">แนบสลิปการโอนเงิน</label>
                <input type="file" id="payment_slip" name="payment_slip" accept="image/*" required>
                
                <button type="submit" name="confirm_booking" id="confirm-button">ยืนยันการจองและแจ้งชำระเงิน</button>
            </form>
            
            <form action="payment.php" method="POST" id="credit_card-form" class="payment-form-section">
                <h2>ชำระเงินผ่านบัตรเครดิต</h2>
                <input type="hidden" name="payment_method" value="credit_card">
                <input type="hidden" name="booked_spot" value="<?php echo $selected_spot; ?>">
                <input type="hidden" name="start_time" value="<?php echo htmlspecialchars($start_time); ?>">
                <input type="hidden" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>">

                <div class="credit-card-form">
                    <div class="form-group">
                        <label for="card_number">หมายเลขบัตรเครดิต</label>
                        <input type="text" id="card_number" name="card_number" pattern="[0-9]{16}" placeholder="xxxxxxxxxxxxxxxx" required>
                    </div>
                    <div class="form-group">
                        <label for="card_name">ชื่อบนบัตร</label>
                        <input type="text" id="card_name" name="card_name" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiry">วันหมดอายุ (MM/YY)</label>
                            <input type="text" id="expiry" name="expiry" pattern="(0[1-9]|1[0-2])\/[0-9]{2}" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">รหัส CVV</label>
                            <input type="text" id="cvv" name="cvv" pattern="[0-9]{3,4}" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="confirm_booking" id="confirm-button">ยืนยันการชำระเงิน</button>
            </form>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const methodButtons = document.querySelectorAll('.method-btn');
            const forms = document.querySelectorAll('.payment-form-section');

            methodButtons.forEach(button => {
                button.addEventListener('click', function() {
                    methodButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const selectedMethod = this.dataset.method;
                    forms.forEach(form => {
                        form.classList.remove('active');
                        if (form.id === `${selectedMethod}-form`) {
                            form.classList.add('active');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>