# Foodspead - Bài tập thực hành nhóm số 5 (Nhóm 07)

## Môi trường
- PHP >= 8.1
- Composer
- Chrome/Firefox để kiểm thử DevTools, Lighthouse và cookie/session

## Cài đặt và chạy
```bash
composer install
php -S localhost:8000
```
Mở `http://localhost:8000/`.

Có thể chép toàn bộ thư mục vào `htdocs` của XAMPP và mở bằng đường dẫn tương ứng.

## Tài khoản thử quản trị
- Tên đăng nhập: `admin`
- Mật khẩu: `Nhom07@2026`

## Chức năng - URL - tệp PHP
| Chức năng | URL | Tệp |
|---|---|---|
| Trang chủ + đã xem gần đây | `/index.php` | `index.php` |
| Tìm/lọc/sắp xếp thực đơn | `/danh-sach.php` | `danh-sach.php` |
| Chi tiết + cookie đã xem + thêm giỏ | `/chi-tiet.php?id=1` | `chi-tiet.php` |
| Giỏ hàng session | `/gio-hang.php` | `gio-hang.php`, `src/Services/GioHang.php` |
| Liên hệ + upload + PRG | `/lien-he.php` | `lien-he.php`, `src/Data/KhoLienHe.php` |
| Đăng nhập | `/dang-nhap.php` | `dang-nhap.php` |
| Quản trị liên hệ | `/quan-tri.php` | `quan-tri.php` |
| Giới thiệu nhóm | `/gioi-thieu.php` | `gioi-thieu.php` |

## Kiểm thử gợi ý
- Tắt JavaScript và thử toàn bộ chức năng PHP.
- Dùng `curl` gửi dữ liệu sai tới `lien-he.php` và `gio-hang.php`.
- Kiểm tra `logs/php-error.log` không có Warning/Notice/Deprecated/Fatal sau các ca hợp lệ và dữ liệu sai dự kiến.
- Mở View Source để dán HTML sinh ra vào W3C Validator.

## Git theo yêu cầu đề
Trước khi chuyển bài 4 sang PHP trên kho thật, nhóm cần tự tạo tag từ commit bài 4:
```bash
git tag btn4 <ma-commit-bai-4>
git push origin btn4
```
Sau đó commit phần bài 5 theo từng thành viên. ZIP này không chứa thư mục `.git` nên không thể tạo lịch sử commit thay nhóm.
