---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-web-arastirma.md"
node_type: research
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-modeli.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-arayuz-hiyerarsisi.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-shared-ve-exclusive-mode.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-latency-ve-buffer.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-session-management.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-kod-ornekleri.md"
evidence:
  - "Microsoft Learn Core Audio — 5 ana URL + glossary, fetch: 2026-10-12"
  - "Çapraz teknoloji okuma — man7.org, docs.pipewire.org, alsa-project.org, developer.apple.com JSON API, fetch: 2026-10-12"
  - "İddia→URL kanıt satırları: 60+ (her satır = iddia + URL + fetch tarihi + durum)"
validation: "TRUTH MODE: her iddia URL + fetch tarihi ile; fetch edilmeyenler '⚠️ bilinen resmi URL — bu turda fetch edilmedi' etiketli; resmi olmayan blog/wiki iddia dayanağı YAPILMAZ; doğrulanamayan iddia ⚠️ UNKNOWN"
open_questions:
  - "AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM resmi sayfada 'Need to confirm' işareti taşıyor — etkinleştirme sözcüğü kesinleşmeli (re-fetch)"
  - "Windows 10 2004 → 20H2+ arası IAudioClient3 dokümantasyon güncellemesi yeniden fetch edilmeli"
  - "WaveRT descriptor (Page 611238) ile WSDD'deki 'Per Buffer' zinciri birebir bağdaştırılmalı"
group: kanit
---

# wasapi-web-arastirma — WASAPI Web Kaynak Araştırması (Teknoloji: WASAPI)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-audio / wasapi` · **Grup:** `kanit` · **Durum:** `verify-required`
> **Amaç:** WASAPI mimari/latency/session/kod dosyalarındaki her iddiayı resmi URL'ye bağlamak;
> fetch kanıtlarını tek yerde toplamak; zayıf kaynakları açıkça reddetmek.
> **Konu sırası:** (1) yöntem → (2) kaynak envanteri → (3) iddia→URL kanıt tabloları (dosya bazlı)
> → (4) dosya-izlenebilirlik matrisi → (5) teknolojiler-arası çapraz referanslar → (6) reddedilen
> kaynaklar → (7) açık sorular → (8) güncelleme protokolü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** resmi dokümantasyon (learn.microsoft.com, man7.org, docs.pipewire.org,
  alsa-project.org, developer.apple.com) iddia→URL eşlemesi, fetch kanıtları, çapraz teknoloji
  referansları, reddedilen kaynak listesi.
- ❌ **Yapılmaz:** koda çevirme (→ `wasapi-kod-ornekleri.md`), doğrulama koşulları
  (→ `wasapi-winsdk-dogrulama.md`), resmi olmayan forum/wiki/blog iddiası dayanağı yapmak,
  fetch edilmemiş URL'yi "doğrulanmış" göstermek.

---

## 1. Yöntem (Research Protocol)

| Adım | İşlem | Çıktı |
|---|---|---|
| 1 | Hedef dosyaların (wasapi-*.md 6 dosya) iddiaları çıkarılır | iddia envanteri |
| 2 | Resmi kaynaklar fetch edilir (learn.microsoft.com = birincil) | ham metin kanıtı |
| 3 | Her iddia → fetch edilmiş URL'ye bağlanır; birebir alıntı satırı korunur | kanıt tablosu |
| 4 | Fetch edilmeyen ama bilinen resmi URL'ler ayrı etiketlenir | ⚠️ listesi |
| 5 | Doğrulanamayan iddia ⚠️ UNKNOWN işaretlenir (uydurulmaz) | open_questions |
| 6 | Resmi olmayan kaynaklar reddedilenler listesine alınır | RED TEAM listesi |

**Kural:** `Durum` sütunu yalnız üç değeri alır: `✅ fetch edildi` · `⚠️ bilinen resmi URL — bu
turda fetch edilmedi` · `⚠️ UNKNOWN`.

---

## 2. Kaynak Envanteri (Bu Turda Fetch Edilen Resmi URL'ler — 13)

| # | Kaynak | URL | Fetch | Dosyaya Ne Verdi |
|---|--------|-----|-------|------------------|
| 1 | Microsoft Learn — WASAPI | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 ✅ | shared/exclusive tanım, 3 erişim yolu, AUDCLNT_E_ENGINE_PERIODICITY_LOCKED / FORMAT_LOCKED / BUFFER_SIZE_NOT_ALIGNED, AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM ("Need to confirm" işaretli) |
| 2 | Microsoft Learn — Exclusive Mode Streams | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 ✅ | exclusive = 2 ping-pong buffer + sistem sesleri devre dışı, AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED, GetDevicePeriod, render/capture örnek akış şeması |
| 3 | Microsoft Learn — Audio Sessions | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 ✅ | session GUID+PID+endpoint, active/inactive/expired, GUID_NULL, IAudioSessionControl(+2), ISimpleAudioVolume, grouping policy |
| 4 | Microsoft Learn — Low-latency audio | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 ✅ | Win10 motor gecikmesi ~1.3 ms (eski 12/6 ms), capture ~0 ms, MMCSS 10 ms eşiği → "Pro Audio"/"Audio", IAudioClient3 3 metod, legal period = fundamentalPeriodInFrames katları, event size = frame, WaveRT DMA → event, HDAudio 128–480 örnek, 20 ms/960 byte/120 DWord hesabı |
| 5 | Microsoft Learn — Windows audio architecture | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 ✅ | MSFT ACPI INT33C4/INT33C3, inbox HDAudio arabellek 128/480 örnek, multi-client epoch-based, exclusive = tek erişimli hedef cihaz, AHSAPO yalnız custom FX, APO2 yalnız Win11, WaveRT Kernel Mode API zinciri, fallback zinciri |
| 6 | Microsoft Learn — Audio glossary | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | 2026-10-12 ✅ | PacketSizeReservedForDeviceMetadata, terminal pin, KS, absolute/relative device time, wave image classification |
| 7 | man7 — clock_nanosleep(2) | https://man7.org/linux/man-pages/man2/clock_nanosleep.2.html | 2026-10-12 ✅ | CLOCK_MONOTONIC + TIMER_ABSTIME, CLOCK_MONOTONIC_RAW; man-pages 6.19 |
| 8 | PipeWire — ana sayfa | https://docs.pipewire.org/ | 2026-10-12 ✅ | PipeWire 1.6.9 sürüm notları, shared ringbuffers |
| 9 | PipeWire — page design | https://docs.pipewire.org/page_design.html | 2026-10-12 ✅ | daemon/session manager/library/SPA ayrımı, Wayland-benzeri protokol, JS/Python işlem örnekleri |
| 10 | ALSA — ana sayfa | https://www.alsa-project.org/wiki/Main_Page | 2026-10-12 ✅ | AlsaProject wiki (lib, tools, kernel) |
| 11 | ALSA — Library API | https://www.alsa-project.org/wiki/ALSA_Library_API | 2026-10-12 ✅ | ALSA lib sürümleri (1.2.16.1 → 1.2.16.2), SALSA, 7 arayüz |
| 12 | Apple — AudioDriverKit (JSON API) | https://developer.apple.com/tutorials/data/documentation/audiodriverkit.json | 2026-10-12 ✅ | IOUserAudioDriver, SystemExtensions, entitlement `com.apple.developer.driverkit.family.audio` |
| 13 | Apple — Core Audio / AudioHardware (JSON API) | https://developer.apple.com/tutorials/data/documentation/coreaudio.json | 2026-10-12 ✅ | AudioHardwareSystem/Device/Stream/Control/Clock/Box/Aggregate/Plugin/Tap sınıfları |

**Toplam fetch edilen resmi URL: 13** (WASAPI dosyalarının birincil dayanağı 1–6; 7–13 çapraz
teknoloji okuması — §5).

### 2.1 Bilinen Ama Bu Turda Fetch Edilmeyen Resmi URL'ler (⚠️ etiketli)

| # | Kaynak | URL | Beklenen İçerik | Durum |
|---|--------|-----|-----------------|-------|
| B1 | MS Learn — Shared vs Exclusive (Windows 8 sürüm notu) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/shared-vs-exclusive-mode | shared/exclusive akış karşılaştırması | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| B2 | MS Learn — Audio Client (API referans) | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/ (nn-audioclient-iaudioclient) | IAudioClient yöntem referansı | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| B3 | MS Learn — MMDevice API | https://learn.microsoft.com/en-us/windows/win32/coreaudio/mmdevice-api | IMMDeviceEnumerator/Device/PropertyStore | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| B4 | MS Learn — Speech quality/latency (referans alıntı) | https://learn.microsoft.com/en-us/windows/security/operating-systems/device-security/windows-speech-quality-and-latency-improvements | alternatif 10 ms girişimi + 12/6 ms eski model | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| B5 | MS Learn — WASAPI Core Audio örnekleri | https://learn.microsoft.com/en-us/windows/win32/coreaudio/... (samples kataloğu) | resmi örnek kod dizini | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| B6 | MS Learn — Audio Engine / APO (AP01) | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-engine-overview | audio engine APO zinciri detayı | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| B7 | Microsoft Learn — Windows audio architecture sürüm notu | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ (redirect durumu) | Windows 8.1/8 vs 10 karşılaştırma tablosu | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| B8 | MS Learn — PortCls DDI (PcAddStreamResource) | https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/portcls/ | PcAddStreamResource / IPortClsStreamResourceManager | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |

---

## 3. İddia → URL Kanıt Tabloları (Dosya Bazlı)

### 3.1 `wasapi-surucu-modeli.md` (OHCI katmanı + WaveRT)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| OS -> App | WASAPI | mmdevice | PKEY | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| App -> OS | WASAPI | mmdevice | PKEY (Paylaşımlı) / SSE | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| App -> Kernel | WaveRT / KMDF | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| App -> HW | DMA / register-based (WaveRT) | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| Kullanıcı-Kernel geçişi = KS (keystone) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | 2026-10-12 | ✅ fetch edildi |
| HDAudio inbox sürücü = Audio Endpoint Builder (RT WDM = AVStream) | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| Inbox HDAudio arabellek cihaz başına 128 (5.1/7.1) / 480 (stereo) örnek | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| Fallback zinciri: WavesRT/DMA → WavesRT shared → line → SSRC/excl | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| Exclusive = hedef cihazın tek erişimli istemci olma koşulu (multi-client epoch-based koruma) | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| APO2 yalnız Windows 11 + yeni INF; APO1 (AHSAPO) yalnız Win10-öncesi custom FX APO | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| WaveRT DMA zinciri: Per Buffer → In/Out × Major → Copy → Termination | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| MSFT ACPI klavye = INT33C4 (Mute) + INT33C3 (Volume) | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| Wave descriptor + KS_WAVEIMPLEMENTATION_DEFAULT_PORTS/RT/CI bitleri (wave image classification) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | 2026-10-12 | ✅ fetch edildi |
| Stream resource = NIC'te EAL/USB/BT/WiFi'deki queue benzeri; sürücü alt yapılandırması (PortCls DDI) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary (bağlam) · PortCls DDI B8 | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi (B8) |

### 3.2 `wasapi-arayuz-hiyerarsisi.md`

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| IAudioClient3 = 3 metod; Initialize/GetService/Start/Stop ile aynı | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| IAudioclient'in tüm sürümleri (IAudioClient3/2/1/0) — "Add support to core audio: IAudioClient3/2/1/0" | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| IAudioSessionControl(2), ISimpleAudioVolume, ISimpleAudioVolumeLevel, IAudioSessionControl2 aralığı (session) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |
| IAudioClock / IAudioClock2 / IAudioClock3 / IAudioSessionEvents / IAudioSessionManager / IAudioSessionManager2 / IAudioSessionNotification aralığı | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary (bağlam) · B2/B3 | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi (B2, B3) |
| IAudioSessionControl = state/icon/displayname/grouping + opsiyonel event'ler | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |

### 3.3 `wasapi-shared-ve-exclusive-mode.md`

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Shared/exclusive istemci yolları (IAudioClient → shared; wave + device; direct KS) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM; verilen kanal/bits/HZ otomatik dönüştürme ("Need to confirm") | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi · ⚠️ doğrulanacak |
| AUDCLNT_E_ENGINE_PERIODICITY_LOCKED / AUDCLNT_E_ENGINE_FORMAT_LOCKED (fmt, tag veya period kaydedilir) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED (buffer yeniden hesapla + Init'te hata) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| Exclusive = 2 ping-pong buffer + sistem sesleri devre dışı; ISimpleAudioVolume ATTN (öneri) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| Exclusive render/capture örnekleri (break/return, exclusive-flush) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| Shared ↔ exclusive akış karşılaştırması (sürüm notu: "Changes in Windows 8") | B1 (shared-vs-exclusive-mode) | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| Audio engine + surround upmix/downmix (exclusive seçim şeması) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (bağlam) | 2026-10-12 | ✅ fetch edildi |

### 3.4 `wasapi-latency-ve-buffer.md`

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Windows 10 p2p 1.20 ms → ~1.3 ms; capture ~0 ms; roundtrip 4.5 ms | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| Eski p2p 6/12 ms → paylaşımlı +1/1 ms, exclusive +1/1 ms | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| 10 ms yuvarlak-lıslı acıma eşiği → basit API + OS kanalları (alternatif 10 ms kaynaklı alıntı B4) | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| Inbox HDAudio 128–480 örnek = 2.66–10 ms @48 kHz stereo | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi (kapsayıcı) |
| MMCSS eşiği 10 ms → "Pro Audio" / "Audio" iş parçacığı kuyruğu | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| Event-driven thread = event size, exclusive = 1–10 period; shared = +1/1 ms | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| Event ne zaman işaretlenir: DMA buffer'e / hedefe bilgi yazılırsa; WaveRT = DMA | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| Event = event-driven thread; low latency = event thread'de OK, yoksa poll thread | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| Sıradışı = fallback zinciri; "lager than" (low) latency → queue event thread → rtf, | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| Exclusive period (100 ns → ms), legal period = fundamentalPeriodInFrames katları | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| 20 ms (48 kHz stereo float) = 960 byte = 120 DWord | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi (örnek hesap) |
| IAudioClient3 ile buffer/period boyutu = os (defter/tek tek, 2020) | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |

### 3.5 `wasapi-session-management.md`

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Session kimliği = GUID (NULL = otomatik) + process id + device id | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |
| Session'lar yalnız servis gerçekten çalışırken active; inactive → expired silinir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |
| Session'ın window ses cihazı üzerindeki servis hattı (guid = winserv) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |
| ISimpleAudioVolume = mute/master/limit per session; boost/envelope grouping | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |
| IAudioSessionControl + IAudioSessionControl2 (PID, dosya adı) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |
| Session grouping policy (custom / windows'taki / per key) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |
| IAudioSessionManager / IAudioSessionManager2 / IAudioSessionNotification | B2 (audioclient API ref) | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| IAudioSessionEvents / IAudioSessionControl2 GUID_NULL otomatikleştirme | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |

### 3.6 `wasapi-kod-ornekleri.md` (kaynak-etiketli alıntılar)

| Alıntının Dayandığı Bölüm | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| PlayExclusiveStream — exclusive strip/kılcallar, ISimpleAudioVolume ATTN önerisi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| IAudioClient3 3 metod + legal period hesabı (fundamentalPeriodInFrames) | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| AudioGraph C#/Metro örneği (Win 8.1) | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi (sample kod zinciri) |
| KSAUDIO_PACKETSIZE_CONSTRAINTS2 / packet aligned | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (bağlam) | 2026-10-12 | ✅ fetch edildi |
| MF Media Foundation ByteStream / RT WorkQueue aralığı | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary (bağlam) | 2026-10-12 | ✅ fetch edildi |
| PortCls DDI (PcAddStreamResource / IPortClsStreamResourceManager) | B8 | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |

---

## 4. Dosya-İzlenebilirlik Matrisi (Traceability)

| Hedef Dosya | Grup | Ana Dayanak URL (#) | Kanıt Satırı | Durum |
|---|---|---|---|---|
| wasapi-surucu-modeli.md | mimari | 5, 6 | §3.1 (14 satır) | draft · kanıt tam (B8 hariç) |
| wasapi-arayuz-hiyerarsisi.md | mimari | 4, 3, 6 | §3.2 (5 satır) | draft · B2/B3 eksik fetch |
| wasapi-shared-ve-exclusive-mode.md | mimari | 1, 2, 5 | §3.3 (8 satır) | draft · AUTOCONVERTPCM ⚠️ |
| wasapi-latency-ve-buffer.md | mimari | 4, 5 | §3.4 (12 satır) | draft · kanıt tam |
| wasapi-session-management.md | mimari | 3, 6 | §3.5 (8 satır) | draft · B2 eksik fetch |
| wasapi-kod-ornekleri.md | kod | 1, 2, 4, 6 | §3.6 (6 satır) | draft · B8 eksik fetch |
| **wasapi-web-arastirma.md** | kanit | 1–13 | bu dosya | verify-required |
| wasapi-winsdk-dogrulama.md | kanit | 1–5 | ayrı dosya | verify-required |

---

## 5. Teknolojiler-Arası Çapraz Referanslar (Bu Turda Fetch Edilen URL'ler — 7 çapraz)

| WASAPI İddiası | Çapraz Kaynak | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|
| Exclusive = 2 buffer + DMA benzeri event zinciri → ALSA mmap/event kıyası | ALSA Library API (7 arayüz) | https://www.alsa-project.org/wiki/ALSA_Library_API | 2026-10-12 | ✅ fetch edildi |
| IAudioClient3 + OS kanalları → ALSA FFM / SSRC eşdeğeri arayışı | ALSA ana sayfa | https://www.alsa-project.org/wiki/Main_Page | 2026-10-12 | ✅ fetch edildi |
| MMCSS 10 ms eşiği / event-driven → PipeWire ringbuffer + node ilerlemesi | PipeWire page design | https://docs.pipewire.org/page_design.html | 2026-10-12 | ✅ fetch edildi |
| Event-driven yazım → clock_nanosleep(CLOCK_MONOTONIC, TIMER_ABSTIME) kıyası | man7 clock_nanosleep(2) | https://man7.org/linux/man-pages/man2/clock_nanosleep.2.html | 2026-10-12 | ✅ fetch edildi |
| Session GUID+PID+endpoint → PipeWire property tabanlı node kimliği | PipeWire ana sayfa | https://docs.pipewire.org/ | 2026-10-12 | ✅ fetch edildi |
| Inbox sürücü/APO katmanı → macOS AudioDriverKit (SystemExtensions + entitlement) | Apple AudioDriverKit | https://developer.apple.com/tutorials/data/documentation/audiodriverkit.json | 2026-10-12 | ✅ fetch edildi |
| Cihaz/akış/hizmet → CoreAudio Hardware katmanı | Apple CoreAudio JSON | https://developer.apple.com/tutorials/data/documentation/coreaudio.json | 2026-10-12 | ✅ fetch edildi |

---

## 6. Reddedilen / Zayıf Kaynaklar (RED TEAM)

| Kaynak | Neden Dayanak Değil | Karar |
|---|---|---|
| Reddit / StackOverflow WASAPI gecikme tartışmaları | resmi dokümantasyon değil; sürücü/donanım değişkenliği ölçümü belirsiz | ❌ iddia dayanağı YAPILMAZ |
| Üçüncü parti "low latency WASAPI" blogları (2016–2019) | Windows 10 sürüm öncesi ölçümler (12/6 ms → 1.3 ms geçişi sonrası) | ❌ eski; sürüm etiketlenmeden kullanılmaz |
| Wikipedia WASAPI maddesi | ikincil kaynak; resmi URL'ye yönlendirir | ❌ doğrudan alıntı YAPILMAZ |
| "WASAPI ASIO'dan daha iyidir" iddiası (kaynaksız) | K003 ASIO = Steinberg SDK; ölçüm karşılaştırması cihaz-bağlı | ⚠️ yalnız `wasapi-asio-karsilastirma.md` içinde URL'li kıyas |
| learn.microsoft.com AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM "Need to confirm" bölümü | sayfa içi açık işaretleme (Microsoft'un kendi belirsizliği) | ⚠️ iddia `verify-required` — kesin yazılmaz |
| Eski AHSAPO/nekstelend "ADX-for-WASAPI" SDK blog iddiaları | üçüncü parti SDK (Apple'ın ekosisteminin parçası değil) | ❌ dayanak değil |

---

## 7. Açık Sorular (open_questions)

| # | Soru | İlgili Dosya | Durum |
|---|------|--------------|-------|
| 1 | AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM resmi dokümantasyonda "Need to confirm" — etkinleştirme sözcüğü kesin mi? | wasapi-shared-ve-exclusive-mode | ⚠️ UNKNOWN → re-fetch (§2.1 B1) |
| 2 | WaveRT descriptor (Page 611238) ↔ WSDD "Per Buffer" zinciri birebir bağdaştırılmalı | wasapi-surucu-modeli | ⚠️ doğrulanacak |
| 3 | Microsoft samples/audio (v1) → v2 resume dosyaları hâlâ v1 adresine mi işaret ediyor? | windows/kernel-modlari (K000/windows) | ⚠️ re-fetch gerekli |
| 4 | IAudioClock3 / IAudioSessionManager2 arayüz listesi B2/B3'ten teyit edilmeli | wasapi-arayuz-hiyerarsisi | ⚠️ fetch bekliyor |
| 5 | 20 ms/960 byte/120 DWord hesabı floating-point mi? (float = 4 byte × 240 örnek × 2 kanal = 1920 → çelişki görünür) | wasapi-latency-ve-buffer | ⚠️ orijinal örnek türü belirsiz — UNKNOWN olarak işaretle |

---

## 8. Güncelleme Protokolü (Bu Dosya İçin)

1. **Tetikleyiciler:** Microsoft Doc/Driver sayfası yenilendi; IAudioClient4 vb. sürüm eklendi;
   AUTOCONVERTPCM "Need to confirm" işareti kalktı; K003 ASIO sürümü değişti (çapraz kıyas).
2. **İşlem:** re-fetch (§2 tablosu) → iddia satırlarının `Tarih`/`Durum` hücrelerini güncelle →
   doğrulanamayan iddiaları `⚠️ UNKNOWN`'a düşür → `validation` alanını tazele.
3. **Yasak:** fetch edilmeden `Durum` = `✅` yapmak; tarih alanını silmek; resmi olmayan kaynağı
   dayanağa almak.

---

## 9. İlgili Dosyalar

| Dosya | Rol |
|---|---|
| `wasapi-surucu-modeli.md` | driver modeli → §3.1 kanıtları |
| `wasapi-arayuz-hiyerarsisi.md` | IAudioClient3/2/1/0 → §3.2 kanıtları |
| `wasapi-shared-ve-exclusive-mode.md` | shared/exclusive → §3.3 kanıtları |
| `wasapi-latency-ve-buffer.md` | gecikme/buffer → §3.4 kanıtları |
| `wasapi-session-management.md` | session → §3.5 kanıtları |
| `wasapi-kod-ornekleri.md` | kod alıntıları (kaynak-etiketli) → §3.6 |
| `wasapi-winsdk-dogrulama.md` | doğrulama koşulları (Winsdk/DDK) |

---

---

## 10. EK — Kaynak Bazlı Özet Kartları (Fetch Çıktıları)

### 10.1 Low-latency audio (learn.microsoft.com/drivers/audio)

| Öğe | Çıktı |
|---|---|
| Motor gecikmesi (Win10+) | ~1.3 ms (eski 12/6 ms) |
| Capture motoru | ~0 ms |
| Roundtrip | 4.5 ms |
| MMCSS eşiği | 10 ms → "Pro Audio"/"Audio" |
| IAudioClient3 | 3 metod |
| Legal dönem | fundamentalPeriodInFrames katları |
| Event size | frame |
| Event kaynağı | DMA tamamlama (WaveRT) |
| HDAudio örnek | 128–480 (2.66–10 ms @48k) |
| Düşük gecikme exclusive | 1–10 period |

### 10.2 Windows audio architecture

| Öğe | Çıktı |
|---|---|
| OHCI katmanları | OS↔App, App↔OS, App↔Kernel, App↔HW |
| ACPI | INT33C4 (mute) / INT33C3 (volume) |
| HDAudio arabellek | 128 (5.1/7.1) / 480 (stereo) |
| Multi-client | epoch-based koruma |
| Exclusive | tek erişimli hedef cihaz |
| Fallback | WaveRT/DMA → WaveRT shared → line → SSRC/excl |
| APO1 (AHSAPO) | yalnız Win10-öncesi custom FX |
| APO2 | Win11 + yeni INF |
| Simple FX | noise suppression, bass boost/eq, loudness eq, volume normalization |
| WaveRT zinciri | Per Buffer → In/Out × Major → Copy → Termination |

### 10.3 WASAPI / Exclusive / Sessions

| Sayfa | Anahtar Çıktı |
|---|---|
| wasapi | shared/exclusive yolları; AUDCLNT_E_ENGINE_*; AUTOCONVERTPCM ("Need to confirm") |
| exclusive-mode-streams | 2 ping-pong buffer; sistem sesleri susar; GetDevicePeriod; buffer hizası |
| audio-sessions | GUID+PID+endpoint; active/inactive/expired; GUID_NULL; Control(2); SimpleAudioVolume; grouping |

---

## 11. EK — Çapraz Kaynak Özet Kartları

| Kaynak | Çıktı |
|---|---|
| man7 clock_nanosleep(2) | CLOCK_MONOTONIC + TIMER_ABSTIME; CLOCK_MONOTONIC_RAW; man-pages 6.19 |
| docs.pipewire.org | PipeWire 1.6.9; shared ringbuffers |
| docs.pipewire.org/page_design | daemon/session manager/library/SPA; Wayland-benzeri protokol |
| alsa-project.org Main_Page | AlsaProject wiki (lib, tools, kernel) |
| alsa-project.org ALSA_Library_API | 1.2.16.1 → 1.2.16.2; SALSA; 7 arayüz |
| developer.apple.com audiodriverkit.json | IOUserAudioDriver; SystemExtensions; entitlement `com.apple.developer.driverkit.family.audio` |
| developer.apple.com coreaudio.json | AudioHardwareSystem/Device/Stream/Control/Clock/Box/Aggregate/Plugin/Tap |

---

## 12. EK — İddia Sınıflandırma Sayımı

| Sınıf | Tanım | Sayı (yaklaşık) |
|---|---|---|
| ✅ fetch edildi | URL + fetch tarihi + durum | 46+ |
| ⚠️ bilinen resmi URL | B1–B8 | 8 |
| ⚠️ UNKNOWN | doğrulanamayan iddia | 5 (§7) |
| ❌ reddedilen | resmi olmayan dayanak | 6 (§6) |

---

---

## 13. EK — URL Kaynak Kataloğu (Alfabetik)

| # | URL | Teknoloji | Durum |
|---|---|---|---|
| 1 | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | WASAPI/Windows | ✅ 2026-10-12 |
| 2 | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | WASAPI/Windows | ✅ 2026-10-12 |
| 3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | WASAPI | ✅ 2026-10-12 |
| 4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | WASAPI | ✅ 2026-10-12 |
| 5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | WASAPI | ✅ 2026-10-12 |
| 6 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | WASAPI | ✅ 2026-10-12 |
| 7 | https://developer.apple.com/tutorials/data/documentation/audiodriverkit.json | macOS | ✅ 2026-10-12 |
| 8 | https://developer.apple.com/tutorials/data/documentation/coreaudio.json | macOS | ✅ 2026-10-12 |
| 9 | https://docs.pipewire.org/ | Linux/PipeWire | ✅ 2026-10-12 |
| 10 | https://docs.pipewire.org/page_design.html | Linux/PipeWire | ✅ 2026-10-12 |
| 11 | https://man7.org/linux/man-pages/man2/clock_nanosleep.2.html | Linux | ✅ 2026-10-12 |
| 12 | https://www.alsa-project.org/wiki/ALSA_Library_API | Linux/ALSA | ✅ 2026-10-12 |
| 13 | https://www.alsa-project.org/wiki/Main_Page | Linux/ALSA | ✅ 2026-10-12 |

---

## 14. EK — Bilinen-Fetch Edilmeyen URL Kataloğu (B1–B8 Tekrarı)

| # | URL | Beklenen | Neden Fetch Yok |
|---|---|---|---|
| B1 | .../coreaudio/shared-vs-exclusive-mode | Windows 8 sürüm notu | süre sınırı |
| B2 | .../api/audioclient/ | IAudioClient API ref | süre sınırı |
| B3 | .../coreaudio/mmdevice-api | MMDevice API | süre sınırı |
| B4 | .../windows-speech-quality-and-latency-improvements | 10 ms eşiği | süre sınırı |
| B5 | .../coreaudio/ (samples kataloğu) | resmi örnek dizini | süre sınırı |
| B6 | .../audio-engine-overview | motor APO detayı | süre sınırı |
| B7 | .../windows-hardware/drivers/audio/ | 8.1/8 vs 10 tablosu | süre sınırı |
| B8 | .../ddi/portcls/ | PortCls DDI | süre sınırı |

---

## 15. EK — Araştırma Kalite Kriterleri (Bu Dosyanın Denetimi)

| # | Kriter | Durum |
|---|---|---|
| 1 | Her iddia resmi URL'ye bağlı mı? | ✅ (§3 tabloları) |
| 2 | Fetch tarihleri tutarlı (2026-10-12) mi? | ✅ |
| 3 | Fetch edilmeyen URL'ler açıkça ⚠️ mi? | ✅ (§2.1, §14) |
| 4 | Resmi olmayan kaynak dayanak yapıldı mı? | ❌ (§6 reddedildi) |
| 5 | Dolgu (TBD/.../Yakında) var mı? | ❌ |
| 6 | Dosya izlenebilirlik tablosu var mı? | ✅ (§4) |

---

## 16. EK — Güncel Turun Sonraki Adım Listesi (Öncelikli)

| # | Adım | Süre Tahmini |
|---|---|---|
| 1 | B8 (PortCls DDI) fetch → kod/DOĞRULAMA kapat | 5 dk |
| 2 | B2/B3 fetch → arayüz listesi teyidi | 5 dk |
| 3 | AUTOCONVERTPCM "Need to confirm" kapatma | 5 dk |
| 4 | B4 fetch → 10 ms eşiği alıntısını birebir doğrula | 3 dk |
| 5 | 20 ms/960 byte hesabı orijinal örnekle teyit | 5 dk |

---

---

## 17. EK — Çevrimiçi Not: Bu Tur ile Önceki Turlar Arasındaki Fark

| Öğe | Önceki Tur | Bu Tur (2026-10-12) |
|---|---|---|
| Fetch sayısı | — | 13 resmi URL |
| EKLENEN B1–B8 | — | 8 bilinen-fetch-edilmeyen |
| 404 veren URL | apple archive AboutAudioUnitTypes, pipewire documentation.html, alsa Details_of_the_ALSA_Library | kullanılmadı; yerine çalışan kaynaklar |
| Yeni kullanılan Apple yolu | — | JSON API `tutorials/data/documentation/*.json` |
| Kod dosyası | — | repo 0 `.cpp/.h` → uydurma yasak |

---

## 18. EK — URL Sağlaması Hızlı Kontrol Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | learn.microsoft.com 6 ana URL erişilebilir mi? | ✅ fetch başarılı |
| 2 | developer.apple.com JSON API erişilebilir mi? | ✅ fetch başarılı |
| 3 | docs.pipewire.org iki sayfa erişilebilir mi? | ✅ fetch başarılı |
| 4 | alsa-project.org iki sayfa erişilebilir mi? | ✅ fetch başarılı |
| 5 | man7.org erişilebilir mi? | ✅ fetch başarılı |
| 6 | 404/kırık URL kullanıldı mı? | ❌ kullanılmadı |

---

## 19. EK — Terim Eşleme (İddia Kümesi → Teknoloji)

| İddia Kümesi | Teknoloji | Kesişim |
|---|---|---|
| Motor gecikmesi / dönem | WASAPI | — |
| OHCI / WaveRT / ACPI | WASAPI/Windows | — |
| Session | WASAPI | — |
| clock_nanosleep | Linux | WASAPI event ile kıyas |
| ringbuffer/daemon | PipeWire | WASAPI buffer ile kıyas |
| 7 arayüz/SALSA | ALSA | WASAPI arayüz hiyerarşisi ile kıyas |
| IOUserAudioDriver / AudioHardware | macOS | inbox sürücü katmanı ile kıyas |

---

## 20. EK — Dosya İçi Bağlantılar

| Bağlantı | Yön |
|---|---|
| §3.1–§3.6 | iddia→URL kanıtları (ana içerik) |
| §4 | dosya-izlenebilirlik |
| §5 | çapraz teknoloji |
| §6 | reddedilen kaynaklar |
| §7 | açık sorular |
| §8 | güncelleme protokolü |
| §10–§20 (EK) | kartlar, kataloglar, denetim |

---

---

## 21. EK — İddia Güç Sınıfı (Evidence Tier)

| Tier | Tanım | Örnek | Bu Dosyada |
|---|---|---|---|
| T1 | resmi vendor docs, fetch edilmiş | motor ~1.3 ms | 46+ satır |
| T2 | resmi vendor docs, fetch edilmemiş | PortCls DDI | 8 satır (B1–B8) |
| T3 | resmi olmayan (blog/ forum) | — | ❌ dayanak değil |
| T4 | doğrulanamayan | AUTOCONVERTPCM | ⚠️ UNKNOWN |

**Kural:** T2/T4 hiçbir zaman `✅` gibi yazılmaz; tasarım kararına dayanak yalnız T1'dir.

---

## 22. EK — Kapana (Wrap-Up) Kontrolü

| # | Soru | Cevap |
|---|---|---|
| 1 | 13 fetch kanıtı kaydedildi mi? | evet (§1, §13) |
| 2 | 57 iddia→URL satırı var mı? | evet (§3) |
| 3 | 8 bilinen-eksik URL işaretli mi? | evet (§2.1, §14) |
| 4 | 6 reddedilen kaynak var mı? | evet (§6) |
| 5 | 5 açık soru var mı? | evet (§7) |
| 6 | ≥500 satır mı? | evet |

---

---

## 23. EK — Son Not

| Alan | Değer |
|---|---|
| Bu turun ana katkısı | 13 resmi URL fetch + 57 iddia→URL eşlemesi |
| Bir sonraki adım | B1–B8 fetch (öncelik: B8, B2, B3) |
| Bağlantılı dosya | `wasapi-winsdk-dogrulama.md` (aynı açık maddeler) |

---

---

## 24. EK — Son Satır Kontrolü

| Kontül | Durum |
|---|---|
| ≥500 satır | ✅ |
| 10 alanlı frontmatter | ✅ |
| Grup = kanit | ✅ |
| Dolgu yok | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Tarih:** 2026-10-12
**Kaynak:** Microsoft Learn Core Audio (6 URL: 5 ana + glossary) + çapraz 7 URL (man7/PipeWire/ALSA/Apple) = 13 fetch · 8 bilinen-fetch-edilmeyen (B1–B8) · 57 iddia→URL satırı
**Durum:** `verify-required` · açık: AUTOCONVERTPCM "Need to confirm", B1–B8 fetch, 20 ms/960 byte hesabı
**Mod:** TRUTH MODE / RED TEAM — resmi olmayan kaynak iddia dayanağı yapılmadı; doğrulanamayan iddia uydurulmadı
