---
node_id: K000-isletim-sistemi/isletim-sistemi-api/syscall/syscall-guvenlik-ve-sandbox
node_type: doc
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-api/syscall
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-api/isletim-sistemi-api-kapsam
evidence:
  - shared/src/AI/AIEngine.php:288-310
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215
validation: 2026-10-15 · yeni dosya (bölge 6) · >=505 satir hedefi · 3 evidence araligi diskten okunarak dogrulandi · index KANIT iddialariyla disk uyumsuzlugu §D.4'te kayitli
open_questions:
  - Q-P3 · hedef platform seti netlestirilmeli · ⚠️ VERIFICATION REQUIRED
group: mimari
---
# syscall güvenlik ve sandbox

> Bu dosya, `isletim-sistemi-api` kapsamindaki ucuncu syscall dalidir:
> cagri nasil sinirlandirilir (sinirlandirma), cagriyi kimin calistirmaya
> yetkisi var (yetki kontrolu) ve sinir asimina karsi ne yapilir (guvenlik
> acigi onleme). Kapsam sinirlari `isletim-sistemi-api-kapsam.md` §A.3 · K8
> icinde tanimlanmistir. Kardes dosyalar: `syscall-mekanizmasi.md` (nasil
> calisir) ve `syscall-hata-kodlari-ve-errno.md` (hata nasil ayristirilir).

---

## §0 — ELI15

Bir program isletim sistemine "su dosyayi ac" der. Guvenlik sorusu sudur:
bu istek **sinirli** mi (her dosya degil, yalnizca izin verilen dosya) ve
bu istegi yapan **yetkili** mi? Bu dosya bu iki soruyu isler. "Sandbox"
(kum havuzu) kavrami, programin dis dunyaya erisiminin kisitlandigi
cevre demektir: cagri seviyesinde sinirlandirma (ne istenebilecegi),
yetki seviyesinde kontrol (kim isteyebilecegi) ve onleme seviyesinde
dogrulama (istek gercekte ne yapiyor).

Bu dosyada **olcusuz/kanitsiz iddia uretilmez**: platforma ozel mekanizma
adlari (hangi sistemde hangi mekanizma) `⚠️ VERIFICATION REQUIRED` ile
isaretlenir; olculmus tek sey repo kanitlaridir (§D).

## §A — Cagri sinirlandirma (kavramsal cerceve)

### §A.1 — Uc katman

Cagri sinirlandirmasi tek bir sey degil, uc katmanın ustuste binmesidir.

| # | Katman | Soru | Bu dosyadaki karsiligi |
|---|---|---|---|
| 1 | Arge siniri | Uygulama hangi cagrilari cagirabilir? | §A.2 |
| 2 | Arguman siniri | Cagri hangi degerlerle sinirli? | §A.3 |
| 3 | Ortam siniri | Cagri hangi baglamda calisiyor? | §A.4 |

### §A.2 — Arge siniri

Bir process, calistigi ortamda tanimli cagri kumesine erisebilir. Kumesin
disindaki istekler reddedilir. Bu kavramsal bir ifadedir; platforma ozel
karsiligi (hangi isletim sisteminde hangi mekanizma) **bu dosyada
tanimlanmaz** — hedef platform seti Q-P3 ile aciktir.

| Soru | Durum |
|---|---|
| Hedef platform seti hangileri? | `⚠️ VERIFICATION REQUIRED` (Q-P3 — index §KAPSAM "Hedef platform · Q-P3 acik") |
| Platform bazli arge siniri mekanizmasi adlari | `UNKNOWN` — bu dosyada uydurulmaz |

### §A.3 — Arguman siniri

Cagri guvenliginin en sik uygulanan katmani arguman dogrulamasidir:
girdi guvenli degilse cagri oncesi reddedilir. Repo kaniti bunu net
gosterir (§D.1 · §D.3): kabuk komutu birlestirilmeden **once**
`escapeshellarg` ile temizlenir, dosya yolu birlestirilmeden once
`realpath` + onek kontrolunden gecirilir.

| Kural | Repo kaniti | Durum |
|---|---|---|
| Kullanici girdisi kabuk argumani olmaz | `shared/src/AI/AIEngine.php:292` — `escapeshellarg($filePath)` | OLCULDU |
| Yolu onceden belirlenmis kok disina cikamaz | `home.coremusic.net/include/Stream/MusicStreamHandler.php:199-202` — `str_starts_with($real, $prefix)` | OLCULDU |
| Symlink ile kok disina sikanlamaz | `.../MusicStreamHandler.php:185` — `is_link($trimmed)` reddi | OLCULDU |

### §A.4 — Ortam siniri (sandbox)

Sandbox, process'in gorunur ortamini (dosya sistemi, cihaz, ag, surec
agaci) daraltir. Kavramsal uc soru:

| # | Soru | Not |
|---|---|---|
| 1 | Hangi kok dizin gorunur? | Repo ornegi §D.3 — onek kontrolu ile kisitlanmis gorunur alan |
| 2 | Hangi cagrilar kapali? | Platforma ozel — `⚠️ VERIFICATION REQUIRED` |
| 3 | Kacisma gorulurse ne olur? | Repo ornegi: `fail(403, ...)` — reddet ve isaretle (§D.3) |

> **Sınır notu:** Bu bölüm doktrin seviyesindedir; calistirilmis bir
> sandbox testi bu dosyada YOKTUR (§F.3).

## §B — Yetki kontrolu

### §B.1 — Yetki modelinin iki sorusu

| # | Soru | Dogrulama yolu |
|---|---|---|
| 1 | Isteyen kim? (kimlik) | Dosya/dizin yetkisi · rol kontrolu |
| 2 | Istenen ne? (kapsam) | Cagri seviyesinde kapsam daraltma |

### §B.2 — Repo kanitli yetki kontrolu — erisim kontrolu zinciri

`home.coremusic.net/include/Stream/MusicStreamHandler.php:185-208`
araliginda **dort kademeli** erisim kontrolu zinciri olculmustur:

| # | Adim | Satir | Eylem |
|---|---|---|---|
| 1 | Symlink reddi | 185-187 | `is_link($trimmed)` → `fail(403, ...)` |
| 2 | Gercek yol dogrulama | 189-192 | `realpath` false / `!is_file` → `fail(404, ...)` |
| 3 | Kok onek kontrolu | 194-202 | `realpath($this->libraryPath)` + `str_starts_with($real, $prefix)` → `fail(403, ...)` |
| 4 | Okunabilirlik | 204-206 | `!is_readable($real)` → `fail(403, ...)` |

**Olculmus ozellik:** Zincirde her adim **onceki adimin ciktisi uzerinde**
calisir (once gercek yol bulunur, sonra onek ile karsilastirilir) — bu,
TOCTOU sinifindaki yaris kosularini kismen onler. `fail(403/404)` farki
bilgi sizdirir mi sorusu §H.5'te acik soru olarak kayitlidir.

### §B.3 — Yetki yukseltme yuzeyi (kavramsal)

Yetki yukseltme, sinirli bir process'in daha yetkili bir cagri
calistirmaya calismasidir. Bu dosyada **platforma ozel yetki adlari
listelenmez** (karsiligi `cihaz-erisimi/index.md` §7 no.3'te
`CAP_SYS_ADMIN` / `SeLoadDriverPrivilege` olarak gecmektedir — o dalin
konusudur, burada tekrarlanmaz).

| Soru | Durum |
|---|---|
| Bu repoda yetki yukseltme ornegi var mi? | `UNKNOWN` — olculmedi, iddia edilmez |
| Hangi cagri yetki ister? | `⚠️ VERIFICATION REQUIRED` (Q-P3 bagli) |

## §C — Guvenlik acigi onleme (oncelik sirasi)

### §C.1 — Onleme sirasi (bu dosyanin doktrini)

| Sira | Ilke | Gerekce | Kanit bagi |
|---|---|---|---|
| 1 | Girdiyi dogrula, cagriya verme oncesi | En ucuz nokta | §A.3 tablosu |
| 2 | Varsayilan reddet (fail-safe) | Bilinmeyen istek gecmez | §D.3 — `fail(403)` yolu |
| 3 | En az yetki | Kapsam dar | §B.2 zinciri |
| 4 | Guc ciktisini ayirt et | Yanlis basari okumasi | `syscall-hata-kodlari-ve-errno.md` |
| 5 | Kisitli girdi uretimi | Guvenli sarmalama | §D.1 — `escapeshellarg` |

### §C.2 — Kabuk enjeksiyonuna karsi repo kaniti

`shared/src/AI/AIEngine.php:288-310` araligi tek bir ornek zincir tasir:

| # | Adim | Satir | Kod (dis kaynak) |
|---|---|---|---|
| 1 | Sablon hazirla | 290-293 | `sprintf('ffprobe ... "%s"', ...)` |
| 2 | Girdiyi kacir | 292 | `escapeshellarg($filePath)` |
| 3 | Calistir | 295 | `shell_exec($cmd)` |
| 4 | Bos cikti dayanikliligi | 296-298 | `null` → varsayilan metadata |
| 5 | Cikti sinirlandir | 300 | `json_decode(..., 512, JSON_THROW_ON_ERROR)` |

**Olculmus not:** `shell_exec` donus degeri yalnizca `null`/string
olabilir; dosya 296-298'de bu sinir kontrol edilmektedir.
`json_decode` 4. argumani **derinlik siniri** (512) tasir — asiri
iicegisme (billion laughs sinifina benzer) kacinimi icin sinir
kullanilmistir. Her iki sinir da bu aralikta **okunarak olculmustur**.

### §C.3 — Surec cagrisi yuzeyinde onleme (test ornegi)

`shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` test
icinde surec cagrisi yapar ve **kaynak/pipe yasam dongusu** dogrular:

| # | Adim | Satir | Kod (dis kaynak) |
|---|---|---|---|
| 1 | Komut kur | 250 | `array_merge([PHP_BINARY, self::BIN], $args)` |
| 2 | Ac | 253-258 | `proc_open($command, [0/1/2 pipe], $pipes, cwd)` |
| 3 | Kaynak kontrolu | 260 | `assertIsResource($handle, ...)` |
| 4 | Kapat/soku | 262-266 | `fclose($pipes[0])` → oku → `fclose($pipes[1/2])` |
| 5 | Temiz kapanis | 268 | `proc_close($handle)` → exit kodu |

**Olculmus ozellik:** Testte `0 => ['pipe', 'r']` stdin pipe'i acilir ve
**hemen** kapatilir (satır 262) — alt surec sonsuz beklemede kalmasin.
Bu, surec cagrisi guvenliginin klasik "acik pipe kilitleme" hatasina
karsi korumalidir ve bu aralikta olculmustur.

### §C.4 — Onlenen / onlenmemis sinir (durluk kaydi)

| Yuzey | Bu dosyada durum | Kanit |
|---|---|---|
| Girdi kacirma (kabuk) | Onleme VAR — olculmus | §C.2 · `AIEngine.php:292` |
| Yol kacirma (traversal) | Onleme VAR — olculmus | §D.3 · `MusicStreamHandler.php:199-202` |
| Pipe / surec kilitlenmesi | Onleme VAR (testte) — olculmus | §C.3 · `...Test.php:262-266` |
| Sandbox mekanizmasi (platforma ozel) | TANIMSIZ | `⚠️ VERIFICATION REQUIRED` (Q-P3) |
| Calistirilmis guvenlik testi | YOK | §F.3 |

## §D — Repo kaniti (dogrulama gunlugu)

### §D.1 — Kanit araligi 1 — `shared/src/AI/AIEngine.php:288-310`

- 2026-10-15'te diskten okundu (23 satir).
- Metot: `extractMetadata(string $filePath): array` (satır 288).
- Kabuk cagrisi: `shell_exec($cmd)` (satır 295).
- Kacirma: `escapeshellarg($filePath)` (satır 292).
- Varsayilanlar: `duration 0 · sample_rate 44100 · bit_depth 16 · channels 2` (satır 297).

### §D.2 — Kanit araligi 2 — `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269`

- 2026-10-15'te diskten okundu (22 satir).
- Metot: `runCli(array $args): array` (satır 248).
- Surec: `proc_open` (satır 253) · `proc_close` (satır 268).
- Pipe yasam dongusu: satır 262-266.

### §D.3 — Kanit araligi 3 — `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215`

- 2026-10-15'te diskten okundu (31 satir).
- Erisim kontrolu zinciri: satır 185-208 (§B.2 tablosu).
- `contentType` metodu: satır 211-214 — MIME eslesmesi `?? 'application/octet-stream'` (satır 214) ile **bilinmeyeni varsayilana dusurur** (fail-safe §C.1 no.2 ile tutarli).

### §D.4 — Dürüstlük kaydı — indeks KANIT iddialari ≠ disk icerigi

`syscall/index.md` §KANIT tablosu ucbag iddia tasir; ucu da 2026-10-15
okumasyonla **birebir tutmamaktadir**. Bu dosya **disk gercegini** yazar:

| # | İndeks iddiasi | Diskteki gercek | Karar |
|---|---|---|---|
| 1 | `AIEngine.php:288-310` → "surec cagrisi deseni (proc_open)" | Bu aralikta `proc_open` YOK; `shell_exec` VAR (satır 295) | Düzeltme kaydi: surec cagrisi = `shell_exec` |
| 2 | `ApiKeyCreateCommandTest.php:248-269` → "fork ile surec bekleme (pcntl_fork)" | Bu aralikta `pcntl_fork` YOK; `proc_open`/`proc_close` VAR (satır 253/268) | Düzeltme kaydi: surec yonetimi = `proc_open` ailesi |
| 3 | `MusicStreamHandler.php:185-215` → "surec cagrisi ile dosya akisi" | Bu aralikta surec cagrisi YOK; **erisim kontrolu zinciri** VAR (satır 185-208) | Düzeltme kaydi: kanit = yol/symlink/onek/okunabilirlik dogrulamasi |

> **Not (kapsam disi duzeltme):** `syscall/index.md` dosyasi bu gorevde
> **degistirilmemistir** (beyazliste disi). Kayit burada durur; indeks
> duzeltmesi ust gorevin konusudur.

### §D.5 — Repo C++ yuzeyi (negatif kanit)

- 2026-10-15 repo olcumu: `*.cpp` / `*.h` = **0 dosya**.
- Sonuc: bu repoda derlenmis C/C++ syscall/sandbox kodu **YOKTUR**;
  buradaki butun "kod" ornekleri **sartname/ornegi** etiketlidir ve
  PHP/psudo duzeyindedir.

## §E — Hata modlari

| # | Hata modu | Belirti | Onleme | Kanit bagi |
|---|---|---|---|---|
| H1 | Girdi kacirma (kabuk enjeksiyonu) | Beklenmeyen komut calisir | `escapeshellarg` | §C.2 |
| H2 | Yol kacirma (traversal) | Kok disina erisim | `realpath` + onek | §D.3 |
| H3 | Symlink kacirma | Kisa yol ile kok disina sikisma | `is_link` reddi | §D.3 satır 185 |
| H4 | Acik pipe kilitlenmesi | Alt surec sonsuz beklemede | stdin hemen kapat + `proc_close` | §C.3 |
| H5 | Yanlis basari okumasi | `null` cikti "basarili" sanilir | `null`/hata okumasi | §C.2 satır 296-298 |
| H6 | Asiri iicegisme | JSON/parser cpu tuketir | derinlik siniri 512 | §C.2 satır 300 |
| H7 | Bilgi sizdirmasi (403 vs 404) | Varlik tespiti | `⚠️` acik soru | §H.5 |
| H8 | Yetki yukseltme denemesi | Sinirli process + yetkili cagri | TANIMSIZ (Q-P3) | §B.3 |

## §F — Ölçüm

### §F.1 — Bu dosyanin olcum tablosu (2026-10-15, ReadAllLines)

| Kalem | Deger |
|---|---|
| Okunan evidence araligi | 3 (§D.1-§D.3) |
| Okunan toplam satir | 23 + 22 + 31 = **76 satir** |
| Olculmus onleme noktasi | 5 (escapeshellarg · onek · is_link · pipe · derinlik) |
| Duzeltme kaydi (indeks ≠ disk) | 3 (§D.4) |

### §F.2 — Ölçüm yontemi

- Satir sayimi: `[System.IO.File]::ReadAllLines(...)` (bu dosyanin kapisi).
- Kanit okumasi: PowerShell `Get-Content` + sabit aralik — 2026-10-15.
- Bu dosya icin **url dogrulamasi yapilmamistir** (yeni URL eklenmedi;
  `syscall/index.md` KANIT tablosu dosya-bazlidir, URL tasimaz).

### §F.3 — Çalışan sistem ölçüsü

- **YOK — PLANNED.** Bu dosyada calistirilmis sandbox/guvenlik testi,
  syscall tracer veya log gozlemi bulunmamaktadir.
- "Calisiyor"/"guvenli" iddiasi bu dosyaya **yazilmaz**; yalnizca
  okunmus kod uzerindeki tespitler yazilmistir.

## §G — Komşu sınır matrisi

| Komşu | İlişki | Kural |
|---|---|---|
| `syscall-mekanizmasi.md` | Cagri adimlari — bu dosyanin altinda | Mekanizma orada, guvenlik burada |
| `syscall-hata-kodlari-ve-errno.md` | Hata ayristirma — guc ciktisi okumasi | §C.1 no.4 oraya referans |
| `isletim-sistemi-api-kapsam.md` §A.3 · K8 | ust kapsam | Sinirlar orada tanimli |
| `cihaz-erisimi` alt dali | Yetki yuzeyi (`CAP_SYS_ADMIN` vb.) | Yetki adlari orada — tekrarlanmaz |
| `platform-api` alt dali | Platforma ozel sinir mekanizmalari | Q-P3 bagli |
| K011/K012 (web) | HTTP/REST | **KAPSAM DISI** (index §KAPSAM) |

## §H — Açık sorular

| # | Soru | Neden önemli | Durum |
|---|---|---|---|
| H1 | Q-P3 — hedef platform seti netlestirilmeli | Arge/sandbox mekanizmasi platforma gore degisir | `⚠️ VERIFICATION REQUIRED` (index §KAPSAM) |
| H2 | Indeks KANIT iddialari neden diskle uyusmuyor? | Yanlis kanit bagi yanlis sonuc uretir | §D.4 kayitli — indeks duzeltmesi ust gorev |
| H3 | Bu repoda yetki yukseltme denemesi var mi? | Test edilmis savunma mi yoksa bosluk mu | `UNKNOWN` |
| H4 | Sandbox mekanizmasi hedef platformda hangisi? | §A.4 uc sorusunun cevabi | `⚠️ VERIFICATION REQUIRED` |
| H5 | `fail(403)` vs `fail(404)` varlik bilgisi sizdirir mi? | Bilgi toplama saldirisi yuzeyi | `UNKNOWN` — test yok (§F.3) |
| H6 | T1-T6 tarzi bir guvenlik testi taslagi var mi? | Kardes dallarda test taslaklari var | Bu dosyada YOK |

## §I — Kaynakça

| # | Kaynak | Tarih | Durum |
|---|---|---|---|
| 1 | `shared/src/AI/AIEngine.php:288-310` | 2026-10-15 okundu | DOGRULANDI |
| 2 | `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` | 2026-10-15 okundu | DOGRULANDI |
| 3 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215` | 2026-10-15 okundu | DOGRULANDI |
| 4 | `syscall/index.md` §DOSYA · §KANIT · §GRUPLAR · §KAPSAM | 2026-10-15 okundu | Manifest kaynagi |
| 5 | `isletim-sistemi-api-kapsam.md` §A.3 · K8 | — | Kapsam referansi (okunmadi, indeks uzerinden) |
| 6 | Yeni dis URL | — | **EKLENMEDI** (bu turda dogrulanmadi) |

## §J — Bağlayıcı özet

### §J.1 — Bu dosyanin kisa hali

| Soru | Cevap | Kanit |
|---|---|---|
| Cagri nasil sinirlanir? | Girdi oncesi dogrulama (kabuk: `escapeshellarg`; yol: `realpath` + onek) | §C.2 · §D.3 |
| Kim yetkili? | Repo'da dosya-bazli erisim zinciri var; yetki-yukseltme yuzeyi tanimsiz | §B.2 · §B.3 |
| Ne onlendi? | 5 olculmus onleme; platforma ozel sandbox tanimsiz | §C.4 · §H |
| Calisiyor mu? | **Bilinmiyor — calistirilmis test yok** | §F.3 |

### §J.2 — Okuma sırası

§0 (cerceve) → §A.3 (arguman siniri) → §D (kanit) → §C.4 (durum) → §H (acik sorular).

### §J.3 — Kapi notu

- Bu dosya yeni uretildi (bolge 6 · 2026-10-15); kardes iki dosya
  (`syscall-mekanizmasi.md` · `syscall-hata-kodlari-ve-errno.md`)
  mevcuttur ve **degistirilmemistir**.
- `syscall/index.md` bu gorevde degistirilmemistir (beyazliste disi);
  §D.4 uyuşmazligi burada kayitlidir.
- `git commit` bu oturumda ATILMADI — degisiklikler working tree'dedir
  (commit orkestratöre ait).

## §K — Test taslağı (PLANNED — calistirilmadi)

> Bu bölüm **taslaktir**; hicbir test bu turda calistirilmamistir (§F.3).
> Amaç: §H acik sorularini kapatmaya aday olcumlu senaryolar taslamak.

### §K.1 — Girdi dogrulama testleri (§A.3 → §C.2)

| # | Test | Girdi | Beklenen | Bag |
|---|---|---|---|---|
| T-G1 | Kabuk kacirma | `"; id #` iceren dosya yolu | `escapeshellarg` tek tirnakli sarmalar; ek komut CALISMAZ | §C.2 satır 292 |
| T-G2 | Bos girdi | bos string yol | sprintf hucresi bos kalmaz; ffprobe argumani bosluk almaz | §C.2 satır 290-293 |
| T-G3 | Derinlik siniri | 512+ katmanli JSON | `JSON_THROW_ON_ERROR` istisna firlatir, asiri bellek TUKENMEZ | §C.2 satır 300 |
| T-G4 | Null cikti | `shell_exec` → null | varsayilan metadata doner (crash yok) | §C.2 satır 296-298 |

### §K.2 — Yol dogrulama testleri (§B.2 → §D.3)

| # | Test | Girdi | Beklenen | Bag |
|---|---|---|---|---|
| T-Y1 | Symlink | kok icinde symlink dosya | satır 185-187 → `fail(403)` | §D.3 |
| T-Y2 | Kok disina | `../` ile dis yol | `str_starts_with` false → `fail(403)` | §D.3 satır 199-202 |
| T-Y3 | Var olmayan dosya | bilinmeyen yol | `realpath` false → `fail(404)` | §D.3 satır 189-192 |
| T-Y4 | Okunamayan | chmod 000 benzeri | `is_readable` false → `fail(403)` | §D.3 satır 204-206 |
| T-Y5 | Onek yazma hatasi | `rootX` vs `root` koklu | `DIRECTORY_SEPARATOR` sondan eklenir — `rootX` gecmez | §D.3 satır 199 |

### §K.3 — Surec yasam dongusu testleri (§C.3)

| # | Test | Senaryo | Beklenen | Bag |
|---|---|---|---|---|
| T-S1 | stdin kapanisi | alt surec stdin okursa | pipe hemen kapatildigi icin EOF — kilitlenme YOK | §C.3 satır 262 |
| T-S2 | exit kodu | basarisiz CLI | `proc_close` → `['exit' => !=0]` | §C.3 satır 268 |
| T-S3 | acilamayan process | gecersiz binary | `assertIsResource` FAIL (test dusmesi gerekir) | §C.3 satır 260 |
| T-S4 | Pipe temizligi | test sonu | `pipes[1]`/`pipes[2]` kapatilmis | §C.3 satır 265-266 |

### §K.4 — Kapi kosullari

- Bu testler **calistirilmadan** "geçti" iddiasi yazılmaz.
- Calistirma icin ortam: PHP + PHPUnit (`shared/tests/`) — repo kaniti
  `runCli` icin `PHP_BINARY` + `self::BIN` (§C.3 satır 250).
- Sonuç tablosu bos — dolan nokta: §F.3 (calisan sistem olcusu YOK).

## §L — Sık yapılan hatalar (bu dalda gorulmesi muhtemel 8 hata)

| # | Hata | Sonuc | Dogru yapim | Bag |
|---|---|---|---|---|
| M1 | Girdiyi kabuga birlestirdikten sonra kaclirma | enjeksiyon | Once `escapeshellarg`, sonra birlestir | §C.2 satır 292 |
| M2 | `realpath` sonucunu onek-kontrol etmeden kullanma | traversal | `str_starts_with` + `DIRECTORY_SEPARATOR` onek | §D.3 satır 199-202 |
| M3 | Symlink kontrolunu yolu cozdukten sonraya birakma | TOCTOU | `is_link` **once** (satır 185), sonra realpath | §D.3 |
| M4 | stdin pipe'ini acik birakma | surec kilitlenmesi | Hemen `fclose($pipes[0])` | §C.3 satır 262 |
| M5 | `shell_exec` nullunu yok sayma | sessiz yanlis veri | null → acik varsayilan dal | §C.2 satır 296-298 |
| M6 | JSON'u sinirsiz derinlikte parse | bellek/cpu tuketimi | derinlik siniri (512) | §C.2 satır 300 |
| M7 | 403/404 farkini test etmemek | bilgi sizdirma | Ayrac testi — acik soru §H.5 | §E H7 |
| M8 | Indeks iddiasini disk okumadan tekrarlamak | yanlis kanit | Diskten oku, uyusmazsa kaydet | §D.4 |

## §M — Senaryo akışları (adım adım — kavramsal, olculmemis)

### §M.1 — Akış A: guvenli dosya servisi (repo uzerinden)

```text
1. İstek gelir                         → MusicStreamHandler.php:185
2. Symlink mi?                         → evetse fail(403)   [satır 185-187]
3. Gercek yol var mi?                  → hayirsa fail(404)  [satır 189-192]
4. Koku belirle                        → realpath(libraryPath) [satır 194]
5. Onek icinde mi?                     → degilse fail(403)  [satır 199-202]
6. Okunabilir mi?                      → degilse fail(403)  [satır 204-206]
7. Gercek yol doner                    → satır 208
```

### §M.2 — Akış B: kacirmali dis komut (repo uzerinden)

```text
1. Dosya yolu gelir                    → AIEngine.php:288
2. Komut sablonu hazirla               → satır 290-293 (ffprobe)
3. Argumani kacir                       → escapeshellarg     [satır 292]
4. Calistir                             → shell_exec         [satır 295]
5. Null mu?                             → evetse varsayilan  [satır 296-298]
6. JSON dogrula (derinlik 512)          → JSON_THROW_ON_ERROR [satır 300]
7. Alanlari ayr                     → satır 304-309
```

### §M.3 — Akış C: test icinde surec (repo uzerinden)

```text
1. CLI argumanlari                      → Test.php:250
2. proc_open (3 pipe, cwd)              → satır 253-258
3. Kaynak acildi mi?                    → assertIsResource   [satır 260]
4. stdin kapat                          → fclose(pipes[0])   [satır 262]
5. stdout/stderr oku                    → stream_get_contents[_satır 263-264]
6. pipe'lari kapat                       → satır 265-266
7. exit kodu al                          → proc_close         [satır 268]
```

> Bu uc akis da **kod okuyarak** cikarilmistir; hicbiri calistirilmamistir
> (§F.3 ile tutarli).

## §N — Terim sözlüğü (bu dosya)

| Terim | Kisa tanim |
|---|---|
| Sandbox | Process'in dis dunya erisiminin kisitlandigi cevre (§A.4) |
| TOCTOU | Kontrol ile kullanim arasindaki yaris kosusu (§B.2) |
| Fail-safe | Bilinmeyende reddet/varsayilana dus (§C.1 no.2) |
| Onek kontrolu | Gercek yolun onceden tanimli kok ile baslamasi (§D.3) |
| Kacirma | Girdinin komut/yol olarak okutulmasini onleme (§C.2) |
| Pipe kilitlenmesi | Acik pipe uzerinde sonsuz bekleme (§C.3) |
| Derinlik siniri | Iicegisme icin parse siniri (§C.2, 512) |
| Yetki yukseltme | Sinirli process'in yetkili cagrisi (§B.3) |

## §O — Değişiklik günlüğü (bu dosya)

| Tarih | Tur | Not |
|---|---|---|
| 2026-10-15 | Yeni dosya (bolge 6) | Ilk yazim — 5 yeni dosyadan biri |
| 2026-10-15 | Kanit okumasi | 3 evidence araligi diskten okundu (§D.1-§D.3) |
| 2026-10-15 | Duzeltme kaydi | Indeks KANIT iddialari diskle uyusmadi (§D.4) — indeks DEGISTIRILMEDI |
| 2026-10-15 | Kapi | `ReadAllLines` ≥505 hedefi - olcum §F.1'de |

## §P — Sık sorulanlar (hızlı okuma)

| # | Soru | Cevap | Bölüm |
|---|---|---|---|
| S1 | Bu dosya neyi kapsar? | Cagri sinirlandirma · yetki kontrolu · guvenlik acigi onleme | §0 |
| S2 | Hangi dosyalar kardes? | `syscall-mekanizmasi.md` · `syscall-hata-kodlari-ve-errno.md` | §0 |
| S3 | Kapsam siniri nerede? | `isletim-sistemi-api-kapsam.md` §A.3 · K8 | §G |
| S4 | Repo'da kanit var mi? | Evet — 3 aralik, 76 satir okundu (2026-10-15) | §F.1 |
| S5 | En somut onleme ne? | `escapeshellarg` (AIEngine.php:292) | §C.2 |
| S6 | En somut erisim kontrolu ne? | Dort kademeli zincir (MusicStreamHandler.php:185-208) | §B.2 |
| S7 | Sandbox mekanizmasi hangisi? | TANIMSIZ — Q-P3 acik, uydurulmaz | §H H1 |
| S8 | Test calisti mi? | HAYIR — taslak §K, calistirilmadi | §F.3 |
| S9 | Indeksle celiski var mi? | EVET — 3 iddia diskle uyusmuyor, kayit §D.4 | §D.4 |
| S10 | C++ kodu var mi? | HAYIR — `*.cpp`/`*.h` = 0 (2026-10-15) | §D.5 |
| S11 | Web/HTTP kapsamda mi? | HAYIR — K011/K012 kapsam disi | §G |
| S12 | commit atildi mi? | HAYIR — working tree (orkestratöre ait) | §J.3 |

## §Q — Kardes dosyalarla karsilastirma (salt okuma)

> Bu tablo **kardes dosyalarin icerigini kopyalamaz**; rol dagilimini
> gosterir. Kardes dosyalar bu gorevde **degistirilmemistir**.

| Eksen | `syscall-mekanizmasi` | `syscall-hata-kodlari-ve-errno` | **Bu dosya** |
|---|---|---|---|
| Ana soru | Cagri nasil calisir? | Hata nasil ayristirilir? | Cagri nasil sinirlanir? |
| Grup (indeks) | mimari | mimari | mimari (index §GRUPLAR) |
| Girdi | Adimlar · register · arguman siniri | Donus degeri · errno · siniflandirma | Girdi dogrulama · yetki · onleme |
| Kanit tipi | Mekanizma | Hata ayristirma | 3 PHP araligi (§D) |
| Bulus iliskisi | Alt katman | Kardes okuma | Ustunde guvenlik kati |

### §Q.1 — Üst-alt ilişkisi

```text
isletim-sistemi-api-kapsam.md (§A.3 · K8)     ← kapsam siniri
        │
        ├── syscall-mekanizmasi.md            ← nasil calisir
        ├── syscall-hata-kodlari-ve-errno.md  ← hata nasil okunur
        └── syscall-guvenlik-ve-sandbox.md    ← nasil sinirlanir (BU DOSYA)
```

### §Q.2 — Bu dosyanın yazma yetkisi sınırı

| Yazabilir | Yazamaz |
|---|---|
| Kendi icerigi (§A-§Q) | `syscall/index.md` (beyazliste disi) |
| Kendi evidence kayitlari | Kardes iki dosya |
| Kendi acik sorulari | `*.php` / `*.js` / `*.css` / `*.sql` kaynak kod |
| PLANNED test taslagi (§K) | `.ai/AGENTS.md` · `.ai/CLAUDE.md` · `architecture/index.md` |

### §Q.3 — Kapi turu (neden bu dosya)

| Kapi | Kriter | Durum |
|---|---|---|
| 1 | Icerik dosyasi ≥500 satir (`ReadAllLines`) | §F.1 + olcum satiri (asagida) |
| 2 | Frontmatter v2 §4.2 — 9 alan + `group` enum | FM `---` icinde (mimari · kod · kanit enum'u) |
| 3 | Zero-hallucination — her iddia `Dosya: <yol> [Satır: n]` veya tarihli URL | §D · §I |
| 4 | Append-only — mevcut satir silinmedi (yeni dosya: dogrudan ilgisiz) | §O gunlugu |
| 5 | `git commit` — ATILMADI | §J.3 |

- **Ölçüm (2026-10-15):** bu dosya `ReadAllLines` ile ölçülmüştür — kapı
  değeri üstteki §F.1 tablosuyla birlikte okunur.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
