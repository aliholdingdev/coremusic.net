---
title: "AIMP.Shared.dll + AIMP.Runtime.dll — Tam Full Decompile (Ghidra Headless)"
type: reverse-engineering
project: NevaPlayer
version: 1.5.0
status: active
date: 2026-10-22
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

### 3.1 ASCII ShortString RTTI sinif/harita kanitlari (Shared, offset ~0x206C80+)

> **Duzeltme (tur17, hex-dogrulanmis):** Bu bolümün tur16 basligi "UTF-16 RTTI"
> idi — **YANLIS**. Turkce tarama UTF-16 kodlamasiyla yapilmis, stringler
> **ASCII (Delphi ShortString: 1 uzunluk bayti + karakterler)** olarak
> 0x206C80-0x208500 araliginda bulunmustur. Ek duzeltmeler (hepsi hex/Index
> dogrulamali): sinif adlarinda **nokta YOK** (`TWASAP.AudioClientShareMode`
> degil **`TWASAPIAudioClientShareMode`**; `TASOWASAP.Device` degil
> **`TASOWASAPIDevice`** — onek `TWASAPI`/`TASOWASAPI`, birlesme camel-case).
> **REFUTED (Shared.dll'de YOK):** `TASODeviceBuffer`/`TASODeviceBufferItem`,
> `TAclEnumerator`, `TASOSettingsDevicelistHelper`. Unit yollari NOKTALI
> yazilir (`ASO.Devices.WASAPI.Types`); Itanium `_ZTRN` tip-referanslari da
> ayni camel-case adlari tasir (`_ZTRN3Aso7Devices6Wasapi5Types27TWASAPIAudioClientShareModeE`
> @0x377F24, `...11TWASAPIRoleE` @0x377D36, `...12IAudioClientE`).

Dogrulanmis sinif/ad kumesi (adres = dosya offset'i):

- `TASOWASAPIDevice` (16) @0x20806A/0x20808A · `TASOWASAPIDefaultDevice` @0x20820F/0x208232 ·
  `TASOWASAPIDeviceProvider` @0x208442/0x20846A
- `TASODeviceClient` (16) @0x2075BA/0x2075DA · `TASODeviceClientShared` @0x207717/0x20773A ·
  `TASODeviceClientExclusive` (25) @0x2079F7 · `TASODeviceClientSharedFormatsEnumerator` @0x207866 ·
  `TASODeviceClientExclusiveFormatsEnumerator` @0x207B62
- Enum/rtti: `TWASAPIAudioClientShareMode` {`acsmShared`, `acsmExclusive`} @0x206D8A/DB7/DC2 ·
  `TWASAPIDataFlow` {`dfRender`, `dfCapture`, `dfAll`} @0x206DF8/E1B · `TWASAPIRole`
  {`rConsole`, `rMultimedia`, `rCommunications`} @0x206E5C · `PWASPropertyKey` @0x206EC3 ·
  sarmalayicilar: `IMMNotificationClient` @0x206EE9 · `IPropertyStore` @0x206F42 · `IMMDevice` @0x206F92 ·
  `IMMDeviceCollection` @0x206FE2 · `IMMDeviceEnumerator` @0x20703A · `IAudioClient` @0x207092 ·
  `IAudioRenderClient` @0x2070E2 · `PPWaveFormatExX` @0x206D62 — hepsi unit `ASO.Devices.WASAPI.Types`
- Taban cihaz katmani @0x1A29xx: `TASODeviceState` {`sodsOpen`, `sodsFlushing`, `sodsDisconnected`,
  `sodsStopping`, `sodsPausing`, `sodsSuspended`} @0x1A29EA · `TASODeviceAttribute`
  {`sodaNative`, `sodaExclusive`, `sodaVirtual`} @0x1A2A9A · `IASODeviceManagerListener` @0x1A2B32
- Diger: `TASODataStream` @0x15741E (unit `ASO.DataStream` @0x1570A8) ·
  `ASO.Devices.ASIO.SettingsDialog` @0x200CE6 · `ASO.Devices.DirectSound` unit @0x205AEA+ ·
  `ASO.Devices.DirectSound.Winapi.DirectSound.Winapi.DXTypes` @0x22ACF2 ·
  `ACL.Classes.Collections` @0x13FD8 · `TACLMoveArrayManager<...>` generik @0x33F50
  (WASAPI uzmanlasma ornegi ~0x60D770, Ghidra qword okumasi: `ACLMoveArrayManager<ASO.Devices.WASAPI.TASOWASAPI...`)

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

1. ~~**WASAPI istemci govdesi**~~ — **KAPANDI (tur17, bolum 11).**
   `TASODeviceClientShared/Exclusive` vtable'lari eslesti (@0x608A40/@0x6088C0),
   exclusive event-driven teyit edildi (SetEventHandle + WaitForNoSynchronize).
2. ~~**IAudioClient3 kaniti YOK**~~ — **REFUTED (tur17).** IID_IAudioClient3
   `{726778CD-F60A-4eda-82DE-1DA9D362EC21}` bayt taramasinda Shared.dll'de
   BULUNMADI (kontrol: IID_IAudioClient @0x665318, IID_IAudioRenderClient
   @0x665338, IID_IMMDeviceEnumerator @0x665348 mevcut) + arayuz RTTI
   listesinde adi yok → AIMP yalnizca `IAudioClient` (v1) kullanir.
3. ~~`TASOPlayer` 183 isimli prosedur~~ — **KAPANDI (tur19, bolum 13):**
   kontrol katmani eslendi (Load/transport/seek/cihaz yasam dongusu +
   gapless sonraki-parca zinciri + tempo/pitch). Olcum: `TASOPlayer` =
   **101** isimli prosedur (tur8'deki "183" dogrulanamadi), `Aso::Player`
   namespace toplami = 438. Loader thread ic yapisi + WndProc
   detaylari kapandi (tur20, bolum 13.8) — Radio/Decoder turlari madde 4'te ayrI.
4. ~~Radio 198 / Decoder 185 isimli prosedur~~ — **Decoder KAPANDI (tur21,
   bolum 14):** decoder katmani tam eslesti (185 proc = factory + TBASSDecoder + 7
   plugin literal). Kalan acik: **Radio 198** proseduru — ADR-0106 radyo decode
   kenari icin ayri tur.
5. ~~Sabit bolumleri: `_DAT_005b1a68` (ms→saniye)~~ — **KAPANDI (tur18):
   double 1000.0** (raw 0x408F400000000000, `ReadPlayerConsts.java` ile
   okundu — bolum 12.6); `GetActualCrossMixingTime` = cross-mix (ms, short
   @ fading+0x3A) / 1000 = saniye. ~~Kalan acik: `_DAT_0057f078`~~ — **KAPANDI
   (tur21): double 1000.0** (bolum 14.6): `SoundOutEqBandFreqString` divisor — freq < 1000
   ise IntToStr, aksine FormatFloat(freq / 1000.0) + `LAB_0057f0b8` ("kHz" eki). *(Onceki kismen-kapanan: DAT_0060a788 =
   float 0.2 (sn) ve DAT_0060a790 = double 250.0 — tur17, bolum 11.7.)*
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
- **v1.1.0 (2026-10-20, tur17):** bolum 11 eklendi — WASAPI istemci eslemesi
  (kapi 5.6 KAPANDI). §3.1 "UTF-16" etiketi ASCII ShortString olarak
  duzeltildi; 3 iddia REFUTED (TASODeviceBuffer, TAclEnum*,
  TASOSettingsDevicelistHelper); IAudioClient3 REFUTED (IID bayt taramasi);
  §8 madde 1/2 kapandi, madde 5 kismen kapanan olarak isaretlendi.
- **v1.2.0 (2026-10-20, tur18):** bolum 12 eklendi — oynatma boru hatti
  (TASOMixer::GetData pull-model cekirdek + swap-able DSP slotlari +
  SourceStreamList crossfade/gapless). `_DAT_005b1a68` = double 1000.0
  teyitli (ReadPlayerConsts.java, exit=0); `DAT_005b2540` = 0.0 (terminate
  fade hedefi). §8 madde 3 kismen kapandi, madde 5'ten ms→saniye kismi
  kapandi.
- **v1.3.0 (2026-10-20, tur19):** bolum 13 eklendi — kontrol katmani
  (TASOPlayer: uye haritasi, async yukleme, cihaz hot-swap, transport,
  seek, gapless sonraki-parca, tempo/pitch). **Tam zincir adlari
  kilitlendi** (AfterConstruction @0x5DEF10): TASOStream ->
  SpeedControlFilter -> DSPManager -> Mixer -> Device. Teyitli sabitler:
  uzun-pause geri sarma 10.0 sn, seek min 0.0, yon/state tablolari.
  §8 madde 3 kapandi (olcum: TASOPlayer=101, Aso::Player=438 — tur8 "183"
  dogrulanamadi).
- **v1.4.0 (2026-10-21, tur20):** bolum 13.8 eklendi — loader thread
  mesaisi (ExecuteTask dispatcher + FileLoad/FilePreload/Reconnect +
  LoadFile radio akisi + PrepareNextFile skip-listesi guard'i) + WndProc
  mesaj tablosu (0x956/0x957/0x95a/0x95e) + WndProcEvent olay kodlari
  (1-6) + TASOPlayerLoadingParams offset haritasi + GetStateForTrackStart
  @0x5DEFA0. §8 madde 3'teki "Kalan" satiri kapandi. Yeni bulgu:
  `Basstypes::TBASSDecoder` (170 sembol satiri) — Shared.dll'de BASS
  tabanli decoder katmani; DLL literal adi dump'ta YOK (dolayli yukleme) — **tur21
  duzeltmesi: REFUTED — statik import, bolum 14.1** (dump pseudocode != PE bayt).
  Decoder turu (185 prosedur) icin isaret; §6 "BASS plugin modeli" ile
  uyumlu.
- **v1.5.0 (2026-10-22, tur21):** bolum 14 eklendi — decoder katmani (185 proc)
  + **bass.dll STATIK import duzeltmesi**. PE import parse: bass.dll = AIMP.Shared.dll
  import descriptor [0] (27 fonksiyon, delay import = 0) — v1.4.0 "dolayli yukleme"
  inferansi **REFUTED** (dump pseudocode != PE bayt; satir 333 ve 808 notlari bolum 14.1
  ile duzeltildi). Nuanstan: bass.dll cekirdegi statik, ama opsiyonel codec-plugin
  giris noktalari GERCEKTEN dinamik — `acGetProcAddress` ile BASS_ALAC/MP4/WV_
  StreamCreateFile (TAIMPBASSM4APlugin @0x52D230, TAIMPBASSWavPackPlugin @0x52D810).
  Yeni kanitlar: factory iki faz (CreateDecoderForFileURI @0x54D140 kilitli giris,
  satir 215917-215928 -> CreateDecoderForParams @0x54CBD0 "File was not found." /
  "Decoder was not found for this format." + SetInfo 0xffffffff -> CreateDecoderForStream
  @0x54D4C0 (satir 215758) -> Failed(hr) && uzanti sayaci > 0 ise extension fallback,
  ters iterasyon, Succeeded || hr == E_PENDING (0x8000000A) kabul); TBASSDecoder ctor
  @0x51D150 (BASS_ChannelSetAttribute 0x13001 + DAT_0051d218 = 0.0) + Read @0x51D4A0 /
  SetPositionEx @0x51D4F0 / GetStreamInfo @0x51D410 / IsRealTimeStream @0x51D480 = false /
  IsSeekable @0x51D490; TBASSLibrary::EnsureInitialized @0x51D7C0 (BASS_Init 44100 + 15
  SetConfig raw id); RTTI 7 plugin literal (BASS/MIDI/MP4/WV/TAK/Wave64); BASS playback
  API dump = 0 hit — BASS yalniz DECODER, cikis WASAPI (bolum 11-13 ile bagimsiz
  Mutabik); WMA encoder CreateHandle @0x624860 (BASS_WMA_EncodeOpen, 27 import icinde DEGIL
  — mekanizma VERIFICATION REQUIRED). Sabitler: `_DAT_0057f078` = double 1000.0
  (Hz→kHz, bolme 1000 + "kHz" eki). Olcum: Decoder = 185, Radio = 198,
  _ZN9Basstypes = 30, _ZN3Aso8Decoders = 23.
  — §8 madde 4 (Decoder 185) ve madde 5 (_DAT_0057f078) kapandi; **Radio 198** kaldi.
- **v1.6.0 (2026-10-22, tur22):** bolum 15 eklendi — radyo decode kenari (ADR-0106
  kapanis turu). D agilim (proc-index, 198): RemoteStream 48 · Recorder 91 (9'u
  `TACLLinkedList<TASORadioRecorderBufferChunk>` generic) · OptionsFrame 30 (UI-only) ·
  FormatStringFactory 13 · StationInfo 6 · MetaDownloader 2 · IsRadioStream 1 = 191;
  kalan 7 = Radio/Radio::Stream namespace init+final (0x5B8D40/0x5B8D50/0x5D60F0/0x5D6130)
  + 3 VCL (SetRadioItem @0x406E10, TAIMPUIRadioBoxControl @0x43E028/@0x44B5C0).
  Ana bulgu: **radyo decode kendi HTTP client'i DEGIL** — `Connect` @0x5D09D0
  `BASS_StreamCreateURL(url,0,DECODE|STATUS,ProcDownload)` + BLOCK retry (MEM=1) +
  FLOAT recreate (depth>23) + realtime busy-wait (`filepos(9)` progress = 100-Max(x,0);
  filepos 9 VR) + 7 sync kenari (META=4/DOWNLOAD=7/OGG_CHANGE=0xc dogrulandi;
  0x10100/0x10101/0x10300/0x10301 VR). Metadata zinciri: ReceiveMetadata @0x5D3030 ->
  ParseMeta (tags 8/5 + attrib 0xc Round->bitrate; tag 8 VR) -> realtime ise pending
  meta + TACLTimer debounce (interval `FUN_005d2f50` ile HESAPLANIR, sabit degil) ->
  HandlerSyncMetaDelayTimer @0x5D36A0 -> UpdateMetadata @0x5D31A0 (IsTrackChanged &&
  IsRealTimeStream -> Assign+GetPosition+DoTrackChanged; else BeginUpdate/Assign/
  CancelUpdate) - Recorder = tee-sink (StartCore @0x5BDB80 -> OnData TS+0x84 -> Buffer ->
  OnTrack @0x5BDF00 split dallanmasi -> SetActive @0x5BE280). GetContentType @0x5D3260
  eslemesi kesin (0x10301->audio/mpeg, 0x10002/0x10901->audio/ogg, 0x10003-5->audio/mpeg,
  0x10300->audio/wma, 0x10b00->audio/aac; isim atflari VR). BASS sabitleri bass.h 2.4
  (2012, Un4seen) ile dogrulandi (bolum 15.6 tablosu - dogrulanmis/VR ayrimi);
  tags(7)=BASS_TAG_MP4 (APE=6) duzeltmesi. **Radio 198 KAPANDI** — §8 madde 4 tamamen
  kapali; §14.7 sayimindaki "KALAN tek tur" ibaresi de guncellendi.

## 11. WASAPI istemci eslemesi (tur17 — kapi 5.6 kapandi)

Yontem: Ghidra 12.1.4 headless (`ghidra-shared` kalici proje, scriptler
`MapWasapiClient.java` + `ReadWasapiRTTI.java`, exit=0) + dump pseudocode
blok cikarimi + DLL uzerinde ASCII/UTF-16/GUID bayt taramasi. Slot
anlamlari ASIO'nun sembol-lu vtable'i (@0x6028a8) = Rosetta tasi.

### 11.1 Cihaz vtable slot sirasi (kanon, 14 slot)

`[0] DoCreateFormatsEnumerator · [1] DoGetAttributes · [2] DoGetBuffersLength ·
[3] DoOpen · [4] DoClose · [5] DoClosed · [6] DoPause · [7] DoPlay ·
[8] DoReinitialize · [9] DoUpdateBuffers · [10] DoWaitForData ·
[11] DialogAvailable · [12] DialogExecute · [13] IsReady`

| Backend | Vtable | DoOpen | DoPlay | DoWaitForData | DoUpdateBuffers |
|---|---|---|---|---|---|
| ASIO (kanon) | @0x6028a8 | isimli | isimli | isimli | isimli |
| DirectSound | @0x6065f0 | FUN_00607350 | FUN_006071e0 | (tabanda) | FUN_00607670 |
| WASAPI A | @0x608bc8 | FUN_0060b6f0 | FUN_0060b3e0 | FUN_0060b8d0 (bos stub) | FUN_0060b8a0 (bos stub) |
| WASAPI B | @0x608d98 | ayni slot fonksiyonlari | | | |

Iki WASAPI cihaz vtable'i **byte-identik** → Shared/Exclusive cifti
(TASOWASAPIDevice varyantlari; RTTI @0x20806A). `DoWaitForData` bos stub =
WASAPI'de bekleme cihaz sinifinda degil **istemci sinifinda** yapilir
(asagidaki 11.4). Format enumerator `IsSupported`: WASAPI=FUN_0060a9e0,
DS=FUN_00606ce0. FormatsEnumerator vtable'lari: Shared @0x608e00 (FUN_0060a2a0),
Exclusive @0x608e80 (FUN_00609e30), DS @0x606820 (FUN_00606ca0).

### 11.2 GUID bayt dogrulamasi (.data; kaynak: Windows SDK mmdeviceapi.h genel IID/CLSID)

| Sembol | GUID | Adres | Durum |
|---|---|---|---|
| CLSID_MMDeviceEnumerator | `bcde0395-e52f-467c-8e3d-c4579291692e` | @0x665308 | OK (tur16) |
| IID_IAudioClient | `1cb9ad4c-dbfa-4c32-b178-c2f568a703b2` | @0x665318 | OK (tur17) |
| IID_IAudioRenderClient | `f294acfc-3146-4483-a7bf-addca7c260e2` | @0x665338 | OK — PTR sembol `...IID_IAudioRenderClientE_00668330` → 0x665338 hedefi teyitli |
| IID_IMMDeviceEnumerator | `a95664d2-9614-4f35-a746-de8db63617e6` | @0x665348 | OK (tur16) |
| IID_IAudioClient3 | `726778cd-f60a-4eda-82de-1da9d362ec21` | — | **BULUNMADI → IAudioClient3 REFUTED** |

### 11.3 Provider / fabrika / bildirim katmani

- `FUN_0060bb90` = `TASOWASAPIDeviceProvider` ctor: `CoInitialize` +
  `CoCreateInstance(CLSID_MMDeviceEnumerator, IID_IMMDeviceEnumerator)` +
  `RegisterEndpointNotificationCallback(this+0x20)` (gövde kaniti: log
  `"RegisterEndpointNotificationCallback failed"`, `"Cannot initialize COM library"`).
- `FUN_0060ac80` = cihaz fabrikasi: `TASODevice::C3` + `IMMDevice@+0x90` +
  `ID@+0x98` + log `"WASAPI: Create Device - "` (+`"Windows Default"`).
- `FUN_0060ba60` = "Windows Default" varsayilan cihaz uretimi; `FUN_0060c2b0` =
  `GetDeviceNameByID` (log `"WASAPI: GetDeviceNameByID - ID: "`).
- `FUN_0060bd60` = enumeration: `EnumAudioEndpoints(dfRender, DEVICE_STATE_ACTIVE)`
  → `IMMDeviceCollection::GetCount` (slot 0x18) / `Item` (slot 0x20) dongusu +
  `IMMDevice::OpenPropertyStore` + `IPropertyStore::GetValue(PKEY_Device_FriendlyName)`;
  rol adlari `"rConsole"/"rMultimedia"/"rCommunications"`.
- `FUN_0060c430` = `IMMNotificationClient::OnDeviceStateChanged` (gövde kaniti:
  log `"WASAPI: OnDeviceStateChanged - "`, vtable PTR @0x7d77fc).
  Bildirim client'i provider nesnesine **gömülü** (+0x20); 5-slot ayri
  vtable @0x609768 (diz/liste yardimcisi; kuyruk asmasi notu) — slot-level
  bildirim eslemesi kucuk acik is.

### 11.4 Istemci (IAudioClient sarmalayici) vtable'lari — KANONIK BULGU

Iki 7-slot COM vtable (Ghidra): @0x6088c0 = **event-driven exclusive**,
@0x608a40 = **periodik shared**. Slot sirasi ayni; farklar [1] ve [6]'da.

**@0x6088c0 — TASODeviceClientExclusive (event-driven):**

| Slot | Fonksiyon | Kanit (gövde) |
|---|---|---|
| [0] | FUN_0060a4f0 | `Activate` sarmalayicisi (FUN_00609c00 + wrap) |
| [1] | FUN_0060aa30 | Init: sarmalayici Initialize + `TACLEvent::C3(manual=1)` + **`IAudioClient::SetEventHandle` (slot 0x68)** — literal hata metni `L"IAudioClient.SetEventHandle"` |
| [2] | FUN_0060aae0 | Stop: `IAudioClient::Stop` + event signal + free |
| [3] | FUN_00609af0 | intf clear (ortak) |
| [4] | FUN_0060ab60 | Render: `IAudioRenderClient::GetBuffer` (slot 0x18) + `TASODevice::GetData` + `ReleaseBuffer` (slot 0x20) |
| [5] | FUN_0060ac20 | Wait: **`TACLEvent::WaitForNoSynchronize(INFINITE)`** — render thread olay bekler |
| [6] | FUN_0060ab10 | Ham `IAudioClient::Initialize(shareMode=1 EXCLUSIVE, flags=0x40000 AUDCLNT_STREAMFLAGS_EVENTCALLBACK)` |

**@0x608a40 — TASODeviceClientShared (periodik):**

| Slot | Fonksiyon | Kanit (gövde) |
|---|---|---|
| [0] | FUN_0060a4f0 | ayni Activate |
| [1] | FUN_0060a580 | Init: `GetDevicePeriod` (slot 0x48, literal `L"IAudioClient.GetDevicePeriod"`) → buffer = `max(2*period, 0.2 sn)` → sarmalayici Initialize → **`AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` (0x88890003) retry** (GetBufferSize slot 0x20 + yeniden Initialize — Microsoft'un exclusive hizalama kanonu) → `GetService(IID_IAudioRenderClient)` (slot 0x70, PTR @0x668330; literal `L"IAudioRenderClient Service"`) |
| [2] | FUN_00609c60 | Stop (ortak) |
| [3] | FUN_00609af0 | ortak |
| [4] | FUN_00609f10 | Render varyanti |
| [5] | FUN_00609ff0 | Render varyanti |
| [6] | FUN_0060ac40 | Ham Initialize(shareMode=1, flags=0) — EVENTCALLBACK YOK |

Ortak is akisi: `Open` → (thread `Execute`: `Sleep(10)` + `DoUpdateBuffers`
bos → is istemci thread'inde) → istemci Init → `Start` → dongu:
`[Wait: event INFINITE | shared: periyodik] → GetBuffer → GetData (ring
buffer'dan frames*blockalign kopya) → ReleaseBuffer`. Log kategorisi `"ASO"`
(DAT_0060b4e8/b828/b368 = UTF-16 `"ASO"` inline).

### 11.5 Sabitler (Ghidra bellek okumasi)

- `DAT_0060a788` = **float 0.2** (0x3E4CCCCD) — `SecondsToRefTime(0.2)` = min
  buffer suresi 200 ms; akis: `max(2*period, period, 0.2s)`.
- `DAT_0060a790` = **double 250.0** — `RefTimeToSeconds(buf) * 250.0` →
  istemci+0x28 alanina yazilir (0.2s*250 = 50 → 4 ms'lik chunk sayisi
  imasi; ⚠️ VERIFICATION REQUIRED — chunk anlami govde-disi).

### 11.6 Tur16 string iddialarinin son durumu (hepsi hex/Index dogrulamali)

REFUTED: `TASODeviceBuffer`/`TASODeviceBufferItem`, `TAclEnumerator`,
`TASOSettingsDevicelistHelper` (Shared.dll'de byte-aralikli tarama ile YOK);
`TWASAPDataStream` → gercek: `TASODataStream` @0x15741E (WASAPI ile ilgisiz,
ASO.DataStream). Duzeltildi: `TWASAP.*` → `TWASAPI*`, `TASOWASAP.*` →
`TASOWASAPI*` (noktasiz birlesme). Encoding: ASCII ShortString (§3.1).

### 11.7 Neva sonuclari (ADR-0105 kapi 5.6 icin)

1. **Exclusive = event-driven TEYITLI**: SetEventHandle + TACLEvent +
   INFINITE wait + flags 0x40000. Neva WASAPI exclusive yolu bu kalibi izler.
2. **Shared = periyodik**: GetDevicePeriod + 2x period + 200 ms taban;
   hizalama retry (0x88890003) exclusive kanonuyla uyumlu.
3. **IAudioClient3 = AIMP'de YOK** (REFUTED, §8 madde 2) → Neva icin
   dusuk-gecikme IAudioClient3 yolu AIMP'den farkli/ek bir karardir (ADR).
4. Render dongusu `GetData` = cihaz ring buffer'indan kopya; AIMP'de ek event
   Wait slot'u istemci sinfinda — Neva Engine'deki lock-free ring buffer
   tasarimiyla (ADR-017) dogrudan karsilastirilabilir.

## 12. Oynatma boru hatti (tur18 — SourceStream / Transmitter / Mixer)

Yontem: proc-index sembol taramasi (`Aso::Player::Stream`, `Aso::Mixer`,
`Aso::Data`) + dump'dan 10 govde cikarimi (`player-blocks.txt`) + Ghidra
`ReadPlayerConsts.java` (exit=0) ile sabit okuma. Adresler `aimp-shared-
proc-index.txt` ile teyitli.

### 12.1 Zincir (pull model — cihaz thread'i asagidan ceker)

```
Device render thread (bolum 3/11 — TASODeviceClient*)
  -> TASOMixer::GetData(buf, frames)                  [0x5A2470]
       CS(this+0x38); dongu: transmitter (this+0x78) bos
       (ornek sayisi +0x2C == 0) ise TASODataTransmitter::GetData
       **3 kez** denenir (underrun koruma / dolum denemesi)
       -> Visualizer tap: TASOVisualizer::Mixed (this+0xA0, varsa)
       -> vtable DSP slotlari (bolum 12.2)
       -> TASOSamples::Remove(transmitter, n)         [0x53C030]
  -> TASODataTransmitter::GetData(TASOSamples*)       [0x53C3D0]
       CS(this+0x38) -> kaynak(this+0x40)->vtable[0x30] GetData
       (kaynak = DSP manager / SourceStream akisi / decoder cikisi)
```

### 12.2 Mixer DSP slot mimarisi (Neva icin dogrudan kalip)

`TASOMixer::GetData` ornek boyutlu dongu icinde sample buffer uzerinde
sira ile calisan `(ctx, buf, channels, frames)` imzali slotlar:

| Slot (this+) | Null-check | Rol (INFERENCE, isim ailesinden) |
|---|---|---|
| +0xE0 / +0xE8 | YOK (zorunlu) | Volume proc — `SafeUpdateVolumeProc` @0x5A28A0 |
| +0xB8 / +0xC0 | var | Opsiyonel DSP 1 — `SafeUpdatePreprocessors` @0x5A2920 ailesi (anticlipping/dither) |
| +0xC8 / +0xD0 | var | Opsiyonel DSP 2 (loudness/EQ bilesimi — ⚠️ VERIFICATION REQUIRED, govde-disi) |
| +0xD8 | YOK (cikis) | Cikis asamasi — hedef buffer'a kopya/mix; bayt = frames * blocksize(this+0x19) |

Slotlar `SafeFlush` @0x5A2740 / `SafeFormatChanged` @0x5A27A0 /
`SafeUpdateSourceFormat` @0x5A29F0 ile yonetilir — Neva Engine icin ayni
desende swap-able DSP stage slotlari (ADR-017 lock-free hedef uyumlu).

### 12.3 TASODataTransmitter (thread-safe kaynak passthrough)

- `GetData @0x53C3D0`: CS(this+0x38) -> kaynak(this+0x40)->vtable[0x30].
- `SetSource @0x53C530`: double-check idiom (disarda + kilit icinde tekrar)
  -> this[8] = kaynak -> vtable[0x40] bildirim (kaynak degisti: flush/notify).
- `TASOMixer` BIR transmitter'dir: ctor @0x5A2330 default ctor ->
  `DataSource::SetFormat` -> vtable[0x48] SetSource — mixer, kaynagi
  sarmalayan katmandir, ayri bir miks cekirdegi degil.

### 12.4 TASOSourceStreamList (crossfade / gapless kuyruk)

- `MainStream @0x5B2930`: Lock -> liste bos ise null, degilse **GetLast**
  (AKTIF stream = listenin SON ogesi) -> Unlock.
- `Prebuffer @0x5B29B0`: Lock -> tum ogeler uzerinde vtable[0x20]->[0x38]
  bool sonuclarinin OR'u = **herhangi bir stream hala prebuffer'da mi**
  (gapless baslama kapisi) -> Unlock.
- Crossfade mekaniği: yeni stream sona eklenir; eski stream'ler fade +
  drain sonunda listeden dusulur (MainStream=last kurali).

### 12.5 TASOSourceStream (per-stream fade + drain)

- `Fade @0x5B2340`: clamp = `Min(time, Duration - Position)` — fade suresi
  **track sonuna sigdirilir**; hedef `TASOVolume::Fade(Efdid) @0x53A450`
  (vol, clamped, sampleRate@stream+0x25, delay) — stream'in TASOVolume'u
  this+0x20.
- `Finished @0x5B2460`: drain predicate = (terminateFlag@+0x38 || volume
  bitti) && buffer bos (+0x28->+0x2C==0) && !HasData -> bitis dogrulamasi.
- `TerminateAfterDelay @0x5B24F0`: `Max(DAT_005b2540, delay - fade)` ->
  `Fade(DAT_005b2540, delay, clamped)` -> terminateFlag@+0x38 = 1
  (fade-to-silence + gecikmeli son).

### 12.6 Teyitli sabitler (ReadPlayerConsts.java, exit=0)

- `_DAT_005b1a68` = **double 1000.0** (raw 0x408F400000000000) —
  `GetActualCrossMixingTime @0x5B1A20`: cross-mix suresi (ms, short @
  fading+0x3A) / 1000 = **saniye**; sart zinciri: options+0x50 &&
  fading+0x3C && fading+0x38 (§8 madde 5'ten ms→saniye kismi kapandi).
- `DAT_005b2540` = **0.0** (raw 0x0000000000000000; float 0.0 + double 0.0
  ayni 8 bayt) — TerminateAfterDelay fade hedefi = sifir (silinmeye fade).

### 12.7 Neva sonuclari (tur18)

1. **Pull-model + slot DSP** (12.2): cihaz thread'i mixer'den ceker; DSP
   asamalari swap-able (ctx,fn) slotlaridir — volume zorunlu + 2 opsiyonel
   slot. Neva Engine ayni kalibi lock-free uygulayabilir (ADR-017).
2. **Crossfade = liste + per-stream volume fade** (12.4/12.5): gapless icin
   "son og = aktif", prebuffer OR-kapisi, drain predicate — Neva crossfade
   tasarimi icin dogrudan referans (ADR-0105 kapi 5.12).
3. Fade clamp (track sonuna sigdirma) ve fade-to-silence (0.0 hedef)
   AIMP'in edge davranisidir — Neva'da ayni kalip veya bilincli fark
   tercih edilebilir (ADR'de karar).
4. Underrun korumada 3 kesintisiz dolum denemesi (12.1) — Neva ring buffer
   tasariminda karsiligi: boslukta tekrarli pull, yoksa break.
5. ~~KONTROL katmani (Load/Play/SetDeviceParams, tempo-pitch) hala acik~~
   — **KAPANDI (tur19, bolum 13).**

## 13. Kontrol katmani — TASOPlayer (tur19-20)

Yontem: proc-index (`_ZN3Aso6Player10TASOPlayer` = **101** isimli prosedur;
`Aso::Player` namespace toplami **438**) + 25 govde cikarimi
(player-ctrl-blocks.txt / blocks2.txt) + Ghidra `ReadPlayerConsts2.java` +
`ReadVolConst.java` (exit=0). Tum adresler index ile teyitli.

### 13.0 Tam zincir (AfterConstruction @0x5DEF10 — tur18'in bosluklari doldu)

```
TASOStream(+0xE8) --SetSource--> TASOSpeedControlFilter(+0xE0)   [tempo/pitch]
             --SetSource--> TASODSPManager(+0x48)               [custom DSP/EQ]
             --SetSource--> TASOMixer(+0x90)                    [ses/balance, bolum 12]
             --SetSource--> TASODevice(+0x20)                   [WASAPI/ASIO, bolum 3/11]
```

Her halka `vtable[0x48] SetSource` ile baglanir (tur18'de adlari bilinmeyen
"source" hoplarinin gercek siniflari: **Stream -> SpeedControl ->
DSPManager -> Mixer**). Ayrica AfterConstruction: Mixer.SetVolume(0.0)
(DAT_005def90 = inline 0x00000000 — **baslangicta sessiz, ApplySettings
kayitli sesi uygular**; INFERENCE) + Mixer.SetVisualizer(+0x100).

### 13.1 Uye haritasi (ctor @0x5DE8E0, this+X)

| Offset | Uye | Not |
|---|---|---|
| +0x20 | TASODevice* | DeviceInitialize'da kurulur |
| +0x28 | TACLTimer 250 ms (0xFA) | closure = DeviceReinitialize |
| +0x30/+0x70/+0x88 | CS "DeviceLock"/"LoaderLock"/"PlayerLock" | |
| +0x38..0x43 | TASODeviceParams kaydi | +0x39 cihaz adi "Windows Default", +0x41 format=5, +0x42 rate=44100 |
| +0x48 | TASODSPManager | cb DoFilterChanged |
| +0x50 | TASOErrorLog | |
| +0x58/+0x60 | TACLEventener(0x957) + gizli mesaj penceresi | WndProc/WndProcDevice/WndProcSync/WndProcEvent |
| +0x68 | durum bit bayti | bit0=guard(play/pause/stop), bit1=seek guard, bit2=next-file guard, bit3=reinit guard |
| +0x78 | TASOPlayerLoader (lazy, async thread) | |
| +0x90 | TASOMixer | bolum 12 zincirinin sahibi |
| +0x98 | TASOPartRepeatInfo | |
| +0xA0 | TASOPlayerPersistent/PauseBetweenTracksHelper | |
| +0xB0 | TASOPlayerPreloadedTrack | gapless preload onbelleği |
| +0xB8 | onbellegi alinmis durum | UpdateState change-detect |
| +0xC0 | TASORadioRecorder | cb DoRipperStateChanged |
| +0xC8 | TASOPlayerReconnectHelper | |
| +0xD0 | yon byte'i (0=ileri, 1=geri) | |
| +0xD8 | TASOPlayerSettings | cb DoSettingsChanged |
| +0xE0 | TASOSpeedControlFilter | tempo/pitch; cb DoFilterChanged |
| +0xE8 | TASOStream (buffering yoneticisi) | +0x70 params, +0x78 options, +0xa8 custom trigger |
| +0xF0 | TASOPlayerStreamInfo | |
| +0x108 | TASOPlayerVolumeSettings | |
| +0x1A0/1A8 | pozisyon degisim bildirim callback'i | |
| +0x1F0 | durum degisim bildirim event'i | CallNotifyEvent |
| +0x280 | TASODeviceManager.ListenerAdd | cihaz tak/cock kaydi |

### 13.2 Durumlar ve yukleme yolu (async)

Durumlar (GetState @0x5E2940 / GetRealState @0x5E2900): **0=durdu,
1=duraklatildi, 2=oynatiliyor** (GetState: !device.IsPaused -> 2;
GetRealState: !device.IsPlaying -> 1; ikisi de HasAudioStream degilse 0 —
HasAudioStream = device acik && Stream bos degil). `InternalRestoreState
@0x5E2700`: state 1 -> InternalPause(false), 2 -> InternalPlay(false).

Yukleme: `Load(ucstring,...) @0x5DF050` -> TASOPlayerLoadingParams (state,
yon, start-pos, on-tanimli bilgi; state==0 -> BreakLoading) ->
`Load(params) @0x5E09B0`: LoaderLock + BreakLoading + lazy
TASOPlayerLoader + SetParams + vtable[0x28] Start -> "Player.LoaderStarted".
**Decode/yukleme asla UI thread'inde calismaz.**
`OpenStream @0x5DFB10` (loader thread): DeviceLock -> lazy DeviceOpen ->
"Player.Stream.Add" -> **immediate = (durum==DURAKLATILDI || hedef
state==DURAKLATILDI)** -> Stream.LockBuffering -> `AddStream(params,
immediate)`; immediate=false -> **gapless kuyruga ekle (crossfade),
true -> aninda gecis**; immediate ise FlushPauseTimestamp + mixer.Flush +
InternalRestoreState -> "Player.Stream.Ready".

### 13.3 Cihaz yasam dongusu (hot-swap kalibi)

- `DeviceOpen @0x5E13B0`: mevcut cihaz hazir ise (vtable[0x70]) koru;
  degilse PopulateDevices -> FindDeviceForPlayback(+0x39 ad) ->
  DeviceInitialize -> **listeden Extract = sahiplik transferi** ->
  TASODevice::Open(rate@+0x42, format@+0x41, channelsMode@+0x38); hata:
  "Device not found".
- `DeviceInitialize @0x5E18D0`: Finalize(eski) -> device@+0x20 = dev;
  dev+0x60/68 = DeviceError, dev+0x70/78 = DeviceNotify geri cagrilari;
  **SetSource(mixer@+0x90)** — bolum 12 zinciri burada baglanir.
- `DeviceFinalize @0x5E1740`: atomik swap (dev=null) -> "Device.Close" ->
  Close -> geri cagrilari sifirla -> SetSource(0) -> Free.
- `DeviceReinitialize @0x5E1A20`: timer kapat; bit3 guard; durum/pos/
  partRepeat kaydet; LockBuffering; Finalize; Open; **basarili ise
  SetPosition(kayitli pos) + InternalRestoreState + partRepeat geri
  yukleme**; basarisiz ise "Failed to reinit device" + Stop(true).
  -> cihaz degisimi/kaybi sirasinda **konum korunur**.
- `SetDeviceParams @0x5E29A0`: DeviceLock -> DeviceCanChange -> CopyRecord
  (+0x38 kayit, RTTI: TASODeviceParams) -> DeviceReinitialize.

### 13.4 Transport (Play/Pause/Stop/Seek)

- `InternalPlay @0x5E2340`: bit0 guard; GetRealState==1 gerekli; radio/
  intf check (Supports GUID -> Flush + Reconnect); **uzun-pause geri
  sarma: ayar biti acik ise SetPosition(pos - 10.0)** (_DAT_005e2528 =
  **double 10.0**, teyitli) + mixer.Flush; ses = VolumeSettings.
  ConvertVolume(+0x108); fade = IfThen(param2, GetFadingOnPlayPauseTime,
  0) -> TASODevice::Play(vol, fade).
- `InternalPause @0x5E2200`: durum==2; SetPauseTimestamp; realtime (radyo)
  stream'lerde ozel intf cagrisi; Device::Pause(fade).
- `InternalStop @0x5E2650`: HasAudioStream; FlushPauseTimestamp;
  Device::Stop(fade).
- `SetPosition @0x5E2C10`: bit1 guard; EnsureRange(pos, **0.0**, Duration)
  (DAT_005e2d68 = float 0.0, teyitli); PartRepeat kontrolu; oynatiliyor &&
  ayar biti -> **SetSmoothSeeking(true)** (stream options+0x78);
  Stream.SetPosition; oynatilmiyor ise mixer.Flush; pozisyon bildirim
  callback (+0x1A0).

### 13.5 Gapless sonraki-parca zinciri (uc katman)

- `PreloadNextFile @0x5E00D0`: DoGetNextFile(**dir=2 = ileri**); uzak dosya
  -> preload yok; stream'te zaten varsa (IsNextFilePart) atla;
  PreloadedTrack.Match -> degilse Preload(next) — **on-acma**.
- `StartNextFilePlayback @0x5E0240`: bit2 guard; DoGetNextFile(yon@+0xD0)
  -> uygulama ogesi secimi (DoSelectNextPotentialPlayingItem); bulunan
  dosya icin FileManager.GetVirtualFile -> **TASOStream::SetNextFilePart
  (gapless sonraki-parca enjeksiyonu)** -> DoFileLoading/DoFileLoaded
  olaylari; offset > **0.0** (_DAT_005e0408 = 0.0, teyitli) ise
  SetPosition; pause-after-track ise Pause; SetNextFilePart basarisiz ise
  Load yoluna dus (state tablosu DAT_006605a3 = [**2,1**]: pauseAfter
  false -> state 2 oynat, true -> state 1 duraklat).
- `CheckStreamFinished @0x5DFE30`: device var && Stream.IsEmpty &&
  !LoaderActive (loader+0x70 != 0, LoaderLock) -> custom trigger ->
  WndProcEvent; **ara-duraklama (PauseBetweenTracks) aktifse
  StartWaitTimer, degilse (trigger yoksa) TASODevice::
  WaitForDataEndAndStop** — drain-then-stop.
- Yon tablosu DAT_006605a1 = [**2,3**] (yon byte 0=ileri->2, 1=geri->3;
  ileri atfi PreloadNextFile'nin sabit 2 kullanimi ile INFERENCE).

### 13.6 Tempo/pitch

`SetPlaybackTempo @0x5E2BD0` / `SetPlaybackPitch @0x5E2BB0` = ince
passthrough -> `TASOSpeedControlFilter(+0xE0).SetTempo/SetPitch`
(index'de: TempoIncrease/Decrease @0x5CDB70/0x5CDD30, SetPitch/SetTempo
@0x5CE070/0x5CE110). Filtre zincirde 2. halka (13.0); degisiklik
DoFilterChanged -> ApplySettings.

### 13.7 Neva sonuclari (tur19)

1. **Tam boru hatti adlandirildi** (13.0): Neva Engine zinciri icin tek
   referans diyagram — Stream -> SpeedControl -> DSP -> Mixer -> Device,
   her halka SetSource ile (ADR-0105 kapi 5.9/5.12 icin dogrudan girdi).
2. **Async yukleme + gizli pencere event marshalling** (13.2): decode loader
   thread'inde; olaylar WndProc ile tasinir — Neva icin ayni kalip.
3. **Hot-swap + konum koruma** (13.3): device reinit sirasinda pozisyon/
   durum/partRepeat kaydedilip geri yuklenir — Neva cihaz degisimi kalibi.
4. **Gapless uc katman** (13.5): preload + enjeksiyon + drain-then-stop.
5. **UX detaylari (teyitli)**: uzun-pause'da 10 sn geri sarma; play/pause/
   stop fade (GetFadingOnPlayPauseTime); smooth seeking; baslangic sesi
   0.0 + ApplySettings — Neva'da ayni veya farkli tercih ADR konusu.
6. ~~Kalan acik (KISMEN): loader thread ic yapisi, WndProcEvent dispatch
   tablosu, GetStateForTrackStart (0x5DEFA0)~~ — **KAPANDI (tur20,
   bolum 13.8):** loader mesaisi, WndProc mesaj tablosu, WndProcEvent
   olay kodlari, params offset haritasi cekildi; BASS decoder katmani
   kesfedildi.

### 13.8 Loader thread mesaisi + WndProc dispatch (tur20 eki)

Yontem: 17 loader proseduru + WndProc/GetStateForTrackStart blok cikarimi
(player-ctrl-blocks3.txt 13 blok + blocks5.txt 4 blok) + sembol taramasi
(TASOPlayerLoader = 17, WndProc* = 3). Tum adresler index ile teyitli.

**Loader sinif zinciri (LoaderCtor @0x5DB710):**
`TACLPauseableThread` taban (Acl::Threading) + back-ref'ler: +0x78 =
TASOPlayer*, +0x70 = TASOPlayerLoadingParams, +0x68 = TACLSortedStrings
(skip listesi). Task API'leri TASOMultipleTaskThread'den gelir
(GetAborting cagrilari; zincir TACLPauseableThread <-
TASOMultipleTaskThread <- TASOPlayerLoader — INFERENCE: ctor +
GetAborting kanitlarindan).

**TASOPlayerLoadingParams offset haritasi (tamamlandi):**

| Offset | Alan | Kanit |
|---|---|---|
| +0x20 | dosya adi (UnicodeString) | WndProcSync DoFileLoading |
| +0x28 | start offset (double) | PrepareNextFile (obj+0x18 -> float) |
| +0x30 | on-tanimli bilgi (intf) | PrepareNextFile |
| +0x40 | olusturulmus decoder/stream intf | LoadFile IntfCopy hedefi |
| +0x60 | preload flag (char) | ExecuteTask dispatch + GetStateForTrackStart |
| +0x61 | hedef state (char) | GetStateForTrackStart |

**ExecuteTask @0x5DBA30 (dispatcher):** params(0x70) varsa:
NotifyLoadingState(2=LOADING) -> params+0x40==0 iken (params+0x60==0 ?
ExecuteTaskFileLoad : ExecuteTaskFilePreload), !=0 iken
ExecuteTaskReconnect -> NotifyLoadingState(0=IDLE) -> SetParams(0)
(params temizle + skip listesi Clear).

**ExecuteTaskFileLoad @0x5DBB90:** dongu: NotifyFileLoading -> LoadFile
-> NotifyFileLoaded (**0x95a mesaji**, main-thread senkron) -> abort
kontrolu -> basarisizsa dosyayi skip listesine ekle + PrepareNextFile
(sonuksuz dongu korumasi) -> devam; basarili decode -> RunInMainThread
(SyncOpenStream). **Decode + dosya secimi loader thread'inde; stream
acilisi main thread'ine marshal edilir.**

**ExecuteTaskFilePreload @0x5DBAF0:** LoadFile -> params+0x40'a IntfCopy
(reconnect icin hazir kaynak) -> NotifyFileLoaded -> RunInMainThread
(SyncPreloadStream).

**ExecuteTaskReconnect @0x5DBCD0:** params+0x40 -> GetActualLink ->
TASORemoteStream::Connect(link, NotifyBufferingExi) -> basarili:
ReconnectHelper.Reset (+0xC8 teyitli); basarisiz: StartWaiting ->
false ise TBASSDecoder::SetFinished.

**LoadFile @0x5DBFC0:** PreloadedTrack(+0xb0).Match -> hazir decoder
(gapless on-acma); degilse SoundOutDecoderManager().CreateDecoderForParams
(hata kodlari: -0x7FFFBFFC, -0x7FFFFFF6). URL dosyalarda
(acIsUrlFileName) GetActualLink -> hala URL ise **TASORemoteStream
olustur + Connect(link, ...)** — internet radyo akisi loader
thread'inde dogar (ADR-0106 radyo kenari icin dogrudan girdi).

**SyncOpenStream @0x5DBE10 (main thread):** TASOPlayer.OpenStream
(params); basarisiz: "Player.LoadFailed(%s)" + error dump +
InternalStop(false) + DoError.

**PrepareNextFile @0x5DC480:** skip listesi guard (Contains(nextfile) ->
false — sonsuz dongu korumasi) -> main-thread closure (DoGetNextFile)
-> bulunan dosya: SetFileName + params+0x30 = next info + params+0x28 =
offset. **DoGetNextFile loader thread'inden main thread'ine RunInMainThread
ile marshal edilir (loader beklemede kalir).**

**SyncPreloadStream @0x5DBF60:** PreloadedTrack(+0xb0).Initialize(params)
— preload decoder'i onbelleğe alir.

**WndProc @0x5E04A0 mesaj tablosu:**

| Mesaj | Handler | Icerik |
|---|---|---|
| 0x956 | WndProcDevice @0x5E05D0 | 1=TACLTimer.Restart (timer +0x28), 2=UpdateState, 3=status bit guard (+0x20 bayt & 8, LoaderLock) -> DeviceStopped |
| 0x957 | Eventener.Reset -> WndProcEvent | TACLEventener tetik kanali (ctor'daki 0x957 ile ayni) |
| 0x95a | WndProcSync @0x5E0690 | wParam 1=DoFileLoading(+0x20 dosya, +0x30 intf), 2=DoFileLoaded(+0x20,+0x30,+0x40), 3=DoLoadingState(state | flags<<8); **guard: player+0x78 !=0 && loader+0x70 == lParam — stale mesajlar drop edilir** |
| 0x95e | player+0x170 callback | harici callback slot (obj=+0x178, lParam) |
| default | DefWindowProc | |

**WndProcEvent @0x5E0710 olay kodlari (Eventener tetikleri):**

| Kod | Aksiyon |
|---|---|
| 1 | player+0x18==1 -> DoSelectNextPotentialPlayingItem; degilse HasPauseBetweenTrack/PauseAfterTheTrack kontrolu -> Reset(5) + StartNextFilePlayback(0) veya pause isareti (+0xA0+0x18=1); sonra SetActionOnTrackEnd(0) — **gapless sonraki-parca tetikleyicisi** |
| 2 | HasAudioStream && PartRepeatState==2 -> Stream.SetPosition((double)float +0x98) + CustomTrigger.SetActive — A-B repeat geri sarma |
| 3 | settings+0x91 -> PreloadNextFile — **gapless preload tetikleyicisi** |
| 4 | StreamChanged |
| 5 | StreamInfo.Update + CheckStreamFinished + StreamChanged |
| 6 | StreamInfo.Update (yalnizca) |

**GetStateForTrackStart @0x5DEFA0:** LoaderLock: loader(0x78) yok /
params yok / params+0x60 (preload) -> PauseBetweenTracksHelper aktifse
state=1, degilse GetRealState; params varsa **params+0x61 (hedef state)
baskin** — yukleme sirasinda "nereye gidilecek" hedefi gecerli durumu
ezer (preload istisna: hedef state uygulanmaz).

**BASS decoder katmani (yeni kesif):** `Basstypes` namespace'inde **170**
isimli BASS sembolu (TBASSDecoder vtable @0x51CA98, TBASSDecoderC3,
BASSUserStreamingCreate(IAIMPStream), TBASSUserData.Wrap, ...). 
~~Dump'ta b-string DLL literal adi YOK (dolayli yukleme)~~ **REFUTED (tur21):**
bass.dll = **statik import** (AIMP.Shared.dll import descriptor [0], 27 fonksiyon,
delay import = 0) — dump'ta literal gorunmemesi Ghidra artefaktidir (pseudocode
sembol adini gosterir, PE baytini degil) — bolum 14.1 (iki-katman nuansi dahil:
cekirdek statik, codec-plugin giris noktalari acGetProcAddress ile dinamik).
Neva sonucu: AIMP decoder katmaninin bir kismi BASS tabanli — Decoder turu
(185 prosedur) bolum 14'te tam eslesti.

## 14. Decoder katmani (tur21 — factory + TBASSDecoder + bass.dll statik import)

> Kapsam: AIMP.Shared.dll decoder olusturma zinciri (factory), BASS tabanli
> decoder instance (TBASSDecoder / TBASSLibrary), PE import gercegi (bass.dll
> **statik**) ve v1.4.0'daki "dolayli yukleme" inferansinin REFUTED edilmesi.
> Kaynaklar: proc-index (14.817 proc) + aimp-shared-full-decompile.txt
> (dump satir referanslari parantez icinde) + PE bayt okumasi
> (parse-imports.ps1: import directory `oh+112+8`, nameRVA -> section
> eslemesi R2F ile file offset'e cevrildi).

### 14.1 PE import gercegi: bass.dll STATIK (v1.4.0 notu REFUTED)

`AIMP.Shared.dll` (4.641.728 bayt) import directory'si PE baytlarindan parse
edildi: **bass.dll = import descriptor [0]** (ilk descriptor):

| Alan | Deger |
|---|---|
| nameRVA / file offset | 0x278ef0 / 0x2762f0 (`.idata`) |
| ILT (lookup table) | 0x26a0e0 |
| IAT | 0x2717e8 |
| Fonksiyon sayisi | **27** |

27 import (olcum sirasi): `BASS_StreamCreate` · `BASS_StreamCreateURL` ·
`BASS_SetConfig` · `BASS_PluginFree` · `BASS_ChannelGetTags` · `BASS_MusicFree`
· `BASS_StreamGetFilePosition` · `BASS_ChannelSetPosition` ·
`BASS_ChannelGetPosition` · `BASS_Init` · `BASS_StreamCreateFile` ·
`BASS_PluginGetInfo` · `BASS_MusicLoad` · `BASS_PluginLoad` ·
`BASS_ChannelSetDSP` · `BASS_StreamFree` · `BASS_GetConfigPtr` ·
`BASS_SetConfigPtr` · `BASS_ChannelGetInfo` · `BASS_ChannelSetSync` ·
`BASS_ChannelGetData` · `BASS_ChannelGetLength` · `BASS_ChannelSetAttribute` ·
`BASS_ChannelGetAttribute` · `BASS_ChannelBytes2Seconds` ·
`BASS_ErrorGetCode` · `BASS_StreamCreateFileUser`

Toplam **10 import descriptor** (bass.dll + winmm + kernel32 + shell32 +
ole32 + ... + `dsound.dll` [#9]); **delay import directory = 0** (tamamini
statik import baglar).

**REFUTED — v1.4.0 "dolayli yukleme" notu (doc satir 333 ve 808):** "Dump'ta
b-string DLL literal adi YOK -> dinamik import/dolayli yukleme" inferansi
gecersizdir ve karsit kanitta oturmaktadir. Nedeni metodolojik: Ghidra dump'i
pseudocode'tur ve statik import'lari **sembol adiyla** gosterir (ham bayt
olarak degil); "bass" literal'inin dump metninde aranmasi yanlis bir deneydir.
Dogru yontem PE import directory parse'idir ve sonuc **statik import**'tur.
Bu, §2'deki tur15 MUTABAKAT notu ile (Shared dump'inda
`BASS_SetConfig @0x51ac60` · `BASS_Init @0x51aca0` · `BASS_PluginLoad @0x51acb0`
import thunk'lari) zaten mutabikti; tur21 bunu bayt seviyesinde kilitledi.
AIMP.exe seviyesinde bass.dll import'unun olmamasi (§2) ayri ve gecerli bir
gozlemdir — bagimlilik AIMP.Shared.dll'e devredilmistir.

**Iki-katmanli nuans (tur21):** "dolayli yukleme" iddiasi **sinirli kapsamda
dogru cikti — ama hedef bass.dll degil, opsiyonel codec-plugin giris
noktalari.** `acGetProcAddress` uzerinden dinamik baglanan fonksiyonlar
(grep-dump8, dump satir 196440-196520): `BASS_ALAC_StreamCreateFile` ·
`BASS_MP4_StreamCreateFile` (`TAIMPBASSM4APlugin` ctor @0x52D230, dtor
@0x52D090, wrapper @0x52D300) · `BASS_WV_StreamCreateFileUserEx`
(`TAIMPBASSWavPackPlugin` @0x52D810). Yani: **bass.dll cekirdegi = statik
import (27), ALAC/M4A/WV codec entry-point'leri = plugin yukleme aninda
GetProcAddress.** Bu iki katman ayristirilmadan "dolayli yukleme" denirse
yanlis olur: bass.dll yuklenmesi statik baglantidir (IAT dolumu loader
tarafindan), codec sembolleri ise run-time cozumlenur.

### 14.2 Factory katmani: SoundOutDecoderManager (Aso::Decoders)

Adresler proc-index ile dogrulandi (`_ZN3Aso8Decoders` = **23** proc):

| Uye | Adres | Kanit |
|---|---|---|
| GetStreamFormat | @0x54C7D0 | `PTR__ZN3Aso5Types18SoundOutNullFormatE_006686d8` (fallback format) |
| SoundOutDecoderManager (singleton) | @0x54C840 | `DAT_006694c8` tekil instance |
| CreateDecoder | @0x54CB80 | CreateDecoderForParams cagirici (satir 215669) |
| CreateDecoderForParams | @0x54CBD0 | `TASOSourceParams::ResetErrorLog` -> `TAIMPErrorInfoAdapter` -> `FileManager::CreateStreamForFileURI` -> `IsFileExists` (satir 215680-215845) |
| CreateDecoderForFileURI | @0x54D140 | CreateDecoderForParams cagirici (satir 215923) |
| CreateDecoderForStream | @0x54D4C0 | ext listesi `param_1+0x10` + `CRITICAL_SECTION` `+0x20` (satir 216091); factory icinden cagri (satir 215758) |
| RegisterExtension | @0x54D6C0 | uzanti kayit |
| UnregisterExtension | @0x54D800 | uzanti kaldirma |
| CanProcess | @0x54D940 | `IAIMPExtensionAudioDecoderOld` + `UnicodeString` |

**Fabrika akisi (ikili faz — dogrulanmis satirlar):**

1. **Giris (CreateDecoderForFileURI, satir 215917-215928):**
   `TACLCriticalSection::Enter(manager+0x20)` -> `TASOSourceParams` ctor
   (vtable `0054ad90`; `+0x2c = (flags & 0x1000) != 0`) -> `SetFileName` ->
   `CreateDecoderForParams` (satir 215923-215924) -> params `Free` -> `Leave`.
2. **Cozum (CreateDecoderForParams):** `ResetErrorLog` -> `TAIMPErrorInfoAdapter`
   -> `FileManager::CreateStreamForFileURI` -> `IsFileExists` (basarisizsa
   `SetInfo(0xffffffff, L"File was not found.")`) -> `CreateDecoderForStream`
   (satir 215758-215759).
3. **Extension fallback (satir 215761-215762):** `Failed(hr)` **ve** uzanti
   sayaci > 0 ise (konteyner `*(manager+0x18)`, ic sayac `+0x20`, array
   `+0x28`) uzanti listesi **ters iterasyon** ile gezinilir:
   `CanProcess(ext, ...)` -> `ext vtable+0x18` (CreateDecoder) ->
   `Succeeded(hr)` **veya** `hr == E_PENDING (0x8000000A)` ise kabul.
   Hicbiri tutmazsa `SetInfo(0xffffffff, L"Decoder was not found for this
   format.")`. Boylece dosya-varligi ile format-destegi ayri ayri raporlanir.

Ayrica `CheckBufferForReading/Writing/SafeGetValue` (@0x14170 / @0x141b0 /
@0x141f0) null/bad-pointer durumunda hata kodu dondurur (bkz. 14.6).

### 14.3 TBASSDecoder (decoder instance — Basstypes, 30 proc)

| Uye | Adres | Davranis |
|---|---|---|
| ctor | @0x51D150 | `+0x1c` = BASS handle, `+0x18` = flag; `BASS_ChannelSetAttribute(handle, 0x13001, DAT_0051d218)` + `CheckDuration` |
| BASSUserStreamingCreate | @0x51CFE0 | `BASS_StreamCreateFileUser` |
| TBASSLibrary::EnsureInitialized | @0x51D7C0 | `BASS_Init(0, 0xac44, 0, 0)` + SetConfig zinciri |
| CheckForError | @0x51D570 | `BASS_ErrorGetCode` + `IAIMPErrorInfo` uzerinden kod cevirimi |
| GetStreamInfo | @0x51D410 | `BASS_ChannelGetInfo`; `flags &1 -> 1`, `&0x100 -> 5`, else `2` |
| IsRealTimeStream | @0x51D480 | **sabit 0 (`return false`)** |
| IsSeekable | @0x51D490 | `+0x1a` bayt 0 ise `-1` (True) |
| Read | @0x51D4A0 | `BASS_ChannelGetData`; hata -> `SetFinished` + 0 |
| SetPositionEx | @0x51D4F0 | flag `0x40000000` ise `IfThen`; `BASS_ChannelSetPosition`; basari -> `finished = false` |
| GetPosition | @0x51D3B0 | `BASS_ChannelGetPosition` |
| GetSize | @0x51D3D0 | `BASS_ChannelGetLength` |
| GetAvailableData | @0x51D2E0 | `GetLength - GetPosition` (uzunluk 0 -> 0) |
| CheckDuration | @0x51D280 | sure kontrolu |

**`DAT_0051d218` = 0** (PE: 4 bayt `00 00 00 00`, file offset 0x11c618) ->
ctor'da `BASS_ChannelSetAttribute(ch, 0x13001, 0.0)`. **`0x13001` attribute
adinin ne oldugu dogrulanamadi** -> VERIFICATION REQUIRED. Ayrica seek
flag'i `0x40000000` ve `GetStreamInfo` flag bit adlari (1 / 5 / 2 eslemesi)
VERIFICATION REQUIRED.

**TBASSLibrary::EnsureInitialized (@0x51D7C0):** `BASS_Init(0, 0xac44 = 44100,
0, 0)` + ardindan SetConfig zinciri (raw id'ler; isim eslemesi VERIFICATION
REQUIRED): `0x10` · `0x28` · `9` · `0x39` · `0xc` · `0x15` · `0xf` · `0x3c` ·
`0x25` · `0xb` · `0x48 = 64000` · `1` · `0x10101` · `0x17` · `0x34` +
`SetConfigPtr`/`GetConfigPtr` cagrilari; hata durumunda
`L"BASS_INIT_ERROR"` mesaji.

### 14.4 BASS yalniz DECODER (playback API = 0 hit)

dump'ta `BASS_ChannelPlay` · `BASS_ChannelStop` · `BASS_ChannelPause` ·
`BASS_ChannelSetVolume` · `BASS_ChannelIsActive` · `BASS_StreamPutData`
-> **0 isabet** (isimli
prosedur de, cagri de yok). Sonuc: AIMP BASS'i **oynatma motoru olarak
kullanmiyor**; BASS yalnizca decode tarafinda calisir — pull-model
`BASS_ChannelGetData` (Read @0x51D4A0, TBASSDecoder uyesi olarak cagriliyor);
BASS'in genisletme yetenekleri 14.1'deki import listesinden okunur:
`StreamCreateURL` (radyo) · `MusicLoad` (tracker) · `ChannelSetDSP` (DSP hook)
· `ChannelGetTags` (import kaniti; cagri noktasi olculmedi). Ses cikisi
tamamen WASAPI'dir (§11-§13). Bu bulgu §11-§13 zinciriyle **bagimsiz Mutabik**:
BASS playback API'nin dump'ta olmemesi, WASAPI'in tek cikis oldugunu dogrular.

**Istisna — encode yolu (tur21):** `Aso::Encoders::Wma::TASOWMAEncoderStream::CreateHandle`
@0x624860 (+ @0x623a90, dump satir 341890/342446) `BASS_WMA_EncodeOpen` /
`BASS_WMA_EncodeOpenFile` cagirir; hedef `DAT_00669970` — yani AIMP, BASS'i
WMA kodlayici olarak da kullanabiliyor (encode = decode'in tersi, PCM -> WMA).
Dikkat: bu iki sembol 27 statik import **arasinda degil** -> cozumleme
mekanizmasi (dolayli yukleme / GetProcAddress olasiligi) VERIFICATION
REQUIRED. Encode Neva kapsaminda ayri bir kapi olarak degerlendirilir
(decode cekirdegi ile karistirilmaz).

### 14.5 RTTI plugin literal'lari (PE ASCII scan — 7 distinct)

`AIMP.Plugins.Input.BASS` · `.BASS.MIDI` · `.BASS.MIDI.SettingsDialog` ·
`.BASS.MP4` · `.BASS.WV` · `.TAK` · `.Wave64` (44 hit) + demangled siniflar:
`Aimp::Plugins::Input::Bass::TASOBASSDecoderExtension` @0x523168 ·
`TASOBASSTrackerMusicDecoderExtension` @0x5234E0 ("DecoderExtension" 38 hit).
Yani decoder plugin kumesi: BASS ailesi (BASS/MIDI/MP4/WV) + TAK + Wave64 —
§6'daki "BASS plugin modeli" tespiti sayisal olarak genislemis oldu.

### 14.6 Sabit olcumleri ve hata kodlari

| Sabit / kod | Deger | Kanit |
|---|---|---|
| `_DAT_0057f078` | **double 1000.0** | dump satir 247448: `SoundOutEqBandFreqString` icinde divisor — freq < 1000 -> `IntToStr`, freq >= 1000 -> `FormatFloat(freq / 1000.0)` + `LAB_0057f0b8` ("kHz" eki). **§8 madde 5 KAPANDI** |
| `DAT_0051d218` | **0** (`0.0f`) | PE file offset 0x11c618 (4 x `00`) |
| `EQ_BANDS` @0x64C070 | 20 double (31.5..22000 Hz) | PE okumasi Ghidra dump ile mutabik (§5) |
| `-0x7ffffff6` | `0x8000000A` = `E_PENDING` | aritmetik turev (COM standart degeri) |
| `-0x7fffbffc` | `0x80004004` = `E_ABORT` | aritmetik turev |
| `-0x7ff8ffa9` | `0x80070057` = `E_INVALIDARG` | aritmetik turev (CheckBuffer*/SafeGetValue) |
| `-0x7fffbffd` | `0x80004003` | aritmetik turev; isim atfi (`E_POINTER`) VERIFICATION REQUIRED |

Hex degerler aritmetik olarak kesindir; isim atflari COM tanimlarindan
turetilmistir (§3 zero-hallucination: turev oldugu isaretlenmistir).

### 14.7 Olcum tablosu (proc-index)

| Terim | Sayi |
|---|---|
| Toplam proc (AIMP.Shared.dll) | 14.817 |
| "Decoder" substring | **185** — §8 madde 4 KAPANDI |
| "Radio" substring | **198** — §8 madde 4 KAPANDI (tur22, bolum 15) |
| `_ZN9Basstypes` | 30 |
| `_ZN3Aso8Decoders` | 23 |

Not: "Aso::Decoders" demangled kalibi indekste **0** — indeks mangled-only'dir;
eski "0" olcumu yanlis kalipti, bu bolumde duzeltildi (doc'a 0 yazilmaz).

### 14.8 Neva karsiliklari

1. **Factory deseni:** `DecoderRegistry` + per-format `CanProcess(magic/ext)`
   + ayri `CreateDecoder` — CreateDecoderForParams/CanProcess zincirinin
   birebir karsiligi; "dosya yok" ve "format desteklenmiyor" hatasi ayri
   tip olarak (typed error) modellenmeli.
2. **Decoder != Output:** BASS yalnizca decode, cikis WASAPI ayriligi,
   ADR-019 `IAudioBackend` katmanlasmayi dogrular — Neva'da decode backend
   (miniaudio/FFmpeg) ile render backend (WASAPI exclusive) ayni arayuzun
   iki implementasyonu degil, iki ayri katmandir.
3. **Idempotent init:** `EnsureInitialized` critical-section + tek seferlik
   `BASS_Init` + SetConfig zinciri — Neva'da `std::call_once` + tek init
   noktasi (RT callback'e init gecmez, §11 kurali ile ayni).
4. **Error-info adapter:** `IAIMPErrorInfo` uzerinden kod+mesaj cevirimi —
   Neva'da `DecoderError{kind, osCode}` (osCode BASS/FFmpeg hata kodu,
   kind = NotFound / UnsupportedFormat / IoError).
5. **Realtime isareti:** `IsRealTimeStream = false` + `IsSeekable` bayti —
   radyo/akis hedefleri icin bayt isareti; **KAPANDI (tur22, bolum 15):**
   `TASORemoteStream` bu slotu override eder (vtable+0x30) ve Connect/Metadata
   (UpdateMetadata) kararlarinda dogrudan kullanilir.

---

## 15. Radio 198 — radyo decode kenari (tur22 — ADR-0106 KAPANDI)

> Kapsam: AIMP.Shared.dll "Radio" substring = **198** prosedur. Yontem:
> proc-index gruplandirma + Ghidra extract bloklari (Connect/metadata/recorder)
> + **bass.h 2.4 (2012, Un4seen; samp-incognito GitHub mirror'indan tekrar
> dogrulandi — 271 define regex cekildi)**. §8 madde 4'teki son "KALAN" tur
> bu bolumde kapandi. ASCII guard: bu bolumde diakritik YOK.

### 15.1 D agilim (198 = 191 anlamli + 7)

| Grup | Proc | Not |
|---|---|---|
| `Aso::Player::Radio::Recorder` | **91** | radyo kayit tee-sink (9'u `TACLLinkedList<TASORadioRecorderBufferChunk>` generic, 0x5BE400-0x5BE660) |
| `...::Radio::Stream::TASORemoteStream` | **48** | radyo decode kenari — bolum 15.2-15.4 |
| `...::Gui::OptionsFrame` | **30** | ayar sayfasi (UI-only; decode zincirine girmez) |
| `TASOStationInfoFormatStringFactory` | **13** | istasyon baslik formatlama (ToString) |
| `TASOStationInfo` | **6** | metadata tasiyici (Assign/Clear/IsTrackChanged/SetFormat) |
| `TASORemoteStreamMetaDownloader` | **2** | ctor+dtor (detay disi) |
| `TASOPlayerStreamInfo::IsRadioStream` | **1** | radyo bayti okuyucu |
| **Toplam anlamli** | **191** | |
| Radio namespace init/final | 4 | `Radio::initialization` @0x5B8D50 · `Radio::Finalization` @0x5B8D40 · `Radio::Stream::initialization` @0x5D6130 · `Radio::Stream::Finalization` @0x5D60F0 |
| VCL kalan | 3 | `Vcl::Menus::TMenuItem::SetRadioItem` @0x406E10 · `TAIMPUIRadioBoxControl::_IntfE` @0x43E028 · `TAIMPUIRadioBoxControl::CreateComponent` @0x44B5C0 |
| **Toplam** | **198** | 191 + 4 + 3 = 198 ✓ (proc-index TOTAL ile mutabik) |

### 15.2 Ana bulgu: radyo decode kendi HTTP client'i DEGIL

`TASORemoteStream` = `TBASSDecoder` turevi (§14.3); HTTP indirmeyi BASS'a
devreder. `Connect` @0x5D09D0 (dump L296249-296376) kesin zincir:

1. `flags = 0xA00000` = `BASS_STREAM_DECODE` (0x200000) | `BASS_STREAM_STATUS`
   (0x800000) — **bass.h dogrulandi**.
2. `BASS_StreamCreateURL(url, 0, flags, ProcDownload)` — indirme callback'i
   `TASORemoteStream::ProcDownload` (vtable +0x10, §14.2).
3. Handle == 0 ve `BASS_ErrorGetCode() == 1` (**BASS_ERROR_MEM**, dogrulandi)
   → `flags |= 0x100000` (**BASS_STREAM_BLOCK**, dogrulandi) → retry (L296270).
4. Handle != 0 → `GetOriginalBitDepth() > 23` ve `Is32BitSupported` →
   `flags |= 0x100` (**BASS_SAMPLE_FLOAT**, dogrulandi) → `BASS_StreamFree` +
   recreate (L296286).
5. `IsRealTimeStream` (vtable+0x30) != 0 ve BLOCK yoksa → **busy-wait:**
   `BASS_StreamGetFilePosition(handle, 9)` her 10 ms'de (`Sleep(10)`) okunur,
   ilerleme `100 - Max(x,0)` olarak obje+0x10'a yazilir, x < 1'de kirilir.
   **filepos 9 = VERIFICATION REQUIRED** (bass.h 2.4'te 0-6 araliginda; muhtemel
   yeni surum eklentisi).
6. Sync kayitlari + DSP (bolum 15.3), sonra: `BeginUpdate` → `GetStreamFormat` →
   `SetFormat` → `GetContentType` (stationInfo+400) → `GetAttribute(0xc)` →
   `Round` → stationInfo+0x10A (bitrate; attrib 0xc **VR**) → `GetTags(8)` →
   `ProcessServerHeaders` → `EndUpdate` → `UpdateRecorder` →
   `NotifyInputFormatChanged` → `ReceiveMetadata(handle, 0)` → flag temizligi.

`Read` @0x5D16F0: `BASS_ChannelGetData` < 0 → hata logu; `ErrorGetCode`
**0x25** (`BASS_ERROR_NOTAVAIL`, 37 — dogrulandi) veya **0x2D**
(`BASS_ERROR_ENDED`, 45 — dogrulandi) → obje+0x10 bit 0x04 → `DoFinished`
@0x5D1450. `+0x19` flag false ise `FastZeroMem` + tam uzunluk dondurur
(sessiz padding — `NotAVAIL` aninda buffer'i doldurur); true ise 0 dondurur.

### 15.3 Sync kenarlari ve metadata zinciri

**Sync kayitlari (Connect icinde, 7 adet):**

| Sync id | Callback | bass.h 2.4 |
|---|---|---|
| `4` | `ProcSyncMeta` (obje+0x14) | **BASS_SYNC_META = 4** ✓ |
| `0x10301` | `ProcSyncHLSDescriptionTable` (+0xA4) | 2012'te YOK — **VR** |
| `0x10300` | `ProcSyncHLSSegment` (+0x15) | 2012'te YOK — **VR** (HLS destegi sonrasi) |
| `7` | `ProcSyncDownloaded` | **BASS_SYNC_DOWNLOAD = 7** ✓ |
| `0xc` | `ProcSyncMeta` | **BASS_SYNC_OGG_CHANGE = 12** ✓ |
| `0x10100` | `ProcSyncMeta` | 2012'te YOK — **VR** |
| `0x10101` | `ProcSyncMeta` | 2012'te YOK — **VR** |

Not: 2012 header'inda `BASS_TAG_MUSIC_INST = 0x10100` / `BASS_TAG_MUSIC_SAMPLE
= 0x10300` **sayisal cakismasi** var — ayni degerler sync tipi olarak kullaniliyor
(semantik farkli; cakisma not edilir, eslestirilmez).

**Metadata zinciri:**

- `ProcPCMData` (DSP) → `ProcessStreamData` (bolum 15.4 tee-sink).
- `ReceiveMetadata` @0x5D3030 (dump L298017-298067): `ParseMeta` (2 overloads
  — asagi) → gecerlilik → `CS(+0xB0)` Enter → external listener (+0xC0,
  vtable+0x28) → `IsRealTimeStream` != 0 (canli): pending meta (+0x90)
  `ExchangePtr` + `TACLTimer` (+0x98) `SetInterval(FUN_005d2f50(...))` +
  `Restart` — **debounce araligi HESAPLANIR, sabit degil** (uydurma ms
  DEGILDIR); realtime degilse aninda `UpdateMetadata`.
- `HandlerSyncMetaDelayTimer` @0x5D36A0: timer → `UpdateMetadata` →
  `FreeAndNil(+0x90)`.
- `UpdateMetadata` @0x5D31A0: `TASOStationInfo::IsTrackChanged` && realtime →
  `Assign` + `GetPosition` → +0x60 + `DoTrackChanged` @0x5D1510; aksi halde
  `BeginUpdate`/`Assign`/`CancelUpdate` (sessiz tazeleme).
- `ParseMeta` — IKI overload: **@0x5D1DE0** (UStr; tags parse → stationInfo)
  ve **@0x5D2160** (uint; `GetTags(8)` + `GetTags(5=BASS_TAG_META ✓)`
  `_UStrCatN` ile birlestirilir → UStr overload → `GetAttribute(0xc)` → Round
  → +0x10A bitrate; tag 8 **VR** — header'da 7 (MP4) ile 9 (VENDOR) arasi bosluk).
- `ProcessTagsPairs` @0x5D2930: `GetTags(2)` (**BASS_TAG_OGG** ✓) +
  `GetTags(7)` (**BASS_TAG_MP4** ✓ — *duzeltme: onceki taslakta "APE"
  yazilmisti; APE = 6'dir*) → VorbisComments + MappedFiler → GetString(2)
  bos ise GetString(0xc)+`FUN_005D41A0` → Merge.
- `ProcessDVBDescription` @0x5D2B80: `GetTags(0x14003)` (DVB aciklama;
  **VR** — 2012'te bu aralikta tag yok) → binary parse: ilk bayt 0x42 ('B'),
  `Swap16` uzunluk & 0xFFF.
- `GetContentType` @0x5D3260 (dump L298139-298186) — **kesin esleme:**

| ctype | MIME | bass.h 2.4 |
|---|---|---|
| `0x10301` | `audio/mpeg` | YOK — **VR** |
| `0x10002` | `audio/ogg` | `BASS_CTYPE_STREAM_OGG` ✓ |
| `0x10003-0x10005` (MP1/MP2/MP3) | `audio/mpeg` | `BASS_CTYPE_STREAM_MP1/2/3` ✓ |
| `0x10300` | `audio/wma` | YOK — **VR** |
| `0x10901` | `audio/ogg` | YOK — **VR** |
| `0x10b00` | `audio/aac` | YOK — **VR** |
| diger | "" (bos doner) | — |

### 15.4 Recorder mimarisi (tee-sink, 91 proc)

`TASORadioRecorder` @0x5B9BB8 — radyo akisini "decode sonrasi, mixer oncesi"
PCM'den kopyalayip encoder'a veren tee-sink (Neva'daki tee-node kavraminin
AIMP karsiligi):

- **StartCore** @0x5BDB80: `CreateEncoder` → Controller ctor (vtable 0x5B93E8,
  encoder, recorder) → +0x18 = controller → `Initialize(fileInfo, format)` →
  vtable+0x58 → `SetCapturing(true)`.
- **OnData** @0x5BDE50: `CS(+0x40)` → `TACLThread::Timestamp` → +0x84 (zaman
  damgali chunk).
- **OnTrack** @0x5BDF00: `CS(+0x40)` → `param_3 == (Buffer+0x30 != 0)` →
  `Buffer::Clear(0)` → +0x58 (tek-parca flag) == 0 ise: controller (+0x18) != 0
  → `fileInfo(+0x50).Assign(stationInfo)` → `SplitOptions` (+0x48): `+8 == 0 ||
  +9 != 0` → vtable+0x58 (parca guncelleme); `+8 && +9 == 0` → controller+0x38
  `.Assign` + `SplitFile(SplitOptions+0x1c gecikme)`; `+0x58 != 0` →
  `SetActive(false)`.
- **SetActive** @0x5BE280: kapanis → `CS` + `SetCapturing(false)` +
  `FreeAndNil(+0x18)`; acilis → `+0x6D` int > 0 → `StartCore(+0x68 format,
  +0x50 fileInfo)`.
- **Controller** (`TASOEncodingController` turevi): +0x90 CS · +0x98 staging
  (`TACLByteBuffer`) · +0xA0 `TASORadioRecorderDecoder` · +0xB0 → +0x10 Buffer ·
  +0xB8 `TASOMixer` · +0xC0 raw encoder stream · +0xD0 delayed-split · +0xD8
  async-flag.
  - `WritePCMData` @0x5BBBD0: CS+0x90 → `Decoder::PutData(+0xa0)` → dongu:
    `Decoder::GetUsed` → `Mixer::GetData(+0xb8)` → `SetUsed` → dolunca base
    `WriteData` (encoder'a).
  - `WriteRawData` @0x5BBCF0: vtable+0x18 `Write` (bolme sabiti `_DAT_005BBDA0`
    **VR**).
  - `AsyncEncodeBuffer` @0x5BBDD0: `Buffer::Extract` FIFO → +0xC0 == 0 ?
    `WritePCMData` : `WriteRawData(chunk+0x20 - +0x24)`; son chunk'ta +0xD8 = 0.
  - `CheckNeedSplit` @0x5BACC0: `SplitOptions+8` ve `+9 == 1` (boyut modu) →
    `GetOutputBytes >= (+0xC MB) << 20`.
  - `SplitFile` @0x5BB1C0: CS+0x90 → `FreeAndNil(+0xd0)` → base `SplitFile`;
    `SplitDelayTimerHandler` vtable+0x50.
  - `PopulateFormatList`: sample-format 0..6 × StdSampleRates (16) combo.
- **Buffer**: `Add`/`Extract` (FIFO `TACLLinkedList`) / `Clear` / `SetRawFormat`
  @0x5BB690; 9 generic-uye proseduru (0x5BE400-0x5BE660).
- **Decoder** (`TASORadioRecorderDecoder`): `PutData`/`GetAvailableData`
  (+0x10 byte buffer, pos/size) · `SetFormat` @0x5BB260 · GetFormatType/
  GetFileInfo/GetPosition/GetSize/GetStreamInfo/IsRealTimeStream/IsSeekable.

### 15.5 BASS sabit dogrulamasi (bass.h 2.4 — 2012, Un4seen)

**Dogrulanmis** (271 define regex cekimi; kullanimi ile birebir):

| Sabit | Deger | Kullanim (bolum) |
|---|---|---|
| `BASS_STREAM_DECODE` | 0x200000 | Connect flags (15.2) |
| `BASS_STREAM_STATUS` | 0x800000 | Connect flags (15.2) |
| `BASS_STREAM_BLOCK` | 0x100000 | MEM retry (15.2) |
| `BASS_SAMPLE_FLOAT` | 0x100 (256) | FLOAT recreate (15.2) |
| `BASS_ERROR_MEM` | 1 | StreamCreateURL hatasi (15.2) |
| `BASS_ERROR_NOTAVAIL` | 37 (0x25) | Read → DoFinished (15.2) |
| `BASS_ERROR_ENDED` | 45 (0x2D) | Read → DoFinished (15.2) |
| `BASS_SYNC_META` | 4 | ProcSyncMeta (15.3) |
| `BASS_SYNC_DOWNLOAD` | 7 | ProcSyncDownloaded (15.3) |
| `BASS_SYNC_OGG_CHANGE` | 12 (0xc) | ProcSyncMeta (15.3) |
| `BASS_TAG_META` | 5 | ParseMeta (15.3) |
| `BASS_TAG_OGG` | 2 | ProcessTagsPairs (15.3) |
| `BASS_TAG_MP4` | **7** | ProcessTagsPairs (15.3) — *APE = 6 ile cakisma YOK; onceki "tags(7)=APE" okumasi duzeltildi* |
| `BASS_FILEPOS_DOWNLOAD` / `END` | 1 / 2 | GetBufferingProgress (dosya modu) |
| `BASS_CTYPE_STREAM_OGG` | 0x10002 | GetContentType → audio/ogg |
| `BASS_CTYPE_STREAM_MP1/MP2/MP3` | 0x10003-5 | GetContentType → audio/mpeg |
| config `UPDATEPERIOD/FLOATDSP/NET_TIMEOUT/NET_BUFFER/NET_PREBUF/NET_AGENT/NET_PLAYLIST/VERIFY/NET_READTIMEOUT` | 1/9/0xb/0xc/0xf/0x10/0x15/0x17/0x25 | §14.3 EnsureInitialized |

**⚠️ VERIFICATION REQUIRED** (2012 header'da TANIMLI DEGIL — kapatilmayacak):

| Kod | Kullanim | Not |
|---|---|---|
| sync `0x10100`, `0x10101` | ProcSyncMeta | header sync tanimlari 0-12 arasi; ust aralik muhtemel surum eklentisi |
| sync `0x10300`, `0x10301` | HLS segment / description table | HLS destegi 2012 sonrasi |
| tag `8` | ProcessServerHeaders + ParseMeta | header bosluk: 7 (MP4), 9 (VENDOR) |
| tag `0x14003` | ProcessDVBDescription | DVB |
| attrib `0xc` | bitrate (Round → +0x10A) | header'da bu id yok |
| filepos `9` | Connect busy-wait | header 0-6 |
| ctype `0x10300/0x10301/0x10901/0x10b00` | GetContentType | plugin ctypes — §6 "BASS plugin modeli" ile tutarli, isimler VR |
| config `0x28, 0x34, 0x39, 0x3c, 0x48, 0x10101` | §14.3 SetConfig zinciri | — |

### 15.6 Neva karsiliklari (ADR-0106 radyo decode kenari)

| AIMP (bolum 15) | Neva karsiligi | Not |
|---|---|---|
| `BASS_StreamCreateURL` + `ProcDownload` | kendi HTTP client'i (ADR-0106 karari: ICY/Shoutcast v1+v2 ozel tuner + HLS/FFmpeg gateway) | AIMP her seyi BASS'a devrediyor; Neva tuneri kendisi yaziyor — fark bilincli |
| realtime busy-wait (filepos 9, Sleep 10) | baglanti asamasinda progress event'i (polling YOK) | AIMP bloklayici polling; Neva'da non-blocking asama makinesi |
| `IsRealTimeStream` override (vtable+0x30) | `SourceKind::LiveRadio` varyanti | §14.8 madde 5 kapanisi |
| metadata debounce (TACLTimer, sure `FUN_005d2f50` ile hesap) | metadata callback rate-limit (ayni debouncer deseni) | aralik sabit degil — Neva kendi degerini secer |
| 7 sync kenari (META/OGG_CHANGE/DOWNLOAD + 4 VR) | demux seviyesinde ICY-NAME/GENRE/URL + ogg granule olaylari | HLS sync'lerinin karsiligi gateway ciktisinda segment olaylari |
| Recorder tee-sink (OnData → Buffer → Decoder → Mixer → encoder) | tee-sink: decode → tee → (player + file sink) | mixer-oncesi PCM kopyasi — ADR-0105 SPSC ring yapisine oturur |
| OnTrack → SplitFile (parca/split/size modlari) | segmentWriter: parca degisiminde dosya bolme | SplitOptions 3 modu (kapatik/split-on-track/size) |
| GetContentType ctype→mime | demux container/codec adindan mime turetme | VR ctype'ler plugin araligi |
| Read 0x25/0x2D → DoFinished + silence padding (+0x19) | EOF/hata → "yayin bitti" olayi + zero-fill fallback | padding bayti: hata aninda buffer doldurulur |

**Kalan acik (kucuk):** config VR seti (0x28/0x34/0x39/0x3c/0x48/0x10101) —
§14.3 ile ayni; gecikme/RAM/VRAM/olcum turleri derived (tur21'den devam).

