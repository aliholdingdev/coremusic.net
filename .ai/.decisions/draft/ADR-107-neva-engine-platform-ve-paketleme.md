---
reference_doc: .ai/.templates/adr/adr-nygard-template.md (v1.0.1 — Guardrail #16)
title: "CoreMusic — ADR-107: Neva Engine Platform Kararı ve Paketleme Stratejisi (Özel C++20 Çekirdek + Backend Soyutlama + Statik Lib + Çok Executable)"
type: adr
category: decisions
date: 2026-10-22
updated: 2026-10-22
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/architecture/neva-engine-platform-paketleme.md + .ai/research/reverse-voicemeeter-fxsound-2026-10-22.md + .ai/research/sanal-audio-cihaz-arastirmasi-2026-10-22.md"
---

# ADR-107 — Neva Engine: Platform Kararı ve Paketleme Stratejisi

**Durum:** draft — kullanıcı onayı bekliyor (onay → `accepted/`, redd → `rejected/`)
**Tarih:** 2026-10-22
**Karar Veren:** Bayram Ali / Vault Steward
**İlgili ADR'ler:** ADR-017 (DSP hardware mode) · ADR-014/040 (MySQL kapsamı — novasearch hariç) · ADR-097/098 (K-space — bu ADR dokunmaz)

---

## §1 Bağlam (Context)

1. **Neva Engine hedefi** (kullanıcı kısıtı, 2026-10-22): **ASIO + WASAPI exclusive her sistemde çalışsın** (Windows/Linux/macOS), **hi-res 32-bit/192 kHz bit-perfect**. Sanal ses cihazı 192 kHz/32-bit desteklemeli (FxSound bunu sağlamadığı için alternatif kabul edilmiyor).
2. **Platform/paketleme kararı açık** — 3 prompt'luk işin 3. kolunun Neva Engine platform kararı + ADR üretimi.
3. **RE bulguları** (`.ai/research/reverse-voicemeeter-fxsound-2026-10-22.md`): VoiceMeeter üretimde **modüler executable** stratejisi kullanıyor (BUS GEQ15 / BUSMatrix8 / MacroButtons / StreamView / VBAN2MIDI ayrı exe + Remote DLL); **her sanal cihaza ayrı ASIO bridge DLL'i** veriyor; VM **x64/x86/ARM64** sürücü varyantları dağıtıyor. FxSound ise **WASAPI-only** (loopback) + bit-perfect **DEĞİL** + **AGPL** (kod referansı yasak) + AI katmanı olarak **Go MCP sunucusu** (`fxmcp`) kullanıyor.
4. **Sanal cihaz araştırması** (`.ai/research/sanal-audio-cihaz-arastirmasi-2026-10-22.md`): Windows'ta 32-bit/192 kHz destekleyen tek doğrudan aday `VirtualDrivers/Virtual-Audio-Driver` (MIT); SysVAD pin'leri 16-bit'e sabit; Win10 1607+ yeni kernel sürücü = EV + Attestation/WHQL; PipeWire MIT + `F32P` 192 kHz yapılandırılabilir.
5. **Teknik kısıt gerçeği:** ASIO yalnızca Windows API'sidir; "her sistemde ASIO" fiziksel olarak mümkün değil. Kısıt bu ADR'de **"Windows'ta ASIO+exclusive, diğer OS'lerde exclusive/doğrudan"** olarak resmileştirilir (yorumlama notu §2.1).
6. **Alternatifler elendi:** JUCE'e tam bağımlılık (cross-platform kolay ama bit-perfect + backend kontrolü kısıtlı), tek executable (VoiceMeeter emsali tersini gösteriyor), hazır sanal cihaz bağımlılığı (192k/32-bit garantisi yok).

---

## §2 Karar (Decision)

### §2.1 K1 — Özel C++20 çekirdek + backend soyutlama

- **Çekirdek:** `neva-core` — **C++20**, `noexcept` real-time yol, **JUCE'ye bağımlı değil**.
- **Soyutlama:** `INevaAudioBackend` (enumerate / supported_formats / open / start / stop / negotiated_format).
- **Fallback zinciri:** **ASIO2 → WASAPI exclusive → WDM-KS → ALSA → PipeWire → CoreAudio → Null**.
- **Yorumlama notu (§1.5):** "ASIO her yerde" = Windows'ta ASIO; Linux/macOS'ta exclusive/doğrudan eşdeğer yol.

### §2.2 K2 — Paketleme: statik lib + çok executable

| Hedef | Tip | Windows | Linux |
|---|---|---|---|
| `neva-core` | statik lib | `.lib` | `.a` |
| `neva-player` | exe (CLI) | `neva-player.exe` | `neva-player` |
| `neva-host` | exe (GUI/IPC köprüsü) | `neva-host.exe` | `neva-host` |
| `neva-plugin` | VST3/AU wrap | `.vst3` | `.vst3` |
| `neva-eq` · `neva-matrix` | exe (VoiceMeeter emsal modülleri) | ✅ | ✅ |
| `neva-remote` | dll/lib (Uzaktan API) | ✅ | ✅ |
| `neva-mcp` | exe (AI/MCP sunucu — fxmcp emsal) | ✅ | ✅ |
| **`neva-cli-<kullanıcı>`** | exe — **kullanıcı kendi executable'ını `apps/` altına ekler** | ✅ | ✅ |

**Eklenebilirlik kuralı:** yeni exe yalnız `apps/CMakeLists.txt` → `neva_add_executable(...)` ile eklenir; `neva-core`'a dokunulmaz.

### §2.3 K3 — bit-perfect doğrulama kapısı

WAV hash byte-eşitlik testi (§ backend matrisi × 44.1/48/96/192 kHz × 16/24/32f) **CI kapısı** olur; `negotiated_format() == target` şartı zorunlu.

### §2.4 K4 — Lisans kapıları (release gate)

| SDK | Lisans riski | Kapı |
|---|---|---|
| **ASIO SDK** | GPLv3 (açık kaynak) / ticari | ADR onayı: GPLv3 uygulama mı, ticari lisans mı |
| **VST3 SDK** | GPLv3 / ticari | Aynı |
| **FxSound kodu** | **AGPL** | **KOD KOPYALAMA/UYARLAMA YASAK** — yalnız mimari referans (WORKFLOW §8.1C) |
| **VoiceMeeter** | Kapalı kaynak/ticari | Statik RE + interoperability only |
| Sanal cihaz fork | MIT (`Virtual-Audio-Driver`) | ✅ uygun |

### §2.5 K5 — Sürücü işi ayrı

Sanal ses cihazı (EV/Attestation/WHQL imzalama matrisi, x86/x64/ARM64, OS varyantları) **Neva core'dan bağımsız ayrı iş birimi** ve ayrı sürüm döngüsüdür (VoiceMeeter 20 `.sys` varyantı emsali).

---

## §3 Reddedilen Alternatifler (Alternatives Considered)

| Alternatif | Neden reddedildi |
|---|---|
| **JUCE tam platform** | Backend derinliği/bit-perfect kontrolü sınırlı; ek AGPL/commercial riski; FxSound zaten JUCE + WASAPI-only = 192k bit-perfect yolunu **kanıtlanmış şekilde** kapalı |
| **Tek executable (monolit)** | VoiceMeeter emsali modüler exe'yi üretimde kullanıyor; ayrı exe = ayrı süreç/önem önceliklendirme, kullanıcı eklentisi için tek kapı |
| **Hazır sanal cihaz (BlackHole/Voicemeeter VAIO) bağımlılığı** | 192 kHz/32-bit garantisi yok / kapalı kaynak; cross-platform lisans/garanti sorunu (bkz. sanal cihaz raporu) |
| **PortAudio/RtAudio sarmalayıcı** | `⚠️ VERIFICATION REQUIRED` (bit-perfect exclusive davranış kanıtı yok) + 3. parti real-time riski |
| **yalnız WASAPI (FxSound yaklaşımı)** | Bit-perfect + low-latency DAW uyumu yoksun; ASIO bridge gerekir |

---

## §4 Sonuçlar (Consequences)

**Olumlu:**
- Bit-perfect hedefi **ölçülebilir kapıya** bağlanır (hash testi).
- Kullanıcı ileride kendi exe'lerini **`apps/` tek satırıyla** ekler.
- Backend çeşitliliği (ASIO/WASAPI/WDM-KS/ALSA/PipeWire/CoreAudio) + ARM64 sütunu (VoiceMeeter emsal).
- AI/MCP katmanı (`neva-mcp`) farklılaştırıcı.

**Olumsuz / risk:**
- ASIO + VST3 **lisans kapıları** (R1) — release öncesi çözümlenmeli.
- Sürücü **imzalama maliyeti** (R2 — EV/Attestation).
- Çok executable → **sürüm senkronu** tek repo/sürüm dosyasıyla zorunlu (R5).
- Implementasyon yükü (JUCE yok) — gerçek mühendislik bütçesi.

**Nötr:**
- `.ai/electronic/dsp/` zero-allocation/lock-free kuralları bu kararla bağlayıcı hale gelir.

---

## §5 Uygulama Adımları (Fazlar)

1. **Faz 0 — Onay:** Bu ADR onayı + ASIO/VST3 lisans kapısı kararı.
2. **Faz 1 — Sözleşme:** `Types.h` / `IAudioBackend.h` / `BackendRegistry.h` + `Null` backend + WAV IO (contract test).
3. **Faz 2 — P0 backend:** WASAPI exclusive + ASIO (Windows) + bit-perfect hash CI kapısı.
4. **Faz 3 — P1 backend:** PipeWire + ALSA (Linux, `home.coremusic.net` hedefi).
5. **Faz 4 — Ürün exe'leri:** `neva-player` → `neva-host` → `neva-eq`/`neva-matrix` → `neva-mcp`.
6. **Faz 5 — Sanal cihaz:** ayrı iş (research raporu §4 planı) + ASIO bridge DLL.
7. **Faz 6 — Plugin wrap:** VST3 (lisans kapısı açık değilse AU/AAX ertelenir).

---

## §6 Doğrulama & İlişki

| Kanıt/İlişki | Dosya |
|---|---|
| Platform/backend/paketleme detayı | `.ai/architecture/neva-engine-platform-paketleme.md` |
| RE emsal bulguları | `.ai/research/reverse-voicemeeter-fxsound-2026-10-22.md` |
| Sanal cihaz + 192k doğrulama planı | `.ai/research/sanal-audio-cihaz-arastirmasi-2026-10-22.md` |
| FxSound kaynak mimari (AGPL, MCP) | `.ai/research/fxsound-app-source-analizi-2026-10-22.md` |
| Katmanlı mimari revizyonu | `.ai/architecture/neva-katmanli-mimari-revizyon.md` |
| MySQL BCNF denetimi | `.ai/research/mysql-normalizasyon-analizi-2026-10-22.md` |

**Açık maddeler (UNKNOWN):** U1 ASIO SDK güncel lisans metni · U2 VST3 SDK sürüm/lisans · U3 macOS HAL bit-perfect toggle · U4 PipeWire 192k `hw:` doğrudan kanıtı · U5 `neva-mcp` dil seçimi (Go vs Node/PHP) — kullanıcı kararı.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-22
**Mode:** Red Team · Human Mode · Truth Mode
