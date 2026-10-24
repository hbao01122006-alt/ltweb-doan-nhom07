<?php
// sua-san-pham.php?id=12 - quản trị: sửa món, form điền sẵn dữ liệu cũ; id sai -> 404; PRG + flash.
// Dùng chung kiemTraSanPham() và inc/form-san-pham.php với trang thêm.
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/kiem-tra.php';
use App\Data\KhoSanPham;

yeuCauQuyen('quan_tri');
$kho = new KhoSanPham(db());
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$cu = $id ? $kho->dongTho((int) $id) : null;
if (!$cu) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}
$loi = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$du, $loi] = kiemTraSanPham($_POST, $kho->idDanhMuc());
    [$anh, $loiAnh] = xuLyAnhTaiLen($_FILES['anh'] ?? null);
    if ($loiAnh) $loi['anh'] = $loiAnh;
    if (!$loi) {
        $kho->capNhat((int) $id, $du, $anh);
        flash('qt', 'Đã lưu món #' . $id . '.');
        chuyenHuong('quan-tri.php');
    }
}
$f = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $cu;
$dsDm = $kho->danhMuc();
$anhHienTai = $cu['anh'];
$nhanNut = 'Lưu thay đổi';
$tieuDe = 'Sửa món ăn';
$trang = 'quan-tri';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
  <h1>Sửa món ăn #<?= (int) $id ?></h1>
  <?php require __DIR__ . '/inc/form-san-pham.php'; ?>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
