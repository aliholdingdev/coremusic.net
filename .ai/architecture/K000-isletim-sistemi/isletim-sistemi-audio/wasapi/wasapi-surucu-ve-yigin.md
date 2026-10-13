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

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0)
**Sıradaki adım:** `wasapi-asio-karsilastirma.md` — WASAPI↔ASIO matrisi + karar ağacı.
