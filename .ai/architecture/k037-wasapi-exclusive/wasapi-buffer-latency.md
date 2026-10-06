---
title: "K037.4 — WASAPI Buffer & Latency: Endpoint Buffer, Period Negotiation, Event-driven vs Pull"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K037.4 — WASAPI Buffer & Latency

**Bağlantılar:** [[index]] · [[wasapi-exclusive-shared]] · [[wasapi-exclusive-mode]] · [[wasapi-shared-mode]] · [[wasapi-format-negotiation]] · [[wasapi-hata-kodlari]] · [[../k036-asio-drivers/index]]

---

## §1 Genel Bakış

WASAPI'de gecikmenin büyük bölümü **buffer/period kararından** doğar. Bu doküman üç katmanı ayrı ayrı ele alır:

| Katman | Soru | Bölüm |
|--------|------|-------|
| Uygulama buffer'ı | Ne kadar veri biriktirilir, ne zaman yazılır? | §4 |
| Endpoint / engine period | Cihaz kaç örnekte bir veri ister? | §5, §6 |
| Zamanlama modeli | Callback event ile mi, yoksa periyodik okumayla (pull) mı gelir? | §7 |

Kaynak dokümanın hesap formülü:

```
BufferDuration = (BufferSize / SampleRate) * 1,000,000 (μs)

Örnek:
Buffer: 288 samples @ 48kHz
Duration: (288 / 48000) * 1,000,000 = 6000μs = 6ms
```

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L79–L86.

---

## §2 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Buffer süresi hesabı ve period seçimi | Ring buffer veri yapısı → `../k042-buffer-management` (dış modül, diskte yok → düz metin) |
| Event-driven vs pull (polling) zamanlama | Genel latency zinciri bütçesi → `../k043-latency-optimization` (dış modül) |
| Endpoint buffer davranışı (exclusive/shared) | DMA/FIFO donanım yolu → `../k048-interrupt-dma-flow` (dış modül) |
| `IAudioClient3` shared period seçimi (official docs) | ASIO buffer switch → `../k036-asio-drivers` |
| Xrun (underrun/overrun) koruması | RT thread kuyruk → `../k053-threading-lockfree` (dış modül) |

> Not: Bu dosyada diskte olmayan klasörlere **wiki-link kurulmaz**; yalnızca düz metin modül adı olarak geçer (index.md'deki mevcut wiki-linkler korunmuştur).

---

## §3 Buffer Katmanları

### §3.1 Exclusive ve Shared buffer zinciri

```
EXCLUSIVE:
┌──────────────────────────────────────────────┐
│  App Buffer ──▶ Driver Buffer ──▶ HW         │
│  (definite)      (definite)                  │
└──────────────────────────────────────────────┘

SHARED:
┌──────────────────────────────────────────────┐
│  App Buffer ──▶ Audio Engine ──▶ HW          │
│  (definite)      (indefinite)                │
└──────────────────────────────────────────────┘
```

Kanıt: `wasapi-exclusive.md` L65–L77.

### §3.2 Katmanların sahibi ve kontrol edilebilirliği

| Katman | Sahibi | Uygulama kontrolü | Ölçülebilirliği |
|--------|--------|-------------------|-----------------|
| App buffer (yazma alanı) | Uygulama | Tam | Tam (kod içi sayaç) |
| Padding (dolu kısım) | Sürücü/OS | `GetCurrentPadding` ile okunur (official docs) | Okunur |
| Engine period (shared) | Windows | `IAudioClient3` ile daraltılabilir | Okunur/yazılır (seçim) |
| Endpoint buffer (HW) | Sürücü/donanım | Salt okunur | Tahmin / official docs |
| DMA/FIFO | Donanım | Yok | Dış modül kapsamında |

### §3.3 Buffer süre hesabı (kaynak formül)

| Girdi | Örnek | Çıktı |
|-------|-------|-------|
| BufferSize = 288 örnek | @48 kHz | 6000 µs = 6 ms |

Kanıt: `wasapi-exclusive.md` L79–L86.

K2 genel latency tablosu (buffer → tek yön ms, 48 kHz):

| Buffer | Tek yön | Hesap |
|--------|---------|-------|
| 512 örnek | 10.67 ms | `k2-surucu/CLAUDE.md` L39 |
| 256 örnek | 5.33 ms | L40 |
| 128 örnek | 2.67 ms | L41 |
| Çift yön | ≈ 2 × tek yön | L42 |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md` L36–L43.

---

## §4 Uygulama Buffer Boyutu Seçimi

### §4.1 Seçim girdileri

| # | Girdi | Kaynak |
|---|-------|--------|
| 1 | Hedef round-trip latency | `wasapi-exclusive.md` L181 (3 ms / 30 ms) |
| 2 | Örnekleme hızı | Cihaz yeteneği |
| 3 | Kanal sayısı | Format kararı |
| 4 | Cihazın desteklediği minimum/varsayılan period | official docs (`GetDevicePeriod`) |
| 5 | CPU toleransı | `wasapi-exclusive.md` L182 (CPU %0.5 / %2) |

### §4.2 Karar matrisi

| Hedef round-trip | Mod | Buffer bandı | Dayanak |
|------------------|-----|--------------|---------|
| ≤3 ms | Exclusive | Küçük (3–6 ms buffer bandı, donanıma bağlı) | L117 |
| ~30 ms | Shared | Orta | L181 |
| Kararlılık önceliği | Her ikisi | Büyüt → xrun azalır, latency artar | guardrail #3 |

K2 hesap örneği: 128 örnek @48 kHz = 2.67 ms tek yön → çift yön ≈5.3 ms; bu, **3 ms round-trip hedefiyle gerilim yaratır** → exclusive'te buffer seçimi k2 tablosundan daha küçük bir banda oturmalıdır (kaynakta bu bandın **sayısal değeri yok** → `⚠️ VERIFICATION REQUIRED`).

### §4.3 Xrun dengesi

| Yön | Belirti | Neden | Müdahale |
|-----|---------|-------|----------|
| Underflow (underrun) | Crackling/tıslama | RT thread geç kaldı / buffer küçük | Buffer büyüt, RT öncelik |
| Overrun | Kopma/distortion | Yazma okumadan hızlı | Kuyruk disiplini |

Guardrail: "Buffer underrun koruması zorunlu → Crackling" · Kanıt: `k2-surucu/CLAUDE.md` L18–L24.

> Buffer artırımının latency'ye maliyeti: `Δlatency = ΔBuffer / SampleRate`. Bu bağıntı formülün kendisinden türetilmiştir (`wasapi-exclusive.md` L81).

---

## §5 Endpoint Buffer

### §5.1 Tanım ve konum

Endpoint buffer, **Windows ses motoru ile donanım arasındaki** tamponuktur; exclusive modda sürücü buffer'ının karşılığı, shared modda engine'in donanıma yazdığı kuyruktur.

| Özellik | Exclusive | Shared |
|---------|-----------|--------|
| Konum | Driver buffer → HW | Audio Engine → HW |
| Karakter (kaynak) | definite | indefinite |
| Uygulamanın yazabildiği yer | Doğrudan driver buffer | Yalnız app buffer |
| Ölçüm | `GetBufferSize` / `GetStreamLatency` (official docs) | Karışık — engine period üzerinden |

Kanıt: `wasapi-exclusive.md` L65–L77 (definite/indefinite diyagramı).

### §5.2 Endpoint buffer kuralları

| # | Kural | Neden |
|---|-------|-------|
| 1 | Endpoint buffer boyutu cihaz tarafından belirlenir; uygulama **yazamaz** | Sürücü sahipliği |
| 2 | Exclusive açılışta buffer süresi isteği cihaz yeteneğine kırpılabilir | `0x88890018` riski |
| 3 | Endpoint buffer + app buffer toplamı round-trip'in parçasıdır | Ölçüm bütçesi |
| 4 | Bit-perfect endpoint'te bozulmaz (dönüşüm yok) | §6'daki mix farkı |

### §5.3 Süre ↔ örnek dönüştürme tablosu (48 kHz)

| Süre | Örnek (48 kHz) | Kaynak |
|------|----------------|--------|
| 6 ms | 288 | `wasapi-exclusive.md` L83–L85 |
| 10.67 ms | 512 | `k2-surucu/CLAUDE.md` L39 |
| 5.33 ms | 256 | L40 |
| 2.67 ms | 128 | L41 |

> Formül `wasapi-exclusive.md` L81'den türetilmiştir; örnekler doğrudan kaynak satırlarıdır.

---

## §6 Period Negotiation (buffer boyutu / latency pazarlığı)

### §6.1 Negotiation akışı (official docs temelli)

```
Uygulama hedefi: hedef latency
        │
        ▼
IAudioClient::GetDevicePeriod
   ├─ varsayılan period (pbDefaultDevicePeriod)
   └─ minimum period (pbMinimumDevicePeriod)
        │
        ▼
Uygulama süresi (REFERENCE_TIME, 100 ns birimi) belirler
        │
        ├─ exclusive: cihaz yeteneğine göre kırpılabilir/reddedilebilir
        │             └─ red → 0x88890018 → yeniden seç [[wasapi-hata-kodlari]]
        │
        └─ shared (IAudioClient3, Windows 10+):
              GetSharedModeEnginePeriod ile mevcut/desteklenen
              dönemler okunur, ardından yeniden Initialize
        │
        ▼
GetBufferSize (örnek cinsinden) ──▶ süre = (örnek / SR) × 1,000,000 µs  (L81)
```

> `GetDevicePeriod`, `REFERENCE_TIME`, `IAudioClient3::GetSharedModeEnginePeriod` ve `GetBufferSizeRange` **official Microsoft WASAPI dokümanı** bilgisidir; repo'da karşılığı yoktur → uygulama kodu `⚠️ VERIFICATION REQUIRED`.

### §6.2 Negotiation tablosu

| Mod | Kim belirler? | Uygulama ne yapabilir? | Hata |
|-----|---------------|------------------------|------|
| Exclusive | Cihaz + uygulama talebi | Süre talep et, minimum period'a yaklaş | `0x88890018` |
| Shared (klasik) | Windows | Süre talep et; OS uygunlaştırır | Redd'i nadir |
| Shared (IAudioClient3) | Uygulama, desteklenen kümelerden seçer | En küçük desteklenen dönemi seç → latency düşer | Uyumsuz kombinasyon → Initialize red |

### §6.3 `IAudioClient3` (shared düşük gecikme)

| Konu | official docs notu |
|----------------------|---------------------|
| Amaç | Shared modda engine period'unun uygulama tarafından seçilebilmesi |
| Ek methodlar | `GetSharedModeEnginePeriod` · `GetBufferSizeRange` |
| Kullanım | Mevcut/desteklenen dönem listesi okunur → tercih edilen dönemle yeniden `Initialize` |
| K037 kapsamı | Buffer/latency müzakeresinin parçası (bu dosya §6.1) |
| Repo kanıtı | 🔴 Yok → `⚠️ VERIFICATION REQUIRED` |

### §6.4 Period müzakeresi karar matrisi

| Senaryo | Strateji | Beklenen sonuç |
|---------|----------|----------------|
| Exclusive, min latency | Minimum period'a yakın süre + event-driven | 3 ms bandı (kaynak L181) |
| Exclusive, kararlılık | 3–6 ms bandının üstü | Xrun yok, latency artar (L117) |
| Shared, standart | Varsayılan period | 15 ms (L180) |
| Shared, düşük gecikme | `IAudioClient3` ile en küçük desteklenen dönem | 15 ms altı ⚠️ ölçüm gerekli |
| Cihaz minimum period'u hedefin altındaysa | Hedefi minimuma eşitle | Gerçekçi hedef |

---

## §7 Event-driven vs Pull (Polling) Modu

### §7.1 İki model

| Boyut | Event-driven | Pull (polling/timer) |
|-------|--------------|----------------------|
| Tetik | `SetEventHandle` ile cihaz olayı (official docs; kaynak: L118 "SetEventHandle ile kesme zamanlaması") | Uygulama periyodik sorgular (`GetCurrentPadding`, official docs) |
| Uyanma maliyeti | Düşük — olayla uyanır | Daha yüksek — uyku/timer hatası |
| RT güvenliği | Olay thread'i kısa iş yapmalı | Döngü aralığı küçük tutulmalı |
| Kaynakta geçişi | ✅ `wasapi-exclusive.md` L118 | ❌ Kaynakta geçmiyor (official docs bilgisi) |
| Kullanım | Exclusive düşük gecikme | Basit/shared senaryolar |

### §7.2 Event-driven akış

```
Initialize (+ event-driven bayrağı)
   │
SetEventHandle(handle)
   │
Start()
   │
   ▼
┌─────────────────────────────────────────────┐
│ RT thread döngüsü:                          │
│  WaitForSingleObject(handle, timeout)       │
│    │ olay geldi                             │
│    ▼                                        │
│  GetCurrentPadding → boş alan               │
│    │                                        │
│    ├─ render: GetBuffer → doldur → Release  │
│    └─ capture: GetBuffer → oku → Release    │
│    │                                        │
│  xrun sayacı güncelle                       │
└─────────────────────────────────────────────┘
```

> `WaitForSingleObject`, `GetCurrentPadding` official Windows/WASAPI API'leridir; bloke edici bekleme **RT thread içinde** yalnız bu olay beklemesidir — guardrail #2 (audio thread blocking yasak) kapsamı dışında mı, **teyit gerekir** → `⚠️ VERIFICATION REQUIRED` (kural yorumu mimari karar gerektirir).

### §7.3 Pull model akış

```
RT/UI thread döngüsü:
  period aralığında:
    padding = GetCurrentPadding()
    boşluk = bufferSize - padding
    boşluk ≥ eşik? ──evet──▶ GetBuffer + yaz + Release
        │hayır
        ▼
    bekle (kısa uyku) ── tekrar
```

### §7.4 Model seçim tablosu

| Kriter | Tercih | Dayanak |
|--------|--------|---------|
| Round-trip ≤3 ms hedefi | Event-driven | `wasapi-exclusive.md` L117–L118 |
| Basit senaryo / paylaşımlı | Pull | Yeterli doğruluk, daha az RT riski |
| Jitter hassasiyeti | Event-driven | Periyodik wake-up jitter üretir |
| Uygulama karmaşıklığı | Pull | Daha az handle yönetimi |
| CPU (boşta %0.5 hedefi) | Event-driven | Gereksiz wake-up yok (L182) |

---

## §8 Latency Bütçesi ile Bağlantı

### §8.1 Round-trip bileşenleri (WASAPI)

```
Round-trip = input latency + output latency

Kaynak değerler (L179–L181):
  Exclusive: 1.5 + 1.5 = 3 ms
  Shared:   15 + 15 = 30 ms
```

### §8.2 Buffer → latency bağıntısı (k2 hesabı)

```
tek yön (ms) = örnek / SR × 1000
512 @48k = 10.67 ms · 256 = 5.33 ms · 128 = 2.67 ms
çift yön ≈ 2 × tek yön
```

Kanıt: `k2-surucu/CLAUDE.md` L36–L43.

### §8.3 Gerilim tablosu (bilinen çelişki)

| Hedef | K2 hesabı | Durum |
|-------|-----------|-------|
| Exclusive round-trip ≤3 ms | 128 örnek tek yön 2.67 ms → çift yön 5.33 ms | ⚠️ Çelişki: 128 örnek buffer tek başına bütçeyi aşar → exclusive buffer'ın 128'den küçük olması gerekir; **sayısal band kaynakta yok** |

> Bu satır, iki kaynaklı (L181 vs CLAUDE L41) gerilimi işaretler; çözümü için ölçüm kapısı gerekir → index.md §14.

---

## §9 Kenar Durumlar

| # | Kenar durum | Davranış |
|---|------------|----------|
| 1 | İstenen süre cihaz yeteneği dışında | `0x88890018` → yeniden müzakere |
| 2 | Buffer süresi 0 veya negatif | Initialize reddi → geçerli süre |
| 3 | SR değişirse süre sabit kalır, örnek sayısı değişir | §5.3 tablosu yeniden hesaplanır |
| 4 | Event handle kurulmadan event-driven başlatma | Resmi hata (official docs) → `⚠️ VERIFICATION REQUIRED` (kod yok) |
| 5 | RT thread geç kaldı | Underrun → guardrail #3 |
| 6 | Cihaz periyodu değişir (sürücü güncelleme) | Yeniden negotiation |
| 7 | Shared'da engine period uygulama isteğine uymaz | En yakın desteklenen dönem seçilir (IAudioClient3) |

---

## §10 Test Matrisi (BL-serisi)

| ID | Test | Beklenen | Durum |
|----|------|----------|-------|
| BL-W01 | Buffer hesabı (288@48k) | 6 ms | Hesap kaynaklı ✓ |
| BL-W02 | 128/256/512 örnek süreleri | 2.67/5.33/10.67 ms | k2 tablosu ✓ |
| BL-W03 | Exclusive min period | 3 ms bandı ölçümü | ⚠️ ölçüm yok |
| BL-W04 | `0x88890018` tetikleme | Red → yeniden seçim | ⚠️ kod yok |
| BL-W05 | Event-driven jitter | ≤ period bütçesi | ⚠️ ölçüm yok |
| BL-W06 | Pull mod stabilite | Xrun = 0 | ⚠️ ölçüm yok |
| BL-W07 | IAudioClient3 dönem seçimi | Küçük dönem → düşük latency | ⚠️ API kodu yok |
| BL-W08 | Underrun kurtarma | Sayaç artar, ses kurtulur | ⚠️ kod yok |

---

## §11 Bağımlılıklar

| Bağımlılık | Yön | Not |
|-----------|-----|-----|
| Windows SDK / WASAPI | Alt | official docs arayüzleri |
| K1 Windows Core | Alt | event/timer/thread servisleri |
| K2 buffer yönetimi | Alt | Uygulama içi tampon |
| K043 latency (dış modül) | Üst | Bütçe ve ölçüm |
| K036 ASIO | Yatay | Buffer switch karşılaştırması |

---

## §12 Kanıt Satırları

| # | İddia | Kaynak | Satır |
|---|-------|--------|-------|
| 1 | Buffer formülü ve 288@48k = 6 ms örneği | `_backup/.../k2-surucu/wasapi-exclusive.md` | L79–L86 |
| 2 | Exclusive/Shared buffer diyagramı (definite/indefinite) | aynı dosya | L61–L77 |
| 3 | Buffer süresi 3–6 ms bandı | aynı dosya | L117 |
| 4 | `SetEventHandle` ile kesme zamanlaması | aynı dosya | L118 |
| 5 | Thread önceliği `THREAD_PRIORITY_TIME_CRITICAL` | aynı dosya | L119 |
| 6 | CPU affinity | aynı dosya | L120 |
| 7 | Latency hesabı 512/256/128 örnek | `_backup/.../k2-surucu/CLAUDE.md` | L36–L43 |
| 8 | Round-trip 3/30 ms | `wasapi-exclusive.md` | L177–L183 |
| 9 | Underrun guardrail | `k2-surucu/CLAUDE.md` | L18–L24 |
| 10 | `0x88890018` BUFFER_SIZE_ERROR | `wasapi-exclusive.md` | L124–L130 |

> §6–§7'deki **arayüz adları ve davranışlar** official Microsoft WASAPI dokümanıdır — repo kanıtı değildir; hex/değer üretilmemiştir.

---

## §13 Wiki-Bağlantılar

| Hedef | Bağlantı | İlişki |
|-------|----------|--------|
| Hub | [[index]] | K037 indeksi |
| Mod rehberi | [[wasapi-exclusive-shared]] | §4 buffer özeti |
| Exclusive | [[wasapi-exclusive-mode]] | Süre seçimi akışı |
| Shared | [[wasapi-shared-mode]] | Engine period |
| Format | [[wasapi-format-negotiation]] | SR ↔ örnek hesabı |
| Hata kodları | [[wasapi-hata-kodlari]] | `0x88890018` |
| ASIO indeksi | [[../k036-asio-drivers/index]] | Buffer switch kıyası |

---

## §14 Açık Konular

| # | Konu | Durum |
|---|------|-------|
| 1 | Exclusive için minimum buffer bandının sayısı | ⚠️ VERIFICATION REQUIRED |
| 2 | 128 örnek ↔ 3 ms geriliminin ölçümle çözülmesi | ⚠️ VERIFICATION REQUIRED |
| 3 | `IAudioClient3` destekli cihaz/kullanım | ⚠️ VERIFICATION REQUIRED |
| 4 | Event-driven beklemenin guardrail #2 yorumu | ⚠️ VERIFICATION REQUIRED |
| 5 | Endpoint buffer gerçek boyutu (cihaz bazlı) | ⚠️ VERIFICATION REQUIRED |
| 6 | Kod implementasyonu | ⚠️ VERIFICATION REQUIRED |

---

## §15 Kontrol Listesi

- [x] Frontmatter 7 alan · `version: 4.0.0` · `updated: 2026-10-06`
- [x] Diskte olmayan klasörlere wiki-link **eklenmedi**
- [x] Formül ve örnek değerler kaynak satırlı
- [x] official docs bilgisi işaretli, hex değer üretilmedi
- [x] Doğrulanamayan `⚠️ VERIFICATION REQUIRED`
- [x] `git commit` atılmadı

---

## §16 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — Buffer & Latency konu dosyası (multi-md yapılandırması) | Vault Documentation Specialist |
