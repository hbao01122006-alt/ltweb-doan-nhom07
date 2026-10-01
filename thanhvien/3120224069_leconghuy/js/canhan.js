/**
 * Script trang cá nhân - Lê Công Huy
 * Chức năng:
 * 1. Bộ phát âm thanh thư giãn / tiếng mưa tự động bằng Web Audio API.
 * 2. Công cụ tính điểm tổng kết môn học & quy đổi hệ 4 (GPA).
 * 3. Linh vật Gấu trúc (🐼) di chuyển bằng 4 MŨI TÊN + bấm phím H-U-Y + Kéo thả.
 */

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. BỘ PHÁT ÂM THANH THƯ GIÃN (WHITE NOISE / LO-FI)
    // ==========================================
    const btnPhatNhac = document.getElementById('btn-phat-nhac');
    const thanhAmLuong = document.getElementById('thanh-am-luong');
    const trangThaiNhac = document.getElementById('trang-thai-nhac');

    let audioCtx = null;
    let noiseNode = null;
    let gainNode = null;
    let isPlaying = false;

    function createWhiteNoise() {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const bufferSize = audioCtx.sampleRate * 2;
        const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
        const data = buffer.getChannelData(0);

        for (let i = 0; i < bufferSize; i++) {
            data[i] = Math.random() * 2 - 1;
        }

        noiseNode = audioCtx.createBufferSource();
        noiseNode.buffer = buffer;
        noiseNode.loop = true;

        gainNode = audioCtx.createGain();
        gainNode.gain.value = parseFloat(thanhAmLuong ? thanhAmLuong.value : 0.5);

        noiseNode.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        noiseNode.start();
    }

    if (btnPhatNhac) {
        btnPhatNhac.addEventListener('click', () => {
            if (!isPlaying) {
                if (!audioCtx) {
                    createWhiteNoise();
                } else if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                isPlaying = true;
                btnPhatNhac.textContent = '⏸️ Dừng âm thanh tập trung';
                if (trangThaiNhac) trangThaiNhac.textContent = '🎵 Đang phát âm thanh thư giãn (Tiếng mưa nhẹ)...';
            } else {
                if (audioCtx) audioCtx.suspend();
                isPlaying = false;
                btnPhatNhac.textContent = '▶️ Bật âm thanh thư giãn (White Noise)';
                if (trangThaiNhac) trangThaiNhac.textContent = 'Âm thanh đang tắt.';
            }
        });
    }

    if (thanhAmLuong) {
        thanhAmLuong.addEventListener('input', (e) => {
            if (gainNode && audioCtx) {
                gainNode.gain.value = parseFloat(e.target.value);
            }
        });
    }

    // ==========================================
    // 2. TÍNH ĐIỂM TỔNG KẾT & GPA DỰ KIẾN
    // ==========================================
    const btnTinhGpa = document.getElementById('btn-tinh-gpa');
    const ketQuaGpa = document.getElementById('ket-qua-gpa');

    if (btnTinhGpa && ketQuaGpa) {
        btnTinhGpa.addEventListener('click', () => {
            const cc = parseFloat(document.getElementById('diem-chuyen-can').value);
            const gk = parseFloat(document.getElementById('diem-giua-ky').value);
            const ck = parseFloat(document.getElementById('diem-cuoi-ky').value);

            if (isNaN(cc) || isNaN(gk) || isNaN(ck) || cc < 0 || cc > 10 || gk < 0 || gk > 10 || ck < 0 || ck > 10) {
                ketQuaGpa.textContent = '⚠️ Vui lòng nhập đầy đủ điểm từ 0 đến 10 cho cả 3 thành phần!';
                ketQuaGpa.style.color = '#b5122b';
                return;
            }

            const diemHe10 = (cc * 0.1) + (gk * 0.3) + (ck * 0.6);
            let diemChuu = 'F';
            let diemHe4 = 0.0;

            if (diemHe10 >= 8.5) { diemChuu = 'A'; diemHe4 = 4.0; }
            else if (diemHe10 >= 7.0) { diemChuu = 'B'; diemHe4 = 3.0; }
            else if (diemHe10 >= 5.5) { diemChuu = 'C'; diemHe4 = 2.0; }
            else if (diemHe10 >= 4.0) { diemChuu = 'D'; diemHe4 = 1.0; }
            else { diemChuu = 'F'; diemHe4 = 0.0; }

            ketQuaGpa.style.color = 'var(--chu)';
            ketQuaGpa.innerHTML = `
                📊 <strong>Kết quả dự tính của Huy:</strong><br>
                - Điểm hệ 10: <strong>${diemHe10.toFixed(2)}</strong><br>
                - Điểm chữ: <strong>${diemChuu}</strong> | Điểm hệ 4 (GPA): <strong>${diemHe4.toFixed(1)}</strong>
            `;
        });
    }

    // ==========================================
    // 3. LINH VẬT GẤU TRÚC (🐼) TƯƠNG TÁC & DI CHUYỂN
    // ==========================================
    let pet = document.getElementById('pet-thu-cung');
    if (!pet) {
        pet = document.createElement('div');
        pet.id = 'pet-thu-cung';
        pet.textContent = '🐼';
        document.body.appendChild(pet);
    }

    let pandaX = window.innerWidth - 90;
    let pandaY = window.innerHeight - 90;
    const buocNhay = 25; // Khoảng cách di chuyển mỗi lần bấm phím (px)

    function capNhatViTriPanda() {
        // Giới hạn trong giới hạn màn hình
        pandaX = Math.max(10, Math.min(window.innerWidth - 70, pandaX));
        pandaY = Math.max(10, Math.min(window.innerHeight - 70, pandaY));

        pet.style.left = `${pandaX}px`;
        pet.style.top = `${pandaY}px`;

        // Cập nhật vị trí bóng bóng nói nếu đang hiện
        const bubble = document.getElementById('pet-bubble');
        if (bubble && bubble.style.display === 'block') {
            bubble.style.left = `${Math.max(10, pandaX - 30)}px`;
            bubble.style.top = `${Math.max(10, pandaY - 40)}px`;
        }
    }
    capNhatViTriPanda();

    function showPetBubble(text) {
        let bubble = document.getElementById('pet-bubble');
        if (!bubble) {
            bubble = document.createElement('div');
            bubble.id = 'pet-bubble';
            document.body.appendChild(bubble);
        }
        bubble.textContent = text;
        bubble.style.left = `${Math.max(10, pandaX - 30)}px`;
        bubble.style.top = `${Math.max(10, pandaY - 40)}px`;
        bubble.style.display = 'block';

        setTimeout(() => {
            bubble.style.display = 'none';
        }, 2000);
    }

    // Hành động thoại H - U - Y
    const hanhDongPanda = {
        H: () => {
            pet.classList.add('panda-nhay');
            showPetBubble('Chào bạn! Mình là Gấu trúc của Huy nè 👋🐼');
            setTimeout(() => pet.classList.remove('panda-nhay'), 500);
        },
        U: () => {
            pet.textContent = '🐼✨';
            showPetBubble('Chúc bạn học tập thật tốt và luôn vui vẻ! 🌟');
            setTimeout(() => { pet.textContent = '🐼'; }, 1500);
        },
        Y: () => {
            pet.textContent = '🐼💪';
            showPetBubble('Thành công luôn đến từ sự kiên trì! Cố lên! 🔥');
            setTimeout(() => { pet.textContent = '🐼'; }, 1500);
        }
    };

    // BÀN PHÍM: Xử lý 4 Mũi tên di chuyển + 3 phím H-U-Y
    document.addEventListener('keydown', (e) => {
        if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

        const key = e.key;

        // Di chuyển bằng 4 phím mũi tên
        if (key === 'ArrowUp') {
            e.preventDefault();
            pandaY -= buocNhay;
            capNhatViTriPanda();
        } else if (key === 'ArrowDown') {
            e.preventDefault();
            pandaY += buocNhay;
            capNhatViTriPanda();
        } else if (key === 'ArrowLeft') {
            e.preventDefault();
            pandaX -= buocNhay;
            capNhatViTriPanda();
        } else if (key === 'ArrowRight') {
            e.preventDefault();
            pandaX += buocNhay;
            capNhatViTriPanda();
        }

        // Thoại bằng H - U - Y
        const keyUpper = key.toUpperCase();
        if (hanhDongPanda[keyUpper]) {
            hanhDongPanda[keyUpper]();
        }
    });

    // Nút bấm trên giao diện H - U - Y
    const mapNutPanda = {
        'btn-panda-h': hanhDongPanda.H,
        'btn-panda-u': hanhDongPanda.U,
        'btn-panda-y': hanhDongPanda.Y
    };

    Object.keys(mapNutPanda).forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                mapNutPanda[id]();
            });
        }
    });

    // Kéo thả linh vật Gấu trúc
    let isDragging = false, offsetX = 0, offsetY = 0;

    pet.addEventListener('pointerdown', (e) => {
        isDragging = true;
        offsetX = e.clientX - pandaX;
        offsetY = e.clientY - pandaY;
        pet.style.cursor = 'grabbing';
        pet.setPointerCapture(e.pointerId);
    });

    pet.addEventListener('pointermove', (e) => {
        if (!isDragging) return;
        pandaX = e.clientX - offsetX;
        pandaY = e.clientY - offsetY;
        capNhatViTriPanda();
    });

    const ngungKeo = (e) => {
        if (isDragging) {
            isDragging = false;
            pet.style.cursor = 'grab';
            try { pet.releasePointerCapture(e.pointerId); } catch(err) {}
        }
    };

    pet.addEventListener('pointerup', ngungKeo);
    pet.addEventListener('pointercancel', ngungKeo);
});