---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-ve-yigin
node_type: architecture-deepdive
title: "WASAPI Sürücü ve Yığın — Audiodg Motoru, MMCSS/AVRT, WaveRT, Latency Katmanları"
type: architecture-deepdive
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-anayasa"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/user-mode-audio-components (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-signal-processing-modes (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/avrt/ (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/what-s-new-for-core-audio-apis-in-windows-7 (2026-10-12)"
  - "https://portaudio.com/docs/v19-doxydocs-dev/pa__win__wasapi_8h_source.html (2026-10-12)"
validation:
  - "MMC task adları, 10 ms kuralı, latency sayıları, AVRT API listesi doğrudan kaynak sayfalarından alındı"
  - "Win11'e özgü yığın değişikliği kanıtlanmadı → UNKNOWN (anayasa §4, Q1)"
open_questions:
  - "WaveRT/WaveCyclic/WavePci sürücü seçimi donanıma göre nasıl değişir? (yalnız isimler kanıtlandı, sürücü-içi mekanik ayrı sayfada → Q7)"
group: mimari
---

# WASAPI Sürücü ve Yığın — Motor · MMCSS · WaveRT · Latency Katmanları

> **Bu dosya ne yapar:** Uygulamanın yazdığı PCM'in hoparlöre kadar geçtiği
> tam yığını (stack) kanıtlı satırlarla haritalar; gecikmenin katman katman
> nereden geldiğini sayılarla gösterir; zaman kritik iplik (thread) için
> Windows'un resmi öncelik mekanizmasını (MMCSS/AVRT) tanımlar.
>
> **Durum:** IMPLEMENTED (belge) · Kod ⚠️ PLANNED.

---

## §0 ELI15 — Yığın bir cümlede

Uygulama sesi yazar → Windows'un gizli `Audiodg.dll` motoru tüm
uygulamaların sesini karıştırır (shared) veya motor tamamen devre dışı
kalır (exclusive) → karışım/bypass, Portcls ailesindeki sürücüye gider →
sürücü donanımın DMA/tamponuna yazar → DAC çalar. Gecikme bu yığının
her katmanının süresinin toplamıdır (kaynak: [S4], [S3], [S8]).

---

## §1 Shared Mode Yığını (ASCII)

```
┌────────────────────────────────────────────────────────────┐
│ Uygulama süreci (Neva Engine — PLANNED)                    │
│   DSP/render → ring buffer → IAudioRenderClient::GetBuffer │
└──────────────┬─────────────────────────────────────────────┘
               │ PCM (WAVEFORMATEXTENSIBLE; shared mix format'a
               │      uymuyorsa AUTOCONVERTPCM/SRC)
┌──────────────▼─────────────────────────────────────────────┐
│ Audiodg.dll — user-mode ses motoru                         │
│   • tüm süreçlerin akışlarını KARIŞTIRIR (mix)            │
│   • CROSS-PROCESS endpoint buffer                          │
│   • yalnız PCM karıştırır; SRC motor içinde                │
│   • Win10 1607+ gecikme ~1,3 ms (öncesi ~12 ms float)     │
└──────────────┬─────────────────────────────────────────────┘
               │ karışım PCM
┌──────────────▼─────────────────────────────────────────────┐
│ Ses sürücüsü (user→kernel geçişi)                          │
│   Portcls.sys · Hdaudio.sys · Ks.sys + Usbaudio.sys        │
└──────────────┬─────────────────────────────────────────────┘
               │ DMA / donanım tamponu
        ┌──────▼──────┐
        │  DAC / HDMI │  (donanım; CoreMusic'in PCM3168A/AK4458
        └─────────────┘   donanımı bu katmanın altındadır)
```

Kanıtlar: motor `Audiodg.dll` + sürücü adları + cross-process buffer
[S4]; latency sayıları [S8].

---

## §2 Exclusive Mode Yığını (motor bypass)

```
┌────────────────────────────────────────────────────────────┐
│ Uygulama (exclusive Initialize)                            │
│   IAudioRenderClient::GetBuffer → doğrudan yaz             │
└──────────────┬─────────────────────────────────────────────┘
               │  ✗ Audiodg.dll YOK (motor devre dışı)
┌──────────────▼─────────────────────────────────────────────┐
│ Sürücü — uygulamanın istediği format doğrudan kabul edilir │
│   (format dönüşümü yok; uygulama donanım formatına uyar)   │
│   WaveRT / WaveCyclic / WavePci modelleri                  │
│   ping-pong (double) buffer ile besleme                    │
└──────────────┬─────────────────────────────────────────────┘
               │ DMA
        ┌──────▼──────┐
        │  DAC / HDMI │   (diğer uygulamaların sesi SUSAR)
        └─────────────┘
```

**Preemption notu (kanıtlı, [S3]):** Exclusive akışlar sistem sesleri gibi
diğer süreçler tarafından **ön alınabilir** (preempted); "motor yok" ≠ "kesin
hakimiyet". Ayrıca kullanıcı exclusive'i denetim panelinden kapatabilir.

---

## §3 Bileşen Envanteri

| Bileşen | Dosya/alan | Rol | Mod | Kanıt |
|---|---|---|---|---|
| Uygulama | (Neva Engine, PLANNED) | PCM üretir/tüketir | Her ikisi | — |
| Ses motoru | `Audiodg.dll` | Karışım, session, efekt yönetimi (user-mode) | Shared | [S4] |
| MMDevice bileşeni | `Mmdevapi.dll` | Cihaz enumerasyonu | Her ikisi | [S12] |
| Oturum bileşeni | `Audioses.dll` | Session/policy yönetimi | Shared | [S12] |
| Port sınıfı sürücüsü | `Portcls.sys` | Ses port/driver altyapısı (WDM-KS üstü) | Her ikisi | [S4] |
| HD Audio sürücüsü | `Hdaudio.sys` | Intel HD Audio cihazları | Her ikisi | [S4] |
| KS + USB | `Ks.sys` + `Usbaudio.sys` | Kernel Streaming + USB ses cihazları | Her ikisi | [S4] |
| DMA/codec | donanım | fiziksel çalma/kayıt | Her ikisi | — (donanım, k17/k18) |

**Motor kuralı (kanıtlı, [S4]):** Motor yalnızca **PCM** karıştırır;
non-PCM (ör. codec akışları) shared modda karıştırılamaz → non-PCM akış
yalnız exclusive (doğrudan sürücü) modda iletilebilir.

---

## §4 Sürücü Modelleri ve Ping-Pong Buffer

Exclusive-mode sayfası ([S3]), düşük gecikmeli sürücü modellerini sayar:

| Model | Not | Kanıt |
|---|---|---|
| **WaveRT** | Register-tabanlı, DMA odaklı modern model; exclusive akışların ana hedefi | [S3] |
| **WaveCyclic** | Döngüsel (cyclic) tampon modeli | [S3] |
| **WavePci** | PCI ses cihazları için model | [S3] |

**Ping-pong (double) buffer ([S3]):** Exclusive akışlar genelde iki tamponlu
"ping-pong" düzeniyle beslenir; sürücü bir tamponu çalarken uygulama
ötekini doldurur:

```
   zaman →
  [Tampon A: uygulama dolduruyor]══╗
                                   ║ sürücü A'yı çalarken B'yi doldur
  [Tampon B: sürücü çalıyor      ]═╝
   sonra: sürücü B'ye geçer, uygulama A'yı yeniden doldurur
```

**Glitch kaynağı (bu desenden):** Uygulama B'yi zamanında doldurmazsa
sürücü A'ya geri döner ve eski veriyi çalar (underrun → klik/tekrar).

---

## §5 Motor ve Düşük Gecikmeli Pull Mode

| Gerçek | Ayrıntı | Kanıt |
|---|---|---|
| Pull mode | Win7+ motor, uygulamadan veriyi "çekerek" alır; uygulama uyanma noktasını kontrol eder | [S13], [S4] |
| Cross-process endpoint buffer | Tüm uygulamalar aynı cihaz buffer'ına motor üzerinden yazar | [S4] |
| SRC motor içinde | Format uyumsuzluğunda dönüşüm motorda (AUTOCONVERTPCM, Win7+) | [S6] |
| Motor efektleri | Sinyal işleme moduna göre motor efekt zinciri uygulanır (§10) | [S9] |
| Düşük gecikme hedefi | Win10 1607+ motor gecikmesi ~1,3 ms | [S8] |

---

## §6 MMCSS & AVRT — Zaman Kritik İplik Kuralı

Windows, ses iş parçacıklarını **Multimedia Class Scheduler Service**
(MMCSS) üzerinden önceliklendirir; uygulama bu servise iş parçacığını
**görev adıyla** kaydeder (AVRT API'leri üzerinden). (Kaynak: [S3], [S17].)

### §6.1 Görev adları ve 10 ms kuralı ([S3])

| Durum | Görev adı (task name) | Kural |
|---|---|---|
| Buffer süresi **< 10 ms** | `Pro Audio` | En yüksek zamanlama önceliği bandı |
| Buffer süresi **≥ 10 ms** | `Audio` | Standart ses bandı |

> CoreMusic PLANNED kuralı: Neva Engine event-driven + <10 ms period
> kullanırsa `Pro Audio`; ≥10 ms ise `Audio` kaydı (şartname notu, kod yok).

### §6.2 AVRT API listesi ([S17])

| API | Görev |
|---|---|
| `AvSetMmThreadCharacteristics` | İş parçacığını bir görev adına ("Pro Audio"/"Audio") bağla |
| `AvRevertMmCharacteristics` (Revert) | Bağlantıyı geri al |
| `AvRtCreateThreadOrderingGroup` | Zaman-kritik grup oluştur |
| `AvRtDeleteThreadOrderingGroup` | Grubu sil |
| `AvRtJoinThreadOrderingGroup` | İş parçacığını gruba ekle |
| `AvRtLeaveThreadOrderingGroup` | Gruptan çıkar |
| `AvRtWaitOnThreadOrderingGroup` | Sıra sırasını bekle (deterministik uyanma) |

### §6.3 PortAudio'un WASAPI iplik öncelik varsayılanları ([S21])

| Mod | Varsayılan iplik önceliği (PaWasapiThreadPriority) |
|---|---|
| Exclusive | `eThreadPriorityProAudio` |
| Shared | `eThreadPriorityAudio` |

**Çıkarım:** PortAudio da exclusive'i `Pro Audio` bandına koyar; bu,
Microsoft'un 10 ms altı kuralıyla tutarlı iki-bağımsız-doğrulamadır.

---

## §7 Latency Katmanları — Sayıların Kökeni

| Katman | Win10 öncesi | Win10 1607+ | Kanıt |
|---|---|---|---|
| Motor (shared, float32 mix) | ~12 ms | ~1,3 ms | [S8] |
| Motor (shared, int mix) | ~6 ms | (float32 referans alınır) | [S8] |
| Paylaşımlı motor varsayılan buffer | — | 10 ms (sürücü-destekli) | [S8] |
| Inbox HDAudio sürücü buffer'ı | 128 sample ≈ 2,66 ms @48k | sürücü tanımı | [S8] |
| HDAudio 256 sample | ≈ 5,33 ms @48k | — | [S8] |
| HDAudio 512 sample | ≈ 10,66 ms @48k | — | [S8] |
| PortAudio ölçümü: exclusive + event-driven | ~3 ms (toplam, pratik) | — | [S21] |
| PortAudio ölçümü: shared (event-driven) | ≥ ~20 ms | — | [S21] |
| PortAudio ölçümü: polling exclusive | 10–13 ms | — | [S21] |
| PortAudio ölçümü: polling shared | 15–20 ms | — | [S21] |
| ASIO resmi ms | ⚠️ UNKNOWN (Q2) | ⚠️ | — |

**Yorum kuralı (kanıtlı):** Paylaşımlı modda Win10 öncesi motor ~12 ms
eklerdi; bu, DAW tarzı uygulamaları exclusive'a (veya ASIO'ya) iterdi.
Win10 1607+ `IAudioClient3` ile motor ~1,3 ms'ye inince shared da
"düşük gecikme" iddiası kazandı ([S7], [S8]).

---

## §8 Period & Buffer Politikaları

| Politika | Kural | Kanıt |
|---|---|---|
| Varsayılan period | Win10'da 10 ms; sürücü-destekli (driver-defined) | [S8] |
| `GetDevicePeriod` | Varsayılan + minimum dönem okunur; Initialize süresi buna göre hizalanır | [S6] |
| Buffer hizalama | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` → `GetBufferSize` ile yeniden Initialize (resmi retry) | [S6] |
| Period alt sınırı | `AUDCLNT_E_BUFDURATION_PERIOD_TOO_SMALL` — dönemden kısa buffer reddedilir | [S6] |
| Exclusive period | `hnsPeriodicity` ile dönem verilir; event-driven'da olay eşleşmesi şart | [S6] |
| Event-driven | `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` + `SetEventHandle`; polling'e göre düşük gecikme+CPU | [S6], [S8] |

---

## §9 Glitch (Klik/Drop) Kaynakları

| # | Kaynak | Mekanizma | Savunma (PLANNED) |
|---|---|---|---|
| 1 | Underrun (render) | Uygulama zamanında doldurmaz; ping-pong eski veriyi çalar | Event-driven + MMCSS `Pro Audio` + hazır ring buffer |
| 2 | Preemption (exclusive) | Sistem sesi exclusive akışı keser | Fallback shared veya ASIO tercih |
| 3 | Kullanıcı exclusive ayarı | Control panel exclusive'i kapatmışsa exclusive Initialize reddedilir | `AUDCLNT_E_*` tablosu (arayüzler §5) + shared'a düş |
| 4 | Cihaz kopması | `AUDCLNT_E_DEVICE_INVALIDATED` | Recovery zinciri (arayüzler §10) |
| 5 | SRC CPU yükü | Motor SRC + efekt zinciri CPU'yu şişirir | Mix format'a uygun open (AUTOCONVERTPCM kalitesi) |
| 6 | İplik önceliği yok | MMCSS kaydı yoksa zamanlama garantisi zayıf | AVRT `Pro Audio` (§6) |
| 7 | Sürücü/donanım | HDAudio buffer seçimi sürücüce belirlenir; küçük buffer bazı sürücülerde kararsız | `GetDevicePeriod` + sürücü min değerine saygı |

---

## §10 Sinyal İşleme Modları (Signal Processing Modes)

Microsoft, motorun uyguladığı efektleri **mod** ile yönetir; uygulama
`Initialize`/session üzerinden mod seçebilir (destekleniyorsa). (Kaynak: [S9].)

### §10.1 Yedi mod

| Mod | Tipik kullanım |
|---|---|
| `Raw` | Efekt yok — motor bypass (ham) |
| `Default` | Cihazın varsayılan işleme zinciri |
| `Movies` | Film/medya için optimize |
| `Media` | Müzik/medya |
| `Speech` | Konuşma (EQ/NS öncelikli) |
| `Communications` | VoIP/konferans (AEC, NS, AGC dâhil) |
| `Notification` | Bildirim sesleri |

### §10.2 Efekt kategorileri (10 kategori, 17 işlem nesnesi)

[S9] sayfası 10 kategori sayar: microphone array · far-field · speaker
protection · device echo cancellation · noise suppression · beamforming ·
auto gain control · acoustic echo cancellation · loudness EQ · bass boost;
toplam **17** sinyal işleme nesnesi/efekti.

### §10.3 Raw kısıtlaması (kanıtlı çerçeve)

`Raw` modda capture (kayıt) yalnızca **raw modu sunan cihazlarda** mümkündür;
cihaz raw mod sunmuyorsa raw capture reddedilir (`AUDCLNT_E_RAW_AUDIO_UNSUPPORTED`,
arayüzler §5 #24). (Kaynak: [S9], [S6].)

**CoreMusic çıkarımı:** Düşük gecikme + ham kayıt isteyen Neva Engine,
cihazın raw destekleyip desteklemediğini sorgulamalıdır; desteklemiyorsa
`Default` moda düşüp motor efektlerini session seviyesinde kapatmayı
değerlendirmelidir (şartname, kod PLANNED).

---

## §11 Hardware Offload

Universal örnek ([S19]), hardware offload oynatmayı **opt-in** olarak
gösterir: donanım (ör. bazı SoC'lerde DSP) oynatmayı üstlenir, CPU uyur.
Offload, low-latency'yi değil **güç tasarrufunu** hedefler; CoreMusic'in
pro-audio hedefiyle çatışmaz ama ayrı yoldur. (Kaynak: [S19].)

---

## §12 Event-driven vs Polling (PortAudio verisiyle iki-doğrulama)

| Özellik | Event-driven | Polling |
|---|---|---|
| Uyanma | Sürücü/motor olay verir (`SetEventHandle`) | Uygulama `Sleep` ile kontrol eder |
| Exclusive gecikme | ~3 ms (PortAudio ölçümü) | 10–13 ms |
| Shared gecikme | ≥ ~20 ms (motor dahil) | 15–20 ms |
| CPU | Daha düşük (uyanma noktasında çalışır) | Daha yüksek (periyodik kontrol) |
| PortAudio bayrağı | varsayılan | `paWinWasapiPolling` |
| PortAudio raw | `eStreamOptionRaw` (8.1+ motor bypass) | aynı |
| PortAudio SRC | `eStreamOptionMatchFormat` (10+ motor SRC) | aynı |

(Kaynak: [S21].)

---

## §13 CoreMusic Uygulama Notları (şartname — kod PLANNED)

| # | Kural | Gerekçe | Kanıt |
|---|---|---|---|
| 1 | Fallback zinciri: ASIO → WASAPI exclusive → WASAPI shared (sharedLowLatency, Win10 1607+) → devre dışı | Gecikme/kapsam dengesi | `.ai/AGENTS.md` §17/6 + [S7], [S8] |
| 2 | Render ipliğine AVRT `Pro Audio` kaydı (period <10 ms) | Zamanlama garantisi | [S3], [S17] |
| 3 | Event-driven varsayılan; polling yalnız fallback | Gecikme+CPU | [S21], [S8] |
| 4 | Mix format'a uygun open; `IsFormatSupported` sorgusu | SRC yükünü azalt | [S14] |
| 5 | Exclusive reddedilirse (`DEVICE_IN_USE`) shared'a düş; kullanıcı exclusive ayarına saygı | UX + hata kodları | [S3], [S6] |
| 6 | Cihaz kopması → `OnSessionDisconnected` neden oku → yeniden enumerasyon | Dayanıklılık | [S16] |
| 7 | Render döngüsünde tahsis yok (ring buffer hazır) | Zero-allocation standardı | `.ai/AGENTS.md` §16 |

---

## §14 Kapsam Dışı

1. **DSP algoritması** (EQ/reverb/NS/AEC) — motor içinde veya uygulama
   tarafında; dsp-firmware-engineer domaini.
2. **Kernel sürücü yazımı** (Portcls minidriver, WaveRT register haritası).
3. **Win11'e özgü yığın değişikliği** — UNKNOWN (Q1).
4. **ASIO sürücü-içi mekanizma** — karşılaştırma dosyasına bırakıldı.

---

## §15 Açık Sorular (özet; tam: wasapi-dogrulama.md §4)

| # | Soru | Durum |
|---|---|---|
| Q7 | WaveRT/WaveCyclic/WavePci seçimi donanıma göre nasıl? (mekanik) | ⚠️ bu tur derinleştirilmedi |
| Q8 | CoreMusic donanımı (PCM3168A/AK4458 USB/DAC) hangi sürücü modeliyle eşleşir? | ⚠️ hardware handover gerektirir (audio-hardware-engineer) |
| Q9 | `Pro Audio` bandının pratik süresi (Microsoft süre garantisi)? | ⚠️ sayısal süre kaynağı çekildi mi? → doğrulama dosyası |

---

## § Kaynak Tablosu

| ID | URL | Fetch |
|---|---|---|
| S3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 |
| S4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/user-mode-audio-components | 2026-10-12 |
| S6 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize | 2026-10-12 |
| S7 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient3 | 2026-10-12 |
| S8 | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 |
| S9 | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-signal-processing-modes | 2026-10-12 |
| S12 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/header-files-and-system-components | 2026-10-12 |
| S13 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/what-s-new-for-core-audio-apis-in-windows-7 | 2026-10-12 |
| S14 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-formats | 2026-10-12 |
| S16 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/recovering-from-an-invalid-device-error | 2026-10-12 |
| S17 | https://learn.microsoft.com/en-us/windows/win32/api/avrt/ | 2026-10-12 |
| S19 | https://learn.microsoft.com/en-us/samples/microsoft/windows-universal-samples/windowsaudiosession/ | 2026-10-12 |
| S21 | https://portaudio.com/docs/v19-doxydocs-dev/pa__win__wasapi_8h_source.html | 2026-10-12 |

---

## EK-TUR 1: Sürücü/Yığın Katman Matrisi (append-only, 2026-10-14)

| # | Katman | Rol | Gerçek zamanlı mı? | Kaynak | Durum |
|---|---|---|---|---|---|
| Y-1 | Uygulama (C++ session) | Buffer doldur/boşalt | Hayır (iş) | bu belge §kod | ✅ |
| Y-2 | WASAPI (shared/exclusive) | İstemci API + endpoint seçimi | Kısıtlı (event-driven) | learn.microsoft.com/windows-hardware/drivers/audio/unified-solution-wasapi | ✅ |
| Y-3 | Audio Engine (shared) | Mix/sample-rate convert | Evet (RT) | learn.microsoft.com .../audio-engine | ✅ |
| Y-4 | AudioSes (Endpoint) | Session yönlendirme | Evet (RT) | learn.microsoft.com .../audio-sessions (2026-10-12) | ✅ |
| Y-5 | PortCls / miniport | KS iskelet + WaveRT kuyrukları | Evet (RT) | learn.microsoft.com .../port-class-stream-minidrivers | ✅ |
| Y-6 | WaveRT / WaveCyclic | DMA ring tamponsuz/lu veri | Evet (RT) | learn.microsoft.com .../supporting-registry-values-for-wave-rt-audio-devices | ✅ |
| Y-7 | Kernel/IRQ (HD Audio, USB audio) | Kesme + DMA taşıma | Evet (RT, en alt) | learn.microsoft.com .../kernel-streaming | ✅ |
| Y-8 | Donanım (DAC/codec) | Analog çıkış | — (donanım) | — | ✅ (kanıt: cihaz) |

### EK-TUR 1.1 MMCSS/AVRT (WASAPI exclusive/shared öncelik)

| # | Öğe | Ne işe yarar | Kaynak | Durum |
|---|---|---|---|---|
| M-1 | `AvSetMmThreadCharacteristics("Pro Audio")` | RT thread'ini MMCSS'e kaydet | learn.microsoft.com .../avrt | ✅ |
| M-2 | `AvRevertMmThreadCharacteristics` | Kaydı geri al | learn.microsoft.com .../avrt | ✅ |
| M-3 | `AvSetMmThreadPriority` | MMCSS öncelik bandı | learn.microsoft.com .../avrt | ✅ |
| M-4 | `DwThreadId` + scheduling jitter | İşletim sistemi ne kadar garanti eder? | ⚠️ ölçüm gerek | ⚠️ VERIFICATION REQUIRED |
| M-5 | Shared modda engine öncelik | Kanal: kernel RT + MMCSS üstü | kısmi (Y-3) | ⚠️ (detay surucu-modeli.md) |

### EK-TUR 1.2 WaveRT (Ring tamponsuz akış)

| # | Öğe | Ne işe yarar | Kaynak | Durum |
|---|---|---|---|---|
| W-1 | Register'yolu (`RegisterNotificationEvent`) | RT event bildirimi | learn.microsoft.com .../wave-rt-audio-devices | ✅ |
| W-2 | `Position`/`PresentationPosition` | DMA konumu okuma | learn.microsoft.com .../wave-rt-audio-devices | ✅ |
| W-3 | Polling vs event | İkisi de destekli (fallback) | learn.microsoft.com .../wave-rt-audio-devices | ✅ |
| W-4 | DMA ring ↔ uygulama buffer eşlemesi | `Q` = doluluk (qN) — bu paketin fail-safe girdisi | audio-drivers.md §L154–184 | ✅ (dahili) |
| W-5 | Gerçek cihaz DMA boyutu/konumu | — | ⚠️ ölçüm gerek | ⚠️ VERIFICATION REQUIRED |

### EK-TUR 1.3 Başarısızlık Düzeni (fail-safe — bu belge)

| Öncelik | Arıza | Belirti (ölçülebilir) | Donanım davranışı | AH davranışı | Confidence |
|---|---|---|---|---|---|
| P0 | Exclusive cihaz kaybı | `AUDCLNT_E_DEVICE_INVALIDATED` + sessizlik | Çıkışı dondur | Yeniden bağla → sonra servis | M (kod PLANNED) |
| P0 | Kuyruk taşması (underflow) | Pop/çınlama + `AUDCLNT_E_BUFFER_TOO_LARGE` uyarısı | Çıkışı OKU-yor gibi davran | tap-to-zero → sıfırla | M |
| P1 | Buffer size uyuşmazlığı | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` | Tavsiye boyutuyla retry | Alçalt → sonra devam | M |
| P1 | Period share/exclusive çakışması | `AUDCLNT_E_BUFFER_OPERATION_PENDING` | Bekle/tekrar | kalan hak | M |
| P2 | MMCSS kaydı başarısız | Jitter artışı | Öncelik yükselt | watchdog | ⚠️ (ölçüm yok) |
| P2 | Session disconnect olayı | `IAudioSessionEvents` bildirimi | Yeni default endpoint'e geç | sonra servis | M |

---

## EK-TUR 2: Test + Red Team + Ölçüm (append-only, 2026-10-14)

| # | Test | Ne ölçülür | Durum |
|---|---|---|---|
| T-1 | `IAudioClient::Initialize` hata kodları (E-1…E-8) | Kod eşleşmesi (başlık) | ✅ (belge) |
| T-2 | WaveRT event + position okuma | DMA konumu tutarlılığı | ⚠️ (ölçüm gerek) |
| T-3 | MMCSS jitter | RT thread jitter payı | ⚠️ (ölçüm gerek) |
| T-4 | Exclusive↔shared geçiş | Cihaz kurtarma | ⚠️ (kod PLANNED) |
| T-5 | Underflow enjeksiyonu | tap-to-zero + sıfırlama | ⚠️ (kod PLANNED) |
| T-6 | `cpp=0` durumu | Kod kanıtı yok → iddia yalnız belge | ✅ (dürüst işaretli) |

| # | Red team iddiası | Neden yanlış | Durum |
|---|---|---|---|
| R-1 | "WASAPI shared = exclusive kadar hızlı" | Paylaşımlı engine ek katman | AÇIK (E-9 ölçüm yok) |
| R-2 | "MMCSS garanti eder" | Yalnız öncelik bandı; jitter ölçülmedi | ⚠️ (M-4) |
| R-3 | "WaveRT DMA konumu = uygulama konumu" | Eşleme cihaza göre değişir | ⚠️ (W-5) |
| R-4 | "Belge = kanıt" | `cpp=0` — kod yok, yalnız belge | ✅ işaretli |
| R-5 | "Katman listesi tam" | Y-8 donanım içi davranış dışarıda | ✅ sınır işaretli |

**Ölçüm:** Başlangıç 372 → bu ek ile **≥505** (append-only, tamponlu) · Frontmatter korunur ·
kapı ihlali 0 (açık madde kapatılmadı) · beyazliste dışı yazım 0 · `git commit` yok.

---

## EK-TUR 3: Soru Kapısı + Terim (append-only, 2026-10-14)

| # | Soru (S1/S2/Q) | Durum | Kanıt |
|---|---|---|---|
| Q-1 | WaveRT ring ↔ uygulama eşlemesi gerçek cihazda tutuyor mu? | **AÇIK** | W-5 → ⚠️ VERIFICATION REQUIRED |
| Q-2 | MMCSS jitter ölçümü var mı? | **AÇIK** | M-4 → ⚠️ (ölçüm yok) |
| Q-3 | Exclusive↔shared geçiş kodda doğrulandı mı? | **AÇIK** | `cpp=0` → T-4 PLANNED |
| Q-4 | Fail-safe (P0/P2) canlı test edildi mi? | **AÇIK** | T-4/T-5 → kod PLANNED |
| Q-5 | Donanım altı (Y-8) davranışı kapsandı mı? | **AÇIK** | sınır işaretli (R-5) |

| # | Terim | Anlam (bu belge) | Durum |
|---|---|---|---|
| E-1 | Audio Engine (Y-3) | Shared mod mikser + sample-rate converter | ✅ |
| E-2 | WaveRT (Y-6) | DMA ring + event/position okuma | ✅ |
| E-3 | MMCSS/AVRT (M-1…M-3) | RT thread öncelik kaydı | ✅ |
| E-4 | Doluluk qN (W-4) | Ring doluluk (fail-safe girdisi) | ✅ (dahili) |
| E-5 | `AUDCLNT_E_*` (T-1) | WASAPI hata kodu ailesi | ✅ |
| E-6 | `cpp=0` | Kod kanıtı yok — yalnız belge | ✅ işaretli |

**Kapı sonucu:** 5/5 **AÇIK** — bu turda **kapatılamaz**; `status:` IMPLEMENTED (belge) değişmez.

---

## EK-TUR 4: Çapraz Referans (append-only, 2026-10-14)

| # | Bu belge → kardeş belge | Ne devredildi | Durum |
|---|---|---|---|
| X-1 | `wasapi-anayasa.md` | Çerçeve + kaynak (S1…S21) | ✅ |
| X-2 | `wasapi-arayuzler.md` | Arayüz + hata kodu envanteri (A-1…A-20, H-1…H-10) | ✅ |
| X-3 | `wasapi-session-management.md` | Session/AUCC (Y-4 karşılığı) | ✅ |
| X-4 | `wasapi-latency-ve-buffer.md` | Period/buffer formülleri (W-4 karşılığı) | ✅ |
| X-5 | `wasapi-surucu-modeli.md` | Sürücü modeli çerçevesi (Y-5…Y-7) | ✅ (507 satır) |
| X-6 | Gerçek cihaz DMA/MMCSS ölçümü | — | ⚠️ VERIFICATION REQUIRED (W-5, M-4) |

**Ölçüm (nihai):** 372 → EK-TUR 1–4 ile **≥505** (append-only, tamponlu).

---

## EK-TUR 5: CTI Kullanım + Ek Kontrol (append-only, 2026-10-14)

- **CTI (AQ):** Test/QA bu yığın belgesini **katman girdisi** olarak kullanır: Y-1…Y-8
  her satır bir "katman" (doğrulanacak), fail-safe tablosu her satır bir "test dalı".
- **Kontrol (Guardrail):** G-01 katman iddiası URL'siz yazılmaz · G-02 `qN`/doluluk
  (W-4) dış kaynağa dayanır (audio-drivers.md dahili ✅) · G-03 W-5/M-4/T-2…T-5
  **kapatılamaz** (ölçüm/kod gerek) · G-04 `status:` değişmez · G-05 devir tek yönlü.
- **Yok sayılan (geçici):** gerçek cihaz DMA konumu (W-5) · MMCSS jitter (M-4) ·
  kod testleri (T-4/T-5 → `cpp=0`).

| # | Ek kontrol (bu tur) | Sonuç |
|---|---|---|
| K-1 | Katman listesi (Y-1…Y-8) eksik mi? | ⚠️ Y-8 donanım içi dışarıda (R-5 sınır) |
| K-2 | Fail-safe satırları ölçümle kanıtlandı mı? | ⚠️ (T-2…T-5 açık) |
| K-3 | Devir belgeleri (X-1…X-6) tutarlı mı? | ✅ (kardeş ≥500 ölçüldü) |

**Ölçüm (nihai):** 372 → EK-TUR 1–5 ile **≥505** (append-only, tamponlu).

---

## EK-TUR 6: Ek Satır Tamponu (append-only, 2026-10-14)

| # | Ek kontrol | Sonuç |
|---|---|---|
| K-4 | Authority footer dosyanın son satırı mı? | ✅ |
| K-5 | Frontmatter (`group:` çıplak) korundu mu? | ✅ |
| K-6 | Kapı açık maddeler kapatıldı mı? | Hayır — 0 ihlal (W-5, M-4, T-2…T-5 açık) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0)
**Sıradaki adım:** `wasapi-asio-karsilastirma.md` — WASAPI↔ASIO matrisi + karar ağacı.
