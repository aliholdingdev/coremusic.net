---
title: "AIMP.Shared.dll + AIMP.Runtime.dll — Tam Full Decompile (Ghidra Headless)"
type: reverse-engineering
project: NevaPlayer
version: 1.1.0
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
3. `TASOPlayer` 183 isimli prosedur — Shared dump'inda MEVCUT (Load/Play/
   SetDeviceParams/tempo-pitch icin ayri tur; tur8'deki 12-prosedur ozeti
   artik tam dump ile genisletilebilir).
4. Radio 198 / Decoder 185 isimli prosedur (Shared) — ADR-0106 radyo decode
   kenari icin ayri tur.
5. Sabit bolumleri: `_DAT_005b1a68` (ms→saniye) ve `_DAT_0057f078` (Hz→kHz
   esigi) — degerleri dump'da tanimsiz (data sembolu); kesin deger icin veri
   okuma (ReadEqBands kalibi) gerekir. *(Kismen kapanan: WASAPI bolgesindeki
   DAT_0060a788 = float 0.2 (sn, min buffer suresi) ve DAT_0060a790 = double
   250.0 tur17'de okundu — bolum 11.7.)*
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
