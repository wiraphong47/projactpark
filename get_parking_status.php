<?php
require_once 'config.php';

// --- ส่วนจำลองการสุ่มสถานะ (สำหรับทดสอบ) ---
// หมายเหตุ: ในระบบจริง คุณควรลบบรรทัดนี้ออก เพราะสถานะจะเปลี่ยนจากการจองจริง
$conn->query("UPDATE parking_spots SET status = IF(RAND() > 0.5, 'occupied', 'available') WHERE spot_name LIKE 'A%'");


// --- ดึงข้อมูลสถานะล่าสุดจากฐานข้อมูล ---
$parking_statuses = [];
$available_count = 0;
$occupied_count = 0;

$result = $conn->query("SELECT spot_name, status FROM parking_spots WHERE spot_name LIKE 'A%' ORDER BY id");

if ($result) {
    while($row = $result->fetch_assoc()) {
        $parking_statuses[$row['spot_name']] = $row['status'];
        if ($row['status'] == 'available') {
            $available_count++;
        } else {
            $occupied_count++;
        }
    }
}

$conn->close();

// --- เตรียมข้อมูลเพื่อส่งกลับไปเป็นรูปแบบ JSON ---
$response_data = [
    'statuses' => $parking_statuses,
    'available' => $available_count,
    'occupied' => $occupied_count
];

// --- ส่งข้อมูลกลับในรูปแบบ JSON ---
header('Content-Type: application/json');
echo json_encode($response_data);
?>