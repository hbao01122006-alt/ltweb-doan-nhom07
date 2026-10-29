<?php
// dang-ky.php - tạo tài khoản khach_hang; mật khẩu băm bằng password_hash, KHÔNG nhận vai_tro từ form.
// Thử: email "abc", mật khẩu 7 ký tự, email trùng (viết HOA) -> báo lỗi từng ô; hợp lệ -> chuyển sang đăng nhập.
require __DIR__ . '/inc/config.php';
use App\Data\KhoNguoiDung;

$lay = static fn(string $k): string => is_string($_POST[$k] ?? null) ? $_POST[$k] : '';
$du = ['ho_ten' => '', 'email' => ''];
$loi = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $du['ho_ten'] = trim($lay('ho_ten'));
    $du['email'] = mb_strtolower(trim($lay('email')));
    $matKhau = $lay('mat_khau');
    if (mb_strlen($du['ho_ten']) < 2 || mb_strlen($du['ho_ten']) > 100) $loi['ho_ten'] = 'Họ tên dài 2 - 100 ký tự.';
    if (!filter_var($du['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($du['email']) > 190) $loi['email'] = 'Email không hợp lệ.';
    if (mb_strlen($matKhau) < 8) $loi['mat_khau'] = 'Mật khẩu phải có ít nhất 8 ký tự.';
    if (!$loi) {
        try {
            (new KhoNguoiDung(db()))->taoKhachHang($du['email'], $du['ho_ten'], password_hash($matKhau, PASSWORD_DEFAULT));
            flash('dang_ky', 'Đăng ký thành công, hãy đăng nhập.');
            chuyenHuong('dang-nhap.php');
        } catch (PDOException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) !== 1062) throw $e;
            $loi['email'] = 'Email đã được đăng ký.';
        }
    }
}
$tieuDe = 'Đăng ký';
$trang = '';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
  <h1>Đăng ký tài khoản</h1>
  <form method="post" class="the">
    <p><label for="ht">Họ tên</label><br>
      <input id="ht" name="ho_ten" value="<?= e($du['ho_ten']) ?>" maxlength="100" required autocomplete="name">
      <?php if (isset($loi['ho_ten'])): ?><span class="loi" role="alert"><?= e($loi['ho_ten']) ?></span><?php endif; ?></p>
    <p><label for="em">Email</label><br>
      <input id="em" type="email" name="email" value="<?= e($du['email']) ?>" maxlength="190" required autocomplete="email">
      <?php if (isset($loi['email'])): ?><span class="loi" role="alert"><?= e($loi['email']) ?></span><?php endif; ?></p>
    <p><label for="mk">Mật khẩu (tối thiểu 8 ký tự)</label><br>
      <input id="mk" type="password" name="mat_khau" minlength="8" required autocomplete="new-password">
      <?php if (isset($loi['mat_khau'])): ?><span class="loi" role="alert"><?= e($loi['mat_khau']) ?></span><?php endif; ?></p>
    <button type="submit">Đăng ký</button>
  </form>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
