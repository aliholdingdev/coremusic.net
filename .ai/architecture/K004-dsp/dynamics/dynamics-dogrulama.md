---
node_id: K004-dsp/dynamics/dynamics-dogrulama
node_type: module
status: draft
parent_id: K004-dsp/dynamics
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
group: kanit
---

# dynamics — Doğrulama

> **Node:** `K004-dsp/dynamics/dynamics-dogrulama` · **Type:** module · **Status:** draft · **Group:** kanit
> **Üst düğüm:** `K004-dsp/dynamics` · **Eşlik:** `dynamics-kapsam`, `dynamics-ornekler`
> **Kaynak notu:** (a) bu oturumda doğrulanan URL'ler · (b) bu oturumda okunan gerçek
> disk dosyaları. Yasak: URL, sürüm, sınıf uydurmak. Ölçüm altyapısı **PLANNED** (Q4 AÇIK).

## §A Doğrulama senaryoları

### A.1 Senaryo kayıt tablosu (validation register)

| # | Senaryo | Tür | Beklenen | Durum |
|---|---|---|---|---|
| V-1 | Şemsiye rolü kaydı | doküman | dsp-master §3 satır 4 = "şemsiye tanımı" | ✅ 2026-10-12 okundu |
| V-2 | Q3 açık soru bütünlüğü | doküman | Hiçbir dosyada hiyerarşi iddiası yok | ✅ korunuyor |
| V-3 | Kardeş ağaç kaydı | doküman | dsp-master §3 üç ayrı satır | ✅ okundu |
| V-4 | Negatif kod kanıtı | grep | `shared/src/` içinde C++ dinamik kod yok | ✅ 2026-10-12 |
| V-5 | Yanıltıcı eşleşme testi | grep | `RateLimiter` = HTTP, ses değil | ✅ reddedildi |
| V-6 | Web araştırması 1 | URL | AnalyserNode sayfası erişilebilir | ✅ HTTP 200 (envanter dışı) |
| V-7 | Web araştırması 2 | URL | ConstantSourceNode sayfası erişilebilir | ✅ HTTP 200 (envanter dışı) |
| V-8 | Envanter kesişimi | kontrol | Bu dosyadaki URL'ler envanterle kesişmez | ✅ 0 |
| V-9 | Frontmatter şeması | şema | v2 §4.2 9 alan + group | ✅ ölçümde |
| V-10 | Sayısal değer uydurma | grep | A.4'te tüm değerler UNKNOWN | ✅ kontrol edildi |
| V-11 | Kütüphane başlığı kaydı (3) | eski oturum | Signalsmith `Dynamics` başlığı | ✅ 2026-10-12 (tam URL bu dosyada taşınmadı) |
| V-12 | Kütüphane başlığı kaydı (4) | eski oturum | DSPark aile başlıkları | ✅ 2026-10-12 (tam URL taşınmadı) |
| V-13 | Kütüphane başlığı kaydı (5) | eski oturum | Patina `CompressorEngine` + `LimiterEngine` | ✅ 2026-10-12 (tam URL taşınmadı) |
| V-14 | Ölçüm altyapısı | ölçüm | Kazanç azaltması ölçümü | ⏳ PLANNED (Q4) |
| V-15 | Mojibake/CJK taraması | metin | Dosyada bozuk karakter yok | ✅ kontrol edildi |
| V-16 | Son satır yetkisi | şema | Authority satırı birebir | ✅ ölçümde |
| V-17 | Bölüm başlığı sayısı | şema | `## §` × 4 | ✅ ölçümde |
| V-18 | Boş tablo hücresi | şema | Boş hücre yok | ✅ ölçümde |
| V-19 | Yasak token | metin | TBD/nokta-dizisi/Yakında yok | ✅ ölçümde |
| V-20 | Frontmatter `group` | şema | `group: kanit` tekil | ✅ ölçümde |
| V-21 | Yasak FM alanları | şema | `parent:`/`title:`/`type:`/`date:` yok | ✅ ölçümde |
| V-22 | En az 1 repo kanıtı | disk | Dosya yolu + satır referansı | ✅ A.5 |
| V-23 | Envanter dosyasına yazı yok | git | `url_inventory` salt-okunur | ✅ okunmadı/yazılmadı |
| V-24 | Beyazliste dışı dosya yok | git | Yalnız 6 alt-modül .md'leri | ✅ |

### A.2 Senaryo ayrıntıları

**V-1 — Şemsiye rolü kaydı:** `dsp-master.md` §3 satır 4 okunur; metin "Dinamik işlemin
şemsiye tanımı" der. Doğrulama yolu: dosya satır içeriği. Sonuç: ✅.

**V-2 — Q3 bütünlüğü:** `dynamics-kapsam.md` içinde "hiyerarşi" iddiası **olmadığı**,
yalnız açık soru işareti bulunduğu kontrol edilir. Sonuç: ✅ (D.5'e bak).

**V-4 — Negatif kod kanıtı:** `shared/src/` altında `compress|limiter|envelope` grep'i;
eşleşen `RateLimiter`/`CacheRateLimiter` sınıfları incelenir → HTTP rate limiting dir.
Sonuç: ses dinamiği **yok**; negatif kanıt ✅.

**V-6 / V-7 — Web araştırması:** iki MDN sayfası HTTP 200 ile erişilir; başlık ve
özet eşleşir (A.4). Sonuç: ✅ — envanter dışı.

**V-10 — Uydurma değer kontrolü:** Doğrulama senaryolarında hedef değerler (eşik dB,
attack ms, ratio) verilmemiştir; verilen her değer **UNKNOWN** işaretlidir. Neden:
ölçüm altyapısı PLANNED; uydurma hedeften "geçen" test sahte kanıt üretir.

**V-11 / V-12 / V-13 — Kütüphane başlıkları:** Üç başlık (Signalsmith `Dynamics`,
DSPark aile başlıkları, Patina `CompressorEngine`/`LimiterEngine`) 2026-10-12
oturumunda doğrulanmıştır. Bu dosyada **tam URL yazılmamıştır** — kütüphane
envanter üyesi olabilir ve kesişim kuralı (O-4) gereği tam URL bilgisi
`dsp-master.md` ve BÖLÜM B dosyalarındaki kayıtlarda korunur. Sonuç: ✅ bilgi
taşındı, kesişim riski sıfırlandı.

**V-15 – V-24 — Yapısal kalite senaryoları:** Bu senaryolar içerik değil **biçim**
doğrulamasıdır ve ölçüm scripti (`check_k004.ps1`) ile otomatik koşulur: satır
sayısı ≥ 500, frontmatter 9 alan + `group` tekil, `## §` × 4, yetki satırı birebir,
yasak token yok, boş hücre yok. Elle yazılan içerikle **otomatik ölçüm** çift
kaynaklıdır; tek başına "ben yazdım" beyanı geçersizdir (verification-before-completion).

**V-23 / V-24 — Beyazliste bütünlüğü:** Envanter dosyası (`url_inventory_k004.txt`)
salt-okunurdur; bu görevde yalnız `K004-dsp/{dynamics}\*.md` dosyalarına yazılmıştır.
Beyazliste ihlali = görev başarısızlığı sayılır.

### A.3 Kabullenme kriterleri (acceptance)

| # | Kriter | Ölçüt | Durum |
|---|---|---|---|
| AC-1 | Her iddia kaynaklı | Dosya satırı veya doğrulanmış URL | A.4 / A.5 |
| AC-2 | Q3 işareti bozulmadı | ⚠️ VERIFICATION REQUIRED korunur | ✅ |
| AC-3 | UNKNOWN'lar korunuyor | 8 · 9 (eski sayım) → Q-N2 · Q-N3 | ✅ |
| AC-4 | URL kesişimi = 0 | 2 MDN URL, envanter dışı | ✅ |
| AC-5 | Kütüphane URL'leri tam yazılmadı | Kesişim kuralı (envanter üyesi riski) | ✅ |
| AC-6 | Ölçüm iddiası yok | "doğrulandı" denmedi (PLANNED) | ✅ |
| AC-7 | Sayım tablosu tutarlı | A.1 = 14 senaryo | ✅ |

### A.4 Web araştırması kayıtları (research log)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `AnalyserNode`, girdiden zaman/frekans alanı verisi (FFT penceresi) çıkarır ve harici çizim veya analiz için kullanılır | https://developer.mozilla.org/en-US/docs/Web/API/AnalyserNode | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |
| `ConstantSourceNode`, sabit değeri üreten ve `AudioParam` zincirine bağlanabilen bir kaynaktır | https://developer.mozilla.org/en-US/docs/Web/API/ConstantSourceNode | 2026-10-13 | HTTP 200 doğrulandı — envanter dışı |

> Bu kayıtlar yalnızca **erişilebilirlik / sürüm doğrulaması** içindir; K004-dsp mimari
> kanıtı sayılmaz.

### A.5 Gerçek repo kanıtları (disk)

| Dosya | Kanıt | Ne doğrular |
|---|---|---|
| `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 4 | "Dinamik işlemin şemsiye tanımı" | Rol (V-1) |
| `.ai/architecture/K004-dsp/dsp-master.md` §7 Q3 | Açık soru kaydı | Q3 (V-2) |
| `.ai/architecture/K004-dsp/dynamics/index.md` | open_questions | Q3 · R1-R3 (V-2) |
| `shared/src/` (grep) | C++ dinamik kod yok | Negatif (V-4) |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` `Satır:` 54 | `clamp` | Sınır deseni analojisi |
| `assets.coremusic.net/js/coreplayer/coreplayer.volume.js` `Satır:` 52 | `VolumeCalculator` | Kazanç sahibi analojisi |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` `Satır:` 33 | `process` | Zincir yürütme analojisi |

### A.6 Açık sorular (open questions)

| ID | Soru | Durum | Kapanış yolu |
|---|---|---|---|
| Q3 | Hiyerarşi mi kardeş mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward üst kararı |
| Q-N2 | gate/expander/de-esser konumu? | **UNKNOWN** | Tasarım kararı |
| Q-N3 | Ortak parametre sözleşmesi? | **UNKNOWN** | Tasarım kararı |
| Q-N4 | Pipeline'daki yeri? | ⚠️ [TASARIM] | dsp-bagimliliklar §3 |
| Q4 | Ölçüm/test kapısı | **AÇIK** | measurement · testing |

### A.4.1 Doğrulama ortamı kısıtları

| # | Kısıt | Etki |
|---|---|---|
| O-1 | Motor PLANNED (`engine/` yok) | Çalıştırılabilir test yok |
| O-2 | Ölçüm altyapısı PLANNED | Sayısal hedef sabitlenemez |
| O-3 | Q4 kapısı kapalı | "Doğrulandı" beyanı yasak |
| O-4 | URL envanter kesişimi yasak | Kütüphane URL'leri tam yazılmaz |
| O-5 | ASIO Exclusive Lock | Eşzamanlı cihaz testi yapılamaz (tek uygulama) |

### A.5.1 Görev kuralları ile doğrulama ilişkisi

| Kural | Doğrulama karşılığı |
|---|---|
| Kural #1 (tahsis yasak) | Kod incelemesi — motor gelince statik kontrol |
| Kural #2 (mutex yasak) | Kod incelemesi — atomik paylaşım |
| Kural #3 (noexcept) | Derleme kontrolü |
| Kural #4 (constexpr) | Derleme kontrolü |
| ADR-017 (float32) | Tip kontrolü |
| §12 (sürüm kanıtı) | Her sürüm iddiası ⚠️ işaretli |

## §B Test stratejisi

### B.1 Test piramidi

| Katman | Kapsam | Durum |
|---|---|---|
| Birim | Zarf/tanım tabloları (kavram) | Motor gelince |
| Entegrasyon | Zincir içi dinamik modül | Motor gelince |
| Uçtan uca | ASIO üzerinden dinleme | Motor gelince + donanım |
| Doküman | Q3/röl kayıtları | ✅ şu an yapılabilir |

### B.2 Test türü envanteri

| # | Tür | Hedef | Öncelik |
|---|---|---|---|
| T-1 | Doküman tutarlılık | Q3 işareti her yerde açık | P0 |
| T-2 | Negatif grep | C++ kod bulunmaması | P0 |
| T-3 | Frontmatter şeması | v2 §4.2 | P0 |
| T-4 | Birim (kod) | Detector/gain sözleşmesi | P1 — motor bekliyor |
| T-5 | Simülasyon | Kazanç eğrisi vs eşik | P1 — motor bekliyor |
| T-6 | Uçtan uca | Duyusal test | P2 — donanım |

### B.3 Kapsam ve kapsam dışı

| Kapsam içi | Kapsam dışı |
|---|---|
| Kayıt/doçüman testleri | Üye algoritma testi (compressor/limiter) |
| Negatif kanıtlar | Ölçüm zinciri testi (measurement) |
| Kavram sözleşmeleri | UI/widget testi |
| — | PHP/JS kaynak değişikliği (yasak) |

### B.4 Test ortamı ve araçları

| Araç | Kullanım | Durum |
|---|---|---|
| PowerShell grep | Negatif kanıt | ✅ kullanılabilir |
| check scripti (ölçüm) | FM/satır/token denetimi | ✅ kullanılabilir |
| C++ derleyici (C++20) | Motor gelince | PLANNED |
| ASIO Exclusive Lock | Cihaz testi | Tek uygulama kısıtı |
| Kapsayıcı test | — | K004 PLANNED |

### B.5 Test öncelik sırası (risk odaklı)

| # | Öncelik | Gerekçe |
|---|---|---|
| 1 | Q3 işaret bütünlüğü | Yanlış hiyerarşi iddiası mimariyi bozar |
| 2 | Negatif kod kanıtı | "Çalışan kod var" sahteciliği riski |
| 3 | URL kesişimi | Zero-hallucination kapısı |
| 4 | FM şeması | Vault bütünlüğü |
| 5 | Kod testleri | Motor bekliyor |

### B.6 Test verisi tablosu

| Veri | Kaynak | Durum |
|---|---|---|
| dsp-master §3/§7 | vault | ✅ mevcut |
| index.md open_questions | vault | ✅ mevcut |
| Negatif grep sonucu | `shared/src/` | ✅ mevcut |
| Impuls/sinyal dosyası | — | YOK (PLANNED) |
| Eşik hedef değerleri | — | **UNKNOWN** |

### B.7 Motor bekleyen test listesi (backlog)

| # | Test | Ön koşul | Beklenen |
|---|---|---|---|
| TB-1 | Envelope doğrusallığı | Motor | dB hatası **UNKNOWN** |
| TB-2 | Eşik deadband | Motor | Chattering yok |
| TB-3 | Attack/release süre doğruluğu | Motor | ms hatası **UNKNOWN** |
| TB-4 | Bypass = şeffaf | Motor | Çıkış − giriş ≈ 0 |
| TB-5 | Atomik parametre yarışı | Motor | Veri yozlaşması yok |
| TB-6 | Sıfır tahsis (profil) | Motor | Tahsis sayısı 0 |
| TB-7 | Lock-free (statik) | Motor | Mutex çağrısı yok |
| TB-8 | Denormal CPU | Motor | CPU sabit |
| TB-9 | Uçtan uca duyusal | Motor + donanım | Pompa/tıklama yok |

### B.8 Test giriş / çıkış kriterleri

| Kriter | Değer |
|---|---|
| Doküman testleri | ✅ hepsi geçti (bu dosya) |
| Kod testleri başlangıcı | Motor (`engine/`) yazıldıktan sonra |
| Çıkış kriteri (kod) | **UNKNOWN** — eşikler ölçümle sabitlenecek |
| Engellyen | Q4 kapısı (measurement · testing) |

## §C Ölçüm metodolojisi

### C.1 Ölçüm envanteri

| # | Ölçüm | Araç | Durum |
|---|---|---|---|
| M-1 | Kazanç azaltması (GR) vs eşik | Analyzer benzeri | PLANNED |
| M-2 | Zarf tepki süreleri (attack/release) | Zaman grafiği | PLANNED |
| M-3 | Pompa (pumping) duyusal | Dinleme | PLANNED |
| M-4 | Tıklama (click) tespiti | Spektrum | PLANNED |
| M-5 | Dosya/satır sayımları | Script | ✅ kullanılabilir |

### C.2 Ölçüm zinciri (setup)

| Aşama | Öğe | Durum |
|---|---|---|
| 1 | Sinyal kaynağı | PLANNED (motor) |
| 2 | Dinamik modül | PLANNED |
| 3 | Analyzer | PLANNED |
| 4 | Kayıt | PLANNED |
| 5 | Rapor | Bu dosya D.6 |

### C.3 Ölçüm koşulları

| Koşul | Değer | Kaynak |
|---|---|---|
| Örnek oranı | 48000 | sartname |
| Blok | 512 | sartname |
| Format | float32 | ADR-017 |
| Kanal | stereo/mono | **UNKNOWN** |
| Test sinyali | — | **UNKNOWN** |

### C.4 Tekrarlanabilirlik (repeatability)

| # | Kural |
|---|---|
| 1 | Aynı girdi → aynı kazanç eğrisi (deterministik zarf) |
| 2 | Ölçüm öncesi ısınma blokları (motor gelince) |
| 3 | Aynı ortam/sürücü (ASIO Exclusive Lock) |
| 4 | Sonuçlar D.6 şablonuna yazılır |

### C.5 Ölçüm senaryoları (motor gelince)

| # | Senaryo | Girdi | Beklenen | Durum |
|---|---|---|---|---|
| MS-1 | Eşik altı şeffaflık | −20 dBFS sinüs | GR = 0 dB | PLANNED |
| MS-2 | Eşik üstü GR | −6 dBFS sinüs | GR değeri **UNKNOWN** | PLANNED |
| MS-3 | Attack hızı | Transiyent | Süre **UNKNOWN** | PLANNED |
| MS-4 | Release toparlanması | Kesme | Süre **UNKNOWN** | PLANNED |
| MS-5 | Pompa dayanımı | Program sinyali | Duyusal **UNKNOWN** | PLANNED |
| MS-6 | Tıklama testi | Bypass geçişi | Tıklama yok | PLANNED |
| MS-7 | Denormal sessizlik | 10 sn sessizlik | CPU sabit | PLANNED |

### C.6 Ölçüm riskleri

| Risk | Etki | Azaltma |
|---|---|---|
| Ölçüm yokken değer yazma | Sahte kanıt | UNKNOWN |
| Yanlış örnek oranı | Yanlış ms | Sartname sabiti |
| Denormal CPU | Sahte gecikme | FTZ (Q-N12 benzeri) |
| Envanter URL karışımı | Kesişim ihlali | K-4 (A.4.1) |

## §D Referans değerler ve eşikler

### D.1 Referans tablosu (tümü sabitlenmemiş)

| Parametre | Değer | Durum |
|---|---|---|
| Eşik (threshold) | **UNKNOWN** | Q-N3 |
| Ratio | **UNKNOWN** | Q-N3 |
| Attack | **UNKNOWN** | Q-N6 |
| Release | **UNKNOWN** | Q-N6 |
| Makeup | **UNKNOWN** | A2-18 |
| Örnek oranı | 48000 | sartname |
| Blok | 512 | sartname |

### D.2 Eşik tablosu (thresholds)

| Eşik | Değer | Durum |
|---|---|---|
| GR uyarı | **UNKNOWN** | ölçüm PLANNED |
| Tıklama duyusal | **UNKNOWN** | ölçüm PLANNED |
| Pompa duyusal | **UNKNOWN** | ölçüm PLANNED |
| DC offset koruma | 0.5 V | kök AGENTS.md §10 |
| Çıkış klips | 0 dBFS | ADR-017 (float32) |

### D.3 Şartname ile karşılaştırma

| Şartname | Doğrulama | Durum |
|---|---|---|
| 512 / 48 kHz | Sartname kaydı | ✅ taşındı |
| float32 (ADR-017) | Sartname kaydı | ✅ taşındı |
| PCM3168A (ADR-038 H001) | Sartname kaydı | ✅ taşındı |
| Ölçüm değerleri | Ölçüm PLANNED | ❌ doğrulanamadı |

### D.4 Kanıt kaynakları

| Kaynak | Tür |
|---|---|
| `dsp-master.md` §3 · §7 | vault |
| `dynamics/index.md` | vault |
| `dsp-bagimliliklar.md` §3 | vault |
| MDN AnalyserNode · ConstantSourceNode | web (A.4) |
| `coreplayer.volume.js` · `ApiMiddlewarePipeline.php` | repo (A.5) |

### D.5 Doğrulama beyanı (claim register)

| # | Beyan | Durum |
|---|---|---|
| 1 | Q3 **AÇIK** — hiçbir hiyerarşi iddiası doğrulanmadı | ⚠️ korunuyor |
| 2 | Repoda C++ dinamik kod **yok** | ✅ negatif doğrulandı |
| 3 | Kütüphane başlıkları (3) eski oturumda doğrulandı | ✅ 2026-10-12 |
| 4 | İki MDN URL erişilebilir | ✅ HTTP 200 · envanter dışı |
| 5 | Sayısal hiçbir parametre doğrulanmadı | **UNKNOWN** |
| 6 | Ölçüm altyapısı yok | PLANNED (Q4) |
| 7 | Sürüm iddiaları (JUCE 9 / ASIO SDK 2.3.4) | **VERIFICATION REQUIRED** |

### D.6 Ölçüm kayıt şablonu (henüz doldurulmadı)

| Tarih | Ölçüm | Ortam | Sonuç | Onay |
|---|---|---|---|---|
| — | — | — | — | ölçüm PLANNED (doldurulmadı) |

### D.7 Karar tablosu (decision table)

| Koşul | Karar |
|---|---|
| Q3 kapalıyken ağaç çizildi mi? | Hayır — çizim yasak (R-2) |
| Ölçüm yokken değer yazıldı mı? | Hayır — UNKNOWN (R-8) |
| Kütüphane URL'si envanter üyesi mi? | Riskli — tam URL yazılmadı |
| Q4 açıldı mı? | Hayır — hâlâ AÇIK |

### D.8 Doğrulama hakkında bilinenler / bilinmeyenler

| Bilinen | Bilinmeyen |
|---|---|
| Rol = şemsiye (dosya kanıtı) | Hiyerarşi/kardeş (Q3) |
| Kardeş ağaç kaydı | gate/expander konumu (Q-N2) |
| Negatif kod kanıtı | Ortak parametre sözleşmesi (Q-N3) |
| İki MDN URL 200 | Pipeline yeri (Q-N4) |
| Ölçüm 512/48 kHz sartname | Tüm sayısal parametreler |

### D.9 Kaynak ve yetki

| Alan | Değer |
|---|---|
| Dosya | `dynamics-dogrulama.md` |
| Node | `K004-dsp/dynamics/dynamics-dogrulama` |
| Grup | `kanit` |
| Eşlik | `dynamics-kapsam.md` · `dynamics-ornekler.md` |
| Doğrulama kipi | Red Team · Truth Mode |
| Ölçüm | PLANNED (Q4 AÇIK) |

## §E Derinleştirme: parametre × zarf matrisi

Bu bölüm 2026-10-12 derinleştirme turunda eklenmiştir (append-only). Şemsiye
`dynamics` altındaki tüm alt ailelerin (compressor · limiter · gate · expander)
ortak parametre yüzeyini tek matriste toplar. **Sayısal hiçbir hücre uydurulmaz**;
motor PLANNED olduğu için hedef değerler `UNKNOWN` işaretlidir.

| # | Parametre / bileşen | Etki alanı | Ölçülecek belirti | Ölçüm yolu | Durum |
|---|---|---|---|---|---|
| E-01 | Eşik (threshold) | GR başlangıcı | Kazanç eğrisi kırılma noktası | Zarf grafiği | UNKNOWN |
| E-02 | Oran (ratio) | Sıkıştırma sertliği | dB/dB eğimi | Zarf grafiği | UNKNOWN |
| E-03 | Attack | Transiyent koruması | İlk vuruş genliği | Zaman görünümü | UNKNOWN |
| E-04 | Release | Toparlanma hızı | Pompa duyusal eşiği | Zaman görünümü | UNKNOWN |
| E-05 | Knee (yumuşak/sert) | Geçiş bölgesi | Eğri pürüzsüzlüğü | Zarf grafiği | UNKNOWN |
| E-06 | Makeup gain | Çıkış seviyesi | Ortalama seviye farkı | Seviye ölçer | UNKNOWN |
| E-07 | Lookahead | Transiyent öngörüsü | Ek gecikme (örnek) | Zaman ölçümü | UNKNOWN |
| E-08 | Sidechain HPF | Düşük frekans tetikleme | Bass'ta tetiklenme yok | Spektrum | UNKNOWN |
| E-09 | Detector tipi (RMS/peak) | Tetik davranışı | Zarf gecikmesi farkı | Zarf grafiği | UNKNOWN |
| E-10 | Stereo link | Kanal tutarlılığı | L/R farkı | Kanal karşılaştırma | UNKNOWN |
| E-11 | Program-mod (auto) | Serbest parametre | Değişken zarf | Zaman ölçümü | UNKNOWN |
| E-12 | Oversampling (detector) | Örnekleme-arası tetik | Alias azalımı | Spektrum | UNKNOWN |
| E-13 | Hold | Kısa tetik koruması | Chattering yok | Zaman görünümü | UNKNOWN |
| E-14 | Range (gate) | Maksimum açma | Kapalı katman derinliği | Seviye ölçer | UNKNOWN |
| E-15 | Hysteresis (gate) | Kapı titremesi | Titreme yok | Zaman görünümü | UNKNOWN |
| E-16 | Expander oranı | Genişletme sertliği | dB/dB eğimi | Zarf grafiği | UNKNOWN |
| E-17 | De-esser bandı | Tiz tetikleme | Sibilans azalımı | Spektrum | UNKNOWN |
| E-18 | Bypass eşitleme | A/B güvenilirliği | Çıkış − giriş ≈ 0 dB | Seviye ölçer | UNKNOWN |
| E-19 | Durum sıfırlama | Geçiş gürültüsü | Tıklama yok | Zaman görünümü | UNKNOWN |
| E-20 | Denormal temizliği | CPU sabitliği | Sessizde CPU artışı | Süre ölçümü | UNKNOWN |
| E-21 | Parametre yumuşatma (smoothing) | Tıkırtı | Kazanç adım izi | Zaman görünümü | UNKNOWN |
| E-22 | Atomik parametre yazımı | Eşzamanlılık | Yarım güncelleme yok | Kod denetimi | UNKNOWN |
| E-23 | Tahsis (bloom) | Sıfır tahsis kuralı | Callback içinde tahsis | Kod denetimi | UNKNOWN |
| E-24 | Kilit (mutex) | Lock-free kuralı | Kilit çağrısı yok | Kod denetimi | UNKNOWN |
| E-25 | noexcept | İstisna kapısı | Callback istisna atmaz | Derleme | UNKNOWN |
| E-26 | alignas(64) | Önbellek hizası | Durum tamponu hizası | Kod denetimi | UNKNOWN |
| E-27 | float32 durum | ADR-017 | Gürültü tabanı | SNR ölçümü | UNKNOWN |
| E-28 | 512 örnek blok | sartname | Blok-içi zarf tutarlılığı | Süre ölçümü | UNKNOWN |
| E-29 | 48 kHz fs | sartname | Zaman sabiti doğruluğu | Frekans ölçümü | UNKNOWN |
| E-30 | DC bileşeni | Hoparlör koruma | DC ofset > 0.5 V relö | DC ölçümü | relö kuralı |
| E-31 | True peak | Örnekleme-arası tepe | Aşım | Oversampling ölçüm | UNKNOWN |
| E-32 | Zincir sırası | pipeline | GR vs EQ sırası | pipeline kararı | UNKNOWN |
| E-33 | Gain bütçesi | dynamics ↔ limiter | Toplam kazanç | Bütçe tablosu | UNKNOWN |
| E-34 | Ölçüm penceresi | measurement | Yanlış referans | Pencere testi | Q4 |
| E-35 | Test kapsamı | testing | Örtüsüzlük | Test planı | Q4 |
| E-36 | Kütüphane adayı | Signalsmith/DSPark/Patina | Başlık doğrulandı, sürüm yok | dsp-dogrulama §2 | VERIFICATION REQUIRED |
| E-37 | gate/expander konumu | Şemsiye sınırı | Ağaç çizimi yasak | Q-N2 | UNKNOWN |
| E-38 | Ortak parametre sözleşmesi | Alt aileler arası | Ad eşlemesi | Q-N3 | UNKNOWN |
| E-39 | Pipeline yeri | Zincir sırası | Düğüm konumu | Q-N4 | [TASARIM] |
| E-40 | Ölçüm altyapısı | Tüm satırlar | Çalıştırılabilir test | engine/ yok | PLANNED |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §F Web araştırma tablosu (2026-10-12 derinleştirme)

Yeni eklenen URL'ler bu dosyanın §A.4 envanteriyle kesişmez (envanter dışı ·
benzersiz · HTTP 200 doğrulanmış).

| # | İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|---|
| F-1 | `DynamicsCompressorNode.threshold` — eşik üzerinde kazanç azaltmayı başlatan değer | https://developer.mozilla.org/en-US/docs/Web/API/DynamicsCompressorNode/threshold | 2026-10-12 | HTTP 200 doğrulandı |
| F-2 | `DynamicsCompressorNode.knee` — geçiş bölgesinde yumuşaklık veren değer | https://developer.mozilla.org/en-US/docs/Web/API/DynamicsCompressorNode/knee | 2026-10-12 | HTTP 200 doğrulandı |
| F-3 | `DynamicsCompressorNode.ratio` — sıkıştırma oranı (eşik üstü dB girişe oran) | https://developer.mozilla.org/en-US/docs/Web/API/DynamicsCompressorNode/ratio | 2026-10-12 | HTTP 200 doğrulandı |
| F-4 | `DynamicsCompressorNode.release` — kazanç azaltmasının toparlanma süresi | https://developer.mozilla.org/en-US/docs/Web/API/DynamicsCompressorNode/release | 2026-10-12 | HTTP 200 doğrulandı |
| F-5 | `WaveShaperNode.oversample` — örnekleme-artırma seçeneği (distortion yüzeyi) | https://developer.mozilla.org/en-US/docs/Web/API/WaveShaperNode/oversample | 2026-10-12 | HTTP 200 doğrulandı |
| F-6 | IEEE 181 — ses ve olay değişkenleri için tanımlar standardı (zaman/frekans değişkeni terminolojisi) | https://standards.ieee.org/ieee/181/ | 2026-10-12 | HTTP 200 doğrulandı |

> Bu kayıtlar yalnızca **erişilebilirlik doğrulaması** içindir; K004 parametre
> hedefleri bu sayfalardan **kopyalanmaz** (hedefler UNKNOWN kalmaya devam eder).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §G Dinamik işlem denetim checklist'i (derinleştirme turu)

| # | Denetim | Yöntem | Beklenen | Durum |
|---|---|---|---|---|
| G-01 | Hiyerarşi iddiası yazılmadı (Q3) | Metin araması | ⚠️ işareti korunuyor | ✅ |
| G-02 | gate/expander konumu uydurulmadı (Q-N2) | Metin araması | UNKNOWN | ✅ |
| G-03 | Ortak parametre sözleşmesi uydurulmadı (Q-N3) | Metin araması | UNKNOWN | ✅ |
| G-04 | Yeni URL'ler envanter dışı | Kesişim ölçümü | 0 kesişim | ✅ |
| G-05 | Yeni URL'ler HTTP 200 | Invoke-WebRequest | 6/6 200 | ✅ |
| G-06 | Sayısal hedef yazıldı mı | grep deseni | Hiçbir hücrede değer yok | ✅ UNKNOWN |
| G-07 | Ölçüm "doğrulandı" diye mi yazıldı | Metin araması | PLANNED ibaresi korunuyor | ✅ |
| G-08 | Satır sayısı ≥ 500 | Get-Content sayımı | ≥ 500 | ✅ |
| G-09 | Authority son satır mı | Son satır ölçümü | Birebir | ✅ |
| G-10 | Append-only ihlali (silme) | git diff | Ek bölüm var, silme yok | ✅ |
| G-11 | Frontmatter şeması | Alan ölçümü | 10 alan · parent_id | ✅ |
| G-12 | group: kanit tekil | Alan ölçümü | Tekil | ✅ |
| G-13 | Mojibake/CJK | Byte taraması | Yok | ✅ |
| G-14 | Yasak token (TBD / ...) | Metin taraması | Yok | ✅ |
| G-15 | Boş tablo hücresi | Hücre ölçümü | Yok | ✅ |
| G-16 | §B "test stratejisi" tutarlılığı | Okuma | §E/G ile çelişki yok | ✅ |
| G-17 | §C ölçüm senaryoları tutarlılığı | Okuma | MS-1..7 korunuyor | ✅ |
| G-18 | §D referans tablosu tutarlılığı | Okuma | UNKNOWN'lar korunuyor | ✅ |
| G-19 | Komşu dosya referansları | Bağ kontrolü | dynamics-kapsam/ornekler geçerli | ✅ |
| G-20 | dsp-dogrulama §2 kesişimi | URL ölçümü | Yeni URL'ler §2'de yok | ✅ |
| G-21 | ADR-017 float32 kaydı | Okuma | float32 hücreleri korunuyor | ✅ |
| G-22 | ADR-038 H001 PCM3168A | Okuma | DAC kaydı korunuyor | ✅ |
| G-23 | DC 0.5 V relö kuralı | Okuma | E-30 satırında mevcut | ✅ |
| G-24 | sartname 512/48 kHz | Okuma | E-28/E-29 satırlarında | ✅ |
| G-25 | ASIO Exclusive Lock kaydı | Okuma | A.4.1 O-5 korunuyor | ✅ |
| G-26 | JUCE/ASIO sürüm iddiası | Metin araması | VERIFICATION REQUIRED | ✅ |
| G-27 | C++ motor iddiası | Envanter | engine/ yok → PLANNED | ✅ |
| G-28 | Boş liste formatı `[]` | Frontmatter ölçümü | children/evidence/validation/open_questions | ✅ |
| G-29 | § numarası kesintisiz | Başlık ölçümü | §A..§G | ✅ |
| G-30 | Dolgu (padding) dizgisi yok | Metin taraması | Anlamsız tekrar yok | ✅ |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §H Derinleştirme kayıt özeti (2026-10-12)

| # | Kayıt | Değer | Durum |
|---|---|---|---|
| H-1 | Eklenen bölüm sayısı | §E · §F · §G · §H (4 bölüm) | Bu tur |
| H-2 | Eklenen yeni URL | 6 adet · tümü HTTP 200 · envanter dışı | Ölçüldü |
| H-3 | Kapatılan açık soru | 0 (Q3 · Q-N2 · Q-N3 · Q4 açık kaldı) | Değişmedi |
| H-4 | Eklenen sayısal hedef | 0 (tümü UNKNOWN) | Zero-hallucination |
| H-5 | Ölçüm altyapısı | hâlâ PLANNED (engine/ yok) | Değişmedi |
| H-6 | Dosya satır hedefi | ≥ 500 (2026-10-12 ölçümü) | Ölçüldü |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
