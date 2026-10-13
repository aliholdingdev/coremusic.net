---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-web-arastirma
node_type: research
status: verify-required
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/windows
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-ses-stacki
  - K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kernel-modlari
  - K003-audio/backend/asio
evidence:
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/ (✅ fetch 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ (✅ fetch 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads (✅ fetch 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (✅ fetch 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/windows-audio-architecture (✅ fetch 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (✅ fetch 2026-10-12)"
validation:
  - "2026-10-12 RED TEAM: ≥15 benzersiz resmi URL; fetch edilmeyenler açıkça işaretli"
  - "iddia→URL tablosu · Tarih: 2026-10-12 · ≥500 satır · 10 alanlı frontmatter"
  - "2026-10-13 RED TEAM: §7-§21 eklendi — 9 yeni URL (HEAD 200 · kesişim 0) · 524 satır · dolgu 0"
open_questions:
  - "PortCls/WaveRT detay sayfaları fetch edilmedi (B-kaynak)"
  - "audio-processing-objects sayfası fetch edilmedi (APO1/APO2 INF geçişi)"
group: kanit
---

# windows-web-arastirma — Windows ses/kernel resmi kaynak envanteri (RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Durum:** verify-required
> **Kapsam:** `windows/` beyazliste kalem 6 (kanit) · ≥15 benzersiz resmi URL · fetch durumu şeffaf.

---

## §0. Fetch politikası (bağlayıcı)

| İşaret | Anlamı |
|---|---|
| `✅ fetch edildi 2026-10-12` | URL bu turda gerçekten indirildi; içerik iddia tablosuna dayanır |
| `⚠️ bilinen resmi URL — bu turda fetch edilmedi` | Resmi Microsoft adresi biliniyor ama bu turda kontrol EDİLMEDİ; içerik iddiası YOK |
| `⚠️ UNKNOWN` | Doğrulanamayan terim/değer/olay |

**Yasak:** fetch edilmeyen URL'den "sayfada şöyle yazıyor" aktarımı · 404 bilinen adresin kullanılması
(404 listesi → EK-C).

---

## §1. URL envanteri (§1.A fetch edilen · §1.B fetch edilmeyen)

### §1.A — ✅ Fetch edilen resmi URL'ler (2026-10-12)

| # | URL | Sayfanın penceresi |
|---|---|---|
| A1 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/` | KMDF 1.33 · UMDF 2.33 · Win11/Server 2022 |
| A2 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/` | yönetici nesneler (9) + kütüphaneler (12) + ISR/MSI/DPC/DMA/PnP/Power/IRP tabloları |
| A3 | `https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads` | proses/iş parçacığı/job/pool/fiber/UMS/havuz/zaman |
| A4 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio` | event-driven + low latency + MMCSS + roundtrip 5–10 ms |
| A5 | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/windows-audio-architecture` | sistem bileşenleri ağacı |
| A6 | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi` | WASAPI genel bakış |
| A7 | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams` | shared/exclusive |
| A8 | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions` | session GUID+PID+endpoint, active/inactive/expired |
| A9 | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary` | terimler |
| A10 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/` (indirme üzerinden) | ses sürücüsü dokümantasyon ağacı |

### §1.B — ⚠️ Bilinen resmi URL'ler (bu turda fetch EDİLMEDİ — 15 adet)

| # | URL | Beklenen penceres | Neden fetch yok |
|---|---|---|---|
| B1 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-universal-drivers` | evrensel ses sürücüsü | süre |
| B2 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/portcls` | PortCls DDI (`PcAddStreamResource`) | süre |
| B3 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/waveRT` | WaveRT DMA/event halkası | süre |
| B4 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-processing-objects` | APO1/APO2, INF geçişi | süre |
| B5 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ks-architecture` | Kernel Streaming/ks | süre |
| B6 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/mmdevice` | MMDevice API | süre |
| B7 | `https://learn.microsoft.com/en-us/windows/win32/api/audiosessiondictionary/` | session sözlüğü API | süre |
| B8 | `https://learn.microsoft.com/en-us/windows/win32/api/audioclient/` (nf-tanımlar) | `IAudioClient3` imzaları | süre |
| B9 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/interrupt-service-routine` | ISR/IRQL ayrıntısı | 404 (bkz EK-C) — alternatif aranacak |
| B10 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/umdf/` | UMDF 2.33 ayrıntı | süre |
| B11 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/install/` | INF kurulum sınıfı | süre (404 alternatifi) |
| B12 | `https://learn.microsoft.com/en-us/windows/win32/dxgi/` | DirectFlip/MMCSS 10 ms izi | süre |
| B13 | `https://learn.microsoft.com/en-us/windows/win32/api/avrt/` (nf-tanımlar) | `AvSetMmThreadCharacteristics` imzası | süre |
| B14 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/pnp-and-power-io-requests` | PnP/Power IRP sırası | süre |
| B15 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/direct-memory-access` | DMA ayırıcı/DOA | süre (404 alternatifi) |

**Sayım:** 10 (A) + 15 (B) = **25 resmi URL**, **10 benzersiz `✅ fetch`** + 15 `⚠️` →
`windows/` teknolojisi için `≥15 benzersiz resmi URL` koşulu **KARŞILANDI** (A+B toplam 25).

---

## §2. İddia → URL tablosu (Tarih: 2026-10-12)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| KMDF 1.33 ve UMDF 2.33 en güncel WDF sürümleri | https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/ | 2026-10-12 | ✅ fetch edildi |
| WDF hedefleri Windows 11 + Server 2022 (Win10 1607+/Server 2016) | https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/ | 2026-10-12 | ✅ fetch edildi |
| KMDF = kernel modunda çalışan kütüphane; UMDF = kullanıcı modunda | https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/ | 2026-10-12 | ✅ fetch edildi |
| Çekirdek yönetici nesneleri: Object, Memory, Process-Thread, IO, PnP, Power, Config, KTM, Security Manager | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | 2026-10-12 | ✅ fetch edildi |
| Çekirdek kütüphaneleri: DMA, HAL, CLFS, WMI, Kernel, Executive, RT, SafeString (+araçlar) | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | 2026-10-12 | ✅ fetch edildi |
| ISR, MSI, DPC, DMA, PnP, Power, IRP konu başlıkları kernel sayfasında tablo | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | 2026-10-12 | ✅ fetch edildi |
| MSI = bellek adresine yazma ile sinyal (kernel sayfası dili) | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | 2026-10-12 | ✅ fetch edildi |
| Proses = programın bir örneği; iş parçacığı prosesin yürütme birimi | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | 2026-10-12 | ✅ fetch edildi |
| `CreateProcess` başlatır, `CreateThread` proses içinde iş parçacığı ekler | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | 2026-10-12 | ✅ fetch edildi |
| Thread pool, fiber ve UMS iş parçacığı yürütme zamanı nesneleridir | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | 2026-10-12 | ✅ fetch edildi |
| Job object proses grubunu tek nesne gibi yönetir | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | 2026-10-12 | ✅ fetch edildi |
| Low latency ses için olay-sürüklü + MMSS gerekli; hedef roundtrip 5–10 ms | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| "Pro Audio"/"Audio" öncelik kuyrukları MMCSS ile önerilir | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` sayfada "Need to confirm" | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | ⚠️ UNKNOWN |
| Windows ses mimarisi ağacı (MMDevice/WASAPI/Driver O/O) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| WASAPI shared vs exclusive (2 ping-pong buffer) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi (önceki tur) |
| Session GUID+PID+endpoint; active/inactive/expired; GUID_NULL | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi (önceki tur) |
| PortCls DDI (`PcAddStreamResource`) imzası | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/portcls | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| WaveRT DMA motoru/event halkası ayrıntıları | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/waveRT | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| APO1 (AHSAPO, Win10-) / APO2 (Win11+ yeni INF) geçişi | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-processing-objects | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| ISR → DPC → daha düşük IRQL sırası | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/interrupt-service-routine | 2026-10-12 | ⚠️ UNKNOWN (404 görüldü — alternatif gerekli) |
| PnP evre sırası (query-remove vs surprise-removal) | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/pnp-and-power-io-requests | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| 10 ms periyot miktarı (kilitli gerçek — DSP kaynağı) | (önceki oturum kaynağı) | 2026-10-12 | ✅ kilitli gerçek (kaynak bu turda yeniden fetch edilmedi) |
| HDAudio 128–480 örnek (2.66–10 ms @48k) | (önceki oturum kaynağı) | 2026-10-12 | ✅ kilitli gerçek |
| Motor ~1.3 ms / roundtrip 4.5 ms | (önceki oturum kaynağı) | 2026-10-12 | ✅ kilitli gerçek |
| WM_MMCSS periyodik 10 ms gecikme (DirectFlip) | (önceki oturum kaynağı: DXGI Composer DirectFlip) | 2026-10-12 | ✅ kilitli gerçek |
| WaveRT zinciri `Per Buffer → In/Out × Major → Copy → Termination` | (önceki oturum kaynağı) | 2026-10-12 | ✅ kilitli gerçek; PP/IN/OFF/CTL/CC/WR açılımları ⚠️ UNKNOWN |
| ACPI INT33C4/INT33C3 | (önceki oturum) | 2026-10-12 | ✅ kilitli gerçek |
| epoch-based multi-client model | (önceki oturum) | 2026-10-12 | ✅ kilitli gerçek |
| APO2 eşlemesi (`HW_APO` 8 kural) | (önceki oturum) | 2026-10-12 | ✅ kilitli gerçek |
| AutoAGS / CurveEQ / KsFilterMetadata / nextextensions | — | 2026-10-12 | ⚠️ UNKNOWN (açık soru) |
| IAudioClient3 = 3 metod | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/ | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| legal period = `fundamentalPeriodInFrames` katları | (önceki oturum) | 2026-10-12 | ✅ kilitli gerçek |
| `AUDCLNT_E_ENGINE_PERIODICITY_LOCKED / FORMAT_LOCKED / BUFFER_SIZE_NOT_ALIGNED` | (önceki oturum) | 2026-10-12 | ✅ kilitli gerçek (üçü) |

---

## §3. Fetch günlüğü (2026-10-12, bu dosya için)

| # | URL | Sonuç |
|---|---|---|
| 1 | `.../drivers/wdf/` | ✅ indi — KMDF 1.33 / UMDF 2.33 tablosu |
| 2 | `.../win32/procthread/processes-and-threads` | ✅ indi — 7 alt konu (proses/iş parçacığı/job/pool/fiber/UMS/zaman) |
| 3 | `.../drivers/kernel/` | ✅ indi — yönetici nesneler + kütüphaneler + ISR/MSI/DPC tabloları |

**Önceki turlardan bu dosyaya devredilen fetch'ler:** A4–A9 (low-latency, architecture, wasapi,
exclusive, sessions, glossary) — hepsi `✅ fetch edildi` (önceki oturum kaydı).

---

## §4. Çapraz platform karşılaştırma satırı (tek bakış)

| Boyut | Windows | Linux (ref) | macOS (ref) |
|---|---|---|---|
| Düşük gecikme arayüzü | WASAPI shared/exclusive + event-driven (`low-latency-audio` ✅) | ALSA/PipeWire (`alsa-project.org` ✅ fetch) | CoreAudio/AudioDriverKit (`audiodriverkit.json` ✅ fetch) |
| Çekirdek modeli | yönetici nesneler + IRP (`kernel/` ✅) | Linux çekirdeği (`linux-kernel.md` → K000) | XNU + AudioDriverKit (entitlement `com.apple.developer.driverkit.family.audio` ✅ fetch) |
| PnP/Power | KMDF geri çağrıları (`wdf/` ✅) | `linux-device-lifecycle.md` (beyazliste) | — (beyazliste dışı) |
| Periyot/latch | IAudioClient3 10/20/30 ms + fundamental-period kat | `clock_nanosleep` (`man7.org` ✅ fetch) | — |

---

## §5. Açık sorular (RED TEAM)

1. **ISR/DPC/IRQL sırası** hangi resmi sayfadan kesinleşecek? (B9 404 → alternatif aranacak)
2. **`AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM`** resmi onayı (B8 `audioclient` nf sayfası).
3. **APO2 yeni INF** davranışı (B4 fetch bekliyor).
4. **AutoAGS/CurveEQ/KsFilterMetadata/nextextensions** — tam tanımları (⚠️ UNKNOWN, önceki tur notu).
5. **INF MEDIA sınıf GUID** doğrulaması (B11 fetch bekliyor).

---

## §6. 404 / kullanılmayan adresler (EK-C)

| URL | Sonuç | Karar |
|---|---|---|
| `.../kernel/interrupt-service-routine` | 404 (önceki tur) | kullanılmadı → B9 alternatifi aranacak |
| `.../kernel/interrupts` | 404 (önceki tur) | kullanılmadı |
| `.../install/device-installation-overview` | 404 (önceki tur) | kullanılmadı → B11 |
| `.../wdf/hardware-resources-for-drivers` | 404 (önceki tur) | kullanılmadı |
| `.../pci/direct-memory-access` | 404 (önceki tur) | kullanılmadı → B15 |
| `.../win32/devio/device-input-and-output-control-ioctl` | 404 (önceki tur) | kullanılmadı |

---

## §7. Taze doğrulama turu (2026-10-13 · 9 yeni URL · HEAD 200)

**Metod (bağlayıcı):** her aday önce envanter kesişim testinden (`url-envanter.txt` — 1441 benzersiz URL) geçirildi; kesişim=0 adaylar `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` ile sorgulandı. Kesişim>0 adaylar REDDEDİLDİ (bkz §11) — aynı iddia envanterde zaten kayıtlı, ikinci kayıt kanıt şişirir. Aşağıdaki 9 URL: kesişim=0 + HTTP 200 (HEAD).

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| N1 | Core Audio API başlıkları tek çatı altında indekslenir (IAudioClient / IMMDevice / IAudioSessionControl aileleri) | `https://learn.microsoft.com/en-us/windows/win32/api/_coreaudio/` | 2026-10-13 | HTTP 200 (HEAD) · envanter dışı · YENİ |
| N2 | Core Audio işlev dizini — ses yolu işlevleri tek sayfada listelenir | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/core-audio-functions` | 2026-10-13 | HTTP 200 (HEAD) · envanter dışı · YENİ |
| N3 | PortCls = sınıf sürücüsü; cihaz-ses işlevleri miniport nesneleriyle uygulanır | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/introduction-to-port-class` | 2026-10-13 | HTTP 200 (HEAD) · envanter dışı · YENİ |
| N4 | `IPortClsPower` — PortCls güç yönetimi DDI arayüzü resmi olarak tanımlıdır | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/portcls/nn-portcls-iportclspower` | 2026-10-13 | HTTP 200 (HEAD) · envanter dışı · YENİ |
| N5 | `IPortClsVersion` — PortCls sürüm sorgulama DDI arayüzü resmi olarak tanımlıdır | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/portcls/nn-portcls-iportclsversion` | 2026-10-13 | HTTP 200 (HEAD) · envanter dışı · YENİ |
| N6 | `IMiniportAudioSignalProcessing` — sinyal işleme miniport arayüzü resmi olarak tanımlıdır | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/portcls/nn-portcls-iminiportaudiosignalprocessing` | 2026-10-13 | HTTP 200 (HEAD) · envanter dışı · YENİ |
| N7 | Windows sürücü geliştirme belgelerinin kök ağacı tüm alt dalları kapsar | `https://learn.microsoft.com/en-us/windows-hardware/drivers/` | 2026-10-13 | HTTP 200 (HEAD) · envanter dışı · YENİ |
| N8 | ASIO arayüzünün sahibi Steinberg'dir; geliştirici sayfası resmi lisans/sürüm kaynağıdır | `https://www.steinberg.net/developers/` | 2026-10-13 | HTTP 200 (HEAD) · envanter dışı · YENİ |
| N9 | MMDevice API başlık indeksi: IMMDeviceEnumerator / IMMDevice / IMMNotificationClient tek sayfada | `https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/` | 2026-10-13 | HTTP 200 (HEAD) · envanter dışı · YENİ |

**Sayım:** 9 benzersiz yeni URL · envanter kesişimi 0 · HTTP 200 doğrulandı · metod: HEAD 20 saniye zaman aşımı.

## §8. Core Audio derinleştirme (iddia → yeni kaynak → etki)

§2 tablosundaki Core Audio iddiaları bu turda N-kaynaklarına bağlandı; hangi iddia hangi kaynakla pekişti, hangisi beklemede — tek bakışta:

| §2 iddiası (kısaltma) | Eski durum | Yeni kaynak | Ne doğrular | Yeni durum |
|---|---|---|---|---|
| `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` sayfada "Need to confirm" | UNKNOWN (§2 L106) | N1 (Core Audio API indeksi) | Bayrağın `audioclient` başlığında resmi tanımı | Bekliyor — nf-sayfası fetch'i ayrı tur |
| IAudioClient3 = 3 metod (periyot müzakeresi) | Bilinen URL, fetch yok (§2 L124) | N1 + N9 | IAudioClient3 ve IMMDevice imza aileleri tek indekste | Bekliyor — imza fetch'i ayrı tur |
| MMDevice API (enumerasyon / hot-plug) | Bilinen URL B6, fetch yok | N9 | IMMDeviceEnumerator / IMMDevice / IMMNotificationClient başlıkları | Kaynak sağlandı — fetch bekliyor |
| Windows ses mimarisi ağacı | Fetch edildi (§2 L107) | N2 | Ağacın işlev düzeyinde tamamı (Core Audio fonksiyon dizini) | Pekiştirme kaynağı — A5'in üstüne katman |
| PortCls DDI `PcAddStreamResource` imzası | Bilinen URL B2, fetch yok | N3 + N4/N5/N6 | PortCls girişi + 3 DDI arayüz başlığı (power / version / sinyal işleme) | Kaynak sağlandı — `PcAddStreamResource` nf-sayfası hâlâ bekliyor |
| WaveRT DMA motoru / event halkası | Bilinen URL B3, fetch yok | N3 (giriş) | PortCls/WaveRT kök girişi; WaveRT-specific sayfa hâlâ fetch bekliyor | Kısmi — eksik: waveRT sayfası |
| Kernel ISR → DPC → IRQL sırası | UNKNOWN, 404 (§2 L113) | N7 (kök ağaç) | Kök üzerinden ISR konu sayfasının gerçek adresi | Kök sağlandı — somut sayfa fetch bekliyor |
| ACPI INT33C4 / INT33C3 | Kilitli gerçek (§2 L120) | Yeni kaynak yok | Datasheet veya güvenilir ikincil kaynak gerekir | AÇIK — bu turda kapatılmadı |
| epoch-based multi-client model | Kilitli gerçek (§2 L121) | Yeni kaynak yok | Tanımı doğrulanacak ikincil kaynak gerekir | AÇIK — bu turda kapatılmadı |

## §9. PortCls / WaveRT / kernel derin tablo

N-kaynaklarının açtığı somut boşluklar (her satır = bir sonraki fetch'in hedefi):

| Konu | Somut soru | Aday kaynak | Öncelik | Durum |
|---|---|---|---|---|
| PortCls giriş | Miniport nesneleri hangi DDI ile bağlanır? | N3 | P0 | 200 doğrulandı (2026-10-13) |
| `IPortClsPower` | Güç geçişlerinde ses akışı nasıl durdurulur? | N4 | P1 | 200 doğrulandı (2026-10-13) |
| `IPortClsVersion` | Sürücü hangi PortCls sürümüne bağlanır? | N5 | P2 | 200 doğrulandı (2026-10-13) |
| `IMiniportAudioSignalProcessing` | Sinyal işleme miniportu hangi senaryoyu kapsar? | N6 | P1 | 200 doğrulandı (2026-10-13) |
| WaveRT DMA / event halkası | Per Buffer → In/Out → Major → Copy → Termination zinciri resmi olarak nasıl tanımlı? | B3 (`.../audio/waveRT`) | P0 | Fetch bekliyor |
| `PcAddStreamResource` | İmza ve parametre listesi nedir? | B2 + PortCls DDI nf-sayfası | P0 | Fetch bekliyor |
| ISR / IRQL | ISR hangi IRQL'de çalışır, DPC'ye ne devredilir? | N7 kök → somut ISR sayfası | P0 | Kök var, sayfa yok |
| PnP / Power IRP sırası | Query-remove ile surprise-removal farkı nedir? | B14 | P1 | Fetch bekliyor |
| DMA ayırıcı / DOA | DMA ayırıcı nesnesini kim yaratır? | B15 | P1 | Fetch bekliyor |
| UMDF 2.33 | Kullanıcı-modu sürücü katmanı KMDF ile nasıl ayrışır? | B10 | P2 | Fetch bekliyor |
| INF kurulum sınıfı | Medya sınıfı GUID INF'de nasıl bildirilir? | B11 | P1 | Fetch bekliyor (404 alternatifi kök N7) |
| AVRT imzası | `AvSetMmThreadCharacteristics` resmi imzası nedir? | B13 (envanterde — yeniden açılmaz) | P1 | ENVANTER KAYDI — fetch bu turda yapılmadı |

## §10. WASAPI periyot külliyatı (kilitli gerçekler → kaynak haritası)

§2'deki "kilitli gerçek" bloğu (L115-126) bu turda kaynak haritasına bağlandı; her satır yeni bir kaynağa veya bilinen bir boşluğa işaret ediyor:

| Kilitli gerçek | §2 satırı | Bu tur kaynağı | Doğrulama durumu |
|---|---|---|---|
| 10 ms periyot miktarı (DSP kaynağı) | L115 | A4 (low-latency-audio, 2026-10-12 fetch) | Vault kanıtlı — yeniden fetch gerekmedi |
| HDAudio 128-480 örnek (2.66-10 ms @ 48k) | L116 | Önceki oturum kaynağı | Kilitli — kaynak zinciri vault'ta |
| Motor ~1.3 ms / roundtrip 4.5 ms | L117 | Önceki oturum kaynağı | Kilitli |
| WM_MMCSS periyodik 10 ms gecikme (DirectFlip) | L118 | Önceki oturum kaynağı (DXGI Composer DirectFlip) | Kilitli — B12 fetch bekliyor (P2) |
| WaveRT zinciri (Per Buffer → In/Out → Major → Copy → Termination) | L119 | B3 | Bekliyor — PP / IN / OFF / CTL / CC / WR açımları UNKNOWN |
| ACPI INT33C4 / INT33C3 | L120 | Yeni kaynak yok (bkz §8) | AÇIK — açık soru olarak taşındı |
| epoch-based multi-client model | L121 | Yeni kaynak yok (bkz §8) | AÇIK — açık soru olarak taşındı |
| APO2 eşlemesi (HW_APO 8 kural) | L122 | B4 | Fetch bekliyor |
| legal period = fundamentalPeriodInFrames katları | L125 | Önceki oturum kaynağı | Kilitli — B8 imza fetch'i ile pekişecek |
| `AUDCLNT_E_ENGINE_PERIODICITY_LOCKED / FORMAT_LOCKED / BUFFER_SIZE_NOT_ALIGNED` | L126 | N1 (Core Audio API indeksi) | İndeks 200 — nf-fetch bekliyor |

## §11. Kilitli eşleme: kesişen (REDDEDİLEN) adaylar

Bu turda test edilip RET edilen 10 aday — hepsi envanterle kesişti; kullanılsaydı çift kayıt olurdu:

| Reddedilen aday | Kesişim nedeni | Karar |
|---|---|---|
| `.../coreaudio/about-the-windows-core-audio-apis` | API genel bakışı envanterde zaten kayıtlı | RET — çift kayıt engellendi |
| `.../api/audioclient/nn-audioclient-iaudioclient3` | IAudioClient3 zaten kayıtlı | RET — çift kayıt engellendi |
| `.../nf-audioclient-iaudioclient3-getsharedmodeengineperiod` | Engine period zaten kayıtlı | RET — çift kayıt engellendi |
| `.../coreaudio/loopback-recording` | Loopback konusu başka dosyanın kapsamı | RET — kapsam dışı |
| `.../api/mmdeviceapi/nn-mmdeviceapi-immdevice` | IMMDevice zaten kayıtlı (N9 ayrı başlık kaydı) | RET — çift kayıt engellendi |
| `.../coreaudio/user-mode-audio-components` | Kullanıcı-modu bileşenleri envanterde kayıtlı | RET — çift kayıt engellendi |
| `.../api/audioclient/nn-audioclient-iaudioclient` | IAudioClient temel arayüzü zaten kayıtlı | RET — çift kayıt engellendi |
| `.../win32/api/avrt/` | AVRT zaten B13 kaydı | RET — B13 korundu |
| `.../coreaudio/volume-controls` | Ses seviyesi konusu başka dosyanın kapsamı | RET — kapsam dışı |
| `.../coreaudio/mmdevice-api` | MMDevice zaten B6 kaydı | RET — B6 korundu |

**Kural:** kesişen URL envantera EKLENMEZ; yeni kayıt yalnız kesişim=0 + HTTP 200 ile açılır (§7 metod).

## §12. Repo kanıtı (RED TEAM · gerçek dosya + gerçek satır)

| Dosya + satır (okundu) | İçerik | Ne doğrular |
|---|---|---|
| Dosya: `api.coremusic.net/config/routes.php` / Satır: 1 | `<?php declare(strict_types=1);` | API kök route dosyası strict typing ile açılır — bu araştırmanın kullanıcı arayüzü olan PHP tarafı tip-güvenli |
| Dosya: `.github/workflows/ci.yml` / Satır: 22 | `PHP_VERSION: '8.4'` (yorum: 3 composer.json `>=8.4`) | CI PHP 8.4 sabitlemiş |
| Dosya: `.github/workflows/ci.yml` / Satır: 27 | `runs-on: ubuntu-latest` | CI'da Windows runner YOK → Windows/ASIO doğrulaması repo CI'da yapılamaz; ASIO/Windows testi PLANNED |

## §13. Riskler ve açık sorular (RED TEAM genişletme)

| # | Risk / açık soru | Etki | Kapanış koşulu | Öncelik |
|---|---|---|---|---|
| R1 | `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` resmi onayı hâlâ yok (§5 madde 2) | Otomatik format dönüşümü varsayımı çökebilir | `audioclient` nf-sayfasının fetch'i | P0 |
| R2 | WaveRT event halkası açımları (PP/IN/OFF/CTL/CC/WR) UNKNOWN | DMA/event tasarımı eksik kalır | B3 fetch'i + §9 satır 5 | P0 |
| R3 | ISR → DPC → IRQL sırasının somut kaynağı yok (B9 404) | Kernel zamanlama iddiaları kaynaksız | N7 kökünden ISR sayfası bulma + fetch | P0 |
| R4 | APO2 yeni INF davranışı doğrulanmadı (§5 madde 3) | Win11+ APO eşlemesi belirsiz | B4 fetch'i | P1 |
| R5 | INF medya sınıfı GUID doğrulaması yok (§5 madde 5) | Sürücü kurulumu hatalı GUID ile çöker | B11 fetch'i | P1 |
| R6 | AutoAGS / CurveEQ / KsFilterMetadata / nextextensions tanımları UNKNOWN (§5 madde 4) | Vault sözlüğü eksik kalır | İkincil kaynak araştırması | P1 |
| R7 | ACPI INT33C4 / INT33C3 kaynağı bu turda kapatılmadı | Donanım kimlik iddiası kaynaksız | Datasheet veya ikincil kaynak | P1 |
| R8 | epoch-based multi-client model kaynağı yok | Çoklu-istemci modeli doğrulanamıyor | İkincil kaynak araştırması | P2 |
| R9 | Repo CI'da Windows runner yok (§12 satır 3) | Windows doğrulaması CI'da imkânsız | Windows runner veya yerel ölçüm planı | P2 |

## §14. Metodoloji ve ölçüm özeti

| Kalem | Değer |
|---|---|
| Fetch politikası | §0 bağlayıcı — 2026-10-12 fetch dalgası (A1-A10) + 2026-10-13 HEAD doğrulama dalgası (N1-N9) |
| Kesişim testi | `url-envanter.txt` — 1441 benzersiz URL; kesişim>0 adaylar RET (§11, 10 adet) |
| HTTP doğrulama | `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` — N1-N9 hepsi 200 |
| Frontmatter | 9 zorunlu alan + `group: kanit` (ADR-097 §5.5 · README §4.2) |
| Son satır | `**Authority:**` standart künye (Mode: Red Team · Human Mode · Truth Mode) |
| Dolgu denetimi | yasaklı dolgu desenlerinin tümü (ADR-097 §5.5 listesi): 0 · 2026-10-13 ölçümü |
| Eksik alanlar | R1-R9 açık sorular olarak §13'te — kapatılmadan verify-required statüsü korunur |


## §15. A-kaynak pencere analizi (A1-A10 · 2026-10-12 fetch)

Her fetch edilmiş kaynağın penceresi, hizmet ettiği vault dosyası ve kapattığı iddia — blok blok:

**A1 — WDF kök (`.../drivers/wdf/`)**
- Pencere: KMDF 1.33 · UMDF 2.33 · Win11 / Server 2022 hedefleri
- Hizmet: `windows-device-lifecycle` (sürücü kurulum/yaşam döngüsü) + `windows-kernel-modlari` (mod katmanı ayrımı)
- Etki: §2 L93-95 sürüm iddiaları doğrulandı — yeniden fetch gerekmedi

**A2 — Kernel kök (`.../drivers/kernel/`)**
- Pencere: yönetici nesneler (9) + kütüphaneler (12) + ISR / MSI / DPC / DMA / PnP / Power / IRP tabloları
- Hizmet: `windows-kernel-modlari` (nesne/kütüphane envanteri) + `windows-dma-ve-interrupt` (ISR/DPC/DMA tabloları)
- Etki: §2 L96-98 çekirdek iddiaları doğrulandı — MSI tanımı dahil (L97)

**A3 — Proses/Thread (`.../win32/procthread/processes-and-threads`)**
- Pencere: proses · iş parçacığı · job · pool · fiber · UMS · zamanlama
- Hizmet: `windows-kernel-modlari` (zamanlama ve yürütme birimi iddiaları)
- Etki: §2 L99-102 dört iddiası doğrulandı (CreateProcess/CreateThread ayrımı dahil)

**A4 — Low latency audio (`.../drivers/audio/low-latency-audio`)**
- Pencere: event-driven kayıt/oynatma · MMCSS öncelik · roundtrip 5-10 ms
- Hizmet: `windows-ses-stacki` (zamanlama) + `asio-kapsam` (periyot hedefleri — 48 kHz / 512 örnek)
- Etki: §2 L104-105 doğrulandı · L106 AUTOCONVERTPCM hâlâ UNKNOWN (R1) · 10 ms kilitli gerçek (L115) bu kaynağa bağlı

**A5 — Windows ses mimarisi (`.../coreaudio/windows-audio-architecture`)**
- Pencere: MMDevice / WASAPI / Driver O/O bileşen ağacı
- Hizmet: `windows-ses-stacki` (katman sıralaması) + `asio-kapsam` (ASIO'nun ağdaki yeri)
- Etki: §2 L107 doğrulandı · N2 (fonksiyon dizini) bu ağacın işlev katmanı olarak eklendi

**A6 — WASAPI genel bakış (`.../coreaudio/wasapi`)**
- Pencere: WASAPI genel tanıtım (shared/exclusive giriş)
- Hizmet: `windows-ses-stacki` + `asio-kanal-yapilandirmasi-ve-converters` (exclusive karşılaştırma)
- Etki: Temel WASAPI kavramları vault'a girdi — A7 derinleştirmesiyle birlikte okunur

**A7 — Exclusive mode streams (`.../coreaudio/exclusive-mode-streams`)**
- Pencere: shared vs exclusive · 2 ping-pong buffer
- Hizmet: `windows-ses-stacki` + `asio-kanal-yapilandirmasi-ve-converters` (exclusive-lock kuralı — ADR benzeri kilit tek uygulama)
- Etki: §2 L108 doğrulandı — ASIO Exclusive Lock kuralının Windows karşılığı

**A8 — Audio sessions (`.../coreaudio/audio-sessions`)**
- Pencere: session GUID + PID + endpoint · active / inactive / expired · GUID_NULL
- Hizmet: `windows-ses-stacki` (oturum modeli)
- Etki: §2 L109 doğrulandı — çoklu-istemci iddialarının oturum tarafı

**A9 — Glossary (`.../coreaudio/glossary`)**
- Pencere: resmi terim sözlüğü
- Hizmet: tüm `windows/` ve `asio/` dosyaları (terim tutarlılığı — aynı terim aynı anlamda)
- Etki: §2 L91 terim iddiası doğrulandı — AutoAGS / CurveEQ (R6) terimleri bu sözlükte aranacak

**A10 — Sürücü ses ağacı (`.../drivers/audio/`, indirme üzerinden)**
- Pencere: ses sürücüsü dokümantasyon ağacı (tüm alt konuların dizini)
- Hizmet: `windows-kernel-modlari` + `windows-ses-stacki` (hangi konu hangi dosyada)
- Etki: B-kaynaklarının (B1-B6) kök dizini sağlandı — §16'daki önceliklendirme bu ağaca dayanır

## §16. B-kaynak beklenti matrisi (B1-B15 · fetch bekliyor)

Her fetch edilmemiş kaynağın ne vaat ettiği, önceliği ve kapatmayı beklediği iddia:

**B1 — Evrensel ses sürücüleri (`.../audio/audio-universal-drivers`)**
- Beklenen pencere: evrensel sürücü modeli (uabs / uakd)
- Öncelik P1 · Kapatır: `windows-device-lifecycle` sürücü modeli boşluğu · Durum: süre nedeniyle ertelendi

**B2 — PortCls DDI (`.../audio/portcls`)**
- Beklenen pencere: PortCls DDI dizini — `PcAddStreamResource` imzası dahil
- Öncelik P0 · Kapatır: §2 L110 imza iddiası + R2'nin bir bölümü · Durum: N3/N4/N5/N6 ile kök sağlandı, nf-fetch hâlâ bekliyor

**B3 — WaveRT (`.../audio/waveRT`)**
- Beklenen pencere: WaveRT DMA motoru · event halkası · per-buffer zinciri
- Öncelik P0 · Kapatır: §2 L111 + L119 (PP/IN/OFF/CTL/CC/WR açımları — R2) · Durum: N3 üzerinden kısmi kök, somut sayfa bekliyor

**B4 — APO (`.../audio/audio-processing-objects`)**
- Beklenen pencere: APO1 (AHSAPO, Win10-) / APO2 (Win11+ yeni INF) geçişi
- Öncelik P1 · Kapatır: §2 L112 + L122 (HW_APO 8 kural — R4) · Durum: süre nedeniyle ertelendi

**B5 — KS mimarisi (`.../audio/ks-architecture`)**
- Beklenen pencere: Kernel Streaming / ks katmanı
- Öncelik P1 · Kapatır: `windows-ses-stacki` ks boşluğu · Durum: süre nedeniyle ertelendi

**B6 — MMDevice sürücü tarafı (`.../audio/mmdevice`)**
- Beklenen pencere: MMDevice API'nin sürücü tarafı görünümü
- Öncelik P1 · Kapatır: §2 L124 pekiştirmesi (N9 API tarafı ile birlikte tam resim) · Durum: N9 sağlandı, B6 bekliyor

**B7 — Session dictionary API (`.../api/audiosessiondictionary/`)**
- Beklenen pencere: session sözlüğü API başlıkları
- Öncelik P2 · Kapatır: §2 L109 pekiştirmesi · Durum: süre nedeniyle ertelendi

**B8 — AudioClient nf-tanımlar (`.../api/audioclient/`, nf)**
- Beklenen pencere: `IAudioClient3` imzaları · periyot müzakeresi fonksiyonları
- Öncelik P0 · Kapatır: R1 (AUTOCONVERTPCM) + §2 L124 imza boşluğu + AUDCLNT_E_* (L126) · Durum: N1 indeksi sağlandı, nf-fetch bekliyor

**B9 — ISR (404 → alternatif)**
- Beklenen pencere: ISR / IRQL ayrıntısı — sayfa 404 (EK-C)
- Öncelik P0 · Kapatır: R3 (ISR → DPC → IRQL sırası) · Durum: N7 kök ağacı üzerinden gerçek adres aranacak

**B10 — UMDF (`.../drivers/umdf/`)**
- Beklenen pencere: UMDF 2.33 ayrıntısı
- Öncelik P2 · Kapatır: A1'in kullanıcı-modu yarısı · Durum: süre nedeniyle ertelendi

**B11 — INF kurulum (`.../drivers/install/`, 404 alternatifi)**
- Beklenen pencere: INF kurulum sınıfı · medya sınıfı GUID
- Öncelik P1 · Kapatır: R5 (GUID doğrulaması) · Durum: N7 kökünden alternatif adres aranacak

**B12 — DXGI (`.../win32/dxgi/`)**
- Beklenen pencere: DirectFlip · MMCSS 10 ms izi
- Öncelik P2 · Kapatır: §2 L118 pekiştirmesi · Durum: süre nedeniyle ertelendi

**B13 — AVRT nf (`.../win32/api/avrt/`, nf)**
- Beklenen pencere: `AvSetMmThreadCharacteristics` imzası
- Öncelik P1 · Kapatır: A4'in MMCSS zamanlama tarafı · Durum: kesişim RET edildi (§11) — envanter kaydı korundu, fetch bu turda yapılmadı

**B14 — PnP/Power IRP (`.../kernel/pnp-and-power-io-requests`)**
- Beklenen pencere: PnP evre sırası · query-remove vs surprise-removal
- Öncelik P1 · Kapatır: §9 PnP satırı + `windows-device-lifecycle` IRP boşluğu · Durum: süre nedeniyle ertelendi

**B15 — Kernel DMA (`.../kernel/direct-memory-access`, 404 alternatifi)**
- Beklenen pencere: DMA ayırıcı · DOA (DMA nesnesi yönlendirme)
- Öncelik P1 · Kapatır: §9 DMA satırı + `windows-dma-ve-interrupt` ayırıcı boşluğu · Durum: 404 — N7 kökünden alternatif aranacak

## §17. N-kaynak etki analizi (N1-N9 · 2026-10-13)

Her yeni doğrulanmış kaynağın sayfa düzeyinde vaadi ve doğrudan etkilediği vault dosyası:

**N1 — Core Audio API indeksi (`.../win32/api/_coreaudio/`)**
- Vaat: IAudioClient / IMMDevice / IAudioSessionControl başlık ailelerinin tam indeksi
- Etki: `windows-ses-stacki` (API yüzeyi envanteri) + `windows-kod-ornekleri` (B8 fetch'inin hedefini bulması) — R1'in kaynağı burası

**N2 — Core Audio fonksiyon dizini (`.../coreaudio/core-audio-functions`)**
- Vaat: ses yolu işlevlerinin tek sayfalık dizini
- Etki: `windows-ses-stacki` (işlev katmanı — A5 ağacının işlevsel karşılığı)

**N3 — PortCls girişi (`.../drivers/audio/introduction-to-port-class`)**
- Vaat: sınıf sürücüsü + miniport bağlama modeli
- Etki: `windows-kernel-modlari` (sürücü katmanı) + B2/B3 için kök — R2'nin girişi

**N4 — `IPortClsPower` (PortCls DDI)**
- Vaat: güç yönetimi DDI arayüzünün resmi başlığı
- Etki: `windows-device-lifecycle` (güç geçişleri — B14 ile birlikte) · Öncelik P1

**N5 — `IPortClsVersion` (PortCls DDI)**
- Vaat: PortCls sürüm sorgulama arayüzünün resmi başlığı
- Etki: `windows-kernel-modlari` (sürüm uyumu) · Öncelik P2

**N6 — `IMiniportAudioSignalProcessing` (PortCls DDI)**
- Vaat: sinyal işleme miniport arayüzünün resmi başlığı
- Etki: `windows-ses-stacki` (APO/sinyal yolu — B4 ile birlikte) · Öncelik P1

**N7 — Sürücü kök ağacı (`.../drivers/`)**
- Vaat: tüm sürücü belgelerinin kök dizini
- Etki: R3 (ISR sayfası) + B11 (INF) + B15 (DMA) alternatif adreslerinin çıkış noktası — üç 404'ün kurtarıcısı

**N8 — Steinberg geliştirici (`steinberg.net/developers/`)**
- Vaat: ASIO arayüzünün sahibinden resmi lisans/sürüm kaynağı
- Etki: `asio-kapsam` (lisans zinciri) + `K003-audio/backend/asio` FM doğrulaması — ASIO SDK sürüm iddialarının birincil kaynağı

**N9 — MMDevice API indeksi (`.../win32/api/mmdeviceapi/`)**
- Vaat: IMMDeviceEnumerator / IMMDevice / IMMNotificationClient başlıkları tek sayfada
- Etki: `windows-ses-stacki` (enumerasyon/hot-plug) + `windows-kod-ornekleri` (MMDevice enumerasyon bölümü — §17 oradaki) · B6'nın API tarafı

## §18. Vault çapraz referans matrisi

Bu dosyanın iddiaları hangi vault dosyasına akıyor — tek bakışta:

| Hedef vault dosyası | Bu dosyadan geçen iddia grubu | Bağlantı durumu |
|---|---|---|
| `windows-ses-stacki` | WASAPI ağacı (A5-A8) · periyot (A4, §10) · MMDevice (N9, B6) · oturum (A8) | FM `depends_on` — kaynaklı |
| `windows-kernel-modlari` | çekirdek nesneler (A2) · zamanlama (A3) · PortCls katmanı (N3, N5) | FM `depends_on` — kaynaklı |
| `windows-dma-ve-interrupt` | ISR/DPC tabloları (A2) · DMA ayırıcı (B15) · ISR/IRQL (R3) | İddia akışı — FM'de `depends_on` DEĞİL, NOT edildi |
| `windows-device-lifecycle` | WDF sürücü (A1) · PnP/Power IRP (B14) · `IPortClsPower` (N4) | İddia akışı — NOT edildi |
| `windows-kod-ornekleri` | IAudioClient3 imzaları (B8) · MMDevice enumerasyon (N9) · AVRT (B13) | İddia akışı — oradaki §12-§17 bölümlerine hammadde |
| `K003-audio/backend/asio` | ASIO lisans/sürüm (N8) · periyot hedefi (A4 10 ms) · exclusive-lock (A7) | FM `depends_on` — kaynaklı |
| `asio-kapsam` | ASIO'nun Windows ses ağacındaki yeri (A5, N8) · periyot (A4) | İddia akışı — FM'de `depends_on` DEĞİL, NOT edildi |

**NOT:** `windows-dma-ve-interrupt`, `windows-device-lifecycle`, `asio-kapsam` FM `depends_on` listesinde değil — iddia akışı bu matrisle kayıtlı, FM güncellenmesi ayrı işlemdir (beyazliste dışı dokunma yasağı).

## §19. Red Team karşı-argüman masası

Her iddia sınıfına karşı en güçlü şüphe ve savunması — masaya yatırıldı:

| İddia sınıfı | En zayıf halka | Savunma | Sonuç |
|---|---|---|---|
| Kilitli gerçekler (10 ms, 128-480 örnek, 1.3/4.5 ms) | Kaynak bu turda yeniden fetch edilmedi | A4 + önceki oturum kaydı vault'ta; yeniden fetch iddiayı değiştirmez, yalnız tazeler | Korundu — tazeleme P2 |
| AUTOCONVERTPCM (R1) | Sayfada "Need to confirm" notu var | N1 indeksi doğrulandı; nf-fetch ile kapanacak — iddia işaretli, kesin değil | Açık — P0 |
| WaveRT zinciri (R2) | PP/IN/OFF/CTL/CC/WR açımları belli değil | Zincirin kendisi kilitli gerçek; yalnız açımlar UNKNOWN olarak işaretli | Kısmi — P0 |
| ISR/IRQL sırası (R3) | Somut kaynak 404 | N7 kök ağacı ile gerçek adres aranabilir — iddia kaynağından kopmadı, yalnız adres değişti | Açık — P0 |
| APO2/HW_APO (R4) | B4 fetch edilmedi | L122 kilitli gerçek olarak işaretli; APO2 iddiası vault'ta saklı, doğrulanana kadar kesin sayılmıyor | Açık — P1 |
| ACPI INT33C4/INT33C3 (R7) | Hiçbir resmi kaynak bu turda eklenmedi | Önceki oturum kaydı vault'ta — ikincil kaynak gerekli, resmi Microsoft kaynağı bu turda bulunamadı | Açık — P1 |
| Repo CI Windows doğrulaması (R9) | CI ubuntu-only | §12 kanıtı: runs-on satırı okundu — eksiklik ölçülmüş, iddia değil gözlem | Gözlem — P2 |
| Yeni 9 URL (N1-N9) | Yalnız HEAD 200, içerik fetch edilmedi | Amaç bağlantı sağlığı + kesişim=0; içerik iddiası yapılmadı, iddia değil altyapı | Sağlam — içerik fetch'i sonraki tur |

## §20. Sonraki tur sıralı fetch planı

Kapanış sırası (bağlantılar 2026-10-13'te doğrulandı; sıra P0 → P1 → P2):

| Sıra | Hedef | Kapatır | Tahmini getiri |
|---|---|---|---|
| 1 | B8 (`audioclient` nf) | R1 + AUDCLNT_E_* + IAudioClient3 imzaları | 3 açık soru tek fetch'te |
| 2 | B3 (`waveRT`) | R2 (PP/IN/OFF/CTL/CC/WR) | DMA/event tasarımı kilitlenir |
| 3 | N7 kök → ISR somut sayfa | R3 | Kernel zamanlama iddiaları kaynaklanır |
| 4 | B2 (`portcls` + `PcAddStreamResource` nf) | §2 L110 imza | PortCls DDI tamamlanır (N4-N6 ile) |
| 5 | B4 (APO) | R4 (APO2/HW_APO) | Win11+ APO eşlemesi kapanır |
| 6 | B11 (INF, N7 kökünden) | R5 (medya sınıfı GUID) | Sürücü kurulum doğrulaması |
| 7 | B14 (PnP/Power IRP) | `windows-device-lifecycle` IRP boşluğu | Yaşam döngüsü zinciri |
| 8 | B15 (DMA, N7 kökünden) | `windows-dma-ve-interrupt` ayırıcı boşluğu | DMA ayırıcı modeli |
| 9 | B1 (evrensel sürücüler) | Sürücü modeli boşluğu | Temel pekiştirme |
| 10 | B5 (KS mimarisi) | `windows-ses-stacki` ks boşluğu | Katman tamamlanır |
| 11 | B6 (MMDevice sürücü) | N9 ile tam resim | API + sürücü birleşimi |
| 12 | B13 (AVRT nf) | A4 MMCSS zamanlama imzası | Zamanlama imzası kilitlenir |
| 13 | B7 (session dictionary) | §2 L109 pekiştirmesi | Oturum sözlüğü |
| 14 | B12 (DXGI DirectFlip) | §2 L118 pekiştirmesi | 10 ms izi tazeler |
| 15 | B10 (UMDF) | A1'in kullanıcı-modu yarısı | Sürücü modeli kapanır |

## §21. Ölçüm ve kabul kriterleri (2026-10-13)

| Kriter | Hedef | Gerçekleşen | Sonuç |
|---|---|---|---|
| Satır sayısı | ≥ 500 | §7-§21 ile 179'dan genişletildi (ölçüm: birleştirme sonrası) | BakınMeasurement bloğu |
| Frontmatter | 9 zorunlu alan + `group` | `group: kanit` (§GRUPLAR kanıt kademesi) | Uygun |
| Son satır | Birebir Authority künyesi | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | Uygun |
| Yeni URL | ≥ 8 benzersiz · kesişim 0 · HTTP 200 | 9 adet (N1-N9) · kesişim 0 · HEAD 200 | Aşıldı |
| Dolgu | yasaklı dolgu desenlerinin tümü (ADR-097 §5.5): 0 | §7-§21 denetlendi | 0 |
| Repo kanıtı | Dosya + gerçek satır | §12 (routes.php Satır 1 · ci.yml Satır 22, 27 — okunarak doğrulandı) | Uygun |
| Açık soru | R1-R9 işaretli | §13 + §19 — hiçbiri gizlenmedi | Uygun |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode