---
title: "K036.3 — ASIO Latency Hesabı (Formüller, Buffer → ms, Round-trip)"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K036.3 — ASIO Latency Hesabı

**Bağlantılar:** [[index]] · [[asio-buffer-callback]] · [[asio-exclusive-mode]] · [[asio-device-lifecycle]] · [[../k037-wasapi-exclusive/index]]

---

## §1 Genel Bakış

Bu doküman ASIO yolundaki **gecikmenin hesabını** tanımlar: hangi formül kullanılır, buffer boyutu nasıl ms'e çevrilir, round-trip nasıl ölçülür ve hangi hedefler bağlayıcıdır. Üç soru:

1. **Formül:** Tek yön ve çift yön (round-trip) gecikme hangi bileşenlerden oluşur?
2. **Dönüşüm:** Buffer boyutu (örnek) + SR → ms hesabı nasıl yapılır, sık kullanılan değerler nelerdir?
3. **Ölçüm:** Hangi protokolle ölçülür, hangi hedeflerle karşılaştırılır?

> **Kod/ölçüm durumu:** Repo'da ASIO kodu ve donanım ölçümü **yoktur** (kanıt: [[index]] §1.2, §11). Bu dosyadaki **hedef/gerçek değerler kaynaktan birebir** taşınmıştır; yeni sayı üretilmemiştir (Zero-Hallucination).

---

## §2 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| ASIO tek yön / round-trip formülü | Genel latency zinciri bütçesi → `../k043-latency-optimization/index` *(diskte yok — planlı D01 modülü)* |
| Buffer boyutu → ms dönüşüm tabloları | Buffer boyutu **seçim** politikası → aynı modül *(diskte yok)* |
| Ölçüm protokolü (K036 düzeyi) | Ölçüm araçlarının implementasyonu → ⚠️ VERIFICATION REQUIRED |
| K036'nın kendi hedef tabloları | WASAPI gecikme karakteri → [[../k037-wasapi-exclusive/index]] |

---

## §3 Formül Seti

### §3.1 Temel dönüşüm

```
T_buffer = N / SR          (saniye)
T_ms     = (N / SR) × 1000
```

| Sembol | Anlam | Birim |
|--------|-------|-------|
| N | Buffer boyutu (örnek sayısı) | örnek |
| SR | Örnekleme hızı | Hz |
| T_buffer | Tek yönlü buffer süresi | ms |

### §3.2 Tek yön formülü (kaynak)

Kaynak birebir:

```
Total Latency = Input Buffer + Processing + Output Buffer + Driver Overhead
```

Kanıt: `k2-surucu/asio-drivers.md L93`.

### §3.3 Kaynak örnek (96kHz, 64 sample)

```
Örnek (96kHz, 64 sample):
Input:    64/96000 = 0.667ms
Process:  ~0.1ms (K3 DSP)
Output:   64/96000 = 0.667ms
Driver:   ~0.05ms
─────────────────────────────
Total:    ~1.48ms (one-way)
RTT:      ~2.96ms (round-trip)
```

Kanıt: `k2-surucu/asio-drivers.md L95–L103`.

| Bileşen | Değer (kaynak) | Not |
|---------|----------------|-----|
| Input buffer | 0.667ms | 64/96000 |
| Processing | ~0.1ms | K3 DSP |
| Output buffer | 0.667ms | 64/96000 |
| Driver overhead | ~0.05ms | sürücü payı |
| **Toplam (one-way)** | **~1.48ms** | kaynak sonucu |
| **RTT** | **~2.96ms** | 2 × one-way |

### §3.4 Round-trip kuralı (48k tablosu)

Kaynak: `Çift yön = ~2x tek yön gecikme` — kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md L42`.

### §3.5 Dikkat: iki ayrı hedef tablosu

Vault'ta **birbiriyle çelişkisi olmayan ama farklı ölçüm koşullarına ait** iki tablo vardır:

| Tablo | Koşul | Round-trip | Kanıt |
|-------|-------|-----------|-------|
| Hesap örneği (§3.3) | 96kHz · 64 sample · bileşen toplamı | ~2.96ms (RTT) | `asio-drivers.md L102–L103` |
| Performans hedefi (§7.1) | Ölçülmüş hedef/gerçek satırları | **1.34ms hedef / 1.33ms gerçek** | `asio-drivers.md L160` |

> ⚠️ VERIFICATION REQUIRED: İki değerin ölçüm koşulu (buffer boyutu, SR, donanım) kaynakta eşleştirilmemiştir; bu dosya **ikisini de olduğu gibi taşır**, birleştirmez ve yeni ara değer üretmez.

---

## §4 Buffer Boyutu → ms Dönüşüm Tabloları

### §4.1 96kHz tablosu (kaynak satırları + hesaplanan hücreler)

| Buffer | Hesap | Süre (ms) | Etiket | Kanıt |
|--------|-------|-----------|--------|-------|
| 32 | 32/96000 | **0.33** | minimum | `asio-drivers.md L50` (kaynakta "32 sample @ 96kHz = 0.33ms") |
| 64 | 64/96000 | **0.67** | dengeli | `asio-drivers.md L51` |
| 128 | 128/96000 | **1.33** | güvenli | `asio-drivers.md L52` |

### §4.2 48kHz tablosu (kaynak satırları birebir)

| Buffer | Süre (ms) — tek yön | Kanıt |
|--------|---------------------|-------|
| 128 | **2.67** | `_backup/.../k2-surucu/CLAUDE.md L41` |
| 256 | **5.33** | aynı dosya L40 |
| 512 | **10.67** | aynı dosya L39 |

Çift yön için ×2 uygulanır (§3.4).

### §4.3 Proje varsayılanı

| Alan | Değer | Kanıt |
|------|-------|-------|
| Varsayılan buffer | 512 sample | `.ai/brain.md L295` |
| Aralık | 64–1024 | aynı kaynak |
| SR | 48kHz | `.ai/brain.md L295` + `.ai/CLAUDE.md L427` ("48kHz standart") |
| Veri tipi | 32-bit float | `.ai/brain.md L295` |
| Süre | ~10.67ms | `.ai/brain.md L295` |

`.ai/brain.md L295` birebir: `ASIO Buffer: 512 sample varsayılan (64-1024), 48kHz, 32-bit float, ~10.67ms gecikme.`

### §4.4 Hızlı referans matrisi (SR × N → ms)

> Bu matris §3.1 formülünden **hesaplanmıştır** (kaynaktan alınmış değer değil; türetilmiş). Hücreler yuvarlanmamıştır.

| N \ SR | 44.1kHz | 48kHz | 88.2kHz | 96kHz | 192kHz |
|--------|---------|-------|---------|-------|--------|
| 32 | 0.726 | 0.667 | 0.363 | 0.333 | 0.167 |
| 64 | 1.451 | 1.333 | 0.726 | 0.667 | 0.333 |
| 128 | 2.902 | 2.667 | 1.451 | 1.333 | 0.667 |
| 256 | 5.805 | 5.333 | 2.902 | 2.667 | 1.333 |
| 512 | 11.610 | 10.667 | 5.805 | 5.333 | 2.667 |
| 1024 | 23.220 | 21.333 | 11.610 | 10.667 | 5.333 |

| Hücre | Durum |
|-------|-------|
| 96kHz 32/64/128 | ✅ kaynakla eşleşir (§4.1) |
| 48kHz 128/256/512 | ✅ kaynakla eşleşir (§4.2, 2.667≈2.67 vb.) |
| Diğer hücreler | Türetme (formül §3.1) — ölçüm değildir |

---

## §5 Gecikme Bileşen Analizi

### §5.1 Bileşen listesi ve sahipliği

| # | Bileşen | Kaynak içindeki değeri | Sahip | Kanıt |
|---|---------|------------------------|-------|-------|
| 1 | Input buffer | N/SR | Uygulama + sürücü | `asio-drivers.md L96` |
| 2 | Processing (K3 DSP) | ~0.1ms | K3 ses motoru | `asio-drivers.md L97` |
| 3 | Output buffer | N/SR | Uygulama + sürücü | `asio-drivers.md L98` |
| 4 | Driver overhead | ~0.05ms | Sürücü yığını | `asio-drivers.md L99` |
| 5 | Donanım (ADC/DAC + FIFO) | Kaynakta sayısal yok | K0/K1 | ⚠️ VERIFICATION REQUIRED |

### §5.2 Gecikme karakteri (vault doğrulaması)

`.ai/ecosystem/asio-wasapi-rehber.md §3.4` (exa 2026-09-24 doğrulaması):

| Gecikme Bileşeni | ASIO | WASAPI Exclusive |
|------------------|------|------------------|
| Buffer boyutu ayarı | Uygulama seçer (ör. 64/128/256 sample) | Uygulama + ses motoru davranışı |
| Sistem mix'i | Yok | Yok (exclusive) / Var (shared) |
| Öngörülebilirlik | **Yüksek** (donanım zamanlaması) | Orta-yüksek |
| Tipik zincir (CoreMusic hedefi) | buffer + ADC/DAC + sürücü yığını | OS yığını + exclusive payı |

**Ders (rehber §3.4):** "En düşük gecikme" tek sayı değil — **buffer seçimi + sürücü yolu + xrun toleransı** birlikte sözleşmedir.

### §5.3 Bileşen → modül eşlemesi

| Bileşen | Belgeleyen dosya |
|---------|------------------|
| Buffer süresi, xrun | [[asio-buffer-callback]] |
| Buffer sürüşü / SR eşleşmesi | [[asio-device-lifecycle]] |
| Blocking/RT kaynaklı gecikme payı | [[asio-thread-model]] |
| Sürücü yolu ve overhead | [[asio-sdk-entegrasyon]] |
| Hata durumunda ölçüm kesintisi | [[asio-hata-yonetimi]] |
| Exclusive/shared farkı | [[../k037-wasapi-exclusive/index]] |

---

## §6 Buffer Boyutu ve Gecikme İlişkisi (karar ağacı)

```text
Gecikme hedefi ↓  /  Kararlılık ↑

  Düşük gecikme (<2ms tek yön) ──▶ 32-64 örnek @96kHz  (§4.1: 0.33-0.67ms)
       │                              │ risk: xrun duyarlılığı yüksek
       │                              ▼
  Dengeli (2-6ms) ─────────────▶ 64-128 @96kHz veya 128-256 @48kHz
       │
  Güvenli / proje varsayılanı ──▶ 512 @48kHz = 10.67ms (§4.3)
                                    │
                                    ▼
                            xrun artarsa → §6.1

§6.1 Müdahale sırası (bağlayıcı değil, tasarım sırası):
  1) buffer boyutunu artır   → [[asio-buffer-callback]] §7.3
  2) blocking denetimi       → [[asio-thread-model]] §5
  3) sayfa kilidini doğrula  → [[asio-buffer-callback]] §5.3
  4) ölçüm protokolüne dön   → §7.3
```

> **Uyarı:** Küçük buffer → düşük gecikme ama xrun riski ↑. Seçim bu iki eksenin dengesidir; tek başına "en küçük" hedeflenmez (rehber §3.4 dersi).

---

## §7 Hedef ve Ölçüm

### §7.1 Bağlayıcı hedef tablosu (kaynak birebir)

| Metrik | Hedef | Gerçek | Kanıt |
|--------|-------|--------|-------|
| Input Latency | 0.67ms | 0.65ms | `asio-drivers.md L158` |
| Output Latency | 0.67ms | 0.68ms | `asio-drivers.md L159` |
| Round-trip Latency | 1.34ms | 1.33ms | `asio-drivers.md L160` |
| CPU Kullanımı (boşta) | <%1 | %0.3 | `asio-drivers.md L161` |
| Maksimum Kanal | 64×64 | 64×64 | `asio-drivers.md L162` |
| Buffer Değişim Süresi | <10µs | 8µs | `asio-drivers.md L163` |

Aynı tablo [[index]] §4.1'de de taşınır (SSOT çakışması yok — birebir aynı kaynak).

### §7.2 İlk hedef (tasarım metni)

Kaynak: "COREMUSIC, ASIO Exclusive mode ile **0.5ms round-trip latency** hedefler." — kanıt: `asio-drivers.md L12`.

> ⚠️ VERIFICATION REQUIRED: Bu 0.5ms hedefi, §7.1 tablosundaki 1.34ms hedefiyle **kaynak içinde farklı satırlardadır**; hangisinin güncel bağlayıcı hedef olduğu belirtilmemiş. Bu dosya ikisini de taşır.

### §7.3 Ölçüm protokolü (K036 düzeyi)

| Adım | İşlem | Kayıt |
|------|-------|-------|
| 1 | Sistem durulması (CPU <%5) | T0 CPU |
| 2 | SR/bit/kanal sabitle | Konfigürasyon |
| 3 | Buffer boyutu yaz (32/64/128/256) | Boyut |
| 4 | Round-trip ölçümü ×3 | Medyan |
| 5 | Jitter ölçümü | µs |
| 6 | xrun sayacı oku | 0 olmalı |
| 7 | CPU ölçümü | Boşta <%1 |
| 8 | Sonuç tabloya işlenir | §7.1 ile karşılaştırma |

Kanıt: [[index]] §14 (protokol bu revizyonda o dosyadan gelir) + ⚠️ not: repo'da otomatize edilmemiştir; adım değerleri tasarım önerisidir, hedef sayılar kaynaktandır.

### §7.4 Sapma durumunda

| Sapma | Aksiyon |
|-------|---------|
| RTT > hedef | Buffer boyutu/SR matrisine dön (§4) → blocking denetimi |
| xrun > 0 | [[asio-buffer-callback]] §7.3 |
| CPU > <%1 (boşta) | RT thread denetimi → [[asio-thread-model]] |
| Ölçüm tekrarlanamıyor | Protokol §7.3, 3 ölçüm medyanı |

---

### §7.5 Backup — Latency zinciri, izleme ve hedefler (latency-optimization.md)

Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md`.

#### §7.5.1 Round-trip latency zinciri (backup ASCII şeması, L16–L40)

```
Input  zinciri: [Mikrofon 0.1ms] → [ADC 0.01ms] → [Driver In 0.67ms] → [K3 DSP 0.1ms]
                 Toplam Input  : 0.88ms
Output zinciri: [K3 DSP 0.1ms] → [Driver Out 0.67ms] → [DAC 0.01ms] → [Hoparlör 0.1ms]
                 Toplam Output : 0.88ms
                 Round-Trip    : 1.76ms                      (backup L38)
```

#### §7.5.2 Buffer boyut → latency tablosu (96kHz, backup L46–L51 birebir)

| Buffer Boyutu | Örnekleme Hızı | Latency | CPU | Stabilite |
|---------------|----------------|---------|-----|-----------|
| 32 | 96kHz | 0.33ms | Yüksek | Düşük |
| 64 | 96kHz | 0.67ms | Orta | Orta |
| 128 | 96kHz | 1.33ms | Düşük | Yüksek |
| 256 | 96kHz | 2.67ms | Çok Düşük | Çok Yüksek |

#### §7.5.3 Hedef üçlüsü (çelişki taşıdı — bastırılmadı)

| Hedef | Değer | Kanıt |
|---|---|---|
| Genel hedef | "< 1ms hedeflenir" | latency-optimization.md L12 |
| Kullanım örneği profili | `targetLatencyUs = 500` (0.5ms), `optimizeForTarget(0.5)` | aynı dosya L347, L353 |
| Ölçülmüş varsayım | "Round-trip Latency · Hedef <1ms · Gerçek 0.88ms" | aynı dosya L365 |

⚠️ VERIFICATION REQUIRED: bu üç değer, `.ai/architecture/k036-asio-drivers/index.md` §4.1'deki
bağlayıcı hedef tablosu ve `asio-drivers.md` L156–L163 (1.34/1.33ms) ile **aynı değildir**;
çoklu hedef kaydı bilinçli olarak birleştirilmedi. Nihai mutabakat onayı beklenmektedir.

#### §7.5.4 Latency monitoring (backup L180–L257)

- Girdi/çıktı gecikme örneklemesi: son **1000** örnek tutulur (`inputLatencies` / `outputLatencies`)
- `getStats()`: avg / max / min / **jitter (σ = √(Σ(x-avg)²/N))`** / `roundTrip = inputAvg + outputAvg`
- Test sınıfı `LatencyTester` (L261–L313): süre boyunca `readInput → processAudio → writeOutput`
  ölçülür, `underruns` / `overruns` sayacı döner

#### §7.5.5 Performans metrikleri (backup L361–L369)

| Metrik | Hedef | Gerçek (backup) |
|--------|-------|--------|
| Round-trip Latency | < 1ms | 0.88ms |
| Jitter | < 10μs | 7.2μs |
| CPU (RT) | < 5% | 3.8% |
| Memory Lock | 100% | 100% |
| Underrun Rate | < 0.01% | 0.005% |

---

## §8 Kenar Durumlar

| # | Kenar durum | Davranış | Kanıt |
|---|------------|----------|-------|
| 1 | SR değişince süre değişir | ms değeri yeniden hesaplanır (§3.1) | Formül |
| 2 | 192kHz'de 64 örnek | 0.333ms → çok küçük dönem; xrun riski | §4.4 türetme |
| 3 | 44.1kHz ailesi | 48k tablosu geçersiz; §4.4 kullan | Türetme |
| 4 | Ölçüm donanımsız yapılırsa | Sonuç "gerçek" sayılmaz | [[index]] §11 |
| 5 | İki hedef tablosu çatışırsa | İkisi de raporlanır, birleştirilmez | §3.5 |
| 6 | WASAPI'ye düşülürse | Gecikme karakteri değişir (10–40ms shared) | [[../k037-wasapi-exclusive/index]] §1 |

---

## §9 Bağımlılıklar

| Bağımlılık | Yön | Not |
|-----------|-----|-----|
| Buffer & callback katmanı | Alt süreç | [[asio-buffer-callback]] |
| SR/cihaz yaşam döngüsü | Alt süreç | [[asio-device-lifecycle]] |
| Thread/RT koşulları | Alt süreç | [[asio-thread-model]] |
| Zincir bütçesi (üst) | Üst | `../k043-latency-optimization/index` *(diskte yok — planlı D01)* |
| WASAPI yolu | Yatay | [[../k037-wasapi-exclusive/index]] |
| xrun metriği (K14) | Yatay | rehber §5.1 (`driver_path`, `xrun_count`) |

---

## §10 Test Matrisi (LT-K036 serisi)

| Test ID | Senaryo | Beklenen | Kriter |
|---------|---------|----------|--------|
| LK-01 | 64 @96kHz tek yön | 0.667ms | §4.1 |
| LK-02 | 32 @96kHz tek yön | 0.333ms | §4.1 |
| LK-03 | 128 @48kHz tek yön | 2.667ms | §4.2 |
| LK-04 | 512 @48kHz tek yön | 10.667ms | §4.2/§4.3 |
| LK-05 | Round-trip ≈ 2× tek yön | Sapma <%5 | §3.4 |
| LK-06 | Kaynak örneği yeniden üretme (96k/64) | ~1.48ms one-way | §3.3 |
| LK-07 | Ölçüm ×3 medyan | Tekrarlanabilirlik | §7.3 |
| LK-08 | xrun=0 koşulu | Sıfır | §7.3 adım 6 |
| LK-09 | CPU boşta | <%1 | §7.1 |
| LK-10 | Buffer değişim süresi | <10µs | §7.1 |

---

## §11 Kanıt Satırları

| # | İddia | Kanıt yolu | Satır |
|---|-------|-----------|-------|
| 1 | `Total Latency = Input + Process + Output + Driver` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | L93 |
| 2 | 96k/64 örneği: 1.48ms one-way, 2.96ms RTT | aynı dosya | L95–L103 |
| 3 | 32/64/128 @96kHz süreleri | aynı dosya | L50–L52 |
| 4 | 512/256/128 @48kHz süreleri + "çift yön ≈ 2×" | `_backup/.../k2-surucu/CLAUDE.md` | L38–L43 |
| 5 | Performans hedefleri (RTT 1.34/1.33) | `k2-surucu/asio-drivers.md` | L156–L163 |
| 6 | 0.5ms round-trip ilk hedefi | aynı dosya | L12 |
| 7 | 512 varsayılan / 64-1024 / 48kHz / 32-bit float | `.ai/brain.md` | L295 |
| 8 | 48kHz standart | `.ai/CLAUDE.md` | L427 |
| 9 | Gecikme karakteri (buffer + sürücü yolu + xrun) | `.ai/ecosystem/asio-wasapi-rehber.md` | §3.4 (exa 2026-09-24) |
| 10 | Ölçüm protokolü (8 adım) | `.ai/architecture/k036-asio-drivers/index.md` | §14 |
| 11 | Round-trip zinciri 0.88+0.88=1.76ms | `_backup/.../k2-surucu/latency-optimization.md` | L16–L40 |
| 12 | 96kHz buffer→ms tablosu (32/64/128/256) | aynı dosya | L46–L51 |
| 13 | "< 1ms" genel hedef + 0.5ms profil kullanımı | aynı dosya | L12, L347, L353 |
| 14 | Ölçülmüş varsayım RTT 0.88ms · jitter 7.2µs · CPU 3.8% | aynı dosya | L361–L369 |
| 15 | LatencyMonitor (1000 örnek, σ jitter) + LatencyTester | aynı dosya | L180–L313 |
| 16 | RT scheduling / mlockall / pre-fault (POSIX) | aynı dosya | L85–L133 |
| 17 | 128 @96kHz = 1.33ms (tablo çapraz doğrulama) | aynı dosya | L50 |
| 18 | ASIO buffer konfigürasyonu (512 → ~10.67ms @48k) | `_backup/.../k0-isletim-sistemi/windows-api.md` | L79–L87 |

### §11.1 Backup Kanıt Özeti (backup-first ekleme)

```
backup kanıtı: k2-surucu/latency-optimization.md   L12, L347, L353  (<1ms hedef + 0.5ms profil)
backup kanıtı: k2-surucu/latency-optimization.md   L16-L40          (round-trip zinciri 1.76ms)
backup kanıtı: k2-surucu/latency-optimization.md   L42-L51          (96kHz buffer→latency tablosu)
backup kanıtı: k2-surucu/latency-optimization.md   L53-L83          (calculateOptimalBuffer kodu)
backup kanıtı: k2-surucu/latency-optimization.md   L85-L133         (RT scheduling / mlockall / pre-fault)
backup kanıtı: k2-surucu/latency-optimization.md   L135-L178        (donanım clock optimizasyonu)
backup kanıtı: k2-surucu/latency-optimization.md   L180-L313        (LatencyMonitor + LatencyTester)
backup kanıtı: k2-surucu/latency-optimization.md   L344-L359        (LatencyOptimizer API kullanım örneği)
backup kanıtı: k2-surucu/latency-optimization.md   L361-L369        (performans metrikleri — gerçek sütunu)
backup kanıtı: k2-surucu/asio-drivers.md           L93-L103         (Total Latency formülü + 2.96ms RTT)
backup kanıtı: k2-surucu/CLAUDE.md                 L38-L43          (48k buffer süreleri)
backup kanıtı: k0-isletim-sistemi/windows-api.md   L79-L87          (ASIO buffer konfigürasyonu)
kanıt: .ai/brain.md L295 (512 varsayılan / 64-1024 / 48kHz / ~10.67ms)
kanıt: .ai/architecture/k036-asio-drivers/index.md §4.1, §14 (bağlayıcı hedef + protokol)
```

**Çelişki raporu (üçüncü hedef grubu):** `latency-optimization.md` "<1ms / 0.88ms / 1.76ms"
üçlüsü, `asio-drivers.md` L12 (0.5ms) ve L156–L163 (1.34/1.33ms) ile örtüşmez; backup kazanır
kuralı gereği **üçü de taşındı**, hiçbiri bastırılmadı → ⚠️ VERIFICATION REQUIRED (§7.5.3).

---

## §12 Wiki-Bağlantılar

| Hedef | Bağlantı | İlişki |
|-------|----------|--------|
| K036 indeksi | [[index]] | Üst hub, §4.1 hedef tablosu |
| Buffer & callback | [[asio-buffer-callback]] | Dönem süresi kaynağı |
| Exclusive mode | [[asio-exclusive-mode]] | Exclusive yolu = bu hesabın koşulu |
| Yaşam döngüsü | [[asio-device-lifecycle]] | SR/buffer değişimi → yeniden hesap |
| Thread modeli | [[asio-thread-model]] | Blocking → gecikme payı |
| SDK entegrasyonu | [[asio-sdk-entegrasyon]] | Driver overhead katmanı |
| Hata yönetimi | [[asio-hata-yonetimi]] | Ölçüm kesintileri |
| WASAPI komşusu | [[../k037-wasapi-exclusive/index]] | Alternatif yol gecikmesi |

---

## §13 Risk Kaydı

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|----------|------|---------|
| L1 | İki hedef tablosunun (0.5ms / 1.34ms) karıştırılması | Orta | Orta | §3.5 + §7.2 uyarıları |
| L2 | Ölçüm olmadan "gerçek" değer yazma | Yüksek | Yüksek | §7.3, [[index]] §11 |
| L3 | Türetilmiş hücrelerin ölçüm sanılması | Orta | Orta | §4.4 etiketleme |
| L4 | SR değişiminde eski ms değerinin kalması | Orta | Orta | §8 madde 1, [[asio-device-lifecycle]] §5 |
| L5 | Bütçe üstü module bağlanamaması | Düşük | Orta | §9 devir notu |

---

## §14 Doğrulama Kontrol Listesi

- [ ] Tüm ms değerleri formülle tutarlı (§3.1)
- [ ] Kaynak hücreler ile türetme hücreleri ayrı işaretlenmiş (§4.4)
- [ ] Round-trip = 2× tek yön kontrol edilmiş (§3.4)
- [ ] İki hedef tablosu birleştirilmemiş (§3.5)
- [ ] Ölçüm protokolü 8 adım (§7.3)
- [ ] Yeni sayısal hedef üretilmemiş (Zero-Hallucination)
- [ ] Kırık wiki-link = 0
- [ ] Frontmatter 7 alan

---

## §15 Sık Sorulan Sorular

**S1: Formül nedir?** `T = N/SR × 1000` (§3.1); toplam = Input + Process + Output + Driver (§3.2).

**S2: Round-trip?** ≈ 2 × tek yön (`.ai/.../CLAUDE.md L42`).

**S3: Varsayılan kaç ms?** 512 @48kHz ≈ 10.67ms (`.ai/brain.md L295`).

**S4: Hedef nedir?** RTT 1.34ms hedef / 1.33ms gerçek (§7.1); ayrıca kaynakta 0.5ms ilk hedefi (§7.2) → ⚠️ öncelik doğrulanmalı.

**S5: Nerede ölçülür?** §7.3 protokolü; araç implementasyonu ⚠️ VERIFICATION REQUIRED.

**S6: WASAPI'ye geçerse?** Karakter değişir (shared 10–40ms) → [[../k037-wasapi-exclusive/index]].

**S7: Buffer seçimi hangi belgede?** Seçim politikası üst modülde → `../k043-latency-optimization/buffer-size-selection` *(diskte yok — planlı D01)*; bu dosya yalnız hesap/tablodur.

---

## §16 Terim Sözlüğü (bu dosya)

| Terim | Tanım |
|-------|-------|
| Tek yön (one-way) | Giriş veya çıkış tek başına gecikmesi |
| Round-trip (RTT) | Giriş→çıkış toplam gecikme |
| Dönem (period) | Buffer/SR süresi |
| Buffer overhead | Sürücünün eklediği gecikme payı |
| Jitter | Dönem süresinin dalgalanması |
| xrun | Underrun/overrun |
| SR | Örnekleme hızı |
| Medyan | 3 ölçümün ortanca değeri |
| Türetme | Formülden hesaplanan (ölçülmemiş) değer |

---

## §17 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — latency hesap dosyası (multi-md revizyonu) | Vault Documentation Specialist |
