<?php
namespace App\Models;
use JsonSerializable;
final class SanPham implements JsonSerializable {
    public function __construct(
        public readonly int $id, public readonly int $danhMucId, public readonly string $ten,
        public readonly string $danhMuc, public readonly string $moTa, public readonly int $gia,
        public readonly int $soLuongTon, public readonly string $anh, public readonly string $anhMoTa,
        public readonly string $thanhPhan, public readonly string $khauPhan, public readonly int $nangLuongKcal,
        public readonly int $thoiGianPhut, public readonly bool $noiBat
    ) {}
    public static function tuMang(array $d): self { return new self(
        (int)($d['id']??0),(int)($d['danh_muc_id']??0),trim((string)($d['ten']??'')),trim((string)($d['danh_muc']??$d['danh_muc_ten']??'')),
        trim((string)($d['mo_ta']??'')),(int)($d['gia']??0),(int)($d['ton_kho']??$d['so_luong_ton']??0),trim((string)($d['anh']??'')),
        trim((string)($d['anh_mo_ta']??'')),trim((string)($d['thanh_phan']??'')),trim((string)($d['khau_phan']??'')),
        (int)($d['nang_luong_kcal']??0),(int)($d['thoi_gian_phut']??0),(bool)($d['noi_bat']??false)); }
    public function jsonSerialize(): array { return ['id'=>$this->id,'ten'=>$this->ten,'danh_muc'=>$this->danhMuc,'gia'=>$this->gia,'ton_kho'=>$this->soLuongTon,'anh'=>$this->anh,'mo_ta'=>$this->moTa]; }
}
