<?php
// dat-hang.php - đặt hàng từ giỏ (phải đăng nhập). GET chỉ hiện trang xác nhận; chỉ POST mới tạo đơn.
// Thử: đặt hợp lệ rồi F5 (không tạo đơn thứ hai - PRG); gõ ?xac_nhan=1 trên thanh địa chỉ cũng không tạo đơn.
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/don-hang.php';
use App\Services\GioHang;

$nd = yeuCauDangNhap();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $tieuDe = 'Xác nhận đặt hàng';
    $trang = 'gio-hang';
    require __DIR__ . '/inc/header.php';
    ?>
<main class="trang__chinh">
  <h1>Xác nhận đặt hàng</h1>
  <p>Đơn hàng sẽ trừ tồn kho trong một giao dịch MySQL.</p>
  <form method="post"><button type="submit">Đặt hàng</button></form>
  <p><a href="gio-hang.php">Quay lại giỏ hàng</a></p>
</main>
    <?php
    require __DIR__ . '/inc/footer.php';
    exit;
}
try {
    $id = luuDonHang((int) $nd['id'], new GioHang());
    flash('don', 'Đặt hàng thành công, mã đơn #' . $id);
    chuyenHuong('don-hang.php?id=' . $id);
} catch (RuntimeException $e) {
    flash('gio', $e->getMessage());
    chuyenHuong('gio-hang.php');
}
