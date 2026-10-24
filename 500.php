<?php
// 500.php - trang lỗi máy chủ. CỐ Ý không nạp config/header và không truy vấn CSDL:
// khi chính CSDL hỏng mà trang lỗi cũng cần CSDL thì người dùng chỉ thấy một trang trắng.
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lỗi máy chủ | Foodspead</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="trang">
<main class="trang__chinh">
  <h1>500 - Có lỗi xảy ra</h1>
  <p>Hệ thống đang gặp sự cố. Vui lòng thử lại sau.</p>
  <p><a href="index.php">Quay về trang chủ</a></p>
</main>
</body>
</html>
