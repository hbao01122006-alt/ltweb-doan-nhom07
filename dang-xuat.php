<?php
// dang-xuat.php - huy thong tin dang nhap trong session va quay ve trang dang nhap.
require __DIR__ . '/inc/config.php';
unset($_SESSION['user']);
session_regenerate_id(true);
flash('dang_nhap','Bạn đã đăng xuất.');
chuyenHuong('dang-nhap.php');
