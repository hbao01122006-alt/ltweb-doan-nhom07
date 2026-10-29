<?php
require __DIR__ . '/inc/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['uid']);
    session_regenerate_id(true);
}

chuyenHuong('dang-nhap.php');