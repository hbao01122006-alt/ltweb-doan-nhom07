<?php
// dang-nhap.php - đăng nhập bằng email + password_verify; session chỉ lưu uid; một thông báo lỗi chung.
// Thử: sai mật khẩu và email lạ nhận cùng một thông báo; đăng nhập đúng thì PHPSESSID đổi giá trị.
require __DIR__ . '/inc/config.php';

use App\Data\KhoNguoiDung;

$loi = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = mb_strtolower(trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : ''));
  $matKhau = is_string($_POST['mat_khau'] ?? null) ? $_POST['mat_khau'] : '';
  $kho = new KhoNguoiDung(db());
  $tk = $kho->timTheoEmail($email);
  if ($tk && password_verify($matKhau, $tk['mat_khau'])) {
    if (password_needs_rehash($tk['mat_khau'], PASSWORD_DEFAULT)) {
      $kho->capNhatMatKhau((int) $tk['id'], password_hash($matKhau, PASSWORD_DEFAULT));
    }
    session_regenerate_id(true);                       // chống session fixation
    $_SESSION['uid'] = (int) $tk['id'];
    $url = $_SESSION['url_sau_dang_nhap'] ?? 'index.php';
    unset($_SESSION['url_sau_dang_nhap']);
    chuyenHuong(is_string($url) && str_starts_with($url, '/') && !str_starts_with($url, '//') ? $url : 'index.php');
  }
  $loi = 'Email hoặc mật khẩu không đúng.';
}
$tb = flash('dang_ky');
$tieuDe = 'Đăng nhập';
$trang = '';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
  <h1>Đăng nhập</h1>
  <?php if ($tb): ?><p role="status"><?= e($tb) ?></p><?php endif; ?>
  <?php if ($loi): ?><p class="loi" role="alert"><?= e($loi) ?></p><?php endif; ?>
  <form method="post" class="the">
    <p><label for="u">Email</label><br><input id="u" type="email" name="email" value="<?= e($email) ?>" required autocomplete="username"></p>
    <p><label for="p">Mật khẩu</label><br><input id="p" name="mat_khau" type="password" required autocomplete="current-password"></p>
    <button type="submit">Đăng nhập</button> <a href="dang-ky.php">Đăng ký</a>
  </form>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>