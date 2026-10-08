<?php
// danh-sach.php - danh sach mon an sinh bang PHP, loc/tim/sap xep qua GET.
require __DIR__ . '/inc/config.php';
use App\Data\KhoSanPham;

$kho = new KhoSanPham(__DIR__ . '/data/san-pham.json');
$q = trim((string)($_GET['q'] ?? ''));
if (mb_strlen($q) > 100) $q = mb_substr($q, 0, 100);
$dm = trim((string)($_GET['dm'] ?? ''));
$dsDanhMuc = $kho->danhMuc();
if ($dm !== '' && !in_array($dm, $dsDanhMuc, true)) $dm = '';
$sx = (string)($_GET['sx'] ?? 'mac-dinh');
$choPhepSapXep = ['mac-dinh', 'gia-tang', 'gia-giam', 'ten-az'];
if (!in_array($sx, $choPhepSapXep, true)) $sx = 'mac-dinh';
$ds = $kho->timKiem($q, $dm, $sx);
$tieuDe = 'Thực đơn'; $trang = 'danh-sach'; $lopTrang = 'trang--danh-sach';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
<h1>Thực đơn thức ăn nhanh Foodspead</h1>
<form method="get" class="cong-cu-loc" role="search" style="display:grid">
  <p><label for="q">Tìm món ăn:</label><br><input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Ví dụ: gà rán, burger"></p>
  <p><label for="dm">Danh mục:</label><br><select id="dm" name="dm"><option value="">Tất cả danh mục</option><?php foreach ($dsDanhMuc as $ten): ?><option value="<?= e($ten) ?>"<?= $dm === $ten ? ' selected' : '' ?>><?= e($ten) ?></option><?php endforeach; ?></select></p>
  <p><label for="sx">Sắp xếp:</label><br><select id="sx" name="sx"><option value="mac-dinh"<?= $sx==='mac-dinh'?' selected':'' ?>>Mặc định</option><option value="gia-tang"<?= $sx==='gia-tang'?' selected':'' ?>>Giá tăng dần</option><option value="gia-giam"<?= $sx==='gia-giam'?' selected':'' ?>>Giá giảm dần</option><option value="ten-az"<?= $sx==='ten-az'?' selected':'' ?>>Tên A → Z</option></select></p>
  <p><button type="submit">Lọc kết quả</button> <a href="danh-sach.php">Xóa bộ lọc</a></p>
</form>
<p class="trang-thai">Hiển thị <?= count($ds) ?> / <?= count($kho->tatCa()) ?> món ăn.</p>
<div class="danh-sach__khoi luoi-the">
<?php if (!$ds): ?><p>Không có món ăn nào phù hợp. Hãy thử từ khóa hoặc bộ lọc khác.</p><?php endif; ?>
<?php foreach ($ds as $sp): ?>
<article class="the-san-pham">
<h2 class="the-san-pham__tieu-de"><?= e($sp->ten) ?></h2>
<img class="the-san-pham__anh" src="<?= e($sp->anh) ?>" alt="<?= e($sp->anhMoTa) ?>" width="300" height="200" loading="lazy">
<p><?= e($sp->moTa) ?></p>
<p><strong>Giá: <?= e(vnd($sp->gia)) ?></strong><?= $sp->soLuongTon === 0 ? ' <span class="het-hang">(Hết hàng)</span>' : '' ?></p>
<p class="the-san-pham__hanh-dong"><a href="chi-tiet.php?id=<?= $sp->id ?>">Xem chi tiết món ăn</a> <button type="button" class="nut-yeu-thich" data-yeu-thich-id="<?= $sp->id ?>" data-ten="<?= e($sp->ten) ?>" aria-pressed="false">♡ Yêu thích</button></p>
</article>
<?php endforeach; ?>
</div>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
