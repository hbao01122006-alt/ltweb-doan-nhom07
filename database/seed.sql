SET NAMES utf8mb4;
USE ltweb_nhom07;
INSERT INTO danh_muc(ten,slug) VALUES("Gà rán","ga-ran");
INSERT INTO danh_muc(ten,slug) VALUES("Burger","burger");
INSERT INTO danh_muc(ten,slug) VALUES("Combo","combo");
INSERT INTO danh_muc(ten,slug) VALUES("Món phụ","mon-phu");
INSERT INTO danh_muc(ten,slug) VALUES("Đồ uống","do-uong");
INSERT INTO danh_muc(ten,slug) VALUES("Món theo mùa","mon-theo-mua");
INSERT INTO nguoi_dung(id,email,ho_ten,mat_khau,vai_tro) VALUES(1,'admin@foods.example','Quản trị Nhóm 07','$2y$12$qHb8qF.xgsQsDEN5nvxo6OsTDLDQmDQmTnjDdlNCp7MqAzd4fkWBG','quan_tri');
INSERT INTO nguoi_dung(id,email,ho_ten,mat_khau,vai_tro) VALUES(2,'khach1@foods.example','Khách hàng Một','$2y$12$u.0T5HsnlVJWcPLCbRUsIO9EXccEweLCYOD7R46J.TJ9bH4ju5eGC','khach_hang');
INSERT INTO nguoi_dung(id,email,ho_ten,mat_khau,vai_tro) VALUES(3,'khach2@foods.example','Khách hàng Hai','$2y$12$3ZoP8Zw46/1SUlmMu/unR.ZIvgTjCuo2NGWs2F9ttQBE.FmFo83bO','khach_hang');
-- Tài khoản chủ trang cá nhân (Phần B), mật khẩu matkhau123; mỗi tài khoản có một đơn mẫu ở dưới.
INSERT INTO nguoi_dung(id,email,ho_ten,mat_khau,vai_tro) VALUES
(4,'bao@foods.example','Hoàng Gia Bảo','$2y$12$u.0T5HsnlVJWcPLCbRUsIO9EXccEweLCYOD7R46J.TJ9bH4ju5eGC','khach_hang'),
(5,'hau@foods.example','Trần Hồ Trung Hậu','$2y$12$u.0T5HsnlVJWcPLCbRUsIO9EXccEweLCYOD7R46J.TJ9bH4ju5eGC','khach_hang'),
(6,'huy@foods.example','Lê Công Huy','$2y$12$u.0T5HsnlVJWcPLCbRUsIO9EXccEweLCYOD7R46J.TJ9bH4ju5eGC','khach_hang'),
(7,'phong@foods.example','Phạm Hồng Phong','$2y$12$u.0T5HsnlVJWcPLCbRUsIO9EXccEweLCYOD7R46J.TJ9bH4ju5eGC','khach_hang'),
(8,'tai@foods.example','Nguyễn Hữu Tài','$2y$12$u.0T5HsnlVJWcPLCbRUsIO9EXccEweLCYOD7R46J.TJ9bH4ju5eGC','khach_hang');
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(1,'Gà Rán Giòn Tan',35000,80,'images/ga-ran.jpg','Món gà rán giòn của FastFood','Gà rán giòn bên ngoài, mềm bên trong, đậm đà hương vị đặc trưng.','Thịt đùi/cánh gà tươi, bột chiên xù xốp, gia vị thảo mộc đặc trưng','1 - 2 người ăn',320,12,1);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(2,'Burger Bò Phô Mai',45000,60,'images/burger-bo-pho-mai.jpg','Burger bò phô mai với hành chiên, rau xà lách và cà chua','Burger bò kết hợp với phô mai, rau tươi và sốt ăn kèm.','Bánh mì mềm, thịt bò xay nướng, phô mai cheddar, rau xà lách, cà chua, sốt đặc biệt','1 người ăn',520,8,1);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(3,'Combo Tiết Kiệm',69000,45,'images/combo-ga.jpg','Combo gà rán, khoai tây chiên và nước uống của FastFood','Combo gồm gà rán, khoai tây chiên và nước uống.','2 miếng gà rán, khoai tây chiên vừa, 1 ly nước ngọt','1 - 2 người ăn',890,15,1);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(4,'Khoai Tây Chiên Lớn',25000,120,'images/khoai-tay.jpg','Khoai tây chiên của FastFood','Khoai tây chiên vàng giòn, dùng kèm với các món chính.','Khoai tây tươi, dầu thực vật, muối biển','1 - 2 người ăn',410,6,0);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(1,'Gà Rán Giòn Tan Đậm Đà',39000,50,'images/ga-ran-gion.jpg','Miếng gà rán giòn tan màu vàng đượm trên đĩa','Công thức tẩm ướp đặc biệt, thịt mềm ngọt mọng nước bên trong.','Thịt gà tươi, bột chiên giòn, bí quyết tẩm ướp 11 loại gia vị','1 - 2 người ăn',350,12,1);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(1,'Gà Rán Cay Hàn Quốc',42000,35,'images/ga-ran-cay-han-quoc.jpg','Gà rán phủ sốt cay Hàn Quốc rắc mè trên giấy nâu','Gà rán phủ sốt cay ngọt kiểu Hàn, rắc mè rang thơm.','Thịt gà tươi, sốt gochujang, mật ong, mè rang','1 - 2 người ăn',380,14,0);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(2,'Burger Gà Giòn',40000,70,'images/burger-ga-gion.jpg','Burger gà giòn kèm phô mai, cà chua và dưa chuột muối','Phi lê gà chiên giòn kẹp rau tươi và sốt mayonnaise béo nhẹ.','Bánh mì mềm, phi lê gà chiên giòn, xà lách, sốt mayonnaise','1 người ăn',480,8,0);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(2,'Burger Bò Đôi',59000,40,'images/burger-bo-doi.jpg','Burger bò đôi với hai lớp thịt bò nướng và rau tươi','Hai lớp thịt bò nướng, hai lớp phô mai dành cho người ăn khỏe.','Bánh mì mềm, 2 miếng thịt bò nướng, 2 lát phô mai, dưa chuột muối, sốt BBQ','1 người ăn',760,10,0);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(3,'Combo Gia Đình',189000,20,'images/combo-gia-dinh.jpg','Combo gia đình gồm gà rán, gà viên, mì Ý, khoai tây chiên và ba ly nước','Gà rán, gà viên, mì Ý sốt bò bằm, khoai tây chiên và ba ly nước cho cả nhà.','3 miếng gà rán, 1 phần gà viên, 1 đĩa mì Ý sốt bò bằm, 1 khoai tây chiên, 3 ly nước ngọt','3 - 4 người ăn',2400,20,1);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(3,'Combo Bạn Bè',119000,0,'images/combo-ban-be.jpg','Combo gồm gà rán, burger bò, khoai tây chiên và hai ly nước','Hai miếng gà rán, một burger bò phô mai, khoai tây chiên và hai ly nước.','2 miếng gà rán, 1 burger bò phô mai, 1 khoai tây chiên, 2 ly nước ngọt','2 người ăn',1500,18,0);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(4,'Khoai Tây Chiên Sốt Cà',32000,90,'images/khoai-tay-sot-ca.jpg','Đĩa khoai tây chiên vàng giòn kèm chén sốt cà chua','Khoai tây chiên vàng giòn dùng kèm sốt cà chua chua ngọt.','Khoai tây tươi, dầu thực vật, muối, sốt cà chua','1 - 2 người ăn',380,7,0);
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES(4,'Gà Viên Popcorn',30000,100,'images/ga-vien-popcorn.jpg','Đĩa gà viên popcorn chiên giòn vàng','Gà viên nhỏ chiên giòn, ăn vặt tiện lợi, chấm kèm sốt tùy chọn.','Thịt gà xay, bột chiên giòn, gia vị','1 người ăn',300,6,0);
-- Món bổ sung cho đủ >= 30 dòng; có tên chứa dấu nháy đơn (Levi's), ký tự % (50%), món hết hàng; danh mục 6 cố ý để trống.
INSERT INTO san_pham(danh_muc_id,ten,gia,ton_kho,anh,anh_mo_ta,mo_ta,thanh_phan,khau_phan,nang_luong_kcal,thoi_gian_phut,noi_bat) VALUES
(5,'Coca-Cola lon',15000,200,'images/coca-cola-lon.jpg','Coca-Cola lon','Nước ngọt có ga lon 330ml.','Đang cập nhật','1 người ăn',300,6,0),
(5,'Pepsi lon',15000,180,'images/pepsi-lon.jpg','Pepsi lon','Nước ngọt có ga lon 330ml.','Đang cập nhật','1 người ăn',300,6,0),
(5,'Trà đào cam sả',25000,60,'images/tra-dao-cam-sa.jpg','Trà đào cam sả','Trà đào thơm mát với cam và sả.','Đang cập nhật','1 người ăn',300,6,0),
(5,'Trà sữa trân châu',29000,50,'images/tra-sua-tran-chau.jpg','Trà sữa trân châu','Trà sữa béo nhẹ kèm trân châu dẻo.','Đang cập nhật','1 người ăn',300,6,0),
(5,'Nước cam ép',27000,40,'images/nuoc-cam-ep.jpg','Nước cam ép','Cam tươi ép nguyên chất.','Đang cập nhật','1 người ăn',300,6,0),
(5,'Nước suối',10000,0,'images/nuoc-suoi.jpg','Nước suối','Nước tinh khiết 500ml (tạm hết hàng).','Đang cập nhật','1 người ăn',300,6,0),
(5,'Sữa tươi lắc',22000,45,'images/sua-tuoi-lac.jpg','Sữa tươi lắc','Sữa tươi lắc đá mát lạnh.','Đang cập nhật','1 người ăn',300,6,0),
(4,'Salad rau trộn',28000,35,'images/salad-rau-tron.jpg','Salad rau trộn','Rau củ tươi trộn sốt mè rang.','Đang cập nhật','1 người ăn',300,6,0),
(4,'Phô mai que',30000,70,'images/pho-mai-que.jpg','Phô mai que','Phô mai chiên giòn kéo sợi.','Đang cập nhật','1 người ăn',300,6,0),
(4,'Hành tây chiên',27000,65,'images/hanh-tay-chien.jpg','Hành tây chiên','Hành tây tẩm bột chiên giòn.','Đang cập nhật','1 người ăn',300,6,0),
(4,'Xúc xích nướng',24000,55,'images/xuc-xich-nuong.jpg','Xúc xích nướng','Xúc xích nướng kèm sốt mù tạt.','Đang cập nhật','1 người ăn',300,6,0),
(1,'Gà Rán Sốt Phô Mai',45000,40,'images/ga-ran-sot-pho-mai.jpg','Gà Rán Sốt Phô Mai','Gà rán giòn phủ sốt phô mai béo.','Đang cập nhật','1 người ăn',300,6,0),
(1,'Gà Sốt Mật Ong',43000,38,'images/ga-sot-mat-ong.jpg','Gà Sốt Mật Ong','Gà rán áo sốt mật ong tỏi.','Đang cập nhật','1 người ăn',300,6,0),
(1,'Cánh Gà BBQ',41000,42,'images/canh-ga-bbq.jpg','Cánh Gà BBQ','Cánh gà nướng sốt BBQ khói.','Đang cập nhật','1 người ăn',300,6,0),
(2,'Burger Levi''s Đặc Biệt',62000,30,'images/burger-levis-dac-biet.jpg','Burger Levi''s Đặc Biệt','Burger bò đặc biệt theo công thức Levi''s của cửa hàng.','Đang cập nhật','1 người ăn',300,6,0),
(2,'Burger Cá Giòn',38000,45,'images/burger-ca-gion.jpg','Burger Cá Giòn','Phi lê cá chiên giòn kẹp rau tươi.','Đang cập nhật','1 người ăn',300,6,0),
(2,'Burger Gà Teriyaki',44000,40,'images/burger-ga-teriyaki.jpg','Burger Gà Teriyaki','Gà áo sốt teriyaki ngọt mặn.','Đang cập nhật','1 người ăn',300,6,0),
(2,'Burger Bò Nấm',52000,25,'images/burger-bo-nam.jpg','Burger Bò Nấm','Bò nướng với nấm xào bơ.','Đang cập nhật','1 người ăn',300,6,0),
(3,'Combo Trưa Giảm 50% Nước Uống',79000,30,'images/combo-trua.jpg','Combo Trưa Giảm 50% Nước Uống','Combo bữa trưa, nước uống giảm 50%.','Đang cập nhật','1 người ăn',300,6,0),
(3,'Combo Cặp Đôi',149000,25,'images/combo-cap-doi.jpg','Combo Cặp Đôi','Hai burger, khoai tây chiên và hai nước.','Đang cập nhật','1 người ăn',300,6,0),
(3,'Combo Học Sinh',55000,60,'images/combo-hoc-sinh.jpg','Combo Học Sinh','Gà rán, khoai nhỏ và nước ngọt giá mềm.','Đang cập nhật','1 người ăn',300,6,0);
INSERT INTO don_hang(id,nguoi_dung_id,trang_thai) VALUES(1,2,'hoan_tat'),(2,3,'moi');
INSERT INTO don_hang(id,nguoi_dung_id,trang_thai) VALUES(3,4,'moi'),(4,5,'moi'),(5,6,'moi'),(6,7,'moi'),(7,8,'moi');
INSERT INTO chi_tiet_don(don_hang_id,san_pham_id,so_luong,don_gia) VALUES(1,1,2,35000),(1,2,1,45000),(2,3,1,69000),(3,4,1,25000),(4,1,1,35000),(5,4,2,25000),(6,2,1,45000),(7,3,1,69000);
INSERT INTO lien_he(ho_ten,email,sdt,noi_dung) VALUES('Khách mẫu','demo@example.com','0900000000','Liên hệ mẫu để kiểm thử quản trị.');
INSERT INTO thanh_vien(mssv,ho_ten,nguoi_dung_id) VALUES('3120224008','Hoàng Gia Bảo',4);
INSERT INTO thanh_vien(mssv,ho_ten,nguoi_dung_id) VALUES('3120224053','Trần Hồ Trung Hậu',5);
INSERT INTO thanh_vien(mssv,ho_ten,nguoi_dung_id) VALUES('3120224069','Lê Công Huy',6);
INSERT INTO thanh_vien(mssv,ho_ten,nguoi_dung_id) VALUES('3120224110','Phạm Hồng Phong',7);
INSERT INTO thanh_vien(mssv,ho_ten,nguoi_dung_id) VALUES('3120224126','Nguyễn Hữu Tài',8);
INSERT INTO tv_hoang_gia_bao(mssv,ten_khach,loi_nhan) VALUES
('3120224008','Minh','Chúc nhóm hoàn thành tốt bài tập.'),
('3120224008','Lan','Trang cá nhân trình bày rất rõ ràng.');
INSERT INTO tv_luot_xem(mssv,ngay,so_luot) VALUES
('3120224008', CURDATE(), 3),
('3120224008', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 2);

INSERT INTO tv_tran_ho_trung_hau_cau_hoi
(mssv,noi_dung,lua_chon_a,lua_chon_b,lua_chon_c,lua_chon_d,dap_an_dung) VALUES
('3120224053','Hàm nào mã hóa ký tự đặc biệt trước khi in dữ liệu người dùng ra HTML?','strip_tags()','htmlspecialchars()','addslashes()','trim()','b'),
('3120224053','Sau khi xử lý POST thành công, nên làm gì để bấm F5 không gửi lại biểu mẫu?','In thông báo bằng echo','Chuyển hướng bằng header rồi exit','Gọi sleep()','Gọi session_destroy()','b'),
('3120224053','Hàm nào dùng để lưu mật khẩu dưới dạng băm an toàn?','md5()','base64_encode()','password_hash()','sha1()','c'),
('3120224053','setcookie() phải được gọi vào lúc nào?','Sau thẻ đóng HTML','Trước khi có bất kỳ output nào','Chỉ trong vòng lặp foreach','Sau session_destroy()','b');
INSERT INTO tv_tran_ho_trung_hau_ket_qua(mssv,diem,tong) VALUES('3120224053',3,4);
INSERT INTO tv_tran_ho_trung_hau_ngay_thi(mssv,ngay_thi) VALUES('3120224053','2026-12-20');

INSERT INTO tv_le_cong_huy_diem(mssv,a1,a2,a3,tong,xep_loai) VALUES
('3120224069',8.00,7.50,9.00,8.45,'B+');
INSERT INTO tv_le_cong_huy_muc_tieu(mssv,muc_tieu,hoan_thanh) VALUES
('3120224069','Hoàn thành chức năng CSDL cá nhân',0),
('3120224069','Ôn lại prepared statement',1);

INSERT INTO tv_pham_hong_phong_anh(mssv,duong_dan) VALUES
('3120224110','thanhvien/3120224110_phamhongphong/images/chan-dung-phong.jpg');
INSERT INTO tv_pham_hong_phong_thong_ke
(mssv,noi_dung,so_ky_tu,so_ky_tu_khong_cach,so_tu,so_cau,tu_dai_nhat,tu_hay_gap) VALUES
('3120224110','Foodspead là dự án môn lập trình web.',37,31,7,1,'Foodspead','[["foodspead",1],["là",1],["dự",1]]');

INSERT INTO tv_nguyen_huu_tai_ky_nang(mssv,ten,nhom,mo_ta,muc_do) VALUES
('3120224126','HTML5','web','Dựng cấu trúc trang bằng các thẻ ngữ nghĩa.',4),
('3120224126','CSS3','web','Trình bày và bố cục responsive cho trang web.',4),
('3120224126','JavaScript','web','Xử lý tương tác và dữ liệu phía trình duyệt.',4),
('3120224126','Lập trình web','lap-trinh','Xây dựng website từ giao diện đến xử lý dữ liệu.',4),
('3120224126','Lập trình ứng dụng','lap-trinh','Viết chương trình giải quyết một bài toán cụ thể.',3),
('3120224126','Python','lap-trinh','Ngôn ngữ dùng cho bài tập lập trình và xử lý dữ liệu.',4),
('3120224126','Git và GitHub','cong-cu','Quản lý phiên bản và phối hợp trong nhóm.',4),
('3120224126','Làm việc nhóm','ky-nang-mem','Phân công, trao đổi và hoàn thành việc chung.',5);
INSERT INTO tv_nguyen_huu_tai_tuy_chon(mssv,giao_dien) VALUES('3120224126','sang');
