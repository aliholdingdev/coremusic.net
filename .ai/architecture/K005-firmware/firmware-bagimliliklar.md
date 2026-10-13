---
node_id: K005-firmware/firmware-bagimliliklar.md
node_type: subdomain
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
  - K003-audio
  - K004-dsp
evidence:
  - "firmware-master.md §5 (bu üretim) — bağımlılık omurgası — yazıldı 2026-10-13"
  - "coremusic-katmanli-mimari-agaci-v2.md §2.5 (satır 232–249) — çocuk listesi — okundu 2026-10-12"
  - "shared/src/** dosya okuma (2026-10-13) — §4 satır numaraları satır satır yeniden doğrulandı"
  - ".github/workflows/ci.yml + .ai/.sql/mysql/coremusic_neva.sql — referans bağları (2026-10-13)"
validation:
  - "2026-10-13 — §4'teki 13 Dosya/Satır çiftinin tamamı diskten okunarak yeniden doğrulandı (13/13)"
  - "2026-10-13 — dış bağımlılık listesi boş: firmware'e harici paket eklenmedi"
  - "2026-10-13 — §7.1 yoğunluk matrisi 9 çocuk × 5 kolon ile §2/§3 satırları eşleşti"
open_questions:
  - "K000-isletim-sistemi bağımlılığı hedefe göre değişiyor (bare-metal vs host) — hangisi geçerli? (⚠️)"
  - "K004-dsp'e kontrol parametresi arayüzü sözleşmesi henüz yazılmadı (⚠️)"
  - "§4 bağları hedef dil seçildikten sonra birebir kod karşılığına çevrilecek mi? (⚠️)"
group: mimari
---

# firmware-bagimliliklar — Bağımlılık Haritası

> Dayanak: `firmware-master.md` §5 · v2 §2.5. Bu dosya iki şeyi yazar:
> (1) **düğüm bağımlılıklarını**, (2) **mimari↔kod bağlarını** (gerçek repo yolları —
> her satır 2026-10-13'te diskten yeniden okunarak doğrulandı).

## §1 ELI15

> **Firmware tek başına çalışmaz: donanıma yazar, komşu düğümlerden sınır alır,
> repo'daki bootstrap/registry/middleware kalıplarını taklit eder.**

Bağımlılık iki katmanlıdır: **mimari** (K düğümleri arası — burada §2/§3) ve
**kod** (repo dosyaları arası — §4). Mimari bağımlılık onaysız tersine çevrilmez;
ters yön yalnız **olay/çağrı** ile ve tek satırla ifade edilir.

## §2 Dış bağımlılık (K005 → diğer düğümler)

| # | Hedef | Ne alınır | Yön | Geri dönüş | Etiket |
|---|---|---|---|---|---|
| E1 | K001-donanım | register haritası · clock · pin · DMA tanımı | K005 → K001 | Donanım firmware ile uyanır | [TASARIM] |
| E2 | K000-isletim-sistemi | hedef platform kısıtları (yalnız host/bare-metal sınırında) | K005 → K000 | — | [TASARIM] ⚠️ |
| E3 | K003-audio | ses yolu hazır durumu (bayrak sorgusu) | K003 → K005 | Yol hazır bayrağı | [TASARIM] |
| E4 | K004-dsp | kontrol parametresi gönderimi | K005 → K004 | Algoritma firmware'e bağlı değil | [TASARIM] ⚠️ |

## §3 İç bağımlılık (kademe-2 çocuklar arası)

| # | Kaynak | Hedef | İlişki | Zamanlama | Etiket |
|---|---|---|---|---|---|
| I1 | boot | runtime | İlk-stage → ana firmware teslim | açılışta 1 kez | [TASARIM] |
| I2 | boot | power-control | Brown-out eşiği açılışta kurulur | açılışta 1 kez | [TASARIM] |
| I3 | runtime | device-control | Görev/kesme üzerinden komut kuyruğu | sürekli | [TASARIM] |
| I4 | runtime | audio-control | Gain/mute değişikliği ana döngüde uygulanır | sürekli | [TASARIM] |
| I5 | runtime | power-control | Idle kipinde düşük güç talebi | sürekli | [TASARIM] |
| I6 | runtime | diagnostics | Reset nedeni + watchdog olayı raporu | olayda | [TASARIM] |
| I7 | update | boot | Yeni bölüm doğrulaması sonraki açılışta | güncellemede | [TASARIM] |
| I8 | update | security | İmza doğrulama güvenlik modülünden | güncellemede | dalga-3 |
| I9 | diagnostics | testing | Self-test sonuçları test kriterine girer | olayda | dalga-3 |
| I10 | power-control | audio-control | Kapanmadan önce ses yolu susturulur | kapanışta | [TASARIM] |

## §4 Mimari ↔ kod bağları (gerçek repo dosyaları — disk kanıtı, 2026-10-13 yeniden okundu)

| # | Mimari kural | Dosya (repo) | Satır | Bağ türü |
|---|---|---|---|---|
| K1 | Açılışta yapılandırma bir kez kurulur (`boot()` girişi) | `shared/src/Bootstrap/RuntimeBootstrap.php` | 7 | taklit (bootstrap) |
| K2 | Ortam değişkenleri tek yönden okunur (`load()`) | `shared/src/Config/EnvParser.php` | 7 | taklit (config) |
| K3 | Tek kayıt-defteri (registry) deseni (`register()`) | `shared/src/Api/Registry/ServiceRegistry.php` | 26 | taklit (registry) |
| K4 | Önbellek adaptörü seçimi çalışma anında (`getAdapter()`) | `shared/src/Cache/CacheManager.php` | 9 | taklit (adapter) |
| K5 | Sayaç tabanlı limit (`isLimited(key, max, window)`) | `shared/src/Security/CacheRateLimiter.php` | 16 | taklit (sayaç) |
| K6 | Olay dağıtımı tek noktadan (`dispatch()`) | `shared/src/Events/EventDispatcher.php` | 28 | taklit (observer) |
| K7 | Log fabrikası — doğrudan yazma yok (`final class LoggerFactory`) | `shared/src/Log/LoggerFactory.php` | 11 | taklit (factory) |
| K8 | Hassas veri maskeleme tek noktada (Redactor sınıfı) | `shared/src/Log/Redactor.php` | 6 | taklit (redaction) |
| K9 | Middleware dizisi tek yerde kurulur (`$this->middlewares = …`) | `shared/src/PageRouter/PageRouterKernel.php` | 78 | taklit (pipeline) |
| K10 | Kimlik doğrulama katmanı (AuthenticationMiddleware) | `shared/src/Api/Middleware/AuthenticationMiddleware.php` | 5 | taklit (auth) |
| K11 | Güvenlik başlıkları merkezi (`final class SecurityHeaders…`) | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 8 | taklit (headers) |
| K12 | Şema dosyası — sürüm kaynağı (`CREATE DATABASE …`) | `.ai/.sql/mysql/coremusic_neva.sql` | 12 | referans (şema) |
| K13 | CI pipeline — test kapısı (`name: CI Pipeline`) | `.github/workflows/ci.yml` | 8 | referans (CI) |

> Bu bağlar **taklit (analogi)** düzeyindedir: PHP repo kodu firmware'in kendisi
> değildir; firmware bu desenleri C/C++ tarafında kurar. Eşdeğerlik iddiası
> `⚠️ VERIFICATION REQUIRED` — hedef dil henüz seçilmedi.
> `Dosya:` yolları repo köküne görelidir (`C:\www\coremusic.net\`) ve 2026-10-13
> `Test-Path` + satır okumasıyla **13/13** doğrulanmıştır.

## §5 Harici paket bağımlılığı (yok)

| # | Soru | Cevap | Etiket |
|---|---|---|---|
| P1 | K005'e NuGet/Composer/npm paketi ekleniyor mu? | Hayır — bu üretim yalnız `.md` yazdı | ✅ 2026-10-13 |
| P2 | Firmware hedefi için RTOS/harici kütüphane seçildi mi? | Seçilmedi — `firmware-bilesenler.md` §5 bekliyor | ⚠️ UNKNOWN |
| P3 | Lisans denetimi (harici kütüphane seçilince) | Seçim sonrası `dependency-manager` görevi | ⏳ koşullu |

## §6 Bağımlılık kuralları

| # | Kural | Kaynak |
|---|---|---|
| R1 | Katman yönü: K005 → K001/K000 (tek yön); tersi yalnız olay/çağrı ile | AGENTS §5 katman |
| R2 | K004-dsp'e bağımlılık **tek yönlü parametre** — algoritma geri bağlı değil | master §5 |
| R3 | Yeni harici paket onaysız eklenmez | AGENTS bağımlılık kuralı |
| R4 | Domain katmanına harici bağımlılık eklenmez | Developer Agent kuralı |
| R5 | Kod bağları (§4) yalnız gerçek dosya yolu + satır numarası taşır | Zero-Hallucination |

## §7 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| B1 | §2/§3 satırları master §5 ile birebir | çapraz okuma | ✅ 2026-10-13 |
| B2 | §4 yolları diskte mevcut | `Test-Path` | ✅ 13/13 (2026-10-13) |
| B3 | §5 P1: yeni paket yok | git status + composer kontrolü | ✅ 2026-10-13 |
| B4 | K004 sözleşmesi yazılı | üst karar | ❌ açık (Q2) |

### §7.1 Bağımlılık matrisi (yoğunluk — hangi düğüm neye bağlı)

| Kaynak ↓ / Hedef → | K000 | K001 | K003 | K004 | iç (çocuklar) |
|---|---|---|---|---|---|
| boot | — | register/clock | — | — | → runtime · power-control |
| runtime | — | — | hazır bayrak | parametre | → device · audio · power · diagnostics |
| device-control | — | bus/driver | — | — | ← runtime (kuyruk) |
| audio-control | — | ses register | hazır bayrak | parametre | ← runtime · power-control |
| power-control | — | güç/brown-out | — | — | ← runtime · → audio-control |
| update | — | bölme belleği | — | — | → boot · security |
| diagnostics | — | — | — | — | ← tümü (dalga-3) |
| security | — | ROM/anahtar | — | — | ← update (dalga-3) |
| testing | — | — | — | — | ← tümü (dalga-3) |

> Yoğunluk hücreleri `firmware-master.md` §5 + bu dosya §2/§3'ten türetilmiştir;
> okuma yönü = bağımlılık yönü. `—` = doğrudan bağımlılık yok.

### §7.2 Değişiklik etkisi (bir satır değişince ne kırılır)

| Değişiklik | Etkilenen | Denetim |
|---|---|---|
| K001 register haritası güncellenirse | device-control · audio-control · power-control | §4 satır yeniden okunur |
| K004 sözleşmesi değişirse | audio-control (kontrol parametresi) | master §5 satırı + açık soru |
| K003 hazır bayrak adı değişirse | runtime · audio-control | §2 E3 satırı |
| bootstrap deseni değişirse (§4 K1–K13) | `firmware-kod-ornekleri.md` §2–§5 | Dosya/Satır yeniden okunur |
| Yeni harici paket önerilirse | §5 P1/P2/P3 | onay + lisans denetimi (R3) |

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Hedef platform: bare-metal mi, gömülü OS mi? (E2 bağımlılığını değiştirir) | `⚠️ VERIFICATION REQUIRED` |
| Q2 | K004-dsp kontrol parametresi sözleşmesi (alan adı · aralık · frekans) | `⚠️ VERIFICATION REQUIRED` |
| Q3 | §4 bağları hedef dil seçildikten sonra birebir kod karşılığına çevrilecek mi? | `⚠️ VERIFY REQUIRED` |

## §9 Komşu düğüm başına bağımlılık detayı

### §9.1 K001-donanım bağımlılığı (E1)

| # | Alınan | Kullanıldığı bileşen | Güncelleme tetiği | Boşsa ne olur |
|---|---|---|---|---|
| E1a | Register adres haritası | device-control · audio-control | Donanım revizyonu | `⚠️ UNKNOWN` + çalıştırma |
| E1b | Reset değerleri | boot · audio-control | Donanım revizyonu | Varsayılan değer yazılmaz |
| E1c | Clock/pin tanımı | boot | Kristal değişimi | Açılış reddi |
| E1d | DMA kanal/nöbet tanımı | runtime | Perifer eki | DMA'sız yola geçiş |
| E1e | Gerilim eşikleri (brown-out) | power-control | PMIC değişimi | Kapanma kipi |
| E1f | Termal eşik tablosu | power-control | Mekanik tasarım | Kısıtlamaya git |
| E1g | DAC/ADC register dokümanı | audio-control | DAC değişimi | `⚠️ VERIFY` (ADR-038: PCM3168A) |
| E1h | IRQ vektör listesi | boot · runtime | Şema değişimi | Bağlantı reddi |
| E1i | WDT zaman birimi | runtime | Çip değişimi | Ölçüm belirsiz |
| E1j | Test noktaları listesi | testing (dalga-3) | PCB revizyonu | HIL eksik kalır |
| E1k | NVM/OTP yerleşimi | security · update | Bellek değişimi | `UNKNOWN` |
| E1l | PMIC register tanımı | power-control | Güç devresi değişimi | Kip geçişi reddi |

### §9.2 K004-dsp bağımlılığı (E4)

| # | Giden | Dönen | Sözleşme durumu |
|---|---|---|---|
| E4a | Bypass kipi bayrağı | — | Sözleşme yazılmadı (Q2) |
| E4b | Preset numarası | — | Sözleşme yazılmadı (Q2) |
| E4c | Eşik değeri (limiter) | — | Sözleşme yazılmadı (Q2) |
| E4d | Mute kipi | — | Ortak: audio-control §3 I4 |
| E4e | Geçerli bayrağı | hazır/red | Beklenen yanıt biçimi tanımsız |
| E4f | — | Sürüm/özelliği | Sorgu yönü tanımı yok |
| E4g | — | DSP hazır | Hazır sorgusu adı yok |
| E4h | — | Algoritma ayarı | Algoritma K004'ün — okuma yok |

### §9.3 K003-audio bağımlılığı (E3)

| # | Sorgu | Yön | Bayrak adı | Etiket |
|---|---|---|---|---|
| E3a | Yol hazır mı | K005 → K003 | `path_ready` (taslak) | ⚠️ taslak |
| E3b | Kanal sayısı uyumu | K005 → K003 | `channels_ok` (taslak) | ⚠️ taslak |
| E3c | Örnekleme oranı | K005 → K003 | `rate_ok` (taslak) | ⚠️ taslak |
| E3d | Çıkış devrede mi | K005 → K003 | `out_active` (taslak) | ⚠️ taslak |
| E3e | Routing kipi | K005 → K003 | `route_mode` (taslak) | ⚠️ taslak |
| E3f | — | K003 → K005 | Gecikme bildirimi | ⚠️ taslak |

### §9.4 K000-isletim-sistemi bağımlılığı (E2)

| # | Konu | Durum | Not |
|---|---|---|---|
| E2a | Hedef gömülü/metal ise bağımlılık yok | beklenen | §1 R1 yönü korunur |
| E2b | Hedef host üzerinde çalışıyorsa | kısmi | Zaman/dosya hizmetleri ayrılır |
| E2c | Sürücü modeli | dışarıda | K000'ün alanı (kapsam O4) |
| E2d | Zaman kaynağı | firmware içi | Tick/timer firmware'de tanımlı |
| E2e | Dosya sistemi | dışarıda | Host katmanı (kapsam O5) |
| E2f | Hedef seçimi kararı | AÇIK | Q1 — `⚠️ VERIFICATION REQUIRED` |

## §10 Zincir (transitif) bağımlılıklar

| # | Zincir | Sonuç | Risk |
|---|---|---|---|
| Z1 | boot → runtime → device-control | Açılış gecikmesi tüm komut yolunu etkiler | Orta |
| Z2 | K001 → audio-control → K003 | DAC değişirse hem ayar hem hazır bayrak etkilenir | Yüksek |
| Z3 | update → security → boot | İmza zinciri açılışı bloklar | Yüksek |
| Z4 | power-control → audio-control | Kapanış sırası ses yoluyla bağlı | Orta |
| Z5 | runtime → power-control → K001 | Idle gücü doğrudan donanıma iner | Orta |
| Z6 | diagnostics → testing → tümü (dalga-3) | Test kapsamı bağımlılığın tamamını çeker | Düşük |
| Z7 | update → K001 (bölme) → boot | Bölme haritası üç dosyayı bağlar | Yüksek |
| Z8 | runtime → K004 (parametre) | Zamanlama bozulursa DSP kipi gecikir | Düşük |

## §11 Bağımlılık kural kataloğu (R1–R5'in genişletmesi)

| # | Kural | Uygulama | İhlali |
|---|---|---|---|
| R1a | K005 → K001 yönü kodda tersine çevrilemez | Arayüz yalnız okuma/yazma | `firmware-kararlar.md` §5 |
| R1b | Ters yön yalnız olay/çağrı | Bildirim tek satırla | Aynı |
| R2a | DSP'e giden tek şey parametre | Ses örneği geçmez | `firmware-kapsam.md` §7.2 |
| R2b | DSP'den gelen tek şey durum bayrağı | Algoritma durumu okunmaz | Aynı |
| R3a | Harici paket = ADR | Lisans + boyut + bakım | DUR |
| R3b | Domain katmanı bağımsız kalır | Çekirdek/RT kodu harici pakete bağlanmaz | Revert |
| R4a | Kod bağları = gerçek dosya + satır | Tahmini satır yasak | `firmware-dogrulama.md` D-kriteri |
| R5a | Bağ değişince §4 satırı yeniden okunur | Üretim sonunda ölçüm | §7 B2 |

## §12 Bağımlılık ↔ kök dosya çaprazı

| # | Bağ | Giden dosya | Bölüm |
|---|---|---|---|
| X1 | §2 dış bağımlılık | `firmware-kapsam.md` §4 | Sınır geçişleri |
| X2 | §3 iç bağımlılık | `firmware-master.md` §3 | Çapraz modül kuralları |
| X3 | §4 kod bağları | `firmware-kod-ornekleri.md` §2–§5 | Örnek kod |
| X4 | §7.1 yoğunluk | `firmware-bilesenler.md` §10 | Bileşen evreleri |
| X5 | §10 zincirler | `firmware-riskler.md` §2 | Zincir riski |
| X6 | §11 kurallar | `firmware-kararlar.md` §5 | Karar defteri |
| X7 | §13 döngü denetimi | `firmware-dogrulama.md` §1 | D-kriteri |
| X8 | §14 etki | `firmware-test-stratejisi.md` §4 | Regresyon kapsamı |
| X9 | §9.3 bayrak taslakları | `firmware-evrensel-dil.md` §6 | Terim adları |
| X10 | §15 anti-pattern | `firmware-riskler.md` §3 | Azaltma planı |

## §13 Döngü (cycle) denetimi

| # | Aday döngü | Sonuç | Gerekçe |
|---|---|---|---|
| CY1 | boot ↔ runtime | YOK (tek yön I1) | Teslim tek yönlü |
| CY2 | update ↔ boot | YOK | Update boot'a yalnız "sonraki açılış" yazar (I7) |
| CY3 | runtime ↔ device-control | YOK | Kuyruk tek yön (I3) |
| CY4 | power-control ↔ audio-control | DİKKAT | I10 (kapanışta sustur) + §9? — tek yazıcı kuralı gerekli |
| CY5 | update ↔ security | YOK (dalga-3) | İmza tek yön (I8) |
| CY6 | diagnostics ↔ testing | YOK | Sonuç tek yön (I9) |
| CY7 | K005 ↔ K001 | YOK | Ters yön yalnız olay (R1b) |
| CY8 | K005 ↔ K004 | YOK | Parametre + bayrak ayrı yönler (R2a/R2b) |

> **CY4 tek dikkat satırıdır:** iki yönlü çağrı var, döngü değil — çünkü
> tetikleyici farklı (runtime talebi vs kapanış sırası). Karar: sıra tanımı
> power-control'de, uygulama audio-control'de (`firmware-kapsam.md` §17 Ov4).

## §14 Değişiklik etkisi — geniş liste

| # | Değişiklik | Birinci etki | İkinci etki | Denetim |
|---|---|---|---|---|
| X1 | Register adresi değişirse | device-control | audio-control · power-control | §9.1 E1a |
| X2 | DAC modeli değişirse | audio-control | update (ADR-038) | §9.1 E1g |
| X3 | Kristal değişirse | boot (clock) | runtime (tick) | §9.1 E1c |
| X4 | PMIC değişirse | power-control | audio-control (kapanış) | §9.1 E1l |
| X5 | DSP kip tablosu değişirse | audio-control | runtime (zamanlama) | §9.2 |
| X6 | Yol bayrak adı değişirse | runtime | audio-control | §9.3 |
| X7 | Hedef platform değişirse | E2 | tümü (zaman/dosya) | §9.4 · Q1 |
| X8 | OTA bölme planı değişirse | update | boot · security | Z7 |
| X9 | Middleware deseni değişirse (repo) | §4 K9 | `firmware-kod-ornekleri.md` | §7.2 |
| X10 | CI kapısı değişirse | §4 K13 | `firmware-test-stratejisi.md` §6 | §7.2 |
| X11 | Şema sürümü değişirse | §4 K12 | Uygulama katmanı | §4 K12 |
| X12 | Log deseni değişirse | §4 K7 · K8 | diagnostics (dalga-3) | §4 |

## §15 Bağımlılık anti-pattern'leri

| # | Yanlış | Doğru | Dayanak |
|---|---|---|---|
| Y1 | Ters bağımlılığı kodla zorlamak | Olay/çağrı tek satır (R1b) | §11 R1b |
| Y2 | DSP'e ses örneği göndermek | Parametre (R2a) | §11 R2a |
| Y3 | Tahmini satır numarası yazmak | Diskten okunan satır (13/13) | §4 · §7 B2 |
| Y4 | Harici paketi onaysız eklemek | ADR + lisans (R3a) | §5 P1 |
| Y5 | Bağımlılığı gizli tutmak | §2/§3 satırı zorunlu | §6 |
| Y6 | Döngü kurmak | §13 tek-yön kuralı | §13 |
| Y7 | Etkiyi tek dosyada bırakmak | §14 denetim sütunu | §14 |
| Y8 | Bayrak adını tahminle yazmak | `⚠️ taslak` işareti | §9.3 |
| Y9 | Şema/CI bağımlılığını unutmak | §4 K12 · K13 | §4 |
| Y10 | Bilinmeyen bağımlılığı yok saymak | `UNKNOWN`/`⚠️` ile yaz | §8 |

## §16 Özet sayılar

| # | Sayı | Değer |
|---|---|---|
| 1 | Dış bağımlılık satırı | 4 (E1–E4) |
| 2 | İç bağımlılık satırı | 10 (I1–I10) |
| 3 | Kod bağı (gerçek dosya) | 13 (K1–K13) — 13/13 doğrulandı |
| 4 | Komşu detay satırı (§9) | 12 + 8 + 6 + 6 = 32 |
| 5 | Zincir | 8 (Z1–Z8) |
| 6 | Döngü denetimi | 8 (CY1–CY8) — 1 dikkat |
| 7 | Etki satırı (§14) | 12 |
| 8 | Anti-pattern | 10 (Y1–Y10) |
| 9 | Harici paket | 0 |
| 10 | Açık soru | 3 (Q1–Q3) |

## §17 Bileşen-bazlı bağımlılık satırları (F1–F14 — `firmware-bilesenler.md` §4 ile eş)

| # | Bileşen | Dış bağımlılık | İç bağımlılık | Yön |
|---|---|---|---|---|
| D1 | F1 Reset & ilk-stage | K001 (E1a–E1c) | → F2 · F3 | tek yön |
| D2 | F2 Clock/pin kurucusu | K001 (E1c) | ← F1 | tek yön |
| D3 | F3 Görev/ana döngü | K000 (E2 — koşullu) | ← F1 · F2 | tek yön |
| D4 | F4 Kesme yöneticisi | K001 (E1h) | ← F3 | tek yön |
| D5 | F5 Watchdog bekçisi | K001 (E1i) | ← F3 | tek yön |
| D6 | F6 Bus denetçisi | K001 (E1a) | ← F3 | tek yön |
| D7 | F7 GPIO/driver | K001 (E1a) | ← F6 | tek yön |
| D8 | F8 Ses register/ayar | K001 (E1g) · K003 (E3a) · K004 (E4a–E4h) | ← F6 | tek yön |
| D9 | F9 Güç durum makinesi | K001 (E1e) | ← F3 | tek yön |
| D10 | F10 Termal/akım | K001 (E1f) | ← F9 | tek yön |
| D11 | F11 Self-test | K001 (test yüzeyi) | ← F1–F10 | toplama |
| D12 | F12 Güncelleme motoru | K001 (E1k · bölme) | ← F1 | tek yön |
| D13 | F13 İmza/anahtar | K001 (E1k · ROM) | ← F1 · F12 | tek yön |
| D14 | F14 HIL/test | gerçek donanım | ← hepsi | toplama |

## §18 Bağımlılık kaydı formatı (bu dosyaya satır eklerken)

| # | Alan | Zorunlu mu | Format | Örnek |
|---|---|---|---|---|
| 1 | No | Evet | E/I/K/Z/CY benzersiz | `E5` |
| 2 | Hedef | Evet | `K00x-…` veya çocuk adı | `K001-donanım` |
| 3 | Ne alınır | Evet | kısa madde | `register haritası` |
| 4 | Yön | Evet | `K005 → K001` | ok yönü |
| 5 | Geri dönüş | Evet | olay/bayrak/yok | `Yol hazır bayrağı` |
| 6 | Etiket | Evet | `[TASARIM]` / `dalga-3` / `⚠️` | `[TASARIM]` |
| 7 | Tarih | Evet (yeni satır) | `YYYY-MM-DD` | `2026-10-13` |
| 8 | Kanıt | Kod bağı için | `Dosya:` + `Satır:` | `Dosya: … Satır: 7` |
| 9 | Tahmini değer | **Yasak** | Diskten okunur | — |
| 10 | Kayıp alan | Zorunlu | `UNKNOWN` / `⚠️` | `⚠️ UNKNOWN` |

## §19 Kod-yüzü analogu genişlemesi (§4 K1–K13'ün firmware karşılığı)

| # | Repo deseni (§4) | Firmware karşılığı (taslak) | Not |
|---|---|---|---|
| N1 | `RuntimeBootstrap::boot()` | `fw_boot_sequence()` — tek giriş | Ad **taslak** (seçim yok) |
| N2 | `EnvParser::load()` | Ayar tablosu tek okuma noktası | Aynı |
| N3 | `ServiceRegistry::register()` | Modül kayıt defteri | Aynı |
| N4 | `CacheManager::getAdapter()` | Arka uç (backend) seçici | Seçim `⚠️ UNKNOWN` |
| N5 | `CacheRateLimiter::isLimited()` | Komut rate-limit (test kipinde) | Kural §5 R3'e bağlı |
| N6 | `EventDispatcher::dispatch()` | Olay kuyruğu (ISR → döngü) | §3 I6 |
| N7 | `LoggerFactory` | Log yazıcı fabrikası | Dalga-3 |
| N8 | `Redactor` | Hassas veri maskeleme | Dalga-3 (security) |
| N9 | `PageRouterKernel` middleware dizisi | Init adımı dizisi (§13 init zinciri) | Aynı sıra mantığı |
| N10 | `AuthenticationMiddleware` | Yetki denetleyici (komut seviyesi) | Dalga-3 |
| N11 | `SecurityHeadersMiddleware` | Güvenli varsayılanlar (imza/kısıt) | Dalga-3 |
| N12 | `coremusic_neva.sql` | Şema/sürüm kaynağı | Analogi: manifest sürümü |
| N13 | `.github/workflows/ci.yml` | Test kapısı (gate) | Analogi: `firmware-test-stratejisi.md` §6 |

> Bu tablo **analogidir** — eşdeğerlik iddiası değildir; hedef dil seçilmeden
> `⚠️ VERIFICATION REQUIRED` işareti korunur.

## §20 Senaryo bazlı bağımlılık zincirleri (5 senaryo)

### §20.1 Açılış senaryosu

| Adım | Çağrı | Bağımlılık | Başarısızlık |
|---|---|---|---|
| 1 | Reset → F1 | K001 (E1a–E1c) | Güvenli döngü |
| 2 | F2 clock/pin | K001 (E1c) | Varsayılana düş |
| 3 | F9/F10 başlangıç | K001 (E1e · E1f) | Kapanma kipine git |
| 4 | F4 → F3 | K001 (E1h) | Ana döngüsüz kal |
| 5 | F6 → F7 → F8 (mute) | K001 (E1a · E1g) | Mute'da kal |
| 6 | F11 self-test | K001 test yüzeyi | Hata logu |
| 7 | F12 bölüm kontrolü | K001 (E1k) + F13 | Rollback |
| 8 | Normal çalışma | §3 iç bağımlılıklar | watchdog reset |

### §20.2 Kip geçişi senaryosu (uyku)

| Adım | Çağrı | Bağımlılık | Not |
|---|---|---|---|
| 1 | Idle → F9 talebi | §3 I5 | runtime tetikler |
| 2 | F8 mute | §3 I10 · K003 (E3a) | Sustur → sonra uyu |
| 3 | F6/F7 perifer kapat | K001 (E1a) | Sıra korunur |
| 4 | F9 uyku yaz | K001 (E1e) | Uyanma kaynağı |
| 5 | Uyanma → F1 (kısa) → F3 | §20.1 adımları | Tam yeniden kurulum |

### §20.3 Güncelleme senaryosu

| Adım | Çağrı | Bağımlılık | Not |
|---|---|---|---|
| 1 | Manifest al | §9.4/uygulama (taslak) | Şekil §4 K12 analogi |
| 2 | Bölme seç | K001 (bölme haritası) | Z7 zinciri |
| 3 | İndir/doldur | — (iç) | Güç kesilirse U6 |
| 4 | Doğrula | F13 + K001 (E1k) | Red → adım 1'e |
| 5 | Geçiş yaz | F12 | sayaç++ |
| 6 | Yeniden başlat | F1 | §20.1 |
| 7 | Açılışta doğrula | F1 + F13 | Başarızsa rollback |

### §20.4 Hata senaryosu (kurtarma)

| Tetik | Yakalayan | Etkilenen | Sonuç |
|---|---|---|---|
| WDT zaman aşımı | F5 | tümü | Reset + neden kaydı |
| Brown-out | F10 | F9 | Güvenli kapanma |
| Termal aşım | F10 | F9 · F8 | Kısma → kapama |
| Bus NACK | F6 | F7 · F8 | Cihaz yok bayrağı |
| İmza reddi | F13 | F12 · F1 | Eski bölüm |

### §20.5 Test (HIL) senaryosu

| Adım | Çağrı | Bağımlılık | Not |
|---|---|---|---|
| 1 | Test kipi gir | F7 · F11 | §9.3 d8 · §9.6 g5 |
| 2 | Senaryo çalış | F14 | Gerçek donanım |
| 3 | Ölçüm topla | F10 · F8 | Eşik `⚠️ UNKNOWN` |
| 4 | Rapor yaz | F11 | `firmware-test-stratejisi.md` |
| 5 | Kritere karşılaştır | F14 | §5 gate |

## §21 Bayrak/alan adı taslak sözlüğü (§9.3–§9.4'teki `⚠️ taslak` satırları)

| # | Taslak ad | Kullanan | Neden taslak |
|---|---|---|---|
| 1 | `path_ready` | K003 ↔ runtime | Bayrak adı K003'te tanımlı değil |
| 2 | `channels_ok` | K003 ↔ audio-control | Aynı |
| 3 | `rate_ok` | K003 ↔ audio-control | Aynı |
| 4 | `out_active` | K003 ↔ power-control | Aynı |
| 5 | `route_mode` | K003 ↔ audio-control | Aynı |
| 6 | `latency_notice` | K003 → runtime | Aynı |
| 7 | `dsp_bypass` | K004 ↔ audio-control | Sözleşme açık (Q2) |
| 8 | `dsp_preset` | K004 ↔ audio-control | Aynı |
| 9 | `dsp_ready` | K004 → audio-control | Aynı |
| 10 | `dsp_version` | K004 → runtime | Aynı |

> Bu adlar **kodda kullanılmaz**; nihai ad `firmware-evrensel-dil.md` §6'da
> dalga-3'te yayımlanır. Uydurma sürüm/ölçü bu tabloda da yoktur.

## §22 Kapanış kontrol listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | §2 dış bağımlılık 4/4 etiketli | ✅ |
| 2 | §3 iç bağımlılık 10/10 zamanlamalı | ✅ |
| 3 | §4 kod bağları 13/13 dosya + satır diskten okundu | ✅ 2026-10-13 |
| 4 | §5 harici paket 0 | ✅ |
| 5 | §13 döngü denetimi — CY4 dışında döngü yok | ✅ |
| 6 | §19 analogi `⚠️` işareti korunuyor | ✅ |
| 7 | §21 taslak adlar kodda yok | ✅ |
| 8 | Tahmini satır numarası 0 | ✅ |
| 9 | Yeni dosya/dizin 0 | ✅ |
| 10 | Kırmızı kod yüzeyi değişikliği 0 | ✅ |

## §23 Bağımlılık kayıt günlüğü (bu üretim)

| # | Tarih | İşlem | Etkilenen satır | Sonuç |
|---|---|---|---|---|
| 1 | 2026-10-12 | §2–§8 ilk üretim | tümü | yazıldı |
| 2 | 2026-10-13 | §4 kod bağları yeniden okundu (13 dosya) | K1–K13 | 6 satır numarası düzeltildi (K1 · K3 · K5 · K6 · K9 · K12) |
| 3 | 2026-10-13 | §9 komşu detayı eklendi | yeni | 32 satır |
| 4 | 2026-10-13 | §10 zincirler eklendi | yeni | 8 satır |
| 5 | 2026-10-13 | §11 kural kataloğu eklendi | yeni | 8 satır |
| 6 | 2026-10-13 | §12 çapraz referans eklendi | yeni | 10 satır |
| 7 | 2026-10-13 | §13 döngü denetimi eklendi | yeni | 8 satır (CY4 dikkat) |
| 8 | 2026-10-13 | §14 etki + §15 anti-pattern eklendi | yeni | 22 satır |
| 9 | 2026-10-13 | §17–§22 eklendi | yeni | bileşen/format/analogi/senaryo/taslak/kontrol |
| 10 | 2026-10-13 | §23 bu günlük | — | kapandı |
| 11 | 2026-10-13 | Frontmatter 9 alan (`group` 9.) | tümü | hizalandı |
| 12 | 2026-10-13 | Silinen satır | — | 0 |
| 13 | 2026-10-13 | Dosya adı değişikliği | — | 0 |
| 14 | 2026-10-13 | Yeni dosya/dizin | — | 0 |

> **Düzeltme notu (madde 2):** Eski satır numaraları satır içeriğiyle örtüşmüyordu
> (ör. K1 `9` → dosyadaki gerçek `boot()` girişi `7` idi). Zero-Hallucination
> gereği numara değil, **dosyanın o anki gerçek satırı** esastır; 6 düzeltme
> disk okumasıyla yapıldı, eski değerler burada saklandı.

## §24 Bağımlılık durum sözlüğü (bu dosyada geçen işaretler)

| İşaret | Anlamı | Ne zaman kalkar |
|---|---|---|
| `[TASARIM]` | Taslak kayıt — kod karşılığı yok | İlgili kod yazıldığında |
| `dalga-3` | Üçüncü dalgada ele alınacak satır | Dalga-3 başladığında |
| `⚠️` | Kanıt/üst karar bekliyor | Kanıt eklendiğinde |
| `⚠️ UNKNOWN` | Değer bilinmiyor — uydurulmaz | Değer diskte/üst kararda göründüğinde |
| `⚠️ taslak` | Ad/alan tahmini, kodda kullanılmaz | `firmware-evrensel-dil.md` §6 yayınında |
| `✅ 13/13` | Ölçüm sonucu (kanıtlı) | — |
| `❌ açık` | Kapalı olmayan kriter | Kriter kapanınca |
| `⏳ koşullu` | Koşul oluşunca çalışacak iş | Koşul oluştuğunda |
| `—` | İlişki yok | — |

---


### §25 Paket sürüm sabitleme politikası (özet)

| Kural | Uygulama | Gerekçe |
|---|---|---|
| ASCII-only sürüm etiketi | `MAJOR.MINOR.PATCH` — harfli suffiks (`-rc1`, `-beta`) üretim dalında yasak | Sürüm karşılaştırma hatalarını engeller |
| Değiştirilebilir bağımlılık yok | `composer.lock` / `package-lock.json` üretim derlemesine dahil edilir | Ortamlar arası birebir aynı bağımlılık ağacı |
| Yükseltme = bilinçli karar | Sürüm bump, ilgili ADR ve geri alma planı olmadan yapılmaz | Kural 13 (bağımlılık bump → DUR + handover) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
