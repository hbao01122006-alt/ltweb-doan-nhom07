
<?php
// ======================================
// 1. KIEM TRA DUONG DAN YEU CAU
// ======================================

$uriPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
) ?: '/';

$path = str_replace('\\', '/', rawurldecode($uriPath));
$lowerPath = strtolower($path);

// Chan duong dan khong an toan va ma hoa lap
if (
    str_contains($path, "\0") ||
    preg_match('~%[0-9a-f]{2}~i', $path)
) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Bad Request');
}

// ======================================
// 2. CHAN FILE VA THU MUC NOI BO
// ======================================

// Chan file an, vi du .env, .gitignore
$hiddenFile = preg_match(
    '~(^|/)\.[^/]*~',
    $lowerPath
);

// Chan thu muc khong duoc cong khai
$privateDir = preg_match(
    '~(^|/)(vendor|database|inc|src|storage|tests|logs|kiemtra|dev)(/|$)~',
    $lowerPath
);

// Chan file cau hinh va file nhay cam
$privateFile = preg_match(
    '~(^|/)(composer\.json|composer\.lock|router\.php|api\.http|phpunit\.xml)(/|$)~',
    $lowerPath
);

// Chan file sao luu, SQL, cau hinh
$privateExtension = preg_match(
    '~\.(sql|log|ini|bak|config)$~',
    $lowerPath
);

if (
    $hiddenFile ||
    $privateDir ||
    $privateFile ||
    $privateExtension
) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') {
        echo '403 Forbidden';
    }
    exit;
}

// ======================================
// 3. GIU NGUYEN XU LY CSS VA JAVASCRIPT
// ======================================

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

if (in_array($ext, ['css', 'js'], true)) {
    $root = realpath(__DIR__);
    $relative = ltrim($path, '/');
    $file = realpath(__DIR__ . DIRECTORY_SEPARATOR . $relative);

    if (
        $root === false ||
        $file === false ||
        !is_file($file) ||
        !str_starts_with($file, $root . DIRECTORY_SEPARATOR)
    ) {
        http_response_code(404);
        exit;
    }

    header('X-Content-Type-Options: nosniff');

    header('Content-Type: ' . (
        $ext === 'css'
            ? 'text/css; charset=UTF-8'
            : 'text/javascript; charset=UTF-8'
    ));

    header('Content-Length: ' . filesize($file));

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') {
        readfile($file);
    }

    exit;
}

// ======================================
// 4. CHO PHP XU LY CAC TRANG CON LAI
// ======================================

return false;
