<?php
// gio-hang.php - Quan ly gio hang Foodspead bang Session, du lieu san pham tu MySQL.
require __DIR__ . '/inc/config.php';
use App\Data\KhoSanPham;
use App\Services\GioHang;

$kho = new KhoSanPham(db());
$gio = new GioHang();
$loi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hanhDong = (string)($_POST['hanh_dong'] ?? '');
    try {
        if (in_array($hanhDong, ['xoa_tat_ca', 'xoa-het'], true)) {
            $gio->xoaHet();
            flash('gio', 'Đã xóa toàn bộ giỏ hàng.');
            chuyenHuong('gio-hang.php');
            exit;
        }
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        $spChon = ($id === false || $id === null) ? null : $kho->timTheoId((int)$id);
        if ($spChon === null) {
            throw new InvalidArgumentException('Món ăn không hợp lệ.');
        }
        if ($hanhDong === 'xoa') {
            $gio->xoa((int)$id);
            flash('gio', 'Đã xóa món khỏi giỏ hàng.');
            chuyenHuong('gio-hang.php');
            exit;
        }
        $soLuong = filter_var($_POST['so_luong'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>20]]);
        if ($soLuong === false || $soLuong === null) throw new InvalidArgumentException('Số lượng phải từ 1 đến 20.');
        if (in_array($hanhDong, ['them', 'dat_ngay'], true)) {
            $hienTai = (int)($gio->cacMuc()[(int)$id] ?? 0);
            if ($hienTai + $soLuong > 20) throw new InvalidArgumentException('Tổng số lượng của món không được vượt quá 20.');
            if ($hienTai + $soLuong > $spChon->soLuongTon) throw new InvalidArgumentException('Không đủ hàng: món này chỉ còn ' . $spChon->soLuongTon . ' suất.');
            $gio->them((int)$id, (int)$soLuong);
            if ($hanhDong === 'dat_ngay') {
                chuyenHuong('dat-hang.php');
                exit;
            }
            flash('gio', 'Đã thêm món vào giỏ hàng.');
        } elseif (in_array($hanhDong, ['cap_nhat','cap-nhat'], true)) {
            $gio->capNhat((int)$id, (int)$soLuong);
            flash('gio', 'Đã cập nhật số lượng món ăn.');
        } else {
            throw new InvalidArgumentException('Thao tác giỏ hàng không hợp lệ.');
        }
        chuyenHuong('gio-hang.php');
        exit;
    } catch (InvalidArgumentException $e) {
        $loi = $e->getMessage();
    }
}

$thongBao = flash('gio');
$tieuDe = 'Giỏ hàng'; $trang = 'gio-hang';
$cacMuc = $gio->cacMuc();
$map = $kho->theoIds(array_keys($cacMuc));
$tongTien = 0;
foreach ($cacMuc as $idMon=>$sl) if (isset($map[$idMon])) $tongTien += $map[$idMon]->gia * $sl;
require __DIR__ . '/inc/header.php';
?>
<main class="trang__chinh">
<h1>Giỏ hàng của bạn</h1>
<?php if ($thongBao): ?><p class="trang-thai" role="status"><?= e($thongBao) ?></p><?php endif; ?>
<?php if ($loi): ?><p class="loi" role="alert"><?= e($loi) ?></p><?php endif; ?>
<?php if (empty($cacMuc)): ?>
<p>Giỏ hàng hiện đang trống. <a href="danh-sach.php">Tiếp tục chọn món</a></p>
<?php else: ?>
<div class="bang-boc"><table><caption>Các món trong giỏ hàng</caption><thead><tr><th scope="col">Món ăn</th><th scope="col">Đơn giá</th><th scope="col">Số lượng</th><th scope="col">Thành tiền</th><th scope="col">Thao tác</th></tr></thead><tbody>
<?php foreach ($cacMuc as $idMon=>$sl): if (!isset($map[$idMon])) continue; $sp=$map[$idMon]; ?>
<tr><td><a href="chi-tiet.php?id=<?= (int)$idMon ?>"><?= e($sp->ten) ?></a></td><td><?= e(vnd($sp->gia)) ?></td><td><form action="gio-hang.php" method="post"><input type="hidden" name="hanh_dong" value="cap_nhat"><input type="hidden" name="id" value="<?= (int)$idMon ?>"><label>Số lượng <input type="number" name="so_luong" min="1" max="20" value="<?= (int)$sl ?>" required></label><button type="submit">Cập nhật</button></form></td><td><?= e(vnd($sp->gia*$sl)) ?></td><td><form action="gio-hang.php" method="post"><input type="hidden" name="hanh_dong" value="xoa"><input type="hidden" name="id" value="<?= (int)$idMon ?>"><button type="submit">Xóa</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<p><strong>Tổng tiền: <?= e(vnd($tongTien)) ?></strong></p>
<form action="gio-hang.php" method="post"><input type="hidden" name="hanh_dong" value="xoa_tat_ca"><button type="submit">Xóa toàn bộ giỏ hàng</button></form>
<p><a href="danh-sach.php">Tiếp tục mua sắm</a></p>
<p><a href="dat-hang.php"><strong>Tiến hành đặt hàng</strong></a></p>
<?php endif; ?>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
