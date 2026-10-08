<?php
// dang-nhap.php - dang nhap quan tri bang password_verify va doi ma phien.
require __DIR__.'/inc/config.php';
$loi=''; $tenDangNhap='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $tenDangNhap=trim((string)($_POST['ten_dang_nhap']??'')); $matKhau=(string)($_POST['mat_khau']??'');
  $taiKhoan=require __DIR__.'/inc/tai-khoan.php'; $hash=$taiKhoan[$tenDangNhap]??null;
  if (is_string($hash) && password_verify($matKhau,$hash)) {
    session_regenerate_id(true); $_SESSION['user']=$tenDangNhap; flash('dang_nhap','Đăng nhập thành công.'); chuyenHuong('quan-tri.php');
  }
  $loi='Sai tên đăng nhập hoặc mật khẩu.'; error_log('Dang nhap sai: '.preg_replace('/[^a-zA-Z0-9_.-]/','?',$tenDangNhap));
}
$tb=flash('dang_nhap'); $tieuDe='Đăng nhập'; $trang=''; require __DIR__.'/inc/header.php';
?>
<main class="trang__chinh"><h1>Đăng nhập quản trị</h1><?php if($tb):?><p><?=e($tb)?></p><?php endif;?><?php if($loi):?><p class="loi" role="alert"><?=e($loi)?></p><?php endif;?><form method="post" class="the"><p><label for="u">Tên đăng nhập</label><br><input id="u" name="ten_dang_nhap" value="<?=e($tenDangNhap)?>" required autocomplete="username"></p><p><label for="p">Mật khẩu</label><br><input id="p" name="mat_khau" type="password" required autocomplete="current-password"></p><button>Đăng nhập</button></form></main>
<?php require __DIR__.'/inc/footer.php'; ?>
