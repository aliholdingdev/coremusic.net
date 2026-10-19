---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-anayasa"
evidence:
  - "home.coremusic.net/.htaccess:30"
  - "home.coremusic.net/web.config:30"
  - "shared/src/Api/Bff/DesktopBff.php:37"
  - ".github/workflows/ci.yml:44"
  - "https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-loadlibraryw"
  - "https://learn.microsoft.com/en-us/cpp/build/reference/compiler-options-listed-by-category"
validation: "2026-10-13 · 2 yeni URL HEAD 200 · envanter (2633) ile kesişim 0 · repo satırları Select-Object -Index (N-1) ile okundu"
open_questions:
  - "Q-X51 — soyutlama maliyeti ölçülmedi → ⚠️ VERIFICATION REQUIRED"
  - "Q-X52 — Windows/macOS koşu kanıtı yok → UNKNOWN"
  - "Q-X57 — adaptör katmanı için hedef arayüz (interface) tanımı yok → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# tasinabilirlik-siniri-surucu — Kademe-4 Uygulama Sürücüsü

> **Kapsam:** Anayasada tanımlanan kararların **uygulanacağı katman**, **akış**,
> **hata modları**, **test stratejisi** ve **ölçüm**. Bu dosya "nasıl
> uygulanır" sorusunu yanıtlar. **Kanıt dili:** S1 = birebir repo satırı ·
> S4 = mimari yorum · S7 = bilinmeyen (UNKNOWN).

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-surucu` |
| parent_id | `…/isletim-sistemi-cross/tasinabilirlik-siniri` |
| group | `mimari` |
| Bağımlı | `tasinabilirlik-siniri-anayasa.md` (§2 kategoriler · §4 eşikler) |
| Repo gerçeği | Uygulanacak soyutlama katmanı bugün **yalnız 2 yerde** var (`DIRECTORY_SEPARATOR` · `php_uname`) |

## §0 ELI15

Anayasa **karar verir**, bu dosya **uygular**: hangi katmanda soyutlama kurulur,
bir platforma özel satır görüldüğünde ne yapılır, hata çıktısında karar nasıl
yansıtılır ve kararın **doğru olduğu** nasıl test edilir. Bugün bu repo'da
uygulama yüzeyi **dar**dır (2 soyutlama noktası); büyümesi PLANNED'dır.

| # | Bölüm | Ne verir |
|---|---|---|
| 1 | §1 Uygulama katmanı | nereye kod yazılır (katman listesi) |
| 2 | §2 Akış | kararın çalıştırılma sırası |
| 3 | §3 Hata modları | ne ters giderse ne görünür |
| 4 | §4 Test stratejisi | nasıl doğrulanır |
| 5 | §5 Ölçüm | hangi sayı izlenir |
| 6 | §6 Repo kanıtı | bugünkü uygulamanın satırı |
| 7 | §7 Kaynakça | doğrulanmış kaynaklar |

## §1 Uygulama katmanı

### §1.1 Katman listesi (nereye yazılır)

| # | Katman | İçerik | Taşınabilirlik etkisi | Repo durumu |
|---|---|---|---|---|
| 1 | Dil çekirdeği | saf PHP sınıfı, tip bildirimi | tam taşınabilir | ✅ `shared/src/**` |
| 2 | Standart kütüphane sarmalayıcı | `realpath` · `sys_get_temp_dir` · `random_bytes` | taşınabilir + dikkat | ✅ `TemplateEngine.php:73` |
| 3 | Platform algılama | `php_uname('s')` · `PHP_OS_FAMILY` | koşullu | ✅ `DesktopBff.php:35` |
| 4 | Yol/ayraç soyutlaması | `DIRECTORY_SEPARATOR` · `rtrim(..., '\\/')` | soyutlanmış | ✅ `MusicStreamHandler.php:199` |
| 5 | Sunucu yapılandırması | `.htaccess` ↔ `web.config` | **çift yazılım** | ✅ iki dosya da diskte |
| 6 | Ortam/değişken | `$_SERVER['SERVER_SOFTWARE']` | koşullu | ✅ `DesktopBff.php:37` |
| 7 | CI/platform | `runs-on` · `runner.os` | platforma özel | ✅ `ci.yml:27,44` |
| 8 | Dosya izni/ACL | `mkdir` mod bayrağı / ACL | platforma özel | ⚠️ VERIFY (satır okunmadı) |
| 9 | Yerel kütüphane yükleme | `dlopen` / `LoadLibraryW` | **platforma özel** | ❌ repo'da YOK (PLANNED) |
| 10 | Sürücü/IOCTL yüzeyi | — | **platforma özel** | ❌ repo'da YOK (kapsam dışı) |

### §1.2 Adaptör arayüzü tasarımı (PLANNED — Q-X57)

| # | Öğe | Öneri | Durum |
|---|---|---|---|
| 1 | Arayüz adı | `PlatformSurface` (tek arayüz) | ⚠️ VERIFICATION REQUIRED (tanımlı değil) |
| 2 | Yöntemler | `separator()` · `tempDir()` · `osName()` · `serverSoftware()` | ⚠️ VERIFICATION REQUIRED |
| 3 | Implementasyon | `PosixSurface` · `WindowsSurface` · `DarwinSurface` | ⚠️ VERIFICATION REQUIRED |
| 4 | Seçim | çalışma zamanında tek kez, başlangıçta | ⚠️ VERIFICATION REQUIRED |
| 5 | Önbellek | sonuç immutable (çalışma boyunca değişmez) | ⚠️ VERIFICATION REQUIRED |
| 6 | Sıfır-allocation kuralı | ses/iş parçacığında adaptör **çağrılmaz** (L0 kuralı) | bağlayıcı (AGENTS §domain) |

### §1.3 Bugünkü soyutlama envanteri (S1)

| # | Soyutlama | Kullanım yeri | Kaç çağrı? | Durum |
|---|---|---|---|---|
| 1 | `DIRECTORY_SEPARATOR` | `TemplateEngine.php:73` | 1 (cache yolu) | ✅ |
| 2 | `DIRECTORY_SEPARATOR` | `MusicStreamHandler.php:199` | 1 (prefix kontrolü) | ✅ |
| 3 | `php_uname('s')` | `DesktopBff.php:35` | 1 (platform adı) | ✅ |
| 4 | `sys_get_temp_dir()` | `TemplateEngine.php:73` | 1 (geçici dizin) | ✅ |
| 5 | `realpath()` | `MusicStreamHandler.php:199 (önceki satırlar)` | ≥1 | ✅ (satır kanıtı §6) |
| 6 | adaptör arayüzü | — | 0 | ❌ yok |
| 7 | `PHP_OS_FAMILY` | — | 0 | ⚠️ VERIFICATION REQUIRED (tarama yapılmadı) |

> **Sayısal gerçek:** Envanter 6 soyulabilir yüzeyden **4'ü** uygulanmış,
> **1'i** yok, **1'i** ölçülmemiş.

### §1.4 Katman yerleşim kuralı

| # | Kural | Neden |
|---|---|---|
| 1 | OS API çağrısı **yalnız** adaptör arkasında | çağrı dağılması durdurulur |
| 2 | Çift yazılım dosyaları **aynı dizin ağacında** tutulur | diff denetimi kolaylaşır |
| 3 | Platform dalı **en derinde** (işlevin sonunda) kalır | okunabilirlik |
| 4 | Sesi/iş parçacığı yolunda soyutlama **çağrılmaz** | sıfır-allocation + lock-free (L0) |
| 5 | Yeni platform = **yeni adaptör**, mevcut kod değişmez | açık/kapalı ilkesi |
| 6 | Ölçülmeyen yüzey `UNMEASURED` etiketi taşır | zero-hallucination |

### §1.5 Kategori 4/5 yüzey için uygulanabilirlik matrisi (PLANNED)

> Bu tablo anayasa §2.1 kategori 4 (platforma özel) ve 5 (karar gerektiren)
> yüzeylerin **uygulanabilirlik** durumunu tarar. Repo'da karşılığı olmayan
> satırlar `upstream docs` kaynaklıdır (§6.3 dürüstlük kuralı).

| # | Yüzey | Windows karşılığı | POSIX karşılığı | Uygulanabilir mi? | Repo durumu |
|---|---|---|---|---|---|
| 1 | Yerel kütüphane yükleme | `LoadLibraryW` / `GetProcAddress` | `dlopen` / `dlsym` | evet (adapte edilebilir) | ❌ YOK (PLANNED) |
| 2 | Dosya yolu ayracı | `\` (`DIRECTORY_SEPARATOR`) | `/` | evet (mevcut) | ✅ 2 kullanım |
| 3 | Geçici dizin | `%TEMP%` / `GetTempPath` | `/tmp` · `sys_get_temp_dir` | evet (mevcut) | ✅ `TemplateEngine.php:73` |
| 4 | Platform adı | `PHP_OS_FAMILY === 'Windows'` | `php_uname('s')` | evet (mevcut) | ✅ `DesktopBff.php:35` |
| 5 | Sunucu yazılımı | `$_SERVER['SERVER_SOFTWARE']` (IIS) | Apache/Nginx | evet (mevcut) | ✅ `DesktopBff.php:37` |
| 6 | Dosya izni | ACL / `icacls` | `chmod` mod biti | kısmen | ⚠️ satır okunmadı |
| 7 | Sürücü IOCTL | `DeviceIoControl` | — (kullanıcı düzeyi yok) | hayır (kapsam dışı) | ❌ YOK |
| 8 | Sinyal/iş parçacığı | SEH / `__try` | `sigaction` · `pthread` | evet | ❌ YOK (kapsam dışı) |
| 9 | CI platformu | `windows-latest` | `ubuntu-latest` | evet | ✅ yalnız `ubuntu` |
| 10 | Dosya kilidi | `LockFileEx` | `flock` | evet | ❌ YOK |

> **Sonuç:** 10 yüzeyin **4'ü** uygulanmış, **5'i** PLANNED/kapsam dışı,
> **1'i** ölçülmemiş. Uygulanabilirlik ≠ uygulanmışlık: bu tablo yalnızca
> "yapılabilir mi" sorusunu yanıtlar, "yapıldı mı" §1.3'tedir.

### §1.6 Adaptör maliyet modeli (Q-X51 — ölçülmedi)

| # | Maliyet kalemi | Nasıl ölçülür | Birim | Bugün |
|---|---|---|---|---|
| 1 | Arayüz başına satır | adaptör sınıfı satır sayısı | satır | ⚠️ VERIFICATION REQUIRED |
| 2 | Çağrı gecikmesi | adaptör `separator()` süresi | ns | ⚠️ VERIFICATION REQUIRED |
| 3 | Bellek | adaptör nesnesi boyutu | byte | ⚠️ VERIFICATION REQUIRED |
| 4 | Test yüzeyi | adaptör başına test sayısı | test | ⚠️ VERIFICATION REQUIRED |
| 5 | Bakım | yeni platform başına ek adaptör | adet | 2 (öneri: `PosixSurface` · `WindowsSurface`) |

> Bu 5 kalem **hiçbiri ölçülmedi** → anayasa §10 Q-X51 ile aynı işaret.
> Ölçüm olmadan "soyutlama pahalı mı" tartışması **karar verilemez** (§3.1 #10).

## §2 Akış

### §2.1 Karar akışı (anayasa §3.1'in uygulama hâli)

| # | Adım | Tetikleyici | Aksiyon | Kayıt |
|---|---|---|---|---|
| 1 | Yüzey tespiti | yeni dosya / PR | tarama: `*.htaccess` · `web.config` · platform çağrısı | envanter |
| 2 | Kategorileme | her yüzey | anayasa §2.1'e göre 1-6 | etiket |
| 3 | Uygulama kararı | kategori 2-3 | mevcut soyutlamayı kullan | — |
| 4 | Uygulama kararı | kategori 4 | adaptör + çift yazılım eşdeğeri | `⚠️` |
| 5 | Eşik kontrolü | kategori 5 sayısı > 2 | onay iste | Q-X55 |
| 6 | Doğrulama | her PR | §4 testleri koş | rapor |
| 7 | Kapanış | — | `-dogrulama.md` satırı | kanıt |

### §2.2 Çift yazılım senkron akışı (PLANNED — §3.3 riskinin kapatılması)

| # | Adım | Ne yapılır | Beklenen |
|---|---|---|---|
| 1 | Kural çıkarımı | `.htaccess` rewrite/dosya bloklarını listele | 4 kural |
| 2 | Karşılık eşleme | `web.config` kural bloklarıyla eşle | 4/4 eşleşme |
| 3 | Diff | iki listenin aksiyonlarını karşılaştır | uyuşmazlık listesi |
| 4 | Eşik | uyuşmazlık = 0 | geç |
| 5 | Rapor | PR yorumu | görünür |

> Bu akış **PLANNED**'dır; bugün otomatik denetim **yoktur** (`-anayasa` §3.3 #4).

### §2.3 Platform algılama akışı (çalışan — `DesktopBff.php:34-37`)

| # | Satır | Alan | Ne okur | Kategori |
|---|---|---|---|---|
| 1 | 35 | `platform` | `php_uname('s')` | 2 (koşullu) |
| 2 | 36 | `phpVersion` | `PHP_VERSION` | 1 (taşınabilir) |
| 3 | 37 | `serverSoftware` | `$_SERVER['SERVER_SOFTWARE'] ?? 'unknown'` | 2 (koşullu) |
| 4 | 34 | `meta.system` | üçlünün kabı | — |

> `?? 'unknown'` ifadesi, ortam değişkeninin **olmadığı** durumu da kapsar —
> bu, anayasa §2.1 kategori 6 (`UNMEASURED`) ile uyumludur: bilinmeyen,
> **bilinmiyormuş gibi** raporlanır.

### §2.4 Yol güvenliği akışı (çalışan — `MusicStreamHandler.php:199-202`)

| # | Satır | İşlem | Taşınabilirlik etkisi |
|---|---|---|---|
| 1 | 199 | `rtrim($root, '\\/') . DIRECTORY_SEPARATOR` | iki ayracı da **keser** → iki platformda da doğru |
| 2 | 200 | `str_starts_with($real, $prefix)` | gerçek yol kontrolü |
| 3 | 201 | `fail(403)` | ihlalde reddet |
| 4 | 204 | `is_readable($real)` | erişim kontrolü |

> **Neden önemli:** Bu 4 satır, anayasa §2.1'de **kategori 3** (soyutlanmış)
> örnek olarak verilir: güvenlik kuralı **tek yazım** ile iki platformu kapsar.

### §2.5 CI platform matrisi akışı (çalışan — `ci.yml:27,44`)

| # | Satır | Alan | Değer | Etki |
|---|---|---|---|---|
| 1 | 27 | `runs-on` | `ubuntu-latest` | tek platform koşusu |
| 2 | 44 | cache `key` | `${{ runner.os }}-composer-…` | OS ayrımı hazır |
| 3 | 155 | kurulum | `npm ci` | lock-faithful (§4.1 #1) |
| 4 | 126 | denetim | `composer audit` | bağımlılık güvenlik adımı |

> **Kritik gözlem:** `runner.os` ayrımı **tanımlı**, ancak job **tek** `ubuntu-latest`
> ile çalıştığı için ayrım **bugün hiçbir fark yaratmaz**. Matris büyürse
> (`windows-latest` eklenirse) cache anahtarı zaten hazır olur — bu, §4.3 #2
> kapısının "yalnız 1" durumunun **geleceğe hazır** olduğunu gösterir.

### §2.6 Karar kayıt akışı (PLANNED — `-dogrulama.md` bağlantısı)

| # | Adım | Ne kaydedilir | Nereye | Durum |
|---|---|---|---|---|
| 1 | Yeni yüzey tespiti | yüzey adı + kategori | envanter tablosu (§1.5) | PLANNED |
| 2 | Kategori 4 kararı | "adaptör mü, çift yazılım mı" | ADR / karar satırı | PLANNED |
| 3 | Ölçüm | §5.1 sayaç değeri | `-dogrulama.md` | PLANNED |
| 4 | Eşik ihlali | ihlal + onay | açık soru (Q-X55) | PLANNED |
| 5 | Kapanış | karar + kanıt + tarih | `-dogrulama.md` §6 | PLANNED |

> Bu akış **PLANNED**'dır; bugün `-dogrulama.md` dosyası var ancak bu akışın
> çıktısı olarak **kullanılmamıştır** (otomatik senkron yok).

## §3 Hata modları

### §3.1 Hata modu tablosu

| # | Hata modu | Belirti | Kök neden | Kapsam |
|---|---|---|---|---|
| 1 | **Kural sürüklenmesi** | bir platformda 403, diğerinde 200 | `.htaccess` güncellendi, `web.config` güncellenmedi | çift yazılım |
| 2 | **Ayrac karışıklığı** | yol bulunamadı / yol kaçışı başarısız | `DIRECTORY_SEPARATOR` yerine sabit `/` | kategori 3 |
| 3 | **Küçük/büyük harf** | Linux'ta dosya var, Windows'ta çakışma | duyarlılık farkı | kategori 4 |
| 4 | **Platform algılama yanlışlığı** | yanlış ram ekranda platform adı | `php_uname` sahte/ısıtmalı ortam | kategori 2 |
| 5 | **Eksik eklenti** | `Class "PDO" not found` | `pdo_mysql` kurulmamış | §2.3 #10 |
| 6 | **Cache ayrışması** | Windows runner'da Linux cache anahtarı | `runner.os` yanlış gömülürse | kategori 2 |
| 7 | **Tek platform yanılgısı** | Linux'ta geçer, Windows'ta patlar | matris test yok (Q-X52) | kategori 4 |
| 8 | **Sessiz `UNKNOWN`** | `?? 'unknown'` her yerde görünür | ortam değişkeni hiç set edilmiyor | kategori 6 |
| 9 | **Eşik aşımı** | çift yazılım dosyası sayısı > 2 | yeni platform eklenirken denetim yok | Q-X55 |
| 10 | **Ölçüm yokluğu** | karar tartışması tekrarlanır | §2.4'ün 5 kalemi ölçülmemiş | Q-X51 |

### §3.2 Hata modu → gözlem eşlemesi (PLANNED)

| # | Hata modu | Ne loglanır | Nereye |
|---|---|---|---|
| 1 | kural sürüklenmesi | diff sayısı (0 değilse) | PR raporu |
| 2 | ayrac karışıklığı | yol denetimi başarısızlık sayısı | uygulama logu |
| 3 | küçük/büyük harf | dosya-bulunamadı sayısı | uygulama logu |
| 4 | algılama yanlışlığı | `platform` + `serverSoftware` çifti | `/api` meta alanı |
| 5 | eksik eklenti | `composer check-platform-reqs` (PLANNED) | CI |
| 6 | cache ayrışması | `runner.os` + `key` | CI logu |
| 7 | tek platform yanılgısı | matris yokluğu | `isletim-sistemi-observability` (devir) |
| 8 | sessiz `UNKNOWN` | `unknown` sayacı | `isletim-sistemi-observability` (devir) |

### §3.3 Sınıf tasarımındaki kırılma noktaları

| # | Kırılma | Sonuç | Önleme |
|---|---|---|---|
| 1 | Adaptör arayüzüne 5. yöntem eklenirse | tüm implementasyonlar değişir | arayüz 5 yöntemle sınırlı (Q-X57) |
| 2 | Çift yazılım dosyası silinirse | tek platform kalır | §2.2 senkron denetimi |
| 3 | Platform algılama her çağrıda yapılırsa | maliyet + yarış | bir kez, başlangıçta |
| 4 | Ölçümü `UNKNOWN`'a düşürmek | karar tartışması döner | §5 sayaçları |
| 5 | `UNMEASURED` yüzeyin sessizce üretime çıkması | zero-hallucination ihlali | §4.3 kapısı |

### §3.4 Hata modu → öncelik matrisi

| # | Hata modu | §3.1 # | Olasılık | Etki | Öncelik | Düzeltme maliyeti |
|---|---|---|---|---|---|---|
| 1 | Kural sürüklenmesi | 1 | yüksek | yüksek | **P0** | düşük (satır ekleme) |
| 2 | Ayrac karışıklığı | 2 | düşük | yüksek | P1 | zaten önlenmiş (§1.3 #1-2) |
| 3 | Küçük/büyük harf | 3 | orta | orta | P2 | orta (test + normalize) |
| 4 | Algılama yanlışlığı | 4 | düşük | orta | P3 | düşük (bir kez, başlangıçta) |
| 5 | Eksik eklenti | 5 | düşük | yüksek | P1 | düşük (`check-platform-reqs`) |
| 6 | Cache ayrışması | 6 | düşük | orta | P3 | zaten hazır (`runner.os`) |
| 7 | Tek platform yanılgısı | 7 | **yüksek** | **yüksek** | **P0** | yüksek (2. job + test) |
| 8 | Sessiz `UNKNOWN` | 8 | orta | düşük | P4 | düşük (sayaç) |
| 9 | Eşik aşımı | 9 | orta | orta | P2 | düşük (denetim betiği) |
| 10 | Ölçüm yokluğu | 10 | **yüksek** | orta | P1 | orta (ölçüm altyapısı) |

> **P0 olan 2 hata modu** bugün **açık**: kural sürüklenmesi (1 gerçek uyuşmazlık
> var, §4.2 #7) ve tek platform yanılgısı (yalnız `ubuntu-latest`). İkisi de
> §4.3 kapılarının tanımamasından kaynaklanır — kapı tanımlanırsa hata modu
> **erken yakalanır**.

### §3.5 Hata modu simülasyonu (PLANNED — test tasarımı)

| # | Senaryo | Nasıl tetiklenir | Beklenen sonuç | Kapı |
|---|---|---|---|---|
| 1 | `web.config`'e kural ekle, `.htaccess`'e ekleme | manuel PR | §4.2 tablosu 1 uyuşmazlık gösterir | §4.3 #1 |
| 2 | `DIRECTORY_SEPARATOR` yerine `/` yaz | manuel PR | Linux'ta geçer, Windows'ta patlar | §4.3 #2 |
| 3 | `runs-on`'a `windows-latest` ekle | manuel PR | cache ayrımı çalışır (§2.5) | §4.3 #2 |
| 4 | `php_uname`'ı sahte ortamda çalıştır | konteyner | yanlış platform adı | §4.1 #2 |
| 5 | `SERVER_SOFTWARE`'i sil | konteyner | `?? 'unknown'` devreye girer | §4.1 #2 |
| 6 | `composer check-platform-reqs` çalıştır | CI | exit 0 (tüm eklentiler var) | §4.3 #4 |
| 7 | Platforma özel yüzey ekle | envanter | sayaç 3 → 4 (eşik ihlali) | §4.3 #3 |

> Bu 7 senaryonun **hiçbiri** bugün çalışmıyor (kapılar tanımsız). Senaryolar
> yalnızca **tasarım**tır; uygulama PLANNED'dır.

## §4 Test stratejisi

### §4.1 Test katmanları

| # | Katman | Ne test eder | Bugün var mı? |
|---|---|---|---|
| 1 | Birim — yol/ayraç | `DIRECTORY_SEPARATOR` yolları iki ayracta da doğru | ⚠️ VERIFICATION REQUIRED (test içeriği okunmadı) |
| 2 | Birim — algılama | `php_uname` dalı beklenen adı verir | ⚠️ VERIFICATION REQUIRED |
| 3 | Eşik — senkron | `.htaccess` ↔ `web.config` kural eşitliği | ❌ yok (PLANNED) |
| 4 | Eşik — envanter | platforma özel yüzey sayısı ≤ eşik | ❌ yok (PLANNED) |
| 5 | Bütünleşik — çift platform | Linux + Windows job'ı | ❌ yok (Q-X52) |
| 6 | Bütünleşik — sunucu | Apache **ve** IIS üzerinde SPA router | ⚠️ VERIFICATION REQUIRED |
| 7 | Regresyon | 403/200 davranışı | `home.coremusic.net` testleri ⚠️ VERIFY |

### §4.2 Test verisi — senkron denetimi için beklentiler (PLANNED)

| # | Kural (`web.config` tarafı) | Karşılığı (`.htaccess` tarafı) | Beklenen |
|---|---|---|---|
| 1 | `Static files` (`web.config:16-19`) | `RewriteRule \.(css\|js\|…)$ - [L]` (`:34`) | eşleşme |
| 2 | `Block config/include/pages` (`:20-23`) | `RewriteRule ^(config\|include\|pages)…` (`:35`) | eşleşme |
| 3 | `Block sensitive files` (`:24-27`) | `<FilesMatch "\.(env\|log\|…)$">` (`:18-20`) | eşleşme |
| 4 | `SPA Router` (`:28-35`) | `RewriteCond -f/-d` + `:30-32` | eşleşme |
| 5 | `Rewrite to index.php` (`:36-39`) | `RewriteRule ^(.*)$ index.php [QSA,L]` (`:36`) | eşleşme |
| 6 | Güvenlik başlıkları (`:6-11`) | `<IfModule mod_headers.c>` (`:9-16`) | kısmi eşleşme ⚠️ |
| 7 | `Permissions-Policy` (`:11`) | `.htaccess`'te **YOK** | **uyuşmazlık (bulgu)** |

> Satır 7 gerçek bir **uyuşmazlık**tır: `web.config` `Permissions-Policy`
> başlığını set eder, `.htaccess` set etmez. Bu, §3.1 #1 hata modunun
> **gözlemlenmiş örneğidir** → §6.3.

### §4.3 Kapılar (gate)

| # | Kapı | Koşul | Durum |
|---|---|---|---|
| 1 | Çift yazılım senkron | uyuşmazlık = 0 | ❌ tanımsız (ve 1 uyuşmazlık var) |
| 2 | Platform matrisi | ≥ 2 `runs-on` | ❌ yalnız 1 |
| 3 | Envanter taraması | platforma özel yüzey sayısı ≤ eşik | ❌ tanımsız |
| 4 | Eksik eklenti | `composer check-platform-reqs` exit 0 | ⚠️ tanımsız |
| 5 | `UNKNOWN` sayacı | 0 artmaz | ⚠️ tanımsız |

### §4.4 Test senaryosu envanteri (PLANNED — §3.5'ten bağımsız detay)

| # | Senaryo | Katman | Girdi | Beklenen | Durum |
|---|---|---|---|---|---|
| T1 | Yol ayracı dönüşümü | birim | `rtrim($root, '\\/')` | iki ayrac da kesilir | ⚠️ test yok |
| T2 | Gerçek yol kontrolü | birim | `str_starts_with` | kaçış reddedilir | ⚠️ test yok |
| T3 | Platform algılama | birim | `php_uname('s')` | beklenen OS adı | ⚠️ test yok |
| T4 | Sunucu yazılımı yok | birim | `$_SERVER` boş | `unknown` döner | ⚠️ test yok |
| T5 | `.htaccess` kural sayısı | eşik | Apache blokları | 4 kural | ❌ yok |
| T6 | `web.config` kural sayısı | eşik | IIS blokları | 5 kural | ❌ yok |
| T7 | Kural eşlemesi | eşlik | T5 + T6 | 4/5 eşleşme + 1 uyuşmazlık | ❌ yok |
| T8 | Dosya/dizin istisnası | eşlik | `REQUEST_FILENAME` | -f/-d dalı çalışır | ❌ yok |
| T9 | SPA router yönlendirme | bütünleşik | `/music` | `index.php`'ye gider | ⚠️ VERIFY |
| T10 | Hassas dosya engeli | bütünleşik | `/.env` | 403 döner | ⚠️ VERIFY |
| T11 | CI cache ayrımı | bütünleşik | `runner.os` | OS başına ayrı cache | ✅ `ci.yml:44` |
| T12 | Composer platform gereksinimi | eşlik | `composer.json:8` | CI PHP sürümüyle eşleşir | ⚠️ VERIFY |
| T13 | `npm ci` lock uyumu | birim | `package-lock.json` | lock-faithful kurulum | ✅ `ci.yml:155` |
| T14 | `composer audit` çağrısı | birim | bağımlılıklar | exit 0 | ✅ `ci.yml:126` |
| T15 | `optimize-autoloader` | birim | `composer.json:44` | `true` | ✅ repo satırı |

> **Özet:** 15 senaryonun **4'ü** bugün kanıtlanmış (T11 · T13-15),
> **6'sı** `⚠️ VERIFY` (içerik okunmadı), **5'i** tamamen yok (PLANNED).

## §5 Ölçüm

### §5.1 Sayaç envanteri

| # | Sayaç | Tanım | Hedef | Bugün |
|---|---|---|---|---|
| 1 | Çift yazılım satır farkı | `web.config` satırı − `.htaccess` satırı | ≤ 6? | **6** (43 − 37) |
| 2 | Kural uyuşmazlığı | eşlenemeyen kural sayısı | **0** | **1** (§4.2 #7) |
| 3 | Platforma özel yüzey | kategori 4+5 yüzey sayısı | ≤ 2 | **3** (`.htaccess` · `web.config` · CI platformu) |
| 4 | Test edilen platform | farklı `runs-on` değeri | ≥ 2 | **1** |
| 5 | Ölçülmüş maliyet kalemi | §2.4 ölçülen satır | 6/6 | **1/6** |
| 6 | `UNKNOWN` sayısı | açık soru | 0 | **5** (index §7) |
| 7 | Soyutlama noktası | §1.3 sütunu 5 | ≥ 6 | **4** |
| 8 | Adaptör arayüzü | var/YOK | var | **YOK** |

### §5.2 Ölçüm tekrarı

| # | Ne | Ne sıklıkla | Kim |
|---|---|---|---|
| 1 | `.htaccess`/`web.config` satır + kural diff | her PR | CI (PLANNED) |
| 2 | `runs-on` matrisi sayısı | her PR | CI (PLANNED) |
| 3 | Açık soru sayısı | her oturum | Vault Steward |
| 4 | Platform envanteri | yeni alt-alan eklenince | Vault Steward |
| 5 | Adaptör çağrı maliyeti | L0 fazında (PLANNED) | `embedded-engineer` |

### §5.3 Ölçüm eşikleri ve karar kuralları

| # | Sayaç | §5.1 # | Eşik | Eşik altı | Eşik üstü |
|---|---|---|---|---|---|
| 1 | Kural uyuşmazlığı | 2 | **0** | → kural sürüklenmesi (P0) | geç |
| 2 | Test edilen platform | 4 | **≥ 2** | → tek platform yanılgısı (P0) | geç |
| 3 | Soyutlama noktası | 7 | **≥ 6** | → adaptör ekle | yeterli |
| 4 | Adaptör arayüzü | 8 | **var** | → Q-X57 kararı gerekli | yeterli |
| 5 | Ölçülmüş maliyet | 5 | **6/6** | → Q-X51 açık kalır | karar verilebilir |
| 6 | `UNKNOWN` sayısı | 6 | **0** | → vault Steward görevi | temiz |

> **Karar kuralı:** Eşik altı değeri olan her sayaç **otomatik olarak** bir açık
> soru (Q) üretir. Eşik **yalnızca** Vault Steward tarafından değiştirilebilir
> (§2.6 #4).

### §5.4 Ölçüm araçları (PLANNED)

| # | Araç | Ne sayar | Komut / yöntem | Durum |
|---|---|---|---|---|
| 1 | Kural diff betiği | `.htaccess` ↔ `web.config` kural farkı | PowerShell / bash | ❌ yok |
| 2 | Envanter tarayıcı | platforma özel yüzey sayısı | `Select-String` desen | ❌ yok |
| 3 | `composer check-platform-reqs` | eksik eklenti | composer yerleşik | ⚠️ CI'da yok |
| 4 | `npm ci` lock denetimi | lock uyumu | CI adımı | ✅ `ci.yml:155` |
| 5 | `composer audit` | bilinen güvenlik açığı | CI adımı | ✅ `ci.yml:126` |
| 6 | Platform matrisi raporu | farklı `runs-on` sayısı | CI job sayısı | ❌ yalnız 1 |

## §6 Repo kanıtı

### §6.1 Doğrulanmış satırlar (S1 · `Select-Object -Index (N-1)`)

| # | Dosya | Satır | İçerik özeti |
|---|---|---|---|
| 1 | `home.coremusic.net/.htaccess` | 26 | `<IfModule mod_rewrite.c>` — Apache rewrite bloğu |
| 2 | `home.coremusic.net/.htaccess` | 30-31 | `RewriteCond %{REQUEST_FILENAME} -f/-d` — dosya/dizin istisnası |
| 3 | `home.coremusic.net/web.config` | 30-32 | `<add input="{REQUEST_FILENAME}" matchType="IsFile/IsDirectory"/>` |
| 4 | `home.coremusic.net/web.config` | 11 | `Permissions-Policy` başlığı (`.htaccess`'te yok) |
| 5 | `shared/src/Api/Bff/DesktopBff.php` | 37 | `$_SERVER['SERVER_SOFTWARE'] ?? 'unknown'` |
| 6 | `.github/workflows/ci.yml` | 44 | `key: ${{ runner.os }}-composer-…` |

### §6.2 Gözlemlenen uyuşmazlık (bulgu)

| # | Bulgu | Kanıt | Sonuç |
|---|---|---|---|
| 1 | `Permissions-Policy` yalnız IIS'te | `web.config:11` mevcut · `.htaccess` satır 9-16'da yok | **Apache'de başlık yok** |
| 2 | Satır farkı +6 | 43 vs 37 | büyük ölçüde kural ve satır sarması |
| 3 | Ortak kural 4/5 | §4.2 tablosu | 1 uyumsuz |

### §6.3 Kaynak: upstream (repo'da karşılığı yok)

| # | Konu | Neden repo'da yok | Kaynak |
|---|---|---|---|
| 1 | Yerel kütüphane yükleme (`LoadLibraryW`) | native kod yok | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-loadlibraryw |
| 2 | MSVC derleme bayrakları | native derleme yok | https://learn.microsoft.com/en-us/cpp/build/reference/compiler-options-listed-by-category |

> **Dürüstlük:** CoreMusic'te **çekirdek seviyesi kod YOK** — yukarıdaki iki satır
> `upstream docs` kaynaklıdır; depoda karşılığı **uydurulmamıştır**.

### §6.4 Çift yazılım dosyalarının tam karşılaştırması (2026-10-13 okuması)

| # | Özellik | `.htaccess` (Apache) | `web.config` (IIS) | Eşleşme |
|---|---|---|---|---|
| 1 | Toplam satır | 37 | 43 | +6 fark |
| 2 | Güvenlik başlığı | `<IfModule mod_headers.c>` (9-16) | `<httpProtocol><customHeaders>` (6-11) | kısmi |
| 3 | `Permissions-Policy` | **YOK** | `:11` mevcut | ❌ uyuşmazlık |
| 4 | Statik dosya istisnası | `:34` rewrite | `:16-19` `<files>` | ✅ |
| 5 | Config/include/pages engeli | `:35` rewrite | `:20-23` `<location>` | ✅ |
| 6 | Hassas dosya engeli | `:18-20` `<FilesMatch>` | `:24-27` `<location>` | ✅ |
| 7 | SPA router | `:30-32` `RewriteCond -f/-d` | `:28-35` `<rule>` | ✅ |
| 8 | `index.php` yönlendirme | `:36` | `:36-39` | ✅ |
| 9 | Dosya/dizin istisnası | `RewriteCond %{REQUEST_FILENAME}` | `<add input="{REQUEST_FILENAME}"` | ✅ |

> **Ölçümsel gerçek:** 9 özelliğin **8'i** eşleşiyor; **1'i** (`Permissions-Policy`)
> yalnız IIS'te mevcut. Bu, §3.1 #1 hata modunun **gözlemlenmiş somut örneğidir**.

### §6.5 Repo kanıtı güvenilirlik notu

| # | Kanıt türü | Kaynak | Güvenilirlik |
|---|---|---|---|
| 1 | S1 (birebir satır) | `Select-Object -Index (N-1)` | yüksek (dosya okundu) |
| 2 | S4 (mimari yorum) | anayasa §2-§3 | orta (yorum) |
| 3 | S7 (bilinmeyen) | `⚠️ VERIFICATION REQUIRED` | işaretli |
| 4 | upstream docs | Microsoft/POSIX dokümanları | yüksek (resmi kaynak) |

## §7 Kaynakça

### §7.1 Web doğrulama (2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Windows yerel kütüphane yükleme API'si `LoadLibraryW`'dir | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-loadlibraryw | 2026-10-13 | ✅ HTTP 200 |
| MSVC derleme seçenekleri kategoriye göre listelenir | https://learn.microsoft.com/en-us/cpp/build/reference/compiler-options-listed-by-category | 2026-10-13 | ✅ HTTP 200 |

### §7.2 Repo kanıtları özeti

| # | Dosya | Satır | Rol |
|---|---|---|---|
| 1 | `home.coremusic.net/.htaccess` | 18-37 | Apache yüzeyi (çift yazılım A) |
| 2 | `home.coremusic.net/web.config` | 14-41 | IIS yüzeyi (çift yazılım B) |
| 3 | `shared/src/Api/Bff/DesktopBff.php` | 34-37 | platform algılama |
| 4 | `shared/src/Component/TemplateEngine.php` | 73 | ayrac + geçici dizin |
| 5 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 199-202 | yol güvenliği + ayrac |
| 6 | `.github/workflows/ci.yml` | 27, 44 | platform + cache anahtarı |

### §7.3 İlişkili vault dosyaları

| # | Dosya | İlişki |
|---|---|---|
| 1 | `tasinabilirlik-siniri-anayasa.md` | §2-§4 karar ve eşik |
| 2 | `tasinabilirlik-siniri-ornekler.md` | §1-§3 çalışan örnekler |
| 3 | `../platform-yuzeyleri/` | fark matrisi |
| 4 | `../../isletim-sistemi-observability/` | log/sayaç devri |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
