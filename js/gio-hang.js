// js/gio-hang.js - "Thêm vào giỏ" không tải lại trang: POST JSON tới api/gio-hang.php rồi cập nhật số món trên header.
// Tắt JavaScript thì form vẫn gửi bình thường tới gio-hang.php (nâng cấp dần).
document.querySelectorAll('.them-gio-form').forEach((form) => {
  form.addEventListener('submit', async (suKien) => {
    // "Đặt hàng ngay" phải đi theo luồng PHP để thêm món rồi chuyển sang trang xác nhận.
    if (suKien.submitter?.value === 'dat_ngay') return;
    if (!window.fetch) return;
    suKien.preventDefault();
    const thongBao = form.querySelector('[data-thong-bao]') ?? form.appendChild(Object.assign(document.createElement('p'), { role: 'status' }));
    thongBao.setAttribute('data-thong-bao', '');
    try {
      const phanHoi = await fetch('api/gio-hang.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: Number(form.elements.id.value), sl: Number(form.elements.so_luong.value) }),
      });
      const json = await phanHoi.json();
      if (!phanHoi.ok) throw new Error(json.loi || 'Không thêm được vào giỏ.');
      document.querySelectorAll('[data-so-mon]').forEach((o) => { o.textContent = String(json.so_mon); });
      thongBao.textContent = 'Đã thêm vào giỏ hàng.';
    } catch (loi) {
      thongBao.textContent = loi.message;
    }
  });
});
