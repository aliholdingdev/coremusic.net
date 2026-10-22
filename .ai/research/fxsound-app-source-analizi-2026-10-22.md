---
title: "FxSound App — Açık Kaynak Kaynak Mimari Analizi (github.com/fxsound2/fxsound-app)"
type: research
category: reverse-engineering
status: draft
version: 1.0.0
date: 2026-10-22
authority: "Windows Software Engineer — L0 Platform"
tier: 3
domain: audio
ssot: false
risk: low
owner: "MO"
depends-on: ["app-cpp-projects/fxsound-app"]
---

# FxSound App — Kaynak Mimari Analizi

**Repo:** `https://github.com/fxsound2/fxsound-app.git` · **Clone:** `app-cpp-projects/fxsound-app` · **Commit:** `4383180e02ff435d191f71220e8da41f9fa06951` (2026-10-22, `Merge pull request #659`)
**Lisans:** **AGPL v3.0** (LICENSE satır 1) · **JUCE** (AGPL v3.0) · Advanced Installer Professional (kurulum)
**Amaç:** CoreMusic'in Neva Engine'i için **mimari referans** (WORKFLOW §8.1C — kod kopyalama/uyarlama **YASAK**; AGPL kaynak paylaşım yükümlülüğü doğurur).

---

## §1 Depo Yapısı (disk ölçümü)

```text
fxsound-app/
├── audiopassthru/         ← WASAPI ses cihazı katmanı (include/ + src/)
├── dsp/                   ← DfxDsp + ptechDsp (DSP algoritmaları) + ptutil + Win32Main
├── fxsound/               ← JUCE GUI uygulaması (Source/ + JuceLibraryCode/ + Project/)
├── fxmcp/                 ← Go MCP sunucusu (AI kontrol katmanı)
├── fxdiag/                ← Tanılama aracı
├── Installer/             ← Advanced Installer + DfxInstall (COM/DLL installer)
├── bin/                   ← x64 · x86 · arm64 çıktıları + BonusPresets
├── release/               ← arm64 dahil yayın artefaktları
├── Resources/             ← git submodule → fxsound2/Resources (branch: main)
├── web/dsp-tutorial/      ← DSP eğitimi içeriği
├── docs/                  ← COMMAND_LINE_OPTIONS.md vb.
├── .github/workflows/     ← CI
└── README.md · CONTRIBUTING.md · LICENSE · CLAUDE.md · .gitmodules
```

**Dosya uzantı dağılımı (`.git` hariç):**

| Uzantı | Sayı | Yorum |
|---|---|---|
| `.cpp` | 180 | Ana dil C++ |
| `.h` | 165 | — |
| `.c` | 46 | Legacy DSP çekirdekleri (ptechDsp) |
| `.svg` | 69 | Vektör ikonlar |
| `.fac` | 68 | Fabrika verisi (preset/katsayı — `⚠️ VERIFICATION REQUIRED`) |
| `.go` | 32 | **fxmcp MCP sunucusu** |
| `.exe` | 30 | Araç/bin çıktıları |
| `.sys` | 23 | **Sürücü binary'leri (fxvad.sys varyantları) — kaynakla birlikte depoda** |
| `.inf` | 21 | Sürücü kurulum bilgileri |
| `.ttf` / `.otf` | 21 / 15 | Font varlıkları |

**Bulgu:** Depo, **sürücü binary'lerini de kaynakla birlikte tutuyor** (23 `.sys`) → sürücü kaynak kodu bu depoda **YOK** (yalnız binary+INF). Sürücü ayrı/imzalı bir dağıtım artefaktıdır.

---

## §2 Üç Bileşenli Uygulama Mimarisi (README kanıtı)

README §Build Instructions bileşenleri açıkça tanımlar:

| # | Bileşen | Görev | Teknoloji |
|---|---|---|---|
| 1 | **FxSound GUI app** | Kullanıcı arayüzü | **JUCE 6.1.6** (x64/x86) · ARM64 için "latest JUCE" |
| 2 | **audiopassthru module** | Uygulamanın ses cihazlarıyla etkileşimi | C++ (WASAPI) |
| 3 | **DfxDsp module** | Ses işleme | C/C++ (legacy ASM dahil: `dsp/ptechDsp/Dly8/DLY8.ASM`) |

Ek araçlar: `fxdiag.exe` (tanılama), `fxmcp.exe` (MCP), `DfxInstall` (COM installer DLL'i).

**Build sistemi:** Visual Studio 2022 + Windows SDK + **Projucer** (JUCE proje üretimi) → `fxsound/Project/FxSound.sln`. ARM64 için ayrı `ProjectARM/`.

---

## §3 audiopassthru — Ses Cihazı Katmanı

### §3.1 Dosya envanteri (src/)

`AudioPassthru.cpp`, `AudioPassthruPrivate.cpp`, `sndDevices*` ailesi, `Mth*` (matematik yardımcıları: `MthBuffer`, `MthStat`, `MthTrig`, `MthWarp`, `MthLinearRegression`, `Mthfreq`), `pstr*` (string), `operatingSystem*.cpp` (OS ses olayları).

### §3.2 API kanıtları (satır referanslı)

| Dosya | Satır | Kanıt |
|---|---|---|
| `audiopassthru/include/sndDevices.h` | 223 | `IMMDeviceEnumerator *_pEnumerator;` |
| `audiopassthru/src/sndDevices/sndDevicesGet.cpp` | 410 | `IAudioClient *pAudioClient;` |
| `audiopassthru/src/sndDevices/sndDevicesInit.cpp` | 51 | `IMMDeviceEnumeratorPtr pEnumerator = NULL;` |
| `audiopassthru/src/sndDevices/sndDevicesReInit.cpp` | 347 | `IMMDeviceEnumeratorPtr pEnumerator = NULL;` |
| `audiopassthru/src/sndDevices/sndDevicesSetupDevices.cpp` | 75 | `pCaptureDevice->Activate(IID_IAudioClient, CLSCTX_ALL, NULL, (void**)&…pAudioClientCapture);` |
| `audiopassthru/src/sndDevices/sndDevicesSetupDevices.cpp` | 170 | `StreamFlags = AUDCLNT_STREAMFLAGS_LOOPBACK;` |
| `audiopassthru/src/sndDevices/sndDevicesSetupDevices.cpp` | 173 | `StreamFlags = AUDCLNT_STREAMFLAGS_LOOPBACK\|AUDCLNT_SESSIONFLAGS_DISPLAY_HIDE;` |

**Çıkarım 1 (YÜKSEK güven):** FxSound **yalnızca WASAPI** kullanır — depoda `ASIO`/`asio` eşleşmesi yoktur. Yakalama `AUDCLNT_STREAMFLAGS_LOOPBACK` ile, çıkış sanal cihaz (`fxvad`) endpoint'ine yazılır.
**Çıkarım 2 (YÜKSEK güven):** Loopback + paylaşımlı zincir → **bit-perfect değil** (Windows mixer/resampler devrede). Bu, CoreMusic'in 32-bit/192 kHz bit-perfect hedefiyle **uyumsuz**.

### §3.3 Neva Engine için çıkarım

| FxSound yaklaşımı | Neva karşılığı |
|---|---|
| Tek WASAPI backend | **Backend soyutlama** (ASIO + WASAPI exclusive + WDM-KS + ALSA + CoreAudio) — ADR-107 |
| Loopback tap (başka uygulama sesini yakala) | Neva'da **"system audio tap"** özelliği olarak ayrı backend politikası (bit-perfect değil, bilinçli) |
| `IMMDeviceEnumerator` cihaz listeleme/dinleme | Neva `DeviceEnumerator` port'u için doğrudan referans |
| `AUDCLNT_SESSIONFLAGS_DISPLAY_HIDE` | Panelin Windows ses çubuğunda görünmemesi — UX detayı, Neva'da uygulanabilir |

---

## §4 dsp — DSP Zinciri

### §4.1 Dizin ağacı

```text
dsp/
├── DfxDsp.cpp · DfxDspEq.cpp · DfxDspPreset.cpp · DfxDspPrivate.cpp · DfxDspRegistry.cpp
├── u_DfxDsp.h · include/DfxDsp.h
├── ptComSftDfx/Comsftwr.c · ComsftwrCPP.cpp · u_comSftwr.h   ← COM software wrapper
├── ptutil/                                                      ← yardımcılar
├── Win32Main/                                                   ← Win32 giriş/ana döngü
└── ptechDsp/
    ├── Aural/   (Aural0/Auralp.c · AuralpInt.c · Aural032/Auralp32.c · Aur_num.h)
    ├── Dly8/    (DLY8.ASM · DLY8.C · Dly8p.c · DLYBK8.C · dly_num.h)
    ├── Dly32/   (Dly832/dly8p32.c · dly_num.h)
    ├── Lex/     (Lex16/Lex16.c · Lex32/Lex32.c, Lex32loc.c, Lex32org.c)
    ├── Maximizer/
    ├── Peq/
    ├── Play/
    └── wide/
```

### §4.2 Zincir elemanları ve Neva karşılıkları

| FxSound modülü | İşlev | Neva Engine karşılığı (hedef) |
|---|---|---|
| `Peq` | Parametrik EQ | **31-band PEQ + grafik EQ** (1/3 oktav) |
| `Lex` (Lexicon tarzı, 16/32-bit sürümler) | Reverb | **Psikoakustik reverb** (düğün salonu/konser alanı/stüdyo/kulüp) |
| `Aural` | Mekân auralization | Mekan akustiği simülasyonu (vault `electronic/dsp/`) |
| `Dly8`/`Dly32` | Gecikme (ASM + C) | Gecikme efekti (düşük öncelikli) |
| `wide` | Stereo genişletme | Stereo genişletme |
| `Maximizer` | Loudness/brickwall | **True Peak brickwall limiter** (THD+N <%0.01, SNR >100 dB) |

**Ölçüm:** 8 DSP modülü · `dly_num.h` / `Aur_num.h` gibi **sabit-katsayı başlıkları** (algoritma parametreleri compile-time sabit).

---

## §5 fxmcp — AI/MCP KONTROL KATMANI (en yüksek değerli referans)

### §5.1 Ne olduğu (README kanıtı — birebir özet)

> "An MCP (Model Context Protocol) server that exposes FxSound's diagnostics (`fxdiag.exe`) and control surface (`FxSound.exe`'s command-line options, driven through `FxController::applyConfig`) as MCP resources, tools, and prompts — so an AI assistant can inspect the current audio environment and drive presets, EQ, effects, and output device selection."
> — Windows-only; shells out to `FxSound.exe`/`fxdiag.exe`; registry/process APIs ile sürücü.

### §5.2 Teknoloji (go.mod kanıtı)

| Alan | Değer |
|---|---|
| `module` | `fxmcp` |
| `go` | **1.26.5** |
| MCP SDK | **`github.com/modelcontextprotocol/go-sdk v1.6.1`** |
| Yardımcılar | `golang.org/x/sys v0.41.0`, `golang.org/x/text v0.38.0` |
| Indirect | `jsonschema-go`, `segmentio/asm`, `segmentio/encoding`, `uritemplate`, `oauth2` |

### §5.3 Paket yapısı

```text
fxmcp/
├── cmd/fxmcp/       main.go · main_test.go · prompts_test.go · security_test.go · write_tools_test.go
│                    resource_windows_{386,amd64,arm64}.syso · versioninfo.json
├── internal/fxsound/ config.go · diagnostics.go · effects.go · equalizer.go · errors.go
│                     language.go · locate.go · presets.go · process.go · running.go · status.go
│                     (+ *_test.go dosyaları: config/effects/equalizer/errors/language/presets/process)
├── mcpb/            MCP bundle manifesti
├── build.ps1 · build-mcpb.ps1
└── go.mod · go.sum · README.md
```

**Test disiplini:** `internal/fxsound/*.go` neredeyse tamamının `*_test.go` karşılığı var; ayrıca `security_test.go` (güvenlik denetimi) ve `write_tools_test.go` (AI araç yazım testleri).

**Çıkarım:** FxSound, AI agent'lara **EQ/preset/efekt/teşhis/cihaz seçimi** araçlarını MCP üzerinden sunuyor; sürüm/artefakt yapısı (`bin\<arch>\fxmcp.exe` yanında `FxSound.exe`) çok-mimari (x64/x86/arm64).

### §5.4 CoreMusic için doğrudan karşılık

| FxSound fxmcp | Neva karşılığı (öneri) |
|---|---|
| `fxmcp` (Go, MCP SDK) | **`neva-mcp`** hedefi — dil kararı ADR-107'de (Go vs Node/PHP bridge) |
| `equalizer.go` | `neva_eq_set(band, freq, gain, q)` MCP aracı |
| `presets.go` | `neva_preset_list/load/save` |
| `effects.go` | `neva_reverb_set`, `neva_limiter_set`, `neva_wide_set` |
| `diagnostics.go` | `neva_status()` — device/rate/xrun ölçümleri |
| `security_test.go` | AI araç yazım güvenlik denetimi (prompt-injection / yetki) |
| `mcpb/` bundle | AI agent paketleme referansı |

> **Eşzamanlılık:** CoreMusic `.ai/AGENTS.md` §4'te 11 uzmanlık alanlı AI agent sistemi tanımlar; `.ai/engine.md` orkestrasyon motoru PLANNED. FxSound örneği, bu vizyonun **gerçek üretim örneğidir** → `neva-mcp` hedefi ADR-107'ye eklendi.

---

## §6 Installer & Sürücü Dağıtımı

| Yol | Kanıt |
|---|---|
| `Installer/DfxInstall/DfxInstall.sln` + `DfxInstall.cpp/.h/.rc`, `dllmain.cpp` | **COM DLL tabanlı installer** (Advanced Installer Professional) |
| `Installer/Drivers/` | Sürücü paketleri (fxvad.inf/.sys/.cat) |
| `bin/{x64,x86,arm64}` | Üç mimari için çıktı |
| `release/arm64` | ARM64 yayını |

**Ders:** Sürücü kurulumu ayrı bir COM DLL (`DfxInstall`) ile yürütülüyor; GUI'den bağımsız. CoreMusic sanal cihaz işi için **ayrı kurulum birimi** kuralı desteklenir.

---

## §7 Neva Engine — çıkarılan mimari dersler (özet)

| # | Ders | Aksiyon |
|---|---|---|
| 1 | 3 bileşen ayrımı: **GUI / device-passthrough / DSP** | Neva: `neva-host` / backend / `neva-core` ayrımı (ADR-107) |
| 2 | WASAPI loopback tap ≠ bit-perfect | Bit-perfect yol = exclusive + sanal cihaz (başka plan) |
| 3 | DSP modülleri dosya-başına ayrılmış, sabitler header'da | `neva-core/dsp/{eq,reverb,limiter,wide}/` + `*_params.h` |
| 4 | **AI/MCP kontrol katmanı** ayrı dilde (Go) ve testli | `neva-mcp` hedefi + tool-security testleri |
| 5 | Sürücü binary + INF depoda, kaynak ayrı | Sürücü = ayrı repo/iş birimi |
| 6 | Çok mimari (x64/x86/arm64) çıktı | Neva toolchain matrisine ARM64 sütunu |
| 7 | **AGPL** lisansı | ⚠️ KOD KOPYALAMA/UYARLAMA **YASAK** — yalnız mimari referans |

---

## §8 Açık konular

| # | Madde | Durum |
|---|---|---|
| U1 | `DfxDsp` API sözleşmesi (`include/DfxDsp.h`) içeriği | ✅ **KAPANDI** (§19.1 — gerçek yol `dsp/include/DfxDsp.h`, 79 satır tam okundu) |
| U2 | `FxController::applyConfig` CLI sözleşmesi (`docs/COMMAND_LINE_OPTIONS.md`) | OKUNMADI — sonraki oturum |
| U3 | `.fac` dosyalarının formatı | `⚠️ VERIFICATION REQUIRED` |
| U4 | `Resources` submodule içeriği (clone edilmedi) | `⚠️ VERIFICATION REQUIRED` |

---

## §9 fxmcp — MCP YÜZEY KATALOĞU (README kanıtı, birebir)

### §9.1 Taşıma ve çözümleme

| Konu | Kanıt |
|---|---|
| Protokol | **MCP over stdio** (JSON-RPC stdin) |
| Çıktı yolu | `go build -o dist\fxmcp.exe .\cmd\fxmcp` veya `..\bin\x64\fxmcp.exe` |
| Konumlandırma | Program Files default konumu → Windows uninstall **registry** → `PATH` sırasıyla denenir |
| Başlangıç stderr | `fxmcp: resolved FxSound.exe=… fxdiag.exe=…` |
| Hata satırı | `warning: could not locate ...` → resources/tools başarısız olur |
| Host entegrasyonu | Claude Desktop: `%APPDATA%\Claude\claude_desktop_config.json` → `mcpServers.fxsound.command` |

### §9.2 Resources (6 URI)

| URI | İçerik |
|---|---|
| `fxsound://diagnostics` | Windows playback cihaz + audio session anlık görüntüsü (`fxdiag.exe --json`) |
| `fxsound://status` | Canlı tam durum: power, presets, output, equalizer, effects |
| `fxsound://presets` | Seçili preset + yerleşik/kullanıcı preset listeleri |
| `fxsound://equalizer` | Band sayısı, band başına freq/gain, master gain, volume leveling, **filter Q**, balance |
| `fxsound://effects` | Clarity / ambience / surround / dynamicboost / bass (0-10 ölçeği) |
| `fxsound://docs/command-line-options` | Otorite seçenek referansı (aralıklar, yuvarlama, preset komut kuralları) |

### §9.3 Tools (14 araç, 4 kategori)

| Kategori | Araçlar | Çalışma koşulu |
|---|---|---|
| Salt-okunur | `fxsound_is_running`, `fxsound_get_status` | Her zaman |
| Tek-ayar (tek başına) | `fxsound_set_power`, `fxsound_select_preset`, `fxsound_set_output_device` | FxSound **çalışmasa da** çalışır |
| Preset yönetimi | `fxsound_save_preset`, `fxsound_overwrite_preset`, `fxsound_undo_preset`, `fxsound_rename_preset`, `fxsound_delete_preset` | **Çalışan örnek** gerektirir |
| Toplu ayar | `fxsound_set_eq_bands`, `fxsound_set_eq_band_gains`, `fxsound_set_effects` | Çalışan örnek gerektirir |
| Kompozit | `fxsound_apply_settings` | **Tek atomik çağrıda** tüm flag yüzeyi |

### §9.4 Prompts (4 şablon)

| Prompt | Argüman |
|---|---|
| `diagnose-audio-issue` | `symptom` |
| `tune-for-scenario` | `scenario` |
| `switch-listening-profile` | `profile` |
| `create-preset-from-current` | `name` |

> **Mimari ders (CoreMusic):** EQ, `filter Q`, `balance`, `master gain` gibi **çoklu parametre kümesi** tek `apply_settings` kompozitinde atomik uygulanıyor → Neva `neva-mcp` için `neva_apply_settings` aracı tasarımına doğrudan girdi. Ayrıca "tek-ayar araçları süreçsiz, toplu araçlar süreçli" ayrımı **güvenlik/yetki modeli** olarak kullanışlıdır.

---

## §10 JUCE GUI KATMANI (fxsound/)

### §10.1 Dosya envanteri

| Dosya | Boyut | Rol |
|---|---|---|
| `fxsound/Source/Main.cpp` | 12 604 B | Uygulama girişi (`START_JUCE_APPLICATION` benzeri) |
| `fxsound/Source/MainComponent.cpp` | 5 304 B | Ana bileşen |
| `fxsound/Source/MainComponent.h` | 2 282 B | — |
| `fxsound/Source/GUI/` | dizin | Pencere/bileşen ağacı |
| `fxsound/Source/Utils/` | dizin | Yardımcılar |
| `fxsound/JuceLibraryCode/` | dizin | Projucer üretimi JUCE sarmalayıcıları |
| `fxsound/Project/FxSound.sln` | — | VS2022 çözümü (x64/x86) |
| `fxsound/ProjectARM/` | — | ARM64 proje varyantı |

### §10.2 GUI ↔ motor sınırı

```text
[JUCE GUI (FxSound.exe)]
      │  command-line seçenekleri / FxController::applyConfig
      ▼
[audiopassthru (WASAPI)] ──► [DfxDsp/ptechDsp] ──► [fxvad.sys sanal cihaz]
      ▲
      └── fxdiag.exe --json  (tanılama) ── fxmcp.exe (MCP, AI)
```

**Kanıt:** fxmcp README — kontrol yüzeyi "FxSound.exe's command-line options, driven through `FxController::applyConfig`" cümlesiyle tanımlanır → **GUI ile motor arasındaki tek kapı bir CLI/Controller sözleşmesidir**. Bu, Neva için `neva-host` ↔ `neva-core` IPC/CLI sınırına birebir örnektir.

---

## §11 Docs & CI

| Dosya | İçerik |
|---|---|
| `docs/COMMAND_LINE_OPTIONS.md` | **Otorite** CLI seçenek referansı (aralıklar, yuvarlama, preset komut kuralları) |
| `README.md` | 3 bileşen + build adımları |
| `CONTRIBUTING.md` | Katkı kuralları |
| `.github/workflows/` | CI (build) |
| `.gitmodules` | `Resources` → `fxsound2/Resources` (branch main) |
| `CLAUDE.md` | AI asistan bağlam dosyası (depoda mevcut) |

---

## §12 Sürücü paketi envanteri (depoda, kaynak YOK)

| Yol (repo içi) | Sayı | Not |
|---|---|---|
| `*.sys` | **23** | fxvad.sys varyantları + yardımcılar |
| `*.inf` | **21** | Sürücü kurulum bilgileri |
| `Installer/Drivers/` | — | Paket kaynakları |
| `Installer/DfxInstall/` | COM DLL | Sürücü+DOOM kurulum mantığı ayrı DLL |

**Çıkarım:** Sürücü kaynağı **bu depoda yok** → FxSound sürücüsü ya ayrı bir depoda ya da açıklaştırılmamış dağıtımdadır. `fxvad.sys` pin formatları bu nedenle **yalnız binary analiziyle** doğrulanabilir (`reverse-voicemeeter-fxsound-2026-10-22.md` §9-U2).

> **GÜNCELLEME (2026-10-22 derin tur):** Binary taraması yapıldı ve U2 kapandı — `fxvad.sys` içinde 88.2/96/176.4/192 kHz sabitleri **0 isabet**, yalnız 44.1/48 kHz var; `fxvad.inf` default endpoint blob'u **48 kHz/16-bit/stereo WAVEFORMATEXTENSIBLE**. FxSound sanal cihazı 192k hedefini karşılamaz. Tam kanıt: `@.ai/research/multi-os-audio-stack-veri-toplama-2026-10-22.md` §3.

---

## §13 Neva Engine — 12 maddelik uygulanabilir ders listesi

| # | Ders | Neva'da karşılığı | Öncelik |
|---|---|---|---|
| 1 | GUI / device / DSP 3 bileşen ayrımı | `neva-host` / backend / `neva-core` | P0 |
| 2 | Tek kapı: CLI/Controller sözleşmesi | `neva-remote` IPC sözleşmesi | P0 |
| 3 | WASAPI loopback tap ayrı özellik | `system-audio-tap` (bit-perfect değil, bilinçli) | P2 |
| 4 | DSP modül başına dosya + sabit header | `neva-core/dsp/*` + `*_params.h` | P0 |
| 5 | EQ'da **filter Q + balance + master gain** | `neva_eq` parametre seti | P0 |
| 6 | Toplu/atomik ayar aracı | `neva_apply_settings` | P1 |
| 7 | Preset yaşam döngüsü (save/undo/rename) | `neva_preset` API | P1 |
| 8 | AI/MCP ayrı dilde + testli | `neva-mcp` | P1 |
| 9 | Tanılama ayrı exe + `--json` | `neva-diag --json` | P1 |
| 10 | Sürücü ayrı paket | Ayrı iş birimi | P0 |
| 11 | 3 mimari çıktı (x64/x86/arm64) | toolchain matrisi | P1 |
| 12 | AGPL sınırı | **kod kopyalama yasak** | sürekli |

---

## §14 Metodoloji ve sınırlamalar

| Konu | Durum |
|---|---|
| Yöntem | Repo clone (depth 1) + dosya envanteri + README/go.mod/README-kod okuması + `Select-String` kod taraması |
| **Yapılmayan** | Derleme, test çalıştırma, çalışma-zamanı gözlemi, `Resources` submodule clone |
| Güven | §3 API satır kanıtları **YÜKSEK**; §4 `.fac` ve §7 U maddeleri **`⚠️ VERIFICATION REQUIRED`** |
| Lisans | AGPL v3.0 — yalnız mimari referans; **kod kopyalama/uyarlama yasak** |
| Tekrarlanabilirlik | `git clone --depth 1 https://github.com/fxsound2/fxsound-app.git app-cpp-projects/fxsound-app` → commit `4383180e` |

---

## §15 Dizin Bazlı Dosya Sayımları (tam depo ölçümü, `.git` hariç)

| Dizin | Dosya | Uzantı dağılımı (ilk 4) | Yorum |
|---|---|---|---|
| `dsp/` | **240** | `.h` 100 · `.cpp` 81 · `.c` 46 · `.txt` 5 | En büyük motor katmanı |
| `fxsound/` | **199** | `.svg` 69 · `.cpp` 40 · `.h` 33 · `.ico` 10 | GUI + vektör ikonlar |
| `Installer/` | **154** | `.exe` 25 · **`.sys` 23** · **`.inf` 21** · `.cat` 15 | Sürücü paketi burada |
| `audiopassthru/` | **73** | `.cpp` 48 · `.h` 22 · `.vcxproj` 1 | WASAPI katmanı |
| `bin/` | 64 | **`.fac` 55** · `.pdb` 4 · `.exe` 3 | `.fac` = fabrika verisi dosyaları |
| `fxmcp/` | 48 | `.go` 32 · `.md` 3 · `.syso` 3 · `.json` 2 | MCP sunucu |
| `web/` | 22 | `.js` 15 · `.md` 2 · `.cpp` 1 | `dsp-tutorial` sitesi |
| `docs/` | 15 | `.png` 12 · `.md` 3 | CLI referansı + görseller |
| `fxdiag/` | 13 | `.h` 5 · `.cpp` 4 | Tanılama aracının kaynağı |
| `.github/` | 6 | **`.yml` 4** | CI |
| `release/` | 5 | `.txt` 3 · `.exe` 2 | Yayın artefaktı |
| `Resources/` | **0** | — | git submodule (**clone edilmedi**) |

### §15.1 `dsp/ptechDsp` modül boyutları

| Modül | Dosya | Çekirdek |
|---|---|---|
| `Play/` | **27** | transport/çalma yardımcıları (en kalabalık) |
| `Maximizer/` | 7 | loudness brickwall |
| `Lex/` | 6 | reverb (16/32-bit sürümler) |
| `Aural/` | 5 · `Dly8/` 5 | mekân + gecikme (ASM dâhil) |
| `Peq/` 4 · `wide/` 4 | 8 | EQ + stereo genişletme |
| `Dly32/` | 2 | 32-bit gecikme |

### §15.2 `audiopassthru/include` — 16 başlık

| Başlık | Boyut | Ne |
|---|---|---|
| `sndDevices.h` | **23 318 B** | Ses cihazı katmanının ana sözleşmesi (en büyük header) |
| `dfxpdefs.h` | 10 342 B | DFX efekt tanımları (`⚠️ DFX marka/efekt kökeni — doğrulanacak`) |
| `PolicyConfig.h` | 9 368 B | **Windows default-cihaz değiştirme politikası COM arayüzü** (bilinmeyen/üretici arayüz) |
| `BladeMP3EncDLL.h` | 7 931 B | **MP3 encoder DLL arayüzü** (BladeEnc/LAME uyumlu) → encode çıkışı |
| `pstr.h` | 9 127 B | string yardımcıları |
| `File.h` 6 965 · `codedefs.h` 5 807 · `mth.h` 4 764 | | dosya/codec/matematik |
| `AudioPassthru.h` 2 819 · `u_AudioPassthru.h` 2 688 | | genel sarmalayıcı |

> **`PolicyConfig.h` bulgusu:** FxSound, `IShellDispatch`/`PolicyConfig` ile **varsayılan çıkış cihazını programatik değiştiriyor** → `fxsound_set_output_device` MCP aracının altyapısı. Neva `neva_set_output` için aynı arayüz (üretici API'si, `⚠️ resmi DEĞİL`) kullanılır.

### §15.3 GUI sınıf envanteri (`fxsound/Source/GUI` — ilk 30 dosya)

| Sınıf | `.cpp` boyutu | Rol |
|---|---|---|
| **`FxController.cpp`** | **79 904 B** | Ana denetleyici — GUI↔motor kapısı (`FxController::applyConfig` burada) |
| `FxEqualizer.cpp` | 22 158 B | 31-band EQ görünümü (`⚠️ band sayısı README'den doğrulanmadı`) |
| `FxAudioControls.cpp` | 18 198 B | ses kontrolleri |
| `FxMainWindow.cpp` | 18 788 B | ana pencere |
| `FxOutputPreference.cpp` | 12 830 B | çıkış tercihleri |
| `FxHotkeyLabel.cpp` 5 928 · `FxNotification.cpp` 5 556 · `FxBalanceSlider.cpp` 5 036 | | kısayol / bildirim / denge |
| `FxMessage.cpp` (`.h` 7 908 B) | | mesajlaşma |
| `FxLanguage.cpp` 4 330 B | | **çok dilli** (21 font ile tutarlı) |
| `FxModel.cpp` 3 554 B | | MVC **model** katmanı |
| `FxLiteView.cpp` 1 954 B | | **Lite görünüm** (kompakt mod) |

**Mimari desen:** `FxModel` (veri) · `FxController` (mantık/kapı) · `Fx*View/Controls` (görünüm) → **MVC**. `FxController.cpp` 80 KB = **God-object riski** (CoreMusic ADR-104 benzeri bölmeyi işaret eder `⚠️`).

### §15.4 CI (`.github/workflows`, 4 yml)

| Workflow | Amaç |
|---|---|
| `claude.yml` | **Claude AI ile PR/denetim akışı** |
| `claude-code-review.yml` | **AI kod incelemesi** |
| `publish-dsp-tutorial.yml` | `web/dsp-tutorial` yayını |
| (4. yml) | `⚠️ okunmadı — isim listelenmedi (yalnız sayı 4)` |

> **Ders:** FxSound depoda **AI destekli kod incelemesi** çalıştırıyor (Claude workflows). CoreMusic `.github` + `.workflows/` yapısıyla uyumlu; `.workflows/security` gate'ine AI review eklenebilir (öneri, karar değil).

---

## §16 FxSound ↔ Neva Engine — Parametre Sözleşmesi Önerisi

| FxSound yüzeyi | Kaynak | Neva karşılığı (öneri) |
|---|---|---|
| `equalizer`: band count, freq/gain, **master gain**, **volume leveling**, **filter Q**, **balance** | fxmcp README (§9.2) | `neva_eq_state{bands[], master_db, q, balance}` |
| `effects`: clarity/ambience/surround/dynamicboost/bass **0–10** | §9.2 | `neva_fx_state` (ölçek 0–10 → dB/eşik dönüşümü `⚠️`) |
| `presets`: save/overwrite/undo/rename/delete | §9.3 | `neva_preset_*` (undo = geri yığın) |
| `apply_settings` atomik kompozit | §9.3 | `neva_apply_settings(flags…)` — tek çağrıda tam ayar |
| `fxsound://diagnostics` (`fxdiag --json`) | §9.2 | `neva-diag --json` |
| CLI → `FxController::applyConfig` | README/§10.2 | `neva-remote` IPC komutları |

**Ölçek uyumu notu:** FxSound efekt ölçeği 0–10 tamsayı; Neva DSP'si dB/eşik kullanır. Dönüşüm **Neva tarafında** yapılır (kaynak ölçek korunmaz) — `⚠️ tasarım kararı`.

---

## §17 Açık Madde Listesi (bu raporun güncel hali)

| # | Madde | Durum | Kapatma yolu |
|---|---|---|---|
| U1 | `include/DfxDsp.h` API sözleşmesi | ✅ **KAPANDI** (§19.1) | — |
| U2 | `docs/COMMAND_LINE_OPTIONS.md` CLI yüzeyi | ✅ **KAPANDI** (§18 — 128 satır tam okundu) | — |
| U3 | `.fac` formatı (55 `bin/`, 68 depo) | `⚠️ VERIFICATION REQUIRED` | hex header |
| U4 | `Resources` submodule içeriği | clone edilmedi | `git submodule update --init` |
| U5 | `.fac` = preset mi FIR mı | ✅ **KAPANDI** (§19.2 — düz metin preset, FIR yok) | — |
| U6 | FxSound **exclusive** iddiası (forum) vs `LOOPBACK` (kod) | **Çelişki — kod lehine çözüldü**, ama çıkış yönü `sndDevices*` dışında tam tarama gerekli | `grep -r AUDCLNT_SHAREMODE` |
| U7 | `dfxpdefs.h` — DFX kökeni/etiketi | `⚠️ VERIFICATION REQUIRED` | Dosya okuması |
| U8 | 4. CI workflow adı | okunmadı | dosya adı listesi |

---

## §18 CLI YÜZEYİ — `docs/COMMAND_LINE_OPTIONS.md` (U2 KAPATILDI)

> Bu bölüm, §17'de "okunmadı" olarak işaretlenen U2 maddesini **dosya okumasıyla kapatır** (128 satır, tamamı okundu).

### §18.1 Davranış kuralları

| Kural | Metin özeti |
|---|---|
| Sözdizimi | `--opt=<değer>` — `=` **bitişik** olmalı (`--power 1` sessizce yutulur) |
| Kapsam | Hem ilk açılışta hem **çalışan örneğe** itme (aynı CLI) |
| Aralık dışı sayı | **Sessizce varsayılana sıfırlanır** (hata yok) — Neva için NOT: hata üretmek daha güvenli |
| Yuvarlama | `--balance`, `--master_gain` tam sayıya; `--filter_q`, `--volume_leveling` **0,5 adıma** (ayar diyaloğu slider'ıyla eş) |
| Boşluklu değer | `=` hemen ardından çift tırnak |
| Durum yazımı | `--status` → `%APPDATA%\FxSound\status.json` (diğer opsiyonlar yok sayılır) |

### §18.2 Tam option tablosu (18 option)

| Option | Değer / aralık | Açıklama |
|---|---|---|
| `--power` | `0\|1` | DFX işleme açık/kapalı |
| `--preset` | `<name>` (case-sensitive) | Aktif preset (yalnız power=1) |
| `--save_preset` | `<name>` | Yeni kullanıcı preseti kaydet + seç |
| `--overwrite_preset` | flag | Mevcut kullanıcı presetini üzerine yaz |
| `--undo_preset` | flag | Kaydedilmemiş değişiklikleri geri al |
| `--rename_preset` | `<name>` | Kullanıcı presetini yeniden adlandır |
| `--delete_preset` | flag | Kullanıcı presetini sil |
| `--output` | `<device>` (Windows friendly name, **tam eşleşme**) | Çıkış cihazı seçimi |
| `--view` | `1\|2` | Lite / Full görünüm |
| `--language` | `en`, `fr`, `fi` … | Görüntüleme dili (kayıt → sistem sırası) |
| **`--num_bands`** | **`5 \| 10 \| 15 \| 20 \| 31`** (varsayılan 10) | **EQ bant sayısı** |
| `--balance` | `-20,0 … +20,0` dB | L/R denge |
| `--filter_q` | `1,0 … 3,0` (varsayılan 1,0) | **EQ Q — bant genişliği** |
| `--master_gain` | `-20,0 … +20,0` dB | Ana kazanç |
| `--volume_leveling` | `0,0 … 4,0` dB | Ses seviye eşitleme |
| `--set_band_freq` | `band:freq[,...]` (0-tabanlı) | Band merkez frekansı (çalışan örnek) |
| `--set_band_gain` | `band:gain[,...]`, `-12,0 … +12,0` dB | Band boost/cut |
| `--set_effect` | `fidelity\|clarity`, `ambience`, `surround`, `dynamicboost\|dynamic_boost`, `bass\|bassboost\|bass_boost` : `0,0–10,0` | Etki seviyeleri (aralık dışı **değiştirilmez**) |
| `--status` | flag | Canlı durumu JSON'a yaz |
| `--run_minimized` | flag | Tepsi simidiyle başlat |

### §18.3 Preset komut durum makinesi

- `--preset / --save_preset / --overwrite_preset / --undo_preset / --rename_preset / --delete_preset` **karşılıklı dışlayıcı**; birleşik geçişte **öncelik sırası** işler, diğerleri sessiz yutulur.
- `--save_preset` **max kullanıcı preset sınırına** takılırsa no-op.
- Ad temizleme (3 adım, sırayla): `< > : " / \ | ? *` sil → **64 karaktere kırp** → **büyük/küçük harf duyarsız** mevcut adla çakışma varsa no-op.
  - Örnek (dokümandan): `--save_preset="Mu:sic"` → `:` silinince `"Music"` ile eşleşiyorsa **no-op**.

### §18.4 Örnekler (dokümandan, birebir)

```text
fxsound.exe --save_preset="My Preset"
fxsound.exe --preset="Bass Booster" --output="Speakers (Realtek High Definition Audio)" --run_minimized
fxsound.exe --view=2 --power=1
fxsound.exe --num_bands=15 --filter_q=2.0 --master_gain=3.0 --volume_leveling=2.0
fxsound.exe --set_band_freq="0:60,1:150" --set_band_gain="0:3.0,1:-2.5"
fxsound.exe --set_effect="bass:7.5,ambience:4.0"
fxsound.exe --language=fr
fxsound.exe --status
```

> **Neva için doğrudan çıkarım:** `neva-remote` IPC komut yüzeyi bu 18 option'ın **Neva karşılıklarıyla** aynı adlandırmayı kullanmalı (`--power`, `--preset`, `--num_bands`, `--set_band_gain`, `--status`) → AI/kullanıcı öğrenme maliyeti sıfır. Fark: **aralık dışı değer Neva'da hata döndürür** (sessiz sıfırlama yerine).

---

## §19 WASAPI/COM API KANIT TABLOSU (tüm depo, `*.cpp|*.h|*.c`)

| Sembol | Dosya sayısı | Kritik satır kanıtı | Çıkarım |
|---|---|---|---|
| **`AUDCLNT_SHAREMODE_EXCLUSIVE`** | **0** | — | ❌ **Exclusive asla kullanılmıyor** — bit-perfect kapalı (kesin kanıt) |
| `AUDCLNT_STREAMFLAGS_LOOPBACK` | 1 | `sndDevicesSetupDevices.cpp:170, 173` | Yakalama = loopback |
| `IsFormatSupported` | 1 | `sndDevicesSetupDevices.cpp:466` → **`AUDCLNT_SHAREMODE_SHARED`** | Format pazarlığı paylaşımlı modda |
| `WAVEFORMATEXTENSIBLE` | 3 dosya | `PolicyConfig.h:53,140,227` · `sndDevicesSet.cpp:454` · `sndDevicesSetupDevices.cpp:175` | Format ayarı (default cihaz) |
| `IMMDeviceEnumerator` | 8 dosya | `sndDevices.h:223` vb. | Cihaz keşfi |
| `IAudioRenderClient` / `IAudioCaptureClient` | 3 / 3 | — | Çıkış + yakalama akışları |
| **`IMMNotificationClient`** | 2 | `sndDevices.h:220` (`class CsndDevicesMMNotificationClient`) · `sndDevicesDeviceCallbacks.cpp:80,83` | **Cihaz değişikliği canlı bildirimi** |
| **`SetDefaultEndpoint`** | 1+3 | `PolicyConfig.h:100,182,274` (üretici COM) · `sndDevicesSet.cpp:112` | **Varsayılan cihaz değiştirme** |
| `CoInitialize` | 5 | — | COM yaşam döngüsü |
| `QueryPerformanceCounter` | 3 | — | **Zamanlama ölçümü** (callback süresi? `⚠️`) |
| `CreateThread` / `CriticalSection` | 1 / 1 | `std::thread` **0** | Win32 thread + kritik bölüm |
| `Registry` | 37 dosya | — | Ayar/preset kalıcılığı |
| `snd_pcm` / ALSA | **0** | — | **Windows-only** (doğrulandı) |

> **`IMMNotificationClient` + `SetDefaultEndpoint` ikilisi**, FxSound'ın "cihaz takılıyorsa otomatik geçiş / kullanıcı seçimi" yeteneğini verir → Neva `DeviceEnumerator` port'una zorunlu iki özellik: **değişiklik bildirimi** + **default değiştirme (PolicyConfig)**.

---

## §20 EQ Parametre Matrisi — Neva karşılaştırma

| Parametre | FxSound (CLI) | Neva hedefi (ADR-107) | Uyum |
|---|---|---|---|
| Bant sayısı | 5/10/15/20/**31** (def. 10) | **31 (1/3 oktav)** + 1/6 opsiyonel | ✅ üstü kapsıyor |
| Band frekansı | `--set_band_freq` (band başına, aralık sınırlı) | 31 sabit ızgara (`eq_grids.h` — mevcut HEAD) | ✅ |
| Band kazancı | ±12 dB | ±12 dB (`⚠️ ADR-017 ile teyit`) | ✅ |
| **Filter Q** | 1,0–3,0 (adım 0,5) | `⚠️` karar gerekli — FxSound aralığı emsal | önerilen |
| Master gain | ±20 dB | ±15 dB (`⚠️`) | ⚠️ farklı |
| Volume leveling | 0–4 dB | Loudness eşitleme (modül `⚠️`) | önerilen |
| Balance | ±20 dB | Pan kontrolü | ✅ |
| Etkiler | fidelity/clarity, ambience, surround, dynamicboost, bass (0–10) | Neva: clarity/ambience/surround/bass + **reverb/limiter ayrı** | kısmi |

## §19 DERİN TUR — DfxDsp API + .fac preset + WASAPI akış zinciri (2026-10-22 · U1/U5 KAPANDI)

> Bu bölüm §17'deki U1 (`DfxDsp.h` okunmadı) ve U5 (`.fac` = preset mi FIR mı) maddelerini
> **dosya okumasıyla** kapatır. Kanıtlar: `dsp/include/DfxDsp.h` (79 satır, tamamı okundu) ·
`Factsoft/*.fac` (13 dosya, hex + tam metin okundu) · `audiopassthru/src/sndDevices/*.cpp`
> (grep satır kanıtları). Tam bağlam: `@.ai/research/multi-os-audio-stack-veri-toplama-2026-10-22.md`.

### §19.1 DfxDsp API sözleşmesi (U1 KAPANDI)

`dsp/include/DfxDsp.h` — AGPL başlığı (FxSound LLC 2025 + theremino katkıcıları). Tek sınıf:

| Grup | Üyeler | Not |
|---|---|---|
| Efekt enum | `Fidelity=0, Ambience=1, Surround=2, DynamicBoost=3, Bass=4` (`NumEffects=5`) | 5 efekt — `.fac`'taki `Main 0..5` (6 slot) ile birebir örtüşme **kanıtlanmadı** ⚠️ |
| Format | `setSignalFormat(bps, nch, srate, valid_bits)` | Tek çağrıda tam format |
| İşleme | `processAudio(short* in, short* out, num_sets, check_duplicate_buffers)` | **Public API 16-bit (short)** — float yol içerde (`dfxpProcessReal.cpp`) olabilir, sözleşme short ⚠️ |
| Preset | `loadPreset/savePreset/exportPreset` (`.fac` yolu) | §19.2 |
| EQ | `getNumEqBands/setNumBands`, `get/setEqBandFrequency` + `getEqBandFrequencyRange`, `get/setEqBandBoostCut`, `get/setFilterQ`, `eqOn` | Bant sayısı **dinamik** (setNumBands) |
| Master | `get/setBalance`, `get/setNormalization`, `get/setVolumeLeveling`, `get/setMasterGain` | dB tabanlı |
| Güç | `powerOn/isPowerOn` | |
| Telemetri | `getTotalAudioProcessedTime/reset`, `getSpectrumBandValues(float*, size)` | Spectrum UI beslemesi |

PIMPL: `DfxDspPrivate *data_` — uygulama ile DSP arasındaki tek geçiş noktası.

### §19.2 .fac preset formatı (U5 KAPANDI — "preset", FIR DEĞİL)

**13 dosya** `C:\Program Files\FxSound LLC\FxSound\Factsoft\` (944–984 bayt, `Default.fac` +
`1..12.fac`). Hex header = **düz metin** (CRLF): `CLASS1 : Effect Type\r\n9: Version\r\n<isim>...`

Şema (tam metin okuma):

```
CLASS1 : Effect Type          ← dosya sınıfı
9: Version                    ← şema sürümü 9
<Default|General|Light Processing|...>   ← efekt adı
0: Double Params Flag
1: Total number of elements
<6×> "Main 0..5"              ← 6 ana parametre (Light Processing: 25/0/0/35/5/20)
<7×> "Param 0..6"             ← element parametreleri
7: Number of Application Dependent Integers
<7×> "Integer[0..6]"          ← bayraklar (1/0/1/1/1/0/2 deseni)
10: Number of EQ Bands        ← 10 bant EQ
1: On/Off Flag
Band N → "<CF>: CF" + "<dB>: Boost/Cut"   ← merkez frekans + kazanç
```

**EQ CF değerleri (Default.fac):** 62.5 · 121.5 · 225 · 416.5 · 770.5 · 1425 · 2645 · 4895 ·
9060 · 13885 Hz (CLI'daki 10-bant moduyla uyumlu; 7.fac kaba örnekte CF'ler kayar —
115.734/214.311/... → CF'ler preset'e göre serbest).

**Neva çıkarımı:** preset formatı insan-okur metin (INI-benzeri) — Neva için aynı sadelikte
metin preset yeterli; FIR katsayı dosyası FxSound'da **yok**.

### §19.3 WASAPI akış zinciri (kanıt satırları)

```text
CAPTURE (loopback)                          PLAYBACK
─────────────────                           ────────
pCaptureDevice                              pPlaybackDevice
  ->Activate(IAudioClient)        :75         ->Activate(IAudioClient)    :310/:426/:472
     (taze client için re-activate           (yorum: "Release and re-activate
      yorumu L424)                            to get a fresh, uninitialized IAudioClient")
pAudioClientCapture                         pAudioClientPlayback
  ->Initialize(SHARED, flags, dur) :185      ->Initialize(SHARED, flags, dur, 0, pwfx) :481
     (flags iç LOOPBACK=1 — §kod :170,173)     hata durumunda IsFormatSupported
                                               pClosestMatch ile tekrar :474/:491
  ->GetService(IAudioCaptureClient) :203    pAudioClientPlayback
     → pAudioCaptureLoopback                  ->GetService(IAudioRenderClient) :518
                                                → pAudioClientPlaybackRender
```

**Sonuç:** FxSound passthru her iki uçta da `AUDCLNT_SHAREMODE_SHARED` (exclusive yok —
§kod teyidi); playback uçunda format uyumsuzluğunda `pClosestMatch` fallback'i var. Neva
IAudioBackend bu akışın exclusive varyantını hedefler (ADR-107).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-22
**Mode:** Red Team · Human Mode · Truth Mode
