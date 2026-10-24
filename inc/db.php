<?php
// inc/db.php - kết nối PDO MySQL/MariaDB dùng chung (hàm db(), slide 17).
// Tài khoản root, mật khẩu rỗng như XAMPP; CSDL ltweb_nhom07 tạo từ database/schema.sql.
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=ltweb_nhom07;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}
