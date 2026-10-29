<?php
// gioithieu.php - Trang cá nhân của Hoàng Gia Bảo, chứa hai chức năng CSDL MySQL theo yêu cầu bài tập.
// Chức năng CSDL 1: sổ lưu bút MySQL có phân trang; chủ trang hoặc quản trị mới được xóa; POST dùng PRG.
// Chức năng CSDL 2: bộ đếm lượt xem theo ngày bằng INSERT ... ON DUPLICATE KEY UPDATE và bảng 7 ngày gần nhất.
// Cách thử: gửi lời nhắn rồi F5; ?trang=2; đăng nhập bao@foods.example / matkhau123 để thấy nút Xóa.
require __DIR__ . '/../../inc/config.php';
$goc = '../../';
$lopTrang = 'trang-ca-nhan';
$cssRien = 'style.css';
$jsRien = 'js/canhan.js';

const MSSV = '3120224008';
$nd = nguoiDung();
$laChuTrang = (new App\Data\KhoThanhVien(db()))->laChuTrang(MSSV, $nd ? (int) $nd['id'] : null) || ($nd['vai_tro'] ?? '') === 'quan_tri';

// Đọc một ô POST dạng chuỗi đã cắt khoảng trắng; ô gửi dạng mảng (ten[]=x) coi như rỗng.
$lay = static fn(string $khoa): string => is_string($_POST[$khoa] ?? null) ? trim($_POST[$khoa]) : '';

// ---------- Chức năng 1: sổ lưu bút ----------
$du = ['ten' => '', 'loi_nhan' => ''];
$loi = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $lay('chuc_nang') === 'xoa') {
    $idXoa = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    if (!$laChuTrang) {
        http_response_code(403);
        require __DIR__ . '/../../403.php';
        exit;
    }
    if ($idXoa) {
        db()->prepare('DELETE FROM tv_hoang_gia_bao WHERE id = ? AND mssv = ?')->execute([$idXoa, MSSV]);
        flash('bao_luubut', 'Đã xóa lời nhắn.');
    }
    chuyenHuong('gioithieu.php');
}
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
        db()->prepare('INSERT INTO tv_hoang_gia_bao (mssv, ten_khach, loi_nhan) VALUES (?, ?, ?)')
            ->execute([MSSV, $du['ten'], $du['loi_nhan']]);
        flash('bao_luubut', 'Đã lưu lời nhắn của bạn, cảm ơn!');
        chuyenHuong('gioithieu.php');          // PRG: bấm F5 không gửi lại
    }
}
$tbLuuBut = flash('bao_luubut');

// Năm lời nhắn mỗi trang, mới nhất trước
$st = db()->prepare('SELECT COUNT(*) FROM tv_hoang_gia_bao WHERE mssv = ?');
$st->execute([MSSV]);
$soTrang = max(1, (int) ceil($st->fetchColumn() / 5));
$hienTai = filter_var($_GET['trang'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);
$hienTai = min((int) $hienTai, $soTrang);
$st = db()->prepare('SELECT id, ten_khach, loi_nhan, thoi_gian FROM tv_hoang_gia_bao WHERE mssv = ?
                     ORDER BY thoi_gian DESC, id DESC LIMIT 5 OFFSET ?');
$st->execute([MSSV, ($hienTai - 1) * 5]);
$dsLoiNhan = $st->fetchAll();

// ---------- Chức năng 2: lượt xem theo ngày (mỗi session chỉ tính một lần) ----------
if (empty($_SESSION['da_dem_' . MSSV])) {
    db()->prepare('INSERT INTO tv_luot_xem (mssv, ngay, so_luot) VALUES (?, CURDATE(), 1)
                   ON DUPLICATE KEY UPDATE so_luot = so_luot + 1')->execute([MSSV]);
    $_SESSION['da_dem_' . MSSV] = true;
}
$st = db()->prepare('SELECT ngay, so_luot FROM tv_luot_xem WHERE mssv = ? ORDER BY ngay DESC LIMIT 7');
$st->execute([MSSV]);
$bay7Ngay = $st->fetchAll();
$st = db()->prepare('SELECT COALESCE(SUM(so_luot), 0) FROM tv_luot_xem WHERE mssv = ?');
$st->execute([MSSV]);
$luot = (int) $st->fetchColumn();

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
  <?php if ($bay7Ngay): ?>
  <table><caption>Lượt xem 7 ngày gần nhất</caption><thead><tr><th scope="col">Ngày</th><th scope="col">Lượt</th></tr></thead><tbody>
    <?php foreach ($bay7Ngay as $n): ?><tr><td><?= e($n['ngay']) ?></td><td><?= (int) $n['so_luot'] ?></td></tr><?php endforeach; ?>
  </tbody></table>
  <?php endif; ?>

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

  <h3>Lời nhắn (5 lời mỗi trang)</h3>
  <?php if (!$dsLoiNhan): ?>
    <p>Chưa có lời nhắn nào, hãy là người đầu tiên!</p>
  <?php else: ?>
    <ul>
    <?php foreach ($dsLoiNhan as $m): ?>
      <li><strong><?= e($m['ten_khach']) ?></strong>: <?= nl2br(e($m['loi_nhan'])) ?> <small>(<?= e($m['thoi_gian']) ?>)</small>
        <?php if ($laChuTrang): ?><form method="post" action="gioithieu.php" class="form-dong"><input type="hidden" name="chuc_nang" value="xoa"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><button type="submit">Xóa</button></form><?php endif; ?></li>
    <?php endforeach; ?>
    </ul>
    <?php if ($soTrang > 1): ?><nav aria-label="Phân trang lưu bút"><p>
      <?php for ($i = 1; $i <= $soTrang; $i++): ?><a href="?trang=<?= $i ?>"<?= $i === $hienTai ? ' aria-current="page"' : '' ?>><?= $i ?></a> <?php endfor; ?>
    </p></nav><?php endif; ?>
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
