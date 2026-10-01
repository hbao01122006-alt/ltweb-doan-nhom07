// js/trang-chi-tiet.js — trang chi-tiet.html?id=N: đọc id bằng URLSearchParams,
// tìm đúng một món trong data/san-pham.json, đổi document.title theo tên món.
// Không có id / id sai thì báo "Không tìm thấy".
import { taiJSON } from './api.js';
import { coYeuThich, doiYeuThich } from './yeu-thich.js';



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

const khung = document.querySelector('#chi-tiet');
const vungTrangThai = document.querySelector('#chi-tiet-trang-thai');
const id = Number(new URLSearchParams(location.search).get('id'));

function taoHang(nhan, giaTri) {
  const hang = document.createElement('tr');
  const o = taoPhanTu('th', nhan);
  o.scope = 'row';
  hang.append(o, taoPhanTu('td', giaTri));
  return hang;
}

function hienKhongTimThay() {
  const lienKet = taoPhanTu('a', 'Quay lại thực đơn');
  lienKet.href = 'danh-sach.html';
  const thongBao = taoPhanTu('p', 'Không tìm thấy món ăn này. ');
  thongBao.append(lienKet);
  khung.replaceChildren(taoPhanTu('h1', 'Không tìm thấy món ăn', 'chi-tiet__tieu-de'), thongBao);
  document.title = 'Không tìm thấy món ăn | Foodspead';
}

function hienMon(mon) {
  document.title = `${mon.ten} – Foodspead`;
  khung.querySelector('h1').textContent = `Thông tin chi tiết: ${mon.ten}`;
  const anh = khung.querySelector('.chi-tiet__anh');
  anh.src = mon.anh;
  anh.alt = mon.anh_mo_ta;
  khung.querySelector('figcaption').textContent = `Hình 1: ${mon.ten} của hệ thống cửa hàng Foodspead`;
  khung.querySelector('caption').textContent = `Bảng thông số chi tiết của ${mon.ten}`;
  khung.querySelector('tbody').replaceChildren(
    taoHang('Tên món ăn', mon.ten),
    taoHang('Danh mục', mon.danh_muc),
    taoHang('Giá bán', dinhDangGia(mon.gia)),
    taoHang('Tình trạng', mon.so_luong_ton > 0 ? `Còn hàng (${mon.so_luong_ton} suất)` : 'Hết hàng'),
    taoHang('Mô tả', mon.mo_ta),
    taoHang('Khẩu phần', mon.khau_phan),
    taoHang('Thành phần', mon.thanh_phan),
    taoHang('Năng lượng', `~ ${mon.nang_luong_kcal} kcal`),
    taoHang('Thời gian chế biến', `${mon.thoi_gian_phut} phút`),
  );
  const nut = khung.querySelector('.nut-yeu-thich');
  const datNhan = (daThich) => {
    nut.textContent = daThich ? '♥ Đã thích' : '♡ Thêm vào yêu thích';
    nut.setAttribute('aria-pressed', String(daThich));
  };
  datNhan(coYeuThich(mon.id));
  nut.addEventListener('click', () => datNhan(doiYeuThich(mon.id)));
}

async function taiMon() {
  vungTrangThai.textContent = 'Đang tải thông tin món ăn…';
  khung.setAttribute('aria-busy', 'true');
  try {
    const danhSach = await taiJSON('data/san-pham.json');
    const mon = Number.isInteger(id) ? danhSach.find((x) => x.id === id) : undefined;
    if (!mon) {
      hienKhongTimThay();
    } else {
      hienMon(mon);
    }
    vungTrangThai.textContent = '';
  } catch (loi) {
    console.error(loi);
    hienLoi(vungTrangThai, 'Không tải được dữ liệu món ăn.', taiMon);
  } finally {
    khung.removeAttribute('aria-busy');
  }
}

taiMon();
