<?php
// lien-he.php - kiem tra bieu mau o may chu, upload ảnh an toàn, lưu vào bảng lien_he (KhoLienHe) và PRG.
require __DIR__ . '/inc/config.php';
use App\Data\KhoLienHe;

$du=['hoten'=>'','email'=>'','sdt'=>'','noidung'=>'']; $loi=[]; $tenAnh=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  foreach ($du as $k=>$v) $du[$k]=is_string($_POST[$k]??null) ? trim($_POST[$k]) : '';
  if ($du['hoten']==='' || mb_strlen($du['hoten'])<2 || mb_strlen($du['hoten'])>80) $loi['hoten']='Họ tên phải từ 2 đến 80 ký tự.';
  if (!filter_var($du['email'], FILTER_VALIDATE_EMAIL)) $loi['email']='Email không hợp lệ.';
  if (!preg_match('/^(0|\+84)[0-9]{9,10}$/', $du['sdt'])) $loi['sdt']='Số điện thoại không hợp lệ.';
  if ($du['noidung']==='' || mb_strlen($du['noidung'])<5 || mb_strlen($du['noidung'])>1000) $loi['noidung']='Nội dung phải từ 5 đến 1000 ký tự.';
  $tep=$_FILES['anh']??null;
  if (is_array($tep) && ($tep['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE) {
    if (($tep['error']??UPLOAD_ERR_OK)!==UPLOAD_ERR_OK) $loi['anh']='Tải ảnh lên không thành công.';
    elseif (($tep['size']??0)>2*1024*1024) $loi['anh']='Ảnh không được lớn hơn 2 MB.';
    else {
      $finfo=new finfo(FILEINFO_MIME_TYPE); $mime=$finfo->file((string)$tep['tmp_name']);
      $choPhep=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
      if (!isset($choPhep[$mime])) $loi['anh']='Chỉ nhận ảnh JPG, PNG hoặc WebP.';
      elseif (!$loi) {
        $tenAnh=bin2hex(random_bytes(16)).'.'.$choPhep[$mime];
        if (!(is_dir(__DIR__.'/uploads') || mkdir(__DIR__.'/uploads', 0775, true)) || !move_uploaded_file((string)$tep['tmp_name'], __DIR__.'/uploads/'.$tenAnh)) { $loi['anh']='Không lưu được ảnh tải lên.'; $tenAnh=null; }
      }
    }
  }
  if (!$loi) {
    $kho=new KhoLienHe(db());
    $kho->them(['ho_ten'=>$du['hoten'],'email'=>$du['email'],'sdt'=>$du['sdt'],'noi_dung'=>$du['noidung'],'anh'=>$tenAnh]);
    flash('lien_he','Đã gửi liên hệ, cảm ơn bạn!'); chuyenHuong('lien-he.php');
  }
}
$tb=flash('lien_he'); $tieuDe='Liên hệ & Đặt hàng'; $trang='lien-he'; $lopTrang='trang--lien-he';
require __DIR__.'/inc/header.php';
?>
<main class="trang__chinh lien-he__luoi"><h1 class="lien-he__tieu-de">Liên hệ &amp; Gửi yêu cầu</h1>
<?php if($tb):?><p role="status" class="trang-thai"><?=e($tb)?></p><?php endif;?>
<form class="lien-he__bieu-mau" method="post" enctype="multipart/form-data" novalidate>
<fieldset><legend>Thông tin liên hệ</legend>
<p><label for="hoten">Họ và tên (*)</label><br><input id="hoten" name="hoten" value="<?=e($du['hoten'])?>" maxlength="80" required><?php if(isset($loi['hoten'])):?><span class="loi"><?=e($loi['hoten'])?></span><?php endif;?></p>
<p><label for="email">Email (*)</label><br><input id="email" type="email" name="email" value="<?=e($du['email'])?>" required><?php if(isset($loi['email'])):?><span class="loi"><?=e($loi['email'])?></span><?php endif;?></p>
<p><label for="sdt">Số điện thoại (*)</label><br><input id="sdt" name="sdt" value="<?=e($du['sdt'])?>" required><?php if(isset($loi['sdt'])):?><span class="loi"><?=e($loi['sdt'])?></span><?php endif;?></p>
<p><label for="noidung">Nội dung (*)</label><br><textarea id="noidung" name="noidung" rows="5" maxlength="1000" required><?=e($du['noidung'])?></textarea><?php if(isset($loi['noidung'])):?><span class="loi"><?=e($loi['noidung'])?></span><?php endif;?></p>
<p><label for="anh">Ảnh đính kèm (không bắt buộc, ≤ 2 MB)</label><br><input id="anh" name="anh" type="file" accept="image/jpeg,image/png,image/webp"><?php if(isset($loi['anh'])):?><span class="loi"><?=e($loi['anh'])?></span><?php endif;?></p>
</fieldset><p class="lien-he__nhom-nut"><button type="submit">Gửi liên hệ</button><button type="reset">Nhập lại</button></p></form>
<aside class="lien-he__thong-tin"><h2>Thông tin cửa hàng</h2><p>Foodspead - hệ thống đồ ăn nhanh trực tuyến</p><p>Hotline: 08888 214 88</p><p>Email: foodspead@example.com</p><p>Hỗ trợ: 08:00 - 22:00 mỗi ngày</p></aside></main>
<?php require __DIR__.'/inc/footer.php'; ?>
