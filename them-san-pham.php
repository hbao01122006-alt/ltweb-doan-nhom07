<?php
// them-san-pham.php - quản trị: thêm món (kiểm tra bằng kiemTraSanPham, ảnh tải lên, PRG + flash).
// Thử: tên trống, giá 0, tồn kho -1, danh mục 999 -> báo lỗi cạnh từng ô, giữ giá trị đã nhập, không thêm dòng nào.
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/kiem-tra.php';
use App\Data\KhoSanPham;

yeuCauQuyen('quan_tri');
$kho = new KhoSanPham(db());
$loi = [];
$f = ['ten' => '', 'danh_muc_id' => 0, 'gia' => '', 'ton_kho' => 0, 'mo_ta' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$du, $loi] = kiemTraSanPham($_POST, $kho->idDanhMuc());
    [$anh, $loiAnh] = xuLyAnhTaiLen($_FILES['anh'] ?? null);
    if ($loiAnh) $loi['anh'] = $loiAnh;
    if (!$loi) {
        $id = $kho->them($du, $anh ?? 'images/logo.png');
        flash('qt', 'Đã thêm món #' . $id . '.');
        chuyenHuong('quan-tri.php');
    }
    $f = $_POST;
}
$dsDm = $kho->danhMuc();
$anhHienTai = null;
$nhanNut = 'Thêm món';
$tieuDe = 'Thêm món ăn';
$trang = 'quan-tri';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
  <h1>Thêm món ăn</h1>
  <?php require __DIR__ . '/inc/form-san-pham.php'; ?>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
