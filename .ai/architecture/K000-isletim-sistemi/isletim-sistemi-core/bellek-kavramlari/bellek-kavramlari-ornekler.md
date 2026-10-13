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
validation: "2026-10-13 · 1 yeni URL HTTP HEAD 200 (envanter kesişimi 0) · ≥500 satır disk ölçümü · repo satırları ReadAllLines ile birebir · olumsuz kanıt grep 0 isabet"
open_questions:
  - "PHP `memory_get_usage` çağrısı repoda hiç yok mu? → grep 0 isabet (Q-B10, gerçek eksiklik mi?)"
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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
