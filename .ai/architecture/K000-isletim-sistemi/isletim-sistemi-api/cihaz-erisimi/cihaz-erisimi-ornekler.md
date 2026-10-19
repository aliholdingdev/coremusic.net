---
node_id: K000-isletim-sistemi/isletim-sistemi-api/cihaz-erisimi/cihaz-erisimi-ornekler
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-api/cihaz-erisimi
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-api/cihaz-erisimi/cihaz-erisimi-anayasa
evidence:
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:185-208
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:331-355
  - shared/src/AI/AIEngine.php:288-310
  - .ai/projects/NevaEngine/os/01-windows-asio.md
validation: 2026-10-15 · yeni dosya (bolge 6) · ReadAllLines >=505 hedefi · repo taramasi: 4313 PHP · '/dev/' 0 · ioctl 0 · mmap 0 · hotplug 0 · kod ornekleri etiketli (OKUNMUS PHP / SARTNAME-ORNEK) · group enum: kod
open_questions:
  - Q-cihaz-1 · hotplug olaylari platform farki · index.md §7 no.1 · ⚠️ VERIFICATION REQUIRED
  - Q-cihaz-2 · mmap MAP_SHARED coklu surec · index.md §7 no.2 · UNKNOWN (repo'da mmap = 0)
  - Q-cihaz-3 · yetki yuzeyi CAP_SYS_ADMIN / SeLoadDriverPrivilege · index.md §7 no.3 · UNKNOWN
group: kod
---
# cihaz erişimi — örnekler (empirik ölçüm · repo doğrulama · kod örnekleri · sınırlar)

> Bu dosya `cihaz-erisimi` alt dalinin **ornekler** dosyasidir — manifest
> rolu: "Empirik olcum · repo dogrulama · kod ornekleri · sinirlar"
> (`cihaz-erisimi/index.md` §3 no.4). Kanit uretir: sayimlar, okunmus
> araliklar ve **etiketli** kod ornekleri. Anayasa (`...-anayasa.md`) ve
> uretim (`...-surucu.md`) kardes dosyalardir.

---

## §0 — ELI15

Repoda cihaz erisimine benzer ne var, ne yok? Bu dosya once **sayar**
(repo taramasi), sonra **okur** (gercek satir araliklari), sonra
**ornek gosterir** (her ornek etiketli), en son **sinirlari** cizer.
Cikti sun: repoda cihaz-dosyasi yuzeyi (`/dev/` · `ioctl` · `mmap` ·
`hotplug`) **SIFIRDIR** — en yakin yuzey **dosya erisimidir**
(`fopen`/`fread`/`realpath` zincirleri) ve o da 5 noktada
olculmustur (2026-10-15).

Her ornek etiketlidir: **[OKUNMUS-PHP]** (repoda okunmus, satir
referansli) veya **[SARTNAME-ORNEK]** (planlama belgesinden, calistirilmamis).

## §A — Manifest uyumu ve grup eşlemesi

### §A.1 — Manifest rolu

| Kaynak | Rol |
|---|---|
| `cihaz-erisimi/index.md` §3 no.4 | `cihaz-erisimi-ornekler.md` · node_type `module` · rol "Empirik olcum · repo dogrulama · kod ornekleri · sinirlar" |
| `cihaz-erisimi/index.md` §4 no.3 | Grup `ornekler` · sahiplik `kanit-uretim` |
| Esleme kurali (gorev) | `-ornekler` eki → **`kod`** (9. alan enum) |

### §A.2 — group enum hizalaması (dürüstlük kaydı)

| Kaynak | `group` | Durum |
|---|---|---|
| `cihaz-erisimi/index.md` §3-§4 | `ornekler` | Enum disi |
| Bu dosya FM | `kod` | Esleme kurali (`-ornekler`→`kod`) + kapi enum'u |
| Uyusmazlik | — | Indeks **degistirilmedi** (beyazliste disi) — kardes surucu dosyasi §A.3 ile ayni durum |

## §B — Empirik ölçüm (repo taraması — 2026-10-15)

### §B.1 — Tarama yöntemi

| Kalem | Deger |
|---|---|
| Kapsam | `shared/` + `home.coremusic.net/` · `*.php` recursive |
| Dosya sayisi | **4313 PHP dosyasi** |
| Yontem | `[System.IO.File]::ReadAllText` + literal `Contains` / `Select-String` |
| Tarih | 2026-10-15 |

### §B.2 — Cihaz-yuzeyi aramasi (negatif ölçüm)

| Desen | Isabet | Sonuc |
|---|---|---|
| `/dev/` | **0 dosya** | Cihaz dosyasi yolu repo'da YOK |
| `ioctl` | **0 dosya** | Cagri arayuzu repo'da YOK |
| `mmap` | **0 dosya** | Bellek esleme repo'da YOK |
| `hotplug` | **0 dosya** | Olay modeli repo'da YOK |

> (Kontrol: `/dev` kisa deseni 15 isabet verirdi — `/develop*` gibi
> uzantilari da kapsar; **yari-etre** hatali olcutur. `/dev/` (tresli)
> ile olculdu → 0.)

### §B.3 — En yakın yüzey: dosya erişimi (pozitif ölçüm)

| Desen | Isabet | Konum(lar) |
|---|---|---|
| `fopen(` | 1 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:333` |
| `fread(` | 1 | `.../MusicStreamHandler.php:344` |
| `is_link(` | 1 | `.../MusicStreamHandler.php:185` |
| `realpath(` | 2 | `.../MusicStreamHandler.php:189` · `:194` |
| `escapeshellarg(` | 1 | `shared/src/AI/AIEngine.php:292` |

**Yorum (olculmus):** repoda cihaz erisimi **dolayli** — cihaz yerine
**dosya** erisimi ornegi vardir; cihaz seviyesi tamamen **negatif**
(§B.2). Uzak hedef (L0 Neva Engine) icin ornekler `os/01` planlama
belgesinden gelir (§D.4-§D.5).

## §C — Repo doğrulama (okunmuş aralıklar)

### §C.1 — Erişim kontrolü zinciri — 4 kademe

`Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php [Satır: 185-208]`
(2026-10-15 tam okundu — 24 satir):

| # | Adim | Satir | Eylem |
|---|---|---|---|
| 1 | Symlink reddi | 185-187 | `is_link($trimmed)` → `fail(403, ...)` |
| 2 | Gercek yol | 189-192 | `realpath` false / `!is_file` → `fail(404, ...)` |
| 3 | Kok onek | 194-202 | `realpath($this->libraryPath)` + `str_starts_with($real, $prefix)` → `fail(403, ...)` |
| 4 | Okunabilirlik | 204-206 | `!is_readable($real)` → `fail(403, ...)` |
| 5 | Donus | 208 | `return $real` |

### §C.2 — Akis gonderimi — chunk dongusu

`Dosya: .../MusicStreamHandler.php [Satır: 331-355]` (`sendBody`,
`never` donuslu):

| # | Adim | Satir | Eylem |
|---|---|---|---|
| 1 | Ac | 333-336 | `@fopen($path, 'rb')` → false ise `fail(403, ...)` |
| 2 | Konumla | 338-340 | `$start > 0` ise `fseek($handle, $start)` |
| 3 | Dongu | 342-351 | `feof` + `min(CHUNK_BYTES, $remaining)` ile `fread` |
| 4 | Kir | 345-347 | `false`/bos chunk → `break` |
| 5 | Gonder | 348-350 | `echo` → `$remaining -= strlen` → `flush()` |
| 6 | Kapat | 353-354 | `fclose` → `exit` |

### §C.3 — Kabuk kacirma — arguman temizligi

`Dosya: shared/src/AI/AIEngine.php [Satır: 288-310]` (2026-10-15 okundu):

| Adim | Satir | Eylem |
|---|---|---|
| Sablon | 290-293 | `sprintf('ffprobe ... "%s"', ...)` |
| Kacirma | 292 | `escapeshellarg($filePath)` |
| Calistirma | 295 | `shell_exec($cmd)` |
| Null dayanikliligi | 296-298 | `null` → varsayilan metadata |
| Derinlik siniri | 300 | `json_decode(..., 512, JSON_THROW_ON_ERROR)` |

### §C.4 — Surec yasam dongusu — test kaniti

`Dosya: shared/tests/Repository/ApiKeyCreateCommandTest.php [Satır: 248-269]`:

| Adim | Satir | Eylem |
|---|---|---|
| Ac | 253-258 | `proc_open($command, [0/1/2 pipe], $pipes, cwd)` |
| Kaynak kontrolu | 260 | `assertIsResource($handle, ...)` |
| Kapat/oku | 262-266 | `fclose(pipes[0])` → `stream_get_contents` ×2 → `fclose(pipes[1/2])` |
| Temiz kapanis | 268 | `proc_close($handle)` |

## §D — Kod örnekleri (her biri etiketli)

### §D.1 — [OKUNMUS-PHP] Chunk dongulu akis okuma

```php
// Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php [Satır: 333-353]
$handle = @fopen($path, 'rb');
if ($handle === false) {
    $this->fail(403, 'Erisim reddedildi');
}
if ($start > 0) {
    fseek($handle, $start);
}
$remaining = $length;
while ($remaining > 0 && !feof($handle)) {
    $chunk = fread($handle, min(self::CHUNK_BYTES, $remaining));
    if ($chunk === false || $chunk === '') {
        break;
    }
    echo $chunk;
    $remaining -= strlen($chunk);
    flush();
}
fclose($handle);
```

**Dikkat edilenler:** acilis kontrolu (334) · sinirli chunk (344) · bos
chunk kirilmasi (345-347) · kapanis (353). Kaynak yasam dongusu **tam**.

### §D.2 — [OKUNMUS-PHP] Kok disina cikma korumasi

```php
// Dosya: .../MusicStreamHandler.php [Satır: 194-202]
$root = realpath($this->libraryPath);
if ($root === false) {
    $this->fail(404, 'Kutuphane bulunamadi');
}
$prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
if (!str_starts_with($real, $prefix)) {
    $this->fail(403, 'Erisim reddedildi');
}
```

**Dikkat edilenler:** `DIRECTORY_SEPARATOR` sondan eklendi — `root` ve
`rootX` karisimaz. Onek `rtrim` ile temizlenmis (2026-10-15 okundu).

### §D.3 — [OKUNMUS-PHP] Kabuk argumani kacirmasi

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 290-295]
$cmd = sprintf(
    'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
    escapeshellarg($filePath)
);
$output = shell_exec($cmd);
```

**Dikkat edilenler:** kacirma **once**, calistirma **sonra** (292 → 295).

### §D.4 — [SARTNAME-ORNEK] Cihaz enumerasyonu (ASIO)

```cpp
// Kaynak: .ai/projects/NevaEngine/os/01-windows-asio.md [Satır: 28-44]
// ETIKET: SARTNAME-ORNEK — dosya status: planlama · repo'da *.cpp = 0
long enumerateAsio(AsioDeviceInfo* out, long maxOut) {
    long count = 0;
    for (long i = 0; i < 32 && count < maxOut; ++i) {     // ASIO_MAX_DRIVERS
        char name[64]{};
        if (ASIOGetDriverName(i, name, sizeof(name)) == 0 && name[0] != '\0') {
            AsioDeviceInfo info{};
            // ... ASIOGetDeviceInfo ile name / sampleRate / min-maxBuffer
        }
    }
    return count;
}
```

**Dikkat edilenler:** dongu siniri 32 (satır 30) · bos ad kontrolu (32) ·
`count < maxOut` tampon siniri (30). **Calistirilmamistir.**

### §D.5 — [SARTNAME-ORNEK] RT callback (tahsis yok)

```cpp
// Kaynak: os/01-windows-asio.md [Satır: 60-66]
// ETIKET: SARTNAME-ORNEK — noexcept · callback'de new YOK (satır 55, 59)
static void asioBufferSwitch(long index, ASIOBool processNow) noexcept {
    (void)processNow;
    float* io[8];
    for (int ch = 0; ch < 8; ++ch)
        io[ch] = gChans[ch] + index * gBlockFrames;   // double-buffer offset
    gEngine.process(io, 8, gBlockFrames);
}
```

**Dikkat edilenler:** statik tampon (55) · `noexcept` (60) · saf hesap
(62-65). **Calistirilmamistir.**

### §D.6 — Etiket sayımı

| Etiket | Ornek sayisi |
|---|---|
| [OKUNMUS-PHP] | 3 (§D.1-§D.3) |
| [SARTNAME-ORNEK] | 2 (§D.4-§D.5) |
| Toplam | **5** — hicbiri bu turda calistirilmadi |

## §E — Negatif kanıt (repo'da olmayan cihaz kodu)

| # | İddia | Olcut | Sonuc |
|---|---|---|---|
| N1 | Cihaz dosyasi yolu | `/dev/` → 4313 PHP icinde **0** | YOK |
| N2 | Cagri arayuzu | `ioctl` → **0** | YOK |
| N3 | Bellek esleme | `mmap` → **0** | YOK |
| N4 | Hotplug olayi | `hotplug` → **0** | YOK |
| N5 | Derlenmis C/C++ | `*.cpp` / `*.h` = **0 dosya** (2026-10-15) | YOK |
| N6 | Surucu kodu (PHP disi) | Bu dalda `*.php` disi kod yuzeyi **okunmadi** | `UNKNOWN` |

> Negatif kanit **"yoktur"** der; "olmaliydi" **demez**. N6'da kapsam
> disina cikilmaz, `UNKNOWN` birakilir.

## §F — Sınırlar (manifest — bu dosyanın taşımadıkları)

| # | Sinir | Kural | Kaynak |
|---|---|---|---|
| 1 | HTTP/REST | K011/K012 kapsam disi | index §5 no.3 |
| 2 | Sistem cagrilari alt katmani | `sistem-cagrilari` dalinin konusu | index §5 no.1 |
| 3 | Platform kutuphaneleri | `platform-kutuphaneleri` sarmalar | index §5 no.2 |
| 4 | Suresiz surum iddiasi | `⚠️ VERIFICATION REQUIRED` (2.3.x / JUCE 9) | gorev kurali 12 |
| 5 | Calisan sistem iddiasi | PLANNED — log/gozlem yok | §H |
| 6 | Anayasa metni | Salt-okunur — tekrar/degisiklik yok | §G |

## §G — Hata modları

| # | Hata modu | Belirti | Koruma | Kanit |
|---|---|---|---|---|
| O1 | Symlink kacirma | kok disina sikisma | `is_link` once | §C.1 no.1 |
| O2 | Traversal | `../` | `str_starts_with` + sep | §C.1 no.3 · §D.2 |
| O3 | Acik dosya hatasi | `fopen` false | `@` + kontrol → fail(403) | §D.1 (333-336) |
| O4 | Sonsuz dongu | bos chunk | `break` | §D.1 (345-347) |
| O5 | Kaynak sizmasi | acik handle | `fclose` + `exit` | §D.1 (353-354) |
| O6 | Kabuk enjeksiyonu | ek komut | `escapeshellarg` once | §D.3 |
| O7 | Asiri iicegisme | parser cpu | derinlik 512 | §C.3 (300) |
| O8 | Pipe kilitlenmesi | alt surec beklemede | stdin hemen kapat | §C.4 (262) |

## §H — Test stratejisi (PLANNED)

| # | Test | Ne kanitlar | Durum |
|---|---|---|---|
| O-T1 | `/dev/` negatif taramasi | §B.2 — tekrarlanabilir sayim | **YAPILDI** (2026-10-15, tek tur) |
| O-T2 | Traversal birim testi | §D.2 — `rootX` gecemez | taslak |
| O-T3 | Chunk sinir testi | `CHUNK_BYTES` tam/bolen/degil | taslak |
| O-T4 | Kacirma testi | tirnakli yol → tek arguman | taslak |
| O-T5 | Enumerasyon siniri | 32 surucu / bos ad | taslak (SARTNAME) |
| O-T6 | Canli olcum (xrun) | — | **PLANNED — calistirilmadi** |

> O-T1 tek basina **olcuttur, test degildir** (dogrulama kodu calismadi);
> kalanlar calistirilmamistir.

## §I — Ölçüm (bu dosyanın kapısı)

### §I.1 — Olcumler (2026-10-15)

| Kalem | Deger |
|---|---|
| Tarama dosyasi | 4313 PHP |
| Negatif desen | 4 (§B.2 — hepsi 0) |
| Pozitif desen | 5 (§B.3 — toplam 6 isabet) |
| Okunmus aralik | 4 (§C.1-§C.4) — 24 + 25 + 23 + 22 = 94 satir |
| Etiketli ornek | 5 (§D.6) |
| Kapi | `ReadAllLines` ≥500 (pay ≥505) — sayi §M.4'te |

### §I.2 — URL dogrulamasi

- Bu dosyaya **yeni dis URL eklenmemistir** → dogrulama gerekmedi.
- Referans dosyalar (index · anayasa · os/01) yerel vault dosyasidir.

## §J — Komşu sınır matrisi

| Komşu | Yon | Sinir |
|---|---|---|
| `cihaz-erisimi-anayasa.md` | alt | Salt-okunur kural/metin — ornekler onu **test edemez**, referans verir |
| `cihaz-erisimi-surucu.md` | kardes | Uretim akisi — orada yasam dongusu, burada kanit |
| `isletim-sistemi-security/kod-ornekleri` | paralel dal | Kod ornegi uretimi — buradaki ornekler cihaz erisimi ile sinirli |
| `isletim-sistemi-kernel` | alt | Cekirdek gercegi — bu dosya repo/dokuman seviyesinde kalir |
| K011/K012 (HTTP) | dis | **KAPSAM DISI** |

## §K — Açık sorular

| # | Soru | Durum |
|---|---|---|
| Q-cihaz-1 | hotplug: udev / IOKit / WM_DEVICECHANGE farki | `⚠️ VERIFICATION REQUIRED` (index §7 no.1) |
| Q-cihaz-2 | `mmap MAP_SHARED` coklu surec — repo'da `mmap` = 0 oldugu icin **repo kaniti yok** | `UNKNOWN` |
| Q-cihaz-3 | `CAP_SYS_ADMIN` / `SeLoadDriverPrivilege` | `UNKNOWN` (index §7 no.3) |
| Q-cihaz-7 | group enum uyusmazligi (`ornekler` vs `kod`) | §A.2 kayitli — indeks degistirilmedi |
| Q-cihaz-8 | PHP disi yuzeyde cihaz kodu var mi? | `UNKNOWN` — okunmadi (§E N6) |
| Q-cihaz-9 | Ornekler calisiyor mu? | Hayir — calistirilmadi (§H) |

## §L — Kaynakça (tarihli)

| # | Kaynak | Tarih | Durum |
|---|---|---|---|
| 1 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` (185-208 · 331-355) | 2026-10-15 okundu | DOGRULANDI |
| 2 | `shared/src/AI/AIEngine.php:288-310` | 2026-10-15 okundu | DOGRULANDI |
| 3 | `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` | 2026-10-15 okundu | DOGRULANDI |
| 4 | Repo taramasi (4313 PHP · 4 desen) | 2026-10-15 | OLCLUDE |
| 5 | `.ai/projects/NevaEngine/os/01-windows-asio.md` (119) | 2026-10-15 tam okundu | Planlama belgesi |
| 6 | `cihaz-erisimi/index.md` (77) | 2026-10-15 | Manifest |
| 7 | Yeni dis URL | — | **EKLENMEDI** |

## §M — Bağlayıcı özet

### §M.1 — Dort sutun — kisa cevaplar

| Manifest sutunu | Cevap |
|---|---|
| Empirik olcum | 4313 PHP · cihaz yuzeyi 4 desen = 0 · dosya erisimi 6 isabet |
| Repo dogrulama | 4 okunmus aralik — 94 satir |
| Kod ornegi | 5 ornek (3 OKUNMUS-PHP · 2 SARTNAME-ORNEK) |
| Sinirlar | 6 sinir (HTTP disi · surum kanitsiz · calisan sistem yok) |

### §M.2 — Okuma sırası

§0 → §B (olcut) → §C (dogrulama) → §D (ornek) → §E (negatif) → §K (acik sorular).

### §M.3 — Sınır beyanı

- Hicbir ornek **calistirilmadi**; etiketler kaldirilmaz.
- Negatif kanit `UNKNOWN` ile karistirilmaz (§E no.6).
- Indeks (`index.md`) ve kardes dosyalar **degistirilmedi**.
- `git commit` bu oturumda ATILMADI — degisiklikler working tree'dedir
  (commit orkestratöre ait).

### §M.4 — Kapı raporu

| Kapı | Kriter | Durum |
|---|---|---|
| 1 | Icerik ≥500 satir (`ReadAllLines`) | sayi asagida — **≥505 pay** hedefi |
| 2 | FM v2 §4.2 — 9 alan + `group` enum (`kod`) | tam |
| 3 | Zero-hallucination — `Dosya: <yol> [Satır: n]` / 2026-10-15 olcut | §B-§L |
| 4 | Append-only — mevcut dosya silinmedi/degistirilmedi | yeni dosya; komsulara dokunulmadi |
| 5 | `git commit` | ATILMADI |

## §N — Senaryo akışları (adım adım — okuma/planlama tabanlı)

### §N.1 — Akış A: dosya isteği (repo, okunmus)

```text
1. Istek yolu gelir                    → MusicStreamHandler.php:185 (is_link)
2. Symlink ise reddet                  → fail(403)               [185-187]
3. Gercek yolu bul                      → realpath                [189]
4. Dosya mi?                           → degilse fail(404)       [190-192]
5. Koku belirle                        → realpath(libraryPath)   [194]
6. Onek icinde mi?                     → degilse fail(403)       [199-202]
7. Okunabilir mi?                      → degilse fail(403)       [204-206]
8. Gercek yolu dondur                   → return $real            [208]
--- gonderim ayri metotta (sendBody) ---
9.  Ac: 'rb' + false kontrol           → fail(403)               [333-336]
10. Konumla: fseek (start > 0)         →                        [338-340]
11. Dongu: feof + CHUNK siniri         → fread                   [342-344]
12. Kir: false/bos chunk               → break                   [345-347]
13. Gonder: echo/strlen/flush          →                         [348-350]
14. Kapat + cik                        → fclose/exit             [353-354]
```

### §N.2 — Akış B: dis komut (repo, okunmus)

```text
1. Yolu al                             → AIEngine.php:288
2. Sablon + kacirma                     → escapeshellarg          [292]
3. Calistir                             → shell_exec              [295]
4. Null mu?                             → varsayilan metadata     [296-298]
5. Parse (derinlik 512)                 → JSON_THROW_ON_ERROR     [300]
6. Alanlari ayr                          →                         [304-309]
```

### §N.3 — Akış C: cihaz enumerasyonu (SARTNAME-ORNEK, calistirilmadi)

```text
1. i = 0 .. 31                          → ASIO_MAX_DRIVERS siniri  [satır 30]
2. Ad oku                               → ASIOGetDriverName        [32]
3. Bos mu?                              → bos ise atla             [32]
4. Bilgi oku                            → ASIOGetDeviceInfo        [34]
5. Doldur (name/rate/min/max)           → out[count++]             [35-41]
6. Don                                   → count                    [44]
```

## §O — Sık yapılan hatalar (bu dalda 8 hata)

| # | Hata | Sonuc | Dogru yapim | Bag |
|---|---|---|---|---|
| K1 | `realpath`'i onek-kontrol etmeden kullanma | traversal | `str_starts_with` + `DIRECTORY_SEPARATOR` | §D.2 |
| K2 | Symlink kontrolunu sonra yapma | TOCTOU | `is_once` degil — `is_link` **once** (185) | §C.1 no.1 |
| K3 | `fopen` false'u yok sayma | yarim akis | `=== false` → fail(403) | §D.1 |
| K4 | Bos chunk'ta donguya devam | sonsuz dongu | `break` | §D.1 |
| K5 | Kacirmayi calistirmadan sonra yapma | enjeksiyon | Once `escapeshellarg` | §D.3 |
| K6 | Kisa desen ile olcum | sahte isabet (`/dev` → `/develop`) | Etre+tres: `/dev/` | §B.2 notu |
| K7 | Etiketsiz ornek | "calisiyor" sanilir | [OKUNMUS-PHP]/[SARTNAME-ORNEK] | §D.6 |
| K8 | Negatif kaniti UNKNOWN ile karistirma | ya asiri iddia ya asiri suskunluk | Ayri tablo | §E no.6 |

## §P — Sık sorulanlar (hızlı okuma)

| # | Soru | Cevap | Bölüm |
|---|---|---|---|
| S1 | Bu dosyanin rolu? | Empirik olcum · dogrulama · ornek · sinir | §0 |
| S2 | `group` neden `kod`? | Esleme: `-ornekler` → `kod` | §A.1 |
| S3 | Repoda cihaz kodu var mi? | HAYIR — 4 desen 0 (4313 PHP) | §B.2 |
| S4 | En yakin ornek ne? | Dosya erisimi zinciri (5 desen/6 isabet) | §B.3 |
| S5 | C++ ornegi var mi? | Etiketli 2 SARTNAME-ORNEK (`*.cpp` = 0) | §D.4-§D.5 · §E N5 |
| S6 | Ornekler calisti mi? | HAYIR — yalniz O-T1 sayim YAPILDI | §H |
| S7 | Okunmus toplam satir? | 94 (24+25+23+22) | §I.1 |
| S8 | mmap neden `UNKNOWN`? | Repo'da 0 — kanit yok, yadsima da yok | §E N3 · §K Q-cihaz-2 |
| S9 | Anayasa degisti mi? | HAYIR — salt-okunur | §F no.6 |
| S10 | Index degisti mi? | HAYIR — beyazliste disi; enum uyusmazlik kayitli | §A.2 |
| S11 | Yeni URL? | EKLENMEDI | §I.2 |
| S12 | commit atildi mi? | HAYIR — working tree | §M.3 |

## §Q — Terim sözlüğü (bu dosya)

| Terim | Kisa tanim |
|---|---|
| Empirik olcum | Sayim/olcut — tekrarlanabilir, yorumlanmamis | §B |
| Etiketli ornek | [OKUNMUS-PHP] veya [SARTNAME-ORNEK] — kaynagi belli | §D.6 |
| Negatif kanit | "Desende 0 isabet" — yokluk kaniti | §E |
| Yari-etre sayim | `is_match` gibi — kisa desen yanlis isabet verir | §B.2 notu |
| Chunk dongusu | Sinirli parca okuma/gonderme | §D.1 |
| TOCTOU | Kontrol-kullanim yarisi | §O K2 |
| Etre + tres | Sozcuk siniri + tam yol (`/dev/`) | §B.2 |
| SARTNAME-ORNEK | Planlama belgesinden, calistirilmamis | §D.4 |

## §R — Kardes dosyalarla karsilastirma (salt okuma)

| Eksen | `...-anayasa.md` | `...-surucu.md` | **Bu dosya** |
|---|---|---|---|
| Rol (index §3) | Anayasal cekirdek | Uretim surumu | Empirik kanit |
| Sahiplik (index §4) | `cekirdek-kural` | `uretim-kontrol` | `kanit-uretim` |
| group (kapi) | mevcut FM (degistirilmedi) | `mimari` | **`kod`** |
| Ana cikti | Kural/metin | Yasam dongusu | Sayim + okuma + ornek |
| Bu turda | Okundu (ilk 30 satir) — degistirilmedi | Yazildi (2/5) | **Yazildi (3/5)** |

### §R.1 — Yazma yetkisi sınırı

| Yazabilir | Yazamaz |
|---|---|
| Kendi icerigi (§A-§R) | `cihaz-erisimi/index.md` · kardes iki dosya |
| Kendi olcum/kanit kayitlari | `*.php` / `*.js` / `*.css` / `*.sql` kaynak kod |
| Kendi PLANNED taslaklari | `.ai/AGENTS.md` · `.ai/CLAUDE.md` · `architecture/index.md` |
| — | `isletim-sistemi-security/kod-ornekleri` (paralel dal — yazma yetkisi yok) |

## §S — Değişiklik günlüğü (bu dosya)

| Tarih | Tur | Not |
|---|---|---|
| 2026-10-15 | Yeni dosya (bolge 6) | Ilk yazim — 5 yeni dosyadan 3.'su |
| 2026-10-15 | Repo taramasi | 4313 PHP · 4 negatif + 5 pozitif desen (§B) |
| 2026-10-15 | Okuma | 4 aralik — 94 satir (§C) + os/01 tam (119) |
| 2026-10-15 | Ornek uretimi | 5 ornek, 2 etiketli SARTNAME (§D.6) |
| 2026-10-15 | Duzeltme kaydi | group enum (`ornekler` → `kod`) — indeks DEGISTIRILMEDI (§A.2) |
| 2026-10-15 | Kapi | `ReadAllLines` ≥505 hedefi — sayi §M.4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
