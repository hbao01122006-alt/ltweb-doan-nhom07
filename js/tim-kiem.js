// js/tim-kiem.js - tìm kiếm tức thời ở danh-sach.php: gõ xong 350 ms mới gọi api/san-pham.php?q= (debounce).
// Kết quả dựng bằng textContent (không innerHTML). Tắt JavaScript thì form GET vẫn tìm được.
(() => {
  const o = document.querySelector('#q');
  const khung = document.querySelector('#ket-qua-tim-kiem');
  if (!o || !khung) return;
  let hen;
  let lanGoi = 0;
  o.addEventListener('input', () => {
    clearTimeout(hen);
    hen = setTimeout(async () => {
      const lan = ++lanGoi;
      try {
        const phanHoi = await fetch('api/san-pham.php?q=' + encodeURIComponent(o.value.trim()));
        if (!phanHoi.ok || lan !== lanGoi) return;
        const { du_lieu: ds } = await phanHoi.json();
        if (!ds.length) {
          khung.replaceChildren(Object.assign(document.createElement('p'), { textContent: 'Không có món ăn nào phù hợp.' }));
          return;
        }
        khung.replaceChildren(...ds.map((x) => {
          const the = document.createElement('article');
          the.className = 'the-san-pham';
          const tieuDe = document.createElement('h2');
          tieuDe.className = 'the-san-pham__tieu-de';
          tieuDe.textContent = x.ten;
          const gia = document.createElement('p');
          gia.textContent = new Intl.NumberFormat('vi-VN').format(x.gia) + ' VNĐ' + (x.ton_kho === 0 ? ' (Hết hàng)' : '');
          const lienKet = document.createElement('a');
          lienKet.href = 'chi-tiet.php?id=' + x.id;
          lienKet.textContent = 'Xem chi tiết món ăn';
          the.append(tieuDe, gia, lienKet);
          return the;
        }));
      } catch (loi) {
        console.error(loi);
      }
    }, 350);
  });
})();
