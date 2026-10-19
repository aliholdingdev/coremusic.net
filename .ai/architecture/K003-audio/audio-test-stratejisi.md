---
node_id: K003-audio/test-stratejisi
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on:
  - K005-firmware
  - K006-codebase
  - K007-sandbox
  - K008-superapp
  - K009-api
  - K010-network
  - K011-ui-kit
evidence:
  - ".ai/AGENTS.md §5 (Audio: SNR >100dB, THD+N <0.01%@1kHz) — boot 2026-10-18"
  - ".ai/architecture/K003-audio/audio-master.md §3.4 (callback sözleşmesi, durum makinesi) — okundu 2026-10-18"
  - ".ai/architecture/K003-audio/audio-master.md §29.2 (ASIO lisans) — okundu 2026-10-18"
validation: "Test kapıları .ai/AGENTS.md §5 ses kalitesi eşikleriyle hizalı"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — otomatik ölçüm altyapısı (auval/alsabat/WASAPI test rig)
  repo'da yok; test betikleri üretim öncesi `.ai/templates/` ile doğrulanacak.
group: kod
---

# K003-audio — Test Stratejisi

## §1 — ELI15: Bu nedir?

Ses motorunu nasıl sınayacağımızın planı. Üç katman var: en altta **birim**
(parçalar tek tek, hızlı), ortada **entegrasyon** (backend + cihaz
simülasyonu), en üstte **donanım** (gerçek ses kartı, kulak/mikrofon
ölçümü). "Ses temiz mi?" sorusunun sayısal cevabı var: SNR > 100 dB ve
THD+N < %0.01 (@1 kHz) — `.ai/AGENTS.md §5` zorunluluğu.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Test piramidi, senaryolar, kabul kriterleri (SNR/THD+N/latency/xrun) |
| Kapsam dışı | DSP algoritma test derinliği (K004-dsp test-stratejisi) · firmware testleri (K005) |

## §3 — Test piramidi

| Katman | Ne test edilir | Araç | Pay |
|---|---|---|---|
| Birim | Ring buffer SPSC, durum makinesi geçişleri, MIME haritası, EQ preset migration | PHPUnit 12 · Vitest (JS) | %60 |
| Entegrasyon | Backend aç/kapa, hot-plug simülasyonu, callback bütçesi (mock cihaz) | PHPUnit + fake device · mock WASAPI/ALSA | %30 |
| Donanım | SNR/THD+N, round-trip latency, xrun under load | auval (macOS) · alsabat (Linux) · WASAPI rig ⚠️ | %10 |

## §4 — Senaryolar

### 4.1 Birim

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-A1 | Ring buffer dolduğunda yazma | Taşma engellenir, dönen yazılan < istenen; xrun sayacı artar | ADR-019 · C-buf-1 |
| T-A2 | Durum makinesi geçersiz geçiş (RUNNING→STARTING) | Reddedilir, durum değişmez | C-st-1 (`audio-master.md` §3.4) |
| T-A3 | device-lost → 3 başarısız deneme | RECOVERING→STOPPED, olay K016'ya | C-st-1 · KA-A6 |
| T-A4 | DSD/MQA dosyası → MIME | `audio/dsd` / `audio/flac` (gerçek harita) | `MusicStreamHandler.php` L24-L44 ✓ |
| T-A5 | Profil migration v1→v2 | Biquad katsayı dönüşümü veya açık hata (sessiz bozulma YOK) | C-prof-1 |

### 4.2 Entegrasyon

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-A6 | Exclusive açılamaz (cihaz meşgul) → shared fallback | Akış açılır, bit-exact=false bayrağı set | KA-A5 · ADR-025 |
| T-A7 | 44.1 kHz kaynak → 48 kHz cihaz | Yalnız zorunlu zincirde resample; bit-exact yolda hata | KA-A4 |
| T-A8 | Hot-plug: cihaz çıkar → tak | Kalıcı ID ile yeniden bağlanır, profil korunur | C-dev-1 |
| T-A9 | 20 dk kesintisiz akış | xrun = 0 (yapısal); non-zero ise bütçe aşımı raporlanır | KA-A6 |

### 4.3 Donanım (ölçüm)

| # | Senaryo | Beklenen | Araç |
|---|---|---|---|
| T-A10 | 1 kHz sine @ -20 dBFS round-trip | SNR > 100 dB | auval/alsabat ⚠️ |
| T-A11 | THD+N @ 1 kHz | < %0.01 | auval/alsabat ⚠️ |
| T-A12 | Round-trip latency (buffer 128/256/512) | raporlanır (ms); hedef ≤ 20 ms @ 256 | K00305-latency betiği |
| T-A13 | 44.1/48/96/192 kHz + 16/24/32f matrisi | Tüm kombinonlar açılır veya net hata | KA-A3 |

## §5 — Kabul kriterleri

| Kriter | Eşik | Kapı |
|---|---|---|
| SNR | > 100 dB | `.ai/AGENTS.md §5` — geçmezse release BLOCK |
| THD+N @1 kHz | < %0.01 | `.ai/AGENTS.md §5` — geçmezse release BLOCK |
| Callback bütçesi | 5-20 ms (KA-A6) | yapısal — aşım xrun event'i |
| Xrun (20 dk) | 0 yapısal | T-A9 |
| Birim kapsamı | K003 çekirdek (ring/state/MIME/preset) %90+ | release gate |
| Bit-exact yollar | dönüşüm yok (KA-A4) | T-A7 |

## §6 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| SNR/THD+N eşikleri | `.ai/AGENTS.md §5` | ✓ Boot 2026-10-18 |
| Durum makinesi/makine sözleşmeleri | `audio-master.md` §3.4 | ✓ Okundu 2026-10-18 |
| MIME gerçek kanıtı | `MusicStreamHandler.php` L24-L44 | ✓ Okundu 2026-10-18 |
| Ölçüm araçları (auval/alsabat/WASAPI rig) | repo'da test altyapısı YOK | ⚠️ VERIFICATION REQUIRED |

## §7 — Açık sorular

| # | Soru | Sahip |
|---|---|---|
| 1 | Donanım ölçüm rig'i hangi donanımla kurulacak (gerçek DAC/mik)? | K003-audio |
| 2 | auval/alsabat CI'ye mi girer, elle mi koşulur? | K007-sandbox |
| 3 | T-A9 xrun=0 eşiği hangi CPU'da geçerli sayılacak (cihaz matrisi)? | K005-firmware |

## §8 — Ölçüm raporu şablonu

Her donanım testi (T-A10..T-A13) sonrası bu şablon doldurulur:

```yaml
test_id: T-A10
cihaz: "<DAC/marka/model>"
backend: asio | wasapi | coreaudio | alsa
sample_rate: 48000
buffer_frames: 256
olcumler:
  snr_db: 0.0        # hedef > 100
  thd_n_pct: 0.0     # hedef < 0.01 (@1 kHz)
  latency_ms: 0.0    # round-trip
sonuc: gecti | gecmedi
tarih: YYYY-MM-DD
not: "⚠️ eğer araç/sürüm teyitsiz"
```

### Kapı karar matrisi

| Ölçüm | Eşik | Aşarsa/ne olur |
|---|---|---|
| SNR | > 100 dB | BLOCK — release durur |
| THD+N @1 kHz | < %0.01 | BLOCK — release durur |
| Latency @256 | rapor (hedef ≤20 ms) | raporlanır, BLOCK değil |
| xrun (20 dk) | 0 yapısal | BLOCK — bütçe aşımı |

### Cihaz matrisi (hangi donanımda koşulacak)

| Cihaz sınıfı | Backend | Durum |
|---|---|---|
| Windows USB DAC | ASIO (birincil) + WASAPI (fallback) | ⚠️ gerçek cihaz seçimi yok |
| macOS dahili | CoreAudio | ⚠️ rig yok |
| Linux HDMI/USB | ALSA (+ JACK/PW) | ⚠️ rig yok |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
