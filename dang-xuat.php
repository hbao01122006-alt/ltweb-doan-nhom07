<?php
require __DIR__ . '/inc/config.php';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method Not Allowed');
}
dangXuat();
chuyenHuong('dang-nhap.php');
