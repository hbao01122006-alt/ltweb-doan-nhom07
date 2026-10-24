<?php
// inc/xac-thuc.php - đọc tài khoản từ CSDL (session chỉ giữ uid) và kiểm tra đăng nhập / vai trò.
// Vai trò luôn đọc lại từ CSDL ở mỗi request nên đổi quyền có hiệu lực ngay.
declare(strict_types=1);

use App\Data\KhoNguoiDung;

function nguoiDung(): ?array
{
    static $daDoc = false, $nd = null;
    if ($daDoc) return $nd;
    $daDoc = true;
    $uid = filter_var($_SESSION['uid'] ?? null, FILTER_VALIDATE_INT);
    if (!$uid) return null;
    $nd = (new KhoNguoiDung(db()))->timTheoId((int) $uid);
    return $nd;
}

function yeuCauDangNhap(): array
{
    $nd = nguoiDung();
    if (!$nd) {
        $_SESSION['url_sau_dang_nhap'] = $_SERVER['REQUEST_URI'] ?? 'index.php';
        chuyenHuong('dang-nhap.php');
    }
    return $nd;
}

function yeuCauQuyen(string $vaiTro): array
{
    $nd = yeuCauDangNhap();
    if ($nd['vai_tro'] !== $vaiTro) {
        http_response_code(403);
        require THU_MUC_GOC . '/403.php';
        exit;
    }
    return $nd;
}
