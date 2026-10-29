<?php
// gioithieu.php - Trang cá nhân của Phạm Hồng Phong, chứa hai chức năng CSDL MySQL theo yêu cầu bài tập.
// Chức năng CSDL 1: ảnh đại diện tải lên an toàn; đường dẫn ảnh hiện tại được lưu trong MySQL, chủ trang mới được sửa/xóa.
// Chức năng CSDL 2: thống kê văn bản ở máy chủ và lưu toàn bộ kết quả vào MySQL, có lịch sử gần nhất và thống kê tổng hợp.
// Cách thử: đăng nhập phong@foods.example / matkhau123 để đổi ảnh; nhập văn bản rồi F5 để kiểm tra PRG.
require __DIR__ . '/../../inc/config.php';
$goc='../../'; $lopTrang='trang-ca-nhan'; $cssRien='style.css'; $jsRien='js/canhan.js';
const MSSV='3120224110';
$lay=static fn(string $k): string => is_string($_POST[$k]??null)?trim($_POST[$k]):'';
$hanhDong=$_SERVER['REQUEST_METHOD']==='POST'?$lay('chuc_nang'):'';
$nd=nguoiDung();
$laChuTrang=(new App\Data\KhoThanhVien(db()))->laChuTrang(MSSV,$nd?(int)$nd['id']:null)||($nd['vai_tro']??'')==='quan_tri';
$thuMucAnh=THU_MUC_GOC.'/uploads';
$anhMacDinh='thanhvien/3120224110_phamhongphong/images/chan-dung-phong.jpg';
$st=db()->prepare('SELECT duong_dan FROM tv_pham_hong_phong_anh WHERE mssv=?'); $st->execute([MSSV]);
$duongDanDb=$st->fetchColumn(); $duongDanAnh=is_string($duongDanDb)?$duongDanDb:$anhMacDinh;

// Chức năng 1: upload file, nhưng trạng thái ảnh hiện tại được quản lý bằng CSDL thay vì cookie.
$loiAnh='';
$tongQuaLon=$_SERVER['REQUEST_METHOD']==='POST'&&!$_POST&&!$_FILES&&(int)($_SERVER['CONTENT_LENGTH']??0)>0;
if($tongQuaLon) $loiAnh='Dữ liệu gửi lên quá lớn, ảnh tối đa 2 MB.';
elseif($hanhDong==='avatar'||$hanhDong==='xoa-avatar'){
    if(!$laChuTrang){ http_response_code(403); require __DIR__.'/../../403.php'; exit; }
    if($hanhDong==='xoa-avatar'){
        if(str_starts_with($duongDanAnh,'uploads/phong_')){ $tep=THU_MUC_GOC.'/'.$duongDanAnh; if(is_file($tep)) unlink($tep); }
        db()->prepare('INSERT INTO tv_pham_hong_phong_anh (mssv,duong_dan) VALUES (?,?) ON DUPLICATE KEY UPDATE duong_dan=VALUES(duong_dan), cap_nhat=CURRENT_TIMESTAMP')->execute([MSSV,$anhMacDinh]);
        flash('phong_anh','Đã quay về ảnh mặc định.'); chuyenHuong('gioithieu.php#php-avatar');
    }
    $f=$_FILES['avatar']??null; $ma=(is_array($f)&&is_int($f['error']??null))?$f['error']:UPLOAD_ERR_NO_FILE;
    if($ma===UPLOAD_ERR_NO_FILE) $loiAnh='Hãy chọn một ảnh.';
    elseif($ma===UPLOAD_ERR_INI_SIZE||$ma===UPLOAD_ERR_FORM_SIZE) $loiAnh='Ảnh lớn hơn 2 MB.';
    elseif($ma!==UPLOAD_ERR_OK) $loiAnh='Tải ảnh không thành công.';
    elseif(!is_string($f['tmp_name']??null)||!is_uploaded_file($f['tmp_name'])) $loiAnh='Tệp tải lên không hợp lệ.';
    elseif((int)($f['size']??0)>2*1024*1024) $loiAnh='Ảnh lớn hơn 2 MB.';
    else{
        $kieu=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']); $duoi=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$kieu]??null;
        if($duoi===null) $loiAnh='Chỉ nhận JPG, PNG hoặc WebP.';
        else{
            if(!is_dir($thuMucAnh)) mkdir($thuMucAnh,0775,true);
            $ten='phong_'.bin2hex(random_bytes(12)).'.'.$duoi; $duongMoi='uploads/'.$ten;
            if(move_uploaded_file($f['tmp_name'],$thuMucAnh.'/'.$ten)){
                db()->prepare('INSERT INTO tv_pham_hong_phong_anh (mssv,duong_dan) VALUES (?,?) ON DUPLICATE KEY UPDATE duong_dan=VALUES(duong_dan), cap_nhat=CURRENT_TIMESTAMP')->execute([MSSV,$duongMoi]);
                if(str_starts_with($duongDanAnh,'uploads/phong_')){ $cu=THU_MUC_GOC.'/'.$duongDanAnh; if(is_file($cu)) unlink($cu); }
                flash('phong_anh','Đã cập nhật ảnh và lưu đường dẫn vào CSDL.'); chuyenHuong('gioithieu.php#php-avatar');
            } else $loiAnh='Không lưu được ảnh lên máy chủ.';
        }
    }
}
$tbAnh=flash('phong_anh');
$st=db()->prepare('SELECT duong_dan FROM tv_pham_hong_phong_anh WHERE mssv=?'); $st->execute([MSSV]); $duongDanDb=$st->fetchColumn();
$duongDanAnh=is_string($duongDanDb)?$duongDanDb:$anhMacDinh;

// Chức năng 2: thống kê văn bản và lưu kết quả trong CSDL.
$thuong=static fn(string $s):string=>mb_strtolower($s,'UTF-8'); $duVanBan=''; $loiVanBan='';
if($hanhDong==='thong-ke'){
    $duVanBan=str_replace("\r\n","\n",$lay('van_ban'));
    if($duVanBan==='') $loiVanBan='Hãy nhập văn bản cần thống kê.';
    elseif(preg_match('//u',$duVanBan)!==1) $loiVanBan='Văn bản cần là UTF-8 hợp lệ.';
    elseif(mb_strlen($duVanBan)>1000) $loiVanBan='Văn bản tối đa 1000 ký tự.';
    else{
        preg_match_all('/[\p{L}\p{N}]+(?:[\'’-][\p{L}\p{N}]+)*/u',$duVanBan,$kq); $cacTu=$kq[0];
        $tanSo=array_count_values(array_map($thuong,$cacTu)); arsort($tanSo); $tuDai=''; foreach($cacTu as $tu) if(mb_strlen($tu)>mb_strlen($tuDai)) $tuDai=$tu;
        $hayGap=array_slice(array_map(static fn($k,$v)=>[mb_substr((string)$k,0,40),$v],array_keys($tanSo),$tanSo),0,3); $tuDai=mb_substr($tuDai,0,200);   // cắt cho vừa cột VARCHAR(200) / VARCHAR(500), tránh lỗi 1406
        $soKyTu=mb_strlen($duVanBan); $khongCach=mb_strlen((string)preg_replace('/\s+/u','',$duVanBan)); $soTu=count($cacTu); $soCau=max(1,(int)preg_match_all('/[.!?…]+(?=\s|$)/u',$duVanBan));
        $st=db()->prepare('INSERT INTO tv_pham_hong_phong_thong_ke (mssv,noi_dung,so_ky_tu,so_ky_tu_khong_cach,so_tu,so_cau,tu_dai_nhat,tu_hay_gap) VALUES (?,?,?,?,?,?,?,?)');
        $st->execute([MSSV,$duVanBan,$soKyTu,$khongCach,$soTu,$soCau,$tuDai,json_encode($hayGap,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)]);
        flash('phong_tk','Đã thống kê và lưu kết quả vào CSDL.'); chuyenHuong('gioithieu.php#php-thong-ke');
    }
}
$tbThongKe=flash('phong_tk');
$st=db()->prepare('SELECT noi_dung,so_ky_tu,so_ky_tu_khong_cach,so_tu,so_cau,tu_dai_nhat,tu_hay_gap,thoi_gian FROM tv_pham_hong_phong_thong_ke WHERE mssv=? ORDER BY thoi_gian DESC,id DESC LIMIT 5');
$st->execute([MSSV]); $lichSuTk=$st->fetchAll(); $kqThongKe=$lichSuTk[0]??null;
$st=db()->prepare('SELECT COUNT(*) AS so_lan,COALESCE(AVG(so_tu),0) AS tb_tu,COALESCE(MAX(so_tu),0) AS max_tu FROM tv_pham_hong_phong_thong_ke WHERE mssv=?'); $st->execute([MSSV]); $tongHop=$st->fetch();

$tieuDe='Phạm Hồng Phong'; $trang=''; require __DIR__.'/../../inc/header.php';
?>
<main class="noi-dung">
<h1 class="tieu-de-chinh">Giới thiệu thành viên: Phạm Hồng Phong</h1>
<section class="the php-box" aria-labelledby="php-tieu-de">
  <h2 id="php-tieu-de">Ảnh đại diện và thống kê văn bản bằng MySQL</h2>
  <h3 id="php-avatar">1. Ảnh đại diện có trạng thái lưu CSDL</h3>
  <p><img src="<?= e($goc.$duongDanAnh) ?>" alt="Ảnh đại diện hiện tại" width="120" height="120"></p>
  <?php if($tbAnh): ?><p class="tb-ok" role="status"><?= e($tbAnh) ?></p><?php endif; ?>
  <?php if($laChuTrang): ?><form method="post" action="gioithieu.php#php-avatar" enctype="multipart/form-data"><input type="hidden" name="chuc_nang" value="avatar"><p><label for="php-avatar-file">Chọn JPG, PNG hoặc WebP, tối đa 2 MB</label><br><input id="php-avatar-file" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" required></p><?php if($loiAnh): ?><p class="loi" role="alert"><?= e($loiAnh) ?></p><?php endif; ?><button type="submit">Tải ảnh lên</button></form><form method="post" action="gioithieu.php#php-avatar"><button name="chuc_nang" value="xoa-avatar" type="submit">Dùng ảnh mặc định</button></form><?php else: ?><p><small>Chỉ chủ trang được thay ảnh đại diện.</small></p><?php endif; ?>

  <h3 id="php-thong-ke">2. Thống kê văn bản và lịch sử CSDL</h3>
  <?php if($tbThongKe): ?><p class="tb-ok" role="status"><?= e($tbThongKe) ?></p><?php endif; ?>
  <form method="post" action="gioithieu.php#php-thong-ke"><input type="hidden" name="chuc_nang" value="thong-ke"><p><label for="php-van-ban">Văn bản, tối đa 1000 ký tự</label><br><textarea id="php-van-ban" name="van_ban" rows="5" maxlength="1000" required><?= e($duVanBan) ?></textarea></p><?php if($loiVanBan): ?><p class="loi" role="alert"><?= e($loiVanBan) ?></p><?php endif; ?><button type="submit">Thống kê và lưu</button></form>
  <p>Đã lưu <?= e($tongHop['so_lan']) ?> lần; trung bình <?= e(number_format((float)$tongHop['tb_tu'],1,',','.')) ?> từ; dài nhất <?= e($tongHop['max_tu']) ?> từ.</p>
  <?php if($kqThongKe): $top=json_decode((string)$kqThongKe['tu_hay_gap'],true); ?><div class="tb-ok"><p>Kết quả gần nhất (<?= e($kqThongKe['thoi_gian']) ?>):</p><ul><li><?= e($kqThongKe['so_ky_tu']) ?> ký tự; <?= e($kqThongKe['so_tu']) ?> từ; <?= e($kqThongKe['so_cau']) ?> câu</li><li>Từ dài nhất: <?= e($kqThongKe['tu_dai_nhat']) ?></li><?php if(is_array($top)): foreach($top as $x): if(is_array($x)&&count($x)>=2): ?><li>Từ gặp nhiều: <?= e($x[0]) ?> — <?= e($x[1]) ?> lần</li><?php endif; endforeach; endif; ?></ul></div><?php endif; ?>
</section>

      <!-- BẢNG ĐIỀU KHIỂN TƯƠNG TÁC KÍNH LÚP VÀ LINH VẬT F-O-G -->
      <section class="the thanh-cong-cu-phong">
        <h2>🛠 Bảng điều khiển tương tác</h2>
        
        <!-- Chức năng Kính lúp soi phóng to chữ -->
        <div class="nhom-nut-chuc-nang">
          <button id="btn-kinh-lup" type="button" class="btn-tuong-tac">🔍 Bật Kính Lúp Soi Chữ</button>
        </div>

        <!-- Tương tác Linh vật Con Heo bộ 3 phím F - O - G -->
        <div class="khu-vuc-linh-vat-nut">
          <p class="tieu-de-nut-pet">🐷 Tương tác Linh vật Con Heo (Bấm nút bên dưới hoặc nhấn phím F - O - G):</p>
          <div class="nhom-3-nut-pet">
            <button id="btn-pet-f" type="button" class="btn-tuong-tac btn-pet">F (Phóng to Heo)</button>
            <button id="btn-pet-o" type="button" class="btn-tuong-tac btn-pet">O (Oai phong xoay tròn)</button>
            <button id="btn-pet-g" type="button" class="btn-tuong-tac btn-pet">G (Ghé góc màn hình)</button>
          </div>
        </div>
      </section>

      <section class="the gioi-thieu-ban-than">

        <h2>Giới thiệu bản thân</h2>

        <figure class="chan-dung">
          <img
            class="chan-dung__anh"
            src="images/chan-dung-phong.jpg"
            alt="Ảnh chân dung Phạm Hồng Phong"
            width="200"
            height="250">

          <figcaption>
            Chân dung Phạm Hồng Phong - Thành viên Nhóm 07
          </figcaption>
        </figure>

        <p>
          Xin chào! Tôi là Phạm Hồng Phong, sinh viên Khoa Toán - Tin,
          Trường Đại học Sư phạm - Đại học Đà Nẵng.
          Tôi đam mê phát triển phần mềm và thiết kế giao diện web tối ưu trải nghiệm người dùng.
        </p>

        <h3>Danh sách kỹ năng cá nhân</h3>

        <!-- Ô tìm kiếm kỹ năng -->
        <div class="khu-tim-kiem-ky-nang">
          <label for="o-tim-kiem-ky-nang" class="nhan-tim-kiem">🔍 Tìm kiếm kỹ năng:</label>
          <input
            type="text"
            id="o-tim-kiem-ky-nang"
            class="input-tim-kiem"
            placeholder="Nhập từ khóa (vd: HTML, Git, JS)...">
          <p id="thong-bao-ky-nang" class="thong-bao-tim-kiem" aria-live="polite"></p>
        </div>

        <ul class="danh-sach-ky-nang">
          <li>Lập trình web: HTML5, CSS3, JavaScript</li>
          <li>Quản lý nguồn mã: Git, GitHub</li>
          <li>Thiết kế giao diện Responsive và Tối ưu trải nghiệm (UI/UX)</li>
          <li>Kỹ năng giải quyết vấn đề và tư duy thuật toán</li>
        </ul>

      </section>

      <article class="the du-an-so-thich">

        <h2>Dự án &amp; Sở thích</h2>

        <p>
          Tôi đảm nhận xây dựng trang cá nhân và tham gia hoàn thiện giao diện đồ án FastFood của Nhóm 07.
        </p>

        <h3>Sở thích cá nhân</h3>

        <ul class="danh-sach-so-thich">
          <li>Chơi Game</li>
          <li>Xem Phim</li>
          <li>Chơi Thể Thao</li>
          <li>Nghe Nhạc thư giãn</li>
          <li>Khám phá các công nghệ mới</li>
        </ul>

      </article>

      <section class="the khu-thoi-khoa-bieu">

        <h2>Thời khóa biểu học tập trong tuần</h2>

        <table class="thoi-khoa-bieu">

          <caption>
            Bảng 1: Thời khóa biểu học tập các ngày trong tuần của Phạm Hồng Phong
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

      </section>

    
</main>
<?php require __DIR__ . '/../../inc/footer.php'; ?>
