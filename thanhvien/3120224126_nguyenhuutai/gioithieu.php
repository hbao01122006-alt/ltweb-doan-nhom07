<?php
// gioithieu.php - Trang cá nhân của Nguyễn Hữu Tài, chứa hai chức năng CSDL MySQL theo yêu cầu bài tập.
// Chức năng CSDL 1: danh sách kỹ năng của Bài 5 được chuyển từ mảng PHP sang MySQL, có tìm/lọc/sắp xếp và quản lý.
// Chức năng CSDL 2: lựa chọn giao diện sáng/tối được chuyển từ cookie sang MySQL; chỉ chủ trang được thay đổi.
// Cách thử: lọc ?nhom=web&sx=ten-za; đăng nhập tai@foods.example / matkhau123 để thêm/xóa kỹ năng hoặc đổi giao diện.
require __DIR__ . '/../../inc/config.php';
$goc = '../../';
$cssRien = 'style.css';
$jsRien = 'js/canhan.js';
const MSSV = '3120224126';
$lay = static fn(string $k): string => is_string($_POST[$k] ?? null) ? trim($_POST[$k]) : '';
$nd = nguoiDung();
$laChuTrang = (new App\Data\KhoThanhVien(db()))->laChuTrang(MSSV, $nd ? (int)$nd['id'] : null) || ($nd['vai_tro'] ?? '') === 'quan_tri';
$dsNhom = ['tat-ca' => 'Tất cả', 'web' => 'Web', 'lap-trinh' => 'Lập trình', 'cong-cu' => 'Công cụ', 'ky-nang-mem' => 'Kỹ năng mềm'];
$dsSapXep = ['ten-az' => 'Tên A → Z', 'ten-za' => 'Tên Z → A', 'muc-giam' => 'Mức độ giảm dần'];
$nhom = is_string($_GET['nhom'] ?? null) && isset($dsNhom[$_GET['nhom']]) ? $_GET['nhom'] : 'tat-ca';
$sapXep = is_string($_GET['sx'] ?? null) && isset($dsSapXep[$_GET['sx']]) ? $_GET['sx'] : 'ten-az';
$tuKhoa = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 50) : '';
$orderBy = ['ten-az' => 'ten ASC,id', 'ten-za' => 'ten DESC,id', 'muc-giam' => 'muc_do DESC,ten ASC'][$sapXep];
$truyVan = http_build_query(['nhom' => $nhom, 'q' => $tuKhoa, 'sx' => $sapXep]);

// Chức năng 2 xử lý trước output: giao diện lưu MySQL.
$tbTheme = flash('tai_theme');
$loiKyNang = [];
$duKn = ['ten' => '', 'nhom' => 'web', 'mo_ta' => '', 'muc_do' => '3'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chuc = $lay('chuc_nang');
    if (in_array($chuc, ['theme', 'them-ky-nang', 'xoa-ky-nang'], true) && !$laChuTrang) {
        http_response_code(403);
        require __DIR__ . '/../../403.php';
        exit;
    }
    if ($chuc === 'theme') {
        $chon = $lay('theme');
        if (in_array($chon, ['sang', 'toi'], true)) {
            db()->prepare('INSERT INTO tv_nguyen_huu_tai_tuy_chon (mssv,giao_dien) VALUES (?,?) ON DUPLICATE KEY UPDATE giao_dien=VALUES(giao_dien), cap_nhat=CURRENT_TIMESTAMP')->execute([MSSV, $chon]);
            flash('tai_theme', $chon === 'toi' ? 'Đã lưu giao diện tối vào CSDL.' : 'Đã lưu giao diện sáng vào CSDL.');
        } else flash('tai_theme', 'Giao diện không hợp lệ.');
        chuyenHuong('gioithieu.php?' . $truyVan . '#php-theme');
    }
    if ($chuc === 'xoa-ky-nang') {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if ($id) db()->prepare('DELETE FROM tv_nguyen_huu_tai_ky_nang WHERE id=? AND mssv=?')->execute([$id, MSSV]);
        flash('tai_kn', 'Đã xóa kỹ năng.');
        chuyenHuong('gioithieu.php?' . $truyVan . '#php-ky-nang');
    }
    if ($chuc === 'them-ky-nang') {
        $duKn = ['ten' => $lay('ten'), 'nhom' => $lay('nhom_kn'), 'mo_ta' => $lay('mo_ta'), 'muc_do' => $lay('muc_do')];
        $muc = filter_var($duKn['muc_do'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
        if (mb_strlen($duKn['ten']) < 1 || mb_strlen($duKn['ten']) > 100) $loiKyNang['ten'] = 'Tên kỹ năng dài 1 - 100 ký tự.';
        if (!isset($dsNhom[$duKn['nhom']]) || $duKn['nhom'] === 'tat-ca') $loiKyNang['nhom'] = 'Chọn nhóm hợp lệ.';
        if (mb_strlen($duKn['mo_ta']) < 3 || mb_strlen($duKn['mo_ta']) > 300) $loiKyNang['mo_ta'] = 'Mô tả dài 3 - 300 ký tự.';
        if ($muc === false) $loiKyNang['muc_do'] = 'Mức độ từ 1 đến 5.';
        if (!$loiKyNang) {
            $st = db()->prepare('SELECT COUNT(*) FROM tv_nguyen_huu_tai_ky_nang WHERE mssv=? AND ten=?');
            $st->execute([MSSV, $duKn['ten']]);
            if ((int)$st->fetchColumn() > 0) $loiKyNang['ten'] = 'Kỹ năng này đã có.';
            else {
                db()->prepare('INSERT INTO tv_nguyen_huu_tai_ky_nang (mssv,ten,nhom,mo_ta,muc_do) VALUES (?,?,?,?,?)')->execute([MSSV, $duKn['ten'], $duKn['nhom'], $duKn['mo_ta'], $muc]);
                flash('tai_kn', 'Đã thêm kỹ năng.');
                chuyenHuong('gioithieu.php?' . $truyVan . '#php-ky-nang');
            }
        }
    }
}
$tbKyNang = flash('tai_kn');
$st = db()->prepare('SELECT giao_dien FROM tv_nguyen_huu_tai_tuy_chon WHERE mssv=?');
$st->execute([MSSV]);
$themeDb = $st->fetchColumn();
$theme = in_array($themeDb, ['sang', 'toi'], true) ? $themeDb : 'sang';
$lopTrang = 'trang-ca-nhan theme-' . $theme;

// Chức năng 1: tìm/lọc/sắp xếp trực tiếp trên bảng kỹ năng MySQL.
$where = 'WHERE mssv=? AND (ten LIKE ? OR mo_ta LIKE ?)';
$tim = '%' . addcslashes($tuKhoa, '%_\\') . '%';
$ts = [MSSV, $tim, $tim];
if ($nhom !== 'tat-ca') {
    $where .= ' AND nhom=?';
    $ts[] = $nhom;
}
$st = db()->prepare("SELECT id,ten,nhom,mo_ta,muc_do FROM tv_nguyen_huu_tai_ky_nang $where ORDER BY $orderBy");
$st->execute($ts);
$kyNangLoc = $st->fetchAll();
$st = db()->prepare('SELECT nhom,COUNT(*) AS so_luong,AVG(muc_do) AS tb FROM tv_nguyen_huu_tai_ky_nang WHERE mssv=? GROUP BY nhom ORDER BY nhom');
$st->execute([MSSV]);
$theoNhom = $st->fetchAll();

$tieuDe = 'Nguyễn Hữu Tài';
$trang = '';
require __DIR__ . '/../../inc/header.php';
?>
<main class="noi-dung">
    <h1 class="tieu-de-chinh">Giới thiệu thành viên: Nguyễn Hữu Tài</h1>
    <section class="the php-box" aria-labelledby="php-tieu-de">
        <h2 id="php-tieu-de">Kỹ năng và giao diện bằng MySQL</h2>
        <h3 id="php-ky-nang">1. Kỹ năng từ CSDL</h3>
        <?php if ($tbKyNang): ?><p class="tb-ok" role="status"><?= e($tbKyNang) ?></p><?php endif; ?>
        <form method="get" action="gioithieu.php">
            <p><label for="php-q">Từ khóa</label><br><input id="php-q" name="q" type="search" maxlength="50" value="<?= e($tuKhoa) ?>"></p>
            <p><label for="php-nhom">Nhóm</label><br><select id="php-nhom" name="nhom"><?php foreach ($dsNhom as $ma => $nhan): ?><option value="<?= e($ma) ?>" <?= $ma === $nhom ? ' selected' : '' ?>><?= e($nhan) ?></option><?php endforeach; ?></select></p>
            <p><label for="php-sx">Sắp xếp</label><br><select id="php-sx" name="sx"><?php foreach ($dsSapXep as $ma => $nhan): ?><option value="<?= e($ma) ?>" <?= $ma === $sapXep ? ' selected' : '' ?>><?= e($nhan) ?></option><?php endforeach; ?></select></p><button type="submit">Tìm / lọc</button>
        </form>
        <p>Tìm thấy <?= e((string)count($kyNangLoc)) ?> kỹ năng.</p>
        <?php if (!$kyNangLoc): ?><p>Không có kỹ năng phù hợp.</p><?php else: ?><ul><?php foreach ($kyNangLoc as $k): ?><li><strong><?= e($k['ten']) ?></strong> — <?= e($dsNhom[$k['nhom']] ?? $k['nhom']) ?> — mức <?= e($k['muc_do']) ?>/5: <?= e($k['mo_ta']) ?><?php if ($laChuTrang): ?> <form method="post" action="gioithieu.php?<?= e($truyVan) ?>#php-ky-nang" class="form-dong"><input type="hidden" name="chuc_nang" value="xoa-ky-nang"><input type="hidden" name="id" value="<?= e($k['id']) ?>"><button type="submit">Xóa</button></form><?php endif; ?></li><?php endforeach; ?></ul><?php endif; ?>
        <?php if ($laChuTrang): ?><h4>Thêm kỹ năng</h4>
            <form method="post" action="gioithieu.php?<?= e($truyVan) ?>#php-ky-nang"><input type="hidden" name="chuc_nang" value="them-ky-nang">
                <p><label for="kn-ten">Tên</label><br><input id="kn-ten" name="ten" maxlength="100" required value="<?= e($duKn['ten']) ?>"></p><?php if (isset($loiKyNang['ten'])): ?><p class="loi"><?= e($loiKyNang['ten']) ?></p><?php endif; ?><p><label for="kn-nhom">Nhóm</label><br><select id="kn-nhom" name="nhom_kn"><?php foreach ($dsNhom as $ma => $nhan): if ($ma === 'tat-ca') continue; ?><option value="<?= e($ma) ?>" <?= $duKn['nhom'] === $ma ? ' selected' : '' ?>><?= e($nhan) ?></option><?php endforeach; ?></select></p>
                <p><label for="kn-mo-ta">Mô tả</label><br><input id="kn-mo-ta" name="mo_ta" maxlength="300" required value="<?= e($duKn['mo_ta']) ?>"></p>
                <p><label for="kn-muc">Mức độ 1-5</label><br><input id="kn-muc" name="muc_do" type="number" min="1" max="5" value="<?= e($duKn['muc_do']) ?>"></p><button type="submit">Thêm kỹ năng</button>
            </form><?php endif; ?>
        <h4>Thống kê theo nhóm</h4>
        <ul><?php foreach ($theoNhom as $r): ?><li><?= e($dsNhom[$r['nhom']] ?? $r['nhom']) ?>: <?= e($r['so_luong']) ?> kỹ năng, mức TB <?= e(number_format((float)$r['tb'], 1, ',', '.')) ?></li><?php endforeach; ?></ul>

        <h3 id="php-theme">2. Giao diện sáng / tối lưu CSDL</h3>
        <?php if ($tbTheme): ?><p class="tb-ok" role="status"><?= e($tbTheme) ?></p><?php endif; ?><p>Giao diện đang lưu: <strong><?= $theme === 'toi' ? 'Tối' : 'Sáng' ?></strong>.</p>
        <?php if ($laChuTrang): ?><form method="post" action="gioithieu.php?<?= e($truyVan) ?>#php-theme"><input type="hidden" name="chuc_nang" value="theme"><button name="theme" value="sang" type="submit">Giao diện sáng</button><button name="theme" value="toi" type="submit">Giao diện tối</button></form><?php else: ?><p><small>Chỉ chủ trang được đổi giao diện đã lưu.</small></p><?php endif; ?>
    </section>

    <!-- BẢNG ĐIỀU KHIỂN TƯƠNG TÁC -->
    <section class="the thanh-cong-cu-tai">
        <h2>🛠 Bảng điều khiển tương tác</h2>

        <div class="nhom-nut-chuc-nang">
            <button id="btn-pencil" type="button" class="btn-tuong-tac">✏️ Bật Bút Chì Vẽ</button>
            <button id="btn-clear-canvas" type="button" class="btn-tuong-tac">🗑 Xóa Nét Vẽ</button>
        </div>

        <div class="khu-vuc-linh-vat-nut">
            <p class="tieu-de-nut-pet">🦊 Tương tác Linh vật Con Cáo (Bấm nút bên dưới hoặc gõ phím T - A - I):</p>
            <div class="nhom-3-nut-pet">
                <button id="btn-pet-t" type="button" class="btn-tuong-tac btn-pet">T (Tăng kích thước)</button>
                <button id="btn-pet-a" type="button" class="btn-tuong-tac btn-pet">A (Ảo thuật xoay tròn)</button>
                <button id="btn-pet-i" type="button" class="btn-tuong-tac btn-pet">I (In dấu góc trang)</button>
            </div>
        </div>
    </section>

    <section class="the gioi-thieu-ban-than">
        <h2>Giới thiệu bản thân</h2>

        <figure class="chan-dung">
            <img src="images/chan-dung-tai.gif"
                alt="Ảnh chân dung Nguyễn Hữu Tài"
                width="200"
                height="250" class="chan-dung__anh">

            <figcaption>
                Chân dung Nguyễn Hữu Tài - Thành viên Nhóm 07
            </figcaption>
        </figure>

        <h3>Danh sách kỹ năng cá nhân</h3>

        <!-- Ô TÌM KIẾM KỸ NĂNG VÀ SỞ THÍCH -->
        <div class="khu-tim-kiem">
            <label for="o-tim-kiem" class="nhan-tim-kiem">🔍 Tìm kiếm kỹ năng &amp; sở thích:</label>
            <input
                type="text"
                id="o-tim-kiem"
                class="input-tim-kiem"
                placeholder="Nhập từ khóa (vd: HTML, Python, Đá bóng)...">
            <p id="thong-bao-tim-kiem" class="thong-bao-tim-kiem" aria-live="polite"></p>
        </div>

        <ul class="danh-sach-ky-nang">
            <li>HTML5</li>
            <li>Sử dụng Git và GitHub</li>
            <li>Làm việc nhóm</li>
            <li>Lập trình web</li>
            <li>Lập trình ứng dụng</li>
            <li>Python</li>
        </ul>
    </section>

    <article class="the du-an-so-thich">
        <h2>Dự án và sở thích</h2>

        <p>
            Tôi tham gia thực hiện đồ án xây dựng website FastFood
            của Nhóm 07.
        </p>

        <h3>Sở thích</h3>

        <ul class="danh-sach-so-thich">
            <li>Đá bóng</li>
            <li>Chơi cầu lông</li>
            <li>Chơi game</li>
            <li>Xem phim</li>
            <li>Đọc sách</li>
        </ul>
    </article>

    <section class="the khu-thoi-khoa-bieu">
        <h2>Thời khóa biểu học tập trong tuần</h2>

        <table class="thoi-khoa-bieu">

            <caption>
                Thời khóa biểu học tập của Nguyễn Hữu Tài
            </caption>

            <thead>
                <tr>
                    <th scope="col">Thứ</th>
                    <th scope="col">Tiết</th>
                    <th scope="col">Mã học phần</th>
                    <th scope="col">Môn học</th>
                    <th scope="col">Phòng</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <th scope="row">Thứ 2</th>
                    <td>7 - 9</td>
                    <td>31221010 - 24-0102</td>
                    <td>An toàn thông tin</td>
                    <td>A6-401</td>
                </tr>

                <tr>
                    <th scope="row">Thứ 3</th>
                    <td>8 - 10</td>
                    <td>31241283 - 24-0103</td>
                    <td>Hệ quản trị cơ sở dữ liệu</td>
                    <td>B3-304</td>
                </tr>

                <tr>
                    <th scope="row">Thứ 5</th>
                    <td>4 - 6</td>
                    <td>31231330 - 24-0102</td>
                    <td>Khai phá dữ liệu</td>
                    <td>A5-404B</td>
                </tr>

                <tr>
                    <th scope="row">Thứ 5</th>
                    <td>7 - 9</td>
                    <td>31231016 - 24-0102</td>
                    <td>Công nghệ phần mềm</td>
                    <td>B3-303</td>
                </tr>

                <tr>
                    <th scope="row">Thứ 7</th>
                    <td>1 - 3</td>
                    <td>31231755 - 24-0102</td>
                    <td>Thiết kế và lập trình web</td>
                    <td>B3-303</td>
                </tr>

                <tr>
                    <th scope="row">Thứ 7</th>
                    <td>7 - 9</td>
                    <td>21221904 - 25-0332</td>
                    <td>Lịch sử Đảng Cộng sản Việt Nam</td>
                    <td>B3-302</td>
                </tr>

            </tbody>

        </table>
    </section>

</main>
<?php require __DIR__ . '/../../inc/footer.php'; ?>