---
node_id: K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari/bellek-kavramlari-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari
evidence:
  - https://man7.org/linux/man-pages/man5/core.5.html
  - shared/src/Cache/MemoryAdapter.php:25
  - shared/src/Cache/ApcuAdapter.php:22
validation: "2026-10-14 · §17 grupları G4a/G4b/G5 (12 platform kolu · 8 kod bloğu) · §17.4 8 YENİ HTTP 200 (envanter kesişimi 0) · §17.5 14 reuse yeniden 200 · §18 PHP köprüsü (3 gerçek-satır bloğu · yeni URL 0 · memory_get_usage/gc_collect_cycles/shm_open/mmap/error_get_last/mt_rand grep 0 → §18.6) · §19 çok dilli JS/C/Python/Go/Rust/C# (5 URL = 1 reuse + 4 yeni · 5 kalıp blok · JS depo satırı ContentFetcher/Logger) · 968 satır · repo satırları ReadAllLines ile birebir · olumsuz kanıt grep 0 isabet"
open_questions:
  - "PHP `memory_get_usage` çağrısı repoda hiç yok mu? → grep 0 isabet (Q-B10) · 2026-10-14 §18.6 ile YENİDEN DOĞRULANDI: 0 isabet, kesin; 'gerçek eksiklik mi karar mı?' hâlâ açık"
  - "APCu TTL/limit üretim değeri → UNKNOWN (Q-B11)"
  - "Core dump açık mı? → UNKNOWN (ortam okunmadı, Q-B12)"
group: kod
---

# bellek-kavramlari-ornekler — Repo Örnekleri ve Anti-Pattern (Kademe-5)

## §0 ELI15

"Bu projede bellek gerçekten nasıl kullanılıyor?" sorusunun cevabı: repoda
bellek yüzeyi **iki önbellek sınıfında** toplanır — süreç içi dizi deposu
(`MemoryAdapter`) ve süreçler arası paylaşımlı bellek (`ApcuAdapter`); ikisi
de **ölçülmemektedir** (`memory_get_usage` grep 0 isabet). Bu dosya bu iki
örneği satır kanıtıyla açar, doğru kullanım/anti-pattern ayırır ve ölçüm
eksikliğinin olumsuz kanıtını taşır.

## §1 Repo örnekleri — çalışan önbellek sınıfları

### §1.1 `MemoryAdapter` — süreç içi dizi deposu

| Dosya | Satır | Ne görülür | Yorum |
|---|---|---|---|
| `shared/src/Cache/MemoryAdapter.php` | 8 | sınıf/`namespace` sınırı | depo kimliği |
| `shared/src/Cache/MemoryAdapter.php` | 25–28 | değeri diziye yazma | tahsis yüzeyi |
| `shared/src/Cache/MemoryAdapter.php` | 49 | değeri okuma | okuma yüzeyi |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| ömür | süreç içi (betik sonu) | sınıf amacı |
| dayanıklılık | kalıcı **değil** | dosya/DB değil |
| kapasite sınırı | kodda tanımlı **görünmüyor** | satır taraması |
| ölçüm | **yok** | `memory_get_usage` grep 0 |
| serbest bırakma | PHP motoru (betik sonu) | genel PHP davranışı |

**Doğru kullanım:** Kısa ömürlü, geçici, tekrar kurulabilir veri için;
kayıp kabul edilebilir olduğunda. **Yanlış kullanım:** Kalıcı veri, tek kaynak
olgusu, kapasitesiz büyük koleksiyon.

### §1.2 `ApcuAdapter` — paylaşımlı bellek (APC/user cache)

| Dosya | Satır | Ne görülür | Yorum |
|---|---|---|---|
| `shared/src/Cache/ApcuAdapter.php` | 13 | `apcu` arayüzüne bağımlılık | paylaşımlı sağlayıcı |
| `shared/src/Cache/ApcuAdapter.php` | 22 | önbelleğe yazma | süreçler arası tahsis |
| `shared/src/Cache/ApcuAdapter.php` | 38 | önbellekten okuma | paylaşılan okuma |

**Davranış özeti:**

| Alan | Değer | Kaynak |
|---|---|---|
| ömür | süreçler arası (paylaşımlı) | APCu modeli |
| dayanıklılık | süreç sonuyla biter | APCu (veritabanı değil) |
| kapasite | sunucu yapılandırması | `UNKNOWN` — okunmadı |
| TTL | kodda mevcut mu? | `UNKNOWN` — tam dosya taraması yapılmadı |
| ölçüm | **yok** | `apc_sma_info` grep 0 isabet |

**Doğru kullanım:** Çok okunan, az yazılan, TTL ile tazelenen paylaşılan
veri (yapılandırma, oturum dışı önbellek). **Yanlış kullanım:** TTL'siz
büyük nesneler, tek süreçlik veri, sıra/transaction verisi.

### §1.3 Önbellek sınıfları karşılaştırması

| Kriter | `MemoryAdapter` | `ApcuAdapter` |
|---|---|---|
| kapsam | tek süreç | tüm süreçler |
| kalıcılık | yok | yok (süreç sonu) |
| hız | çok hızlı (bellek) | hızlı (paylaşımlı bellek) |
| kapasite riski | PHP `memory_limit` | APCu bellek limiti |
| arıza modu | H1/H4 (anayasa/surucu §3) | H3 |
| ölçüm | `memory_get_usage` (yok) | APCu sayaçları (yok) |
| repo kanıtı | `:8 · :25–28 · :49` | `:13 · :22 · :38` |

## §2 Ölçüm eksikliğinin olumsuz kanıtı

| # | Aranan | Kapsam | İsabet | Yorum |
|---|---|---|---|---|
| O1 | `memory_get_usage` | tüm `*.php` | 0 | süreç içi ölçüm yok |
| O2 | `memory_get_peak_usage` | tüm `*.php` | 0 | zirve ölçümü yok |
| O3 | `apc_sma_info` | tüm `*.php` | 0 | APCu kapasite okuması yok |
| O4 | `memory_limit` | tüm `*.php` | 0 | kod içi limit denetimi yok |
| O5 | `pcntl` / `nice` / `umask` | tüm `*.php` | 0 | süreç/süreç-öncelik kodu yok |

**Dürüstlük cümlesi:** 0 isabet "**repo'da o kod yok**" der; "**özellik hiç
kullanılmaz**" **demez**. Bu dosyadaki iddialar yalnızca grep kapsamıyla
sınırlıdır (2026-10-13, tüm `*.php`).

### §2.1 Ölçüm eksikliğinin sonucu — ne yapılamıyor

| Soru | Cevap | Neden |
|---|---|---|
| zirve bellek kaç bayt? | `UNKNOWN` | O2 = 0 |
| limit aşımı riski var mı? | `UNKNOWN` | O1/O4 = 0 |
| APCu doluluk oranı? | `UNKNOWN` | O3 = 0 |
| hangi istek en pahalı? | `UNKNOWN` | ölçüm yok |
| alarm eşiği ne olmalı? | yazılmaz | İlke B10 (anayasa) |

## §3 Doğru kullanım örnekleri — bu repoda görülen örüntüler

| # | Örüntü | Repo kanıtı | Neden doğru |
|---|---|---|---|
| D1 | önbellek arayüzünü soyutlama | `MemoryAdapter` / `ApcuAdapter` ayrı sınıflar | sağlayıcı değişimi tek yerden |
| D2 | dosyayı zamanında kapatma | `FileHandler.php:251` | tutucu serbest bırakılıyor |
| D3 | hassas veriyi maskeleme | log redaksiyonu (log dalı) | bellekte kopya bilinçli |
| D4 | önbelleğe kısa yanıt yazma | `ApcuAdapter:22` | paylaşımlı tahsis kontrollü |
| D5 | ölçüm yoksa eşik yazmama | bu dalın tabloları (`UNKNOWN`) | uydurma değer yok |

**Örnek kuralı:** "Doğru" burada **mimari uygunluk** demektir; performans
doğruluğu ölçülmediği için **`UNKNOWN`** kalır (ölçüm yok → doğrulama yok).

### §3.1 Örnek kod akışı — önbellek yaz/oku (kavramsal alıntı)

```php
// Kavramsal akıntı — repo satırları §1.1/§1.2'de işaretlidir, kopyalanmaz.
// 1) anahtar üret  2) yaz (tahsis)  3) oku (erişim)  4) TTL/ömür sonu
```

| Adım | Bellek etkisi | Repo karşılığı |
|---|---|---|
| 1 anahtar | küçük tahsis | `MemoryAdapter` iç kullanımı |
| 2 yazma | tahsis (dizi/paylaşımlı) | `:25–28` / `:22` |
| 3 okuma | tahsis yok (okuma) | `:49` / `:38` |
| 4 son | betik sonu / TTL | PHP motoru / APCu |

**Alıntı kuralı:** Blok **kavramsal akıştır**; gerçek kod satırı dosyada
okunur, buraya kopyalanmaz (yalnız satır numarasıyla işaretlenir).

## §4 Anti-pattern'ler — yapılmaması gerekenler

| # | Anti-pattern | Sonuç | İlgili hata modu |
|---|---|---|---|
| A1 | ölçüm olmadan limit koymak | yanlış alarm/yanlış güvenlik | H8 |
| A2 | paylaşımlı belleğe büyük veri yazmak | APCu taşması | H3 |
| A3 | TTL'siz paylaşımlı önbellek | kalıntı veri, tutarsızlık | H3 |
| A4 | açık dosya/tutucu bırakma | süreç boyunca kaynak tutma | H2 |
| A5 | diziye tekrar tekrar ekleme (kuyruk) | zirve taşması/parçalanma | H1/H4 |
| A6 | kopyalama noktasını görmezden gelme | tahsis iki katına çıkar | §2.7 (surucu) |
| A7 | Windows/Linux ölçümünü eşitlemek | yanlış yorum (İlke B11) | H8 |
| A8 | 0 isabeti "yok" diye genişletmek | zero-hallucination ihlali | — |

### §4.1 Anti-pattern → repo korelasyonu

| Anti-pattern | Bu repoda var mı? | Kanıt | Sonuç |
|---|---|---|---|
| A1 | evet (kanıt: ölçüm yok) | O1–O4 = 0 | ölçüm PLANNED |
| A2 | `UNKNOWN` | kapasite okunmadı | Q-B11 |
| A3 | `UNKNOWN` | TTL satırı tam okunmadı | Q-B11 |
| A4 | hayır (kapatma var) | `FileHandler.php:251` | uygun |
| A5 | `UNKNOWN` | kuyruk kodu ölçülmedi | ölçüm PLANNED |
| A6 | `UNKNOWN` | kopya ölçümü yok | ölçüm PLANNED |
| A7 | hayır | eşleştirme kodu yok | — |
| A8 | hayır (bu dosyada) | `UNKNOWN`/`⚠️` işaretli | korunuyor |

**Korelasyon kuralı:** "Var mı?" hücreleri **kanıtla** cevaplanır; kanıt
yoksa `UNKNOWN` yazılır, "yok" **yazılmaz**.

## §5 Açık sorular ve ölçüm planı

| # | Soru | Etiket | Kapanış yolu |
|---|---|---|---|
| Q-B10 | ölçüm kodu hiç mi yazılmayacak? | PLANNED | ölçüm eklemesi + test |
| Q-B11 | APCu TTL/limit üretim değeri? | UNKNOWN | ortam/prod okuması |
| Q-B12 | core dump açık mı (crash teşhisi)? | UNKNOWN | ortam okuması (§6) |
| Q-B13 | en pahalı istek hangisi? | PLANNED | istek başına ölçüm |
| Q-B14 | `MemoryAdapter` kullanımı hangi çağrı zincirinde? | ⚠️ VERIFICATION REQUIRED | tam çağrı grafiği taraması |

### §5.1 Ölçüm planı — sırayla ne yapılacak

| Adım | Eylem | Araç | Beklenen çıktı |
|---|---|---|---|
| 1 | `memory_get_usage(true)` ekleme tasarımı | PHP | bayt cinsinden okuma |
| 2 | istek başına zirve kaydı | log | `UNKNOWN` → sayı |
| 3 | APCu doluluk okuması | APCu sayaçları | bayt cinsinden doluluk |
| 4 | örneklem toplama | monitor | dağılım |
| 5 | eşik üretimi | §5.3 (surucu) | meşru eşik |
| 6 | alarm bağlama | monitoring dalı | alarm kuralı |

**Plan durumu:** Adımlar 1–6 **PLANNED**'dir; hiçbiri bu turda
çalıştırılmadı. Beklenen çıktı sütunu hedef **türünü** verir, değer **değil**.

### §5.2 Core dump ile teşhis — kavram (çalıştırılmadı)

| Konu | Bilgi | Kaynak |
|---|---|---|
| core dosyası | süreç anında sonlandığında bellek görüntüsü | §6 URL |
| ne zaman oluşur | çekirdek politikasına bağlı | `UNKNOWN` — ortam okunmadı |
| repo'da karşılığı | yok (PHP katmanı) | grep 0 |
| kullanım | çökme sonrası satır/teşhis | test dalı |

**Durum:** Core dump bu ortamda **açık mı bilinmiyor** (Q-B12); dosya yalnız
kavramı taşır, "açık" iddiası **yazılmaz**.

## §6 Kaynakça — 1 URL, HTTP HEAD 200, envanter kesişimi 0

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| O-W1 | `core(5)` — çekirdek (core) dosyası oluşumu ve içeriği (§5.2) | https://man7.org/linux/man-pages/man5/core.5.html | 2026-10-13 | 200 |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
→ HTTP 200 (2026-10-13). Envanter kesişimi: `.ai/architecture/**` benzersiz
URL kümeleriyle karşılaştırıldı → **0**. `-anayasa` (memfd_create · msync ·
swapon) ve `-surucu` (virtualfree · heapalloc · getprocessmemoryinfo) kendi
URL'lerini ayrı taşır.

## §7 Derin örnekler — önbellek ömür döngüsü adım adım

### §7.1 İstek içi ömür (MemoryAdapter) — adım tablosu

| Adım | Olay | Bellek etkisi | Ölçüm | Repo |
|---|---|---|---|---|
| 1 | istek başlar | (PHP süreci hazır) | yok | — |
| 2 | `MemoryAdapter` üzerinden yazma | dizi slotu tahsisi | yok | `:25–28` |
| 3 | okuma | tahsis yok | yok | `:49` |
| 4 | istek biter | betik değişkenleri düşer | yok | — |
| 5 | süreç yaşarsa | önbellek de yaşar | yok | — |

**Önemli ayrım:** Adım 4 yalnızca **betik** değişkenlerini düşürür; PHP
süreci (worker) ayaktaysa sınıf üyesi/`static` veri süreç boyunca kalabilir.
Kalıp-kalma (persistence) `UNKNOWN` — tam dosya okunmadı (Q-B14).

### §7.2 Paylaşımlı ömür (ApcuAdapter) — adım tablosu

| Adım | Olay | Kapsam | Ölçüm | Repo |
|---|---|---|---|---|
| 1 | süreç A yazar | tüm süreçler görür | yok | `:22` |
| 2 | süreç B okur | paylaşılan okuma | yok | `:38` |
| 3 | TTL/doluluk | APCu yönetir | yok | `UNKNOWN` |
| 4 | süreç A kapanır | veri **kalır** (paylaşımlı) | yok | — |
| 5 | süreç sonu hepsi | paylaşımlı alan boşalır | yok | — |

**Ayrım kuralı:** `MemoryAdapter` verisi 4. adımda **düşmeyebilir** (süreç
yaşadıkça), `ApcuAdapter` verisi 4. adımda **düşmez** (süreç A kapansa bile).
İki ömür karıştırılırsa yanlış kapasite hesabı doğar.

### §7.3 İki adapter'ın birlikte kullanımı — hiyerarşi

| Sıra | Katman | Ne tutar | Eşik |
|---|---|---|---|
| 1 | `ApcuAdapter` | paylaşılan, TTL'li | `UNKNOWN` |
| 2 | `MemoryAdapter` | istek içi geçici | `UNKNOWN` |
| 3 | PHP motoru | betik değişkenleri | `memory_limit` (php.ini) |
| 4 | OS | süreç/çerçeve | OOM (§2.4 surucu) |

**Hiyerarşi kuralı:** Düşük katman dolarsa üst katman çalışmaz ama **görmez**;
arıza tespiti her katmanda ayrı ölçüm ister — bu repoda hiçbir katta ölçüm
**yok** (§2 O1–O5).

## §8 Örnek × hata modu matrisi — hangi örnek hangi arızaya gebe

| Örnek ↓ / Hata → | H1 (zirve) | H2 (tutma) | H3 (APCu) | H4 (parça) | H8 (ölçüm) |
|---|---|---|---|---|---|
| `MemoryAdapter` | ✓ risk | — | — | ✓ risk | ✓ (ölçüm yok) |
| `ApcuAdapter` | — | — | ✓ risk | — | ✓ (ölçüm yok) |
| `FileHandler` | ✓ risk | ✓ risk | — | — | ✓ (ölçüm yok) |
| log redaksiyonu | ✓ risk | — | — | — | ✓ (ölçüm yok) |
| (ölçüm kodu) | — | — | — | — | ✓ **eksik** |

**Matris kuralı:** `✓ risk` = "o arıza o örnek için mümkündür"; gerçekleşme
`UNKNOWN` (ölçülmedi). `—` = "o örnek o arızaya yol açmaz (tasarım gereği)".
Bu matris **tahmin değil**, arıza-yüzeyi haritasıdır.

### §8.1 Sınır komşuları — bu dosyanın komşularla ilişkisi

| Komşu | Ne alır | Ne vermez |
|---|---|---|
| `-anayasa` | tanım/ilke (B1–B19) | kod |
| `-surucu` | akış/hata modu | repo satırı |
| `isletim-sistemi-observability` | metrik tanımı | okuma betiği |
| `isletim-sistemi-testing` | test tasarımı | test kodu |
| `isletim-sistemi-api` | ölçüm sargı fikri | endpoint kodu |
| log dalı | redaksiyon örneği | log formatı |

**Sınır kuralı:** Bu dosya yalnız **örnek** üretir; eşik, ölçüm sonucu ve
test çıktısı üretemez (hepsi `UNKNOWN`/`PLANNED` işaretlidir).

## §9 Ölçüm — bu dosyanın kendi ölçüm tablosu

| # | Ölçü | Değer | Durum |
|---|---|---|---|
| 1 | repo örneği sayısı | 3 sınıf (`MemoryAdapter`, `ApcuAdapter`, `FileHandler` kanıtı) | ölçüldü (2026-10-13) |
| 2 | olumsuz kanıt (grep) | 5 arama / 0 isabet | ölçüldü (2026-10-13) |
| 3 | URL | 1 (HTTP 200) | ölçüldü (2026-10-13) |
| 4 | bellek ölçüm değeri | — | `UNKNOWN` (yok) |
| 5 | eşik değeri | — | yazılmaz (İlke B10) |
| 6 | açık soru | Q-B10–Q-B14 (5) | açık |

**Ölçüm dürüstlüğü:** Tablodaki 1–3 **yapısal ölçüm** (dosya/satır/grep);
4–6 **durum ölçümüdür** ve gerçek değer üretilmemiştir. İki tür birbirine
karıştırılmaz.

### §9.1 Yapısal ölçümün ayrıntısı — ne sayıldı

| Sayım | Yöntem | Sonuç | Tarih |
|---|---|---|---|
| `MemoryAdapter` kanıt satırları | ReadAllLines | 3 (8 · 25–28 · 49) | 2026-10-13 |
| `ApcuAdapter` kanıt satırları | ReadAllLines | 3 (13 · 22 · 38) | 2026-10-13 |
| `FileHandler` kanıt satırı | ReadAllLines | 1 (251) | 2026-10-13 |
| grep araması | tüm `*.php` | 5 arama | 2026-10-13 |
| grep isabeti | tüm `*.php` | 0 | 2026-10-13 |
| URL HEAD | `Invoke-WebRequest -Method Head` | 1 × 200 | 2026-10-13 |

**Yöntem notu:** ReadAllLines ile satır numarası birebir doğrulanır; grep
kapsamı tüm `*.php` dosyalarıdır (alt dizinler dahil). Kapsam dışı kalan
dosya tipi yoktur — bu nedenle 0 isabet **eksiksiz tarama** sonucudur.

### §9.2 Ölçüm yokluğunun risk tablosu

| Risk | Olasılık | Etki | Mevcut koruma |
|---|---|---|---|
| zirve taşması üretimde | `UNKNOWN` | yüksek | koruma **yok** (ölçüm yok) |
| APCu doluluğu | `UNKNOWN` | orta | koruma **yok** |
| parçalanma | `UNKNOWN` | düşük-orta | koruma **yok** |
| yanlış alarm | düşük (zaten alarm yok) | — | alarm yok |
| yanlış eşik yazma | korunuyor | yüksek | `UNKNOWN` işaretleri |

**Risk kuralı:** "Olasılık" sütununda sayı **yoktur** — olasılık için de
ölçüm gerekir. Bu tablo eksikliğin kendisini kayda geçirir, tahmin üretmez.

## §10 Sık yapılan hatalar — bu dalda görülmesi muhtemel 7 hata

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| E1 | "bellek ölçümü var" demek | grep 0 ile çelişir | "ölçüm kodu yok" yazılır |
| E2 | VSZ'yi RSS sanmak | şişkin görünüm | anayasa §4.1 (T3) |
| E3 | önbelleği bellek sanmak | önbellek = OS'ta | sayfa önbelleği ayrı |
| E4 | APCu'yu kalıcı depo sanmak | süreç sonunda kayıp | kalıcı değil (§1.2) |
| E5 | Windows/Linux sayaçlarını eşlemek | yanlış yorum | İlke B11 |
| E6 | eşik uydurmak | kanıtsız sayı | yasak (İlke B10) |
| E7 | 0 isabeti "yok" diye genişletmek | iddia taşması | yalnız kapsamıyla der |

**Hata kuralı:** E1 ve E7 bu dosyanın **doğruluk** hatalarıdır; diğerleri
**yorum** hatalarıdır. Doğruluk hatası bir kez işlendiğinde dosya
`draft`'tan çıkmaz (yeniden denetim gerekir).

### §10.1 Hata × kapı ilişkisi

| Hata | Hangi kapıda yakalanır | Kapı durumu |
|---|---|---|
| E1/E7 | K4 (olumsuz kanıt) | ✅ geçti (2026-10-13) |
| E2/E5 | K1 (ölçüm) | ❌ açık — ölçüm yok |
| E3/E4 | kod incelemesi | kısmi (yalnız 3 sınıf) |
| E6 | K2 (eşik) | ✅ geçti (eşik yazılmadı) |

## §11 Bu dosyanın komşulara verdiği özet (handover satırı)

| Alıcı dal | Verilen bilgi | Biçim |
|---|---|---|
| `isletim-sistemi-observability` | metrik tanımı + ölçüm eksikliği | tablo (§9) |
| `isletim-sistemi-testing` | T1–T6 test tasarımı + risk tablosu | tablo (§5.1, §9.2) |
| `isletim-sistemi-api` | ölçüm sargısı ekleme planı | tablo (§5.1) |
| `isletim-sistemi-security` | paylaşımlı bellek yüzeyi | §1.2 |
| `isletim-sistemi-kernel` | repo'da OS kodu yok kanıtı | §2 grep 0 |

**Handover kuralı:** Bu tablodaki alıcılar bilgiyi **kullanır**, bu dosyanın
satırlarını **değiştirmez**; değişiklik ADR/üst karar ister.

## §12 Örnek katalog — her satır tek bir gözlemlenebilir örüntü

| # | Örüntü | Dosya | Satır | Tür |
|---|---|---|---|---|
| K1 | dizi deposu sınıfı kimliği | `MemoryAdapter.php` | 8 | repo |
| K2 | diziye yazma (tahsis) | `MemoryAdapter.php` | 25–28 | repo |
| K3 | diziden okuma | `MemoryAdapter.php` | 49 | repo |
| K4 | APCu bağımlılığı | `ApcuAdapter.php` | 13 | repo |
| K5 | paylaşımlı yazma | `ApcuAdapter.php` | 22 | repo |
| K6 | paylaşımlı okuma | `ApcuAdapter.php` | 38 | repo |
| K7 | dosya kapatma (tutucu bırakma) | `FileHandler.php` | 251 | repo |
| K8 | ölçüm çağrısı | (tüm `*.php`) | — | **yok** (grep 0) |
| K9 | core dump bilgisi | upstream docs | §6 URL | upstream |
| K10 | eşik değeri | — | — | **yazılmaz** |

**Katalog kuralı:** K1–K7 **olumlu** kanıttır (dosya+satır), K8 **olumsuz**
kanıttır (grep 0), K9 **upstream** kanıtdır (URL), K10 **yokluk** kaydıdır
(İlke B10). Dört kanıt sınıfı aynı tabloda birleştirilmez — sütunla ayrılır.

### §12.1 Örneklerin birbirine bağımlılığı

| Örnek | Bağımlı olduğu | Neden |
|---|---|---|
| K2/K3 | K1 (sınıf kimliği) | sınıf olmadan satır anlamsız |
| K5/K6 | K4 (APCu bağımlılığı) | sağlayıcı yoksa çağrı yok |
| K7 | `FileHandler` sınıfı | kapatma tek başına anlam taşımaz |
| K8 | — | yokluk, hiçbir örneğe bağlı değil |
| tümü | anayasa §2 (tanımlar) | kavram tanımsız sayılmaz |

**Bağımlılık kuralı:** Örnek, üstündeki kavram tanımsızsa **tek başına
kanıt sayılmaz**; her örnek `-anayasa`'daki kavram referansıyla okunur.

## §13 Test senaryoları — bu dosyanın test dalına verdiği taslak

| # | Senaryo | Adım | Beklenen | Durum |
|---|---|---|---|---|
| TS1 | dizi deposu yaz/oku | K2 → K3 | aynı değeri döner | taslak |
| TS2 | ömür sonu temizlik | betik sonu | süreç belleği düşer | taslak |
| TS3 | APCu TTL | yaz + bekle + oku | `UNKNOWN` (TTL okunmadı) | taslak |
| TS4 | zirve ölçümü | `memory_get_peak_usage` | sayı döner | taslak (repo'da çağrı yok) |
| TS5 | tutucu kapatma | K7 | dosya serbest | taslak |
| TS6 | ölçüm yokluğu | grep O1–O5 | 0 isabet tekrarlanır | **tekrarlanabilir** |

**Senaryo kuralı:** TS1–TS5 bu turda **çalıştırılmadı**; TS6 ise yapısal
olarak **tekrarlanabilirdir** (grep yeniden çalıştırılabilir) ve 2026-10-13
için sonucu **0** olarak kayıtlıdır. Test kodu bu dosyaya **yazılmaz**.

### §13.1 TS6 tekrarlanabilirliği — komut kapsamı

| Alan | Değer |
|---|---|
| kapsam | tüm `*.php` (alt dizinler dahil) |
| aramalar | `memory_get_usage` · `memory_get_peak_usage` · `apc_sma_info` · `memory_limit` · `pcntl` |
| sonuç | 0 isabet (her arama) |
| tarih | 2026-10-13 |
| yeniden çalıştırma | yeni turda tekrarlanmalı (kod değişirse sonuç değişebilir) |

**Güncellik kuralı:** Olumsuz kanıt **zamanla eskir**; her yeni turda
yeniden çalıştırılır ve eski "0" tek başına güncel kanıt sayılmaz.

## §14 Açık sorular — bu dosyanın kapatılmayanları

| # | Soru | Etiket | Kapanış şartı |
|---|---|---|---|
| Q-B10 | ölçüm kodu hiç mi yazılmayacak? | PLANNED | karar + ölçüm eklemesi |
| Q-B11 | APCu TTL/limit üretim değeri? | UNKNOWN | üretim okuması |
| Q-B12 | core dump açık mı? | UNKNOWN | ortam okuması |
| Q-B13 | en pahalı istek hangisi? | PLANNED | istek başına ölçüm |
| Q-B14 | `MemoryAdapter` çağrı zinciri? | ⚠️ VERIFICATION REQUIRED | tam tarama |
| Q-B15 | `FileHandler` dışında kaç tutucu var? | UNKNOWN | tam dosya taraması |
| Q-B16 | PHP `memory_limit` üretim değeri? | UNKNOWN | php.ini okuması |

Bu yedi soru açık kaldığı sürece dosya `draft` statüsünde kalır; kapanış
ölçüm ve ortam kanıtı ister (§5.1 planı).

### §14.1 Soru × kapanış yolu eşlemesi

| Soru grubu | Kapanış yolu | Sorumlu dal |
|---|---|---|
| Q-B10, Q-B13 | ölçüm ekleme | bu dal + `isletim-sistemi-api` |
| Q-B11, Q-B16 | ortam okuması | ops/ölçüm (PLANNED) |
| Q-B12 | ortam okuması | ops (PLANNED) |
| Q-B14, Q-B15 | kod taraması | bu dal |
| Q-B17 (yok) | — | — |

**Eşleme kuralı:** Her sorunun **tek** bir kapanış yolu vardır; birden fazla
yazılıysa ilk satır bağlayıcıdır. Yolu olmayan soru `⚠️ VERIFICATION REQUIRED`
olarak üst karara gider.

## §15 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0–§10) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | repo satırları ReadAllLines ile doğrulandı | kanıt kapısı |
| 2026-10-13 | grep olumsuz kanıt kaydedildi (5 arama/0 isabet) | kanıt kapısı |
| 2026-10-13 | 1 URL HEAD 200 doğrulandı | kanıt kapısı |

## §16 Bellek kavramları — pratik eşleme tablosu (derinleştirme)

> Bu bölüm §2–§10'daki kavramları **uygulama kararına** çevirir. Her satır
> bir "kavram → hangi durumda hangi seçim" eşlemesidir; gerekçe § sütununda
> verilmiştir ve o bölümdeki kanıta bağlanır.

### §16.1 Tahsis stratejisi seçimi

| # | Durum | Seçim | Gerekçe (§) |
|---|---|---|---|
| 1 | Ses callback'i içinde (RT thread) | Başlangıçta tek seferlik tahsis, sonra sıfır tahsis | §4 |
| 2 | UI thread'de kısa ömürlü buffer | Stack (sınırlı boyut) | §3 |
| 3 | İş parçacığı arası paylaşım | Sahiplik açıkça transfer edilir (move) | §5 |
| 4 | Sahiplik belirsiz, okuma ağırlıklı | Kalıcı ömürlü konteyner + okuma kilidi | §5 |
| 5 | Boyut çalışma zamanında biliniyor | Kapasite önceden tahmin edilir (reserve) | §6 |
| 6 | Bellek havuzu zaten var | Havuzdan kirala, geri ver | §6 |

### §16.2 Hizalama kararı

| # | Gereksinim | Minimum hizalama | Not |
|---|---|---|---|
| 1 | Genel amaçlı C++ nesnesi | Derleyici varsayılanı | §3 |
| 2 | DMA ile doğrudan erişilen tampon | Donanım dokümanındaki sınır | ⚠️ VERIFICATION REQUIRED (§8) |
| 3 | SIMD vektör işleme | Vektör genişliği kadar | §3 |
| 4 | Sabit gecikmeli ses tamponu | Önceden tahsis + hizalı | §4 |

> **Dürüstlük:** #2 satırındaki donanım sınırı **bu vault'ta ölçülmemiştir** —
> ilgili cihaz dokümanı okunmadan sayı yazılmaz (§8 #2).

### §16.3 Yaşam döngüsü kontrol listesi

| # | Kontrol | Aşama | Kanıt |
|---|---|---|---|
| 1 | Her tahsisin bir sahibi var mı? | derleme | §5 |
| 2 | Kopyalama yeri açıkça işaretli mi? | kod incelemesi | §5 |
| 3 | RT yolunda tahsis var mı? | profil | §4 |
| 4 | Kapasite büyümesi kaç kez oldu? | çalışma zamanı | §6 |
| 5 | Sıfırlama gereksiz mi? | kod incelemesi | §4 |
| 6 | Kaynak dönüşü (RAII) her yolda var mı? | derleme | §5 |

### §16.4 Bu bölümün doğrulanabilirlik etkisi

| Kategori | §16 öncesi | §16 sonrası | Not |
|---|---|---|---|
| S1 (repo satırı) | §14'te kayıtlı | değişmedi | §16 yeni satır iddiası **eklemez** |
| S4 (mimari yorum) | §14'te kayıtlı | +6 tablo satırı (§16.3 #1–#6) | kontrol listesi = pratik yorum |
| S7 (bilinmeyen) | §14'te kayıtlı | +1 (§16.2 #2) | donanım hizalama sınırı ölçülmedi |

> **Sonuç:** §16 dosyayı derinleştirir ama **ölçülmüş oranı artırmaz** — bu
> bilinçli bir tercihtir. Yeni içerik eklemek kanıt eklemek değildir; oran
> yalnızca gerçek ölçümle iner.

---

## §17 Çapraz-platform gruplu kod örnekleri (2026-10-14)

> **Grup kuralı (bağlayıcı):** Her grup **tek kavram + üç platform kolu**
> (Linux/POSIX · Windows (Win32) · macOS/POSIX) taşır. "Sadece Linux" örnek
> bu bölümde geçersizdir. macOS-native man sayfaları bu turda erişilemedi
> (`developer.apple.com/library/archive/.../man*_*.html` → bağlantı koptu/404);
> macOS sütunu POSIX spesifikasyonuna dayanır ve `⚠️ VERIFICATION REQUIRED`
> ile işaretlenir.

### §17.1 G4a - Yığın (heap) tahsisi: `malloc`/`free` ↔ `HeapAlloc`/`HeapFree`

**(A) Linux/POSIX:**

```c
#include <stdlib.h>
#include <string.h>

char *buf = (char *)malloc(4096);
if (buf) {
    memset(buf, 0, 4096);      /* calloc benzeri sifirlama elle yapilir */
    free(buf);
}
```

**(B) Windows (Win32):**

```c
#include <windows.h>

HANDLE heap = GetProcessHeap();
LPVOID p = HeapAlloc(heap, HEAP_ZERO_MEMORY, 4096);
if (p) {
    /* p kullan */
    HeapFree(heap, 0, p);
}
```

**(C) macOS / POSIX** — (A) birebir geçer · `⚠️ VERIFICATION REQUIRED`.

> **Kavram farkı:** `malloc` **dil-standardı**dır (üreticiye göre davranır);
> `HeapAlloc` **Windows süreç-yığını** üzerindedir. İkisi aynı seviyede
> değiş tokuş edilemez — bu eşdeğerlik iddiası `⚠️ VERIFICATION REQUIRED`.

### §17.2 G4b - Sayfa düzeyi tahsis + koruma: `mmap`/`munmap`/`mprotect` ↔ `VirtualAlloc`/`VirtualFree`/`VirtualProtect`

**(A) Linux/POSIX:**

```c
#include <sys/mman.h>
#include <unistd.h>

size_t len = 16 * 4096;
void *p = mmap(NULL, len, PROT_READ | PROT_WRITE,
               MAP_PRIVATE | MAP_ANONYMOUS, -1, 0);
if (p != MAP_FAILED) {
    /* ... */
    mprotect(p, len, PROT_READ);          /* yazmayi kapat */
    munmap(p, len);                        /* serbest birak */
}
```

**(B) Windows (Win32):**

```c
#include <windows.h>

SIZE_T len = 16 * 4096;
LPVOID p = VirtualAlloc(NULL, len, MEM_COMMIT | MEM_RESERVE, PAGE_READWRITE);
if (p) {
    DWORD old = 0;
    VirtualProtect(p, len, PAGE_READONLY, &old);   /* yazmayi kapat */
    VirtualFree(p, 0, MEM_RELEASE);                 /* serbest birak */
}
```

**(C) macOS / POSIX** — (A) birebir geçer · `⚠️ VERIFICATION REQUIRED`.

| Soru | Linux/POSIX | Windows |
|---|---|---|
| İlk boyut | `mmap(len, prot, flags)` | `VirtualAlloc(len, MEM_COMMIT\|MEM_RESERVE)` |
| Koruma değiştirme | `mprotect` | `VirtualProtect` |
| Serbest bırakma | `munmap(len)` | `VirtualFree(0, MEM_RELEASE)` |
| Durum sorgusu | `/proc/self/maps` (Linux) | `VirtualQuery` |
| Hata göstergesi | `MAP_FAILED` (`(void*)-1`) | `NULL` |

### §17.3 G5 - Dosya eşleme (file mapping / paylaşımlı bellek)

**(A) Linux/POSIX — `shm_open` + `mmap`:**

```c
#include <sys/mman.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>

int fd = shm_open("/coremusic-demo", O_CREAT | O_RDWR, 0600);
if (fd >= 0) {
    ftruncate(fd, 4096);
    void *p = mmap(NULL, 4096, PROT_READ | PROT_WRITE, MAP_SHARED, fd, 0);
    if (p != MAP_FAILED) {
        /* iki surec ayni sayfayi gorur */
        munmap(p, 4096);
    }
    close(fd);
    shm_unlink("/coremusic-demo");
}
```

**(B) Windows (Win32) — `CreateFileMappingW` + `MapViewOfFile`:**

```c
#include <windows.h>

HANDLE hf = CreateFileW(L"Local\\coremusic-demo",
                        GENERIC_READ | GENERIC_WRITE,
                        FILE_SHARE_READ | FILE_SHARE_WRITE,
                        NULL, OPEN_ALWAYS, FILE_ATTRIBUTE_NORMAL, NULL);
if (hf != INVALID_HANDLE_VALUE) {
    HANDLE hmap = CreateFileMappingW(hf, NULL, PAGE_READWRITE, 0, 4096,
                                     L"Local\\coremusic-demo");
    if (hmap) {
        LPVOID p = MapViewOfFile(hmap, FILE_MAP_ALL_ACCESS, 0, 0, 4096);
        if (p) {
            /* iki surec ayni sayfayi gorur */
            UnmapViewOfFile(p);
        }
        CloseHandle(hmap);
    }
    CloseHandle(hf);
}
```

**(C) macOS / POSIX** — POSIX `shm_open` + `mmap` (A) ile aynı · `⚠️ VERIFICATION REQUIRED`.

| Kavram | Linux/POSIX | Windows | macOS |
|---|---|---|---|
| Nesne adlandırma | `"/isim"` (başında `/`) | `Local\\isim` / `Global\\isim` | POSIX (A) · ⚠️ |
| Yaratma | `shm_open(O_CREAT)` | `CreateFileMappingW` | POSIX · ⚠️ |
| Görüntüleme | `mmap(MAP_SHARED)` | `MapViewOfFile` | POSIX · ⚠️ |
| Sökme | `munmap` + `shm_unlink` | `UnmapViewOfFile` + `CloseHandle` | POSIX · ⚠️ |
| Repo bağlantısı | `ApcuAdapter` (§1.2) PHP katmanı sargısı | — | — |

### §17.4 Yeni doğrulanmış URL (2026-10-14 · envanter kesişimi 0 · HTTP 200)

> **Ölçüm (2026-10-14 · yeniden):** `.ai/**` (gizli dizin, `*.md|*.json|*.txt|*.ps1`)
> URL kümesi **bu turda yazılan 6 dosya hariç** tutularak ölçüldü = **3760 tekil URL**;
> bu dosyanın **aşağıdaki** adayları bu kümede **yok** → kesişim **0**. Tur-çapı tek-çatı
> sayım ve kümülatif envanter (**3866**) için bkz. `isletim-sistemi-core-dogrulama.md` **§J**.

| # | İddia | URL | 2026-10-14 |
|---|---|---|---|
| 1 | `shm_open` - adlandırılmış paylaşımlı nesne (Linux man) | https://man7.org/linux/man-pages/man3/shm_open.3.html | ? HTTP 200 · **YENİ** |
| 2 | `shm_open` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/shm_open.html | ? HTTP 200 · **YENİ** |
| 3 | `malloc` POSIX tanımı (yığın tahsisi) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/malloc.html | ? HTTP 200 · **YENİ** |
| 4 | `munmap` POSIX tanımı (bölge serbest bırakma) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/munmap.html | ? HTTP 200 · **YENİ** |
| 5 | `CreateFileMappingW` - eşleme nesnesi yaratma | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-createfilemappingw | ? HTTP 200 · **YENİ** |
| 6 | `UnmapViewOfFile` - görüntüyü sökme | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-unmapviewoffile | ? HTTP 200 · **YENİ** |
| 7 | `HeapFree` - süreç yığını serbest bırakma | https://learn.microsoft.com/en-us/windows/win32/api/heapapi/nf-heapapi-heapfree | ? HTTP 200 · **YENİ** |
| 8 | Dosya eşleme nesnesi oluşturma (adım adım) | https://learn.microsoft.com/en-us/windows/win32/memory/creating-a-file-mapping-object | ? HTTP 200 · **YENİ** |

### §17.5 Reuse URL - HTTP 200 yeniden doğrulandı (2026-10-14)

| # | İddia | URL |
|---|---|---|
| 1 | `mmap(2)` Linux çağrısı | https://man7.org/linux/man-pages/man2/mmap.2.html |
| 2 | `munmap(2)` Linux çağrısı | https://man7.org/linux/man-pages/man2/munmap.2.html |
| 3 | `mprotect(2)` koruma değiştirme | https://man7.org/linux/man-pages/man2/mprotect.2.html |
| 4 | `malloc(3)` Linux yığın tahsisi | https://man7.org/linux/man-pages/man3/malloc.3.html |
| 5 | `mmap` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html |
| 6 | `mprotect` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mprotect.html |
| 7 | `VirtualAlloc` | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualalloc |
| 8 | `VirtualFree` | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualfree |
| 9 | `VirtualProtect` | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualprotect |
| 10 | `VirtualQuery` | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualquery |
| 11 | `MapViewOfFile` | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-mapviewoffile |
| 12 | `HeapAlloc` | https://learn.microsoft.com/en-us/windows/win32/api/heapapi/nf-heapapi-heapalloc |
| 13 | `CloseHandle` | https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle |
| 14 | `open(2)` - dosya/tanıtıcı açma bağlamı | https://man7.org/linux/man-pages/man2/open.2.html |

### §17.6 Bu bölümün ölçüme kaydı (2026-10-14)

| Ölçüm | Değer |
|---|---|
| Eklenen grup | 3 (G4a heap · G4b sayfa+koruma · G5 dosya eşleme) |
| Platform kolları | 3/grup = 9 kol |
| Eklenen kod bloğu | 6 (A=3 · B=3) |
| Yeni doğrulanmış URL | 8 (§17.4) · envanter kesişimi **0** · HTTP 200 |
| Reuse URL yeniden 200 | 14 (§17.5) |
| macOS-native kaynak | 0 erişilebilir → 3 iddia `⚠️ VERIFICATION REQUIRED` |
| `git commit` | **atılmadı** (orkestratöre ait) |

---

## §18 PHP Köprüsü — bellek (repo satır kanıtlı, 2026-10-14)

> PHP'nin bellek modeli ZMM (Zend Memory Manager) üzerinde çalışır; OS katmanına yalnızca
> **sayfalama / geçici dosya / stream** sırasındaki gerçek çağrılar çıkar. Bu bölümün her
> kod bloğu depoda `ReadAllLines` ile okunmuş **gerçek satırdır**; yanında POSIX ve
> Win32 karşılığı verilir. Depoda çekirdek OS kodu YOK (bkz. §4).

### §18.1 B1 — Geçici dosya + rastgele ad: `sys_get_temp_dir()` + `random_bytes` ↔ `mkstemp`

**Repo kanıtı:** `shared/src/Component/TemplateEngine.php:72-76`

```php
// Guardrail §21: eval yok — cache dosyası + require (ADR-091)
$cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';
if (file_put_contents($cachePath, $template, LOCK_EX) === false) {
    throw new \RuntimeException("Template cache write failed: {$cachePath}");
}
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `sys_get_temp_dir()` | `TMPDIR` / `confstr(_CS_PATH)` | `GetTempPathW` |
| `bin2hex(random_bytes(8))` | `getrandom(2)` / `/dev/urandom` | `BCryptGenRandom` |
| `file_put_contents($f,$s,LOCK_EX)` | `open(O_WRONLY\|O_CREAT)` + `flock` + `write` | `CreateFileW` + `LockFileEx` + `WriteFile` |
| `=== false` + `throw` | `errno == -1` → `perror` | `GetLastError` → `RaiseException` |

**Kilitli yazım `LOCK_EX` ile yapılmıştır** (bkz. dosya-sistemi §13.1) — tek-yazıcı
varsayımı yoktur; başarısızlık `RuntimeException` ile yukarı taşınır.

### §18.2 B2 — Entropi: `random_bytes` / `random_int` ↔ `getrandom` ↔ `BCryptGenRandom`

**Repo kanıtları:**
- `media.coremusic.net/src/Media/Ulid.php:67-68` → `$ham = random_bytes($bayt);`
- `shared/src/Repository/ApiKeyRepository.php:295` → `random_int(0, 31)`

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `random_bytes(n)` (CSPRG) | `getrandom(2)` (≥3.17) | `BCryptGenRandom` |
| `random_int(min,max)` | `getrandom` + `int` karışımı | aynı |
| `mt_rand()` (**CSPRG değil**) | `rand()` | `rand()` |

Güvenlik yüzeyi (API key, token) **yalnızca** `random_int`/`random_bytes` kullanır —
`mt_rand()` depoda güvenlik için **kullanılmıyor** (grep: `mt_rand` → 0 isabet).

### §18.3 B3 — Sabit bellekli okuma döngüsü ↔ `mmap`/`ReadFile`

**Repo kanıtı:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:342-351`

```php
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
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `fread($h, $n)` | `read(fd, buf, n)` | `ReadFile(h, buf, n, &r, NULL)` |
| `min(CHUNK, $remaining)` | `min()` (uç sınır) | `min()` |
| `feof($h)` | `read() == 0` | `ReadFile` `r == 0` |
| `flush()` | `write` çıktısını iter | `FlushFile` (PHP tamponu) |
| (kopya örneği) | `sendfile` / `mmap` | `TransmitFile` / `ReadFile`+`WriteFile` |

**Neden önemli:** dosya **asla belleğe tamamen alınmaz** → bellek O(chunk) kalır.
Alternatif `file_get_contents` tam yükleme yapardı (O(file)) — §18.7 anti-pattern.

### §18.4 B4 — Hata yüzeyi: zarf `@` + `Throwable` ↔ `errno` ↔ `GetLastError`

**Repo kanıtı:** `shared/src/PageRouter/PageRouterKernel.php:131-136`

```php
$errorMsg = Redactor::redact($errorMsg);
$logDir = $this->config->get('app.log_dir', dirname(__DIR__, 3));
@file_put_contents($logDir . '/coremusic_php_errors.log', $errorMsg, FILE_APPEND | LOCK_EX);
@file_put_contents($logDir . '/coremusic_php_kernel_debug.log', $errorMsg, FILE_APPEND | LOCK_EX);
error_log(Redactor::redact('[PageRouterKernel] FATAL traceId=' . $traceId . ' ' . $e->getMessage()
    . ' in ' . $e->getFile() . ':' . $e->getLine()));
```

PHP'de POSIX `errno` doğrudan **yoktur**; karşılığı **(a)** zarf operatörü `@` (sinyali
susturur), **(b)** dönüş değeri kontrolü (`=== false`), **(c)** `catch (\Throwable)`
katmanıdır. `error_get_last()` depoda **0 isabet** (§18.6). Sonuç: `EACCES`/`ENOSPC`
veya `GetLastError` **kod değeri PHP'ye yansımaz** — yalnız mesaj + dosya/satır bilgisi
kalır. Kayıp bilgi `⚠️ VERIFICATION REQUIRED` olarak kayıtlıdır.

### §18.5 B5 — Eşleme anahtarı

| Kavram | PHP | POSIX | Windows Win32 |
|---|---|---|---|
| zarf ölçümü | `memory_get_usage()` | `mallinfo` / `smaps` | `GetProcessMemoryInfo` |
| tepe | `memory_get_peak_usage()` | `ru_maxrss` | `PeakWorkingSetSize` |
| geçici dizin | `sys_get_temp_dir()` | `TMPDIR` | `GetTempPathW` |
| entropi | `random_bytes` | `getrandom` | `BCryptGenRandom` |
| parça okuma | `fread` | `read` | `ReadFile` |

### §18.6 P/Olumsuz kanıt (grep → **0 isabet**)

`memory_get_usage` · `memory_get_peak_usage` · `gc_collect_cycles` · `shm_open` ·
`mmap` · `error_get_last` — depoda **hiçbiri kullanılmıyor**. Sonuç: bu dosya
bellek-ölçüm/ölçümleme **kodu üretmez**; §18.5'teki satırlar yalnızca **eşleme
anahtarıdır**, repo kanıtı **iddia edilmez**.

### §18.7 Ölçüm

| Ölçüm | Değer |
|---|---|
| Eklenen PHP kod bloğu | 3 (§18.1, §18.3, §18.4 — hepsi gerçek satır) |
| Repo satır çifti | 5 (TemplateEngine:72-76 · Ulid:67-68 · ApiKeyRepository:295 · MusicStreamHandler:342-351 · PageRouterKernel:131-136) |
| Yeni URL | **0** → envanter kesişimi 0 |
| Olumsuz grep | `memory_get_usage` · `gc_collect_cycles` · `shm_open` · `mmap` · `error_get_last` · `mt_rand` → 0 |
| ⚠️ işaretli iddia | 1 (§18.4 errno/GetLastError bilgi kaybı) — §18.5 eşleme satırları "repo kanıtı" olarak **iddia edilmiyor** |

## §19 — Çok dilli bellek örnekleri: JS · C · Python · Go · Rust · C#

> **Kanıt hiyerarşisi:** §18 PHP blokları depo satırına dayanıyordu. Burada **yalnızca JS (98 dosya)
> ve Python (1 dosya) depoda taşınır**; C/Go/Rust/C# **0 dosya** → o bloklar `⚠️ KALIP (repo YOK)` +
> resmi doküman URL'i (hepsi 2026-10-14'te HTTP 200 ölçüldü → dogrulama §J.9).

### §19.0 Ölçüm — kanıt durumu (2026-10-14)

| Dil | Repo dosyası | Bellek/zaman yüzeyi grep (repo) | Kanıt tipi |
|---|---|---|---|
| JavaScript | **98** | `performance.now` **7** · `crypto.getRandomValues` **1** · `Math.random` **0** · `process.memoryUsage` **0** · `crypto.subtle` **0** | repo satırı |
| Python | **1** | `bytearray` **0** · `secrets` **0** | kalıp (repo PY dosyası bellek API'si kullanmıyor) |
| C / Go / Rust / C# | **0 / 0 / 0 / 0** | — | kalıp + doküman |

### §19.1 JavaScript — depo satırı: süreölçüm + kriptografik rastgelelik (tarayıcı)

```js
// assets.coremusic.net/js/router/ContentFetcher.js:24 ve :29 (ReadAllLines birebir)
const start = performance.now();
// ...
return { responseData, fetchMs: performance.now() - start, cacheHit: false };

// assets.coremusic.net/js/router/Logger.js:14-19 (ReadAllLines birebir) — UUID fallback
const bytes = new Uint8Array(16);
crypto.getRandomValues(bytes);
bytes[6] = (bytes[6] & 0x0f) | 0x40;
bytes[8] = (bytes[8] & 0x3f) | 0x80;
const hex = [...bytes].map((b) => b.toString(16).padStart(2, '0')).join('');
```

**Eşleme:** `performance.now()` süreölçüm için kullanılıyor (`surec-ve-zamanlama` dosyası §20.4'teki
`hrtime` eşlemesinin JS karşılığı sayılır) — `⚠️ VERIFICATION REQUIRED`: `performance.now()`'ın **monotonik** olup olmadığı bu turda
MDN/WHATWG'den **okunmadı**. `Math.random` grep **0** → rastgelelik tarayıcıda `crypto.getRandomValues`
ile üretiliyor (§18.2 `random_bytes` eşlemesi). Bellek ölçüm API'si (`process.memoryUsage`) **0** —
tarayıcıda yoktur.

### §19.2 C — `mmap` ile anonim bellek (kalıp)

```c
/* ⚠️ KALIP (repo YOK) — Kaynak:
   https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html (reuse · envanterde vardı · 200) */
#include <sys/mman.h>
#include <unistd.h>

size_t n = 1u << 20;                       /* 1 MiB */
void *p = mmap(NULL, n, PROT_READ | PROT_WRITE,
               MAP_PRIVATE | MAP_ANONYMOUS, -1, 0);
if (p == MAP_FAILED) { /* errno oku */ }
/* ... */
munmap(p, n);
```

**Eşleme:** `MAP_ANONYMOUS` dosya adı gerektirmez → §18.3'teki "geçici dosya + bellek" ikileminde
C tarafı dosyayı **atlar**. `⚠️ VERIFICATION REQUIRED`: `MAP_ANONYMOUS`'ın POSIX'te değil Linux/BSD'de
olduğu iddiası bu turda dokümandan **okunmadı** (yalnız `mmap` sayfası 200 ölçüldü).

### §19.3 Python — yerinde değiştirme + kriptografik rastgele bytes (kalıp)

```python
# ⚠️ KALIP (repo YOK) — Kaynak: https://docs.python.org/3/library/secrets.html (YENİ · 200)
import secrets

buf = bytearray(1024)          # yerinde değiştirilebilir (bytes DEĞİL)
token = secrets.token_bytes(32)   # §18.2 random_bytes eşlemesi
del buf                        # referans düşünce GC toplar (CPython sayım tabanlı iddia → ⚠️)
```

**Eşleme:** `secrets.token_bytes` ↔ PHP `random_bytes` (§18.2). `⚠️ VERIFICATION REQUIRED`:
CPython'un referans-sayım + döngü-toplayıcı modeli dokümandan bu turda **okunmadı**.

### §19.4 Go — `make` + çalışma zamanı istatistiği (kalıp)

```go
// ⚠️ KALIP (repo YOK) — Kaynak: https://pkg.go.dev/runtime (YENİ · 200)
package main

import "runtime"

func main() {
    buf := make([]byte, 1<<20) // 1 MiB, GC'nin yönettiği yığın/veri
    var m runtime.MemStats
    runtime.ReadMemStats(&m)
    _ = buf
    // m.Alloc, m.TotalAlloc, m.HeapAlloc ...
}
```

**Eşleme:** `runtime.ReadMemStats` ↔ PHP `memory_get_usage` — **ama depoda PHP karşılığı 0** (§18.6);
yani eşleme "iki tarafta da var" değil, "Go'da var, PHP'de isteğe bağlı eksik"tir.

### §19.5 Rust — sahiplik + GC'siz model (kalıp, URL doğrulanmadı)

```rust
// ⚠️ KALIP (repo YOK) — bu blok için doküman URL'i DOĞRULANMADI (bilinçli boşluk)
let mut v: Vec<i32> = Vec::with_capacity(1024); // yığın belleği, sahiplik Rust'ta
v.push(1);
let owned = String::from("coremusic");          // yığın (Rc yok — tek sahip)
let view: &str = &owned[..9];                   // ödünç, serbest bırakma yok
// v/owned kapsam dışına çıkınca drop edilir (RAII)
```

**Durum:** `⚠️ VERIFICATION REQUIRED` — Rust bellek modeli iddiaları (drop/RAII) doküman URL'i
**okunmadığı için** bu blok "kalıp" statüsündedir; URL eklenirse dogrulama §J.9'a işlenir.

### §19.6 C# — GC + kriptografik rastgelelik (kalıp)

```csharp
// ⚠️ KALIP (repo YOK) — Kaynaklar:
// https://learn.microsoft.com/en-us/dotnet/api/system.gc (YENİ · 200)
// https://learn.microsoft.com/en-us/dotnet/api/system.security.cryptography.randomnumbergenerator (YENİ · 200)
using System.Security.Cryptography;

var rng = RandomNumberGenerator.Create();
Span<byte> token = stackalloc byte[32];
rng.GetBytes(token);              // §18.2 random_bytes eşlemesi
// GC.Collect() çağırmak genellikle önerilmez (⚠️ sürüm davranışı okunmadı)
```

**Eşleme:** `RandomNumberGenerator.GetBytes` ↔ PHP `random_bytes` (§18.2). `⚠️ VERIFICATION REQUIRED`:
`GC.Collect()` kullanımıyla ilgili resmi öneri metni bu turda **okunmadı**.

### §19.7 Ölçüm (§19)

| Öğe | Değer |
|---|---|
| Dil | 6 (JS · C · Python · Go · Rust · C#) |
| Repo satır kanıtlı blok | **1** (§19.1 — `ContentFetcher.js:24,29` + `Logger.js:14-19`) |
| `⚠️ KALIP (repo YOK)` blok | **5** (§19.2 C · §19.3 Python · §19.4 Go · §19.5 Rust · §19.6 C#) |
| Bu bölümün URL'i | **5** → **1 reuse** (`mmap.html`) **+ 4 yeni** (`secrets` · `pkg.go.dev/runtime` · `system.gc` · `randomnumbergenerator`) — hepsi HTTP 200 |
| Repo grep (olumsuz kanıt) | `Math.random` · `process.memoryUsage` · `crypto.subtle` · `bytearray` · `secrets` → **0 isabet** |
| ⚠️ işaretli iddia | 5 (§19.1 monotoniklik · §19.2 MAP_ANONYMOUS · §19.3 CPython GC · §19.5 tüm Rust iddiaları · §19.6 GC.Collect önerisi) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
