// js/yeu-thich.js — đọc / ghi danh sách món yêu thích trong localStorage
// (lưu mảng id dạng JSON) và cập nhật số đếm trên header.

const KHOA = 'foodspead-yeu-thich';

export function docYeuThich() {
  try {
    const ds = JSON.parse(localStorage.getItem(KHOA) ?? '[]');
    return Array.isArray(ds) ? ds : [];
  } catch (loi) {
    console.error(loi);
    return [];
  }
}

export function coYeuThich(id) {
  return docYeuThich().includes(id);
}

// Thêm nếu chưa có, bỏ nếu đã có; trả về true khi đang là "yêu thích"
export function doiYeuThich(id) {
  const ds = docYeuThich();
  const daCo = ds.includes(id);
  const moi = daCo ? ds.filter((x) => x !== id) : [...ds, id];
  try {
    localStorage.setItem(KHOA, JSON.stringify(moi));
  } catch (loi) {
    console.error(loi);
  }
  capNhatSoDem();
  return !daCo;
}

export function capNhatSoDem() {
  const soLuong = docYeuThich().length;
  document.querySelectorAll('[data-dem-yeu-thich]').forEach((o) => {
    o.textContent = String(soLuong);
  });
}
