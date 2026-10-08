<?php
// src/Data/KhoSanPham.php - noi DUY NHAT doc data/san-pham.json.
namespace App\Data;

use App\Models\SanPham;
use JsonException;
use RuntimeException;

final class KhoSanPham
{
    /** @var array<int,SanPham>|null */
    private ?array $ds = null;

    public function __construct(private readonly string $tepJson) {}

    /** @return array<int,SanPham> */
    public function tatCa(): array
    {
        if ($this->ds !== null) return $this->ds;
        if (!is_file($this->tepJson)) throw new RuntimeException('Không có tệp dữ liệu sản phẩm.');
        $json = file_get_contents($this->tepJson);
        if ($json === false) throw new RuntimeException('Không đọc được tệp dữ liệu sản phẩm.');
        try {
            $mang = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Dữ liệu sản phẩm không hợp lệ.', 0, $e);
        }
        if (!is_array($mang)) throw new RuntimeException('Dữ liệu sản phẩm phải là mảng JSON.');
        $this->ds = array_map(static fn(array $d): SanPham => SanPham::tuMang($d), $mang);
        return $this->ds;
    }

    public function timTheoId(int $id): ?SanPham
    {
        foreach ($this->tatCa() as $sp) if ($sp->id === $id) return $sp;
        return null;
    }

    /** @return array<int,SanPham> */
    public function timKiem(string $q, string $danhMuc, string $sapXep): array
    {
        $q = trim($q);
        $ketQua = array_values(array_filter($this->tatCa(), static function (SanPham $sp) use ($q, $danhMuc): bool {
            $hopTuKhoa = $q === '' || mb_stripos($sp->ten . ' ' . $sp->moTa, $q) !== false;
            $hopDanhMuc = $danhMuc === '' || $sp->danhMuc === $danhMuc;
            return $hopTuKhoa && $hopDanhMuc;
        }));
        usort($ketQua, static function (SanPham $a, SanPham $b) use ($sapXep): int {
            return match ($sapXep) {
                'gia-tang' => $a->gia <=> $b->gia,
                'gia-giam' => $b->gia <=> $a->gia,
                'ten-az' => strcasecmp($a->ten, $b->ten),
                default => $a->id <=> $b->id,
            };
        });
        return $ketQua;
    }

    /** @return array<int,string> */
    public function danhMuc(): array
    {
        $ds = array_values(array_unique(array_map(static fn(SanPham $sp): string => $sp->danhMuc, $this->tatCa())));
        sort($ds);
        return $ds;
    }
}
