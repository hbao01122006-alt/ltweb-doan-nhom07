<?php
// chi-tiet.php - chi tiet theo id, ghi cookie 4 mon da xem va them vao gio.
require __DIR__ . '/inc/config.php';
use App\Data\KhoSanPham;

$kho = new KhoSanPham(__DIR__ . '/data/san-pham.json');
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$sp = $id === false || $id === null ? null : $kho->timTheoId((int)$id);
if ($sp === null) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}
$cu = [];
foreach (explode(',', $_COOKIE['da_xem'] ?? '') as $x) {
    $cid = filter_var($x, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($cid !== false && $kho->timTheoId((int)$cid) !== null) $cu[] = (int)$cid;
}
$moi = array_values(array_unique([$sp->id, ...$cu]));
setcookie('da_xem', implode(',', array_slice($moi, 0, 4)), ['expires'=>time()+30*86400,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
$tieuDe = $sp->ten; $trang = 'danh-sach'; $lopTrang = 'trang--chi-tiet';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
<article class="chi-tiet__luoi">
<h1 class="chi-tiet__tieu-de">Thông tin chi tiết: <?= e($sp->ten) ?></h1>
<section class="chi-tiet__muc">
<h2>Hình ảnh sản phẩm</h2>
<figure><img class="chi-tiet__anh" src="<?= e($sp->anh) ?>" alt="<?= e($sp->anhMoTa) ?>" width="450" height="300"><figcaption><?= e($sp->ten) ?> của Foodspead</figcaption></figure>
<form method="post" action="gio-hang.php">
<input type="hidden" name="hanh_dong" value="them"><input type="hidden" name="id" value="<?= $sp->id ?>">
<label for="sl">Số lượng:</label> <input id="sl" name="so_luong" type="number" min="1" max="20" value="1" required>
<button type="submit"<?= $sp->soLuongTon === 0 ? ' disabled' : '' ?>>Thêm vào giỏ</button> <button type="button" class="nut-yeu-thich" data-yeu-thich-id="<?= $sp->id ?>" data-ten="<?= e($sp->ten) ?>" aria-pressed="false">♡ Yêu thích</button>
</form>
</section>
<section class="chi-tiet__muc chi-tiet__muc--phu"><h2>Thông số chi tiết</h2><table class="chi-tiet__bang"><tbody>
<tr><th scope="row">Tên món</th><td><?= e($sp->ten) ?></td></tr><tr><th scope="row">Danh mục</th><td><?= e($sp->danhMuc) ?></td></tr><tr><th scope="row">Giá bán</th><td><?= e(vnd($sp->gia)) ?></td></tr><tr><th scope="row">Tình trạng</th><td><?= $sp->soLuongTon > 0 ? 'Còn hàng (' . $sp->soLuongTon . ' suất)' : 'Hết hàng' ?></td></tr><tr><th scope="row">Mô tả</th><td><?= e($sp->moTa) ?></td></tr><tr><th scope="row">Khẩu phần</th><td><?= e($sp->khauPhan) ?></td></tr><tr><th scope="row">Thành phần</th><td><?= e($sp->thanhPhan) ?></td></tr><tr><th scope="row">Năng lượng</th><td>~ <?= $sp->nangLuongKcal ?> kcal</td></tr><tr><th scope="row">Thời gian chế biến</th><td><?= $sp->thoiGianPhut ?> phút</td></tr>
</tbody></table></section>
</article></main>
<?php require __DIR__ . '/inc/footer.php'; ?>
