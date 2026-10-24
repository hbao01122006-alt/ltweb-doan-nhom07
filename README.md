# Foodspead - Bài tập thực hành nhóm số 6 (Nhóm 07)

Website đặt đồ ăn nhanh viết bằng PHP thuần + PDO + MySQL/MariaDB (không framework, không ORM, không mysqli).

## Cài đặt và chạy
Cần: PHP >= 8.1 (bật `pdo_mysql`, `mbstring`, `fileinfo`), MySQL hoặc MariaDB, Composer.

1. `composer install`
2. Tạo CSDL `ltweb_nhom07` (xóa và tạo lại từ đầu): phpMyAdmin → Import `database/schema.sql`, rồi Import `database/seed.sql`; hoặc gõ lệnh trong Git Bash / cmd (mysql của XAMPP ở `C:\xampp\mysql\bin`):
   ```
   mysql -u root < database/schema.sql
   mysql -u root ltweb_nhom07 < database/seed.sql
   ```
3. `php -S localhost:8000` rồi mở http://localhost:8000 (hoặc chép thư mục vào `htdocs` của XAMPP).

Kết nối trong `inc/db.php`: `127.0.0.1`, CSDL `ltweb_nhom07`, tài khoản `root`, mật khẩu rỗng. `inc/config.php` đặt `MOI_TRUONG = 'dev'` (đổi `'prod'` để xem trang 500 thân thiện); log lỗi ở `logs/php-error.log`.

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
Zip không chứa `.git` và `vendor/`. Trên kho thật nhóm cần gắn thẻ commit Bài 5 (`git tag btn5 <mã-commit>`, `git push origin btn5`) và mỗi thành viên commit bằng tài khoản GitHub của mình.


## Phần B - phân công chức năng CSDL

| Thành viên | Bảng `tv_...` chính | Hai chức năng CSDL trên `gioithieu.php` |
|---|---|---|
| Hoàng Gia Bảo | `tv_hoang_gia_bao`, `tv_luot_xem` | Sổ lưu bút có phân trang/quyền xóa; lượt xem theo ngày + 7 ngày gần nhất |
| Trần Hồ Trung Hậu | `tv_tran_ho_trung_hau_cau_hoi`, `tv_tran_ho_trung_hau_ket_qua`, `tv_tran_ho_trung_hau_ngay_thi` | Trắc nghiệm chuyển từ mảng PHP sang MySQL; ngày thi chuyển từ cookie sang MySQL |
| Lê Công Huy | `tv_le_cong_huy_diem`, `tv_le_cong_huy_muc_tieu` | Tính/lưu lịch sử điểm; mục tiêu học tập chuyển từ session sang MySQL |
| Phạm Hồng Phong | `tv_pham_hong_phong_anh`, `tv_pham_hong_phong_thong_ke` | Ảnh đại diện lưu trạng thái trong MySQL; thống kê văn bản lưu lịch sử MySQL |
| Nguyễn Hữu Tài | `tv_nguyen_huu_tai_ky_nang`, `tv_nguyen_huu_tai_tuy_chon` | Kỹ năng chuyển từ mảng PHP sang MySQL; giao diện sáng/tối chuyển từ cookie sang MySQL |
