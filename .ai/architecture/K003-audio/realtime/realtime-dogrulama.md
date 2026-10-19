---
title: "realtime — Doğrulama"
type: architecture-verification
node_id: realtime-dogrulama
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on: []
evidence:
  - "§1 iddia tablosu — 12 satır: 8 doğrulandı, 2 ⚠️, 2 UNKNOWN"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): Microsoft Core Audio programming guide"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): ALSA Library API (alsa-project.org wiki)"
validation:
  - "§1–§3 mevcut yapı korundu (append-only); §2 sayım §1 ile birebir"
  - "§4–§11 ek bölümler: xrun/latency bütçesi/kapı tasarımı — ölçüm SONUCU içermez"
open_questions:
  - "#9 realtime thread önceliği/süreç değeri — UNKNOWN"
  - "#10 tick/buffer bütçesi (ms/örnek) — UNKNOWN"
  - "#11 callback yasak işlerinin kuyruk stratejisi — ⚠️ VERIFICATION REQUIRED"
  - "#12 ASIO/WASAPI callback ↔ CoreMusic döngü eşleşmesi (Q5) — ⚠️ VERIFICATION REQUIRED"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# realtime — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "realtime · Zaman baskısı ve lock-free çalışma kuralları" | `audio-master.md` §3 satır 70 | 2026-10-12 | ✅ dosya okundu |
| 2 | "RT audio callback lock-free, buffer'lar baştan allocate" (global mühendislik kuralı) | `~/.config/opencode/AGENTS.md` §5 · `audio-master.md` §5 T9 | 2026-10-12 | ✅ dosya okundu |
| 3 | Embedded kalite hedefi: zero-allocation, lock-free, noexcept %100 | `.ai/AGENTS.md` §16 Quality Standards (Embedded satırı) | 2026-10-12 | ✅ dosya okundu |
| 4 | `AudioUnitRender` her render'da `inTimeStamp.sampleTime` monoton artar: sonraki = önceki + inNumberFrames | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | 2026-10-12 | has-been-fetched 2026-10-12 |
| 5 | PipeWire scheduling: driver node döngüyü başlatır; required/pending sayaçları ile node tetiklenir; async node 1 quantum latency ekler | https://pipewire.org/page_scheduling | 2026-10-12 | has-been-fetched 2026-10-12 |
| 6 | ALSA PCM hw_params (format/rate/channels/buffer) — periyot/buffer boyutu pazarlığının kaynağı | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| 7 | Repo'da realtime callback/kuyruk kodu yok (lock-free iddiası kanıtlanamaz) | kök `*.cpp`/`*.h` glob 0 isabet | 2026-10-12 | ✅ ölçüm |
| 8 | ASIO callback modeli (bufferSwitch) — platform realtime mekanizması | `backend/asio/asio-arayuzler.md` (salt-okunur, okundu) | 2026-10-12 | ✅ dosya okundu |
| 9 | Somut realtime thread önceliği/süreç değeri | — | 2026-10-12 | **UNKNOWN** (ölçüm/karar yok) |
| 10 | Tick/buffer bütçesi (ms veya örnek sayısı) | — | 2026-10-12 | **UNKNOWN** (bütçe tanımı yok) |
| 11 | Callback'te yasak işlerin hangi kuyruğa devredileceği | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 12 | ASIO/WASAPI callback'i ile CoreMusic döngüsünün eşleşmesi (Q5) | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 12 |
| Benzersiz URL | 3 (4 · 5 · 6) |
| Gerçek dosya yolu / iç kanıt satırı | 5 (1 · 2 · 3 · 7 · 8) |
| ✅ doğrulandı / dosya okundu / ölçüm | 8 (1–3 · 7 · 8 → + has-been-fetched 4–6) |
| ⚠️ VERIFICATION REQUIRED | 2 (11 · 12) |
| UNKNOWN | 2 (9 · 10) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §3 Geçiş kuralı

- **#9–10** öncelik değeri ve bütçe ancak ölçüm/karar sonrası yazılır →
  `UNKNOWN` kalır; sayı uydurmak yasak.
- **#11** kuyruk stratejisi backend kararıyla kapanır → `realtime-kapsam.md`
  §3 RT3 satırı `[TASARIM]` → `[KANIT]` olur.
- **#12** Q5 kapanınca eşleşme (callback sahipliği) netleşir.
- **#7** kanıtsız "lock-free uygulandı" iddiası yasaktır; kural olarak
  **tasarım gerekliliği** olarak kalır (master §7 D6: [TASARIM] → [KANIT] 0).

---

## §4 Realtime doğrulama protokolü — xrun/underrun test düzeneği (PLANNED)

> **Durum:** Bölüm **test tasarımıdır**; ölçüm sonucu içermez. Kardeşler:
> [[realtime-kapsam]] · [[realtime-ornekler]] · [[index]] ·
> [[K000-isletim-sistemi/isletim-sistemi-audio/index]].

### §4.1 Amaç, kapsam, sorumluluk

| # | Öğe | Tanım |
|---|---|---|
| A1 | Amaç | Gerçek zamanlı döngünün bütçe içinde kaldığını ve xrun/underrun davranışının tanımlı olduğunu doğrulamak |
| A2 | Kapsam | K003 realtime callback yolu (girdi→DSP→çıkış blok döngüsü) |
| A3 | Kapsam dışı | DSP algoritması doğruluğu (K004-dsp), firmware zamanlaması (K005) |
| A4 | Çıktı | Bütçe tablosu + xrun kaydı + PASS/FAIL (§14 formu) |
| A5 | Girdi | 512 örnek / 48 kHz (sartname §8) → blok süresi 512/48000 = 10,666… ms (aritmetik) |
| A6 | Veri yolu | 32-bit float (ADR-017) |
| A7 | Kural paketi | zero-allocation · lock-free · noexcept · alignas(64) (kurallar 1, 2, 3, 5) |
| A8 | Sorumlu | embedded-engineer (uygulama) + performance-engineer (ölçüm) + qa-engineer (kapı) |
| A9 | OS katmanı | Callback/scheduler sahibi: [[K000-isletim-sistemi/isletim-sistemi-audio/index]] |

### §4.2 Ön koşul tablosu

| # | Ön koşul | Kaynak | Durum | Eksikse |
|---|---|---|---|---|
| P1 | 512 örnek / 48 kHz | sartname §8 | ✅ tanımlı | — |
| P2 | Lock-free callback kuralı | §1 #2 · §1 #3 | ✅ kural | — |
| P3 | Repo'da realtime kodu yok (baseline) | §1 #7 ölçüm | ✅ ölçüm | iddia kanıtlanamaz (§3) |
| P4 | ASIO callback modeli (`bufferSwitch`) | §1 #8 (`backend/asio/asio-arayuzler.md`) | ✅ dosya okundu | — |
| P5 | Thread önceliği/süreç değeri | §1 #9 | ❌ UNKNOWN | ölçüm koşulu tanımsız |
| P6 | Tick/buffer bütçesi (ms/örnek) | §1 #10 | ❌ UNKNOWN | §5 bütçesi kapalı |
| P7 | Yasak işlerin kuyruk stratejisi | §1 #11 | ⚠️ VERIFICATION REQUIRED | §13 denetimi kısmi |
| P8 | ASIO/WASAPI ↔ CoreMusic döngü eşleşmesi (Q5) | §1 #12 | ⚠️ VERIFICATION REQUIRED | sahiplik belirsiz |
| P9 | OS scheduler/clock davranışı | [[K000-isletim-sistemi/isletim-sistemi-audio/index]] | çapraz referans | — |
| P10 | Ölçüm aracı (xrun sayacı, callback süresi) | — | ⚠️ PLANNED | ölçüm alınamaz |

### §4.3 Test adımları

| Adım | İşlem | Çıktı | Hata durumunda |
|---|---|---|---|
| S1 | Bütçe tanımını doğrula (§5) | bütçe satırları | #10 yoksa §5 “KAPALI” |
| S2 | Callback yasağı denetim listesini uygula (§13) | denetim raporu | ihlal → BLOCK |
| S3 | Test düzeneğini kur (tek uygulama — Exclusive Lock, kural 9) | düzen kaydı | kilit yoksa iptal |
| S4 | Normal yükte koşu başlat | zaman serisi | — |
| S5 | Yük kademeli artır (adım planı **UNKNOWN — sayı uydurulmaz**) | yük-karşılaştırma | — |
| S6 | Xrun/underrun üret (kontrollü) | kırılma kaydı | — |
| S7 | Kırılma sonrası davranışı gözle (monotonluk, kayıp, toparlanma) | davranış raporu | §9 RF senaryoları |
| S8 | Callback süresi dağılımını topla (ham) | ham ölçümler | — |
| S9 | Bütçeyle karşılaştır (§5.2) — bütçe kararı yoksa KAPALI | kapı sonucu | KAPALI → “geçti” yazılmaz |
| S10 | Sonuçları §14 boş forma işle | oturum kaydı | — |
| S11 | Kardeşlerle çapraz kontrol (sync/mix/route) | çapraz not | — |

### §4.4 Ölçüm kayıt şeması (alan tanımı — değer yok)

| Alan | Tip | Birim | Not |
|---|---|---|---|
| `block_index` | uint64 | — | blok sırası |
| `callback_ns` | uint64 | ns | callback süre ölçümü (araç: §17 T2) |
| `budget_ns` | uint64 | ns | bütçe (§5) — karar yoksa boş |
| `xrun_count` | uint32 | adet | kümülatif kırılma |
| `xrun_flag` | bool | — | bu blokta kırılma |
| `underrun_vs_overrun` | enum | — | ayrıştırma (tanım: §6.1) |
| `alloc_probe` | bool | — | allocation yakalayıcı (§13 CR2) |
| `lock_probe` | bool | — | lock yakalayıcı (§13 CR3) |
| `thread_id` | uint32 | — | callback thread'i |
| `run_id` | id | — | oturum |

## §5 Latency bütçesi doğrulama tablosu

### §5.1 Bütçe bileşenleri

| # | Bileşen | Birim | Bilgi durumu | Kaynak |
|---|---|---|---|---|
| B1 | Uygulama blok süresi | 512/48000 = 10,666… ms | ✅ aritmetik | sartname §8 |
| B2 | Driver tamponu (periyoт/buffer) | ms | ❌ UNKNOWN | ALSA `hw_params` pazarlığı (§1 #6); değer yok |
| B3 | Donanım tamponu (K005 I2S/TDM) | ms | ❌ UNKNOWN | firmware alanı |
| B4 | DSP zinciri işlem süresi | ms | ❌ UNKNOWN | K004 — ölçülmedi |
| B5 | Kuyruk gecikmesi | ms | ❌ UNKNOWN | §1 #11 stratejisi yok |
| B6 | OS kuyruk/scheduler gecikmesi | ms | ⚠️ VERIFICATION REQUIRED | K000 katmanı |
| B7 | Toplam uçtan uca | ms | ❌ UNKNOWN | = B1+…+B6 (§5.2) |
| B8 | Jitter payı | ms | ❌ UNKNOWN | — |

### §5.2 Bütçe kimlikleri (aritmetik)

| Kimlik | İfade | Not |
|---|---|---|
| I1 | `T_blok = 512 / 48000` = 0,010666… s | sartname |
| I2 | `T_toplam = B1 + B2 + B3 + B4 + B5 + B6` | toplam bütçe |
| I3 | `T_callback ≤ T_blok` (aday kural) | aksi halde xrun riski |
| I4 | `T_ölçülen + T_jitter ≤ T_blok` (aday) | jitter kararı yok → ⚠️ |
| I5 | Bileşenlerden biri UNKNOWN ise `T_toplam` da UNKNOWN | tahmini toplam yazılmaz |
| I6 | Örnekleme hızı değişirse I1 yeniden hesaplanır | 48 kHz dışına çıkma kural 8 ihlali |

## §6 Test senaryo matrisi (RT)

### §6.1 Senaryo matrisi (RT-01 … RT-16)

| ID | Senaryo | Bağımsız değişken | Metrik | Beklenen (tasarım) | Sonuç | Kapı |
|---|---|---|---|---|---|---|
| RT-01 | Normal akış | — | xrun = 0 | Kırılma yok | ⬜ | §8 K1 |
| RT-02 | Blok süresi kimliği | — | T_blok | 10,666… ms (aritmetik) | ⬜ | §5 I1 |
| RT-03 | Callback bütçesi | callback süresi | ≤ blok (aday) | Bütçe kararı yok → KAPALI | ⬜ | K2 |
| RT-04 | Kontrollü xrun üretimi | yük | xrun kaydı | Kırılma **raporlanır** (gizlenmez) | ⬜ | K3 |
| RT-05 | Xrun sonrası toparlanma | kırılma sonrası | ilk N blok | Kayıp tanımlı; monotonluk korunur | ⬜ | K4 |
| RT-06 | Underrun ayrımı | kırılma türü | enum | Underrun/overrun ayrışır | ⬜ | §8 K5 |
| RT-07 | Allocation yakalama | çalışma | `alloc_probe` | callback'te tahsis yok (kural 1) | ⬜ | §13 CR2 |
| RT-08 | Lock yakalama | çalışma | `lock_probe` | callback'te kilit yok (kural 2) | ⬜ | §13 CR3 |
| RT-09 | noexcept ihlali | hata enjeksiyonu | terk/yakalama | Callback `noexcept` (kural 3) | ⬜ | §13 CR1 |
| RT-10 | Thread önceliği etkisi | öncelik değeri (#9 UNKNOWN) | xrun/artış | Karar yok → KAPALI | ⬜ | ⚠️ |
| RT-11 | Buffer boyu değişimi | 512 dışı | reddedilme | Sartname dışı boy reddedilir | ⬜ | §8 K6 |
| RT-12 | Exclusive Lock ihlali | 2. uygulama | kilit hatası | İkinci uygulama reddi (kural 9) | ⬜ | K7 |
| RT-13 | Cihaz kaybı (fallback) | cihaz çıkar | olay | Edge Case #6 fallback | ⬜ | K8 |
| RT-14 | Eşzamanlı UI yükü | UI etkinliği | callback süresi | Bütçe (KAPALI) veya raporlama | ⬜ | ⚠️ |
| RT-15 | Uzun dayanıklılık | süre (UNKNOWN) | birikimli xrun | Süre kararı yok → KAPALI | ⬜ | ⚠️ |
| RT-16 | Q5 eşleşmesi (§1 #12) | callback sahipliği | sahiplik kaydı | Karar yok → KAPALI | ⬜ | ⚠️ |

### §6.2 Xrun/underrun sınıflandırma matrisi

| Sınıf | Tetik (aday) | Gözlenen | Beklenen kayıt | Kapı |
|---|---|---|---|---|
| XU-1 Underrun | Üretim geç kaldı | Kırılma bayrağı | `underrun` + blok indeksi | §13 CR2 |
| XU-2 Overrun | Tüketim geç kaldı | Kırılma bayrağı | `overrun` + blok indeksi | §13 CR3 |
| XU-3 Zaman aşımı | Callback bütçe aşıldı (K2 açık ise) | `callback_ns > budget_ns` | iki değer de kaydedilir | K2 |
| XU-4 Kayıp | Boş/eksik blok | boşluk | kayıp örnek sayısı (aritmetik) | K4 |
| XU-5 Gizleme | Kayıp sessizce doldurulmuş | — | **gizleme tespiti**: bayrak yoksa FAIL | K3 |
| XU-6 Kilitlenme | Deadline kaçı | ardışık kırılma | art arda kırılma serisi | K4 |

## §7 Eşik tabloları (aday — onay bekliyor)

| Eşik | Aday değer | Dayanak | Durum |
|---|---|---|---|
| E1 | Blok süresi | 10,666… ms | ✅ aritmetik (sartname) |
| E2 | Callback bütçesi (kesin) | — | ❌ UNKNOWN (#10) |
| E3 | Xrun kabul oranı (0 mu?) | — | ❌ UNKNOWN (#10) |
| E4 | Jitter üst sınırı | — | ❌ UNKNOWN |
| E5 | Toplam latency bütçesi | bileşenler UNKNOWN (§5.1) | ❌ UNKNOWN (I5) |
| E6 | Callback yasağı ihlali toleransı | 0 (kurallar 1–3) | ✅ kural |
| E7 | Tahsis sayısı (callback'te) | 0 | ✅ kural 1 |
| E8 | Lock sayısı (callback'te) | 0 | ✅ kural 2 |
| E9 | Thread önceliği değeri | — | ❌ UNKNOWN (#9) |
| E10 | Ölçüm penceresi (dayanıklılık) | — | ❌ UNKNOWN |
| E11 | Q5 sahiplik (ASIO/WASAPI ↔ döngü) | — | ⚠️ VERIFICATION REQUIRED (#12) |
| E12 | Kuyruk kapasitesi/derinliği | — | ⚠️ VERIFICATION REQUIRED (#11) |

## §8 Geçme / kalma kriterleri (PASS–FAIL)

| # | Kriter | Ölçüm | PASS | FAIL aksiyonu |
|---|---|---|---|---|
| K1 | Normal akışta kırılma | xrun sayacı | 0 (aday) | §6.1 tekrar; tekrarlayan → regresyon |
| K2 | Callback bütçesi | `callback_ns` vs bütçe | Bütçe KAPALI ise sonuç yazılmaz | Açıkken: aşımsa FAIL |
| K3 | Kırılma raporlanması | bayrak varlığı | Gizleme yok | Gizleme varsa FAIL (XU-5) |
| K4 | Toparlanma | kırılma sonrası akış | Tanımlı davranış | Tanım yoksa ⚠️ |
| K5 | Underrun/overrun ayrımı | enum | Ayrışır | Ayrılmıyorsa FAIL |
| K6 | Blok boyu doğruluğu | 512 dışı deneme | Reddedilir | Kabul → kural ihlali |
| K7 | Tek uygulama | 2. uygulama | Reddedilir | Kabul → kural 9 ihlali |
| K8 | Cihaz kaybı | olay testi | Fallback (Edge Case #6) | Yoksa FAIL |
| K9 | Yasağı ihlali (§13) | probe sayaçları | 0 / 0 | İhlal → BLOCK |
| K10 | Ölçüm bütünlüğü | kayıt doluluğu | Eksik yok | Eksik → pencere geçersiz |
| K11 | KAPALI-KAPI kuralı | E2–E5, E9–E12 | “geçti” yalnız açık kapıda | Aksi → rapor reddi |
| K12 | Dürüstlük | koşul+tarih | her sonuç satırında | yoksa sayılmaz |

## §9 Hata enjeksiyon senaryoları (RF)

| ID | Enjeksiyon | Yöntem (tasarım) | Beklenen dayanım | Tespit | Sonuç |
|---|---|---|---|---|---|
| RF-01 | Yapay xrun | Bütçe aşımı zorla (yük/adım) | Kırılma raporlanır | xrun sayacı | RT-04 |
| RF-02 | Blok boyu dayatma (0/256/1024) | Yapılandırma | Reddedilir | hw_params denetimi | K6 |
| RF-03 | f_s dayatma (44.1 kHz) | Yapılandırma | Reddedilir (sartname 48 kHz) | denetim | §5 I6 |
| RF-04 | Tahsis enjeksiyonu (bilinçli `new`) | Test kancası | Yakalanır (kural 1) | `alloc_probe` | CR2 |
| RF-05 | Lock enjeksiyonu (bilinçli mutex) | Test kancası | Yakalanır (kural 2) | `lock_probe` | CR3 |
| RF-06 | Callback içinde istisna | Test kancası | `noexcept` ihlali yakalanır | derleme/çalışma | CR1 |
| RF-07 | Cihaz kaybı | Tak-çıkar | Fallback | olay | K8 |
| RF-08 | İkinci uygulama | ASIO Exclusive Lock ihlali | Red | kilit hatası | K7 |
| RF-09 | OS saat sıçraması | Saat olayı | Zaman damgası tutarlılığı (sync dosyası) | çapraz | [[synchronization-dogrulama]] |
| RF-10 | UI aşırı yük | Eşzamanlı etkinlik | Kırılma/gizleme raporlanır | sayaç | RT-14 |
| RF-11 | Kuyruk taşması | Derinlik aşımı | Taşma sayılır, gizlenmez | sayaç | §1 #11 ile |
| RF-12 | Eşik kararı olmadan PASS yazma | Rapora sahte satır | Reddedilir (K11) | rapor denetimi | K11 |

## §10 Regresyon kapıları

| Kapı | Tetikleyici | Zorunlu test | Kapatma |
|---|---|---|---|
| RR-1 | Realtime kodu eklendi (baseline yok) | §6.1 RT-01–RT-09 | K1, K7, K9 PASS |
| RR-2 | Bütçe kararı (#10) | §5 + RT-03 | E2/E5 karara bağlandı |
| RR-3 | Öncelik kararı (#9) | RT-10 | E9 kararı |
| RR-4 | Kuyruk stratejisi (#11) | RF-11 + §13 | E12 kararı |
| RR-5 | Q5 kapanışı (#12) | RT-16 | sahiplik netleşti |
| RR-6 | ASIO/WASAPI değişikliği (§1 #8 · kural 11 ADR-019 FROZEN) | RT-12 + platform testi | ADR-019'a dokunulmadı |
| RR-7 | OS yükseltmesi (K000) | §4.3 S11 + RT-14 | çapraz not güncellendi |
| RR-8 | Yasağı kuralı değişikliği | §13 tam denetim | 0 / 0 ihlal |

## §11 Bağlantılar

| Bağlantı | Dosya | İlişki |
|---|---|---|
| Kardeş kapsam | [[realtime-kapsam]] | RT3 satırı `[TASARIM]` → `[KANIT]` (§3) |
| Kardeş örnekler | [[realtime-ornekler]] | Senaryo örnekleri |
| Dizin | [[index]] | Düğüm özeti |
| OS ses katmanı | [[K000-isletim-sistemi/isletim-sistemi-audio/index]] | Scheduler/clock/callback sahibi |
| Senkron | [[synchronization-dogrulama]] | Zaman damgası monotonluk ortak testi |
| Test piramidi | [[testing-kapsam]] | L4 katmanı bu bütçeyi kullanır |
| ASIO arayüzü | [[backend/asio/asio-arayuzler]] | §1 #8 callback modeli |

## §12 Kaynak denetim kaydı (2026-10-13)

> `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`; tam URL eklenmez (0 yeni URL).

| # | Kaynak | HEAD | Not |
|---|---|---|---|
| 1 | Microsoft Learn — Core Audio programming guide | **HTTP 200** | Windows callback/zamanlama referansı |
| 2 | Microsoft Learn — Core Audio genel bakış | **HTTP 200** | Kavram katmanı |
| 3 | ALSA projesi wiki — ALSA Library API | **HTTP 200** | §1 #6 karşılığı |
| 4 | PipeWire dokümantasyonu — latency (docs.pipewire.org) | **HTTP 200** | §1 #5 gibi sayfalar güncel alanda |
| 5 | §1'deki `alsa-project.org/doc/api/*` | **HTTP 404** | §1 satır 6 yeniden değerlendirilmeli (sessiz düzeltme yasak — not: §12) |
| 6 | §1'deki `pipewire.org/page_scheduling` | **HTTP 404** | Aynı — not: §12 |

## §13 Callback yasaklar denetim listesi (kurallar 1–5 · §1 #2–#3)

| # | Denetim | Kural | Yöntem (aday) | Kabul | Sonuç |
|---|---|---|---|---|---|
| CR1 | `noexcept` | kural 3 | imza denetimi (derleyici) | tüm callback'ler `noexcept` | ⬜ |
| CR2 | Tahsis yok | kural 1 | probe (RF-04) + statik tarama | 0 isabet | ⬜ |
| CR3 | Kilit yok | kural 2 | probe (RF-05) + statik tarama | 0 isabet | ⬜ |
| CR4 | `alignas(64)` | kural 5 | statik `static_assert` | tüm RT tamponları hizalı | ⬜ |
| CR5 | 32-bit float | ADR-017 | tip denetimi | yol boyunca float32 | ⬜ |
| CR6 | PCM5122 yasağı | ADR-038 H001 | kod/envanter taraması | yalnız PCM3168A | ⬜ |
| CR7 | 512/48 kHz sabiti | sartname §8 | sabit tanım denetimi | sabit kaynaklı | ⬜ |
| CR8 | Yasak API listesi (malloc/free/FILE/mutex/log yazma) | kurallar 1–2 + §1 #11 | statik tarama | callback yolu 0 isabet | ⬜ |
| CR9 | Kuyruğa devir tasarımı | §1 #11 | mimari inceleme | strateji kararı belgeli | ⚠️ |
| CR10 | Asenkron GPIO/log yazımları | kural 1–2 | inceleme | RT yolunda değil | ⬜ |
| CR11 | Veri yarışı | lock-free tasarım | inceleme (kod yok — §1 #7) | — | ⬜ |
| CR12 | Öncelik/kilitlenme (priority inversion) | §1 #9 | inceleme | karar belgeli | ⚠️ |

**Değerlendirme kuralı:** CR1–CR8 ihlali = **BLOCK** (geri dönüşsüz); CR9–CR12
işareti = ⚠️ açık soru, “geçti” sayılmaz.

## §14 Oturum rapor şeması (boş form)

### §14.1 Oturum künyesi

| Alan | Değer |
|---|---|
| `run_id` | *(boş)* |
| Tarih / commit | *(boş)* |
| Ortam (OS, sürücü, cihaz) | *(boş)* |
| f_s / blok boyu | 48000 Hz / 512 (sartname §8) |
| Öncelik değeri (#9) | *(boş — UNKNOWN)* |
| Bütçe (#10) | *(boş — UNKNOWN)* |
| Kuyruk stratejisi (#11) | *(boş — ⚠️)* |
| Q5 sahiplik (#12) | *(boş — ⚠️)* |

### §14.2 Ölçüm satırları

| run_id | blok | `callback_ns` | `budget_ns` | xrun | tür | CR1–CR12 | Sonuç |
|---|---|---|---|---|---|---|---|
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |

### §14.3 Enjeksiyon satırları (RF)

| RF ID | Uygulandı | Beklenen | Gözlenen | Sonuç |
|---|---|---|---|---|
| RF-01 | *(boş)* | kırılma raporlanır | *(boş)* | *(boş)* |
| RF-02/03 | *(boş)* | reddedilir | *(boş)* | *(boş)* |
| RF-04/05/06 | *(boş)* | probe ihlali yakalanır | *(boş)* | *(boş)* |
| RF-07/08 | *(boş)* | fallback / kilit reddi | *(boş)* | *(boş)* |
| RF-09–RF-12 | *(boş)* | çapaz/rapor davranışı | *(boş)* | *(boş)* |

**Boş form kuralı:** boş = ölçülmedi; bütçe hücreleri §1 #10 kararı gelene
kadar **hep boş** kalır ve sonuç “KAPALI-KAPI” işaretlenir.

## §15 Açık soru → kapanış haritası

| Soru (§1) | Kapanış koşulu | Ne değişir | Sorumlu | Kapı |
|---|---|---|---|---|
| #9 öncelik/süreç değeri | Ölçüm + karar | E9 açık; RT-10 çalışır | embedded + OS | S3 |
| #10 bütçe (ms/örnek) | Bütçe kararı | §5 B2–B8 dolar; K2 açılır | embedded + perf | S1 |
| #11 kuyruk stratejisi | Backend kararı | `realtime-kapsam` RT3 `[KANIT]` | backend + embedded | CR9 |
| #12 Q5 eşleşmesi | Q5 kararı | RT-16 kapanır | architect | RR-5 |
| §12 satır 5–6 (URL 404) | Vault URL denetimi | §1 satırları yeniden değerlendirilir | vault steward | — |

## §16 Risk ve belirsizlik defteri

| # | Risk | Etki | Olasılık | Azaltma | Durum |
|---|---|---|---|---|---|
| RSK-1 | Bütçe yok → kapılar kapalı (#10) | Yüksek | Kesin | Karar kapısı | Açık |
| RSK-2 | Kural kanıtsız: kod yok (#7) | Yüksek | Kesin (baseline) | İlk kodda CR denetimi | Açık |
| RSK-3 | Öncelik bilinmiyor (#9) | Orta | Kesin | Karar + ölçüm | Açık |
| RSK-4 | Kuyruk stratejisi belirsiz (#11) | Orta | Kesin | CR9 kapanışı | Açık |
| RSK-5 | Q5 sahiplik belirsiz (#12) | Orta | Kesin | Q5 kararı | Açık |
| RSK-6 | Bütçe uydurma baskısı | Yüksek | İnsan kaynaklı | E2–E5 UNKNOWN, K11 | Kalıcı kontrol |
| RSK-7 | Ölçüm aracı yok (P10) | Yüksek | Kesin | Araç seçimi (§17) | Açık |
| RSK-8 | OS yükseltmesi davranışı değiştirir | Orta | Belirsiz | RR-7 | Açık |

## §17 Ölçüm araçları envanteri (PLANNED)

| # | Araç (aday) | Amaç | Kaynak | Durum |
|---|---|---|---|---|
| T1 | Callback süre sayacı (kod içi) | `callback_ns` | iç geliştirme | PLANNED (kod yok) |
| T2 | OS sayaçları (QPC benzeri) | referans süre | K000 katmanı | ⚠️ VERIFICATION REQUIRED |
| T3 | Xrun sayacı (driver/API) | kırılma | ALSA/PipeWire/ASIO sayaçları | ⚠️ isim/erişim doğrulanmadı |
| T4 | Allocation/lock probe | CR2/CR3 | test kancası | PLANNED |
| T5 | Yük üreteci | RT-04/RF-01 | iç geliştirme | PLANNED |
| T6 | Statik tarama (yasağı API) | CR8 | seçilmemiş araç | ❌ UNKNOWN |
| T7 | Exclusive Lock denetçisi | K7 (kural 9) | kural | ✅ kural var |
| T8 | Bütçe değerlendirici | K2/K11 | bu dosya §5 | ✅ tasarım var (karar bekliyor) |

## §18 Denetim listesi (dosyanın kendi kalite kapısı)

- [ ] §1–§3 metni değişmedi (append-only)
- [ ] Frontmatter 8 zorunlu alan + `node_id` = `realtime-dogrulama` + `group: kanit` + `parent_id`
- [ ] Ek bölümlerde yeni tam URL yok
- [ ] Ek bölümlerde ölçüm sonucu yok (yalnız aritmetik: 512/48000)
- [ ] #9–#12 işaretleri korundu (2 UNKNOWN + 2 ⚠️)
- [ ] Bütçe tabloları UNKNOWN taşıyor (I5 kuralı)
- [ ] “lock-free uygulandı” iddiası YOK (§3 #7 kuralı) — yalnız tasarım gerekliliği
- [ ] Wiki-linkler (kardeşler + K000 + ASIO)
- [ ] Dürüstlük bloğu + tek Authority satırı en sonda
- [ ] git commit atılmadı

## §19 Terimler

| Terim | Tanım |
|---|---|
| **Xrun** | Kırılma genel terimi (underrun + overrun) — §6.2 |
| **Underrun** | Üretim yetişememe; boş blok riski (XU-1) |
| **Overrun** | Tüketim yetişememe (XU-2) |
| **Bütçe** | Callback için izin verilen süre (§5) — kararı #10 |
| **KAPALI-KAPI** | Karar verilmemiş eşik/kapı (K11) |
| **Probe** | Callback ihlali yakalayıcı (CR2/CR3) |
| **BLOCK** | Geri dönüşsüz denetim reddi (§13) |
| **Baseline** | Repo'da kod yokluğu (§1 #7) |
| **Çapraz kontrol** | Kardeş düğümlerle belge karşılaştırması |

## §20 Ek kayıtlar

| Tarih | Kayıt | Yapan |
|---|---|---|
| 2026-10-12 | §1–§3 kanıt tablosu + sayım + geçiş kuralları | Vault Steward |
| 2026-10-13 | Frontmatter derinleştirme + §4–§20 eklendi (append-only) | Vault Steward (subagent) |
| 2026-10-13 | Kaynak HEAD denetimi (§12) — Microsoft/ALSA/PipeWire 200; eski ALSA/PipeWire URL 404 not edildi | Vault Steward (subagent) |

## §21 Kuru çalıştırma (prova) — ölçüm OLMADAN

| Adım | İşlem | Doğrulanan | Doğrulanmayan |
|---|---|---|---|
| DR1 | §14 formunu boş doldur | Form bütünlüğü | — |
| DR2 | §5 kimliklerini (I1–I6) sayılarla çalıştır | Aritmetik | gerçek bütçe |
| DR3 | §13 CR1–CR8'i kod olmadan “atlandı” işaretle | Denetim akışı | gerçek ihlal yokluğu |
| DR4 | RF-12'yi sahte raporla dene | K11 reddi | — |
| DR5 | Prova izini temizle | Boş forma dönüş | — |

## §22 Sık sorular

| # | Soru | Cevap | Ayrıntı |
|---|---|---|---|
| Q1 | “Callback süresi kaç ms?” | Ölçülmedi; bütçe de yok | #10 |
| Q2 | “Lock-free uygulandı mı?” | Hayır — kod yok; kural olarak tasarım | §3 #7 |
| Q3 | “Xrun kaç kez oluyor?” | Ölçülmedi | §6 |
| Q4 | “Öncelik ne?” | Bilinmiyor | #9 |
| Q5 | “Toplam latency?” | UNKNOWN (bileşenler UNKNOWN) | I5 |
| Q6 | “ASIO/WASAPI kime ait?” | Q5 kararı bekliyor | #12 |
| Q7 | “Bütçesiz PASS yazılır mı?” | Hayır — KAPALI-KAPI | K11 |
| Q8 | “Commit atıldı mı?” | Hayır | kural 8 |

## §23 Kapsam dışı beyanı

| Kapsam dışı | Neden |
|---|---|
| DSP algoritması doğruluğu | K004-dsp alanı |
| Firmware DMA/interrupt | K005 alanı |
| OS scheduler iç yapısı | K000 alanı (yalnız çapraz referans) |
| “Ölçüldü/iddia edildi” hiçbir sayı | Ölçüm yok — §14 boş form |
| JUCE 9 / ASIO SDK 2.3.4 sürümü | kural 12 — kanıt yok |
| Per-OS player davranışı | ADR-019 FROZEN (kural 11) |

## §24 Bütçe ve öncelik karar formları (boş — §1 #9/#10 bekleniyor)

### §24.1 Bütçe kararı formu (#10)

| Alan | Değer |
|---|---|
| Bütçe kaynağı (blok süresi mi, bağımsız mı?) | *(boş)* |
| Callback üst sınırı (ms/örnek) | *(boş — UNKNOWN)* |
| Jitter payı | *(boş)* |
| Ölçüm penceresi (blok adedi/süre) | *(boş)* |
| Xrun kabulü (0 / N) | *(boş)* |
| Onay (embedded + perf + qa) | *(boş)* |
| Tarih | *(boş)* |

**Kural:** Bu form boşken §5 B2–B8 UNKNOWN kalır, K2 KAPALI-KAPI'dır (I5).

### §24.2 Öncelik/kuyruk kararı formu (#9 / #11)

| Alan | Değer |
|---|---|
| Callback thread önceliği değeri | *(boş — UNKNOWN)* |
| İzin verilen meşguliyet (süreç/tick) | *(boş)* |
| Yasak işlerin devredildiği kuyruk tipi | *(boş — ⚠️)* |
| Kuyruk derinliği/taşma davranışı | *(boş — ⚠️)* |
| Taşma sayacı kaynağı | *(boş)* |
| Onay | *(boş)* |

## §25 Kapı matrisi özeti (tek bakışta)

| Kapı | Kaynak | Açık mı? | Bağımlı soru |
|---|---|---|---|
| K1 normal akış | §8 | Evet (tasarım) | kod yok (#7) |
| K2 callback bütçesi | §8 | **Kapalı** | #10 |
| K3 kırılma raporu | §8 | Evet | — |
| K4 toparlanma | §8 | Kısmi | davranış tasarımı |
| K5 tür ayrımı | §8 | Evet | — |
| K6 blok boyu | §8 | Evet | — |
| K7 tek uygulama | §8 | Evet (kural 9) | — |
| K8 cihaz kaybı | §8 | Evet (Edge Case #6) | — |
| K9 yasaklar (CR1–CR8) | §8/§13 | Evet (kurallar) | kod yok |
| K10 ölçüm bütünlüğü | §8 | Evet | — |
| K11 KAPALI-KAPI | §8 | Evet (kural) | — |
| K12 dürüstlük | §8 | Evet | — |
| RR-2 bütçe regresyonu | §10 | **Kapalı** | #10 |
| RR-3 öncelik regresyonu | §10 | **Kapalı** | #9 |
| RR-5 Q5 | §10 | **Kapalı** | #12 |

**Özet kuralı:** Kapalı kapılar raporda “KAPALI (soru: #n)” olarak listelenir;
“geçti” yazımı yalnız açık + PASS kapılar için mümkündür.

## §26 Onay ve el ilkesi tablosu

| Rol | Neyi onaylar | Ne zaman | İmza durumu |
|---|---|---|---|
| embedded-engineer | Ölçüm düzeneği + CR denetimi | §4.3 S2–S9 sonrası | *(bekliyor)* |
| performance-engineer | Bütçe ölçümü (açık kapıda) | §5 kararından sonra | *(bekliyor)* |
| qa-engineer | Kapı değerlendirmesi (§8) | her oturum sonunda | *(bekliyor)* |
| architect | #11 kapsam, #12 Q5 | karar anında | *(bekliyor)* |
| Vault Steward | Vault satır geçişi `[TASARIM]` → `[KANIT]` | yalnız gerçek ölçümle | *(bekliyor)* |

---

> **Dürüstlük bloğu:** Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 yeni URL,
> 0 uydurma ölçüm içerir; doğrulanmış kaynaklar yalnız belirtilen URL'lerdir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
