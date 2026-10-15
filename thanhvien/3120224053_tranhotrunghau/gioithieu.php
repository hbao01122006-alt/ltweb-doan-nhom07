<?php
// gioithieu.php - trang cá nhân của Trần Hồ Trung Hậu (3120224053), Bài tập nhóm 5 - Phần B.
// Dùng chung inc/header.php và inc/footer.php của nhóm; giữ nguyên style.css và js/canhan.js riêng.
// Chức năng 1: trắc nghiệm PHP - đề lấy từ mảng PHP, chấm ở máy chủ, kết quả lưu trong session, PRG.
// Chức năng 2: ghi nhớ ngày thi bằng cookie (kiểm tra lại khi đọc) và tính số ngày còn lại.
// Cách thử: php -S localhost:8000, mở /thanhvien/3120224053_tranhotrunghau/gioithieu.php rồi làm bài.
require __DIR__ . '/../../inc/config.php';
$goc = '../../';
$lopTrang = 'trang-ca-nhan';
$cssRien = 'style.css';
$jsRien = 'js/canhan.js';

date_default_timezone_set('Asia/Ho_Chi_Minh');
$lay = static fn(string $khoa): string => is_string($_POST[$khoa] ?? null) ? trim($_POST[$khoa]) : '';
$hanhDong = $_SERVER['REQUEST_METHOD'] === 'POST' ? $lay('chuc_nang') : '';
$homNay = new DateTimeImmutable('today');

// Đọc một chuỗi Y-m-d; trả về null nếu sai định dạng hoặc ngày không có thật (ví dụ 2026-02-31).
$docNgay = static function (string $chuoi): ?DateTimeImmutable {
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $chuoi);
    return ($d !== false && $d->format('Y-m-d') === $chuoi) ? $d : null;
};
$tuyChonCookie = ['path' => '/', 'httponly' => true, 'samesite' => 'Lax'];

// ---------- Chức năng 2 (xử lý trước vì dùng setcookie): ngày thi ----------
$duNgayThi = '';
$loiNgayThi = '';
if ($hanhDong === 'ngay-thi') {
    $duNgayThi = $lay('ngay_thi');
    $dt = $docNgay($duNgayThi);
    if ($duNgayThi === '') {
        $loiNgayThi = 'Hãy chọn ngày thi.';
    } elseif ($dt === null) {
        $loiNgayThi = 'Ngày thi không hợp lệ (cần dạng năm-tháng-ngày, ngày có thật).';
    } elseif ($dt < $homNay) {
        $loiNgayThi = 'Ngày thi phải từ hôm nay trở đi.';
    } elseif ($dt > $homNay->modify('+2 years')) {
        $loiNgayThi = 'Chỉ nhận ngày thi trong vòng 2 năm tới.';
    } else {
        setcookie('hau_ngay_thi', $duNgayThi, $tuyChonCookie + ['expires' => time() + 30 * 86400]);
        flash('hau_thi', 'Đã ghi nhớ ngày thi.');
        chuyenHuong('gioithieu.php');
    }
}
if ($hanhDong === 'xoa-ngay-thi') {
    setcookie('hau_ngay_thi', '', $tuyChonCookie + ['expires' => time() - 3600]);
    flash('hau_thi', 'Đã xóa ngày thi đã lưu.');
    chuyenHuong('gioithieu.php');
}
// Đọc cookie: không tin giá trị, kiểm tra lại từng lần; giá trị lạ thì bỏ qua.
$ngayThi = null;
$soNgayCon = null;
$giaTriCookie = $_COOKIE['hau_ngay_thi'] ?? '';
if (is_string($giaTriCookie) && ($d = $docNgay($giaTriCookie)) !== null) {
    $ngayThi = $d;
    $soNgayCon = (int)$homNay->diff($d)->format('%r%a');
}
$tbNgayThi = flash('hau_thi');

// ---------- Chức năng 1: trắc nghiệm PHP ----------
$cauHoi = [
    'q1' => [
        'noi_dung' => 'Hàm nào mã hóa ký tự đặc biệt trước khi in dữ liệu người dùng ra HTML?',
        'lua_chon' => ['a' => 'strip_tags()', 'b' => 'htmlspecialchars()', 'c' => 'addslashes()', 'd' => 'trim()'],
        'dap_an' => 'b',
    ],
    'q2' => [
        'noi_dung' => 'Sau khi xử lý POST thành công, nên làm gì để bấm F5 không gửi lại biểu mẫu?',
        'lua_chon' => ['a' => 'In thông báo bằng echo', 'b' => 'Chuyển hướng bằng header(\'Location\') rồi exit', 'c' => 'Gọi sleep()', 'd' => 'Gọi session_destroy()'],
        'dap_an' => 'b',
    ],
    'q3' => [
        'noi_dung' => 'Hàm nào dùng để lưu mật khẩu dưới dạng băm an toàn?',
        'lua_chon' => ['a' => 'md5()', 'b' => 'base64_encode()', 'c' => 'password_hash()', 'd' => 'sha1()'],
        'dap_an' => 'c',
    ],
    'q4' => [
        'noi_dung' => 'setcookie() phải được gọi vào lúc nào?',
        'lua_chon' => ['a' => 'Sau thẻ đóng </html>', 'b' => 'Trước khi có bất kỳ output nào', 'c' => 'Chỉ trong vòng lặp foreach', 'd' => 'Sau session_destroy()'],
        'dap_an' => 'b',
    ],
];
$traLoi = [];
$loiQuiz = [];
if ($hanhDong === 'quiz') {
    foreach ($cauHoi as $ma => $ch) {
        $v = $_POST[$ma] ?? null;
        if (is_string($v) && array_key_exists($v, $ch['lua_chon'])) {   // chỉ nhận a, b, c, d
            $traLoi[$ma] = $v;
        } else {
            $loiQuiz[$ma] = 'Hãy chọn một đáp án.';
        }
    }
    if (!$loiQuiz) {
        $dung = [];
        foreach ($cauHoi as $ma => $ch) {
            $dung[$ma] = $traLoi[$ma] === $ch['dap_an'];
        }
        $_SESSION['hau_quiz'] = [
            'diem' => count(array_filter($dung)),
            'tong' => count($cauHoi),
            'dung' => $dung,
            'luc' => date('H:i d/m/Y'),
        ];
        chuyenHuong('gioithieu.php#php-quiz');
    }
}
$kqQuiz = is_array($_SESSION['hau_quiz'] ?? null) ? $_SESSION['hau_quiz'] : null;

$tieuDe = 'Trần Hồ Trung Hậu';
$trang = '';
require __DIR__ . '/../../inc/header.php';
?>
<main class="noi-dung">


        <h1 class="tieu-de-chinh">Giới thiệu thành viên: Trần Hồ Trung Hậu</h1>

<section class="the php-box" aria-labelledby="php-tieu-de">
  <h2 id="php-tieu-de">Trắc nghiệm PHP và ngày thi</h2>

  <h3 id="php-quiz">Trắc nghiệm nhanh về PHP</h3>
  <?php if ($kqQuiz): ?>
    <div role="status">
      <p class="tb-ok">Lần làm gần nhất (<?= e($kqQuiz['luc'] ?? '') ?>): đúng <strong><?= (int)($kqQuiz['diem'] ?? 0) ?>/<?= (int)($kqQuiz['tong'] ?? 0) ?></strong> câu.</p>
      <ul>
      <?php foreach ($cauHoi as $ma => $ch): ?>
        <li>Câu <?= (int)substr($ma, 1) ?>: <?= !empty($kqQuiz['dung'][$ma]) ? 'đúng' : 'sai' ?></li>
      <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <form method="post" action="gioithieu.php#php-quiz">
    <input type="hidden" name="chuc_nang" value="quiz">
    <?php foreach ($cauHoi as $ma => $ch): ?>
      <fieldset>
        <legend>Câu <?= (int)substr($ma, 1) ?>. <?= e($ch['noi_dung']) ?></legend>
        <?php foreach ($ch['lua_chon'] as $khoa => $nhan): ?>
          <p><label><input type="radio" name="<?= e($ma) ?>" value="<?= e($khoa) ?>"<?= ($traLoi[$ma] ?? '') === $khoa ? ' checked' : '' ?>> <?= e(strtoupper($khoa)) ?>. <?= e($nhan) ?></label></p>
        <?php endforeach; ?>
        <?php if (isset($loiQuiz[$ma])): ?><p class="loi" role="alert"><?= e($loiQuiz[$ma]) ?></p><?php endif; ?>
      </fieldset>
    <?php endforeach; ?>
    <button type="submit">Nộp bài</button>
  </form>

  <h3>Ghi nhớ ngày thi</h3>
  <?php if ($tbNgayThi): ?><p class="tb-ok" role="status"><?= e($tbNgayThi) ?></p><?php endif; ?>
  <?php if ($ngayThi !== null): ?>
    <p role="status">Ngày thi đã lưu: <strong><?= e($ngayThi->format('d/m/Y')) ?></strong> —
    <?php if ($soNgayCon === 0): ?>hôm nay là ngày thi!
    <?php elseif ($soNgayCon > 0): ?>còn <strong><?= (int)$soNgayCon ?></strong> ngày.
    <?php else: ?>đã qua <?= abs((int)$soNgayCon) ?> ngày.
    <?php endif; ?></p>
    <form method="post" action="gioithieu.php">
      <input type="hidden" name="chuc_nang" value="xoa-ngay-thi">
      <button type="submit">Xóa ngày thi đã lưu</button>
    </form>
  <?php endif; ?>
  <form method="post" action="gioithieu.php">
    <input type="hidden" name="chuc_nang" value="ngay-thi">
    <p>
      <label for="php-ngay-thi">Ngày thi</label><br>
      <input id="php-ngay-thi" name="ngay_thi" type="date" required value="<?= e($duNgayThi) ?>"<?= $loiNgayThi !== '' ? ' aria-invalid="true" aria-describedby="php-loi-ngay-thi"' : '' ?>>
    </p>
    <?php if ($loiNgayThi !== ''): ?><p class="loi" id="php-loi-ngay-thi"><?= e($loiNgayThi) ?></p><?php endif; ?>
    <button type="submit">Lưu ngày thi</button>
  </form>
</section>


        <!-- TIỆN ÍCH TƯƠNG TÁC CỦA HẬU -->
        <section class="the tien-ich-hau">
            <h2>🎮 Tiện ích tương tác &amp; Đếm ngược ngày thi</h2>

            <!-- Tính năng 1: Đếm ngược ngày thi -->
            <div class="khu-vuc-dem-nguoc">
                <div class="dong-chon-ngay">
                    <label for="input-ngay-thi" class="nhan-chon-ngay">📅 Chọn ngày thi giữa kỳ / cuối kỳ:</label>
                    <input type="date" id="input-ngay-thi" class="input-ngay">
                </div>
                <div id="ket-qua-dem-nguoc" class="ket-qua-dem-nguoc">
                    ⏳ Vui lòng chọn ngày thi trên lịch để bắt đầu đếm ngược.
                </div>
            </div>

            <hr class="duong-phan-cach">

            <!-- Tính năng 2: Bảng nút điều khiển Thả bọ & Linh vật Rồng -->
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
                <img src="images/chan-dung-hau.jpg"
                     alt="Ảnh chân dung Trần Hồ Trung Hậu"
                     width="200"
                     height="250" class="chan-dung__anh">

                <figcaption>
                    Chân dung Trần Hồ Trung Hậu - Thành viên Nhóm 07
                </figcaption>
            </figure>

            <h3>Danh sách kỹ năng cá nhân</h3>

            <ul class="danh-sach-ky-nang">
                <li>HTML5</li>
                <li>Sử dụng Git và GitHub</li>
                <li>Làm việc nhóm</li>
                <li>Lập trình web</li>
                <li>Lập trình ứng dụng</li>
                <li>Python</li>
            </ul>

            <!-- MỚI: DEVELOPER TERMINAL CLI MINI (Thay thế phần tìm kiếm cũ) -->
            <div class="terminal-container">
                <div class="terminal-header">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                    <span class="terminal-title">bash - hau@dev-console</span>
                </div>
                <div id="terminal-output" class="terminal-output">
                    <div><span class="prompt">hau@dev:~$</span> welcome</div>
                    <div style="color: #ffc928;">💻 Chào mừng bạn! Gõ <b style="color:#00ff00;">help</b> để xem danh sách câu lệnh.</div>
                </div>
                <div class="terminal-input-line">
                    <span class="prompt">hau@dev:~$</span>
                    <input type="text" id="terminal-input" placeholder="Gõ lệnh ở đây (vd: help, skills, projects)..." autocomplete="off">
                </div>
            </div>
        </section>

        <article class="the du-an-so-thich">
            <h2>Dự án và sở thích</h2>

            <p>
                Tôi tham gia thực hiện đồ án xây dựng website FastFood
                của Nhóm 07 (Trang giới thiệu).
                của Nhóm 07(Trang giới thiệu).
            </p>

            <h3>Sở thích</h3>

            <ul class="danh-sach-so-thich">
                <li>Ngủ</li>
                <li>Chơi cầu lông</li>
                <li>Chơi game</li>
                <li>Xem phim</li>
                <li>Đọc sách</li>
                <li>Tìm hiểu web</li>
            </ul>
        </article>

        <section class="the khu-thoi-khoa-bieu">
            <h2>Thời khóa biểu học tập trong tuần</h2>

            <table class="thoi-khoa-bieu">

                <caption>
                    Thời khóa biểu học tập của Trần Hồ Trung Hậu
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
