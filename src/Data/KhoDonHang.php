<?php
// src/Data/KhoDonHang.php - lớp truy cập dữ liệu của don_hang và chi_tiet_don.
// Giao dịch (beginTransaction / commit) do inc/don-hang.php điều phối vì còn phải trừ tồn kho ở KhoSanPham.
namespace App\Data;

use PDO;

final class KhoDonHang
{
    /** Trạng thái hợp lệ (trùng ENUM trong schema.sql); trang quản trị chỉ nhận giá trị trong danh sách này. */
    public const TRANG_THAI = ['moi', 'xac_nhan', 'dang_giao', 'hoan_tat', 'huy'];

    public function __construct(private readonly PDO $db) {}

    public function tao(int $nguoiDungId): int
    {
        $st = $this->db->prepare("INSERT INTO don_hang (nguoi_dung_id, trang_thai) VALUES (?, 'moi')");
        $st->execute([$nguoiDungId]);
        return (int) $this->db->lastInsertId();
    }

    /** Lưu cả đơn giá lúc đặt: giá có thể đổi sau này nhưng đơn cũ không được đổi theo. */
    public function themChiTiet(int $donId, int $sanPhamId, int $soLuong, int $donGia): void
    {
        $st = $this->db->prepare(
            'INSERT INTO chi_tiet_don (don_hang_id, san_pham_id, so_luong, don_gia) VALUES (?, ?, ?, ?)'
        );
        $st->execute([$donId, $sanPhamId, $soLuong, $donGia]);
    }

    /**
     * Danh sách đơn kèm tổng tiền (JOIN + GROUP BY + SUM).
     * $nguoiDungId = null: quản trị viên xem mọi đơn; có giá trị: chỉ đơn của người đó.
     */
    public function danhSach(?int $nguoiDungId): array
    {
        $sql = 'SELECT d.id, d.ngay_tao, d.trang_thai, n.ho_ten, SUM(c.so_luong * c.don_gia) AS tong
                FROM don_hang d
                JOIN chi_tiet_don c ON c.don_hang_id = d.id
                JOIN nguoi_dung n ON n.id = d.nguoi_dung_id'
             . ($nguoiDungId === null ? '' : ' WHERE d.nguoi_dung_id = ?')
             . ' GROUP BY d.id, d.ngay_tao, d.trang_thai, n.ho_ten ORDER BY d.id DESC';
        $st = $this->db->prepare($sql);
        $st->execute($nguoiDungId === null ? [] : [$nguoiDungId]);
        return $st->fetchAll();
    }

    /** Một đơn; khách hàng chỉ xem được đơn của mình (AND nguoi_dung_id = ?), đơn người khác coi như không có. */
    public function timTheoId(int $id, ?int $nguoiDungId): ?array
    {
        $sql = 'SELECT d.id, d.ngay_tao, d.trang_thai, n.ho_ten, n.email
                FROM don_hang d JOIN nguoi_dung n ON n.id = d.nguoi_dung_id
                WHERE d.id = ?' . ($nguoiDungId === null ? '' : ' AND d.nguoi_dung_id = ?');
        $st = $this->db->prepare($sql);
        $st->execute($nguoiDungId === null ? [$id] : [$id, $nguoiDungId]);
        return $st->fetch() ?: null;
    }

    public function chiTiet(int $donId): array
    {
        $st = $this->db->prepare(
            'SELECT c.san_pham_id, s.ten, c.so_luong, c.don_gia
             FROM chi_tiet_don c JOIN san_pham s ON s.id = c.san_pham_id
             WHERE c.don_hang_id = ? ORDER BY s.ten'
        );
        $st->execute([$donId]);
        return $st->fetchAll();
    }

    public function doiTrangThai(int $id, string $trangThai): bool
    {
        if (!in_array($trangThai, self::TRANG_THAI, true)) return false;
        $st = $this->db->prepare('UPDATE don_hang SET trang_thai = ? WHERE id = ?');
        $st->execute([$trangThai, $id]);
        return true;
    }
}
