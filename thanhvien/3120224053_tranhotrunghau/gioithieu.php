<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/config.php';
$goc = '../../';
$cssRien = 'style.css';
$jsRien = 'js/canhan.js';
$tieuDe = 'Giới thiệu cá nhân - Trần Hồ Trung Hậu';
$trang = 'gioi-thieu';

// Chuc nang 1: Tim kiem, loc, sap xep ky nang bang GET.
$kyNang = [
    ['ten' => 'HTML5', 'nhom' => ['web'], 'mo_ta' => 'Xây dựng cấu trúc trang web.'],
    ['ten' => 'Sử dụng Git và GitHub', 'nhom' => ['cong-cu'], 'mo_ta' => 'Quản lý phiên bản và làm việc với kho mã nguồn.'],
    ['ten' => 'Làm việc nhóm', 'nhom' => ['ky-nang-mem'], 'mo_ta' => 'Phối hợp thực hiện dự án.'],
    ['ten' => 'Lập trình web', 'nhom' => ['web', 'lap-trinh'], 'mo_ta' => 'Phát triển trang web và các chức năng tương tác.'],
    ['ten' => 'Lập trình ứng dụng', 'nhom' => ['lap-trinh'], 'mo_ta' => 'Xây dựng ứng dụng giải quyết bài toán.'],
    ['ten' => 'Python', 'nhom' => ['lap-trinh'], 'mo_ta' => 'Lập trình và xử lý dữ liệu bằng Python.'],
];
$dsNhom = ['tat-ca' => 'Tất cả', 'web' => 'Web', 'lap-trinh' => 'Lập trình', 'cong-cu' => 'Công cụ', 'ky-nang-mem' => 'Kỹ năng mềm'];
$dsSapXep = ['ten-az' => 'Tên A → Z', 'ten-za' => 'Tên Z → A'];
$nhom = is_string($_GET['nhom'] ?? null) && isset($dsNhom[$_GET['nhom']]) ? $_GET['nhom'] : 'tat-ca';
$sapXep = is_string($_GET['sx'] ?? null) && isset($dsSapXep[$_GET['sx']]) ? $_GET['sx'] : 'ten-az';
$tuKhoa = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 50) : '';
$kyNangLoc = array_values(array_filter($kyNang, static function (array $k) use ($nhom, $tuKhoa): bool {
    if ($nhom !== 'tat-ca' && !in_array($nhom, $k['nhom'], true)) return false;
    return $tuKhoa === '' || mb_stripos($k['ten'] . ' ' . $k['mo_ta'], $tuKhoa) !== false;
}));
usort($kyNangLoc, static fn(array $a, array $b): int => $sapXep === 'ten-za'
    ? strcmp($b['ten'], $a['ten']) : strcmp($a['ten'], $b['ten']));
$truyVan = http_build_query(['nhom' => $nhom, 'q' => $tuKhoa, 'sx' => $sapXep]);

// Chuc nang 2: Giao dien sang/toi luu cookie, xu ly POST truoc khi in HTML.
$lay = static fn(string $khoa): string => is_string($_POST[$khoa] ?? null) ? trim($_POST[$khoa]) : '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && $lay('chuc_nang') === 'theme') {
    $chon = $lay('theme');
    if (in_array($chon, ['sang', 'toi'], true)) {
        setcookie('hau_theme', $chon, ['expires' => time() + 30 * 86400, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
        flash('hau_theme', $chon === 'toi' ? 'Đã chuyển sang giao diện tối.' : 'Đã chuyển sang giao diện sáng.');
    } else {
        flash('hau_theme', 'Giá trị giao diện không hợp lệ.');
    }
    chuyenHuong('gioithieu.php?' . $truyVan);
    exit;
}
$theme = is_string($_COOKIE['hau_theme'] ?? null) && in_array($_COOKIE['hau_theme'], ['sang', 'toi'], true)
    ? $_COOKIE['hau_theme'] : 'sang';
$tbTheme = flash('hau_theme');
$lopTrang = 'trang-ca-nhan theme-' . $theme;
require __DIR__ . '/../../inc/header.php';
?>
<main class="noi-dung">
    <nav class="dieu-huong" aria-label="Điều hướng cá nhân">
        <ul>
            <li><a href="../../index.php">Quay về Trang chủ Nhóm</a></li>
            <li><a href="../../gioi-thieu.php">Quay về Giới thiệu Nhóm</a></li>
        </ul>
    </nav>

    <h1 class="tieu-de-chinh">Giới thiệu thành viên: Trần Hồ Trung Hậu</h1>

    <section class="the php-box" aria-labelledby="php-tieu-de">
        <h2 id="php-tieu-de">Lọc kỹ năng và giao diện sáng / tối</h2>
        <h3>Lọc danh sách kỹ năng</h3>
        <form method="get" action="gioithieu.php">
            <p><label for="php-q">Từ khóa</label><br>
                <input id="php-q" name="q" type="search" maxlength="50" value="<?= e($tuKhoa) ?>"></p>
            <p><label for="php-nhom">Nhóm kỹ năng</label><br>
                <select id="php-nhom" name="nhom">
                    <?php foreach ($dsNhom as $ma => $nhan): ?>
                        <option value="<?= e($ma) ?>"<?= $ma === $nhom ? ' selected' : '' ?>><?= e($nhan) ?></option>
                    <?php endforeach; ?>
                </select></p>
            <p><label for="php-sx">Sắp xếp</label><br>
                <select id="php-sx" name="sx">
                    <?php foreach ($dsSapXep as $ma => $nhan): ?>
                        <option value="<?= e($ma) ?>"<?= $ma === $sapXep ? ' selected' : '' ?>><?= e($nhan) ?></option>
                    <?php endforeach; ?>
                </select></p>
            <button type="submit">Lọc</button>
            <a href="gioithieu.php">Xóa bộ lọc</a>
        </form>
        <p role="status">Tìm thấy <strong><?= count($kyNangLoc) ?></strong> kỹ năng.</p>
        <?php if (!$kyNangLoc): ?>
            <p>Không có kỹ năng nào khớp với bộ lọc hiện tại.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($kyNangLoc as $k): ?>
                    <li><strong><?= e($k['ten']) ?></strong> — <?= e($k['mo_ta']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <h3>Giao diện</h3>
        <?php if ($tbTheme): ?><p class="tb-ok" role="status"><?= e($tbTheme) ?></p><?php endif; ?>
        <p>Giao diện đang ghi nhớ: <strong><?= $theme === 'toi' ? 'Tối' : 'Sáng' ?></strong></p>
        <form method="post" action="gioithieu.php?<?= e($truyVan) ?>">
            <input type="hidden" name="chuc_nang" value="theme">
            <button type="submit" name="theme" value="sang">Giao diện sáng</button>
            <button type="submit" name="theme" value="toi">Giao diện tối</button>
        </form>
    </section>

    <section class="the tien-ich-hau">
        <h2>🎮 Tiện ích tương tác &amp; Đếm ngược ngày thi</h2>
        <div class="khu-vuc-dem-nguoc">
            <div class="dong-chon-ngay">
                <label for="input-ngay-thi" class="nhan-chon-ngay">📅 Chọn ngày thi giữa kỳ / cuối kỳ:</label>
                <input type="date" id="input-ngay-thi" class="input-ngay">
            </div>
            <div id="ket-qua-dem-nguoc" class="ket-qua-dem-nguoc">⏳ Vui lòng chọn ngày thi trên lịch để bắt đầu đếm ngược.</div>
        </div>
        <hr class="duong-phan-cach">
        <div class="khu-vuc-tro-choi">
            <p class="mo-ta-tro-choi">🐉 Linh vật Rồng &amp; Trò chơi thả bọ (Phím H - A - U):</p>
            <div class="nhom-nut-chuc-nang">
                <button id="btn-tha-bo" type="button" class="btn-tuong-tac btn-bo">🐛 Thả con bọ</button>
                <button id="btn-rong-h" type="button" class="btn-tuong-tac btn-rong">H (Xin chào)</button>
                <button id="btn-rong-a" type="button" class="btn-tuong-tac btn-rong">A (Phun lửa 🔥)</button>
                <button id="btn-rong-u" type="button" class="btn-tuong-tac btn-rong">U (Phóng to/Thu nhỏ &amp; Bay về)</button>
            </div>
        </div>
    </section>

    <section class="the gioi-thieu-ban-than">
        <h2>Giới thiệu bản thân</h2>
        <figure class="chan-dung">
            <img src="images/chan-dung-hau.jpg" alt="Ảnh chân dung Trần Hồ Trung Hậu" width="200" height="250" class="chan-dung__anh">
            <figcaption>Chân dung Trần Hồ Trung Hậu - Thành viên Nhóm 07</figcaption>
        </figure>
        <h3>Danh sách kỹ năng cá nhân</h3>
        <ul class="danh-sach-ky-nang">
            <?php foreach ($kyNang as $k): ?><li><?= e($k['ten']) ?></li><?php endforeach; ?>
        </ul>
        <div class="terminal-container">
            <div class="terminal-header">
                <span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
                <span class="terminal-title">bash - hau@dev-console</span>
            </div>
            <div id="terminal-output" class="terminal-output">
                <div><span class="prompt">hau@dev:~$</span> welcome</div>
                <div class="terminal-welcome">💻 Chào mừng bạn! Gõ <b>help</b> để xem danh sách câu lệnh.</div>
            </div>
            <div class="terminal-input-line">
                <span class="prompt">hau@dev:~$</span>
                <input type="text" id="terminal-input" aria-label="Nhập lệnh terminal" placeholder="Gõ lệnh ở đây (vd: help, skills, projects)..." autocomplete="off">
            </div>
        </div>
    </section>

    <article class="the du-an-so-thich">
        <h2>Dự án và sở thích</h2>
        <p>Tôi tham gia thực hiện đồ án xây dựng website Foodspead của Nhóm 07 (Trang giới thiệu).</p>
        <h3>Sở thích</h3>
        <ul class="danh-sach-so-thich">
            <li>Ngủ</li><li>Chơi cầu lông</li><li>Chơi game</li><li>Xem phim</li><li>Đọc sách</li><li>Tìm hiểu web</li>
        </ul>
    </article>

    <section class="the khu-thoi-khoa-bieu">
        <h2>Thời khóa biểu học tập trong tuần</h2>
        <table class="thoi-khoa-bieu">
            <caption>Thời khóa biểu học tập của Trần Hồ Trung Hậu</caption>
            <thead><tr><th scope="col">Thứ</th><th scope="col">Tiết</th><th scope="col">Mã học phần</th><th scope="col">Môn học</th><th scope="col">Phòng</th></tr></thead>
            <tbody>
                <tr><th scope="row">Thứ 2</th><td>7 - 9</td><td>31221010 - 24-0102</td><td>An toàn thông tin</td><td>A6-401</td></tr>
                <tr><th scope="row">Thứ 3</th><td>8 - 10</td><td>31241283 - 24-0103</td><td>Hệ quản trị cơ sở dữ liệu</td><td>B3-304</td></tr>
                <tr><th scope="row">Thứ 5</th><td>4 - 6</td><td>31231330 - 24-0102</td><td>Khai phá dữ liệu</td><td>A5-404B</td></tr>
                <tr><th scope="row">Thứ 5</th><td>7 - 9</td><td>31231016 - 24-0102</td><td>Công nghệ phần mềm</td><td>B3-303</td></tr>
                <tr><th scope="row">Thứ 7</th><td>1 - 3</td><td>31231755 - 24-0102</td><td>Thiết kế và lập trình web</td><td>B3-303</td></tr>
                <tr><th scope="row">Thứ 7</th><td>7 - 9</td><td>21221904 - 25-0332</td><td>Lịch sử Đảng Cộng sản Việt Nam</td><td>B3-302</td></tr>
            </tbody>
        </table>
    </section>
</main>
<?php require __DIR__ . '/../../inc/footer.php'; ?>
