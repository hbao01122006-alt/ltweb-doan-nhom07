// js/trang-danh-sach.js — trang danh-sach.html: tải data/san-pham.json,
// tìm kiếm tức thời (gõ không dấu vẫn được), lọc danh mục, sắp xếp,
// thêm / bỏ yêu thích bằng ủy quyền sự kiện. Đủ 3 trạng thái: tải – lỗi – rỗng.
import { taiJSON } from './api.js';
import { coYeuThich, doiYeuThich } from './yeu-thich.js';



function boDau(chuoi) {
  return chuoi.normalize('NFD').replace(/\p{M}/gu, '').replace(/đ/g, 'd').replace(/Đ/g, 'D').toLowerCase();
}

function dinhDangGia(gia) {
  return `${gia.toLocaleString('vi-VN')} VNĐ`;
}

function taoPhanTu(thang, noiDung = '', tenLop = '') {
  const phanTu = document.createElement(thang);
  if (noiDung !== '') phanTu.textContent = noiDung;
  if (tenLop !== '') phanTu.className = tenLop;
  return phanTu;
}

function hienLoi(vung, thongBao, hamThuLai) {
  const nut = taoPhanTu('button', 'Thử lại', 'nut-thu-lai');
  nut.type = 'button';
  nut.addEventListener('click', hamThuLai);
  vung.replaceChildren(taoPhanTu('span', `${thongBao} `), nut);
}

const oTimKiem = document.querySelector('#tim-kiem');
const oDanhMuc = document.querySelector('#loc-danh-muc');
const oSapXep = document.querySelector('#sap-xep');
const oChiYeuThich = document.querySelector('#chi-yeu-thich');
const vungTrangThai = document.querySelector('#trang-thai');
const luoi = document.querySelector('#luoi-san-pham');

let danhSachMon = [];

function taoNutYeuThich(mon) {
  const nut = taoPhanTu('button', '', 'nut-yeu-thich');
  nut.type = 'button';
  nut.dataset.id = String(mon.id);
  datNhanYeuThich(nut, mon.ten, coYeuThich(mon.id));
  return nut;
}

function datNhanYeuThich(nut, tenMon, daThich) {
  nut.textContent = daThich ? '♥ Đã thích' : '♡ Yêu thích';
  nut.setAttribute('aria-pressed', String(daThich));
  nut.setAttribute('aria-label', `${daThich ? 'Bỏ yêu thích' : 'Thêm yêu thích'} ${tenMon}`);
}

function taoThe(mon) {
  const the = taoPhanTu('article', '', 'the-san-pham');
  const anh = document.createElement('img');
  anh.className = 'the-san-pham__anh';
  anh.src = mon.anh;
  anh.alt = mon.anh_mo_ta;
  anh.width = 300;
  anh.height = 200;
  anh.loading = mon.id === 1 ? 'eager' : 'lazy';
  if (mon.id === 1) anh.fetchPriority = 'high';
  const gia = taoPhanTu('p');
  gia.append(taoPhanTu('strong', `Giá: ${dinhDangGia(mon.gia)}`));
  if (mon.so_luong_ton === 0) gia.append(taoPhanTu('span', ' (Hết hàng)', 'het-hang'));
  const hanhDong = taoPhanTu('p', '', 'the-san-pham__hanh-dong');
  const lienKet = taoPhanTu('a', 'Xem chi tiết món ăn');
  lienKet.href = `chi-tiet.html?id=${mon.id}`;
  hanhDong.append(lienKet, taoNutYeuThich(mon));
  the.append(taoPhanTu('h3', mon.ten, 'the-san-pham__tieu-de'), anh,
    taoPhanTu('p', mon.mo_ta), gia, hanhDong);
  return the;
}

function sapXep(ds, kieu) {
  const banSao = [...ds];
  if (kieu === 'gia-tang') banSao.sort((a, b) => a.gia - b.gia);
  else if (kieu === 'gia-giam') banSao.sort((a, b) => b.gia - a.gia);
  else if (kieu === 'ten-az') banSao.sort((a, b) => a.ten.localeCompare(b.ten, 'vi'));
  return banSao;
}

function locVaHienThi() {
  const tuKhoa = boDau(oTimKiem.value.trim());
  const ketQua = sapXep(
    danhSachMon.filter((mon) =>
      boDau(mon.ten).includes(tuKhoa)
      && (oDanhMuc.value === '' || mon.danh_muc === oDanhMuc.value)
      && (!oChiYeuThich.checked || coYeuThich(mon.id))),
    oSapXep.value,
  );
  luoi.replaceChildren(...ketQua.map(taoThe));
  vungTrangThai.textContent = ketQua.length === 0
    ? 'Không có món ăn nào phù hợp. Hãy thử từ khóa hoặc bộ lọc khác.'
    : `Hiển thị ${ketQua.length} / ${danhSachMon.length} món ăn.`;
}

function napDanhMuc(ds) {
  const cacDanhMuc = [...new Set(ds.map((mon) => mon.danh_muc))];
  cacDanhMuc.forEach((ten) => {
    const luaChon = taoPhanTu('option', ten);
    luaChon.value = ten;
    oDanhMuc.append(luaChon);
  });
}

function hienKhungDangTai() {
  // Giữ sẵn không gian cho lưới trong lúc fetch để tránh layout shift (CLS).
  const cacKhung = Array.from({ length: 12 }, () => {
    const the = taoPhanTu('article', '', 'the-san-pham the-san-pham--dang-tai');
    the.setAttribute('aria-hidden', 'true');
    the.innerHTML = '<div class="khung-tai khung-tai--tieu-de"></div><div class="khung-tai khung-tai--anh"></div><div class="khung-tai khung-tai--dong"></div><div class="khung-tai khung-tai--dong"></div>';
    return the;
  });
  luoi.replaceChildren(...cacKhung);
}

async function taiDanhSach() {
  vungTrangThai.textContent = 'Đang tải thực đơn…';
  // Khung tải đã có sẵn trong HTML trước lần vẽ đầu tiên để tránh CLS.
  if (luoi.children.length === 0) hienKhungDangTai();
  luoi.setAttribute('aria-busy', 'true');
  try {
    danhSachMon = await taiJSON('data/san-pham.json');
    if (oDanhMuc.options.length === 1) napDanhMuc(danhSachMon);
    locVaHienThi();
  } catch (loi) {
    console.error(loi);
    hienLoi(vungTrangThai, 'Không tải được thực đơn.', taiDanhSach);
  } finally {
    luoi.removeAttribute('aria-busy');
  }
}

[oTimKiem, oDanhMuc, oSapXep, oChiYeuThich].forEach((o) => {
  o.addEventListener('input', locVaHienThi);
});

// Ủy quyền sự kiện: một trình nghe ở phần tử cha cho mọi nút yêu thích
luoi.addEventListener('click', (suKien) => {
  const nut = suKien.target.closest('.nut-yeu-thich');
  if (!nut) return;
  const id = Number(nut.dataset.id);
  const mon = danhSachMon.find((x) => x.id === id);
  const daThich = doiYeuThich(id);
  if (oChiYeuThich.checked) locVaHienThi();
  else datNhanYeuThich(nut, mon.ten, daThich);
});

oChiYeuThich.checked = new URLSearchParams(location.search).get('yeu-thich') === '1';
taiDanhSach();
