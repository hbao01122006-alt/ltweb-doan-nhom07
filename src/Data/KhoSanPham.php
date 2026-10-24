<?php
namespace App\Data;
use App\Models\SanPham;
use PDO;

final class KhoSanPham {
    public function __construct(private readonly PDO $db) {}

    private function dieuKien(array $loc): array {
        $dk=[]; $ts=[];
        $q = trim((string)($loc['q'] ?? ''));
        if ($q !== '') {
            // utf8mb4_unicode_ci: không phân biệt hoa/thường và dấu; addcslashes để % _ \ trong từ khóa chỉ là ký tự thường
            // Riêng đ/Đ: utf8mb4_unicode_ci coi là chữ cái khác d nên gõ "do uong" không ra "Đồ uống";
            // đổi đ/Đ thành d ở cả tên món lẫn từ khóa để tìm không dấu vẫn khớp.
            $dk[] = "REPLACE(REPLACE(s.ten, 'đ', 'd'), 'Đ', 'D') LIKE ? ESCAPE '\\\\'";
            $ts[] = '%' . addcslashes(str_replace(['đ', 'Đ'], ['d', 'D'], $q), '%_\\') . '%';
        }
        if (!empty($loc['dm'])) { $dk[]='s.danh_muc_id = ?'; $ts[]=(int)$loc['dm']; }
        if (!empty($loc['giaToiDa'])) { $dk[]='s.gia <= ?'; $ts[]=(int)$loc['giaToiDa']; }
        return [$dk ? 'WHERE '.implode(' AND ', $dk) : '', $ts];
    }

    public function dem(array $loc=[]): int {
        [$w,$ts]=$this->dieuKien($loc);
        $st=$this->db->prepare("SELECT COUNT(*) FROM san_pham s $w");
        $st->execute($ts);
        return (int)$st->fetchColumn();
    }

    public function trang(array $loc,string $sx,int $limit,int $offset): array {
        [$w,$ts]=$this->dieuKien($loc);
        $order=match($sx){
            'gia-tang'=>'s.gia ASC, s.id ASC',
            'gia-giam'=>'s.gia DESC, s.id ASC',
            'ten'=>'s.ten ASC, s.id ASC',
            default=>'s.id ASC'
        };
        $st=$this->db->prepare("SELECT s.*,d.ten danh_muc FROM san_pham s JOIN danh_muc d ON d.id=s.danh_muc_id $w ORDER BY $order LIMIT ? OFFSET ?");
        $st->execute([...$ts,$limit,$offset]);
        return array_map(fn($r)=>SanPham::tuMang($r),$st->fetchAll());
    }

    public function tatCa(): array { return $this->trang([], 'mac-dinh', 500, 0); }

    public function timTheoId(int $id): ?SanPham {
        $st=$this->db->prepare('SELECT s.*,d.ten danh_muc FROM san_pham s JOIN danh_muc d ON d.id=s.danh_muc_id WHERE s.id=?');
        $st->execute([$id]);
        $r=$st->fetch();
        return $r ? SanPham::tuMang($r) : null;
    }

    public function theoIds(array $ids): array {
        $ids=array_values(array_unique(array_filter(array_map('intval',$ids),fn($x)=>$x>0)));
        if(!$ids) return [];
        $dau=implode(',',array_fill(0,count($ids),'?'));
        $st=$this->db->prepare("SELECT s.*,d.ten danh_muc FROM san_pham s JOIN danh_muc d ON d.id=s.danh_muc_id WHERE s.id IN ($dau)");
        $st->execute($ids);
        $map=[];
        foreach($st->fetchAll() as $r){$sp=SanPham::tuMang($r);$map[$sp->id]=$sp;}
        return $map;
    }

    public function noiBat(int $limit=4): array {
        $st=$this->db->prepare('SELECT s.*,d.ten danh_muc FROM san_pham s JOIN danh_muc d ON d.id=s.danh_muc_id WHERE s.noi_bat=1 ORDER BY s.id ASC LIMIT ?');
        $st->execute([$limit]);
        return array_map(fn($r)=>SanPham::tuMang($r),$st->fetchAll());
    }

    public function cungDanhMuc(int $dm,int $tru,int $limit=4): array {
        $st=$this->db->prepare('SELECT s.*,d.ten danh_muc FROM san_pham s JOIN danh_muc d ON d.id=s.danh_muc_id WHERE s.danh_muc_id=? AND s.id<>? ORDER BY s.id ASC LIMIT ?');
        $st->execute([$dm,$tru,$limit]);
        return array_map(fn($r)=>SanPham::tuMang($r),$st->fetchAll());
    }

    public function danhMuc(): array {
        return $this->db->query('SELECT id,ten FROM danh_muc ORDER BY id')->fetchAll();
    }

    // ---------- Quản trị: thêm, sửa, xóa, trừ tồn kho ----------

    /** Số dòng của bảng, dùng phân trang trang quản trị. */
    public function demTatCa(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM san_pham')->fetchColumn();
    }

    /** Một trang danh sách cho quản trị, mới nhất trước. */
    public function trangQuanTri(int $soDong, int $boQua): array
    {
        $st = $this->db->prepare(
            'SELECT s.id, s.ten, s.gia, s.ton_kho, d.ten AS danh_muc
             FROM san_pham s JOIN danh_muc d ON d.id = s.danh_muc_id
             ORDER BY s.id DESC LIMIT ? OFFSET ?'
        );
        $st->execute([$soDong, $boQua]);
        return $st->fetchAll();
    }

    /** Dòng thô của bảng (để điền sẵn form sửa). */
    public function dongTho(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM san_pham WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /** @return int[] các id danh mục hiện có, dùng kiểm tra danh_muc_id gửi lên. */
    public function idDanhMuc(): array
    {
        return array_map('intval', $this->db->query('SELECT id FROM danh_muc')->fetchAll(PDO::FETCH_COLUMN));
    }

    /** $du: ten, gia, ton_kho, danh_muc_id, mo_ta (đã kiểm tra); $anh: đường dẫn ảnh. */
    public function them(array $du, string $anh): int
    {
        $st = $this->db->prepare(
            "INSERT INTO san_pham (danh_muc_id, ten, gia, ton_kho, mo_ta, anh, anh_mo_ta, thanh_phan, khau_phan)
             VALUES (:danh_muc_id, :ten, :gia, :ton_kho, :mo_ta, :anh, :ten_anh, 'Đang cập nhật', '1 người ăn')"
        );
        $st->execute($du + ['anh' => $anh, 'ten_anh' => $du['ten']]);
        return (int) $this->db->lastInsertId();
    }

    /** $anh = null: giữ ảnh cũ. */
    public function capNhat(int $id, array $du, ?string $anh): void
    {
        $sql = 'UPDATE san_pham SET ten = :ten, gia = :gia, ton_kho = :ton_kho,
                danh_muc_id = :danh_muc_id, mo_ta = :mo_ta' . ($anh === null ? '' : ', anh = :anh') . ' WHERE id = :id';
        $tham = $du + ['id' => $id];
        if ($anh !== null) $tham['anh'] = $anh;
        $this->db->prepare($sql)->execute($tham);
    }

    /** Ném PDOException mã 1451 nếu món đã nằm trong đơn hàng. */
    public function xoa(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM san_pham WHERE id = ?');
        $st->execute([$id]);
        return $st->rowCount() === 1;
    }

    /** Trừ tồn kho nếu còn đủ; false = không đủ (điều kiện ton_kho >= ? nằm ngay trong câu UPDATE). */
    public function giamTon(int $id, int $soLuong): bool
    {
        $st = $this->db->prepare('UPDATE san_pham SET ton_kho = ton_kho - ? WHERE id = ? AND ton_kho >= ?');
        $st->execute([$soLuong, $id, $soLuong]);
        return $st->rowCount() === 1;
    }
}
