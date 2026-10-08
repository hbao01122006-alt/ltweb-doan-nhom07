<?php
// src/Data/KhoLienHe.php - luu lien he theo dang JSON Lines trong storage/.
namespace App\Data;

use RuntimeException;

final class KhoLienHe
{
    public function __construct(private readonly string $tep) {}

    public function them(array $lh): void
    {
        $thuMuc = dirname($this->tep);
        if (!is_dir($thuMuc) && !mkdir($thuMuc, 0775, true) && !is_dir($thuMuc)) {
            throw new RuntimeException('Không tạo được thư mục lưu liên hệ.');
        }
        $dong = json_encode($lh, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (file_put_contents($this->tep, $dong . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException('Không lưu được liên hệ.');
        }
    }

    /** @return array<int,array<string,mixed>> */
    public function tatCa(): array
    {
        if (!is_file($this->tep)) return [];
        $dong = file($this->tep, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($dong === false) return [];
        $ds = [];
        foreach ($dong as $d) {
            $m = json_decode($d, true);
            if (is_array($m)) $ds[] = $m;
        }
        return array_reverse($ds);
    }
}
