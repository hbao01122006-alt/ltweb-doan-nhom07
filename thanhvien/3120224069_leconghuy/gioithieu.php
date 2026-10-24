<?php
// gioithieu.php - Trang cá nhân của Lê Công Huy, chứa hai chức năng CSDL MySQL theo yêu cầu bài tập.
// Chức năng CSDL 1: máy tính điểm học phần; mỗi kết quả hợp lệ được lưu MySQL và hiển thị lịch sử gần nhất.
// Chức năng CSDL 2: mục tiêu học tập của Bài 5 được chuyển từ session sang bảng MySQL; chủ trang mới được sửa/xóa.
// Cách thử: tính điểm rồi F5; đăng nhập huy@foods.example / matkhau123 để thêm, đổi trạng thái hoặc xóa mục tiêu.
require __DIR__ . '/../../inc/config.php';
$goc='../../'; $lopTrang='trang-ca-nhan'; $cssRien='style.css'; $jsRien='js/canhan.js';
const MSSV='3120224069';
$lay=static fn(string $k): string => is_string($_POST[$k] ?? null) ? trim($_POST[$k]) : '';
$hanhDong=$_SERVER['REQUEST_METHOD']==='POST' ? $lay('chuc_nang') : '';
$nd=nguoiDung();
$laChuTrang=(new App\Data\KhoThanhVien(db()))->laChuTrang(MSSV,$nd?(int)$nd['id']:null)||($nd['vai_tro']??'')==='quan_tri';
$xepLoai=static function(float $d): string { return match(true){$d>=8.5=>'A',$d>=8.0=>'B+',$d>=7.0=>'B',$d>=6.5=>'C+',$d>=5.5=>'C',$d>=5.0=>'D+',$d>=4.0=>'D',default=>'F'}; };

// Chức năng 1: tính điểm và lưu lịch sử vào CSDL.
$nhanDiem=['a1'=>'chuyên cần (10%)','a2'=>'giữa kỳ (30%)','a3'=>'cuối kỳ (60%)']; $du=['a1'=>'','a2'=>'','a3'=>'']; $loiGpa=[];
if($hanhDong==='gpa'){
    $diem=[];
    foreach($nhanDiem as $k=>$nhan){
        $du[$k]=$lay($k); $chuan=str_replace(',','.',$du[$k]); $so=filter_var($chuan,FILTER_VALIDATE_FLOAT);
        if($chuan==='') $loiGpa[$k]="Hãy nhập điểm $nhan.";
        elseif(mb_strlen($chuan)>6||$so===false) $loiGpa[$k]="Điểm $nhan phải là số.";
        elseif($so<0||$so>10) $loiGpa[$k]="Điểm $nhan phải từ 0 đến 10.";
        else $diem[$k]=(float)$so;
    }
    if(!$loiGpa){
        $tong=round(0.1*$diem['a1']+0.3*$diem['a2']+0.6*$diem['a3'],2); $loai=$xepLoai($tong);
        $st=db()->prepare('INSERT INTO tv_le_cong_huy_diem (mssv,a1,a2,a3,tong,xep_loai) VALUES (?,?,?,?,?,?)');
        $st->execute([MSSV,$diem['a1'],$diem['a2'],$diem['a3'],$tong,$loai]);
        flash('huy_diem',"Đã tính và lưu: $tong - xếp loại $loai."); chuyenHuong('gioithieu.php#php-diem');
    }
}
$tbDiem=flash('huy_diem');
$st=db()->prepare('SELECT a1,a2,a3,tong,xep_loai,thoi_gian FROM tv_le_cong_huy_diem WHERE mssv=? ORDER BY thoi_gian DESC,id DESC LIMIT 5');
$st->execute([MSSV]); $lichSuDiem=$st->fetchAll();
$st=db()->prepare('SELECT COUNT(*) AS so_lan, COALESCE(AVG(tong),0) AS trung_binh FROM tv_le_cong_huy_diem WHERE mssv=?');
$st->execute([MSSV]); $tkDiem=$st->fetch();

// Chức năng 2: mục tiêu học tập chuyển từ session Bài 5 sang bảng MySQL.
$duMucTieu=''; $loiMucTieu='';
if(in_array($hanhDong,['them-muc-tieu','doi-muc-tieu','xoa-muc-tieu','xoa-het-muc-tieu'],true)){
    if(!$laChuTrang){ http_response_code(403); require __DIR__.'/../../403.php'; exit; }
    if($hanhDong==='them-muc-tieu'){
        $duMucTieu=preg_replace('/\s+/u',' ',$lay('muc_tieu'))??'';
        if(mb_strlen($duMucTieu)<3||mb_strlen($duMucTieu)>120) $loiMucTieu='Mục tiêu phải từ 3 đến 120 ký tự.';
        else{
            $st=db()->prepare('SELECT COUNT(*) FROM tv_le_cong_huy_muc_tieu WHERE mssv=? AND muc_tieu=?'); $st->execute([MSSV,$duMucTieu]);
            if((int)$st->fetchColumn()>0) $loiMucTieu='Mục tiêu này đã tồn tại.';
            else{ db()->prepare('INSERT INTO tv_le_cong_huy_muc_tieu (mssv,muc_tieu) VALUES (?,?)')->execute([MSSV,$duMucTieu]); flash('huy_mt','Đã thêm mục tiêu.'); chuyenHuong('gioithieu.php#php-muc-tieu'); }
        }
    } elseif($hanhDong==='xoa-het-muc-tieu'){
        db()->prepare('DELETE FROM tv_le_cong_huy_muc_tieu WHERE mssv=?')->execute([MSSV]); flash('huy_mt','Đã xóa toàn bộ mục tiêu.'); chuyenHuong('gioithieu.php#php-muc-tieu');
    } else {
        $id=filter_var($_POST['id']??'',FILTER_VALIDATE_INT);
        if($id){
            $sql=$hanhDong==='doi-muc-tieu'?'UPDATE tv_le_cong_huy_muc_tieu SET hoan_thanh=1-hoan_thanh WHERE id=? AND mssv=?':'DELETE FROM tv_le_cong_huy_muc_tieu WHERE id=? AND mssv=?';
            db()->prepare($sql)->execute([$id,MSSV]);
        }
        flash('huy_mt',$hanhDong==='doi-muc-tieu'?'Đã đổi trạng thái.':'Đã xóa mục tiêu.'); chuyenHuong('gioithieu.php#php-muc-tieu');
    }
}
$tbMucTieu=flash('huy_mt');
$st=db()->prepare('SELECT id,muc_tieu,hoan_thanh,thoi_gian FROM tv_le_cong_huy_muc_tieu WHERE mssv=? ORDER BY hoan_thanh,id DESC');
$st->execute([MSSV]); $dsMucTieu=$st->fetchAll();

$tieuDe='Lê Công Huy'; $trang=''; require __DIR__.'/../../inc/header.php';
?>
<main class="noi-dung">
<h1 class="tieu-de-chinh">Giới thiệu thành viên: Lê Công Huy</h1>
<section class="the php-box" aria-labelledby="php-tieu-de">
  <h2 id="php-tieu-de">Điểm học phần và mục tiêu bằng MySQL</h2>
  <h3 id="php-diem">1. Máy tính điểm học phần + lịch sử CSDL</h3>
  <?php if($tbDiem): ?><p class="tb-ok" role="status"><?= e($tbDiem) ?></p><?php endif; ?>
  <form method="post" action="gioithieu.php#php-diem"><input type="hidden" name="chuc_nang" value="gpa">
    <?php foreach($nhanDiem as $k=>$nhan): ?><p><label for="php-<?= e($k) ?>">Điểm <?= e($nhan) ?></label><br><input id="php-<?= e($k) ?>" name="<?= e($k) ?>" type="text" inputmode="decimal" maxlength="6" required value="<?= e($du[$k]) ?>"></p><?php if(isset($loiGpa[$k])): ?><p class="loi" role="alert"><?= e($loiGpa[$k]) ?></p><?php endif; ?><?php endforeach; ?>
    <button type="submit">Tính và lưu điểm</button>
  </form>
  <p>Đã lưu <?= e($tkDiem['so_lan']) ?> lần; điểm trung bình: <?= e(number_format((float)$tkDiem['trung_binh'],2,',','.')) ?>.</p>
  <?php if($lichSuDiem): ?><table><caption>5 kết quả gần nhất</caption><thead><tr><th scope="col">CC</th><th scope="col">GK</th><th scope="col">CK</th><th scope="col">Tổng</th><th scope="col">Loại</th><th scope="col">Thời gian</th></tr></thead><tbody><?php foreach($lichSuDiem as $r): ?><tr><td><?= e($r['a1']) ?></td><td><?= e($r['a2']) ?></td><td><?= e($r['a3']) ?></td><td><?= e($r['tong']) ?></td><td><?= e($r['xep_loai']) ?></td><td><?= e($r['thoi_gian']) ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?>

  <h3 id="php-muc-tieu">2. Mục tiêu học tập lưu MySQL</h3>
  <?php if($tbMucTieu): ?><p class="tb-ok" role="status"><?= e($tbMucTieu) ?></p><?php endif; ?>
  <?php if($laChuTrang): ?><form method="post" action="gioithieu.php#php-muc-tieu"><input type="hidden" name="chuc_nang" value="them-muc-tieu"><p><label for="php-mt">Mục tiêu mới</label><br><input id="php-mt" name="muc_tieu" maxlength="120" required value="<?= e($duMucTieu) ?>"></p><?php if($loiMucTieu): ?><p class="loi" role="alert"><?= e($loiMucTieu) ?></p><?php endif; ?><button type="submit">Thêm mục tiêu</button></form><?php else: ?><p><small>Đăng nhập đúng tài khoản chủ trang để quản lý mục tiêu.</small></p><?php endif; ?>
  <?php if(!$dsMucTieu): ?><p>Chưa có mục tiêu.</p><?php else: ?><ul><?php foreach($dsMucTieu as $m): ?><li><?= e($m['muc_tieu']) ?> — <?= (int)$m['hoan_thanh']===1?'Đã xong':'Đang làm' ?><?php if($laChuTrang): ?> <form method="post" action="gioithieu.php#php-muc-tieu" class="form-dong"><input type="hidden" name="id" value="<?= e($m['id']) ?>"><button name="chuc_nang" value="doi-muc-tieu" type="submit">Đổi trạng thái</button><button name="chuc_nang" value="xoa-muc-tieu" type="submit">Xóa</button></form><?php endif; ?></li><?php endforeach; ?></ul><?php endif; ?>
  <?php if($laChuTrang && $dsMucTieu): ?><form method="post" action="gioithieu.php#php-muc-tieu"><button name="chuc_nang" value="xoa-het-muc-tieu" type="submit">Xóa hết mục tiêu</button></form><?php endif; ?>
</section>

        <!-- TIỆN ÍCH CÁ NHÂN LÊ CÔNG HUY -->
        <section class="the tien-ich-huy">
            <h2>🎧 Tiện ích học tập &amp; Linh vật Gấu trúc</h2>

            <!-- Tính năng 1: Bộ phát nhạc -->
            <div class="khu-vuc-lofi">
                <h3>🎵 Bộ phát âm thanh nền tập trung học tập</h3>

                <div class="bo-phat-nhac">
                    <button id="btn-phat-nhac"
                            type="button"
                            class="btn-lofi">
                        ▶️ Bật âm thanh thư giãn (White Noise)
                    </button>

                    <div class="dieu-chinh-am-luong">
                        <label for="thanh-am-luong">🔊 Âm lượng:</label>

                        <input type="range"
                               id="thanh-am-luong"
                               min="0"
                               max="1"
                               step="0.1"
                               value="0.5">
                    </div>
                </div>

                <p id="trang-thai-nhac" class="ghi-chu-lofi">
                    Âm thanh đang tắt.
                </p>
            </div>

            <hr class="duong-phan-cach">

            <!-- Tính năng 2: Bộ tính điểm GPA -->
            <div class="khu-vuc-gpa">
                <h3>🧮 Máy tính điểm môn học dự kiến (Hệ 10 &amp; Hệ 4)</h3>

                <form id="form-tinh-gpa"
                      class="form-gpa"
                      onsubmit="return false;">

                    <div class="hang-nhap">
                        <label for="diem-chuyen-can">
                            Điểm chuyên cần (10%):
                        </label>

                        <input type="number"
                               id="diem-chuyen-can"
                               min="0"
                               max="10"
                               step="0.1"
                               placeholder="Ví dụ: 9">
                    </div>

                    <div class="hang-nhap">
                        <label for="diem-giua-ky">
                            Điểm giữa kỳ (30%):
                        </label>

                        <input type="number"
                               id="diem-giua-ky"
                               min="0"
                               max="10"
                               step="0.1"
                               placeholder="Ví dụ: 8">
                    </div>

                    <div class="hang-nhap">
                        <label for="diem-cuoi-ky">
                            Điểm cuối kỳ (60%):
                        </label>

                        <input type="number"
                               id="diem-cuoi-ky"
                               min="0"
                               max="10"
                               step="0.1"
                               placeholder="Ví dụ: 8.5">
                    </div>

                    <button id="btn-tinh-gpa"
                            type="button"
                            class="btn-tinh">
                        Tính kết quả
                    </button>
                </form>

                <div id="ket-qua-gpa" class="ket-qua-gpa-box">
                    Nhập điểm các thành phần để xem điểm tổng kết môn!
                </div>
            </div>

            <hr class="duong-phan-cach">

            <!-- Bổ sung: Điều khiển Gấu trúc -->
            <div class="khu-vuc-tro-choi">
                <h3>🐼 Điều khiển Linh vật Gấu trúc</h3>

                <p class="mo-ta-tro-choi">
                    Dùng <strong>4 phím mũi tên (⬅️ ➡️ ⬆️ ⬇️)</strong>
                    để di chuyển Gấu trúc trên màn hình hoặc nhấn các phím tương ứng:
                </p>

                <div class="nhom-nut-chuc-nang">
                    <button id="btn-panda-h"
                            type="button"
                            class="btn-tuong-tac btn-panda">
                        H (Chào hỏi 👋)
                    </button>

                    <button id="btn-panda-u"
                            type="button"
                            class="btn-tuong-tac btn-panda">
                        U (Lời chúc ✨)
                    </button>

                    <button id="btn-panda-y"
                            type="button"
                            class="btn-tuong-tac btn-panda">
                        Y (Động lực 💪)
                    </button>
                </div>
            </div>
        </section>

        <!-- GIỚI THIỆU BẢN THÂN -->
        <section class="the gioi-thieu-ban-than">
            <h2>Giới thiệu bản thân</h2>

            <figure class="chan-dung">
                <img src="images/chan-dung-huy.jpg"
                     alt="Ảnh chân dung Lê Công Huy"
                     width="200"
                     height="271"
                     class="chan-dung__anh">

                <figcaption>
                    Chân dung Lê Công Huy - Thành viên Nhóm 07
                </figcaption>
            </figure>

            <p>
                <strong>Họ và tên:</strong> Lê Công Huy
            </p>

            <p>
                <strong>Mã sinh viên:</strong> 3120224069
            </p>

            <p>
                Xin chào! Tôi là Lê Công Huy, sinh viên Khoa Toán - Tin,
                Trường Đại học Sư phạm - Đại học Đà Nẵng. Tôi có niềm đam mê lớn
                với công nghệ thông tin và thiết kế giao diện web.
            </p>

            <h3>Danh sách kỹ năng cá nhân</h3>

            <ul class="danh-sach-ky-nang">
                <li>Lập trình web nền tảng: HTML5, CSS3, JavaScript</li>
                <li>Tối ưu hóa mã nguồn và cấu trúc ngữ nghĩa HTML</li>
                <li>Sử dụng thành thạo Git và quản lý kho lưu trữ GitHub</li>
                <li>Kỹ năng giải quyết vấn đề và làm việc nhóm</li>
            </ul>
        </section>

        <!-- DỰ ÁN VÀ SỞ THÍCH -->
        <article class="the du-an-so-thich">
            <h2>Đóng góp trong dự án Foodspead &amp; Sở thích</h2>

            <p>
                Trong đồ án xây dựng website đặt đồ ăn nhanh Foodspead, tôi đảm nhận
                việc biên soạn và tối ưu hóa file <code>danh-sach.php</code>
                (Trang Thực đơn), đảm bảo cấu trúc bảng chuẩn accessibility và validation.
            </p>

            <h3>Sở thích</h3>

            <ul class="danh-sach-so-thich">
                <li>Chơi game</li>
                <li>Đọc truyện anime</li>
                <li>Nghe nhạc</li>
            </ul>
        </article>

        <!-- THỜI KHÓA BIỂU -->
        <section class="the khu-thoi-khoa-bieu">
            <h2>Thời khóa biểu học tập trong tuần</h2>

            <div class="khung-bang">
                <table class="thoi-khoa-bieu">

                    <caption>
                        Thời khóa biểu học tập của Lê Công Huy
                    </caption>

                    <thead>
                        <tr>
                            <th scope="col">Buổi</th>
                            <th scope="col">Thứ 2</th>
                            <th scope="col">Thứ 3</th>
                            <th scope="col">Thứ 4</th>
                            <th scope="col">Thứ 5</th>
                            <th scope="col">Thứ 6</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <th scope="row">Sáng</th>
                            <td>Lập trình Web</td>
                            <td>Chủ nghĩa XH-KH</td>
                            <td>Lập trình Web</td>
                            <td>Tự học HTML/CSS</td>
                            <td>Thực hành Web</td>
                        </tr>

                        <tr>
                            <th scope="row">Chiều</th>
                            <td>Tự học Java</td>
                            <td>Thể dục</td>
                            <td>Học Tiếng Anh</td>
                            <td>Kinh tế chính trị</td>
                            <td>Họp Nhóm 07</td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </section>

    
</main>
<?php require __DIR__ . '/../../inc/footer.php'; ?>
