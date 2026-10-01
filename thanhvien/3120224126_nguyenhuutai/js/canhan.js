/**
 * Script trang cá nhân - Nguyễn Hữu Tài
 */

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. TÌM KIẾM / LỌC KỸ NĂNG VÀ SỞ THÍCH
    // ==========================================
    const oTimKiem = document.getElementById('o-tim-kiem');
    const danhSachKyNang = document.querySelectorAll('.danh-sach-ky-nang li');
    const danhSachSoThich = document.querySelectorAll('.danh-sach-so-thich li');
    const thongBaoTimKiem = document.getElementById('thong-bao-tim-kiem');

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

            danhSachSoThich.forEach((li) => {
                const noiDung = li.textContent.toLowerCase();
                if (noiDung.includes(tuKhoa)) {
                    li.style.display = '';
                    soLuongKhop++;
                } else {
                    li.style.display = 'none';
                }
            });

            if (thongBaoTimKiem) {
                thongBaoTimKiem.textContent = (soLuongKhop === 0 && tuKhoa !== '') 
                    ? 'Không tìm thấy kỹ năng hoặc sở thích phù hợp.' 
                    : '';
            }
        });
    }

    // ==========================================
    // 2. BÚT CHÌ VẼ TRỰC TIẾP (BỔ SUNG PHÍM TẮT ESC / X)
    // ==========================================
    let canvas = document.getElementById('draw-canvas');
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.id = 'draw-canvas';
        document.body.appendChild(canvas);
    }
    const ctx = canvas.getContext('2d');

    let dangVe = false;
    let batButVe = false;

    function capNhatCanvasSize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    capNhatCanvasSize();
    window.addEventListener('resize', capNhatCanvasSize);

    const btnPencil = document.getElementById('btn-pencil');
    const btnClearCanvas = document.getElementById('btn-clear-canvas');

    // Hàm chuyển đổi trạng thái Bật / Tắt Bút
    function togglePencil(forceState = null) {
        if (forceState !== null) {
            batButVe = forceState;
        } else {
            batButVe = !batButVe;
        }
        
        dangVe = false;

        if (batButVe) {
            // BẬT BÚT VẼ
            canvas.classList.add('active-ve');
            canvas.style.pointerEvents = 'auto';
            if (btnPencil) {
                btnPencil.classList.add('active');
                btnPencil.textContent = '✏️️ Tắt Bút Chì (ESC / X)';
            }
        } else {
            // TẮT BÚT VẼ
            canvas.classList.remove('active-ve');
            canvas.style.pointerEvents = 'none';
            if (btnPencil) {
                btnPencil.classList.remove('active');
                btnPencil.textContent = '✏️ Bật Bút Chì Vẽ';
            }
        }
    }

    if (btnPencil) {
        btnPencil.addEventListener('click', (e) => {
            e.preventDefault();
            togglePencil();
        });
    }

    if (btnClearCanvas) {
        btnClearCanvas.addEventListener('click', (e) => {
            e.preventDefault();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });
    }

    canvas.addEventListener('pointerdown', (e) => {
        if (!batButVe) return;
        dangVe = true;
        ctx.beginPath();
        ctx.moveTo(e.clientX, e.clientY);
    });

    canvas.addEventListener('pointermove', (e) => {
        if (!dangVe || !batButVe) return;
        ctx.strokeStyle = '#b5122b';
        ctx.lineWidth = 4;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.lineTo(e.clientX, e.clientY);
        ctx.stroke();
    });

    const dungVe = () => { dangVe = false; };
    canvas.addEventListener('pointerup', dungVe);
    canvas.addEventListener('pointercancel', dungVe);

    // ==========================================
    // 3. XỬ LÝ PHÍM TẮT TRÊN BÀN PHÍM
    // ==========================================
    document.addEventListener('keydown', (e) => {
        // Nếu người dùng đang gõ trong ô tìm kiếm thì bỏ qua phím tắt
        if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

        const key = e.key.toUpperCase();

        // 1. Bấm ESC hoặc phím X để TẮT BÚT
        if (e.key === 'Escape' || key === 'X') {
            togglePencil(false); // Ép trạng thái tắt bút
            return;
        }

        // 2. Bấm phím B để BẬT/TẮT BÚT nhanh
        if (key === 'B') {
            togglePencil();
            return;
        }

        // 3. Tương tác với Con Cáo bằng T - A - I
        if (hanhDongCao[key]) {
            hanhDongCao[key]();
        }
    });

    // ==========================================
    // 4. LINH VẬT CON CÁO (🦊) TƯƠNG TÁC T - A - I
    // ==========================================
    let pet = document.getElementById('pet-thu-cung');
    if (!pet) {
        pet = document.createElement('div');
        pet.id = 'pet-thu-cung';
        pet.textContent = '🦊';
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

    const hanhDongCao = {
        T: () => {
            pet.style.transform = 'scale(1.6)';
            showPetBubble('Cáo nhỏ phóng to nè! 🦊💥');
            setTimeout(() => pet.style.transform = 'scale(1)', 800);
        },
        A: () => {
            pet.classList.add('cao-xoay');
            showPetBubble('Ảo thuật xoay 360 độ! 🌀✨');
            setTimeout(() => pet.classList.remove('cao-xoay'), 600);
        },
        I: () => {
            posX = 30;
            posY = 30;
            capNhatViTriPet();
            showPetBubble('Cáo ghé góc trang rồi! 📍');
        }
    };

    const mapNutCao = {
        'btn-pet-t': hanhDongCao.T,
        'btn-pet-a': hanhDongCao.A,
        'btn-pet-i': hanhDongCao.I
    };

    Object.keys(mapNutCao).forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                mapNutCao[id]();
            });
        }
    });

    // Kéo thả con cáo
    let isDragging = false, offsetX = 0, offsetY = 0;

    pet.addEventListener('pointerdown', (e) => {
        if (batButVe) return;
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
            showPetBubble('Cáo thả bạn ra nè! 🦊');
            try { pet.releasePointerCapture(e.pointerId); } catch(err) {}
        }
    });
});
// Cap nhat chuc nang trang ca nhan - Nguyen Huu Tai