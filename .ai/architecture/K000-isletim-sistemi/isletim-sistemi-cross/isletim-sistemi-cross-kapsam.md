---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/isletim-sistemi-cross-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross
children: []
depends_on: []
evidence:
  - ".ai/architecture/K000-isletim-sistemi/os-master.md §5"
  - ".ai/architecture/K000-isletim-sistemi/os-bagimliliklar.md §3"
  - ".github/workflows/ci.yml:27"
  - "home.coremusic.net/web.config:14"
validation: "2026-10-14 · §A-§F tam · §A.1 A19-A20 eklendi (OS gruplu örnek seti + OS×yüzey matrisi — ornekler §22-§28) · 3 alt-dal envanteri disk sayımıyla · satır ≥500"
open_questions:
  - "Q4 · hedef platform seti tek noktada tanımlı değil → ⚠️ VERIFICATION REQUIRED"
  - "Q-X2 · cross ↔ turu sınırı onaylanmadı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# isletim-sistemi-cross — Kapsam (Deep-Dal Kök)

Bu dosya, `isletim-sistemi-cross` dalının **kapsam sözleşmesidir**: ne içerir, ne içermez,
üç alt-dalı hangi kanıtlarla beslenir ve dört platform yüzeyi nereye dağıtılmıştır.
Kök `index.md` künyesini tekrar etmez; onu **tamamlar**.

## §A — Kapsam

**ELI15 (2 satır):** Farkı **yutan** kod ile farkı **tarif eden** belge ayrı dosyalardır.
Bu dal yalnızca yutan tarafı ve onun sınırını kaydeder; karşılaştırma `turu`'ya, API `api`'ye aittir.

### §A.1 — Kapsam İÇİNDE

| # | Kapsam maddesi | Somut karşılık (repo/dosya) | Kanıt tipi |
|---|---|---|---|
| A1 | Yol ayracı soyutlaması (`DIRECTORY_SEPARATOR`) | `shared/src/Component/TemplateEngine.php:73` | disk kod |
| A2 | Yol ayracı test içinde de korunuyor mu | `shared/tests/Component/TemplateEngineTest.php:27` | disk kod |
| A3 | Runtime platform tespiti (`php_uname('s')`) | `shared/src/Api/Bff/DesktopBff.php:35` | disk kod |
| A4 | Dosya çözümleme ve symlink denetimi | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185` | disk kod |
| A5 | Kök yol gerçekleme (`realpath`) ile eşitleme | `media.coremusic.net/bin/ingest.php:104` | disk kod |
| A6 | Çoklu web sunucusu yapılandırması (Apache) | 4 alt-alanda `.htaccess` | disk config |
| A7 | Çoklu web sunucusu yapılandırması (IIS) | 4 alt-alanda `web.config` | disk config |
| A8 | CI çalışma ortağı seçimi (`ubuntu-latest`) | `.github/workflows/ci.yml:27` | disk CI |
| A9 | CI platform bağımlı cache anahtarı | `.github/workflows/ci.yml:44` | disk CI |
| A10 | Test matrisi tek OS üzerinde mi | `.github/workflows/ci.yml:71` (`strategy.matrix`) | disk CI |
| A11 | Uzantı sözleşmesi taşınabilirliği (`ext-*`) | `shared/composer.json:9` | disk manifest |
| A12 | PHP sürüm sözleşmesi (`>=8.4`) | `shared/composer.json:8` | disk manifest |
| A13 | POSIX taşınabilirlik standardı referansı | kök `index.md` §4 · POSIX.1-2024 | web (2026-10-12) |
| A14 | Kütüphane sargı katmanı (glibc) referansı | kök `index.md` §4 · glibc System-Calls | web (2026-10-12) |
| A15 | Platform bağımlılık envanteri (P1-P6) | `../os-bagimliliklar.md` §3 | vault |
| A16 | Platform bağımlılığı kırılma noktası | `../os-master.md` §5 | vault |
| A17 | Yerel hedef makine (Raspberry Pi 5 — Linux/ARM) | `home.coremusic.net/AGENTS.md` §1 | vault (repo içi) |
| A18 | 3 alt-dalın kendi envanteri ve bağımlılıkları | bu dosya §B · §D | bu tur |
| A19 | OS gruplu örnek seti (Grup L · Grup W · Grup M) | `isletim-sistemi-cross-ornekler.md` §22-§28 | bu tur (2026-10-14) |
| A20 | OS × yüzey matrisi (10 yüzey × 3 OS) | `isletim-sistemi-cross-ornekler.md` §25 | bu tur (2026-10-14) |

### §A.2 — Kapsam DIŞINDA (sınır komşuları → nereye)

| # | Dışında olan | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | OS ailesinin **karşılaştırılması** (ne benzer, ne farklı) | `isletim-sistemi-turu` | kök `index.md` §3 |
| B2 | Platform API'sinin kendisi (syscall vs kütüphane) | `isletim-sistemi-api` | kök `index.md` §3 |
| B3 | OS güvenlik mekanizmaları (capability, seccomp) | `isletim-sistemi-security` | kök `index.md` §3 |
| B4 | CI/CD pipeline'ının tasarımı, deploy | K002-altyapi · `devops` | `../os-bagimliliklar.md` §4 |
| B5 | Ses platform API'leri (ALSA/WASAPI/CoreAudio) | `isletim-sistemi-audio` | kök `index.md` §3 |
| B6 | Uygulamanın iş mantığı (platform-döngüsel) | K001+ | `../os-master.md` §2 |
| B7 | Çekirdek içi davranış (interrupt, scheduler) | `isletim-sistemi-kernel` | kardeş dal |
| B8 | Gözlemlenebilirlik metrikleri/log şeması | `isletim-sistemi-observability` | kardeş dal |
| B9 | Zamanlama/planlama katmanı (scheduling plan) | `isletim-sistemi-plan` | kardeş dal |
| B10 | Çalışma zamanı soyutlaması (runtime lifecycle) | `isletim-sistemi-runtime` | kardeş dal |
| B11 | Test altyapısının kendisi (PHPUnit/Vitest kurgusu) | `isletim-sistemi-testing` | kardeş dal |
| B12 | Elektronik donanım belgeleri (diskte `electroncis/`) | `electroncis` | kardeş dizin (ad korunur) |
| B13 | Tek platform derinliği (yalnız Windows / yalnız Linux) | `isletim-sistemi-turu` | bu dosya §E |
| B14 | Paket biçimlerinin üretimi (build server, signing altyapısı) | `derleme-ve-paketleme` alt-dalı | bu dosya §B |

> **B1 en riskli komşudur.** `cross` = farkı **yutma**; `turu` = farkı **tarif etme**. Bu ayrım
> yazılı bir kararla onaylanmamıştır → §F Q-X2.

### §A.3 — Kapsam karar kuralları (bu dalda dosya nasıl açılır)

| # | Kural | Uygulama | İhlalde |
|---|---|---|---|
| C1 | Dosya yalnız beyazliste 4 hedefe yazılır | kök `*.md` + 3 alt-dal `**/*.md` | DUR · orkestratöre bildir |
| C2 | İçerik dosyası ≥500 tam satır | sayım: `Get-Content` `.Count` | 499 = başarısız sayılır |
| C3 | Alt-dal `index.md` 40-100 satır | aynı sayım | 39/101 = başarısız |
| C4 | Her dosya 9 alanlı frontmatter taşır | `node_id` … `group` | FM kesik = hata |
| C5 | `parent:` alanı YASAK, `parent_id` zorunlu | grep: `^parent:` = 0 | hata |
| C6 | `group` tırnaksız yazılır | `group: mimari` | hata |
| C7 | Gerçek repo kodu her içerik dosyasında ≥1 | `Dosya:` + `Satır:` + doğrulama | `Kaynak:` ile işaretle |
| C8 | URL yalnız resmi kaynak, HTTP 200, 2026-10-13 | `Invoke-WebRequest -Method Head` | 404 = elenir |
| C9 | Envant URL tekrarı yapılmaz (kesişim 0) | envanter ≠ yeni kümeler | kesişim >0 = hata |
| C10 | Uydurma satır numarası yasak | `Select-Object -Index (N-1)` | `⚠️ VERIFICATION REQUIRED` |
| C11 | Eksik bilgi = `UNKNOWN`, tahmin değil | her tablo hücresi | uydurma = hallüsinasyon |
| C12 | `git commit` subagent'ta yok | yalnız beyazliste yazımı | commit orkestratöre |

### §A.4 — Kabul kriterleri (bu tur için)

| # | Kriter | Hedef | Ölçüm |
|---|---|---|---|
| K1 | Kök dosya sayısı | 4 | `Get-ChildItem -Filter *.md` |
| K2 | Alt-dal klasörü sayısı | 3 | `Get-ChildItem -Directory` |
| K3 | Toplam dosya | 16 | sayım |
| K4 | ≥500 satır dosya | 12 | `.Count -ge 500` |
| K5 | Kök `index.md` satırı | 100 | `.Count` |
| K6 | Alt-dal `index.md` aralığı | 40-100 | `.Count` |
| K7 | Dışarıya yazım | 0 | whitelist diff |
| K8 | Dosya/dizin adı değişikliği | 0 | ad listesi |

### §A.5 — Kanıt sınıfları (bu dalda hangi iddia hangi sınıfla taşınır)

| # | Kanıt sınıfı | Tanım | Örnek | Güç |
|---|---|---|---|---|
| S1 | disk kod | repo `.php`/`.yml`/`.json` satırı, numaralı | `TemplateEngine.php:73` | en yüksek |
| S2 | disk config | `.htaccess` / `web.config` / `phpunit.xml` | `web.config:14` | yüksek |
| S3 | disk CI | `.github/workflows/*.yml` satırı | `ci.yml:27` | yüksek |
| S4 | vault | `.ai/architecture/**` içindeki kayıtlı karar | `os-master.md §5` | orta (kendi turunu doğrulamaz) |
| S5 | web (resmi) | POSIX/MS/Apple/GNU/man7/GitHub docs, HTTP 200 | POSIX fonksiyon listesi | orta-yüksek |
| S6 | repo-içi belge | `home.coremusic.net/AGENTS.md §1` | Raspberry Pi 5 ifadesi | orta (niyet) |
| S7 | UNKNOWN | hiçbir sınıf yok | üretim OS | kanıt sayılmaz |
| S8 | VERIFICATION | iddia var, sınıf eksik | `cross` ↔ `turu` sınırı | kanıt sayılmaz |

### §A.6 — Yüzey bazlı kapsama olgunluğu

| # | Yüzey | Kapsam içinde mi | Kanıt sınıfı | Açık kalan |
|---|---|---|---|---|
| 1 | Yol ayracı (ayraç) | ✅ | S1 | — |
| 2 | Yol ayracı (case) | ✅ | S1 + S5 | — |
| 3 | Yol ayracı (UNC/uzun yol) | ✅ | S5 | repo'da örnek yok → UNKNOWN |
| 4 | Yol ayracı (symlink/NTFS) | ✅ | S1 | Windows davranışı doğrulanmadı |
| 5 | Platform adı tespiti | ✅ | S1 | yalnız `php_uname` — tek yol |
| 6 | Web sunucusu Apache | ✅ | S2 | — |
| 7 | Web sunucusu IIS | ✅ | S2 | runtime testi yok (Q-X7) |
| 8 | CI çalışma ortağı | ✅ | S3 | tek runner OS (Q-X3) |
| 9 | Derleme yüzeyi | ✅ (alt-dal 2) | S1 + S5 | repo'da native derleme yok |
| 10 | Paketleme/dağıtım | ✅ (alt-dal 2) | S5 | imzalama politikası yok (Q-X6) |
| 11 | Taşınabilirlik seviyesi | ✅ (alt-dal 3) | S4 + S6 | seviye tanımı yok → Q4 |
| 12 | Üretim platformu | ❌ | S7 | Q4 açık |

## §B — 3 alt-dal envanteri

### §B.1 — Genel envanter (2026-10-13 ölçümü)

| # | Alt-dal | Dosya | İçerik dosyası | node_type | group (dosyalar) |
|---|---|---|---|---|---|
| 0 | kök | `index.md` | — | subdomain | mimari |
| 0a | kök | `isletim-sistemi-cross-kapsam.md` | bu dosya | module | mimari |
| 0b | kök | `isletim-sistemi-cross-dogrulama.md` | kanıt matrisi | module | kanit |
| 0c | kök | `isletim-sistemi-cross-ornekler.md` | gerçek repo kodu | module | kod |
| 1 | `platform-yuzeyleri/` | `index.md` | — | subdomain | mimari |
| 1a | `platform-yuzeyleri/` | `platform-yuzeyleri-anayasa.md` | §0-§9 anayasa | module | mimari |
| 1b | `platform-yuzeyleri/` | `sistem-cagrisi-yuzey-farklari.md` | POSIX/Win32/Darwin | module | mimari |
| 1c | `platform-yuzeyleri/` | `dosya-ve-yol-yuzey-farklari.md` | `/` `\` · case · UNC | module | mimari |
| 2 | `derleme-ve-paketleme/` | `index.md` | — | subdomain | mimari |
| 2a | `derleme-ve-paketleme/` | `derleme-ve-paketleme-anayasa.md` | §0-§9 anayasa | module | mimari |
| 2b | `derleme-ve-paketleme/` | `paket-bicimleri-ve-dagitim.md` | deb/rpm/msi/pkg/apk | module | mimari |
| 2c | `derleme-ve-paketleme/` | `derleyici-ve-baglayici-farklari.md` | GCC/Clang/MSVC · ABI | module | mimari |
| 3 | `tasinabilirlik-siniri/` | `index.md` | — | subdomain | mimari |
| 3a | `tasinabilirlik-siniri/` | `tasinabilirlik-siniri-anayasa.md` | §0-§9 anayasa | module | mimari |
| 3b | `tasinabilirlik-siniri/` | `repo-platform-bagimsizlik-kanitlari.md` | `Dosya:`/`Satır:` | module | mimari |
| 3c | `tasinabilirlik-siniri/` | `ci-ve-platform-matrisi.md` | workflow/runner matrisi | module | mimari |

### §B.2 — Alt-dal 1 · `platform-yuzeyleri/` — beslenme kaynağı

| Dosya | Ne taşır | Birincil kanıt | Komşu |
|---|---|---|---|
| `index.md` | 4 dosyalık katalog, 40-100 satır | sayım | kök `index.md` §1 |
| `platform-yuzeyleri-anayasa.md` | §0 ELI15 … §9 açık sorular | `os-master.md` §5 | `isletim-sistemi-api` |
| `sistem-cagrisi-yuzey-farklari.md` | POSIX vs Win32 vs Darwin eşlemesi | POSIX fonksiyon listesi (2026-10-13) | `isletim-sistemi-api` |
| `dosya-ve-yol-yuzey-farklari.md` | ayraç · case · UNC · symlink | `TemplateEngine.php:73` · `MusicStreamHandler.php:199` | `isletim-sistemi-security` |

### §B.3 — Alt-dal 2 · `derleme-ve-paketleme/` — beslenme kaynağı

| Dosya | Ne taşır | Birincil kanıt | Komşu |
|---|---|---|---|
| `index.md` | 4 dosyalık katalog, 40-100 satır | sayım | kök `index.md` §1 |
| `derleme-ve-paketleme-anayasa.md` | derleyici/bağlayıcı yüzeyi, uzantı bağımlılığı | `shared/composer.json:8` | `K002-altyapi` |
| `paket-bicimleri-ve-dagitim.md` | deb/rpm/msi/pkg/apk + depo + geri alma | npm/packagist manifestleri | `devops` |
| `derleyici-ve-baglayici-farklari.md` | GCC/Clang/MSVC · sembol/ABI · bayrak | `ci.yml:33` (`setup-php`) | `isletim-sistemi-runtime` |

### §B.4 — Alt-dal 3 · `tasinabilirlik-siniri/` — beslenme kaynağı

| Dosya | Ne taşır | Birincil kanıt | Komşu |
|---|---|---|---|
| `index.md` | 4 dosyalık katalog, 40-100 satır | sayım | kök `index.md` §1 |
| `tasinabilirlik-siniri-anayasa.md` | taşınabilirlik seviyeleri, bilinen/UNKNOWN | `../index.md` §2 | `isletim-sistemi-turu` |
| `repo-platform-bagimsizlik-kanitlari.md` | §5 kanıtlarının derinleşmesi | `ci.yml:44` · `web.config:14` | `isletim-sistemi-testing` |
| `ci-ve-platform-matrisi.md` | workflow × runner matrisi, Q4 | `ci.yml:27` · `secret-scan.yml:21` | `devops` · K002 |

### §B.5 — Bu turun satır ölçümü (hedef ↔ gerçekleşen)

| Dosya | Öncesi | Hedef | Not |
|---|---|---|---|
| kök `index.md` | 115 | 100 | `children` açıldı, §1 dağılım notu |
| `*-kapsam.md` | 97 | ≥500 | §A-§F |
| `*-dogrulama.md` | 78 | ≥500 | §A-§F |
| `*-ornekler.md` | 133 | ≥500 | §A-§D |
| 3 × alt-dal `index.md` | dosya yok | 40-100 | bu turda AÇILDI |
| 9 × alt-dal içerik | dosya yok | ≥500 | bu turda AÇILDI |

### §B.6 — 16 dosyanın bölüm iskeleti (§ nereye yazılır)

| Dosya | §0 | §1 | §2 | §3 | §4 | §5 | §6 | §7 | §8 | §9 |
|---|---|---|---|---|---|---|---|---|---|---|
| kök `index.md` | — | 4 yüzey | bilinen/UNKNOWN | sınır komşuları | kaynaklar | repo kanıtları | açık sorular | — | — | — |
| `*-kapsam.md` | — | §A kapsam | §B envanter | §C dağılım | §D bağımlılık | §E komşu | §F sorular | — | — | — |
| `*-dogrulama.md` | — | §A senaryo | §B test stratejisi | §C ölçüm | §D eşik | §E web matris | §F disk kanıt | — | — | — |
| `*-ornekler.md` | — | §A yapılandırma | §B repo kodu | §C kötü uygulama | §D platform matrisi | — | — | — | — | — |
| 1 `index.md` | — | katalog | 4 dosya özeti | okuma sırası | — | — | — | — | — | — |
| 1 `*-anayasa.md` | ELI15 | kimlik | 4 yüzey | yüzey riski | platform farkı maliyeti | ayırma desenleri | komşu sınırı | terimler | kapsam dışı | açık sorular |
| 1 `sistem-cagrisi…` | ELI15 | POSIX/Win32/Darwin | çağrı eşlemesi | taşınabilir sarmalama | kaçış noktaları | ölçüm | referanslar | — | — | — |
| 1 `dosya-ve-yol…` | ELI15 | ayraç | case | uzun yol/UNC | symlink & NTFS | taşınabilir sarmalama | referanslar | — | — | — |
| 2 `index.md` | — | katalog | 4 dosya özeti | okuma sırası | — | — | — | — | — | — |
| 2 `*-anayasa.md` | ELI15 | kimlik | derleyici/bağlayıcı yüzeyi | bağımlılık yönetimi | paket biçimi | mimari farkları | komşu sınırı | terimler | kapsam dışı | açık sorular |
| 2 `paket-bicimleri…` | ELI15 | biçim matrisi | depo/registry | sürümleme | imzalama | geri alma | ölçüm | referanslar | — | — |
| 2 `derleyici-ve-baglayici…` | ELI15 | GCC/Clang/MSVC | sembol/ABI | bayrak farkları | sızdırma (IFUNC/PLT) | ölçüm | referanslar | — | — | — |
| 3 `index.md` | — | katalog | 4 dosya özeti | okuma sırası | — | — | — | — | — | — |
| 3 `*-anayasa.md` | ELI15 | kimlik | taşınabilirlik seviyeleri | bilinen/UNKNOWN | POXIS çerçevesi | fark sarma desenleri | komşu sınırı | terimler | kapsam dışı | açık sorular |
| 3 `repo-…-kanitlari` | ELI15 | §5 derinleşmesi | `Dosya:`/`Satır:` | yüzey bazlı | bilinmeyen yüzeyler | ölçüm | referanslar | — | — | — |
| 3 `ci-ve-platform-matrisi` | ELI15 | workflow matrisi | OS runner matrisi | koruma alanları | bilinmeyen (Q4) | ölçüm | referanslar | — | — | — |

### §B.7 — Ölçüm komutları (repo kökünden çalıştırılır)

```powershell
# 1) Dosya ve satır sayımı (16 hedef)
$root = 'C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-cross'
Get-ChildItem $root -Recurse -Filter *.md | ForEach-Object {
  $n = (Get-Content $_.FullName).Count
  [PSCustomObject]@{ Dosya = $_.Name; Satir = $n; Gev500 = ($n -ge 500) }
} | Sort-Object Satir -Descending

# 2) Frontmatter kesik kontrolü (ilk satır --- olmalı, 17. satır olmamalı)
Get-ChildItem $root -Recurse -Filter *.md | ForEach-Object {
  $l = Get-Content $_.FullName
  if ($l[0] -ne '---') { 'FM KESIK: ' + $_.Name }
  if (($l | Where-Object { $_ -eq '---' }).Count -lt 2) { 'FM KAPANIS YOK: ' + $_.Name }
}

# 3) parent: (yasak) ve tırnaklı group denetimi
Select-String -Path "$root\*.md","$root\*\*.md" -Pattern '^parent:'   # 0 olmalı
Select-String -Path "$root\*.md","$root\*\*.md" -Pattern '^group: "'  # 0 olmalı

# 4) Mojibake ve yasak doldurucu taraması
$bad = 'TBD|Yakında|// \.\.\.|\{\.\.\.\}'
Get-ChildItem $root -Recurse -Filter *.md | Select-String -Pattern $bad  # 0 olmalı
```

| # | Komut | Beklenen | Kırılırsa |
|---|---|---|---|
| M1 | dosya sayısı | 16 | beyazliste dışı var |
| M2 | `gev500` | 12 `True` | içerik dosyası eksik |
| M3 | kök `index.md` | 100 | A1 hedefi kaymış |
| M4 | `^parent:` | 0 satır | FM kuralı ihlali |
| M5 | `^group: "` | 0 satır | tırnaklı group |
| M6 | FM kesik | 0 | FM hatalı |
| M7 | `$bad` taraması | 0 | dolgu/uydurma |

### §B.8 — Alt-dal dosyalarının satır hedefleri

| Dosya | Öncesi | Hedef | Durum (bu tur) |
|---|---|---|---|
| 1 `index.md` | yok | 40-100 | AÇILDI |
| 1 `platform-yuzeyleri-anayasa.md` | yok | ≥500 | AÇILDI |
| 1 `sistem-cagrisi-yuzey-farklari.md` | yok | ≥500 | AÇILDI |
| 1 `dosya-ve-yol-yuzey-farklari.md` | yok | ≥500 | AÇILDI |
| 2 `index.md` | yok | 40-100 | AÇILDI |
| 2 `derleme-ve-paketleme-anayasa.md` | yok | ≥500 | AÇILDI |
| 2 `paket-bicimleri-ve-dagitim.md` | yok | ≥500 | AÇILDI |
| 2 `derleyici-ve-baglayici-farklari.md` | yok | ≥500 | AÇILDI |
| 3 `index.md` | yok | 40-100 | AÇILDI |
| 3 `tasinabilirlik-siniri-anayasa.md` | yok | ≥500 | AÇILDI |
| 3 `repo-platform-bagimsizlik-kanitlari.md` | yok | ≥500 | AÇILDI |
| 3 `ci-ve-platform-matrisi.md` | yok | ≥500 | AÇILDI |
| kök `index.md` | 115 | 100 | DÜŞÜLDÜ |
| kök `*-kapsam.md` | 97 | ≥500 | GENİŞLETİLDİ |
| kök `*-dogrulama.md` | 78 | ≥500 | GENİŞLETİLDİ |
| kök `*-ornekler.md` | 133 | ≥500 | GENİŞLETİLDİ |

## §C — 4 yüzeyin dağılım haritası

### §C.1 — Yüzey → alt-dal

| # | Yüzey (kök `index.md` §1) | Ana fark | Gittiği alt-dal | Gittiği dosya |
|---|---|---|---|---|
| 1 | Yol ayracı | `/` vs `\` | `platform-yuzeyleri` | `dosya-ve-yol-yuzey-farklari.md` |
| 2 | Platform adı | Linux/Windows/Darwin | `platform-yuzeyleri` | `platform-yuzeyleri-anayasa.md` §2 |
| 3 | Web sunucusu | Apache vs IIS | `platform-yuzeyleri` | `platform-yuzeyleri-anayasa.md` §2 |
| 4 | Çalışma ortağı | Linux vs Windows | `tasinabilirlik-siniri` | `ci-ve-platform-matrisi.md` |

### §C.2 — Yüzey → alt yüzey ayrıştırması

| Yüzey | Alt yüzey | Ayrıştığı yer | Durum |
|---|---|---|---|
| 1 Yol ayracı | ayraç karakteri (`/` `\`) | `dosya-ve-yol-yuzey-farklari.md` §1 | disk kanıtlı |
| 1 Yol ayracı | büyük/küçük harf duyarlılığı | `dosya-ve-yol-yuzey-farklari.md` §2 | disk kanıtlı (case-sensitivity URL) |
| 1 Yol ayracı | uzun yol / UNC | `dosya-ve-yol-yuzey-farklari.md` §3 | web kanıtlı |
| 1 Yol ayracı | symlink & NTFS | `dosya-ve-yol-yuzey-farklari.md` §4 | disk kanıtlı (`is_link`) |
| 1 Yol ayracı | taşınabilir sarmalama | `dosya-ve-yol-yuzey-farklari.md` §5 | disk kanıtlı (`rtrim`) |
| 2 Platform adı | runtime tespit | `platform-yuzeyleri-anayasa.md` §2 | disk kanıtlı (`php_uname`) |
| 2 Platform adı | süreç oluşturma farkı | `sistem-cagrisi-yuzey-farklari.md` §1 | web kanıtlı |
| 2 Platform adı | hata kodu farkı | `sistem-cagrisi-yuzey-farklari.md` §1 | web kanıtlı |
| 3 Web sunucusu | Apache rewrite | `platform-yuzeyleri-anayasa.md` §2 | disk kanıtlı (`.htaccess`) |
| 3 Web sunucusu | IIS rewrite | `platform-yuzeyleri-anayasa.md` §2 | disk kanıtlı (`web.config:14`) |
| 4 Çalışma ortağı | CI runner OS | `ci-ve-platform-matrisi.md` §1 | disk kanıtlı (`ci.yml:27`) |
| 4 Çalışma ortağı | runner OS cache anahtarı | `ci-ve-platform-matrisi.md` §3 | disk kanıtlı (`ci.yml:44`) |

### §C.3 — Harita takip tablosu (hangi bölüm hangi dosyada)

| Bölüm | Kök | Alt-dal 1 | Alt-dal 2 | Alt-dal 3 |
|---|---|---|---|---|
| ELI15 | `index.md` | `*-anayasa.md` §0 | `*-anayasa.md` §0 | `*-anayasa.md` §0 |
| Kimlik | `index.md` künye | `*-anayasa.md` §1 | `*-anayasa.md` §1 | `*-anayasa.md` §1 |
| 4 yüzey | `index.md` §1 | `*-anayasa.md` §2 | — | — |
| Derleme yüzeyi | — | — | `*-anayasa.md` §2-§3 | — |
| Taşınabilirlik | — | — | — | `*-anayasa.md` §2-§4 |
| Risk | `index.md` §6 | `*-anayasa.md` §3-§4 | `*-anayasa.md` §4-§6 | `*-anayasa.md` §3-§6 |
| Ölçüm | `*-dogrulama.md` §C-§D | konu dosyaları §5/§6 | konu dosyaları §5/§7 | konu dosyaları §5/§6 |
| Açık soru | `*-kapsam.md` §F | `*-anayasa.md` §9 | `*-anayasa.md` §9 | `*-anayasa.md` §9 |

### §C.4 — Yüzey × alt-dal sorumluluk matrisi

| Yüzey ↓ / Alt-dal → | `platform-yuzeyleri` | `derleme-ve-paketleme` | `tasinabilirlik-siniri` |
|---|---|---|---|
| 1 Yol ayracı | **sahip** (`dosya-ve-yol…`) | — | kanıt taşır |
| 2 Platform adı | **sahip** (`…-anayasa` §2) | — | kanıt taşır |
| 3 Web sunucusu | **sahip** (§2) | — | kanıt taşır |
| 4 Çalışma ortağı | — | — | **sahip** (`ci-ve-platform-matrisi`) |
| 5 Derleme yüzeyi | — | **sahip** (`…-anayasa` §2-§3) | — |
| 6 Paket biçimi | — | **sahip** (`paket-bicimleri…`) | — |
| 7 Mimari (CPU) farkı | — | **sahip** (§5) | etkilenir |
| 8 Taşınabilirlik seviyesi | girdi verir | girdi verir | **sahip** (anayasa §2) |
| 9 Bilinen/UNKNOWN | — | — | **sahip** (anayasa §3) |
| 10 Repo kanıtları | kanıt verir | kanıt verir | **sahip** (`repo-…-kanitlari`) |

> **Çakışma kuralı:** Bir satırda tek `**sahip**` olur. İki alt-dal aynı satırda sahipse →
> orkestratöre bildirim ve kök `*-kapsam.md` §F'e soru satırı açılır.

## §D — Bağımlılık grafiği

### §D.1 — ASCII grafik (okuma yönü: yukarı → aşağı)

```text
K000-isletim-sistemi
└── isletim-sistemi-cross          (kök index.md · children: 3)
    ├── platform-yuzeyleri/        (4 dosya)
    │   ├── index.md
    │   ├── platform-yuzeyleri-anayasa.md
    │   ├── sistem-cagrisi-yuzey-farklari.md
    │   └── dosya-ve-yol-yuzey-farklari.md
    ├── derleme-ve-paketleme/      (4 dosya)
    │   ├── index.md
    │   ├── derleme-ve-paketleme-anayasa.md
    │   ├── paket-bicimleri-ve-dagitim.md
    │   └── derleyici-ve-baglayici-farklari.md
    └── tasinabilirlik-siniri/     (4 dosya)
        ├── index.md
        ├── tasinabilirlik-siniri-anayasa.md
        ├── repo-platform-bagimsizlik-kanitlari.md
        └── ci-ve-platform-matrisi.md
```

### §D.2 — Dosya bağımlılık tablosu

| # | Dosya | depends_on (iç) | depends_on (dış) | Kim ona güvenir |
|---|---|---|---|---|
| 1 | kök `index.md` | 3 alt-dal `index.md` | `../os-master.md` §5 | her alt-dal |
| 2 | `*-kapsam.md` | kök `index.md` | `../os-bagimliliklar.md` §3 | `*-dogrulama.md` |
| 3 | `*-dogrulama.md` | `*-kapsam.md` · `*-ornekler.md` | 4 kök eski URL + yeni URL | kök `index.md` §4 |
| 4 | `*-ornekler.md` | `*-kapsam.md` | repo kod satırları | `*-dogrulama.md` §F |
| 5 | `platform-yuzeyleri/index.md` | 3 kardeş dosya | kök `index.md` §1 | okuyucu girişi |
| 6 | `platform-yuzeyleri-anayasa.md` | sibling 2 dosya | `../os-master.md` §5 | iki konu dosyası |
| 7 | `sistem-cagrisi-yuzey-farklari.md` | anayasa §2 | POSIX/MS/man7 URL (2026-10-13) | `isletim-sistemi-api` |
| 8 | `dosya-ve-yol-yuzey-farklari.md` | anayasa §2 | repo `TemplateEngine`/`MusicStreamHandler` | `isletim-sistemi-security` |
| 9 | `derleme-ve-paketleme/index.md` | 3 kardeş dosya | kök `index.md` dağılım notu | okuyucu girişi |
| 10 | `derleme-ve-paketleme-anayasa.md` | sibling 2 dosya | `shared/composer.json` · `package.json` | iki konu dosyası |
| 11 | `paket-bicimleri-ve-dagitim.md` | anayasa §4 | depo/registry URL (2026-10-13) | `devops` |
| 12 | `derleyici-ve-baglayici-farklari.md` | anayasa §2-§3 | GCC/Clang/MSVC URL (2026-10-13) | `isletim-sistemi-runtime` |
| 13 | `tasinabilirlik-siniri/index.md` | 3 kardeş dosya | kök `index.md` §1/§2 | okuyucu girişi |
| 14 | `tasinabilirlik-siniri-anayasa.md` | sibling 2 dosya | kök `index.md` §2 (bilinen/UNKNOWN) | iki konu dosyası |
| 15 | `repo-platform-bagimsizlik-kanitlari.md` | anayasa §3 | kök `index.md` §5 (P1-P7) | `*-dogrulama.md` §F |
| 16 | `ci-ve-platform-matrisi.md` | anayasa §4 | `.github/workflows/*.yml` + GitHub URL | `devops` · K002-altyapi |

### §D.3 — Güncelleme tetikleyicileri

| Tetikleyici | Güncellenmesi gereken | Neden |
|---|---|---|
| `ci.yml` runner değişimi | `ci-ve-platform-matrisi.md` §1-§2 | matris satırı eskir |
| Yeni `.htaccess`/`web.config` | `platform-yuzeyleri-anayasa.md` §2 | yüzey sayısı artar |
| `composer.json` uzantı değişimi | `derleme-ve-paketleme-anayasa.md` §3 | bağımlılık sözleşmesi |
| Q4 kapanışı (üretim OS) | kök `index.md` §2 · `*-kapsam.md` §F | UNKNOWN → bilinen |
| `turu` ile sınır kararı | `*-kapsam.md` §A.2 B1 · §F Q-X2 | sınır kayması |
| Repo'ya yeni workflow | `ci-ve-platform-matrisi.md` §1 | envanter büyür |

### §D.4 — Ters bağımlılık (hangi dosya kimi okur)

| # | Okuyan | Okunan | Gerekçe |
|---|---|---|---|
| R1 | `platform-yuzeyleri/*` | kök `index.md` §1 | 4 yüzeyin tanımı |
| R2 | `derleme-ve-paketleme/*` | kök `index.md` dağılım notu | boşluk gerekçesi |
| R3 | `tasinabilirlik-siniri/*` | kök `index.md` §2 | bilinen/UNKNOWN |
| R4 | `*-dogrulama.md` | `*-kapsam.md` §A | kapsam içi iddialar |
| R5 | `*-dogrulama.md` | `*-ornekler.md` §B | disk kod kanıtı |
| R6 | `repo-platform-bagimsizlik-kanitlari.md` | kök `index.md` §5 | P1-P7 listesi |
| R7 | `ci-ve-platform-matrisi.md` | `.github/workflows/ci.yml` | runner/matris satırları |
| R8 | `ci-ve-platform-matrisi.md` | `.github/workflows/secret-scan.yml` | ikinci workflow |
| R9 | `dosya-ve-yol-yuzey-farklari.md` | `TemplateEngine.php:73` | ayraç kanıtı |
| R10 | `dosya-ve-yol-yuzey-farklari.md` | `MusicStreamHandler.php:199` | prefix denetimi |
| R11 | `derleme-ve-paketleme-anayasa.md` | `shared/composer.json:8` | `php >=8.4` |
| R12 | `derleme-ve-paketleme-anayasa.md` | `package.json:5` | npm scriptleri |
| R13 | `paket-bicimleri-ve-dagitim.md` | `shared/composer.json:13` | bağımlılık sürüm aralıkları |
| R14 | `sistem-cagrisi-yuzey-farklari.md` | `DesktopBff.php:35` | platform tespiti |
| R15 | tüm alt-dallar | `../os-master.md` §5 | master sınır referansı |
| R16 | kök `index.md` | 3 alt-dal `index.md` | çocuk kataloğu |

## §E — Sınır komşuları

### §E.1 — Komşu + ne paylaşılıyor

| # | Komşu | Ne ortak | Ne asla paylaşılmaz | Risk |
|---|---|---|---|---|
| E1 | `isletim-sistemi-turu` | platform farkının **varlığı** | farkın **karşılaştırılması** (`turu` tekelinde) | yüksek (Q-X2) |
| E2 | `isletim-sistemi-api` | sistem çağrısı **farkı** | çağrının **imzası/proto­tipi** (`api` tekelinde) | orta |
| E3 | `isletim-sistemi-security` | dosya yolu denetimi örneği | politika/kural tanımı | orta |
| E4 | `isletim-sistemi-audio` | platform ses API adı geçişi | ALSA/WASAPI detayı | düşük |
| E5 | `isletim-sistemi-kernel` | syscall kavramı | çekirdek içi davranış | düşük |
| E6 | `isletim-sistemi-observability` | CI log/ölçüm terminolojisi | metrik şeması | düşük |
| E7 | `isletim-sistemi-plan` | hedef platform planı | planlama takvimi | düşük |
| E8 | `isletim-sistemi-runtime` | çalışma zamanı soyutlaması | lifecycle sahipliği | orta |
| E9 | `isletim-sistemi-testing` | test **platformu** | test **altyapısı/kurgusu** | yüksek |
| E10 | `K002-altyapi` · `devops` | workflow envanteri | pipeline tasarımı, deploy | yüksek |
| E11 | `electroncis` (ad korunur) | — (ayrı alan) | her şey | yok |
| E12 | `../index.md` · `os-*.md` | master §5 bağımlılık tablosu | master metni düzenleme | orta |

### §E.2 — Çakışma denetimi (bu tur · beyazliste)

| # | Denetim | Sonuç |
|---|---|---|
| D1 | Beyazliste dışı dosyaya yazım | 0 |
| D2 | Kardeş `isletim-sistemi-*` dizinine yazım | 0 |
| D3 | `K000-isletim-sistemi/index.md` · `os-*.md` değişimi | 0 |
| D4 | `K001..K019` yazımı | 0 |
| D5 | `.ai/architecture/index.md` yazımı | 0 |
| D6 | `*.php` `*.js` `*.css` `*.sql` yazımı | 0 |
| D7 | Dosya adı / dizin adı değişikliği | 0 |
| D8 | `git commit` | 0 (orkestratöre ait) |

### §E.3 — Beyazliste (tek geçerli yazım yüzeyi)

| # | Kalıp | Kapsam |
|---|---|---|
| W1 | `isletim-sistemi-cross/*.md` | kök 4 dosya (tek yıldız) |
| W2 | `isletim-sistemi-cross/platform-yuzeyleri/**/*.md` | 4 dosya |
| W3 | `isletim-sistemi-cross/derleme-ve-paketleme/**/*.md` | 4 dosya |
| W4 | `isletim-sistemi-cross/tasinabilirlik-siniri/**/*.md` | 4 dosya |
| W5 | Toplam | 16 dosya · 3 yeni dizin |

### §E.4 — Bu turda YASAK yüzeyler (diğer paralel ajanlar dahil)

| # | Yasak yüzey | Neden | Doğrulama |
|---|---|---|---|
| Y1 | `isletim-sistemi-{core,kernel,observability,plan,runtime,security,testing}/**` | kardeş dal mülkiyeti | `Get-ChildItem` + git status |
| Y2 | `isletim-sistemi-audio/**` | kardeş dal mülkiyeti | aynı |
| Y3 | `isletim-sistemi-api/**` | kardeş dal mülkiyeti | aynı |
| Y4 | `isletim-sistemi-turu/**` | kardeş dal mülkiyeti | aynı |
| Y5 | `electroncis/**` | ad ayrı alan (yazım korunur) | aynı |
| Y6 | `K000-isletim-sistemi/index.md` · `os-*.md` | master yüzeyi | aynı |
| Y7 | `K001..K019/**` | diğer konu aileleri | aynı |
| Y8 | `.ai/architecture/index.md` | vault katalog | aynı |
| Y9 | `.ai/electronic/**` · `.ai/.decisions/**` | salt-okunur alanlar | aynı |
| Y10 | `*.php` `*.js` `*.css` `*.sql` | kaynak kod | `git status --porcelain` |

## §F — Açık sorular (Q4 ailesi)

| # | Soru | Etiket | Sahip | Etkilenen |
|---|---|---|---|---|
| Q4 | Hedef platform seti tek noktada tanımlı mı? | ⚠️ VERIFICATION REQUIRED | Vault Steward | kök `index.md` §2 · `ci-ve-platform-matrisi.md` §4 |
| Q-X1 | Q4 tek noktası var mı (yoksa hangi dosya SSOT olacak)? | ⚠️ VERIFICATION REQUIRED | Vault Steward | `*-kapsam.md` §F |
| Q-X2 | `cross` ↔ `turu` sınırı onaylandı mı? | ⚠️ VERIFICATION REQUIRED | Vault Steward | §A.2 B1 · `turu` dalı |
| Q-X3 | macOS/Windows üzerinde çalıştırma testi var mı? | UNKNOWN | Vault Steward | `ci-ve-platform-matrisi.md` §2 |
| Q-X4 | Raspberry Pi 5 üretim hedefi mi, geliştirme ortamı mı? | UNKNOWN | Vault Steward | kök `index.md` §2 |
| Q-X5 | K002-altyapi ile sınır (Q5) tanımlı mı? | ⚠️ VERIFICATION REQUIRED | registry §6 | §E.1 E10 |
| Q-X6 | Paket imzalama (code signing) politikası var mı? | UNKNOWN | DevOps | `paket-bicimleri-ve-dagitim.md` §4 |
| Q-X7 | Windows üzerinde `web.config` runtime test ediliyor mu? | UNKNOWN | QA | `repo-platform-bagimsizlik-kanitlari.md` §4 |
| Q-X8 | Üretimde Apache mı IIS mı kullanılıyor? | UNKNOWN | DevOps | `platform-yuzeyleri-anayasa.md` §9 |
| Q-X9 | ARM64 (Raspberry Pi) için derleme/paket adımı var mı? | UNKNOWN | Embedded | `derleme-ve-paketleme-anayasa.md` §5 |
| Q-X10 | `cross` içinde `php_uname` dışındaki tespit yolları kabul ediliyor mu? | ⚠️ VERIFICATION REQUIRED | Backend | `sistem-cagrisi-yuzey-farklari.md` §3 |

### §F.1 — Etiket sözlüğü

| Etiket | Anlam | Davranış |
|---|---|---|
| ✅ doğrulandı | disk kanıtı + satır numarası okundu | kullanılabilir |
| ⚠️ VERIFICATION REQUIRED | iddia doğru ama kanıt yetersiz | kullanılmaz, işaretli kalır |
| UNKNOWN | bilgi repo'da yok | tahmin edilmez |
| `[TASARIM]` | henüz karar alınmamış | karar orkestratör/üst |
| `Q4` | hedef platform seti sorusu | kök `index.md` §6 |

### §F.2 — Kapanış planı (her soru için hangi kanıt kapatır)

| # | Soru | Kapatmak için gereken kanıt | Kanıt sınıfı | Bloke ettiği dosya |
|---|---|---|---|---|
| Z1 | Q4 | tek SSOT dosyasında hedef platform seti kararı | S4 (vault kararı) | kök `index.md` §2 |
| Z2 | Q-X1 | SSOT dosya adının yazılması + link | S4 | `*-kapsam.md` §F |
| Z3 | Q-X2 | `cross` ↔ `turu` sınır kararı (ADR/registro satırı) | S4 | §A.2 B1 |
| Z4 | Q-X3 | `windows-latest` / `macos-latest` runner'lı workflow | S3 | `ci-ve-platform-matrisi.md` §2 |
| Z5 | Q-X4 | Raspberry Pi 5 rol ifadesinin teyidi (üretim/geliştirme) | S6 → S4 | kök `index.md` §2 |
| Z6 | Q-X5 | K002-altyapi sınır kaydı | S4 | §E.1 E10 |
| Z7 | Q-X6 | code signing politikası belgesi | S4 + S5 | `paket-bicimleri-ve-dagitim.md` §4 |
| Z8 | Q-X7 | IIS üzerinde smoke testi | S3 + S1 | `repo-platform-bagimsizlik-kanitlari.md` §4 |
| Z9 | Q-X8 | üretim web sunucusu envanteri | S6 | `platform-yuzeyleri-anayasa.md` §9 |
| Z10 | Q-X9 | ARM64 build adımı (repo'da native derleme yok) | S1 | `derleme-ve-paketleme-anayasa.md` §5 |
| Z11 | Q-X10 | `php_uname` dışı tespit yollarının kararı | S4 | `sistem-cagrisi-yuzey-farklari.md` §3 |

> **Kapanış kuralı:** Kanıt sınıfı S7/S8 olan hiçbir satır `verified` yapılmaz; etiket
> korunur ve sonraki tura devredilir. `status: draft` bu nedenle bu turda `verified` yapılmaz.

---

## §G - Alt-dal açılış kaydı (2026-10-13)

> Bu bölüm, kök `index.md` §GRUPLAR'ında ilan edilen 3 alt-dalın bu turda nasıl
> açıldığını kayıt altına alır. Ölçüm yöntemi: ham satır = `Get-Content <dosya>.Count`
> (boş satırlar dâhil). İçerik dosyaları ≥500 ham satır, alt-dal `index.md` dosyaları
> 40-100 ham satır hedefiyle üretilir. Bu bölüm mevcut §A-§F içeriğinin ÜZERİNE
> eklenmiştir; §A-§F satırları değiştirilmemiştir.

### §G.1 - Açılan 12 alt-dal dosyası (beyazliste: `isletim-sistemi-cross/**`)

| # | Alt-dal | Dosya (göreli yol) | node_type | group |
|---|---|---|---|---|
| 1 | platform-yuzeyleri | `platform-yuzeyleri/index.md` | subdomain | mimari |
| 2 | platform-yuzeyleri | `platform-yuzeyleri/platform-yuzeyleri-anayasa.md` | module | mimari |
| 3 | platform-yuzeyleri | `platform-yuzeyleri/platform-yuzeyleri-surucu.md` | module | mimari |
| 4 | platform-yuzeyleri | `platform-yuzeyleri/platform-yuzeyleri-ornekler.md` | module | kod |
| 5 | derleme-ve-paketleme | `derleme-ve-paketleme/index.md` | subdomain | mimari |
| 6 | derleme-ve-paketleme | `derleme-ve-paketleme/derleme-ve-paketleme-anayasa.md` | module | mimari |
| 7 | derleme-ve-paketleme | `derleme-ve-paketleme/derleme-ve-paketleme-surucu.md` | module | mimari |
| 8 | derleme-ve-paketleme | `derleme-ve-paketleme/derleme-ve-paketleme-ornekler.md` | module | kod |
| 9 | tasinabilirlik-siniri | `tasinabilirlik-siniri/index.md` | subdomain | mimari |
| 10 | tasinabilirlik-siniri | `tasinabilirlik-siniri/tasinabilirlik-siniri-anayasa.md` | module | mimari |
| 11 | tasinabilirlik-siniri | `tasinabilirlik-siniri/tasinabilirlik-siniri-surucu.md` | module | mimari |
| 12 | tasinabilirlik-siniri | `tasinabilirlik-siniri/tasinabilirlik-siniri-ornekler.md` | module | kod |

### §G.2 - Frontmatter şeması (v2 §4.2 8 alan + 9. alan `group`)

| Alan | Kök `index.md` | Alt-dal `index.md` | İçerik dosyası (module) |
|---|---|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross` | `…/isletim-sistemi-cross/<alt-dal>` | `…/<alt-dal>/<alt-dal>-<tip>` |
| node_type | subdomain | subdomain | module |
| parent_id | `K000-isletim-sistemi` | `K000-isletim-sistemi/isletim-sistemi-cross` | `…/isletim-sistemi-cross/<alt-dal>` |
| children | 3 alt-dal adı | 3 dosya adı | `[]` |
| depends_on / evidence / validation / open_questions | liste veya metin | liste | liste (boşsa `[]`) |
| group | `mimari` | `mimari` | `-anayasa`/`-surucu` → `mimari` · `-ornekler` → `kod` · `-dogrulama` → `kanit` |

> **Yasak alan:** `parent:` (noktasız) kullanılmaz — yalnız `parent_id`. Eski şemada
> `parent:` taşıyan `isletim-sistemi-cross-ornekler.md` bu turda `parent_id` şemasına
> çevrilmiştir. `group` değeri tırnaksız yazılır.

### §G.3 - §B envanter adları ile bu tur açılan adlar arasındaki fark

| §B'deki plan adı | Bu turki durum | Not |
|---|---|---|
| `platform-yuzeyleri-anayasa.md` | açıldı | §G.1 #2 |
| `derleme-ve-paketleme-anayasa.md` | açıldı | §G.1 #6 |
| `tasinabilirlik-siniri-anayasa.md` | açıldı | §G.1 #10 |
| `dosya-ve-yol-yuzey-farklari.md` | açılmadı | bu tur beyazlistesinde değil → Q-X12 |
| `sistem-cagrisi-yuzey-farklari.md` | açılmadı | bu tur beyazlistesinde değil → Q-X12 |
| `derleyici-ve-baglayici-farklari.md` | açılmadı | bu tur beyazlistesinde değil → Q-X12 |
| `paket-bicimleri-ve-dagitim.md` | açılmadı | bu tur beyazlistesinde değil → Q-X12 |
| `ci-ve-platform-matrisi.md` | açılmadı | bu tur beyazlistesinde değil → Q-X12 |
| `repo-platform-bagimsizlik-kanitlari.md` | açılmadı | bu tur beyazlistesinde değil → Q-X12 |
| `*-surucu.md` (3 dosya) | açıldı | §B envanterinde adı geçmiyordu → §G.1 #3 · #7 · #11 |
| `*-ornekler.md` (3 dosya) | açıldı | §B envanterinde adı geçmiyordu → §G.1 #4 · #8 · #12 |

### §G.4 - Web doğrulama eki (2026-10-13 · `Invoke-WebRequest -Method Head` · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Debian ana site — dağıtım/paket yüzeyi resmi kaynağı | https://www.debian.org/ | 2026-10-13 | ✅ HTTP 200 |
| Debian Wiki — paketleme ve platform notları resmi kaynağı | https://wiki.debian.org/ | 2026-10-13 | ✅ HTTP 200 |
| Debian Packages — paket envanteri resmi kaynağı | https://packages.debian.org/ | 2026-10-13 | ✅ HTTP 200 |
| Debian Sürüm Sayfası — hedef sürüm/platform teyidi resmi kaynağı | https://www.debian.org/releases/ | 2026-10-13 | ✅ HTTP 200 |

### §G.5 - Bu tur devreden açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-X12 | §B'deki 6 ad dosya (`dosya-ve-yol-…` · `sistem-cagrisi-…` · `derleyici-ve-baglayici-…` · `paket-bicimleri-…` · `ci-ve-platform-…` · `repo-platform-…`) sonraki turda açılacak mı? | ⚠️ VERIFICATION REQUIRED |
| Q-X1 | Hedef platform seti tek noktada tanımlı mı? (Q4 — §F Z1 devam) | ⚠️ VERIFICATION REQUIRED |
| Q-X3 | `windows-latest` / `macos-latest` runner kanıtı yok (§F Z4 devam) | UNKNOWN |

> **Kapanış kuralı:** Bu bölüm `status: draft`ı değiştirmez; `verified` yapmak için §F'deki
> kanıt sınıfı kuralları (S7/S8 yasağı) geçerlidir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
