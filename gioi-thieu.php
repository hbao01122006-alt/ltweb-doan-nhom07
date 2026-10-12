
<?php
require __DIR__ . '/inc/config.php';

$tieuDe = 'Giới thiệu nhóm & Dự án';
$trang = 'gioi-thieu';

$thanhVien = [
    [
        'hoTen' => 'Hoàng Gia Bảo',
        'maSV' => '3120224008',
        'vaiTro' => 'Nhóm trưởng',
        'thuMuc' => '3120224008_hoanggiabao',
        'anh' => 'chan-dung-bao.jpg',
        'trang' => 'gioithieu.php'
    ],
    [
        'hoTen' => 'Nguyễn Hữu Tài',
        'maSV' => '3120224126',
        'vaiTro' => 'Thành viên',
        'thuMuc' => '3120224126_nguyenhuutai',
        'anh' => 'chan-dung-tai.gif',
        'trang' => 'gioithieu.php'
    ],
    [
        'hoTen' => 'Trần Hồ Trung Hậu',
        'maSV' => '3120224053',
        'vaiTro' => 'Thành viên',
        'thuMuc' => '3120224053_tranhotrunghau',
        'anh' => 'chan-dung-hau.jpg',
        'trang' => 'gioithieu.php'
    ],
    [
        'hoTen' => 'Lê Công Huy',
        'maSV' => '3120224069',
        'vaiTro' => 'Thành viên',
        'thuMuc' => '3120224069_leconghuy',
        'anh' => 'chan-dung-huy.jpg',
        'trang' => 'gioithieu.php'
    ],
    [
        'hoTen' => 'Phạm Hồng Phong',
        'maSV' => '3120224110',
        'vaiTro' => 'Thành viên',
        'thuMuc' => '3120224110_phamhongphong',
        'anh' => 'chan-dung-phong.jpg',
        'trang' => 'gioithieu.php'
    ]
];

require __DIR__ . '/inc/header.php';
?>

<main class="trang__chinh">

    <h1>Giới thiệu Nhóm 07 - Đồ án Website Foodspead</h1>

    <section class="gioi-thieu__khoi">
        <h2>Mục đích dự án</h2>

        <img
            src="images/banner-food.jpg"
            alt="Mô phỏng hệ thống Foodspead"
            class="gioi-thieu__banner"
            width="600"
            height="320"
            loading="lazy"
        >

        <p class="gioi-thieu__doan">
            Dự án Foodspead được xây dựng nhằm mô phỏng
            một hệ thống đặt đồ ăn nhanh trực tuyến hiện đại.
            Website giúp người dùng lựa chọn món ăn,
            xem chi tiết sản phẩm, quản lý giỏ hàng
            và thực hiện các thao tác mua sắm trực tuyến.
        </p>
    </section>

    <section class="gioi-thieu__khoi">
        <h2>Danh sách thành viên Nhóm 07</h2>

        <ul class="danh-sach-thanh-vien">

            <?php foreach ($thanhVien as $tv): ?>
                <?php
                $duongDan = 'thanhvien/' . $tv['thuMuc'];
                $lienKet = $duongDan . '/' . $tv['trang'];
                ?>

                <li class="thanh-vien">

                    <img
                        src="<?= e($duongDan . '/images/' . $tv['anh']) ?>"
                        alt="<?= e('Ảnh ' . $tv['hoTen']) ?>"
                        width="100"
                        height="100"
                        loading="lazy"
                    >

                    <strong><?= e($tv['hoTen']) ?></strong>

                    <p>
                        Mã sinh viên:
                        <?= e($tv['maSV']) ?>
                    </p>

                    <p><?= e($tv['vaiTro']) ?></p>

                    <a href="<?= e($lienKet) ?>">
                        Xem trang cá nhân
                    </a>

                </li>
            <?php endforeach; ?>

        </ul>
    </section>

</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
