/**
 * Script trang cá nhân - Phạm Hồng Phong
 */

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. ĐỔI GIAO DIỆN SÁNG / TỐI (DARK MODE)
    // ==========================================
    const nutDoiGiaoDien = document.getElementById('btn-doi-giao-dien') || document.getElementById('btn-dark-mode');

    if (nutDoiGiaoDien) {
        try {
            const giaoDienDaLuu = localStorage.getItem('giao-dien-phong');
            if (giaoDienDaLuu === 'dark') {
                document.body.classList.add('dark-mode');
                nutDoiGiaoDien.textContent = '☀️ Chế độ Sáng';
            }
        } catch (err) {
            console.warn('Khởi tạo giao diện: - canhan.js:20', err);
        }

        nutDoiGiaoDien.addEventListener('click', () => {
            const laGiaoDienToi = document.body.classList.toggle('dark-mode');
            nutDoiGiaoDien.textContent = laGiaoDienToi ? '☀️ Chế độ Sáng' : '🌙 Chế độ Tối';
            try {
                localStorage.setItem('giao-dien-phong', laGiaoDienToi ? 'dark' : 'light');
            } catch (err) {
                console.warn('Lưu giao diện thất bại: - canhan.js:29', err);
            }
        });
    }

    // ==========================================
    // 2. TÍNH NĂNG KÍNH LÚP SOI CHỮ
    // ==========================================
    const btnKinhLup = document.getElementById('btn-kinh-lup');
    let kinhLup = document.getElementById('kinh-lup-soi');
    
    if (!kinhLup) {
        kinhLup = document.createElement('div');
        kinhLup.id = 'kinh-lup-soi';
        document.body.appendChild(kinhLup);
    }

    let batKinhLup = false;

    if (btnKinhLup) {
        btnKinhLup.addEventListener('click', (e) => {
            e.preventDefault();
            batKinhLup = !batKinhLup;
            kinhLup.style.display = batKinhLup ? 'block' : 'none';
            btnKinhLup.classList.toggle('active', batKinhLup);
            btnKinhLup.textContent = batKinhLup ? '🔍 Tắt Kính Lúp' : '🔍 Bật Kính Lúp Soi Chữ';
        });

        document.addEventListener('pointermove', (e) => {
            if (!batKinhLup) return;

            const x = e.clientX;
            const y = e.clientY;

            kinhLup.style.left = `${x - 80}px`;
            kinhLup.style.top = `${y - 80}px`;

            // Lấy phần tử chữ bên dưới con trỏ
            kinhLup.style.pointerEvents = 'none';
            const elem = document.elementFromPoint(x, y);

            if (elem && elem.textContent.trim() !== '') {
                kinhLup.textContent = elem.textContent;
                kinhLup.style.fontSize = '22px';
                kinhLup.style.fontWeight = 'bold';
                kinhLup.style.display = 'flex';
                kinhLup.style.alignItems = 'center';
                kinhLup.style.justifyContent = 'center';
                kinhLup.style.textAlign = 'center';
                kinhLup.style.overflow = 'hidden';
                kinhLup.style.padding = '10px';
            }
        });
    }

    // ==========================================
    // 3. LINH VẬT CON HEO (🐷) TƯƠNG TÁC F - O - G
    // ==========================================
    let pet = document.getElementById('pet-thu-cung');
    if (!pet) {
        pet = document.createElement('div');
        pet.id = 'pet-thu-cung';
        pet.textContent = '🐷';
        document.body.appendChild(pet);
    }

    let posX = window.innerWidth - 90;
    let posY = window.innerHeight - 90;

    function capNhatViTriPet() {
        pet.style.left = `${posX}px`;
        pet.style.top = `${posY}px`;
    }
    capNhatViTriPet();

    function showPetBubble(text) {
        let bubble = document.getElementById('pet-bubble');
        if (!bubble) {
            bubble = document.createElement('div');
            bubble.id = 'pet-bubble';
            document.body.appendChild(bubble);
        }
        bubble.textContent = text;
        bubble.style.left = `${Math.max(10, posX - 20)}px`;
        bubble.style.top = `${Math.max(10, posY - 40)}px`;
        bubble.style.display = 'block';

        setTimeout(() => {
            bubble.style.display = 'none';
        }, 1600);
    }

    const hanhDongHeo = {
        F: () => {
            pet.style.transform = 'scale(1.6)';
            showPetBubble('Phóng to siêu ú ỉn! 🐷💥');
            setTimeout(() => pet.style.transform = 'scale(1)', 800);
        },
        O: () => {
            pet.classList.add('heo-xoay');
            showPetBubble('Oai phong xoay tròn! 🌀✨');
            setTimeout(() => pet.classList.remove('heo-xoay'), 600);
        },
        G: () => {
            posX = 30;
            posY = 30;
            capNhatViTriPet();
            showPetBubble('Ghé góc màn hình nè! 📍');
        }
    };

    document.addEventListener('keydown', (e) => {
        if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;
        const key = e.key.toUpperCase();
        if (hanhDongHeo[key]) {
            hanhDongHeo[key]();
        }
    });

    const mapNutHeo = {
        'btn-pet-f': hanhDongHeo.F,
        'btn-pet-o': hanhDongHeo.O,
        'btn-pet-g': hanhDongHeo.G,
        'btn-heo-p': hanhDongHeo.F,
        'btn-heo-o': hanhDongHeo.O,
        'btn-heo-g': hanhDongHeo.G
    };

    Object.keys(mapNutHeo).forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                mapNutHeo[id]();
            });
        }
    });

    // Kéo thả con heo
    let isDragging = false, offsetX = 0, offsetY = 0;

    pet.addEventListener('pointerdown', (e) => {
        isDragging = true;
        offsetX = e.clientX - posX;
        offsetY = e.clientY - posY;
        pet.setPointerCapture(e.pointerId);
    });

    pet.addEventListener('pointermove', (e) => {
        if (!isDragging) return;
        posX = e.clientX - offsetX;
        posY = e.clientY - offsetY;
        capNhatViTriPet();
    });

    pet.addEventListener('pointerup', (e) => {
        if (isDragging) {
            isDragging = false;
            showPetBubble('Ủn ỉn thả tớ ra rồi! 🐷');
            try { pet.releasePointerCapture(e.pointerId); } catch(err) {}
        }
    });
});