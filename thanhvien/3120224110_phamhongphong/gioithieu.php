<?php
// gioithieu.php - trang cá nhân của Phạm Hồng Phong (3120224110), Bài tập nhóm 5 - Phần B.
// Dùng chung inc/header.php và inc/footer.php của nhóm; giữ nguyên style.css và js/canhan.js riêng.
// Chức năng 1: ảnh đại diện tải lên an toàn (<= 2 MB, kiểm tra kiểu thật bằng finfo, tên ngẫu nhiên, uploads/).
// Chức năng 2: thống kê văn bản ở máy chủ (số ký tự, số từ, số câu, từ dài nhất, từ lặp nhiều), PRG.
// Cách thử: php -S localhost:8000, mở /thanhvien/3120224110_phamhongphong/gioithieu.php rồi tải ảnh lên.
require __DIR__ . '/../../inc/config.php';
$goc = '../../';
$lopTrang = 'trang-ca-nhan';
$cssRien = 'style.css';
$jsRien = 'js/canhan.js';

$lay = static fn(string $khoa): string => is_string($_POST[$khoa] ?? null) ? trim($_POST[$khoa]) : '';
$hanhDong = $_SERVER['REQUEST_METHOD'] === 'POST' ? $lay('chuc_nang') : '';
$thuMucAnh = THU_MUC_GOC . '/uploads';
$mauTenAnh = '/^phong_[a-f0-9]{24}\.(jpg|png|webp)$/';
$tuyChonCookie = ['path' => '/', 'httponly' => true, 'samesite' => 'Lax'];

// Tên ảnh đại diện hợp lệ trong cookie (đã kiểm tra mẫu tên và tệp còn tồn tại), hoặc null.
$anhHienTai = static function () use ($mauTenAnh, $thuMucAnh): ?string {
    $ten = $_COOKIE['phong_avatar'] ?? '';
    return (is_string($ten) && preg_match($mauTenAnh, $ten) === 1 && is_file($thuMucAnh . '/' . $ten)) ? $ten : null;
};

// ---------- Chức năng 1: ảnh đại diện ----------
$loiAnh = '';
$tongKichThuocQuaLon = $_SERVER['REQUEST_METHOD'] === 'POST' && !$_POST && !$_FILES
    && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;                 // vượt post_max_size thì PHP bỏ trống cả hai
if ($tongKichThuocQuaLon) {
    $loiAnh = 'Dữ liệu gửi lên quá lớn, ảnh tối đa 2 MB.';
} elseif ($hanhDong === 'avatar') {
    $f = $_FILES['avatar'] ?? null;
    $ma = (is_array($f) && is_int($f['error'] ?? null)) ? $f['error'] : UPLOAD_ERR_NO_FILE;
    if ($ma === UPLOAD_ERR_NO_FILE) {
        $loiAnh = 'Hãy chọn một ảnh để tải lên.';
    } elseif ($ma === UPLOAD_ERR_INI_SIZE || $ma === UPLOAD_ERR_FORM_SIZE) {
        $loiAnh = 'Ảnh lớn hơn 2 MB, hãy chọn ảnh nhỏ hơn.';
    } elseif ($ma !== UPLOAD_ERR_OK) {
        $loiAnh = 'Tải ảnh lên không thành công, bạn thử lại nhé.';
    } elseif (!is_string($f['tmp_name'] ?? null) || !is_uploaded_file($f['tmp_name'])) {
        $loiAnh = 'Tệp tải lên không hợp lệ.';
    } elseif ((int)($f['size'] ?? 0) > 2 * 1024 * 1024) {
        $loiAnh = 'Ảnh lớn hơn 2 MB, hãy chọn ảnh nhỏ hơn.';
    } else {
        $kieu = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);   // kiểu thật theo nội dung, không theo đuôi tên
        $duoi = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$kieu] ?? null;
        if ($duoi === null) {
            $loiAnh = 'Chỉ nhận ảnh JPG, PNG hoặc WebP.';
        } else {
            if (!is_dir($thuMucAnh)) mkdir($thuMucAnh, 0775, true);
            $tenMoi = 'phong_' . bin2hex(random_bytes(12)) . '.' . $duoi;
            if (move_uploaded_file($f['tmp_name'], $thuMucAnh . '/' . $tenMoi)) {
                $cu = $anhHienTai();
                if ($cu !== null) unlink($thuMucAnh . '/' . $cu);         // không để ảnh cũ chất đống
                setcookie('phong_avatar', $tenMoi, $tuyChonCookie + ['expires' => time() + 30 * 86400]);
                flash('phong_anh', 'Đã cập nhật ảnh đại diện.');
                chuyenHuong('gioithieu.php');
            }
            $loiAnh = 'Không lưu được ảnh lên máy chủ.';
        }
    }
} elseif ($hanhDong === 'xoa-avatar') {
    $cu = $anhHienTai();
    if ($cu !== null) unlink($thuMucAnh . '/' . $cu);
    setcookie('phong_avatar', '', $tuyChonCookie + ['expires' => time() - 3600]);
    flash('phong_anh', 'Đã quay về ảnh mặc định.');
    chuyenHuong('gioithieu.php');
}
$tenAnh = $anhHienTai();
$duongDanAnh = $tenAnh !== null ? '../../uploads/' . $tenAnh : 'images/chan-dung-phong.jpg';
$tbAnh = flash('phong_anh');

// ---------- Chức năng 2: thống kê văn bản ----------
$thuong = static fn(string $s): string => function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
$duVanBan = '';
$loiVanBan = '';
if ($hanhDong === 'thong-ke') {
    $duVanBan = str_replace("\r\n", "\n", $lay('van_ban'));
    if ($duVanBan === '') {
        $loiVanBan = 'Hãy nhập văn bản cần thống kê.';
    } elseif (preg_match('//u', $duVanBan) !== 1) {
        $loiVanBan = 'Văn bản chứa ký tự không hợp lệ (cần UTF-8).';
    } elseif (mb_strlen($duVanBan) > 1000) {
        $loiVanBan = 'Văn bản tối đa 1000 ký tự.';
    } else {
        preg_match_all('/[\p{L}\p{N}]+(?:[\'’-][\p{L}\p{N}]+)*/u', $duVanBan, $kq);
        $cacTu = $kq[0];
        $tanSo = array_count_values(array_map($thuong, $cacTu));
        arsort($tanSo);
        $tuDai = '';
        foreach ($cacTu as $tu) {
            if (mb_strlen($tu) > mb_strlen($tuDai)) $tuDai = $tu;
        }
        $_SESSION['phong_tk'] = [
            'ky_tu' => mb_strlen($duVanBan),
            'ky_tu_khong_cach' => mb_strlen((string)preg_replace('/\s+/u', '', $duVanBan)),
            'so_tu' => count($cacTu),
            'so_cau' => max(1, (int)preg_match_all('/[.!?…]+(?=\s|$)/u', $duVanBan)),
            'tu_dai_nhat' => $tuDai,
            'hay_gap' => array_slice(array_map(static fn($k, $v) => [(string)$k, $v], array_keys($tanSo), $tanSo), 0, 3),
        ];
        chuyenHuong('gioithieu.php#php-thong-ke');
    }
}
$kqThongKe = $_SESSION['phong_tk'] ?? null;                      // chỉ hiện đúng một lần
unset($_SESSION['phong_tk']);
if (!is_array($kqThongKe)) $kqThongKe = null;

$tieuDe = 'Phạm Hồng Phong';
$trang = '';
require __DIR__ . '/../../inc/header.php';
?>
<main class="noi-dung">


      <h1 class="tieu-de-chinh">Giới thiệu thành viên: Phạm Hồng Phong</h1>

<section class="the php-box" aria-labelledby="php-tieu-de">
  <h2 id="php-tieu-de">Ảnh đại diện và thống kê văn bản</h2>

  <h3>Ảnh đại diện</h3>
  <p><img src="<?= e($duongDanAnh) ?>" alt="Ảnh đại diện hiện tại" width="120" height="120"></p>
  <?php if ($tbAnh): ?><p class="tb-ok" role="status"><?= e($tbAnh) ?></p><?php endif; ?>
  <form method="post" action="gioithieu.php" enctype="multipart/form-data">
    <input type="hidden" name="chuc_nang" value="avatar">
    <p>
      <label for="php-avatar">Chọn ảnh JPG, PNG hoặc WebP (tối đa 2 MB)</label><br>
      <input id="php-avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" required<?= $loiAnh !== '' ? ' aria-invalid="true" aria-describedby="php-loi-anh"' : '' ?>>
    </p>
    <?php if ($loiAnh !== ''): ?><p class="loi" id="php-loi-anh"><?= e($loiAnh) ?></p><?php endif; ?>
    <button type="submit">Tải ảnh lên</button>
  </form>
  <?php if ($tenAnh !== null): ?>
    <form method="post" action="gioithieu.php">
      <input type="hidden" name="chuc_nang" value="xoa-avatar">
      <button type="submit">Dùng lại ảnh mặc định</button>
    </form>
  <?php endif; ?>

  <h3 id="php-thong-ke">Thống kê văn bản</h3>
  <?php if ($kqThongKe): ?>
    <div class="tb-ok" role="status">
      <p>Kết quả thống kê:</p>
      <ul>
        <li>Số ký tự: <?= (int)$kqThongKe['ky_tu'] ?> (không tính khoảng trắng: <?= (int)$kqThongKe['ky_tu_khong_cach'] ?>)</li>
        <li>Số từ: <?= (int)$kqThongKe['so_tu'] ?>; số câu: <?= (int)$kqThongKe['so_cau'] ?></li>
        <li>Từ dài nhất: <?= e($kqThongKe['tu_dai_nhat'] !== '' ? $kqThongKe['tu_dai_nhat'] : '(không có)') ?></li>
        <?php foreach ($kqThongKe['hay_gap'] as [$tu, $lan]): ?>
          <li>Từ gặp nhiều: “<?= e($tu) ?>” — <?= (int)$lan ?> lần</li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <form method="post" action="gioithieu.php#php-thong-ke">
    <input type="hidden" name="chuc_nang" value="thong-ke">
    <p>
      <label for="php-van-ban">Văn bản (tối đa 1000 ký tự)</label><br>
      <textarea id="php-van-ban" name="van_ban" rows="5" cols="40" maxlength="1000" required<?= $loiVanBan !== '' ? ' aria-invalid="true" aria-describedby="php-loi-van-ban"' : '' ?>><?= e($duVanBan) ?></textarea>
    </p>
    <?php if ($loiVanBan !== ''): ?><p class="loi" id="php-loi-van-ban"><?= e($loiVanBan) ?></p><?php endif; ?>
    <button type="submit">Thống kê</button>
  </form>
</section>


      <!-- BẢNG ĐIỀU KHIỂN TƯƠNG TÁC KÍNH LÚP VÀ LINH VẬT F-O-G -->
      <section class="the thanh-cong-cu-phong">
        <h2>🛠 Bảng điều khiển tương tác</h2>
        
        <!-- Chức năng Kính lúp soi phóng to chữ -->
        <div class="nhom-nut-chuc-nang">
          <button id="btn-kinh-lup" type="button" class="btn-tuong-tac">🔍 Bật Kính Lúp Soi Chữ</button>
        </div>

        <!-- Tương tác Linh vật Con Heo bộ 3 phím F - O - G -->
        <div class="khu-vuc-linh-vat-nut">
          <p class="tieu-de-nut-pet">🐷 Tương tác Linh vật Con Heo (Bấm nút bên dưới hoặc nhấn phím F - O - G):</p>
          <div class="nhom-3-nut-pet">
            <button id="btn-pet-f" type="button" class="btn-tuong-tac btn-pet">F (Phóng to Heo)</button>
            <button id="btn-pet-o" type="button" class="btn-tuong-tac btn-pet">O (Oai phong xoay tròn)</button>
            <button id="btn-pet-g" type="button" class="btn-tuong-tac btn-pet">G (Ghé góc màn hình)</button>
          </div>
        </div>
      </section>

      <section class="the gioi-thieu-ban-than">

        <h2>Giới thiệu bản thân</h2>

        <figure class="chan-dung">
          <img
            class="chan-dung__anh"
            src="images/chan-dung-phong.jpg"
            alt="Ảnh chân dung Phạm Hồng Phong"
            width="200"
            height="250">

          <figcaption>
            Chân dung Phạm Hồng Phong - Thành viên Nhóm 07
          </figcaption>
        </figure>

        <p>
          Xin chào! Tôi là Phạm Hồng Phong, sinh viên Khoa Toán - Tin,
          Trường Đại học Sư phạm - Đại học Đà Nẵng.
          Tôi đam mê phát triển phần mềm và thiết kế giao diện web tối ưu trải nghiệm người dùng.
        </p>

        <h3>Danh sách kỹ năng cá nhân</h3>

        <!-- Ô tìm kiếm kỹ năng -->
        <div class="khu-tim-kiem-ky-nang">
          <label for="o-tim-kiem-ky-nang" class="nhan-tim-kiem">🔍 Tìm kiếm kỹ năng:</label>
          <input
            type="text"
            id="o-tim-kiem-ky-nang"
            class="input-tim-kiem"
            placeholder="Nhập từ khóa (vd: HTML, Git, JS)...">
          <p id="thong-bao-ky-nang" class="thong-bao-tim-kiem" aria-live="polite"></p>
        </div>

        <ul class="danh-sach-ky-nang">
          <li>Lập trình web: HTML5, CSS3, JavaScript</li>
          <li>Quản lý nguồn mã: Git, GitHub</li>
          <li>Thiết kế giao diện Responsive và Tối ưu trải nghiệm (UI/UX)</li>
          <li>Kỹ năng giải quyết vấn đề và tư duy thuật toán</li>
        </ul>

      </section>

      <article class="the du-an-so-thich">

        <h2>Dự án &amp; Sở thích</h2>

        <p>
          Tôi đảm nhận xây dựng trang cá nhân và tham gia hoàn thiện giao diện đồ án FastFood của Nhóm 07.
        </p>

        <h3>Sở thích cá nhân</h3>

        <ul class="danh-sach-so-thich">
          <li>Chơi Game</li>
          <li>Xem Phim</li>
          <li>Chơi Thể Thao</li>
          <li>Nghe Nhạc thư giãn</li>
          <li>Khám phá các công nghệ mới</li>
        </ul>

      </article>

      <section class="the khu-thoi-khoa-bieu">

        <h2>Thời khóa biểu học tập trong tuần</h2>

        <table class="thoi-khoa-bieu">

          <caption>
            Bảng 1: Thời khóa biểu học tập các ngày trong tuần của Phạm Hồng Phong
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

      </section>

    
</main>
<?php require __DIR__ . '/../../inc/footer.php'; ?>
