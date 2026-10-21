# Sözleşmeler — Thread · Sahiplik · Arayüz (Neva Engine disk gerçekleri)

> **Otorite:** disk kodu (`app-cpp-projects/neva-engine/include/neva/*.h`) + atıf yaptığı ADR'ler
> (ADR-019 §2.2b · ADR-017 · ADR-0105 §2.5/§5 · ADR-106 §2.2b — ADR metinleri frozen, okunur
> değiştirilmez). Bu dosya SKILL.md §4 üzerinden bağlayıcıdır.
> **Kural:** sözleşmeyi değiştiren her kod imza/ownership tablosunu da günceller; tabloya
> aykırı kod = H001 RED.

## §1 Katman & Dosya Sözleşmesi (Bridge + Adapter — ADR-019)

```text
[Çekirdek: decode / DSP / state machine]  ──yalnız──►  IAudioBackend (arayüz)
                                                            ▲
                              ┌─────────────────────────────┴───────────────┐
                              │ adapter dosyaları (OS kodu BURADA)          │
                              │  src/wasapi_backend.cpp · src/null_backend.cpp
                              │  (ASIO adapter: DOSYA YOK — PLANNED, §1.2 SKILL.md)
                              └─────────────────────────────────────────────┘
```

| Kural | Kaynak (kod yorumu) | Denetim |
|-------|---------------------|---------|
| Çekirdek **yalnız** `IAudioBackend` görür; OS API'si çekirdekte **YASAK** | `iaudio_backend.h` §kural: "OS API'si cekirdekte YASAK; `#ifdef`/OS kodu yalniz adapter dosyalarinda" (ADR-019 kural 1) | `#include <windows.h>`/`<mmdeviceapi>` yalnız `src/*backend*.cpp` içinde |
| Tüm adapter'lar **aynı** `BackendResult` enum'unu döndürür → fallback tablosu OS'tan bağımsız | `iaudio_backend.h` (ADR-019 kural 4) | Yeni sonuç değeri eklenecekse enum + fallback tablosu birlikte |
| Fabrika fonksiyonları: çekirdek `unique_ptr<IAudioBackend>` alır | `createNullBackend()` / `createWasapiBackend()` — WASAPI başarısızsa `nullptr` (kod yorumu) | nullptr kontrolü çağrı yerinde |
| Build | `CMakeLists.txt`: `neva_engine` STATIC (çekirdek+adapter) · `neva_session` SHARED (C ABI) · `ole32 avrt uuid` link | CLAUDE/ctest §9.1 |

## §2 RT Callback Sözleşmesi

```cpp
// iaudio_backend.h — bu alias TÜM adapter'ların bağlandığı tek callback sözleşmesi
using RenderCallback = void (*)(void* userData, float* out, std::uint32_t frames,
                                std::uint16_t channels) noexcept;
```

| Alan | Zorunluluk | Not |
|------|-----------|-----|
| `out` | **interleaved** float, cihaz formatında | planar isteyen DSP katmanı kendi deinterleave/scratch'ini `prepare()`'da çözér |
| `frames` | Her çağrıda değişebilir (R6) | sabit blok varsayımı yasak |
| `channels` | `StreamConfig.channels` ile eşleşmeli (varsayılan 2) | mismatch → `open()` başarısız sayılır |
| `noexcept` | İmza gereği (R3) | Gövdede throw yok; hata fail-silent |
| Tahsis/lock/log | **YOK** (R1/R2/R7) | "icinde tahsisat/lock/log YOK" — kod yorumu (ADR-017) |
| Çağrı kaynağı | Backend thread'i (WASAPI: event-driven; MMCSS "Pro Audio") | UI thread'inden çağrılmaz |

**Hazırlık/zamanlama sözleşmesi:**
`enumerate → open(cfg) → start(cb, userData) → [RT callback döngüsü] → stop() → kill()`
— `start()`'tan önce `open()` başarısı zorunlu; `kill()` = kill-switch (ADR-019 §2.2c, kod yorumu).
JUCE dünyasındaki karşılığı (`prepareToPlay` tahmin → `processBlock` gerçek, S1): tahmin **garanti
değildir**, gerçek `frames` her seferinde okunur.

## §3 Sahiplik Tablosu (kim yazar / kim okur / hangi sırayla)

| Nesne | Yazar (thread) | Okuyucu (thread) | Mekanizma | Değişirse |
|-------|----------------|------------------|-----------|-----------|
| `EqChain::targets_`, `steps_`, `gains_db_` | RT-dışı (UI/komut) `set_gain` | RT (ramp başlatırken) | `pending_` publish: `fetch_add(release)` → RT `load(acquire)` | Sözleşme bozulur → tearing |
| `EqChain::active_mask_` | RT-dışı `set_active_bands` | RT `process` acquire | **maske publish'i `pending_` bump'ından ÖNCE** (release-acquire yayılımı — kod yorumu ADR-106 kural 4-5) | Sıra tersize bayrak/veri yarışı |
| `EqChain::biquads_` katsayı durumu | RT (`process` ramp) | yalnız RT | tek yazıcı | RT-dışı dokunma → yarış |
| `SpscRing` write indeksi | üretici (decode) | tüketici acquire | `alignas(64)` satır | MPSC'ye genişletme yeni tasarım |
| `SpscRing` read indeksi | tüketici (RT) | üretici acquire | `alignas(64)` satır | aynı |
| `SpscRing` buffer elemanları | üretici (yazmadan önce) | tüketici (okuduktan sonra) | indeks zinciri (yayılım) | eleman kendisi atomik değil — ABA yok, boyut sabit |
| Metering/olay kuyruğu (RT→UI) | RT `push` (false → drop) | UI/looper `pop` | drop-policy + sayaç | Bloklanma eklenirse R2 ihlali |
| `DeviceList` (16×64 char) | RT-dışı `enumerate` | RT-dışı (UI) | sabit boyut, **tahsis yok** | Dinamik `vector<string>` yasak (R1, RT-dışı bile bu yapıda) |

## §4 Backend Sonuç & Fallback Sözleşmesi (OS'tan bağımsız)

```cpp
// iaudio_backend.h — tüm adapter'lar aynı enum (ADR-019 kural 4)
enum class BackendResult : std::uint32_t {
  Ok = 0, DeviceLost, RateUnsupported, Busy, Permission, Fatal
};
enum class StreamMode : std::uint32_t { Exclusive, Shared };
```

| Sonuç | Anlamı | Önerilen fallback (vault zinciri: ASIO → WASAPI exclusive → shared) | Son durak |
|-------|--------|------------------------------------|-----------|
| `Ok` | Akış açık | — | — |
| `DeviceLost` | Cihaz kaybı/hot-swap | enumerate yenile → open retry | NULL backend (sessiz) + UI bildirim |
| `RateUnsupported` | SR müzakeresi başarısız (örn. cihaz 48k kabul etmiyor) | `setDeviceRate` alternatif → `StreamMode::Shared` | NULL backend |
| `Busy` | Exclusive kilit — **başka uygulama** cihazda (ASIO exclusive çoklu deneme çökme riski: brain.md #910) | `switchMode(Shared)` | kullanıcıya bildir (kilit kimde görünmez — tahmin yasak) |
| `Permission` | Erişim izni yok (Windows cihaz izni) | kullanıcı akışı → tekrar dene | kilitli UI + talimat |
| `Fatal` | Kurtarılamaz (sürücü çöktü/bilinmeyen) | NULL backend + rapor | DUR, log yaz (RT-dışı) |

**Zorunluluklar:**
1. Fallback zinciri **kodda** tablo/switch olarak tek yerde toplanır (adapter başına tekrarlanmaz).
2. Geçiş sırasında RT callback'i susturulur → `stop()` → yeniden `start()`; geçiş sırasında
   kesinti UI'da görünür olmalı (sessiz kayıp yasak).
3. `Exclusive` denemesi **ikinci süreçte** yapılmaz (tek uygulama kilidi).
4. Yeni sonucu enum'a eklemek → fallback tablosu + test birlikte (eksik satır = sıkışmış ses).

## §5 DSP Sınırı Sözleşmesi (EqChain — 31-band, disk)

| Kalem | Değer (disk kanıtı) | Not |
|-------|---------------------|-----|
| Bant × kanal | `kEqBands=31` × `kEqChannels=2` → **62 biquad** (kural: cascade 124 **varsayılan değil**, gerekçe yazılmadan kullanılmaz — kod yorumu ADR-0105 §2.2) | Yeni bant ekleme → §2.2 gerekçesi |
| Frekanslar | ISO 1/3-oktav sabit dizi: 20 … 20000 Hz (`kEqFrequencies`) | Uydurma frekans ekleme/yok sayma yasak |
| Katsayı/durum | `double` katsayı + `double` state (ADR-0105 §2.3) | `float` katsayıya geçiş = bilinçli karar + ölçüm |
| Bant kapatma | Bant sayısı değişimi **bypass maskesi** = yeni zincir tahsisi/takası YOK (ADR-106 §2.2b kural 4-5) | "Bant azaltınca `vector` küçült" → R1 ihlali |
| Ramp | 5–20 ms (tıklama yasağı); `ramp_ms → blok adımı` hesabı `block=256` varsayımıyla (kod TODO'su: benchmark — §5.6) | ⚠️ 256 varsayımı gerçek blok boyunca doğrulanmalı (R6) |
| RT girişi | `process(float* L, float* R, size_t n) noexcept` — tahsisat/lock/log yok | planar stereo; 8.1 genişlemesi matris aşamasında |

## §6 Sözleşme Değişiklik Prosedürü

1. İlgili ADR okunur (frozen metin: okunur, **değiştirilmez**; yeni karar → yeni ADR taslağı).
2. Değişen imza/sahiplik SKILL.md §7 zincirine ve bu dosyanın tablolarına yansıtılır.
3. `examples/` içindeki çalışan örnek yeni imza ile güncellenir (eski örnek kalıntı bırakmaz).
4. ctest + §6.2 denetimi (SKILL.md) çalıştırılır; sonuç rapora yazılır.
5. `BackendResult`/fallback değiştiyse §4 tablosu + test birlikte (tek başına enum ekleme yasak).

*CoreMusic Skill v3.0 — references/contracts.md — Updated: 2026-10-21*
