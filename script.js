// script.js
document.addEventListener('DOMContentLoaded', function() {

    const parkingGrid = document.querySelector('.parking-grid');
    const bookButton = document.getElementById('book-button');
    const selectedSpotInput = document.getElementById('selected-spot-input');

    let currentSelectedSpot = null; // ตัวแปรสำหรับเก็บช่องที่ถูกเลือกอยู่

    // ตรวจจับการคลิกที่ช่องจอด
    parkingGrid.addEventListener('click', function(event) {
        
        const clickedSpot = event.target;

        // เช็คว่าที่คลิกคือช่องจอดที่ "ว่าง" หรือไม่
        if (clickedSpot.classList.contains('spot') && clickedSpot.classList.contains('available')) {
            
            // ถ้ามีช่องที่ถูกเลือกอยู่แล้ว (สีเขียว) ให้เอาสีเขียวออกก่อน
            if (currentSelectedSpot) {
                currentSelectedSpot.classList.remove('selected');
            }

            // เพิ่มสีเขียวให้กับช่องที่เพิ่งคลิก
            clickedSpot.classList.add('selected');
            currentSelectedSpot = clickedSpot;

            // แสดงปุ่มจอง
            bookButton.style.display = 'block';

            // นำรหัสของช่องจอดไปใส่ใน input ที่ซ่อนไว้
            const spotId = clickedSpot.dataset.spotId;
            selectedSpotInput.value = spotId;

        } else if (clickedSpot.classList.contains('occupied')) {
            // ถ้าคลิกช่องที่ไม่ว่าง (สีแดง)
            alert('ที่จอดนี้ไม่ว่าง กรุณาเลือกช่องอื่น');
        }
    });

});