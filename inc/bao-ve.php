<?php
// inc/bao-ve.php - bao ve trang chi danh cho nguoi da dang nhap.
if (!isset($_SESSION['user']) || !is_string($_SESSION['user'])) {
    $_SESSION['flash']['dang_nhap'] = 'Vui lòng đăng nhập để truy cập trang quản trị.';
    chuyenHuong('dang-nhap.php');
}
