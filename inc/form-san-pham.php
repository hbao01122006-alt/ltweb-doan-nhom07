<?php
// inc/form-san-pham.php - form dùng chung cho them-san-pham.php và sua-san-pham.php.
// Cần: $f (giá trị hiện tại), $loi (lỗi theo ô), $dsDm (danh mục), $anhHienTai (hoặc null), $nhanNut.
$o = static fn(string $k): string => isset($loi[$k]) ? '<span class="loi" role="alert">' . e($loi[$k]) . '</span>' : '';
?>
<form method="post" enctype="multipart/form-data" class="the">
  <p><label for="ten">Tên món</label><br>
    <input id="ten" name="ten" value="<?= e($f['ten'] ?? '') ?>" maxlength="200" required> <?= $o('ten') ?></p>
  <p><label for="dm">Danh mục</label><br>
    <select id="dm" name="danh_muc_id">
      <?php foreach ($dsDm as $d): ?>
        <option value="<?= (int) $d['id'] ?>"<?= (int) ($f['danh_muc_id'] ?? 0) === (int) $d['id'] ? ' selected' : '' ?>><?= e($d['ten']) ?></option>
      <?php endforeach; ?>
    </select> <?= $o('danh_muc_id') ?></p>
  <p><label for="gia">Giá (VNĐ, từ 1.000)</label><br>
    <input id="gia" type="number" name="gia" min="1000" value="<?= e($f['gia'] ?? '') ?>"> <?= $o('gia') ?></p>
  <p><label for="tk">Tồn kho</label><br>
    <input id="tk" type="number" name="ton_kho" min="0" max="9999" value="<?= e($f['ton_kho'] ?? 0) ?>"> <?= $o('ton_kho') ?></p>
  <p><label for="mt">Mô tả</label><br>
    <textarea id="mt" name="mo_ta" rows="5" maxlength="2000"><?= e($f['mo_ta'] ?? '') ?></textarea> <?= $o('mo_ta') ?></p>
  <p><label for="anh">Ảnh đại diện (JPG, PNG, WebP, tối đa 2 MB)</label><br>
    <input id="anh" type="file" name="anh" accept="image/jpeg,image/png,image/webp"> <?= $o('anh') ?>
    <?php if (!empty($anhHienTai)): ?><br><small>Ảnh hiện tại: <?= e($anhHienTai) ?> (chọn tệp mới để thay)</small><?php endif; ?></p>
  <button type="submit"><?= e($nhanNut) ?></button> <a href="quan-tri.php">Hủy</a>
</form>
