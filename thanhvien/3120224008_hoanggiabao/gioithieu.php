<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta
    name="description"
    content="Trang giới thiệu cá nhân của Hoàng Gia Bảo, nhóm trưởng Nhóm 07 thực hiện website Foodspead."
  >

  <title>Hoàng Gia Bảo | Foodspead - Nhóm 07</title>

  <!-- CSS chung của nhóm -->
  <link rel="stylesheet" href="../../css/style.css">

  <!-- CSS riêng của Hoàng Gia Bảo -->
  <link rel="stylesheet" href="style.css">
</head>

<body class="trang-ca-nhan">

  <!-- ===== HIỆU ỨNG LỬA TRANG TRÍ ===== -->
  <div
    class="flicker-flame flame-top-left"
    aria-hidden="true"
  >
    🔥
  </div>

  <div
    class="flicker-flame flame-top-right"
    aria-hidden="true"
  >
    🔥
  </div>

  <div
    class="flicker-flame flame-bottom-left"
    aria-hidden="true"
  >
    🔥
  </div>

  <div
    class="flicker-flame flame-bottom-right"
    aria-hidden="true"
  >
    🔥
  </div>


  <!-- ===== HEADER ===== -->
  <header class="dau-trang">

    <p>
      Foodspead - Ngon Mỗi Ngày, Giao Nhanh Tận Nơi
    </p>

    <p>
      Hệ thống đặt đồ ăn nhanh trực tuyến - Nhóm 07
    </p>

  </header>


  <!-- ===== MENU ===== -->
  <nav
    class="dieu-huong"
    aria-label="Điều hướng chính"
  >

    <ul>

      <li>
        <a href="../../index.html">
          Trang chủ
        </a>
      </li>

      <li>
        <a href="../../danh-sach.html">
          Thực đơn
        </a>
      </li>

      <li>
        <a href="../../chi-tiet.html">
          Món nổi bật
        </a>
      </li>

      <li>
        <a href="../../gioi-thieu.html">
          Giới thiệu
        </a>
      </li>

      <li>
        <a href="../../lien-he.html">
          Liên hệ đặt hàng
        </a>
      </li>

    </ul>

  </nav>


  <!-- ===== NỘI DUNG CHÍNH ===== -->
  <main class="noi-dung">

    <h1 class="tieu-de-chinh">
      Giới thiệu cá nhân - Hoàng Gia Bảo
    </h1>


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
        <a href="../../index.html">
          Quay về trang chủ Foodspead
        </a>
      </p>

      <p>
        <a href="../../gioi-thieu.html">
          Quay về trang giới thiệu Nhóm 07
        </a>
      </p>

    </section>

  </main>


  <!-- ===== LINH VẬT PHƯỢNG HOÀNG ===== -->
  <aside
    id="phoenix-container"
    aria-label="Linh vật Phượng Hoàng"
  >

    <div
      id="phoenix-speech"
      aria-live="polite"
    ></div>


    <div
      id="phoenix-avatar"
      aria-hidden="true"
    >
      🐦‍🔥
    </div>


    <div id="phoenix-controls">

      <button
        id="btn-p-b"
        type="button"
        title="Bốc lửa thành tro - Phím B"
      >
        🔥 B
      </button>

      <button
        id="btn-p-a"
        type="button"
        title="Niết bàn tái sinh - Phím A"
      >
        ✨ A
      </button>

      <button
        id="btn-p-o"
        type="button"
        title="Bay về góc đẻ trứng - Phím O"
      >
        🥚 O
      </button>

    </div>

  </aside>


  <!-- ===== FOOTER ===== -->
  <footer class="chan-trang">

    <p>
      &copy; 2026 Nhóm 07 - Khoa Toán - Tin,
      Trường Đại học Sư phạm - Đại học Đà Nẵng
    </p>

  </footer>


  <!-- ===== JAVASCRIPT CÁ NHÂN ===== -->
  <script src="js/canhan.js"></script>

</body>

</html>