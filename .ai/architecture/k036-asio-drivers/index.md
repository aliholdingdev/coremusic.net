---
title: "K036 — ASIO Sürücüleri (Düşük Gecikmeli Windows Ses Sürücü Katmanı)"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K036 — ASIO Sürücüleri

> **Kapsam özeti (K özeti):** Bu klasör, CoreMusic mimarisinde **Windows platformu düşük gecikmeli ses yolu** olan ASIO (Audio Stream Input/Output) sürücü katmanını tanımlar. Kapsam: exclusive-mode kilidi, buffer yönetimi, callback zinciri, donanım soyutlama katmanları ve latency hesabı. Kaynak katman: `k2-surucu/` (sürücü katmanı) + `k0-isletim-sistemi/` (Windows çekirdek servisleri).
>
> **Yer:** D01 aralığı (k036–k053) · görüntüleme/ses sürücüleri, latency, buffer, kernel-arayüz.

**Zorunlu bağlantılar (wiki):**
- Kendi dosyaları: [[asio-exclusive-mode]] · [[asio-buffer-callback]] · [[asio-latency-hesap]] · [[asio-device-lifecycle]] · [[asio-thread-model]] · [[asio-sdk-entegrasyon]] · [[asio-hata-yonetimi]]
- Komşu modüller: [[../k037-wasapi-exclusive/index]] · [[../k043-latency-optimization/index]] · [[../k042-buffer-management/index]] · [[../k041-driver-stack-mimari/index]]
- Uzak modüller: [[../k047-kernel-audio-api/index]] · [[../k048-interrupt-dma-flow/index]]

---

## §1 Genel Bakış

ASIO, Windows işletim sisteminin standart ses yolunu (WASAPI Shared, DirectSound, MME) **atlayarak** uygulamayı ses donanımıyla doğrudan konuşturan bir sürücü arayüzüdür. CoreMusic mimarisinde ASIO, `k2-surucu` katmanının birincil Windows low-latency yolu olarak tanımlanır.

K036 klasörü üç soruyu yanıtlar:

1. **Kilit:** ASIO exclusive-mode kilidi nasıl uygulanır ve tek uygulama kuralı nasıl korunur?
2. **Buffer:** Uygulama ile sürücü arasındaki çift yönlü buffer nasıl boyutlandırılır, kim doldurur/boşaltır?
3. **Callback:** Sürücü çağrısı hangi sırada hangi thread'te çalışır, gerçek-zamanlı güvenlik nasıl ihlal edilmez?

### §1.1 Bu klasörün sınırları

| Kapsam | Kapsam Dışı |
|--------|-------------|
| ASIO exclusive-mode davranışı | WASAPI modları → [[../k037-wasapi-exclusive/index]] |
| ASIO buffer ve callback zinciri | Genel ring-buffer veri yapıları → [[../k042-buffer-management/index]] |
| ASIO latency hesabı | Genel latency zinciri bütçesi → [[../k043-latency-optimization/index]] |
| HAL/SDK soyutlama sınıfları | Sürücü yığını lifecycle → [[../k041-driver-stack-mimari/index]] |
| Windows çekirdek servisleri (girdi) | NT API/syscall detayı → [[../k047-kernel-audio-api/index]] |

### §1.2 Durum

| Alan | Durum | Kanıt |
|------|-------|-------|
| Tasarım dokümanı (bu klasör) | 🟢 Üretildi (2026-10-06) | Bu dosya |
| Kaynak teknik içerik | 🟢 Mevcut (k2-surucu/asio-drivers.md) | Kanıt L8–L173 |
| Kod implementasyonu | 🔴 Yok (repo'da ASIO implementasyonu bulunamadı) | ⚠️ VERIFICATION REQUIRED |
| Donanım doğrulaması | 🔴 Yok | ⚠️ VERIFICATION REQUIRED |

---

## §2 Mimari Konum

```
┌──────────────────────────────────────────────────────────────────────┐
│  Uygulama (K3 Ses Motoru / Neva Engine)                              │
│     │  processAudio(input, output)  ← çift buffer sahibi             │
│     ▼                                                                │
│  ┌──────────────────────────────────────────────────────────────┐    │
│  │ K036 ASIO KATMANI                                            │    │
│  │  ┌────────────────┐   ┌─────────────────┐                    │    │
│  │  │ Exclusive Lock │   │ Buffer Switch   │                    │    │
│  │  │ (tek uygulama) │   │ (double buffer) │                    │    │
│  │  └───────┬────────┘   └────────┬────────┘                    │    │
│  │          │                     │                             │    │
│  │  ┌───────▼─────────────────────▼────────┐                    │    │
│  │  │ ASIO Callback Zinciri (RT thread)    │                    │    │
│  │  └───────┬──────────────────────────────┘                    │    │
│  └──────────┼───────────────────────────────────────────────────┘    │
│             ▼                                                        │
│  ┌──────────────────────────────────────────────────────────────┐    │
│  │ HAL Abstraction → Driver Interface → DMA Engine (K1/K0)      │    │
│  └──────────────────────────────────────────────────────────────┘    │
│             ▼                                                        │
│        Ses kartı (donanım FIFO)                                      │
└──────────────────────────────────────────────────────────────────────┘
```

**Katman sırası (kaynak):** ASIO SDK → Driver Interface → HAL Abstraction → DMA Engine.
Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md L79-L89`

---

## §3 Dosya Haritası

| Dosya | Konu | Ana kaynak |
|-------|------|-----------|
| `index.md` | K036 genel bakış, kapsam, bağlantılar | Bu dosya |
| `asio-exclusive-mode.md` | Exclusive mode kilidi, tek uygulama kuralı, cihaz sahipliği | `k2-surucu/asio-drivers.md` L24–L40, L51–L60 |
| `asio-buffer-callback.md` | Buffer yönetimi, callback zinciri, çift buffer, xrun | `k2-surucu/asio-drivers.md` L41–L113 · `k2-surucu/buffer-management.md` L16–L365 |
| `asio-latency-hesap.md` | Latency formülleri, buffer→ms tabloları, hedefler | `k2-surucu/asio-drivers.md` L93–L103 · `k2-surucu/latency-optimization.md` L12–L369 |
| `asio-device-lifecycle.md` | Cihaz açma/kapama/reset, SR/canal değişimi, fallback durum makinesi | `k2-surucu/driver-stack-mimari.md` L116–L260 · `k0/.../windows-api.md` L79–L87 |
| `asio-thread-model.md` | RT thread, öncelik hiyerarşisi, lock-free, sıfır tahsis | `k0/.../README.md` L230–L284 · `k0/.../windows-api.md` L152–L204 |
| `asio-sdk-entegrasyon.md` | ASIO SDK 2.3.4 yapısı, callback protokolü, HAL zinciri, lisans | `k0/.../windows-api.md` L25–L87 · `k2-surucu/driver-stack-mimari.md` L50–L134 |
| `asio-hata-yonetimi.md` | `ASIOError_*`, ErrorChain+retry, Edge Case #6, eskalasyon | `k2-surucu/asio-drivers.md` L105–L113 · `k2-surucu/driver-stack-mimari.md` L212–L260 |

---

## §4 Teknik Özet Tabloları

### §4.1 Ölçülmüş performans hedefleri (kaynak tablosu)

| Metrik | Hedef | Gerçek | Kanıt |
|--------|-------|--------|-------|
| Input Latency | 0.67ms | 0.65ms | `asio-drivers.md L158` |
| Output Latency | 0.67ms | 0.68ms | `asio-drivers.md L159` |
| Round-trip Latency | 1.34ms | 1.33ms | `asio-drivers.md L160` |
| CPU Kullanımı (boşta) | < %1 | %0.3 | `asio-drivers.md L161` |
| Maksimum Kanal | 64×64 | 64×64 | `asio-drivers.md L162` |
| Buffer Değişim Süresi | < 10µs | 8µs | `asio-drivers.md L163` |

### §4.2 Sürücü öncelik sırası (k2 CLAUDE.md guardrail)

| Sıra | Sürücü | Platform | Kullanım |
|------|--------|----------|----------|
| 1 | ASIO | Windows | Low-latency | 
| 2 | WASAPI Exclusive | Windows | Bit-perfect |
| 3 | CoreAudio | macOS | Native |
| 4 | ALSA | Linux | Kernel |
| 5 | PipeWire | Linux | Modern |
| 6 | WASAPI Shared | Windows | Fallback |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md L27-L34`

### §4.3 Hard guardrail'lar (bu klasörü bağlayan)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | ASIO Exclusive Lock → tek uygulama | Sürücü çökmesi |
| 2 | Audio thread blocking yasak | Ses takılması |
| 3 | Buffer underrun koruması zorunlu | Crackling |
| 4 | Sample rate mismatch önlenmeli | Pitch shift |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md L18-L24`

---

## §5 Alt Dosya Özeti

### §5.1 `asio-exclusive-mode.md` — ne anlatır?

- Exclusive mode'un uygulama–cihaz ilişkisi (tek sahip, dış uygulamalar dışarıda).
- Kilidin kaybedilmesi (device loss) ve uygulamanın çökmesi yerine graceful fallback.
- Exclusive vs Shared karşılaştırma tablosu (latency, bit-perfect, CPU, multi-app, DSP kaynağı).
- Cihaz yeniden bağlanma (re-enumeration) akışı.

### §5.2 `asio-buffer-callback.md` — ne anlatır?

- Double buffer / buffer switch mekanizması ve `bufferSwitchTimeInfo` benzeri callback sırası.
- Callback içinde **yapılmaması gerekenler** (bloke eden kilit, heap alloc, syscall, log yazımı).
- Latency hesabı: buffer boyutu ÷ örnekleme hızı × kanal başına gecikme bileşenleri.
- Hata modları: driver reset, sample-rate uyuşmazlığı, callback overrun.
- Backup-first: lock-free ring buffer/SPSC, buffer boyut optimizasyonu, adaptif buffer (§3.5).

### §5.3 `asio-latency-hesap.md` — ne anlatır?

- Formül seti (tek yön / round-trip), 96kHz ve 48kHz buffer→ms tabloları.
- Gecikme bileşen analizi ve bileşen→modül eşlemesi.
- Hedef tabloları: `asio-drivers.md` L156–L163 (1.34/1.33ms) vs L12 (0.5ms) vs backup
  `latency-optimization.md` (<1ms / 0.88ms / 1.76ms) — üçü de taşındı, ⚠️ işaretli.

### §5.4 `asio-device-lifecycle.md` — ne anlatır?

- Durum makinesi (backup `DriverLifecycle` geçiş izinleri) + ASIO Exclusive lock eşlemesi.
- Açma/kapama/sıfırlama sıralaması; SR/canal değişimi tepkileri.
- Cihaz kaybı → retry ×3 → WASAPI fallback (Edge Case #6) akışı.

### §5.5 `asio-thread-model.md` — ne anlatır?

- Thread öncelik hiyerarşisi (Audio Processing = TIME_CRITICAL + dedicated core).
- RT yasak/izin tablosu (mutex ❌ · `atomic<>` ✅ · `alignas(64)` zorunlu).
- Uygulama↔RT iletişim desenleri, lock-free kuyruk kuralları; backup DoubleBuffer mutex çelişkisi.

### §5.6 `asio-sdk-entegrasyon.md` — ne anlatır?

- `ASIOSDK2/` dosya ağacı, `ASIOCallbacks` protokolü, buffer konfigürasyonu.
- HAL → adapter → OS → driver → hardware zinciri + `AudioDriverFactory` fallback.
- Steinberg özel lisansı, 64-bit-only kısıtı, SDK 2.3.4 sürüm kanıtları.

### §5.7 `asio-hata-yonetimi.md` — ne anlatır?

- `ASIOError_*` kodları + tepkiler (InvalidMode→Shared, HardwareFailure→K3 durdur, ×3 retry).
- Katmanlı `ErrorChain` (ERR_* 1001–1006, retry'lı kodlar), kurtarma <100ms hedefi.
- Edge Case #6 fallback zinciri ve §10.1 eskalasyon yolları.

---

## §6 Bağımlılıklar

| Bağımlılık | Yön | Tür | Not |
|-----------|-----|-----|-----|
| K2 sürücü yığını (`driver-stack-mimari.md`) | Alt | Mimari | HAL soyutlamasını kullanır |
| K2 buffer yönetimi (`buffer-management.md`) | Alt | Veri yapısı | Ring/double buffer tanımları |
| K2 latency optimizasyonu | Alt | Metrik | Zincir bütçesi bu modülün üstüdür |
| K1 Windows Core (k0-isletim-sistemi) | Alt | OS servisi | Thread, bellek, event servisleri |
| K0 donanım / DMA | Alt | Donanım | Ses kartı FIFO'suna DMA |
| K3 Neva Engine (ses motoru) | Üst | Tüketici | processAudio callback'i |
| ADR-017 (XMOS XU316 + PCM3168A DSP) | Uzay | Karar | DSP donanım modu |

---

## §7 Kanıt Satırları (Bu dosyadaki iddiaların kaynağı)

| # | İddia | Kanıt yolu | Satır |
|---|-------|-----------|-------|
| 1 | ASIO katman sırası SDK→Driver→HAL→DMA | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | L79–L89 |
| 2 | Round-trip latency 1.34ms hedef / 1.33ms gerçek | aynı dosya | L160 |
| 3 | Buffer değişim süresi <10µs hedef / 8µs gerçek | aynı dosya | L163 |
| 4 | ASIO SDK 2.3+ dış bağımlılık | aynı dosya | L167–L171 |
| 5 | ASIO Exclusive Lock tek uygulama kuralı | `_backup/.../k2-surucu/CLAUDE.md` | L18–L24 |
| 6 | Sürücü öncelik sırası #1 ASIO | aynı dosya | L27–L34 |
| 7 | K2 → K0 DMA/IRQ bağımlılığı | `_backup/.../k2-surucu/index.md` | L57–L61 |

---

## §8 Kenar Durumlar (Özet)

| # | Kenar durum | Davranış | Dosya |
|---|------------|----------|-------|
| 1 | Aynı anda ikinci uygulama ASIO'yu açmak ister | Kilidin ikinci açılışı reddedilmesi gerekir | `asio-exclusive-mode.md` |
| 2 | Sürücü kilidi uygulama kapanışında serbest kalmaz | Timeout + OS handle temizliği | `asio-exclusive-mode.md` |
| 3 | Callback süresi buffer period'unu aşarsa | Underrun/overrun → xrun sayacı | `asio-buffer-callback.md` |
| 4 | Örnekleme hızı uygulama ile cihazda farklı | Pitch shift; açılışta reddet | `asio-buffer-callback.md` |
| 5 | Cihaz USB ile aniden çıkarılırsa | Device-loss → fallback yolu | `asio-exclusive-mode.md` |

---

## §9 Hata Modları (Özet)

| Hata | Belirti | Kök neden | İlk müdahale |
|------|---------|-----------|--------------|
| Exclusive lock ihlali | Sürücü çökmesi | İki süreç aynı cihazı açtı | Tek-sahip kilidi zorla |
| Underrun | Crackling / pop | Callback geç kaldı | Buffer boyutunu artır |
| Sample-rate mismatch | Pitch shift | Farklı SR | Açılışta SR doğrula |
| Callback overrun | Ses kesilmesi | RT thread engellendi | Blocking çağrıları kaldır |
| Driver reset | Ani sessizlik | Donanım hatası | Cihaz kapat/aç döngüsü |

---

## §10 Doğrulama Kontrol Listesi

- [ ] Exclusive lock yalnız bir süreçte tutuluyor
- [ ] Callback içinde heap allocation yok (kod taraması)
- [ ] Callback içinde bloke eden mutex/kilit yok
- [ ] Buffer boyutu SR ile uyumlu (tablo §4.1)
- [ ] Round-trip ölçümü yapılıyor (hedef 1.34ms)
- [ ] xrun/underrun sayacı loglanıyor
- [ ] Cihaz kaybında graceful degradation çalışıyor
- [ ] WASAPI fallback'e geçiş test edilmiş → [[../k037-wasapi-exclusive/index]]
- [ ] Latency bütçesi K2 toplam bütçeyle uyumlu → [[../k043-latency-optimization/index]]

---

## §11 Açık Konular / Riskler

| # | Konu | Durum |
|---|------|-------|
| 1 | Repo'da ASIO kod implementasyonu bulunamadı | ⚠️ VERIFICATION REQUIRED |
| 2 | Gerçek donanımda round-trip ölçümü yapılmadı | ⚠️ VERIFICATION REQUIRED |
| 3 | ASIO SDK lisans koşulu (Steinberg) | ⚠️ VERIFICATION REQUIRED |
| 4 | 64×64 kanal sınırının donanım karşılığı | ⚠️ VERIFICATION REQUIRED |

---

## §12 Wiki-Bağlantılar (Tüm klasör)

| Hedef | Bağlantı | İlişki |
|-------|----------|--------|
| ASIO exclusive mode | [[asio-exclusive-mode]] | Bu klasör dosyası |
| ASIO buffer/callback | [[asio-buffer-callback]] | Bu klasör dosyası |
| ASIO latency hesabı | [[asio-latency-hesap]] | Bu klasör dosyası |
| ASIO cihaz yaşam döngüsü | [[asio-device-lifecycle]] | Bu klasör dosyası |
| ASIO thread modeli | [[asio-thread-model]] | Bu klasör dosyası |
| ASIO SDK entegrasyonu | [[asio-sdk-entegrasyon]] | Bu klasör dosyası |
| ASIO hata yönetimi | [[asio-hata-yonetimi]] | Bu klasör dosyası |
| WASAPI modülü | [[../k037-wasapi-exclusive/index]] | Windows fallback yolu |
| Buffer yönetimi | [[../k042-buffer-management/index]] | Alt veri yapısı |
| Latency optimizasyonu | [[../k043-latency-optimization/index]] | Üst bütçe |
| Driver stack | [[../k041-driver-stack-mimari/index]] | Yığın mimarisi |
| Kernel API | [[../k047-kernel-audio-api/index]] | OS arayüzü |
| IRQ/DMA akışı | [[../k048-interrupt-dma-flow/index]] | Donanım aktarımı |
| Process isolation | [[../k051-process-isolation/index]] | Kilidi tutan süreç sınırı |

---

## §13 Senaryo Yürütmeleri (K036 geneli)

### §13.1 Düşük gecikmeli kayıt senaryosu

1. Uygulama ASIO cihazını listeler ve exclusive açar → [[asio-exclusive-mode]] §5.
2. Buffer boyutu SR ile eşleştirilir (96kHz'de 64/128 örnek seçenekleri) → [[../k043-latency-optimization/buffer-size-selection]].
3. Çift buffer kurulur, sayfalar kilitlenir.
4. Callback zinciri çalışır; girdi buffer'ı doldurulur, çıktısı okunur.
5. Round-trip ölçümü yapılır: hedef 1.34ms (kaynak L160).
6. xrun sayacı 0'da tutulur; artışta buffer büyütme kararı verilir.

### §13.2 Çakışma senaryosu

1. İkinci uygulama exclusive dener → reddedilir.
2. Arayüz, kullanıcılara shared yol önerir → [[../k037-wasapi-exclusive/wasapi-exclusive-shared]].
3. Olay loglanır; birinci uygulama kesintisiz çalışır.

### §13.3 Cihaz kaybı senaryosu

1. USB cihaz çıkar → LOST.
2. Uygulama "cihaz yok" durumuna geçer, çökmez.
3. RECOVERING ile yeniden bağlanma denenir.
4. Başarıda akış devam; başarısızlıkta kullanıcıya bildirim.

### §13.4 Sürücü önceliği senaryosu

1. ASIO kullanılabilirse #1 tercih edilir.
2. Kullanılamazsa WASAPI Exclusive (#2).
3. O da yoksa WASAPI Shared fallback (#6).
4. Sıra kaynağı: `k2-surucu/CLAUDE.md L27-L34`.

### §13.5 Ölçüm senaryosu

1. Sistem kararlı (arka planda ağır işlem yok).
2. Buffer 64 örnek, SR 96kHz.
3. Round-trip ölçümü 3 kez tekrarlanır, medyan alınır.
4. Sonuç §4.1 tablosuyla karşılaştırılır.
5. Hedeften sapma varsa [[../k043-latency-optimization/index]] §§10'a başvurulur.

---

## §14 Ölçüm Protokolü

| Adım | İşlem | Kayıt |
|------|-------|-------|
| 1 | Sistem durulması beklenir (CPU <%5) | T0 CPU |
| 2 | SR/bit/kanal sabitlenir | Konfigürasyon |
| 3 | Buffer boyutu yazılır | 32/64/128/256 |
| 4 | Round-trip ölçümü 3× | Medyan |
| 5 | Jitter ölçümü | µs |
| 6 | xrun sayacı okunur | Sıfır olmalı |
| 7 | CPU ölçümü | Boşta <%1 |
| 8 | Sonuç tabloya işlenir | §4.1 ile karşılaştırma |

> ⚠️ VERIFICATION REQUIRED: Bu protokol repo'da otomatize edilmiş değildir; adım değerleri tasarım önerisidir. Hedef sayısal değerler kaynaktan alınmıştır (§4.1).

---

## §15 Test Matrisi (K036 indeks düzeyi)

| Kapsam | Dosya | Test grubu |
|--------|-------|-----------|
| Exclusive kilidi | `asio-exclusive-mode.md` | EX-01..EX-25 |
| Buffer/callback | `asio-buffer-callback.md` | BC-01..BC-20 (dosya içinde) |
| Fallback | `../k037-wasapi-exclusive` | FB-01..FB-10 |
| Bütçe | `../k043-latency-optimization` | LT-01..LT-15 |
| Donanım aktarımı | `../k048-interrupt-dma-flow` | DM-01..DM-12 |

---

## §16 Sık Sorulan Sorular (K036)

**S1: K036 neden ayrı bir modül?** ASIO yolu, WASAPI/ALSA/CoreAudio yollarından farklı sahiplik ve callback kurallarına sahiptir; karıştırılmaması için ayrıldı.

**S2: K036 kime bağlıdır?** `k2-surucu` katmanına (sürücü), o da K1/K0 donanım ve OS servislerine.

**S3: En kritik kural nedir?** Tek uygulama kilidi (guardrail #1) ve audio thread'te blocking yasak (guardrail #2).

**S4: Latency hedefi nedir?** Round-trip 1.34ms hedef / 1.33ms gerçek (kaynak L160).

**S5: Buffer seçimi nerede?** [[../k043-latency-optimization/buffer-size-selection]].

**S6: Paylaşımlı yol nerede?** [[../k037-wasapi-exclusive/wasapi-exclusive-shared]].

**S7: Kod nerede?** ⚠️ VERIFICATION REQUIRED — repo'da ASIO implementasyonu bulunamadı.

**S8: Ölçüm var mı?** ⚠️ VERIFICATION REQUIRED — donanım ölçümü yok.

---

## §17 Terim Sözlüğü (K036)

| Terim | Tanım |
|-------|-------|
| ASIO | Uygulamayı donanıma doğrudan bağlayan sürücü arayüzü |
| Exclusive | Tek sahipli kullanım yolu |
| Callback | Sürücünün uygulamayı çağırması |
| Double buffer | Dönüşümlü iki buffer |
| Round-trip | Giriş→çıkış toplam gecikme |
| xrun | Underrun/overrun |
| HAL | Donanım soyutlama katmanı |
| DMA | Doğrudan bellek erişimi |
| SR | Örnekleme hızı |
| Fallback | Alternatif yol |
| Guardrail | Bağlayıcı kural |
| Ownership | Cihaz mülkiyeti |

---

## §18 Risk & Açık Konu Kaydı

| # | Konu | Durum | Sahip |
|---|------|-------|-------|
| 1 | ASIO kod implementasyonu yok | ⚠️ VERIFICATION REQUIRED | Embedded |
| 2 | Donanım ölçümü yok | ⚠️ VERIFICATION REQUIRED | QA |
| 3 | SDK lisansı | ⚠️ VERIFICATION REQUIRED | Legal |
| 4 | 64×64 kanal sınırı doğrulaması | ⚠️ VERIFICATION REQUIRED | Embedded |
| 5 | Fallback yolu kodu | ⚠️ VERIFICATION REQUIRED | Backend/Platform |

---

## §19 Kapsam-Dışı Yönlendirme

| Konu | Gideceği modül | Bağlantı |
|------|----------------|----------|
| WASAPI modları | K037 | [[../k037-wasapi-exclusive/index]] |
| CoreAudio | K038 | [[../k038-core-audio-macos/index]] |
| ALSA | K039 | [[../k039-alsa-native/index]] |
| PipeWire | K040 | [[../k040-pipewire-modern/index]] |
| Sürücü yığını | K041 | [[../k041-driver-stack-mimari/index]] |
| Ring buffer | K042 | [[../k042-buffer-management/index]] |
| Latency bütçesi | K043 | [[../k043-latency-optimization/index]] |
| USB ses | K044 | [[../k044-usb-audio-class/index]] |
| Ağ sesi | K045 | [[../k045-network-audio-drivers/index]] |
| Bluetooth | K046 | [[../k046-bluetooth-a2dp/index]] |
| Kernel arayüz | K047 | [[../k047-kernel-audio-api/index]] |
| IRQ/DMA | K048 | [[../k048-interrupt-dma-flow/index]] |
| Görüntüleme | K049 | [[../k049-display-graphics-stack/index]] |
| Hotplug | K050 | [[../k050-device-hotplug-power/index]] |
| İzolasyon | K051 | [[../k051-process-isolation/index]] |
| IPC | K052 | [[../k052-ipc-shared-memory/index]] |
| Lock-free | K053 | [[../k053-threading-lockfree/index]] |

---

### §19.1 D01 kapsam kontrol tablosu

| # | Klasör | Konu | Durum |
|---|--------|------|-------|
| 1 | k036-asio-drivers | ASIO exclusive + callback | Bu klasör |
| 2 | k037-wasapi-exclusive | WASAPI exclusive/shared + session | Komşu |
| 3 | k038-core-audio-macos | CoreAudio HAL + AudioUnit | Komşu |
| 4 | k039-alsa-native | ALSA PCM + period/interrupt | Komşu |
| 5 | k040-pipewire-modern | PipeWire SPA + session manager | Komşu |
| 6 | k041-driver-stack-mimari | Yığın katmanları + HAL | Komşu |
| 7 | k042-buffer-management | Ring buffer + underrun | Komşu |
| 8 | k043-latency-optimization | Latency zinciri + boyut seçimi | Komşu |
| 9 | k044-usb-audio-class | UAC2 + isochronous | Komşu |
| 10 | k045-network-audio-drivers | Dante/AVB/RAVENNA + clock | Komşu |
| 11 | k046-bluetooth-a2dp | A2DP codec + latency | Komşu |
| 12 | k047-kernel-audio-api | syscall + epoll/kqueue/io_uring | Komşu |
| 13 | k048-interrupt-dma-flow | IRQ/DMA + RT scheduling | Komşu |
| 14 | k049-display-graphics-stack | GPU/Metal + display yolu | Komşu |
| 15 | k050-device-hotplug-power | Hotplug + power-state | Komşu |
| 16 | k051-process-isolation | Sandbox + seccomp | Komşu |
| 17 | k052-ipc-shared-memory | IPC + shared ring | Komşu |
| 18 | k053-threading-lockfree | RT thread pool + atomics | Komşu |

### §19.2 K036 bağlantı bütünlüğü (bu klasör)

| Kaynak dosya | Hedef | Tür | Durum |
|--------------|-------|-----|-------|
| `index.md` | `[[asio-exclusive-mode]]` | iç | ✓ |
| `index.md` | ``asio-buffer-callback`` | iç | ✓ (4.1.0'da `[[asio-buffer-callback]]` yapıldı — bkz. §20) |
| `index.md` | `[[asio-latency-hesap]]` · `[[asio-device-lifecycle]]` · `[[asio-thread-model]]` · `[[asio-sdk-entegrasyon]]` · `[[asio-hata-yonetimi]]` | iç | ✓ (4.1.0 eki — 5 yeni topic) |
| `index.md` | `[[../k037-wasapi-exclusive/index]]` | çapraz | ✓ (üretim sırası) |
| `index.md` | `[[../k042-buffer-management/index]]` | çapraz | ✓ |
| `index.md` | `[[../k043-latency-optimization/index]]` | çapraz | ✓ |
| `asio-exclusive-mode.md` | `[[index]]` | iç | ✓ |
| `asio-exclusive-mode.md` | ``asio-buffer-callback`` | iç | ✓ (4.1.0'da `[[asio-buffer-callback]]` yapıldı — §13) |
| `asio-exclusive-mode.md` | `[[asio-latency-hesap]]` · `[[asio-device-lifecycle]]` · `[[asio-thread-model]]` · `[[asio-sdk-entegrasyon]]` · `[[asio-hata-yonetimi]]` | iç | ✓ (4.1.0 eki — §13) |
| `asio-exclusive-mode.md` | `[[../k037-wasapi-exclusive/wasapi-exclusive-shared]]` | çapraz | ✓ |
| `asio-exclusive-mode.md` | `[[../k050-device-hotplug-power/hotplug-detect-flow]]` | çapraz | ✓ |
| 6 yeni topic dosyası (buffer→hata) | `[[index]]` + komşu K036 topic linkleri + yalnız `[[../k037-wasapi-exclusive/*]]` / `[[../k038-core-audio-macos/index]]` çapraz link | iç/çapraz | ✓ (diskte olmayan k041–k053 hedeflerine yeni link verilmedi — düz metin not) |

### §19.3 Kanıt envanteri (K036)

| Kaynak dosya | Kullanılan aralıklar |
|--------------|---------------------|
| `k2-surucu/asio-drivers.md` | L8–L173 (tüm başlıklar + tablolar) |
| `k2-surucu/CLAUDE.md` | L16–L49 (guardrail, öncelik, ADR) |
| `k2-surucu/wasapi-exclusive.md` | L51–L59 (exclusive/shared tablosu) |
| `k2-surucu/index.md` | L41–L71 (ilkeler + metrikler) |
| `k0-isletim-sistemi/windows-core.md` | L18–L331 (OS servisleri) |
| `k2-surucu/buffer-management.md` | L16–L381 (ring/SPSC/double buffer, optimizasyon, adaptif, metrikler) |
| `k2-surucu/latency-optimization.md` | L12–L369 (zincir, buffer tablosu, RT scheduling, izleme, metrikler) |
| `k2-surucu/driver-stack-mimari.md` | L16–L362 (katman diyagramı, HAL, factory, durum makinesi, ErrorChain, metrikler) |
| `k0-isletim-sistemi/windows-api.md` | L25–L249 (ASIO SDK yapısı, callback, buffer config, threading, COM) |
| `k0-isletim-sistemi/README.md` | L90–L92, L230–L284, L359–L369 (initializeASIO, thread öncelik, LockFreeQueue, ASIOTimeInfo) |
| `k3-ses-motoru/README.md` | L37 (ASIO callback noexcept zorunlu) |
| `k12-izleme/README.md` | L72 (ASIO buffer underrun metriği) |

### §19.4 Sürüm notu

Frontmatter `version: 4.0.0` ve `updated: 2026-10-06` tüm D01 (k036–k053) dosyalarında sabittir; değişiklik yalnız sayaç/revizyon eklentisiyle yapılır ve append-only kayda işlenir.

---

### §19.5 Wiki-link biçimi kuralları (bu klasörde uygulanan)

| Kaynak | Hedef biçimi | Örnek |
|--------|--------------|-------|
| Aynı klasör | `[[dosya-adi]]` | `[[asio-exclusive-mode]]` |
| Başka klasör (indeks) | `[[../k0xx-slug/index]]` | `[[../k037-wasapi-exclusive/index]]` |
| Başka klasör (dosya) | `[[../k0xx-slug/dosya-adi]]` | `[[../k050-device-hotplug-power/hotplug-detect-flow]]` |
| Kanıt yolu | backtick düz metin | `` `_backup/.../asio-drivers.md` `` |

### §19.6 Kanıt formatı

| Alan | Format | Zorunluluk |
|------|--------|-----------|
| Dosya yolu | `_backup/arch-2026-10-06_1057/architecture/<klasor>/<dosya>.md` | Gerçek dosya olmalı |
| Satır | `L<başlangıç>-L<bitiş>` veya tek `L<satır>` | Okunmuş aralık |
| Doğrulanamayan | `⚠️ VERIFICATION REQUIRED` | Uydurma yasak |
| Bilinmeyen değer | `UNKNOWN` | Tahmin yazılmaz |

### §19.7 K036 okuma sırası (öneri)

1. Bu dosya (`index.md`) — kapsam ve bağlantılar.
2. `asio-exclusive-mode.md` — sahiplik ve kilidin tamamı.
3. `asio-buffer-callback.md` — callback + buffer veri yolu.
4. `asio-latency-hesap.md` — hesap ve hedefler.
5. `asio-device-lifecycle.md` — açma/kapama/kayıp durumları.
6. `asio-thread-model.md` — RT koşulları.
7. `asio-sdk-entegrasyon.md` — SDK ve lisans.
8. `asio-hata-yonetimi.md` — hata kodları ve eskalasyon.
9. `../k037-wasapi-exclusive/index.md` — Windows alternatif yolu.
10. (Diskte olmayan komşular `../k041-*`, `../k042-*`, `../k043-*` — planlı D01; bu klasörde yalnız düz metin referans.)

### §19.8 Kapı (gate) kontrolü — bu klasör tamamlanmadan geçilemez

- [ ] İki dosya da ≥500 satır
- [ ] Frontmatter 7 alan + version 4.0.0 + updated 2026-10-06
- [ ] Kırık wiki-link = 0
- [ ] Her teknik iddianın kanıtı veya ⚠️ işareti var
- [ ] Türkçe karakter bütünlüğü (mojibake yok)
- [ ] Başka dosyaya yazılmadı
- [ ] Commit atılmadı
- [ ] Her topic dosyasında `backup kanıtı: <dosya> Lx-Ly` bloğu var
- [ ] Diskte olmayan klasörlere yeni wiki-link verilmedi (yalnız düz metin not)

### §19.9 Üretim kararları (K036 bu revizyon)

| # | Karar | Dayanak |
|---|-------|---------|
| 1 | Klasörde **2 dosya** üretilir (`index.md` + `asio-exclusive-mode.md`) | D01 üretimi 2–4 MD aralığında |
| 2 | Planlı `asio-buffer-callback.md` bu revizyonda **üretilmedi**; içerik `asio-exclusive-mode.md` §buffer/callback ile örtüşür | Çakışmayı önlemek için |
| 3 | `asio-buffer-callback` adı yalnız **düz metin** (`` ` ``) olarak geçer; wiki-link yapılmaz | Kırık link = 0 hedefi |
| 4 | Ölçüm değerleri **kaynaktan birebir** taşınır; yeni sayı üretilmez | Zero-Hallucination |
| 5 | Kaynakta olmayan her iddia `⚠️ VERIFICATION REQUIRED` işaretlenir | Zero-Hallucination |
| 6 | Dosya adları bu revizyonda değişmez (in-place) | Vault kuralı 1 |
| 7 | Commit bu oturumda **atılmaz** | Görev kısıtı |
| 8 | **4.1.0 backup-first:** 6 yeni topic dosyası üretildi (buffer-callback, latency, lifecycle, thread, sdk, hata); içerik `_backup/arch-2026-10-06_1057/architecture/` (k2-surucu + k0-isletim-sistemi) birincil kaynaktır | Kullanıcı yönergesi: "İçerik eski backup'tan kontrol edilerek yazılacak" |
| 9 | Her dosyaya `backup kanıtı: <dosya> Lx-Ly` bloğu eklendi | Kullanıcı çıktı kuralı |
| 10 | `` `asio-buffer-callback` `` düz metinleri `[[asio-buffer-callback]]` wiki-linkine çevrildi (dosya artık diskte) | §19.5 biçim kuralı; kırık link = 0 |
| 11 | Backup'ta olmayan iddialar `⚠️ VERIFICATION REQUIRED` ile işaretlendi; bilinmeyen `[UNKNOWN]`/yok | Zero-Hallucination §3 |
| 12 | Backup↔vault çelişkileri bastırılmadı, ikisi de taşındı (buffer 32-64@96k vs 512@48k; hedef 0.5 / 1.34 / <1ms üçlüsü) | "Çelişki varsa backup kazanır + raporla" |
| 13 | Diskte olmayan k041–k053 komşularına **yeni** link verilmedi; yalnız düz metin + "diskte yok — planlı D01 modülü" notu | Kırık link = 0 hedefi |

### §19.10 K036 okuma haritası (ihtiyaç → bölüm)

| İhtiyaç | Bu dosyada | Ayrıntı |
|---------|-----------|---------|
| ASIO nedir, ne zaman kullanılır | §1, §4.1 | `asio-exclusive-mode.md` §1–§3 |
| Öncelik sırası (ASIO #1) | §4.2 | `k2-surucu/CLAUDE.md` L27–L34 |
| Round-trip 1.34/1.33 ms | §4.1, §7 | `asio-drivers.md` L160 |
| Buffer switch <10µs / 8µs | §4.1, §7 | `asio-drivers.md` L163 |
| 64×64 kanal sınırı | §4.1 | `asio-drivers.md` L156–L163 |
| Cihaz kaybı / kilip kırılması | §8, §13.3 | `asio-exclusive-mode.md` §8, §13 |
| Windows alternatifi | §19 | `../k037-wasapi-exclusive/index` |
| Buffer veri yapısı | §6 | `asio-buffer-callback.md` §3 (backup: `buffer-management.md`) · `../k042-buffer-management/index` |
| Gecikme bütçesi | §6, §14 | `asio-latency-hesap.md` §3–§7 · `../k043-latency-optimization/index` |
| Callback ne yapmaz (RT) | — | `asio-buffer-callback.md` §4.4 · `asio-thread-model.md` §3 |
| Durum makinesi / cihaz açma-kapama | — | `asio-device-lifecycle.md` §2–§3 |
| ASIO SDK dosya yapısı / lisans | — | `asio-sdk-entegrasyon.md` §2, §8 |
| `ASIOError_*` kodları / kurtarma / eskalasyon | — | `asio-hata-yonetimi.md` §2–§6 |
| 96kHz zincir 1.76ms / <1ms hedefi (backup) | — | `asio-latency-hesap.md` §7.5 (⚠️ çelişkili hedef grubu) |

---

## §20 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — D01 k036 klasör indeksi | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | §19.9–§19.10 eklendi; §19.2 "EKLENECEK" satırları düz metin notuna çevrildi (488 → 505+ satır) | Vault Documentation Specialist |
| 2026-10-06 | 4.1.0 | **Backup-first multi-MD genişleme:** 6 yeni topic (K036.2–K036.7: `asio-buffer-callback` · `asio-latency-hesap` · `asio-device-lifecycle` · `asio-thread-model` · `asio-sdk-entegrasyon` · `asio-hata-yonetimi`); `asio-exclusive-mode` derinleştirildi (§6.1, §12.1, §13); §1–§6 iskeleti korunarak §3/§5/§12/§19.2/§19.3/§19.7/§19.9/§19.10 güncellendi; `asio-buffer-callback` düz metin → wiki-link. Birincil kaynak: `_backup/arch-2026-10-06_1057/architecture/` (k2-surucu: asio-drivers L8–L173, buffer-management L16–L381, latency-optimization L12–L369, driver-stack-mimari L16–L362, CLAUDE L16–L49, wasapi-exclusive L40–L69, index L41–L71 · k0-isletim-sistemi: windows-api L25–L249, README L90–L369). Çelişki raporları: buffer 32-64@96k vs 512@48k · hedef 0.5/1.34/<1ms+0.88/1.76 (üçü taşındı, ⚠️ işaretli). Commit atılmadı | Embedded Engineer (backup-first revizyon) |
