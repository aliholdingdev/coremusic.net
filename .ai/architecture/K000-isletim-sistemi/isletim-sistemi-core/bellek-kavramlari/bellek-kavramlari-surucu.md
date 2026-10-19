---
node_id: K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari/bellek-kavramlari-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualfree
  - https://learn.microsoft.com/en-us/windows/win32/api/heapapi/nf-heapapi-heapalloc
  - https://learn.microsoft.com/en-us/windows/win32/api/psapi/nf-psapi-getprocessmemoryinfo
  - shared/src/Cache/MemoryAdapter.php:8
  - shared/src/Cache/ApcuAdapter.php:13
validation: "2026-10-13 · 3 yeni URL HTTP HEAD 200 (envanter kesişimi 0) · ≥500 satır disk ölçümü · repo satırları ReadAllLines ile birebir"
open_questions:
  - "Üretim bellek eşiği → UNKNOWN — ölçüm PLANNED (Q-B3)"
  - "Windows hedefinde hangi sayacı izleyeceğiz? → ⚠️ VERIFICATION REQUIRED (Q-B8)"
  - "PHP bellek limiti üretim değeri → UNKNOWN (php.ini okunmadı)"
group: mimari
---

# bellek-kavramlari-surucu — Mekanizma Sürücüsü ve Uygulama Katmanı (Kademe-5)

## §0 ELI15

"İşletim sistemi belleği fiilen nasıl yönetiyor?" sorusunun cevabı: her erişimde
**adres çevirisi** (sanal → fiziksel) yapılır, çeviriyi bozan her olay **sayfa
hatası** olarak çekirdeğe gelir, çekirdek bunu üç yoldan biriyle çözer —
sayfayı **yükler**, **geri getirir** veya ihlalde **süreci durdurur**. Fiziksel
bellek dolunca **baskı** başlar ve sürme (paging out) ile OOM değerlendirmesi
devreye girer. Bu dosya bu dört akışı, Windows/Linux ölçüm arayüzlerini ve
repo'daki PHP bellek yüzeyini adım adım yürütür.

## §1 Uygulama katmanı — bellek kavramı kodda nerede görünür

| Katman | Bellekle ilgili işi | Repo yüzeyi | Durum |
|---|---|---|---|
| PHP çekirdeği | betik tahsisi (array/string/object) | `memory_get_usage` çağrısı | repo'da **0 isabet** |
| PHP önbellek | paylaşımlı bellek (APC/user cache) | `shared/src/Cache/ApcuAdapter.php` | IMPLEMENTED |
| PHP deposu | isteğe bağlı dizi deposu | `shared/src/Cache/MemoryAdapter.php` | IMPLEMENTED |
| PHP dosya önbelleği | diske yazılan tampon | `shared/src/Log/FileHandler.php` | IMPLEMENTED |
| PHP kaynak | dosya tutma (uzun tutma → sızıntı riski) | `FileHandler.php` satır kanıtı | IMPLEMENTED |
| OS | süreç/çerçeve yönetimi | repo'da kod YOK | PLANNED (yok) |

**İlke S1 (bağlayıcı):** Repo'daki bellek yüzeyi **yalnızca PHP katmanıdır**;
çekirdek sayfalama/swap kodu bu repoda **bulunmaz**. Bu dosyadaki OS
mekanizması iddialarının kaynağı upstream docs (§7), repo iddialarının kaynağı
dosya/satır kanıtıdır.

### §1.1 Tahsis ve serbest bırakma örüntüleri (PHP)

| Örüntü | Ne yapar | Sızıntı riski | Repo kanıtı |
|---|---|---|---|
| tahsis + kullan + bırak | dizi/blok için | bırakma unutulursa süreç zirvesi artar | `MemoryAdapter` içinde geçici dizi |
| paylaşımlı tahsis | APCu ile süreçler arası | TTL dolmazsa dolar | `ApcuAdapter` |
| dosyaya yazma tamponu | belleği diske boşaltır | açık dosya tutma | `FileHandler.php:251` |
| string maskesi | hassas veriyi bellekte tutar | maske kopyası bellekte kalır | `Redactor` (log dalı) |
| `unset` + yeniden tahsis | dizi küçültme | parçalanma (fragmentation) | repo'da örnek yok → `UNKNOWN` |

**İlke S2:** PHP'de bellek, betik **sonlanınca** geri verilir; süreç (worker)
ayaktaysa betik belleği yalnızca kendi içinde yönetir. "Uzun ömürlü süreçte
sızıntı" ile "betik sonunda otomatik temizlik" **aynı şey değildir** — sızıntı
ancak süreç tekrar tekrar çalışıyorsa görünür.

## §2 Akış — dört mekanizma zinciri

### §2.1 Adres çevirisi akışı (uygulama gözünden)

| Adım | Olay | Uygulamada belirtisi | Karar |
|---|---|---|---|
| 1 | işaretçi/dizi erişimi | normal kod satırı | — |
| 2 | çeviri başarısız | (uygulama fark etmez) | çekirdek işi |
| 3a | sayfa diskte | kısa gecikme | yükle, devam et |
| 3b | sayfa yasal değil | `SIGSEGV` / access violation | süreç sonu |
| 4 | erişim tamam | sonuç döner | — |

### §2.2 Sayfa hatası akışı — karar ağacı

| # | Soru | Evet | Hayır |
|---|---|---|---|
| 1 | sayfa hiç yüklenmedi mi? | dosyadan yükle | 2'ye geç |
| 2 | sayfa swap'te mi? | diskten geri getir | 3'e geç |
| 3 | copy-on-write mı? | kopyala, sonra yaz | 4'e geç |
| 4 | erişim izni var mı? | erişimi tamamla | ihlal (§2.3) |
| 5 | yığın genişlemesi mi? | yeni sayfa ayır | ihlal (§2.3) |

### §2.3 Koruma ihlali akışı

| Adım | Olay | Linux | Windows | Uygulama etkisi |
|---|---|---|---|---|
| 1 | R/W/X ihlali | `SIGSEGV` | access violation | anlık son |
| 2 | yığına taşma | `SIGSEGV` | stack overflow | anlık son |
| 3 | yakalayıcı var mı? | sinyal yakalayıcı | SEH/`__try` | nadiren kurtarılır |
| 4 | kurtarma mümkün mü? | tasarıma bağlı | tasarıma bağlı | **genelde hayır** |
| 5 | son kayıt | çekirdek günlüğü | olay günlüğü | `-observability` dalı |

**İlke S3:** Bellek ihlali akışının **uygulama tarafı kurtarma adımı yoktur**;
en iyi senaryo "son çalışmayı diske yazıp temiz kapanmaktır". Kurtarma
mecburiyeti gibi görünen her tasarım bu dosyada `⚠️ VERIFICATION REQUIRED`
olarak işaretlenir.

### §2.4 Baskı ve sürme akışı

| Adım | Eşik/olay | Kaynak | Sonuç |
|---|---|---|---|
| 1 | kullanılabilir bellek azalır | OS sayacı | — |
| 2 | önceliklendirme devreye girer | LRU benzeri politika | sürme adayı seçilir |
| 3 | sayfa diske yazılır | disk yazması | fiziksel kazanç |
| 4 | erişim gelir | sayfa hatası | geri getirme (gecikme) |
| 5 | döngü sürerse | thrash | CPU/IO israfı |
| 6 | OOM değerlendirmesi | Linux OOM killer / Windows baskı | süreç sonu riski |

### §2.5 Ölçüm akışı — OS arayüzleri (kavram, çalıştırılmadı)

| Platform | Arayüz | Ne ölçer | Kaynak (§7) |
|---|---|---|---|
| Linux | `ps` / `/proc/self/status` | RSS, VSZ, sayfa hatası | upstream docs |
| Linux | `free` / `/proc/meminfo` | sistem belleği, swap | upstream docs |
| Windows | `GlobalMemoryStatusEx` | fiziksel/sanal bellek durumu | W-W2 |
| Windows | `GetProcessMemoryInfo` | süreç çalışma kümesi | W-W3 |
| Windows | performans sayaçları | uzun süreli izleme | §6.6 (PLANNED) |
| PHP | `memory_get_usage(true)` | betik tahsisi | repo'da 0 isabet |
| PHP | `memory_get_peak_usage(true)` | süreç zirvesi | repo'da 0 isabet |

**Durum:** Bu tablodaki hiçbir arayüz bu turda **çalıştırılmadı**; tablo
arayüz/kapsam sözleşmesidir, ölçüm sonucu değil (anayasa §4 İlke B10).

### §2.6 Tahsis ömrü akışı — kısa ömürlü vs uzun ömürlü

| Ömür | Tipik kullanım | Repo örneği | Serbest bırakma |
|---|---|---|---|
| istek ömrü | geçici dizi/tampon | `MemoryAdapter` kullanımı | betik sonu / `unset` |
| süreç ömrü | uzun ömürlü değişken | worker değişkenleri (repo'da kanıt yok) | süreç sonu |
| paylaşımlı ömür | süreçler arası önbellek | `ApcuAdapter` | TTL / `apcu_delete` |
| dosya ömrü | diske yazılmış veri | `FileHandler` | kapatma (`:251`) |
| kalıcı ömür | veritabanı/dosya | bu dalın konusu değil | — |

**İlke S5:** Ömür kısa ise bellek otomatik geri verilir; ömür uzun ise
**explicit** serbest bırakma gerekir ve unutulursa H2 (§3) oluşur. Paylaşımlı
ömürde serbest bırakma **bir süreç için değil tüm süreçler için** yapılır —
TTL dışında elle silme diğer okuyucuları etkiler.

### §2.7 Kopyalama noktaları — bellek nerede ikiye katlanır

| Nokta | Ne kopyalanır | Ek yük | Önleme (kavram) |
|---|---|---|---|
| copy-on-write | yazma anında sayfa | tek yazma maliyeti | OS kendi uygular |
| PHP array assignment | dizi kopyası | tahsis + kopya | referans/`&` |
| string birleştirme | yeni string | tahsis + kopya | tek geçişte kurma |
| dosyadan okuma tamponu | dosya → bellek | tampon boyu | parçalı okuma |
| log yazımı | veri → dosya tamponu | tampon boyu | tampon boyu ölçümü (PLANNED) |
| önbellek yazma | veri → paylaşımlı alan | iki kopya (süreç + paylaşımlı) | TTL + limit |

**Kopyalama kuralı:** Her kopya bir **tahsis** ve bir **serbest bırakma**
borcu doğurur; borcun sahibi kopyayı yaratan katmandır. Bu tabloda repo
karşılığı olmayan satırlar kavram düzeyinde kalır.

## §3 Hata modları — belleğe özgü arızalar

| # | Hata modu | Belirti | Kök neden | Müdahale |
|---|---|---|---|---|
| H1 | süreç zirvesi taşması | `Allowed memory size exhausted` | tahsis döngüsü / büyük veri | limit + akış kesme |
| H2 | açık dosya/bellek tutma | RSS artışı, geri verilmez | `unset` unutulan tutucular | temizleme (RAII benzeri) |
| H3 | paylaşımlı önbellek taşması | `apc` reddi / yer yok | TTL + boyut politikası | TTL/limit ayarı |
| H4 | parçalanma | tahsis hatası, boşluk var | sık ekle/sil | dizi yeniden kurma |
| H5 | ihlal sonu | `SIGSEGV`/access violation | işaretçi/kopya hatası | kod incelemesi |
| H6 | baskı/thrash | sistem yavaş, IO dolu | fiziksel yetersiz | limit + ölçekleme |
| H7 | OOM sonlandırma | süreç aniden biter | baskı | eşik/ölçüm |
| H8 | ölçüm yanlışlığı | yanlış alarm | §4.1 tuzakları | yöntem kaydı |

### §3.1 Hata modu × repo yüzeyi eşlemesi

| Hata modu | Repo'da tetiklenebilir mi? | Kanıt | Yorum |
|---|---|---|---|
| H1 | evet (büyük veri işlerinde) | eşiği ölçen kod **YOK** | ölçüm eksik → `UNKNOWN` |
| H2 | olası | `FileHandler` dosya tutuyor | kapatma satırı `FileHandler.php:251` |
| H3 | evet | `ApcuAdapter.php` | TTL/limit satırları §6 |
| H4 | olası | `MemoryAdapter` dizi yeniden kurulumu | ölçüm yok |
| H5 | PHP'de nadir | extension/işaretçi hatası | kaynağı PHP çekirdeği |
| H6 | evet (sistem geneli) | repo ölçümü yok | OS katmanı |
| H7 | evet (sistem geneli) | repo ölçümü yok | OS katmanı |
| H8 | evet | `memory_get_usage` 0 isabet | ölçüm yapılmıyor |

**İlke S4:** Hata modu ile **müdahale** arasında repo kanıtı yoksa, müdahale
önerisi bu dosyada **plan** olarak durur (`PLANNED`), uygulanmış çözüm olarak
yazılmaz.

## §4 Test stratejisi — bu dalın doğrulama yaklaşımı

| # | Test | Ne doğrular | Yöntem | Durum |
|---|---|---|---|---|
| T1 | tahsis/serbest bırak döngüsü | sızıntı yok | tekrarlı çalıştırma + zirve ölçümü | PLANNED |
| T2 | paylaşımlı önbellek TTL | TTL davranışı | APCu TTL denemesi | PLANNED |
| T3 | limit aşımı davranışı | hata tipi | düşük `memory_limit` | PLANNED |
| T4 | dosya tutma temizliği | açık dosya | kapatma doğrulaması | PLANNED |
| T5 | OS sayacı okuma | ölçüm arayüzü | `ps`/`GlobalMemoryStatusEx` | PLANNED (çalıştırılmadı) |
| T6 | ölçüm doğruluğu (§4.1) | tuzaklar | bilinen tahsis ile karşılaştırma | PLANNED |

**Test kuralı:** T1–T6 bu turda **çalıştırılmadı**; satırlar test tasarımı
sözleşmesidir. Test kodu bu dosyaya **yazılmaz** — test dalının işidir
(anayasa §7).

### §4.1 Test × hata modu kapsama matrisi

| Hata modu → Test ↓ | H1 | H2 | H3 | H4 | H5 | H6 | H7 | H8 |
|---|---|---|---|---|---|---|---|---|
| T1 (tahsis döngüsü) | ✓ | ✓ | — | ✓ | — | — | — | — |
| T2 (TTL) | — | — | ✓ | — | — | — | — | — |
| T3 (limit aşımı) | ✓ | — | — | — | — | — | — | ✓ |
| T4 (dosya tutma) | — | ✓ | — | — | — | — | — | — |
| T5 (OS sayacı) | — | — | — | — | — | ✓ | ✓ | ✓ |
| T6 (ölçüm doğruluğu) | — | — | — | — | — | — | — | ✓ |

**Kapsama yorumu:** `—` hücresi "o test o modu kapsamaz" demektir; H5 (ihlal)
bu dalda **test edilemez** (PHP seviyesinde üretilmesi güvensizdir) — yalnız
kod incelemesiyle ele alınır. Bu matris kapsama **hedefidir**, çalıştırılmış
sonuç **değildir**.

### §4.2 Doğrulama kapıları — ne zaman "bitti" denir

| Kapı | Şart | Kanıt türü | Durum |
|---|---|---|---|
| K1 | ölçümler alındı (Ö-S1–Ö-S8) | ortam çıktısı | ❌ alınmadı |
| K2 | eşikler yazıldı | ölçüm sonrası değer | ❌ yazılmadı (İlke B10) |
| K3 | T1–T6 çalıştırıldı | test çıktısı | ❌ çalıştırılmadı |
| K4 | olumsuz kanıt tazelendi | grep kaydı | ✅ §6.2 (2026-10-13) |
| K5 | repo satırları doğrulandı | ReadAllLines | ✅ §6 (2026-10-13) |
| K6 | URL'ler HEAD 200 | HTTP yanıtı | ✅ §7 (2026-10-13) |

**Kapı kuralı:** K1–K3 açık olduğu için bu dosya `draft` statüsünden çıkmaz;
K4–K6 yalnızca "kanıt tazelendi" der, "ölçüldü" **demez**.

## §5 Ölçüm — bu dalın ölçüm ve eşik tablosu

| # | Ölçü | Arayüz | Birim | Ölçüldü mü? |
|---|---|---|---|---|
| Ö-S1 | süreç RSS | `ps` / `/proc` | KiB | hayır → `UNKNOWN` |
| Ö-S2 | çalışma kümesi (Windows) | `GetProcessMemoryInfo` | bayt | hayır → `UNKNOWN` |
| Ö-S3 | sistem kullanılabilirliği (Windows) | `GlobalMemoryStatusEx` | bayt | hayır → `UNKNOWN` |
| Ö-S4 | betik tahsisi | `memory_get_usage(true)` | bayt | hayır → `UNKNOWN` |
| Ö-S5 | betik zirvesi | `memory_get_peak_usage(true)` | bayt | hayır → `UNKNOWN` |
| Ö-S6 | APCu kullanımı | APCu sayaçları | bayt | repo'da okuma yok → `UNKNOWN` |
| Ö-S7 | sayfa hatası sayısı | OS sayacı | adet | hayır → `UNKNOWN` |
| Ö-S8 | swap kullanımı | `free` / OS | MiB | hayır → `UNKNOWN` |

**Eşik durumu:** Ö-S1–Ö-S8 için **hiçbir eşik değeri yazılmadı** — ölçüm
yapılmadan eşik uydurmak bu dalın yasağıdır (İlke B10). Eşik hücreleri
`UNKNOWN` kalır ve ölçüm PLANNED'dir.

### §5.1 Ölçüm birim dönüşümü — dikkat edilecekler

| Kaynak | Birim | Yaygın hata | Doğrusu |
|---|---|---|---|
| `/proc` RSS | KiB | bayt sanma | 1024 ile çarpım |
| PHP `memory_get_*` | bayt | KiB sanma | 1024'e bölme |
| Windows bellek API | bayt | KiB sanma | 1024'e bölme |
| `free -m` | MiB | bayt sanma | 1048576 ile çarpım |
| sayfa hatası | adet | süre sanma | yalnız sayaç |

### §5.2 Ölçüm sözleşmesi — kayıt şeması (henüz üretilmedi)

| Alan | Tip | Örnek değer | Durum |
|---|---|---|---|
| ts | ISO 8601 | (üretilmedi) | `UNKNOWN` |
| host | dize | (üretilmedi) | `UNKNOWN` |
| metric | dize | `rss_kib` | tanım var, değer yok |
| value | sayı | (üretilmedi) | `UNKNOWN` |
| unit | dize | `KiB` | sabit |
| method | dize | `ps` / `GlobalMemoryStatusEx` | seçilmedi (Q-S2) |

**Şema kuralı:** Kayıt şeması bu dalda **tanımlıdır**; satırlar üretim
verisi **değildir**. Gerçek satır üretildiğinde `unit` alanı zorunludur —
birimsiz metrik bu dalda geçersizdir (§5.1 tuzakları).

### §5.3 Eşik yazım kuralı — meşru eşik nasıl doğar

| Adım | Gereken | Şu anki durum |
|---|---|---|
| 1 | ölçüm al (Ö-S1–Ö-S8) | ❌ yapılmadı |
| 2 | örneklem topla (≥N okuma) | ❌ yapılmadı |
| 3 | dağılımı gör (ortalama/zirve) | ❌ yapılmadı |
| 4 | eşik öner (zirve + pay) | ❌ yazılmaz |
| 5 | alarm kuralı bağla | ❌ yazılmaz |
| 6 | ADR/vault kaydı | ❌ beklemede |

**Yasak:** Adım 1 tamamlanmadan eşik hücresine sayı yazmak **uydurmadır** ve
bu dalın zero-hallucination kuralıyla çakışır; tablolar bu yüzden `UNKNOWN`
ile işaretlidir.

## §6 Repo kanıtı — dosya ve satır (ReadAllLines ile birebir)

| Dosya | Satır | Ne yapar | İlişki |
|---|---|---|---|
| `shared/src/Cache/MemoryAdapter.php` | 8 | sınıf başlığı/`namespace` sınırı | depo kimliği |
| `shared/src/Cache/MemoryAdapter.php` | 25–28 | diziye değer yazma | tahsis yüzeyi |
| `shared/src/Cache/MemoryAdapter.php` | 49 | dizi okuma/erişim | okuma yüzeyi |
| `shared/src/Cache/ApcuAdapter.php` | 13 | `apcu` arayüzüne bağımlılık | paylaşımlı bellek |
| `shared/src/Cache/ApcuAdapter.php` | 22 | önbelleğe yazma | tahsis (paylaşımlı) |
| `shared/src/Cache/ApcuAdapter.php` | 38 | önbellekten okuma | okuma |
| `shared/src/Log/FileHandler.php` | 251 | dosya kapatma | tutucu serbest bırakma |

**Olumsuz kanıt (repo taraması, 2026-10-13):**

| Arama | İsabet | Yorum |
|---|---|---|
| `memory_get_usage` | 0 | süreç içi ölçüm **yok** |
| `memory_get_peak_usage` | 0 | zirve ölçümü **yok** |
| `mmap` / `VirtualAlloc` | 0 | doğrudan bellek API'si **yok** |
| `pcntl` / bellek limiti kodu | 0 | süreç/bellek limiti kodu **yok** |

**Dürüstlük cümlesi:** Repo'da OS seviyesi bellek kodu YOK — bu dosyadaki OS
akışlarının kaynağı upstream docs (§7); repo satırları yalnız PHP önbellek
yüzeyini gösterir.

### §6.1 Repo kod örnekleri — okunabilir alıntılar (salt-okunur alıntı)

**A) `MemoryAdapter.php` — dizi deposu (tahsis/serbest bırakma yüzeyi)**

```php
// Dosya: shared/src/Cache/MemoryAdapter.php (satır 1-8 arası kimlik/namespace)
// Amaç: bellekte çalışan geçici önbellek deposu
// Kural: bu deposun içeriği süreç sonunda kaybolur (kalıcı DEĞİL)
```

| Satır | Davranış | Bellek etkisi |
|---|---|---|
| 8 | sınıf/namespace tanımı | — |
| 25–28 | değeri diziye yazma | tahsis (array slot) |
| 49 | değeri okuma | tahsis YOK (okuma) |

**B) `ApcuAdapter.php` — paylaşımlı bellek (APC/user cache)**

```php
// Dosya: shared/src/Cache/ApcuAdapter.php
// Satır 13: apcu arayüzüne bağımlılık — paylaşımlı bellek sağlayıcısı
// Satır 22: yazma — süreçler arası ortak alana tahsis
// Satır 38: okuma — paylaşılan alandan erişim
```

| Satır | Davranış | Paylaşımlı mı? |
|---|---|---|
| 13 | sağlayıcı bağımlılığı | evet (ortak bellek) |
| 22 | `apcu` yazma | evet |
| 38 | `apcu` okuma | evet (değişmez) |

**C) `FileHandler.php` — dosya tutucunun serbest bırakılması**

```php
// Dosya: shared/src/Log/FileHandler.php
// Satır 251: dosya kapatma — tutucunun serbest bırakılması
// İlkeyle ilgisi: açık tutulan dosya = süreç boyunca geri verilmeyen kaynak
```

**Alıntı kuralı:** Yukarıdaki bloklar **açıklama alıntısıdır**, kodun tam
kopyası değildir; her satır kaynağı dosya/numara ile işaretlidir. Kodun tam
metni repo dosyasında okunur — bu dosyaya kopyalanmaz.

### §6.2 Ölçüm eksikliğinin olumsuz kanıtı — ne arandı, ne bulunmadı

| # | Aranan | Yol / kapsam | İsabet | Sonuç |
|---|---|---|---|---|
| N1 | `memory_get_usage` | tüm `*.php` | 0 | süreç içi ölçüm yok |
| N2 | `memory_get_peak_usage` | tüm `*.php` | 0 | zirve ölçümü yok |
| N3 | `memory_limit` | tüm `*.php` | 0 | limit denetimi kodda yok |
| N4 | `apc_sma_info` | tüm `*.php` | 0 | APCu SMA okuması yok |
| N5 | `unset(` (bellek amaçlı) | `shared/src/Cache` | özel tarama | bellek amaçlı `unset` kanıtı yok |
| N6 | `str_repeat` (mask eleme) | `shared/src/Log` | rapor `-ornekler` | maskeleme bellekte geçici kopya üretir |

**Yorum kuralı:** 0 isabet, "**özellik yoktur**" demek değildir — "**bu repoda
o ölçüme/koda rastlanmadı**" der. Fark yazılmazsa yanlış iddia doğar.

### §6.3 Baskı senaryoları — neyin neyi tetiklediği

| Senaryo | Tetikleyici | Sonuç | Önleme (plan) |
|---|---|---|---|
| S1 | tek istekte büyük dizi | zirve taşması riski | sayfa-parcası işleme (PLANNED) |
| S2 | APCu önbelleği dolu | yazma reddi | TTL/limit ölçümü (PLANNED) |
| S3 | log tamponu uzun açık | bellek + dosya tutma | kapatma kanıtı (`FileHandler.php:251`) |
| S4 | süreç tekrar tekrar (worker) | birikim | ölçülmüyor → `UNKNOWN` |
| S5 | sistem belleği dolu | baskı → thrash/OOM | OS ölçümü (§5) PLANNED |

**Senaryo kuralı:** Her senaryo "mümkün" olarak işaretlenir; gerçekleşme
sıklığı ölçülmeden "oluyor/olmuyor" **denmez**.

### §6.4 İhlal sonrası teşhis akışı (kavramsal — çalıştırılmadı)

| Adım | Eylem | Araç | Durum |
|---|---|---|---|
| 1 | sonlanma biçimi kaydı | çekirdek günlüğü | PLANNED |
| 2 | son zirve değeri | `memory_get_peak_usage` | repo'da yok |
| 3 | son zirve değeri (OS) | `GetProcessMemoryInfo` | çalıştırılmadı |
| 4 | hangi kod satırı? | PHP hata günlüğü | ortam kanıtı yok |
| 5 | tekrar üretme | düşük limit denemesi | PLANNED |
| 6 | düzeltme doğrulama | T1 testi (§4) | PLANNED |

**Kural:** Teşhis akışının adımları bu dosyada **sözleşme** olarak durur;
hiçbiri bu turda çalıştırılmadı (`UNKNOWN`).

### §6.5 Ölçüm araçları envanteri — hangi platformda ne okunur

| Araç/Komut | Platform | Ne verir | Durum |
|---|---|---|---|
| `ps -o pid,rss,vsz,comm` | Linux | süreç RSS/VSZ | çalıştırılmadı |
| `/proc/self/status` | Linux | VmRSS/VmSwap ayrıntısı | çalıştırılmadı |
| `free -m` | Linux | sistem bellek/swap | çalıştırılmadı |
| `GlobalMemoryStatusEx` | Windows | sistem bellek durumu | çalıştırılmadı |
| `GetProcessMemoryInfo` | Windows | süreç çalışma kümesi | çalıştırılmadı |
| performans sayaçları (Windows) | Windows | uzun süreli izleme | çalıştırılmadı |
| `memory_get_usage(true)` | PHP | betik tahsisi | repo'da çağrı yok |
| APCu sayaçları | PHP | paylaşımlı önbellek | repo'da okuma yok |

**Çalıştırma durumu sütunu zorunludur:** Bu tabloda "çalıştırılmadı" yazan
hiçbir değer ölçüm sonucu olarak kullanılamaz.

### §6.6 Performans sayaçları — izleme sözleşmesi (Windows, PLANNED)

| Sayaç | Ne sayar | Birim | Alarm? |
|---|---|---|---|
| Working Set | fiilen kullanılan sayfa | bayt | `UNKNOWN` |
| Private Bytes | sürece özel fiziksel pay | bayt | `UNKNOWN` |
| Page Faults/sn | sayfa hatası hızı | adet/sn | `UNKNOWN` |
| Page File Bytes | diske taşınan sayfa | bayt | `UNKNOWN` |
| Committed Bytes | taahhüt edilen toplam | bayt | `UNKNOWN` |

**Sayaç kuralı (İlke B11 ile uyumlu):** Windows sayaçları Linux RSS/VSZ ile
**eşleştirilmez**; her platform kendi sayacını kendi birimiyle raporlar.
Ölçüm yapılmadan hiçbir sayaca eşik yazılmaz.

### §6.7 Uygulama katmanı ile OS katmanı — sorumluluk tablosu

| Soru | Uygulama (PHP) | OS | Sınır kuralı |
|---|---|---|---|
| tahsisi kim ayırır? | PHP çekirdeği | çerçeve tahsisi | ikisi ayrı ölçülür |
| ihlali kim yakalar? | yakalayamaz | evet (§2.3) | kurtarma uygulamada yok |
| limiti kim koyar? | `memory_limit` (php.ini) | OOM/limit | ikisi ayrıdır |
| ölçümü kim yapar? | `memory_get_*` | RSS/VSZ | eşitlenmez (İlke B9) |
| paylaşımlı belleği kim yönetir? | APCu | shm/OS | ikisi ayrı ölçülür |
| diske yazma kimin işi? | uygulama (`FileHandler`) | sayfa önbelleği | iki tampon ayrıdır |

**Sorumluluk kuralı:** Bir arıza tabloda hangi sütundaysa, müdahale de o
sütundadır; uygulama sütunundaki arıza OS aracıyla **onarılmaz** (ve tersi).

### §6.8 Açık sorular — bu dosyanın kapatılmayanları

| # | Soru | Etiket | Kapanış şartı |
|---|---|---|---|
| Q-S1 | Üretim bellek eşiği (PHP `memory_limit`)? | UNKNOWN | php.ini ortam okuması |
| Q-S2 | Windows/Linux izleme aracından hangisi? | ⚠️ VERIFICATION REQUIRED | platform kararı (Q-B8) |
| Q-S3 | APCu kapasitesi/TTL üretim değeri? | UNKNOWN | üretim okuması |
| Q-S4 | Sayfa hatası hızı ölçümü ne sıklıkla? | PLANNED | monitoring dalı |
| Q-S5 | `FileHandler.php:251` dışında kaç açık tutucu var? | UNKNOWN | tam dosya taraması yapılmadı |

## §7 Kaynakça — 3 URL, HTTP HEAD 200, envanter kesişimi 0

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| W-W1 | `VirtualFree` — sanal bellek aralığını serbest bırakır (İlke B12) | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualfree | 2026-10-13 | 200 |
| W-W2 | `HeapAlloc` — süreç yığınından (heap) tahsis yapar (§1.1) | https://learn.microsoft.com/en-us/windows/win32/api/heapapi/nf-heapapi-heapalloc | 2026-10-13 | 200 |
| W-W3 | `GetProcessMemoryInfo` — süreç bellek sayacı okur (§2.5) | https://learn.microsoft.com/en-us/windows/win32/api/psapi/nf-psapi-getprocessmemoryinfo | 2026-10-13 | 200 |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
→ HTTP 200 (2026-10-13). Envanter kesişimi: `.ai/architecture/**` içindeki
benzersiz URL kümeleriyle karşılaştırıldı → **0**.

`-anayasa` (memfd_create · msync · swapon) ve `-ornekler` (core.5) kendi
URL'lerini ayrı taşır; aynı URL iki dosyada tekrarlanmaz.

### §7.1 Kaynak iddia eşlemesi — hangi bölüm hangi kaynağa dayanır

| Bölüm | İddia türü | Dayandığı kaynak | Sınıf |
|---|---|---|---|
| §1.1 (PHP tahsis) | repo davranışı | `MemoryAdapter`/`ApcuAdapter` satırları | repo |
| §2.1–§2.4 (akışlar) | OS davranışı | anayasa §3 + upstream docs | upstream |
| §2.5 (ölçüm arayüzü) | OS API | W-W1–W-W3 (§7) | upstream |
| §3 (hata modları) | genel + repo eşlemesi | repo satırları + tarama | karma |
| §4 (test) | tasarım | bu dalın sözleşmesi | plan |
| §5 (ölçüm) | sözleşme | anayasa §4 | sözleşme |
| §6 (repo kanıtı) | dosya/satır | ReadAllLines (2026-10-13) | repo |
| §6.2 (olumsuz) | yokluk kanıtı | grep 0 isabet | repo |

**Sınıf kuralı:** Kaynak sınıfı (**repo** / **upstream** / **plan** /
**sözleşme**) her bölümde açıkça yazılır; karışık satırda her iki sınıf da
sütunla ayrılır. Ölçüm sonucu sınıfı **yoktur** — ölçüm yapılmadığı için.

### §7.2 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0–§7) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | 3 URL HEAD 200 doğrulandı | kanıt kapısı (K6) |
| 2026-10-13 | repo satırları ReadAllLines ile doğrulandı | kanıt kapısı (K5) |
| 2026-10-13 | olumsuz kanıt tazelendi (grep 0) | kanıt kapısı (K4) |

**Bu dosyanın sayısal özeti:** 8 alt bölüm (§0–§7) · 6 akış (§2.1–§2.6) ·
8 hata modu (§3) · 6 test (§4) · 8 ölçüm sözleşmesi (§5) · 7 repo satırı +
6 olumsuz kanıt (§6) · 3 URL (§7). Eşik değeri: **0** (hepsi `UNKNOWN`).
Ölçüm çalıştırıldı mı? **Hayır** — kapı K1–K3 açık, dosya `draft` kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
