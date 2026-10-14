<?php
// gioithieu.php - trang cá nhân của Nguyễn Hữu Tài (3120224126), Bài tập nhóm 5 - Phần B.
// Dùng chung inc/header.php và inc/footer.php của nhóm; giữ nguyên style.css và js/canhan.js riêng.
// Chức năng 1: danh sách kỹ năng từ mảng PHP, tìm / lọc theo nhóm / sắp xếp bằng tham số GET (URL chia sẻ được).
// Chức năng 2: giao diện sáng / tối ghi nhớ bằng cookie (POST, kiểm tra giá trị cho phép, PRG).
// Cách thử: php -S localhost:8000, mở /thanhvien/3120224126_nguyenhuutai/gioithieu.php?nhom=web&sx=ten-za
require __DIR__ . '/../../inc/config.php';
$goc = '../../';
$cssRien = 'style.css';
$jsRien = 'js/canhan.js';

// ---------- Chức năng 1: lọc kỹ năng bằng GET ----------
$kyNang = [
    ['ten' => 'HTML5', 'nhom' => ['web'], 'mo_ta' => 'Dựng cấu trúc trang bằng các thẻ ngữ nghĩa.'],
    ['ten' => 'CSS3', 'nhom' => ['web'], 'mo_ta' => 'Trình bày và bố cục responsive cho trang web.'],
    ['ten' => 'JavaScript', 'nhom' => ['web', 'lap-trinh'], 'mo_ta' => 'Xử lý tương tác và dữ liệu phía trình duyệt.'],
    ['ten' => 'Lập trình web', 'nhom' => ['web', 'lap-trinh'], 'mo_ta' => 'Xây dựng website từ giao diện đến xử lý dữ liệu.'],
    ['ten' => 'Lập trình ứng dụng', 'nhom' => ['lap-trinh'], 'mo_ta' => 'Viết chương trình giải quyết một bài toán cụ thể.'],
    ['ten' => 'Python', 'nhom' => ['lap-trinh'], 'mo_ta' => 'Ngôn ngữ dùng cho bài tập lập trình và xử lý dữ liệu.'],
    ['ten' => 'Git và GitHub', 'nhom' => ['cong-cu'], 'mo_ta' => 'Quản lý phiên bản và phối hợp trong nhóm.'],
    ['ten' => 'Làm việc nhóm', 'nhom' => ['ky-nang-mem'], 'mo_ta' => 'Phân công, trao đổi và hoàn thành việc chung.'],
];
$dsNhom = ['tat-ca' => 'Tất cả', 'web' => 'Web', 'lap-trinh' => 'Lập trình', 'cong-cu' => 'Công cụ', 'ky-nang-mem' => 'Kỹ năng mềm'];
$dsSapXep = ['ten-az' => 'Tên A → Z', 'ten-za' => 'Tên Z → A'];

// Chỉ nhận giá trị nằm trong danh sách cho phép; còn lại dùng mặc định.
$nhom = is_string($_GET['nhom'] ?? null) && isset($dsNhom[$_GET['nhom']]) ? $_GET['nhom'] : 'tat-ca';
$sapXep = is_string($_GET['sx'] ?? null) && isset($dsSapXep[$_GET['sx']]) ? $_GET['sx'] : 'ten-az';
$tuKhoa = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 50) : '';

$kyNangLoc = array_values(array_filter($kyNang, static function (array $k) use ($nhom, $tuKhoa): bool {
    if ($nhom !== 'tat-ca' && !in_array($nhom, $k['nhom'], true)) return false;
    return $tuKhoa === '' || mb_stripos($k['ten'] . ' ' . $k['mo_ta'], $tuKhoa) !== false;
}));
usort($kyNangLoc, static fn(array $a, array $b): int => $sapXep === 'ten-za'
    ? strcmp($b['ten'], $a['ten'])
    : strcmp($a['ten'], $b['ten']));
$truyVan = http_build_query(['nhom' => $nhom, 'q' => $tuKhoa, 'sx' => $sapXep]);   // giữ bộ lọc khi đổi giao diện

// ---------- Chức năng 2: giao diện sáng / tối bằng cookie ----------
$lay = static fn(string $khoa): string => is_string($_POST[$khoa] ?? null) ? trim($_POST[$khoa]) : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $lay('chuc_nang') === 'theme') {
    $chon = $lay('theme');
    if ($chon === 'sang' || $chon === 'toi') {
        setcookie('tai_theme', $chon, ['expires' => time() + 30 * 86400, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
        flash('tai_theme', $chon === 'toi' ? 'Đã chuyển sang giao diện tối.' : 'Đã chuyển sang giao diện sáng.');
    } else {
        flash('tai_theme', 'Giao diện không hợp lệ, giữ nguyên như cũ.');
    }
    chuyenHuong('gioithieu.php?' . $truyVan);
}
$theme = (is_string($_COOKIE['tai_theme'] ?? null) && in_array($_COOKIE['tai_theme'], ['sang', 'toi'], true)) ? $_COOKIE['tai_theme'] : 'sang';
$tbTheme = flash('tai_theme');
$lopTrang = 'trang-ca-nhan theme-' . $theme;                     // header in ra thẻ <body>

$tieuDe = 'Nguyễn Hữu Tài';
$trang = '';
require __DIR__ . '/../../inc/header.php';
?>
<main class="noi-dung">


        <h1 class="tieu-de-chinh">Giới thiệu thành viên: Nguyễn Hữu Tài</h1>

<section class="the php-box" aria-labelledby="php-tieu-de">
  <h2 id="php-tieu-de">Lọc kỹ năng và giao diện sáng / tối</h2>

  <h3>Lọc danh sách kỹ năng</h3>
  <form method="get" action="gioithieu.php">
    <p>
      <label for="php-q">Từ khóa</label><br>
      <input id="php-q" name="q" type="search" maxlength="50" value="<?= e($tuKhoa) ?>">
    </p>
    <p>
      <label for="php-nhom">Nhóm kỹ năng</label><br>
      <select id="php-nhom" name="nhom">
        <?php foreach ($dsNhom as $ma => $nhan): ?>
          <option value="<?= e($ma) ?>"<?= $ma === $nhom ? ' selected' : '' ?>><?= e($nhan) ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <p>
      <label for="php-sx">Sắp xếp</label><br>
      <select id="php-sx" name="sx">
        <?php foreach ($dsSapXep as $ma => $nhan): ?>
          <option value="<?= e($ma) ?>"<?= $ma === $sapXep ? ' selected' : '' ?>><?= e($nhan) ?></option>
        <?php endforeach; ?>
      </select>
    </p>
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
