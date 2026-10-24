<?php
// inc/don-hang.php - luuDonHang(): đặt hàng trong MỘT giao dịch (tất cả hoặc không gì cả).
// Thử: giảm ton_kho của một món trong phpMyAdmin rồi đặt -> báo thiếu hàng, không có đơn, tồn kho món kia không đổi.
use App\Data\KhoDonHang;
use App\Data\KhoSanPham;
use App\Services\GioHang;

function luuDonHang(int $uid, GioHang $gio): int
{
    $db = db();
    $mon = $gio->cacMuc();
    if (!$mon) throw new RuntimeException('Giỏ hàng trống.');

    $khoSp = new KhoSanPham($db);
    $khoDon = new KhoDonHang($db);
    $sp = $khoSp->theoIds(array_keys($mon));          // một câu IN (...), không truy vấn trong vòng lặp

    $db->beginTransaction();
    try {
        $donId = $khoDon->tao($uid);
        foreach ($mon as $id => $sl) {
            if (!isset($sp[$id])) throw new RuntimeException('Có món không còn tồn tại.');
            if (!$khoSp->giamTon($id, $sl)) throw new RuntimeException('Không đủ hàng cho "' . $sp[$id]->ten . '".');
            $khoDon->themChiTiet($donId, $id, $sl, $sp[$id]->gia);
        }
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
    $gio->xoaHet();
    return $donId;
}
