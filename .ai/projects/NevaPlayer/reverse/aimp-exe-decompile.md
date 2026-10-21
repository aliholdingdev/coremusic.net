---
title: "AIMP v5.4.0.2722 — Ghidra Full Decompile Dump (AIMP.exe)"
type: reverse-engineering
target: "C:\Program Files\AIMP\AIMP.exe (AIMP v5.4.0.2722)"
method: "Ghidra 12.1.4 analyzeHeadless + DumpAllProcedures.java (full pseudocode dump) — REA provider_timeout rotasyonu sonrası doğrudan Ghidra rota"
date: 2026-10-20
status: evidence-backed
evidence:
  - "sha256 d04a35f8926c8f557afbc3854310a2313fd39d9d6af72d8b14bd5e8ee6342abd (open_binary)"
  - "PE machine 0x8664 (x64) + optional magic PE32+ (bayt-seviyesi okuma) — FileVersion 5.4.0.2722"
  - "ghidra-aimp-run.log: Analysis succeeded + Import succeeded + DUMP_DONE count=23888 failures=0 seconds=108"
  - "dump sha256 6562C9C01B350511E65115B2554D5A4756366818062AA910A23BBC9B46BDEBD6 (15.140.096 B)"
  - "bayt tarama: AIMP.Shared.dll x1, AIMP.Runtime.dll x1, WASAPI/AUDCLNT/IAudioClient/bass/AVRT x0"
---

# AIMP.exe — Full Decompile Dump

> **Kapsam notu:** Önceki `aimp-decompile-dump.md` **AIMP.Shared.dll** katmanıdır (tur8).
> Bu dosya **AIMP.exe (ana executable)** katmanıdır — FULL DECOMPILE kapsamı, private/local.

## §1 Hedef ve künye

| Alan | Değer |
|---|---|
| Dosya | `C:\Program Files\AIMP\AIMP.exe` |
| sha256 | `d04a35f8926c8f557afbc3854310a2313fd39d9d6af72d8b14bd5e8ee6342abd` |
| Boyut | 7.138.752 B |
| Format | PE, **machine 0x8664 = x64 (64-bit)**, optional magic PE32+ (bayt-seviyesi teyit) |
| Sürüm | FileVersion / ProductVersion `5.4.0.2722` — Company: Artem Izmaylov |
| Dil | C++ (Embarcadero C++Builder) — tüm isimler **Itanium C++ mangling** (`_ZN...`, `System::`/`Vcl::`/`Acl::` namespace'leri) |
| Yöntem | Ghidra 12.1.4 headless (`analyzeHeadless`) + `DumpAllProcedures.java` → tüm prosedür pseudocode |
| Dump sha256 | `6562C9C01B350511E65115B2554D5A4756366818062AA910A23BBC9B46BDEBD6` |

**⚠️ Düzeltme:** Önceki oturum notlarında "x86 (32-bit), Delphi" yazıyordu — **yanlış**.
PE header bayt okuması x64 + Itanium C++ mangling kanıtladı: v5.4, C++Builder ile derlenmiş x64.

## §2 Yöntem rotasyonu (neden Ghidra headless?)

| Rota | Sonuç |
|---|---|
| REA `list_procedures` (AIMP.exe) | `provider_timeout` 330 sn ×2 — 7.1MB binary'de Ghidra auto-analysis REA eşiğini aşıyor |
| REA `rea doctor` | Ghidra 12.1.4 + JDK21 + native decompiler sağlıklı; Hopper/IDA/JADX yok; timeout ayarı CLI'da yok |
| **Doğrudan Ghidra `analyzeHeadless`** | **BAŞARILI**: Analysis succeeded → postScript → `DUMP_DONE count=23888 failures=0 seconds=108` |

Kalıcı proje: `Temp\opencode\ghidra-aimp\AIMPProject` (yeniden hedef analizinde tekrar kullanılır).
İlk deneme `Directory not found` (createProject için dizin önceden yok) → dizin oluşturulup tekrar başarılı.

## §3 Sayım ve sınıflandırma

| Kategori | Sayı | Not |
|---|---:|---|
| Toplam prosedür | **23.888** | 0 hata, 0 decompile başarısızlığı |
| Gerçek gövde (>400 B pseudocode) | 8.978 | analiz edilebilir kod |
| Küçük gövde (≤400 B) | 11.552 | thunk yardımcıları / getter-setter |
| Import-thunk (`Treating indirect jump as call`) | 3.358 | **gövde yok** — harici DLL import kuyruğu |
| İsimli prosedür (C++ mangled) | 3.404 | RE için doğrudan anahtar |
| Adsız (`FUN_...`) | 20.484 | iç yardımcılar |

**İsimli namespace sayım:** `Acl::*` 1.623 · `System::*` (Embarcadero RTL) 539 · `Aimp::*` 304 (gerçek gövdeli 268) · `Vcl::*` 211 · `Aso::*` 118 (hepsi import-thunk!) · `Apiwrappers::*` 26

## §4 Mimari çıkarım — 3 katmanlı import sınırı

```text
AIMP.exe (bu dump — UI + shell + katalog)
  ├─ Acl::* 1.623 / Vcl::* 211 / System::* 539   = statik linkli Embarcadero VCL+RTL+AIMP component lib
  ├─ Aimp::* 304                                  = uygulama çekirdeği (Core/Messages, Ui, Filemanager)
  └─ Aso::* 118 → hepsi import-thunk (16-byte slot, govde YOK)
        ├─→ AIMP.Shared.dll   (bayt tarama x1)  = Aso ses cekirdegi API (tur8 dump'da: EQ, SoXR, SafeProcessData)
        └─→ AIMP.Runtime.dll  (bayt tarama x1)  = render/WASAPI beklentisi (hala RE BEKLIYOR)
```

**Negative evidence (bayt tarama, AIMP.exe içi):** `WASAPI` 0 · `AUDCLNT` 0 · `IAudioClient` 0 ·
`IAudioRenderClient` 0 · `bass.dll` 0 · `AVRT` 0 · `mmdevapi` 0 · `dsound` 0
→ **AIMP.exe'de ses render kodu YOK.** WASAPI render her hâlükârda `AIMP.Runtime.dll` katmanında
(çakışma nedeniyle tüm `*Do*` regexi `DoPopup` gibi false-positive üretir; sonuçlar ayrıca `Do` önekiyle doğrulandı).

## §5 Kilit sembol yüzeyi (import edilen Aso API'si — Neva için şartname değeri)

| Alan | Semboller (kanıt) |
|---|---|
| **EQ** | `Aso::Processors::Equalizer::TASOCustomEqualizer::{GetEqBand,SetEqBand,SetEQBandsArr,SetPreamp,ResetBands}` — `SetEQBandsArr(StaticArray<double, Li20>)` = **20 band** (Li20) · `Aso::Utils::SoundOutEqBand{Format,FreqString,ToPercent}` / `SoundOutPercentToEqBand` · `Aimp::Core::Messages::CorePropertyEqualizerGetBandValue(int)` / `SetBandValue(int,float)` (plugin property API) · `Aimp::Ui::Equalizerdisplay::TAIMPEqualizerDisplaySubClass::{Draw,MouseDown,MouseMove,MouseUp}` (interaktif EQ UI + TCanvas) |
| **Player** | `Aso::Player::TASOPlayer::{Load,Pause,Play,Stop,SetPosition,GetDuration,SetDeviceParams,SetPlaybackTempo,SetPlaybackPitch,SetPlaybackSpeed,GetVolumeSettings}` — tempo/pitch/time-stretch API'si · `TASODeviceParams`, `TASODeviceManager::PopulateDevices` (cihaz listesi) |
| **Gapless/Fade** | `Aso::Player::Stream::TASOStreamFadingOptions::{SetCrossMixing,SetCrossMixingTime,SetFadeIn,SetFadeInTime,SetFadeOut,SetFadeOutTime,GetActualCrossMixingTime}` — **crossfade/gapless bayrağı + süre API'si** (kapı 5.12 için AIMP gerçeği) |
| **Radio** | `Aso::Player::Radio::Recorder::TASORadioRecorder::{SetActive,SetStopAfterTrack,SetMode} (RTS/Yakala) · `TASOPlayerStreamInfo::{IsRadioStream,IsRealTimeStream,IsRemoteStream,GetRemoteStreamServerHeaders,GetRemoteStreamFormat}` — ICY/HTTP başlık erişimi düzeyinde |
| **DSP** | `Aso::Processors::Dsp::{TASONormalizer,TASOCustomDSPManager,TASOCustomProcessor}` (preamp min/max/target) · `Aso::Processors::Adaptive::TASOAdaptiveProcessor` (buffer adaptive yönetimi) |
| **Format** | `Aso::Types::{TASOStreamFormat,TASOSampleFormat,SoundOutBufferAlloc,SoundOutBufferFree,TASOPartRepeatInfo}` (bit derinliği, A-B repeat, buffer alloc/free — **kullanıcı-üstü buffer yönetimi**) |
| **Diğer** | ReplayGain: `Aso::Analysis::Replaygain::*` (7) · Visualizer: `TASOWaveformVisualizer` (256-bin) · SQLite 14 (katalog) · PlayList 20 · Tag 24 · Lyrics 4 |

**0/negative (isim tarama):** `Decoder` 0 · `Codec` 0 · `Icy` 0 · `Shoutcast` 0 · `Video` 0 · `Shader` 0 ·
`WASAPI` 0 → decode/codec/ICY katmanı da `AIMP.Shared.dll` + `AIMP.Runtime.dll` tarafında (ortak sınır).

## §6 Vault'a işlenen dosyalar

| Dosya | Boyut | İçerik |
|---|---:|---|
| `reverse/aimp-exe-full-decompile.txt` | 15.140.096 B | Tam pseudocode dump (23.888 prosedür, tam metin) |
| `reverse/aimp-exe-proc-index.txt` | 715.865 B | Adres + sembol adı indeksi (arama/işleme girişi) |
| `reverse/aimp-exe-decompile.md` | bu dosya | Özet + künye + mimari çıkarım |

Yeniden üretim: `Temp\opencode\ghidra-aimp-run.log` + kalıcı Ghidra projesi (`Temp\opencode\ghidra-aimp\AIMPProject`).
Dump, hedef binary ile birlikte `Ghidra 12.1.4` `DumpAllProcedures.java` postScript'i ile tekrar üretilebilir.

## §7 RE bekleyenler (dump kapatmadıkları — sıradaki adımlar)

1. **`AIMP.Runtime.dll` WASAPI render** — AIMP.exe'de render yok (negative evidence §4); `IAudioClient3`/exclusive akış gerçek kodu Runtime.dll'de. Kapı 5.6/ADR-0105 §5.4 için asıl hedef.
2. **`Aso::Player::TASOPlayer::*` gövdeleri** — bu dump'ta yalnız thunk; gerçek gövde `AIMP.Shared.dll` dump'ında aranmalı (tur8 dosyası `aimp-decompile-dump.md`).
3. **`Aso::Types::EQ_BANDS` @0x668390 (AIMP.Shared.dll)** — içerik doğrulaması (31-band vektörü) hâlâ açık.
4. **Gapless gerçek davranış** — `TASOStreamFadingOptions` API'si var; asıl crossfade kenar koşulları (MP3 gapless metadata) decode gövdesinde (Shared/Runtime).
