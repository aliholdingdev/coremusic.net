---
title: "Neva Engine — Açık Kaynak Sanal Ses Sürücüleri Envanteri & Karşılaştırma (VB-Audio / FxSound ile)"
type: doc
category: audio-engine
version: 1.2.0
status: analiz
authority: "Kullanıcı direktifi 2026-10-15 (sıfırdan proje kurulumu için OSS veri tabanı) · GitHub repo README/LICENSE kanıtları · disk kanıtları (DriverStore/servisler) · MS Download Center/Learn doğrudan fetch (2026-10-15 — WDK matrisi ⚠️'leri kapatıldı)"
updated: 2026-10-15
---

# §1 Amaç & Kapsam

Kullanıcı direktifi (2026-10-15): **GitHub'dan açık kaynak virtual audio driver bul** —
Windows, Linux, macOS her işletim sistemi için — sonra **VB-Audio sürücüleri (`.sys`) ve
FxSound sürücüsü ile karşılaştır**; **sıfırdan proje kurmak için veri tabanı** olarak
kullan. Her aday **OS type/version uyum** sütunu taşır:

| Seçenek | OS | Rol |
|---|---|---|
| **1. seçenek** | **Windows XP** | Legacy dal (07 §3 — ayrı modül, sınırlı özellik) |
| **2. seçenek** | **Windows 7** | Modern dal **tam destek** (07 §3 — C++20 · JUCE 9 · ASIO <10ms) |

> OS hedefleri Q1/Q3 kararlarıyla (2026-10-15) vault'a işlidir; bu rapor her bulguyu
> bu iki OS'a göre puanlar. Zero-hallucination: disk/README kanıtı olmayan hiçbir
> iddia yazılmaz; doğrulanamayan `⚠️ VERIFICATION REQUIRED`.

# §2 OS Hedef Matrisi (bağlayıcı — 01/07 ile uyumlu)

| Dal | OS | Min sürüm | Ses API'leri | Sürücü stratejisi |
|---|---|---|---|---|
| **Legacy** | Windows XP | SP3 (32-bit) ⚠️ VERIFICATION REQUIRED (64-bit XP senaryoya bağlı) | MME · DirectSound · **WASAPI YOK** · KS (kernel streaming) sınırlı · ASIO host çalışır | Sanal sürücü **opsiyonel**; XP hedefleme **yalnız eski araçlarla** (§2.1): XP DDK 2600 · WDK Server 2008 SP1 (6001.18002) · **son kapsayıcı kit = WDK 7.1.0 (7600.16385.1)** — WDK 8.1/10 ile XP hedefi **YOK** |
| **Modern** | Windows 7 | **SP1 x64** | MME · DirectSound · WASAPI (shared+exclusive) · **ASIO** · KS | Sanal sürücü gerekirse **WDM/KS (PortCls WaveRT/Cyclic)** — Win7 WDK ile imzalı yüklemesi mümkün |

**Kritik XP gerçeği:** Microsoft'un resmi sürücü örnekleri (Sysvad vb.) **Windows 8+**
hedefler; XP için OSS sanal audio sürücüsü **bulunamadı** (envanter §3 — XP uyumlu tek
OSS yok). XP legacy dalı sürücü yazmadan, mevcut cihazların MME/DirectSound/ASIO
host katmanıyla çalıştırılabilir (Neva host = zaten cihaz tüketir, cihaz üretmez).

## §2.1 Derleme Araç Matrisi — WDK (2026-10-15 ARAŞTIRMA · KAPANDI)

| Araç | Build / tarih | Resmi hedef aralığı | Neva dalı |
|---|---|---|---|
| Windows XP DDK | 2600 (2001-09-21) | Windows XP | 🗄️ yedek — **resmi Microsoft indirmesi silinmiş** (2026-10-15: "All legacy WDKs are gone" OSR + MS Previous-WDK sayfası); topluluk arşivi mevcut (GitHub `KunYi/WDK_DDKArchive` releases, XP DDK 5.1.2600.1106) ⚠️ lisans/üçüncü taraf kaynağı — **gerçek ihtiyaç WDK 7.1.0 ile karşılanır, XP DDK indirilmez** |
| WDK Server 2008 SP1 | 6001.18002 (2008-12-08) | XP SP1 → Vista SP1 · Server 2000 SP4 → 2008 SP1 | — |
| **WDK 7.1.0** | **7600.16385.1 (2010-02-26)** | **XP SP3 → 7 · Server 2003 SP1 → 2008 R2** | ⭐ **tek kit = hem 1. seçenek (XP) hem 2. seçenek (Win7)** |
| WDK 8+ / Windows Kits 10 | (modern) | Win8/10+ — Sysvad bu ailede | yalnız Win10+ hedefi |

**Sonuç (v1.2.0 — KAPANDI, 2026-10-15):** XP legacy ve Win7 modern **tek WDK 7.1.0
kurulumuyla** derlenebilir; WDK 7.1.0 **resmi indirme sayfası hâlâ canlı** (Microsoft
Download Center id=11800 · `GRMWDK_EN_7600_1.ISO` · 619.8 MB · yayın 2024-07-15 · WGA
genuine-Windows doğrulaması istenir — kurumsal makinede engel olabilir). **Down-level
(Win7) hedefleme tekniği artık resmi MS dokümanı** (eski ⚠️ kalktı): MS Learn
`building-drivers-for-different-versions-of-windows` → `.vcxproj` içinde
`<TargetVersion>Windows7</TargetVersion>` · MS TechCommunity (2024)
`_NT_TARGET_VERSION` (appendix A, VS Driver Settings) — "fully supported by Microsoft".
**Ancak WDK 10 sürüm sınırı:** 10.0.19041 (2004) = "Supported for Windows 7/Windows 8/
Windows 8.1 driver development only" · 10.0.22621 (22H2) = yalnız Win10 x86/ARM32 →
**Win7 hedefi için son WDK 10 = 19041; 22621+ Win7 hedefini resmen düşürdü**. XP
hedefi yalnız WDK 7.1.0 (XP DDK/6001 opsiyonel — resmi indirme silindi, bkz. satır).
Win7/Vista WDM linker gotcha'sı: `BufferOverflowK.lib` gerekir (8/8.1 config
`BufferOverflowFastFailK.lib` linkler — aşağı sürümde çalışmaz, MS Learn notu).
Kaynak: MS Download Center 11800 (fetch 2026-10-15) · MS Learn build-for-different-versions ·
MS TechCommunity down-level blog · MS Learn other-wdk-downloads sürüm tablosu ·
OSR "All legacy WDKs are gone" + "WDK 7.1.0 ... XP to Win8, includes compiler" (Mark Roddy).
→01 §Faz 0 envanterine eklenecek (araç indirme, kullanıcı onayı).

# §3 Windows — Açık Kaynak Sürücüler (GitHub)

| # | Proje | Lisans | Mimari | OS uyum (XP / Win7 / Win10+) | Neva değeri |
|---|---|---|---|---|---|
| 1 | **[VirtualDrivers/Virtual-Audio-Driver](https://github.com/VirtualDrivers/Virtual-Audio-Driver)** (MikeTheTech) | **MIT** (+MS-PL — Sysvad türevi third-party) | **WDK kernel sürücü**: sanal speaker + sanal mikrofon (Root-enumerated, INF: `VirtualAudioDriver.inf`); format 8-bit/8kHz → 32-bit/192kHz; Windows Sonic spatial; exclusive mode | ✗ / ✗ / **✓ Win10 1903+ x64·ARM64** (README Compatibility) — **beta, test-signing zorunlu** | ⭐ **En yakın mimari emsal**: Sysvad türevi, INF+AddReg deseni, kanal/format tablosu — Win7'ye taşınabilir WaveRT deseni (⚠️ taşınabilirlik doğrulanmadı — hedef WDK sürümü Win10) |
| 2 | **[microsoft/Windows-driver-samples → audio/sysvad](https://github.com/microsoft/Windows-driver-samples/tree/main/audio/sysvad)** (Sysvad) | Microsoft Driver Samples (MS-PL) | **WDM WaveRT sanal cihaz** (KMDF+WDM karışık); Microsoft resmi örneği; tablet/multi-device varyantları | ✗ / **⚠️ AÇIK — resmi Sysvad WDK10/Win8+ hedefli** · **Win7 yolu (araştırma 2026-10-15): WDK 7.1.0 içindeki Msvad WDM ses örneği** (portcls/WaveRT API'si Vista'dan beri Win7'de mevcut) — Msvad readme'si OS = **Win7 / Server 2008 R2** (kaynak ✓, fiziksel derleme Faz 0'da ⚠️) / ✓ | ⭐ **Sıfırdan proje başlangıç noktası** — #1'in de kaynağı; WaveRT topology, miniport, port/class factory desenleri. Win7 tam destek için: Sysvad deseni **temiz oda + WDK 7.1.0 hedefiyle yeniden derlenir** (kod kopyalanmaz, desen taşınır) — kaynak doğrulaması ✓, derleme Faz 0 ⚠️ |
| 3 | **[dechamps/FlexASIO](https://github.com/dechamps/FlexASIO)** | **MIT** (ASIO SDK ayrı lisans — LICENSE.txt'te açık not) | **User-mode ASIO driver** (COM, PortAudio tabanlı); backend: WASAPI shared/exclusive · KS · DirectSound · MME; config dosyası; default 32-bit float / 20 ms buffer | **✓ Vista+** (README) → **Win7 ✓** · XP ✗ (Vista min) | ⭐⭐ **Neva ASIO host test aracı + mimari referans**: evrensel ASIO driver nasıl kurulur (DllGetClassObject deseni 04 §8.2 ile aynı); Win7'de çalışır → Faz 0 doğrulama zinciri |
| 4 | **[levmin/ASIO2WASAPI](https://github.com/levmin/ASIO2WASAPI)** (referans) | **GPL** (repo `gpl.txt` — GNU Public License; `Install.cpp` başlık: "ASIO2WASAPI is free software; you can redistribute it and/or modify" — 2026-10-15 fetch) | User-mode ASIO → WASAPI exclusive | ✗ / ✓ (Vista+ WASAPI) / ✓ | Düşük — FlexASIO kapsamını kapsıyor (FlexASIO: ASIO2WASAPI "exclusive mode only" notu) |
| 5 | **USB/IP sanal USB audio** (topic: virtual-audio-cable) | **Karışık** (2026-10-15 fetch): `dorssel/usbipd-win` **GPL-3.0** (6.2k⭐ · sunucu/WSL tarafı, client tooling proje dışı — issue #1342) · `vadimgrn/usbip-win2` **BSD-2-Clause** (1.6k⭐ · Windows client) · `cezanne/usbip-win` lisansı OKUNMADI ⚠️ VERIFICATION REQUIRED · sourceforge `usbip` (legacy, 2009-2011 — ReactOS imzasıyla Win7/Vista x64, aktif geliştirme YOK) | Sürücü yazmadan **sanal USB audio cihazları** (Windows yerleşik USB audio class driver'ı kullanır) | ⚠️ PARTIAL: usbipd-win/usbip-win2 modern Windows (Win10+ ağırlıklı ⚠️ XP/Win7 client matrisi doğrulanmadı) · legacy sourceforge client = Win7/Vista x64 (ReactOS sertifikası, 2011 kaynağı) · XP = sınır (WinUSB/driver imza) | Düşük-karmaşık: XP WinUSB ile sınırı aşar; ama "driver imzalama derdi yok" avantajı |
| 6 | Valve **Steam Streaming Speakers** (DriverStore kanıtı — 3. taraf tartışma) | Kapalı (Valve) | Hazır imzalı sanal endpoint (DriverStore `steamstreamingspeakers.inf_amd64`) | Win10+ | Sadece emsal: "tek INF ile sanal endpoint" mümkün |

**Windows OSS sonucu:** XP için OSS sanal sürücü **YOK**. Win7 için **hazır OSS yok**
(mevcutlar Win8/10 hedefli); ancak **FlexASIO (MIT, Vista+/Win7 ✓)** sanal cihaz
yazmadan ASIO katmanı ihtiyacını karşılıyor. Sanal **device** gerekirse temiz oda +
Sysvad deseni (#2) referans alınır (clean-room, MS-PL kod kopyalanmaz — desen öğrenilir).

# §4 Linux — Açık Kaynak (Windows hedefinin referansı)

| # | Proje | Mimari | Lisans | Not |
|---|---|---|---|---|
| 1 | **`snd-aloop`** (in-tree kernel module) | ALSA loopback PCM — `snd_aloop` modprobe ile sanal ses kartı | GPL-2.0 (Linux kernel) | Tek komutla sanal card; PipeWire/PulseAudio üstünde çalışır |
| 2 | **PipeWire** (native virtual devices) | `pw-loopback`, `null-sink`, `virtual-source` — **user-space**, sürücü yazımı yok | MIT | Modern Linux ses sunucusu; grafik patchbay (qpwgraph) |
| 3 | **PulseAudio** (`module-null-sink`, `module-virtual-source`, `module-loopback`) | User-space mixing | LGPL-2.1 | Eski ama yaygın |

**Neva dersi:** Linux'ta sanal cihaz **user-space** ile çözülüyor (kernel modülü yerine
sunucu katmanı); Neva R17 mobile DSP bağlamında Android/iOS'ta da benzer: **AAudio/
Oboe/AVAudioEngine user-space** — kernel sürücü yazımı yalnız Windows'ta kaçınılmaz.

# §5 macOS — Açık Kaynak

| # | Proje | Mimari | Lisans | OS uyum |
|---|---|---|---|---|
| 1 | **[ExistentialAudio/BlackHole](https://github.com/ExistentialAudio/BlackHole)** | **CoreAudio AudioServerPlugIn** (user-space AUHAL plugin — **kext değil**); zero-latency loopback; 2/16 kanal sürümleri | **GPL-3.0** (kaynak kod — raw LICENSE kanıtı, 2026-10-15 · "The BlackHole source code is licensed under the GNU General Public License v3.0") · resmi derlenmiş binary + ad/logo/branding **ayrı telif** (Existential Audio Inc.) — resmi olmayan build marka kullanamaz · GPLv3 §13 AGPL ile kombinasyona izin verir (FxSound AGPL repo ile teknik olarak birleşebilir) | macOS 10.10+ (SourceForge sayfası) · Intel + Apple Silicon |

**Neva dersi:** macOS'ta sanal cihaz **kext'siz** user-space plugin ile çözülüyor —
Apple'ın AudioServerPlugIn modeli sürücü imzalama derdini tamamen kaldırıyor. XP/Win7
karşılığı yok (Windows'ta bu model APO Extension katmanı, §7).

# §6 VB-Audio Sürücüleri — Disk Kanıtları (bu makine)

| Varlık | Kanıt | Yorum |
|---|---|---|
| **VAIO kernel driver** | Servis `VBVoicemeeterVAIOMME` → `\SystemRoot\System32\DriverStore\FileRepository\vbvoicemeetervaio64_win10.inf_amd64_...\vbvoicemeetervaio64_win10.sys` | **WDM sanal ses cihazı** (VAIO = Virtual Audio Input/Output); Win10 varyantı kurulu |
| **Cable driver** | Servis `VB-MultiCable` (ImagePath boş okundu ⚠️ — muhtemelen DriverStore altında `transfercable.inf_amd64_*`) | VB-Cable ailesi; transfercable DriverStore'da mevcut |
| **Sınıf kaydı** | `{4d36e96c-e325-11ce-bfc1-08002be10318}\0007` = "VB-Audio Voicemeeter VAIO" · v3.4.1.7 · oem52.inf | Media class (ses cihazı) |
| **ASIO COM katmanı** | `vbvm*_asiodriver64.dll` ×5 (04 §8 — v1.0.2.2) + `HKCR\ASIO` 5 CLSID | **User-mode ASIO shim** → shared memory → kernel VAIO | 
| **Sahiplik deseni** | 04 §8.3: SR/buffer engine'den shared-memory ile | Cihaz bazlı dinamik varsayılan (R18 emsalı) |

**VB mimari özeti:** `kernel WDM sanal endpoint (`.sys`)` + `user-mode mixer engine
(voicemeeter8x64.exe)` + `user-mode ASIO COM shim (vbvm*_asiodriver64.dll)` + `IPC =
shared memory + multimedia timer`. Dört katmanlı; Neva için 1:1 hedeflenebilir (engine
= Neva C++ çekirdek; ASIO shim = JUCE ASIO host; sanal endpoint = opsiyonel faz).

# §7 FxSound Sürücüsü — Disk Kanıtları (bu makine)

| Varlık | Kanıt | Yorum |
|---|---|---|
| **⭐ fxvad.sys (kernel driver!)** | `C:\Windows\System32\drivers\fxvad.sys` (326.656 B) · servis `fxvad` **Type 1 (kernel)** Start 3 (demand) · DriverStore `fxvad.inf_amd64_a75d87b3871a94a9` (inf 5.334 B + cat) · sınıf `{4d36e96c}\0005` "FxSound Audio Enhancer" v**14.1.0.0** (DriverVer 17.03.2021) · `MatchingDeviceId: Root\FXVAD` · `AddReg=DFX_Device.AddReg` | **FxSound tamamen driver'sız DEĞİL** — Root-enumerated WDM sanal audio endpoint (`Root\FXVAD`); kurulum `fxdevcon64.exe` (04 §3.3) INF'i DriverStore'a yazıyor. Eski DFX kökenli AddReg adı ("DFX_Device") → miras |
| **⭐ fxsound-app — AÇIK KAYNAK (AGPL-3.0)** | `fxmcp.mcpb` manifest.json: `"license": "AGPL-3.0"`, `"repository": github.com/fxsound2/fxsound-app` · LICENSE 35.184 B (AGPL metni) | **FxSound uygulaması GitHub'da açık kaynak** — DSP/effekt kodu (EQ, surround, ambience) AGPL-3.0 ile incelenebilir. **Neva temiz-oda kuralı:** AGPL kodu kopyalanamaz/entegre edilemez (copyleft) — yalnız **davranış/parametre referansı** |
| **fxmcp.mcpb** | ZIP: icon.png + LICENSE + manifest.json (1.441 B) — MCP bundle v0.3, entry `fxmcp.exe`, platform win32 | AI asistan kontrol paketi — 04 §3.4 tamamlayıcısı |
| **fxdiag.exe** | PDB: `C:\Users\vijay\...\FxSound\main\fxsound-app\fxdiag\x64\Release\fxdiag.pdb` | Repo ağacı `fxsound-app/` — manifest ile tutarlı |

# §8 Karşılaştırma Matrisi → Neva Sıfırdan Proje Stratejisi

| Katman | VB-Audio (disk kanıtı) | FxSound (disk kanıtı) | OSS referans | **Neva kararı** |
|---|---|---|---|---|
| Kernel sanal endpoint | ✓ `vbvoicemeetervaio64_win10.sys` (WDM) | ✓ `fxvad.sys` (Root\FXVAD, WDM, 326 KB) | Sysvad (MS-PL desen) · Virtual-Audio-Driver (MIT) | **Faz 4+ opsiyonel** — Neva başta **endpoint üretmez**, mevcut cihazlara ASIO/WASAPI ile bağlanır (R2 10 çıkış = physical device matrix) |
| User-mode engine | ✓ voicemeeter8x64.exe (mixer+DSP) | ✓ FxSound.exe (DSP, OpenGL UI) | PipeWire/BlackHole (user-space felsefesi) | ⭐ **Neva çekirdeği bu katman** — C++20, JUCE 9, R17 mobile aynı çekirdek |
| ASIO COM shim | ✓ vbvm*_asiodriver64.dll (04 §8) | — (FxSound ASIO host tüketir) | **FlexASIO (MIT)** | JUCE ASIO host (Faz 0); FlexASIO test zinciri |
| Kontrol API | ✓ VBVMR (Remote64.dll) | ✓ CLI + fxmcp (MCP) + açık kaynak | — | Ad-tabanlı API (04 §7.7.4) + R11 PHP control plane |
| IPC | ✓ shared memory + timer | ✓ (capture thread + resample) | — | Engine↔UI: named pipe/local socket (11 uzak istemci, 05 §2) |
| Cihaz algılama | ✓ SR/buffer shared-memory'den | ✓ MMNotificationClient hot-swap (04 §3.3) | — | **R18**: OS device-change callback → per-device profil |
| Lisans riski | Kapalı (clean-room şart) | **AGPL-3.0 açık** (incelenebilir, kopyalanamaz) | MIT/MS-PL/Apache temiz referanslar | Clean-room + MIT kaynak önceliği |

# §9 OS Uyumu & Varsayılan Politikası (bağlayıcı — 01/06/07 ile)

1. **1. seçenek Win XP (legacy):** OSS sanal sürücü **yok** → XP dalı **endpoint üretmez**;
   MME/DirectSound + ASIO host ile mevcut cihazları tüketir; DSP zinciri özellik-kısıtlı
   (01 §legacy). **ARAŞTIRMA KAPANDI (2026-10-15):** XP araç hedefi = **WDK 7.1.0
   (7600.16385.1)** — §2.1 matrisi; WDK 8.1/10 ile XP hedefi yok.
2. **2. seçenek Win 7 (modern, tam destek):** FlexASIO (Vista+ ✓) ile ASIO zinciri
   doğrulanabilir; sanal endpoint gerekirse **yol = WDK 7.1.0 + Msvad deseni** (resmi
   Sysvad örneği WDK10/Win8+ hedefli — §3 #2). **Msvad→Win7 KAYNAK DOĞRULAMASI
   TAMAM (v1.2.0, 2026-10-15):** MSVAD sample readme'si OS gereksinimini açıkça
   **Client: Windows 7 · Server: Windows Server 2008 R2** olarak yazıyor (WDK 8 samples
   paketi description.html — uri247/wdk81 arşivi); MS Learn "Sample Audio Drivers"
   Msvad'ı WDK örnek listesine taşır; "For Windows 7, samples were included in the WDK"
   (MS Learn samples). **Kalan tek adım = fiziksel derleme denemesi** (kendi makinemizde
   WDK 7.1.0 + Msvad → .sys) — bu **Faz 0'a** ertelendi ⚠️ (araştırma modu, kod yasağı);
   kağıt/kanıt tarafı kapanmıştır, tasarım riski yok sayılıyor.
3. **R18 — cihaz bazlı otomatik varsayılan (kullanıcı direktifi 2026-10-15):** global
   sabit varsayılan (44100 Hz/512 smp) **YOK**. Varsayılanlar **cihaza göre otomatik
   tespit** edilir; yeni cihaz bağlandığında/mevcut cihaz değiştiğinde OS device-change
   callback'i ile cihazın yeteneği (SR/buffer/kanal) okunur ve **o cihaza özel profil**
   otomatik uygulanır. Em-saller: VB engine sahipliği (04 §8.3) · FxSound hot-swap
   (04 §3.3) + per-device `device_configs` (04 §7.7). R5 profil sistemiyle entegre.
   (06 R18'e işlendi.)
4. **Temiz-oda kuralı:** VB (kapalı) ve FxSound AGPL kodu kopyalanmaz — yalnız
   parametre/davranış/şema referansı; MIT/MS-PL OSS kaynaklar desen kaynağı.

---

## §10 Faz 0 Öncesi Kural Okuması — `legacy-2026-07/rules/` (4/4 OKUNDU · 2026-10-15)

> Kullanıcı direktifi ("sadece araştırma, kod yok") — 4 kural dosyası okundu; bağlayıcı
> maddeler burada toplandı. Tam metin: `.ai/projects/NevaEngine/legacy-2026-07/rules/`.

| Dosya | Satır | Bağlayıcı öz |
|---|---|---|
| `neva-engine-CLAUDE.md` | 648 | <5 ms gecikme hedefi (RTL ASIO @ 32 smp/96 kHz) · THD+N <0.001% @1 kHz · REST **9741** / WS **9742** · paylaşılan bellek ring buffer (Neva Player↔Engine) · headless VST3/LV2 host · FFT 30 FPS WebSocket · stateless JSON config · her commit sonrası wiki-sync checklist |
| `neva-engine-AGENTS.md` | 335 | **5 kritik gotcha:** (1) audio callback'te allocation **YASAK** (pre-allocated workBuffer) · (2) denormal → FTZ/DAZ (`juce::FloatVectorOperations::disableDenormalisedNumberSupport`) · (3) ring buffer **lock-free atomik** (memory_order_release) · (4) VST3 param güncelleme → **lock-free kuyruk** (UI thread → callback drain) · (5) ASIO latency compensation (timestamp'li spectrum frame) |
| `cpp-global-CLAUDE.md` | 704 | vcpkg + **CMake 3.20+** + MSVC 2022 araç zinciri · 5 proje (Engine/Player/WirelessConnect/NevaConnect/shared) · port grep denetimi (9741/9742/9743/3001 ≈ 10-15 referans) · ctest smoke · derleme komutları (cmake -B build + toolchain file) |
| `cpp-global-AGENTS.md` | 386 | **5 gotcha:** callback thread safety (mutex/std::cout/malloc yasak) · Metal GPU thread affinity · shared-memory RAII + crash sonrası stale PID temizliği (OpenProcess alive check) · port fallback ADR-031 (9741 doluysa 9741+99) · WebSocket handshake versiyonlama ADR-032 · **eskalasyon:** güvenlik/kredital/ASIO-davranış/threading/API-kırıcı = dur-sor · standart: C++20 RAII, smart pointer, lock-free callback, `std::optional/std::expected` |

**Faz 0'a etkisi:** kural seti C++20 · JUCE · ASIO hedefiyle uyumlu; bu 10 gotcha Faz 0
sonrası kod yazımında LINT gibi uygulanacak. İlgili ADR'ler (okunacak): `.ai/.decisions/`
→ 017 (RPi5) · 019 (per-OS Player) · 025 (EQ) · 026-027 (WirelessConnect) · 031-032
(IPC port + handshake versiyonlama).

---

**Authority:** Embedded Engineer (L1) · **Statü (v1.2.0):** OSS ENVANTERİ + ARAŞTIRMA
TAMAM — Windows 6 proje · Linux 3 · macOS 1 · VB disk kanıtları · FxSound fxvad.sys +
AGPL repo · **XP/Win7 araç matrisi = WDK 7.1.0 (§2.1 KAPANDI)** · **BlackHole = GPL-3.0
(§5 KAPANDI)** · **3 açık ⚠️ KAPANDI (2026-10-15 MS fetch — §2.1/§3/§9):**
(1) *Msvad→Win7* → MSVAD readme OS = **Win7/Server 2008 R2** ✓ kaynak kapatıldı,
kalan = fiziksel derleme (Faz 0) ⚠️ · (2) *XP DDK indirilebilirlik* → resmi MS
indirmesi **silinmiş**, topluluk arşivi (KunYi/WDK_DDKArchive) var, gerçek ihtiyaç
**WDK 7.1.0 ISO ile karşılanır** (canlı, id=11800, 619.8 MB) ✓ · (3) *down-level WDK
tekniği* → resmi MS dokümanı (`TargetVersion=Windows7` + `_NT_TARGET_VERSION`) ✓;
sınır: WDK 10.0.19041 son Win7-hedefli sürüm, 22621+ düşürdü; WDM Win7 linker =
`BufferOverflowK.lib` · **rules/ 4/4 okundu (§10)** · **§3 #4 ASIO2WASAPI = GPL ·
#5 USB/IP lisansları kısmen kapatıldı**. Kalan ⚠️: 64-bit XP senaryo bağlı (§2) ·
`cezanne/usbip-win` lisansı okunmadı · §3 #1 Win7 taşınabilirlik · Faz 0 derleme denemesi
