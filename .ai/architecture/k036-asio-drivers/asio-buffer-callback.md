---
title: "K036.2 — ASIO Buffer & Callback Zinciri (Çift Buffer, bufferSwitch, RT Akış)"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K036.2 — ASIO Buffer & Callback Zinciri

**Bağlantılar:** [[index]] · [[asio-exclusive-mode]] · [[asio-latency-hesap]] · [[asio-thread-model]] · [[asio-hata-yonetimi]] · [[../k037-wasapi-exclusive/index]]

---

## §1 Genel Bakış

Bu doküman, CoreMusic ASIO yolunun **veri taşıma çekirdeğini** tanımlar: çift buffer (double buffering) modeli, sürücünün uygulamayı çağırdığı callback zinciri ve bu callback'in gerçek-zamanlı (RT) koşulları. Üç soru yanıtlanır:

1. **Buffer:** Giriş ve çıkış buffer'ları nasıl doldurulur/boşaltılır, kim sahiptir, geçiş nasıl tetiklenir?
2. **Callback:** Sürücü hangi sırada, hangi bilgiyle çağırır; callback içinde ne yapılır/ne yapılmaz?
3. **Xrun:** Dönem (period) aşımı ne zaman olur, sayacı nasıl tutulur, ilk müdahale nedir?

> **Kod durumu:** Repo'da ASIO implementasyonu **yoktur** (kanıt: [[index]] §1.2). Bu dokümandaki çağrı adları ve akışlar `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` içindeki **tasarım/pseudocode**'dan türetilmiştir; birebir SDK imzası değildir → ilgili satırlar `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

### §1.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| ASIO çift buffer ve buffer geçişi (bufferSwitch) | Genel ring buffer veri yapısı (kavramsal komşu) → `../k042-buffer-management/index` *(diskte yok — planlı D01 modülü)* |
| Callback zinciri sırası ve RT koşulları | RT thread işletim önceliği ayrıntısı → [[asio-thread-model]] |
| Buffer boyutu → gecikme hesabı | Toplam zincir bütçesi → `../k043-latency-optimization/index` *(diskte yok — planlı D01 modülü)* · hesap detayı: [[asio-latency-hesap]] |
| xrun (underrun/overrun) tespiti ve sayacı | Hata kodlarının tamamı ve kurtarma → [[asio-hata-yonetimi]] |
| Cihaz açma/kapama sırası | Yaşam döngüsü → [[asio-device-lifecycle]] |

### §1.2 Durum

| Alan | Durum | Kanıt |
|------|-------|-------|
| Tasarım içeriği | 🟢 Mevcut (kaynak L41–L113) | `k2-surucu/asio-drivers.md` |
| Callback sözleşmesi (şema) | 🟢 Vault'ta mevcut | `.ai/ecosystem/asio-wasapi-rehber.md` §5.4 |
| Kod implementasyonu | 🔴 Repo'da yok | ⚠️ VERIFICATION REQUIRED |
| xrun sayacı implementasyonu | 🔴 Repo'da yok | ⚠️ VERIFICATION REQUIRED |

---

## §2 Mimari Konum

```
┌─────────────────────────────────────────────────────────────────────┐
│ K3 Ses Motoru (Neva Engine) — processAudio(...)                     │
│    ▲ çıktı buffer'ı (doldurulacak)     │                            │
│    │                                   ▼                            │
│ ┌──┴──────────────────────────────────────────────────────────┐     │
│ │ K036.2 BUFFER & CALLBACK KATMANI                            │     │
│ │   Input A ──┐                       ┌── Output A            │     │
│ │   Input B ──┼── sayfa değişimi ─────┼── Output B            │     │
│ │             │   (bufferSwitch)      │                       │     │
│ │   [xrun sayacı]  [callback süresi ölçümü]                   │     │
│ └──┬──────────────────────────────────────────────────────────┘     │
│    │ sürücü çağrısı (RT thread)                                     │
│    ▼                                                                │
│ ASIO sürücüsü → HAL → DMA Engine → donanım FIFO                     │
└─────────────────────────────────────────────────────────────────────┘
```

**Katman sırası (kaynak):** ASIO SDK → Driver Interface → HAL Abstraction → DMA Engine.
Kanıt: `k2-surucu/asio-drivers.md L79–L89`

---

## §3 Çift Buffer (Double Buffering) Modeli

### §3.1 Temel ilke

Kaynakta model iki maddeyle tanımlanır: **"Buffer A okunurken Buffer B yazılır"** ve geçişin **`callbackDrivenMode` ile tetiklenmesi**.

| # | İlke | Açıklama | Kanıt |
|---|------|----------|-------|
| 1 | İki buffer | A ve B dönüşümlü kullanılır | `asio-drivers.md L45–L47` |
| 2 | Aynızamanlı oku/yaz | Bir taraf okunurken diğeri yazılır | `asio-drivers.md L46` |
| 3 | Geçiş tetikleyici | `callbackDrivenMode` | `asio-drivers.md L47` |
| 4 | Kesintisiz akış | Geçiş sırasında ses kesintisi olmaz | `asio-drivers.md L45` |

> `callbackDrivenMode` adı **kaynaktan birebir** alınmıştır; SDK'nın gerçekte hangi bayrak/enum ile karşılandığı repo'da yok → ⚠️ VERIFICATION REQUIRED.

### §3.2 Sayfa (buffer index) geçişi

Sürücü her dönem sonunda bir sonraki buffer sayfasını işaret eder; uygulama o sayfayı doldurur/boşaltır. Kaynak pseudocode'unda ilk parametre `index`'tir (`ASIOCallback(long index, long process)` — `asio-drivers.md L64`).

```
Dönem n:    sürücü → index=0  → uygulama Input[0] okur, Output[0] yazar
Dönem n+1:  sürücü → index=1  → uygulama Input[1] okur, Output[1] yazar
Dönem n+2:  sürücü → index=0  → döngü sürer
```

| Alan | Sahip | Yazan | Okuyan | Kanıt |
|------|-------|-------|--------|-------|
| Input buffer (ham kayıt) | Sürücü/HW | Donanım (DMA) | Uygulama (callback içinde) | `asio-drivers.md L66` |
| Output buffer (ham çıkış) | Uygulama | Uygulama (callback içinde) | Donanım (DMA) | `asio-drivers.md L74` |
| buffer index sayacı | Sürücü | Sürücü | Uygulama (parametre) | `asio-drivers.md L64` |

### §3.3 Ring buffer ilişkisi (kaynak şeması)

Kaynak, çift buffer'a ek olarak ring buffer şemasını da verir:

```
Head → [data] → [data] → [data] → Tail
Head, donanım tarafından güncellenir
Tail, uygulama tarafından güncellenir
```

Kanıt: `asio-drivers.md L54–L59`.

| Kavram | Rol | Not |
|--------|-----|-----|
| Head | Donanım (yazıcı) konumu | HW/DMA tarafı ilerletir |
| Tail | Uygulama (okuyucu) konumu | Uygulama tarafından ilerletir |
| Çift buffer | Dönüşümlü iki sayfa | ASIO'nun birincil modeli |
| Ring buffer | Sürekli kuyruk | Kaynakta birlikte anılmış; hangi modülün kullandığı → ⚠️ VERIFICATION REQUIRED (repo kodu yok) |

> Kapsam notu: ring buffer'ın **genel** veri yapısı tanımı bu klasörün dışındadır → `../k042-buffer-management/index` *(diskte yok — planlı D01 modülü)*.

### §3.4 Buffer sayfası yaşam döngüsü (dönem içi)

| # | Adım | Ne olur | Zorunlu kural |
|---|------|---------|----------------|
| 1 | Sürücü hazır sinyali verir | Callback çağrılır (`index` ile) | RT thread, bloklama yok |
| 2 | Girdi okunur | `readInputBuffer(index, inputBuffers)` | Kopya tek geçişte |
| 3 | Motor çalışır | `feedToEngine(...)` → `readFromEngine(...)` | Tahsis yok |
| 4 | Çıktı yazılır | `writeOutputBuffer(index, outputBuffers)` | Callback dönmeden tamamlanmalı |
| 5 | Dönem kapanır | Sürücü HW'yi besler, sonraki `index`'e geçer | Süre ≤ buffer period'u |

Kanıt (adım 1–4): `asio-drivers.md L63–L77`.

---

### §3.5 Backup — Lock-free veri yapıları ve buffer boyut optimizasyonu (buffer-management.md)

Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md`.

| Konu | Backup içeriği | Kanıt |
|---|---|---|
| Ring buffer şeması | Read/Write pointer ASCII şeması (L18–L32) | L16–L32 |
| Lock-free ring buffer | `write/read` — `memory_order_relaxed/acquire/release`, tek producer/tek consumer | L36–L98 |
| SPSC queue | `push/pop` — dolu/boş tespiti iki atomik işaretçiyle, mutex yok | L178–L226 |
| Double buffering | ASCII akış şeması (Zaman Dilimi 1/2) | L100–L125 |
| DoubleBuffer sınıfı | `std::mutex` + `lock_guard` içerir → **RT thread'de yasak, RT-dışı taslak** | L127–L176 |
| Buffer boyut optimizasyonu | `calculateOptimalBufferSize`: min frames → **%20 güvenlik payı** → 2'nin kuvvetine yuvarla → min/max sınır | L228–L258 |
| Adaptif buffer | `AdaptiveBuffer::adjust`: 100 örneklik jitter geçmişi, `targetSize = (avgJitter×2 + targetLatency) × SR / 1000`, yumuşak geçiş ±1 | L260–L301 |
| Buffer performans metrikleri | Write <1µs (gerçek 0.8µs) · Read <1µs (0.7µs) · Buffer değişim <5µs (3.2µs) · CPU <%0.1 · Bellek <10MB | L357–L365 |
| Faz planı | Faz 1 ring buffer → Faz 2 double buffering → Faz 3 SPSC → Faz 4 adaptif | L375–L381 |

```
Buffer boyutu seçimi (backup L240-L257):

  minFrames   = (sampleRate × targetLatencyMs) / 1000
  safeFrames  = minFrames × 1.2                 ← %20 güvenlik payı
  optimal     = 2'nin kuvveti (safeFrames'e yuvarla)
  optimal     = clamp(optimal, minBufferSize, maxBufferSize)
```

⚠️ VERIFICATION REQUIRED: adaptif buffer `sampleRate = 96000` varsayımıyla yazılmıştır
(backup L298); proje varsayılanı 48kHz'tir (`.ai/brain.md` L295). Uyarlama repo'da yoktur.

> **Çelişki notu:** backup DoubleBuffer mutex kullanır (L140/L149/L159); `.ai/AGENTS.md` §16.2
> RT thread'de mutex yasaklar → ASIO callback yolunda **lock-free SPSC (L36–L98)** esastır,
> mutex'li DoubleBuffer yalnız RT-dışı referanstır. Ayrıntı: [[asio-thread-model]] §4.4.

---

## §4 Callback Zinciri

### §4.1 Kaynak pseudocode (değiştirilmeden)

```cpp
void ASIOCallback(long index, long process) {
    // 1. Input buffer'ı oku
    readInputBuffer(index, inputBuffers);

    // 2. K3 Ses Motoru'na ilet
    feedToEngine(inputBuffers, sampleCount);

    // 3. K3'ten output buffer'ı al
    readFromEngine(outputBuffers, sampleCount);

    // 4. Output buffer'ı donanıma yaz
    writeOutputBuffer(index, outputBuffers);
}
```

Kanıt: `k2-surucu/asio-drivers.md L63–L77`.

> ⚠️ VERIFICATION REQUIRED: Bu isimler **kaynak dokümanın kendi pseudocode'udur**; gerçek ASIO SDK callback imzaları (ve `bufferSwitch` / `bufferSwitchTimeInfo` gibi adlar) repo'da bulunmamıştır. Rehber yalnız şemayı verir: `sürücü ──bufferSwitch(index)──> CoreMusic K2 callback` (`.ai/ecosystem/asio-wasapi-rehber.md §5.4`).

### §4.2 Adım sırası ve bağımlılık tablosu

| Sıra | Adım | Girdi | Çıktı | Bağımlılık |
|------|------|-------|-------|------------|
| 1 | `readInputBuffer` | `index`, sürücü buffer'ı | `inputBuffers` | Sürücü verisi hazır olmalı |
| 2 | `feedToEngine` | `inputBuffers`, `sampleCount` | motor durumu | Motor tahsis etmemiş olmalı |
| 3 | `readFromEngine` | motor çıktısı | `outputBuffers` | Adım 2 tamamlanmalı |
| 4 | `writeOutputBuffer` | `outputBuffers`, `index` | — | Adım 3 tamamlanmalı; dönem süresi dolmadan |

### §4.3 Callback sözleşmesi (vault şeması)

`.ai/ecosystem/asio-wasapi-rehber.md` §5.4 (aşağıdan birebir):

```text
ASIO yolu:  sürücü ──bufferSwitch(index)──> CoreMusic K2 callback
  girdi: hazır input buffer (index), çıktıyı K2 doldurur
  kural: callback içinde bloklama YOK (lock-free — ASIO karakteri §3.4)
WASAPI exclusive yolu: event-driven ──IAudioClient event──> K2 callback (aynı K3'e teslim)
Ortak K2→K3 arayüzü: process(io, channels, n) — 3/6 §5.1 ile aynı imza
```

| Sözleşme maddesi | Değer | Kaynak |
|------------------|-------|--------|
| Tetikleyici | `bufferSwitch(index)` | rehber §5.4 |
| Girdi | hazır input buffer + index | rehber §5.4 |
| Kural | callback içinde bloklama YOK (lock-free) | rehber §5.4 + §3.1 |
| Ortak motor arayüzü | `process(io, channels, n)` | rehber §5.4 (3/6 §5.1 ile aynı imza) |
| WASAPI yolu da aynı arayüze gider | `IAudioClient` event → aynı K3 | rehber §5.4 |

> `process(io, channels, n)` imzası vault'ta geçtiği için kullanılmıştır; repo kodu yine yok → ⚠️ VERIFICATION REQUIRED (implementasyon kanıtı).

### §4.4 Callback içinde: Yapılır / Yapılmaz

| ✅ Yapılır | ❌ Yapılmaz | Kural kaynağı |
|-----------|------------|---------------|
| Hazır buffer'dan oku/yaz | `malloc` / `new` / `delete` | `.ai/AGENTS.md §16` (Embedded: Zero-allocation) |
| Hesap (DSP, kopya) | Mutex / lock_guard / unique_lock | `.ai/AGENTS.md §16` (lock-free) |
| Sayac güncelle (xrun, süre) | Dosyaya log yazma | `asio-exclusive-mode.md §22` (kod öncesi kontrol listesi) |
| Atomic bayrak bırak (sinyal) | `sleep` / bloke eden wait | `k2-surucu/CLAUDE.md L21` (audio thread blocking yasak) |
| `noexcept` sınırında kal | Throw / istisna fırlatma | `.ai/AGENTS.md §16` (noexcept) |

### §4.5 Guardrail bağlantısı

| # | Guardrail | İhlalde görünen belirti | Bu dosyadaki karşılığı |
|---|-----------|-------------------------|------------------------|
| 2 | Audio thread blocking yasak → ses takılması | Callback overrun | §7.2 |
| 3 | Buffer underrun koruması zorunlu → crackling | Xrun artışı | §7.1 |
| 4 | Sample rate mismatch önlem → pitch shift | Açılışta reddet | §6.3, [[asio-device-lifecycle]] §6 |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md L18–L24`.

---

## §5 Buffer Kurulumu (Açılış Sırası)

### §5.1 On adımlık kurulum (özet → [[asio-exclusive-mode]] §5)

| # | Adım | Bu dosyada | Ayrıntı |
|---|------|-----------|---------|
| 1–4 | Cihaz listesi, ön kontrol, exclusive open, format sabitleme | — | [[asio-exclusive-mode]] §5.1 |
| 5 | Buffer hazırlığı | ✅ | §5.2 aşağıdaki |
| 6 | Bellek kilidi (page lock) | ✅ | §5.3 |
| 7 | Callback kaydı | ✅ | §4 |
| 8 | Akış başlatma | ✅ | §5.4 |
| 9–10 | İzleme / ters sıra | ✅ | §7, [[asio-exclusive-mode]] §5.1 |

### §5.2 Buffer boyut seçimi (kaynak tablosu)

| Buffer | SR | Tek yön süre | Kullanım etiketi | Kanıt |
|--------|-----|--------------|------------------|-------|
| 32 sample | 96kHz | 0.33ms | minimum | `asio-drivers.md L50` |
| 64 sample | 96kHz | 0.67ms | dengeli | `asio-drivers.md L51` |
| 128 sample | 96kHz | 1.33ms | güvenli | `asio-drivers.md L52` |
| 128 sample | 48kHz | 2.67ms | (48k tablosu) | `k2-surucu/CLAUDE.md L41` |
| 256 sample | 48kHz | 5.33ms | (48k tablosu) | `k2-surucu/CLAUDE.md L40` |
| 512 sample | 48kHz | 10.67ms | **proje varsayılanı** | `k2-surucu/CLAUDE.md L39` + `.ai/brain.md L295` |

`.ai/brain.md L295` birebir: `ASIO Buffer: 512 sample varsayılan (64-1024), 48kHz, 32-bit float, ~10.67ms gecikme.`

→ Gecikme hesabı ayrı dosyada: [[asio-latency-hesap]].

### §5.3 Bellek kilidi (page lock)

| Adım | İşlem | Hata durumunda | Kanıt |
|------|-------|----------------|-------|
| 6a | Buffer sayfalarını sayfa-fault'a karşı kilitle | Kilitleme hatası → devam et + uyarı | `asio-exclusive-mode.md §5.1` (adım 6) |
| 6b | Kilitli sayfa sayısını doğrula | Kilitsiz sayfa varsa log (test EX-14) | `asio-exclusive-mode.md §17` EX-14 |
| 6c | Kapanışta kilidi bırak | Sıra: stop → detach → flush → close → unlock | `asio-exclusive-mode.md §5` (adım 10) |

> Kaynakta "Memory Lock 100%" hedefi `asio-exclusive-mode.md §5` içinde geçer; asıl ölçüm belgesi `../k043-latency-optimization/index` *(diskte yok — planlı D01 modülü)*.

### §5.4 Akış başlatma ve ön-doldurma

| # | Adım | Amaç | Kanıt |
|---|------|------|-------|
| 1 | Akışı başlat (start) | DMA döngüsü başlar | `asio-exclusive-mode.md §5` (adım 8) |
| 2 | İlk iki callback'te FIFO'yu doldur | İlk dönem underrun'u önlenir | `asio-exclusive-mode.md §16.1` (adım 9) |
| 3 | İzleme başlat | callback süresi, xrun, cihaz varlık bayrağı | `asio-exclusive-mode.md §16.1` (adım 10) |

---

## §6 Format ve SR Davranışı

### §6.1 Desteklenen kombinasyon doğrulaması

| Kontrol | Kural | İhlalde |
|---------|-------|---------|
| SR uyuşmazlığı | Açılışta reddet (guardrail #4) | Pitch shift |
| Bit derinliği uyuşmazlığı | Açılışta reddet | Test EX-08 (açılış reddi) |
| Kanal sayısı | 64×64 sınırı korunur | Test EX-22 |
| Buffer boyutu desteklenmiyor | `ASIOError_BufferSize` → alternatif boyut | `asio-drivers.md L110` |

### §6.2 32-bit float kuralı

Ses verisi 32-bit float'tır (proje kuralı); vault kanıtı: `.ai/brain.md L295` ("32-bit float"). DSP donanım kararı ADR-017'dir (`.ai/AGENTS.md §21` Cross References).

### §6.3 SR değişim akışı

SR değiştirme = **akış durdur → format sabitle → buffer yeniden kur → akışı başlat**; akış açıkken SR değiştirilmez.

| Adım | İşlem | Risk |
|------|-------|------|
| 1 | Akışı durdur | Devam eden DMA yazımı |
| 2 | `canSampleRate` doğrulası | Desteklenmeyen SR → reddet |
| 3 | Buffer boyutunu SR ile yeniden eşleştir | Yanlış süre → xrun |
| 4 | `setSampleRate` | Hata → eski SR'ye dön |
| 5 | Akışı başlat | İlk 2 callback'te FIFO ön-doldur |

İmza adları kaynaktaki `ASIODriverManager` taslağındandır (`asio-drivers.md L125–L127`: `canSampleRate`, `setSampleRate`) → ⚠️ VERIFICATION REQUIRED (repo implementasyonu yok). Ayrıntı: [[asio-device-lifecycle]] §5.

---

## §7 Xrun (Underrun / Overrun)

### §7.1 Sınıflandırma

| Tür | Ne zaman | Belirti | Kaynak terim |
|-----|----------|---------|--------------|
| Underrun | Çıktı buffer'ı zamanında dolmadı | Crackling / pop | `k2-surucu/CLAUDE.md L22` (guardrail #3) |
| Overrun | Girdi okunmadı / callback çok uzun | Ses kesilmesi | `asio-drivers.md` hata modları (index §9) |
| Callback overrun | Dönem süresi aşıldı | Ses kesilmesi → RT thread engelli | [[index]] §9 |

### §7.2 Tespit ve sayacı

| Ölçüm | Tanım | Hedef |
|-------|-------|-------|
| Callback süresi | Callback giriş→çıkış arası | ≤ buffer period'u |
| Buffer değişim süresi | Sayfa geçişi | <10µs hedef / 8µs gerçek (`asio-drivers.md L163`) |
| xrun sayacı | Underrun+overrun toplamı | 0 ([[index]] §10 kontrol listesi: "xrun/underrun sayacı loglanıyor") |
| CPU (boşta) | Boşta kullanım | <%1 hedef / %0.3 (`asio-drivers.md L161`) |

> Metrik adı "xrun" — `.ai/ecosystem/asio-wasapi-rehber.md §3.4` içinde "xrun (underflow/overflow) toleransı" ve K14'e metrik yayını (`driver_path`, `xrun_count` — §5.1) olarak geçer.

### §7.3 Müdahale sırası (karar ağacı)

```text
xrun sayacı arttı mı?
  │
  ├─ HAYIR → izlemeye devam
  │
  └─ EVET
      ├─ 1) Buffer boyutunu artır (32→64→128)   [asio-latency-hesap §6]
      ├─ 2) RT thread'de blocking çağrıyı bul    [asio-thread-model §5]
      ├─ 3) Bellek kilidini doğrula (page lock)  [§5.3]
      └─ 4) Hâlâ artıyorsa → ölçüm protokolüne dön [[index]] §14
```

İlk müdahale "buffer boyutunu artır" — kanıt: [[index]] §9 (underrun satırı: "İlk müdahale: Buffer boyutunu artır").

---

## §8 Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Öncelik | Bağlantı |
|---|------------|-------------------|---------|----------|
| 1 | Callback dönmeden dönem süresi dolar | xrun++ ; ses bozulur ama süreç devam eder | HIGH | §7 |
| 2 | Aynı `index` üst üste gelir (sürücü davranışı) | Uygulama idempotent okur/yazar | HIGH | ⚠️ VERIFICATION REQUIRED |
| 3 | Buffer boyutu cihaz tarafından desteklenmiyor | `ASIOError_BufferSize` → alternatif sun | MEDIUM | `asio-drivers.md L110` |
| 4 | Motor buffer'a geç yazarsa (geç frame) | Çıktı zero-fill → underrun önlenir | MEDIUM | ⚠️ VERIFICATION REQUIRED (tasarım önerisi) |
| 5 | SR akış açıkken değiştilirse | Reddet / akışı durdur | CRITICAL | §6.3 |
| 6 | Kapanışta callback hâlâ çalışıyor | Önce akışı durdur, sonra callback kaldır | CRITICAL | [[asio-exclusive-mode]] §7 madde 5 |
| 7 | İlk callback'te FIFO boş | İlk iki callback'te ön-doldurma | HIGH | §5.4 |

---

## §9 Hata Modları (buffer/callback eksenli)

| Hata | Belirti | Kök neden | İlk müdahale | Kanıt |
|------|---------|-----------|--------------|-------|
| `ASIOError_BufferSize` | Boyut reddi | Cihaz desteklemiyor | Alternatif boyut öner | `asio-drivers.md L110` |
| `ASIOError_UnableToStart` | Başlatılamıyor | Zamanlama/sürücü | 3 yeniden deneme | `asio-drivers.md L112` |
| Underrun (guardrail #3) | Crackling | Geç callback | Buffer artır | `k2-surucu/CLAUDE.md L22` |
| SR mismatch (guardrail #4) | Pitch shift | Farklı SR | Açılışta reddet | `k2-surucu/CLAUDE.md L23` |
| Callback overrun | Ses kesilmesi | Blocking çağrı | Çağrıyı kaldır | [[index]] §9 |

Tam hata kataloğu ve kurtarma: [[asio-hata-yonetimi]].

---

## §10 Test Matrisi (BC serisi)

| Test ID | Senaryo | Girdi | Beklenen | Öncelik |
|---------|---------|-------|----------|---------|
| BC-01 | Normal callback akışı | Akış aktif | Her dönem 4 adım tamamlanır | P0 |
| BC-02 | Buffer A/B dönüşümü | 2+ dönem | index 0/1 değişir, kesinti yok | P0 |
| BC-03 | Girdi→motor→çıkış bütünlüğü | Test tonu | Girdi-çıkış korelasyonu | P0 |
| BC-04 | İlk dönem underrun yok | Akış başlangıcı | FIFO dolu | P0 |
| BC-05 | Yapay gecikme (callback overrun) | Sleep ekleme | xrun sayacı artar | P1 |
| BC-06 | Buffer 32 @96kHz | Boyut 32 | 0.33ms dönem, xrun=0 | P1 |
| BC-07 | Buffer 64 @96kHz | Boyut 64 | 0.67ms dönem, xrun=0 | P0 |
| BC-08 | Buffer 128 @96kHz | Boyut 128 | 1.33ms dönem, xrun=0 | P1 |
| BC-09 | Buffer 512 @48kHz (varsayılan) | Boyut 512 | 10.67ms dönem | P0 |
| BC-10 | Desteklenmeyen boyut | Boyut reddi | `ASIOError_BufferSize` yolu | P1 |
| BC-11 | Buffer değişim süresi ölçümü | 1000 dönem | <10µs hedef | P1 |
| BC-12 | CPU boşta ölçüm | Boşta | <%1 | P2 |
| BC-13 | Kapanışta sıra | stop → detach | Callback kapanışta çağrılmaz | P0 |
| BC-14 | SR açıkken değiştirme denemesi | SR değişimi | Red / akış durur | P0 |
| BC-15 | Sayfa kilidi (page lock) | Kilitsiz sayfa | 0 (EX-14 ile örtüşür) | P1 |
| BC-16 | 64×64 kanal | Kanal sınırı | Sınır korunur | P2 |
| BC-17 | Ölçüm tekrarlanabilirliği | 3 ölçüm | Medyan §tablo ile karşılaştırılır | P1 |
| BC-18 | Fallback sonrası buffer temizliği | ASIO→WASAPI | Eski buffer bırakılır | P1 |
| BC-19 | RT thread'de tahsis denetimi | Kod taraması | malloc/new = 0 | P0 |
| BC-20 | Log yazımı denetimi | Kod taraması | Dosya I/O = 0 (callback içinde) | P1 |

> `[[index]] §15` bu seriyi "BC-01..BC-20 (dosya içinde)" olarak önceden planlamıştır; tablo bu plana uyar.

---

## §11 Bağımlılıklar

| Bağımlılık | Yön | Not |
|-----------|-----|-----|
| ASIO SDK (callback yönetimi, buffer değişimi) | Alt | `asio-drivers.md L85` (ASIO SDK sorumluluğu) |
| K1 Windows Core servisleri | Alt | Thread/event/bellek → `k0-isletim-sistemi/windows-core.md` (backup) |
| Ring/double buffer veri yapısı | Alt | `../k042-buffer-management/index` *(diskte yok — planlı D01)* |
| K3 ses motoru (`process(io, channels, n)`) | Üst | rehber §5.4 |
| Xrun metriği yayını (K14) | Yatay | rehber §5.1 (`driver_path`, `xrun_count`) |

---

## §12 Kanıt Satırları

| # | İddia | Kanıt yolu | Satır/Bölüm |
|---|-------|-----------|-------------|
| 1 | Double buffering + `callbackDrivenMode` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | L45–L47 |
| 2 | Ring buffer şeması (Head/Tail) | aynı dosya | L54–L59 |
| 3 | Callback pseudocode (4 adım) | aynı dosya | L63–L77 |
| 4 | Buffer boyut tablosu (32/64/128 @96kHz) | aynı dosya | L50–L52 |
| 5 | 48kHz buffer süreleri (512/256/128) | `_backup/.../k2-surucu/CLAUDE.md` | L38–L43 |
| 6 | Guardrail 2/3/4 (blocking, underrun, SR) | aynı dosya | L18–L24 |
| 7 | Callback sözleşmesi `bufferSwitch(index)` + lock-free | `.ai/ecosystem/asio-wasapi-rehber.md` | §5.4, §3.1 |
| 8 | Ortak motor arayüzü `process(io, channels, n)` | aynı dosya | §5.4 |
| 9 | Zero-allocation / lock-free / noexcept standardı | `.ai/AGENTS.md` | §16 (Quality Standards — Embedded) |
| 10 | ASIO Buffer 512 varsayılan, 32-bit float | `.ai/brain.md` | L295 |
| 11 | Buffer değişim <10µs / 8µs · CPU <%1 / %0.3 | `k2-surucu/asio-drivers.md` | L163, L161 |
| 12 | xrun metrik yayını (`driver_path`, `xrun_count`) | `.ai/ecosystem/asio-wasapi-rehber.md` | §5.1 |
| 13 | Lock-free ring buffer + SPSC (memory_order) | `_backup/.../k2-surucu/buffer-management.md` | L36–L98, L178–L226 |
| 14 | Buffer boyut optimizasyonu (%20 pay, 2'nin kuvveti) | aynı dosya | L228–L258 |
| 15 | Adaptif buffer (jitter geçmişi 100 örnek) | aynı dosya | L260–L301 |
| 16 | Buffer performans metrikleri (değişim <5µs) | aynı dosya | L357–L365 |
| 17 | ASIO buffer konfigürasyon tablosu (512/48000/32f/8.1) | `_backup/.../k0-isletim-sistemi/windows-api.md` | L79–L87 |

### §12.1 Backup Kanıt Özeti (backup-first ekleme)

```
backup kanıtı: k2-surucu/buffer-management.md      L16-L32   (ring buffer ASCII şeması)
backup kanıtı: k2-surucu/buffer-management.md      L36-L98   (lock-free ring buffer kodu)
backup kanıtı: k2-surucu/buffer-management.md      L100-L125 (double buffering akış şeması)
backup kanıtı: k2-surucu/buffer-management.md      L127-L176 (DoubleBuffer mutex'li — RT-dışı, çelişki §3.5)
backup kanıtı: k2-surucu/buffer-management.md      L178-L226 (SPSC queue)
backup kanıtı: k2-surucu/buffer-management.md      L228-L258 (buffer boyut optimizasyonu)
backup kanıtı: k2-surucu/buffer-management.md      L260-L301 (adaptif buffer)
backup kanıtı: k2-surucu/buffer-management.md      L357-L365 (performans metrikleri)
backup kanıtı: k2-surucu/asio-drivers.md           L43-L77   (double buffer + ring + callback)
backup kanıtı: k2-surucu/CLAUDE.md                 L18-L43   (guardrail + 48k buffer süreleri)
backup kanıtı: k0-isletim-sistemi/windows-api.md   L79-L87   (ASIO buffer konfigürasyonu)
kanıt: .ai/AGENTS.md §16 (zero-allocation / lock-free / noexcept)
kanıt: .ai/brain.md L295 (512 varsayılan / 48kHz / 32-bit float)
```

---

## §13 Wiki-Bağlantılar

| Hedef | Bağlantı | İlişki |
|-------|----------|--------|
| K036 indeksi | [[index]] | Üst hub |
| Exclusive mode (kilidi) | [[asio-exclusive-mode]] | Buffer kurulumu adım 5 bu dokümanda |
| Latency hesabı | [[asio-latency-hesap]] | Dönem süresi → ms dönüşümü |
| Yaşam döngüsü | [[asio-device-lifecycle]] | SR/buffer yeniden kurulumu |
| Thread modeli | [[asio-thread-model]] | Callback RT koşulları |
| SDK entegrasyonu | [[asio-sdk-entegrasyon]] | Callback kaydı ve SDK arayüzü |
| Hata yönetimi | [[asio-hata-yonetimi]] | `ASIOError_*` kodları |
| WASAPI komşusu | [[../k037-wasapi-exclusive/index]] | Fallback yolu |

---

## §14 Risk Kaydı

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|----------|------|---------|
| B1 | Pseudocode ile gerçek SDK imzası uyuşmazlığı | Yüksek | Orta | ⚠️ işareti + SDK entegrasyon dosyası |
| B2 | xrun sayacı implementasyonu yok | Yüksek | Yüksek | Test BC-05/BC-11 |
| B3 | Ring buffer vs çift buffer karmaşası | Orta | Orta | §3.3 kapsam notu |
| B4 | Callback'te tahsis sızması | Orta | Yüksek | BC-19 kod taraması |
| B5 | Ölçüm olmadan hedef yazma | Orta | Orta | [[index]] §14 protokolü |

---

## §15 Doğrulama Kontrol Listesi

- [ ] Buffer A/B dönüşümü kesintisiz (BC-02)
- [ ] Callback 4 adımı sırayla çalışıyor (BC-01)
- [ ] İlk iki callback'te FIFO ön-dolduruluyor (BC-04)
- [ ] Callback içinde tahsis/blokama/log yok (BC-19, BC-20)
- [ ] xrun sayacı 0'da (BC-05 hariç)
- [ ] Buffer değişim süresi <10µs (BC-11)
- [ ] SR açıkken değiştirilemiyor (BC-14)
- [ ] Varsayılan 512 @48kHz kabul ediliyor (BC-09)
- [ ] Kapanışta sıra korunuyor (BC-13)
- [ ] Fallback'te eski buffer bırakılıyor (BC-18)

---

## §16 Sık Sorulan Sorular

**S1: Buffer kimin?** Input buffer sürücü/donanım, output buffer uygulama sahipliğindedir (§3.2).

**S2: Geçiş ne tetikler?** Kaynağa göre `callbackDrivenMode` (§3.1); vault sözleşmesinde tetikleyici `bufferSwitch(index)` (§4.3).

**S3: Kaç buffer?** İki (A/B) dönüşümlü — çift buffer (§3.1).

**S4: Varsayılan boyut?** 512 örnek @48kHz ≈ 10.67ms (`.ai/brain.md L295`); aralık 64–1024 (aynı kaynak).

**S5: Xrun artarsa ne yapılır?** Karar ağacı §7.3 — önce buffer boyutu, sonra blocking denetimi.

**S6: Hesaplar nerede?** [[asio-latency-hesap]].

**S7: Kod nerede?** ⚠️ VERIFICATION REQUIRED — repo'da ASIO implementasyonu yok.

---

## §17 Terim Sözlüğü (bu dosya)

| Terim | Tanım |
|-------|-------|
| Çift buffer (double buffering) | Dönüşümlü iki buffer ile kesintisiz akış |
| Sayfa / index | Callback'e gelen buffer kimliği (0/1) |
| Dönem (period) | Tek callback aralığı; süre = buffer/SR |
| bufferSwitch | Sürücünün buffer hazır diye çağrısı (rehber §5.4) |
| Underrun | Çıktının zamanında dolmaması |
| Overrun | Girdinin okunamaması / dönem aşımı |
| xrun | Underrun+overrun ortak sayacı |
| FIFO ön-doldurma | Akışta ilk iki callback'te donanım tamponunu doldurma |
| Page lock | Buffer sayfalarını takas dışında tutma |
| RT thread | Gerçek-zamanlı callback thread'i |
| `process(io, channels, n)` | Ortak K2→K3 motor arayüzü (rehber §5.4) |

---

## §18 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — buffer & callback dosyası (multi-md revizyonu) | Vault Documentation Specialist |
