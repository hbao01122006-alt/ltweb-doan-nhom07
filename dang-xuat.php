<?php
// dang-xuat.php - đăng xuất; chỉ nhận POST (nút trong header). GET chỉ chuyển về trang chủ, không đổi gì.
require __DIR__ . '/inc/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['uid']);
    session_regenerate_id(true);
}
chuyenHuong('index.php');
