<?php
// index.php - trang chu PHP: mon noi bat, da xem gan day va REST API thoi tiet.
require __DIR__ . '/inc/config.php';
use App\Data\KhoSanPham;

$kho = new KhoSanPham(db());
$noiBat = $kho->noiBat(4);
$idsDaXem = [];
foreach (explode(',', is_string($_COOKIE['da_xem'] ?? null) ? $_COOKIE['da_xem'] : '') as $x) {
    $id = filter_var($x, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id !== false) $idsDaXem[] = (int)$id;
}
$mapDaXem = $kho->theoIds($idsDaXem);
$daXem = [];
foreach ($idsDaXem as $id) if (isset($mapDaXem[$id])) $daXem[] = $mapDaXem[$id];
$tieuDe = 'Trang chủ';
$trang = 'index';
$lopTrang = 'trang--chu';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
  <h1>Chào mừng bạn đến với FastFood Foodspead</h1>
  <section class="the the--noi-bat">
    <h2>Món nổi bật</h2>
    <div class="danh-sach__khoi luoi-the">
      <?php foreach ($noiBat as $sp): ?>
      <article class="the-san-pham">
        <h3 class="the-san-pham__tieu-de"><?= e($sp->ten) ?></h3>
        <img class="the-san-pham__anh" src="<?= e($sp->anh) ?>" alt="<?= e($sp->anhMoTa) ?>" width="300" height="200" loading="lazy">
        <p><?= e($sp->moTa) ?></p>
        <p><strong>Giá: <?= e(vnd($sp->gia)) ?></strong></p>
        <p><a href="chi-tiet.php?id=<?= $sp->id ?>">Xem chi tiết</a></p>
      </article>
      <?php endforeach; ?>
    </div>
  </section>
  <section class="the the--noi-bat">
    <h2>Đã xem gần đây</h2>
    <?php if (!$daXem): ?><p>Chưa có món nào được xem gần đây.</p><?php else: ?>
      <ul><?php foreach ($daXem as $sp): ?><li><a href="chi-tiet.php?id=<?= $sp->id ?>"><?= e($sp->ten) ?></a></li><?php endforeach; ?></ul>
    <?php endif; ?>
  </section>
  <section class="the the--noi-bat">
    <h2>Thời tiết Đà Nẵng hôm nay</h2>
    <div aria-live="polite" id="thoi-tiet-noi-dung"><p>Đang tải thời tiết…</p></div>
    <noscript><p>JavaScript đang tắt. Các chức năng PHP của website vẫn sử dụng bình thường.</p></noscript>
  </section>
</main>
<script src="js/trang-chu.js" type="module"></script>
<?php require __DIR__ . '/inc/footer.php'; ?>
