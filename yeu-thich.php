<?php
// yeu-thich.php - danh sach mon an yeu thich luu tren trinh duyet bang localStorage.
require __DIR__ . '/inc/config.php';
use App\Data\KhoSanPham;

$kho = new KhoSanPham(db());
$tatCa = $kho->tatCa();
$tieuDe = 'Danh sách yêu thích';
$trang = 'yeu-thich';
$lopTrang = 'trang--yeu-thich';
require __DIR__ . '/inc/header.php';
?>
<style>
/* Trang yeu thich: mac dinh an tat ca, chi JS danh dau mon da thich moi duoc hien. */
.trang--yeu-thich [data-the-yeu-thich-id] { display: none !important; }
.trang--yeu-thich [data-the-yeu-thich-id].dang-yeu-thich { display: flex !important; }
</style>
<main class="trang__chinh">
  <h1>Danh sách yêu thích</h1>
  <p>Các món bạn đánh dấu yêu thích trên thiết bị này sẽ xuất hiện ở đây.</p>
  <p id="yeu-thich-trang-thai" class="trang-thai" aria-live="polite">Đang kiểm tra danh sách yêu thích…</p>

  <div class="danh-sach__khoi luoi-the" id="danh-sach-yeu-thich">
    <?php foreach ($tatCa as $sp): ?>
      <article class="the-san-pham" data-the-yeu-thich-id="<?= $sp->id ?>" hidden>
        <h2 class="the-san-pham__tieu-de"><?= e($sp->ten) ?></h2>
        <img class="the-san-pham__anh" src="<?= e($sp->anh) ?>" alt="<?= e($sp->anhMoTa) ?>" width="300" height="200" loading="lazy">
        <p><?= e($sp->moTa) ?></p>
        <p><strong>Giá: <?= e(vnd($sp->gia)) ?></strong><?= $sp->soLuongTon === 0 ? ' <span class="het-hang">(Hết hàng)</span>' : '' ?></p>
        <p class="the-san-pham__hanh-dong">
          <a href="chi-tiet.php?id=<?= $sp->id ?>">Xem chi tiết món ăn</a>
          <button type="button" class="nut-yeu-thich" data-yeu-thich-id="<?= $sp->id ?>" data-ten="<?= e($sp->ten) ?>" aria-pressed="true">♥ Đã thích</button>
        </p>
      </article>
    <?php endforeach; ?>
  </div>

  <noscript><p>Trang danh sách yêu thích cần JavaScript vì dữ liệu yêu thích được lưu trên trình duyệt.</p></noscript>
</main>
<script src="js/trang-yeu-thich.js?v=3" type="module"></script>
<?php require __DIR__ . '/inc/footer.php'; ?>
