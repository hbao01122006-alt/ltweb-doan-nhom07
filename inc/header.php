<?php
// inc/header.php - khung dau trang va menu dung chung cho moi trang.
$goc ??= '';
$trang ??= '';
$tieuDe ??= 'Foodspead';
$gio = new App\Services\GioHang();
$menu = [
  'index' => ['Trang chủ', 'index.php'],
  'danh-sach' => ['Thực đơn', 'danh-sach.php'],
  'gio-hang' => ['Giỏ hàng (' . $gio->soMon() . ')', 'gio-hang.php'],
  'gioi-thieu' => ['Giới thiệu', 'gioi-thieu.php'],
  'lien-he' => ['Liên hệ đặt hàng', 'lien-he.php'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Foodspead - website đặt đồ ăn nhanh của Nhóm 07">
  <title><?= e($tieuDe) ?> | Foodspead</title>
  <script>document.documentElement.classList.add('js');</script>
  <link rel="stylesheet" href="<?= e($goc) ?>css/style.css">
  <?php if (!empty($cssRien)): ?><link rel="stylesheet" href="<?= e($cssRien) ?>"><?php endif; ?>
</head>
<body class="trang <?= e($lopTrang ?? '') ?>">
<header class="trang__dau">
  <p><strong>Foodspead - Ngon Mỗi Ngày, Giao Nhanh Tận Nơi</strong></p>
  <p>Hệ thống đặt đồ ăn nhanh trực tuyến - Nhóm 07</p>
  <p class="trang__yeu-thich" style="display:block">♥ Yêu thích (<span data-dem-yeu-thich>0</span>)</p>
  <p>
    <?php if (isset($_SESSION['user']) && is_string($_SESSION['user'])): ?>
      Xin chào, <strong><?= e($_SESSION['user']) ?></strong> · <a href="<?= e($goc) ?>dang-xuat.php" style="color:#fff">Đăng xuất</a>
    <?php else: ?>
      <a href="<?= e($goc) ?>dang-nhap.php" style="color:#fff">Đăng nhập quản trị</a>
    <?php endif; ?>
  </p>
</header>
<nav aria-label="Điều hướng chính" class="trang__menu">
  <button aria-controls="menu-chinh" aria-expanded="false" class="nut-menu" type="button">☰ Menu</button>
  <ul class="menu" id="menu-chinh">
    <?php foreach ($menu as $ma => [$nhan, $href]): ?>
      <li><a href="<?= e($goc . $href) ?>"<?= $ma === $trang ? ' aria-current="page"' : '' ?>><?= e($nhan) ?></a></li>
    <?php endforeach; ?>
    <?php if (isset($_SESSION['user'])): ?><li><a href="<?= e($goc) ?>quan-tri.php"<?= $trang === 'quan-tri' ? ' aria-current="page"' : '' ?>>Quản trị</a></li><?php endif; ?>
  </ul>
</nav>
