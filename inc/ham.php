<?php
// inc/ham.php - cac ham tien ich dung chung cho website.
// e() ma hoa du lieu truoc khi in HTML; vnd() dinh dang gia tien.
function e(mixed $giaTri): string
{
    if (is_array($giaTri) || is_object($giaTri) && !method_exists($giaTri, '__toString')) return '';   // ten[]=x: không Warning
    return htmlspecialchars((string)$giaTri, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function vnd(int $gia): string
{
    return number_format($gia, 0, ',', '.') . ' VNĐ';
}

function chuyenHuong(string $url): never
{
    header('Location: ' . $url, true, 302);
    exit;
}

function flash(string $khoa, ?string $giaTri = null): ?string
{
    if ($giaTri !== null) {
        $_SESSION['flash'][$khoa] = $giaTri;
        return null;
    }
    $thongBao = $_SESSION['flash'][$khoa] ?? null;
    unset($_SESSION['flash'][$khoa]);
    return is_string($thongBao) ? $thongBao : null;
}

// Fallback cho moi truong PHP khong bat extension mbstring; tren XAMPP/Laragon co mbstring thi dung ham goc.
if (!function_exists('mb_strlen')) {
    function mb_strlen(string $chuoi, ?string $encoding = null): int { return strlen($chuoi); }
}
if (!function_exists('mb_substr')) {
    function mb_substr(string $chuoi, int $start, ?int $length = null, ?string $encoding = null): string { return $length === null ? substr($chuoi, $start) : substr($chuoi, $start, $length); }
}
if (!function_exists('mb_stripos')) {
    function mb_stripos(string $haystack, string $needle, int $offset = 0, ?string $encoding = null): int|false { return stripos($haystack, $needle, $offset); }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower(string $chuoi, ?string $encoding = null): string { return strtolower($chuoi); }
}

// Gia tri tham so mang/doi tuong bi loai bo; khong ep kieu thanh "Array".
function chuoi(mixed $v): string
{
    return is_string($v) ? $v : '';
}

// Chi ghi ten su kien, IP, uid va cac thong tin can thiet; TUYET DOI khong ghi bi mat.
function ghiLogBaoMat(string $suKien, array $chiTiet = []): void
{
    $hopLe = ['csrf_sai', 'cam_truy_cap', 'dang_nhap_sai'];
    if (!in_array($suKien, $hopLe, true)) return;
    $chiTiet = array_intersect_key($chiTiet, array_flip(['trang', 'email']));
    $chiTiet['ip'] = $_SERVER['REMOTE_ADDR'] ?? '-';
    $chiTiet['uid'] = $_SESSION['uid'] ?? null;
    $dong = date('Y-m-d H:i:s') . ' ' . $suKien . ' '
          . json_encode($chiTiet, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    error_log($dong, 3, THU_MUC_GOC . '/logs/bao-mat.log');
}
