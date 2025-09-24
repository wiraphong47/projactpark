document.addEventListener('DOMContentLoaded', function() {

    const parkingGrid = document.querySelector('.parking-grid');
    const bookButton = document.getElementById('book-button');
    const selectedSpotInput = document.getElementById('selected-spot-input');

    let currentSelectedSpot = null; // ตัวแปรสำหรับเก็บช่องที่ถูกเลือกอยู่

    // ตรวจจับการคลิกที่ช่องจอด
    parkingGrid.addEventListener('click', function(event) {
        
        // --- ส่วนที่ปรับปรุง ---
        // ใช้ .closest('.spot') เพื่อให้แน่ใจว่าเราได้ Element ของช่องจอดจริงๆ
        const clickedSpot = event.target.closest('.spot');

        // ถ้าไม่ได้คลิกที่ช่องจอด ให้ออกจากฟังก์ชันไปเลย
        if (!clickedSpot) {
            return; 
        }

        // เช็คว่าช่องที่คลิก "ว่าง" หรือไม่
        if (clickedSpot.classList.contains('available')) {
            
            // ถ้ามีช่องที่ถูกเลือกอยู่แล้ว ให้เอา class 'selected' ออกก่อน
            if (currentSelectedSpot) {
                currentSelectedSpot.classList.remove('selected');
            }

            // เพิ่ม class 'selected' ให้กับช่องที่เพิ่งคลิก
            clickedSpot.classList.add('selected');
            currentSelectedSpot = clickedSpot;

            // แสดงปุ่มจอง
            bookButton.style.display = 'block';

            // นำรหัสของช่องจอดไปใส่ใน input ที่ซ่อนไว้
            selectedSpotInput.value = clickedSpot.dataset.spotId;

        } else if (clickedSpot.classList.contains('occupied')) {
            // ถ้าคลิกช่องที่ไม่ว่าง (สีแดง)
            alert('ที่จอดนี้ไม่ว่าง กรุณาเลือกช่องอื่น');
        }
    });

});