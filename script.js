// script.js

// รอให้หน้าเว็บโหลดเสร็จก่อนเริ่มทำงาน
document.addEventListener('DOMContentLoaded', function() {

    // ดึงองค์ประกอบที่ต้องใช้จากหน้า HTML มาเก็บในตัวแปร
    const parkingGrid = document.querySelector('.parking-grid');
    const bookButton = document.getElementById('book-button');
    const selectedSpotInput = document.getElementById('selected-spot-input');

    // ตัวแปรสำหรับเก็บข้อมูลช่องจอดที่ถูกเลือกอยู่ ณ ปัจจุบัน
    let currentSelectedSpot = null;

    // เพิ่ม Event Listener เพื่อดักจับการคลิกภายในพื้นที่จอดรถทั้งหมด
    parkingGrid.addEventListener('click', function(event) {
        
        // event.target คือสิ่งที่เราคลิกโดน
        const clickedSpot = event.target;

        // --- เริ่มเงื่อนไขการตรวจสอบ ---

        // 1. ตรวจสอบว่าสิ่งที่คลิกคือ "ช่องจอด" และ "ว่าง" หรือไม่?
        if (clickedSpot.classList.contains('spot') && clickedSpot.classList.contains('available')) {
            
            // 2. ถ้ามีช่องอื่นที่ถูกเลือกไว้ก่อนหน้า (เป็นสีเขียวอยู่) ให้เอาสีเขียวออก
            if (currentSelectedSpot) {
                currentSelectedSpot.classList.remove('selected');
            }

            // 3. เพิ่ม class 'selected' (สีเขียว) ให้กับช่องที่เพิ่งคลิก
            clickedSpot.classList.add('selected');
            currentSelectedSpot = clickedSpot; // อัปเดตช่องที่เลือกอยู่ปัจจุบัน

            // 4. แสดงปุ่ม "จองที่จอด"
            bookButton.style.display = 'block';

            // 5. นำรหัสของช่องจอด (จาก data-spot-id) ไปใส่ใน input ที่ซ่อนไว้เพื่อเตรียมส่งข้อมูล
            const spotId = clickedSpot.dataset.spotId;
            selectedSpotInput.value = spotId;

        } else if (clickedSpot.classList.contains('occupied')) {
            // กรณีคลิกช่องที่ไม่ว่าง (สีแดง)
            alert('ที่จอดนี้มีผู้จองแล้ว กรุณาเลือกช่องอื่น');
        }
    });
});