-- Chay bang root SAU schema.sql va seed.sql; thay mat khau giu cho truoc khi dung.
CREATE USER IF NOT EXISTS 'nhom07_app'@'localhost'
 IDENTIFIED BY 'THAY_BANG_MAT_KHAU_DAI_NGAU_NHIEN';
GRANT SELECT, INSERT, UPDATE, DELETE
 ON ltweb_nhom07.* TO 'nhom07_app'@'localhost';
