# Phần B — hướng dẫn kiểm thử phủ định trên máy nhóm (chưa có kết quả thực nghiệm)

Tệp `phan-b.http` mô tả **25 ca tương ứng 5 thành viên × 5 nhóm tình huống** (XSS, CSRF, khách/sai chủ trang, sửa bản ghi khác chủ, tham số mảng). Sử dụng tiện ích REST Client trong VS Code; chỉ thử trên website và CSDL của nhóm chạy ở localhost. Không ghi cookie phiên và CSRF thật vào Git hoặc báo cáo.

## Bảo (ưu tiên số một)

Trang `thanhvien/3120224008_hoanggiabao/gioithieu.php` có 2 chức năng: **sổ lưu bút MySQL** và **lượt xem MySQL theo ngày**. Mẫu XSS `<b>Bình</b>` cùng dấu `" ' &` nay được phép làm dữ liệu nhập hợp lệ, nhưng được in bằng `e()`/`nl2br(e())` để thành chữ chứ không thực thi HTML. Phần ghi lượt xem chỉ dùng **POST có CSRF**; GET thông thường không đổi dữ liệu. Xóa lời nhắn cần vai trò chủ trang (hoặc quản trị) và `DELETE ... WHERE id = ? AND mssv = ?`.

Các ca Bảo cần kiểm tra trực tiếp:

1. **XSS:** gửi tên `<b>Bình</b>`, lời nhắn chứa dấu `" ' &`. Kiểm tra dữ liệu lưu MySQL và trang hiển thị chữ nguyên văn; xem nguồn có `&lt;b&gt;`, không có thẻ `<b>` được phân tích từ dữ liệu.
2. **CSRF:** POST thiếu mã, sai mã hoặc `csrf[]=x` phải nhận HTTP 403; không ghi thêm tin, log `csrf_sai`.
3. **Truy cập sai chủ:** đăng nhập khách (`khach1@foods.example`), dùng *cookie và mã CSRF của khách ấy* POST `chuc_nang=xoa&id=...`; phải nhận HTTP 403, không xóa, có log `cam_truy_cap`.
4. **Khác MSSV:** trên phpMyAdmin ở CSDL thử của riêng nhóm, tạo **một** lời nhắn trong bảng `tv_hoang_gia_bao` mang `mssv='3120224053'` (thuộc thành viên Hậu) để kiểm tra khóa của dòng. Ví dụ câu SQL: `INSERT INTO tv_hoang_gia_bao(mssv,ten_khach,loi_nhan) VALUES ('3120224053','Test Phan B','KHONG DUOC XOA');`. Lấy `id` dòng mới, thay `@id_ngoai`; đăng nhập **Bảo**, gửi POST xóa ID đó. Request có thể trả 302 nhưng hàng mang MSSV khác phải **còn nguyên** vì SQL có `AND mssv = ?`. Xóa dòng thử bằng phpMyAdmin sau khi kiểm tra.
5. **Tham số mảng:** mở `gioithieu.php?trang[]=1`, gửi `ten[]=...` với CSRF hợp lệ; không sinh PHP Warning/TypeError/500, không thêm dòng sai.

## Bốn thành viên còn lại

- **Hậu:** trường ngày thi dạng ngày, thử HTML phải bị từ chối bằng thông báo hợp lệ (không chạy script); thao tác cập nhật/xóa ngày chỉ kiểm tra chủ trang, không có ID bản ghi do dùng khóa `mssv`.
- **Huy:** mục tiêu học tập có `DELETE/UPDATE ... WHERE id=? AND mssv=?`. Có thể thêm mục tiêu tạm cho `mssv='3120224008'` trong `tv_le_cong_huy_muc_tieu`, thử xóa ID đó khi đăng nhập Huy và kiểm tra bản ghi vẫn còn.
- **Phong:** thống kê văn bản chấp nhận chuỗi có HTML nhưng in qua `e()`. Upload ảnh chỉ người sở hữu trang; kiểm tra MIME `finfo`, kích thước 2MB, tên ngẫu nhiên; thử tệp không phải ảnh và đối chiếu DB. Ảnh đại diện dùng khóa `mssv` nên không có ID để sửa của người khác; thử bằng tài khoản chủ trang khác.
- **Tài:** kỹ năng có `DELETE ... WHERE id=? AND mssv=?`; có thể thêm kỹ năng tạm cho `mssv='3120224008'` vào `tv_nguyen_huu_tai_ky_nang`, rồi thử xóa bằng Tài và so sánh DB trước/sau.

**Các kết quả trạng thái ở trên là MONG ĐỢI, không phải kết quả đã chạy.** Trong Bảng 5 của báo cáo phải điền lệnh đã chạy, kết quả thực tế, số ca đạt/tổng, người rà soát và mã commit cá nhân, rồi chụp minh chứng từ localhost với giá trị phiên/CSRF được che.
