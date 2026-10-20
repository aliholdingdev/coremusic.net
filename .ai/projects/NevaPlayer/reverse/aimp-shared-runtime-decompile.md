---
title: "AIMP.Shared.dll + AIMP.Runtime.dll — Tam Full Decompile (Ghidra Headless)"
type: reverse-engineering
project: NevaPlayer
version: 1.0.0
status: active
date: 2026-10-20
authority: "Zero-hallucination: her iddia asagida kaynakli. Isim-eslesme sayimlari yalnizca orneklenmis terimler icin gecerli (bolum 6 kirlilik notu)."
evidence:
  - "C:\Program Files\AIMP\AIMP.Shared.dll | 4.641.728 B | sha256 E9622014E251A5AA2DEB1616187032C7388EA686448A1EBC304F711C8BC05CE7 | v5.4.0.2722 | PE x64 (0x8664/PE32+)"
  - "C:\Program Files\AIMP\AIMP.Runtime.dll | 15.701.952 B | sha256 6E2BA2DA397488B3153C73AADA7B8D83067415435A60E4A75D1BBD7D34C2AA81 | v5.4.0.2722 | PE x64 (0x8664/PE32+)"
  - "reverse/aimp-shared-full-decompile.txt | 12.459.577 B | sha256 E95BE6598CFE178DB84879EA0A6992D109564A53B1F1A9D21482D61B7A1A9FF1 | 14.817 prosedur | 0 dump hatasi | 49 sn"
  - "reverse/aimp-runtime-full-decompile.txt | 45.199.925 B | sha256 D554A736243933606B1E2D1179183422E82613C5F925292CB781BDC5210AABF8 | 50.249 prosedur | 2 dump hatasi | 327 sn"
  - "reverse/aimp-shared-proc-index.txt | 1.018.609 B | sha256 ED74D30DF0EE236F163F8D3A1B20C29427BA06B27D065418C2B79EE52B5028ED"
  - "reverse/aimp-runtime-proc-index.txt | 3.074.911 B | sha256 B74700CA9876E16880E689BB6E3A65FE70ECDB08009EDD45322AB2ED763F963C"
related:
  - "reverse/aimp-exe-decompile.md (tur14 — AIMP.exe katmani)"
  - "reverse/runtime-ozel-gozlem-capraz-dogrulama.md (tur15 — paralel oturum, runtime modul gozlemi; buralarla MUTABAKATLI)"
  - "@.ai/.decisions/accepted/ADR-0105-neva-player-media-stack.md (kapı 5.6/5.12)"
---

# AIMP.Shared.dll + AIMP.Runtime.dll — Tam Full Decompile (tur16)

## 1. Kapsam ve yontem

Tur14'teki AIMP.exe decompile'unun devami: AIMP 5.4.0.2722 kurulumundaki iki cekirdek
DLL tam olarak Ghidra'ya alinip TUM prosedur pseudocode'lari dump edildi.

| Og | Deger |
|---|---|
| Yontem | Ghidra 12.1.4 `analyzeHeadless.bat` + `DumpAllProcedures.java` (tur14 ile AYNI script) |
| Projeler | `Temp\opencode\ghidra-shared\AIMPShared` + `Temp\opencode\ghidra-runtime\AIMPRuntime` (kalici; yeniden import/analiz gerekmez, `-process` ile uzerine script calisilir) |
| Ek script | `ReadEqBands.java` (sembol bazli veri okuma — bolum 5) |
| Shared dump | 14.817 prosedur (9.001 isimli / 5.816 `FUN_`) · 0 hata · 49 sn · 12.459.577 B |
| Runtime dump | 50.249 prosedur (32.557 isimli / 17.692 `FUN_`) · 2 hata · 327 sn · 45.199.925 B |
| Index | Her iki dump icin `adres sembol` arama dosyasi (bolum 9) |

Not (sayim farki): tur8 REA `binary_overview` Shared icin 16.723 prosedur
"analyze edilmis" raporlamisti; bu tur Ghidra dump'i 14.817 FONKSIYON girdisi
uretti. Fark arac/kurulum farkindan kaynaklanir; bu dokumandaki otorite sayi
14.817'dir (dump dosyasinin kendi DUMP_DONE ciktisi ile birebir).

## 2. Mimari duzeltmesi — render nerede? (tur14 hipotezi REFUTED)

Tur14'te acik birakilan hipotez: "WASAPI render -> AIMP.Runtime.dll". **Bu tur
YANLIS cikti ve kapatildi.**

58 binary (dll/exe/apl/ax/ocx) uzerinde byte-tarama (IgnoreCase):

| Dosya | WASAPI | IAudioClient | AUDCLNT | mmdevapi | AVRT |
|---|---|---|---|---|---|
| AIMP.exe (tur14) | 0 | 0 | 0 | 0 | 0 |
| **AIMP.Shared.dll** | **70** | **5** | 0 | 0 | 0 |
| AIMP.Runtime.dll | 0 | 0 | 0 | 0 | 0 |
| diger 55 dosya | 0 | 0 | 0 | 0 | 0 |

AUDCLNT/mmdevapi/AVRT'nin Shared.dll'de de 0 olmasi normaldir: WASAPI COM
kullanimi vtablo uzerindendir (string'siz); ASCII/UTF-16 referanslar sadece
RTTI/sinif adlarinda ve yardimci fonksiyon adlarinda gorunur.

**AIMP.Runtime.dll'in rolu (import tablosu + dump ornekleri):**
`user32 · shell32 · kernel32 · advapi32 · windowscodecs · rpcrt4 · gdi32 ·
wtsapi32 · msimg32 · sqlite3.dll · imm32 · shcore · gdiplus · mp.runtime.dll ·
uxtheme · ole32 · comctl32 · ws2_32 · comdlg32 · oleacc` + DelayLoad `sqlite3_*`
(`DelayLoad_sqlite3_close @0x00ab5040`) — yani UI/infra + depolama + goruntu;
**ses cikisi YOK, ses decode YOK** (bolum 6).

**Paralel oturum (tur15) ile MUTABAKAT:** `reverse/runtime-ozel-gozlem-capraz-dogrulama.md`
"bass.dll + dsound.dll statik import'u AIMP.exe seviyesinde degil
**AIMP.Shared.dll seviyesinde**" bulgusu — bu turun dump'iyla dogrulandi:
Shared dump'inda `BASS_SetConfig @0x0051ac60` · `BASS_Init @0x0051aca0` ·
`BASS_PluginLoad @0x0051acb0` import thunk'lari + "bass" iceren 170 isimli
prosedur + `DirectSoundCreate @0x00606a10` · `DirectSoundEnumerateW @0x00606a20`.

**Sonuc (3 binary mimarisi, kanitli):**

| Katman | Dosya | Icerik |
|---|---|---|
| UI kabuk | AIMP.exe (7,1 MB) | Vcl/Acl/Aimp; Aso::* sembollerinin TAMAMI import-thunk (tur14) |
| **Ses cekirdegi** | **AIMP.Shared.dll (4,6 MB)** | **BASS decode motoru importlari + TASODevice cihaz katmani (WASAPI/ASIO/DirectSound) + TASOPlayer + Radio + Decoder yoneticisi + EQ + Fading/gapless + Replaygain + Playlist** |
| UI/infra runtime | AIMP.Runtime.dll (15,7 MB) | Vcl/Acl grafik (Skinimage), System RTL, sqlite3 (delay-load), ATL tag okuyuculari, web araclari; GDI+ tabanli "Render" prosedurleri (UI) |

## 3. Render katmani — Aimp.Shared.dll `Aso::Devices`

### 3.1 UTF-16 RTTI sinif/harita kanitlari (Shared, offset ~2.125.195+)

`TASOWASAP.Device` · `TASODeviceClientShared` · `TASODeviceClientExclusive` ·
`TASODeviceClientX` · `TASODeviceClientSharedFormatsEnumerator` ·
`TASODeviceClientExclusiveFormatsEnumerator` · `TASOWASAP.DefaultDevice` ·
`TASOWASAP.DeviceProvider` · enum `TWASAP.AudioClientShareMode { acsmShared,
acsmExclusive }` · enum `TWASAP.DataFlow { dfRender, dfCapture, dfAll }` ·
enum `TWASAP.Role { rConsole, rMultimedia, rCommunications }` · sarmalayici
adlari: `MMDevice`, `MMDeviceEnumerator`, `MMDeviceCollection`,
`AudioClient`, `AudioRenderClient`, `PropertyStore`, `MMNotificationClient`,
`WASPropertyKey` · `TWASAPDataStream` · `TASODeviceBuffer`/`TASODeviceBufferItem` ·
`TAclEnumerator<ASO.Devices.WASAP.TASOWASAP.Device>` · `ACL.Classes.Collections`.
ASIO ve DirectSound de ayni DLL'de: `ASO.Devices.ASIO.SettingsDialog`,
`ASO.Devices.DirectSound.Winapi.DirectSound`.

### 3.2 Uc backend — prosedur bolgeleri (index kaniti)

| Backend | Bolge | Kanit |
|---|---|---|
| ASIO | 0x00601910-0x00606450 | `TASOASIODevice::{DoClose, DoClosed, DoPlay, DoReinitialize, DoSwitchBuffer(int), DoUpdateBuffers, DoWaitForData, DialogAvailable, DialogExecute(HWND*), LoadSettings, SaveSettings, GetDriver}` + **ornek donusum yukleyicileri** `LoadDirect(uchar**,uchar**)`, `LoadDirect24`, `LoadDirect32`, `LoadDirect32Aligned`, `LoadSwapped`, `LoadSwapped24`, `LoadSwapped32`, `LoadSwapped32Aligned` + `TASOASIODeviceFormatsEnumerator::{IsSupported(int,int,TASOSampleFormat), Populate}` + `TASOASIODeviceProvider::Populate @0x00606340` + `TASOASIODeviceSettingsDialog::{Execute, ListChannels, LoadSettings, LoadChannelsState, SaveChannelsState, SaveSettings}` |
| DirectSound | 0x00606a10-0x00607930 | Import thunk'lari `DirectSoundCreate @0x00606a10`, `DirectSoundEnumerateW @0x00606a20` + ~25 **isimsiz** (`FUN_`) govde + modul `initialization/finalization @0x00607920/0x00607930` |
| WASAPI | 0x00607d30-0x00608080 | 8 isimli yardimci: `GetDeviceID(IMMDevice)` · `GetDeviceName(IMMDevice)` · `GetDeviceNameByID(IMMDeviceEnumerator, wchar*)` · `IsDefaultDevice(IMMDeviceEnumerator, UnicodeString)` + `initialization/finalization`; **istemci (client) govdesi bu bolgede ISIMSIZ (`FUN_`)** |

`Aso::Devices` altinda toplam 142 isimli prosedur. Taban sinif `TASODevice`
(Ianium mangling ile tam liste): `Open(uint, TASOSampleFormat, TASOChannelsMode)`
· `Play(float, uint)` · `Pause(uint)` · `Stop(uint)` · `Close()` · `DoPlay` ·
`DoPause` · `DoUpdateBuffers` · `DoWaitForData` · `DoGetBuffersLength` ·
`DoReinitialize` · `GetData(uchar*, uint)` · `SetSource(TASOMixer*)` ·
`BeforeDestruction` · `TASODeviceThread::{Start, Stop, Execute}` ·
`TASODeviceWaitForHelper::DataReceived(int, int)` ·
`TASODeviceManager::{PopulateDevices, ConfigLoad, ConfigSave, GetSetting(GUID,...),
SetSetting(GUID,...), ListenerAdd, ListenerRemove, NotifyDeviceArrival}` ·
`TASODeviceFormatsEnumerator::{Populate, TestChannels(int, TASOSampleFormat,
TASOChannelsMode&), TestSampleFormats(int, TASOSampleFormat&), TestSampleRate(int,
TASOSampleFormat, TASOChannelsMode)}` · `TASODeviceCustomFormatsEnumerator::{C3,
D0, IsSupported(int,int,TASOSampleFormat), Populate}` ·
`TASOStreamFormat::{D0, Initialize(TASOSampleFormat,int,int), CheckFormat(TASOStreamFormat&)}`.

## 4. Gercek govde kanitlari (dump'dan suzulmus pseudocode)

### 4.1 `TASODevice::Open @0x005a6610` — cihaz acilis akisi

1. Kaynak atanmamis ise `EASODeviceError("Cannot open device. Source is not assigned", -1)` raise.
2. `TASOStreamFormat::Initialize(TASOSampleFormat, int, int)` — (format, rate, channels).
3. `TASODeviceFormats::CheckFormat(...)` — iki kez; bos liste ise
   `EASODeviceError("Output format list is empty. It seems the device is not ready", -1)` raise.
4. `TASODataSource::SetFormat(StreamFormat)` → `TASODeviceThread::C3(TASODevice*)`
   — worker thread olusturulur; `Open` bu noktada tamamlanir.

### 4.2 `TASODeviceThread::Execute @0x005a7e50` — TABAN katman bekleme dongusu

```
while (true) {
  TACLCriticalSection::Enter();
  if ((*(byte*)(obj+0x10) & 0x20) == 0)  vtable+0x50(obj);   // sanal (Do* ailesi)
  TACLCriticalSection::Leave();
  if ((*(byte*)(obj+0x10) & 0x20) == 0)  vtable+0x58(obj);   // sanal (Do* ailesi)
  ... veri yokken TThread::Sleep(10);
}
```

**YORUM (derived):** AIMP'in taban cihaz katmanindaki bekleme **10 ms polling**.
WASAPI Exclusive "event-driven" davranisi bu taban katmanda DEGIL; isimsiz
istemci govdesinde (bolum 8, bekleyen 1).

### 4.3 `TASOStreamFadingOptions::C3 @0x005b1070` — varsayilan degerler

`(this+0x38)=1 (bool), (this+0x3a)=1000 (uint16)` ·
`(this+0x3d)=1, (this+0x3e)=1000` · `(this+0x40)=1, (this+0x42)=1000`.
`+0x38/+0x3a` ciftinin **crossmixing** oldugu `GetActualCrossMixingTime`
govdesiyle TEYITLI (bolum 4.4); diger iki cift fade-in/fade-out olarak
**derived** (setter offsetleri dogrulanmadi — `SetFadeIn/SetFadeOut/
SetCrossMixing/SetEnabled/ConfigLoad/ConfigSave/ResetToDefaults` hepsi dump'ta
mevcut, tur8'deki API yuzeyiyle ayni).

### 4.4 `TASOStreamOptions::GetActualCrossMixingTime @0x005b1a20`

`GetFading(reason)` → `+0x38` flag → `Math.IfThen(bool, *(uint16*)(+0x3a), 0)`
→ `return deger / _DAT_005b1a68`. Varsayilan 1000 (uint16); bolum sabiti
muhtemel 1000.0 (ms→saniye) — sabitin kesin degeri **dogrulanmadi (VR)**,
fakat `uint16` +1000 degeri + "CrossMixingTime" adi ile ~1.0 saniye sonucu
guclu derived kanittir. `TASOStreamOptions::GetFading @0x005b19c0` de dump'ta.

### 4.5 `SoundOutEqBandFreqString @0x0057efe0` — bant frekans gosterimi

`freq < _DAT_0057f078` (muhtemel 1000.0) ise `_Trunc(freq)` (tam Hz);
degilse `FormatFloat(&DAT_0057f0a4, freq / _DAT_0057f078)` — yani >=1000 Hz
bantlar kHz biciminde (16000 → "16" vb.). Eslikci:
`SoundOutEqBandFormat @0x0057eeb0`, `SoundOutEqBandToPercent @0x0057f0c0`,
`SoundOutPercentToEqBand @0x0057f100`, `SoundOutLogarithmicScale(Inv) @0x0051f520/0x0051f590`
(logaritmik bant olcekli dB↔yuzde donusumleri).

## 5. `EQ_BANDS` icerik dogrulamasi — ACIK MADDE KAPANDI

`ReadEqBands.java` (headless `-process`, sembol `Aso::Types::EQ_BANDS`)
dump disinda veri okudu. **Adres duzeltmesi:** sembol bu analizde
**@0x0064c070**; eski vault notlarindaki `0x668390` kaydi YANLIS`tir.

20 adet IEEE754 double (LE) — **Neva AK-3 band grid referansi**:

| # | Hz | # | Hz | # | Hz | # | Hz |
|---|---|---|---|---|---|---|---|
| 0 | 31.5 | 5 | 175 | 10 | 1000 | 15 | 5600 |
| 1 | 43 | 6 | 250 | 11 | 1400 | 16 | 8000 |
| 2 | 63 | 7 | 350 | 12 | 2000 | 17 | 11200 |
| 3 | 87 | 8 | 500 | 13 | 2800 | 18 | 16000 |
| 4 | 125 | 9 | 700 | 14 | 4000 | 19 | 22000 |

21. double'dan itibaren veri baska yapiya gecer (EQ_BANDS dizisi 20 eleman).
Bu degerler **ISO 1/3-oktav degildir** (43 / 87 / 175 / 11200 gibi AIMP-ozel
degerler) — Neva "AIMP-uyumlu 20-band mod" isterse bu tablo birebir kopya
referansdir. Uc bagimsiz kaynak ayni sonucu veriyor: (1) bu dump, (2) tur14
exe katmaninda `SetEQBandsArr(StaticArray<double,20>)` imzasi, (3) tur15
paralel gozlemde `aimp.ini` `Band0..Band19` (20 bant).

## 6. AIMP.Runtime.dll rolu — "codec" degil, TAG + UI (duzeltme)

Baslangicta kategori taramasi "Ogg 105 / Aac 46 / Opus 33 / Flac 30 / Tag 588"
gibi sayilar uretti; **ornekleme ile bu sayimlarin KIRLI oldugu gosterildi**:

| Gorunen eslesme | Gercek kaynak | Hukum |
|---|---|---|
| "Ogg" ×105 | `TCustomEdit::ObserverToggle` / `Toggle` ("ogg" alt-kelime) | SAHTE |
| "Aac" ×46 | prosedur ADRES hex'i (`0040aac0` vb.) | SAHTE |
| "Tag" ×588 | `System::TAggregatedObject` ("Tag" alt-kelime) | SAHTE |

**Dogrulanmis gercek icerik (isim ornekleri):**

- `Acl::Atl::Fileformats::Mp3::TATLTagsContainerMP3` (vtable @0x00cf8328, C3 @0x00cf8670)
- `Acl::Atl::Fileformats::Flac::TFlacBlock::{Init,Size}` @0x00ceb450/0x00ceb520
- `Acl::Atl::Fileformats::Opus::TATLTagOpusFieldMap` / `TATLTagOpusReader` @0x00cfbba0/0x00cfbdb8
  → bunlar **metadata/tag okuyuculardir** (ID3v2/APE konteyneri, FLAC STREAMINFO
  metadata blogu, OpusHead/OpusTags duzeyi) — audio DECODE degil.
- `Acl::Graphics::Skinimage::{TACLSkinImage, acCalculateTiledAreas @0x009f5480,
  EZLibError}` → skin/gorsel motoru.
- `sqlite3.dll` DELAY-LOAD (`DelayLoad_sqlite3_close @0x00ab5040`, `sqlite3_close @0x00ab5050`).
- "Render" ×141 isim — GDI+/UI render olasiligi; **orneklenmedi, derived (VR)**.

**Ses decode nerede?** BASS plug-in DLL'lerinde (diskte: `bass_flac.dll`,
`bass_aac.dll`, `bass_opus.dll` vb. — tur15b paralel gozlemde runtime'da
yuklenirken olculdu). AIMP.Shared.dll sadece BASS ana API'sini import eder
(bolum 2). Boylece tur8'deki "Aimp.Runtime.dll = codec" izlenimi de duzeltilir:
codec ne Runtime.dll'de ne Shared'de statik; plugin DLL'lerinde.

## 7. Neva'ya dogrudan girenler

| AIMP kaniti (bu tur) | Neva karsiligi |
|---|---|
| `TASODevice::Open` akisi: format dogrula → thread uret; `TestSampleRate/TestSampleFormats/TestSampleChannels` ile cihaz yetenek taramasi | Kapı 5.6 Exclusive format matrisi: `IsFormatSupported` oncesi yetenek tarama kalibi — AIMP ile ayni soruyu soruyor (cihaz bu formati kabul eder mi?) |
| Taban katman 10 ms polling; Exclusive event-driven isimsiz istemci katmaninda | ADR-0105 §5.4 Neva hedefi (event-driven exclusive) AIMP'in taban katmanindan ILERIDE; yine de AIMP'in exclusive modu calistigini biliyoruz (RTTI `acsmExclusive` + `TASODeviceClientExclusive`) |
| EQ_BANDS 20 double (bolum 5) | 20-band "AIMP-uyumlu" mod icin birebir grid; 31-band (AK-3 ana hedef) icin ISO 1/3-oktav ADR-0105 §2.1'deki gibi korunur |
| Crossmixing varsayilan 1000 (uint16; ~1.0 s, VR) | Gapless crossfade referans degeri (kapı 5.12 A/B); AIMP hangi birimde sakliyor — uint16 + ms sonucuyla tutarli, kesin bolum sabiti VR |
| ASIO `LoadDirect24/32/32Aligned` + `LoadSwapped*` ornek donusum ailesi | Exclusive modda bit-perfect yol dogru formatta ise donusum YOK; donusum lazimsa bilincli format dusme — Neva "exclusive + custom format" kombinasyonunda ayni ayrimi yapmali |
| `TASODeviceManager::ListenerAdd/NotifyDeviceArrival` (hot-plug) | Cihaz degisikligi dinleyicisi — Neva IAudioClient3 shared fallback (ADR-0105 risk 6) tetikleyicisi |

## 8. RE bekleyenler (bu tur kapanmayanlar)

1. **WASAPI istemci govdesi** — `TASODeviceClientShared/Exclusive` metotlari
   dump'da ISIMSIZ (`FUN_`, bolge ~0x00606xxx-0x0060dxxx); adlandirma icin
   RTTI→vtable→method eslemesi gerekli. Exclusive event-driven'in nihai teyidi
   (Initialize hangi mode ile, buffer stratejisi) bu adima bagli — kapı 5.6
   icin EN ONCELIKLI acik madde.
2. **IAudioClient3 kaniti YOK** — pseudocode metninde `IAudioClient3`/`AUDCLNT`
   gecmiyor (COM vtable = string'siz); "AIMP IAudioClient3 kullaniyor mu"
   su an icin **UNKNOWN**.
3. `TASOPlayer` 183 isimli prosedur — Shared dump'inda MEVCUT (Load/Play/
   SetDeviceParams/tempo-pitch icin ayri tur; tur8'deki 12-prosedur ozeti
   artik tam dump ile genisletilebilir).
4. Radio 198 / Decoder 185 isimli prosedur (Shared) — ADR-0106 radyo decode
   kenari icin ayri tur.
5. Sabit bolumleri: `_DAT_005b1a68` (ms→saniye) ve `_DAT_0057f078` (Hz→kHz
   esigi) — degerleri dump'da tanimsiz (data sembolu); kesin deger icin veri
   okuma (ReadEqBands kalibi) gerekir.
6. Runtime "Render" ×141 ve Shared "Tag" ×645 sayimlari orneklenmedi (bolum 6
   kirlilik notu nedeniyle raporlanmiyor).

## 9. Vault artefaktlari

| Dosya (reverse/) | Icerik |
|---|---|
| `aimp-shared-full-decompile.txt` | Shared.dll tam pseudocode (14.817 proc; hash bolum onunde) |
| `aimp-runtime-full-decompile.txt` | Runtime.dll tam pseudocode (50.249 proc) |
| `aimp-shared-proc-index.txt` | Shared adres→sembol indeksi (14.817 satir) |
| `aimp-runtime-proc-index.txt` | Runtime adres→sembol indeksi (50.249 satir) |
| `aimp-shared-runtime-decompile.md` | Bu ozet |

Arama ornegi: `Select-String -Path aimp-runtime-proc-index.txt -Pattern "TASOASIODevice"`.

## 10. Revizyon kaydi

- **v1.0.0 (2026-10-20, tur16):** ilk yazim. Iki acik madde kapatildi (render
  yeri = Shared.dll; EQ_BANDS icerik). Tur14 "Runtime.dll WASAPI" hipotezi
  refuted; tur8 "Runtime.dll codec" izlenimi duzeltildi (tag okuyucular +
  BASS plugin modeli). Paralel tur15 doc'u ile mutabakat saglandi.
