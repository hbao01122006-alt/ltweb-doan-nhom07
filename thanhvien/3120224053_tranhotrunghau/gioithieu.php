<?php
// gioithieu.php - Trang cá nhân của Trần Hồ Trung Hậu, chứa hai chức năng CSDL MySQL theo yêu cầu bài tập.
// Chức năng CSDL 1: trắc nghiệm PHP lấy câu hỏi từ MySQL và lưu lịch sử kết quả vào bảng tv_tran_ho_trung_hau_*.
// Chức năng CSDL 2: ngày thi lưu trong MySQL; chỉ chủ trang hoặc quản trị được thêm, sửa, xóa; POST thành công dùng PRG.
// Cách thử: làm quiz rồi F5; đăng nhập hau@foods.example / matkhau123 để đổi ngày thi; dữ liệu in ra đều qua e().
require __DIR__ . '/../../inc/config.php';
$goc = '../../';
$lopTrang = 'trang-ca-nhan';
$cssRien = 'style.css';
$jsRien = 'js/canhan.js';
const MSSV = '3120224053';

date_default_timezone_set('Asia/Ho_Chi_Minh');
$lay = static fn(string $khoa): string => is_string($_POST[$khoa] ?? null) ? trim($_POST[$khoa]) : '';
$hanhDong = $_SERVER['REQUEST_METHOD'] === 'POST' ? $lay('chuc_nang') : '';
$nd = nguoiDung();
$laChuTrang = (new App\Data\KhoThanhVien(db()))->laChuTrang(MSSV, $nd ? (int)$nd['id'] : null) || ($nd['vai_tro'] ?? '') === 'quan_tri';
$homNay = new DateTimeImmutable('today');
$docNgay = static function (string $chuoi): ?DateTimeImmutable {
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $chuoi);
    return ($d !== false && $d->format('Y-m-d') === $chuoi) ? $d : null;
};

// Chức năng 1: câu hỏi trắc nghiệm của Bài 5 được chuyển từ mảng PHP sang bảng MySQL.
$st = db()->prepare('SELECT id, noi_dung, lua_chon_a, lua_chon_b, lua_chon_c, lua_chon_d, dap_an_dung
                     FROM tv_tran_ho_trung_hau_cau_hoi WHERE mssv = ? ORDER BY id');
$st->execute([MSSV]);
$cauHoi = $st->fetchAll();
$traLoi = [];
$loiQuiz = [];
if ($hanhDong === 'quiz') {
    foreach ($cauHoi as $ch) {
        $id = (int)$ch['id'];
        $khoa = 'q_' . $id;
        $v = is_string($_POST[$khoa] ?? null) ? $_POST[$khoa] : '';
        if (!in_array($v, ['a', 'b', 'c', 'd'], true)) $loiQuiz[$id] = 'Hãy chọn một đáp án.';
        else $traLoi[$id] = $v;
    }
    if (!$loiQuiz && $cauHoi) {
        $diem = 0;
        foreach ($cauHoi as $ch) {
            if (($traLoi[(int)$ch['id']] ?? '') === $ch['dap_an_dung']) $diem++;
        }
        $st = db()->prepare('INSERT INTO tv_tran_ho_trung_hau_ket_qua (mssv, diem, tong) VALUES (?, ?, ?)');
        $st->execute([MSSV, $diem, count($cauHoi)]);
        flash('hau_quiz', "Đã lưu kết quả: $diem/" . count($cauHoi) . ' câu đúng.');
        chuyenHuong('gioithieu.php#php-quiz');
    }
}
$tbQuiz = flash('hau_quiz');
$st = db()->prepare('SELECT diem, tong, thoi_gian FROM tv_tran_ho_trung_hau_ket_qua WHERE mssv = ? ORDER BY thoi_gian DESC, id DESC LIMIT 5');
$st->execute([MSSV]);
$lichSuQuiz = $st->fetchAll();

// Chức năng 2: ngày thi của Bài 5 được chuyển từ cookie sang MySQL.
$duNgayThi = '';
$loiNgayThi = '';
if ($hanhDong === 'ngay-thi' || $hanhDong === 'xoa-ngay-thi') {
    if (!$laChuTrang) {
        http_response_code(403);
        require __DIR__ . '/../../403.php';
        exit;
    }
    if ($hanhDong === 'xoa-ngay-thi') {
        $st = db()->prepare('DELETE FROM tv_tran_ho_trung_hau_ngay_thi WHERE mssv = ?');
        $st->execute([MSSV]);
        flash('hau_thi', 'Đã xóa ngày thi.');
        chuyenHuong('gioithieu.php#php-ngay-thi');
    }
    $duNgayThi = $lay('ngay_thi');
    $dt = $docNgay($duNgayThi);
    if ($duNgayThi === '') $loiNgayThi = 'Hãy chọn ngày thi.';
    elseif ($dt === null) $loiNgayThi = 'Ngày thi không hợp lệ.';
    elseif ($dt < $homNay) $loiNgayThi = 'Ngày thi phải từ hôm nay trở đi.';
    elseif ($dt > $homNay->modify('+2 years')) $loiNgayThi = 'Chỉ nhận ngày thi trong vòng 2 năm tới.';
    else {
        $st = db()->prepare('INSERT INTO tv_tran_ho_trung_hau_ngay_thi (mssv, ngay_thi) VALUES (?, ?)
                             ON DUPLICATE KEY UPDATE ngay_thi = VALUES(ngay_thi), cap_nhat = CURRENT_TIMESTAMP');
        $st->execute([MSSV, $duNgayThi]);
        flash('hau_thi', 'Đã lưu ngày thi vào CSDL.');
        chuyenHuong('gioithieu.php#php-ngay-thi');
    }
}
$st = db()->prepare('SELECT ngay_thi FROM tv_tran_ho_trung_hau_ngay_thi WHERE mssv = ?');
$st->execute([MSSV]);
$ngayThiChuoi = $st->fetchColumn();
$ngayThi = is_string($ngayThiChuoi) ? $docNgay($ngayThiChuoi) : null;
$soNgayCon = $ngayThi ? (int)$homNay->diff($ngayThi)->format('%r%a') : null;
$tbNgayThi = flash('hau_thi');

$tieuDe = 'Trần Hồ Trung Hậu';
$trang = '';
require __DIR__ . '/../../inc/header.php';
?>
<main class="noi-dung">
<h1 class="tieu-de-chinh">Giới thiệu thành viên: Trần Hồ Trung Hậu</h1>
<section class="the php-box" aria-labelledby="php-tieu-de">
  <h2 id="php-tieu-de">Trắc nghiệm và ngày thi bằng MySQL</h2>
  <h3 id="php-quiz">1. Trắc nghiệm PHP từ CSDL</h3>
  <?php if ($tbQuiz): ?><p class="tb-ok" role="status"><?= e($tbQuiz) ?></p><?php endif; ?>
  <?php if (!$cauHoi): ?><p>Chưa có câu hỏi trong CSDL.</p><?php else: ?>
  <form method="post" action="gioithieu.php#php-quiz">
    <input type="hidden" name="chuc_nang" value="quiz">
    <?php foreach ($cauHoi as $stt => $ch): $id=(int)$ch['id']; ?>
      <fieldset>
        <legend>Câu <?= e((string)($stt + 1)) ?>. <?= e($ch['noi_dung']) ?></legend>
        <?php foreach (['a','b','c','d'] as $k): $cot='lua_chon_'.$k; ?>
          <p><label><input type="radio" name="q_<?= e((string)$id) ?>" value="<?= e($k) ?>"<?= ($traLoi[$id] ?? '') === $k ? ' checked' : '' ?>> <?= e(strtoupper($k)) ?>. <?= e($ch[$cot]) ?></label></p>
        <?php endforeach; ?>
        <?php if (isset($loiQuiz[$id])): ?><p class="loi" role="alert"><?= e($loiQuiz[$id]) ?></p><?php endif; ?>
      </fieldset>
    <?php endforeach; ?>
    <button type="submit">Nộp bài và lưu kết quả</button>
  </form>
  <?php endif; ?>
  <h4>5 lần làm gần nhất</h4>
  <?php if (!$lichSuQuiz): ?><p>Chưa có kết quả.</p><?php else: ?><ul>
    <?php foreach ($lichSuQuiz as $r): ?><li><?= e($r['thoi_gian']) ?>: <?= e($r['diem']) ?>/<?= e($r['tong']) ?> câu đúng</li><?php endforeach; ?>
  </ul><?php endif; ?>

  <h3 id="php-ngay-thi">2. Ngày thi lưu trong CSDL</h3>
  <?php if ($tbNgayThi): ?><p class="tb-ok" role="status"><?= e($tbNgayThi) ?></p><?php endif; ?>
  <?php if ($ngayThi): ?><p>Ngày thi đang lưu: <strong><?= e($ngayThi->format('d/m/Y')) ?></strong> — còn <?= e((string)$soNgayCon) ?> ngày.</p><?php else: ?><p>Chưa lưu ngày thi.</p><?php endif; ?>
  <?php if ($laChuTrang): ?>
  <form method="post" action="gioithieu.php#php-ngay-thi">
    <input type="hidden" name="chuc_nang" value="ngay-thi">
    <p><label for="php-ngay-thi-input">Ngày thi</label><br><input id="php-ngay-thi-input" name="ngay_thi" type="date" required value="<?= e($duNgayThi !== '' ? $duNgayThi : ($ngayThi ? $ngayThi->format('Y-m-d') : '')) ?>"></p>
    <?php if ($loiNgayThi): ?><p class="loi" role="alert"><?= e($loiNgayThi) ?></p><?php endif; ?>
    <button type="submit">Lưu ngày thi</button>
  </form>
  <?php if ($ngayThi): ?><form method="post" action="gioithieu.php#php-ngay-thi"><input type="hidden" name="chuc_nang" value="xoa-ngay-thi"><button type="submit">Xóa ngày thi</button></form><?php endif; ?>
  <?php else: ?><p><small>Đăng nhập đúng tài khoản chủ trang để thay đổi ngày thi.</small></p><?php endif; ?>
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
