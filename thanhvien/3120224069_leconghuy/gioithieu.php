<?php
// gioithieu.php - trang cá nhân của Lê Công Huy (3120224069), Bài tập nhóm 5 - Phần B.
// Dùng chung inc/header.php và inc/footer.php của nhóm; giữ nguyên style.css và js/canhan.js riêng.
// Chức năng 1: máy tính điểm học phần 0,2 x A1 + 0,3 x A2 + 0,5 x A3, kiểm tra điểm 0 - 10 ở máy chủ.
// Chức năng 2: danh sách mục tiêu học tập lưu trong session (thêm, xóa một mục, xóa hết), PRG.
// Cách thử: php -S localhost:8000, mở /thanhvien/3120224069_leconghuy/gioithieu.php rồi nhập điểm.
require __DIR__ . '/../../inc/config.php';
$goc = '../../';
$lopTrang = 'trang-ca-nhan';
$cssRien = 'style.css';
$jsRien = 'js/canhan.js';

$lay = static fn(string $khoa): string => is_string($_POST[$khoa] ?? null) ? trim($_POST[$khoa]) : '';
$hanhDong = $_SERVER['REQUEST_METHOD'] === 'POST' ? $lay('chuc_nang') : '';

// ---------- Chức năng 1: máy tính điểm học phần ----------
$xepLoai = static function (float $diem): string {
    return match (true) {
        $diem >= 8.5 => 'A',
        $diem >= 8.0 => 'B+',
        $diem >= 7.0 => 'B',
        $diem >= 6.5 => 'C+',
        $diem >= 5.5 => 'C',
        $diem >= 5.0 => 'D+',
        $diem >= 4.0 => 'D',
        default => 'F',
    };
};
$nhanDiem = ['a1' => 'A1', 'a2' => 'A2', 'a3' => 'A3'];
$du = ['a1' => '', 'a2' => '', 'a3' => ''];
$loiGpa = [];
if ($hanhDong === 'gpa') {
    $diem = [];
    foreach ($nhanDiem as $khoa => $nhan) {
        $du[$khoa] = $lay($khoa);
        $chuan = str_replace(',', '.', $du[$khoa]);          // chấp nhận cả 7,5 lẫn 7.5
        $so = filter_var($chuan, FILTER_VALIDATE_FLOAT);
        if ($chuan === '') {
            $loiGpa[$khoa] = "Hãy nhập điểm $nhan.";
        } elseif (mb_strlen($chuan) > 6 || $so === false) {
            $loiGpa[$khoa] = "Điểm $nhan phải là một số (ví dụ 7.5).";
        } elseif ($so < 0 || $so > 10) {
            $loiGpa[$khoa] = "Điểm $nhan phải nằm trong khoảng từ 0 đến 10.";
        } else {
            $diem[$khoa] = (float)$so;
        }
    }
    if (!$loiGpa) {
        $tong = round(0.2 * $diem['a1'] + 0.3 * $diem['a2'] + 0.5 * $diem['a3'], 2);
        $_SESSION['huy_kq_gpa'] = $diem + ['tong' => $tong, 'loai' => $xepLoai($tong)];
        chuyenHuong('gioithieu.php#php-diem');
    }
}
$kqGpa = $_SESSION['huy_kq_gpa'] ?? null;                      // chỉ hiện đúng một lần
unset($_SESSION['huy_kq_gpa']);
if (!is_array($kqGpa)) $kqGpa = null;

// ---------- Chức năng 2: mục tiêu học tập trong session ----------
$dsMucTieu = [];
foreach (is_array($_SESSION['huy_ds_muc_tieu'] ?? null) ? $_SESSION['huy_ds_muc_tieu'] : [] as $m) {
    if (is_array($m) && is_string($m['id'] ?? null) && is_string($m['nd'] ?? null)) $dsMucTieu[] = $m;
}
const TOI_DA_MUC_TIEU = 8;
$duMucTieu = '';
$loiMucTieu = '';
if ($hanhDong === 'them-muc-tieu') {
    $duMucTieu = preg_replace('/\s+/u', ' ', $lay('muc_tieu')) ?? '';
    $trung = false;
    foreach ($dsMucTieu as $m) {
        if (mb_strtolower($m['nd']) === mb_strtolower($duMucTieu)) $trung = true;
    }
    if ($duMucTieu === '') {
        $loiMucTieu = 'Hãy nhập mục tiêu.';
    } elseif (mb_strlen($duMucTieu) < 3 || mb_strlen($duMucTieu) > 120) {
        $loiMucTieu = 'Mục tiêu phải từ 3 đến 120 ký tự.';
    } elseif (count($dsMucTieu) >= TOI_DA_MUC_TIEU) {
        $loiMucTieu = 'Chỉ lưu tối đa ' . TOI_DA_MUC_TIEU . ' mục tiêu, hãy xóa bớt trước.';
    } elseif ($trung) {
        $loiMucTieu = 'Mục tiêu này đã có trong danh sách.';
    } else {
        $dsMucTieu[] = ['id' => bin2hex(random_bytes(4)), 'nd' => $duMucTieu];
        $_SESSION['huy_ds_muc_tieu'] = $dsMucTieu;
        flash('huy_mt', 'Đã thêm mục tiêu.');
        chuyenHuong('gioithieu.php#php-muc-tieu');
    }
} elseif ($hanhDong === 'xoa-muc-tieu') {
    $id = $lay('id');
    $con = array_values(array_filter($dsMucTieu, static fn(array $m): bool => $m['id'] !== $id));
    if (preg_match('/^[a-f0-9]{8}$/', $id) !== 1 || count($con) === count($dsMucTieu)) {
        flash('huy_mt', 'Không tìm thấy mục tiêu cần xóa.');
    } else {
        $_SESSION['huy_ds_muc_tieu'] = $con;
        flash('huy_mt', 'Đã xóa mục tiêu.');
    }
    chuyenHuong('gioithieu.php#php-muc-tieu');
} elseif ($hanhDong === 'xoa-het-muc-tieu') {
    unset($_SESSION['huy_ds_muc_tieu']);
    flash('huy_mt', 'Đã xóa toàn bộ mục tiêu.');
    chuyenHuong('gioithieu.php#php-muc-tieu');
}
$tbMucTieu = flash('huy_mt');

$tieuDe = 'Lê Công Huy';
$trang = '';
require __DIR__ . '/../../inc/header.php';
?>
<main class="noi-dung">


        <h1 class="tieu-de-chinh">Giới thiệu thành viên: Lê Công Huy</h1>

<section class="the php-box" aria-labelledby="php-tieu-de">
  <h2 id="php-tieu-de">Tính điểm học phần và mục tiêu học tập</h2>

  <h3 id="php-diem">Máy tính điểm học phần</h3>
  <p>Công thức: 0,2 × A1 + 0,3 × A2 + 0,5 × A3 (mỗi điểm từ 0 đến 10).</p>
  <?php if ($kqGpa): ?>
    <p class="tb-ok" role="status">Với A1 = <?= e($kqGpa['a1'] ?? '') ?>, A2 = <?= e($kqGpa['a2'] ?? '') ?>, A3 = <?= e($kqGpa['a3'] ?? '') ?>:
      điểm học phần là <strong><?= e(number_format((float)($kqGpa['tong'] ?? 0), 2)) ?></strong>, xếp loại <strong><?= e($kqGpa['loai'] ?? '') ?></strong>.</p>
  <?php endif; ?>
  <form method="post" action="gioithieu.php#php-diem">
    <input type="hidden" name="chuc_nang" value="gpa">
    <?php foreach ($nhanDiem as $khoa => $nhan): ?>
      <p>
        <label for="php-<?= e($khoa) ?>">Điểm <?= e($nhan) ?></label><br>
        <input id="php-<?= e($khoa) ?>" name="<?= e($khoa) ?>" type="text" inputmode="decimal" maxlength="6" required value="<?= e($du[$khoa]) ?>"<?= isset($loiGpa[$khoa]) ? ' aria-invalid="true" aria-describedby="php-loi-' . e($khoa) . '"' : '' ?>>
      </p>
      <?php if (isset($loiGpa[$khoa])): ?><p class="loi" id="php-loi-<?= e($khoa) ?>"><?= e($loiGpa[$khoa]) ?></p><?php endif; ?>
    <?php endforeach; ?>
    <button type="submit">Tính điểm</button>
  </form>

  <h3 id="php-muc-tieu">Mục tiêu học tập (lưu trong session)</h3>
  <?php if ($tbMucTieu): ?><p class="tb-ok" role="status"><?= e($tbMucTieu) ?></p><?php endif; ?>
  <form method="post" action="gioithieu.php#php-muc-tieu">
    <input type="hidden" name="chuc_nang" value="them-muc-tieu">
    <p>
      <label for="php-mt">Mục tiêu mới (3 – 120 ký tự)</label><br>
      <input id="php-mt" name="muc_tieu" type="text" maxlength="120" required value="<?= e($duMucTieu) ?>"<?= $loiMucTieu !== '' ? ' aria-invalid="true" aria-describedby="php-loi-mt"' : '' ?>>
    </p>
    <?php if ($loiMucTieu !== ''): ?><p class="loi" id="php-loi-mt"><?= e($loiMucTieu) ?></p><?php endif; ?>
    <button type="submit">Thêm mục tiêu</button>
  </form>
  <?php if (!$dsMucTieu): ?>
    <p>Chưa có mục tiêu nào.</p>
  <?php else: ?>
    <ul>
    <?php foreach ($dsMucTieu as $m): ?>
      <li>
        <?= e($m['nd']) ?>
        <form method="post" action="gioithieu.php#php-muc-tieu" style="display:inline">
          <input type="hidden" name="chuc_nang" value="xoa-muc-tieu">
          <input type="hidden" name="id" value="<?= e($m['id']) ?>">
          <button type="submit" aria-label="Xóa mục tiêu: <?= e($m['nd']) ?>">Xóa</button>
        </form>
      </li>
    <?php endforeach; ?>
    </ul>
    <form method="post" action="gioithieu.php#php-muc-tieu">
      <input type="hidden" name="chuc_nang" value="xoa-het-muc-tieu">
      <button type="submit">Xóa hết mục tiêu</button>
    </form>
  <?php endif; ?>
</section>


        <!-- TIỆN ÍCH CÁ NHÂN LÊ CÔNG HUY -->
        <section class="the tien-ich-huy">
            <h2>🎧 Tiện ích học tập &amp; Linh vật Gấu trúc</h2>

            <!-- Tính năng 1: Bộ phát nhạc -->
            <div class="khu-vuc-lofi">
                <h3>🎵 Bộ phát âm thanh nền tập trung học tập</h3>

                <div class="bo-phat-nhac">
                    <button id="btn-phat-nhac"
                            type="button"
                            class="btn-lofi">
                        ▶️ Bật âm thanh thư giãn (White Noise)
                    </button>

                    <div class="dieu-chinh-am-luong">
                        <label for="thanh-am-luong">🔊 Âm lượng:</label>

                        <input type="range"
                               id="thanh-am-luong"
                               min="0"
                               max="1"
                               step="0.1"
                               value="0.5">
                    </div>
                </div>

                <p id="trang-thai-nhac" class="ghi-chu-lofi">
                    Âm thanh đang tắt.
                </p>
            </div>

            <hr class="duong-phan-cach">

            <!-- Tính năng 2: Bộ tính điểm GPA -->
            <div class="khu-vuc-gpa">
                <h3>🧮 Máy tính điểm môn học dự kiến (Hệ 10 &amp; Hệ 4)</h3>

                <form id="form-tinh-gpa"
                      class="form-gpa"
                      onsubmit="return false;">

                    <div class="hang-nhap">
                        <label for="diem-chuyen-can">
                            Điểm chuyên cần (10%):
                        </label>

                        <input type="number"
                               id="diem-chuyen-can"
                               min="0"
                               max="10"
                               step="0.1"
                               placeholder="Ví dụ: 9">
                    </div>

                    <div class="hang-nhap">
                        <label for="diem-giua-ky">
                            Điểm giữa kỳ (30%):
                        </label>

                        <input type="number"
                               id="diem-giua-ky"
                               min="0"
                               max="10"
                               step="0.1"
                               placeholder="Ví dụ: 8">
                    </div>

                    <div class="hang-nhap">
                        <label for="diem-cuoi-ky">
                            Điểm cuối kỳ (60%):
                        </label>

                        <input type="number"
                               id="diem-cuoi-ky"
                               min="0"
                               max="10"
                               step="0.1"
                               placeholder="Ví dụ: 8.5">
                    </div>

                    <button id="btn-tinh-gpa"
                            type="button"
                            class="btn-tinh">
                        Tính kết quả
                    </button>
                </form>

                <div id="ket-qua-gpa" class="ket-qua-gpa-box">
                    Nhập điểm các thành phần để xem điểm tổng kết môn!
                </div>
            </div>

            <hr class="duong-phan-cach">

            <!-- Bổ sung: Điều khiển Gấu trúc -->
            <div class="khu-vuc-tro-choi">
                <h3>🐼 Điều khiển Linh vật Gấu trúc</h3>

                <p class="mo-ta-tro-choi">
                    Dùng <strong>4 phím mũi tên (⬅️ ➡️ ⬆️ ⬇️)</strong>
                    để di chuyển Gấu trúc trên màn hình hoặc nhấn các phím tương ứng:
                </p>

                <div class="nhom-nut-chuc-nang">
                    <button id="btn-panda-h"
                            type="button"
                            class="btn-tuong-tac btn-panda">
                        H (Chào hỏi 👋)
                    </button>

                    <button id="btn-panda-u"
                            type="button"
                            class="btn-tuong-tac btn-panda">
                        U (Lời chúc ✨)
                    </button>

                    <button id="btn-panda-y"
                            type="button"
                            class="btn-tuong-tac btn-panda">
                        Y (Động lực 💪)
                    </button>
                </div>
            </div>
        </section>

        <!-- GIỚI THIỆU BẢN THÂN -->
        <section class="the gioi-thieu-ban-than">
            <h2>Giới thiệu bản thân</h2>

            <figure class="chan-dung">
                <img src="images/chan-dung-huy.jpg"
                     alt="Ảnh chân dung Lê Công Huy"
                     width="200"
                     height="271"
                     class="chan-dung__anh">

                <figcaption>
                    Chân dung Lê Công Huy - Thành viên Nhóm 07
                </figcaption>
            </figure>

            <p>
                <strong>Họ và tên:</strong> Lê Công Huy
            </p>

            <p>
                <strong>Mã sinh viên:</strong> 3120224069
            </p>

            <p>
                Xin chào! Tôi là Lê Công Huy, sinh viên Khoa Toán - Tin,
                Trường Đại học Sư phạm - Đại học Đà Nẵng. Tôi có niềm đam mê lớn
                với công nghệ thông tin và thiết kế giao diện web.
            </p>

            <h3>Danh sách kỹ năng cá nhân</h3>

            <ul class="danh-sach-ky-nang">
                <li>Lập trình web nền tảng: HTML5, CSS3, JavaScript</li>
                <li>Tối ưu hóa mã nguồn và cấu trúc ngữ nghĩa HTML</li>
                <li>Sử dụng thành thạo Git và quản lý kho lưu trữ GitHub</li>
                <li>Kỹ năng giải quyết vấn đề và làm việc nhóm</li>
            </ul>
        </section>

        <!-- DỰ ÁN VÀ SỞ THÍCH -->
        <article class="the du-an-so-thich">
            <h2>Đóng góp trong dự án Foodspead &amp; Sở thích</h2>

            <p>
                Trong đồ án xây dựng website đặt đồ ăn nhanh Foodspead, tôi đảm nhận
                việc biên soạn và tối ưu hóa file <code>danh-sach.php</code>
                (Trang Thực đơn), đảm bảo cấu trúc bảng chuẩn accessibility và validation.
            </p>

            <h3>Sở thích</h3>

            <ul class="danh-sach-so-thich">
                <li>Chơi game</li>
                <li>Đọc truyện anime</li>
                <li>Nghe nhạc</li>
            </ul>
        </article>

        <!-- THỜI KHÓA BIỂU -->
        <section class="the khu-thoi-khoa-bieu">
            <h2>Thời khóa biểu học tập trong tuần</h2>

            <div class="khung-bang">
                <table class="thoi-khoa-bieu">

                    <caption>
                        Thời khóa biểu học tập của Lê Công Huy
                    </caption>

                    <thead>
                        <tr>
                            <th scope="col">Buổi</th>
                            <th scope="col">Thứ 2</th>
                            <th scope="col">Thứ 3</th>
                            <th scope="col">Thứ 4</th>
                            <th scope="col">Thứ 5</th>
                            <th scope="col">Thứ 6</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <th scope="row">Sáng</th>
                            <td>Lập trình Web</td>
                            <td>Chủ nghĩa XH-KH</td>
                            <td>Lập trình Web</td>
                            <td>Tự học HTML/CSS</td>
                            <td>Thực hành Web</td>
                        </tr>

                        <tr>
                            <th scope="row">Chiều</th>
                            <td>Tự học Java</td>
                            <td>Thể dục</td>
                            <td>Học Tiếng Anh</td>
                            <td>Kinh tế chính trị</td>
                            <td>Họp Nhóm 07</td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </section>

    
</main>
<?php require __DIR__ . '/../../inc/footer.php'; ?>
