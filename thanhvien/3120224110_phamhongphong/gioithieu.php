<?php
// gioi-thieu.php - gioi thieu nhom va lien ket den trang PHP cua tung thanh vien.
require __DIR__.'/inc/config.php';
$tieuDe='Giới thiệu Nhóm 07'; $trang='gioi-thieu'; $lopTrang='trang--gioi-thieu'; require __DIR__.'/inc/header.php';
$thanhVien=[
 ['Hoàng Gia Bảo','3120224008','thanhvien/3120224008_hoanggiabao/gioithieu.php','thanhvien/3120224008_hoanggiabao/images/chan-dung-bao.jpg'],
 ['Nguyễn Hữu Tài','3120224126','thanhvien/3120224126_nguyenhuutai/gioithieu.php','thanhvien/3120224126_nguyenhuutai/images/chan-dung-tai.gif'],
 ['Trần Hồ Trung Hậu','3120224053','thanhvien/3120224053_tranhotrunghau/gioithieu.php','thanhvien/3120224053_tranhotrunghau/images/chan-dung-hau.jpg'],
 ['Lê Công Huy','3120224069','thanhvien/3120224069_leconghuy/gioithieu.php','thanhvien/3120224069_leconghuy/images/chan-dung-huy.jpg'],
 ['Phạm Hồng Phong','3120224110','thanhvien/3120224110_phamhongphong/gioithieu.php','thanhvien/3120224110_phamhongphong/images/chan-dung-phong.jpg'],
];
?>
<main class="trang__chinh"><h1>Giới thiệu Nhóm 07 - Đồ án Website Foodspead</h1>
<section class="gioi-thieu__khoi"><h2>Mục đích dự án</h2><img class="gioi-thieu__banner" src="images/banner-food.jpg" alt="Mô phỏng hệ thống Foodspead" width="600" height="320"><p class="gioi-thieu__doan">Dự án Foodspead mô phỏng hệ thống đặt đồ ăn nhanh trực tuyến hiện đại. Bài thực hành 5 chuyển website sang PHP động, có dữ liệu, biểu mẫu máy chủ, giỏ hàng, cookie, session và đăng nhập.</p></section>
<section class="gioi-thieu__khoi"><h2>Danh sách thành viên Nhóm 07</h2><ul class="danh-sach-thanh-vien"><?php foreach($thanhVien as [$ten,$mssv,$link,$anh]):?><li class="thanh-vien"><img src="<?=e($anh)?>" alt="Ảnh <?=e($ten)?>" width="100" height="100" loading="lazy"><strong><?=e($ten)?></strong><span>MSSV: <?=e($mssv)?></span><a href="<?=e($link)?>">Trang cá nhân PHP</a></li><?php endforeach;?></ul></section></main>
<?php require __DIR__.'/inc/footer.php'; ?>
