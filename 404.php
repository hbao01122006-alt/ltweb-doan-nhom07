<?php
// 404.php - trang loi than thien va tra dung ma HTTP 404.
if (!defined('THU_MUC_GOC')) require __DIR__.'/inc/config.php';
http_response_code(404); $tieuDe='Không tìm thấy trang'; $trang=''; require __DIR__.'/inc/header.php';
?>
<main class="trang__chinh"><h1>404 - Không tìm thấy</h1><p>Địa chỉ bạn yêu cầu không tồn tại hoặc món ăn không hợp lệ.</p><p><a href="index.php">Quay về trang chủ</a></p></main>
<?php require __DIR__.'/inc/footer.php'; ?>
