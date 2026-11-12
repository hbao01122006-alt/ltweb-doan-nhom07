<?php
// Cau hinh dung chung; require dau tien o moi trang (bao gom API va trang ca nhan).
declare(strict_types=1);

const THU_MUC_GOC = __DIR__ . '/..';

// Cai dat ghi loi va bo xu ly ngoai le TRUOC khi nap .env: neu .env sai/thieu
// thong tin ket noi, che do production van phai tra 500 an toan.
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', THU_MUC_GOC . '/logs/php-error.log');
ini_set('display_errors', '0');
set_exception_handler(static function (Throwable $e): void {
    error_log((string) $e);
    http_response_code(500);
    $duong = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if (str_contains($duong, '/api/')) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['loi - config.php:19' => 'Lỗi máy chủ'], JSON_UNESCAPED_UNICODE);
    } elseif (defined('DEBUG') && DEBUG) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<pre> - config.php:22' . htmlspecialchars((string) $e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
    } else {
        require THU_MUC_GOC . '/500.php';
    }
});

$autoload = THU_MUC_GOC . '/vendor/autoload.php';
if (!is_file($autoload)) {
    http_response_code(500);
    exit('Chua cai Composer. Hay chay composer install theo README.md.');
}
require_once $autoload;

$dotenv = Dotenv\Dotenv::createImmutable(THU_MUC_GOC);
$dotenv->load();
// DB_PASSWORD phai duoc khai bao, nhung duoc phep rong tren XAMPP (root khong mat khau).
$dotenv->required(['DB_HOST', 'DB_DATABASE', 'DB_USERNAME']);
if (!array_key_exists('DB_PASSWORD', $_ENV)) {
    throw new RuntimeException('Thieu bien DB_PASSWORD trong .env (cho phep gia tri rong).');
}
define('DEBUG', ($_ENV['APP_DEBUG'] ?? '') === 'true');

ini_set('display_errors', DEBUG ? '1' : '0');
ini_set('expose_php', '0');

require_once __DIR__ . '/ham.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/xac-thuc.php';
require_once __DIR__ . '/csrf.php';

// Chi cho phep tai nguyen cua du an va Open-Meteo dang duoc su dung.
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; "
    . "img-src 'self' data:; font-src 'self'; "
    . "connect-src 'self' https://api.open-meteo.com; "
    . "object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header_remove('X-Powered-By');
$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
if ($isHttps) header('Strict-Transport-Security: max-age=31536000');

// Thiet lap phien truoc session_start().
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (time() - ($_SESSION['hoat_dong'] ?? time()) > 30 * 60) {
    $_SESSION = [];
    session_regenerate_id(true);
}
$_SESSION['hoat_dong'] = time();

// Kiem tra CSRF cho moi yeu cau POST, ke ca API JSON.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !csrfHopLe()) {
    ghiLogBaoMat('csrf_sai', ['trang' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/']);
    $loi = 'Trang da het han, hay tai lai trang roi gui lai.';
    http_response_code(403);
    if (str_contains(strtolower($_SERVER['CONTENT_TYPE'] ?? ''), 'json')) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['loi - config.php:82' => $loi], JSON_UNESCAPED_UNICODE);
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo $loi;
    }
    exit;
}

// adv 