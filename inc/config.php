<?php
// inc/config.php - cau hinh chung, bat session, ghi log va xu ly ngoai le.
// MOI_TRUONG de 'dev' khi nop bai; doi 'prod' de thu trang 500 than thien.
declare(strict_types=1);

const MOI_TRUONG = 'dev';
const THU_MUC_GOC = __DIR__ . '/..';

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', THU_MUC_GOC . '/logs/php-error.log');
ini_set('display_errors', MOI_TRUONG === 'dev' ? '1' : '0');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$autoload = THU_MUC_GOC . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
} else {
    // Fallback chi de source chay truoc khi composer install; Composer van la autoload chinh khi cham bai.
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) return;
        $relative = substr($class, strlen($prefix));
        $file = THU_MUC_GOC . '/src/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) require $file;
    });
}
require_once __DIR__ . '/ham.php';

set_exception_handler(static function (Throwable $loi): void {
    error_log((string)$loi);
    if (MOI_TRUONG === 'prod') {
        http_response_code(500);
        require THU_MUC_GOC . '/500.php';
        return;
    }
    throw $loi;
});
