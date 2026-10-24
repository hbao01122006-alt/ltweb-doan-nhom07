<?php
// 403.php - trang "không có quyền"; trả đúng mã HTTP 403. Được yeuCauQuyen() nạp vào, mở thẳng cũng chạy.
if (!defined('THU_MUC_GOC')) require __DIR__ . '/inc/config.php';
http_response_code(403);
$tieuDe = '403 - Không có quyền';
$trang = '';
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
  <h1>403 - Không có quyền truy cập</h1>
  <p>Tài khoản hiện tại không được phép thực hiện thao tác này.</p>
  <p><a href="index.php">Về trang chủ</a></p>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
