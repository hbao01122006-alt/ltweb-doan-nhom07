// js/main.js — nạp ở mọi trang: đánh dấu có JavaScript, menu thu gọn
// trên điện thoại (nút ☰, aria-expanded, Esc để đóng), số đếm yêu thích.
import { capNhatSoDem, coYeuThich, doiYeuThich } from './yeu-thich.js';

document.documentElement.classList.add('js');

const nutMenu = document.querySelector('.nut-menu');
const menu = document.querySelector('#menu-chinh');

function datTrangThaiMenu(dangMo) {
  menu.classList.toggle('mo', dangMo);
  nutMenu.setAttribute('aria-expanded', String(dangMo));
}

if (nutMenu && menu) {
  nutMenu.addEventListener('click', () => {
    datTrangThaiMenu(!menu.classList.contains('mo'));
  });
  document.addEventListener('keydown', (suKien) => {
    if (suKien.key === 'Escape' && menu.classList.contains('mo')) {
      datTrangThaiMenu(false);
      nutMenu.focus();
    }
  });
}

// Đánh dấu liên kết của trang hiện tại
const tenTep = location.pathname.split('/').pop() || 'index.html';
document.querySelectorAll('#menu-chinh a').forEach((lienKet) => {
  if (lienKet.getAttribute('href').split('?')[0] === tenTep) {
    lienKet.setAttribute('aria-current', 'page');
  }
});

capNhatSoDem();


function datNhanNutYeuThich(nut, daThich) {
  const ten = nut.dataset.ten || 'món ăn';
  nut.textContent = daThich ? '♥ Đã thích' : '♡ Yêu thích';
  nut.setAttribute('aria-pressed', String(daThich));
  nut.setAttribute('aria-label', `${daThich ? 'Bỏ yêu thích' : 'Thêm yêu thích'} ${ten}`);
}

document.querySelectorAll('[data-yeu-thich-id]').forEach((nut) => {
  const id = Number(nut.dataset.yeuThichId);
  if (Number.isInteger(id) && id > 0) datNhanNutYeuThich(nut, coYeuThich(id));
});

document.addEventListener('click', (suKien) => {
  const nut = suKien.target.closest('[data-yeu-thich-id]');
  if (!nut || nut.closest('#danh-sach-yeu-thich')) return;
  const id = Number(nut.dataset.yeuThichId);
  if (!Number.isInteger(id) || id < 1) return;
  datNhanNutYeuThich(nut, doiYeuThich(id));
});
