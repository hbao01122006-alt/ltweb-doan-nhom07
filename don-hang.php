<?php
// don-hang.php - "Đơn hàng của tôi": danh sách đơn kèm tổng tiền, xem chi tiết một đơn.
// Quản trị viên xem mọi đơn và đổi trạng thái (POST). Khách chỉ thấy đơn của mình; đơn người khác -> 404.
require __DIR__ . '/inc/config.php';
use App\Data\KhoDonHang;

$nd = yeuCauDangNhap();
$laAdmin = $nd['vai_tro'] === 'quan_tri';
$kho = new KhoDonHang(db());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$laAdmin) {                                    // đổi trạng thái chỉ dành cho quản trị
        http_response_code(403);
        require __DIR__ . '/403.php';
        exit;
    }
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    $tt = is_string($_POST['trang_thai'] ?? null) ? $_POST['trang_thai'] : '';
    if ($id && $kho->doiTrangThai((int) $id, $tt)) flash('don', 'Đã cập nhật trạng thái đơn #' . $id . '.');
    else flash('don', 'Trạng thái hoặc mã đơn không hợp lệ.');
    chuyenHuong('don-hang.php');
}

$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (isset($_GET['id'])) {
    $don = $id ? $kho->timTheoId((int) $id, $laAdmin ? null : (int) $nd['id']) : null;
    if (!$don) {
        http_response_code(404);
        require __DIR__ . '/404.php';
        exit;
    }
    $chiTiet = $kho->chiTiet((int) $don['id']);
    $tong = array_sum(array_map(fn($r) => $r['so_luong'] * $r['don_gia'], $chiTiet));
} else {
    $ds = $kho->danhSach($laAdmin ? null : (int) $nd['id']);
}
$tb = flash('don');
$tieuDe = $laAdmin ? 'Tất cả đơn hàng' : 'Đơn hàng của tôi';
$trang = 'don-hang';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
  <h1><?= e($tieuDe) ?></h1>
  <?php if ($tb): ?><p role="status" class="trang-thai"><?= e($tb) ?></p><?php endif; ?>
  <?php if (isset($don)): ?>
    <h2>Đơn #<?= (int) $don['id'] ?></h2>
    <p>Khách: <?= e($don['ho_ten']) ?> · Ngày đặt: <?= e($don['ngay_tao']) ?> · Trạng thái: <?= e($don['trang_thai']) ?></p>
    <div class="bang-boc"><table>
      <caption>Chi tiết đơn</caption>
      <thead><tr><th scope="col">Món</th><th scope="col">SL</th><th scope="col">Đơn giá lúc đặt</th><th scope="col">Thành tiền</th></tr></thead>
      <tbody>
      <?php foreach ($chiTiet as $r): ?>
        <tr><td><?= e($r['ten']) ?></td><td><?= (int) $r['so_luong'] ?></td><td><?= e(vnd((int) $r['don_gia'])) ?></td><td><?= e(vnd((int) $r['so_luong'] * (int) $r['don_gia'])) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <p><strong>Tổng tiền: <?= e(vnd((int) $tong)) ?></strong></p>
    <p><a href="don-hang.php">&larr; Danh sách đơn</a></p>
  <?php elseif (!$ds): ?>
    <p>Chưa có đơn hàng nào. <a href="danh-sach.php">Chọn món ngay</a></p>
  <?php else: ?>
    <div class="bang-boc"><table>
      <caption>Danh sách đơn hàng</caption>
      <thead><tr><th scope="col">Mã</th><?php if ($laAdmin): ?><th scope="col">Khách</th><?php endif; ?><th scope="col">Ngày</th><th scope="col">Tổng</th><th scope="col">Trạng thái</th><th scope="col">Xem</th></tr></thead>
      <tbody>
      <?php foreach ($ds as $r): ?>
        <tr>
          <td>#<?= (int) $r['id'] ?></td>
          <?php if ($laAdmin): ?><td><?= e($r['ho_ten']) ?></td><?php endif; ?>
          <td><?= e($r['ngay_tao']) ?></td>
          <td><?= e(vnd((int) $r['tong'])) ?></td>
          <td><?= e($r['trang_thai']) ?>
            <?php if ($laAdmin): ?>
              <form method="post">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <label>Trạng thái mới <select name="trang_thai">
                  <?php foreach (KhoDonHang::TRANG_THAI as $x): ?><option<?= $x === $r['trang_thai'] ? ' selected' : '' ?>><?= e($x) ?></option><?php endforeach; ?>
                </select></label>
                <button type="submit">Đổi</button>
              </form>
            <?php endif; ?>
          </td>
          <td><a href="don-hang.php?id=<?= (int) $r['id'] ?>">Xem</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
