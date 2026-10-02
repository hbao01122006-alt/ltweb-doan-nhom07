// js/trang-lien-he.js — kiểm tra biểu mẫu phía client (validity /
// setCustomValidity), báo lỗi dưới từng ô khi rời ô và khi gửi,
// gửi bằng fetch POST tới API giả lập jsonplaceholder, khóa nút khi chờ.
import { taiJSON, guiJSON } from './api.js';



function taoPhanTu(thang, noiDung = '', tenLop = '') {
  const phanTu = document.createElement(thang);
  if (noiDung !== '') phanTu.textContent = noiDung;
  if (tenLop !== '') phanTu.className = tenLop;
  return phanTu;
}

const API_LIEN_HE = 'https://jsonplaceholder.typicode.com/posts';
const bieuMau = document.querySelector('#bieu-mau-lien-he');
const nutGui = bieuMau.querySelector('button[type="submit"]');
const vungKetQua = document.querySelector('#ket-qua-gui');
const oNgayGiao = bieuMau.elements['ngay-giao'];

// Ngày giao không được ở quá khứ
const homNay = new Date().toISOString().slice(0, 10);
oNgayGiao.min = homNay;

function layThongBaoLoi(o) {
  o.setCustomValidity('');
  if (o.name === 'ho-ten' && o.value.trim().length > 0 && o.value.trim().length < 2) {
    o.setCustomValidity('Họ và tên cần ít nhất 2 ký tự.');
  }
  const v = o.validity;
  if (v.valid) return '';
  if (v.valueMissing) return 'Vui lòng nhập thông tin này.';
  if (v.typeMismatch) return 'Địa chỉ email chưa đúng định dạng (ví dụ: ten@gmail.com).';
  if (v.patternMismatch) return 'Số điện thoại gồm đúng 10 chữ số.';
  if (v.rangeUnderflow) return o.type === 'date' ? 'Ngày nhận hàng không được ở quá khứ.' : 'Số lượng tối thiểu là 1.';
  if (v.rangeOverflow) return 'Số lượng tối đa là 20.';
  return o.validationMessage;
}

function kiemTraO(o) {
  const thongBao = layThongBaoLoi(o);
  const vungLoi = document.querySelector(`#loi-${o.id}`);
  if (vungLoi) vungLoi.textContent = thongBao;
  o.setAttribute('aria-invalid', String(thongBao !== ''));
  return thongBao === '';
}

const cacO = [...bieuMau.elements].filter((o) => o.id && document.querySelector(`#loi-${o.id}`));

// Rời ô thì kiểm tra ô đó; đang sửa ô đã báo lỗi thì kiểm tra lại ngay
bieuMau.addEventListener('focusout', (suKien) => {
  if (cacO.includes(suKien.target)) kiemTraO(suKien.target);
});
bieuMau.addEventListener('input', (suKien) => {
  if (suKien.target.getAttribute('aria-invalid') === 'true') kiemTraO(suKien.target);
});
bieuMau.addEventListener('reset', () => {
  cacO.forEach((o) => {
    document.querySelector(`#loi-${o.id}`).textContent = '';
    o.removeAttribute('aria-invalid');
  });
  vungKetQua.textContent = '';
});

bieuMau.addEventListener('submit', async (suKien) => {
  suKien.preventDefault();
  const oLoi = cacO.filter((o) => !kiemTraO(o));
  if (oLoi.length > 0) {
    vungKetQua.textContent = `Có ${oLoi.length} ô chưa hợp lệ, vui lòng kiểm tra lại.`;
    oLoi[0].focus();
    return;
  }
  nutGui.disabled = true;
  vungKetQua.textContent = 'Đang gửi đơn đặt hàng…';
  try {
    const duLieu = Object.fromEntries(new FormData(bieuMau));
    const phanHoi = await guiJSON(API_LIEN_HE, {
      title: `Đơn đặt hàng của ${duLieu['ho-ten']}`,
      body: JSON.stringify(duLieu),
      userId: 1,
    });
    bieuMau.reset();
    vungKetQua.textContent = `Gửi thành công! Mã đơn tạm: #${phanHoi.id}. Foodspead sẽ liên hệ bạn sớm.`;
  } catch (loi) {
    console.error(loi);
    vungKetQua.textContent = 'Gửi không thành công (kiểm tra kết nối mạng). Dữ liệu vẫn được giữ, hãy bấm gửi lại.';
  } finally {
    nutGui.disabled = false;
  }
});

// Nạp đủ món ăn vào ô chọn; lỗi thì giữ các lựa chọn tĩnh có sẵn
try {
  const danhSach = await taiJSON('data/san-pham.json');
  const oChon = bieuMau.elements['mon-an'];
  oChon.replaceChildren(...danhSach.map((mon) => {
    const luaChon = taoPhanTu('option', mon.ten);
    luaChon.value = String(mon.id);
    return luaChon;
  }));
} catch (loi) {
  console.error(loi);
}
