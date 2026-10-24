-- database/schema.sql - xóa và tạo lại CSDL ltweb_nhom07 từ đầu (chạy lại được nhiều lần).
-- Lưu ý thiết kế: chi_tiet_don.don_gia cố ý lưu lặp giá lúc đặt; san_pham.danh_muc_id không ghi ON DELETE
-- nên không xóa được danh mục còn món (lỗi 1451); san_pham n-n don_hang qua chi_tiet_don (khóa chính ghép).
SET NAMES utf8mb4;
DROP DATABASE IF EXISTS ltweb_nhom07;
CREATE DATABASE ltweb_nhom07 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ltweb_nhom07;


CREATE TABLE danh_muc (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, ten VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL UNIQUE);
CREATE TABLE nguoi_dung (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,email VARCHAR(190) NOT NULL UNIQUE,ho_ten VARCHAR(100) NOT NULL,mat_khau VARCHAR(255) NOT NULL,vai_tro ENUM('quan_tri','khach_hang') NOT NULL DEFAULT 'khach_hang',ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE san_pham (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,danh_muc_id INT UNSIGNED NOT NULL,ten VARCHAR(200) NOT NULL,gia INT UNSIGNED NOT NULL CHECK(gia>=1000),ton_kho INT UNSIGNED NOT NULL DEFAULT 0,anh VARCHAR(255),anh_mo_ta VARCHAR(255),mo_ta TEXT,thanh_phan TEXT,khau_phan VARCHAR(100),nang_luong_kcal INT UNSIGNED NOT NULL DEFAULT 0,thoi_gian_phut INT UNSIGNED NOT NULL DEFAULT 0,noi_bat TINYINT(1) NOT NULL DEFAULT 0,ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(danh_muc_id) REFERENCES danh_muc(id),INDEX idx_dm_gia(danh_muc_id,gia),INDEX idx_ten(ten));
CREATE TABLE don_hang (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,nguoi_dung_id INT UNSIGNED NOT NULL,trang_thai ENUM('moi','xac_nhan','dang_giao','hoan_tat','huy') NOT NULL DEFAULT 'moi',ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(nguoi_dung_id) REFERENCES nguoi_dung(id));
CREATE TABLE chi_tiet_don (don_hang_id INT UNSIGNED NOT NULL,san_pham_id INT UNSIGNED NOT NULL,so_luong SMALLINT UNSIGNED NOT NULL CHECK(so_luong>0),don_gia INT UNSIGNED NOT NULL,PRIMARY KEY(don_hang_id,san_pham_id),FOREIGN KEY(don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE,FOREIGN KEY(san_pham_id) REFERENCES san_pham(id));
CREATE TABLE lien_he (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,ho_ten VARCHAR(80) NOT NULL,email VARCHAR(190) NOT NULL,sdt VARCHAR(20) NOT NULL,noi_dung VARCHAR(1000) NOT NULL,anh VARCHAR(255),ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE thanh_vien (mssv CHAR(10) PRIMARY KEY,ho_ten VARCHAR(100) NOT NULL,nguoi_dung_id INT UNSIGNED NULL UNIQUE,FOREIGN KEY(nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL);
CREATE TABLE tv_hoang_gia_bao (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv CHAR(10) NOT NULL,
    ten_khach VARCHAR(50) NOT NULL,
    loi_nhan VARCHAR(500) NOT NULL,
    thoi_gian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE,
    INDEX idx_bao_luubut_mssv_thoigian (mssv, thoi_gian)
);
CREATE TABLE tv_luot_xem (
    mssv CHAR(10) NOT NULL,
    ngay DATE NOT NULL,
    so_luot INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (mssv, ngay),
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
);

CREATE TABLE tv_tran_ho_trung_hau_cau_hoi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv CHAR(10) NOT NULL,
    noi_dung VARCHAR(300) NOT NULL,
    lua_chon_a VARCHAR(200) NOT NULL,
    lua_chon_b VARCHAR(200) NOT NULL,
    lua_chon_c VARCHAR(200) NOT NULL,
    lua_chon_d VARCHAR(200) NOT NULL,
    dap_an_dung ENUM('a','b','c','d') NOT NULL,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE,
    INDEX idx_hau_cauhoi_mssv (mssv)
);
CREATE TABLE tv_tran_ho_trung_hau_ket_qua (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv CHAR(10) NOT NULL,
    diem TINYINT UNSIGNED NOT NULL,
    tong TINYINT UNSIGNED NOT NULL,
    thoi_gian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE,
    CHECK (diem <= tong),
    INDEX idx_hau_kq_mssv_thoigian (mssv, thoi_gian)
);
CREATE TABLE tv_tran_ho_trung_hau_ngay_thi (
    mssv CHAR(10) PRIMARY KEY,
    ngay_thi DATE NOT NULL,
    cap_nhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
);

CREATE TABLE tv_le_cong_huy_diem (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv CHAR(10) NOT NULL,
    a1 DECIMAL(4,2) NOT NULL,
    a2 DECIMAL(4,2) NOT NULL,
    a3 DECIMAL(4,2) NOT NULL,
    tong DECIMAL(4,2) NOT NULL,
    xep_loai VARCHAR(2) NOT NULL,
    thoi_gian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE,
    CHECK (a1 BETWEEN 0 AND 10),
    CHECK (a2 BETWEEN 0 AND 10),
    CHECK (a3 BETWEEN 0 AND 10),
    CHECK (tong BETWEEN 0 AND 10),
    INDEX idx_huy_diem_mssv_thoigian (mssv, thoi_gian)
);
CREATE TABLE tv_le_cong_huy_muc_tieu (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv CHAR(10) NOT NULL,
    muc_tieu VARCHAR(120) NOT NULL,
    hoan_thanh TINYINT(1) NOT NULL DEFAULT 0,
    thoi_gian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE,
    UNIQUE KEY uq_huy_muctieu (mssv, muc_tieu),
    INDEX idx_huy_muctieu_mssv_trangthai (mssv, hoan_thanh)
);

CREATE TABLE tv_pham_hong_phong_anh (
    mssv CHAR(10) PRIMARY KEY,
    duong_dan VARCHAR(255) NOT NULL,
    cap_nhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
);
CREATE TABLE tv_pham_hong_phong_thong_ke (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv CHAR(10) NOT NULL,
    noi_dung TEXT NOT NULL,
    so_ky_tu INT UNSIGNED NOT NULL,
    so_ky_tu_khong_cach INT UNSIGNED NOT NULL,
    so_tu INT UNSIGNED NOT NULL,
    so_cau INT UNSIGNED NOT NULL,
    tu_dai_nhat VARCHAR(200) NOT NULL,
    tu_hay_gap VARCHAR(500) NOT NULL,
    thoi_gian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE,
    INDEX idx_phong_tk_mssv_thoigian (mssv, thoi_gian)
);

CREATE TABLE tv_nguyen_huu_tai_ky_nang (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv CHAR(10) NOT NULL,
    ten VARCHAR(100) NOT NULL,
    nhom VARCHAR(30) NOT NULL,
    mo_ta VARCHAR(300) NOT NULL,
    muc_do TINYINT UNSIGNED NOT NULL DEFAULT 3,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE,
    UNIQUE KEY uq_tai_kynang (mssv, ten),
    CHECK (muc_do BETWEEN 1 AND 5),
    INDEX idx_tai_kynang_mssv_nhom (mssv, nhom)
);
CREATE TABLE tv_nguyen_huu_tai_tuy_chon (
    mssv CHAR(10) PRIMARY KEY,
    giao_dien ENUM('sang','toi') NOT NULL DEFAULT 'sang',
    cap_nhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
);
