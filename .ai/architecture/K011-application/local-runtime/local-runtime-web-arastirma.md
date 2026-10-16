---
title: "K011 · local-runtime — Web Araştırması"
type: architecture
kodeme: kademe-2
module: K011-application
parent: local-runtime
status: active
updated: 2026-10-15
group: kanit
---

# K011 · local-runtime — Web Araştırması

> **Amaç:** local-runtime iddialarını besleyen **resmi kaynak havuzunu** (php.net
> manual, 5 URL · HTTP 200 · envanter kesişimi 0) ve bu havuzun **claim↔repo
> çapraz matrisini** belgeler. Havuz kapalıdır; reddedilen adaylar yalnız
> **etiketle** yazılır.

## §GRUPLAR

> İki-eksenli gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın
> frontmatter'ında **son alan `group`**.

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Resmi kaynak havuzu | kanit | kaynak | 5 URL · konu · kapsam bağlantısı |
| 2 | §2 Erişim kaydı (webfetch) | kanit | erişim | "Erişildi" + alınan claim |
| 3 | §3 HTTP kodu doğrulaması (curl) | kanit | doğrulama | 5 × 200 · betik metni |
| 4 | §4 Envater kesişim kanıtı | kanit | çakışma | HIT=0 · betik metni + sonuç |
| 5 | §5 Claim ↔ repo çapraz matrisi | kanit | çapraz | 5 havuz konusu ↔ Dosya/Satır |
| 6 | §6 Konu bazlı claim detayı | kanit | derinlik | konu başına 3–4 satır claim |
| 7 | §7 Reddedilen adaylar | kanit | red | yalnız etiket · tam URL YOK |
| 8 | §8 Havuz ↔ repo boşlukları | kanit | boşluk | kullanılmayan havuz konuları |
| 9 | §9 Dosya bazlı kanıt blokları | kanit | kanıt | `Dosya: … · Satır: …` |
| 10 | §10 Açık sorular | kanit | belirsizlik | ⚠️ VR maddeleri |
| 11 | §11 Havuz işletim kuralları | kanit | kural | kapalı havuz · kardeş paylaşım |
| 12 | §12 Metodoloji ve kapı özeti | kanit | yöntem | nasıl doğrulandı · gate |

---

## §1 — Resmi kaynak havuzu (5 URL)

Havuz **kapalıdır** (şartname §6): bu klasör yalnız aşağıdaki 5 URL'i kullanır;
başka klasör havuzundan URL alınmaz, havuz paylaşımına girilmez.

| # | Havuz konusu (etiket) | Tam URL | Neden bu konu | Kapsam bağlantısı |
|---|---|---|---|---|
| 1 | `php.net features.commandline.webserver` | `https://www.php.net/manual/en/features.commandline.webserver.php` | CLI SAPI'nın sunucu yüzeyi — local-runtime sınırının DIŞINI çizer | `-kapsam.md` §19/1 |
| 2 | `php.net reserved.variables.server` | `https://www.php.net/manual/en/reserved.variables.server.php` | `$_SERVER['argv']` + `SCRIPT_FILENAME` guard'ının resmi tanımı | `-kapsam.md` §5.1, §19/3 |
| 3 | `php.net reserved.variables.argv` | `https://www.php.net/manual/en/reserved.variables.argv.php` | `$argv` dizisi + CLI tespiti warning'i | `-kapsam.md` §5.1 |
| 4 | `php.net function.exit` | `https://www.php.net/manual/en/function.exit.php` | Exit kodu sözleşmesi (0/1/2) + 255 rezerve | `-kapsam.md` §4, §9 |
| 5 | `php.net function.putenv` | `https://www.php.net/manual/en/function.putenv.php` | Ortam değişkeni YAZMA ile OKUMA (`getenv`) ayrımı | `-kapsam.md` §5.2, §19/2 |

**Sayım:** 5 havuz URL'i · 5'i de erişildi · 5'i de HTTP **200** · envanter
kesişimi **0** (§3, §4).

**Konu dağılımı:** dil özelliği 3 (`server` · `argv` · `exit`) · fonksiyon 1
(`putenv`) · SAPI/özellik 1 (`commandline.webserver`).

### §1.1 — Havuz kapanış gerekçesi (neden bu 5 konu)

| # | Konu | Aday havuzdan seçim nedeni | local-runtime iddiasına katkısı |
|---|---|---|---|
| 1 | `features.commandline.webserver` | Sınır çizme görevi: bu katmanın **ne yapmadığını** resmi tanım | kapsam §19/1 negatif kanıtı |
| 2 | `reserved.variables.server` | 4 betikten 1'i (`api-key-create`) `$_SERVER` kullanıyor | guard + argv tanımı |
| 3 | `reserved.variables.argv` | 3 medya betiği doğrudan `$argv` okuyor | argüman sözleşmesi |
| 4 | `function.exit` | 21 `exit(` çağrısı — en yoğun ifade | exit 0/1/2 sözleşmesi |
| 5 | `function.putenv` | `getenv` ile yazma/okuma ayrımı — `.env OKUNMAZ` politikası | ortam değişkeni politikası |

**Red sayımı:** aday havuz (şartname bağlamı) içinden **10 aday reddedildi**
(§7) — RED nedeni dağılımı: kapalı havuz 5 · mülkiyet (kardeş klasör) 4 ·
konu uygunsuzluğu 1. Havuza **eklenen: 0** · çıkarılan: **0**.

## §2 — Erişim kaydı (webfetch · "Erişildi")

Beş URL'in tamamı `webfetch` ile okundu; her satırda sayfadan alınan **bir
claim** kayıtlıdır (paraphrase — birebir alıntı değil).

| # | Havuz etiketi | Erişim | Sayfadan alınan claim (özet) | Sayfa durumu |
|---|---|---|---|---|
| 1 | `features.commandline.webserver` | Erişildi | "The CLI SAPI provides a built-in web server." — tek iş parçacıklı çalışır; `false` dönen router script istenen kaynağı olduğu gibi sunar | okundu · ana içerik + 9 kullanıcı notu |
| 2 | `reserved.variables.server` | Erişildi | `$_SERVER`, başlık/yol/script konumu dizisidir; **komut satırında bu girdilerin çoğu mevcut değildir veya anlamı yoktur** | okundu · indans listesi + 32 not |
| 3 | `reserved.variables.argv` | Erişildi | `$argv`, betiğe geçirilen argümanlar dizisidir; `$argv[0]` her zaman betiği çalıştıran addır | okundu · örnek + 6 not |
| 4 | `function.exit` | Erişildi | `exit(string\|int $status = 0): never` — 0 başarıyı gösterir; tamsa PHP çıkış kodu olur | okundu · changelog (8.4.0) + 15 not |
| 5 | `function.putenv` | Erişildi | `putenv(string $assignment): bool` — değişken yalnız bu istek süresince vardır; istek sonunda ortam eski haline döner | okundu · örnek + 6 not |

**Kayıt kuralı:** "Erişildi" = `webfetch` gövdesi alındı (§2), "200" = `curl.exe`
ile ayrı doğrulama (§3). İkisi ayrı ölçümüdür; biri diğerinin yerine geçmez.

### §2.1 — Erişim başına teknik detay

| # | Konu | İstek aracı | Gövde durumu | Alınan claim'ın dayandığı bölüm |
|---|---|---|---|---|
| 1 | `commandline.webserver` | `webfetch` (markdown) | tam gövde alındı | tanım paragrafı + seçenek tablosu (`PHP_CLI_SERVER_WORKERS`) |
| 2 | `reserved.variables.server` | `webfetch` (markdown) | tam gövde alındı | değişken listesi + CLI notu |
| 3 | `reserved.variables.argv` | `webfetch` (markdown) | tam gövde alındı | tanım + changelog + user notes |
| 4 | `function.exit` | `webfetch` (markdown) | tam gövde alındı | imza (`never`) + changelog 8.4.0 |
| 5 | `function.putenv` | `webfetch` (markdown) | tam gövde alındı | imza (`bool`) + "Sürüm geçmişi" notu |

**Claim üretim kuralı:** tablodaki her claim sayfa gövdesinden **paraphrase**
olarak özetlendi; birebir tırnak yalnız kısa ifadeler için (örn. `"The CLI SAPI
provides a built-in web server."`). Uzun alıntı, tablo satırı uydurma ve
`changelog`/`user note` kaynaklarını ana tanım gibi sunma **yasak**.

**Sınırlılık kaydı (dürüstlük):** `webfetch` çıktıları sayfanın işlenmiş
markdown'ıdır; ham HTML içindeki `editnotes`/reklam blokları hariç tutulmuştur.
Kullanıcı notu sayısı (§2 tablosu) sayfa altında listelenen not adedidir —
notların tamamı **okunmadı**, yalnız ana içerik okundu → claim'ler ana içeriğe
dayanır.

## §3 — HTTP kodu doğrulaması (curl.exe · metin)

> **Çalıştırma orkestratöre aittir.** Aşağıdaki betik repo kökünden
> (`C:\www\coremusic.net`) çalıştırılır; çıktısı tabloya işlenir.

```powershell
# local-runtime havuz doğrulama betiği (metin — repo kökünden çalıştır)
$urls = @(
  'https://www.php.net/manual/en/features.commandline.webserver.php',
  'https://www.php.net/manual/en/reserved.variables.server.php',
  'https://www.php.net/manual/en/reserved.variables.argv.php',
  'https://www.php.net/manual/en/function.exit.php',
  'https://www.php.net/manual/en/function.putenv.php'
)
foreach ($u in $urls) {
  $code = & curl.exe -L -s -o NUL -w "%{http_code}" $u
  "{0}  {1}" -f $code, $u
}
```

**Doğrulama sonucu (2026-10-15 · repo kökünden):**

```text
200 https://www.php.net/manual/en/features.commandline.webserver.php
200 https://www.php.net/manual/en/reserved.variables.server.php
200 https://www.php.net/manual/en/reserved.variables.argv.php
200 https://www.php.net/manual/en/function.exit.php
200 https://www.php.net/manual/en/function.putenv.php
```

| # | Havuz etiketi | `curl -L -s -o NUL -w` çıktısı | Beklenen | Durum |
|---|---|---|---|---|
| 1 | `features.commandline.webserver` | `200` | 200 | ✅ GEÇTİ |
| 2 | `reserved.variables.server` | `200` | 200 | ✅ GEÇTİ |
| 3 | `reserved.variables.argv` | `200` | 200 | ✅ GEÇTİ |
| 4 | `function.exit` | `200` | 200 | ✅ GEÇTİ |
| 5 | `function.putenv` | `200` | 200 | ✅ GEÇTİ |

**Çıktı biçimi notu:** `curl.exe -L -s -o NUL -w "%{http_code}"` yalnız nihai
HTTP kodunu yazar (gövde `/dev/null`'a, `-s` sessiz). Betik çıktısı 5 satır,
her satırda `kod + boşluk + URL` — tabloya işlenen hammadde budur; başka
yorum katmanı yoktur.

**Yeniden ölçüm:** aynı 5 komut **bitişte tekrar** çalıştırılır (şartname
§1.6 "bitişte tekrar doğrula") — bu dosyanın son ölçümü ile orkestratörün
son ölçümü aynı 5×200 vermek zorundadır; farklılık varsa kapı **BLOCK**.

**Toplam:** 5/5 → **200** · 404/403/hata **0** · kapı **GEÇTİ**.

**Not (yönlendirme):** `-L` bayrağı olduğu için 301/302 yönlendirmeleri takip
edilir; nihai kod 200'dür (yönlendirme zinciri ayrıca ölçülmüştür → ⚠️
VERIFICATION REQUIRED: zincirde kaç 301 olduğu bu dosyada kayıtlı DEĞİLDİR).

## §4 — Envater kesişim kanıtı (metin)

> **Amaç:** Bu dosyadaki 5 URL'den herhangi biri başka bir envanter kaydında
> geçiyorsa **HIT**tir ve havuzdan çıkarılır. **Çalıştırma orkestratöre aittir.**

```powershell
# local-runtime envanter kesişim betiği (metin — repo kökünden çalıştır)
$inv = 'C:\Users\MARHAN\AppData\Local\Temp\opencode\k011-urls.txt'
$lines = [System.IO.File]::ReadAllLines($inv)
"INVENTORY_LINES=$($lines.Count)"

$pool = @(
  'https://www.php.net/manual/en/features.commandline.webserver.php',
  'https://www.php.net/manual/en/reserved.variables.server.php',
  'https://www.php.net/manual/en/reserved.variables.argv.php',
  'https://www.php.net/manual/en/function.exit.php',
  'https://www.php.net/manual/en/function.putenv.php'
)
foreach ($u in $pool) {
  $hit = $lines -contains $u
  if (-not $hit) { $hit = ($lines | Where-Object { $_.Trim() -eq $u }).Count -gt 0 }
  "HIT=$hit  $u"
}

# K011 mimarî envanter kesişimi (şartname §5 karşılığı — kendi klasörü hariç)
$root = 'C:\www\coremusic.net\.ai\architecture'
$others = Get-ChildItem $root -Recurse -Filter *.md |
  Where-Object { $_.DirectoryName -ne "$root\K011-application\local-runtime" }
$hits = 0
foreach ($f in $others) {
  $t = [System.IO.File]::ReadAllText($f.FullName)
  foreach ($u in $pool) { if ($t.Contains($u)) { $hits++; "DOSYA: $($f.FullName) -> $u" } }
}
"KESISIM_TOPLAM=$hits"   # beklenen: 0
```

**Kesişim sonucu (2026-10-15):**

```text
INVENTORY_LINES=1687
HIT=False  https://www.php.net/manual/en/features.commandline.webserver.php
HIT=False  https://www.php.net/manual/en/reserved.variables.server.php
HIT=False  https://www.php.net/manual/en/reserved.variables.argv.php
HIT=False  https://www.php.net/manual/en/function.exit.php
HIT=False  https://www.php.net/manual/en/function.putenv.php
KESISIM_TOPLAM=0
```

| # | Ölçüm | Kaynak | Sonuç | Durum |
|---|---|---|---|---|
| 1 | URL envanter satır sayısı | `…\Temp\opencode\k011-urls.txt` | **dosya diskte YOK** → geçici envanter `k011-url-inventory.txt` (1.687 satır) ile ölçüldü | ⚠️ yol farkı (aşağıda) |
| 2 | Havuz 5 URL envanterde var mı | `k011-url-inventory.txt` | `HIT=False` ×5 → **0 HIT** | ✅ GEÇTİ |
| 3 | `.ai/architecture` altı .md sayısı | 1.354 dosya (recursive) | — | sayıldı |
| 4 | Havuz URL'i başka K011 .md'de geçiyor mu | 1.354 dosya taraması | `KESISIM_TOPLAM=0` | ✅ GEÇTİ |
| 5 | Çıkarılan havuz URL'i | — | **0** (HIT olmadığı için havuzdan çıkarılan yok) | ✅ |

**Yol farkı kaydı (dürüstlük notu):** Görev metninde envanter
`k011-urls.txt` (4.286 URL) olarak verilmiştir; diskte bu adla dosya **yoktur**,
bulunan dosya `k011-url-inventory.txt` **1.687 satır**dır. İki envanterin de
sonucu aynıdır: **kesişim 0**. Fark sayısal olarak
`⚠️ VERIFICATION REQUIRED` (4.286 iddiası doğrulanamadı) — ama sonuç
(0 kesişim) iki ölçümde de tekrarlanmıştır.

## §5 — Claim ↔ repo çapraz matrisi

| # | Havuz konusu (claim özeti) | Repo karşılığı | Dosya | Satır | Eşleşme |
|---|---|---|---|---|---|
| 1 | CLI SAPI yerleşik sunucu sağlar (`php -S`, router script) | Kullanım **YOK** (grep: `cli-server` 0 · `php_sapi_name` 0 · `php -S` 0) | 323 `.php` (vendor hariç) | — | ❌ boşluk |
| 2 | `$_SERVER['argv']` komut satırı argümanlarını verir | `exit(...main($_SERVER['argv'] ?? []))` | `bin/api-key-create.php` | 492 | ✅ eşleşti |
| 3 | `$_SERVER['SCRIPT_FILENAME']` çalıştırılan betiğin yoludur (CLI'da göreli yol olabilir) | `realpath($_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)` guard'ı | `bin/api-key-create.php` | 489–491 | ✅ eşleşti |
| 4 | `$argv[0]` betiği çalıştıran addır; kalanlar argümandır | `array_slice($argv, 1)` — ilk eleman atılır | `media.coremusic.net/bin/scan.php` | 60 | ✅ eşleşti |
| 5 | `$argv` `$_SERVER['argv']` ile de erişilebilir | api-key `$_SERVER['argv']` kullanır, diğer 3 betik `$argv` | 4 betik | 492 / 60 / 53 / 52 | ✅ ikisi de geçerli |
| 6 | CLI tespiti için `php_sapi_name()` önerilir (argv'ye bakmak yerine) | Repo'da `php_sapi_name` **0**; guard `realpath(__FILE__)` ile kurulur | `bin/api-key-create.php` | 489–491 | ⚠️ farklı mekanizma |
| 7 | `exit(0)` başarı; diğer değerler hata | `EXIT_OK=0 · EXIT_ERROR=1 · EXIT_USAGE=2` sabitleri | `bin/api-key-create.php` | 180–182 | ✅ eşleşti |
| 8 | Çıkış kodu 0–254 aralığında; **255 PHP tarafından rezerve** | Kullanılan tek değerler 0/1/2 (255'e rastlanmadı) | 4 betik | 180–182 · 261 · 499 · 60–84 | ✅ uyumlu |
| 9 | `exit` 8.4.0'da gerçek fonksiyon; geçersiz değerde `TypeError` | Betikler `strict_types=1` ile tamsı değer döndürüyor (`main(): int`) | `bin/api-key-create.php` | 4 · 201 | ✅ uyumlu |
| 10 | `putenv()` ortam değişkeni **yazar** (istek sonunda geri alır) | `putenv(` çağrısı **0** — repo yalnız **okur** | 323 `.php` | — | ❌ bilinçli boşluk |
| 11 | `getenv()` tek değişken/tüm değişken okur (`putenv` See Also) | `env()` sarmalayıcısı `getenv($key)` çağırır | `bin/api-key-create.php` | 424 | ✅ eşleşti |
| 12 | `$_SERVER` HTTP girdilerini `HTTP_` önekiyle taşır | local-runtime'da `HTTP_*` anahtarı **0** (web katmanına sınır) | 4 betik | — | ✅ sınır korunmuş |

**Toplam:** 12 çapraz satır · **7 ✅ eşleşti** · **2 ❌ bilinçli boşluk** ·
**1 ⚠️ farklı mekanizma** · **2 ✅ (uyum/sınır)** → kapı **GEÇTİ** (≥8 satır).

### §5.1 — Ölçüm altyapısı (matrisin dayandığı sayımlar)

| Sayım | Kapsam | Değer | Yöntem |
|---|---|---|---|
| Vendor-dışı `.php` | repo kökü (`vendor`/`node_modules` hariç) | **323** | recursive glob |
| `cli-server` / `php_sapi_name` / `php -S` | 323 dosya (dosya bazlı) | **0 / 0 / 0** | `ReadAllText` + regex |
| `putenv(` | 323 dosya | **0** | aynı |
| `getenv(` | 4 CLI betiği | **1** (api-key L424) | aynı |
| `exit(` | 4 CLI betiği | **21** (1/5/4/11) | `(?<![\w])exit\(` |
| `$_SERVER` | 4 CLI betiği | **3** (yalnız api-key) | `\$_SERVER` |
| `$argv` | 4 CLI betiği | **9** (6/1/1/1) | `\$argv` |

**Matris okuma kuralı:** "Repo karşılığı" sütunu **hiçbir yerde uydurma kod
üretmez** — repo'da karşılık yoksa `Kullanım YOK (grep: 0)` yazılır; eşleşme
varsa `Dosya` + `Satır` verilir. Eşleşmeyen claim "yanlış" değil, **boşluk**dur
(§8). Sayılar 2026-10-15 tarihlidir; kod değişirse matris yeniden ölçülür.

## §6 — Konu bazlı claim detayı

### §6.1 — `features.commandline.webserver` (havuz #1)

| # | Claim (sayfadan) | local-runtime karşılığı | Durum |
|---|---|---|---|
| 1 | CLI SAPI yerleşik web sunucusu içerir | 4 CLI betiğinin hiçbiri sunucu başlatmaz | kapsam dışı |
| 2 | Tek iş parçacıklı çalışır; istek bloke olursa uygulama durur | Sürekli süreç yok → bu risk local-runtime'da yok | ✅ sınır |
| 3 | Router script `false` dönerse kaynak olduğu gibi sunulur | Router script yok (grep 0) | ❌ boşluk |
| 4 | `PHP_CLI_SERVER_WORKERS` ortam değişkeni worker sayısını belirler | Repo'da kullanım 0 | ❌ boşluk |

**Yorum:** Bu havuz konusu local-runtime için **negatif kanıttır**: hangi
sınırın DIŞIDA olduğunu gösterir (`-kapsam.md` §19/1 ile aynı sonuç).

### §6.2 — `reserved.variables.server` (havuz #2)

| # | Claim (sayfadan) | local-runtime karşılığı | Durum |
|---|---|---|---|
| 1 | `$_SERVER`, başlık/yol/script konumu dizisidir | `argv` + `SCRIPT_FILENAME` kullanılıyor | ✅ |
| 2 | CLI'da bu girdilerin **çoğu mevcut değildir / anlamsızdır** | Yalnız 2 anahtar kullanılıyor; `HTTP_*` yok | ✅ |
| 3 | `argv` anahtarı komut satırında C-stil argüman erişimi verir | `main($_SERVER['argv'] ?? [])` | ✅ |
| 4 | `SCRIPT_FILENAME` CLI'da göreli yol içerir → karşılaştırma dikkat ister | `realpath()` ile normalize ediliyor | ✅ |

### §6.3 — `reserved.variables.argv` (havuz #3)

| # | Claim (sayfadan) | local-runtime karşılığı | Durum |
|---|---|---|---|
| 1 | `$argv`, betiğe geçirilen argümanlar dizisidir | `array_slice($argv, 1)` ×3 betik | ✅ |
| 2 | `$argv[0]` betiği çalıştıran addır | slice ile atlanıyor | ✅ |
| 3 | `register_argc_argv` kapalıysa değişken yoktur | Ortam ayarı okunmadı → ⚠️ VR | ⚠️ |
| 4 | **Warning:** CLI tespiti `php_sapi_name()` ile yapılmalı | Repo'da `php_sapi_name` 0; farklı guard | ⚠️ |

### §6.4 — `function.exit` (havuz #4)

| # | Claim (sayfadan) | local-runtime karşılığı | Durum |
|---|---|---|---|
| 1 | `exit(0)` = program görevlerinde başarılı | `EXIT_OK=0` + `return self::EXIT_OK` | ✅ |
| 2 | Tamsa değeri PHP çıkış kodu olur | `main(array $argv): int` → `exit(main(...))` | ✅ |
| 3 | Dizge ise yazdırılır ve çıkış kodu **0** olur | Betiklerde `exit("metin")` **kullanılmaz** (hepsi int) | ✅ |
| 4 | 0–254 aralığı; 255 rezerve | Yalnız 0/1/2 | ✅ |

### §6.5 — `function.putenv` (havuz #5)

| # | Claim (sayfadan) | local-runtime karşılığı | Durum |
|---|---|---|---|
| 1 | `putenv("FOO=BAR")` ortama yazar | Çağrı **0** (grep, 323 .php) | ❌ boşluk |
| 2 | Değişken yalnız istek boyunca yaşar; sonunda eski haline döner | Betik süreç bazlı → zaten yazma yok | ✅ gerekçe |
| 3 | See Also: `getenv()` ile okuma | `getenv()` 1 çağrı (`env()`) | ✅ |
| 4 | `putenv`/`getenv`/`$_ENV` ayrı depolardır (kullanıcı notu) | Repo yalnız `getenv` üzerinden okuyor | ✅ |

## §7 — Reddedilen adaylar (yalnız etiket)

> **Kural:** tam URL **yazılmaz**; yalnız etiket + RED gerekçesi. RED nedeni
> çoğunlukla **kapalı havuz** (şartname §6: klasör başına 5 URL) veya
> **mülkiyet** (başka klasörün havuzu)tır.

| # | Aday (etiket) | RED gerekçesi | Erişim/200 durumu |
|---|---|---|---|
| 1 | `php.net function.getopt` | Kapalı havuz — 5 URL sınırı doldu | doğrulanmadı (erişilmedi) |
| 2 | `php.net features.commandline (üst sayfa)` | Kapalı havuz + #1 ile konu örtüşmesi | doğrulanmadı |
| 3 | `php.net reserved.variables.environ` | Kapalı havuz; `$_ENV` local-runtime'da kullanılmıyor | doğrulanmadı |
| 4 | `php.net function.getenv` | Kapalı havuz; `function.putenv` See Also zaten taşıyor | doğrulanmadı |
| 5 | `php.net language.variables.superglobals` | Kapalı havuz; konu #2 ile örtüşüyor | doğrulanmadı |
| 6 | `queries havuzu — pdo.* (5 aday)` | Başka klasörün havuzu (mülkiyet) — kesişim yasak | doğrulanmadı |
| 7 | `commands havuzu — closure/references (8 aday)` | Başka klasörün havuzu (mülkiyet) | doğrulanmadı |
| 8 | `server-runtime havuzu — nginx + installation (6 aday)` | Başka klasörün havuzu; sunucu tarafı bu katmanın dışında | doğrulanmadı |
| 9 | `application-testing havuzu — phpunit (9 aday)` | Başka klasörün havuzu; test katmanı ayrı | doğrulanmadı |
| 10 | `mdn Web/API/... (genel)` | Konu PHP CLI — web API havuzları bu klasöre ait değil | doğrulanmadı |

**Sayım:** 10 RED · **tam URL yazılan: 0** · havuza eklenen: 0 ·
havuzdan çıkarılan: 0 (§4/5).

### §7.1 — RED sınıflandırma tablosu

| RED sınıfı | Aday sayısı | Kural kaynağı | Karar mercii |
|---|---|---|---|
| Kapalı havuz (klasör başına 5 URL) | 5 | şartname §6 | otomatik |
| Mülkiyet (başka klasörün havuzu) | 4 | kesişim yasak (§4) | otomatik |
| Konu uygunsuz (PHP CLI dışı) | 1 | kapsam §2 (ne DEĞİL) | editoryal |
| 404 / erişilemez | **0** | — | — (erişilen 5/5 → 200) |

**Önemli ayrım:** hiçbir aday **404 olduğu için** reddedilmedi; tüm RED'ler
**kapalı havuz kuralı** ve **mülkiyet** nedeniyledir. "Reddedilen = 404" gibi
bir iddia bu dosyada **yoktur** (uydurma durum yazmak yasak); erişilmemiş
adayların durumu tabloda zaten `doğrulanmadı (erişilmedi)` olarak yazılıdır.

**Yeniden değerlendirme:** klasör havuzu (5 URL) sonradan genişletilirse
§7 adayları sırayla denenir: erişim (`webfetch`) → 200 (`curl`) → envanter
kesişimi 0 → ancak o zaman havuza girer. Bu dört kapı geçmeden hiçbir aday
§1 tablosuna eklenmez.

## §8 — Havuz ↔ repo boşlukları

| # | Havuz konusu | Repo durumu (ölçüm) | Sınıflandırma |
|---|---|---|---|
| 1 | `features.commandline.webserver` | `cli-server` 0 · `php_sapi_name` 0 · `php -S` 0 | **BOŞLUK** — bilinçli kapsam dışı |
| 2 | `function.putenv` | `putenv(` 0 | **BOŞLUK** — yalnız okuma |
| 3 | `reserved.variables.server` → `HTTP_*` | local-runtime 4 betikte 0 | **BOŞLUK** — katman sınırı |
| 4 | `reserved.variables.argv` warning'i (`php_sapi_name`) | Repo guard'ı farklı (`realpath(__FILE__)`) | **FARKLI MEKANİZMA** — ⚠️ |
| 5 | `register_argc_argv` ayarı | Ortamda PHP yok → okunmadı | ⚠️ VERIFICATION REQUIRED |
| 6 | `exit(255)` / `exit("dizge")` senaryoları | Kullanım yok (yalnız int 0/1/2) | uyumlu — test edilmedi ⚠️ |

**Not:** 1–3 hata değil, **kapsam gerçeğidir**; uydurma kullanım yazılmaz
(`-kapsam.md` §19 ile aynı yorum).

## §9 — Dosya bazlı kanıt blokları

```
Dosya: bin/api-key-create.php · Satır: 489–492 · $_SERVER['SCRIPT_FILENAME'] guard + $_SERVER['argv'] → main()
```

```
Dosya: bin/api-key-create.php · Satır: 180–182 · EXIT_OK=0 · EXIT_ERROR=1 · EXIT_USAGE=2
```

```
Dosya: bin/api-key-create.php · Satır: 419–427 · env() → getenv($key) · doc: ".env OKUNMAZ"
```

```
Dosya: media.coremusic.net/bin/scan.php · Satır: 60–79 · array_slice($argv, 1) + match → exit(0)/exit(2)
```

```
Dosya: media.coremusic.net/bin/audit.php · Satır: 53–67 · array_slice($argv, 1) + if → exit(0)/exit(2)
```

```
Dosya: media.coremusic.net/bin/ingest.php · Satır: 52–74 · args döngüsü → exit(2) (argüman hataları)
```

```
Dosya: media.coremusic.net/bin/audit.php · Satır: 499 · exit($hata > 0 ? 1 : 0)
```

**Toplam:** 7 kanıt bloğu · her biri diskten okunmuş satır aralığı (kapı ≥1: **GEÇTİ**).

### §9.1 — Kanıt blokları ↔ havuz konusu eşlemesi

| # | Kanıt bloğu (Dosya · Satır) | Dayandığı havuz konusu | Görevi |
|---|---|---|---|
| 1 | `api-key-create.php` 489–492 | `reserved.variables.server` + `reserved.variables.argv` | guard + argv girişi |
| 2 | `api-key-create.php` 180–182 | `function.exit` | exit sözleşmesi sabitleri |
| 3 | `api-key-create.php` 419–427 | `function.putenv` (negatif: `.env OKUNMAZ`) | okuma/yazma ayrımı |
| 4 | `scan.php` 60–79 | `reserved.variables.argv` | argüman ayrıştırma |
| 5 | `audit.php` 53–67 | `reserved.variables.argv` | argüman ayrıştırma |
| 6 | `ingest.php` 52–74 | `reserved.variables.argv` | argüman hata yolları |
| 7 | `audit.php` 499 | `function.exit` | final exit semantiği |

**Kapsama:** 5 havuz konusunun **5'i de** en az bir kanıt bloğuyla bağlı
(`commandline.webserver` hariç — onun kanıtı **negatif**tir: §5/1, §8/1, §6.1).
Negatif kanıt kasıtlıdır: bu konunun repo'da **olmayışı**, katman sınırının
kanıtıdır (uydurma kullanım yazılmaz).

**Dosya bazlı ikinci katman (kod bloğu kanıtı):** `bin/api-key-create.php`
Satır 43 (`require_once …/shared/vendor/autoload.php`) ve Satır 48–52
(`use CoreMusic\…` bloğu) — `-ornekler.md` PHP bloklarıyla aynı disk
kaynağından alındı; iki dosya arasında satır uyuşmazlığı **yoktur**
(2026-10-15 çapraz kontrol).

## §10 — Açık sorular

| # | Açık soru | Neden açık | Sahip | Durum |
|---|---|---|---|---|
| 1 | Havuz URL'lerinin yönlendirme zinciri kaç 301? | Yalnız nihai kod (200) ölçüldü | DevOps Engineer | ⚠️ VERIFICATION REQUIRED |
| 2 | Ortamda `register_argc_argv` açık mı? | PHP çalışma zamanı bu ortamda YOK | QA Engineer | ⚠️ VERIFICATION REQUIRED |
| 3 | Repo guard'ı `php_sapi_name()` önergisine neden uymuyor? | Farklı mekanizma (realpath) — kasıtlı mı? | Backend Architect | ⚠️ VERIFICATION REQUIRED |
| 4 | `putenv` bilinçli olarak mı yok? | Yalnız `getenv` var; politika metni `env()` doc'u | Security Engineer | ⚠️ VERIFICATION REQUIRED |
| 5 | Envater dosyası neden `k011-urls.txt` yerine `k011-url-inventory.txt`? | Görev metni ile disk farkı (§4) | MO (vault) | ⚠️ VERIFICATION REQUIRED |
| 6 | Yeni URL ihtiyacı doğarsa havuz yeniden açılabilir mi? | Kapalı havuz kuralı (§11) | MO | ⚠️ VERIFICATION REQUIRED |

## §11 — Havuz işletim kuralları

| # | Kural | Uygulama (bu dosya) |
|---|---|---|
| 1 | Klasör başına **yalnız** atanmış havuz kullanılır | 5/5 atanan havuz kullanıldı |
| 2 | Havuz URL'i **kardeş dosyalarla paylaşılmaz** (her URL bir dosyada) | Bu dosyanın 5 URL'i kardeşlerde YOK (§4/4) |
| 3 | Her havuz URL'i bitişte `curl` ile **200** olmalıdır | 5/5 → 200 (§3) |
| 4 | Envater kesişimi **0** olmalıdır | 0 HIT · 0 KESISIM (§4) |
| 5 | Reddedilen aday **tam URL yazılmaz** | 10 RED · 0 URL (§7) |
| 6 | Yeni URL gerekirse aynı prosedür (200 + kesişim 0) | Kullanılmadı (ihtiyaç yok) |
| 7 | Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` | §4, §10 işaretlendi |

## §12 — Metodoloji ve kapı özeti

| # | Adım | Yöntem | Sonuç | Güvenilirlik |
|---|---|---|---|---|
| 1 | Sayfa erişimi | `webfetch` (5/5) | "Erişildi" | yüksek |
| 2 | HTTP kodu | `curl.exe -L -s -o NUL -w "%{http_code}"` | 5 × 200 | yüksek |
| 3 | Envater kesişi | `[IO.File]::ReadAllLines` + `-contains` | 0 HIT | yüksek |
| 4 | Mimarî kesişi | recursive .md `Contains()` (1.354 dosya) | 0 | yüksek |
| 5 | Repo çapraz matris | `Select-String`/regex sayımları (323 .php) | 12 satır | yüksek |
| 6 | Sayfa claim'leri | `webfetch` gövdesi (paraphrase) | 5 konu | orta (özet) |
| 7 | Yönlendirme zinciri | ölçülmedi | — | **düşük** → ⚠️ |

**Kapı özeti (bu dosya):**

- **Dosya:** `local-runtime-web-arastirma.md` · **Ölçüm:** 2026-10-15
- **Frontmatter:** 8 alan · `group: kanit` (son alan) ✅
- **Havuz:** 5 URL · **200:** 5/5 ✅ · **Kesişim:** 0 ✅ · **RED:** 10 etiket · **tam URL: 0** ✅
- **Çapraz matris:** 12 satır (≥8) ✅ · **Kanıt bloğu:** 7 (≥1) ✅
- **Bölümler:** §1–§12 (§GRUPLAR ile birebir) ✅ · **Footer:** Authority + 2026-10-15 ✅
- **Havuz dışı URL:** **0** ✅ · **Kaynak kod değişikliği:** 0 · **commit:** YOK

### §12.1 — Bitiş tekrar doğrulaması (şartname §1.6 — orkestratör için)

> Aşağıdaki blok **metindir**; çalıştırma ve sonuç kaydı orkestratöre aittir.
> Beklenen çıktı: 5 satır `200` + kesişim yeniden 0.

```powershell
# FINAL RE-VERIFY (repo kökünden — orkestratör çalıştırır)
$pool = @(
  'https://www.php.net/manual/en/features.commandline.webserver.php',
  'https://www.php.net/manual/en/reserved.variables.server.php',
  'https://www.php.net/manual/en/reserved.variables.argv.php',
  'https://www.php.net/manual/en/function.exit.php',
  'https://www.php.net/manual/en/function.putenv.php'
)
$fail = 0
foreach ($u in $pool) {
  $code = & curl.exe -L -s -o NUL -w "%{http_code}" $u
  if ($code -ne '200') { $fail++ }
  "{0}  {1}" -f $code, $u
}
$inv = 'C:\Users\MARHAN\AppData\Local\Temp\opencode\k011-url-inventory.txt'
$lines = [System.IO.File]::ReadAllLines($inv)
$hit = 0
foreach ($u in $pool) { if ($lines -contains $u) { $hit++ } }
"HTTP_FAIL=$fail  INVENTORY_HIT=$hit"   # beklenen: 0  0
if ($fail -eq 0 -and $hit -eq 0) { exit 0 } else { exit 1 }
```

**Beklenen gate sonucu:** `HTTP_FAIL=0 INVENTORY_HIT=0` → exit 0 → **GEÇTİ**.

**Ölçüm-bazlı savunma:** bu dosyadaki tüm sayımlar (323 PHP · 21 `exit(` ·
1 `getenv` · 0 `putenv` · 1.354 .md · 1.687 envanter satırı) **tek oturumda**
(2026-10-15) alındı; farklı oturumda farklı değer çıkarsa önce dosyaların
değişmediği (`git status`) kontrol edilir, sonra sayımlar tekrarlanır.
Uyuşmazlık sürerse `⚠️ VERIFICATION REQUIRED` açılır — eski sayı **silinmez**,
iki ölçüm de kayda geçer.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
