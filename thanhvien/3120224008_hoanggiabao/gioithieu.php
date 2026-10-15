<?php
// gioithieu.php - trang cá nhân của Hoàng Gia Bảo (3120224008), Bài tập nhóm 5 - Phần B.
// Dùng chung inc/header.php và inc/footer.php của nhóm; giữ nguyên style.css và js/canhan.js riêng.
// Chức năng 1: sổ lưu bút - kiểm tra POST ở máy chủ, lưu storage/3120224008_luubut.jsonl, PRG.
// Chức năng 2: bộ đếm lượt xem - mỗi phiên (session) chỉ tính một lần, số đếm lưu trong storage/.
// Cách thử: php -S localhost:8000, mở /thanhvien/3120224008_hoanggiabao/gioithieu.php rồi gửi lời nhắn.
require __DIR__ . '/../../inc/config.php';
$goc = '../../';
$lopTrang = 'trang-ca-nhan';
$cssRien = 'style.css';
$jsRien = 'js/canhan.js';

$thuMucStorage = THU_MUC_GOC . '/storage';
$tepLuuBut = $thuMucStorage . '/3120224008_luubut.jsonl';
$tepDem = $thuMucStorage . '/3120224008_luotxem.txt';
if (!is_dir($thuMucStorage)) {
    mkdir($thuMucStorage, 0775, true);
}

// Đọc một ô POST dạng chuỗi đã cắt khoảng trắng; ô gửi dạng mảng (ten[]=x) coi như rỗng.
$lay = static fn(string $khoa): string => is_string($_POST[$khoa] ?? null) ? trim($_POST[$khoa]) : '';

// ---------- Chức năng 1: sổ lưu bút ----------
$du = ['ten' => '', 'loi_nhan' => ''];
$loi = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $lay('chuc_nang') === 'luu-but') {
    $du['ten'] = $lay('ten');
    $du['loi_nhan'] = str_replace("\r\n", "\n", $lay('loi_nhan'));

    if ($du['ten'] === '') {
        $loi['ten'] = 'Hãy nhập tên của bạn.';
    } elseif (mb_strlen($du['ten']) < 2 || mb_strlen($du['ten']) > 50) {
        $loi['ten'] = 'Tên phải từ 2 đến 50 ký tự.';
    } elseif (preg_match('/^[\p{L}\p{N}][\p{L}\p{N} ._\'-]*$/u', $du['ten']) !== 1) {
        $loi['ten'] = 'Tên chỉ gồm chữ, số, dấu cách và các ký tự . _ \' -';
    }

    if ($du['loi_nhan'] === '') {
        $loi['loi_nhan'] = 'Hãy nhập lời nhắn.';
    } elseif (mb_strlen($du['loi_nhan']) < 3 || mb_strlen($du['loi_nhan']) > 300) {
        $loi['loi_nhan'] = 'Lời nhắn phải từ 3 đến 300 ký tự.';
    } elseif (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $du['loi_nhan']) === 1) {
        $loi['loi_nhan'] = 'Lời nhắn chứa ký tự điều khiển không hợp lệ.';
    }

    if (!$loi) {
        $dong = json_encode(
            ['ten' => $du['ten'], 'loi_nhan' => $du['loi_nhan'], 'luc' => date('d/m/Y H:i')],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        if (file_put_contents($tepLuuBut, $dong . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
            $loi['chung'] = 'Không ghi được lời nhắn, bạn thử lại sau nhé.';
        } else {
            flash('bao_luubut', 'Đã lưu lời nhắn của bạn, cảm ơn!');
            chuyenHuong('gioithieu.php');          // PRG: bấm F5 không gửi lại
        }
    }
}
$tbLuuBut = flash('bao_luubut');

// Năm lời nhắn mới nhất (dòng cuối tệp là mới nhất)
$dsLoiNhan = [];
if (is_file($tepLuuBut)) {
    $cacDong = file($tepLuuBut, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach (array_reverse($cacDong) as $dong) {
        $m = json_decode($dong, true);
        if (is_array($m) && is_string($m['ten'] ?? null) && is_string($m['loi_nhan'] ?? null)) {
            $dsLoiNhan[] = $m;
            if (count($dsLoiNhan) === 5) break;
        }
    }
}

// ---------- Chức năng 2: bộ đếm lượt xem (mỗi session chỉ tính một lần) ----------
$luot = 0;
$fp = fopen($tepDem, 'c+');
if ($fp !== false) {
    if (flock($fp, LOCK_EX)) {                      // khóa để hai request không ghi đè nhau
        $luot = max(0, (int)trim((string)stream_get_contents($fp)));
        if (empty($_SESSION['da_dem_3120224008'])) {
            $luot++;
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, (string)$luot);
            fflush($fp);
            $_SESSION['da_dem_3120224008'] = true;
        }
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

$tieuDe = 'Hoàng Gia Bảo';
$trang = '';
require __DIR__ . '/../../inc/header.php';
?>
<main class="noi-dung">


    <h1 class="tieu-de-chinh">
      Giới thiệu cá nhân - Hoàng Gia Bảo
    </h1>

<section class="the php-box" aria-labelledby="php-tieu-de">
  <h2 id="php-tieu-de">Sổ lưu bút và lượt xem</h2>
  <p><strong>Lượt xem:</strong> <?= e(number_format($luot, 0, ',', '.')) ?> <small>(mỗi phiên chỉ tính một lần)</small></p>

  <h3>Để lại lời nhắn</h3>
  <?php if ($tbLuuBut): ?><p class="tb-ok" role="status"><?= e($tbLuuBut) ?></p><?php endif; ?>
  <?php if (isset($loi['chung'])): ?><p class="loi" role="alert"><?= e($loi['chung']) ?></p><?php endif; ?>
  <form method="post" action="gioithieu.php">
    <input type="hidden" name="chuc_nang" value="luu-but">
    <p>
      <label for="php-ten">Tên của bạn</label><br>
      <input id="php-ten" name="ten" type="text" maxlength="50" required value="<?= e($du['ten']) ?>"<?= isset($loi['ten']) ? ' aria-invalid="true" aria-describedby="php-loi-ten"' : '' ?>>
    </p>
    <?php if (isset($loi['ten'])): ?><p class="loi" id="php-loi-ten"><?= e($loi['ten']) ?></p><?php endif; ?>
    <p>
      <label for="php-loi-nhan">Lời nhắn (3 – 300 ký tự)</label><br>
      <textarea id="php-loi-nhan" name="loi_nhan" rows="4" cols="40" maxlength="300" required<?= isset($loi['loi_nhan']) ? ' aria-invalid="true" aria-describedby="php-loi-loi-nhan"' : '' ?>><?= e($du['loi_nhan']) ?></textarea>
    </p>
    <?php if (isset($loi['loi_nhan'])): ?><p class="loi" id="php-loi-loi-nhan"><?= e($loi['loi_nhan']) ?></p><?php endif; ?>
    <button type="submit">Gửi lời nhắn</button>
  </form>

  <h3>5 lời nhắn mới nhất</h3>
  <?php if (!$dsLoiNhan): ?>
    <p>Chưa có lời nhắn nào, hãy là người đầu tiên!</p>
  <?php else: ?>
    <ul>
    <?php foreach ($dsLoiNhan as $m): ?>
      <li><strong><?= e($m['ten']) ?></strong>: <?= nl2br(e($m['loi_nhan'])) ?> <small>(<?= e($m['luc'] ?? '') ?>)</small></li>
    <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>



    <!-- ===== THÔNG TIN BẢN THÂN ===== -->
    <section class="the gioi-thieu-ban-than">

      <h2>Thông tin bản thân</h2>

      <figure class="chan-dung">

        <img
          class="chan-dung__anh"
          src="images/chan-dung-bao.jpg"
          alt="Ảnh chân dung của Hoàng Gia Bảo"
          width="250"
          height="313"
        >

        <figcaption>
          Hoàng Gia Bảo - Nhóm trưởng Nhóm 07
        </figcaption>

      </figure>


      <p>
        Xin chào! Mình là
        <strong>Hoàng Gia Bảo</strong>,
        mã sinh viên
        <strong>3120224008</strong>,
        sinh viên Khoa Toán - Tin,
        Trường Đại học Sư phạm - Đại học Đà Nẵng.
      </p>

      <p>
        Mình là nhóm trưởng Nhóm 07 trong học phần
        Thiết kế và Lập trình Web.
        Nhóm mình đang thực hiện dự án website
        đặt đồ ăn nhanh Foodspead.
      </p>


      <h3>Kỹ năng</h3>

      <ul class="danh-sach-ky-nang">

        <li>HTML5</li>
        <li>CSS3</li>
        <li>PHP cơ bản</li>
        <li>Python</li>
        <li>Java</li>
        <li>C++</li>
        <li>Git và GitHub</li>
        <li>Visual Studio Code</li>

      </ul>

    </section>


    <!-- ===== DỰ ÁN VÀ SỞ THÍCH ===== -->
    <article class="the du-an-so-thich">

      <h2>Dự án Foodspead và sở thích</h2>


      <section>

        <h3>Dự án website Foodspead</h3>

        <p>
          Foodspead là website mô phỏng hệ thống đặt đồ ăn nhanh
          trực tuyến. Website giúp người dùng xem thực đơn,
          xem thông tin chi tiết món ăn, lựa chọn món
          và gửi thông tin đặt hàng.
        </p>

        <p>
          Các chức năng dự kiến gồm đăng ký, đăng nhập,
          quản lý giỏ hàng, đặt món, xem lịch sử đơn hàng
          và quản lý dữ liệu dành cho quản trị viên.
        </p>

      </section>


      <section>

        <h3>Sở thích cá nhân</h3>

        <ul class="danh-sach-so-thich">

          <li>Tìm hiểu về công nghệ</li>
          <li>Lập trình</li>
          <li>Máy tính</li>
          <li>Tìm hiểu các công cụ phát triển phần mềm</li>

        </ul>

      </section>


      <!-- ===== TODO LIST ===== -->
      <section class="khu-vuc-todo">

        <h3>📝 Mục tiêu học tập hôm nay</h3>

        <form id="form-todo">

          <label
            class="nhan-an"
            for="input-todo"
          >
            Nhập công việc cần làm
          </label>

          <input
            type="text"
            id="input-todo"
            placeholder="Nhập công việc..."
            autocomplete="off"
            required
          >

          <button
            type="submit"
            class="btn-them-todo"
          >
            Thêm
          </button>

        </form>


        <ul
          id="danh-sach-todo"
          aria-live="polite"
        ></ul>

      </section>

    </article>


    <!-- ===== MINI GAME ===== -->
    <section class="the khu-vuc-game">

      <h2>
        🕹️ Mini-game: Gấu Trúc Hứng FastFood
      </h2>

      <p>
        Dùng phím
        <strong>Mũi tên Trái / Phải (◄ / ►)</strong>
        để di chuyển Gấu trúc hứng bánh mì!
      </p>


      <!--
        role="group" giúp aria-label hợp lệ
        và sửa lỗi W3C Validator.
      -->
      <div
        id="game-container"
        tabindex="0"
        role="group"
        aria-label="Khu vực mini game"
      >

        <div id="game-score">
          Điểm:
          <span id="val-score">0</span>
        </div>

        <div
          id="game-burger"
          aria-hidden="true"
        >
          🍔
        </div>

        <div
          id="game-panda"
          aria-hidden="true"
        >
          🐼
        </div>

      </div>


      <button
        id="btn-start-game"
        class="btn-game-start"
        type="button"
      >
        🎮 Bắt đầu chơi
      </button>

    </section>


    <!-- ===== THỜI KHÓA BIỂU ===== -->
    <section
      class="the khu-thoi-khoa-bieu"
      tabindex="0"
      aria-label="Bảng thời khóa biểu có thể cuộn ngang trong khung"
    >

      <h2>
        Thời khóa biểu tuần
      </h2>


      <!-- Chỉ bảng được phép cuộn ngang -->
      <div class="bang-cuon">

        <table class="thoi-khoa-bieu">

          <caption>
            Thời khóa biểu học tập trong tuần của Hoàng Gia Bảo
          </caption>


          <thead>

            <tr>

              <th scope="col">
                Thứ
              </th>

              <th scope="col">
                Thứ Hai
              </th>

              <th scope="col">
                Thứ Ba
              </th>

              <th scope="col">
                Thứ Tư
              </th>

              <th scope="col">
                Thứ Năm
              </th>

              <th scope="col">
                Thứ Sáu
              </th>

            </tr>

          </thead>


          <tbody>

            <tr>

              <th scope="row">
                Môn
              </th>

              <td>
                An toàn thông tin
              </td>

              <td>
                Hệ quản trị cơ sở dữ liệu
              </td>

              <td>
                Khai phá dữ liệu / Công nghệ phần mềm
              </td>

              <td>
                Lập trình mạng
              </td>

              <td>
                Lập trình web / Lịch sử Đảng
              </td>

            </tr>

          </tbody>

        </table>

      </div>

    </section>


    <!-- ===== LIÊN KẾT ===== -->
    <section class="the">

      <h2>Liên kết</h2>

      <p>
        <a href="../../index.php">
          Quay về trang chủ Foodspead
        </a>
      </p>

      <p>
        <a href="../../gioi-thieu.php">
          Quay về trang giới thiệu Nhóm 07
        </a>
      </p>

    </section>

  
</main>
<?php require __DIR__ . '/../../inc/footer.php'; ?>
