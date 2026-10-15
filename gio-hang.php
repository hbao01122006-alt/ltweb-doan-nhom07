
<?php
// gio-hang.php - Quan ly gio hang Foodspead bang Session.

require __DIR__ . '/inc/config.php';

use App\Data\KhoSanPham;
use App\Services\GioHang;

$kho = new KhoSanPham(__DIR__ . '/data/san-pham.json');
$gio = new GioHang();

$loi = '';

// Xu ly cac yeu cau POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hanhDong = (string) ($_POST['hanh_dong'] ?? '');

    $id = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $soLuong = filter_var(
        $_POST['so_luong'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => [
            'min_range' => 1,
            'max_range' => 20
        ]]
    );

    try {
        // Xoa toan bo gio hang.
        if (in_array($hanhDong, ['xoa_tat_ca', 'xoa-het'], true)) {
            $gio->xoaHet();

            flash('gio', 'Đã xóa toàn bộ giỏ hàng.');
            chuyenHuong('gio-hang.php');
            exit;
        }

        // Kiem tra ID va san pham.
        if (
            $id === false ||
            $id === null ||
            $kho->timTheoId((int) $id) === null
        ) {
            throw new InvalidArgumentException(
                'Món ăn không hợp lệ.'
            );
        }

        // Xoa mot san pham.
        if ($hanhDong === 'xoa') {
            $gio->xoa((int) $id);

            flash('gio', 'Đã xóa món khỏi giỏ hàng.');
            chuyenHuong('gio-hang.php');
            exit;
        }

        // Kiem tra so luong.
        if ($soLuong === false || $soLuong === null) {
            throw new InvalidArgumentException(
                'Số lượng phải từ 1 đến 20.'
            );
        }

        // Them san pham.
        if ($hanhDong === 'them') {
            $soLuongHienTai = (int) (
                $gio->cacMuc()[(int) $id] ?? 0
            );

            if ($soLuongHienTai + $soLuong > 20) {
                throw new InvalidArgumentException(
                    'Tổng số lượng của món không được vượt quá 20.'
                );
            }

            $gio->them((int) $id, (int) $soLuong);

            flash('gio', 'Đã thêm món vào giỏ hàng.');
        }

        // Cap nhat so luong.
        elseif (in_array($hanhDong, ['cap_nhat', 'cap-nhat'], true)) {
            $gio->capNhat((int) $id, (int) $soLuong);

            flash('gio', 'Đã cập nhật số lượng món ăn.');
        }

        // Hanh dong khong hop le.
        else {
            throw new InvalidArgumentException(
                'Thao tác giỏ hàng không hợp lệ.'
            );
        }

        // PRG: Chuyen huong sau POST thanh cong.
        chuyenHuong('gio-hang.php');
        exit;

    } catch (InvalidArgumentException $e) {
        $loi = $e->getMessage();
    }
}

// Lay thong bao tu Session.
$thongBao = flash('gio');

$tieuDe = 'Giỏ hàng';
$trang = 'gio-hang';

$cacMuc = $gio->cacMuc();

require __DIR__ . '/inc/header.php';
?>

<main class="trang__chinh">

    <h1>Giỏ hàng của bạn</h1>

    <!-- Thong bao thanh cong -->
    <?php if ($thongBao): ?>
        <p class="trang-thai" role="status">
            <?= e($thongBao) ?>
        </p>
    <?php endif; ?>

    <!-- Thong bao loi -->
    <?php if ($loi): ?>
        <p class="loi" role="alert">
            <?= e($loi) ?>
        </p>
    <?php endif; ?>

    <?php if (empty($cacMuc)): ?>

        <p>
            Giỏ hàng hiện đang trống.
            <a href="danh-sach.php">
                Tiếp tục chọn món
            </a>
        </p>

    <?php else: ?>

        <div class="bang-boc">

            <table>
                <caption>Các món trong giỏ hàng</caption>

                <thead>
                    <tr>
                        <th scope="col">Món ăn</th>
                        <th scope="col">Đơn giá</th>
                        <th scope="col">Số lượng</th>
                        <th scope="col">Thành tiền</th>
                        <th scope="col">Thao tác</th>
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
                            <!-- Ten san pham -->
                            <td>
                                <a href="chi-tiet.php?id=<?= (int) $id ?>">
                                    <?= e($sp->ten) ?>
                                </a>
                            </td>

                            <!-- Don gia -->
                            <td>
                                <?= e(vnd($sp->gia)) ?>
                            </td>

                            <!-- Cap nhat so luong -->
                            <td>
                                <form action="gio-hang.php" method="post">

                                    <input
                                        type="hidden"
                                        name="hanh_dong"
                                        value="cap_nhat"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $id ?>"
                                    >

                                    <label>
                                        Số lượng
                                        <input
                                            type="number"
                                            name="so_luong"
                                            min="1"
                                            max="20"
                                            value="<?= (int) $sl ?>"
                                            required
                                        >
                                    </label>

                                    <button type="submit">
                                        Cập nhật
                                    </button>

                                </form>
                            </td>

                            <!-- Thanh tien -->
                            <td>
                                <?= e(vnd($sp->gia * $sl)) ?>
                            </td>

                            <!-- Xoa san pham -->
                            <td>
                                <form action="gio-hang.php" method="post">

                                    <input
                                        type="hidden"
                                        name="hanh_dong"
                                        value="xoa"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $id ?>"
                                    >

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

        <!-- Tong tien -->
        <p>
            <strong>
                Tổng tiền:
                <?= e(vnd($gio->tongTien($kho))) ?>
            </strong>
        </p>

        <!-- Xoa toan bo gio hang -->
        <form action="gio-hang.php" method="post">

            <input
                type="hidden"
                name="hanh_dong"
                value="xoa_tat_ca"
            >

            <button type="submit">
                Xóa toàn bộ giỏ hàng
            </button>

        </form>

        <!-- Quay lai thuc don -->
        <p>
            <a href="danh-sach.php">
                Tiếp tục mua sắm
            </a>
        </p>

    <?php endif; ?>

</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
