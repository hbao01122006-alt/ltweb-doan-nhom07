import { docYeuThich, doiYeuThich, capNhatSoDem } from './yeu-thich.js';

const vung = document.querySelector('#danh-sach-yeu-thich');
const trangThai = document.querySelector('#yeu-thich-trang-thai');

function datNhan(nut) {
  const ten = nut.dataset.ten || 'món ăn';
  nut.textContent = '♥ Đã thích';
  nut.setAttribute('aria-pressed', 'true');
  nut.setAttribute('aria-label', `Bỏ yêu thích ${ten}`);
}

function hienThiDanhSach() {
  if (!vung || !trangThai) return;

  const ids = docYeuThich().map(Number).filter(Number.isInteger);
  let soHien = 0;

  vung.querySelectorAll('[data-the-yeu-thich-id]').forEach((the) => {
    const id = Number(the.dataset.theYeuThichId);
    const daThich = ids.includes(id);

    // Chi nhung mon da thich moi duoc phep hien thi.
    the.classList.toggle('dang-yeu-thich', daThich);
    the.hidden = !daThich;

    if (daThich) {
      soHien += 1;
      const nut = the.querySelector('.nut-yeu-thich');
      if (nut) datNhan(nut);
    }
  });

  trangThai.textContent = soHien === 0
    ? 'Bạn chưa có món yêu thích nào. Hãy vào Thực đơn và nhấn “♡ Yêu thích”.'
    : `Bạn đang có ${soHien} món trong danh sách yêu thích.`;

  capNhatSoDem();
}

vung?.addEventListener('click', (suKien) => {
  const nut = suKien.target.closest('.nut-yeu-thich');
  if (!nut) return;

  const id = Number(nut.dataset.yeuThichId);
  if (!Number.isInteger(id) || id < 1) return;

  // Tren trang nay nut chi co nghia la bo khoi danh sach yeu thich.
  doiYeuThich(id);
  hienThiDanhSach();
});

hienThiDanhSach();
