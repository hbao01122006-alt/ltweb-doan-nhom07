// js/main.js — nạp ở mọi trang: đánh dấu có JavaScript, menu thu gọn
// trên điện thoại (nút ☰, aria-expanded, Esc để đóng), số đếm yêu thích.
import { capNhatSoDem } from './yeu-thich.js';

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
