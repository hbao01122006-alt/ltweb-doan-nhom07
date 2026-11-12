# Foodspead - Bài tập thực hành nhóm số 7 (Nhóm 07)

Website đặt đồ ăn nhanh viết bằng PHP thuần + PDO + MySQL/MariaDB (không framework, không ORM, không mysqli).

## Cài đặt và chạy
Cần: PHP >= 8.1 (bật `pdo_mysql`, `mbstring`, `fileinfo`), MySQL hoặc MariaDB, Composer.

1. Chạy `composer install` từ thư mục gốc (đã có `composer.lock` khóa phiên bản `vlucas/phpdotenv`; không đưa `vendor/` lên Git).
2. Tạo CSDL `ltweb_nhom07` (xóa và tạo lại từ đầu): phpMyAdmin → Import `database/schema.sql`, rồi Import `database/seed.sql`; hoặc gõ lệnh trong Git Bash / cmd (mysql của XAMPP ở `C:\xampp\mysql\bin`):
   ```
   mysql -u root < database/schema.sql
   mysql -u root ltweb_nhom07 < database/seed.sql
   ```
3. Sao chép `.env.example` thành `.env` (`copy .env.example .env` trên Windows), điều chỉnh `DB_HOST`, tài khoản, mật khẩu. Mặc định `APP_DEBUG=true` trên máy cá nhân; trên hosting đặt `APP_DEBUG=false`.
4. (Tùy chọn, khuyến khích) Chạy `database/tai-khoan.sql` bằng root, **đổi mật khẩu giữ chỗ thành mật khẩu mạnh** và sửa `.env` thành `DB_USERNAME=nhom07_app`. Tài khoản này chỉ có quyền `SELECT, INSERT, UPDATE, DELETE` trên `ltweb_nhom07.*`.
5. **Chạy từ thư mục gốc**: `php -S localhost:8000 -t . router.php`, rồi mở http://localhost:8000. Router chặn truy cập các tệp nội bộ khi chạy bằng `php -S`. Hoặc chép thư mục vào `htdocs` của XAMPP để Apache áp dụng `.htaccess`.

`inc/db.php` đọc thông tin kết nối qua `.env`, không chứa mật khẩu. `APP_DEBUG=false` cho trang lỗi 500 thân thiện; `DB_PASSWORD` phai ton tai trong `.env` (co the rong tren XAMPP); PHP lỗi ghi tại `logs/php-error.log`. Không commit `.env`, `vendor/`, `logs/*.log`. Lưu ý `php -S` **không đọc** `.htaccess`: khi dùng máy chủ PHP tích hợp, **phải truyền `router.php` như bước 5**; chỉ dùng nội bộ trên máy cá nhân. Thử bảo vệ Apache trong `htdocs` XAMPP (bật `AllowOverride All`).

## Tài khoản thử (mật khẩu: `matkhau123`)
- `admin@foods.example`: quan_tri - vào `quan-tri.php`
- `khach1@foods.example`, `khach2@foods.example`: khach_hang - mỗi người đã có một đơn hàng
- Chủ trang cá nhân (Phần B, khach_hang, mỗi người có một đơn mẫu): `bao@`, `hau@`, `huy@`, `phong@`, `tai@` + `foods.example` — đăng nhập đúng tài khoản của mình mới xóa / đổi trạng thái được dữ liệu trên trang của mình (quản trị viên cũng được).

## Chức năng - URL - Tệp PHP
| Chức năng | URL | Tệp PHP |
|---|---|---|
| 1. Trang chủ, chi tiết, giỏ hàng, liên hệ (lưu bảng `lien_he`, kèm ảnh) | `/index.php`, `/chi-tiet.php?id=1`, `/gio-hang.php`, `/lien-he.php` | các tệp cùng tên, `src/Data/KhoSanPham.php`, `src/Data/KhoLienHe.php`, `src/Services/GioHang.php` |
| 2. Tìm / lọc / sắp xếp / phân trang | `/danh-sach.php?q=ga&dm=1&gia=50000&sx=gia-tang&trang=2` | `danh-sach.php`, `src/Data/KhoSanPham.php` |
| 3. Quản trị món + xem liên hệ | `/quan-tri.php`, `/them-san-pham.php`, `/sua-san-pham.php?id=1`, `/xoa-san-pham.php` (POST) | các tệp cùng tên, `inc/kiem-tra.php`, `inc/form-san-pham.php` |
| 4. Đăng ký / đăng nhập / đăng xuất | `/dang-ky.php`, `/dang-nhap.php`, `/dang-xuat.php` (POST) | các tệp cùng tên, `inc/xac-thuc.php`, `src/Data/KhoNguoiDung.php` |
| 5. Đặt hàng (giao dịch), đơn hàng của tôi | `/dat-hang.php`, `/don-hang.php`, `/don-hang.php?id=1` | các tệp cùng tên, `inc/don-hang.php`, `src/Data/KhoDonHang.php` |
| 6. REST API + JavaScript | `/api/san-pham.php`, `/api/gio-hang.php` | `api/*.php`, `js/tim-kiem.js`, `js/gio-hang.js` |
| Phần B (mỗi thành viên) | `/thanhvien/<MSSV>_<ten>/gioithieu.php` | bảng `thanh_vien`, các bảng `tv_*` riêng; 2 chức năng CSDL/người |
| Trang lỗi | `/403.php`, `/404.php`, `/500.php` (không truy vấn CSDL) | |

## API (JSON, `Content-Type: application/json; charset=utf-8`)
| Method | Endpoint | Mô tả | Mã trạng thái |
|---|---|---|---|
| GET | `/api/san-pham.php?q=ga` | Tìm món theo tên | 200 |
| GET | `/api/san-pham.php?id=1` | Một món | 200, 400 (id sai), 404 |
| GET | `/api/gio-hang.php` | Xem giỏ (session) | 200 |
| POST | `/api/gio-hang.php` | Thân JSON `{"id": 1, "sl": 2}` thêm vào giỏ | 200, 400 (không phải JSON), 404, 422 (sl sai / vượt tồn kho) |
| khác | cả hai endpoint | Sai phương thức | 405 + header `Allow` |
| | | Lỗi máy chủ | 500 `{"loi": "Lỗi máy chủ"}` |

Bộ request kiểm thử: `api.http` (VS Code, extension REST Client).

## CSDL
- `ltweb_nhom07`, utf8mb4 / utf8mb4_unicode_ci. `database/schema.sql` xóa và tạo lại từ đầu; `database/seed.sql` nạp 33 món thuộc 5 danh mục (danh mục "Món theo mùa" để trống), có món hết hàng, tên chứa dấu nháy đơn (`Levi's`) và ký tự `50%`, 3 tài khoản thử + 5 tài khoản chủ trang Phần B, mỗi khách hàng có đơn mẫu. ERD: `database/erd.png`.
- Mọi SQL có dữ liệu người dùng dùng prepared statement; cột sắp xếp chỉ chọn trong danh sách cho phép; SQL về từng thực thể nằm trong lớp `src/Data/Kho*.php`.

## Lưu ý Git
Zip không chứa `.git` và `vendor/`. Trên kho thật nhóm cần gắn thẻ commit đã nộp Bài 6 (`git tag btn6 <mã-commit-Bài-6>`, `git push origin btn6`) và mỗi thành viên commit bằng tài khoản GitHub của mình.


## Phần B - phân công chức năng CSDL

| Thành viên | Bảng `tv_...` chính | Hai chức năng CSDL trên `gioithieu.php` |
|---|---|---|
| Hoàng Gia Bảo | `tv_hoang_gia_bao`, `tv_luot_xem` | Sổ lưu bút có phân trang/quyền xóa; lượt xem theo ngày + 7 ngày gần nhất |
| Trần Hồ Trung Hậu | `tv_tran_ho_trung_hau_cau_hoi`, `tv_tran_ho_trung_hau_ket_qua`, `tv_tran_ho_trung_hau_ngay_thi` | Trắc nghiệm chuyển từ mảng PHP sang MySQL; ngày thi chuyển từ cookie sang MySQL |
| Lê Công Huy | `tv_le_cong_huy_diem`, `tv_le_cong_huy_muc_tieu` | Tính/lưu lịch sử điểm; mục tiêu học tập chuyển từ session sang MySQL |
| Phạm Hồng Phong | `tv_pham_hong_phong_anh`, `tv_pham_hong_phong_thong_ke` | Ảnh đại diện lưu trạng thái trong MySQL; thống kê văn bản lưu lịch sử MySQL |
| Nguyễn Hữu Tài | `tv_nguyen_huu_tai_ky_nang`, `tv_nguyen_huu_tai_tuy_chon` | Kỹ năng chuyển từ mảng PHP sang MySQL; giao diện sáng/tối chuyển từ cookie sang MySQL |

## Bản hiệu đính sau phản hồi (09/11/2026)
- Trang giới thiệu nhóm `/gioi-thieu.php` đã khôi phục từ commit `d822973`; bản sửa thống kê 7 ngày được đặt đúng trong `thanhvien/3120224008_hoanggiabao/gioithieu.php`.
- `yeu-thich.php` không còn chèn `<style>` giữa phần tử `body`; quy tắc CSS đã chuyển về `css/style.css`.
- Công thức tính điểm giữ **10–30–60**, nhất quán với PHP, JS và `database/seed.sql`; báo cáo hiệu đính trong `Nhom07_Baitap6_hieu_dinh.pdf` đã sửa nội dung không khớp và đánh dấu chứng cứ ảnh cần chụp lại. Số commit không thể xác minh từ ZIP (không chứa `.git`), nên không đoán số mới.
- Kịch bản kiểm thử Phần C ở `kiemtra/phan-c/` (giao dịch không/ có rollback; tạo hai tài khoản cùng mật khẩu; đổi ID để thử 403/404). **Chưa phải kết quả thực nghiệm đã chạy.**
- Sau khi thiết lập MySQL theo README và khởi động `php -S localhost:8000 -t . router.php`, chạy `bash kiemtra/smoke-menu.sh http://localhost:8000` và các kiểm thử Phần C theo hướng dẫn. Cần chụp ảnh minh chứng thật và kiểm thử lại 14 ca trước khi nộp. CSRF token và giới hạn đăng nhập sai đã được bổ sung trong mã nguồn Bài 7; cần chạy kiểm thử phủ định thật để xác nhận hoạt động.


## Chương 7 - Bảo mật (bản đã cập nhật từ ZIP Bài 6)

| Biện pháp | Các tệp chính | Slide tham khảo |
|---|---|---|
| CSRF form và fetch | `inc/csrf.php`, `inc/config.php`, `inc/header.php`, `js/gio-hang.js`, `js/api.js`, tất cả form POST, `thanhvien/3120224008_hoanggiabao/gioithieu.php` | 23-24 |
| Cookie và session | `inc/config.php`, `inc/xac-thuc.php`, `dang-xuat.php` | 25 |
| Header và CSP | `inc/config.php`, `js/main.js`, `css/style.css`, các trang cá nhân | 22, 26-27 |
| `.env` | `.env.example`, `.gitignore`, `composer.json`, `inc/db.php` | 29 |
| Apache và upload | `.htaccess`, `uploads/.htaccess` | 28, 30 |
| Quyền DB tối thiểu | `database/tai-khoan.sql` | 20 |
| Chống đoán mật khẩu, log | `database/schema.sql`, `inc/xac-thuc.php`, `inc/ham.php`, `dang-nhap.php` | 31-32 |

### Kiểm thử trước khi nộp

- **Lưu ý:** ZIP này không chứa lịch sử Git, không chứa `vendor/`; không thể xác minh mã commit, tag `btn6`, kết quả `git diff --stat btn6 HEAD` hoặc `composer audit` từ ZIP. Nhóm phải tự thực hiện sau khi đưa các thay đổi vào kho Git chính thức.
- Thêm tag `btn6` trỏ đúng commit Bài 6 **trước khi commit các sửa đổi của Bài 7**. Chia commit theo biện pháp/người phụ trách, không dùng chung danh tính người commit.
- Chạy `composer install`, `composer audit`, `php -l` trên tất cả PHP, rồi chạy cả `php -S localhost:8000 -t . router.php` và Apache XAMPP. Chạy `bash tests/bao-mat.sh` chỉ trên localhost và dùng `tests/bao-mat.http`.
- DevTools → Network/Headers: kiểm tra CSP, HttpOnly, SameSite; Console: không còn lỗi CSP; thư mục logs: đủ `csrf_sai`, `cam_truy_cap`, `dang_nhap_sai` và không lộ mật khẩu, PHPSESSID hay mã CSRF.
- Để chạy API POST có CSRF: mở trang bằng trình duyệt, lấy `csrf` từ meta `csrf-token` hoặc input form và cookie `PHPSESSID` **của cùng phiên**, gửi cả `Cookie` và `X-CSRF-Token`; tuyệt đối không lưu giá trị thật vào `api.http` / shell script hoặc Git.
- Kiểm tra `GET /dang-xuat.php` và `GET /xoa-san-pham.php?id=1` trả 405; gửi POST thiếu CSRF trả 403; 5 lần đăng nhập sai thì lần 6 trả 429; quyền quản trị viên và chủ trang cá nhân vẫn phải kiểm tra riêng.
- **Kiểm thử phủ định phải chạy thực tế** và ghi mã trạng thái, câu trả lời, ảnh DevTools, giá trị CSDL/trích log vào báo cáo; không dùng kết quả mẫu thay bằng chứng thực nghiệm.
- `git ls-files .env` và `git log --all --oneline -- .env` phải không in ra gì. Nếu từng đẩy bí mật lên Git, phải thay mật khẩu/khóa ngay; xóa file ở commit sau không loại khỏi lịch sử.
- API ngoài đang dùng: `api.open-meteo.com` (trang chủ) được khai báo trong CSP `connect-src`; tệp JS mẫu `js/trang-lien-he.js` không được nạp vào trang nên không khai báo JSONPlaceholder. Không dùng wildcard hay `'unsafe-inline'`.
- `composer.lock` trong bản Bài 7 đã chứa `vlucas/phpdotenv` và phải được giữ lại. Chạy `composer install` và `composer audit` trước khi nộp; chỉ cập nhật `composer.lock` khi có thay đổi dependency đã kiểm tra.

### Git và an toàn nộp bài

Yêu cầu sản phẩm Bài 7: mã nguồn, `Nhom07_Baitap7.pdf`, `LTW_BTN7_Nhom07_code.zip` (không chứa `.git`, `.env`, `vendor`, log). Chỉ dùng trên website/localhost của nhóm.

- Bộ đếm lượt xem trang cá nhân Hoàng Gia Bảo nằm chung trong `gioithieu.php`: JavaScript gửi POST tới `gioithieu.php?hanh_dong=luot-xem` kèm CSRF; GET không ghi CSDL. Không cần tệp `luot-xem.php` riêng. Nếu tắt JavaScript, trang vẫn xem được nhưng lượt xem mới không được ghi tự động.

### Lưu ý bản rà soát mã nguồn (09/11/2026)
- Đã sửa: input `hanh_dong[]` không sinh cảnh báo; cập nhật giỏ không cho vượt tồn kho; API thêm món không âm thầm cắt quá 20; kiểm tra metadata upload trước khi đọc MIME; không ghi query string chứa token vào log bảo mật.
- Đã cập nhật `api.http` để gửi đúng PHPSESSID và mã CSRF cùng phiên, bổ sung ca kiểm thử tại `tests/bao-mat.http` và `tests/bao-mat.sh`.
- Trang Bootstrap HTML cũ (link `.html` lỗi, tải Bootstrap CDN không đi qua CSP PHP) được lưu lại tại `kiemtra/legacy-danh-sach-bootstrap.html.txt` để tham khảo; URL cũ hiện chuyển về `danh-sach.php`. Apache chặn truy cập thư mục `kiemtra/`.
- Môi trường rà soát không có MySQL/MariaDB và gói Composer tải sẵn, nên kiểm tra cú pháp không thay thế việc chạy demo, `composer audit` và các ca kiểm thử thực tế.


## Phần B — bổ sung kiểm thử Chương 7 (10/11/2026)

- `tests/phan-b.http` có kịch bản 25 ca phủ định cho 5 trang cá nhân; ưu tiên trang Hoàng Gia Bảo. Hướng dẫn chuẩn bị dữ liệu và kiểm chứng kết quả tại `tests/HUONG_DAN_PHAN_B.md`.
- Sổ lưu bút Bảo cho phép nhập mẫu `<b>Bình</b>` và ký tự `" ' &` để thử XSS; dữ liệu được hiển thị qua `e()` và `nl2br(e())`, không thực thi HTML.
- Chạy các phép thử trên **localhost/CSDL nhóm** rồi ghi số liệu thật trong báo cáo; các tệp này là kịch bản, không phải minh chứng đã đạt.
- Khi đóng ZIP gửi giảng viên phải **loại `.env`, `vendor/`, `.git/`, `logs/*.log`, file đã upload thật**; chỉ giữ `.env.example` và giữ `composer.lock` để khóa phiên bản. Bản nộp cần đặt tên `LTW_BTN7_Nhom07_code.zip`.
- Việc sử dụng AI hỗ trợ rà soát và soạn kịch bản kiểm thử cần được ghi rõ phạm vi trong báo cáo của nhóm; từng thành viên phải tự chạy, hiểu, commit và xác nhận phần việc.
