---
title: "DMA ve Kesinti Yönetimi - k018-dma-kesinti-yonetimi"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# 18. DMA ve Kesinti Yönetimi — `k018-dma-kesinti-yonetimi`

> Dilim: D01 (k018–k035) · Klasör no: 18 · Kategori: mimari · Sürüm: 4.0.0 · Tarih: 2026-10-06
> Sorumlu persona: `dsp-firmware-engineer` (DSP Firmware Mühendisi) · `audio-hardware-engineer` (Audio Hardware Mühendisi)
> Kapsadığı MD: 4 (3 içerik + index) · Kaynak kanıtları: aşağıda dosya bazında `§` + L aralığı ile verilmiştir.

## Genel Bakış

DMA aktarım yolları ve donanım kesintilerinin (IRQ) yönetimi, ölçümü ve doğrulanması.

**Katman bağımlılığı:** K0 çekirdek servisleri (DMA, kesinti), K2 sürücü katmanı, RPi5 DMA engine.

## Klasör Özeti (K Tablosu)

| Numara | Ad | Amaç | Bağımlılık | Sorumlu persona | Kanıt | MD |
|---|---|---|---|---|---|---|
| 1 | DMA Yönetimi | DMA motoru, DMA kanalları ve I2S üzerinden DMA aktarım yolunun tanımı. | K0 çekirdek servisleri (DMA, kesinti), K2 sürücü katmanı, RPi5 DMA engine | dsp-firmware-engineer, audio-hardware-engineer | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` (434 satır) | [[dma-yonetimi.md]] |
| 2 | IRQ ve Kesinti Yöneticisi | Kesinti yönetimi, önceliklendirme ve DMA ile etkileşim. | K0 çekirdek servisleri (DMA, kesinti), K2 sürücü katmanı, RPi5 DMA engine | dsp-firmware-engineer, audio-hardware-engineer | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` (91 satır) | [[irq-kesinti-yoneticisi.md]] |
| 3 | DMA Ölçüm ve Test | DMA transfer gecikmesi ölçümü ve doğrulama test maddeleri. | K0 çekirdek servisleri (DMA, kesinti), K2 sürücü katmanı, RPi5 DMA engine | dsp-firmware-engineer, audio-hardware-engineer | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` (91 satır) | [[dma-olcum-ve-test.md]] |

## Dosya Ayrıntıları ve Kaynak Kanıtları

### DMA Yönetimi — `dma-yonetimi.md`

- **Amaç:** DMA motoru, DMA kanalları ve I2S üzerinden DMA aktarım yolunun tanımı.
- **Persona:** `dsp-firmware-engineer`, `audio-hardware-engineer`
- **Bağımlılık:** K0 çekirdek servisleri (DMA, kesinti), K2 sürücü katmanı, RPi5 DMA engine
- **Wiki-link:** [[dma-yonetimi.md]]
- **Çapraz referanslar:** [[../k022-rpi5-core/rpi5-core.md]] · [[../k031-buffer-management/buffer-management.md]] · [[../k030-driver-stack/driver-stack-mimari.md]]

| Kaynak | § Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Genel Bakış | L12–L14 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Teknik Detaylar | L16–L336 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## API / Arayüz | L338–L381 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Bağımlılıklar | L383–L399 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Performans Metrikleri | L401–L409 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Donanım Notları | L411–L417 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Durum: Implementasyon | L419–L434 | ✓ verbatim |

### IRQ ve Kesinti Yöneticisi — `irq-kesinti-yoneticisi.md`

- **Amaç:** Kesinti yönetimi, önceliklendirme ve DMA ile etkileşim.
- **Persona:** `dsp-firmware-engineer`, `audio-hardware-engineer`
- **Bağımlılık:** K0 çekirdek servisleri (DMA, kesinti), K2 sürücü katmanı, RPi5 DMA engine
- **Wiki-link:** [[irq-kesinti-yoneticisi.md]]
- **Çapraz referanslar:** [[../k022-rpi5-core/rpi5-core.md]] · [[../k031-buffer-management/buffer-management.md]] · [[../k030-driver-stack/driver-stack-mimari.md]]

| Kaynak | § Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Genel Bakış | L10–L12 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Mimari Konum | L14–L20 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Kapsam ve Kategoriler | L22–L39 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Temel İlkeler | L41–L53 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Bağımlılıklar | L55–L61 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Performans Metrikleri | L63–L71 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Dosya Haritası | L73–L87 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Durum: Implementasyon | L89–L91 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Genel Bakış | L12–L14 | — |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Teknik Detaylar | L16–L336 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## API / Arayüz | L338–L381 | — |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Bağımlılıklar | L383–L399 | — |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Performans Metrikleri | L401–L409 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Donanım Notları | L411–L417 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Durum: Implementasyon | L419–L434 | — |

### DMA Ölçüm ve Test — `dma-olcum-ve-test.md`

- **Amaç:** DMA transfer gecikmesi ölçümü ve doğrulama test maddeleri.
- **Persona:** `dsp-firmware-engineer`, `audio-hardware-engineer`
- **Bağımlılık:** K0 çekirdek servisleri (DMA, kesinti), K2 sürücü katmanı, RPi5 DMA engine
- **Wiki-link:** [[dma-olcum-ve-test.md]]
- **Çapraz referanslar:** [[../k022-rpi5-core/rpi5-core.md]] · [[../k031-buffer-management/buffer-management.md]] · [[../k030-driver-stack/driver-stack-mimari.md]]

| Kaynak | § Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Genel Bakış | L10–L12 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Mimari Konum | L14–L20 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Kapsam ve Kategoriler | L22–L39 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Temel İlkeler | L41–L53 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Bağımlılıklar | L55–L61 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Performans Metrikleri | L63–L71 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Dosya Haritası | L73–L87 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Durum: Implementasyon | L89–L91 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Genel Bakış | L10–L12 | — |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Teknik Detaylar | L14–L313 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## API / Arayüz | L315–L359 | — |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Performans Metrikleri | L361–L369 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Bağımlılıklar | L371–L377 | — |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Durum: Implementasyon | L379–L385 | — |

## Dilim Komşuları (k018–k035)

| # | Klasör | Amaç | MD | Index |
|---|---|---|---|---|
| 18 | `k018-dma-kesinti-yonetimi` | DMA aktarım yolları ve donanım kesintilerinin (IRQ) yönetimi, ölçümü ve doğrulanması. | 4 | [[index.md]] |
| 19 | `k019-windows-core` | Windows çekirdek servisleri, Win32/COM API entegrasyonu ve ASIO/WASAPI zemini. | 3 | [[../k019-windows-core/index.md]] |
| 20 | `k020-linux-core` | Linux çekirdek servisleri ve ALSA yerel (native) entegrasyonu. | 3 | [[../k020-linux-core/index.md]] |
| 21 | `k021-macos-core` | macOS çekirdek servisleri ve Core Audio entegrasyonu. | 3 | [[../k021-macos-core/index.md]] |
| 22 | `k022-rpi5-core` | RPi5 çekirdek servisleri, PWM/GPIO ses çıkışı ve I2S/DMA bağlantısı. | 3 | [[../k022-rpi5-core/index.md]] |
| 23 | `k023-system-calls` | Sistem çağrıları (syscall), güvenliği ve hata yönetimi. | 3 | [[../k023-system-calls/index.md]] |
| 24 | `k024-ipc-mekanizmalari` | Süreçler arası iletişim (IPC) mekanizmaları ve performans karşılaştırması. | 3 | [[../k024-ipc-mekanizmalari/index.md]] |
| 25 | `k025-threading-model` | İş parçacığı (thread) modeli ve gerçek zamanlı zamanlama. | 3 | [[../k025-threading-model/index.md]] |
| 26 | `k026-memory-management` | Bellek yönetimi, bellek havuzları ve sızıntı (leak) kontrolü. | 3 | [[../k026-memory-management/index.md]] |
| 27 | `k027-process-isolation` | Süreç izolasyonu, sandbox ve güvenliğin uygulanması. | 3 | [[../k027-process-isolation/index.md]] |
| 28 | `k028-container-runtime` | Konteyner çalışma zamanı ve Docker orkestrasyonu. | 3 | [[../k028-container-runtime/index.md]] |
| 29 | `k029-cross-platform-api` | Çapraz platform API ve platform soyutlama katmanı. | 3 | [[../k029-cross-platform-api/index.md]] |
| 30 | `k030-driver-stack` | Sürücü yığını (driver-stack) mimarisi ve yığın yönetimi. | 3 | [[../k030-driver-stack/index.md]] |
| 31 | `k031-buffer-management` | Ses tamponları (buffer) yönetimi ve kilitsiz kuyruklar. | 3 | [[../k031-buffer-management/index.md]] |
| 32 | `k032-latency-optimization` | Uçtan uca gecikme ölçümü ve ayarlaması (tuning). | 3 | [[../k032-latency-optimization/index.md]] |
| 33 | `k033-platform-ses-suruculeri` | ASIO, WASAPI (exclusive) ve PipeWire sürücü yolları. | 4 | [[../k033-platform-ses-suruculeri/index.md]] |
| 34 | `k034-usb-audio` | USB Audio Class (UAC2), I2S arayüzü ve USB hotplug/enumerasyon. | 4 | [[../k034-usb-audio/index.md]] |
| 35 | `k035-ag-ve-bluetooth-ses` | Ağ üzerinden ses sürücüleri ve Bluetooth A2DP akışı. | 3 | [[../k035-ag-ve-bluetooth-ses/index.md]] |

## Dilim Dosya Envanteri (D01 — 57 MD)

| # | Klasör | Dosya | Amaç | Persona | Link |
|---|---|---|---|---|---|
| 1 | `k018-dma-kesinti-yonetimi` | `dma-yonetimi.md` | DMA motoru, DMA kanalları ve I2S üzerinden DMA aktarım yolunun tanımı. | dsp-firmware-engineer, audio-hardware-engineer | [[dma-yonetimi.md]] |
| 2 | `k018-dma-kesinti-yonetimi` | `irq-kesinti-yoneticisi.md` | Kesinti yönetimi, önceliklendirme ve DMA ile etkileşim. | dsp-firmware-engineer, audio-hardware-engineer | [[irq-kesinti-yoneticisi.md]] |
| 3 | `k018-dma-kesinti-yonetimi` | `dma-olcum-ve-test.md` | DMA transfer gecikmesi ölçümü ve doğrulama test maddeleri. | dsp-firmware-engineer, audio-hardware-engineer | [[dma-olcum-ve-test.md]] |
| 4 | `k018-dma-kesinti-yonetimi` | `index.md` | DMA ve Kesinti Yönetimi klasör özeti ve manifest | dsp-firmware-engineer, audio-hardware-engineer | [[index.md]] |
| 5 | `k019-windows-core` | `windows-core.md` | Windows çekirdek servisleri, mimari konum ve bağımlılıklar. | windows-software-engineer | [[../k019-windows-core/windows-core.md]] |
| 6 | `k019-windows-core` | `windows-api.md` | ASIO SDK, WASAPI, Windows threading, bellek yönetimi ve COM initialization. | windows-software-engineer | [[../k019-windows-core/windows-api.md]] |
| 7 | `k019-windows-core` | `index.md` | Windows Çekirdeği klasör özeti ve manifest | windows-software-engineer | [[../k019-windows-core/index.md]] |
| 8 | `k020-linux-core` | `linux-core.md` | Linux çekirdek servisleri, mimari konum, güvenlik notları ve bağımlılıklar. | embedded-engineer | [[../k020-linux-core/linux-core.md]] |
| 9 | `k020-linux-core` | `alsa-native.md` | ALSA üzerinden yerel ses giriş/çıkışı, API kullanımı ve performans metrikleri. | embedded-engineer | [[../k020-linux-core/alsa-native.md]] |
| 10 | `k020-linux-core` | `index.md` | Linux Çekirdeği klasör özeti ve manifest | embedded-engineer | [[../k020-linux-core/index.md]] |
| 11 | `k021-macos-core` | `macos-core.md` | macOS çekirdek servisleri, hotplug bildirimleri, güvenlik notları ve bağımlılıklar. | embedded-engineer | [[../k021-macos-core/macos-core.md]] |
| 12 | `k021-macos-core` | `core-audio-macos.md` | Core Audio üzerinden cihaz yönetimi, akış ve performans metrikleri. | embedded-engineer | [[../k021-macos-core/core-audio-macos.md]] |
| 13 | `k021-macos-core` | `index.md` | macOS Çekirdeği klasör özeti ve manifest | embedded-engineer | [[../k021-macos-core/index.md]] |
| 14 | `k022-rpi5-core` | `rpi5-core.md` | RPi5 çekirdek servisleri, DMA engine, I2S DMA ve donanım notları. | dsp-firmware-engineer | [[../k022-rpi5-core/rpi5-core.md]] |
| 15 | `k022-rpi5-core` | `rpi5-pwm-gpio-audio.md` | PWM ve GPIO üzerinden ses çıkışı, DMA beslemesi ve ölçüm gereksinimleri. | dsp-firmware-engineer | [[../k022-rpi5-core/rpi5-pwm-gpio-audio.md]] |
| 16 | `k022-rpi5-core` | `index.md` | Raspberry Pi 5 Çekirdeği klasör özeti ve manifest | dsp-firmware-engineer | [[../k022-rpi5-core/index.md]] |
| 17 | `k023-system-calls` | `system-calls.md` | Sistem çağrı arayüzleri, teknik detaylar, API ve performans metrikleri. | embedded-engineer | [[../k023-system-calls/system-calls.md]] |
| 18 | `k023-system-calls` | `syscall-guvenlik-ve-hata.md` | Sistem çağrılarında yetki denetimi, hata sınıflandırması ve kurtarma. | embedded-engineer | [[../k023-system-calls/syscall-guvenlik-ve-hata.md]] |
| 19 | `k023-system-calls` | `index.md` | Sistem Çağrıları klasör özeti ve manifest | embedded-engineer | [[../k023-system-calls/index.md]] |
| 20 | `k024-ipc-mekanizmalari` | `ipc-mekanizmalari.md` | IPC mekanizmalarının teknik detayları, API yüzeyi, güvenlik notları ve metrikleri. | embedded-engineer | [[../k024-ipc-mekanizmalari/ipc-mekanizmalari.md]] |
| 21 | `k024-ipc-mekanizmalari` | `ipc-performans-karsilastirma.md` | IPC mekanizmalarının gecikme/throughput karşılaştırması ve seçim kriterleri. | embedded-engineer | [[../k024-ipc-mekanizmalari/ipc-performans-karsilastirma.md]] |
| 22 | `k024-ipc-mekanizmalari` | `index.md` | IPC Mekanizmaları klasör özeti ve manifest | embedded-engineer | [[../k024-ipc-mekanizmalari/index.md]] |
| 23 | `k025-threading-model` | `threading-model.md` | Thread yaşam döngüsü, senkronizasyon, API yüzeyi ve performans metrikleri. | embedded-engineer | [[../k025-threading-model/threading-model.md]] |
| 24 | `k025-threading-model` | `gercek-zamanli-zamanlama.md` | Gerçek zamanlı zamanlama politikaları, öncelik devri ve deadline yönetimi. | embedded-engineer | [[../k025-threading-model/gercek-zamanli-zamanlama.md]] |
| 25 | `k025-threading-model` | `index.md` | İş Parçacığı Modeli klasör özeti ve manifest | embedded-engineer | [[../k025-threading-model/index.md]] |
| 26 | `k026-memory-management` | `memory-management.md` | Bellek tahsisi, API yüzeyi, performans metrikleri ve güvenlik notları. | embedded-engineer | [[../k026-memory-management/memory-management.md]] |
| 27 | `k026-memory-management` | `bellek-havuzlari-ve-leak.md` | Önceden tahsisli havuzlar, tekrar kullanım ve sızıntı tespit stratejisi. | embedded-engineer | [[../k026-memory-management/bellek-havuzlari-ve-leak.md]] |
| 28 | `k026-memory-management` | `index.md` | Bellek Yönetimi klasör özeti ve manifest | embedded-engineer | [[../k026-memory-management/index.md]] |
| 29 | `k027-process-isolation` | `process-isolation.md` | Süreç izolasyon teknikleri, API yüzeyi, güvenlik notları ve metrikler. | security-engineer | [[../k027-process-isolation/process-isolation.md]] |
| 30 | `k027-process-isolation` | `sandbox-ve-guvenlik.md` | Sandbox katmanı, yetki indirgeme ve sürücü süreçlerinin karantınaya alınması. | security-engineer | [[../k027-process-isolation/sandbox-ve-guvenlik.md]] |
| 31 | `k027-process-isolation` | `index.md` | Süreç İzolasyonu klasör özeti ve manifest | security-engineer | [[../k027-process-isolation/index.md]] |
| 32 | `k028-container-runtime` | `container-runtime.md` | Konteyner çalışma zamanı mimarisi, API, güvenlik notları ve metrikler. | devops-engineer | [[../k028-container-runtime/container-runtime.md]] |
| 33 | `k028-container-runtime` | `docker-orkestrasyon.md` | Docker ile servis orkestrasyonu, ağ ve kaynak limitleri. | devops-engineer | [[../k028-container-runtime/docker-orkestrasyon.md]] |
| 34 | `k028-container-runtime` | `index.md` | Konteyner Çalışma Zamanı klasör özeti ve manifest | devops-engineer | [[../k028-container-runtime/index.md]] |
| 35 | `k029-cross-platform-api` | `cross-platform-api.md` | Platformlar arası ortak API yüzeyi, teknik detaylar ve bağımlılıklar. | embedded-engineer | [[../k029-cross-platform-api/cross-platform-api.md]] |
| 36 | `k029-cross-platform-api` | `platform-soyutlama-katmani.md` | Soyutlama katmanının sınırı, platform-specific kaçakların denetimi. | embedded-engineer | [[../k029-cross-platform-api/platform-soyutlama-katmani.md]] |
| 37 | `k029-cross-platform-api` | `index.md` | Çapraz Platform API klasör özeti ve manifest | embedded-engineer | [[../k029-cross-platform-api/index.md]] |
| 38 | `k030-driver-stack` | `driver-stack-mimari.md` | Sürücü yığınının katmanları, API yüzeyi ve performans metrikleri. | embedded-engineer | [[../k030-driver-stack/driver-stack-mimari.md]] |
| 39 | `k030-driver-stack` | `surucu-yigini-yonetimi.md` | Yığın üzerinde yükleme, güncelleme, hata izolasyonu ve sıcak değişim. | embedded-engineer | [[../k030-driver-stack/surucu-yigini-yonetimi.md]] |
| 40 | `k030-driver-stack` | `index.md` | Sürücü Yığını klasör özeti ve manifest | embedded-engineer | [[../k030-driver-stack/index.md]] |
| 41 | `k031-buffer-management` | `buffer-management.md` | Tampon boyutları, doldurma/boşaltma döngüsü ve performans metrikleri. | embedded-engineer | [[../k031-buffer-management/buffer-management.md]] |
| 42 | `k031-buffer-management` | `kilitsiz-kuyruklar.md` | Lock-free SPSC/MPMC kuyruklar, bellek bariyerleri ve ölçümler. | embedded-engineer | [[../k031-buffer-management/kilitsiz-kuyruklar.md]] |
| 43 | `k031-buffer-management` | `index.md` | Buffer Yönetimi klasör özeti ve manifest | embedded-engineer | [[../k031-buffer-management/index.md]] |
| 44 | `k032-latency-optimization` | `latency-optimization.md` | Gecikme kaynakları, teknik detaylar ve optimizasyon kaldıraçları. | embedded-engineer | [[../k032-latency-optimization/latency-optimization.md]] |
| 45 | `k032-latency-optimization` | `gecikme-olcum-ve-tuning.md` | Round-trip ölçüm yöntemi, hedef metrikler ve ayarlama döngüsü. | embedded-engineer | [[../k032-latency-optimization/gecikme-olcum-ve-tuning.md]] |
| 46 | `k032-latency-optimization` | `index.md` | Gecikme Optimizasyonu klasör özeti ve manifest | embedded-engineer | [[../k032-latency-optimization/index.md]] |
| 47 | `k033-platform-ses-suruculeri` | `asio-drivers.md` | ASIO sürücü yolu, SDK entegrasyonu ve exclusive-mode davranışı. | windows-software-engineer, embedded-engineer | [[../k033-platform-ses-suruculeri/asio-drivers.md]] |
| 48 | `k033-platform-ses-suruculeri` | `wasapi-exclusive.md` | WASAPI exclusive ve shared modlar, akış yönetimi, metrikler. | windows-software-engineer, embedded-engineer | [[../k033-platform-ses-suruculeri/wasapi-exclusive.md]] |
| 49 | `k033-platform-ses-suruculeri` | `pipewire-modern.md` | PipeWire ile modern Linux ses yolu, ALSA köprüsü ve metrikler. | windows-software-engineer, embedded-engineer | [[../k033-platform-ses-suruculeri/pipewire-modern.md]] |
| 50 | `k033-platform-ses-suruculeri` | `index.md` | Platform Ses Sürücüleri klasör özeti ve manifest | windows-software-engineer, embedded-engineer | [[../k033-platform-ses-suruculeri/index.md]] |
| 51 | `k034-usb-audio` | `usb-audio-class.md` | UAC2 descriptor yapısı, isochronous transfer ve örnek hızı desteği. | dsp-firmware-engineer | [[../k034-usb-audio/usb-audio-class.md]] |
| 52 | `k034-usb-audio` | `i2s-interface.md` | I2S sinyalleri, timing, master/slave ve çoklu kanal yapılandırması. | dsp-firmware-engineer | [[../k034-usb-audio/i2s-interface.md]] |
| 53 | `k034-usb-audio` | `usb-hotplug-enumerasyon.md` | Sıcak tak-çıkar (hotplug) olayları, enumerasyon akışı ve sürücü yüklemesi. | dsp-firmware-engineer | [[../k034-usb-audio/usb-hotplug-enumerasyon.md]] |
| 54 | `k034-usb-audio` | `index.md` | USB Ses klasör özeti ve manifest | dsp-firmware-engineer | [[../k034-usb-audio/index.md]] |
| 55 | `k035-ag-ve-bluetooth-ses` | `network-audio-drivers.md` | Ağ üzerinden ses aktarımının sürücü tarafı, paketleme ve metrikler. | embedded-engineer | [[../k035-ag-ve-bluetooth-ses/network-audio-drivers.md]] |
| 56 | `k035-ag-ve-bluetooth-ses` | `bluetooth-a2dp.md` | A2DP profili, codec müzakeresi, gecikme ve sürücü davranışı. | embedded-engineer | [[../k035-ag-ve-bluetooth-ses/bluetooth-a2dp.md]] |
| 57 | `k035-ag-ve-bluetooth-ses` | `index.md` | Ağ ve Bluetooth Ses klasör özeti ve manifest | embedded-engineer | [[../k035-ag-ve-bluetooth-ses/index.md]] |

## Persona Ataması

| Persona | Rol | Dosyalar |
|---|---|---|
| `dsp-firmware-engineer` | DSP Firmware Mühendisi | [[dma-yonetimi.md]], [[irq-kesinti-yoneticisi.md]], [[dma-olcum-ve-test.md]] |
| `audio-hardware-engineer` | Audio Hardware Mühendisi | [[dma-yonetimi.md]], [[irq-kesinti-yoneticisi.md]], [[dma-olcum-ve-test.md]] |

## Kaynak Kanıtları (bu klasör)

| Kaynak dosya | Toplam satır | # Bölüm | Kullanım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | 434 | 7 | `dma-yonetimi.md`, `irq-kesinti-yoneticisi.md` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | 91 | 8 | `irq-kesinti-yoneticisi.md`, `dma-olcum-ve-test.md` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | 385 | 6 | `dma-olcum-ve-test.md` |

## Terminoloji Çapası (D01 kanonik identifier'ları)

> Sayımlar `${BK}/{k0-isletim-sistemi,k1-donanim,k2-surucu}` klasörlerindeki tüm `.md` kaynakları üzerinde tam tarama ile üretildi.

| Identifier | Geçiş sayısı | İlk kanıt |
|---|---|---|
| `ALSA` | 65 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L576 |
| `WASAPI` | 67 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` L45 |
| `ASIO` | 124 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L13 |
| `CoreAudio` | 43 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` L47 |
| `PipeWire` | 57 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L577 |
| `DMA` | 55 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` L54 |
| `IRQ` | 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L59 |
| `UAC2` | 21 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` L784 |
| `I2S` | 132 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` L54 |
| `io_uring` | 53 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` L61 |
| `epoll` | 69 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` L52 |
| `kqueue` | 40 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` L61 |
| `hotplug` | 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L310 |
| `firmware` | 156 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` L67 |
| `driver-stack` | 16 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L85 |
| `A2DP` | 30 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L2 |

## Doğrulama Notları

- **Frontmatter 7 alan:** title, type, category, version, status, authority, updated (üretici tarafından sayılır).
- **Satır hedefi:** her D01 MD ≥ 501 satır (üretim sonrası betik ile kontrol edilir).
- **Wiki-link:** bu dosyadaki tüm wiki-link hedefleri D01 manifestine (57 hedef) kayıtlıdır; yazım sonrası hedef dosya varlığı doğrulanır.
- **Kanıt kuralı:** her aktarım satırı gerçek backup yolunu + `§` + L aralığını taşır; kanıtsız iddia yazılmaz.
- **Çözülmemiş çelişki:** bu klasör için tespit edilmedi.
- ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault yedeği kanıtına dayanır; canlı uygulama kodu ile karşılaştırma yapılmamıştır.
- ⚠️ VERIFICATION REQUIRED — `Belirlenecek` / `UNKNOWN` / `TODO` içeren kaynak satırları korunmuş, üst merci kararı beklenmektedir.

## Ek 1: Wiki-Link Hedef Çizelgesi

> Bu index'in ana bölümlerinde geçen benzersiz wiki-link'ler ve D01 manifestindeki karşılıkları (Ek 2 eklenmeden önceki metin üzerinden taranmıştır; Ek 2'deki linkler de aynı manifest kümelerinden üretilir).

| # | Wiki-link | Resolve edilen hedef | Manifestte |
|---|---|---|---|
| 1 | `[[dma-yonetimi.md]]` | `k018-dma-kesinti-yonetimi/dma-yonetimi.md` | ✓ |
| 2 | `[[irq-kesinti-yoneticisi.md]]` | `k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi.md` | ✓ |
| 3 | `[[dma-olcum-ve-test.md]]` | `k018-dma-kesinti-yonetimi/dma-olcum-ve-test.md` | ✓ |
| 4 | `[[../k022-rpi5-core/rpi5-core.md]]` | `k022-rpi5-core/rpi5-core.md` | ✓ |
| 5 | `[[../k031-buffer-management/buffer-management.md]]` | `k031-buffer-management/buffer-management.md` | ✓ |
| 6 | `[[../k030-driver-stack/driver-stack-mimari.md]]` | `k030-driver-stack/driver-stack-mimari.md` | ✓ |
| 7 | `[[index.md]]` | `k018-dma-kesinti-yonetimi/index.md` | ✓ |
| 8 | `[[../k019-windows-core/index.md]]` | `k019-windows-core/index.md` | ✓ |
| 9 | `[[../k020-linux-core/index.md]]` | `k020-linux-core/index.md` | ✓ |
| 10 | `[[../k021-macos-core/index.md]]` | `k021-macos-core/index.md` | ✓ |
| 11 | `[[../k022-rpi5-core/index.md]]` | `k022-rpi5-core/index.md` | ✓ |
| 12 | `[[../k023-system-calls/index.md]]` | `k023-system-calls/index.md` | ✓ |
| 13 | `[[../k024-ipc-mekanizmalari/index.md]]` | `k024-ipc-mekanizmalari/index.md` | ✓ |
| 14 | `[[../k025-threading-model/index.md]]` | `k025-threading-model/index.md` | ✓ |
| 15 | `[[../k026-memory-management/index.md]]` | `k026-memory-management/index.md` | ✓ |
| 16 | `[[../k027-process-isolation/index.md]]` | `k027-process-isolation/index.md` | ✓ |
| 17 | `[[../k028-container-runtime/index.md]]` | `k028-container-runtime/index.md` | ✓ |
| 18 | `[[../k029-cross-platform-api/index.md]]` | `k029-cross-platform-api/index.md` | ✓ |
| 19 | `[[../k030-driver-stack/index.md]]` | `k030-driver-stack/index.md` | ✓ |
| 20 | `[[../k031-buffer-management/index.md]]` | `k031-buffer-management/index.md` | ✓ |
| 21 | `[[../k032-latency-optimization/index.md]]` | `k032-latency-optimization/index.md` | ✓ |
| 22 | `[[../k033-platform-ses-suruculeri/index.md]]` | `k033-platform-ses-suruculeri/index.md` | ✓ |
| 23 | `[[../k034-usb-audio/index.md]]` | `k034-usb-audio/index.md` | ✓ |
| 24 | `[[../k035-ag-ve-bluetooth-ses/index.md]]` | `k035-ag-ve-bluetooth-ses/index.md` | ✓ |
| 25 | `[[../k019-windows-core/windows-core.md]]` | `k019-windows-core/windows-core.md` | ✓ |
| 26 | `[[../k019-windows-core/windows-api.md]]` | `k019-windows-core/windows-api.md` | ✓ |
| 27 | `[[../k020-linux-core/linux-core.md]]` | `k020-linux-core/linux-core.md` | ✓ |
| 28 | `[[../k020-linux-core/alsa-native.md]]` | `k020-linux-core/alsa-native.md` | ✓ |
| 29 | `[[../k021-macos-core/macos-core.md]]` | `k021-macos-core/macos-core.md` | ✓ |
| 30 | `[[../k021-macos-core/core-audio-macos.md]]` | `k021-macos-core/core-audio-macos.md` | ✓ |
| 31 | `[[../k022-rpi5-core/rpi5-pwm-gpio-audio.md]]` | `k022-rpi5-core/rpi5-pwm-gpio-audio.md` | ✓ |
| 32 | `[[../k023-system-calls/system-calls.md]]` | `k023-system-calls/system-calls.md` | ✓ |
| 33 | `[[../k023-system-calls/syscall-guvenlik-ve-hata.md]]` | `k023-system-calls/syscall-guvenlik-ve-hata.md` | ✓ |
| 34 | `[[../k024-ipc-mekanizmalari/ipc-mekanizmalari.md]]` | `k024-ipc-mekanizmalari/ipc-mekanizmalari.md` | ✓ |
| 35 | `[[../k024-ipc-mekanizmalari/ipc-performans-karsilastirma.md]]` | `k024-ipc-mekanizmalari/ipc-performans-karsilastirma.md` | ✓ |
| 36 | `[[../k025-threading-model/threading-model.md]]` | `k025-threading-model/threading-model.md` | ✓ |
| 37 | `[[../k025-threading-model/gercek-zamanli-zamanlama.md]]` | `k025-threading-model/gercek-zamanli-zamanlama.md` | ✓ |
| 38 | `[[../k026-memory-management/memory-management.md]]` | `k026-memory-management/memory-management.md` | ✓ |
| 39 | `[[../k026-memory-management/bellek-havuzlari-ve-leak.md]]` | `k026-memory-management/bellek-havuzlari-ve-leak.md` | ✓ |
| 40 | `[[../k027-process-isolation/process-isolation.md]]` | `k027-process-isolation/process-isolation.md` | ✓ |
| 41 | `[[../k027-process-isolation/sandbox-ve-guvenlik.md]]` | `k027-process-isolation/sandbox-ve-guvenlik.md` | ✓ |
| 42 | `[[../k028-container-runtime/container-runtime.md]]` | `k028-container-runtime/container-runtime.md` | ✓ |
| 43 | `[[../k028-container-runtime/docker-orkestrasyon.md]]` | `k028-container-runtime/docker-orkestrasyon.md` | ✓ |
| 44 | `[[../k029-cross-platform-api/cross-platform-api.md]]` | `k029-cross-platform-api/cross-platform-api.md` | ✓ |
| 45 | `[[../k029-cross-platform-api/platform-soyutlama-katmani.md]]` | `k029-cross-platform-api/platform-soyutlama-katmani.md` | ✓ |
| 46 | `[[../k030-driver-stack/surucu-yigini-yonetimi.md]]` | `k030-driver-stack/surucu-yigini-yonetimi.md` | ✓ |
| 47 | `[[../k031-buffer-management/kilitsiz-kuyruklar.md]]` | `k031-buffer-management/kilitsiz-kuyruklar.md` | ✓ |
| 48 | `[[../k032-latency-optimization/latency-optimization.md]]` | `k032-latency-optimization/latency-optimization.md` | ✓ |
| 49 | `[[../k032-latency-optimization/gecikme-olcum-ve-tuning.md]]` | `k032-latency-optimization/gecikme-olcum-ve-tuning.md` | ✓ |
| 50 | `[[../k033-platform-ses-suruculeri/asio-drivers.md]]` | `k033-platform-ses-suruculeri/asio-drivers.md` | ✓ |
| 51 | `[[../k033-platform-ses-suruculeri/wasapi-exclusive.md]]` | `k033-platform-ses-suruculeri/wasapi-exclusive.md` | ✓ |
| 52 | `[[../k033-platform-ses-suruculeri/pipewire-modern.md]]` | `k033-platform-ses-suruculeri/pipewire-modern.md` | ✓ |
| 53 | `[[../k034-usb-audio/usb-audio-class.md]]` | `k034-usb-audio/usb-audio-class.md` | ✓ |
| 54 | `[[../k034-usb-audio/i2s-interface.md]]` | `k034-usb-audio/i2s-interface.md` | ✓ |
| 55 | `[[../k034-usb-audio/usb-hotplug-enumerasyon.md]]` | `k034-usb-audio/usb-hotplug-enumerasyon.md` | ✓ |
| 56 | `[[../k035-ag-ve-bluetooth-ses/network-audio-drivers.md]]` | `k035-ag-ve-bluetooth-ses/network-audio-drivers.md` | ✓ |
| 57 | `[[../k035-ag-ve-bluetooth-ses/bluetooth-a2dp.md]]` | `k035-ag-ve-bluetooth-ses/bluetooth-a2dp.md` | ✓ |

## Ek 2: D01 Kapsam Kartları (57 MD)

### k018-dma-kesinti-yonetimi — DMA aktarım yolları ve donanım kesintilerinin (IRQ) yönetimi, ölçümü ve doğrulanması.

- **Bağımlılık:** K0 çekirdek servisleri (DMA, kesinti), K2 sürücü katmanı, RPi5 DMA engine
- **Persona:** `dsp-firmware-engineer`, `audio-hardware-engineer`
  - [[dma-yonetimi.md]] — **DMA Yönetimi**: DMA motoru, DMA kanalları ve I2S üzerinden DMA aktarım yolunun tanımı.
  - [[irq-kesinti-yoneticisi.md]] — **IRQ ve Kesinti Yöneticisi**: Kesinti yönetimi, önceliklendirme ve DMA ile etkileşim.
  - [[dma-olcum-ve-test.md]] — **DMA Ölçüm ve Test**: DMA transfer gecikmesi ölçümü ve doğrulama test maddeleri.
  - [[index.md]] — **Klasör Index**: DMA ve Kesinti Yönetimi klasör özeti ve manifest

### k019-windows-core — Windows çekirdek servisleri, Win32/COM API entegrasyonu ve ASIO/WASAPI zemini.

- **Bağımlılık:** K0 Windows çekirdek servisleri, COM, Win32 API
- **Persona:** `windows-software-engineer`
  - [[../k019-windows-core/windows-core.md]] — **Windows Çekirdeği**: Windows çekirdek servisleri, mimari konum ve bağımlılıklar.
  - [[../k019-windows-core/windows-api.md]] — **Windows API Entegrasyonu**: ASIO SDK, WASAPI, Windows threading, bellek yönetimi ve COM initialization.
  - [[../k019-windows-core/index.md]] — **Klasör Index**: Windows Çekirdeği klasör özeti ve manifest

### k020-linux-core — Linux çekirdek servisleri ve ALSA yerel (native) entegrasyonu.

- **Bağımlılık:** K0 Linux çekirdek servisleri, ALSA, epoll/io_uring
- **Persona:** `embedded-engineer`
  - [[../k020-linux-core/linux-core.md]] — **Linux Çekirdeği**: Linux çekirdek servisleri, mimari konum, güvenlik notları ve bağımlılıklar.
  - [[../k020-linux-core/alsa-native.md]] — **ALSA Yerel Entegrasyonu**: ALSA üzerinden yerel ses giriş/çıkışı, API kullanımı ve performans metrikleri.
  - [[../k020-linux-core/index.md]] — **Klasör Index**: Linux Çekirdeği klasör özeti ve manifest

### k021-macos-core — macOS çekirdek servisleri ve Core Audio entegrasyonu.

- **Bağımlılık:** K0 macOS çekirdek servisleri, Core Audio, IOKit hotplug
- **Persona:** `embedded-engineer`
  - [[../k021-macos-core/macos-core.md]] — **macOS Çekirdeği**: macOS çekirdek servisleri, hotplug bildirimleri, güvenlik notları ve bağımlılıklar.
  - [[../k021-macos-core/core-audio-macos.md]] — **Core Audio Entegrasyonu**: Core Audio üzerinden cihaz yönetimi, akış ve performans metrikleri.
  - [[../k021-macos-core/index.md]] — **Klasör Index**: macOS Çekirdeği klasör özeti ve manifest

### k022-rpi5-core — RPi5 çekirdek servisleri, PWM/GPIO ses çıkışı ve I2S/DMA bağlantısı.

- **Bağımlılık:** K0 RPi5 çekirdek servisleri, BCM2712 DMA/PWM/GPIO, K1 I2S arayüzü
- **Persona:** `dsp-firmware-engineer`
  - [[../k022-rpi5-core/rpi5-core.md]] — **Raspberry Pi 5 Çekirdeği**: RPi5 çekirdek servisleri, DMA engine, I2S DMA ve donanım notları.
  - [[../k022-rpi5-core/rpi5-pwm-gpio-audio.md]] — **RPI5 PWM/GPIO Ses**: PWM ve GPIO üzerinden ses çıkışı, DMA beslemesi ve ölçüm gereksinimleri.
  - [[../k022-rpi5-core/index.md]] — **Klasör Index**: Raspberry Pi 5 Çekirdeği klasör özeti ve manifest

### k023-system-calls — Sistem çağrıları (syscall), güvenliği ve hata yönetimi.

- **Bağımlılık:** K0 çekirdek syscall arayüzleri, sandbox mekanizmaları
- **Persona:** `embedded-engineer`
  - [[../k023-system-calls/system-calls.md]] — **Sistem Çağrıları**: Sistem çağrı arayüzleri, teknik detaylar, API ve performans metrikleri.
  - [[../k023-system-calls/syscall-guvenlik-ve-hata.md]] — **Syscall Güvenlik ve Hata Yönetimi**: Sistem çağrılarında yetki denetimi, hata sınıflandırması ve kurtarma.
  - [[../k023-system-calls/index.md]] — **Klasör Index**: Sistem Çağrıları klasör özeti ve manifest

### k024-ipc-mekanizmalari — Süreçler arası iletişim (IPC) mekanizmaları ve performans karşılaştırması.

- **Bağımlılık:** K0 çekirdek IPC primitives (pipe, socket, shared memory)
- **Persona:** `embedded-engineer`
  - [[../k024-ipc-mekanizmalari/ipc-mekanizmalari.md]] — **IPC Mekanizmaları**: IPC mekanizmalarının teknik detayları, API yüzeyi, güvenlik notları ve metrikleri.
  - [[../k024-ipc-mekanizmalari/ipc-performans-karsilastirma.md]] — **IPC Performans Karşılaştırması**: IPC mekanizmalarının gecikme/throughput karşılaştırması ve seçim kriterleri.
  - [[../k024-ipc-mekanizmalari/index.md]] — **Klasör Index**: IPC Mekanizmaları klasör özeti ve manifest

### k025-threading-model — İş parçacığı (thread) modeli ve gerçek zamanlı zamanlama.

- **Bağımlılık:** K0 çekirdek zamanlayıcıları, POSIX threads / Win32 threads
- **Persona:** `embedded-engineer`
  - [[../k025-threading-model/threading-model.md]] — **İş Parçacığı Modeli**: Thread yaşam döngüsü, senkronizasyon, API yüzeyi ve performans metrikleri.
  - [[../k025-threading-model/gercek-zamanli-zamanlama.md]] — **Gerçek Zamanlı Zamanlama**: Gerçek zamanlı zamanlama politikaları, öncelik devri ve deadline yönetimi.
  - [[../k025-threading-model/index.md]] — **Klasör Index**: İş Parçacığı Modeli klasör özeti ve manifest

### k026-memory-management — Bellek yönetimi, bellek havuzları ve sızıntı (leak) kontrolü.

- **Bağımlılık:** K0 çekirdek bellek yönetimi (malloc/mmap), havuz (pool) katmanı
- **Persona:** `embedded-engineer`
  - [[../k026-memory-management/memory-management.md]] — **Bellek Yönetimi**: Bellek tahsisi, API yüzeyi, performans metrikleri ve güvenlik notları.
  - [[../k026-memory-management/bellek-havuzlari-ve-leak.md]] — **Bellek Havuzları ve Leak**: Önceden tahsisli havuzlar, tekrar kullanım ve sızıntı tespit stratejisi.
  - [[../k026-memory-management/index.md]] — **Klasör Index**: Bellek Yönetimi klasör özeti ve manifest

### k027-process-isolation — Süreç izolasyonu, sandbox ve güvenliğin uygulanması.

- **Bağımlılık:** K0 süreç izolasyon mekanizmaları (namespace, seccomp), K2 sürücü süreci
- **Persona:** `security-engineer`
  - [[../k027-process-isolation/process-isolation.md]] — **Süreç İzolasyonu**: Süreç izolasyon teknikleri, API yüzeyi, güvenlik notları ve metrikler.
  - [[../k027-process-isolation/sandbox-ve-guvenlik.md]] — **Sandbox ve Güvenlik**: Sandbox katmanı, yetki indirgeme ve sürücü süreçlerinin karantınaya alınması.
  - [[../k027-process-isolation/index.md]] — **Klasör Index**: Süreç İzolasyonu klasör özeti ve manifest

### k028-container-runtime — Konteyner çalışma zamanı ve Docker orkestrasyonu.

- **Bağımlılık:** K0 konteyner çalışma zamanı, K13 CI/CD ile orkestrasyon (vault dışı bağımlılık)
- **Persona:** `devops-engineer`
  - [[../k028-container-runtime/container-runtime.md]] — **Konteyner Çalışma Zamanı**: Konteyner çalışma zamanı mimarisi, API, güvenlik notları ve metrikler.
  - [[../k028-container-runtime/docker-orkestrasyon.md]] — **Docker Orkestrasyonu**: Docker ile servis orkestrasyonu, ağ ve kaynak limitleri.
  - [[../k028-container-runtime/index.md]] — **Klasör Index**: Konteyner Çalışma Zamanı klasör özeti ve manifest

### k029-cross-platform-api — Çapraz platform API ve platform soyutlama katmanı.

- **Bağımlılık:** K0 platform çekirdekleri (Windows, Linux, macOS, RPi5)
- **Persona:** `embedded-engineer`
  - [[../k029-cross-platform-api/cross-platform-api.md]] — **Çapraz Platform API**: Platformlar arası ortak API yüzeyi, teknik detaylar ve bağımlılıklar.
  - [[../k029-cross-platform-api/platform-soyutlama-katmani.md]] — **Platform Soyutlama Katmanı**: Soyutlama katmanının sınırı, platform-specific kaçakların denetimi.
  - [[../k029-cross-platform-api/index.md]] — **Klasör Index**: Çapraz Platform API klasör özeti ve manifest

### k030-driver-stack — Sürücü yığını (driver-stack) mimarisi ve yığın yönetimi.

- **Bağımlılık:** K0 çekirdek servisleri, K1 donanım, K2 sürücü katmanı
- **Persona:** `embedded-engineer`
  - [[../k030-driver-stack/driver-stack-mimari.md]] — **Sürücü Yığını Mimarisi**: Sürücü yığınının katmanları, API yüzeyi ve performans metrikleri.
  - [[../k030-driver-stack/surucu-yigini-yonetimi.md]] — **Sürücü Yığını Yönetimi**: Yığın üzerinde yükleme, güncelleme, hata izolasyonu ve sıcak değişim.
  - [[../k030-driver-stack/index.md]] — **Klasör Index**: Sürücü Yığını klasör özeti ve manifest

### k031-buffer-management — Ses tamponları (buffer) yönetimi ve kilitsiz kuyruklar.

- **Bağımlılık:** K0 bellek yönetimi, K2 sürücü yığını, K3 ses motoru akışı
- **Persona:** `embedded-engineer`
  - [[../k031-buffer-management/buffer-management.md]] — **Buffer Yönetimi**: Tampon boyutları, doldurma/boşaltma döngüsü ve performans metrikleri.
  - [[../k031-buffer-management/kilitsiz-kuyruklar.md]] — **Kilitsiz Kuyruklar**: Lock-free SPSC/MPMC kuyruklar, bellek bariyerleri ve ölçümler.
  - [[../k031-buffer-management/index.md]] — **Klasör Index**: Buffer Yönetimi klasör özeti ve manifest

### k032-latency-optimization — Uçtan uca gecikme ölçümü ve ayarlaması (tuning).

- **Bağımlılık:** K2 sürücü yığını, buffer yönetimi, K0 zamanlayıcılar
- **Persona:** `embedded-engineer`
  - [[../k032-latency-optimization/latency-optimization.md]] — **Gecikme Optimizasyonu**: Gecikme kaynakları, teknik detaylar ve optimizasyon kaldıraçları.
  - [[../k032-latency-optimization/gecikme-olcum-ve-tuning.md]] — **Gecikme Ölçüm ve Tuning**: Round-trip ölçüm yöntemi, hedef metrikler ve ayarlama döngüsü.
  - [[../k032-latency-optimization/index.md]] — **Klasör Index**: Gecikme Optimizasyonu klasör özeti ve manifest

### k033-platform-ses-suruculeri — ASIO, WASAPI (exclusive) ve PipeWire sürücü yolları.

- **Bağımlılık:** K0 Windows/Linux çekirdekleri, K2 sürücü yığını
- **Persona:** `windows-software-engineer`, `embedded-engineer`
  - [[../k033-platform-ses-suruculeri/asio-drivers.md]] — **ASIO Sürücüleri**: ASIO sürücü yolu, SDK entegrasyonu ve exclusive-mode davranışı.
  - [[../k033-platform-ses-suruculeri/wasapi-exclusive.md]] — **WASAPI Exclusive**: WASAPI exclusive ve shared modlar, akış yönetimi, metrikler.
  - [[../k033-platform-ses-suruculeri/pipewire-modern.md]] — **PipeWire Modern Ses Yolu**: PipeWire ile modern Linux ses yolu, ALSA köprüsü ve metrikler.
  - [[../k033-platform-ses-suruculeri/index.md]] — **Klasör Index**: Platform Ses Sürücüleri klasör özeti ve manifest

### k034-usb-audio — USB Audio Class (UAC2), I2S arayüzü ve USB hotplug/enumerasyon.

- **Bağımlılık:** K1 USB/I2S donanım arayüzleri, K0 USB yığını, K2 sürücü katmanı
- **Persona:** `dsp-firmware-engineer`
  - [[../k034-usb-audio/usb-audio-class.md]] — **USB Audio Class (UAC2)**: UAC2 descriptor yapısı, isochronous transfer ve örnek hızı desteği.
  - [[../k034-usb-audio/i2s-interface.md]] — **I2S Arayüzü**: I2S sinyalleri, timing, master/slave ve çoklu kanal yapılandırması.
  - [[../k034-usb-audio/usb-hotplug-enumerasyon.md]] — **USB Hotplug ve Enumerasyon**: Sıcak tak-çıkar (hotplug) olayları, enumerasyon akışı ve sürücü yüklemesi.
  - [[../k034-usb-audio/index.md]] — **Klasör Index**: USB Ses klasör özeti ve manifest

### k035-ag-ve-bluetooth-ses — Ağ üzerinden ses sürücüleri ve Bluetooth A2DP akışı.

- **Bağımlılık:** K2 sürücü katmanı, ağ yığını, Bluetooth/USB radyo donanımı
- **Persona:** `embedded-engineer`
  - [[../k035-ag-ve-bluetooth-ses/network-audio-drivers.md]] — **Ağ Ses Sürücüleri**: Ağ üzerinden ses aktarımının sürücü tarafı, paketleme ve metrikler.
  - [[../k035-ag-ve-bluetooth-ses/bluetooth-a2dp.md]] — **Bluetooth A2DP**: A2DP profili, codec müzakeresi, gecikme ve sürücü davranışı.
  - [[../k035-ag-ve-bluetooth-ses/index.md]] — **Klasör Index**: Ağ ve Bluetooth Ses klasör özeti ve manifest

## Ek 3: D01 Kaynak Envanteri (k0/k1/k2)

| Kaynak dosya | Satır | # Bölüm | D01'de kullanan klasörler |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | 511 | 7 | `k026-memory-management`, `k027-process-isolation`, `k028-container-runtime` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | 658 | 6 | `k028-container-runtime`, `k029-cross-platform-api` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | 658 | 7 | `k024-ipc-mekanizmalari`, `k031-buffer-management` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | 617 | 7 | `k020-linux-core`, `k029-cross-platform-api` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | 557 | 7 | `k021-macos-core`, `k034-usb-audio` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/memory-management.md` | 553 | 6 | `k026-memory-management` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | 629 | 7 | `k023-system-calls`, `k027-process-isolation` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | 434 | 7 | `k018-dma-kesinti-yonetimi`, `k022-rpi5-core`, `k034-usb-audio` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | 692 | 6 | `k023-system-calls`, `k025-threading-model` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | 736 | 6 | `k024-ipc-mekanizmalari`, `k025-threading-model` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | 265 | 6 | `k019-windows-core`, `k033-platform-ses-suruculeri` |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | 422 | 7 | `k019-windows-core` |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | 186 | 10 | `k022-rpi5-core`, `k034-usb-audio` |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | 190 | 11 | `k034-usb-audio` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | 246 | 6 | `k020-linux-core`, `k033-platform-ses-suruculeri` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | 179 | 6 | `k033-platform-ses-suruculeri` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | 250 | 6 | `k035-ag-ve-bluetooth-ses` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | 381 | 6 | `k031-buffer-management`, `k032-latency-optimization` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | 290 | 6 | `k021-macos-core` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | 378 | 6 | `k030-driver-stack` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | 91 | 8 | `k018-dma-kesinti-yonetimi`, `k030-driver-stack`, `k031-buffer-management`, `k032-latency-optimization`, `k035-ag-ve-bluetooth-ses` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | 385 | 6 | `k018-dma-kesinti-yonetimi`, `k032-latency-optimization` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | 246 | 6 | `k035-ag-ve-bluetooth-ses` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | 253 | 6 | `k033-platform-ses-suruculeri` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | 254 | 6 | `k034-usb-audio` |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | 199 | 6 | `k033-platform-ses-suruculeri` |

## Ek 4: Bölüm Seviyesi Aktarım Özeti

| Klasör | Dosya | Kaynak | Aktarılan § sayısı |
|---|---|---|---|
| `k018-dma-kesinti-yonetimi` | `dma-yonetimi.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | 7/7 |
| `k018-dma-kesinti-yonetimi` | `irq-kesinti-yoneticisi.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | 8/8 |
| `k018-dma-kesinti-yonetimi` | `irq-kesinti-yoneticisi.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | 3/7 |
| `k018-dma-kesinti-yonetimi` | `dma-olcum-ve-test.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | 8/8 |
| `k018-dma-kesinti-yonetimi` | `dma-olcum-ve-test.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | 2/6 |
| `k019-windows-core` | `windows-core.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | 7/7 |
| `k019-windows-core` | `windows-api.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | 6/6 |
| `k019-windows-core` | `windows-api.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | 2/7 |
| `k020-linux-core` | `linux-core.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | 7/7 |
| `k020-linux-core` | `alsa-native.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | 6/6 |
| `k020-linux-core` | `alsa-native.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | 1/7 |
| `k021-macos-core` | `macos-core.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | 7/7 |
| `k021-macos-core` | `core-audio-macos.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | 6/6 |
| `k021-macos-core` | `core-audio-macos.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | 1/7 |
| `k022-rpi5-core` | `rpi5-core.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | 7/7 |
| `k022-rpi5-core` | `rpi5-pwm-gpio-audio.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | 7/7 |
| `k022-rpi5-core` | `rpi5-pwm-gpio-audio.md` | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | 2/10 |
| `k023-system-calls` | `system-calls.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | 6/6 |
| `k023-system-calls` | `syscall-guvenlik-ve-hata.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | 6/6 |
| `k023-system-calls` | `syscall-guvenlik-ve-hata.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | 2/7 |
| `k024-ipc-mekanizmalari` | `ipc-mekanizmalari.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | 7/7 |
| `k024-ipc-mekanizmalari` | `ipc-performans-karsilastirma.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | 7/7 |
| `k024-ipc-mekanizmalari` | `ipc-performans-karsilastirma.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | 2/6 |
| `k025-threading-model` | `threading-model.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | 6/6 |
| `k025-threading-model` | `gercek-zamanli-zamanlama.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | 6/6 |
| `k025-threading-model` | `gercek-zamanli-zamanlama.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | 2/6 |
| `k026-memory-management` | `memory-management.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/memory-management.md` | 6/6 |
| `k026-memory-management` | `bellek-havuzlari-ve-leak.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/memory-management.md` | 6/6 |
| `k026-memory-management` | `bellek-havuzlari-ve-leak.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | 2/7 |
| `k027-process-isolation` | `process-isolation.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | 7/7 |
| `k027-process-isolation` | `sandbox-ve-guvenlik.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | 7/7 |
| `k027-process-isolation` | `sandbox-ve-guvenlik.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | 2/7 |
| `k028-container-runtime` | `container-runtime.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | 7/7 |
| `k028-container-runtime` | `docker-orkestrasyon.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | 7/7 |
| `k028-container-runtime` | `docker-orkestrasyon.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | 2/6 |
| `k029-cross-platform-api` | `cross-platform-api.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | 6/6 |
| `k029-cross-platform-api` | `platform-soyutlama-katmani.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | 6/6 |
| `k029-cross-platform-api` | `platform-soyutlama-katmani.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | 2/7 |
| `k030-driver-stack` | `driver-stack-mimari.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | 6/6 |
| `k030-driver-stack` | `driver-stack-mimari.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | 8/8 |
| `k030-driver-stack` | `surucu-yigini-yonetimi.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | 6/6 |
| `k030-driver-stack` | `surucu-yigini-yonetimi.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | 8/8 |
| `k031-buffer-management` | `buffer-management.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | 6/6 |
| `k031-buffer-management` | `buffer-management.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | 8/8 |
| `k031-buffer-management` | `kilitsiz-kuyruklar.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | 6/6 |
| `k031-buffer-management` | `kilitsiz-kuyruklar.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | 2/7 |
| `k032-latency-optimization` | `latency-optimization.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | 6/6 |
| `k032-latency-optimization` | `latency-optimization.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | 8/8 |
| `k032-latency-optimization` | `gecikme-olcum-ve-tuning.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | 6/6 |
| `k032-latency-optimization` | `gecikme-olcum-ve-tuning.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | 2/6 |
| `k033-platform-ses-suruculeri` | `asio-drivers.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | 6/6 |
| `k033-platform-ses-suruculeri` | `asio-drivers.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | 6/6 |
| `k033-platform-ses-suruculeri` | `wasapi-exclusive.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | 6/6 |
| `k033-platform-ses-suruculeri` | `wasapi-exclusive.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | 6/6 |
| `k033-platform-ses-suruculeri` | `pipewire-modern.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | 6/6 |
| `k033-platform-ses-suruculeri` | `pipewire-modern.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | 6/6 |
| `k034-usb-audio` | `usb-audio-class.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | 6/6 |
| `k034-usb-audio` | `usb-audio-class.md` | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | 11/11 |
| `k034-usb-audio` | `i2s-interface.md` | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | 10/10 |
| `k034-usb-audio` | `i2s-interface.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | 2/7 |
| `k034-usb-audio` | `usb-hotplug-enumerasyon.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | 6/6 |
| `k034-usb-audio` | `usb-hotplug-enumerasyon.md` | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | 2/7 |
| `k035-ag-ve-bluetooth-ses` | `network-audio-drivers.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | 6/6 |
| `k035-ag-ve-bluetooth-ses` | `network-audio-drivers.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | 8/8 |
| `k035-ag-ve-bluetooth-ses` | `bluetooth-a2dp.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | 6/6 |
| `k035-ag-ve-bluetooth-ses` | `bluetooth-a2dp.md` | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | 6/6 |
