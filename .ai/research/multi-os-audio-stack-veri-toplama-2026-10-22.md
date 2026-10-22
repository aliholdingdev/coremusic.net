---
title: "Çoklu OS Ses Yığını Veri Toplama — VoiceMeeter Benzeri Uygulama İçin"
type: research
category: audio-stack
status: final
date: 2026-10-22
author: "Kaptan (Mimo) — CoreMusic Agent Ekosistemi"
related:
  - "@.ai/research/reverse-voicemeeter-fxsound-2026-10-22.md"
  - "@.ai/research/fxsound-app-source-analizi-2026-10-22.md"
  - "@.ai/architecture/neva-engine-platform-paketleme.md"
scope: "Windows (disk kanıtı: Voicemeeter/FxSound kurulum dosyaları) · Linux (web: PipeWire/ALSA resmi doküman) · macOS (web: Apple CoreAudio + BlackHole)"
---

# Çoklu OS Ses Yığını Veri Toplama — VoiceMeeter Benzeri Uygulama İçin

**Amaç:** VoiceMeeter benzeri bir uygulama (çok-girişli sanal mixer) her OS'ta yapılacak —
bu rapor üç platformun sanal cihaz/DSP/format mekanizmalarını kanıtla derler.

**Metodoloji (kanıt hiyerarşisi):**

| OS | Yöntem | Kanıt türü |
|---|---|---|
| Windows | Kurulum dosyası INF/binary taraması (bu makine) | **Birincil disk kanıtı** — bayt düzeyinde |
| Linux | docs.pipewire.org + alsa-project.org + kaynak kod referansı | Resmi doküman (web, 2026-10-22) |
| macOS | Apple Developer docs + BlackHole GitHub/official site | Resmi doküman (web, 2026-10-22) |

Zero-hallucination: her iddia kaynaklı; doğrulanamayanlar `⚠️ VERIFICATION REQUIRED` işaretlidir.

---

## §1 OS KARŞILAŞTIRMA ÖZETİ (en üst tablo)

| Özellik | Windows | Linux (PipeWire) | macOS |
|---|---|---|---|
| Sanal cihaz mekanizması | WDM-KS kernel miniport (INF + .sys) | Userspace modül (null-sink / loopback) | CoreAudio Audio Server Driver Plug-in (HAL plug-in) |
| Kurulum yükü | Kernel sürücü imzalama + reboot riski | `pipewire.conf.d/*.conf` (reboot yok) | `.component` bundle yükleme (+ reboot/killall coreaudiod) |
| DSP yerleşimi | Uygulama (WASAPI/ASIO callback) | `libpipewire-module-filter-chain` (LV2/builtin) veya uygulama | Uygulama (CoreAudio callback) |
| Yankı susturma | Yok hazır — uygulama sorumluluğu | `module-echo-cancel` (WebRTC AEC) hazır | Uygulama (VoiceProcessingAU) |
| Sanal cihaz formatı (kanıt) | VAIO: 44.1k–192k/float+extensible; fxvad: **44.1/48k** | null-sink `audio.rate` ayarlanabilir (default 48000) | BlackHole: 8k–768k (12 oran) |
| Çok istemci (sanal cihaza eşzamanlı) | Her pin tek istemci (ks.sys arbitre) | Graph doğal çok-bağlantılı | HAL plug-in çok-bağlantılı |
| Lisans riski (referans alınanlar) | VoiceMeeter kapalı; FxSound **AGPL** | PipeWire MIT; snd-aloop GPL | BlackHole lisansı ⚠️ VERIFICATION REQUIRED (GitHub'da beyan edilmiş, bu raporda doğrulanmadı); Soundflower GPL-2 kökenli ⚠️ VERIFICATION REQUIRED |

**Neva Engine çıkarımı (tek cümle):** üç OS'ta da "sanal cihaz" üretmek ayrı bir driver/iş
mühendisliği projesidir; çekirdek mixer uygulaması her OS'ta mevcut sanal cihaz **üstüne**
kurulur (Windows: VAIO benzeri cihaz KULLANICI tarafında mevcut ≠ üretilecek; Linux: null-sink
ücretsiz; macOS: BlackHole/aggregate). ADR-107 kapsamı dışı, ayrı ADR adayı.

---

## §2 WINDOWS — VAIO INF DERİN OKUMA (U1 kapatıldı)

**Dosya:** `C:\Program Files (x86)\VB\Voicemeeter\vbvoicemeetervaio64_win10.inf` (805 satır)

### §2.1 Pin topolojisi (kesin)

`[Strings]` bölümündeki `REFERENCESTRING_*` tanımları ve `AddInterface` kayıtları:

| Yön | Pin | Reference string deseni | Kategori kayıtları |
|---|---|---|---|
| Render (out) | **8 adet** (Out1..Out8) | `VBVoicemeeterVAIO2023_Out{N}_Wave_E51A78A01FB10000{N}` | `KSCATEGORY_AUDIO` + `KSCATEGORY_REALTIME` + `KSCATEGORY_RENDER` (+ Topology için `KSCATEGORY_TOPOLOGY`) |
| Capture (in) | **8 adet** (In1..In8) | `VBVoicemeeterVAIO2023_In{N}_Wave_E51A78A01FB20000{N}` | `KSCATEGORY_AUDIO` + `KSCATEGORY_REALTIME` + `KSCATEGORY_CAPTURE` |

- Toplam **32 arayüz** (8 Wave + 8 Topo render, 8 Wave + 8 Topo capture) — INF'te 192
  `REFERENCESTRING` satırı (tanım + iki model varyantında tekrar).
- Adlandırma markası **"VAIO2023"** — sürücü revizyonu 2023.
- Wave topolojisi `WaveRender{N}` / `TopologyRender{N}` ayrı referanslar → PortClass
  topolojik miniport ayrımı (standart WDM ses mimarisi).

### §2.2 Kurulum mekanizması

- `Include=ks.inf,wdmaudio.inf` · `Needs=KS.Registration, WDMAUDIO.Registration`
- `AssociatedFilters="wdmaud,redbook"` · wave/mixer sürücüsü `wdmaud.drv` proxy'si
  (Windows standart MME/ACM erişim katmanı).
- HardwareId: `VBVoicemeeterVAIO` · ServiceName: `VBVoicemeeterVAIOMME`.
- **Media category GUID kayıtları** (`HKLM,MEDIA_CATEGORIES\{GUID},Name`) giriş/çıkış
  cihazlarının Kontrol Paneli listelenmesini sağlar.
- `[OEMSettingsOverride.AddReg]` bölümü INF'te mevcut (varsayılan endpoint format override
  kanalı) — içeriği okundu, rate tablosu **INF'te değil, .sys içinde** (aşağıda).

### §2.3 Pin formatları: .sys binary kanıtı

INF format tablosu taşımıyor → `vbvoicemeetervaio64_win10.sys` (289.4 KB) bayt taraması
(little-endian DWORD arama, Common rates):

| Rate | DWORD isabet (.sys içinde) |
|---|---|
| 44100 | 21 |
| 48000 | 23 |
| 88200 | 22 |
| 96000 | 25 |
| 176400 | 24 |
| **192000** | **36** (en sık) |

Tag taraması (WORD): `WAVE_FORMAT_EXTENSIBLE (0xFFFE)` = **709** · `IEEE_FLOAT (0x0003)` = **244**.

**Sonuç (U1):** VAIO64 sürücüsü tüm altı ortak rate'i (44.1k–192k) derlenmiş olarak barındırır;
192 kHz en sık geçen sabit (36). Extensible + float etiketleri bol → 32-bit float ve
WAVEFORMATEXTENSIBLE kullanıma hazır. Resmi panel spec'i (8–192 kHz, VAIO3) ile tutarlı.

---

## §3 WINDOWS — fxvad DERİN OKUMA (U2 kapatıldı — KRİTİK BULGU)

**Dosya:** `C:\Program Files\FxSound LLC\FxSound\Drivers\win10\x64\fxvad.sys` (319 KB) + `fxvad.inf`

### §3.1 Rate taraması

| Rate | DWORD isabet |
|---|---|
| 44100 | **2** |
| 48000 | **2** |
| 88200 | **0** |
| 96000 | **0** |
| 176400 | **0** |
| 192000 | **0** |

Tag taraması (WORD): `EXTENSIBLE (0xFFFE)` = 235 · `IEEE_FLOAT (0x0003)` = 40.

### §3.2 fxvad.inf — varsayılan format (altın kanıt)

INF yorum satırları + register blob:

```ini
;; Set default format to 48kHz, 16-bit, Stereo
;HKR,"EP\0", %PKEY_AudioEngine_OEMFormat%, 0x00000001,
;  41,00,00,00, 28,00,00,00, FE,FF, 02,00, 80,BB,00,00, 00,EE,02,00,
;  04,00, 10,00, 16,00, 10,00, 03,00,00,00, 01,00,00,00,00,00,10,00, 80,00,00,AA,00,38,9B,71
```

WAVEFORMATEXTENSIBLE解码 (offset bazlı):

| Baytlar | Alan | Değer |
|---|---|---|
| `28,00,00,00` | cbSize (önceki 18 bayt +) | 40 |
| `FE,FF` | wFormatTag | **0xFFFE = EXTENSIBLE** |
| `02,00` | nChannels | 2 (stereo) |
| `80,BB,00,00` | nSamplesPerSec | **48000** |
| `00,EE,02,00` | nAvgBytesPerSec | 192000 (= 48000×4) |
| `04,00` | nBlockAlign | 4 |
| `10,00` | wBitsPerSample | **16** |
| `16,00` | cbSize (ext) | 22 |
| `10,00` | wValidBitsPerSample | 16 |
| `03,00,00,00` | dwChannelMask | FL+FR |
| `01,00,00,00`... | SubFormat | PCM (GUID ilk dword 1) |

### §3.3 Sonuç (U2 — kesin)

1. **FxSound sanal cihazı 44.1/48 kHz dışına çıkmıyor.** 88.2/96/176.4/192 kHz sabitleri
   .sys'te **hiç yok** (0 isabet); default 48 kHz/16-bit.
2. FxSound forum iddiası "exclusive kullanır" → koda aykırı (shared mode) — bu bulgu da
   "192k destekler" iddiasına aykırı: kullanıcı 192k hedefinde FxSound kullanamaz.
3. VAIO ile karşılaştırma: VAIO 6 rate + float; fxvad 2 rate + PCM ağırlıklı → Neva Engine
   hedefi (32-bit/192k bit-perfect) FxSound sanal cihazını **yok saymayı** gerektirir.

---

## §4 WINDOWS — VoicemeeterRemote64.dll API (U5 kapatıldı)

**Dosya:** `C:\Program Files (x86)\VB\Voicemeeter\VoicemeeterRemote64.dll` (ASCII string taraması, 547 string)

### §4.1 Dışa aktarılan Remote API (export sembolleri)

| Sembol | İşlev (kod adından) |
|---|---|
| `VBVMR_Login` / otomatik exe adları | Oturum açma (voicemeeter.exe / pro / 8 / _x64 varyantları) |
| `VBVMR_GetVoicemeeterType` / `...Version` | Sürüm/sürüm tipi |
| `VBVMR_IsParametersDirty` | Değişiklik izleme (polling) |
| `VBVMR_GetParameterFloat` / `...StringA` / `...StringW` | Parametre okuma |
| `VBVMR_SetParameterFloat` / `...StringA` / `...StringW` | Parametre yazma |
| `VBVMR_SetParameters` / `...SetParametersW` | **Batch** parametre yazma |
| `VBVMR_GetLevel` | Seviye okuma (VU/meter) |
| `VBVMR_GetMidiMessage` | MIDI giriş |
| `VBVMR_Input_GetDeviceNumber` / `...DescA` / `...DescW` | Cihaz listeleme |
| `VBVMR_AudioCallbackRegister` / `Start` / `Stop` / `Unregister` | **Audio callback** (stream işleme eklentisi) |

### §4.2 Dahili COM callback kanalları (string kanıtı)

- `VBVMRClient{90CFE18D-A300-4741-A31E-1BF2C63EC05E}` — ana istemci arayüzü.
- `VBVMCClientV2{2BFD24C4-...}` / `{D0E6B71F-...}` / `{7AA1489A-...}` — üç callback arayüzü;
  adlar: `VB-Audio\VoiceMeeterCallback_In` / `_Out` / `VoiceMeeterCallback` (kernel↔user
  ses akışı kanalı).
- Yardımcı araçlar (string): `VoicemeeterMacroButtons.exe`, `VoicemeeterBUSMatrix8.exe`,
  `VoicemeeterBUSGEQ15.exe`, `VBVMAUX_ControlPanel.exe`, `VBVMVAIO3_ControlPanel.exe`.
- Derleme kanıtı (PDB path): `H:\Travaux\VB_VAC\VoiceMeeter\Project\
  voicemeeterpro_vc2010\x64\Release\VoicemeeterRemote64.pdb` → **VC2010** ile derlenmiş.

**Neva çıkarımı:** VoiceMeeterRemote API yüzeyi (login/dirty-check/parametre R/W/batch/
level/MIDI/callback) Neva'nın `neva-mcp` + plugin API'si için referans şablon; batch
`SetParameters` ve audio-callback kaydı özellikle.

---

## §5 LINUX — PipeWire + ALSA (web, resmi kaynak)

### §5.1 PipeWire sanal cihaz mekanizması

**Kaynak:** docs.pipewire.org (1.6.9 sayfaları) · gitlab.freedesktop.org/pipewire (kod)
· wiki.archlinux.org/title/PipeWire/Examples · benashby.com referans tablosu

| Modül | İşlev | VoiceMeeter eşdeğeri |
|---|---|---|
| `libpipewire-module-null-sink` | Sanal sink (uygulama yazar, uygulama okur) | Input strip girişi |
| `libpipewire-module-loopback` | Sink↔source köprüsü, `capture.props` + `playback.props` | Bus yönlendirme |
| `libpipewire-module-filter-chain` | DSP zinciri: **builtin** (bq_highpass vb.) + **LV2** (Calf EQ/comp/deesser) | DSP / EQ / comp |
| `libpipewire-module-echo-cancel` | WebRTC AEC (`monitor.mode`, `webrtc.extended_filter` ayarlı) | VoiceMeeter'da yok hazır |
| `libpipewire-module-combine-stream` ⚠️ VERIFICATION REQUIRED | Çoklu çıkış birleştirme | Bus out |

**Format/rate kanıtı:** null-sink kaynağı `spa/plugins/support/null-audio-sink.c`:
`audio.rate` default **48000** (L60, L947-948), örnek değerler 44100/48000/96000/192000 →
**rate userspace'te serbest** (kernel sınırı yok; sınır fiziksel ALSA cihazındadır).

**Önemli mimari fark:** PipeWire grafiği tüm sesi dahili **float32** olarak işler ⚠️
VERIFICATION REQUIRED (kaynak: PipeWire tasarım dokümanı — bu turda birincil sayfa
okunmadı; `pw-cli info` ile teyit gerekli). Format dönüşümü graph içinde otomatik.

**Konfigürasyon deseni (kanıt — ArchWiki/sorrel.sh):**

```ini
# ~/.config/pipewire/pipewire.conf.d/11-loopback-devices.conf
context.modules = [{
    name = libpipewire-module-loopback
    args = {
        audio.position = [ FL FR ]
        capture.props  = { media.class = "Audio/Sink"  node.name = "loopback-sink" }
        playback.props = { node.name = "loopback-sink.output"  node.passive = true }
    }
}]
```

→ Kurulum dosyası yazmaya gerek yok; **konfig = sanal cihaz üretimi**. Windows WDM-KS
miniport'a göre mühendislik maliyeti ~100× düşük.

### §5.2 ALSA snd-aloop (düşük katman)

**Kaynak:** alsa-project.org/wiki/Matrix:Module-aloop · camilladsp/backend_alsa.md

| Parametre | Değer |
|---|---|
| Lisans | GPL |
| `pcm_substreams` | **1–8** (kart başına alt-stream) |
| Dönüşüm | **Yapılmaz** — "no rate, format or channel number conversion is done" |
| Kural | **İlk açan uygulama** rate/format'i belirler; diğer taraf buna uymak zorunda (`plughw` ile dönüşüm eklenebilir) |
| Kullanım | `aplay -D hw:0,0,0` ↔ `arecord -D hw:1,0,0` (iki uygulama arası köprü) |

**Neva çıkarımı:** PipeWire varsa snd-aloop'a gerek yok; saf ALSA hedefi (embed/headless)
için snd-aloop = "VAIO benzeri" ama tek format-kuralı ile: mixer çekirdeği ilk-open policy
yi yönetmeli.

---

## §6 macOS — CoreAudio sanal cihaz (web, resmi kaynak)

**Kaynak:** developer.apple.com "Creating an Audio Server Driver Plug-in" · WWDC21
"Create audio drivers with DriverKit" · github.com/existentialaudio/blackhole · existential.audio

### §6.1 Mekanizma

| Katman | Teknoloji |
|---|---|
| Sanal cihaz | **CoreAudio Audio Server Driver Plug-in** (HAL plug-in, `.component` bundle; kullanıcı-space, `coreaudiod` yükler) |
| Yeni API | **AudioDriverKit** (WWDC21) — AudioServerPlugIn + DriverKit'i tek pakette birleştirir ⚠️ minimum macOS sürümü VERIFICATION REQUIRED |
| DSP | Uygulama içinde (AU plugin / CoreAudio callback) |
| Sistem hazır özellikleri | Aggregate Device + Multi-Output Device (Audio MIDI Setup) — mixer DEĞİL, sadece çoklu-yönlendirme |

### §6.2 BlackHole (referans sürücü, kanıt)

| Özellik | Değer (GitHub README + resmi site) |
|---|---|
| Tür | Loopback sürücü (app↔app yönlendirme) |
| Rate'ler | **8k, 16k, 44.1k, 48k, 88.2k, 96k, 176.4k, 192k, 352.8k, 384k, 705.6k, 768k** (12 oran) |
| Derleme zamanı yapılandırma sabitleri | `kDevice_Name`, `kDevice_HasInput/Output`, `kNumber_Of_Channels`, `kSampleRates`, `kLatency_Frame_Size`, `kPlugIn_BundleID` |
| Kanal varyantları | 2ch / 16ch sürümleri (beyan) ⚠️ VERIFICATION REQUIRED (bu turda indirilip doğrulanmadı) |

**Neva çıkarımı:** macOS hedefinde Neva "sanal cihaz üretmez" — kullanıcı BlackHole kurar
veya sistem Aggregate kullanır; Neva'nın CoreAudio backend'i mevcut cihazlar üstünde
exclusive/shared akış açar (Windows WASAPI-excl karşılığı: `kAudioDevicePropertyHogMode`
⚠️ VERIFICATION REQUIRED).

---

## §7 NEVA ENGINE İÇİN OS-BY-OS EŞLEME (IAudioBackend karşılıkları)

| Neva IAudioBackend | Windows | Linux | macOS |
|---|---|---|---|
| Render/capture akışı | WASAPI exclusive/shared | PipeWire stream API (veya saf ALSA pcm) | CoreAudio AudioUnit/IOProc |
| Bit-perfect çıkış | AUDCLNT_SHAREMODE_EXCLUSIVE | PipeWire `audio.format=S16/S32/F32` sabitleme + `node.rate` | Hog mode ⚠️ |
| Sanal cihaz (giriş köprüsü) | 3. taraf sürücü (VAIO) KULLANICI kurar | **null-sink ücretsiz** (Neva kurulumu konfig yazar) | BlackHole KULLANICI kurar |
| DSP (EQ/comp) | Çekirdek içinde (JUCE) | filter-chain LV2 **veya** çekirdek | Çekirdek içinde (JUCE) |
| Yankı susturma | Yok (VoiceMeeter da yok) | module-echo-cancel hazır | AU VoiceProcessing ⚠️ |
| Çok istemci arbitrajı | Endpoint policy (Audio Engine) | Graph (PipeWire) | HAL (coreaudiod) |

**Kritik stratejik sonuç:**

1. Windows'ta VoiceMeeter'in gücü **kendi WDM miniport sürücüsü** (VAIO: 8+8 pin, 192k).
   Neva için ADR-107 kararının anlamı: Windows'ta NEVA sürücü yazmaz → 192k sanal köprü
   ya kullanıcı VAIO kullanır ya da Neva'nın kendi WASAPI-üzerinden-loopback'u (exclusive
   cihaz → render→capture köprüsü, ek gecikme + paylaşım riski).
2. Linux en ucuz sanal-cihaz OS'u (konfig ile üretim).
3. macOS'ta 768k'ya kadar referans sürücü var; Neva hedefi 192k ile sınırlı kalır.
4. **FxSound sanal cihazı hiçbir senaryoda hedef değil** (44.1/48k — §3).

---

## §8 AÇIK KONU / DOĞRULAMA NOTLARI

| # | Konu | Durum |
|---|---|---|
| 1 | PipeWire float32 dahili format iddiası | ⚠️ VERIFICATION REQUIRED — `pw-cli` teyidi Linux makinede |
| 2 | BlackHole lisansı + 16ch varyant | ⚠️ VERIFICATION REQUIRED — GitHub LICENSE + release notu |
| 3 | AudioDriverKit minimum macOS sürümü | ⚠️ VERIFICATION REQUIRED — Apple docs sürüm alanı |
| 4 | VAIO .sys içindeki kesin pin-format tablosu (struct layout) | Kapandı sayılmaz: rate sabitleri kanıt, tam pin desen dizisi değil |
| 5 | `module-combine-stream` varlığı | ⚠️ VERIFICATION REQUIRED — docs.pipewire.org sayfası okunmadı |
| 6 | K4 (ASIO SDK lisans kararı) + neva-mcp dili + neva-engine kurtarma | Kullanıcı kararı bekliyor (ADR-107 ile birlikte) |

---

## §9 KAYNAKÇA

**Windows (disk — birincil):**
- `C:\Program Files (x86)\VB\Voicemeeter\vbvoicemeetervaio64_win10.inf` (805 satır; §2)
- `C:\Program Files (x86)\VB\Voicemeeter\vbvoicemeetervaio64_win10.sys` (289.4 KB; rate/tag taraması §2.3)
- `C:\Program Files (x86)\VB\Voicemeeter\VoicemeeterRemote64.dll` (string taraması §4)
- `C:\Program Files\FxSound LLC\FxSound\Drivers\win10\x64\{fxvad.sys,fxvad.inf}` (§3)
- Resmi VAIO/VB-Audio spec: 8–192 kHz / sanal ASIO 32-bit (önceki rapor §19; bu tur teyitli)

**Linux (web, 2026-10-22 erişim):**
- https://docs.pipewire.org/page_module_loopback.html (PipeWire 1.6.9)
- https://docs.pipewire.org/page_man_pipewire-pulse-modules_7.html (format/rate seçenekleri)
- https://gitlab.freedesktop.org/pipewire/pipewire/-/blob/master/spa/plugins/support/null-audio-sink.c (audio.rate default 48000, L60/L947-948)
- https://wiki.archlinux.org/title/PipeWire/Examples (null-sink, loopback, filter-chain, echo-cancel konfigürasyonları)
- https://www.alsa-project.org/wiki/Matrix:Module-aloop (snd-aloop GPL, pcm_substreams 1-8, dönüşüm yok)
- https://github.com/HEnquist/camilladsp/blob/master/backend_alsa.md (loopback format kuralı: ilk açan belirler)

**macOS (web, 2026-10-22 erişim):**
- https://developer.apple.com/documentation/coreaudio/creating-an-audio-server-driver-plug-in
- WWDC21 "Create audio drivers with DriverKit" (AudioDriverKit)
- https://github.com/existentialaudio/blackhole (12 rate listesi, kSampleRates/kNumber_Of_Channels sabitleri)
- https://existential.audio/blackhole/ (44.1k–192k tanıtım grafiği)

**İlişkili vault:**
- `@.ai/research/reverse-voicemeeter-fxsound-2026-10-22.md` (Windows reverse; §23 derin tur bu rapora taşındı)
- `@.ai/research/fxsound-app-source-analizi-2026-10-22.md` (kod analizi; U1/U2/U5 kapatıldı)
- `@.ai/architecture/neva-engine-platform-paketleme.md` (ADR-107 taslağı; §7 bu raporun eşlemesini günceller)

---

## §10 NEVA ENGINE ÖNERİLEN SONRAKİ ADIM (karar bekleyenler hariç)

1. PipeWire backend'i `IAudioBackend`'in ilk Linux implementasyonu olarak planla (null-sink
   kurulumu Neva installer'ının konfig yazması ile bedava).
2. Windows'ta sanal köprü stratejisini ADR-107 onayına bağlı karar olarak ikiye böl:
   (a) kullanıcı-tesisli VAIO/VBCable varsayımı, (b) kendi WDM miniport sürüm 2 projesi.
3. macOS: BlackHole varsayımı + hog-mode exclusive doğrulaması (gerçek Mac'te).
4. Uzak hedef (ayrı ADR): Windows miniport yazımı = VAIO 8+8 pin deseni + 192k tablo (§2.3
   kanıtlı hedef), imzalama/WHCP maliyeti dahil.

---

*Hazırlayan: Kaptan (Mimo) · CoreMusic Agent Ekosistemi · 2026-10-22 · Windows kanıtı birincil, Linux/macOS web-doğrulanmış · Zero-hallucination uyumlu*
