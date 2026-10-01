// js/api.js — nơi DUY NHẤT gọi fetch của cả website.
// taiJSON: GET một địa chỉ và trả về JSON; guiJSON: POST dữ liệu dạng JSON.
// Cả hai đều kiểm tra res.ok và ném Error nếu máy chủ trả mã lỗi.

export async function taiJSON(url) {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`HTTP ${res.status} khi tải ${url}`);
  return res.json();
}

export async function guiJSON(url, duLieu) {
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json; charset=UTF-8' },
    body: JSON.stringify(duLieu),
  });
  if (!res.ok) throw new Error(`HTTP ${res.status} khi gửi tới ${url}`);
  return res.json();
}
