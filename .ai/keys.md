---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Vault Keyword Map & Concept Router"
type: system
category: vault-navigation
date: 2026-08-12
updated: 2026-10-06
status: active
version: 28.3.6
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/keys.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/WORKFLOW.md · .ai/brain.md · .ai/index.md"
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: []
---

# CoreMusic — Vault Keyword Map & Concept Router

**Zorunlu Baglantilar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (8 aktif skill — Guardrail #16 zorunlu)

---

## Purpose

### §1 Amac

Bu dokuman, .ai/ vault icinde aranan kavramlarin aninda tespit edilmesini saglayan Master Kavram ve Dizin Yonlendirme Haritasidir. Ajanlarin ilk basvurduğu referans dosyasidir.

---

## Scope

### §2 Core Vault Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| vizyon, vision, felsefe, pazar krizi, mülkiyet, handoff, sorunlar | VISION.md |
| proje, projects, coremusic nedir, yetenekler, 10 ozellik, sektorler | PROJECTS.md |
| index, master, katalog | index.md |
| brain, mimari, dsp, kararlar | brain.md |
| memory, bellek, persistent, session | MEMORY.md |
| log, aktivite, audit, trail | log.md |
| agent, yetki, roller, handover | AGENTS.md |
| workflow, surec, faz, lifecycle | WORKFLOW.md |
| claude, talimat, protokol, anayasa | CLAUDE.md |
| engine, orkestra, dispatch | engine.md |
| keys, keyword, navigasyon | keys.md |
| planner, context plan, context loading, bağlam yükleme, tier, domain metadata | PLANNER.md |
| rag, retrieval, indeks, embedding, arama, vektor, chunk, similarity, pipeline | RAG.md |
| glossary, sozluk, terimler | glossary.md |
| architecture-master, canonical count, metadata, ADR count, DB count, layer count | architecture/index.md |

---

### §4 Security Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| CSRF, csrf_token, koruma | [[.decisions/accepted/ADR-010-csrf-protection-strategy]] |
| Session, COREMUSIC_SESS, idle | [[.decisions/accepted/ADR-011-session-management]] |
| CSP nonce, strict-dynamic | [[.decisions/accepted/ADR-012-csp-nonce-strict-dynamic]] |
| Rate Limit, APCu, 60 req/60s | [[.decisions/accepted/ADR-013-rate-limiting-apcu]] |
| Argon2id, AES-256-GCM, sifreleme | [[.decisions/accepted/ADR-022-database-hardened-security]] |
| credential vault, secret | [[.decisions/accepted/ADR-034-credential-vault-normalization]] |
| BypassAuth, test bypass | [[.decisions/accepted/ADR-008-bypass-auth-middleware]] |
| auth, kimlik dogrulama | subdomains/auth.coremusic.net/index |
| OWASP, Top 10 | architecture/k6-guvenlik/ |
| encryption | architecture/k6-guvenlik/ |
| API security, token | architecture/k6-guvenlik/ |
| loglama, logging, PSR-3, Monolog | architecture/k12-izleme/ |
| log_events, log_security, log_performance | architecture/k12-izleme/ |
| log_activity, log_system, redaction | architecture/k12-izleme/ |
| real-time log, dashboard log, monitor | architecture/k12-izleme/ |
| dosya rotasyonu, log rotation, arsiv | architecture/k12-izleme/ |
| social oauth, gender-based oauth, cinsiyet bazlı sosyal medya | [[brain.md]] ADR-088-gender-based-social-oauth |
| OAuth provider, Pinterest, Instagram, TikTok, Discord, Reddit, X, LinkedIn, YouTube | [[brain.md]] ADR-088-gender-based-social-oauth |
| oauth_connections, oauth_states, token şifreleme | [[brain.md]] ADR-088-gender-based-social-oauth |

---

### §5 Database Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| 18 BCNF, normalizasyon | [[brain.md]] ADR-040-database-authority |
| ORM, SELECT *, PDO | [[.decisions/accepted/ADR-002-pdo-mandatory-no-orm]] |
| multi-db, 18 veritabani | [[.decisions/accepted/ADR-003-multi-db-bcnf]] |
| migration, schema degisikligi | [[.decisions/accepted/ADR-014-multi-db-migration-strategy]] |
| SQL normalization | [[.decisions/accepted/ADR-033-sql-normalization-strategy]] |
| DB sync | [[brain.md]] ADR-050-multi-db-sync-strategy |
| database master | .sql/mysql |
| coremusic_musics | .sql/mysql/coremusic_musics.sql |
| coremusic_auth | .sql/mysql/coremusic_auth.sql |
| coremusic_user | .sql/mysql/coremusic_user.sql |
| coremusic_albums | .sql/mysql/coremusic_albums.sql |
| coremusic_playlist | .sql/mysql/coremusic_playlist.sql |
| coremusic_catalog | .sql/mysql/coremusic_catalog.sql |
| coremusic_logs | .sql/mysql/coremusic_logs.sql |
| coremusic_media | .sql/mysql/coremusic_media.sql |
| coremusic_system | .sql/mysql/coremusic_system.sql |
| coremusic_social | .sql/mysql/coremusic_social.sql |
| coremusic_wireless | .sql/mysql/coremusic_wireless.sql |
| coremusic_download | .sql/mysql/coremusic_download.sql |
| coremusic_ai | .sql/mysql/coremusic_ai.sql |
| coremusic_api | .sql/mysql/coremusic_api.sql |
| coremusic_cms | .sql/mysql/coremusic_cms.sql |
| coremusic_neva | .sql/mysql/coremusic_neva.sql |
| coremusic_studio | .sql/mysql/coremusic_studio.sql |
| coremusic_patch | .sql/mysql/coremusic_patch.sql |

---

### §6 Audio Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| ASIO, ses surucusu, low latency | [[.decisions/accepted/ADR-017-dsp-hardware-mode]] |
| Neva Engine, C++, JUCE, ASIO | projects/NevaEngine/00-sdk-lib-envanter |
| equalizer, EQ, 31-band, DSP chain | projects/NevaEngine/02-dsp-zincir-mimari |
| reverse engineering, VoiceMeeter, FxSound, MIDI, os surucu matrisi, vmvaio, vmvaio3, vmauxvaio, vbvoicemeetervaio64, vaio64, arm64 driver, driver suffix xp vista win7 win10, 2.1.5.2, 3.4.1.7, driverstore %13%, wdmaud redbook, fxvad, fxvad.sys, fxsound audio enhancer, dfx mirror, dfx_dev, chicago signature, oem39.inf, fxmcp go, go1.26.5, mcp go-sdk, oauth2, jsonschema-go, fxmcp.mcpb, inspect_pe_resources, inspect_go_binary, resource envanteri, icon merdiveni | projects/NevaEngine/04-reverse-voicemeeter-fxsound |
| mixer, routing matrix, surround, Web PCM | projects/NevaEngine/05-neva-mixer-web-pcm-plani |
| gereksinim, A1-A10 çıkış, konser salonu efekti, dynamic/bass boost | projects/NevaEngine/06-gereksinimler-ve-farkliliklar |
| sanal ses sürücüsü, open source, FlexASIO, Sysvad, BlackHole, snd-aloop, PipeWire, fxvad, AGPL, XP legacy, win7 tam destek, cihaz tespiti, R18, wdk 7.1.0, msvad, gpl-3.0, rules gotcha, down-level wdk, targetversion, nt_target_version, wdk 19041, xp ddk arşivi, bufferoverflowk, asio2wasapi gpl, usbip | projects/NevaEngine/09-open-source-virtual-audio-driver-karsilastirma |
| io format, 32-bit float, float32 input, float32 output, 192 khz, 176.4, 96 khz, 88.2, 44.1, ornekleme hizi, bit-perfect, tpdf dither, src, sample rate conversion, wasapi exclusive float32, getmixformat, alsa float_le, coreaudio float32, asio 32f, k1 k6, roundtrip, buffer latency | projects/NevaEngine/10-io-format-32bit-192khz-kurallari |
| cok dilli kod ornekleri, 10 dil ornek paketi, c11 ornek, cpp20 ornek, csharp ornek, php8 ornek, javascript ornek, typescript ornek, python ornek, go ornek, rust ornek, html css mixer ui, biquad r16 ornek, spsc ring buffer ornek, wav 32f ornek, compressor limiter ornek, os enum r18 ornek, audio worklet ornek, pack g float32 php, rbj peaking, k1-k6 ornek, derlenmedi | projects/NevaEngine/11-cok-dilli-kod-ornekleri + 11-examples/ |
| spatial audio, HRTF, binaural, head tracking | projects/NevaEngine/legacy-2026-07/spatial-audio |
| VST3, plugin hosting, LV2, CLAP, sandbox | projects/NevaEngine/legacy-2026-07/vst3-hosting |
| MIDI learn, transport, mapping profili | projects/NevaEngine/legacy-2026-07/midi-system |
| routing matrix, send/return, bus sistemi | projects/NevaEngine/legacy-2026-07/routing-matrix |
| AI upscaling, ONNX, auto-EQ, genre detection | projects/NevaEngine/legacy-2026-07/ai-models |
| legacy spesifikasyon arşivi, eski evrak (2026-07) | projects/NevaEngine/legacy-2026-07/index |
| control plane, PHP web, C# masaüstü, Windows XP, mobil DSP, Android NDK, iOS CoreAudio, mimari diyagram | projects/NevaEngine/07-mimari-diyagram-kontrol-yuzeyleri |
| freelancer doküman, mekan presetleri, düğün salonu, arena, kulüp, 64-bit float, LUFS, XMOS | projects/NevaEngine/08-freelancer-dokumantasyon-cikarim |
| banana, potato, voicemeeter8, stripcomp, vban şeması, vbvmr, geq15, ghidra, ambience, decompile, asio sürücü, vbvasio, vboardlatency, shared memory | projects/NevaEngine/04-reverse-voicemeeter-fxsound |
| wlanapi, wlan, native wifi, xp sp3, win7, win8, win8.1, win10, export matrisi, hostednetwork, wifi direct, wfd, plap, anqp, ftm, getprocaddress, pconlife, neva connect, windows wlan katmanı, sürüm farkı | projects/NevaConnect/01-wlanapi-sürüm-matrisi-ve-windows-plani |
| çok exe, multi-exe, v141_xp, sdk 7.1a, _WIN32_WINNT, ntddi, subsystem 5.01, dumpbin, ayrı derleme, toolset, /MT statik crt, ci matrisi | projects/NevaConnect/02-coklu-exe-windows-sdk-derleme-mimarisi |
| feature gate, özellik kısıtlama, getprocaddress resolver, degradation, fallback, list2, dual-STA, wlan notification, desteklenmiyor mesajı, wzc | projects/NevaConnect/03-wlan-api-ozellik-kisitlama-tablosu |
| wifi 7, 802.11be, eht, wificx, mlo, wpa3, 24h2, bluetooth, bluetoothapis, winrt bluetooth, winusb, setupapi, sata, nvme, m.2, msata, ioctl_storage_query_property, bus type | projects/NevaConnect/04-kapsam-yol-haritasi-wifi7-bluetooth-usb-depolama |
| neva-wlan exe, çekirdek modül, wifi list, wifi scan, wifi connect, wifi status, profil xml, spsc kuyruk, notification callback, ws 9742 json sözleşmesi, wifi_event, wlan reason code | projects/NevaConnect/05-neva-wlan-cekirdek-modul-tasarimi |
| test planı, smoke test, os uç matrisi, negatif test, ctest, coverage kapısı, dumpbin imports kapısı, gha windows-2022, v141_xp smoke, nevatest profil, qa pass block | projects/NevaConnect/06-test-ve-kalite-plani |
| native wifi temelleri, wlanapi oturum açılışı, negotiated version, wlanfreememory, wlan interface state, dot11 phy type, wmi msft_wlan, winrt wifiadapter, xp beyaz liste | architecture/K000-isletim-sistemi/isletim-sistemi-api/ag-api/native-wifi-api-temelleri |
| nt5src, xp sp1 kaynak, wzcsvc, wzcsapi, wzcsapi.def, wzc.idl, ndisuiо, ndisuio, oid_802_11, deviceio, hash.c, lanui, wzcui, chklist, saui, eapol, xp panel, xp kaynak arsivi, sızıntı, felaket yolu, isomorfizm | projects/NevaConnect/07-xp-wzc-stack-kaynak-arastirmasi |
| xp panel, lanui, wzcui, chklist, wlantypv2, 3500 ms timer, ids_8021x_bcast, ids_cannot_associate, agerestriction, mac-filter, profile-order, ws 9742 xp eşleme | projects/NevaConnect/08-xp-panel-davranis-sozlesmesi |
| ndisuio, \\.\ndisuio, ioctl_ndisuio_query_binding, ioctl_ndisuio_open_device, ioctl_ndisuio_set_oid_value, ndis_wlan_bssid, nuiouser.h, xp felaket yolu, net stop wzcsvc, t-01 t-04 t-09 | projects/NevaConnect/09-xp-ndisuio-felaket-yolu-tasarimi |
| registry, regedit, wzcsvc registry, software\microsoft\wzcsvc\parameters, interfaces\guid, activesettings, static#, wzconfig binary, layoutversion, gpszlocpolicyagent, wirelessspd, gptwirelesspolicy, gptwifipolicy, policy cache, pastore, eapol registry, elreadglobalregistryparams, authperiod, heldperiod, supplicantmode, eapolenabled, interfacelist, user eapinfo, ras_eap_registry_location, eapol.inx tracing, wzctrace yorumlu, netwzc.inx servicedll, ndisuio ndis 6.30, wlansvc dependonservice, dot3svc, eaphost, xp modern eşleme, ndisuio.inx, servicetype 1, starttype 3, errorcontrol, loadordergroup, characteristics 0x28, persist.c cache, classname, msieee80211-policy, wirelessname, wirelessid, wirelessdatatype, wirelesstype, whenchanged, layoutversion 1, reg_layout_version 4, allowapmode, vwifisettings | projects/NevaConnect/10-wzc-ndisuio-registry-degerleri |
| wlanapi mimarisi, pe analizi, pefile, pe_arch.py, import tablosu, wzcsapi 19 fn, uuidfromstringw, ndr64asynccall, rpcasync, wnfn rtlsubscribewnf, threadpool, didat delay-load, guard_cf, dllcharacteristics, aslr dep, wfd export ailesi, wfd-int, querynetcon, wifidisplay, toplam vs wlan mutabakat, controlservice, OpenSCManagerW, security-base ace, wlan_write_access, setthreadtoken impersonation, wlanutil WlanStringToSsid, ucrt gecisi, getversionex, wlanregisternotification xp | projects/NevaConnect/11-windows-wlanapi-mimarisi |
| novasearch, nova search engine, youtube arama motoru, metadata arama, youtubei.js, openrouter free, offline-first, cache-first, hybrid cache, iki cli, wizard cli, parametrik cli, bun test, coverage kapisi, sqlite mysql adapter, anti-ban jitter | projects/NovaSearchEngine/01-kurulum-mimari-ve-acik-konular |
| 32-bit float, ring buffer | projects/NevaEngine/02-dsp-zincir-mimari |
| 8.1 surround, PCM3168A | [[brain.md]] ADR-038-8-1-sound-card-chip-selection |
| ASIO/WASAPI/CoreAudio | projects/NevaEngine/03-isletim-sistemi-kod-ornekleri |
| audio service | architecture/K012-services/index.md |
| device service, BT, WiFi | architecture/K012-services/index.md |
| network audio, WebRTC | architecture/K010-network/index.md |
| AI service | architecture/K006-yapay-zeka/index.md |
| YouTube, deemix, FLAC | [[brain.md]] |

---

### §7 Hardware Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| PCM3168A, 8 kanal, DAC | [[brain.md]] ADR-038-8.1-sound-card-chip-selection |
| PCM5122, REDDED, H001 | [[brain.md]] ADR-038-8.1-sound-card-chip-selection |
| XMOS XU316, DSP | [[.decisions/accepted/ADR-017-dsp-hardware-mode]] |
| AK4458, DAC opsiyonel | electronic/hardware/audio-interface.md *(Faz 1: electronic/ kök tasarım dosyaları kaldırıldı — gerçek konumlar alt klasörlerde)* |
| Class AB, amfi, 100W | electronic/amplifier/ *(kök `amplifier-design.md` kaldırıldı — arşiv)* |
| hardware roadmap, 3 faz | **DOĞRULAMA GEREKLİ** — `electronic/hardware-roadmap.md` vault'ta yok |
| audio organization, 5 bolum | **DOĞRULAMA GEREKLİ** — `electronic/audio-organization.md` vault'ta yok |
| ASIO driver | **DOĞRULAMA GEREKLİ** — `electronic/asio-driver-design.md` vault'ta yok; yakın karşılık `electronic/drivers/` |
| xmos-pcm3168a, devre | **DOĞRULAMA GEREKLİ** — `electronic/xmos-pcm3168a-design.md` vault'ta yok; yakın karşılık `electronic/hardware/audio-interface.md` |
| audio interface, PCM3168A devre | electronic/hardware/audio-interface.md |
| frequency response, frekans yaniti | electronic/hardware/frequency-response.md |
| SNR, THD, THD+N, olcum | electronic/hardware/snr-thd-measurement.md |
| test protokolu, hardware test | **DOĞRULAMA GEREKLİ** — `electronic/test-protocols.md` vault'ta yok |
| termal analiz, is sicaklik | **DOĞRULAMA GEREKLİ** — `electronic/thermal-analysis.md` vault'ta yok; yakın karşılık `electronic/amplifier/thermal.md` |
| DSP pipeline, equalizer, crossover | electronic/dsp/index.md |
| driver framework, USB, BT, WiFi | electronic/drivers/index.md |
| amplifier architecture, power supply | electronic/amplifier/index.md |
| hardware design, PCB | electronic/hardware/index.md |
| firmware, RTOS, OTA | electronic/firmware/index.md |
| CoreMusic Electronics, genel bakis | electronic/core-music-electronics-overview.md |
| platform architecture, 9 katman | electronic/platform-architecture.md |
| device architecture, cihaz aileleri | electronic/device-architecture.md |
| OS architecture, PAL, platform adapter | electronic/operating-system-architecture.md |
| device ecosystem, cihaz ekosistemi | electronic/device-ecosystem.md |
| software architecture, 5 katman | electronic/software-architecture.md |
| service architecture, 13 servis | electronic/service-architecture.md |
| audio architecture, ses pipeline | electronic/audio-architecture.md |
| DSP engine, EQ compressor limiter | electronic/dsp-engine-architecture.md |
| driver framework, ASIO WASAPI ALSA | electronic/driver-framework.md |
| amplifier architecture, 8+1 | electronic/amplifier-architecture.md |
| hardware design, PCB EMI EMC | electronic/hardware-design.md |
| firmware architecture, RTOS HAL OTA | electronic/firmware-architecture.md |

---

### §7A AI Architecture Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| AI engine, recommendation, music AI | architecture/ai/ai-engine.md |
| AI orchestrator, task dispatch | architecture/ai/ai-orchestrator.md |
| agent system, 11 agents | architecture/ai/agent-system.md |
| knowledge base, semantic search | architecture/ai/knowledge-base.md |
| memory system, session memory | architecture/ai/memory-system.md |
| prompt engine, prompt generation | architecture/ai/prompt-engine.md |
| tool calling, external tools | architecture/ai/tool-calling.md |
| MCP, model context protocol | architecture/ai/mcp-integration.md |
| AI workflow, recommendation engine | architecture/ai/ai-workflow.md |
| AI strategy, prompt engineering | [[.decisions/accepted/ADR-030-ai-strategy-core]] |
| system prompt, prompt standards | [[.decisions/accepted/ADR-035-system-prompt-engineering]] |
| multi-project prompt | [[.decisions/accepted/ADR-036-multi-project-prompt-maker]] |
| startup prompt loader | [[brain.md]] ADR-049-startup-prompt-loader |
| AI electronics engine | architecture/ai/ai-electronics-engine.md |
| AI workflow electronics | architecture/ai/ai-workflow-electronics.md |
| ai-workflow-standards | architecture/03-contracts/ai-workflow-standards.md |

---

### §8 Panel & Service Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| 10 panel, Music, Admin, Car | subdomains/README.md |
| music.coremusic.net, port 81 | subdomains/music.coremusic.net/index.md |
| auth.coremusic.net | subdomains/auth.coremusic.net/index.md |
| download.coremusic.net, port 3001 | subdomains/download.coremusic.net/domains/index.md |
| Control Service, port 81 | ecosystem/README.md (7-servis tablosu) |
| Media Service, port 5000/6000 | ecosystem/README.md (7-servis tablosu) |
| Audio Service, port 9741/9742 | ecosystem/README.md (7-servis tablosu) |
| 7 servis, platform | [[brain.md]] ADR-039-7-service-platform-architecture |
| servis entegrasyonu | ecosystem/7-service-integration.md |
| health check | ecosystem/service-health-check.md |

---

### §9 Theme & CSS Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| tema, theme, dinamik tema | [[brain.md]] ADR-044-dynamic-user-theme-engine |
| cinsiyet, gender, pembe, mavi | [[brain.md]] ADR-044-dynamic-user-theme-engine |
| data-gender, CSS tokens | [[brain.md]] ADR-044-dynamic-user-theme-engine |
| multi-domain view mode | [[brain.md]] ADR-045-multi-domain-view-mode-architecture |
| cross-view state | [[brain.md]] ADR-046-cross-view-state-preservation |
| View Transition API | [[brain.md]] ADR-048-view-transition-api-integration |
| responsive, media query, breakpoint, token konsolidasyon | assets.coremusic.net/Css/01_Abstracts/a-layout-tokens.css |
| 1024px default, mockup reference, RPi5 embedded | assets.coremusic.net/Css/01_Abstracts/a-layout-tokens.css |
| device css, d-embedded, d-desktop, d-tablet, device override | assets.coremusic.net/Css/08_Devices/ |
| device-loader, cihaz algilama | assets.coremusic.net/js/device-loader.js |
| 08_Devices, d-phone, d-tablet | assets.coremusic.net/Css/08_Devices/ |
| 09_ViewModes, v-home, v-pro | assets.coremusic.net/Css/09_ViewModes/ |

---

### §10 ADR Keyword Mapping (001-050)

| ADR | Anahtar Kelimeler | Kategori |
|-----|-------------------|----------|
| ADR-001 | vanilla JS, ITCSS, framework yasak | Frontend |
| ADR-002 | PDO, ORM yasak, SELECT *, prepared | Database |
| ADR-003 | 9 BCNF, multi-db | Database |
| ADR-004 | multi-domain SPA, vault versiyonlama | Architecture |
| ADR-005 | ultrathink, zero hallucination | Quality |
| ADR-006 | performans, hedef, benchmark | Performance |
| ADR-007 | cache namespace, zero code before plan | Infrastructure |
| ADR-008 | bypass auth, test bypass | Security |
| ADR-009 | clean URL, redirect | Routing |
| ADR-010 | CSRF, csrf_token, form | Security |
| ADR-011 | session, COREMUSIC_SESS, idle timeout | Security |
| ADR-012 | CSP, nonce, strict-dynamic | Security |
| ADR-013 | rate limit, APCu | Security |
| ADR-014 | migration, multi-DB | Database |
| ADR-015 | env parser, .env | Infrastructure |
| ADR-016 | URL normalization | Routing |
| ADR-017 | DSP hardware, XMOS, JUCE, ASIO | Audio |
| ADR-018 | footer player, vaporwave | UI |
| ADR-019 | Neva Player, per-OS | Audio |
| ADR-020 | API public, guvenlik | Security |
| ADR-021 | SPA router, immutable contract | Routing |
| ADR-022 | DB hardened, Argon2id, AES-256-GCM | Security |
| ADR-023 | persona-driven, test | Testing |
| ADR-024 | ecosystem, modular docs | Documentation |
| ADR-025 | professional EQ, 31-band | Audio |
| ADR-026 | download service, architecture | Architecture |
| ADR-027 | dual-mode storage | Infrastructure |
| ADR-028 | anti-ban, ARL token | Download |
| ADR-029 | listening rooms, social | Social |
| ADR-030 | AI strategy, core | AI |
| ADR-031 | mobile, PWA, Flutter | Mobile |
| ADR-032 | IPC contract, versioning | Architecture |
| ADR-033 | SQL normalization | Database |
| ADR-034 | credential vault, AES-256-GCM | Security |
| ADR-035 | system prompt, engineering | AI |
| ADR-036 | multi-project, prompt maker | AI |
| ADR-037 | WirelessConnect, WiFi | Integration |
| ADR-038 | PCM3168A, XMOS XU316, 8.1 surround | Audio |
| ADR-039 | 7 servis, platform mimarisi | Architecture |
| ADR-040 | 18 BCNF, DB authority | Database |
| ADR-041 | DB normalization supplementary | Database |
| ADR-042 | vault restructuring, PHP 8.4, port 81 | Vault |
| ADR-043 | auth subdomain, konsolidasyon | Security |
| ADR-044 | dynamic theme, gender, pembe/mavi | UI |
| ADR-045 | multi-domain view mode | UI |
| ADR-046 | cross-view state | UI |
| ADR-048 | View Transition API | UI |
| ADR-049 | startup prompt loader | AI |
| ADR-050 | multi-db sync | Database |
| ADR-061 | electronics architecture, L6 layer | Electronics |
| ADR-062 | DSP pipeline architecture | Electronics |
| ADR-063 | hardware design standards | Electronics |
| ADR-064 | electronics platform, L6, 5 cihaz ailesi, 13 servis | Electronics |
| ADR-072 | social database, comments, shares, activity, notifications | Database |
| ADR-073 | podcast database, shows, episodes, transcripts | Database |
| ADR-074 | radio database, stations, schedules, now_playing | Database |
| ADR-075 | ai database, preferences, features, recommendations, models | Database |
| ADR-076 | video database, music_videos, playback, subtitles | Database |
| ADR-077 | studio database, sessions, tracks, presets, equipment | Database |
| ADR-078 | cms database, pages, blog, tags, faqs, banners | Database |
| ADR-079 | i18n database, languages, translations, ui_strings | Database |
| ADR-083 | SPA Router, PHP+JS Hybrid, History API, DOMParser | Routing |
| ADR-084 | API Gateway, API-First, BFF, CQRS, Tek Gateway | Architecture |
| ADR-085 | Shared Library Hybrid, tek shared/ + PSR-4 namespace, circular dependency yasak | Infrastructure |
| ADR-086 | Event Driven, PSR-14, Domain Event, Integration Event | Architecture |
| ADR-087 | Master Implementation Plan, 5 faz, 40 gun, 22 bolum, sirfirdan gelistirme | Architecture |
| ADR-088 | Gender-Based Social OAuth, cinsiyet bazlı sosyal medya, OAuth 2.0, Pinterest, Instagram, TikTok, Discord, Reddit, X, LinkedIn, YouTube | Security |

---

## Architecture

### §3 L0-L6 Layer Keywords

### §3.1 L0 Infrastructure

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L0, altyapi, infrastructure, cache, APCu, Redis | architecture/k0-isletim-sistemi/ |
| db, database, veritabani, PDO | architecture/k5-veri-yonetimi/ |
| filesystem, IPC, shared memory | architecture/k0-isletim-sistemi/ |
| credential vault, secret, key | architecture/k0-isletim-sistemi/credential-vault.md |

### §3.2 L1 Security

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L1, guvenlik, security, middleware, pipeline | architecture/k6-guvenlik/ |
| session, oturum, cookie, CSRF, csrf_token | architecture/k6-guvenlik/ |
| CSP, nonce, strict-dynamic, rate limit | architecture/k6-guvenlik/ |
| OWASP, zafiyet, tehdit | architecture/k6-guvenlik/ |

### §3.3 L2 Routing

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L2, routing, SPA, single page, router | architecture/k9-api-routing/ |
| URL, normalization, subdomain | architecture/k9-api-routing/ |
| PageRouter, PageRouterKernel, HTML shell | architecture/k9-api-routing/ |
| RouteRegistry, SpaRoute, route config | architecture/k9-api-routing/ |
| HtmlShellRenderer, CSP nonce, device CSS | architecture/k9-api-routing/html-shell-renderer.md |
| AuthGuard, AuthUrlBuilder, guard pipeline | architecture/k9-api-routing/guard-pipeline.md |
| JS Router, Router.js, DomPatcher, GuardPipeline | architecture/k9-api-routing/js-router.md |
| Middleware pipeline, session, CSRF | architecture/k9-api-routing/middleware-pipeline.md |
| Subdomain routing, port mapping | architecture/k9-api-routing/subdomain-routing.md |
| URL normalization, clean URL | architecture/k9-api-routing/ |
| Service discovery, health check | architecture/k9-api-routing/service-discovery.md |

### §3.4 L3 Presentation

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L3, presentation, vanilla JS, framework yasak | architecture/k11-ux/ |
| ITCSS, BEM, BEMIT, TrustedTypes, DOMParser | architecture/k11-ux/ |
| Web Audio, ses API | architecture/k15-medya-streaming/ |
| Device CSS, responsive device rendering, scale | ui-design/05-responsive-architecture |
| Responsive frontend architecture, Single View | architecture/k11-ux/responsive-frontend-architecture.md |
| Scale sistemi, router, CSS rehberi, frontend entegrasyon, adım adım | architecture/k11-ux/scale-router-css-frontend-guide.md |

### §3.4A UI Design System & Mockup Otoritesi (SSOT)

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| ui-design, mockup, 19 png, mockup index, home-1024, shared-1024, home-1920 | ui-design/01-mockup-index.md |
| c01-c16, component inventory, bileşen envanteri, nav-link, media-card, status-widget | ui-design/02-component-inventory.md |
| implementation plan, 15 step css, css uygulama planı, mockup to code | ui-design/03-implementation-plan.md |
| ascii art, wireframe, ascii view, 1024x600 layout, screen spec | ui-design/screens/00-ascii-art-index.md |
| ascii art views, all views, wireframes, home layout, auth layout | ui-design/screens/00-ascii-art-index.md (eski `00-ascii-art-views.md` Faz 7'de diskte YOK — tek indeks 00-ascii-art-index + 20 spec) |
| design tokens, ui tokens, platform tokens, color palettes, glass tokens | ui-design/tokens/design-tokens-master.md |
| accessibility gaps, wcag 2.2 aa, touch target 48px, contrast check | ui-design/04-accessibility-gaps.md |
| responsive device mode, embedded 1024, desktop 1920, mobile 375, tv 3840 | ui-design/05-responsive-architecture.md (eski `responsive-device-mode.md` diskte YOK — taşınma kaydı) |
| device-aware rendering, tek bileşen, single component, conditional render | brain.md §18C |
| device token, header-h, footer-h, content-h, spacing-scale | ui-design/tokens/design-tokens-master.md |
| device behavioral, hover disabled, touch target 48px, scrollbar override | ui-design/05-responsive-architecture.md (eski `ui-design/05-responsive-architecture` diskte YOK — katman adı k11-ux'e taşındı) |
| backend scope, widget count, feature toggle, nav links, content config | brain.md §18C |
| frontend scope, token override, media query, grid template, layout grid | brain.md §18C |
| layer violation, presentation→infrastructure, php sunum kararı yasak | brain.md §18C |
| widget grid kanonik kuralı, 12 slot, 20 slot | AGENTS.md §13.9 kural 1 (Figma API çıktısından önceliklidir) |
| home 1920 mockup, 1920 desktop home, 1920 ascii art, 1920 pixel measurements | .ai/.png/home-1920/ + ui-design/screens/T17-monitor-22fhd/home-dashboard.md (eski `ui-design/mockups/02-home-screens-1920.md` diskte YOK) |
| ui prompt, component prompt, page prompt, screen prompt, layout prompt | ui-design/prompt/00-prompt-index.md |
| auth screens, login girl, select gender, register girl 1-3 | ui-design/screens/shared/ (eski `screens/05-auth-layouts.md` diskte YOK) |
| home layouts, welcome popup, split 42/58, now playing 1024 | ui-design/screens/T07-embedded/ (home-dashboard · welcome-popup — eski `01-home-layouts.md` YOK) |
| connectivity layouts, wifi quick, bluetooth quick, wifi connect | ui-design/screens/T07-embedded/ (wifi-quick · wifi-connect-light · bluetooth-quick — eski `04-connectivity-layouts.md` YOK) |

### §3A L4 Domain

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L4, domain, business rules, entities, aggregates | architecture/k10-uygulama/ |
| DDD, value object, domain event | architecture/k10-uygulama/ |
| repository interface, use case interface | architecture/k10-uygulama/ |

### §3B L5 Services

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L5, services, application services, use case | architecture/k8-servis/ |
| CQRS, command, query, event bus, PSR-14 | architecture/k8-servis/ |
| transaction management, DTO mapping | architecture/k8-servis/ |

### §3C L6 Electronics

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L6, electronics, hardware, firmware, driver, DSP | architecture/k1-donanim/ |
| XMOS, PCM3168A, Class AB, audio engine | architecture/k1-donanim/ |
| ASIO, WASAPI, JUCE, C++20 | architecture/k2-surucu/ |
| amplifier, Class AB, 50W, MJL21194, MJL21193 | architecture/amplifier-classab-circuit.md |
| bias, quiescent, thermal tracking, overcurrent | architecture/amplifier-classab-circuit.md |
| power supply, ±35V, boost, LM5122, interleaved | architecture/power-supply-classab.md |
| BOM, bill of materials, component count | architecture/k16-class-ab/bom-classab.md |
| PCB, stackup, 6-layer, impedance, thermal | architecture/pcb-classab.md |
| heatsink, fan control, thermal management | architecture/thermal-design-classab.md |

### §3D Skills Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| skill, beceri, agentic, orkestrasyon | .opencode/skills/*/SKILL.md |
| ui-code-generator, ui kod üretimi | .opencode/skills/ui-code-generator/SKILL.md |
| ui-analyzer, ui analiz | .opencode/skills/ui-analyzer/SKILL.md |
| skill-maker, skill oluştur | .opencode/skills/skill-maker/SKILL.md |
| hallucination-control, halüsinasyon | .opencode/skills/hallucination-control/SKILL.md |
| human-mode, insan onayı, HITL | .opencode/skills/human-mode/SKILL.md |
| red-team, truth mode, adversarial | .opencode/skills/red-team-truth-mode/SKILL.md |
| prompt-maker, prompt mühendisliği | .opencode/skills/prompt-maker/SKILL.md |
| agent-orchestrator, görev dağıtımı | .opencode/skills/agent-orchestrator/SKILL.md |
| composer-sync, vendor sync | .opencode/skills/composer-sync/SKILL.md |
| database-normalize, bcnf, normalizasyon | .opencode/skills/database-normalize-maker/SKILL.md |

### §3C Templates Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| template, şablon, şablon | .ai/.templates/index.md |
| adr template, karar şablonu | .ai/.templates/adr/adr-template.md |
| php template, backend şablonu | .ai/.templates/backend/php-template.md |
| js template, frontend şablonu | .ai/.templates/frontend/js-template.md |
| css template, itcss şablonu | .ai/.templates/frontend/css-template.md |
| phpunit template, test şablonu | .ai/.templates/testing/phpunit-template.md |
| migration template, db migration | .ai/.templates/infrastructure/migration-template.md |
| docker template, container | **Bilinen çelişki (Faz 1):** Dosya diskte VAR (`docker-template.md`) ancak not "Kaldırıldı" diyor — Docker kullanılmıyor; dosya kaldırma kararı Vault Steward'a bağlı |
| github actions, ci/cd şablonu | .ai/.templates/infrastructure/github-actions-template.md |
| api doc, api dokümantasyonu | .ai/.templates/documentation/api-doc-template.md |
| security audit, güvenlik denetimi | .ai/.templates/documentation/security-audit-template.md |
| c template, embedded şablonu | .ai/.templates/other/c-template.md |
| query template, sql şablonu | .ai/.templates/query/Query-Template.md |
| session log, oturum kaydı | .ai/.templates/session-log-template.md |

### §3D Agent Profile Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| agent profile, agent tanımlı | .ai/.agents/AGENTS.md |
| master orchestrator, mo | .ai/.agents/master-orchestrator.md |
| backend architect, php api | .ai/.agents/backend-architect.md |
| ui designer, frontend | .ai/.agents/ui-designer.md |
| security engineer, güvenlik | .ai/.agents/security-engineer.md |
| data engineer, veritabanı | .ai/.agents/data-engineer.md |
| embedded engineer, c++ | .ai/.agents/embedded-engineer.md |
| qa engineer, test | .ai/.agents/qa-engineer.md |
| devops engineer, ci/cd | .ai/.agents/devops-engineer.md |
| audio hardware, dac/adc | .ai/.agents/audio-hardware-engineer.md |
| dsp firmware, xmos | .ai/.agents/dsp-firmware-engineer.md |
| windows software, wasapi | .ai/.agents/windows-software-engineer.md |

### §3A Frontend & UI Design Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| frontend, css, html, ui, layout, bileşen, ekran, sayfa, tasarım | .ai/ui-design/01-mockup-index.md |
| mockup, görsel, png, screenshot | .ai/ui-design/01-mockup-index.md + .ai/.png/** |
| component, bileşen, C01-C16, BEM | .ai/ui-design/02-component-inventory.md |
| implementation, uygulama, plan, css planı | .ai/ui-design/03-implementation-plan.md |
| accessibility, erişilebilirlik, wcag, touch target | .ai/ui-design/04-accessibility-gaps.md |
| header, footer, nav, navigation | .ai/ui-design/flow/navigation/ (01-spa-routing · 02-header-nav · 03-footer-player) |
| modal, popup, overlay, welcome | .ai/ui-design/screens/T07-embedded/welcome-popup.md |
| auth, login, register, gender | .ai/ui-design/screens/shared/ (login · register-step1/2/3 · select-gender[-selected]) |
| home, ana sayfa, dashboard | .ai/ui-design/screens/T07-embedded/home-dashboard.md |
| albums, albümler, artists, sanatçılar | .ai/ui-design/screens/T07-embedded/ (albums · album-detail · singer) |
| playlist, player, oynatıcı | .ai/ui-design/screens/T07-embedded/ (playlist · playlist-video) |
| file manager, dosya yöneticisi, göz at | .ai/ui-design/screens/T07-embedded/ (browse · browse-clicked) |
| wifi, bluetooth, quick panel | .ai/ui-design/screens/T07-embedded/ (wifi-quick · wifi-connect-light · bluetooth-quick) |
| flow, akış, kullanıcı akışı | .ai/ui-design/flow/ |
| prompt, şablon | .ai/ui-design/prompt/ |
| design tokens, token, renk, yazı tipi | .ai/ui-design/tokens/design-tokens-master.md *(Faz 1: kırık `reference/02-design-tokens.md` hedefi düzeltildi)* |
| ascii art, piksel, ölçü, layout view | .ai/ui-design/screens/00-ascii-art-index.md |
| screen spec, ekran özelliği, pixel exact | .ai/ui-design/screens/ |
| layout pattern, standard 60/40, split home | .ai/ui-design/prompt/layout/ (01-mobile-stack … 09-watch-micro) |
| png mockup, .png dosyası, görsel referans, screenshot | .ai/.png/home-1024/ + .ai/.png/home-1920/ + .ai/.png/shared-1024/ |
| home-1024, RPi5 mockup, 1024×600 | .ai/.png/home-1024/ |
| home-1920, desktop mockup, 1920×1080 | .ai/.png/home-1920/ |
| shared-1024, auth mockup, login png | .ai/.png/shared-1024/ |
| png mockup index, mockup tablosu | .ai/ui-design/01-mockup-index.md |
| component inventory, bileşen envanteri | .ai/ui-design/02-component-inventory.md |
| implementation plan, uygulama planı | .ai/ui-design/03-implementation-plan.md |
| accessibility gaps, wcag analizi | .ai/ui-design/04-accessibility-gaps.md |
| device matrix, cihaz matrisi, 45-tier, CatID | .ai/ui-design/00-device-matrix.md |
| responsive architecture, tier fallback, 4K ortalamama | .ai/ui-design/05-responsive-architecture.md |
| vault registration, vault kayıt | .ai/ui-design/04-vault-registration.md — ⚠️ VERIFICATION REQUIRED (Faz 7: dosya diskte YOK; kayıt/registry fiilen `.ai/index.md` §4A'da — sahip kararı) |
| device manager, cihaz yönetimi, DeviceManager.php, fromRequest, fromDevice | shared/src/Device/DeviceManager.php |
| device-aware rendering, cihaz bazlı html, 5 cihaz bloğu, feature toggles | shared/src/Device/DeviceManager.php |
| widget count, recent card count, playlist count, upNext count, content config | shared/src/Device/DeviceManager.php |
| showVolume, showFullMetadata, showSidebar, showSeekBar, showPodcastWidget | shared/src/Device/DeviceManager.php |
| layoutClass, allClasses, dataAttributes, css class helper | shared/src/Device/DeviceManager.php |
| device nav links, device conditional, isEmbedded, isPhone, isLaptop, isDesktop, is4kTv | shared/src/Device/DeviceManager.php |
| shouldRenderEmbeddedLayout, shouldRenderWideLayout, shouldRender4kLayout, shouldShowFallback, shouldRenderWelcomePopup, isSupportedResolution | shared/src/Device/DeviceManager.php |
| device types, phone, tablet, embedded, laptop, desktop, 4k-tv, 4k-monitor, 7 cihaz | shared/src/Device/DeviceManager.php |
| nav links, NAV_LINKS, device nav, cihaz navigasyonu | shared/src/Device/DeviceManager.php |
| device profile, deviceProfile, embedded-1024, small-desktop, tv-4k, 4k-monitor | shared/src/Device/DeviceManager.php |
| device queries, isTouch, isWide, isLarge, isMobile, isSmallDesktop, isTv | shared/src/Device/DeviceManager.php |
| device content config, widgetCount, recentCardCount, playlistCount, upNextCount | shared/src/Device/DeviceManager.php |
| welcome popup, shouldRenderWelcomePopup, RPi5 1024 | shared/src/Device/DeviceManager.php |
| 4-tier conditional rendering, koşullu render, phone layout, 4k layout, wide layout, embedded layout, fallback always false | .ai/ui-design/05-responsive-architecture.md (eski hedef `responsive-device-mode.md` Faz 7'de diskte bulunamadı — taşınma kaydı) |
| cm_viewport_w, cm_viewport_h, viewport cookie | assets.coremusic.net/js/device-loader.js |
| viewport whitelist, cookie-based viewport, JS→PHP viewport | shared/src/PageRouter/PageRouter.php |
| conditional rendering php guide, php implementasyon rehberi, DeviceManager nasıl kullanılır, 4-tier render | .ai/architecture/conditional-rendering-php-guide.md — ⚠️ VERIFICATION REQUIRED (Faz 7: dosya diskte YOK; en yakın mevcut hedef `architecture/k8-servis/device-service.md` — eşdeğerlik sahip onayı bekliyor) |

---

## Rules

### §12 Navigation Rules (ADR-042 Uyumlu)

### §12.2 Zero Misdirection

| Yanlis | Dogru |
|-----------|----------|
| Tahmin yurutme | keys.md'den keyword ara |
| Recursive glob | Doğrudan glob kullan |
| Web arama | Sadece vault + ADR referanslari |
| Kodu okumadan tahmin | Once kodu oku, sonra ADR |
| Uydurma API/endpoint | // VERIFICATION REQUIRED yaz |

### §12.3 Oncelik Matrisi

```
P0: CLAUDE.md, AGENTS.md, WORKFLOW.md
P1: index.md, keys.md, brain.md, MEMORY.md, log.md
P2: decisions/accepted/ADR-NNN, architecture/L[0-3]/*
P3: testing/*, ui-design/*, .personas/*
```

---

### §15 Critical Warnings

| # | Uyari |
|---|-------|
| 1 | **Rastgele okuma yasak.** Her zaman keys.md kullanin. Token asimina yol acar. |
| 2 | **PCM5122 REDDEDILMISTIR (H001).** 8.1 surround icin yetersiz. Sadece PCM3168A kullanin. |
| 3 | **CSRF Token Key = csrf_token.** _csrf_token 2026-05-30'da kaldirildi. |
| 4 | **Middleware sirasi degistirilemez.** OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller |
| 5 | **ORM yasak.** Sadece PDO prepared statement. SELECT * yasak -- acik kolon listesi zorunlu. |
| 6 | **PowerShell 5.1 `.ps1` dosyalari UTF-8 BOM ile kaydedilir (Faz 7 — 2026-09-29).** BOM'suz `.ps1`, PS 5.1 tarafindan ANSI kod sayfasiyla okunur; Turkce bolum basliklari bozulur ve kapi betikleri **yanlis basarisizlik** uretir (2026-09-29: `kalip-abc-check.ps1` BOM'suzken **91 sahte hata**, BOM eklendikten sonra **gercek 69 hata** kaldi; Faz 6 kalip duzeltmelerinden sonraki koku olcumde `A:0 B:0 C:0 = GECTI`). Buna karsilik **`.md` dosyalari `.ai` disk gerceginde BOM'SUZ yazilir** (sablon §4.2). Ikisi karistirilmaz: uzanti = kod sayfasi kurali. |

---

### §15.1 Guardrail #16 Kaydi — Sablon Zorunlu Okuma + Kapi Betikleri (Faz 7, 2026-09-29)

1. **Guardrail #16 (sablon):** `.ai/ui-design/**` altina `.md` yazmadan önce ilgili kalip okunur — referans/tokens/root indeks → Kalıp A · `flow/` → Kalıp B · `prompt/` → Kalıp C · `screens/` → Kalıp D (`[[.templates/ui-design/reference-template]]` · `flow-template` · `prompt-template` · `screen-spec-template`). Şablonsuz ui-design dosyası geçersizdir (detay: [[CLAUDE.md]] §7.3, [[AGENTS.md]] §13 kural 5).
2. **Kapi betikleri (repo KÖKÜNDEN calisir):** `screens-frontmatter-check.ps1` → `dosya 21 | sorunlu 0` · `kalip-abc-check.ps1` → `A:0 B:0 C:0 (GECTI)` · `wiki-link-check.ps1` → `227 link, gercek kirik 0` · **`figma-tokens.ps1` → `SONUC -> toplam=7 | pass=5 | bos=2 | fail=0 | bos_bp=3840,tv` + `BITTI` (exit 0; her breakpoint satirinda `DURUM: PASS|BOS|FAIL`)** — sayilar 2026-09-29 Faz 9 olcumudur, iddia degildir. Ek denetimler: `device-matrix-catid.ps1` · `figma-extract.ps1`.
3. **6 sahte kirik link — DÜZELTILEMEZ:** `flow/auth/01-login.md` `[[C]]` · `flow/auth/04-select-gender.md` `[[V]]` · `flow/music/01-playback.md` `[[V]]` · `flow/settings/04-general.md` `[[V]]` (ASCII art kacis dizgesi) · `reference/legacy-inventory.md` `[[link]]` x2 (```` ```yaml ```` blogu icinde). Bunlar sablon degiskenidir, gercek kirik link degildir; "onarmaya" kalkisan sablonu bozar.
4. **Token SSOT:** `FIGMA_TOKEN` / `FIGMA_FILE_KEY` **yalnz `.ai/.env.figma`'dan** okunur (`.gitignore:69`); anahtar hicbir `.md` / `.json` / `.log`'a yazilmaz — `figma-extract.ps1` ve `figma-tokens.ps1`'de sabit anahtar yoktur.
5. **Kod sayfasi madde 6 ile birlikte okunur:** `.ps1` → BOM'lu, `.md` → BOM'suz (§15 madde 6 · sablon §4.2).

---

## Workflow

### §11 Decision Tree

```
Istenen Bilgi -> Ilk Kontrol:
|
|-- Mimari/Layer -> architecture/k0-isletim-sistemi/ | k6-guvenlik/ | k9-api-routing/ | k11-ux/
|-- ADR Karari -> decisions/accepted/ADR-NNN-*.md
|-- Guvenlik -> ADR-010/011/012/013/022 + architecture/k6-guvenlik/
|-- Veritabani -> ADR-040 + .sql/mysql + .sql/
|-- Ses/Donanim -> ADR-017/038 + electronic/ + projects/NevaEngine/
|-- Panel/Servis -> subdomains/ + ecosystem/ + architecture/K012-services/
|-- Test -> ui-design/04-accessibility-gaps.md + reports/ (`.ai/testing/` dizini yok — Faz 1 notu)
|-- Vault -> index.md -> keys.md (bu dosya)
```

---

### §13 Troubleshooting

| Sorun | Cozum |
|-------|-------|
| Dosya bulunamadi | keys.md veya index.md icinde grep ile ara |
| Token limiti asildi | Buyuk dosyalari parca parca oku (offset/limit) |
| Kirik wiki-link | index.md'de gercek dosya yolunu dogrula |
| Eski ADR referansi | decisions/accepted/ dizininde ADR-NNN ara |
| Bilinmeyen terim | brain.md'de teknik detaylari kontrol et |
| Yanlis port/protokol | Bolum 6'daki port haritasina bak |
| BCNF ihlali | ADR-040 ve architecture/k5-veri-yonetimi/ + .sql/mysql/ kontrol |
| CSRF hatasi | ADR-010 ve architecture/k6-guvenlik/ kontrol |
| Middleware sirasi | ADR-010/011/012/013/022 -- sira FROZEN |

---

### §14 Quick Reference

| Ihtiyac | Ilk Adim |
|---------|----------|
| Mimari karar | brain.md -> decisions/accepted/ |
| Guvenlik | architecture/k6-guvenlik/ -> ADR-010/011/012/013/022 |
| Veritabani | .sql/mysql -> ADR-040 |
| Frontend | architecture/k11-ux/ -> ADR-001 |
| Backend | architecture/k9-api-routing/ -> ADR-002 |
| Audio/Donanim | electronic/ -> ADR-017/038 |
| Test | ui-design/04-accessibility-gaps.md -> reports/ (testing/ dizini yok — Faz 1 notu) |
| Vault yapisi | index.md -> bu dosya (keys.md) |
| Agent yetkileri | AGENTS.md -> .agents/ |
| Servisler | ecosystem/7-service-integration.md |
| Deploy | architecture/k13-cicd/ |
| Tema | ADR-044 -> [[brain.md]] §22 (prompt arşivi — tema kuralları) |

### Section 3B: Prompt Archive Keywords

| Keywords | Dosya |
|----------|-------|
| prompt0, genel ana prompt, tüm sistem kuralları, 11 alt domain, 10 panel, 20 analiz görevi | archives/prompt0-genel-ana-prompt-2026-09-01 |
| prompt1, spa router, enterprise router, history api, SOLID, PSR, attribute-based | archives/prompt1-spa-router-2026-09-01 |
| prompt2, auth, merkezi auth, jwt, session, cors, rbac, middleware pipeline | archives/prompt2-auth-2026-09-01 |
| prompt3, api-first, gateway, cqrs, event driven, 14 servis, coremusic-shared | archives/prompt3-api-2026-09-01 |

---

## Validation

### §17 Quality Report

| Metrik | Deger |
|--------|-------|
| Version | 28.3.5 |
| Status | Red Team · Human Mode · Truth Mode verified |
| ADR Coverage | 001-089 (80 karar: 37 Frozen + 31 Active + 12 Rejected) |
| Vault Envanteri | 587 .md dosyasi (ölçüm 2026-09-24 21:37), 80 ADR, 18 BCNF DB, hedef 10 panel / 7 servis (fiziksel: 4 domain + assets), shared/ hybrid yapı — önceki sahip doğrulaması 518 .md (2026-09-23) → disk ölçümüyle düzeltildi |

---

### §19 Doküman İskeleti (8-Bölüm Uyumu — Vault Refactor Engine 2026-09-23)

> **Not:** v28.1.0 → v28.2.0 (normalize: minor+1); satır-edit + ekleme (ADR-042), §1-§18 korundu.

### §19.1 İskelet Eşlemesi

| İskelet Bölümü | Karşılık Gelen § |
|----------------|------------------|
| Başlık | H1 + frontmatter (7 zorunlu alan) |
| Purpose | §1 Amac |
| Scope | §2 Core + §4-§10 (Security/DB/Audio/HW/AI/Panel/Theme/ADR keyword grupları) |
| Architecture | §3 L0-L6 Layer Keywords (§3.1-§3.4A, §3A-§3D alt grupları) + [[brain.md]] §5 |
| Rules | §12 Navigation Rules (ADR-042 Uyumlu) + §15 Critical Warnings |
| Workflow | §11 Decision Tree + §13 Troubleshooting + §14 Quick Reference (+ Section 3B Prompt Archive) |
| Validation | §17 Quality Report + bu bölüm §19 (§19.1-§19.4) |
| References | §16 Cross References + §17A Implementasyon Dosyaları + §18 PDF Keyword Haritası |

### §19.2 Faz 3 Doğrulama (2026-09-23)

- [x] Frontmatter 7 alan tam; version 28.3.0; updated 2026-09-23
- [x] §1-§18 korundu, silme yok; yeni bölüm §19 eklendi
- [x] REFACTOR REPORT: FILE: keys.md · PURPOSE: Keyword router SSOT · VALIDATION: § + link korundu · RELATED: [[index.md]] · [[CLAUDE.md]] · [[glossary.md]] · [[log.md]]

### §19.3 İlgili Dosyalar

[[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[brain.md]] · [[index.md]] · [[glossary.md]] · [[log.md]]

### §19.4 Faz 2-3 İskelet Yeniden Düzenleme (2026-09-23)

- [x] 7 İngilizce H2 iskeleti uygulandı (Purpose/Scope/Architecture/Rules/Workflow/Validation/References); H1 korundu
- [x] Eski H2/H3 başlıklar `### §N` olarak taşındı; numaralar birebir korundu (§3.4A, §7A, §17A dahil)
- [x] Version 28.2.0 → 28.3.0 (frontmatter + §17 + §19.2)
- [x] §19.1 İskelet Eşlemesi nihai gruplamayla güncellendi
- [ ] ⚠️ VERIFICATION REQUIRED: yinelenen §3A/§3C/§3D etiketleri (×2) ve §12'de §12.1 boşluğu — kasıtlı korundu, sahip kararı bekleniyor
- [ ] ⚠️ VERIFICATION REQUIRED: `.ai/.templates/session/session-log-template.md` hedefi diskte `.ai/.templates/session-log-template.md` konumunda (yol öncesi)
- [x] REFACTOR REPORT: FILE: keys.md · PURPOSE: Keyword router SSOT (7-bölüm iskelet) · VALIDATION: 7 H2 + §-ankraj + mojibake 0 · RELATED: [[index.md]] · [[CLAUDE.md]] · [[glossary.md]] · [[log.md]]

---

## References

### §16 Cross References

| Kaynak | Hedef |
|--------|-------|
| keys.md | [[CLAUDE.md]], [[AGENTS.md]], [[WORKFLOW.md]], [[index.md]], [[brain.md]], [[MEMORY.md]], [[log.md]] |
| keys.md | [[.decisions/accepted/ADR-004-multi-domain-spa]], [[.decisions/accepted/ADR-005-ultrathink-protocol]] |

---

### §17A Implementasyon Dosyaları

> Detaylı dosya yapısı için: [[architecture/03-contracts/project-structure]] ve [[../shared/]] dizin yapısı

---

### §18 PDF Keyword Haritası

### Sistem Terimleri
| Keyword | Tanım | İlgili Dosya |
|---------|-------|-------------|
| Neva Engine | C++20 ses işleme motoru | [[architecture/k3-ses-motoru]] |
| Offline-First | İnternet olmadan çalışma | [[VISION]] |
| FLAC | Kayıpsız ses formatı | [[architecture/k15-medya-streaming]] |
| ASIO | Düşük gecikmeli ses protokolü | [[architecture/k2-surucu]] |
| WASAPI | Windows ses oturumu | [[architecture/k2-surucu]] |
| 8.1 Surround | 8 hoparlör + 1 subwoofer | [[architecture/k1-donanim]] |
| Class AB | Amplifikatör topolojisi | [[architecture/k16-k20-electronics/amfii/amplifier-classab-circuit]] |
| LM5122 | Boost converter | [[architecture/k16-k20-electronics/power/power-supply-classab]] |
| MJL21194 | NPN output transistör | [[architecture/k16-k20-electronics/amfii/amplifier-classab-circuit]] |
| 31-Band EQ | Parametrik EQ | [[architecture/k3-ses-motoru]] |
| Multi-Room | Çok odalı ses | [[architecture/k14-ag]] |
| DLNA | Medya paylaşım protokolü | [[architecture/k14-ag]] |
| WebRTC | P2P iletişim | [[architecture/k14-ag]] |

### Platform Terimleri
| Keyword | Tanım | İlgili Dosya |
|---------|-------|-------------|
| car.coremusic.net | Araç içi bilgi-eğlence | [[architecture/k10-uygulama]] |
| home.coremusic.net | Ev medya merkezi | [[architecture/k10-uygulama]] |
| studio.coremusic.net | Profesyonel stüdyo | [[architecture/k10-uygulama]] |
| media.coremusic.net | Merkezi medya depo | [[architecture/k15-medya-streaming]] |
| download.coremusic.net | İndirme servisi | [[architecture/k8-servis]] |
| api.coremusic.net | API Gateway | [[architecture/k9-api-routing]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
