<?php
// src/Data/KhoLienHe.php - lớp truy cập dữ liệu của bảng lien_he (thay cho storage/lien-he.jsonl của Bài tập 5).
namespace App\Data;

use PDO;

final class KhoLienHe
{
    public function __construct(private readonly PDO $db) {}

    public function them(array $d): void
    {
        $st = $this->db->prepare(
            'INSERT INTO lien_he (ho_ten, email, sdt, noi_dung, anh)
             VALUES (:ho_ten, :email, :sdt, :noi_dung, :anh)'
        );
        $st->execute($d);
    }

    /** Liên hệ đã nhận, mới nhất trước. */
    public function moiNhat(int $soLuong = 20): array
    {
        $st = $this->db->prepare('SELECT * FROM lien_he ORDER BY ngay_tao DESC, id DESC LIMIT ?');
        $st->execute([$soLuong]);
        return $st->fetchAll();
    }
}
