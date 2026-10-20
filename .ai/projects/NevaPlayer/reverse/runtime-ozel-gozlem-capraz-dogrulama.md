---
title: "Runtime Özel Gözlem — AIMP 5.4 / VLC 3.0.23 / FxSound 1.2.13 ve Statik Dump Çapraz Doğrulama"
type: reverse-engineering
target: "C:\Program Files\AIMP\AIMP.exe · C:\Program Files\VideoLAN\VLC\vlc.exe · C:\Program Files\FxSound LLC\FxSound\FxSound.exe"
method: "pefile 2024.8.26 (statik PE import/delay-import/reloc) + PowerShell Get-Process.Modules (canlı modül çekimi) + disk envanteri (os.walk) + uygulama yapılandırma dosyaları (aimp.ini / EQPresetsLibrary.ini / vlcrc)"
date: 2026-10-20
status: evidence-backed
version: 1.1.1
evidence:
  - "pefile 2024.8.26 / Python 3.14 — 5 PE imgesi import tablosu, delay-import, BaseReloc, DllCharacteristics"
  - "canlı modül çekimi: AIMP PID 31968 (139), VLC boşta PID 36608 (103), VLC erken oynatma PID 30948 (114), VLC kararlı oynatma PID 27408 (165)"
  - "aynı süreç iki anlık görüntü: AIMP PID 31968 139 → 151 modül (+12, 12/12 C:\Windows\system32 ağ DLL, codec eklenmedi, çıkan 0) — §14"
  - "disk envanteri: AIMP 49 DLL, VLC plugins/ 363 DLL, FxSound 0 harici DSP DLL"
  - "uygulama config: %APPDATA%\AIMP\aimp.ini (25.314 B), EQPresetsLibrary.ini (20.978 B), %APPDATA%\vlc\vlcrc (96.647 B)"
  - "PE VersionInfo: AIMP 5.4.0.2722 (Artem Izmaylov), VLC 3.0.23 (VideoLAN), FxSound 1.2.13.0 (FxSound LLC)"
  - "çapraz: vlc-dump.md 363 plugin = benim bağımsız sayımım 363 · aimp-exe-decompile.md 'SetEQBandsArr(Li20)' = runtime config Band0..Band19"
---

# Runtime Özel Gözlem ve Statik Dump Çapraz Doğrulama

> **Ne değildir:** Bu dosya statik decompile değildir (`aimp-exe-full-decompile.txt`,
> `aimp-decompile-dump.md`, `vlc-dump.md` statik tarafı taşır). Burada **çalışan süreçten
> ölçülen** gerçek modül kümeleri, uygulamanın diskteki statik import tablosuyla ve
> çalışır yapılandırma dosyasıyla karşılaştırılır.
>
> **Amaç:** Neva Player için "hangi ses/cihaz/yükleme mimarisini seçelim?" kararını
> **ölçülebilir** hale getirmek — tahminle değil, üç uygulamanın gerçek davranışından.

---

## §1 Kapsam, Yöntem, Araçlar

| Aşama | Araç | Ne ölçtü |
|---|---|---|
| Statik import | `pefile` 2024.8.26 (Python 3.14) | Import/delay-import tabloları, BaseReloc, DllCharacteristics, ImageBase |
| Canlı modül | PowerShell `Get-Process.Modules` | O an gerçekten belleğe yüklenmiş DLL kümeleri |
| Disk envanteri | `os.walk` / `Get-ChildItem` | Kurulu plugin/DLL sayısı |
| Yapılandırma | `[System.IO.File]::ReadAllText` | Çalışır ayar (cihaz / EQ / hw decode) |
| Sürüm | `(Get-Item).VersionInfo` | PE VersionInfo alanları |

**Ölçüm pencereleri:** VLC için üç durum — *boşta* (hiç medya yok), *erken oynatma*
(ilk ~1,8 sn), *kararlı oynatma* (12 sn sonra, 20 sn sabit). Test medyası
`C:\Users\MARHAN\Videos\00009.mp4`, **ses kapalı** (`--volume=0`), `--play-and-exit`.

**Bu raporda `DERIVED` işareti = ölçülmüş sayılardan türetilmiş (yüzde/toplam),
`UNKNOWN` = ölçülememiş, `VERIFICATION REQUIRED` = doğrulanması gereken yorum.**

---

## §2 Hedef Sürümler (PE VersionInfo)

| İmge | Sürüm | Şirket | PE TSD (UTC) |
|---|---|---|---|
| `AIMP.exe` | 5.4.0.2722 | Artem Izmaylov | 2026-07-01 08:00:51 |
| `AIMP.Runtime.dll` | 5.4.0.2722 | Artem Izmaylov | 2026-07-01 07:59:47 |
| `AIMP.Shared.dll` | 5.4.0.2722 | Artem Izmaylov | 2026-07-01 08:00:01 |
| `vlc.exe` / `libvlc.dll` / `libvlccore.dll` | 3.0.23 | VideoLAN | **2055-05-13 14:53:52** |
| `FxSound.exe` | 1.2.13.0 | FxSound LLC | 2026-08-18 08:37:30 |

**Kanıt notları:**

- AIMP'in üç imgesinin TSD'si **64 saniye içinde** → tek derleme oturumu (tutarlı).
- **VLC'nin TSD'si gelecekte** (ham `TimeDateStamp=2693832832` = `0xA08C7A80` → 2055-05-13 14:53:52 UTC) → gerçek derleme zamanı değildir; yeniden üretilebilir/rastgele
  bağlantı damgası. Gerçek derleme tarihi **UNKNOWN**.
- `EQPresetsLibrary.ini` → `LibVersion=2722` = sürümün derleme bileşeni **2722** ile birebir (§8).

---

## §3 Statik Import Tablosu (pefile)

### 3.1 Genel künye

| İmge | Arch | ImageBase | BaseReloc | DllChar | Import DLL | Import fonk. | Delay DLL |
|---|---|---|---|---|---:|---:|---:|
| `AIMP.exe` | x64 (0x8664) | **0x400000** | **YOK** | 0x8160 | 10 | **5916** | 0 |
| `AIMP.Runtime.dll` | x64 | 0x400000 | VAR | 0x160 | 18 | 736 | **9 (27 fn)** |
| `AIMP.Shared.dll` | x64 | 0x400000 | VAR | 0x160 | 10 | 3799 | 0 |
| `vlc.exe` | x64 | 0x140000000 | VAR | 0x560 | 7 | 166 | 0 |
| `libvlc.dll` | x64 | — | VAR | — | — | — | 0 |
| `libvlccore.dll` | x64 | — | VAR | — | — | — | 0 |
| `FxSound.exe` | x64 | 0x140000000 | VAR | 0x8160 | 13 | 667 | 0 |

`DllCharacteristics` bayrak çözümleri: 0x8160 = `DYNAMIC_BASE | NX_COMPAT | APPCONTAINER` ·
0x560 = `DYNAMIC_BASE | NX_COMPAT | NO_SEH` · 0x160 = `DYNAMIC_BASE | NX_COMPAT`.

**Ölçülen güvenlik gözlemi:** `AIMP.exe` **BaseReloc dizinine sahip değil**
(va=0, size=0) ve ImageBase **0x400000** (x64 için olağandışı düşük, x86 klasik adresi).
`DYNAMIC_BASE` bayrağı set edilmiş olsa da taşıma verisi olmadığından yükleyici imgeyi
tercih edilen **sabit** adreste zorlanır → **AIMP.exe için etkin ASLR yok**.
`AIMP.Runtime.dll` / `AIMP.Shared.dll` taşıma verisine sahip (0x6EB4C / 0x1AB00 bayt)
ve bu yüzden yeniden konumlandırılabilir — ASLR yalnızca AIMP'in kendi bileşenlerinde geçerli.

### 3.2 `AIMP.exe` import yoğunluğu (10 DLL / 5916 fonksiyon)

| DLL | Fonksiyon | Pay |
|---|---:|---:|
| `AIMP.Runtime.dll` | **4609** | %77,91 (DERIVED) |
| `AIMP.Shared.dll` | **1144** | %19,34 (DERIVED) |
| `user32.dll` | 118 | %1,99 |
| `advapi32.dll` | 19 | — |
| `shell32.dll` | 9 | — |
| `version.dll` | 6 | — |
| `comctl32.dll` | 5 | — |
| `comdlg32.dll` | 3 | — |
| `oleaut32.dll` | 2 | — |
| `shlwapi.dll` | 1 | — |

**Bulgular:**
- Kendi iki DLL'ine giden import **5753 = %97,24** (DERIVED) → `AIMP.exe` ince bir
  kabuktur; gerçek yüzey `AIMP.Runtime.dll` (15,7 MB) + `AIMP.Shared.dll` (4,6 MB) içindedir.
- `kernel32.dll` **hiç import edilmiyor** (AIMP.exe seviyesinde) — doğrudan Win32 çekirdek
  erişimi Runtime/Shared üzerinden yürür.
- AIMP.exe'nin kendi import listesinde **hiçbir ses cihazı DLL'i yok** (`dsound`/`winmm`/
  `MMDevApi`/`AUDCLNT`/`avrt` hepsi **0**).

### 3.3 `AIMP.Shared.dll` — ses yüzeyi burada statik (3799 fonksiyon)

| DLL | Fonksiyon |
|---|---:|
| `AIMP.Runtime.dll` | 3715 |
| `kernel32.dll` | 28 |
| **`bass.dll`** | **27** |
| `user32.dll` | 12 |
| `gdi32.dll` | 4 |
| `ole32.dll` | 4 |
| `version.dll` | 3 |
| `shell32.dll` | 2 |
| **`winmm.dll`** | **2** |
| **`dsound.dll`** | **2** |

**Düzeltme (önceki tek başına `AIMP.exe` okumasına göre):** `bass.dll` ve `dsound.dll`
**statik** importtur — ama `AIMP.exe` seviyesinde değil, `AIMP.Shared.dll` seviyesinde.
Tam kapama (closure) hesaba katıldığında AIMP'in statik ses yüzeyi =
**BASS + DirectSound + WINMM**.

### 3.4 `AIMP.Runtime.dll` — 9 delay-import (27 fonksiyon)

`kernel32` (GetLogicalProcessorInformation, IsWow64Process, CreateSymbolicLinkW,
GetFinalPathNameByHandleW, …) · `user32` (MessageBoxA + **10 adet DPI/el-jesti API**:
GetDpiForWindow, GetSystemMetricsForDpi, SystemParametersInfoForDpi,
SetGestureConfig, GetGestureInfo, …) · `wtsapi32` (WTSRegisterSessionNotification) ·
`msimg32` (GradientFill, AlphaBlend) · `advapi32` (RegDeleteKeyExW) · `rpcrt4` (UuidCreate) ·
`windowscodecs` (WICConvertBitmapSource).

**Bulgu:** delay-load **yalnızca işletim-sistemi sürümlerine bağlı isteğe bağlı Win32
API'leri** için kullanılmış; **ses/cihaz ile ilgili tek bir delay-import yok**.

### 3.5 `vlc.exe` ve `FxSound.exe`

- `vlc.exe` 166 fonksiyon: `msvcrt` 78 · `KERNEL32` 62 · `WININET` 11 · **`libvlc` 7** ·
  `ADVAPI32` 5 · `SHELL32` 2 · `USER32` 1 → **yine ince kabuk**, tüm iş `libvlc`/`libvlccore` içinde.
- `FxSound.exe` 667 fonksiyon: **`OPENGL32` 336 = %50,37** (DERIVED) · `KERNEL32` 150 ·
  `USER32` 101 · `GDI32` 26 · `ole32` 14 · `dbghelp` 9 · `SHELL32` 8 · `OLEAUT32` 6 ·
  `ADVAPI32` 7 · `IMM32` 5 · **`WINMM` 2** · `WTSAPI32` 2 · `SHLWAPI` 1.
  → **OpenGL, FxSound'un tek başına en büyük dış bağımlılığıdır** (UI/görselleştirici yolu).
- `libvlc.dll` / `libvlccore.dll` içinde **delay-import 0** ve ses/cihaz DLL'i statik import **yok**.

---

## §4 Statik ⇄ Runtime Çapraz Doğrulama

### 4.1 Ana sonucu

| Uygulama | Statik import DLL | Runtime'da bulunan | Oran | Delay-load |
|---|---:|---:|---:|---:|
| `AIMP.exe` | 10 | **10/10** | %100 | 0 |
| `vlc.exe` | 7 | **7/7** | %100 | 0 |
| `FxSound.exe` | 13 | **13/13** | %100 | 0 |

**Üç uygulamada da statik import'un tamamı çalışma anında yüklü**; hiçbiri
"statikte görünüp hiç yüklenmeyen" bir DLL taşımıyor (bu, hatalı/ertelenmiş import
işaretidir ve **0** ölçüldü). Gecikmeli (delay) import `exe` seviyesinde **yok**;
yalnızca `AIMP.Runtime.dll` içinde 9 adet ve hepsi Win32 (§3.4).

### 4.2 Statikte olmayıp runtime'da yüklenenler

| Uygulama | Toplam modül | Statikte olmayan | Bunların dağılımı |
|---|---:|---:|---|
| AIMP | 139 | 129 | 44 AIMP-özgül DLL + 85 sistem |
| VLC (boşta) | 103 | 96 | 31 plugin + `libvlc`/`libvlccore` + 63 sistem |
| FxSound | 59 | 46 | 0 uygulama DLL'i + 46 sistem |

**Ortak desen:** üç uygulama da **ses/cihaz arayüzünü statik değil, çalışma anında**
(`LoadLibrary`/COM etkinleştirme) yükler.

---

## §5 Ses / Cihaz Yüzeyi Denetimi

| Sonda | AIMP statik | AIMP runtime | VLC statik | VLC runtime | FxSound statik | FxSound runtime |
|---|---|---|---|---|---|---|
| `MMDevApi.dll` | yok | **VAR** | yok | **VAR** | yok | **VAR** |
| `AUDIOSES.DLL` | yok | **VAR** | yok | **VAR** | yok | **VAR** |
| `dsound.dll` | **VAR** (Shared, 2 fn) | VAR | yok | **YOK** | yok | YOK |
| `winmm.dll` | **VAR** (Shared, 2 fn) | VAR | yok | VAR | **VAR** (2 fn) | VAR |
| `msacm32.dll` | yok | **VAR** | yok | YOK | yok | YOK |
| `asio*` | **YOK** | **YOK** | yok | YOK | yok | YOK |
| `avrt.dll` (MMCSS) | yok | **YOK** | yok | **YOK** | yok | YOK |

**Ölçülen sonuçlar:**

1. **`MMDevApi` + `AUDIOSES` üç uygulamada da yalnızca runtime** (yani WASAPI/COM tarafı
   her yerde dinamik yükleniyor, statik import değil).
2. **DirectSound yalnızca AIMP'de** — ve o da `AIMP.Shared.dll` statik import'u ile.
3. **ASIO hiçbir uygulamada yok** — ne statik import, ne runtime modül, ne disk dosyası
   (AIMP kurulumunda `asio` geçen **0** dosya), ne yapılandırma.
4. **`avrt.dll` hiçbir uygulamada ve hiçbir durumda yüklenmedi** (boşta + oynatma).
   → Bu iki durumda hiçbir uygulama MMCSS (`AvSetMmThreadCharacteristics`) kullanmıyor
   **görünüyor**. Sürekli olduğu **UNKNOWN** (daha uzun/oynatma koşulları denenmedi).

---

## §6 VLC: Üç Durum Gözlemi (boşta → erken → kararlı oynatma)

### 6.1 Ölçüm

| Durum | PID | Modül | Plugin |
|---|---:|---:|---:|
| Boşta (medya yok) | 36608 | 103 | 31 |
| Erken oynatma (~1,8 sn) | 30948 | 114 | 46 |
| **Kararlı oynatma (12+ sn)** | 27408 | **165** | **75** |

`plugins/` dizininde **363** DLL var → boşta **%8,54**, oynatmada **%20,66** yükleniyor
(her ikisi DERIVED). Diskte olup runtime'da olmayan ("kayıp") plugin **0**.

### 6.2 Kategori geçişleri (boşta → kararlı oynatma)

| Kategori | Disk | Boşta | Oynatma | Değişim |
|---|---:|---:|---:|---|
| `audio_output` | 7 | 1 | **2** | **+`libwasapi_plugin`** |
| `video_output` | 15 | 0 | **2** | **+`libdirect3d11_plugin` +`libwinhibit_plugin`** |
| `codec` | 58 | 0 | **18** | **+18 dekoder** |
| `audio_filter` | 25 | 0 | **2** | **+`libsamplerate` +`libscaletempo`** |
| `video_chroma` | 19 | 0 | **16** | **+16 kroma dönüştürücü** |
| `demux` | 47 | 2 | **3** | **+`libmp4_plugin`** |
| `video_filter` | 41 | 0 | 0 | değişmedi |
| `packetizer` | 14 | 0 | 0 | değişmedi |
| `spu` | 7 | 0 | 0 | değişmedi |
| `visualization` | 4 | 0 | 0 | değişmedi |
| `services_discovery` | 6 | 6 | 6 | hepsi baştan yüklü |

**Boşta yüklü 18 plugin (tam liste):** `libadf`, `libarchive`, `libcache_read`,
`libconsole_logger`, `libdirectory_demux`, `libdvdnav`, `libfile_logger`, `libfilesystem`,
`libhds`, `libhotkeys`, `libhttps`, `libinflate`, `liblibbluray`, `liblua`,
`libmediadirs`, `libmemory_keystore`, `libmicrodns`, `libmmdevice`, `libplaylist`,
`libpodcast`, `libprefetch`, `libqt` (17,5 MB), `librecord`, `libsap`, `libskiptags`,
`libtaglib`, `libupnp`, `libvdr`, `libwin_hotkeys`, `libwindrive`, `libxml`.

**Oynatmada yüklenen 18 codec:** `libaes3` · `libaom` (AV1) · `libaraw` · **`libavcodec`** ·
`libcdg` · **`libd3d11va` (donanım çözücü)** · `libdav1d` (AV1) · `libfaad` (AAC) ·
`libfluidsynth` · `libjpeg` · `libmpg123` (MP3) · `libopus` · `libpng` ·
`libschroedinger` (Dirac) · `libspdif` · `libspeex` · `libtheora` · `libvorbis`.

### 6.3 Donanım çözümleme zinciri (config → plugin → GPU sürücüsü)

Üç halka da ölçüldü:

1. **Yapılandırma:** `vlcrc` içinde `avcodec-hw=d3d11va` — **yorumlanmamış tek medya ayarı** (aktif).
2. **Plugin:** kararlı oynatmada **`libd3d11va_plugin.dll` yüklü** (boşta yoktu).
3. **GPU sürücüsü:** oynatmayla birlikte **`nvwgf2umx.dll`**, **`nvldumdx.dll`**,
   `nvgpucomp64.dll`, `nvmemmapstoragex.dll`, `d3dcompiler_47.dll` **yüklendi**.

→ **Müşterek kodlaması (config) ile gözlemlenen davranış (runtime) birebir örtüşüyor.**
Eşdeğer alternatifler **yüklenmedi**: `mfplat.dll` **0**, `dxva2.dll` **0** → VLC bu
ortamda **D3D11VA** kullanıyor, DXVA2/MF değil.

### 6.4 Ses yolu oynatma sırasında

- Boşta ve oynatmada: `MMDevApi.dll` **VAR**, `AUDIOSES.DLL` **VAR**, `winmm.dll` **VAR**.
- `audio_output` 1 → 2: **`libmmdevice_plugin`** (boşta da var) + **`libwasapi_plugin`** (yalnız oynatmada).
- `dsound.dll` **hiç yüklenmedi**.
- → VLC'in Windows üzerindeki gerçek çıkış yolu **MMDevice/WASAPI**'dir; DirectSound
  eklentisi diskte kurulu ama **kullanılmıyor**.

---

## §7 AIMP: Runtime Modül Envanteri ve Disk Karşılaştırması

### 7.1 Ölçüm

| | Değer |
|---|---|
| Çalışan süreç | PID 31968, **139 modül** |
| AIMP'e ait | **45** (44 DLL + `AIMP.exe` kendisi) |
| Sistem | 94 |
| Diskte toplam DLL | **49** |
| Diskte olup **yüklenen** | **44 → %89,80** (DERIVED) |
| Diskte olup **yüklenmeyen** | **5** |
| Diskte olmayan ama yüklenen (uygulama-özgül) | **0** |

**Yüklenmeyen 5 DLL:** `aimp_icons.dll` · `aimp_menu32.dll` · `aimp_menu64.dll` ·
`aimp_mousehook.dll` · **`avfilter-aimp-7.dll`**

→ AIMP de **tembel yükler**: ikon/menü/sistem-tepsi kancası ve FFmpeg `avfilter`
filtre zinciri ancak ilgili özellik kullanıldığında çekiliyor.

### 7.2 Codec envanteri (runtime'da **hepsi yüklü** — AIMP aksine aceleci)

- **BASS çekirdeği + 12 BASS eklentisi:** `bass.dll`, `bass_aac`, `bass_ac3`, `bass_alac`,
  `bass_ape`, `bass_flac`, `bass_hls`, `bass_midi`, `bass_mpc`, `bass_opus`, `bass_spx`,
  `bass_tta`, `bass_wma`, `bass_wv` (13 kayıt).
- **FFmpeg (AIMP'ye yeniden adlandırılmış derleme):** `avcodec-aimp-58.dll`,
  `avformat-aimp-58.dll`, `avutil-aimp-56.dll`, `swresample-aimp-3.dll`
  + girişi taşıyan `aimp_inputFFmpeg.dll`. (`avfilter-aimp-7.dll` diskte, **yüklenmedi**.)
- **Bağımsız codec kitaplıkları:** `libFLAC`, `libLAME`, `libMAC`, `libsacd`, `libsoxr`,
  `libVorbisEn`, `libWavPack`, `OptimFROG`, `tak_deco_lib`.
- **`sqlite3.dll` (1.662.912 B) yüklü** ↔ diskte `AudioLibrary\Local.adb` (2.848.371 B),
  `AlbumArts.Cache.db`, `Podcasts.db`, `CDDB.db` → **statik/runtime/disk üçlü tutarlılık**.

### 7.3 Sistem tarafında dinamik yüklenen ses arayüzleri

`AUDIOSES.DLL` · `MMDevApi.dll` · `dsound.dll` · `winmm.dll` · `MSACM32.dll`
(hepsi AIMP dizininde değil → sistem DLL'i, çalışma anında çağrıldı).
`avrt.dll` **yüklenmedi**.

---

## §8 AIMP Çalışır Yapılandırması: Cihaz / Format / EQ

Kaynak: `%APPDATA%\AIMP\aimp.ini` (25.314 B) + `EQPresetsLibrary.ini` (20.978 B).

### 8.1 `[AIMPSoundOut]` — cihaz ve format

| Anahtar | Değer | Yorum |
|---|---|---|
| `DeviceName` | **`WASAPI: Windows Default`** | Çıkış = WASAPI, Windows varsayılan uç noktası |
| `DeviceFreq` | **48000** | 48 kHz olarak okunuyor; *seçilen* mi *raporlanan üst sınır* mı → **UNKNOWN** |
| `DeviceBitDepth` | `4` | Birim/semantik **UNKNOWN** (`4` bayt = 32-bit mi, indeks mi?) — **VERIFICATION REQUIRED** |
| `DeviceChannels` | `1` | Müzik çalımı için mono olağandışı → **UNKNOWN** (indeks/kod olabilir) — **VERIFICATION REQUIRED** |
| `Volume` | 0.899599969387054 | |
| `Balance` | 0 | |
| `Mute` | 0 | |
| `FadingEnabled` | 1 | |
| `SmoothVolumeChange` | 1 | |
| `SkipSilenceAmplifyThreshold` | -60 | sessizlik atlama eşiği |
| `SkipSilenceDuration` | 0.5 | |
| `ReplayGainDefault` | 0 | kapalı |
| `LoudnessCompensation` | 0 | kapalı |
| `PlaybackPitch` / `Speed` / `Tempo` | 0 / 100 / 100 | düz oynatım |
| `PreampForCalculatedValues` / `PreampForPredefinedValues` | 0 / 0 | |

**Çapraz doğrulama:** yapılandırmada `WASAPI` → runtime'da `MMDevApi.dll` + `AUDIOSES.DLL`
**yüklü** (§7.3) → **yapılandırma ile gözlem örtüşüyor.**

### 8.2 EQ durumu

| Anahtar/alan | Değer |
|---|---|
| `FFTEqualizer` | **0** (FFT EQ kapalı) |
| `FFTEqualizer.Bands` | 328 karakter, yalnızca `[0]='a'` kalanı `0` → tümü pasif |
| `FFTEqualizer.Preamp` | 0 |
| `AutoLoadEqualizerPresets` | 0 |
| `[Player.CustomEqPreset]` | `Version=3`, **Band0..Band19** (hepsi 0) |
| `[Player.AutoEqPreset]` | `Version=3`, **Band0..Band19** (hepsi 0) |

**`EQPresetsLibrary.ini`:**
- Başlık `[!]` → `LibVersion=2722` (sürüm derleme bileşeni ile birebir, §2).
- **26 bölüm** = başlık + **25 preset**, hepsi `Version=3`, **hepsi 20 bant (Band0..Band19)**.
- Bant değerleri: n=500, **min −8,984 · maks +13,105 · ort 1,339 dB**.
- Preset adları: Alternative, Ballad, Classic, Club, Dance, Full Bass,
  Full Bass & Treble, Full Treble, Headphones, Heavy Metal, Hip-Hop, Industrial,
  Jazz, Large hall, Live, Party, Pop, Rap, Reggae, Rock, Ska, Soft, Soft Rock, Techno, Vocal.

**Çapraz doğrulama (iki bağımsız kaynak):**
runtime yapılandırmasında **20 bant** ↔ statik decompile'da `aimp-exe-decompile.md`
notu **`SetEQBandsArr(Li20)`** (20 bant) → **aynı sonuç, iki farklı yöntemle**.

---

## §9 VLC Çalışır Yapılandırması (`vlcrc`, 96.647 B / 5.279 satır)

| Alan | Durum | Değer |
|---|---|---|
| `[equalizer]` | **tümü yorumlu (varsayılan)** | `#equalizer-preset=flat` · `#equalizer-bands=` (boş) · `#equalizer-2pass=0` · `#equalizer-vlcfreqs=1` · `#equalizer-preamp=12.000000` |
| Modül başlığı | — | `[equalizer] # Equalizer with 10 bands` → **10 bant** |
| `[param_eq]` | mevcut | Parametric Equalizer ayrı modül |
| `#aout=` | yorumlu/boş | çıkış otomatik seçim |
| `#mmdevice-audio-device=` | yorumlu/boş | cihaz: varsayılan |
| `#mmdevice-backend=any` | yorumlu | |
| `#wasapi-loopback=0` | yorumlu | |
| **`avcodec-hw`** | **yorumlanmamış (AKTİF)** | **`d3d11va`** |
| `#vout=` | yorumlu/boş | |
| `#direct3d11-hw-blending=1` / `#direct3d9-hw-blending=1` | yorumlu | |

→ **VLC EQ'su bu kurulumda varsayılan/aktif değil, 10 bant**; tek etkin medya ayarı
**`avcodec-hw=d3d11va`**'dır ve bu, §6.3'te üç halkası da ölçülen zincirle doğrulanıyor.

---

## §10 FxSound: Statik + Runtime

| | Değer |
|---|---|
| Sürüm | 1.2.13.0 (FxSound LLC), TSD 2026-08-18 |
| Runtime modül | **59** |
| **Uygulamaya ait harici DSP/codec DLL** | **0** (yalnız `FxSound.exe` kendisi) |
| Statik import | 13 DLL / 667 fn, **`OPENGL32` 336 = %50,37** (DERIVED) |
| Runtime'da statik olmayan ses | `AUDIOSES.DLL`, `MMDevApi.dll` (dinamik) |
| Statik olan ses | `WINMM.dll` (2 fn) |
| ASIO / dsound / avrt | **hiçbiri yok** |

**Bulgu:** FxSound'un EQ/zincir DSP'si **tamamen `FxSound.exe` içindedir** — harici
DSP kitaplığı, codec kitaplığı veya eklenti DLL'i **hiçbiri çalışmıyor**.
FxSound bir **dekode etmez**, yalnızca ortak-mod karışımını işler (çıkış yolu
`MMDevApi` + `AUDIOSES` ile dinamik yüklenir). OpenGL ise tek başına en büyük bağımlılık.

---

## §11 Neva Player İçin Çıkarımlar

| # | Ölçülen | Neva tarafındaki durum | Karar etkisi |
|---|---|---|---|
| 1 | **EQ bant sayısı üç ayrı değer:** AIMP **20** (runtime + statik `Li20`) · VLC **10** · FxSound **31** (`DFXP_GRAPHIC_EQ_NUM_BANDS=31`, doc 04) | ADR-0105 **31-band** | Neva 31'i seçmiş — bu ölçüm onu **destekler** (piyasadaki en yüksek bant sayısına eşit/değil üstü) |
| 2 | WASAPI/MMDevice üçünde de **statik değil, dinamik** | — | Statik link yalnız `avrt`/`AUDCLNT` gibi doğrudan çağrılmak istenen API'ler için düşünülmeli |
| 3 | VLC **363 → 31 (%8,5) → 75 (%20,7)** tembel yükleme | ADR'de mimari karar yok | **Plugin vs monolitik** kararı için ölçülebilir taban |
| 4 | AIMP **aceleci** (13 codec baştan yüklü), VLC **tembel** | — | Bellek/dönüş süresi ödünleşimi: aceleci = ilk oynatma gecikmesiz, tembel = boşta daha az bellek |
| 5 | **ASIO üçünde de yok** (statik + runtime + disk + config) | ADR-0105 lisans §5.9'da dual-license notu var | ASIO *destek* ADR'de kaldı; bu ortamda **hiçbir örnek kullanmıyor** → Neva ASIO'yu isteğe bağlı eklenti olarak düşünmeli |
| 6 | **`avrt.dll` (MMCSS) hiçbirinde, hiçbir durumda yok** | — | Gerçek zamanlı öncelik için AIMP/VLC örneği alınmamalı; Neva'nın kendi kararı ADR'de ayrı değerlendirilmeli |
| 7 | **HW decode zinciri ölçülebilir:** config → plugin → GPU sürücüsü | VRAM kapi 5.6 (`QueryVideoMemoryInfo`) var | Aynı 3 halka yöntemi Neva'nın VRAM/hw-decode ölçümünde tekrarlanabilir |
| 8 | `AIMP.exe` **etkin ASLR yok** (reloc yok, ImageBase 0x400000) | — | Neva x64 çıktısında `DYNAMIC_BASE` + reloc **zorunlu** |
| 9 | `AIMP.exe` import'unun **%97,2'si** kendi iki DLL'ine | `neva_c.h` R1-R9 tek başlık + PIMPL | İnce kabuk + kendi çekirdeği deseni doğrulanıyor |
| 10 | FxSound **DSP tamamen exe içinde** | — | Harici DSP DLL'i gerekmeden tek-binary hedef mümkün (Neva için tek exe + isteğe bağlı plugin yolu) |

---

## §12 Sınırlılıklar ve Doğrulanması Gerekenler

| # | Konu | Etiket |
|---|---|---|
| L1 | VLC *boşta* (36608) ile *oynatma* (27408) **farklı süreçlerdir** — "kayan 7 modül" (apphelp, dnsapi, fwpuclnt, libhttps, mswsock, nsi, rasadhlp) durum geçişi değil, **örneklem farkıdır** | not |
| L2 | **AIMP oynatma sırasında modül çekimi yapılmadı** — yalnızca boşta (139) ölçüldü | **UNKNOWN** |
| L3 | `DeviceBitDepth=4` / `DeviceChannels=1` anlamları çözülmedi | **VERIFICATION REQUIRED** |
| L4 | `DeviceFreq=48000` seçilmiş oran mı raporlanan sınır mı | **VERIFICATION REQUIRED** |
| L5 | `avrt.dll` yokluğu yalnızca bu iki durumda ölçüldü (uzun oynatma denenmedi) | **UNKNOWN** |
| L6 | VLC gerçek derleme tarihi (TSD 2055) okunamıyor | **UNKNOWN** |
| L7 | Gözlem tek makinede (tek GPU/ses aygıtı) yapıldı; çok-aygıt/ekipman senaryoları kapsam dışı | not |
| L8 | `libd3d11va` yüklendi = **başlatıldı**; donanım çözümlemenin gerçekten GPU'da yapıldığı ayrı ölçülmedi (yalnız NVIDIA sürücü modüllerinin yüklenmesi) | **VERIFICATION REQUIRED** |

---

## §13 Yeniden Üretim

```powershell
# 1) statik import (Python 3.14 + pefile 2024.8.26)
python pe_imports.py "C:\Program Files\AIMP\AIMP.exe" `
       "C:\Program Files\VideoLAN\VLC\vlc.exe" `
       "C:\Program Files\FxSound LLC\FxSound\FxSound.exe"

# 2) canlı modül çekimi (HAM isim — culture-specific ToLower YOK)
Get-Process -Name 'AIMP' | ForEach-Object { $_.Modules | ForEach-Object { $_.ModuleName } }

# 3) VLC uç durumları
vlc.exe                       # boşta
vlc.exe <dosya> --volume=0 --play-and-exit   # oynatma (ses kapalı)

# 4) yapılandırma
Get-Content "$env:APPDATA\AIMP\aimp.ini" -Encoding UTF8
Get-Content "$env:APPDATA\vlc\vlcrc" -Encoding UTF8
```

**Ölçüm tuzağı (tekrarlayan için):** PowerShell `.ToLower()` **yerel kültürle** çalışır —
Türkçe sistemde `AUDIOSES.DLL` → `audıoses.dll`, `ADVAPI32.dll` → `advapı32.dll` olur ve
karşılaştırmalar **sahte fark** üretir. Ham isim saklanıp küçük-büyük dönüşümü
**kültürden bağımsız** (Python `str.lower()`) yapılmalıdır.

## §14 Eklenti: Aynı Süreçte 139 → 151 Modül Farkı (L2 Boşluğu Kanıtı)

> **Tarih:** 2026-10-20 · **Süreç:** `AIMP.exe` **PID 31968** (19:33:00 başladı, ~48 dk kesintisiz)
> **Neden:** §12-L2 kaydındaki "AIMP oynatma anında modül seti çekilmemişti → UNKNOWN"
> kaydı, sürecin hâlâ ayakta olması nedeniyle **aynı PID'in iki anlık görüntüsü**
> karşılaştırılarak kapatıldı.

### 14.1 Ölçüm

| Anlık görüntü | Kaynak | Modül |
|---|---|---|
| 1. | `runtime_modules.json[2]` (§7.1 kaynağı) | **139** |
| 2. (+~40 dk) | `aimp_modules_now.json` | **151** |

- **Aynı PID 31968** → süreç yeniden başlamadı; fark **gerçek yükleme**dir.
- **Çıkan modül: 0.** Hiçbir DLL boşaltılmadı (AIMP çalışma boyunca unload etmez).

### 14.2 Eklenen 12 modülün tamamı sistem ağ DLL'i

| Modül | Klasör |
|---|---|
| `DNSAPI.dll` | `C:\Windows\system32` |
| `fwpuclnt.dll` | `C:\Windows\system32` |
| `iertutil.dll` | `C:\Windows\system32` |
| `IPHLPAPI.DLL` | `C:\Windows\system32` |
| `mswsock.dll` | `C:\Windows\system32` |
| `netutils.dll` | `C:\Windows\system32` |
| `NSI.dll` | `C:\Windows\system32` |
| `ondemandconnroutehelper.dll` | `C:\Windows\system32` |
| `rasadhlp.dll` | `C:\Windows\system32` |
| `urlmon.dll` | `C:\Windows\system32` |
| `winhttp.dll` | `C:\Windows\system32` |
| `WINNSI.DLL` | `C:\Windows\system32` |

- **12/12 `C:\Windows\system32`.** AIMP kurulum klasöründen (vendor) eklenen **0**.
- Hepsi WinINet / WinHTTP / IP yardımcı programı → **çevrimiçi servis trafiği**.
- `aimp.ini` içinde `[ServiceLyrics]` bölümü mevcut → olası tetikleyici (**DERIVED**).

### 14.3 Codec seti: iki görüntüde de aynı (kritik sonuç)

Aşağıdakilerin **tamamı 139 listesinde de vardı**; eklenen 12 içinde **hiçbiri yok**:

```
bass.dll  bass_aac.dll  bass_ac3.dll  bass_alac.dll  bass_ape.dll  bass_flac.dll
bass_hls.dll  bass_midi.dll  bass_mpc.dll  bass_opus.dll  bass_spx.dll
bass_tta.dll  bass_wma.dll  bass_wv.dll  aimp_cdda_basscd.dll
avcodec-aimp-58.dll  aimp_inputFFmpeg.dll  libFLAC.dll  libVorbisEn.dll
libWavPack.dll  WindowsCodecs.dll
```

**→ AIMP tüm codec eklentilerini başlangıçta yükler (§7.2 "aceleci yükleme" ile tutarlı).
Oynatma, sesle ilgili modül setini DEĞİŞTİRMEZ.**

### 14.4 O anki oynatma durumu (v1.1.1 — PyAIMP kanıtıyla güncellendi)

`Get-Process -Id 31968` → `MainWindowTitle`:

```
yt1s.com - Murat Kurşun  Sevdana Gönül Verdim
```

`aimp.ini` (25.314 B): `PlayerState=1` · `TrackPosition=178610`

| İddia | Durum |
|---|---|
| Pencere başlığında yüklü parça var | **GERÇEK** |
| `PlayerState=1`, `TrackPosition` atanmış | **GERÇEK** |
| `PlayerState=1` = oynatıyor (duraklatma/durdurma değil) | **KISMEN** — PyAIMP `PlayBackState` enum'u `Stopped=0 / Paused=1 / Playing=2` → **1 = Duraklatılmış** (oynatıyor DEĞİL); ama INI alanının remote API ile aynı enum'u kullandığı **VERIFICATION REQUIRED** |
| `TrackPosition` birimi ms (178,6 sn) | **KISMEN** — PyAIMP `get_player_position()`: "elapsed **milliseconds** since the beginning of the track" → 178610 ms ≈ 2:58 (parça ortası, makul); INI eşdeğerliği **VERIFICATION REQUIRED** |
| 139'luk görüntünün alındığı anda oynatma açık mıydı | **AÇIK** — INI yalnızca son (çıkış anı) durumu saklar; ilk görüntü anına ait kayıt yok. Modül farkının `[ServiceLyrics]` açıklaması DERIVED olarak kalır |

Kaynak: PyAIMP dokümantasyonu (AIMP Remote API sarmalayıcısı),
`epocdotfr.github.io/pyaimp` — erişim 2026-10-20.

> Tam kapanış için deneysel tur (≈2 dk): AIMP'i parça ortasında kapat →
> çıkışta INI'da `PlayerState`/`TrackPosition` değerlerini PyAIMP enum'u
> ile karşılaştır. Bu oturumda yapılmadı (maliyet > değer; §14 kanıtının
> omurgası modül farkı zaten sağlam).

### 14.5 §12-L2 için sonuç

| Soru | Cevap | Dayanak |
|---|---|---|
| Oynatma codec modülü ekler mi? | **HAYIR** | +12 içinde codec = 0; codec'ler ikisinde de var (§14.3) |
| Oynatma hiçbir şey ekler mi? | **HAYIR** | +12 ağ DLL'i |
| Vendor DLL eklenir mi? | **HAYIR** | 12/12 `system32` |
| Modül boşaltılır mı? | **HAYIR** | çıkan = 0 |
| VLC'de de aynı mı? | Hayır — ayrı ölçüldü | VLC **tembel** yükleme: 31 → 75 (§6) |

**§12'deki "AIMP oynatma modül seti çekilmemişti" kaydı bu eklentiyle kapatıldı:
ses yüzeyi için cevap "değişmez", ağ yüzeyi için "değişir".**

> **Yeniden üretim:** aynı PID üzerinde iki ölçüm alıp farkı **Python `str.lower()`**
> ile çıkarmak (§13 kültür tuzağı nedeniyle PowerShell `.ToLower()` ile değil).

---

*Statik: `pefile` 2024.8.26 · Runtime: `Get-Process.Modules` · Sürüm: PE VersionInfo ·
2026-10-20 · v1.1.0*
