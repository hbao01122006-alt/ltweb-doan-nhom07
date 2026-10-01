// js/trang-chu.js — khối thời tiết Đà Nẵng từ Open-Meteo (REST API công khai,
// không cần khóa). Chỉ lấy các trường cần dùng; có trạng thái tải – lỗi – rỗng.
import { taiJSON } from './api.js';



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

const vung = document.querySelector('#thoi-tiet-noi-dung');

function moTaThoiTiet(ma) {
  if (ma === 0) return 'Trời quang';
  if (ma <= 3) return 'Có mây';
  if (ma <= 48) return 'Sương mù';
  if (ma <= 67 || (ma >= 80 && ma <= 82)) return 'Có mưa';
  if (ma >= 95) return 'Dông';
  return 'Nhiều mây';
}

function goiY(nhietDo, maThoiTiet) {
  if ((maThoiTiet >= 51 && maThoiTiet <= 67) || maThoiTiet >= 80) {
    return 'Trời mưa, ngại ra đường? Để Foodspead giao tận nơi.';
  }
  if (nhietDo >= 32) return 'Trời nóng, hãy thử combo kèm nước mát lạnh.';
  return 'Thời tiết dễ chịu, hợp để thưởng thức gà rán nóng giòn.';
}

async function taiThoiTiet() {
  vung.textContent = 'Đang tải thời tiết…';
  try {
    const thamSo = new URLSearchParams({
      latitude: 16.05,
      longitude: 108.2,
      current: 'temperature_2m,relative_humidity_2m,weather_code',
      timezone: 'auto',
    });
    const duLieu = await taiJSON(`https://api.open-meteo.com/v1/forecast?${thamSo}`);
    const hienTai = duLieu.current;
    if (!hienTai || typeof hienTai.temperature_2m !== 'number') {
      vung.textContent = 'Chưa có dữ liệu thời tiết lúc này.';
      return;
    }
    vung.replaceChildren(
      taoPhanTu('p', `${Math.round(hienTai.temperature_2m)}°C – ${moTaThoiTiet(hienTai.weather_code)}`, 'thoi-tiet__nhiet-do'),
      taoPhanTu('p', `Độ ẩm: ${hienTai.relative_humidity_2m}%`),
      taoPhanTu('p', goiY(hienTai.temperature_2m, hienTai.weather_code)),
    );
  } catch (loi) {
    console.error(loi);
    hienLoi(vung, 'Không tải được thời tiết.', taiThoiTiet);
  }
}

taiThoiTiet();
