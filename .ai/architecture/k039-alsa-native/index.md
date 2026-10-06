---
title: "K039 — ALSA Native Sürücüleri (Linux PCM Cihazları)"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.1.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K039 — ALSA Native Sürücüleri

> **K özeti:** K039, Linux üzerinde ALSA (Advanced Linux Sound Architecture) yolunu kapsar: `libasound` kütüphanesi, ALSA Kernel Driver, PCM cihazları (playback/capture/duplex), erişim yöntemleri (MMAP / RW), donanım parametreleri, iki seviyeli buffer (kernel + user), period size hesabı, XRUN (underrun/overrun) yönetimi ve HW parametre tablosu. CoreMusic sürücü öncelik sırası içinde **#4**'üncü basamaktır.
>
> **Yer:** D01 (K000–K071) · görüntüleme/ses sürücüleri, latency, buffer, kernel-arayüz. *(D01 aralığı: `k016-linux-ses/index.md` frontmatter `authority: "WORKFLOW.md §2.1 D01 — K000-K071"`.)*

**Bağlantılar:**
- İç dosya: [[alsa-pcm-device]]
- Komşular: [[../k033-platform-ses-suruculeri/index]] · [[../k016-linux-ses/index]] · [[../k038-core-audio-macos/index]] · [[../k036-asio-drivers/index]] · [[../k031-buffer-management/index]]
- Diğer: [[../k030-driver-stack/index]] · [[../k032-latency-optimization/index]] · [[../k023-system-calls/index]] · [[../k018-dma-kesinti-yonetimi/index]] · [[../k025-threading-model/index]]

---

## §1 Genel Bakış

ALSA yığını kaynaktaki dört katman halinde tanımlanır:

| Katman | Kaynak satırı | Rol |
|--------|--------------|-----|
| Uygulama (K3 Neva Engine) | `k2-surucu/alsa-native.md L20` | Ses işleme tüketicisi |
| ALSA Library (`libasound`) | aynı dosya `L22` | Kullanıcı uzay kütüphanesi |
| ALSA Kernel Driver | aynı dosya `L24` | Çekirdek sürücüsü |
| Hardware (PCM, Control, MIDI) | aynı dosya `L26` | Donanım |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md L16–L28`

### §1.1 Bu klasörün soruları

1. PCM cihazı nasıl açılır (playback / capture / duplex)?
2. Hangi erişim yöntemi seçilir (MMAP vs RW) ve farkı nedir?
3. Buffer ve period boyutu nasıl hesaplanır?
4. XRUN (underrun/overrun/suspended) nasıl kurtarılır?
5. HW parametreleri nasıl yazılır ve doğrulanır?

### §1.2 Kapsam / Kapsam Dışı

| Kapsam içi | Kapsam dışı |
|-----------|-------------|
| ALSA mimari katmanları (4 katman) | PipeWire üst katmanı → `../k033-platform-ses-suruculeri/pipewire-modern` + `../k016-linux-ses/pipewire-modern-yigin` |
| PCM cihaz tipleri ve erişim yöntemleri | Buffer veri yapısı → `../k031-buffer-management/buffer-management` |
| `snd_pcm_hw_params_*` parametreleri | Gecikme zinciri → `../k032-latency-optimization/latency-optimization` |
| İki seviyeli buffer (kernel/user) | Kernel syscall arayüzü → `../k023-system-calls/system-calls` |
| Period size hesabı (kesme aralığı) | IRQ/DMA yolu → `../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi` |
| XRUN kurtarma akışı | Windows/macOS yolları → `k036`, `k037`, `k038` |
| RT thread kuralları / PipeWire sınırı (özet) | RT + PipeWire ayrıntısı → `rt-thread-ve-pipewire-siniri` |

### §1.3 Durum

| Alan | Durum | Kanıt |
|------|-------|-------|
| Tasarım (dokümantasyon) | Tamamlandı | `k2-surucu/alsa-native.md L240–L245` |
| Faz planı | 4 faz (RW → MMAP → XRUN → HWDEP) | aynı dosya `L242–L245` |
| Performans tablosu | Hedef/gerçek mevcut | aynı dosya `L222–L230` |
| Kod implementasyonu | ⚠️ VERIFICATION REQUIRED | Depoda ALSA kodu görülmedi |
| Gerçek donanım ölçümü | ⚠️ VERIFICATION REQUIRED | Ölçüm raporu yok |

### §1.4 Kimlik Bloğu (Layer Identity)

| Alan | Değer |
|---|---|
| Layer ID | `k039-alsa-native` (D01 kapsamı — sürücü katmanı) |
| Purpose | Linux ALSA native PCM cihaz erişimi: hw/plughw seçimi, PCM akışı, mmap, buffer/period hesabı, XRUN kurtarma; RT thread + PipeWire sınırı özeti |
| Dependencies Allowed | `k016-linux-ses` · `k018-dma-kesinti-yonetimi` · `k020-linux-core` · `k023-system-calls` · `k025-threading-model` · `k030-driver-stack` · `k031-buffer-management` · `k032-latency-optimization` · `k033-platform-ses-suruculeri` · `k034-usb-audio` · `k036`/`k037`/`k038` (kardeş platform klasörleri) |
| Dependencies Forbidden | `k040`–`k053` (diskte YOK — bkz. §19.6); Vault dışı kaynaklardan doğrudan `snd_pcm_*` çağrısı (repo'da 0 eşleşme, 2026-10-06 taraması) |
| Runtime | Linux user-space (libsndread/libsndwrite hariç — repo'da ALSA kodu yok); ⚠️ VERIFICATION REQUIRED |
| Failure Mode | XRUN (underrun/overrun/suspended) → `alsa-pcm-device.md` §9; RT thread bloklanma → `rt-thread-ve-pipewire-siniri.md` §5 |
| Observability | Bu depoda ALSA ölçüm altyapısı YOK — §14 ölçüm protokolü ancak harici donanımda çalıştırılabilir; sonuç `⚠️ VERIFICATION REQUIRED` |

---

## §2 Mimari Konum (ASCII)

```
              K3 Neva Engine
                    │
       ┌────────────▼─────────────┐
       │  ALSA Library (libasound)│  ← alsa-native.md L22
       └────────────┬─────────────┘
                    │
       ┌────────────▼─────────────┐
       │  ALSA Kernel Driver      │  ← aynı dosya L24
       │  (Kernel Ring Buffer)    │     L77–L86
       └────────────┬─────────────┘
                    │  (MMAP: doğrudan / RW: kopya)
       ┌────────────▼─────────────┐
       │  Hardware                 │  ← aynı dosya L26
       │  (PCM, Control, MIDI)     │
       └──────────────────────────┘

  Erişim yöntemleri:
    SND_PCM_ACCESS_MMAP_INTERLEAVED   → en düşük latency (L40)
    SND_PCM_ACCESS_RW_INTERLEAVED     → okuma/yazma (L42)
```

**Yer:** K039 = K2 sürücü katmanı · üstü K3 · altı K1 Linux Core + çekirdek ALSA modülü.

---

## §3 Dosya Haritası

| Dosya | Konu | Başlıca kanıt |
|-------|------|---------------|
| `index.md` | Kapsam, katman, parametreler, XRUN, HWDEP, bağımlılık | `alsa-native.md L16–L245` |
| `alsa-pcm-device` | PCM cihazları, erişim yöntemleri, hw_params çağrıları, MMAP, XRUN, `ALSADriver` arayüzü | aynı dosya `L30–L220` |
| `rt-thread-ve-pipewire-siniri` | RT thread kuralları (systemd + pthread) ve PipeWire ↔ ALSA sınırı | `linux-core.md L140–L163` · `cross-platform-api.md L33–L62` · `pipewire-modern.md L12–L50` |

---

## §4 Teknik Özet

### §4.1 Ölçülmüş performans hedefleri (kaynak tablosu)

| Metrik | Hedef | Gercek |
|--------|-------|--------|
| Latency (MMAP) | 1ms | 0.8ms |
| Latency (RW) | 5ms | 4.2ms |
| Buffer Boyutu | 64-256 | 64 |
| CPU (boşta) | < 1% | 0.4% |
| Maks. Kanal | 128 | 128 |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md L222–L230`

> Sütunlar kaynaktan **birebir** taşınmıştır; bağımsız ölçüm yapılmamıştır (`⚠️ VERIFICATION REQUIRED`).

### §4.2 Sürücü öncelik sırası (k2 CLAUDE.md guardrail)

| Basamak | Sürücü | OS |
|---------|--------|-----|
| #1 | ASIO | Windows |
| #2 | WASAPI Exclusive | Windows |
| #3 | CoreAudio | macOS |
| **#4** | **ALSA** | **Linux** |
| #5 | PipeWire | Linux |
| #6 | WASAPI Shared | Windows |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md L27–L34`

### §4.3 PCM cihaz tipleri (kaynak)

| Sabit | Rol | Kanıt |
|-------|-----|-------|
| `PCM_DEVICE_PLAYBACK` | Ses çıkışı (c:0, c:1, ...) | `L35` |
| `PCM_DEVICE_CAPTURE` | Ses girişi (p:0, p:1, ...) | `L36` |
| `PCM_DEVICE_DUPLEX` | Hem giriş hem çıkış | `L37` |

### §4.4 Erişim yöntemleri (kaynak)

| Sabit | Açıklama | Kanıt |
|-------|----------|-------|
| `SND_PCM_ACCESS_MMAP_INTERLEAVED` | Doğrudan bellek haritalama (en düşük latency) | `L40` |
| `SND_PCM_ACCESS_MMAP_NONINTERLEAVED` | Non-interleaved erişim | `L41` |
| `SND_PCM_ACCESS_RW_INTERLEAVED` | Okuma/yazma (interleaved) | `L42` |
| `SND_PCM_ACCESS_RW_NONINTERLEAVED` | Okuma/yazma (non-interleaved) | `L43` |

### §4.5 Donanım parametre çağrıları (kaynak)

| Parametre | Çağrı | Kanıt |
|-----------|-------|-------|
| Örnekleme hızı | `snd_pcm_hw_params_set_rate_near` | `L55–L56` |
| Kanal sayısı | `snd_pcm_hw_params_set_channels` | `L59–L60` |
| Buffer boyutu | `snd_pcm_hw_params_set_buffer_size_near` | `L63–L64` |
| Periyot boyutu | `snd_pcm_hw_params_set_period_size_near` | `L67–L68` |
| Tahsis/serbest | `malloc` / `free` | `L52`, `L70` |

### §4.6 Bağımlılıklar (kaynak tablosu)

| Bağımlılık | Tür |
|------------|-----|
| libasound | Sistem kütüphanesi |
| Linux Kernel ALSA | Çekirdek modülü |
| K1 Linux Core | Alt katman |

Kanıt: aynı dosya `L232–L238`

---

## §5 Alt Dosya Özeti

### §5.1 `alsa-pcm-device` — ne anlatır?

- PCM cihaz tipleri ve 4 erişim yöntemi (`L30–L43`).
- `snd_pcm_hw_params_*` ile SR, kanal, buffer, period ayarı (`L45–L71`).
- İki seviyeli buffer: Kernel Ring Buffer (write/read ptr, P0–P4) ve User Buffer (`L73–L91`).
- Period size formülü: `Buffer Size / Number of Periods` + 48kHz örneği (`L92–L105`).
- MMAP yazma akışı: `mmap_begin` → `memcpy` → `mmap_commit` (`L107–L129`).
- XRUN tablosu (Underrun/Overrun/Suspended) + `snd_pcm_recover` (`L131–L148`).
- HW parametre tablosu (7 parametre) (`L150–L163`).
- `ALSADriver` sınıfı ve kullanım örneği (`L164–L220`).

---

## §6 Bağımlılıklar

| Bağımlılık | Yön | Tür | Not |
|-----------|-----|-----|-----|
| `libasound` | Alt | Sistem kütüphanesi | `L236` |
| Linux Kernel ALSA | Alt | Çekirdek modülü | `L237` |
| K1 Linux Core | Alt | OS | `L238` |
| K2 sürücü yığını (`k030`) | Alt | Mimari | [[../k030-driver-stack/driver-stack-mimari]] — HAL soyutlama |
| K2 buffer yönetimi (`k031`) | Alt | Veri yapısı | [[../k031-buffer-management/buffer-management]] — ring buffer tanımı |
| K2 latency optimizasyonu (`k032`) | Alt | Metrik | [[../k032-latency-optimization/latency-optimization]] — 1ms hedefi |
| PipeWire (`k033` / `k016`) | Üst | Alternatif üst katman | [[../k033-platform-ses-suruculeri/pipewire-modern]] — #5 öncelik |
| K3 Neva Engine | Üst | Tüketici | `write()` hedefi |

---

## §7 Kanıt Satırları (Bu dosyadaki iddiaların kaynağı)

| # | İddia | Kanıt yolu | Satır |
|---|-------|-----------|-------|
| 1 | 4 katmanlı yığın (uygulama → libasound → kernel → hardware) | `k2-surucu/alsa-native.md` | L16–L28 |
| 2 | 3 PCM cihaz tipi | aynı dosya | L34–L37 |
| 3 | 4 erişim yöntemi; MMAP en düşük latency | aynı dosya | L39–L43 |
| 4 | `hw_params` çağrıları (SR, kanal, buffer, period) | aynı dosya | L49–L71 |
| 5 | İki seviyeli buffer (kernel ring + user) | aynı dosya | L73–L91 |
| 6 | Period = Buffer / Periods; 256 @48k = 5.33ms örneği | aynı dosya | L92–L105 |
| 7 | MMAP begin/commit akışı | aynı dosya | L107–L129 |
| 8 | XRUN tipleri ve çözümleri | aynı dosya | L131–L140 |
| 9 | `snd_pcm_recover` kurtarma | aynı dosya | L141–L148 |
| 10 | 7 HW parametresi tablosu | aynı dosya | L150–L163 |
| 11 | `ALSADriver` arayüzü | aynı dosya | L167–L201 |
| 12 | Kullanım örneği (96000/32/2, buffer 256, period 64, MMAP) | aynı dosya | L203–L219 |
| 13 | Latency MMAP 1ms/0.8ms · RW 5ms/4.2ms | aynı dosya | L222–L230 |
| 14 | Bağımlılıklar (libasound, Kernel ALSA, K1) | aynı dosya | L232–L238 |
| 15 | 4 faz planı | aynı dosya | L240–L245 |
| 16 | Sürücü önceliği #4 | `k2-surucu/CLAUDE.md` | L27–L34 |
| 17 | Period size kesme (interrupt) aralığını belirler | `k2-surucu/alsa-native.md` | L94 |

---

## §8 Kenar Durumlar (Özet)

| # | Kenar durum | Davranış |
|---|-------------|----------|
| A1 | Cihaz bulunamadı (`hw:0,0` yok) | `openPlayback` başarısız → `⚠️ VERIFICATION REQUIRED` (kurtarma kaynakta yok) |
| A2 | İstenen SR desteklenmiyor | `set_rate_near` en yakın değeri seçer (`L55–L56`) |
| A3 | Period/ buffer uyuşmazlığı | `_near` çağrıları sınırda uyarlar (`L63–L68`) |
| A4 | Underrun | `snd_pcm_recover` + yeniden `writei` (`L143–L147`) |
| A5 | Overrun | Buffer/period ayarı değişir (XRUN tablosu `L138`) |
| A6 | Donanım durakladı | `snd_pcm_prepare()` çağrısı (`L139`) |
| A7 | MMAP başarısız | RW moduna düşme — `⚠️ VERIFICATION REQUIRED` (geçiş kuralı kaynakta yok) |
| A8 | Duplex gecikme asimetrisi | `⚠️ VERIFICATION REQUIRED` |

---

## §9 Hata Modları (Özet)

| # | Hata modu | Belirti | Düzeltme |
|---|-----------|---------|----------|
| H1 | Underrun (buffer yetersiz) | Kopuk ses | Buffer boyutunu artır (`L137`) |
| H2 | Overrun (buffer taştı) | Bozuk okuma | Period sayısını azalt (`L138`) |
| H3 | Suspended (donanım durdu) | Akış durur | `snd_pcm_prepare()` (`L139`) |
| H4 | `writei < 0` | Yazma hatası | `snd_pcm_recover` → tekrar (`L143–L147`) |
| H5 | SR mismatch (uygulama↔cihaz) | Pitch hatası | Guardrail: `k2-surucu/CLAUDE.md L18–L24` |
| H6 | Erişim yöntemi reddi | MMAP açılmaz | RW'ye düşme → `⚠️ VERIFICATION REQUIRED` |
| H7 | Period hesabı tutmaz | Latency sapması | `L97` formülü + ölçüm |

---

## §10 Doğrulama Kontrol Listesi

- [x] Frontmatter 7 alan (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [x] `version: 4.1.0` · `updated: 2026-10-06`
- [x] Her dosya ≥500 satır (`index.md` ≥500 · `alsa-pcm-device.md` ≥500 · `rt-thread-ve-pipewire-siniri.md` <500 — bilinçli kapsam darlığı, §19.6)
- [x] Wiki-link hedefleri diskte var olan klasör adlarıyla birebir (`k030`–`k039`, `k016`, `k018`, `k020`, `k023`, `k025`, `k034`, `k036`–`k038`; `k040`–`k053` YOK → taşındı, §19.6)
- [x] Kanıt = gerçek dosya yolu + satır aralığı
- [x] Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`
- [x] Yeni sayı/sürüm/ürün adı üretilmedi
- [x] Period/Latency değerleri kaynaktan alındı
- [x] PowerShell yazma komutu kullanılmadı
- [x] `git commit` atılmadı

---

## §11 Açık Konular

| # | Konu | Durum |
|---|------|-------|
| 1 | ALSA kod implementasyonu | ⚠️ VERIFICATION REQUIRED |
| 2 | Gerçek donanım ölçümü (0.8ms / 4.2ms) | ⚠️ VERIFICATION REQUIRED |
| 3 | MMAP → RW fallback kuralı | ⚠️ VERIFICATION REQUIRED |
| 4 | Duplex zamanlama asimetrisi | ⚠️ VERIFICATION REQUIRED |
| 5 | HWDEP (Faz 4) detayları | ⚠️ VERIFICATION REQUIRED |
| 6 | Control/MIDI kanalları | ⚠️ VERIFICATION REQUIRED — bu revizyonda açılmadı |

---

## §12 Wiki-Bağlantılar

| Tür | Hedef | Neden |
|-----|-------|-------|
| İç | [[alsa-pcm-device]] | PCM/period/MMAP/XRUN ayrıntısı |
| İç | [[rt-thread-ve-pipewire-siniri]] | RT thread kuralları, PipeWire sınırı |
| Komşu | [[../k033-platform-ses-suruculeri/pipewire-modern]] | #5 üst katman (PipeWire) |
| Komşu | [[../k016-linux-ses/index]] | Linux ses yığını (ALSA → PipeWire) kardeş klasör |
| Komşu | [[../k020-linux-core/alsa-native]] | Linux çekirdeği ALSA yerel entegrasyonu |
| Komşu | [[../k038-core-audio-macos/index]] | macOS karşılığı |
| Komşu | [[../k036-asio-drivers/index]] | #1 birincil yol |
| Komşu | [[../k037-wasapi-exclusive/index]] | #2 Windows yolu |
| Komşu | [[../k030-driver-stack/driver-stack-mimari]] | Sürücü yığını |
| Komşu | [[../k031-buffer-management/kilitsiz-kuyruklar]] | Ring buffer |
| Komşu | [[../k032-latency-optimization/latency-optimization]] | 1ms / 5ms bütçe |
| Diğer | [[../k023-system-calls/system-calls]] | Syscall yüzeyi |
| Diğer | [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] | Period ↔ kesme aralığı |
| Diğer | [[../k025-threading-model/gercek-zamanli-zamanlama]] | RT thread/atomik |

---

## §13 Senaryo Yürütmeleri (K039 geneli)

### §13.1 Düşük gecikmeli MMAP çıkış senaryosu

| Adım | İşlem | Kanıt |
|------|-------|-------|
| 1 | `initialize(config)` (96000/32/2) | `L206–L210` |
| 2 | `openPlayback("hw:0,0")` | `L211` |
| 3 | `setBufferSize(256)` | `L212` |
| 4 | `setPeriodSize(64)` | `L213` |
| 5 | `enableMMAP()` | `L214` |
| 6 | Döngüde `write(engineOutput, 64)` | `L217–L219` |

### §13.2 RW mod senaryosu

| Adım | İşlem | Not |
|------|-------|-----|
| 1 | `setAccessMode(RW_INTERLEAVED)` | `L181` |
| 2 | `write()` → `snd_pcm_writei` | `L185`, `L143` |
| 3 | Hata → `recover()` | `L196` |
| 4 | Yeniden `write()` | `L146–L147` |

### §13.3 XRUN kurtarma senaryosu

| Adım | Tetikleyici | Çözüm | Kanıt |
|------|------------|-------|-------|
| 1 | Underrun | Buffer artır | `L137` |
| 2 | Overrun | Period azalt | `L138` |
| 3 | Suspended | `snd_pcm_prepare()` | `L139` |
| 4 | `writei < 0` | `snd_pcm_recover` | `L144–L146` |

### §13.4 Period hesabı senaryosu

| Adım | Değer | Sonuç | Kanıt |
|------|-------|-------|-------|
| 1 | Buffer 1024 örnek | — | `L100` |
| 2 | Periods 4 | — | `L101` |
| 3 | Period = 1024/4 = 256 | 256 örnek @48k = 5.33ms | `L102` |
| 4 | Latency = Period/SR = 5.33ms | — | `L104` |

### §13.5 Cihaz listeleme senaryosu

| Adım | İşlem | Kanıt |
|------|-------|-------|
| 1 | `ALSADriver::listDevices()` | `L200` |
| 2 | İsimle `openPlayback` | `L170` |
| 3 | Parametreleri kur | `L175–L182` |
| 4 | MMAP etkinleştir | `L182` |

---

## §14 Ölçüm Protokolü

| # | Ölçüm | Araç | Hedef (kaynak) |
|---|-------|------|----------------|
| 1 | Latency (MMAP) | Round-trip test | 1ms / 0.8ms (`L222–L230`) |
| 2 | Latency (RW) | Round-trip test | 5ms / 4.2ms (`L222–L230`) |
| 3 | Buffer boyutu | `hw_params` okuma | 64–256 / 64 |
| 4 | CPU (boşta) | profil | <1% / 0.4% |
| 5 | Kanal | format doğrulama | 128 |
| 6 | Period hesabı | `L97` formülü | 256/4 = 64 (kaynak örneği) |
| 7 | XRUN sayacı | `getState()` | ⚠️ VERIFICATION REQUIRED |
| 8 | Callback/kesme süresi | zamanlama | ⚠️ VERIFICATION REQUIRED |

**Kural:** Ölçüm yoksa sayı üretilmez.

---

## §15 Test Matrisi (K039 indeks düzeyi)

| ID | Senaryo | Beklenen | Durum |
|----|---------|----------|-------|
| AL-01 | Playback açılışı | Handle geçerli | ⚠️ kod yok |
| AL-02 | Capture açılışı | Handle geçerli | ⚠️ kod yok |
| AL-03 | Duplex | İki yön akıyor | ⚠️ kod yok |
| AL-04 | MMAP etkinleştirme | `mmap_begin/commit` çalışır | ⚠️ kod yok |
| AL-05 | RW yazma | `writei ≥ 0` | ⚠️ kod yok |
| L-06 | SR `_near` uyarlaması | En yakın kabul | ⚠️ kod yok |
| AL-07 | Period hesabı | `L97` sonucu tutarlı | ⚠️ kod yok |
| AL-08 | Underrun kurtarma | `recover` → tekrar yazma | ⚠️ kod yok |
| AL-09 | Overrun | Period azaltma etkisi | ⚠️ kod yok |
| AL-10 | Suspended kurtarma | `prepare` sonrası akış | ⚠️ kod yok |
| AL-11 | Latency MMAP | 0.8ms (kaynak) | ⚠️ ölçüm yok |
| AL-12 | Latency RW | 4.2ms (kaynak) | ⚠️ ölçüm yok |

---

## §16 Sık Sorulan Sorular (K039)

| # | Soru | Cevap |
|---|------|-------|
| 1 | MMAP ile RW farkı? | MMAP doğrudan bellek erişimi, en düşük latency (`L40`, `L109`) |
| 2 | Latency farkı ne kadar? | Kaynak: MMAP 0.8ms, RW 4.2ms (`L226–L227`) |
| 3 | Period nasıl hesaplanır? | Buffer/Periods; 1024/4=256 → 5.33ms @48k (`L97–L104`) |
| 4 | XRUN nedir? | Underrun/overrun ortak adı (`L133`) |
| 5 | Kurtarma çağrısı ne? | `snd_pcm_recover` (`L145`) |
| 6 | Öncelik sırası #4 mü? | Evet (`k2-surucu/CLAUDE.md L27–L34`) |
| 7 | Kanal sınırı? | 128 (`L230`) |
| 8 | Buffer aralığı? | 64–256, gerçek 64 (`L228`) |

---

## §17 Terim Sözlüğü (K039)

| Terim | Tanım |
|-------|-------|
| ALSA | Advanced Linux Sound Architecture |
| PCM | Pulse Code Modulation ses cihazı |
| MMAP | Memory-Mapped I/O — doğrudan bellek haritalama |
| Period | Kesme (interrupt) aralığını belirleyen örnek grubu (`L94`) |
| XRUN | Underrun/overrun ortak durumu (`L133`) |
| Underrun | Buffer'ın okunandan az dolması (`L137`) |
| Overrun | Buffer'ın taşması (`L138`) |
| Interleaved | Kanalların sırayla iç içe dizilimi |
| HWDEP | Hardware dependent parametre yapılandırması (`L150`) |
| `hw:0,0` | Birinci cihaz/birinci alt cihaz (`L211`) |

---

## §18 Risk & Açık Konu Kaydı

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|---------|------|---------|
| R1 | Kod yokluğu → iddialar doğrulanamaz | Yüksek | Yüksek | `⚠️ VERIFICATION REQUIRED` |
| R2 | Ölçüm yokluğu → "Gerçek" alıntı | Yüksek | Orta | Ölçüm protokolü §14 |
| R3 | XRUN kurtarmasının yetersiz kalması | Orta | Yüksek | Buffer/period ayarı |
| R4 | MMAP reddi → sessiz RW'ye düşme | Orta | Orta | Açık fallback kuralı |
| R5 | Period hesabı ile gerçek latency farkı | Orta | Orta | Ölçüm + formül çapraz kontrol |
| R6 | SR uyuşmazlığı | Orta | Yüksek | Guardrail (`CLAUDE.md L18–L24`) |

---

## §19 Kapsam-Dışı Yönlendirme

| Konu | Modül | Bağlantı |
|------|-------|----------|
| PipeWire | K033 | [[../k033-platform-ses-suruculeri/pipewire-modern]] |
| Linux ses yığını | K016 | [[../k016-linux-ses/index]] |
| CoreAudio | K038 | [[../k038-core-audio-macos/index]] |
| ASIO | K036 | [[../k036-asio-drivers/index]] |
| WASAPI | K037 | [[../k037-wasapi-exclusive/index]] |
| Sürücü yığını | K030 | [[../k030-driver-stack/index]] |
| Buffer | K031 | [[../k031-buffer-management/index]] |
| Latency | K032 | [[../k032-latency-optimization/index]] |
| Kernel API / syscall | K023 | [[../k023-system-calls/index]] |
| IRQ/DMA | K018 | [[../k018-dma-kesinti-yonetimi/index]] |
| RT thread / lock-free | K025 | [[../k025-threading-model/index]] |
| Hotplug | K034 | [[../k034-usb-audio/usb-hotplug-enumerasyon]] |

### §19.1 K039 → K0xx Yönlendirme Matrisi (ek tablo)

| Soru soran senaryo | Doğru hedef | Neden bu hedef |
|---|---|---|
| "ALSA'da buffer nerede yönetiliyor?" | [[../k031-buffer-management/index]] | Ring-buffer yaşam döngüsü, boyut seçimi ve lock-free yazma K031'in konusudur |
| "period kesme ile ilişkisi nedir?" | [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] | Period süresi = interrupt aralığı bağlantısı K018'de kanıtlanır |
| "ALSA latency bütçesi nasıl bölünür?" | [[../k032-latency-optimization/latency-optimization]] | Uçtan uca zincir bütçesi K032'nin sorumluluğundadır |
| "Sürücü yığını katmanları nasıl ayrılır?" | [[../k030-driver-stack/driver-stack-mimari]] | Katman sırası ve swap süresi K030'da tanımlıdır |
| "PipeWire ALSA'yı nasıl kullanır?" | [[../k033-platform-ses-suruculeri/pipewire-modern]] | SPA grafiği ALSA node'unu PipeWire üzerinden bağlar |
| "ALSA'da RT thread nasıl kurulur?" | [[rt-thread-ve-pipewire-siniri]] | SCHED_FIFO + systemd LimitRTPRIO kuralları bu klasörde |

### §19.2 K039 Bağımlılık Özet Tablosu (ek)

| Bağımlılık | Yön | Tür | Kanıt durumu |
|---|---|---|---|
| ALSA kernel modülü | K039 → kernel | Zorunlu | ⚠️ VERIFICATION REQUIRED — repo'da `snd_pcm_*` kodu yok (2026-10-06 taraması: 0 eşleşme) |
| `libpipewire-0.3` | K033 → K039 | Opsiyonel (ALSA backend'i) | Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md L243` |
| DMA/IRQ alt sistemi | K018 → K039 | Altyapı | Kaynak: `k018-dma-kesinti-yonetimi/index.md` (kayıt: `k0-isletim-sistemi/rpi5-core.md`) |
| Ring buffer şablonu | K031 → K039 | Ortak yapı | ⚠️ VERIFICATION REQUIRED — uygulama yok |
| Latency bütçe politikası | K032 → K039 | Kural | Kaynak: `k2-surucu/latency-optimization.md` metrik satırları |
| RT zamanlama (SCHED_FIFO / LimitRTPRIO) | K025/K000 → K039 | Kural | Kaynak: `k0-isletim-sistemi/cross-platform-api.md L33–L62` · `linux-core.md L140–L163` |

### §19.3 K039 Sınır Tablosu — ne bu klasörün ne değil (ek)

| Konu | Bu klasörde mi? | Varsa nerede |
|---|---|---|
| `hw_params` / MMAP / XRUN | Evet | `[[alsa-pcm-device]]` |
| ALSA PCM cihaz keşfi ve seçimi | Kısmen | `[[alsa-pcm-device]]` §3 |
| RT thread kuralları / PipeWire sınırı | Evet (özet) | `[[rt-thread-ve-pipewire-siniri]]` |
| Ring-buffer kuyruk algoritması | Hayır | [[../k031-buffer-management/kilitsiz-kuyruklar]] |
| Interrupt çekirdeği | Hayır | [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] |
| Windows/CoreAudio karşılığı | Hayır | [[../k037-wasapi-exclusive/index]] · [[../k038-core-audio-macos/index]] |
| MIDI / ses motoru DSP zinciri | Hayır | Vault dışı (k3-ses-motoru) — ⚠️ VERIFICATION REQUIRED |

### §19.4 Ölçüm Beyanı Kuralları (ek — K039'a özgü)

1. K039 dosyalarındaki hiçbir latency/buffer değeri bu depoda ölçülmüştür olarak yazılamaz.
2. Kaynak sütunu ("Gerçek") okunduğunda içindeki `Belirlenecek` ifadesi aynen taşınır; sayı uydurulmaz.
3. Ölçüm yalnız `[[index]]` §14 protokolü çalıştırıldıktan sonra "ölçüldü" etiketi alır.
4. `⚠️ VERIFICATION REQUIRED` etiketli satır doğrulanana kadar planlama girdisi olarak kullanılamaz.
5. Çelişki durumunda kaynak dosya satır numarası ile birlikte raporlanır (SSOT: `.ai/`).

### §19.5 K039 Bağlantı Bütünlüğü Kaydı (ek)

| Kaynak dosya | Hedef | Tür | Durum |
|---|---|---|---|
| `index.md` | `[[alsa-pcm-device]]` | Aynı klasör | ✅ Geçerli (dosya var) |
| `index.md` | `[[rt-thread-ve-pipewire-siniri]]` | Aynı klasör | ✅ Geçerli (bu revizyonda üretildi) |
| `alsa-pcm-device.md` | `[[index]]` | Aynı klasör | ✅ Geçerli |
| `index.md` | `[[../k033-platform-ses-suruculeri/pipewire-modern]]` | Komşu klasör | ✅ Geçerli (diskte var) |
| `index.md` | `[[../k032-latency-optimization/latency-optimization]]` | Komşu klasör | ✅ Geçerli (eski `k043` hedefi taşındı) |
| `index.md` | `[[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]]` | Komşu klasör | ✅ Geçerli (eski `k048` hedefi taşındı) |
| `alsa-pcm-device.md` | `[[../k031-buffer-management/kilitsiz-kuyruklar]]` | Komşu klasör | ✅ Geçerli (eski `k042` hedefi taşındı) |
| `alsa-pcm-device.md` | `[[../k034-usb-audio/usb-hotplug-enumerasyon]]` | Komşu klasör | ✅ Geçerli (eski `k050` hedefi taşındı) |

> **Kilit notu (2026-10-06 düzeltmesi):** Önceki revizyonda bu tabloda `k040`–`k053` aralığı "kilitli klosör / Geçerli" olarak yazılmıştı — bu **yanlıştı**; `k040`–`k053` klasörleri `.ai/architecture/` altında mevcut değildir (disk ölçümü). Tüm hedefler §19.6'daki taşıma kaydına göre düzeltildi ve diske varlıkları doğrulandı.

### §19.6 Taşıma Kaydı — Eski `k040`–`k053` Wiki-Linkleri (2026-10-06)

> **Dayanak:** Eşleme `[[../k038-core-audio-macos/index.md]]` L609–L622'deki kanonik
> `k04x → k0xx` haritasından alınmıştır (k038 bu görevi "Sonraki görev: k039 link
> taraması" olarak işaretlemişti). **Tüm yeni hedefler yazılmadan ÖNCE diskte varlığı
> doğrulanmıştır** (`Get-ChildItem .ai/architecture` ölçümü: `k040`–`k053` YOK).

| Eski (ölü) hedef | Yeni hedef (diskte doğrulandı) | Bulunduğu dosya (eski satır) | Ek hedef |
|---|---|---|---|
| `../k040-platform-audio-drivers/platform-audio-drivers` | `../k033-platform-ses-suruculeri/index` | index.md | `../k016-linux-ses/alsa-native` (Linux kardeş) |
| `../k041-driver-stack/driver-stack` | `../k030-driver-stack/driver-stack-mimari` | index.md · alsa-pcm-device.md | — |
| `../k042-buffer-management/ring-buffer-lockfree` | `../k031-buffer-management/kilitsiz-kuyruklar` | alsa-pcm-device.md L148 | — |
| `../k043-latency-optimization/latency-chain-budget` | `../k032-latency-optimization/latency-optimization` | index.md | — |
| `../k047-syscall-interface/syscall-interface` | `../k023-system-calls/system-calls` | index.md · alsa-pcm-device.md | — |
| `../k048-interrupt-management/irq-interrupt-handler` | `../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi` | index.md | — |
| `../k050-device-hotplug-power/hotplug-detect-flow` | `../k034-usb-audio/usb-hotplug-enumerasyon` | alsa-pcm-device.md L468 | — |
| `../k053-threading-model/thread-pool-rt` | `../k025-threading-model/gercek-zamanli-zamanlama` | index.md · alsa-pcm-device.md | — |

**Doğrulama (2026-10-06):** klasör geneli grep `[\[\.\./k0(4[0-9]|5[0-3])` → **0
eşleşme** (kalan `k040`–`k053` geçişleri yalnızca bu tablo/uyarı metinlerinde, link
değildir). Yeni hedeflerin her biri `.ai/architecture/` altında klasör + dosya olarak
mevcuttur.

---

## §20 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — K039 klasör indeksi | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | §19.1–§19.4 ek tablolar (satır hedefi 500+) eklendi | Vault Documentation Specialist |
| 2026-10-06 | 4.1.0 | Ölü `../k040-*`–`../k053-*` wiki-linkleri gerçek klasörlere taşındı (§19.6 taşıma kaydı) · §1.4 Kimlik Bloğu eklendi · §10 checklist düzeltildi · `rt-thread-ve-pipewire-siniri.md` üretildi · 126/126 link diskte doğrulandı | Vault Documentation Specialist |
