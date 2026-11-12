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
        ghiLogBaoMat('cam_truy_cap', ['trang' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/']);
        http_response_code(403);
        require THU_MUC_GOC . '/403.php';
        exit;
    }
    return $nd;
}


// 5 lan sai trong vong 15 phut: khoa tam email do.
function soLanSai(string $email): int
{
    $st = db()->prepare('SELECT COUNT(*) FROM dang_nhap_sai WHERE email = ? AND thoi_gian > NOW() - INTERVAL 15 MINUTE');
    $st->execute([$email]);
    return (int) $st->fetchColumn();
}

function ghiLanSai(string $email): void
{
    db()->prepare('INSERT INTO dang_nhap_sai (email, ip) VALUES (?, ?)')
        ->execute([$email, $_SERVER['REMOTE_ADDR'] ?? '-']);
    ghiLogBaoMat('dang_nhap_sai', ['email' => $email]);
}

function dangXuat(): void
{
    $_SESSION = [];
    $c = session_get_cookie_params();
    if (ini_get('session.use_cookies')) {
        setcookie(session_name(), '', [
            'expires' => time() - 3600, 'path' => $c['path'],
            'domain' => $c['domain'], 'secure' => $c['secure'],
            'httponly' => $c['httponly'], 'samesite' => $c['samesite'],
        ]);
    }
    session_destroy();
}
// adv