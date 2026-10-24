<?php
// src/Data/KhoThanhVien.php - bảng thanh_vien (Phần B): ai là chủ của trang cá nhân nào.
namespace App\Data;

use PDO;

final class KhoThanhVien
{
    public function __construct(private readonly PDO $db) {}

    /** true nếu tài khoản $nguoiDungId được gắn với trang của $mssv trong bảng thanh_vien. */
    public function laChuTrang(string $mssv, ?int $nguoiDungId): bool
    {
        if (!$nguoiDungId) return false;
        $st = $this->db->prepare('SELECT 1 FROM thanh_vien WHERE mssv = ? AND nguoi_dung_id = ?');
        $st->execute([$mssv, $nguoiDungId]);
        return (bool) $st->fetchColumn();
    }
}
