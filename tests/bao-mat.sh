#!/usr/bin/env bash
# Test script ONLY for localhost and the team's OWN website. Never put CSRF/session token into this file.
set -euo pipefail
BASE="${BASE_URL:-http://localhost:8000}"
case "$BASE" in
  http://localhost:*|http://127.0.0.1:*|https://localhost:*|https://127.0.0.1:*) ;;
  *) echo 'Chi duoc kiem thu tren localhost (website cua nhom).' >&2; exit 2 ;;
esac
printf 'BASE_URL=%s\n' "$BASE"
report() { label="$1"; shift; printf '%-38s ' "$label"; curl -sS -o /dev/null -w '%{http_code}\n' "$@"; }
report '01 headers trang chu' -I "$BASE/index.php"
report '02 POST thieu csrf' -X POST -d 'email=demo%40foods.example&mat_khau=sai' "$BASE/dang-nhap.php"
report '02b POST csrf=abc' -X POST -d 'csrf=abc&email=demo%40foods.example&mat_khau=sai' "$BASE/dang-nhap.php"
report '02c POST csrf array' -X POST -d 'csrf%5B%5D=abc' "$BASE/dang-nhap.php"
report '03 API JSON thieu header' -X POST -H 'Content-Type: application/json' -d '{"id":1,"sl":1}' "$BASE/api/gio-hang.php"
report '04 GET xoa-san-pham' "$BASE/xoa-san-pham.php?id=1"
report '04b GET dang-xuat' "$BASE/dang-xuat.php"
report '08 tham so mang danh-sach' -g "$BASE/danh-sach.php?q[]=ao"
report '08b tham so mang chi-tiet' -g "$BASE/chi-tiet.php?id[]=1"
report '08c trang ca nhan mang' -g "$BASE/thanhvien/3120224008_hoanggiabao/gioithieu.php?trang[]=1"
report '10 tep noi bo (php -S bo qua .htaccess)' "$BASE/inc/db.php"
report '13 FoodSpead: GET dat-hang khong tao don' "$BASE/dat-hang.php?xac_nhan=1"
if [[ -n "${PHPSESSID:-}" && -n "${CSRF_TOKEN:-}" ]]; then
  # Chu y: bat buoc dung cookie va token CUNG MOT phien; khong ghi gia tri ra log.
  report '03b API JSON csrf dung' -b "PHPSESSID=${PHPSESSID}" -H "X-CSRF-Token: ${CSRF_TOKEN}" -H 'Content-Type: application/json' -d '{"id":1,"sl":1}' "$BASE/api/gio-hang.php"
  report '03c POST san-pham GET-only' -b "PHPSESSID=${PHPSESSID}" -H "X-CSRF-Token: ${CSRF_TOKEN}" -X POST "$BASE/api/san-pham.php"
  report '14 FoodSpead: khach doi trang thai don' -b "PHPSESSID=${PHPSESSID}" -d "csrf=${CSRF_TOKEN}&id=1&trang_thai=hoan_tat" "$BASE/don-hang.php"
else
  echo 'Bo qua 03b, 14: can export PHPSESSID va CSRF_TOKEN tu MOT phien da dang nhap.'
fi
printf 'Chi la lenh goi thu. Can kiem tra du lieu DB, response, log va ghi ket qua thuc te vao bao cao.\n'
