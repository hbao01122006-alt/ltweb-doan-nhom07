<?php
// inc/ham.php - cac ham tien ich dung chung cho website.
// e() ma hoa du lieu truoc khi in HTML; vnd() dinh dang gia tien.
function e(mixed $giaTri): string
{
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
