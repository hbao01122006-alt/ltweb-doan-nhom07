<?php
// inc/footer.php - chân trang dùng chung: bản quyền và nạp js/main.js (menu, số đếm yêu thích).
// Mỗi trang có thể đặt $jsRien (đường dẫn script riêng) và $goc (tiền tố về thư mục gốc) trước khi nạp tệp này.
// Đóng các thẻ body và html mà inc/header.php đã mở.
?>
<footer class="trang__chan">
  <p>© 2026 Nhóm 07 — Khoa Toán - Tin, Trường Đại học Sư phạm - Đại học Đà Nẵng</p>
</footer>
<script src="<?= e($goc ?? '') ?>js/main.js" type="module"></script>
<?php if (!empty($jsRien)): ?><script src="<?= e($jsRien) ?>"></script><?php endif; ?>
</body>
</html>
