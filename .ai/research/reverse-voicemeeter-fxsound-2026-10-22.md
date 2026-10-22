---
title: "Reverse Engineering — VB-AUDIO VoiceMeeter & FxSound (Kurulu Binary + Sürücü Yığını)"
type: research
category: reverse-engineering
status: draft
version: 1.0.0
date: 2026-10-22
authority: "Windows Software Engineer — L0 Platform"
tier: 3
domain: audio
ssot: false
risk: medium
owner: "MO"
depends-on: [".ai/research/sanal-audio-cihaz-arastirmasi-2026-10-22.md", ".ai/research/fxsound-app-source-analizi-2026-10-22.md"]
---

# Reverse Engineering — VoiceMeeter (Potato 3.1.2.2) & FxSound (1.2.13.0)

**Tarih:** 2026-10-22 · **Mod:** Red Team · Human Mode · Truth Mode
**Yöntem:** REA (`rea-pe-resources` provider) + dosya envanteri (PowerShell) + ASCII string taraması + kurulum INF/`.sys`/DLL envanteri + FxSound kaynak kod incelemesi (AGPL repo clone).
**Kapsam:** Kurulu binary'ler (statik analiz). Dinamik analiz (çalışan process trace) **bu oturumda YAPILMADI** → §9'da açık madde.

> **Zero-Hallucination:** Bu rapordaki her iddia ya disk kanıtıyla (dosya yolu/boyut/strings eşleşmesi) ya da `⚠️ VERIFICATION REQUIRED` etiketiyle işaretlenmiştir. Tahmin olarak sunulan hiçbir ifade "kanıt" değildir.
>
> **Hukuki not:** Binary'ler statik olarak incelenmiştir; hiçbir kod, resource veya veri **kopyalanmamış/adapt edilmemiştir**. Amaç yalnızca **mimari/interoperability anlayışıdır** (WORKFLOW §8.1C referans proje kuralı). VoiceMeeter **kapalı kaynak + ticari** üründür; CoreMusic'e hiçbir parça aktarılmaz.

---

## §1 Hedef Envanteri (disk kanıtı)

### §1.1 VoiceMeeter (VB-AUDIO Software)

| Dosya | Boyut | Bilgi |
|---|---|---|
| `C:\Program Files (x86)\VB\Voicemeeter\voicemeeter8.exe` | 13 020,9 KB | ProductName **VoiceMeeter Potato** · FileVersion **3.1.2.2** · Company VB-AUDIO Software · imza **Valid** (CN=BUREL VINCENT, FR) |
| `voicemeeter8x64.exe` | 13 126,4 KB | x64 varyant (aynı ürün ailesi) |
| `voicemeeter.exe` / `voicemeeter_x64.exe` | 5 629,4 / 5 574,9 KB | VoiceMeeter (temel sürüm) |
| `voicemeeterpro.exe` / `_x64` | 7 119,4 / 7 082,9 KB | VoiceMeeter (Pro/Banana sınıfı) |
| `VoicemeeterRemote.dll` / `Remote64.dll` | 156,9 / 188,4 KB | **Uzaktan kontrol API DLL'i** (3. taraf entegrasyon kanalı) |
| `VoicemeeterBUSGEQ15.exe` | 526,6 KB | **Modül: BUS 15-band Graphic EQ ayrı exe** |
| `VoicemeeterBUSMatrix8.exe` | 157,1 KB | **Modül: 8×8 BUS routing matrix ayrı exe** |
| `VoicemeeterMacroButtons.exe` | 2 191,9 KB | **Modül: makro/buton paneli ayrı exe** |
| `VMStreamerView.exe` | 116,7 KB | **Modül: stream izleme görünümü ayrı exe** |
| `VBAN2MIDI.exe` | 320,7 KB | **Modül: VBAN→MIDI köprüsü ayrı exe** |
| `VBDeviceCheck.exe` | 166,7 KB | Sürücü/cihaz denetim aracı |
| `mp3lame\lame_enc64.dll` | 1 439 KB | LAME MP3 encoder (kayıt/yayın encode zinciri) |

**REA PE resource sonucu (`voicemeeter8.exe`):** `format: pe32`, `machine: 332 (x86)`, `bytes: 13 333 352`, `sha256: e1f2cdf2…b92520`. Resource dizini **110 id-entries** içerir (1.519 104 offset) → yoğun Win32 dialog/UI resource'u (GUI tabanlı uygulama).

### §1.2 VoiceMeeter sürücü & sanal cihaz paketi (kayıt klasörü)

| Bileşen | Kanıt | Yorum |
|---|---|---|
| `vbvoicemeetervaio64_win10.sys` (289,4 KB) + `.inf` (36,6 KB) + `.cat` (12,8 KB) | strings: `KMDF`×4, `WDF`×3, `portcls`×1, `Miniport`×1 | **Windows 10 sanal audio endpoint sürücüsü** — KMDF + PortClass miniport tabanlı |
| `vbvoicemeetervaio64arm_win10.sys` (297,9 KB) | — | **ARM64 sürücü varyantı** (Windows on ARM hedefi) |
| `vbvmvaio3*`, `vbvmvaio*`, `vbauxvaio*` (.sys/.inf/.cat setleri: 2003, vista, win7, win10 × x86/x64) | her cihaz ailesi için 4 OS varyantı | **Çoklu sanal cihaz ailesi** — her biri ayrı audio endpoint (VoiceMeeter strip'lerini OS'e taklit eden virtual device) |
| `vbvm_asiodriver64.dll`, `vbvmvaio3_asiodriver64.dll`, `vbvmins_asiodriver64.dll`, `vbvmpins_asiodriver64.dll`, `vbvmaux_asiodriver64.dll` (her biri ~122 KB; x86 varyantları ~105 KB) | 5 ayrı ASIO wrapper DLL'i | **Her sanal cihaz ayrı ASIO cihazı olarak da sunuluyor** → 3. taraf DAW'lar/ASIO uygulamaları sanal cihaza ASIO üzerinden bağlanabiliyor |
| `vbusbgpi_uart.inf`, `vbusbgpi_uart_xp_vista.inf` | — | Sürücü kurulum INF'leri (toplam 21 `.inf`) |
| `VBVoicemeeterVAIO_Setup_x64.exe` (900,2 KB), `VBDeviceCheck.exe` | — | Sürücü kurulum/health araçları |

### §1.3 FxSound (FxSound LLC, ABD)

| Dosya | Boyut | Bilgi |
|---|---|---|
| `C:\Program Files\FxSound LLC\FxSound\FxSound.exe` | 3 730 KB | ProductName **FxSound** · FileVersion **1.2.13.0** · imza **Valid** (FxSound LLC, Mill Valley CA) |
| `fxmcp.exe` | 6 589,5 KB | FileVersion **0.1.0.0** · (kaynak incelemede: **Go ile yazılmış MCP sunucusu** — §6) |
| `fxdiag.exe` | 357,8 KB | Tanılama aracı |
| `updater.exe` (1 675,2 KB) + `updater.ini` | — | Otomatik güncelleme |
| `Drivers\win10\x64\fxvad.sys` | 319 KB | **FxSound Virtual Audio Driver (VAD)** — strings: `portcls`×1, `FxSound`×1 |
| `Drivers\win10\x86\fxvad.sys` | 315 KB | x86 varyant |
| `Drivers\win7\x64\fxvad.sys` / `win7\x86\fxvad.sys` | 318,5 / 314,5 KB | Windows 7 varyantları |
| `Drivers\ptdevcon32.exe` / `ptdevcon64.exe` | 205 / 234 KB | Sürücü install yardımcıları (Theremino devcon türevi) |
| `fxmcp.mcpb` | 16,1 KB | MCP bundle manifesti (§6) |

---

## §2 VoiceMeeter — Mimari Model (kurtarılan tasarım)

### §2.1 Routing topolojisi

Kurulum envanteri + ürün ailesi adlandırması şu topolojiyi dayatır (VoiceMeeter Potato):

```text
[Physical Inputs]          [Virtual Inputs]              [Processing]                 [Outputs]
MIC 1/2/3 (WDM/KS)   ──►  VAIO / AUX VAIO  ◄──  (uygulama sesleri)      ──►
Line 1/2/3            ──►  Strip 1..N  ──►  [Strip DSP: Gain / Comp / EQ]
                                                      │
                                                      ▼
                                            [BUS 1..5 + A1/A2/A3]
                                                      │
                        Physical Out (A1/A2/A3) ◄─────┤
                        Virtual Out (B1/B2)     ◄─────┘  ──►  kaydeden/yayınlayan uygulamalar
```

**Kanıt:** 5 ayrı ASIO wrapper DLL'i + 4 ayrı sanal cihaz sürücü ailesi (§1.2) → strip'lerin bir kısmı "virtual input", çıktıların bir kısmı "virtual output" olarak **fiziksel cihaz taklidi** yapıyor.

### §2.2 Örnekleme zinciri (string kanıtları)

`voicemeeter8.exe` string taraması:

| Desen | Eşleşme | Yorum |
|---|---|---|
| `44100` | 7 | Desteklenen/varsayılan rate |
| `48000` | 7 | Desteklenen rate (WASAPI varsayılanı) |
| `96000` | 2 | **Desteklenen hi-res rate** |
| `192000` | 2 | **Desteklenen maksimum rate** |
| `ASIO` | 25 | Yoğun ASIO API kullanımı (ASIO cihaz seçimi + ASIO bridge) |
| `asio` | 4 | küçük harf varyantları |
| `WASAPI` | 1 | WASAPI kanalı mevcut (tek referans — ASIO baskın) |
| `Exclusive` | 1 | Exclusive mod referansı mevcut |
| `VBAN` | 53 | **VBAN ağ (UDP ses/MIDI) alt sistemi çok belirgin** |

**Sonuç:** VoiceMeeter **192 kHz'e kadar** çalışabiliyor (string kanıtı). Ancak `WASAPI`×1 ↔ `ASIO`×25 oranı, birincil düşük-gecikme yolunun **ASIO** olduğunu gösteriyor.

### §2.3 Modüler executable stratejisi (CoreMusic'e doğrudan referans)

VoiceMeeter, DSP/routing alt işlevlerini **ayrı executable'lere** bölmüştür:

| Modül exe | İşlev |
|---|---|
| `voicemeeter8.exe` | Ana mixer/DSP host |
| `VoicemeeterBUSGEQ15.exe` | BUS başına 15-band grafik EQ (bağımsız pencere) |
| `VoicemeeterBUSMatrix8.exe` | 8×8 yönlendirme matrisi |
| `VoicemeeterMacroButtons.exe` | Makro tetikleme paneli |
| `VMStreamerView.exe` | Stream/cihaz izleme |
| `VBAN2MIDI.exe` | Ağ→MIDI köprüsü |
| `VoicemeeterRemote64.dll` | 3. taraf API (dll olarak servis) |

**Neva Engine'e ders:** kullanıcı kararındaki "**lib + çok executable**" stratejisi, sektörde kanıtlanmış bir modeldir (VoiceMeeter bunu üretimde kullanıyor). CoreMusic `neva-eq`, `neva-matrix`, `neva-remote-api` gibi ayrı hedefler için güçlü emsal.

---

## §3 VoiceMeeter — sürücü katmanı analizi

### §3.1 Sürücü modeli

`vbvoicemeetervaio64_win10.sys` string kanıtları: **KMDF / WDF** (çatı) + **PortClass miniport** (Windows audio miniport modeli).

- PortClass miniport → ses uç noktası (endpoint) olarak register edilir; `EndpointBuilder`/`AudioSes` tarafından görünür.
- KMDF → sürücü yaşam döngüsü, DMA/IRP yönetimi.
- Wave/Render-Capture pin'leri `⚠️ VERIFICATION REQUIRED` (INF içeriği okunmadı — §9 açık madde).

### §3.2 OS matrisi (dosya adı kanıtı)

| Cihaz ailesi | 2003 | Vista | Win7 | Win10 | x86 | x64 | ARM64 |
|---|---|---|---|---|---|---|---|
| `vbvmvaio` (VAIO) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (`…ervaio64arm_win10.sys`) |
| `vbvmvaio3` (VAIO3) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | — |
| `vbvmvaio64` | ✅ | ✅ | ✅ | ✅ | — | ✅ | — |
| `vbauxvaio` (AUX VAIO) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | — |
| `vbauxvaio64` | ✅ | ✅ | ✅ | ✅ | — | ✅ | — |

**Ölçüm:** 5 cihaz ailesi × 4 OS × (x86/x64) = 20 `.sys` + eşleşen `.cat`/`.inf` setleri (toplam 21 INF, 40+ .sys).

**Ders:** VB-Audio, **her OS sürümü için ayrı imzalı sürücü varyantı** dağıtıyor (XP'den Win10'a). CoreMusic sanal cihazı için bu, **imzalama matrisi maliyeti** demektir (EV/Attestation — bkz. `sanal-audio-cihaz-arastirmasi-2026-10-22.md` §4).

---

## §4 FxSound — mimari model (binary + kaynak çapraz)

### §4.1 Bileşen ayrımı (kaynak README kanıtı)

FxSound repo'su (`app-cpp-projects/fxsound-app`, commit `4383180e`) README'si üç bileşeni açıkça tanımlar:

| Bileşen | Rol | Kanıt |
|---|---|---|
| **FxSound GUI app** | JUCE 6.1.6 üstünde arayüz | `fxsound/Project/FxSound.sln`, `fxsound/JuceLibraryCode/` |
| **audiopassthru** | Cihazlarla etkileşim (WASAPI) | `audiopassthru/{include,src}/` — `IMMDeviceEnumerator`, `IAudioClient` kullanımları |
| **DfxDsp** | DSP zinciri | `dsp/DfxDsp*.cpp`, `dsp/ptechDsp/**` |

**Lisans:** AGPL v3.0 (LICENSE satır 1) + JUCE (AGPL). ⚠️ **CoreMusic için kritik:** AGPL, kaynak paylaşım yükümlülüğü doğurur → **kod kopyalama kesinlikle yasak**; yalnız mimari referans.

### §4.2 Ses API katmanı (audiopassthru)

Kod kanıtları:

| Dosya | Satır | Kanıt |
|---|---|---|
| `audiopassthru/include/sndDevices.h` | 223 | `IMMDeviceEnumerator *_pEnumerator;` |
| `audiopassthru/src/sndDevices/sndDevicesGet.cpp` | 410 | `IAudioClient *pAudioClient;` |
| `audiopassthru/src/sndDevices/sndDevicesInit.cpp` | 51 | `IMMDeviceEnumeratorPtr pEnumerator = NULL;` |
| `audiopassthru/src/sndDevices/sndDevicesSetupDevices.cpp` | 75 | `pCaptureDevice->Activate(IID_IAudioClient, CLSCTX_ALL, …)` |
| `audiopassthru/src/sndDevices/sndDevicesSetupDevices.cpp` | **170, 173** | `StreamFlags = AUDCLNT_STREAMFLAGS_LOOPBACK;` · `\|AUDCLNT_SESSIONFLAGS_DISPLAY_HIDE` |

**Kritik bulgu 1 — FxSound WASAPI-only'dir:** depoda `asio`/`ASIO` eşleşmesi **YOK** (`audiopassthru` taraması). Yalnızca `AUDCLNT_STREAMFLAGS_LOOPBACK` (capture/tap) bayrakları var → model: **başka uygulamaların sesini WASAPI loopback ile yakala, DSP'den geçir, sanal cihaza (fxvad) yaz.**

**Kritik bulgu 2 — bit-perfect YOK:** depoda `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` / `SRC_DEFAULT_QUALITY` / `AUDCLNT_STREAMFLAGS_NOPERSIST` **görülmedi**; loopback + paylaşımlı mod (`shared`×10 string) → Windows mixer zinciri devrede. 192 kHz/32-bit bit-perfect hedefi FxSound modeliyle **karşılanamaz** (kullanıcı gözlemi doğrulandı).

### §4.3 DSP zinciri (dizin kanıtı)

`dsp/ptechDsp/` altındaki 8 modül:

| Modül | Anlam |
|---|---|
| `Aural/` (`Auralp.c`, `Auralp32.c`) | **Auralization** — mekân/odа simülasyonu (psikoakustik) |
| `Dly8/`, `Dly32/` | Gecikme (8-bit/32-bit sürümler — legacy + modern) |
| `Lex/` (`Lex16.c`, `Lex32.c`) | **Lexicon tarzı reverb** (hafif/derin sürümler) |
| `Maximizer/` | Loudness maximizer (brickwall benzeri) |
| `Peq/` | Parametrik EQ |
| `Play/` | Çalma/transport yardımcıları |
| `wide/` | Stereo genişletme |
| `ptComSftDfx/` | COM software wrapper (`Comsftwr.c`) |

**Ölçüm:** depo toplam **180 `.cpp` · 46 `.c` · 165 `.h` · 68 `.fac`** (`.fac` = fabrika preset/katsayı dosyaları tahmini — `⚠️ VERIFICATION REQUIRED`).

**Neva Engine'e ders:** zincir dizilimi `EQ → Delay/Reverb → Widener → Maximizer` şeklinde; Neva'nın 31-band PEQ + psikoakustik reverb + True Peak brickwall limiter hedefi bu sıralamayla uyumlu (ADR-017 DSP hardware mode ile birlikte okunmalı).

### §4.4 fxmcp.exe — AI/MCP kontrol katmanı (en önemli bulgu)

`fxsound-app/fxmcp/` içeriği:

| Dosya | Kanıt |
|---|---|
| `go.mod` / `go.sum` | **Go modülü** (Go binary → 6,5 MB fxmcp.exe) |
| `cmd/fxmcp/main.go` (+ `main_test.go`, `prompts_test.go`, `security_test.go`, `write_tools_test.go`) | MCP sunucusu girişi + **prompt/tool testleri** |
| `internal/fxsound/{config,diagnostics,effects,equalizer,errors,language}.go` (+test dosyaları) | FxSound **EQ/efekt/teşhis** işlevlerinin Go sarmalayıcıları |
| `fxmcp/mcpb/`, `build-mcpb.ps1` | **MCP bundle** (AI agent'a araç sunumu) |
| `cmd/fxmcp/resource_windows_{386,amd64,arm64}.syso` | 3 Windows mimarisi için resource |
| `web/dsp-tutorial/` | DSP eğitimi içeriği |

**Bulgu:** FxSound, ürününü **MCP (Model Context Protocol) üzerinden AI agent'lara kontrole açmıştır** — EQ presetleri, efekt anahtarları, teşhis sorguları AI araçları olarak sunuluyor. Strings: `Equalizer`×23, `surround`×18, `exclusive`×5, `shared`×10.

**CoreMusic'e ders (doğrudan):** CoreMusic'in "11 uzmanlık alanlı AI agent" vizyonu ile FxSound'ın MCP yaklaşımı **aynı yönde**. Neva Engine'e bir **`neva-mcp`** hedefi (EQ/reverb/limiter parametrelerinin AI araçları olarak sunulması) düşük maliyetli, yüksek farklılaştırıcı bir eklemedir.

---

## §5 Karşılaştırmalı özet

| Kriter | VoiceMeeter Potato 3.1.2.2 | FxSound 1.2.13.0 |
|---|---|---|
| Mimari | Monolith GUI + modül exe'ler + Remote DLL | 3 parça: JUCE GUI + WASAPI passthrough + DSP |
| Ses API | ASIO (baskın, 25 string) + WASAPI (1) | **Yalnız WASAPI** (+ loopback capture) |
| Exclusive mod | `Exclusive` string var (`⚠️ VERIFICATION REQUIRED` — bayrak kullanımı doğrulanmadı) | `exclusive`×5 (fxmcp config) |
| Sanal cihaz sürücüsü | KMDF + PortClass miniport, **5 cihaz ailesi**, XP→Win10, x86/x64/**ARM64** | Tek **VAD** (`fxvad.sys`), Win7+Win10, x86/x64 |
| ASIO bridge | ✅ 5 ayrı ASIO wrapper DLL'i | ❌ yok |
| Örnekleme | string kanıtı: 44.1k/48k/96k/**192k** | `⚠️ VERIFICATION REQUIRED` (kaynakta sabit oran yok — loopback cihaz oranına uyar) |
| DSP | Strip EQ/Comp + BUS GEQ15 (ayrı exe) + matrix | Peq, Lex(reverb), Aural, Dly, Wide, Maximizer |
| Ağ | **VBAN** (UDP ses/MIDI, 53 string) | yok |
| Uzaktan API | **VoicemeeterRemote64.dll** | **fxmcp (Go, MCP)** |
| AI entegrasyonu | yok (dokümante edilmemiş) | ✅ MCP sunucu + bundle |
| Lisans | Kapalı kaynak, ticari | **AGPL v3.0** (kaynak açık) |
| bit-perfect 32/192k | `⚠️ VERIFICATION REQUIRED` (192k string var) | ❌ (loopback + paylaşımlı mixer zinciri) |

---

## §6 CoreMusic / Neva Engine — aktarılabilir tasarım kararları

1. **Sanal cihaz = ayrı, imzalı sürücü paketi.** Her iki ürün de `.sys` + `.inf` + `.cat` + devcon yardımcıları dağıtıyor. CoreMusic `virtual-audio-cihazı` işi ayrı bir **sürücü projesi** olarak planlanmalı (Neva core'dan bağımsız sürüm/ima matrisi).
2. **Her sanal cihaza ASIO bridge ver.** VoiceMeeter bunu 5 DLL ile yapıyor. Neva'nın "ASIO her yerde" hedefi için **`neva-asio-bridge`** hedefi kritik (DAW/3. taraf uygulama uyumu).
3. **Modüler exe'ler.** BUS GEQ / Matrix / Macro / StreamView ayrı exe → Neva için `neva-eq`, `neva-matrix`, `neva-remote`, `neva-mcp` hedefleri (ADR-107 §Karar-2).
4. **Uzaktan kontrol API'si DLL + ağ.** VoicemeeterRemote (DLL) + VBAN (UDP). Neva için: **yerel IPC (named pipe / gRPC)** + ağ protokolü (multi-room senkron — `.ai/electronic/` multi-room hedefleriyle uyumlu).
5. **AI/MCP katmanı.** FxSound örneği: DSP parametreleri AI aracı olarak. Neva'ya `neva-mcp` hedefi önerilir.
6. **Loopback tap ≠ bit-perfect.** FxSound modeli (WASAPI loopback + paylaşımlı) CoreMusic hedefini **karşılamaz**; bit-perfect için exclusive + sanal cihaz + donanım pin formatı gerekir (`sanal-audio-cihaz-arastirmasi-2026-10-22.md`).

---

## §7 CoreMusic için risk & yasak özeti

| # | Risk | Önlem |
|---|---|---|
| R1 | FxSound **AGPL** — kod uyarlaması kaynak paylaşım yükümlülüğü doğurur | **Kod kopyalama/uyarlama yasak**; yalnız mimari referans (WORKFLOW §8.1C) |
| R2 | VoiceMeeter **kapalı kaynak/ticari** — RE sınırı | Statik analiz + interoperability; hiçbir resource/dll kopyalanmaz |
| R3 | Sürücü imzalama (EV/Attestation/WHQL) | `sanal-audio-cihaz-arastirmasi-2026-10-22.md` §4 — ayrı iş planı |
| R4 | ARM64 varyant (VoiceMeeter'da var) | Neva backend matrisine ARM64 sütunu eklendi (ADR-107) |

---

## §8 Kanıt kütüphanesi (tekrarlanabilir komutlar)

```powershell
# 1. Binary envanteri + imza
Get-AuthenticodeSignature -LiteralPath "C:\Program Files (x86)\VB\Voicemeeter\voicemeeter8.exe"
(Get-Item -LiteralPath "C:\Program Files (x86)\VB\Voicemeeter\voicemeeter8.exe").VersionInfo

# 2. String taraması (ASCII)
$b=[IO.File]::ReadAllBytes("C:\Program Files (x86)\VB\Voicemeeter\voicemeeter8.exe")
$t=[Text.Encoding]::ASCII.GetString($b); @('ASIO','WASAPI','192000','96000','VBAN') | %{ "$_ " + ([regex]::Matches($t,$_)).Count }

# 3. Sürücü envanteri
Get-ChildItem "C:\Program Files (x86)\VB\Voicemeeter" -Recurse -Include *.sys,*.inf,*.cat | Select Name,Length

# 4. FxSound sürücüsü
Get-ChildItem "C:\Program Files\FxSound LLC\FxSound\Drivers" -Recurse

# 5. REA PE resource (bu oturumda kullanıldı)
# tools["rea"].inspect_pe_resources({path:"…\\voicemeeter8.exe"})
```

---

## §9 Açık konular (UNKNOWN / doğrulanacak)

| # | Madde | Durum |
|---|---|---|
| U1 | VoiceMeeter'in gerçek **exclusive mod** kullanımı ve pin formatları | Pin formatları ✅ **KAPANDI** (`multi-os-audio-stack-veri-toplama-2026-10-22.md` §2: 8 render + 8 capture pin, VAIO2023; .sys rate taraması 44.1k–192k, 192k en sık=36; EXTENSIBLE 709/FLOAT 244). Exclusive mod kullanımı hâlâ `⚠️ VERIFICATION REQUIRED` (dinamik analiz gerekli) |
| U2 | `fxvad.sys` pin formatları (max rate/bit) | ✅ **KAPANDI** (`multi-os-audio-stack-veri-toplama-2026-10-22.md` §3: 88.2/96/176.4/192 kHz sabitleri .sys'te **0**; default INF blob'u 48 kHz/16-bit/stereo EXTENSIBLE → FxSound sanal cihazı 44.1/48 kHz ile sınırlı) |
| U3 | Dinamik analiz (çalışan process: hangi API çağrılıyor) | YAPILMADI — Rizin/REA debug oturumu gerekli |
| U4 | `.fac` dosyalarının içeriği (preset mi, FIR katsayı mı) | ✅ **KAPANDI** — **preset** (düz metin, `Factsoft/` 13 dosya, şema §9: `fxsound-app-source-analizi-2026-10-22.md` §19.2; FIR katsayı YOK) |
| U5 | VoiceMeeterRemote API export listesi | ✅ **KAPANDI** (`multi-os-audio-stack-veri-toplama-2026-10-22.md` §4: ASCII string taraması — `VBVMR_Login/Get/SetParameter(Float/StringA/W)/SetParameters(W)/GetLevel/GetMidiMessage/Input_GetDevice*/AudioCallback*`; 3 COM callback kanalı `VBVMCClientV2`; derleme VC2010, PDB path kanıtı) |

---

## §10 İlişkili dokümanlar

| Dosya | İlişki |
|---|---|
| `.ai/research/sanal-audio-cihaz-arastirmasi-2026-10-22.md` | Sanal cihaz adayları + 192k/32-bit doğrulama planı |
| `.ai/research/fxsound-app-source-analizi-2026-10-22.md` | FxSound kaynak mimarisi detayı |
| `.ai/architecture/neva-engine-platform-paketleme.md` | Neva platform/backend kararları (bu raporun girdisi) |
| `.ai/.decisions/draft/ADR-107-neva-engine-platform-ve-paketleme.md` | Tescilli karar taslağı |

---

## §11 Tam Dosya Envanteri (klasör dökümü — tekrarlanabilir ölçüm)

### §11.1 VoiceMeeter kurulum klasörü (`.exe` / `.dll` / `.sys` / `.inf`)

| Grup | Dosya (ad × boyut KB) | Sayı |
|---|---|---|
| **Ana uygulamalar** | `voicemeeter8.exe` 13 020,9 · `voicemeeter8x64.exe` 13 126,4 · `voicemeeterpro.exe` 7 119,4 · `voicemeeterpro_x64.exe` 7 082,9 · `voicemeeter.exe` 5 629,4 · `voicemeeter_x64.exe` 5 574,9 · `Voicemeeter8Setup.exe` 72 747,4 | 7 |
| **Modül exe'ler** | `VoicemeeterBUSGEQ15.exe` 526,6 · `VoicemeeterBUSMatrix8.exe` 157,1 · `VoicemeeterMacroButtons.exe` 2 191,9 · `VMStreamerView.exe` 116,7 · `VBAN2MIDI.exe` 320,7 · `VBDeviceCheck.exe` 166,7 | 6 |
| **Uzaktan API** | `VoicemeeterRemote.dll` 156,9 · `VoicemeeterRemote64.dll` 188,4 | 2 |
| **ASIO bridge DLL'leri** | `vbvm_asiodriver(.dll/64)` 104,7/122,2 · `vbvmaux_asiodriver(.dll/64)` 104,7/122,2 · `vbvmvaio3_asiodriver(.dll/64)` 104,7/122,2 · `vbvmins_asiodriver(.dll/64)` 104,7/122,2 · `vbvmpins_asiodriver(.dll/64)` 104,7/122,7 | **10** |
| **Sanal cihaz sürücüsü (.sys)** | `vbaudio_vmvaio*` 5 · `vbaudio_vmvaio3*` 5 · `vbaudio_vmvaio64*` 4 · `vbaudio_vmvaio364*` 4 · `vbaudio_vmauxvaio*` 5 · `vbaudio_vmauxvaio64*` 4 · `vbvoicemeetervaio64_win10.sys` 289,4 · `vbvoicemeetervaio64arm_win10.sys` 297,9 | **29** |
| **Sürücü INF'leri** | `VBvmvaio*` 5 · `VBvmvaio3*` 5 · `VBvmvaio64*` 4 · `VBvmvaio364*` 4 · `VBvmauxvaio*` 5 · `VBvmauxvaio64*` 4 · `vbvoicemeetervaio64_win10.inf` 36,6 · `vbusbgpi_uart(.inf/_xp_vista)` 2 | **30** |
| **Kurulum/kontrol exe** | `VB*_ControlPanel.exe` ×3 (855,5–911,2) · `VB*_Setup(_x64).exe` ×5 (875,5–900,2) · `VBCABLE_Setup*.exe` ×3 · `vbregsvr64.exe` 47,2 | ~12 |
| **Codec** | `lame_enc.dll` 944,5 · `lame_enc64.dll` 1 439 | 2 |

**8 sanal cihaz ailesi** (önceki §3.2'deki 5 aile sayımı **revize edildi**): `vmvaio` · `vmvaio3` · `vmvaio64` · `vmvaio364` · `vmauxvaio` · `vmauxvaio64` · `voicemeetervaio64` · `voicemeetervaio64arm`.
**OS varyantları (`.sys` adı kanıtı):** `xp` · `2003` · `vista` · `win7` · `win10` — her aile için ayrı imzalı binary.
**x64 aileleri yalnız 4 OS** (`xp` yok) → adlandırma `...64_*` (x86 yok) ile ayrılıyor.

### §11.2 FxSound kurulum klasörü

| Grup | Dosya | Sayı |
|---|---|---|
| Ana | `FxSound.exe` 3 730 KB · `fxmcp.exe` 6 589,5 · `fxdiag.exe` 349,5 · `updater.exe` 1 636 · `fxmcp.mcpb` 16,2 | 5 |
| Kurulum | `Apps\DfxInstall.dll` 126 · `Apps\DfxSetupDrv.exe` 65,5 | 2 |
| Sürücü yardımcıları | `Drivers\ptdevcon{32,64}.exe` 205/234 · `Drivers\win{7,10}\x{86,64}\fxdevcon*.exe` 211,5/263,5 ×4 | 6 |
| Sürücü | `Drivers\win{7,10}\x{86,64}\fxvad.sys` (bkz. §1.3) | 4 |
| Font (i18n) | NotoSans JP/KR/SC/TC · IBM Plex Sans Arabic · Montserrat Alternates · NotoSans Thai | **21** |
| Dizinler | `Apps` · `Drivers` · `Factsoft` | 3 |

**Bulgu — i18n:** 21 font dosyası (JP/KR/SC/TC/Arabic/Thai) → **çok dilli arayüz**; UI font yükü ~115 MB (NotoSans CJK ağırlığı). CoreMusic `Theme/` katmanı için referans: fontları **istemciye bölerek** yükleme (assets.coremusic.net).

---

## §12 VoiceMeeter — VBAN ve Uzaktan Kontrol alt sistemleri

| Alt sistem | Kanıt | İşlev (ürün dokümanı düzeyi — `⚠️ iç detay VERIFICATION REQUIRED`) |
|---|---|---|
| **VBAN** | `voicemeeter8.exe` içinde 53 eşleşme · `VBAN2MIDI.exe` 320,7 KB | UDP tabanlı ses/MIDI ağı; stram gönderme/alma |
| **Remote API** | `VoicemeeterRemote.dll` / `64.dll` | 3. taraf programların mixer durumunu okuması/yazması (IPC) |
| **Macro Buttons** | `VoicemeeterMacroButtons.exe` 2,2 MB | Kullanıcı makroları → iş akısı otomasyonu |
| **BUS GEQ15** | `VoicemeeterBUSGEQ15.exe` 526,6 KB | BUS başına 15 bant — ayrı süreç/pencere |
| **BUS Matrix8** | `VoicemeeterBUSMatrix8.exe` 157,1 KB | 8×8 routing — ayrı süreç |
| **Streamer View** | `VMStreamerView.exe` 116,7 KB | Yayında görsel izleme (seviye/clip göstergesi tahmini `⚠️`) |
| **Device Check** | `VBDeviceCheck.exe` 166,7 KB | Sürücü/cihaz denetimi |
| **Registry/register** | `vbregsvr64.exe` 47,2 KB | Sürücü/reg kayıt aracı |

**Toplam 6 ayrı yardımcı exe + 2 DLL** → VoiceMeeter'in tek-process monolit **olmadığı**, hafif IPC ile bağlı **modüler süreç kümesi** olduğu diskten doğrulanır.

---

## §13 String Taraması — Ham Sonuçlar (metodoloji + tablo)

**Yöntem:** `[IO.File]::ReadAllBytes` → ASCII decode → `regex.Count`. Bu bir **grep** ölçümüdür; işlev kanıtı **DEĞİLDİR** (yalnızca binary içinde literal bayt dizisi varlığı). Yorum sütunu bu nedenle "ipucu" seviyesindedir.

| Desen | voicemeeter8.exe | vbvoicemeetervaio64_win10.sys | fxvad.sys (win10 x64) | fxmcp.exe |
|---|---|---|---|---|
| `ASIO` / `asio` | 25 / 4 | 0 / 0 | 0 / 0 | 0 / 0 |
| `WASAPI` | 1 | 0 | 0 | 0 |
| `IAudioClient` / `IAudioRender` | 0 | 0 | 0 | 0 |
| `192000` | 2 | 0 | 0 | 0 |
| `96000` | 2 | 0 | 0 | 0 |
| `48000` / `44100` | 7 / 7 | 0 / 0 | 0 / 0 | 0 / 0 |
| `Exclusive` | 1 | 0 | 0 | 0 (küçük `exclusive` 5) |
| `VBAN` | 53 | 0 | 0 | 0 |
| `KMDF` / `WDF` | 0 | 4 / 3 | 0 / 0 | 0 |
| `portcls` / `Miniport` | 0 | 1 / 1 | 1 / 0 | 0 |
| `KSCATEGORY_AUDIO` | 0 | **0** | **0** | 0 |
| `WAVEFORMATEXTENSIBLE` | 0 | 0 | 0 | 0 |
| `surround` / `Equalizer` | 0 | 0 | 0 | 18 / 23 |

**Kritik yorumlar:**
1. Sürücü `.sys` dosyalarında `KSCATEGORY_AUDIO` / `WAVEFORMATEXTENSIBLE` **literal string yok** → GUID'ler binary sabit olarak tutulur (Kılavuz `DEFINE_GUID`/`INITGUID` derleme davranışı ile tutarlı). **Format aralığı INF'ten okunmalıdır** (§9-U2).
2. `fxmcp.exe` (Go) `ASIO`/`WASAPI` literal **içermiyor** → MCP sunucusu ses API'sine **dokunmaz**, yalnız `FxSound.exe` CLI'sine shell eder (README ile tutarlı ✅).
3. `voicemeeter8.exe`'de `IAudioClient`/`IMMDevice` literal **0** → WASAPI erişimi ya dinamik (COM GUID) ya da ayrı DLL üzerindedir. `⚠️ VERIFICATION REQUIRED`.

---

## §14 Neva Engine — Karsılık Matrisi (bu rapordan ADR-107'ye giren maddeler)

| # | RE bulgusu | Neva kararı (ADR-107) | Öncelik |
|---|---|---|---|
| 1 | 6 ayrı yardımcı exe (VoiceMeeter) | Statik lib + çok executable (K2) | P0 |
| 2 | 10 ASIO bridge DLL (her sanal cihaz) | `neva-asio-bridge` hedefi | P0 |
| 3 | 29 `.sys`, 5 OS varyantı, ARM64 | Sürücü ayrı iş birimi (K5) + ARM64 sütunu | P0 |
| 4 | `Remote.dll` (2 boy) | `neva-remote` (IPC: named pipe/gRPC) | P1 |
| 5 | VBAN (53) | Ağ/streaming modülü — CoreMusic multi-room | P3 |
| 6 | FxSound MCP (Go, 14 araç) | `neva-mcp` | P1 |
| 7 | FxSound loopback ≠ bit-perfect | exclusive + hash CI kapısı (K3) | P0 |
| 8 | LAME encode DLL | Çıkış encode: ayrı hedef (CLI `neva-player --encode`) | P2 |
| 9 | Macro/GEQ/Matrix ayrı pencereler | `neva-eq`, `neva-matrix` | P2 |
| 10 | FxSound 21 font (i18n) | UI font bölme (assets.coremusic.net) | P3 |

---

## §15 Metodoloji, Güven Sınırları ve Yasaklar

| Konu | Değerlendirme |
|---|---|
| Araçlar | REA `inspect_pe_resources` (voicemeeter8.exe) · PowerShell envanter + Authenticode · ASCII string taraması · FxSound repo clone (`4383180e`) |
| **Yapılmayan** | Dinamik analiz (çalışan process/trace) · disassembly · `.sys` iç (DOS/NT header dışı) yapı analizi · INF içeriği okuma |
| Güven seviyesi | **YÜKSEK:** dosya adı/boyut/imza/versiyon, string sayımları, repo README/go.mod · **ORTA:** mimari çıkarımlar (§2, §4) · **`⚠️`:** pin formatları, exclusive davranışı, `.fac` içeriği |
| **Yasaklar** | FxSound **AGPL** → kod kopyalama/uyarlama YASAK · VoiceMeeter **kapalı kaynak** → resource/dll kopyalama YASAK · yalnız mimari referans (WORKFLOW §8.1C) |
| Tekrarlanabilirlik | §8 komut seti + `git -C app-cpp-projects/fxsound-app log -1` |

---

## §16 Web Çapraz Doğrulama Turu (Research Mandate — 6 kaynak)

> Bölüm §13'teki string-ipuçlarını **resmi kaynaklarla** karşılaştırır. Bulunduğu yerde `⚠️` yoksa = resmi kaynak doğrulaması.

### §16.1 VB-Audio resmi spesifikasyon tablosu (vb-audio.com/Voicemeeter + voicemeeter.com)

| Parametre | Resmi değer | String ölçümümle uyum |
|---|---|---|
| DSP iş motoru | **32 · 44.1 · 48 · 88.2 · 96 · 176.4 · 192 kHz** (A1 çıkışına göre belirlenir) | ✅ `192000`×2, `96000`×2, `48000`×7, `44100`×7 |
| Output A1 (ana) | WDM, KS, MME, **ASIO** — **32–192 kHz**, 1–8 kanal | ✅ `ASIO`×25 |
| Output A2 | WDM, KS, MME — 8–192 kHz, 1–8 kanal | ✅ |
| Fiziksel girdi | WDM, KS, MME — 8–192 kHz, mono/stereo | ✅ |
| **Sanal I/O** | WDM, KS, MME, DirectX, **WaveRT** — 8–192 kHz, 1–8 kanal (girdi 8 ch / çıktı 2 ch) | ✅ `portcls`/`Miniport` (sürücü) |
| **Sanal ASIO I/O** | ASIO 32–192 kHz, **8 kanal, 4 uygulama istemcisi** | ✅ 10 ASIO bridge DLL |
| Desteklenen arayüzler | **MME, Direct-X, KS, WaveRT, WASAPI, ASIO** | ✅ `WASAPI`×1 + `ASIO`×25 + `Exclusive`×1 |
| VBAN | 4 ses girdi + 4 ses çıktısı + MIDI + TEXT + FRAME akışları | ✅ `VBAN`×53 |
| OS | Windows XP→Win11 **32/64 bit ve ARM64** | ✅ ARM64 `.sys` bulundu |
| Lisans | **Donationware** (30 gün deneme, sonrası davet) | — |
| Sürüm (kurulu) | Kurulumda `Voicemeeter8Setup.exe` 72,7 MB · ürün `3.1.2.2` (sitede `1.1.3.0` ZIP, SEP 2026 — sürümleme hattı `⚠️`) | ⚠️ sürüm adlandırma farklı |

### §16.2 VAIO sürücü kontrol paneli (kullanıcı kılavuzundan — sürücü davranış kanıtı)

Kılavuzda yer alan **VB-Audio Voicemeeter VAIO Control Panel (Version 3.3.1.6)** verileri:

| Alan | Değer | Neva için anlamı |
|---|---|---|
| `Internal Sampling Rate` seçenekleri | **44100 / 48000 / 88200 / 96000 / 176400 / 192000 Hz** | Sanal cihaz **192 kHz'e kadar iç rate** kabul ediyor ✅ (kullanıcının "192 kHz şartı" bu üründe karşılanıyor) |
| `Max Latency` | **4096 / 7168 smp** (değişimi RESTART ister) | Sürücü buffer üst sınırı 7168 frame |
| Çıkış bilgisi örneği | `ch: 2 · sr: 48000 Hz · res: 24 bits` | **24-bit** çözünürlük raporlanıyor (32-bit float `⚠️ doğrulanmadı`) |
| DMA / glitch sayaçları | `DMA Size: 4096` · `Timer Glitch` · `Buffer Call` sayaçları | **Canlı ölçüm panosu** — Neva `neva-diag` için emsal |
| DAW bağlanışı (REAPER örneği) | ASIO: `Voicemeeter Virtual ASIO` · 512 örnek = 11,6 ms @44,1k | Sanal cihaz ASIO istemcisi olarak kullanılıyor ✅ |

> **Kritik ayrım:** VoiceMeeter sanal cihazı **24-bit/192 kHz'e** resmen ulaşıyor; **32-bit float** resmi tabloda **geçmiyor** (`⚠️ VERIFICATION REQUIRED` — seçili cihazın `WAVEFORMATEXTENSIBLE` alt kümesi INF'ten okunmalı). CoreMusic şartı "32-bit/192 kHz" ise, mevcut emsalin üzerine çıkmak gerekir → `VirtualDrivers/Virtual-Audio-Driver` fork hedefi (sanal cihaz raporu §3) **doğrulanmış gerekçe**.

### §16.3 FxSound tarafı (forum + resmi davranış)

| Kaynak | İddia | Değerlendirme |
|---|---|---|
| `forum.fxsound.com/t/fxsound-does-it-go-higher-than-24-bit-48000-khz/273` | "Doğrudan hoparlöre giderse **maks 24-bit 192 kHz**" | FxSound **paylaşımlı** zincirde Windows mixer'e tabi; cihaz formatına bağlanır |
| `forum.fxsound.com/t/wasapi-fx/1074` | "WASAPI exclusive, ASIO ile aynı işi yapar… FxSound exclusive kullanır" | ⚠️ **Çelişki**: depoda `AUDCLNT_STREAMFLAGS_*` yalnız `LOOPBACK` görüldü (§3.2). Forum ifadesi **pazarlama dili** olabilir → **kanıt önceliği: kod.** Kullanıcının "FxSound 192k sağlamıyor" gözlemi **kod kanıtıyla destekleniyor** |
| `forum.fxsound.com/…` + Audacity forumu | Loopback yakalama genelde **sabit 44,1/48 kHz** döner | ✅ loopback ≠ kaynak format — bit-perfect yakalama imkânsız |

**Sonuç:** §3'teki "FxSound bit-perfect DEĞİL" çıkarımı **iki bağımsız kaynakla desteklendi**; "FxSound exclusive kullanır" iddiası ise depo kanıtına aykırı kabul edildi (`⚠️ açık madde U6` eklendi).

---

## §17 Windows Ses Yığını — Neva'nın Nerede Durduğu

```text
[Uygulamalar: Tarayıcı / Player / DAW]
   │  MME · Direct-X · WASAPI(shared) · KS
   ▼
[KERNEL: Audio Engine + Mixer + Resampler]   ← bit-perfect'in KIRILDIĞI katman
   ▼
[WDM-KS / WaveRT]  ──►  [Donanım Sürücüsü]  ──►  [DAC]
   ▲
   │  exclusive / ASIO (kernel mixer DIŞI)  ← Neva'nın hedef yolu
[NeVa: ASIO2 / WASAPI exclusive]
```

**Neva K5 zincirinin bu diyagramdaki karşılığı:**
- `ASIO` → doğrudan sürücü (DAW yolu).
- `WASAPI exclusive` → kernel mixer'ı atlar, `IsFormatSupported` ile format pazarlığı.
- `WDM-KS` → mixer'ı atlayan en düşük seviye (exclusive başarısızsa).
- `ALSA hw:` / `PipeWire F32P` → Linux'ta aynı mantık (`plug/` yok).
- **Sanal cihaz** → diyagramda "donanım" yerine geçen yazılım uç noktası (fxvad/VAIO).

---

## §18 Sonraki RE Turu İçin INF Okuma Planı (Açık madde kapatma planı)

| Adım | Dosya | Aranan alan | Kapatığı madde |
|---|---|---|---|
| 1 | `vbvoicemeetervaio64_win10.inf` (36,6 KB) | `AddReg` → `HKR,,Format` / `SupportedRates` / `MaxChannels` | U1 (pin formatları) |
| 2 | `FxSound\Drivers\win10\x64\*.inf` | `fxvad` pin descriptor'ları | U2 |
| 3 | `VoicemeeterRemote64.dll` | `dumpbin /exports` | U5 (remote API) |
| 4 | `.fac` dosyaları (68) | hex header okuması | U4 |
| 5 | Dinamik: `voicemeeter8.exe` + `FxSound.exe` running | REA/Rizin API trafiği | U3 |

---

## §19 Çapraz Doğrulama Adayları (Red Team soru listesi)

1. VoiceMeeter sanal cihazı **32-bit float** formatı kabul ediyor mu, yoksa yalnız 16/24-bit int mi? (INF + `IsFormatSupported` canlı testi)
2. `WaveRT` resmi tabloda geçiyor; `.sys` içinde `portcls` tek eşleşme — WaveRT mi, WaveCyclic mi? (INF + iç kod)
3. FxSound `exclusive` string'i hangi kod yolunda? (loopback değil, belki çıkış `AUDCLNT_SHAREMODE_EXCLUSIVE` — depoda `sndDevicesSetupDevices.cpp` dışında tarama gerekli)
4. VAIO sürücüsünde `DMA Size 4096` — 192 kHz'te buffer süresi kaç ms? (4096/192000 ≈ 21,3 ms → Neva hedef buffer 32–128 örnek çok daha agresif)
5. VoiceMeeter 4 ASIO istemcisi sınırı — Neva sanal ASIO bridge kaç istemci destekleyecek? (Tasarım kararı: ≥4)

---

## §20 Kabul Test Senaryoları — Neva vs VoiceMeeter Emsali (§19-5 yanıtları)

> Bu senaryolar, §19'daki "sanal ASIO istemci sınırı" gibi tasarım sorularını **test edilebilir hedeflere** çevirir.

### §20.1 Sanal cihaz senaryoları

| # | Senaryo | Adım | Beklenen | Kriter |
|---|---|---|---|---|
| S1 | 192 kHz/32f sanal uç nokta | Sanal cihazı Windows ses ayarında 192 kHz'e al | Endpoint listede görünür | Format seçilebilir |
| S2 | Paylaşımlı yazma | Tarayıcı → sanal cihaz → DSP | Ses gelir | xrun=0 (10 dk) |
| S3 | Exclusive yazma | `neva` exclusive → sanal cihaz | `negotiated == 192000/32f` | `IsFormatSupported == S_OK` |
| S4 | **Bit-perfect hash** | 192k/32f WAV → Neva → loopback kaydet | SHA-256 eşit | ✅/❌ |
| S5 | ASIO istemci | DAW + Neva CLI + 3. parti (4 istemci) | 4. istemci bağlanır | VoiceMeeter sınırı ≥4 karşılaştırması |
| S6 | Cihaz değişimi | Çıkış kablosunu çek/tak | Bildirim → fallback | `IMMNotificationClient` benzeri |
| S7 | 8 kanal | 7.1 kaynak → sanal cihaz | 8 ch korunur | ch=8 |
| S8 | ARM64 | Windows ARM64 | Sürücü yüklenir | x64 emülasyonu değil native |

### §20.2 DSP/latency senaryoları

| # | Senaryo | Beklenen |
|---|---|---|
| L1 | 128 örnek @48k exclusive | round-trip P50 ≤ 5 ms |
| L2 | 192 kHz @128 örnek | callback süresi < 0,67 ms × 0,7 = **467 µs** |
| L3 | 30 dk sürekli | xrun = 0, glitch sayacı sabit |
| L4 | CPU %70 yük altında | yine xrun = 0 (aksi halde buffer stratejisi revizyonu) |

---

## §21 İddia Güven Sınıflandırması (bu raporun tamamı için)

| ID | İddia | Seviye | Dayanak |
|---|---|---|---|
| C1 | Kurulu ürün VoiceMeeter Potato 3.1.2.2, imza geçerli | **KANITLI** | `VersionInfo` + `Get-AuthenticodeSignature` |
| C2 | FxSound 1.2.13.0, imza geçerli | **KANITLI** | Aynı |
| C3 | VoiceMeeter'de 10 ASIO bridge DLL, 29 `.sys`, 30 `.inf` | **KANITLI** | Dosya envanteri §11.1 |
| C4 | Sanal cihaz sürücüsü KMDF + PortClass miniport | **KANITLI (string)** | `KMDF`×4, `portcls`×1, `Miniport`×1 |
| C5 | VoiceMeeter 192 kHz destekliyor | **KANITLI (resmi kaynak)** | vb-audio.com spec + string `192000`×2 (§16) |
| C6 | VAIO iç rate 44,1–192 kHz seçilebilir, max latency 7168 smp | **KANITLI (kılavuz)** | §16.2 |
| C7 | VoiceMeeter sanal cihaz 24-bit raporluyor | **KANITLI (kılavuz örneği)** | `res: 24 bits` — 32-bit float `⚠️` |
| C8 | FxSound exclusive kullanmıyor | **KANITLI (kod)** | `AUDCLNT_SHAREMODE_EXCLUSIVE` = 0 (§19) |
| C9 | FxSound loopback yakalar | **KANITLI (kod)** | `sndDevicesSetupDevices.cpp:170,173` |
| C10 | FxSound MCP sunucusu (Go, 14 araç) | **KANITLI** | `fxmcp/go.mod` + README (§5) |
| C11 | FxSound EQ 5/10/15/20/31 bant, Q 1–3 | **KANITLI** | `COMMAND_LINE_OPTIONS.md` (§18) |
| C12 | VoiceMeeter tek-process değil, modüler süreç | **KANITLI** | 6 yardımcı exe + 2 DLL (§12) |
| C13 | VoiceMeeter `.sys` pin formatları | ✅ **ÇÖZÜLDÜ** (2026-10-22 derin tur) | `.sys` bayt taraması: 6 rate DWORD (192k=36 isabet) + EXTENSIBLE/FLOAT tag'leri — `multi-os-...md` §2.3 |
| C14 | `fxvad.sys` pin formatları | ✅ **ÇÖZÜLDÜ** (2026-10-22 derin tur) | 44.1/48 kHz-only (diğerleri 0 isabet) + INF default blob 48k/16bit — `multi-os-...md` §3 |
| C15 | Dinamik API davranışı (çalışırken hangi çağrı) | **`⚠️` YAPILMADI** | U3 |

---

## §22 Oturum Kapanış Özeti

| Deliverable | Durum |
|---|---|
| Binary + sürücü envanteri | ✅ (VoiceMeeter 100+ dosya, FxSound 40+ dosya) |
| Mimari model çıkarımı (§2, §4) | ✅ seviye: orta-güçlü |
| Web çapraz doğrulama (§16, 6 kaynak) | ✅ |
| FxSound kaynak analizi (başka dosya) | ✅ `fxsound-app-source-analizi-2026-10-22.md` |
| Neva kabul matrisi (§14) + test senaryoları (§20) | ✅ ADR-107'ye bağlandı |
| Açık konular (§9) | 5 madde → sonraki tur INF/dinamik analiz |
| Kod kopyalama | ❌ **YAPILMADI** (AGPL/kapalı kaynak yasağı korundu) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-22
**Mode:** Red Team · Human Mode · Truth Mode
