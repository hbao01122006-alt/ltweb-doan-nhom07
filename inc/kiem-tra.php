<?php
// inc/kiem-tra.php - kiemTraSanPham(): kiểm tra dữ liệu dùng chung cho trang thêm và trang sửa món.
// Trả về [$du, $loi]; các khóa của $du trùng tên tham số :ten, :gia... trong câu INSERT / UPDATE.
function kiemTraSanPham(array $vao, array $idDanhMuc): array
{
    $chuoi = static fn(string $k): string => is_string($vao[$k] ?? null) ? trim($vao[$k]) : '';
    $du = [
        'ten'         => $chuoi('ten'),
        'gia'         => filter_var($vao['gia'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1000]]),
        'ton_kho'     => filter_var($vao['ton_kho'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9999]]),
        'danh_muc_id' => filter_var($vao['danh_muc_id'] ?? '', FILTER_VALIDATE_INT),
        'mo_ta'       => $chuoi('mo_ta'),
    ];
    $loi = [];
    $n = mb_strlen($du['ten']);
    if ($n < 2 || $n > 200) $loi['ten'] = 'Tên dài 2 - 200 ký tự.';
    if ($du['gia'] === false) $loi['gia'] = 'Giá là số nguyên từ 1.000.';
    if ($du['ton_kho'] === false) $loi['ton_kho'] = 'Tồn kho từ 0 đến 9999.';
    if (!in_array($du['danh_muc_id'], $idDanhMuc, true)) $loi['danh_muc_id'] = 'Chọn một danh mục có sẵn.';
    if (mb_strlen($du['mo_ta']) > 2000) $loi['mo_ta'] = 'Mô tả tối đa 2000 ký tự.';
    return [$du, $loi];
}

/**
 * Xử lý ảnh đại diện tải lên (không bắt buộc). Trả về [đường dẫn lưu CSDL | null, lỗi | null].
 * Kiểm tra kiểu thật bằng finfo, <= 2 MB, tên ngẫu nhiên, lưu trong uploads/.
 */
function xuLyAnhTaiLen(?array $tep): array
{
    if (!is_array($tep) || ($tep['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return [null, null];
    if ($tep['error'] !== UPLOAD_ERR_OK) return [null, 'Tải ảnh lên không thành công.'];
    if ($tep['size'] > 2 * 1024 * 1024) return [null, 'Ảnh không được lớn hơn 2 MB.'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string) $tep['tmp_name']);
    $duoi = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if ($duoi === null) return [null, 'Chỉ nhận ảnh JPG, PNG hoặc WebP.'];
    $thuMuc = THU_MUC_GOC . '/uploads';
    if (!is_dir($thuMuc)) mkdir($thuMuc, 0775, true);
    $ten = bin2hex(random_bytes(16)) . '.' . $duoi;
    if (!move_uploaded_file((string) $tep['tmp_name'], $thuMuc . '/' . $ten)) return [null, 'Không lưu được ảnh tải lên.'];
    return ['uploads/' . $ten, null];
}
