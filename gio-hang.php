
<?php
require __DIR__ . '/inc/config.php';

use App\Data\KhoSanPham;
use App\Services\GioHang;

$kho = new KhoSanPham(__DIR__ . '/data/san-pham.json');
$gio = new GioHang();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hanhDong = (string) ($_POST['hanh_dong'] ?? '');

    $id = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $soLuong = filter_var(
        $_POST['so_luong'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($hanhDong === 'xoa_tat_ca') {
        $gio->xoaHet();
    } elseif (
        $id !== false &&
        $id !== null &&
        $kho->timTheoId((int) $id) !== null
    ) {
        if (
            $hanhDong === 'them' &&
            $soLuong !== false &&
            $soLuong !== null &&
            $soLuong >= 1 &&
            $soLuong <= 20
        ) {
            $gio->them((int) $id, (int) $soLuong);
        } elseif (
            $hanhDong === 'cap_nhat' &&
            $soLuong !== false &&
            $soLuong !== null &&
            $soLuong >= 1 &&
            $soLuong <= 20
        ) {
            $gio->capNhat((int) $id, (int) $soLuong);
        } elseif ($hanhDong === 'xoa') {
            $gio->xoa((int) $id);
        }
    }

    chuyenHuong('gio-hang.php');
}

$tieuDe = 'Giỏ hàng';
$trang = 'gio-hang';

$cacMuc = $gio->cacMuc();

require __DIR__ . '/inc/header.php';
?>

<main class="trang__chinh">
    <h1>Giỏ hàng của bạn</h1>

    <?php if (empty($cacMuc)): ?>
        <p>
            Giỏ hàng hiện đang trống.
            <a href="danh-sach.php">Tiếp tục chọn món</a>
        </p>
    <?php else: ?>

        <div class="bang-boc">
            <table>
                <thead>
                    <tr>
                        <th>Món ăn</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($cacMuc as $id => $sl): ?>
                        <?php
                        $sp = $kho->timTheoId((int) $id);
                        if ($sp === null) {
                            continue;
                        }
                        ?>

                        <tr>
                            <td>
                                <a href="chi-tiet.php?id=<?= (int) $id ?>">
                                    <?= e($sp->ten) ?>
                                </a>
                            </td>

                            <td><?= e(vnd($sp->gia)) ?></td>

                            <td>
                                <form action="gio-hang.php" method="post">
                                    <input type="hidden"
                                           name="hanh_dong"
                                           value="cap_nhat">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= (int) $id ?>">

                                    <label>
                                        Số lượng
                                        <input type="number"
                                               name="so_luong"
                                               min="1"
                                               max="20"
                                               value="<?= (int) $sl ?>"
                                               required>
                                    </label>

                                    <button type="submit">
                                        Cập nhật
                                    </button>
                                </form>
                            </td>

                            <td>
                                <?= e(vnd($sp->gia * $sl)) ?>
                            </td>

                            <td>
                                <form action="gio-hang.php" method="post">
                                    <input type="hidden"
                                           name="hanh_dong"
                                           value="xoa">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= (int) $id ?>">

                                    <button type="submit">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p>
            <strong>
                Tổng tiền:
                <?= e(vnd($gio->tongTien($kho))) ?>
            </strong>
        </p>

        <form action="gio-hang.php" method="post">
            <input type="hidden"
                   name="hanh_dong"
                   value="xoa_tat_ca">

            <button type="submit">
                Xóa toàn bộ giỏ hàng
            </button>
        </form>

        <p>
            <a href="danh-sach.php">
                Tiếp tục mua sắm
            </a>
        </p>

    <?php endif; ?>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
