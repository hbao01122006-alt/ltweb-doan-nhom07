/**
 * Script trang cá nhân - Trần Hồ Trung Hậu
 * Chức năng:
 * 1. Developer Terminal CLI Mini (Thay thế tìm kiếm kỹ năng).
 * 2. Đếm ngược thời gian thi giữa kỳ/cuối kỳ dựa theo ngày chọn trên lịch.
 * 3. Linh vật Con Rồng (🐉) với phím H - A - U.
 * 4. Hệ thống Thả Bọ (bò chậm), bắt bọ bằng click hoặc kéo thả Rồng lại gần.
 */

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. DEVELOPER TERMINAL CLI MINI
    // ==========================================
    const termInput = document.getElementById('terminal-input');
    const termOutput = document.getElementById('terminal-output');

    if (termInput && termOutput) {
        termInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const cmd = termInput.value.trim().toLowerCase();
                termInput.value = '';

                if (cmd === '') return;

                let res = '';
                switch (cmd) {
                    case 'help':
                        res = '📌 Danh sách lệnh khả dụng:\n' +
                              ' • <b style="color:#00ff00;">skills</b>   : Xem kỹ năng lập trình\n' +
                              ' • <b style="color:#00ff00;">projects</b> : Xem danh sách dự án\n' +
                              ' • <b style="color:#00ff00;">contact</b>  : Thông tin liên hệ\n' +
                              ' • <b style="color:#00ff00;">matrix</b>   : Bật hiệu ứng Hacker Matrix\n' +
                              ' • <b style="color:#00ff00;">clear</b>    : Xóa màn hình terminal';
                        break;
                    case 'skills':
                        res = '🚀 Kỹ năng: HTML5, CSS3, JavaScript, Python, C++, Java, Git & GitHub, Làm việc nhóm.';
                        break;
                    case 'projects':
                        res = '🍔 FastFood Website (Nhóm 07) | 🐍 Snake Game (OpenGL) | 🛒 Shopping Online (C++)';
                        break;
                    case 'contact':
                        res = '📧 Email: trunghautranho5@gmail.com | MSSV: 312024053';
                        break;
                    case 'matrix':
                        res = '🟢 [MATRIX ACTIVATED] Đã bật chế độ Hacker thành công!';
                        document.body.style.transition = 'background 0.5s ease';
                        document.body.style.background = '#001100';
                        setTimeout(() => {
                            document.body.style.background = '';
                        }, 3000);
                        break;
                    case 'clear':
                        termOutput.innerHTML = '';
                        return;
                    default:
                        res = `❌ Lệnh '${cmd}' không tồn tại. Gõ <b style="color:#00ff00;">help</b> để xem danh sách câu lệnh.`;
                }

                termOutput.innerHTML += `<div><span class="prompt">hau@dev:~$</span> ${cmd}</div>`;
                termOutput.innerHTML += `<div style="color: #aaaaaa; margin-bottom: 8px; white-space: pre-line;">${res}</div>`;
                termOutput.scrollTop = termOutput.scrollHeight;
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
            pet.classList.add('rong-nhay');
            showPetBubble('Rồng chào bạn Hậu nha! 👋🐉');
            setTimeout(() => pet.classList.remove('rong-nhay'), 500);
        },
        A: () => {
            pet.textContent = '🐉🔥';
            showPetBubble('Rồng phun lửa phè phè! 🔥🔥🔥');
            setTimeout(() => {
                pet.textContent = '🐉';
            }, 1200);
        },
        U: () => {
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

    document.addEventListener('keydown', (e) => {
        if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

        const key = e.key.toUpperCase();
        if (hanhDongRong[key]) {
            hanhDongRong[key]();
        }
    });

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

        bugX = Math.floor(Math.random() * (window.innerWidth - 80)) + 20;
        bugY = Math.floor(Math.random() * (window.innerHeight - 80)) + 20;

        activeBug.style.left = `${bugX}px`;
        activeBug.style.top = `${bugY}px`;
        document.body.appendChild(activeBug);

        showPetBubble('Có bọ kìa! Bắt nó đi Hậu ơi! 🐛');

        activeBug.addEventListener('click', batBoThanhCong);

        bugInterval = setInterval(() => {
            if (!activeBug) return;
            bugX += (Math.random() - 0.5) * 20;
            bugY += (Math.random() - 0.5) * 20;

            bugX = Math.max(20, Math.min(window.innerWidth - 60, bugX));
            bugY = Math.max(20, Math.min(window.innerHeight - 60, bugY));

            activeBug.style.left = `${bugX}px`;
            activeBug.style.top = `${bugY}px`;

            kiemTraRongGầnBo();
        }, 300);
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

    function kiemTraRongGầnBo() {
        if (!activeBug) return;
        const dx = (rongX + 30) - (bugX + 20);
        const dy = (rongY + 30) - (bugY + 20);
        const distance = Math.sqrt(dx * dx + dy * dy);

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
        kiemTraRongGầnBo();
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