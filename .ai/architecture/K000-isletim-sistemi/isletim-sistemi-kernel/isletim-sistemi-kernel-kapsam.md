---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/isletim-sistemi-kernel-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel
children: []
depends_on: []
evidence:
  - https://docs.kernel.org/filesystems/vfs.html
  - https://docs.kernel.org/filesystems/index.html
  - https://docs.kernel.org/mm/index.html
  - https://docs.kernel.org/driver-api/driver-model/index.html
  - https://docs.kernel.org/userspace-api/index.html
validation: "2026-10-13 · 6 yeni resmi URL HTTP 200 · envanterle kesişim 0 · 3 alt-dal dosya envanteri disk glob ile sayıldı"
open_questions:
  - "core ↔ kernel sınırı (Q2) kapatılmaz → açık kalır"
  - "Hedef çekirdek (Q4) UNKNOWN"
group: mimari
---

# isletim-sistemi-kernel — Kapsam

## §A — Kapsam

### §A.1 Rol (ELI15)

İşletim sisteminin **çalışan, donanıma dokunan kısmı**. `core` "OS ne demektir" derse,
`kernel` "OS nasıl çalışır" der: çağrıları karşılar, CPU'yu böler, belleği ayırır, dosya
sağlar, güvenliği uygular. Doğrudan donanım sürücülerini yükleyen katmandır.

Analoji: mutfağın **kendisi** — menü (API) siparişi alır, ama ocağı, fırını ve bıçağı
(kernel) **o** kullanır. Menüdeki yazı (`-api`) ile mutfaktaki alet (`kernel`) aynı şey
değildir; menünün aleti **tarif etmesi** ile aleti **kullanması** ayrı yeteneklerdir.

| Öğe | Değer |
|---|---|
| Bu dosya | Kademe-3 subdomain kapsamlama kaydı |
| Bağlı olduğu | `isletim-sistemi-kernel/index.md` (3 alt-dal sahibi) |
| Kapsam genişletme yetkisi | YOK — yalnız sınır ve envanter taşır |
| Ölçüm tarihi | 2026-10-13 |

### §A.2 Kapsam — İÇİNDE NE var

| # | İçinde | Dayanak | Alt-dal karşılığı (bu tur) |
|---|---|---|---|
| A1 | Sistem çağrısını karşılayan çekirdek tarafı | dogrulama §1/1·2 | `kesme-ve-senkronizasyon` §1 (kesme/yazılım kesmesi) |
| A2 | Zamanlama alt sistemi mekanizması (EEVDF / CFS) | dogrulama §1/3 | `cekirdek-yonetim` §5 (sistem durumu, context switch) |
| A3 | Bellek yönetimi çekirdek tarafı (`mmap` eşlemesi, sayfa tablosu) | dogrulama §1/4·5 | `cekirdek-yonetim` §2 (bellek yönetiminin çekirdek rolü) |
| A4 | Dosya sistemi çekirdek tarafı (VFS) | dogrulama §1/6 | `surucu-ve-modul-modeli` §3 (VFS ↔ sürücü arayüzü) |
| A5 | Güvenlik alt sistemi çekirdek tarafı: capability, LSM | dogrulama §1/7·8 | `surucu-ve-modul-modeli` §4 (ioctl yetki denetimi) |
| A6 | Syscall filtreleme (seccomp) çekirdek tarafı | dogrulama §1/9 | `surucu-ve-modul-modeli` §4 (seccomp sınırı) |
| A7 | Sürücü/API arayüzü (`core-api`, `driver-api`) | dogrulama §1/10·11 | `surucu-ve-modul-modeli` (tüm dal) |
| A8 | Platform çekirdekleri: Linux / Windows kernel / Apple Kernel | dogrulama §1/12·13·14 | kök `index.md` §2 (karşılaştırma) |
| A9 | `os-master.md` §3.1-§3.5'teki çekirdek kavramların **mekanizma** tarafı | `os-master.md` §3 | `cekirdek-yonetim` (süreç + bellek) |
| A10 | Kesme (interrupt) yolu: IRQ vektörü, üstelik, bottom-half | kök `index.md` §1/1 | `kesme-ve-senkronizasyon` (tüm dal) |
| A11 | Senkronizasyon yapıları çekirdek tarafı: spinlock, atomik, bellek modeli | kök `index.md` §1/1·5 | `kesme-ve-senkronizasyon` §2 (kilit yapıları) |
| A12 | Modül yükleme modeli (init/exit, bağımlılık, silme) | dogrulama §1/10 | `surucu-ve-modul-modeli` §2 (modül yaşam döngüsü) |

### §A.3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında olan | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Kavramsal "OS ne demektir" haritası (tanım, tarih, türler) | `isletim-sistemi-core` | Q2 · `-core` bu turda 4 alt-dala bölünüyor |
| B2 | Çağrı **yüzeyinin** kullanıcı tarafı (libc, POSIX API imzaları) | `isletim-sistemi-api` | kök `index.md` §4 |
| B3 | OS log/metric/trace (ftrace, ETW, perf okuma dili) | `isletim-sistemi-observability` | kök `index.md` §4 |
| B4 | Uygulama düzeyi güvenlik (`.htaccess`, `realpath` politikası) | `isletim-sistemi-security` | kök `index.md` §4/5 |
| B5 | Test altyapısı (kselftest, KUnit, pytest karşılığı) | `isletim-sistemi-testing` | kök `index.md` §4 |
| B6 | Ses sürücüsü/API'si (ALSA, WASAPI, CoreAudio uçları) | `isletim-sistemi-audio` (+ `wasapi/`) | kök `index.md` §4 |
| B7 | Sürücünün fiziksel devresi (DAC/ADC, güç, PCB) | `electroncis` · `K001-donanım` | Q1 |
| B8 | Firmware ve OTA güncelleme döngüsü | `K005-firmware` | görev sınırı (bu tur yasak) |
| B9 | Zamanlama **politikası** (RT sınıfı seçimi, öncelik stratejisi) | `isletim-sistemi-plan` | Q-P2 · bu dal yalnız mekanizmayı taşır |
| B10 | Uygulama katmanı çalışma zamanı (thread havuzu, kuyruk) | `isletim-sistemi-runtime` | kök `index.md` §4 |
| B11 | OS türü-spezifik anlatım (Linux/Windows detay dokümanları) | `isletim-sistemi-turu` | kök `index.md` §4 |
| B12 | OS geçiş/yenileme (migration) planları | `isletim-sistemi-cross` | kök `index.md` §4 |

### §A.4 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| C1 | `core` ↔ `kernel` | kavram vs mekanizma | ⚠️ Q2 TANIMSIZ |
| C2 | kernel ↔ sürücü | çekirdek içi kod vs yüklenen modül | ✅ §A.2/A7 · driver-api kanıtı |
| C3 | kernel ↔ kullanıcı uzay | syscall geçişi (trap) | ✅ §A.2/A1 · syscalls(2) |
| C4 | çekirdek güvenlik ↔ uygulama güvenlik | seccomp vs `.htaccess` | `[TASARIM]` — §E/5 |
| C5 | platform-özel ↔ POSIX ortak | Linux vs Windows çekirdek | ⚠️ Q4 (hedef bilinmiyor) |
| C6 | mekanizma ↔ politika | "nasıl yapılır" vs "ne seçilir" | ✅ §A.3/B9 — plan'a devredildi |
| C7 | çekirdek ↔ firmware | yazılım katmanı vs MCU kodu | ✅ §A.3/B8 — K005'e devredildi |

### §A.5 Repo ile sınır (dolaylı erişim — bilgi korundu)

| # | Çekirdek mekanizması | Dosya : satır | Doğrudan mı? |
|---|---|---|---|
| R1 | syscall tetikleme | `shared/src/AI/AIEngine.php:290-295` | ❌ dolaylı (PHP→libc) |
| R2 | süreç yaratma | `shared/tests/Repository/ApiKeyCreateCommandTest.php:253-258` | ❌ dolaylı |
| R3 | dosya + izin | `shared/src/Session/SessionConfig.php:33-36` | ❌ dolaylı |
| R4 | yol çözümleme | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-208` | ❌ dolaylı |
| R5 | güvenlik başlıkları | `home.coremusic.net/.htaccess` | ❌ **uygulama** düzeyi, kernel değil |
| R6 | modül/sürücü yüklemesi | **repo'da YOK** | ⚠️ VERIFICATION REQUIRED |
| R7 | kesme/kilit kullanımı | **repo'da YOK** (PHP'de spinlock yok) | ⚠️ VERIFICATION REQUIRED |
| R8 | bekleme/yeniden deneme | `home.coremusic.net/include/Auth/HomeAuthBridge.php:111-118` | ❌ dolaylı (`usleep` → syscall) |

> **R5 kasıtlı uyarıdır:** `.htaccess` güvenlik başlıkları çekirdek **değildir**; iki
> katman kök `index.md` §4/5'te ayrılmıştır. R6-R7 "yok" kaydı uydurma değildir —
> repo PHP uygulamasıdır, çekirdek kodu içermez (dürüstlük kuralı).

### §A.6 Kapsam kararları (bu turda alınan)

| # | Karar | Gerekçe | Kanıt |
|---|---|---|---|
| D1 | 3 alt-dal açıldı: `cekirdek-yonetim` · `kesme-ve-senkronizasyon` · `surucu-ve-modul-modeli` | kök `children: []` bilgiyi taşımıyordu; 5 işlev dağılımı gerekiyordu | kök `index.md` §1 · bu dosya §B |
| D2 | Zamanlama **politikası** bu dalda değil | `-plan` komşusunun konusu | §A.3/B9 |
| D3 | Sürücü **donanım tanımı** bu dalda değil | `K001-donanım` komşusunun konusu | §A.3/B7 |
| D4 | Firmware yaşam döngüsü bu dalda değil | `K005-firmware` komşusunun konusu | §A.3/B8 |
| D5 | `core` ↔ `kernel` sınırı yazılmadı | Q2 kanıtlanmadı; uydurma tanım yasak | §F/Q2 |
| D6 | İşlev dağılımında her işlevin **tek** ana evi var | çift sahiplik çakışma üretir | §C matrisi |

### §A.7 Reddedilen alternatifler

| # | Alternatif | Neden reddedildi | Kayıt |
|---|---|---|---|
| X1 | Alt-dal açmadan kökte kalmak | 4 dosya 362 satır; 5 işlevin dağılımı taşınamıyordu | D1 · §B.0 |
| X2 | Zamanlama politikasını da bu dala almak | `-plan` komşusu ile çakışma (çakışma = 0 zorunlu) | D2 · §A.3/B9 |
| X3 | Sürücü donanım tanımını bu dala almak | `K001-donanım` bu turda yasak yüzey | D3 · §A.3/B7 |
| X4 | Firmware yaşam döngüsünü bu dala almak | `K005-firmware` bu turda yasak yüzey | D4 · §A.3/B8 |
| X5 | `core` ↔ `kernel` sınırını tanımlayıp yazmak | Q2 kanıtsız; zero-hallucination yasağı | D5 · §F/Q-K1 |
| X6 | Bir işlevi iki alt-dala birden sahiplenmek | sahiplik belirsizliği = ileriki çakışma | D6 · §C.1 |
| X7 | `children` alanını `[]` bırakmak | diskte 3 klasör var; envanter ile çelişirdi | §B.0 |

### §A.8 Bu turun kabul kriterleri

| # | Kriter | Ölçüm | Hedef |
|---|---|---|---|
| K1 | Kök `index.md` satır aralığı | `Get-Content.Count` | 40-100 |
| K2 | Kök 3 içerik dosyası | satır sayısı | ≥500 her biri |
| K3 | Alt-dal `index.md` ×3 | satır sayısı | 40-100 her biri |
| K4 | Alt-dal içerik ×9 | satır sayısı | ≥500 her biri |
| K5 | Yeni URL ↔ envanter kesişimi | kesişim kümesi | 0 |
| K6 | Dışarıya yazım | beyazliste dışı dosya | 0 |
| K7 | `parent:` alanı kullanımı | grep `^parent:` | 0 |
| K8 | Tırnaklı `group` değeri | grep `group: "` | 0 |

## §B — 3 alt-dal envanteri

### §B.0 Genel envanter (disk, 2026-10-13)

| # | Alt-dal | Dosya sayısı | İçerik dosyası hedefi | group dağılımı | Durum |
|---|---|---|---|---|---|
| 1 | `cekirdek-yonetim/` | 4 (index + 3 içerik) | 3 × ≥500 satır | 1 subdomain + 3 mimari | ✅ bu turda oluşturuldu |
| 2 | `kesme-ve-senkronizasyon/` | 4 (index + 3 içerik) | 3 × ≥500 satır | 1 subdomain + 3 mimari | ✅ bu turda oluşturuldu |
| 3 | `surucu-ve-modul-modeli/` | 4 (index + 3 içerik) | 3 × ≥500 satır | 1 subdomain + 3 mimari | ✅ bu turda oluşturuldu |
| — | Toplam | 12 dosya / 3 dizin | 9 × ≥500 | — | kök 4 dosya ile birlikte 16 |

### §B.1 `cekirdek-yonetim/` envanteri

| # | Dosya | Yüzeyi | group | Satır hedefi |
|---|---|---|---|---|
| 1 | `index.md` | deep-dal indeksi (Kademe-3) | mimari | 40-100 |
| 2 | `cekirdek-yonetim-anayasa.md` | §0-§4: ELI15, Ring 0/3, bellek (sayfa/TLB/sanallaştırma), modlar, monolitik/mikro/hibrit | mimari | ≥500 |
| 3 | `cekirdek-yonetim-surucu.md` | §5-§9: sistem durumu/context switch, ölçüm, repo kanıtı, riskler, kaynakça | mimari | ≥500 |
| 4 | `cekirdek-yonetim-ornekler.md` | §1-§6: örnek kataloğu, senaryolar, repo örnekleri | kod | ≥500 |

Kapsam sözleşmesi: bu dal yalnızca **çekirdek mekanizmasını** anlatır; kavramsal tanım
(`-core`), syscall imza yüzeyi (`-api`), zamanlama politikası (`-plan`) dışarıdadır.

### §B.2 `kesme-ve-senkronizasyon/` envanteri

| # | Dosya | Yüzeyi | group | Satır hedefi |
|---|---|---|---|---|
| 1 | `index.md` | deep-dal indeksi (Kademe-3) | mimari | 40-100 |
| 2 | `kesme-ve-senkronizasyon-anayasa.md` | §0-§3: ELI15, kesme (IRQ/NMI/vektör), kilit türleri, deadlock/starvation | mimari | ≥500 |
| 3 | `kesme-ve-senkronizasyon-surucu.md` | §4-§6: SMP/tutarlılık, ölçüm (jitter/lock contention), referans | mimari | ≥500 |
| 4 | `kesme-ve-senkronizasyon-ornekler.md` | §1-§6: örnek kataloğu, senaryolar, repo örnekleri | kod | ≥500 |

Kapsam sözleşmesi: bu dal **kesme ve kilit mekanizmasını** anlatır; uygulama katmanı
eşzamanlılığı (`-runtime`) ve DSP zinciri (`K004-dsp`) dışarıdadır.

### §B.3 `surucu-ve-modul-modeli/` envanteri

| # | Dosya | Yüzeyi | group | Satır hedefi |
|---|---|---|---|---|
| 1 | `index.md` | deep-dal indeksi (Kademe-3) | mimari | 40-100 |
| 2 | `surucu-ve-modul-modeli-anayasa.md` | §0-§3: ELI15, yükçekirdek/out-of-tree modül, yaşam döngüsü, sürücü sınıfları | mimari | ≥500 |
| 3 | `surucu-ve-modul-modeli-surucu.md` | §4-§6: kullanıcı-kernel sınırı (syscall/ioctl), sürücü testi (kunit/QEMU), referans | mimari | ≥500 |
| 4 | `surucu-ve-modul-modeli-ornekler.md` | §1-§6: örnek kataloğu, senaryolar, repo örnekleri | kod | ≥500 |

Kapsam sözleşmesi: bu dal **modül/sürücü yüzeyini** anlatır; donanım şeması (`K001-donanım`),
firmware (`K005-firmware`), ses stacki (`-audio`) dışarıdadır.

### §B.4 Alt-dal dosya adları — disk doğrulaması

| # | Beklenen dosya | Doğrulama yöntemi | Durum |
|---|---|---|---|
| 1 | `cekirdek-yonetim/index.md` | `Get-ChildItem -Recurse -Filter *.md` | ✅ yazıldı |
| 2 | `cekirdek-yonetim/cekirdek-yonetim-anayasa.md` | aynı | ✅ yazıldı |
| 3 | `cekirdek-yonetim/cekirdek-yonetim-surucu.md` | aynı | ✅ yazıldı |
| 4 | `cekirdek-yonetim/cekirdek-yonetim-ornekler.md` | aynı | ✅ yazıldı |
| 5 | `kesme-ve-senkronizasyon/index.md` | aynı | ✅ yazıldı |
| 6 | `kesme-ve-senkronizasyon/kesme-ve-senkronizasyon-anayasa.md` | aynı | ✅ yazıldı |
| 7 | `kesme-ve-senkronizasyon/kesme-ve-senkronizasyon-surucu.md` | aynı | ✅ yazıldı |
| 8 | `kesme-ve-senkronizasyon/kesme-ve-senkronizasyon-ornekler.md` | aynı | ✅ yazıldı |
| 9 | `surucu-ve-modul-modeli/index.md` | aynı | ✅ yazıldı |
| 10 | `surucu-ve-modul-modeli/surucu-ve-modul-modeli-anayasa.md` | aynı | ✅ yazıldı |
| 11 | `surucu-ve-modul-modeli/surucu-ve-modul-modeli-surucu.md` | aynı | ✅ yazıldı |
| 12 | `surucu-ve-modul-modeli/surucu-ve-modul-modeli-ornekler.md` | aynı | ✅ yazıldı |

> Satır hedefi ölçümü `-dogrulama.md` §F'de; bu tablo yalnızca **varlık** kanıtı taşır.

### §B.5 Alt-dal giriş/çıkış sözleşmesi (12 dosya)

| # | Dosya | Girişte ne varsayar | Çıkışta ne verir | Verdikleri yere |
|---|---|---|---|---|
| 1 | `cekirdek-yonetim/index.md` | kök `index.md` children alanı | 3 dosyalık katalog | kök §GRUPLAR |
| 2 | `cekirdek-yonetim-anayasa.md` | 5 işlev (kök §1) | Ring 0/3 · bellek · modlar (§0-§4) | `-core` (kavram sınırı) |
| 3 | `cekirdek-yonetim-surucu.md` | anayasa §0-§4 | sistem durumu/context switch · ölçüm · risk (§5-§9) | `-plan` (ölçüm), `-core` (kavram) |
| 4 | `cekirdek-yonetim-ornekler.md` | işlev 2+3 (zamanlar/bellek) | senaryo + repo örnekleri | `-api` (fork yüzeyi) |
| 5 | `kesme-ve-senkronizasyon/index.md` | kök `index.md` children alanı | 3 dosyalık katalog | kök §GRUPLAR |
| 6 | `kesme-ve-senkronizasyon-anayasa.md` | işlev 1 (syscall) | kesme + trap modeli | `-api` (trap çağrısı) |
| 7 | `kesme-ve-senkronizasyon-surucu.md` | anayasa §0-§3 | SMP tutarlılığı · jitter/lock ölçümü (§4-§6) | `observability` (kesme sayacı) |
| 8 | `kesme-ve-senkronizasyon-ornekler.md` | eşzamanlılık gereksinimi | kilit/kesme senaryo örnekleri | `runtime` (uygulama kilit dili) |
| 9 | `surucu-ve-modul-modeli/index.md` | kök `index.md` children alanı | 3 dosyalık katalog | kök §GRUPLAR |
| 10 | `surucu-ve-modul-modeli-anayasa.md` | işlev 4 (dosya) | modül modeli + sürücü sınıfları | `K001-donanım` (sınır), `turu` |
| 11 | `surucu-ve-modul-modeli-surucu.md` | anayasa §0-§3 | syscall/ioctl sınırı · sürücü testi (§4-§6) | `observability` (modül logu) |
| 12 | `surucu-ve-modul-modeli-ornekler.md` | işlev 5 (güvenlik) | modül/sürücü senaryo örnekleri | `security` (uygulama ≠ çekirdek) |

> Sözleşme kuralı: bir dosya, komşusunun **dosya adını** verir; içeriğini kopyalamaz.
> Kopya = bilgi iki yerde = ileriki çelişki riski (D6).

## §C — 5 işlevin dağılım haritası

### §C.1 Matris (ana ev = ✅)

| İşlev ↓ / Alt-dal → | `cekirdek-yonetim` | `kesme-ve-senkronizasyon` | `surucu-ve-modul-modeli` | Ana ev |
|---|---|---|---|---|
| 1 · Sistem çağrısını karşılar | bağlam değiştirme (ikincil) | ✅ trap/yazılım kesmesi | ioctl ikinci kapısı (ikincil) | `kesme-ve-senkronizasyon` |
| 2 · Zamanlar (CPU dağıtım) | ✅ zamanlama mekanizması | kesme üstelik + timer tetikleyici | — | `cekirdek-yonetim` |
| 3 · Bellek ayırır | ✅ sayfa/buddy/CoW | sayfa kilitleri (kaçış) | DMA tampon kayıt (ikincil) | `cekirdek-yonetim` |
| 4 · Dosya sağlar | — | — | ✅ VFS ↔ sürücü arayüzü | `surucu-ve-modul-modeli` |
| 5 · Güvenliği uygular | süreç kimliği/UID | seccomp trap denetimi | ✅ capability + ioctl denetimi | `surucu-ve-modul-modeli` |

### §C.2 İşlev 1 — syscall karşılar

| Öğe | İçerik | Dosya |
|---|---|---|
| Çekirdek mekanizması | kullanıcı→çekirdek geçişi (trap), denetim listesi, dönüş değeri | `kesme-ve-senkronizasyon-anayasa.md` §1 |
| Bağlam tarafı | geçiş anında kaydedilen register/yığın durumu | `cekirdek-yonetim-surucu.md` §5 |
| Komşu | imza yüzeyi ve numaralandırma | `-api` (dışında) |
| Kanıt | syscalls(2) — kök `index.md` §5/2 | ✅ |

### §C.3 İşlev 2 — zamanlar

| Öğe | İçerik | Dosya |
|---|---|---|
| Çekirdek mekanizması | çalıştırılabilir kuyruk, zaman dilimi, devir (tick),EEVDF/CFS | `cekirdek-yonetim-surucu.md` §5 |
| Tetikleyici | zamanlayıcı kesmesi (timer interrupt) | `kesme-ve-senkronizasyon-anayasa.md` §1 |
| Komşu | hangi sınıfın öncelik alacağı (politika) | `-plan` (dışında — §A.3/B9) |
| Kanıt | scheduler/index.html — kök `index.md` §5/6 | ✅ |

### §C.4 İşlev 3 — bellek ayırır

| Öğe | İçerik | Dosya |
|---|---|---|
| Çekirdek mekanizması | adres alanı, sayfa tablosu, ödünç sayfa, buddy/slab, CoW | `cekirdek-yonetim-anayasa.md` §2 |
| Kilit etkisi | sayfa tablosu güncellemesi kilitli yapılır | `kesme-ve-senkronizasyon-anayasa.md` §2 |
| Komşu | kavramsal bellek tanımı | `-core` (Q2 — §E.1) |
| Kanıt | mmap(2) + Windows memory-management (kök `index.md` §5) | ✅ |

### §C.5 İşlev 4 — dosya sağlar

| Öğe | İçerik | Dosya |
|---|---|---|
| Çekirdek mekanizması | VFS, inode/dentry, dosya tanıtıcısı tablosu | `surucu-ve-modul-modeli-anayasa.md` §3 |
| Arayüz | sysfs/procfs/ioctl ile sürücüye ulaşan dosya kapıları | `surucu-ve-modul-modeli-surucu.md` §4 |
| Komşu | kullanıcı yüzeyi (open/read imzaları) | `-api` (dışında) |
| Kanıt | core-api/index.html (kök `index.md` §5/1) + bu dosya §E.3/1 | ✅ |

### §C.6 İşlev 5 — güvenliği uygular

| Öğe | İçerik | Dosya |
|---|---|---|
| Çekirdek mekanizması | capability parçalama, LSM kancaları, seccomp filtresi | `surucu-ve-modul-modeli-surucu.md` §4 |
| Yetki denetimi | ioctl çağrısında yetki kontrolü | `surucu-ve-modul-modeli-surucu.md` §4 |
| Komşu | uygulama güvenliği (`.htaccess`, rol denetimi) | `-security` (dışında — §A.3/B4) |
| Kanıt | capabilities(7) · seccomp(2) — kök `index.md` §5/4-5 | ✅ |

### §C.7 Dağıtılmayan işlevler (kayıt)

| # | İşlev | Neden dağıtılmadı | Nereye |
|---|---|---|---|
| E1 | Ağ yığını (TCP/IP çekirdek tarafı) | bu tur 3 alt-dal arasında yer almadı | ⚠️ VERIFICATION REQUIRED — ayrı kapsam kararı |
| E2 | Dosya sistemi **pazarlık** (defrag, fsync politikası) | `-plan`/uygulama katmanı | `isletim-sistemi-plan` |
| E3 | Sanallaştırma (VM, konteyner çekirdek desteği) | kapsam tanımı yok | UNKNOWN |

### §C.8 Dağılım çökme testi (red-team)

| # | Test | Beklenen | Bulgu |
|---|---|---|---|
| T1 | İşlev 1 iki "ana ev"e yazılırsa | tek ev: `kesme-ve-senkronizasyon` | §C.1'de tek ✅ |
| T2 | İşlev 2 `kesme` dalına kaydırılırsa | zamanlama mekanizması `cekirdek-yonetim`'de kalır | §C.3 ayrıldı (mekanizma vs tetikleyici) |
| T3 | İşlev 4 `cekirdek-yonetim`'e yazılırsa | VFS ↔ sürücü arayüzü `surucu`'da kalır | §C.5 tek ✅ |
| T4 | İşlev 5 `security` dalına yazılırsa | yalnız çekirdek tarafı bu dalda, politika `-security`'de | §C.6 + §A.3/B4 |
| T5 | Herhangi bir işlev `[]` kalırsa | dağılım tamamlanmaz | §C.1'de 5/5 dolu |
| T6 | Alt-dal adı tahmin edilirse | diske uymayan kayıt | §B.4 glob ile doğrulandı |

## §D — Bağımlılık grafiği

### §D.1 Düğüm listesi (bu dal)

| # | Düğüm | Tür | group |
|---|---|---|---|
| N1 | `isletim-sistemi-kernel` (kök) | subdomain | mimari |
| N2 | `isletim-sistemi-kernel-kapsam` | module | mimari |
| N3 | `isletim-sistemi-kernel-dogrulama` | module | kanit |
| N4 | `isletim-sistemi-kernel-ornekler` | module | kod |
| N5 | `cekirdek-yonetim` | subdomain | mimari |
| N6 | `kesme-ve-senkronizasyon` | subdomain | mimari |
| N7 | `surucu-ve-modul-modeli` | subdomain | mimari |

### §D.2 Kenarlar (okuma yönü: bağımlı → bağımlı olduğu)

| # | Kenar | Tür | Gerekçe |
|---|---|---|---|
| L1 | N2 → N1 | kapsam | kökün sınırlarını tarif eder |
| L2 | N3 → N1 · N5 · N6 · N7 | kanıt | kök ve alt-dalları doğrular |
| L3 | N4 → N1 | kod | kökün repo karşılıklarını gösterir |
| L4 | N5 → N1 | hiyerarşi | parent_id ile bağlı |
| L5 | N6 → N1 | hiyerarşi | parent_id ile bağlı |
| L6 | N7 → N1 | hiyerarşi | parent_id ile bağlı |
| L7 | N5 → N6 | işlevsel | bellek kilitleri kilit dalına referans verir |
| L8 | N7 → N6 | işlevsel | ioctl/DMA kesme yoluyla ilişkilidir |
| L9 | N6 → N5 | işlevsel | bağlam değiştirme bellek dalına referans verir |
| L10 | N1 → `os-master.md` | kanıt | katman 1 tanımı (§2) |
| L11 | N1 → `isletim-sistemi-core` | sınır | Q2 komşusu — **yönlü değil, sınır etiketi** |
| L12 | N1 → `isletim-sistemi-api` | sınır | syscall yüzeyi devri |

### §D.3 ASCII grafiğ

```text
K000-isletim-sistemi
└── isletim-sistemi-kernel (N1, subdomain, children: 3)
    ├── isletim-sistemi-kernel-kapsam (N2)
    ├── isletim-sistemi-kernel-dogrulama (N3) ──┐
    ├── isletim-sistemi-kernel-ornekler (N4)    │ doğrulama
    ├── cekirdek-yonetim (N5) ──── L7 ───► kesme-ve-senkronizasyon (N6)
    ├── kesme-ve-senkronizasyon (N6) ── L9 ──► cekirdek-yonetim (N5)
    └── surucu-ve-modul-modeli (N7) ─── L8 ──► kesme-ve-senkronizasyon (N6)
                                              │
komşular (sınır etiketi, bağımlılık değil):   │
  isletim-sistemi-core (Q2) · -api (yüzey) ───┘
  isletim-sistemi-plan (politika) · K001-donanım · K005-firmware
```

### §D.4 Bağımlılık kuralları

| # | Kural | Sonuç |
|---|---|---|
| P1 | Alt-dallar birbirinin `parent_id`'sini değiştirmez | hiyerarşi tek yönlü kalır |
| P2 | Kök dosyalar alt-dal dosyalarını **dosya adıyla** referanslar, satır içeriğini kopyalamaz | bilgi tek yerde |
| P3 | Sınır komşularına bağımlılık `depends_on`'a yazılmaz | `depends_on` yalnızca kanıtlanan teknik bağımlılık |
| P4 | Q2 kenarı (L11) `depends_on` değildir | açık soru, bağımlılık değil |

### §D.5 `depends_on` güncel durumu

| Dosya | `depends_on` | Değer |
|---|---|---|
| `index.md` (kök) | var | `[]` (kapsam dışı bağımlılık yok) |
| `-kapsam.md` | var | `[]` |
| `-dogrulama.md` | var | `[]` |
| `-ornekler.md` | var | `[]` |
| 12 alt-dal dosyası | var | `[]` (hepsi bu turda) |

> `[]` seçimi bilinçlidir: bağımlılık iddiası = kanıt yükü; bu turda hiçbir dosya
> teknik olarak diğerine **çalışmak için** bağlı değildir (yalnız referans/boundary vardır).

### §D.6 Grup ve kademe kuralları

| # | Kural | Uygulama |
|---|---|---|
| G1 | Alt-dal `index.md` → `node_type: subdomain` | 3 dosya |
| G2 | İçerik dosyaları → `node_type: module` | 12 dosya (kök 3 + alt-dal 9) |
| G3 | `group` değeri tırnaksız yazılır | `mimari` · `kod` · `kanit` |
| G4 | `-kapsam` → `mimari` · `-dogrulama` → `kanit` · `-ornekler` → `kod` | kök 3 dosya |
| G5 | Konu dosyaları (`-anayasa` · `-surucu`) → `mimari` | 6 dosya |
| G5b | Örnek dosyaları (`-ornekler`) → `kod` | 3 dosya |
| G6 | `index.md` → `mimari` | 4 dosya |
| G7 | FM `---` kapanışından sonra `group` yazılmaz | 16/16 dosya |
| G8 | `parent` (parent'sız) anahtarı kullanılmaz; `parent_id` kullanılır | 16/16 dosya |

## §E — Sınır komşuları (özellikle `-core` ile Q2 sınırı)

### §E.1 `-core` ile sınır (Q2 — KAPATILMAZ)

| # | Konu | Bu dalda (`kernel`) | `-core` tarafında | Durum |
|---|---|---|---|---|
| Q2-a | "OS ne demek" tanımı | YOK (kapsam dışı) | var (kavramsal) | ⚠️ Q2 açık |
| Q2-b | Zamanlama/bellek kavramı | mekanizması (bu dal) | kavramı (`-core`) | ⚠️ sınır tanımı yok |
| Q2-c | 5 işlevin dağılımı | bu turda **3 alt-dala** bölündü | `-core` bu turda 4 alt-dala bölünüyor (süreç/zamanlama · bellek · dosya sistemi · izin) | iki taraf da genişledi → sınır daha kritik |
| Q2-d | Çakışma önleme | bu dosya §C'de yalnız mekanizma | `-core` dosyasında yalnız kavram | ✅ tasarım kararı (D5/D6) |

> **Çakışma = 0 kuralı:** `-core` alt-dal dosyalarının adları ve içeriği bu turda
> **yazılmadı, okunmadı, tahmin edilmedi** — onları oluşturan ajana aittir. Sınır
> yalnızca bu tabloda **etiketlenir**.

### §E.2 Diğer komşular

| # | Komşu | Sınırda kalan | Kimin | Etiket |
|---|---|---|---|---|
| K1 | `isletim-sistemi-api` | syscall imza yüzeyi (ad, argüman) | `-api` | sınır tanımlı (§A.4/C3) |
| K2 | `isletim-sistemi-plan` | zamanlama politikası, RT sınıfı seçimi | `plan` | sınır tanımlı (§A.4/C6) |
| K3 | `isletim-sistemi-runtime` | uygulama thread/kuyruk eşzamanlılığı | `runtime` | sınır tanımlı (§B.2 sözleşmesi) |
| K4 | `isletim-sistemi-security` | uygulama güvenliği (`.htaccess`) | `security` | `[TASARIM]` (§A.4/C4) |
| K5 | `isletim-sistemi-observability` | ftrace/ETW okuma ve metrik | `observability` | sınır tanımlı (§A.3/B3) |
| K6 | `isletim-sistemi-testing` | kselftest/KUnit altyapısı | `testing` | sınır tanımlı (§A.3/B5) |
| K7 | `isletim-sistemi-audio` | ses sürücüsü/API ucu | `audio` | sınır tanımlı (§A.3/B6) |
| K8 | `K001-donanım` | donanım tanımı, BOM, devre | `K001` | ⚠️ Q1 (sürücü↔donanım sınırı) |
| K9 | `K005-firmware` | MCU firmware, OTA | `K005` | ⚠️ Q-FW |
| K10 | `electroncis` | elektronik tasarım dosyaları | `electroncis` | ⚠️ Q1 (diskte mevcut dizin) |
| K11 | `isletim-sistemi-turu` | platform anlatımı (Linux/Windows detayı) | `turu` | sınır tanımlı (§A.3/B11) |
| K12 | `isletim-sistemi-cross` | OS geçiş planları | `cross` | sınır tanımlı (§A.3/B12) |

### §E.3 Komşu doğrulaması (yeni URL'ler · 2026-10-13 · HTTP 200)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | VFS, dosya sisteminin çekirdek arayüzüdür | https://docs.kernel.org/filesystems/vfs.html | 2026-10-13 | ✅ HTTP 200 |
| 2 | Dosya sistemi dokümantasyonu ayrı alt sistemdir | https://docs.kernel.org/filesystems/index.html | 2026-10-13 | ✅ HTTP 200 |
| 3 | Bellek yönetimi ayrı alt sistemdir (mm) | https://docs.kernel.org/mm/index.html | 2026-10-13 | ✅ HTTP 200 |
| 4 | Sürücü/donanım bağlama modeli driver-model'dir | https://docs.kernel.org/driver-api/driver-model/index.html | 2026-10-13 | ✅ HTTP 200 |
| 5 | Kullanıcı uzay API kapsamı ayrı bölüm olarak yayınlanır | https://docs.kernel.org/userspace-api/index.html | 2026-10-13 | ✅ HTTP 200 |
| 6 | `no_new_privs` bayrağı yetki kazanımını kilitler | https://docs.kernel.org/userspace-api/no_new_privs.html | 2026-10-13 | ✅ HTTP 200 |

> Bu 6 URL envanterle **kesişim 0** (2026-10-13 ölçümü); tam liste `-dogrulama.md` §E.

### §E.4 Disk kanıtları (bilgi korundu)

| # | İddia | Gerçek dosya yolu / satır | Durum |
|---|---|---|---|
| E1 | Katman 1 = çekirdek (süreç, bellek, dosya, izin, ağ) | `.ai/architecture/K000-isletim-sistemi/os-master.md` §2 | ✅ doğrulandı |
| E2 | §3.1-§3.5 çekirdek kavram mekanizmaları | `os-master.md` §3.1-3.5 | ✅ doğrulandı |
| E3 | "çekirdek/iş ayrımı" notu (§3.1) | `os-master.md` §3.1 | ✅ doğrulandı |
| E4 | `fork(2)` POSIX tanımı | `os-master.md` §3.1 | ✅ doğrulandı |
| E5 | Uygulama güvenlik ≠ çekirdek güvenlik ayrımı | `os-master.md` §3.5 | ✅ doğrulandı |
| E6 | Çocuk açık sorusu: "core ↔ kernel ayrım tanımı yok" | `isletim-sistemi-kernel/index.md` frontmatter | ✅ doğrulandı |
| E7 | 3 alt-dal klasörü diskte var | `isletim-sistemi-kernel/{cekirdek-yonetim,kesme-ve-senkronizasyon,surucu-ve-modul-modeli}/` | ✅ glob ile sayıldı |
| E8 | `shell_exec` → libc → syscall zinciri | `shared/src/AI/AIEngine.php:290-295` | ✅ okundu (satır doğrulandı) |
| E9 | `proc_open` + fd 0/1/2 pipe | `shared/tests/Repository/ApiKeyCreateCommandTest.php:253-258` | ✅ okundu (satır doğrulandı) |
| E10 | `mkdir 0750` POSIX izin modu | `shared/src/Session/SessionConfig.php:33-36` | ✅ okundu (satır doğrulandı) |
| E11 | `realpath` + `is_link` çekirdek `stat` sargıları | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-208` | ✅ okundu (satır doğrulandı) |
| E12 | `usleep` ile yeniden deneme döngüsü | `home.coremusic.net/include/Auth/HomeAuthBridge.php:111-118` | ✅ okundu (satır doğrulandı) |

### §E.5 Komşu dosyalara okuma kuralı

| # | Komşu dosya | Bu dosyadan ne okuyabilir | Okumaması gereken |
|---|---|---|---|
| 1 | `-core/kapsam` (Q2 komşusu) | §E.1 sınır etiketi | §C'deki alt-dal dosya sözleşmeleri |
| 2 | `-api/kapsam` | §A.4/C3 (syscall geçişi) | §B envanteri (alt-dal iç işi) |
| 3 | `-plan/kapsam` | §A.3/B9 (politika devri) | §C.3 zamanlama mekanizması detayı |
| 4 | `K001-donanım/kapsam` | §A.3/B7 (donanım tanımı devri) | §B.3 sürücü arayüz envanteri |
| 5 | `K005-firmware/kapsam` | §A.3/B8 (firmware devri) | §D bağımlılık grafiği |
| 6 | `isletim-sistemi-turu/**` | kök §2 platform tablosu | §C dağılım matrisi |

> Kural: komşu dosya bu dalı **referans alır**, içeriğini **içe aktarmaz**. İçe aktarma =
> kopya = iki yerde aynı iddia = çelişki riski (D6 · §B.5).

## §F — Açık sorular

### §F.1 Aktif sorular

| # | Soru | Etiket | Sahip | Bu turda ne yapıldı |
|---|---|---|---|---|
| Q-K1 | `core` ↔ `kernel` sınırı (Q2) | ⚠️ VERIFICATION REQUIRED | Vault Steward | KAPATILMADI — §E.1'de etiketlendi |
| Q-K2 | Hedef çekirdek hangisi (Q4) | UNKNOWN | Vault Steward | KAPATILMADI — §C'de platform bağımsız yazıldı |
| Q-K4 | `electroncis` ↔ kernel sürücü sınırı (Q1) | UNKNOWN | Vault Steward | KAPATILMADI — §A.3/B7 devri kayıtlı |
| Q-K5 | Ağ yığını hangi dala ait? | ⚠️ VERIFICATION REQUIRED | Vault Steward | §C.7/E1'de açık kayıt |
| Q-K6 | Çekirdek sürümleri/spin-off bilgisi (Linux fork) | UNKNOWN | Vault Steward | doğrulanmadı — uydurulmadı |

### §F.2 Kapatılan sorular

| # | Soru | Kapanış | Kanıt |
|---|---|---|---|
| Q-K3 | Zamanlama/bellek kavramları hangi dalda? | ✅ bu tur: mekanizma → `cekirdek-yonetim` | kök `index.md` §1 · bu dosya §C.2-§C.4 |
| Q-K7 | Kök `children: []` neden boştu? | ✅ bu tur: 3 alt-dal açıldı | §B.0 envanter · kök `index.md` children |
| Q-K8 | 5 işlev alt-dallara nasıl dağılır? | ✅ bu tur: §C matrisi (tek ana ev kuralı D6) | §C.1 |

### §F.3 Bilinmeyenler (UNKNOWN — tahmin yok)

| # | Konu | Neden UNKNOWN |
|---|---|---|
| U1 | CoreMusic'in üretimde hedeflediği çekirdek | yalnız CI `ubuntu-latest` (test platformu) var |
| U2 | Repo'da çekirdek modülü yükleme örneği | repo PHP uygulaması — kod YOK |
| U3 | Linux fork'larının sürüm/kararlılık durumu | bu turda doğrulanmadı |
| U4 | `-core` 4 alt-dalının dosya adları | başka ajanın yüzeyi — tahmin edilmedi |

### §F.4 Sonraki tur için açık maddeler

| # | Madde | Beklenen kanıt | Bağımlılık |
|---|---|---|---|
| S1 | Q2: `core` ↔ `kernel` sınır tanımı | v2 §2.1 veya ADR kaydı | `-core` alt-dal dosyalarının kapanması |
| S2 | Q4: hedef çekirdek | CI/CD ya da runtime kanıtı | repo'da platform kararı |
| S3 | Ağ yığını kapsam kararı | kök `K000` children kararı | yeni alt-dal açılıp açılmayacağı |
| S4 | Q1: `electroncis` ↔ K001 ↔ kernel sürücü sınırı | `K001-donanım` ile ortak ADR | bu tur yasak yüzey |
| S5 | Çekirdek sürümü/spin-off doğrulaması | resmi sürüm sayfası (HEAD 200) | yeni URL turu |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
