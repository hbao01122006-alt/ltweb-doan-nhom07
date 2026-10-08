<?php
// src/Services/GioHang.php - boc du lieu gio hang trong SESSION va tinh tong tien.
namespace App\Services;

use App\Data\KhoSanPham;
use InvalidArgumentException;

final class GioHang
{
    /** @return array<int,int> */
    public function cacMuc(): array
    {
        $gio = $_SESSION['gio'] ?? [];
        return is_array($gio) ? array_map('intval', $gio) : [];
    }

    public function them(int $id, int $sl): void
    {
        if ($sl < 1 || $sl > 20) throw new InvalidArgumentException('Số lượng phải từ 1 đến 20.');
        $gio = $this->cacMuc();
        $gio[$id] = min(20, ($gio[$id] ?? 0) + $sl);
        $_SESSION['gio'] = $gio;
    }

    public function capNhat(int $id, int $sl): void
    {
        if ($sl < 1 || $sl > 20) throw new InvalidArgumentException('Số lượng phải từ 1 đến 20.');
        $gio = $this->cacMuc();
        if (isset($gio[$id])) $gio[$id] = $sl;
        $_SESSION['gio'] = $gio;
    }

    public function xoa(int $id): void
    {
        $gio = $this->cacMuc();
        unset($gio[$id]);
        $_SESSION['gio'] = $gio;
    }

    public function xoaHet(): void { $_SESSION['gio'] = []; }
    public function soMon(): int { return array_sum($this->cacMuc()); }

    public function tongTien(KhoSanPham $kho): int
    {
        $tong = 0;
        foreach ($this->cacMuc() as $id => $sl) {
            $sp = $kho->timTheoId((int)$id);
            if ($sp !== null) $tong += $sp->gia * $sl;
        }
        return $tong;
    }
}
