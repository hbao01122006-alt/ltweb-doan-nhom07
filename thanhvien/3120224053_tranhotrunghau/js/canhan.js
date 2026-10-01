/**
 * Script trang cá nhân - Trần Hồ Trung Hậu
 * Chức năng:
 * 1. Lọc danh sách kỹ năng theo từ khóa.
 * 2. Đếm ngược thời gian thi giữa kỳ/cuối kỳ dựa theo ngày chọn trên lịch.
 * 3. Linh vật Con Rồng (🐉) với phím H - A - U.
 * 4. Hệ thống Thả Bọ (bò chậm), bắt bọ bằng click hoặc kéo thả Rồng lại gần.
 */

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. TÌM KIẾM / LỌC KỸ NĂNG CÁ NHÂN
    // ==========================================
    const oTimKiem = document.getElementById('o-tim-kiem-ky-nang');
    const danhSachKyNang = document.querySelectorAll('.danh-sach-ky-nang li');
    const thongBaoKyNang = document.getElementById('thong-bao-ky-nang');

    if (oTimKiem) {
        oTimKiem.addEventListener('input', (e) => {
            const tuKhoa = e.target.value.toLowerCase().trim();
            let soLuongKhop = 0;

            danhSachKyNang.forEach((li) => {
                const noiDung = li.textContent.toLowerCase();
                if (noiDung.includes(tuKhoa)) {
                    li.style.display = '';
                    soLuongKhop++;
                } else {
                    li.style.display = 'none';
                }
            });

            if (thongBaoKyNang) {
                thongBaoKyNang.textContent = (soLuongKhop === 0 && tuKhoa !== '') 
                    ? 'Không tìm thấy kỹ năng phù hợp.' 
                    : '';
            }
        });
    }

    // ==========================================
    // 2. ĐẾM NGƯỢC THỜI GIAN NGÀY THI
    // ==========================================
    const inputNgayThi = document.getElementById('input-ngay-thi');
    const ketQuaDemNguoc = document.getElementById('ket-qua-dem-nguoc');
    let demNguocTimer = null;

    if (inputNgayThi && ketQuaDemNguoc) {
        inputNgayThi.addEventListener('change', () => {
            const ngayChon = inputNgayThi.value;
            if (!ngayChon) return;

            const targetDate = new Date(ngayChon + 'T00:00:00');

            if (demNguocTimer) clearInterval(demNguocTimer);

            function capNhatDemNguoc() {
                const now = new Date();
                const diff = targetDate - now;

                if (diff <= 0) {
                    ketQuaDemNguoc.textContent = '🎉 Đã đến ngày thi rồi! Chúc bạn Hậu thi thật tốt, đạt điểm cao! 💯';
                    clearInterval(demNguocTimer);
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
                const minutes = Math.floor((diff / (1000 * 60)) % 60);
                const seconds = Math.floor((diff / 1000) % 60);

                ketQuaDemNguoc.textContent = `🔥 Còn lại: ${days} ngày, ${hours} giờ, ${minutes} phút, ${seconds} giây là đến ngày thi!`;
            }

            capNhatDemNguoc();
            demNguocTimer = setInterval(capNhatDemNguoc, 1000);
        });
    }

    // ==========================================
    // 3. LINH VẬT CON RỒNG (🐉) VÀ BỘ PHÍM H - A - U
    // ==========================================
    let pet = document.getElementById('pet-thu-cung');
    if (!pet) {
        pet = document.createElement('div');
        pet.id = 'pet-thu-cung';
        pet.textContent = '🐉';
        document.body.appendChild(pet);
    }

    let rongX = window.innerWidth - 100;
    let rongY = window.innerHeight - 100;

    function capNhatViTriRong() {
        pet.style.left = `${rongX}px`;
        pet.style.top = `${rongY}px`;
    }
    capNhatViTriRong();

    function showPetBubble(text) {
        let bubble = document.getElementById('pet-bubble');
        if (!bubble) {
            bubble = document.createElement('div');
            bubble.id = 'pet-bubble';
            document.body.appendChild(bubble);
        }
        bubble.textContent = text;
        bubble.style.left = `${Math.max(10, rongX - 20)}px`;
        bubble.style.top = `${Math.max(10, rongY - 40)}px`;
        bubble.style.display = 'block';

        setTimeout(() => {
            bubble.style.display = 'none';
        }, 1800);
    }

    const hanhDongRong = {
        H: () => {
            // H - Xin chào
            pet.classList.add('rong-nhay');
            showPetBubble('Rồng chào bạn Hậu nha! 👋🐉');
            setTimeout(() => pet.classList.remove('rong-nhay'), 500);
        },
        A: () => {
            // A - Phun lửa
            pet.textContent = '🐉🔥';
            showPetBubble('Rồng phun lửa phè phè! 🔥🔥🔥');
            setTimeout(() => {
                pet.textContent = '🐉';
            }, 1200);
        },
        U: () => {
            // U - Phóng to/Thu nhỏ rồi bay về góc trái
            pet.style.transform = 'scale(1.8)';
            showPetBubble('Biến to nè!... 🐲');

            setTimeout(() => {
                pet.style.transform = 'scale(0.8)';
                showPetBubble('Thu nhỏ và bay về góc trái! 🚀');

                setTimeout(() => {
                    rongX = 20;
                    rongY = 20;
                    capNhatViTriRong();
                    pet.style.transform = 'scale(1)';
                }, 600);
            }, 600);
        }
    };

    // Lắng nghe phím H - A - U
    document.addEventListener('keydown', (e) => {
        if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

        const key = e.key.toUpperCase();
        if (hanhDongRong[key]) {
            hanhDongRong[key]();
        }
    });

    // Lắng nghe 3 nút bấm tương ứng
    const mapNutRong = {
        'btn-rong-h': hanhDongRong.H,
        'btn-rong-a': hanhDongRong.A,
        'btn-rong-u': hanhDongRong.U
    };

    Object.keys(mapNutRong).forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                mapNutRong[id]();
            });
        }
    });

    // ==========================================
    // 4. TRÒ CHƠI THẢ BỌ & BẮT BỌ (BÒ CHẬM)
    // ==========================================
    let activeBug = null;
    let bugInterval = null;
    let bugX = 0, bugY = 0;

    const btnThaBo = document.getElementById('btn-tha-bo');

    function createBug() {
        if (activeBug) activeBug.remove();
        if (bugInterval) clearInterval(bugInterval);

        activeBug = document.createElement('div');
        activeBug.className = 'con-bo-target';
        activeBug.textContent = '🐛';

        // Tọa độ ngẫu nhiên
        bugX = Math.floor(Math.random() * (window.innerWidth - 80)) + 20;
        bugY = Math.floor(Math.random() * (window.innerHeight - 80)) + 20;

        activeBug.style.left = `${bugX}px`;
        activeBug.style.top = `${bugY}px`;
        document.body.appendChild(activeBug);

        showPetBubble('Có bọ kìa! Bắt nó đi Hậu ơi! 🐛');

        // Bắt bọ bằng cách CLICK CHUỘT trực tiếp vào con bọ
        activeBug.addEventListener('click', batBoThanhCong);

        // Bọ di chuyển với TỐC ĐỘ CHẬM
        bugInterval = setInterval(() => {
            if (!activeBug) return;
            // Di chuyển nhẹ từ 8px - 15px
            bugX += (Math.random() - 0.5) * 20;
            bugY += (Math.random() - 0.5) * 20;

            // Giới hạn trong màn hình
            bugX = Math.max(20, Math.min(window.innerWidth - 60, bugX));
            bugY = Math.max(20, Math.min(window.innerHeight - 60, bugY));

            activeBug.style.left = `${bugX}px`;
            activeBug.style.top = `${bugY}px`;

            kiemTraRongGầnBo();
        }, 300); // Cập nhật chậm 300ms/lần
    }

    if (btnThaBo) {
        btnThaBo.addEventListener('click', (e) => {
            e.preventDefault();
            createBug();
        });
    }

    function batBoThanhCong() {
        if (!activeBug) return;
        activeBug.remove();
        activeBug = null;
        if (bugInterval) clearInterval(bugInterval);

        showPetBubble('Đã bắt được bọ rồi! Giỏi quá! 🎉🐛');
    }

    // Kiểm tra khoảng cách khi kéo thả Rồng lại gần Bọ
    function kiemTraRongGầnBo() {
        if (!activeBug) return;
        const dx = (rongX + 30) - (bugX + 20);
        const dy = (rongY + 30) - (bugY + 20);
        const distance = Math.sqrt(dx * dx + dy * dy);

        // Khoảng cách dưới 50px là Rồng ăn bọ
        if (distance < 50) {
            batBoThanhCong();
        }
    }

    // ==========================================
    // 5. KÉO THẢ LINH VẬT CON RỒNG
    // ==========================================
    let isDragging = false, offsetX = 0, offsetY = 0;

    pet.addEventListener('pointerdown', (e) => {
        isDragging = true;
        offsetX = e.clientX - rongX;
        offsetY = e.clientY - rongY;
        pet.style.cursor = 'grabbing';
        pet.setPointerCapture(e.pointerId);
    });

    pet.addEventListener('pointermove', (e) => {
        if (!isDragging) return;
        rongX = e.clientX - offsetX;
        rongY = e.clientY - offsetY;
        capNhatViTriRong();
        kiemTraRongGầnBo(); // Kiểm tra va chạm với Bọ khi đang kéo thả
    });

    const ngungKeoTho = (e) => {
        if (isDragging) {
            isDragging = false;
            pet.style.cursor = 'grab';
            try { pet.releasePointerCapture(e.pointerId); } catch(err) {}
        }
    };

    pet.addEventListener('pointerup', ngungKeoTho);
    pet.addEventListener('pointercancel', ngungKeoTho);
});