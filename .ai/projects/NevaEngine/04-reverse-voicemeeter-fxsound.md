---
title: "Neva Engine — Reverse-Engineering: VoiceMeeter Potato & FxSound"
type: doc
category: audio-engine
version: 1.3.0
status: analiz
authority: "REA shipped-artifact evidence · zero-hallucination (string kanıtları diskte)"
updated: 2026-10-15
---

# RE Raporu — VoiceMeeter Potato + FxSound (Özellik Envanteri)

**Zorunlu Bağlantılar:** [[00-sdk-lib-envanter]] · [[01-gelistirme-plani]] · [[02-dsp-zincir-mimari]] · [[05-neva-mixer-web-pcm-plani]]

> **Amaç:** Sistemde kurulu iki ürünün (VoiceMeeter Potato, FxSound v1.2.13.0)
> özellik yüzeyini çıkarıp Neva Engine'e **clean-room** hedef listesi çıkarmak.
> **Lisans uyarısı:** İkisi de tescilli/kapalı kaynak — kod KOPYALANMAZ; yalnızca
> özellik envanteri (beyaz-sıfırdan uygulanır). Lisans riski: `00-sdk-lib-envanter.md` §5.

## §1 Analiz Yöntemi & Kanıtlar (REA)

| Hedef | SHA-256 | Boyut | Araç |
|---|---|---|---|
| `C:\Program Files (x86)\VB\Voicemeeter\voicemeeterpro_x64.exe` = **Banana** (v2.1.2.2) | `f384446b3c4d56d77f8a690d299bb08169a877a522bde047c8dffd9bb7df8961` | 7.252.840 B | REA `open_binary` + `inspect_pe_resources` (Evidence `ev_55f97af2…`) + string |
| `voicemeeter8x64.exe` = **Potato** (aktif sürüm, FileVersion 3.1.2.2) | `738c876013eaa7ef09c42c4f156b68fbe98a8eb702160ec920d855ce2e8fb2bd` | 13.441.384 B | REA `open_binary` + **PS raw-byte string (65.555 string — §7.6)**; decompile Ghidra restart sonrası |
| `VoicemeeterRemote64.dll` (v1.1.2.2) | — | (VoiceMeeter klasörü) | ASCII/UTF-16 string çıkarma + export envanteri |
| `VoicemeeterBUSGEQ15.exe` (v1.0.1.1) | — | (VoiceMeeter klasörü) | ASCII/UTF-16 string çıkarma |
| `C:\Program Files\FxSound LLC\FxSound\FxSound.exe` (v1.2.13.0) | — | 3.819.472 B | REA `inspect_pe_resources` (provider `rea-pe-resources`) + ASCII/UTF-16 string |
| `…\fxmcp.exe` | — | 6.747.600 B (Go binary) | ASCII/UTF-16 string çıkarma |

**⚠️ DÜZELTME (2026-10-15, zero-hallucination):** Kurulu VB ürünü TEK değil —
**İKİ** üründür: `voicemeeterpro_x64.exe` VersionInfo `ProductName="VoiceMeeter Banana"`
(v2.1.2.2, "pro" = Banana kod adı) · **`voicemeeter8x64.exe` = Potato** (kullanıcı doğruladı;
`Get-Process` kanıtı: `voicemeeter8.exe` ÇALIŞIYOR · `VoicemeeterPotato_Help.xml` kurulu).
§2'deki string kanıtları **pro_x64 = Banana**'dan alınmıştır; Potato'nun kendi string/
decompile taraması §7.3'te (Ghidra katmanı).

**Sınırlama (honest disclosure, güncellendi 2026-10-15):** Ghidra **KURULU + AKTİF** —
`C:\ghidra\ghidra_12.1.4_PUBLIC` (v12.1.4) + JDK 21. Eksik olan `GHIDRA_INSTALL_DIR`
env değişkeniydi; user-scope `setx` MCP sürecine ulaşmadı → **çözüm: `opencode.jsonc`
→ `rea` MCP bloğuna `environment {GHIDRA_INSTALL_DIR, JAVA_HOME}` enjeksiyonu** (§7.7)
→ provider doğrulandı (`ghidra 12.1.4`). Hopper (macOS-only) ve IDA (kayıtsız) bu
makinede yok.

## §2 VoiceMeeter (Banana kanıtları — Potato §7.3) — Özellik Envanteri (kanıt: string)

### §2.1 Mixer topolojisi
- **5 input strip** (3 physical `Strip #1-3` + 2 virtual `Virtual Strip #4-5`)
- **5 output bus** (3 physical `BUS A1-A3` + 2 virtual `BUS B1-B2`)
- Her strip: `Gain/Fader` · `L/R PAN` · `Mute` · `Solo` · `LabelStrip`
- Bus modları: `BUS_MONO` · `BUS_MUTE` · `BUS_CENTERONLY` · `BUS_LFEONLY` · `BUS_REARONLY` · `BUS_STEREOREP`
- **Composite Mode**: 8 kanalı tek bus'a toplama (pre/post-fader seçimli) — `Fold6To2Channels3`, `Fold%luTo%luChannels%lu`

### §2.2 DSP zinciri (per-strip)
- **EQ**: `EQPro-G6` (6 bant — `EQPro-G6 BUS A1…` · `EQGain1-3` · Bass/Med/High bölgeleri) + bus EQ (`VoiceMeeterBUSEQ`, yardımcı exe `VoicemeeterBUSGEQ15.exe` = 15 bantlık grafik EQ paneli)
- **Dynamics**: `Gate Strip` · `Compression Strip` (+`Copy Compressor`) · `OUT Limiter:` (`OutBusLimiter`)
- **Gain/Delay**: `Monitoring Synchro Delay:` (`MaxMonDelayMS`) — monitor gecikme telafisi
- **Audibility**: `audibility_c` / `audibility_g` (görüntülenen seviye eğrisi)
- **K Button** (karaoke/vokal kesme) · **M.C.** (mid-center) · `Limit`
- **Matrix**: `VoicemeeterMatrix8.exe` (8 kanal routing matrisi)

### §2.3 I/O & sürücü
- Çıkış kanalı: `2 (Stereo)` / `4 (Quadri)` / `6 (5.1)` / `8 (7.1)`; `SYNC: Internal Master CLOCK (8 channels virtual out)`
- Sürücü modları: **WDM-KS · MME · ASIO** (`Buffering WDM/KS/MME`, `default: 512/1024`, max buffer `2048 samples`, `(Output A1 supports standard SampleRate only)`)
- Örnekleme: `32 kHz to 48 kHz` varsayılan garanti; `48000/96000/192000 Hz` string'leri (⚠️ 192k destek cihaza bağlı — VERIFICATION REQUIRED)
- Hot-swap: `Auto Restart Audio Engine (A1 Device)` · `Audio Stream needs a complete RESET` · device `Footprint` (cihaz yapılandırma imzası)

### §2.4 Ek sistemler
- **VBAN**: ağ üzerinden UDP audio streaming (`VBANService`, `VBAN-CHAT`, `Send VBAN-Ping`, port çakışma uyarısı)
- **Preset/Scene**: `Store/Load/Copy/Paste/Recall Preset` · XML settings dosyaları (`VoicemeeterBanana_LastSettings.xml` vb.)
- **Macro Buttons**: `VoicemeeterMacroButtons.exe` · `RunMacroButtons on Voicemeeter start`
- **MIDI**: `VoiceMeeterMidiMap` (MIDI learn/atkama)
- **Recorder (Tape)**: mp3 encode (`lame_enc.dll`, `128/256 kbps`)
- **Remote API**: `VoicemeeterRemote64.dll` — COM bileşeni `VBVMRClient{90CFE18D-A300-4741-A31E-1BF2C63EC05E}` · 3. parti uygulama bağlantısı (`Connected Client Applications To VB-Audio Callback`)
- Lisans/DRM: `License Activation` · `DRM_LicenseAcqURL` · GlobalSign EV imzalama → **kapalı kaynak, kopyalanamaz**

## §3 FxSound v1.2.13.0 — Özellik Envanteri (kanıt: string)

### §3.1 Efekt seti (7 efekt · 0–10 skala — kanıt: `by*`/`val*` + MCP jsonschema)
| Efekt | Kanıt string'i |
|---|---|
| Bass Boost | `Boosts low end for full…` · `bass_boost` |
| Ambience | `byAmbience` · `json:"ambience"` |
| Surround | `Surround Sound` · `json:"surround"` |
| Fidelity | `fidelity and presence` · `byFidelity` |
| Dynamic Boost | `Dynamic Boost` · `dynamic_boost` |
| Clarity | `clarity` · `json:"clarity"` |
| Headphone mode | `byHeadphone` · `Headphones` / `Speakers` (çıkış hedefi) |
- Genel bypass: `on` (jsonschema: "true to turn FxSound's audio processing on…")
- Stereo balance: `json:"balance" — stereo balance in dB, -20 to 20`

### §3.2 EQ & preset
- **GraphicEq** + parametrik dokunuş: `num_bands` (parametrik — **KAPANDI 2026-10-15: 31 bant**; kanıt 3 katman: Ghidra `GraphicEqNew` decompile limit `1..32` · `FxSound.settings: num_bands=31` · preset `.fac`: "31: Number of EQ Bands" + ISO 1/3-oktav CF 20→20000 Hz · bkz §7.7) · `eq_band_gains` (dB/bant) · `filter_q` (1–3; settings default **1.5**) · `Hyper-low Bass - First band … down to 20 Hz`
- Preset: `save/delete/rename/import/export` · `max_user_presets` · dosya tabanlı (`.dfxp` iması: `dfxpBeginProcess`, `LASTUSED_DFXP`)
- Dev mirası: fonksiyon adları **dfx\*** (`dfxpModifyRealtypeSamples`, `comProcessWaveBuffer`, `spectrumProcess`) → FxSound = DFX Audio Technologies halefi

### §3.3 Ses motoru & sürücü
- Kendi sanal sürücüsü: `FxSound Device` (`Drivers\win10\x64\fxdevcon64.exe`, win7/x86 varyantları)
- **⭐ KERNEEL SÜRÜCÜ KANITI (2026-10-15, 09 doc §7):** `fxvad.sys` (326.656 B · `system32\drivers\`) — servis `fxvad` Type 1 (kernel driver), Start 3 (demand) · DriverStore `fxvad.inf_amd64_a75d87b3871a94a9` · sınıf `{4d36e96c}\0005` "FxSound Audio Enhancer" v14.1.0.0 (DriverVer 17.03.2021) · `MatchingDeviceId: Root\FXVAD` · `AddReg=DFX_Device.AddReg` (DFX miras adı) — yani FxSound **Root-enumerated WDM sanal endpoint** kuruyor (kurulum = INF DriverStore'a yazma). Ayrıca uygulama **AGPL-3.0 açık kaynak**: github.com/fxsound2/fxsound-app (fxmcp.mcpb manifest kanıtı — 09 §7)
- Cihaz olayları: `MMNotificationClient::OnDefaultDeviceChanged/OnDeviceAdded/OnDeviceRemoved` → **hot-swap**, cihaz değişince processing thread yeniden kurulur
- Resample: `sndDevicesDoCapture():: upsampleRatio is out of range` → capture/resample zinciri
- Exclusive-mode çakışma koruması: `Another application could be using it in exclusive mode…`
- UI: OpenGL (`glClearDepth`, `glDrawBuffer`) + tray (`FxSound in system tray`) + hotkey (`FxSoundHotkeys`)
- Log/dump: `fxsound.log`, `fxsound.dmp`

### §3.4 ⭐ fxmcp.exe — MCP kontrol sunucusu (Go)
`AddTool/CallTool` · `internal/mcpserver/{tools,tools_eq,tools_composite,tools_write,prompts,resources}.go`
JSON API şeması (string kanıtı): `effects` (name→0-10) · `eq_band_gains` · `eq_band_freqs` · `filter_q` (1-3) · `balance` · `device_name` (Windows friendly name) · preset CRUD.
**Çıkarım:** FxSound artık LLM/MCP-erişilebilir bir kontrol düzlemi sunuyor — Neva'nın
web UI + API tasarımı için doğrudan ilham (aynı model: ince istemci → yerel kontrol sunucusu → DSP).

## §4 Karşılaştırma → Neva Hedefi

| Yetenek | VoiceMeeter Potato | FxSound | **Neva (hedef)** | Neva kaynağı |
|---|---|---|---|---|
| Mixer topoloji | 5 strip / 5 bus | 1 enhance yolu | **8 strip / 8 bus + 8.1 matrix** | 02-dsp-zincir §2 |
| Sanal sürücü | VBCABLE (ayrı kurulum) | FxSound Device (dahili) | **WASAPI loopback (kısa vade)** + sv1 sanal sürücü (Faz 6+, ⚠️ PLANNED) | 00-envanter §3 |
| EQ | 6 bant + bus 15 bant grafik | grafik + Q 1–3 | **31 bant TPT parametrik + grafik görünüm** | 02-dsp-zincir §2.2 |
| Dinamik | Gate+Comp+Limit | Dynamic Boost | **Gate+Comp+TP limiter+maximizer** | 02-dsp-zincir §2.3-2.6 |
| Efekt | K-button, audibility | 7 efekt (0–10) | **FX rack: ambience/surround/width (0–10 skala)** | 05-plani §3 |
| Ağ | VBAN (UDP) | — | ⚠️ Faz 7 PLANNED (WebRTC/UDP) | — |
| Uzak API | COM Remote API | **MCP + JSON** | **WebSocket JSON + MCP-uyumlu tool şeması** | 05-plani §4 |
| Preset | XML scene | .dfxp dosya | JSON preset + import/export | 05-plani §4 |
| MIDI/Macro | Var | Hotkey | MIDI + macro buton + web hook | 05-plani §3 |
| Kayıt | Tape (mp3 lame) | — | PCM/WAV (24/32-bit) + mp3 opsiyonel | CLAUDE §19 |
| Lisans | Kapalı | Kapalı | **Açık (AGPLv3 JUCE + MIT/BSD lib)** | 00-envanter §5 |

## §7 Tur 2 — Derin REA Taraması (2026-10-15)

### §7.1 Sürüm/PDB/şirket kanıtları (VersionInfo + PDB string)
| Binary | ProductName | Sürüm | Şirket | PDB (string) |
|---|---|---|---|---|
| voicemeeterpro_x64.exe | **VoiceMeeter Banana** | 2.1.2.2 | VB-AUDIO Software | `H:\Travaux\VB_VAC\VoiceMeeter\Project\voicemeeterpro_vc2010\x64\Release\VoicemeeterRemote64.pdb` → **VS2010 (vc2010)** derlemesi |
| voicemeeter8x64.exe (Potato, aktif) | — | 13.441.384 B | REA `open_binary` + **PS raw-byte string (65.555 — §7.6 TAMAM)**; DSP iç decompile = opsiyonel (§7.7.4) |
| VoicemeeterRemote64.dll | VoicemeeterRemote | 1.1.2.2 | VB-AUDIO Software | (yukarıdaki ile aynı proje ağacı) |
| VoicemeeterBUSGEQ15.exe | VoicemeeterBUSGEQ15 | 1.0.1.1 | Audio Mechanic & Sound Breeder | `…\Project\geq\geq_vc2010\Release\VoicemeeterBUSGEQ15.pdb` → VS2010 |
| FxSound.exe | FxSound | 1.2.13.0 | FxSound LLC | — (C++ native — RTTI `.?AVFxEqualizerControl@@` `.?AVCsndDevicesAudioEndpointVolumeCallbackCapture@@`) |

**Çıkarım:** VB-Audio araç ailesi VS2010 ile derlenmiş eski bir C++ kod tabanı üzerinde
yükseltiliyor (API isimleri sabit tutulmuş) — Neva için tersine ders: API sözleşmesini
sürüm boyunca sabit tut, UI'ı yenile.

### §7.2 VoiceMeeterRemote API export envanteri (Remote64.dll string kanıtı)
`VBVMR_Login` · `VBVMR_RunVoicemeeter` · `VBVMR_GetVoicemeeterVersion` · `VBVMR_GetVoicemeeterType` ·
`VBVMR_GetParameterFloat` · `VBVMR_SetParameterFloat` · `VBVMR_GetParameterStringA/W` ·
`VBVMR_SetParameterStringA/W` · `VBVMR_SetParameters` / `SetParametersW` ·
`VBVMR_MacroButton_GetStatus` / `SetStatus` · `VBVMR_Input/Output_GetDeviceNumber` ·
`VBVMR_Input/Output_GetDeviceDescA/W`
**Çıkarım (Neva kontrol API'si için ders):** flat-C export yüzeyi + tek parametre adı
tabanı (isim→float) = 3. parti entegrasyonu için minimum yüzey; Neva'da eşdeğeri
WS/JSON `set param <name> <value>` metodu (05-plani §4 ile uyumlu).

### §7.3 Banana string bulguları (tur 2, genişletme §2'ye)
- **Per-strip compressor şeması (XML dump string):** `<StripComp index gainin attack release knee comprate threshold automakeup gainout>` → VP'de comp param seti: **gain-in, attack, release, knee, compression-rate, threshold, auto-makeup, gain-out** — **02-dsp-zincir §1.1'e işlendi (2026-10-15): 8/8 birebir örtüşme**
- **Bus modları (ek):** `Composite` · `RearOnly` · `MixDownA` · `Fader` · `TVMIX` · `MIXNORMAL` · `STEREOREP` · `LFEONLY` · `Bus[i].Mono` · `Bus[i].EQ`
- **Strip XML alanları (ek):** `karaoke='%i'` (K-button), `muc`, `prg_c/prg_g`, `dblevel/dblimit`, `A1..A4` atamaları, `busa/busa2/busa3/busb/busb2` bus atamaları
- **PatchComposite:** `c1..c8 + mode` (8 kanal kaynak→hedef patch)
- **VBAN paket şeması:** `<VBANStreamIn index in status name ip port NQ>` · `<VBANFrameOut index source status name ip port mbps fps cursor format quality>` → UDP + jitter-buffer (NQ) + kalite/sıkıştırma alanı
- **EQ:** `Master EQ BUS A2/B2` · `EQPro-G6 BUS B2 (Virtual)` · `BusEQmem` · `VOutEqOn1` · `EQ Med Virtual Strip #4` (strip başına EQ slotu + bus master EQ ayrı)
- **GEQ15 aracının teyidi:** "15 Bands Graphic EQ for Voicemeeter Output BUS" · "Save GEQ Settings" · `GEQ15_SysTray` / `GEQ15_SysTrayShowOnRun` (registry anahtarı) · "VB-AudioVoicemeeterGEQ"

### §7.4 FxSound tur 2 bulguları
- **Ambience etiket teyidi:** `"Ambience" = "Ortam"` (TR lokalizasyonu) — reverb tipi efektin adı kesin
- **EQ band etiketleri (help text):** Hyper-low Bass (20 Hz'e kadar 1. bant) · low-end center · low-mid focal (gitar/vokal) · center mid (ritim/snare) · high-mid (harmonikler/clickiness) · high-end lower (vokal netliği, sibilance kesme) · her bantta **frekans tekerleği + slider** (`%g: Boost/Cut` format string'i)
- **Cihaz kısıtı:** "FxSound does not support mono devices, so FxSound processing had been disabled for this device." → mono cihazda processing tamamen kapanır (Neva: mono çıkış fallback kuralı — 02 §I/O)
- **Endpoint izleme:** RTTI `CsndDevicesAudioEndpointVolumeCallbackCapture` → sistem ses seviyesi değişimini callback ile yakalar (Neva meter tasarımı için referans)
- **Preset:** Import/Export/Change Preset · `https://www.fxsound.com/presets` (paylaşılan preset deposu) · "Duplicate presets not imported"
- **UI:** Always On Top · Change Playback Device (tray) · Sketch design tool varlığı (asset zinciri)

### §7.5 Ghidra katmanı durumu (Faz 6 kapısı)
Ghidra 12.1.4 kurulu (`C:\ghidra\ghidra_12.1_4_PUBLIC`) · JDK 21 mevcut ·
`GHIDRA_INSTALL_DIR` **2026-10-15'te set edildi** (user scope) → OpenCode restart
sonrası REA Ghidra provider (`search_strings`/`analyze_function`/`procedure_pseudo_code`)
aktifleşir. **Potato string taraması 2026-10-15'te PS raw-byte ile yapıldı (§7.6 —
Ghidra'sız; FX envanteri + Banana delta çıkarıldı).** **Ghidra katmanı 2026-10-15'te
AKTİF** (§7.7): FxSound EQ band sayısı KAPANDI (31) · Banana serializer/parser
decompile edildi; kalan opsiyonel: DSP gain-computer matematiği (StripComp/JustVerb/C5
iç saf matematik — simgesiz binary'de maliyetli, opsiyonel Faz 6+ turu).

### §7.6 Potato (voicemeeter8x64.exe) string turu (2026-10-15, PS raw-byte — Ghidra'sız)

**Kimlik (VersionInfo):** `ProductName="VoiceMeeter Potato"` · FileVersion **3.1.2.2** ·
Company "VB-AUDIO Software" · Desc "Virtual Audio Device Mixing Console Application" ·
PDB `H:\Travaux\VB_VAC\VoiceMeeter\Project\Voicemeeter8_vc2010\x64\Release\Voicemeeter8x64.pdb`
→ **proje kodu "Voicemeeter8", yine VS2010** (Banana=pro, Potato=8).

**Ölçüm:** 13.441.384 B · 65.555 string (64.930 ASCII + 625 UTF-16LE).

#### §7.6.1 Insert FX envanteri (per-strip `<FXSection freverb fdelay fdelayIsC5>`) — YENİ
Potato, Banana'dan farklı olarak **şerit başına insert FX** taşıyor (A/B memorili):

| FX | Ad (UI) | XML şeması (param seti) |
|---|---|---|
| **Reverb** | **JustVerb III (Reverb)** | `ReverbGeneral(mute, nuPreset, presetName, dry, wet)` · `ReverbParam(predelay, decay, **EarlyRefAmount**, EQmute)` · `ReverbEQ[3 band](eqon, eqf, eqq, eqg)` |
| **MultiTap Delay** | **MultiTap (Delay)** | `MultiTapGeneral(mute, balance_L/R, dry, wet, autofit, phase)` · `MultiTapDelay(predelay, timeratio, **currentBPM**, feedback, feedbacktype)` · `MultiTapParam[tap](tapon, delay, gain, filtergain, pan)` — **tempo-sync (BPM) delay** |
| **Multiband Comp/Lim** | **C5-Limiter (MultiBand Compressor)** | `C5LimGeneral(mute, automakeup, nbBand, MasterGain, freqRnk1..4)` · `C5LimBand[i](mute, solo, chain, gain, compOn, **attack, release, knee, ratio, threshold, gainOut, limOn, limit, distorsion**)` — **bant başına comp + limiter + distorsion**, 4 adet freqRnk ile bölme |
| **Pitch** | (StripPitch) | `StripPitch(pitchon, drywet, pitchvalue, formantlo/med/high)` — **formant kontrolü** |
| **Denoiser** | Denoiser (strip başına, `dn_learn` ile learn) | `denoiser_g, dn_bypass, dn_learn` |
| Mesafe Preset'leri | `delay 10/20/50/75/100 meters` | hoparlör→mikrofon mesafe telafisi (hız-ı ses üzerinden) |

**Neva'ya doğrudan ders (R6+R10):** reverb param seti (predelay/decay/early-ref) ve
multiband şema (freqRnk + per-band attack/release/knee/ratio/threshold) Neva FX ve
dinamik şartnamesine referans aday; **bant başına `limOn+limit+distorsion`** Vazgeçilen
karmaşık yüzey — Neva'da `distorsion` YOK, comp+limiter ayrık modüller (R8/R10).

#### §7.6.2 Banana'ya karşı farklar (delta tablosu)
| Öğe | Banana (pro_x64) | Potato (voicemeeter8x64) |
|---|---|---|
| Ürün sürümü | 2.1.2.2 | **3.1.2.2** |
| Proje/PDB | `voicemeeterpro_vc2010` | `Voicemeeter8_vc2010` |
| Virtual strip | #1..#4 görünür etiketler | **#1..#8** (Gain/PAN/Mute/Solo/M.C./K/Limit/EQ #6-8 string'leri) |
| Bus ataması (strip XML) | `busa, busa2, busa3, busb, busb2` | `busa..busa5` (**A1..A5**) + `busb, busb2, busb3` (**B1..B3**) |
| Insert FX | Yok (bus master EQ + comp/gate strip) | **Reverb + MultiTap + C5-Lim + Pitch + Denoiser (per-strip, A/B mem)** |
| Gate şeması | `Gate Strip` etiketi | `StripGate(thresin, damping, **bpsidechain**, attack, hold, release)` |
| Composite mode | `Composite` bus modu | `Select PRE/POST-Fader Inputs … for 8 Channels of the BUS` + `PatchComposite c1..c8 mode` |
| Ayar dosyaları | (genel) | `VoiceMeeterPotatoDefault/Last/Today/YesterdaySettings.xml` — **günlük otomatik kayıt** |

#### §7.6.3 UI/ek ipuçları
- Panel koordinatları XML'de saklanır: `Panel3Dx/y, ColorPanelx/y, ModPanelx/y, paneltype`
  → kullanıcı tarafından taşınabilir/düzenlenebilir panel (Neva web UI'da layout persist
  fikrine referans).
- `layer1..layer8` (strip başına 8 float) — 8 kanal Gain layer'ları (Composite 8ch eşleşir).
- Lisans: `Challenge code` / `Copy Challenge Code (ClipBoard)` anti-piracy string'leri
  (Neva: lisans sistemi YOK; ürün içi aktivasyon düşünülürse referans).

### §7.7 Ghidra 12.1.4 decompile turu (2026-10-15 — AKTİF)

**Etkinleştirme:** REA'nın MCP tanımına (`~/.config/opencode/opencode.jsonc` → `rea`
bloğu) `environment: { GHIDRA_INSTALL_DIR: C:\ghidra\ghidra_12.1.4_PUBLIC, JAVA_HOME:
jdk-21.0.12 }` enjekte edildi → MCP sunucusu env'i config'den aldığı için restart
**yeterli oldu** (user-scope setx tek başına yetmiyordu — MCP süreci eski env ile
kalmıştı). Provider teyidi: `provider: ghidra 12.1.4`, `import_mode:
ephemeral-source-immutable`.

#### §7.7.1 FxSound EQ — §3.2 ⚠️ KAPANDI (3 bağımsız kanıt katmanı)
| Katman | Kanıt |
|---|---|
| Dekompile | `GraphicEqNew` (`FUN_1401b33d0`): `param_2 - 1U < 0x20` → **bant sınırı 1..32**; `i_num_bands` caller'da (`valsSetGraphicEq`) **preset/config dosyasından** okunur (`valsRead(): wcp_file_path = %s`) |
| Runtime settings | `%APPDATA%\FxSound\FxSound.settings`: **`num_bands=31`** · `filter_q=1.5` · `normalization=-8.0` · `master_gain=0.0` · `max_user_presets=120` · `language=tr-TR` · `version=1.2.13.0` · `power=1` |
| Preset dosyası | `Presets\*.fac` (metin): **`31: Number of EQ Bands`** + Band 1..31 tek tek: CF (Hz) = 20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160, 200, 250, 315, 400, 500, 630, 800, 1000, 1250, 1600, 2000, 2500, 3150, 4000, 5000, 6300, 8000, 10000, 12500, 16000, 20000 Hz + **Boost/Cut (dB/bant)** |

**Sonuç:** FxSound EQ = **31 bantlık 1/3-oktav ISO grafik EQ** (20 Hz–20 kHz) — klasik
DFX "10 bant" mirası modern 1.2.13 sürümünde geçersiz. **Eski "10 bant" ifadesi bu
kanıtlarla düzeltildi** (zero-hallucination). Neva R16 grafik formu için: **31 bant
1/3-oktav** rakip referansıdır (Neva 15/31 bant seçimi 02-dsp-zincir'de kararlaştırılacak).

#### §7.7.2 Sızan kaynak yolu & modül haritası (MessageBoxW assert'leri)
`C:\Users\vijay\Documents\Projects\FxSound\main\fxsound-app\dsp\ptutil\DspUtil\GraphicEq\GraphicEqInit.cpp`
`…\dsp\ptutil\VALS\Valsset.cpp` · `…\dsp\ptutil\VALS\Valsfile.cpp`
→ kod ağacı: **fxsound-app / dsp / ptutil / {DspUtil, VALS}**; hata raporlama
MessageBoxW ile dosya-satır belirtir (assert kalıntısı — Neva: `JUCE_ASSERT` +
kayıtlı log tercih).

#### §7.7.3 Preset/ayar dosyası formatları (disk kanıtı)
- **`.fac`** = metin preset: `CLASS1 : Effect Type` · `9: Version` · `Main 0..5` (double
  parametreler) · `Element Number / Param 0..6` · `Application Dependent Integers[0..6]`
  · `31: Number of EQ Bands` · bant başına `CF` + `Boost/Cut` — Neva preset dosyası
  için okunabilir-metin formatı referansı.
- **`FxSound.settings`** = XML `<PROPERTIES><VALUE name=… val=…>` — tek key-value XML.
- **Per-cihaz profil:** `device_configs` JSON (device_id + name + **form_factor:
  Speakers/HDMI/Headphones/Headset** + per-device preset) → R5 hot-plug/otomatik profil
  ile birebir aynı problem tanımı (kanıt: rakip de cihaz başına profil tutuyor).
- `AutoSave\*.fac` = otomatik kayıt kopyası (çökme koruması).

#### §7.7.4 Banana serializer/parser decompile (StripComp bağlamı)
- `StripComp` format string xref'leri (3): biri **settings serializer** (`FUN_140115e20`,
  `<VoiceMeeterParameters>` yazıcı), biri **param-switch serializer**
  (`FUN_140126980` — `case 0x262/0x276` gibi **sayısal parametre ID'leri** → strip
  struct **`0x7C` (124 B) adım**, float alanlar `0x9748..0x979c` ofset aralığında).
- **Çıkarım:** VoiceMeeter ayar protokolü **ID tabanlı** (`SetParameterFloat` çağrıları
  sayısal ID ile) — Neva'nın **ad tabanlı** (isim→değer) API'sinden farkı; ID uzayı
  3. parti araç kırılganlığı yaratır, Neva'da isim-uzayı kalacak (R11/05 §4).
- **DSP gain-computer (knee/ratio matematiği)** bu tura SIMGESİZ binary + dev switch
  yüzeyi nedeniyle girilmedi → ⚠️ `VERIFICATION REQUIRED` (opsiyonel Faz 6+ turu).
  **Param seti + XML şeması zaten tam** (§7.3); Neva R10 şartnamesi buna dayanır.

## §8 ASIO Sürücü RE Turu (2026-10-15 — vbvm*_asiodriver64.dll ×5)

### §8.1 Envanter (VersionInfo + registry)
| DLL | Product | Sürüm | ASIO COM adı (HKCR\ASIO) | CLSID |
|---|---|---|---|---|
| `vbvm_asiodriver64.dll` | vbvm_asiodriver — "for Voicemeeter" | 1.0.2.2 | Voicemeeter Virtual ASIO | `{9175CF07-885D-46B4-9EA1-4126D6648DE6}` |
| `vbvmaux_asiodriver64.dll` | vbvmaux_asiodriver | 1.0.2.2 | Voicemeeter AUX Virtual ASIO | `{BAEC28F8-…}` |
| `vbvmins_asiodriver64.dll` | vbvmins_asiodriver | 1.0.2.2 | Voicemeeter Insert Virtual ASIO | `{368955AF-…}` |
| `vbvmpins_asiodriver64.dll` | vbvmpins — "**for Voicemeeter8**" | 1.0.2.2 | **Voicemeeter Potato Insert** Virtual ASIO | `{F5735BD4-…}` |
| `vbvmvaio3_asiodriver64.dll` | vbvmvaio3_asiodriver | 1.0.2.2 | Voicemeeter VAIO3 Virtual ASIO | `{0239BE07-…}` |

(Makinede ayrıca `FL Studio ASIO` kayıtlı.) Hepsi `C:\Program Files (x86)\VB\Voicemeeter\`,
PDB: `voicemeeterpro_vc2010\x64\Release\vbvm_asiodriver64.pdb` (VS2010 — sürücü de
aynı eski proje ağacında derlenmiş).

### §8.2 Mimari (Ghidra decompile + import kanıtı)
- **IPC = shared memory:** `CreateFileMappingA` / `OpenFileMappingA` / `MapViewOfFile` —
  aday `VBVASIOClientV2{E4244739-62ED-4d07-8D1E-16FAE7966868}`; boyut şeması
  `board_index * 0x60000 + 0x280` (başına ~384 KB segment).
- **Zamanlama = multimedia timer + worker thread:** `timeBeginPeriod/timeGetTime/
  timeEndPeriod` + `CreateThread` → `timerOn` logu
  `::timerOn : SR:%i Hz / Latency: %i smp / board_index: %i` (ASIO bufferSwitch
  simülasyonu thread tarafından tetiklenir).
- **Sınıf deseni (RTTI):** `IASIO` + `AsioSample` + `CClassFactory` + `CUnknown` —
  Steinberg ASIO SDK deseni (COM `DllGetClassObject` export).

### §8.3 Parametreler (R3 kanıtları)
| Parametre | Varsayılan | Aralık | Kaynak |
|---|---|---|---|
| Sample rate | **44100 Hz** (`0xac44`) | registry `VBoardSR`: **1000–400000 Hz** | `FUN_180002130` decompile |
| Buffer/latency | **512 smp** (`0x200`) | registry **`VBoardLatency`: 4–2048 smp** | aynı |
| Sahiplik | Engine çalışıyorsa **SR+latency shared-memory'den okunur** (sürücü Voicemeeter'a uyar) | — | `Sleep(50)` + map'ten okuma |
| Config anahtarı | `HKCU\VB-Audio\VoiceMeeter\{VBoardLatency, VBoardSR}` (DWORD) | — | `RegOpenKeyA(0x80000001,…)` |
| AsioSample ctor | `bufsize=0x200`, `outLatency=bufsize`, `+0x24=bufsize*2`, SR=44100.0 (double) | — | `FUN_180002890` |

**`ASIOOutputReady` desteği:** vtable içeriği okunmadı → `UNKNOWN` (⚠️ VERIFICATION
REQUIRED — istenirse bir sonraki turda `IASIO::vftable` slotları enumerate edilir).

### §8.4 Neva'ya dersler (R3)
1. **Sanal ASIO host davranışı:** gecikme = buffer (512 default) + timer tick; Neva host
   tarafında 64–128 smp hedefi VP'nin 512 default'unun ~4-8 kat altında olur (<10 ms).
2. **Kullanıcı ayarı anahtarı deseni:** tek DWORD registry değeriyle gecikme ölçekleme
   (`VBoardLatency` benzeri) — Neva'da equivalent: settings + API endpoint (R11).
3. **Paylaşımlı bellek segment boyutu formülü** (`n*0x60000+0x280`): cihaz başına
   sabit ayrılmış buffer — Neva 10 çıkış planında (R2) benzer channel-block hesabı.


---

## §9 VB Audio / VoiceMeeter OS & Sürücü Matrisi (2026-10-15 RE turu — PE resource + INF disk kanıtlı)

> **Kaynak:** `C:\Program Files (x86)\VB\Voicemeeter` kurulum klasörü (29 `.sys` + 29 `.cat` +
> 40+ INF + ASIO DLL'leri) + `C:\Program Files\VB\VBVoicemeeterVAIOs` (yeni nesil VAIO64) +
> REA `inspect_pe_resources` (statik PE resource envanteri — çalıştırma YOK) + yerel registry okuması.

### §9.1 Aile × OS dosya matrisi (dosya adı suffix'leri — disk listesi kanıtlı)

| Sürücü ailesi | x86 (i386) OS varyantları | x64 OS varyantları | Not |
|---|---|---|---|
| `vbaudio_vmvaio` | xp · 2003 · vista · win7 · win10 (5) | 2003 · vista · win7 · win10 (4) | VAIO #1 |
| `vbaudio_vmvaio64` | - | 2003 · vista · win7 · win10 (4) | VAIO #1 x64 |
| `vbaudio_vmvaio3` | xp · 2003 · vista · win7 · win10 (5) | 4 | VAIO #3 |
| `vbaudio_vmvaio364` | - | 4 | VAIO #3 x64 |
| `vbaudio_vmauxvaio` | xp · 2003 · vista · win7 · win10 (5) | 4 | AUX |
| `vbaudio_vmauxvaio64` | - | 4 | AUX x64 |
| `vbvoicemeetervaio64` | - | **win10 (x64) + win10arm (ARM64)** | **Yeni nesil** (2025) — yalnız Win10 1709+ |

**Toplam 29 `.sys` + 29 `.cat`** (her varyant imzalı çift). ASIO bridge DLL'leri OS-suffix'siz: `vbvm` ·
`vbvmaux` · `vbvmins` · `vbvmpins` · `vbvmvaio3` (32+64 = 10 DLL) — §8'de RE edildi.

### §9.2 PE biçimi & resource envanteri (RE `inspect_pe_resources`, 2026-10-15)

| Dosya | SHA-256 (16) | Byte | PE | Machine | Resource |
|---|---|---|---|---|---|
| `vbaudio_vmauxvaio_xp.sys` | `3d888ae7eda925fb` | 56 256 | PE32 | i386 (332) | 5: icon×2 (7336 B) + group×2 (100·101) + VERSION (948 B) |
| `vbaudio_vmauxvaio_win7.sys` | `224e97354388c71b` | 56 256 | PE32 | i386 (332) | 5: aynı yapı (VERSION 956 B) |
| `vbvoicemeetervaio64_win10.sys` | `12ee8d2bc7309b51` | 296 392 | PE32+ | AMD64 (34404) | **38**: icon×18 (7336 B) + group×18 (100·101·201-208·301-308) + RCDATA + VERSION (860 B) |
| `vbvoicemeetervaio64arm_win10.sys` | (okunmadı) | 305 088 | ARM64 (beklenen) | - | ⚠️ VERIFICATION REQUIRED (yalnız boyut kanıtlı) |

> **Çıkarım:** klasik aileler (XP→Win10 suffix) **aynı kaynak ağacından** tek DriverVer ile
> üretilir (INF DriverVer = 2.1.5.2, §9.3); suffix yalnız hedef OS + PE hedefi seçer. Yeni nesil
> VAIO64 5× icon (18 grup — 100/101 = render/capture, 2xx/3xx = ek kanal rolleri, ‼ tahmin değil
> resource ID okuması) ile tamamen ayrı kod tabanıdır (296 KB vs 56-72 KB, DriverVer 3.4.1.7).

### §9.3 INF DriverVer & servis matrisi (INF disk okuması)

| INF | DriverVer | Servis | Start | ServiceBinary | Fark |
|---|---|---|---|---|---|
| `VBvmauxvaio_xp.inf` | 01/11/2019, **2.1.5.2** | `%SERVICE_KERNEL_DRIVER%` | DEMAND (3) | `%12%\%DriverFile%` | `AssociatedFilters=wdmaud,redbook` (XP wave/mixer mirası) |
| `VBvmauxvaio_win10.inf` | 01/11/2019, **2.1.5.2** | aynı | DEMAND | `%12%\%DriverFile%` | `NTMPDriver=sbemul.sys` eklendi |
| `vbvoicemeetervaio64_win10.inf` | 11/27/2025, **3.4.1.7** | `addService=%ServiceName%` | DEMAND | **`%13%`** (DriverStore) | NTamd64 + **NTARM64** dual · 37 KB INF · Class=MEDIA |

> Tüm INF'ler `Class=MEDIA` · `ClassGUID={4d36e96c-...}` · 2019 ailesi wdmaud.drv wave/mixer
> zincirine, VAIO64 modern Topology (WaveRender1-3 + TopologyRender1-3) dizilimine sahiptir.

### §9.4 Bu kurulu sistemde (Win10/11) registry durumu (2026-10-15 yerel okuma)

| Servis | Durum | Kanıt |
|---|---|---|
| `fxvad` | **KURULU** (§10) | `Start=3` `Type=1` |
| `VB-MultiCable` | kayıt VAR, `ImagePath` boş çıktı | ⚠️ VERIFICATION REQUIRED (VoiceMeeter8 kurulum artığı olabilir) |
| `vbvm`/`vmaux`/`vmins`/`vpins`/`vaio3`/VAIO64 | **YOK** | ASIO bridge + VAIO64 kurulu değil — yalnız kurulum dosyaları diskte (`Program Files`); uygulama da çalışmıyor |

---

## §10 FxSound fxvad.sys RE (2026-10-15 — PE resource + oem39.inf)

### §10.1 Sürücü künyesi

| Alan | Değer |
|---|---|
| Dosya | `C:\Windows\System32\drivers\fxvad.sys` · 326 656 B · SHA-256 `425629b6309000013e8cd1a9b827bee365d21c9f743873aadd0c3bc96a999d2a` |
| PE | PE32+ · AMD64 (machine 34404) |
| VersionInfo | `FileVersion 14.1.0.0` · `ProductName FxSound` · `FileDescription FxSound Audio Enhancer` · `CompanyName "Windows (R) Win 7 DDK provider"` · `Copyright FxSound LLC` · `OriginalFilename fxvad.sys` · **`IsPrivateBuild True`** |
| Servis (registry) | `HKLM\SYSTEM\CurrentControlSet\Services\fxvad`: `Type=1` (KERNEL) · `Start=3` (MANUAL) · `ErrorControl=1` · `ImagePath=\SystemRoot\system32\drivers\fxvad.sys` · `DisplayName=@oem39.inf,%DFX_Device.SvcDesc%` · `Owners=oem39.inf` |
| Kurulum INF | `C:\Windows\INF\oem39.inf`: **`Signature="$CHICAGO$"`** (1995 imza stringi — DFX mirası) · `DriverVer = 03/17/2021,14.1.0.0` · `[DFX_Device]` bölüm adları · `ServiceType=1` · `ServiceBinary=%10%\system32\drivers\fxvad.sys` · `HKR,Drivers,SubClasses,,"wave,midi,mixer"` (eski wave/midi/mixer API) |

### §10.2 Resource envanteri (RE `inspect_pe_resources`)

12 resource (80 girdi tarandı, coverage=complete): RT_ICON×9 (**192·128·96·64·48·32·24·16 px** + 1 default;
32-bit high-color üstler) + RT_GROUP_ICON `RESNAME` (132 B) + RT_VERSION (760 B) + RT_RCDATA (55 B).
Icon set boyutu = masaüstü uygulama ikonu tam merdiven (VoiceMeeter'in 2-icon setinin çok üzerinde).

> **Çıkarım:** fxvad = **DFX Audio Enhancer mirası** (INF bölüm adı `DFX_*`, `$CHICAGO$`, `Apps\DfxInstall.dll`
> 128 976 B + `DfxSetupDrv.exe` kurulum zinciri). Sürücü 2021 derlemesi ama kod tabanı Win7 DDK (8.1/10 WDK
> Provider string'i legacy). `Start=3` (manual) = FxSound.exe başlarken başlatır (10 doc 09 felaket yolu ile
> aynı NDIS olmayan pattern). **FxSound XP/Vista/Win7 varyantları bu sistemde YOK** — tek sürücü (modern
> Windows hedefli); VB ailesinin 5-OS matrisinin tam tersi strateji (§9.1).

### §10.3 Uygulama paketi (2026-10-15 disk)

`C:\Program Files\FxSound LLC\FxSound\`: `FxSound.exe` (3 819 472) · `fxmcp.exe` (6 747 600 — §11) ·
`fxdiag.exe` (357 840) · `updater.exe` (1 675 216) · `fxmcp.mcpb` (16 197 — ZIP: `icon.png` + manifest,
PK imzası okundu) · font seti (IBM Plex · Montserrat · Noto Sans JP/KR/SC/TC/Thai — CJK/Thai UI).
Uninstall kaydı: `FxSound | 1.2.13.0 | C:\Program Files\FxSound LLC\FxSound\`.

---

## §11 fxmcp.exe Go build-info (2026-10-15 — RE `inspect_go_binary`)

| Alan | Değer |
|---|---|
| SHA-256 | `c37649fbe35c7151b53ce0ebf86783952f0dedd874cd140c3eb841f8b8e39369` (6 747 600 B, pe/amd64) |
| Go | **`go1.26.5`** (inline buildinfo, offset 6 177 280) |
| Main | module `fxmcp` (devel) · pkg `fxmcp/cmd/fxmcp` |
| MCP SDK | **`github.com/modelcontextprotocol/go-sdk v1.6.1`** (resmi MCP Go SDK) |
| Bağımlılıklar | `google/jsonschema-go v0.4.3` · `segmentio/asm v1.1.3` · `segmentio/encoding v0.5.4` · `yosida95/uritemplate/v3 v3.0.2` · **`golang.org/x/oauth2 v0.35.0`** · `x/sys v0.41.0` · `x/text v0.38.0` |
| Build | `CGO_ENABLED=0` · `GOOS=windows GOARCH=amd64` · `-ldflags "-s -w"` (stripped) · `-buildmode=exe` |
| VCS | git rev `c2eb75977bb3f413e1e76b9782c9778f983f6ebf` · time 2026-08-18T07:38:52Z · **`vcs.modified=true`** |

> **Çıkarımlar:** (1) FxSound, **resmi MCP go-sdk** ile masaüstü MCP kontrol sunucusu işletiyor — AI
> asistanı entegrasyonu bilinçli ürün özelliği (doc §3.4 bulgusunun kesinleşmesi). (2) `oauth2` bağımlılığı
> → MCP OAuth2 yetkilendirme akışı (Claude Desktop vb. için). (3) `vcs.modified=true` = depo kirli
> ağaçtan derlenmiş (kaynak rev'e birebir değil). (4) `-s -w` + devel = üretim-değil hızlı iteratif
> sürüm — Neva için ders: MCP köprüsü (R12/R13 muadili) Go'da tek static exe olarak paketlenebilir
> (CGO=0 → tek `.exe`, DriverStore bağımlılığı yok).

---

## §12 OS Matrisi → Neva stratejik dersleri (kullanıcı direktifi: 1=XP legacy · 2=Win7 tam destek)

| # | Ders | Kanıt |
|---|---|---|
| 1 | **Tek kod, çok suffix** (VB modeli): WDM çekirdek + per-OS INF/PE varyantı — Neva'da mantık: ortak DSP çekirdeği, per-OS loader (doc 09 + doc 03) | §9.1-9.3 |
| 2 | **Legacy dal (1.seçenek XP):** VB XP'ye wdmaud+redbook wave zinciriyle destek veriyor ama Neva XP dalı (legacy) yalnızca minimal host — kernel sürücü yazımı yerine OSS virtual driver (doc 09: vb-cable tabanlı) veya taşıma | §9.3 xp INF |
| 3 | **Win7 tam destek (2.seçenek):** VB win7 INF = win10 ile aynı DriverVer (2.1.5.2) — Win7 hedefi XP'den AYRI kod değil, aynı çekirdek + target-OS INF. Neva Win7 dalı JUCE/ASIO SDK Win7 sürümüne kilitlenmeli | §9.3 |
| 4 | **DriverStore yolu** (`%13%`) Win10+ zorunlu (VAIO64) — Neva Win10 sürücüsü/driver bağımlılığı yoksa da INF/USB kurulumu yazarsa bu deseni kullanır | §9.3 VAIO64 |
| 5 | **ARM64 erken** (VAIO64 arm varyantı hazır) — Neva native mobil (R13) kararında Windows-on-ARM masaüstü senaryosu VB tarafından desteklenmiş bile | §9.1 |
| 6 | **Tek modern sürücü stratejisi** (FxSound fxvad, yalnız modern Windows hedefi) Neva için en uygun: XP legacy ayrı yatırım değil | §10.2 + §9.1 |
| 7 | **MCP köprüsü** masaüstünde Go static exe ile çalışıyor (fxmcp) — Neva'da PHP yerel sunucu (R11) + varsa MCP köprü bağımsız tek binary olabilir | §11 |

---

## §13 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk RE raporu (string + MCP jsonschema) |
| 1.1.0 | 2026-10-15 | Tur 2: VersionInfo + PDB + Remote API + Potato string |
| 1.2.0 | 2026-10-15 | Tur 3: Ghidra decompile + ASIO sürücü RE (`vbvm*_asiodriver64.dll` ×5) |
| 1.3.0 | 2026-10-15 | **Tur 4: OS & sürücü matrisi** — VB 29-sürücü dosya matrisi (5 OS × 6 aile, PE resource envanteri + INF DriverVer) · FxSound fxvad.sys PE resource + oem39.inf (DFX mirası + `$CHICAGO$`) · fxmcp.exe Go build-info (go1.26.5 + resmi MCP SDK v1.6.1 + oauth2) · §9-§12 yeni · kurulu sistem registry durumu |

**Authority:** Embedded Engineer (L1) · Statü: ANALİZ TUR 2+3+**Ghidra** (string/resource
+ decompile katmanı tam — Banana+Potato+FxSound+GEQ15+**ASIO sürücü ×5** · EQ band=31
KAPANDI · ASIO param aralıkları KAPANDI · DSP iç matematik + outputReady opsiyonel açık)

