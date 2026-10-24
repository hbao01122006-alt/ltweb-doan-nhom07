<?php
// src/Data/KhoNguoiDung.php - lớp truy cập dữ liệu của bảng nguoi_dung (mọi SQL về tài khoản nằm ở đây).
namespace App\Data;

use PDO;

final class KhoNguoiDung
{
    public function __construct(private readonly PDO $db) {}

    /** Thông tin công khai của một tài khoản (không có mật khẩu). */
    public function timTheoId(int $id): ?array
    {
        $st = $this->db->prepare('SELECT id, email, ho_ten, vai_tro FROM nguoi_dung WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /** Dùng khi đăng nhập: có cả chuỗi băm mật khẩu. */
    public function timTheoEmail(string $email): ?array
    {
        $st = $this->db->prepare('SELECT id, mat_khau FROM nguoi_dung WHERE email = ?');
        $st->execute([$email]);
        return $st->fetch() ?: null;
    }

    /** Tạo tài khoản khách hàng; email trùng ném PDOException (mã 1062) cho trang bắt. */
    public function taoKhachHang(string $email, string $hoTen, string $matKhauBam): int
    {
        $st = $this->db->prepare(
            "INSERT INTO nguoi_dung (email, ho_ten, mat_khau, vai_tro) VALUES (?, ?, ?, 'khach_hang')"
        );
        $st->execute([$email, $hoTen, $matKhauBam]);
        return (int) $this->db->lastInsertId();
    }

    public function capNhatMatKhau(int $id, string $matKhauBam): void
    {
        $this->db->prepare('UPDATE nguoi_dung SET mat_khau = ? WHERE id = ?')->execute([$matKhauBam, $id]);
    }
}
