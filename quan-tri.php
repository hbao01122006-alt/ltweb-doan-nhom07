<?php
// quan-tri.php - quản trị: danh sách món có phân trang, liên kết thêm/sửa, nút xóa (POST, có hỏi lại),
// và các liên hệ đã nhận (mới nhất trước) kèm ảnh đính kèm hiện ngay trong bảng (bấm vào ảnh để xem cỡ gốc).
// Chỉ vai_tro = quan_tri: khách -> 403, chưa đăng nhập -> chuyển tới dang-nhap.php (yeuCauQuyen).
// Thử: đăng nhập admin@foods.example, gửi liên hệ kèm ảnh ở lien-he.php rồi mở lại trang này.
require __DIR__ . '/inc/config.php';
use App\Data\KhoLienHe;
use App\Data\KhoSanPham;

yeuCauQuyen('quan_tri');
$kho = new KhoSanPham(db());
$moiTrang = 10;
$soTrang = max(1, (int) ceil($kho->demTatCa() / $moiTrang));
$hienTai = filter_var($_GET['trang'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);
$hienTai = min((int) $hienTai, $soTrang);
$ds = $kho->trangQuanTri($moiTrang, ($hienTai - 1) * $moiTrang);
$dsLienHe = (new KhoLienHe(db()))->moiNhat(10);
$tb = flash('qt');
$tieuDe = 'Quản trị';
$trang = 'quan-tri';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
  <h1>Quản trị Foodspead</h1>
  <?php if ($tb): ?><p role="status" class="trang-thai"><?= e($tb) ?></p><?php endif; ?>
  <p><a href="them-san-pham.php">+ Thêm món ăn</a> · <a href="don-hang.php">Quản lý đơn hàng</a></p>
  <div class="bang-boc"><table>
    <caption>Danh sách món ăn</caption>
    <thead><tr><th scope="col">ID</th><th scope="col">Tên</th><th scope="col">Danh mục</th><th scope="col">Giá</th><th scope="col">Tồn</th><th scope="col">Thao tác</th></tr></thead>
    <tbody>
    <?php foreach ($ds as $r): ?>
      <tr>
        <td><?= (int) $r['id'] ?></td><td><?= e($r['ten']) ?></td><td><?= e($r['danh_muc']) ?></td>
        <td><?= e(vnd((int) $r['gia'])) ?></td><td><?= (int) $r['ton_kho'] ?></td>
        <td><a href="sua-san-pham.php?id=<?= (int) $r['id'] ?>">Sửa</a>
          <form method="post" action="xoa-san-pham.php" class="form-dong" onsubmit="return confirm('Xóa món này?')">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button type="submit">Xóa</button>
          </form></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php if ($soTrang > 1): ?>
    <nav aria-label="Phân trang"><p>
      <?php for ($i = 1; $i <= $soTrang; $i++): ?><a href="?trang=<?= $i ?>"<?= $i === $hienTai ? ' aria-current="page"' : '' ?>><?= $i ?></a> <?php endfor; ?>
    </p></nav>
  <?php endif; ?>

  <h2>Liên hệ mới nhất</h2>
  <?php if (!$dsLienHe): ?><p>Chưa có liên hệ nào.</p><?php else: ?>
  <div class="bang-boc"><table>
    <caption>Liên hệ đã nhận</caption>
    <thead><tr><th scope="col">Ngày</th><th scope="col">Họ tên</th><th scope="col">Email</th><th scope="col">Nội dung</th><th scope="col">Ảnh</th></tr></thead>
    <tbody>
    <?php foreach ($dsLienHe as $r): ?>
      <tr><td><?= e($r['ngay_tao']) ?></td><td><?= e($r['ho_ten']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['noi_dung']) ?></td>
        <td><?php if ($r['anh']): ?><a href="uploads/<?= e(basename($r['anh'])) ?>"><img src="uploads/<?= e(basename($r['anh'])) ?>" alt="Ảnh đính kèm của <?= e($r['ho_ten']) ?>" width="80" height="60" loading="lazy"></a><?php else: ?>-<?php endif; ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
