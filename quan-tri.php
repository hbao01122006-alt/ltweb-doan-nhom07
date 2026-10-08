<?php
// quan-tri.php - trang bao ve, liet ke lien he moi nhat va anh dinh kem.
require __DIR__.'/inc/config.php'; require __DIR__.'/inc/bao-ve.php';
use App\Data\KhoLienHe;
$kho=new KhoLienHe(__DIR__.'/storage/lien-he.jsonl'); $ds=$kho->tatCa();
$tieuDe='Quản trị liên hệ'; $trang='quan-tri'; require __DIR__.'/inc/header.php';
?>
<main class="trang__chinh"><h1>Quản trị liên hệ</h1><?php if($tb=flash('dang_nhap')):?><p role="status"><?=e($tb)?></p><?php endif;?>
<?php if(!$ds):?><p>Chưa có liên hệ nào.</p><?php else:?><div class="bang-boc"><table><thead><tr><th>Thời gian</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Nội dung</th><th>Ảnh</th></tr></thead><tbody><?php foreach($ds as $lh):?><tr><td><?=e($lh['thoiGian']??'')?></td><td><?=e($lh['hoTen']??'')?></td><td><?=e($lh['email']??'')?></td><td><?=e($lh['sdt']??'')?></td><td><?=e($lh['noiDung']??'')?></td><td><?php if(!empty($lh['anh'])):?><a href="uploads/<?=e(basename((string)$lh['anh']))?>">Xem ảnh</a><?php else:?>—<?php endif;?></td></tr><?php endforeach;?></tbody></table></div><?php endif;?></main>
<?php require __DIR__.'/inc/footer.php'; ?>
