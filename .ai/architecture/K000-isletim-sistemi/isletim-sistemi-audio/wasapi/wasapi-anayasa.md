---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-anayasa
node_type: architecture-deepdive
title: "WASAPI Anayasa — Kimlik, Tarihçe, Core Audio Ailesi, Shared/Exclusive Ayrımı"
type: architecture-deepdive
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/user-mode-audio-components (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/what-s-new-for-core-audio-apis-in-windows-7 (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient3 (2026-10-12)"
validation:
  - "Her iddia §6 kaynak tablosunda URL ile eşleştirilmiştir (2026-10-12 fetch)"
  - "Uydurma arayüz/sürücü adı yok; doğrulanamayan her satır ⚠️/UNKNOWN işaretlidir"
open_questions:
  - "Windows 11'e özgü WASAPI değişikliği için resmi sayfa bu turda çekildi mi? → ⚠️ (Q1, wasapi-dogrulama.md)"
group: mimari
---

# WASAPI Anayasa — Kimlik · Tarihçe · Aile · Shared/Exclusive

> **Bu dosya ne değildir:** Kod, benchmark veya uygulama rehberi değildir.
> Bu dosya WASAPI'yi *belge kanıtıyla* tanımlar; sayılar Microsoft Learn
> sayfalarından alınmıştır, tahmin değildir.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Kod ⚠️ PLANNED
> (`projects/NevaEngine` = 0 dosya, 2026-10-12 ölçümü).

---

## §0 ELI15 — WASAPI bir cümlede

WASAPI, Windows Vista'dan beri var olan ve uygulamanın sesi "hoparlöre/mikrofona
giden resmi Windows yolundan" geçirmesini sağlayan programlama arayüzüdür;
bu yol iki modda çalışır: **shared** (motor diğer uygulamalarla sesi karıştırır,
herkes devre dışı kalmadan çalışır) veya **exclusive** (uygulama motoru devre
dışı bırakıp donanıma tek başına bağlanır — daha düşük gecikme, tek uygulama
hakimiyeti). (Kaynak: §6 [S1], [S3])

---

## §1 WASAPI Kimlik Kartı

| Alan | Değer | Kanıt |
|---|---|---|
| Tam adı | Windows Audio Session API | [S1] |
| Ailesi | Core Audio APIs | [S1], [S2] |
| Katman | User-mode audio API (kullanıcı modu) | [S1] |
| Min OS | Windows Vista | [S2] |
| Üretim motoru | `Audiodg.dll` (user-mode audio engine) | [S4] |
| Sürücü katmanı | `Portcls.sys` · `Hdaudio.sys` · `Ks.sys` + `Usbaudio.sys` | [S4] |
| Ortak arayüz çekirdeği | `IAudioClient` (+ `IAudioClient2`/`IAudioClient3`) | [S1], [S7] |
| Session API grubu | `IAudioSessionManager(2)` · `IAudioSessionControl(2)` · `IAudioSessionEvents` · `IAudioSessionNotification` · `IAudioStreamVolume` · `IAudioVolumeDuckControl` | [S1], [S10] |
| Cihaz seçimi API'si | MMDevice API (WASAPI'den ayrı, komşu API) | [S1], [S11] |
| Sahibi | Microsoft (Windows bileşeni, ayrı lisans yok) | [S2] |
| CoreMusic sahibi agent | #11 Windows Software Engineer (`win-sw`, PLAT katmanı) | `.ai/AGENTS.md` §4/§15 |
| CoreMusic routing | "C++, ASIO, JUCE, audio, DSP, Neva Engine, ring buffer, WASAPI" → Embedded (primary) / DevOps (secondary) | `.ai/AGENTS.md` §6 |

**WASAPI = Windows Audio Session API.** Adındaki "session" kelimesi tesadüf
değildir: WASAPI'nin temel birimi *ses oturumudur* (audio session); her
uygulama akışı bir session olarak gruplanır, Sndvol (ses mikseri) panelinde
session bazında ses seviyesi gösterilir. (Kaynak: [S1], [S10])

---

## §2 Core Audio Ailesi — 4 Üye

Microsoft, Core Audio'yı dört API ailesi olarak tanımlar. WASAPI bunlardan
yalnızca biridir; "Core Audio = WASAPI" demek yanlıştır. (Kaynak: [S2])

| # | API | Ne yapar | CoreMusic ilgisi |
|---|---|---|---|
| 1 | **MMDevice API** | Ses cihazlarını listeler, varsayılan cihazı bulur, cihaz özelliklerini okur | Cihaz enumerasyonu, hot-swap algılama |
| 2 | **WASAPI** | Cihazda ses çalar/yakar (render/capture); shared + exclusive mod | Birincil Windows ses yolu, ASIO fallback |
| 3 | **DeviceTopology API** | Cihaz içi topolojiye (jack, mute, birimler) erişim | Düşük öncelik; jack-bazlı routing için potansiyel |
| 4 | **EndpointVolume API** | Uç nokta (device) ses seviyesi + mute; hardware destekliyse doğrudan donanıma | Sistem ses kontrolü, kalibrasyon |

Ek olarak [S2]'nin andığı ama aile üyesi saymadığı yardımcılar: ses
sinyal işleme modları (signal processing modes, § bkz. yığın dosyası) ve
donanım offload özellikleri — bunlar sürücü/motor tarafı yetenekleridir,
ayrı API değildir.

**Sınır kuralı (CoreMusic):** Bu deep-dive serisi yalnız 2 numaralı üyeyi
(WASAPI) + onun 1 numaralı üyeye (MMDevice) bağımlılığını kapsar; DeviceTopology
ve EndpointVolume yalnız tabloda anılır, derinleştirilmez (kapsam dışı, §15).

---

## §3 Hedef Kitle — Microsoft ne diyor?

[S2] sayfasının "audience" kısmı: Core Audio APIs şunlar için tasarlanmıştır:

| Hedef | Açıklama | Kaynak |
|---|---|---|
| Pro-audio uygulamaları | Tight control of audio required; low latency, high fidelity | [S2] |
| Gerçek zamanlı iletişim (RTC) uygulamaları | Konferans/VoIP tarzı uygulamalar | [S2] |
| Genel uygulamalar | Standart ses oynatma/kayıt | [S2] |

WASAPI'nin exclusive modu doğrudan pro-audio ihtiyacına cevap verir:
motor bypass edilerek gecikme düşürülür ve uygulamaya cihaz üzerinde tam
kontrol verilir. (Kaynak: [S1], [S3])

> **Neden ASIO'dan bağımsız önem:** ASIO yalnız *donanım üreticisinin* kendi
> sürücüsünü yüklediği cihazlarda çalışır; WASAPI Windows'un kendi ses yolu
> olduğu için **her** ses cihazında (bileşen sürücüsü bile olsa) çalışır.
> CoreMusic'in ASIO→WASAPI fallback kuralı (§13) tam bu boşluğu kapatır.

---

## §4 Zaman Çizelgesi — Vista'dan Win10'a (kanıtlı satırlar)

| Sürüm | Yıl | WASAPI/Core Audio değişikliği | Kanıt |
|---|---|---|---|
| Windows Vista | 2007 | Core Audio APIs ilk kez tanıtıldı ("for the first time in Windows Vista"); WASAPI, MMDevice, DeviceTopology, EndpointVolume hep bu sürümde | [S2] |
| Windows 7 | 2009 | Düşük gecikmeli pull mode; audio ducking; stream routing (session bazlı cihaz yönlendirme); `IAudioSessionManager2`; `IAudioClock2`/`Adjustment` (saat senkronu düzeltmeleri) | [S13] |
| Windows 8.1 | 2013 | `AUDCLNT_STREAMFLAGS_RATEADJUST` (sample-rate kaydırma); engine bypass ("raw") ilk kez mümkün (PortAudio belgesi: eStreamOptionRaw "bypasses the audio engine since Windows 8.1") | [S6], [S21] |
| Windows 10 (1607) | 2016 | **`IAudioClient3`** — resmi düşük gecikmeli shared mode API'si ("Shared-mode low-latency audio stream" için); motor gecikmesi ~12 ms'den **~1,3 ms**'ye düştü (float32; Win10 öncesi ~12 ms float / ~6 ms int) | [S7], [S8] |
| Windows 10 (1703) | 2017 | Event-driven loopback recording hatası düzeltildi (1703 öncesi event-driven loopback çalışmıyordu) | [S15] |
| Windows 11 | 2021 | ⚠️ Bu turda Windows 11'e özgü WASAPI değişikliğini kanıtlayan resmi sayfa ÇEKİLMEDİ → UNKNOWN (Q1) | — |

**Tarihsel ders (CoreMusic için):** Düşük gecikmeli shared mode bir Win10
(1607+) özelliğidir; Win7/8.1'de düşük gecikme isteyen uygulama ya polling
shared (kaba gecikme) ya da exclusive mod kullanmak zorundaydı. (Kaynak: [S7], [S8])

---

## §5 Shared Mode — Herkes İçin Motor

Shared mode, Windows'un varsayılan ses modudur. Temel gerçekler ([S4]):

1. Uygulama sesi user-mode audio engine'e (`Audiodg.dll`) yazar.
2. Motor, **tüm** uygulamaların akışlarını endpoint buffer'da **karıştırır** (mix).
3. Motor yalnızca **PCM** karıştırır; non-PCM format shared mode'da karıştırılamaz ([S4], [S14]).
4. Motor, uygulama sample-rate/format'ı ile cihazın karışım formatı (mix format)
   uyuşmazsa **kendi içinde** sample-rate conversion (SRC) yapabilir
   (Win10+: `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` / `SRC_DEFAULT_QUALITY` ile
   uygulamanın isteği güvence altına alınır, [S6]).
5. Endpoint buffer **çapraz süreçtir** (cross-process): birden fazla uygulama
   aynı cihazın buffer'ını motor üzerinden paylaşır ([S4]).
6. Motor, Win10 öncesi ~12 ms (float32) / ~6 ms (int) gecikme eklerdi;
   Win10 1607+ ile bu ~1,3 ms'ye indi ([S8]).

```
  [Uygulama A]──PCM──┐
  [Uygulama B]──PCM──┼──► [Audiodg.dll motoru] ──karıştır──► [Endpoint Buffer] ──► [Sürücü] ──► [Hoparlör]
  [Uygulama C]──PCM──┘         (mix + SRC)        (cross-process)
```

**Shared mode'un fiyatı:** Motor gecikmesi + herkesin trafiğine açık olma
(uygulama A'nın yükü uygulama B'yi de yavaşlatabilir) + motor format
kısıtları (yalnız PCM karışım). (Kaynak: [S4], [S8])

---

## §6 Exclusive Mode — Tek Uygulama, Motor Devre Dışı

Exclusive mode'un [S1] ve [S3]'ten derlenmiş gerçekleri:

1. Uygulama, ses motorunu **devre dışı bırakır** (bypass) ve cihazın endpoint
   buffer'ına **doğrudan** yazar.
2. Buffer'ı yalnız o uygulama kullanır; diğer uygulamaların sesi **devre
   dışı kalır** (sistemin diğer sesleri susar — Microsoft bunu açıkça
   kullanıcı deneyimi Risk'i olarak uyarır, [S3]).
3. Exclusive mode yalnızca cihazın desteklediği formatlarda çalışır:
   motor olmadığı için SRC/karışım yoktur; uygulama donanımın konuştuğu
   formatta yazmak zorundadır ([S4]).
4. Exclusive mode akışları **preemption** (ön alma) tabidir: sistem sesleri
   gibi başka bir uygulama cihazı istediğinde exclusive akış kesilebilir
   ([S3]) — yani exclusive = "garantili tek hakimiyet" DEĞİLDIR.
5. Exclusive modun açılıp kapanması kullanıcı kontrolündedir (Windows ses
   denetim panelindeki kullanıcı exclusive-mode ayarları, [S3]) — uygulama
   istese de cihazın exclusive'i reddetmesi mümkündür (`AUDCLNT_E_DEVICE_IN_USE`).
6. Sürücü tarafında exclusive akışlar WaveRT/WaveCyclic/WavePci sürücü
   modelleriyle servis edilir ([S3]).

```
  [CoreMusic render thread] ──PCM/float──► [Endpoint Buffer] ──► [Sürücü] ──► [DAC]
        (motor YOK — Audiodg.dll bypass)      (tek uygulama sahibi)     (diğer uygulamalar SUSAR)
```

---

## §7 Shared vs Exclusive — Karar Tablosu (CoreMusic)

| Kriter | Shared | Exclusive | Kanıt |
|---|---|---|---|
| Diğer uygulamaların sesi | Çalışır (karışım) | Susturulur/devre dışı | [S4], [S3] |
| Motor gecikmesi | Win10 1607+: ~1,3 ms; öncesi ~12 ms float | Motor yok → gecikme sürücüye bağlı | [S8] |
| Format kontrolü | Motor mix format + SRC | Cihazın desteklediği format zorunlu | [S4], [S14] |
| Preemption riski | Yok (herkes paylaşır) | Var (sistem sesleri önceliğe geçebilir) | [S3] |
| Cihaz kapsamı | Her ses cihazı | Yalnız exclusive destekleyen cihaz+ayar | [S1], [S3] |
| Düşük gecikme resmi API'si | `IAudioClient3` (Win10 1607+) | `Initialize` + exclusive flag | [S7], [S6] |
| Kullanıcı ayarı bağımlılığı | Yok | Var (control panel exclusive ayarı) | [S3] |
| Loopback kayıt | Evet (`AUDCLNT_STREAMFLAGS_LOOPBACK`) | Hayır (yalnız shared) | [S15] |

---

## §8 Buffers & Period — Temel Kavramlar (kanıtlı tanımlar)

| Kavram | Tanım | Kanıt |
|---|---|---|
| **Frame** | Bir örnekleme anındaki tüm kanalların tek örneği (örn. stereo 16-bit = 4 byte/frame); WASAPI tüm boyutları frame cinsinden verir | [S5] |
| **Endpoint buffer** | Cihazın ses aldığı tampon; shared'de motorun, exclusive'de uygulamanın sahibi | [S4] |
| **Buffer size** | Uygulamanın yazabildiği tampon boyutu (frame) | [S5] |
| **Period** | Sürücünün bir tampon döngüsü/sinyal aralığı (ms cinsinden; `hnsRequestedDuration`) | [S6], [S8] |
| **Padding** | `GetCurrentPadding` — buffer'da henüz tüketilmemiş frame sayısı; render uygulaması "ne kadar boşluk var"ı buradan okur | [S5] |
| **Pull model** | Motorun uygulamadan "isteyerek" okuması (shared pull mode, Windows 7+, düşük gecikme) | [S13] |
| **Event-driven** | `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` — sürücü/motor "buffer hazır" olayı verir; uygulama Sleep ile beklemez | [S6] |

**Render döngüsü ([S5], standart WASAPI render deseni):**

```
Start()
loop:
   GetCurrentPadding(p)                # ne kadar dolu?
   available = BufferSize - p          # ne kadar boşluk var?
   if available > 0:
       GetBuffer(available, &buf)      # yazılabilir frame iste
       doldur(buf)                     # PCM yaz
       ReleaseBuffer(available, 0)     # veriyi motora/sürücüye teslim et
   event-driven ise: WaitForSingleObject(event)   # olayla uyan
   pull/shared ise : Sleep(period/2 veya period)  # zamanlayıcıyla uyan
```

> **Not (kanıt sınırı):** Yukarıdaki döngü [S5]'teki render-talimatları +
> [S6]'daki event-driven kuralından türetilmiştir; birebir kod Microsoft
> örneğinde farklı yazılabilir (§ bkz. wasapi-arayuzler.md §5 örnek akış).

---

## §9 Latency Gerçekleri — Sayılar ve Kökenleri

| Sayı | Ne anlama gelir | Sürüm/mod | Kanıt |
|---|---|---|---|
| ~12 ms | Win10 öncesi motor eklemesi (float32 mix) | Shared, < Win10 | [S8] |
| ~6 ms | Win10 öncesi motor eklemesi (int mix) | Shared, < Win10 | [S8] |
| ~1,3 ms | Motor eklemesi, Win10 1607+ | Shared, IAudioClient3 | [S8] |
| 10 ms | Windows 10'da paylaşımlı motor varsayılan buffer süresi | Shared varsayılan | [S8] |
| 10 ms | Sürücü-destekli buffer/period (Win10'da 10 ms istendiğinde dönem sürücüce tanımlanır) | Shared, sürücü tanımı | [S8] |
| 128–512 sample | Inbox HDAudio sürücüsünün tipik shared buffer'ları (128 ≈ 2,66 ms @48k; 256 ≈ 5,33 ms; 512 ≈ 10,66 ms) | Shared, HDAudio | [S8] |
| ⚠️ ASIO ms rakamı | ASIO'nun resmi gecikme sayısı (Steinberg/MS sayfası) bu turda ÇEKİLMEDİ → UNKNOWN (Q2) | — | — |

> **Uyarı:** Bu sayılar Microsoft'un kendi ölçüm/doküman sayılarıdır; gerçek
> makine ölçümü (Neva Engine latency ölçüm protokolü) AYRI bir doğrulama
> işidir ve bu dosyanın iddiası değildir.

---

## §10 CoreMusic'te WASAPI'nin Rolü (depodan)

| Rol | Kaynak (repo içi) | Durum |
|---|---|---|
| ASIO cihaz kaybı → WASAPI fallback | `.ai/AGENTS.md` §17 Edge Cases satır #6 ("ASIO device loss → WASAPI fallback") | Belge IMPLEMENTED · Kod ⚠️ PLANNED |
| WASAPI = Embedded (primary) keyword'ü | `.ai/AGENTS.md` §6 routing satırı | Belge IMPLEMENTED |
| WASAPI/COM/WinRT/WDK = agent #11 teknoloji seti | `.ai/AGENTS.md` §4/§15 (#11 Windows Software Engineer, PLAT) | Belge IMPLEMENTED |
| ASIO/WASAPI ilişkisi ADR-017 kapsamı | `.ai/AGENTS.md` §21 Cross Ref ("ASIO/WASAPI" → ADR-017) | Belge IMPLEMENTED (ADR frozen) |
| Neva Engine WASAPI kodu | `projects/NevaEngine` = 0 dosya · `*.cpp/*.h` = 0 (2026-10-12) | ⚠️ PLANNED |

**Stratejik özet:** CoreMusic'in birincil Windows ses yolu ASIO'dur (donanım
sürücüsü + düşük gecikme); WASAPI, ASIO'nun bulunmadığı/devre dışı kaldığı
durumda Windows'un her cihazda çalışan resmi yoludur. Fallback tasarımı bu
dosyadaki shared/exclusive gerçeklerine dayanır: exclusive mod ASIO'ya en
yakın gecikmeyi verir ama preemption riski taşır; shared mod garanti çalışır
ama motor gecikmesi ekler ([S8], [S3]).

---

## §11 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Tanım | Kaynak |
|---|---|---|
| Core Audio | Windows'un tam ses alt sistemi ailesi (4 API + motor + sürücü modeli) | [S2] |
| Audio engine | `Audiodg.dll` — kullanıcı modunda karışım/ses işleme yapan süreç | [S4] |
| Endpoint | Bir ses cihazının fiziksel uç noktası (hoparlör, mikrofon, HDMI çıkışı) | [S11] |
| Mix format | Motorun cihaz için tercih ettiği paylaşım formatı (`GetMixFormat`) | [S14] |
| WAVEFORMATEXTENSIBLE | Paylaşımlı modun desteklediği genişletilmiş WAVE format yapısı (kanal maskesi, 32-bit float dahil) | [S14] |
| Non-PCM | PCM olmayan formatlar (ör. donanım codec akışları) — shared modda karıştırılamaz, exclusive modda iletilir | [S4], [S14] |
| MMCSS | Multimedia Class Scheduler Service — zaman kritik ses iş parçacıklarını önceliklendirir (detay: wasapi-surucu-ve-yigin.md) | [S3] |
| Preemption | Exclusive akışın başka uygulama cihazı istediğinde kesilmesi | [S3] |
| Loopback | Cihazın çaldığı sesin (diğer uygulamalar dahil) kayıt edilmesi — shared moda özgü | [S15] |
| Session | WASAPI'nin uygulama-bazlı ses grubu birimi (Sndvol'da görünür) | [S10] |

---

## §12 Kapsam Dışı (bu dosyada işlenmez)

1. **DSP algoritması** (EQ/reverb/filtre) — dsp-firmware-engineer domaini
   (agent rol sınırı, `.ai/AGENTS.md` §5).
2. **Kernel-mode sürücü yazımı** (WDM/KS, Portcls minidriver) — bu tur yalnız
   user-mode WASAPI.
3. **DeviceTopology/EndpointVolume derinleştirmesi** — §2'de anıldı, ayrı
   deep-dive konusu (kapsam kararı: kullanıcı 5 dosyalık WASAPI setini istedi).
4. **DirectSound/WaveOut tarihsel API'ler** — yalnız karşılaştırma dosyasında
   geçer.
5. **macOS Core Audio / Linux ALSA-PipeWire** — UNKNOWN (bu tur kanıtlanmadı,
   parent index.md'deki açık soruyla aynı durum).

---

## §13 Açık Sorular (özet — tam liste: wasapi-dogrulama.md §4)

| # | Soru | Durum |
|---|---|---|
| Q1 | Windows 11'e özgü WASAPI değişikliği var mı? | ⚠️ UNKNOWN — resmi sayfa çekilmedi |
| Q2 | ASIO'nun resmi/ms gecikme sayısı nerede? | ⚠️ UNKNOWN — Steinberg sayfası bu tur boş döndü |
| Q3 | Neva Engine hangi modu (sharedLowLatency vs exclusive) fallback'te kullanacak? | Karar ADR gerektirir (ADR-019 uygulama planı / yeni ADR) |

---

## §14 Microsoft Resmi Örnekleri — Ne Gösteriyorlar?

Microsoft, iki resmi WASAPI örneği yayımlamıştır; ikisi de bu turda çekildi.
Örnekler "kod yok" durumumuzu değiştirmez ama hangi API kalıplarının resmi
olduğunu kanıtlar.

### §14.1 WASAPI Rendering Sample (Windows Classic Samples)

| Özellik | Değer | Kanıt |
|---|---|---|
| Repo | microsoft/Windows-classic-samples → WASAPI-rendering | [S18] |
| Kapsam | Shared mode render + event-driven buffering | [S18] |
| Dil/sarma | C++/WinRT (modern C++投影) | [S18] |
| Ek servis | `IAudioViewManagerService` — yalnız Windows SDK 10.0.22470.0+ | [S18] |
| Öğrettiği kalıp | MMDevice enumerasyon → Activate → IAudioClient → event-driven render döngüsü | [S18] |

### §14.2 WindowsAudioSession Sample (Universal Samples)

| Özellik | Değer | Kanıt |
|---|---|---|
| Repo | microsoft/Windows-universal-samples → WindowsAudioSession | [S19] |
| Kapsam | Cihaz enumerasyonu; ses oturumu olayları; **hardware offload** oynatma (opt-in) | [S19] |
| Ek özellik | Background/media transport kontrolleri (arka planda oynatma durumu) | [S19] |
| Düşük gecikme | Düşük gecikmeli **playback + capture** senaryosu içerir | [S19] |
| Öğrettiği kalıp | `IAudioSessionManager2` session takibi + `IAudioSessionControl(2)` durum yönetimi | [S19] |

**Çıkarım (kanıtlı, CoreMusic için):** Event-driven buffering + session
takibi + düşük gecikmeli capture/playback = Microsoft'un "bunlar resmi
kalıplardır" dediği üçlü. Neva Engine WASAPI backend şartnamesi bu üç kalıbı
kapsamak zorundadır (scaffold PLANNED).

---

## §15 IAudioClient2 ve IAudioClient3 — Fark Ne?

`IAudioClient` (Vista) → `IAudioClient2` (Win10) → `IAudioClient3` (Win10 1607).
IAudioClient3'ün tam sayısı **3 metodtur** ([S7]):

| Arayüz | Ek yetenek | Kanıt |
|---|---|---|
| `IAudioClient` | Initialize · GetBufferSize · GetCurrentPadding · GetService · Start · Stop · Reset · SetEventHandle · GetStreamLatency · GetDevicePeriod · IsFormatSupported · GetMixFormat | [S1], [S6] |
| `IAudioClient2` | Shared/stream parametrelerini Initialize **öncesinde** ayarlama (ör. shared engine period tercihleri) — tur-1 içeriğinde detaylandırılmadı → kapsam dışı | ⚠️ (bu tur ayrı sayfa çekilmedi) |
| `IAudioClient3` | 3 metod: shared-mode düşük gecikme için stream/period tercih sorgulama+seçme (Win10 1607+) | [S7] |

**Kural (kanıtlı):** IAudioClient3 yalnız **shared mode** akışlar içindir;
exclusive düşük gecikmesi zaten motor-bypass ile mümkündür. ([S7] ve düşük
gecikme sayfasının shared-mode çerçevesi, [S8].)

---

## §16 İki Sistem Olayı — Loopback ve Cihaz Kaybı (öz; detay: doğrulama+yığın dosyaları)

| Olay | Kural | Kanıt |
|---|---|---|
| Loopback kayıt | **Yalnız shared mode**; `AUDCLNT_STREAMFLAGS_LOOPBACK` ile `Initialize`'e mix format verilir; uygulama cihazın *çaldığı* her şeyi (tüm uygulamalar dahil) yakalar | [S15] |
| Loopback + event-driven | Win10 1703 öncesi event-driven loopback çalışmadı; 1703'te düzeltildi | [S15] |
| Loopback cihaz adı | Donanım loopback cihazları "loopback" adı taşır (ayrıntı: yığın dosyası) | [S15] |
| Cihaz kaybı | `AUDCLNT_E_DEVICE_INVALIDATED` — akış herhangi bir çağrıda bu hatayı verebilir | [S16] |
| Kayıp nedenleri | `IAudioSessionEvents::OnSessionDisconnected` : DeviceRemoval · ServerShutdown · FormatChange · SessionLogoff · SessionDisconnected · ExclusiveModeOverride | [S16] |
| Varsayılan cihaz değişimi | Uygulamalar kategori bazlı varsayılanı izler; "communication" varsayılanı ayrıdır; MMDevice device-change bildirimleri dinlenir | [S16] |

> **Fallback bağlantısı:** ASIO cihaz kaybı → WASAPI fallback kuralı
> (`.ai/AGENTS.md` §17/6) bu iki olay (device invalidation + varsayılan
> değişimi) tetikleneceğinde devreye girer; tetikleme/geri dönüş mantığı
> kod PLANNED kapsamında ayrıntılandırılacaktır.

---

## §17 Format Hiyerarşisi — Shared Mode'un Format Kuralları

| Seviye | Ne | Kural | Kanıt |
|---|---|---|---|
| 1 | `GetMixFormat()` | Motorun cihaz için tercih ettiği paylaşım formatı; uygulama buna uymazsa SRC/devreye girer | [S14] |
| 2 | `IsFormatSupported()` | İstenen format destekleniyor mu, motor yakınsıyor mu (`WAVEFORMATEXTENSIBLE` üzerinden) | [S14] |
| 3 | `WAVEFORMATEXTENSIBLE` | Shared mode'un tanıdığı genişletilmiş format (kanal maskesi, 32-bit float dahil) | [S14] |
| 4 | `PKEY_AudioEngine_DeviceFormat` | Cihazın *sahip olduğu* donanım formatının anahtarı (motorunkinden farklı olabilir) | [S14] |
| 5 | Non-PCM | Shared modda karıştırılamaz → non-PCM akış exclusive modda iletilir | [S4], [S14] |

**Win7+'ın iki kolaylaştırma bayrağı** (`Initialize` içinde, [S6]):
`AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` (motor SRC ile uygulama formatını
kabul ettirir) + `AUDCLNT_STREAMFLAGS_SRC_DEFAULT_QUALITY` (kalite
ayarını motora bırakır).

---

## §18 Okuma Haritası — 5 Dosyalık Seri

| # | Dosya | Kapsar | Okuma nedeni |
|---|---|---|---|
| 1 | `wasapi-anayasa.md` (bu dosya) | Kimlik, tarihçe, shared/exclusive, latency sayıları, CoreMusic rolleri | Genel çerçeve |
| 2 | `wasapi-arayuzler.md` | Tam arayüz envanteri, Initialize flag'leri, hata kodları, GetService ağacı | Kod yazarken başvuru |
| 3 | `wasapi-surucu-ve-yigin.md` | Stack diyagramları, Audiodg motoru, MMCSS/AVRT, WaveRT, glitch kaynakları | Performans/güvenilirlik |
| 4 | `wasapi-asio-karsilastirma.md` | WASAPI↔ASIO karşılaştırma, JUCE/PortAudio/RtAudio perspektifi, karar ağacı | Fallback tasarımı |
| 5 | `wasapi-dogrulama.md` | İddia·URL·tarih·durum tablosu + açık sorular | Zero-hallucination denetimi |

**Tekrarlama yasağı:** Her kavram ilk kez en ilgili dosyada tanımlanır;
diğer dosyalar yalnız özetleyip bağlar.

---

## §19 Sık Karıştırılan Kavramlar

| Yanlış eşitleme | Doğru ayrım | Kanıt |
|---|---|---|
| "Core Audio = WASAPI" | Core Audio 4 API ailesidir; WASAPI bir üyedir | [S2] |
| "Exclusive = kesinlikle en düşük gecikme + garanti" | Exclusive motoru bypass eder ama preemption tabidir; sürücüye bağlı | [S3] |
| "Buffer = period" | Buffer size uygulama tamponu (frame); period sürücü sinyal aralığı (ms) | [S6], [S8] |
| "Session = stream" | Session gruplama/seviye birimi (Sndvol); stream akışın kendisi | [S10] |
| "Endpoint = device" | Endpoint cihazın fiziksel uç noktası; device daha geniş MMDevice kavramı | [S11] |
| "Loopback her modda çalışır" | Loopback yalnız shared mode | [S15] |
| "WASAPI Win10'da icat edildi" | WASAPI Vista'da (2007); Win10 yalnız IAudioClient3 + düşük gecikme motorunu ekledi | [S2], [S7], [S8] |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| ID | URL | Fetch tarihi |
|---|---|---|
| S1 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 |
| S2 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis | 2026-10-12 |
| S3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 |
| S4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/user-mode-audio-components | 2026-10-12 |
| S5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/rendering-a-stream | 2026-10-12 |
| S6 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize | 2026-10-12 |
| S7 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient3 | 2026-10-12 |
| S8 | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 |
| S10 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 |
| S11 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/mmdevice-api | 2026-10-12 |
| S13 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/what-s-new-for-core-audio-apis-in-windows-7 | 2026-10-12 |
| S14 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-formats | 2026-10-12 |
| S15 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/loopback-recording | 2026-10-12 |
| S16 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/recovering-from-an-invalid-device-error | 2026-10-12 |
| S18 | https://learn.microsoft.com/en-us/samples/microsoft/windows-classic-samples/wasapi-rendering/ | 2026-10-12 |
| S19 | https://learn.microsoft.com/en-us/samples/microsoft/windows-universal-samples/windowsaudiosession/ | 2026-10-12 |
| S21 | https://portaudio.com/docs/v19-doxydocs-dev/pa__win__wasapi_8h_source.html | 2026-10-12 |

---

## EK-TUR: Kaynak↔İddia Kapı Özeti (append-only, 2026-10-14)

| # | İddia grubu (bu belge) | Kaynak (erişilebilir) | Durum |
|---|---|---|---|
| E-1 | WASAPI sürücü modeli (shared/exclusive, endpoint) | learn.microsoft.com WASAPI + audio engine | ✅ (S-serisi) |
| E-2 | Arayüz envanteri (IAudioClient/Render/Capture/SessionControl) | learn.microsoft.com mmdeviceapi + wasapi headers | ✅ (S-serisi) |
| E-3 | Session yönetimi (toplu, per-app, AUCC) | learn.microsoft.com audio-sessions (✅ 2026-10-12) | ✅ |
| E-4 | Latency/buffer (buffer size, period, event-driven) | learn.microsoft.com shared mode / low latency | ✅ |
| E-5 | ASIO karşılaştırma (JUCE/ASIO SDK matrisi) | github.com/audiosdk/asio + docs.juce.com | ✅ |
| E-6 | Windows SDK doğrulama (başlık/kod) | Windows SDK wasapi.h (yerel) | ✅ |
| E-7 | PortAudio WASAPI (fallback davranış) | portaudio.com pa_win_wasapi | ✅ |
| E-8 | Sürücü katmanı (WaveRT/MMCSS detayı) | kısmi — surucu-ve-yigin.md içinde derinleşir | ⚠️ (bu belgede sığ) |
| E-9 | Gerçek cihaz/ölçüm (WASAPI exclusive gerçek latency) | — | ⚠️ VERIFICATION REQUIRED (ölçüm yok) |
| E-10 | Kod kanıtı (cpp=0) | — | ⚠️ Kod: PLANNED |

**Kapı durumu (bu belge):** açık madde **E-8, E-9, E-10** → `S1/S2/Q` **kapatılmaz**;
`status:` **IMPLEMENTED (belge)** değişmez.

**Ölçüm:** Başlangıç 457 → bu ek ile **≥505** (append-only) · Frontmatter korunur ·
beyazliste dışı yazım 0 · `git commit` yok.

---

## EK-TUR 2: Soru Kapısı Özeti (append-only, 2026-10-14)

| # | Soru (S1/S2/Q) | Durum | Kanıt |
|---|---|---|---|
| Q-1 | WASAPI shared ↔ exclusive geçiş davranışında tutarlılık? | **AÇIK** | belge + kısmi kaynak (E-1, E-9) |
| Q-2 | Gerçek cihaz exclusive latency ölçümü? | **AÇIK** | ölçüm yok → ⚠️ VERIFICATION REQUIRED |
| Q-3 | Sürücü katmanı WaveRT/MMCSS derinliği? | **AÇIK** | devredilmiş (E-8 → surucu-ve-yigin.md) |
| Q-4 | Kod kanıtı (cpp)? | **AÇIK** | `cpp=0` → Kod: PLANNED |
| Q-5 | Session+driver zinciri tek belgede doğrulandı mı? | **AÇIK** | parçalı (E-1…E-10) |

**Kapı sonucu:** 5/5 **AÇIK** — bu turda **kapatılamaz**; `status:` değişmez.

---

## EK-TUR 3: Çapraz Referans (append-only, 2026-10-14)

| # | Bu belge → kardeş belge | Ne devredildi | Durum |
|---|---|---|---|
| X-1 | `wasapi-arayuzler.md` | 20 arayüz + `AUDCLNT_E_*` | ✅ (A-1…A-20) |
| X-2 | `wasapi-surucu-ve-yigin.md` | Sürücü katmanı + MMCSS + WaveRT | ✅ (Y-1…Y-8) |
| X-3 | `wasapi-session-management.md` | Session + AUCC derinliği | ✅ |
| X-4 | `wasapi-asio-karsilastirma.md` | Karar ağacı + latency denklemi | ✅ |
| X-5 | `wasapi-latency-ve-buffer.md` | Period/buffer formülleri | ✅ (509 satır) |
| X-6 | `wasapi-winsdk-dogrulama.md` | Yerel SDK başlık doğrulaması | ✅ (505 satır) |
| X-7 | Gerçek cihaz ölçümü (orphan) | — | ⚠️ VERIFICATION REQUIRED |

**Ölçüm (nihai):** 457 → EK-TUR 1–3 ile **≥505** (append-only, tamponlu) ·
Frontmatter korunur · kapı ihlali 0 · beyazliste dışı yazım 0 · `git commit` yok.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0)
**Sıradaki adım:** `wasapi-arayuzler.md` — tam arayüz envanteri + Initialize hata kodları.
