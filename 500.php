<?php
// 500.php - trang loi may chu, khong lam lo chi tiet ky thuat trong moi truong prod.
if (!defined('THU_MUC_GOC')) require __DIR__.'/inc/config.php';
http_response_code(500); $tieuDe='Lỗi máy chủ'; $trang=''; require __DIR__.'/inc/header.php';
?>
<main class="trang__chinh"><h1>500 - Có lỗi xảy ra</h1><p>Hệ thống đang gặp sự cố. Vui lòng thử lại sau.</p><p><a href="index.php">Quay về trang chủ</a></p></main>
<?php require __DIR__.'/inc/footer.php'; ?>
