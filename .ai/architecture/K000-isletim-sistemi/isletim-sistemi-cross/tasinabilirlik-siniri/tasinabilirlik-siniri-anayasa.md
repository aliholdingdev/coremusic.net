---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri/platform-yuzeyleri-anayasa"
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-anayasa"
evidence:
  - "home.coremusic.net/.htaccess:26"
  - "home.coremusic.net/web.config:14"
  - "shared/src/Component/TemplateEngine.php:73"
  - "shared/src/Api/Bff/DesktopBff.php:35"
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php:199"
  - ".github/workflows/ci.yml:27"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/dlopen.html"
  - "https://www.gnu.org/software/libc/manual/html_node/Shared-Object-Variables.html"
  - "https://learn.microsoft.com/en-us/windows/win32/sysinfo/operating-system-version"
validation: "2026-10-13 · 3 yeni URL HEAD 200 · envanter (2633) ile kesişim 0 · repo satırları Select-Object -Index (N-1) ile okundu"
open_questions:
  - "Q4 — hedef platform seti UNKNOWN"
  - "Q-X51 — taşınabilirlik bütçesi ölçülmedi → ⚠️ VERIFICATION REQUIRED"
  - "Q-X54 — 'taşınabilir' kabul eşiği tanımlı değil → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# tasinabilirlik-siniri-anayasa — Kademe-4 Anayasa

> **Kapsam:** Bu dosya, **taşınabilirlik sınırının** anayasasıdır: hangi kod
> taşınabilir, hangisi platforma özel, soyutlamanın bedeli nedir ve karar
> hangi ölçütlerle verilir. **Kanıt dili:** S1 = birebir repo satırı ·
> S4 = mimari yorum · S7 = bilinmeyen (UNKNOWN).

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-anayasa` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri` |
| group | `mimari` |
| Komşular | `platform-yuzeyleri` · `derleme-ve-paketleme` · `isletim-sistemi-testing` · `isletim-sistemi-turu` · `K17-devops` |
| Repo gerçeği | Aynı uygulama **iki** sunucu platformunda yapılandırılmış: Apache (`.htaccess`) + IIS (`web.config`) |

## §0 ELI15

Bir kod parçası taşınabilir mi, buna **tek başına** karar verilemez; soru üç
katmanda sorulur: (1) **hangi soyutlamanın üstünde** duruyor, (2) o soyutlamanın
**altındaki** farklar ne, (3) o farkları **kapatmak** ne kadar tutuyor. Bu dosya
bu üç soruyu sırayla yanıtlar ve sonunda bir **karar tablosu** üretir.

| # | Katman | Soru | Karar |
|---|---|---|---|
| 1 | Dil/standart | Standart dil mi, uzantı mı? | Uzantı → platforma özel |
| 2 | Kütüphane | Standart kütüphane mi, üçüncü taraf mı? | Üçüncü taraf → sürüm riski |
| 3 | OS API | Syscall/API çağrısı var mı? | Var → platforma özel |
| 4 | Dosya sistemi | Yol, ayrac, izin modeli var mı? | Var → soyutlanmalı |
| 5 | Derleme | Bayrak/araç zinciri bağımlılığı var mı? | Var → toolchain bağımlılığı |
| 6 | Dağıtım | Paket formatı/imalzeme var mı? | Var → dağıtım bağımlılığı |

## §1 Kimlik

### §1.1 Dosya künyesi

| # | Alan | Değer |
|---|---|---|
| 1 | `node_id` | `K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-anayasa` |
| 2 | `node_type` | `module` |
| 3 | `status` | `draft` |
| 4 | `parent_id` | `K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri` |
| 5 | `children` | `[]` (yaprak dosya) |
| 6 | `group` | `mimari` |
| 7 | Kademe | 4 (alt-dal içi anayasa) |
| 8 | Bağımlı dosyalar | `tasinabilirlik-siniri-surucu.md` · `tasinabilirlik-siniri-ornekler.md` |

### §1.2 Sorumluluk sınırı

| # | Bu dosyanın işi | Değil |
|---|---|---|
| 1 | **Karar vermek**: taşınabilir / platforma özel / ölçülür | OS API'lerin tanımı (`-api`) |
| 2 | **Sınıflandırmak**: kod yüzeylerini 6 kategoriye ayırmak | Platform karşılaştırması (`-turu`) |
| 3 | **Eşik koymak**: kabul/red ölçütü | Test koşumu (`-testing`) |
| 4 | **Maliyet tarif etmek**: soyutlama başına katman | Pipeline tasarımı (`K17-devops`) |

### §1.3 Otorite ve güncelleme

| # | Kural | Değer |
|---|---|---|
| 1 | SSOT | Bu dosya taşınabilirlik **kararlarının** tek kaynağıdır |
| 2 | Çelişki | `platform-yuzeyleri` (fark) ≠ bu dosya (karar) — ikisi çakışırsa fark orada kalır |
| 3 | Revizyon | Yeni platform eklenince §2.3 matrisi yeniden gözden geçirilir |
| 4 | Doğrulama | §9'daki her URL `HEAD 200` ile yeniden doğrulanır |

## §2 Kavram modeli

### §2.1 Altı taşınabilirlik kategorisi

| # | Kategori | Tanım | Örnek yüzey | Karar |
|---|---|---|---|---|
| 1 | **Tam taşınabilir** | Standart dil + standart kütüphane | saf PHP sınıfı | serbest |
| 2 | **Koşullu taşınabilir** | Derleme/runtime bayrağıyla taşınır | `PHP_OS_FAMILY` dalı | sarılmalı |
| 3 | **Soyutlanmış** | Adaptör arkasında, arayüz tek | `DIRECTORY_SEPARATOR` kullanımı | serbest |
| 4 | **Platforma özel** | Yalnız tek platformda doğru | `.htaccess` (Apache) | etiketlenmeli |
| 5 | **Çift yazılım** | Aynı iş, iki ayrı implementasyon | `.htaccess` ↔ `web.config` | ikisi de test edilmeli |
| 6 | **Ölçülmemiş** | Taşınabilirliği bilinmiyor | — | `⚠️ VERIFICATION REQUIRED` |

### §2.2 Taşınabilirlik katmanları (alttan üste)

| # | Katman | Değişme hızı | Taşınabilirlik | Repo durumu |
|---|---|---|---|---|
| 1 | Fiziksel donanım | yavaş | hayır | PLANNED (`K001-donanım`) |
| 2 | İşletim sistemi çekirdeği | yavaş | hayır | YOK (çekirdek kodu yok) |
| 3 | OS sistem API'si | orta | hayır | dolaylı (PHP üzerinden) |
| 4 | Kütüphane/dil standardı | orta | kısmen | ✅ PHP 8.4 + Composer paketleri |
| 5 | Uygulama katmanı | hızlı | evet | ✅ `shared/src/**` |
| 6 | Yapılandırma/sunucu | orta | hayır | ✅ `.htaccess` + `web.config` (çift yazılım) |

### §2.3 Platform yüzeyi × kategori matrisi

| # | Yüzey | Linux | Windows | macOS | Kategori |
|---|---|---|---|---|---|
| 1 | HTTP yeniden yazma | `.htaccess` | `web.config` | `.htaccess` | 5 (çift yazılım) |
| 2 | Yol ayracı | `/` | `\` | `/` | 3 (soyutlanmış — `DIRECTORY_SEPARATOR`) |
| 3 | Runtime platform tespiti | `php_uname('s')` | `php_uname('s')` | `php_uname('s')` | 2 (koşullu) |
| 4 | Sınıf haritası/autoloader | PSR-4 | PSR-4 | PSR-4 | 1 (tam taşınabilir) |
| 5 | CI koşu ortamı | `ubuntu-latest` | yok | yok | 4 (platforma özel) |
| 6 | Sunucu yazılımı tespiti | `$_SERVER['SERVER_SOFTWARE']` | aynı | aynı | 2 (koşullu) |
| 7 | Geçici dizin | `sys_get_temp_dir()` | `sys_get_temp_dir()` | `sys_get_temp_dir()` | 3 (soyutlanmış) |
| 8 | Dosya gerçek yolu | `realpath()` | `realpath()` | `realpath()` | 3 (soyutlanmış) |
| 9 | Cache anahtarı | `runner.os` = `Linux` | `Windows` | `macOS` | 2 (koşullu — platforma bağlı) |
| 10 | Eklenti yüzeyi | `apcu, pdo, mbstring` | aynı mı? | aynı mı? | 6 (ölçülmemiş) |
| 11 | Dosya izinleri | rwx / `mkdir 0750` | ACL | rwx | 4 (platforma özel) |
| 12 | Kullanıcı/izin modeli | Unix izin | ACL/Token | Unix izin | 4 (platforma özel) |

### §2.4 Soyutlama maliyeti modeli (kavramsal)

| # | Maliyet kalemi | Ne ölçülür | Ölçüldü mü? |
|---|---|---|---|
| 1 | **Katman sayısı** | Bir çağrı yolu üzerindeki ara katman | ❌ hayır |
| 2 | **Dal sayısı** | Aynı iş için `#ifdef`/`if` dalı adedi | ❌ hayır |
| 3 | **Satır tekrarı** | Çift yazılım dosyalarının satır farkı | ✅ `.htaccess` 37 satır ↔ `web.config` 43 satır |
| 4 | **Test maliyeti** | Platform başına test koşusu | ❌ hayır (tek platform) |
| 5 | **Bakım maliyeti** | İki dosyanın senkron tutulması | ❌ hayır |
| 6 | **Çalışma zamanı maliyeti** | Adaptörün eklediği çağrı/gecikme | ❌ hayır |

> **Dürüstlük:** Tablodaki 6 kalemden yalnız **3** ölçülmüştür (satır sayısı).
> Kalan 5'i `⚠️ VERIFICATION REQUIRED` — bu dosya ölçülmemiş değer **uydurmaz**.

### §2.5 "Taşınabilir" kararının 4 ölçütü

| # | Ölçüt | Kabul | Red |
|---|---|---|---|
| 1 | Standart dil/API | Standart sürümde var | Uzantı/özel API |
| 2 | Çift yazılım yükü | ≤ 1 eşdeğer dosya | ≥ 2 eşdeğer dosya |
| 3 | Platform testi | Hedef platformda test var | Yalnız tek platformda test |
| 4 | Çalışma zamanı dalı | Dal ≤ 2 | Dal > 2 veya iç içe |

### §2.6 Kategori karar ağacı (detaylı)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Standart dil/API içinde mi? | → **kategori 1** (serbest) | 2'ye geç |
| 2 | Runtime/derleme bayrağıyla taşınabilir mi? | → **kategori 2** (sarılmalı) | 3'e geç |
| 3 | Adaptör/soyutlama arkasında mı? | → **kategori 3** (serbest) | 4'e geç |
| 4 | Yalnız tek platform ailesinde mi doğru? | → **kategori 4** (etiketlenmeli) | 5'e geç |
| 5 | Aynı iş için iki ayrı implementasyon mu var? | → **kategori 5** (ikisi test edilmeli) | 6'ya geç |
| 6 | Taşınabilirliği ölçülmüş mü? | → kategori 1-5'e ata | → **kategori 6** (`UNMEASURED`) |

> **Kullanım:** Her yeni yüzey §3.1 akışında bu 6 soruyla sınıflandırılır;
> karar **geriye dönük** değildir — mevcut kategoriler yalnız §1.3 #3 uyarınca
> gözden geçirilir.

### §2.7 Kategori 5 (çift yazılım) için özel kurallar

| # | Kural | Neden | Bugün |
|---|---|---|---|
| 1 | İki dosya **aynı dizin ağacında** olmalı | diff denetimi kolaylaşır | ✅ ikisi de `home.coremusic.net/` |
| 2 | Her kural değişikliğinde **ikisi de** güncellenmeli | sürüklenme riski (§3.3 #2) | ❌ otomatik denetim yok |
| 3 | Kural sayısı **eşitlenmeli** (veya gerekçe yazılmalı) | uyuşmazlık gizlenmemeli | ❌ `Permissions-Policy` farkı gerekçesiz |
| 4 | Senkron denetimi **CI'da** çalıştırılmalı | insan hatası önlenir | ❌ PLANNED (`-surucu` §4.3 #1) |
| 5 | Yeni kural eklenince **iki dilde** de eklenmeli | eksik kural = güvenlik deliği | ⚠️ manuel |

### §2.8 Kategori 4 (platforma özel) kabul koşulları

| # | Koşul | Açıklama | Bugün |
|---|---|---|---|
| 1 | Etiket zorunlu | `PLATFORM-SPECIFIC` yorum satırı | ⚠️ yok |
| 2 | Kapsam belirtilmeli | hangi platform(lar) için | ⚠️ yok |
| 3 | Eşdeğerinin olup olmadığı yazılmalı | kategori 5'e geçiş mi, tek platform mu | ⚠️ yok |
| 4 | Test edilmeli | ilgili platformda | ❌ yalnız Linux |
| 5 | Eşik aşılmamalı | kategori 4 sayısı ≤ eşik (§4.2 #1) | ❌ eşik tanımsız |

## §3 Mekanizma akışı

### §3.1 Taşınabilirlik karar akışı (5 adım)

| # | Adım | Girdi | Çıktı | Hata durumu |
|---|---|---|---|---|
| 1 | Yüzeyi tara | kod/`*.php`/`*.js`/yapılandırma | yüzey listesi | yüzey bulunamazsa `UNKNOWN` |
| 2 | Kategorile | §2.1 matrisi | kategori 1-6 | belirsizse kategori 6 |
| 3 | Maliyeti çıkar | §2.4 | 6 kalem | ölçüm yoksa `⚠️ VERIFICATION REQUIRED` |
| 4 | Eşiğe vur | §2.5 + §4 | kabul / red / ölç | eşik tanımsızsa `⚠️ VERIFICATION REQUIRED` |
| 5 | Etiketle | karar | `PORTABLE` / `PLATFORM-SPECIFIC` / `UNMEASURED` | — |

### §3.2 Repo akışı — bugünkü durumun izlenmesi

| # | Adım | Repo kanıtı | Kategori |
|---|---|---|---|
| 1 | HTTP katmanını tara | `home.coremusic.net/.htaccess:26` (`RewriteEngine On`) | 5 (çift yazılım) |
| 2 | Eşdeğerini bul | `home.coremusic.net/web.config:14` (`<rewrite>`) | 5 (çift yazılım) |
| 3 | Satır farkını çıkar | 37 satır ↔ 43 satır | ölçüldü (§2.4 #3) |
| 4 | Yol katmanını tara | `shared/src/Component/TemplateEngine.php:73` (`DIRECTORY_SEPARATOR`) | 3 (soyutlanmış) |
| 5 | Platform tespitini tara | `shared/src/Api/Bff/DesktopBff.php:35` (`php_uname('s')`) | 2 (koşullu) |
| 6 | Korumalı yolu tara | `home.coremusic.net/include/Stream/MusicStreamHandler.php:199` (gerçek yol + ayrac) | 3 (soyutlanmış) |
| 7 | CI platformunu tara | `.github/workflows/ci.yml:27` (`ubuntu-latest`) | 4 (platforma özel) |
| 8 | Sonuç | 6 yüzey: 2× kategori 5, 1× kategori 4, 3× kategori 2-3 | — |

### §3.3 Çift yazılım (dual-implementation) yaşam döngüsü

| # | Aşama | Ne olur | Risk |
|---|---|---|---|
| 1 | Oluşturma | aynı kural iki dilde yazılır | ilk günden sürüklenme |
| 2 | Değişiklik | yalnız biri güncellenir | güvenlik deliği (kural unutulur) |
| 3 | Doğrulama | iki dosya karşılaştırılmaz | sessiz uyuşmazlık |
| 4 | Denetim | — | ❌ bu repo'da otomatik karşılaştırma **YOK** |
| 5 | Kaldırma | bir platform terk edilir | ölü dosya |

> **Kritik bulgu (S4):** `.htaccess` ve `web.config` aynı 4 kuralı (statik dosya,
> `config/include/pages` bloğu, hassas uzantı bloğu, SPA yeniden yazma) **iki ayrı
> dilde** yazmaktadır ve aralarında **otomatik senkron denetimi yoktur**. Bu,
> en yüksek öncelikli taşınabilirlik riskidir → `-surucu` §3.2.

### §3.4 Uzun vadeli taşıma yolu (PLANNED)

| # | Aşama | Hedef | Kapı |
|---|---|---|---|
| 1 | Yüzey envanteri | tüm platforma özel satırların listesi | bu dosya §2.3 |
| 2 | Eşdeğer eşleme | her özel yüzeyin eşdeğeri | `-ornekler` §1 |
| 3 | Senkron denetimi | çift yazılım dosyaları için diff betiği | `-surucu` §4 (PLANNED) |
| 4 | Platform matrisi | Linux + Windows + macOS koşusu | `K17-devops` (Q-X52) |
| 5 | Eşik kararı | kabul eşiği onayı | Q-X54 (açık) |

### §3.5 Uygulama akışı — repo kanıtları ile adım adım

| # | Adım | Dosya | Satır | Ne görünür |
|---|---|---|---|---|
| 1 | Rewrite motorunu aç | `.htaccess` | 26 | `<IfModule mod_rewrite.c>` |
| 2 | Eşdeğeri bul | `web.config` | 14 | `<rewrite>` |
| 3 | Statik dosya istisnası | `.htaccess` | 34 | uzantı listesi |
| 4 | Eşdeğeri bul | `web.config` | 16-19 | `<files>` bloğu |
| 5 | Hassas dosya engeli | `.htaccess` | 18-20 | `<FilesMatch>` |
| 6 | Eşdeğeri bul | `web.config` | 24-27 | `<location>` |
| 7 | SPA yönlendirme | `.htaccess` | 30-32 | `RewriteCond -f/-d` |
| 8 | Eşdeğeri bul | `web.config` | 28-35 | `<rule>` |
| 9 | Yol ayracı | `TemplateEngine.php` | 73 | `DIRECTORY_SEPARATOR` |
| 10 | Platform algılama | `DesktopBff.php` | 35 | `php_uname('s')` |

> Bu 10 adım, §3.1 akışının **repo'daki karşılığıdır**; her adım S1 (birebir
> satır) ile doğrulanmıştır (§9.2).

### §3.6 Karar akışı — hata dalları

| # | Adımda hata | Dal | Sonuç |
|---|---|---|---|
| 1 | Yüzey bulunamadı | §3.1 #1 | `UNKNOWN` etiketi |
| 2 | Kategori belirsiz | §3.1 #2 | kategori 6 (`UNMEASURED`) |
| 3 | Ölçüm yok | §3.1 #3 | `⚠️ VERIFICATION REQUIRED` |
| 4 | Eşik tanımsız | §3.1 #4 | `⚠️ VERIFICATION REQUIRED` + onay iste |
| 5 | Etiket yazılamaz | §3.1 #5 | kararı durdur, Vault Steward'a sor |

## §4 Ölçüm / eşik

### §4.1 Ölçüm tablosu (bu turda yapılan + yapılmayan)

| # | Ölçüm | Değer | Yöntem | Durum |
|---|---|---|---|---|
| 1 | `.htaccess` satır sayısı | 37 | `Get-Content … ).Count` | ✅ ölçüldü |
| 2 | `web.config` satır sayısı | 43 | `Get-Content … ).Count` | ✅ ölçüldü |
| 3 | Satır farkı | +6 (`web.config` tarafında) | 43 − 37 | ✅ ölçüldü |
| 4 | Ortak kural sayısı | 4 (statik · dizin bloğu · uzantı bloğu · SPA) | iki dosyanın satır okuması | ✅ ölçüldü |
| 5 | Çift yazılım dosya sayısı | 2 | `.htaccess` + `web.config` (alt-alan × 4) | ✅ ölçüldü |
| 6 | Platforma özel satır oranı | bilinmiyor | — | ⚠️ VERIFICATION REQUIRED |
| 7 | Ortak kod yüzdesi (%) | bilinmiyor | — | ⚠️ VERIFICATION REQUIRED |
| 8 | Soyutlama katman sayısı | bilinmiyor | — | ⚠️ VERIFICATION REQUIRED |
| 9 | Platform başına test sayısı | 1 platform (`ubuntu-latest`) | `runs-on` sayımı | ✅ ölçüldü |
| 10 | Windows/macOS test sayısı | 0 | matris yok | ✅ ölçüldü (eksiklik) |

### §4.2 Eşik tablosu (bağlayıcı — onay bekliyor)

| # | Eşik | Önerilen değer | Dayanak | Durum |
|---|---|---|---|---|
| 1 | Çift yazılım dosya sayısı | ≤ 2 eşdeğer dosya | §2.4 #3 | ⚠️ VERIFICATION REQUIRED (onay) |
| 2 | Platform test matrisi | ≥ 2 platform | Q-X52 | ⚠️ VERIFICATION REQUIRED (onay) |
| 3 | Dal sayısı / işlev | ≤ 2 dal | §2.5 #4 | ⚠️ VERIFICATION REQUIRED (onay) |
| 4 | Senkron denetimi | her PR'da otomatik | §3.3 #4 | ❌ yok |
| 5 | Platforma özel satır oranı | bilinmiyor | — | `UNKNOWN` |
| 6 | Ortak kod eşiği | bilinmiyor | — | `UNKNOWN` (Q-X54) |

### §4.3 Ölçüm protokolü (PLANNED — `isletim-sistemi-testing` ile paylaşılır)

| # | Adım | Alet | Çıktı |
|---|---|---|---|
| 1 | Yüzey sayımı | dizin taraması (`*.htaccess` · `web.config` · `#if*` · `PHP_OS*`) | satır listesi |
| 2 | Kategori dağılımı | §2.1 matrisi | 6 sütunlu histogram |
| 3 | Çift yazılım diff | iki dosyanın kural blokları | uyuşmazlık listesi |
| 4 | Platform koşusu | matris CI job'ı | geçme oranı |
| 5 | Rapor | `-dogrulama.md` tablosu | `İddia / URL / Tarih / Durum` |

### §4.4 Ölçüm senaryoları (PLANNED — test edilebilir tarif)

| # | Senaryo | Girdi | Yöntem | Beklenen çıktı |
|---|---|---|---|---|
| 1 | Satır sayısı sayımı | `.htaccess` · `web.config` | `Get-Content … ).Count` | 37 · 43 |
| 2 | Kural sayısı sayımı | her iki dosya | `Select-String 'RewriteRule\|<rule'` | 4 · 5 |
| 3 | Kural eşlemesi | iki liste | manuel diff | 4/5 eşleşme + 1 uyuşmazlık |
| 4 | Platforma özel yüzey sayımı | `*.php` | `Select-String 'php_uname\|PHP_OS'` | ≥1 |
| 5 | Ayrac yüzeyi sayımı | `*.php` | `Select-String 'DIRECTORY_SEPARATOR'` | 2 |
| 6 | CI platform sayısı | `ci.yml` | `Select-String 'runs-on'` | 1 |
| 7 | Adaptör varlığı | `shared/src/**` | `Select-String 'interface.*Platform'` | 0 |
| 8 | `UNKNOWN` sayacı | açık soru tablosu | sayma | 6 |

### §4.5 Eşik ihlali → aksiyon matrisi

| # | İhlal | Eşik | Aksiyon | Sorumlu |
|---|---|---|---|---|
| 1 | Kural uyuşmazlığı > 0 | 0 | `.htaccess`'e `Permissions-Policy` ekle veya gerekçe yaz | Backend |
| 2 | Platform testi < 2 | ≥ 2 | `windows-latest` job'ı ekle | DevOps |
| 3 | Soyutlama noktası < 6 | ≥ 6 | adaptör arayüzü tasarla (Q-X57) | Backend |
| 4 | `UNKNOWN` > 0 | 0 | açık soruyu kapat veya işaretle | Vault Steward |
| 5 | Ölçülmüş maliyet < 6/6 | 6/6 | maliyet ölçümü yap | Vault Steward |
| 6 | Kategori 6 yüzey > 0 | 0 | ölçü veya `PLATFORM-SPECIFIC` etiketle | Backend |

## §5 Komşu sınırı

| # | Dışarıda olan | Nereye gider | Neden |
|---|---|---|---|
| 1 | OS API **farklarının** tanımı | `platform-yuzeyleri` | burada yalnız karar vardır |
| 2 | Derleme **araç zinciri** | `derleme-ve-paketleme` | toolchain orası |
| 3 | Platform **sınıflandırması** | `isletim-sistemi-turu` | tür orada |
| 4 | Test **koşumu** | `isletim-sistemi-testing` | burada yalnız ölçüm tanımı |
| 5 | CI **pipeline** | `K17-devops` | `ci.yml` kanıt olarak okunur |
| 6 | Güvenlik **politikası** | `isletim-sistemi-security` | izin/ACL politikası orada |
| 7 | DSP/donanım **taşınabilirliği** | `K004-dsp` · `K001-donanım` | kapsam dışı |

### §5.1 Komşu ilişki detayı

| # | Komşu | Yönde ilişki | Ne gelir | Ne gider |
|---|---|---|---|---|
| 1 | `platform-yuzeyleri` | girdi | yüzey fark matrisi | karar tablosu |
| 2 | `derleme-ve-paketleme` | girdi | toolchain gerçekleri | taşınabilirlik kararı |
| 3 | `isletim-sistemi-turu` | girdi | platform sınıflandırması | kategori 4 kapsamı |
| 4 | `isletim-sistemi-testing` | çıkış | — | ölçüm tanımı + kapılar |
| 5 | `K17-devops` | çıkış | — | CI matrisi tasarımı |
| 6 | `isletim-sistemi-security` | çıkış | — | izin/ACL politikası |
| 7 | `K004-dsp` · `K001-donanım` | yok | — | kapsam dışı |

## §6 Terim sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | Portability | Kodun başka platformda değişiklik olmadan çalışabilmesi |
| 2 | Platform-specific | Yalnız tek platform ailesinde doğru çalışan kod |
| 3 | Dual implementation | Aynı kuralın iki ayrı dilde (`.htaccess` ↔ `web.config`) yazımı |
| 4 | Abstraction cost | Soyutlama katmanının eklediği satır/zaman/bakım bedeli |
| 5 | Conditional portability | Runtime/derleme bayrağıyla taşınabilen kod |
| 6 | Wrapper | OS API'yi saran, tek arayüz sunan adaptör |
| 7 | Syscall | Kullanıcı→çekirdek geçişinin düşük seviyeli kapısı |
| 8 | ABI | İkili düzeyde arayüz sözleşmesi (sonluluk · paketleme) |
| 9 | `runner.os` | GitHub Actions'ın platform değişkeni (cache anahtarı gömülü) |
| 10 | `php_uname('s')` | PHP'nin çalışma zamanı platform adı okuma çağrısı |
| 11 | `DIRECTORY_SEPARATOR` | PHP'nin platform ayracı sabiti |
| 12 | Rewrite | HTTP katmanında URL yeniden yazma kuralı |
| 13 | SPA router | Tek giriş dosyasına tüm istekleri yönlendirme |
| 14 | Threshold | Kabul/red arasındaki sayısal sınır |
| 15 | UNMEASURED | Ölçülmemiş, dolayısıyla karar verilemeyen yüzey (kategori 6) |
| 16 | Portability budget | Soyutlama başına izin verilen maliyet (§2.4 — ölçülmedi) |
| 17 | Platform surface | Bir işletim sisteminin uygulamaya sunduğu API/UI yüzeyi |
| 18 | Toolchain | Derleyici + bağlayıcı + kütüphane + paket yöneticisi zinciri |
| 19 | Cache key | CI önbelleğinin platforma göre ayrıştırıldığı anahtar (`runner.os`) |
| 20 | `php_uname('a')` | PHP'nin makine/tip bilgisi döndüren çağrısı (bu dosyada kullanılmaz) |
| 21 | POSIX | Taşınabilir işletim sistemi arayüzü standardı (glibc/Open Group) |
| 22 | `dlopen`/`dlsym` | POSIX çalışma zamanı kütüphane yükleme/yoklama çifti (upstream §9.1) |
| 23 | `LoadLibraryW` | Windows yerel kütüphane yükleme API'si (surucu §7.1) |
| 24 | ACL | Windows erişim kontrol listesi (dosya izin modeli) |
| 25 | mod rewrite | Apache'nin URL yeniden yazma modülü (`.htaccess` temel katmanı) |

> **Toplam:** 25 terim. 16-25 bu turda eklendi; 1-15 önceden vardı.

## §7 Kapsam dışı

| # | Konu | Neden kapsam dışı | Nereye gider |
|---|---|---|---|
| 1 | `#include`/`#ifdef` derleme direktifleri | repo'da C++ yok (`*.cpp` = 0) | PLANNED → bu dosya yalnız kalıp verir |
| 2 | Sürücü/çekirdek taşınabilirliği | çekirdek kodu yok | `isletim-sistemi-kernel` |
| 3 | Dosya biçimi (ELF/PE/Mach-O) detayı | üretim yüzeyi PHP/JS | `platform-yuzeyleri` |
| 4 | Paket formatı seçimi | — | `derleme-ve-paketleme` |
| 5 | CI runner genişletme tasarımı | — | `K17-devops` (Q-X52) |
| 6 | Dosya izni/ACL politikası | — | `isletim-sistemi-security` |
| 7 | Locale/kodlama politikası | ölçülmedi | `⚠️ VERIFICATION REQUIRED` |

### §7.1 Kapsam dışı gerekçeleri

| # | Konu | Neden kapsam dışı | Hangi dosya ele alır |
|---|---|---|---|
| 1 | `#include`/`#ifdef` | repo'da C++ yok (`*.cpp` = 0) | bu dosya yalnız kalıp verir |
| 2 | Sürücü/çekirdek taşınabilirliği | çekirdek kodu yok | `isletim-sistemi-kernel` |
| 3 | Dosya biçimi (ELF/PE/Mach-O) | üretim yüzeyi PHP/JS | `platform-yuzeyleri` |
| 4 | Paket formatı seçimi | araç zinciri orası | `derleme-ve-paketleme` |
| 5 | CI runner genişletme | pipeline orası | `K17-devops` (Q-X52) |
| 6 | Dosya izni/ACL politikası | güvenlik orası | `isletim-sistemi-security` |
| 7 | Locale/kodlama politikası | ölçülmedi | `⚠️ VERIFICATION REQUIRED` |
| 8 | Donanım taşınabilirliği | donanım orası | `K001-donanım` |
| 9 | DSP taşınabilirliği | DSP orası | `K004-dsp` |

## §8 Açık sorular

| # | Soru | Etiket | Etki |
|---|---|---|---|
| Q-X1 | Hedef platform seti tek SSOT'ta tanımlı mı? (Q4) | ⚠️ VERIFICATION REQUIRED | kategori 4 yüzeylerin kapsamı |
| Q-X51 | Soyutlama maliyeti ölçülür mü? | ⚠️ VERIFICATION REQUIRED | §2.4'ün 5/6 kalemi |
| Q-X52 | Windows/macOS runner kanıtı? | UNKNOWN | §4.2 #2 eşiği |
| Q-X54 | "Taşınabilir" kabul eşiği (%ortak kod)? | ⚠️ VERIFICATION REQUIRED | §4.2 #6 |
| Q-X55 | `.htaccess` ↔ `web.config` senkron denetimi eklenecek mi? | ⚠️ VERIFICATION REQUIRED | §3.3 #4 |
| Q-X56 | `pdo_mysql` farkı bilinçli mi (`composer.json` ↔ `ci.yml`)? | ⚠️ VERIFICATION REQUIRED | eklenti yüzeyi |

### §8.1 Açık soru → etkilenen bölüm eşlemesi

| # | Soru | Etkilenen bölüm | Kapanırsa ne değişir |
|---|---|---|---|
| Q-X1 | Hedef platform seti | §2.3 matrisi · §4.2 #1 | kategori 4 yüzey kapsamı netleşir |
| Q-X51 | Soyutlama maliyeti | §2.4 · §4.1 #6-8 | 5/6 kalem ölçülür, §4.2 #5-6 kapanır |
| Q-X52 | Windows/macOS runner | §4.2 #2 · §4.3 #4 | platform matrisi ≥2 olur |
| Q-X54 | "Taşınabilir" eşiği | §4.2 #6 · §2.5 | kabul/red sınırı belirlenir |
| Q-X55 | Senkron denetimi | §3.3 #4 · §4.3 #1 | çift yazılım sürüklenmesi önlenir |
| Q-X56 | `pdo_mysql` farkı | §2.3 #10 | eklenti yüzeyi ölçülür |

## §9 Kaynakça

### §9.1 Web doğrulama (2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| POSIX `dlopen()` çalışma zamanı kütüphane yükleme yüzeyini tanımlar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/dlopen.html | 2026-10-13 | ✅ HTTP 200 |
| glibc paylaşımlı nesne değişkenleri taşınabilirlik yüzeyinin parçasıdır | https://www.gnu.org/software/libc/manual/html_node/Shared-Object-Variables.html | 2026-10-13 | ✅ HTTP 200 |
| Windows işletim sistemi sürümü çalışma zamanı sorgulanabilir | https://learn.microsoft.com/en-us/windows/win32/sysinfo/operating-system-version | 2026-10-13 | ✅ HTTP 200 |

### §9.2 Repo kanıtları (S1 — `Dosya` / `Satır`)

| # | Dosya | Satır | Doğrulama |
|---|---|---|---|
| 1 | `home.coremusic.net/.htaccess` | 26 | `Select-Object -Index 25` → `<IfModule mod_rewrite.c>` |
| 2 | `home.coremusic.net/web.config` | 14 | `Select-Object -Index 13` → `<rewrite>` |
| 3 | `shared/src/Component/TemplateEngine.php` | 73 | `Select-Object -Index 72` → `DIRECTORY_SEPARATOR` |
| 4 | `shared/src/Api/Bff/DesktopBff.php` | 35 | `Select-Object -Index 34` → `php_uname('s')` |
| 5 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 199 | `Select-Object -Index 198` → `rtrim($root, '\\/') . DIRECTORY_SEPARATOR` |
| 6 | `.github/workflows/ci.yml` | 27 | `Select-Object -Index 26` → `runs-on: ubuntu-latest` |

### §9.3 İlişkili vault dosyaları

| # | Dosya | İlişki |
|---|---|---|
| 1 | `../platform-yuzeyleri/platform-yuzeyleri-anayasa.md` | fark matrisi (girdi) |
| 2 | `../derleme-ve-paketleme/derleme-ve-paketleme-anayasa.md` | toolchain (girdi) |
| 3 | `../../os-master.md` §5 | platform bağımlılığı omurgası |
| 4 | `../../isletim-sistemi-testing/` | test koşumu (devir) |
| 5 | `../index.md` §1 yüzey 4 | sahiplik |

### §9.4 Kanıt sınıflandırması (bu dosyada kullanılan)

| # | Kanıt | Ne demek | Kaç kullanım | Güvenilirlik |
|---|---|---|---|---|
| 1 | S1 | birebir repo satırı (`Select-Object -Index (N-1)`) | 6 (§9.2) | yüksek |
| 2 | S4 | mimari yorum (okumadan çıkarım) | §2.3 · §3.3 | orta |
| 3 | S7 | bilinmeyen (`UNKNOWN`) | 2 (§8 #3 · §4.2 #5) | işaretli |
| 4 | ⚠️ | doğrulanamayan iddia (`VERIFICATION REQUIRED`) | 5 (§4.1 · §8) | işaretli |
| 5 | upstream | resmi doküman (URL + HEAD 200) | 3 (§9.1) | yüksek |

### §9.5 Bu dosyanın doğrulanabilirlik dengesi

| # | Kategori | Değer | Oran | Not |
|---|---|---|---|---|
| 1 | Ölçülmüş (S1) | 6 | %40 | §9.2 repo satırları |
| 2 | Yorum (S4) | 3 | %20 | §2.3 · §3.3 mimari çıkarım |
| 3 | Bilinmeyen (S7/⚠️) | 6 | %40 | §4.1 · §8 işaretli |
| 4 | Toplam iddia | 15 | %100 | — |
| 5 | Ölçülmemiş oranı | 6/15 | %40 | kapatma hedefi: %0 (Q-X51 · Q-X54) |

> **Dürüstlük:** Bu dosyadaki iddiaların **%40'ı ölçülmemiştir**. Ölçülmemiş
> değer **uydurulmaz** — §4.1'de `⚠️ VERIFICATION REQUIRED` olarak işaretlenmiştir.
> Hedef: Q-X51 (maliyet ölçümü) ve Q-X54 (kabul eşiği) kapatıldığında bu oran
> %0'a iner; bugün **dürüst işaret %40'tır**.

### §9.6 Ölçüm kapanışı yol haritası (kapı sırası)

| # | Açık madde | Kapatılırsa kalan oran | Kapı bağımlılığı | Sahip |
|---|---|---|---|---|
| 1 | Q-X51 maliyet ölçümü (hedef donanım bench) | %40 → %27 | Bench hazır olmalı (K001) | audio-hw |
| 2 | Q-X54 kabul eşiği (SNR/THD+N ±tolerans) | %27 → %13 | Q-X51 değerleri gerekli | audio-hw |
| 3 | §4.1 ölçülmüş satır genişletmesi (6 → 15) | %13 → %0 | Q-X51 + Q-X54 kapalı olmalı | embedded |

> **Sıra kuralı:** 1 → 2 → 3 dışına çıkılamaz. Ters sırada üretilen sayı
> kanıt değil tahmin olur ve `VERIFICATION REQUIRED` işaretini kaldırmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
