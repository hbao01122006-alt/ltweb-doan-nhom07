<?php
// xoa-san-pham.php - quản trị: xóa món, CHỈ nhận POST (GET -> 405). Món đã có trong đơn -> lỗi 1451 được bắt.
require __DIR__ . '/inc/config.php';
use App\Data\KhoSanPham;

yeuCauQuyen('quan_tri');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method Not Allowed');
}
$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    flash('qt', 'Mã món không hợp lệ.');
    chuyenHuong('quan-tri.php');
}
try {
    $da = (new KhoSanPham(db()))->xoa((int) $id);
    flash('qt', $da ? 'Đã xóa món #' . $id . '.' : 'Không có món #' . $id . '.');
} catch (PDOException $e) {
    if ((int) ($e->errorInfo[1] ?? 0) !== 1451) throw $e;
    flash('qt', 'Không thể xóa: món đã có trong đơn hàng.');
}
chuyenHuong('quan-tri.php');
