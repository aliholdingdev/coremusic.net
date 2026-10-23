---
title: "CoreMusic Vault — Master Index"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 30.2.0
updated: 2026-10-23
date: 2026-08-09
total_root_files: 24
total_root_md: 23
total_ai_dirs: 21
total_recursive_files: 2530
total_recursive_md: 2239
total_recursive_dirs: 394
total_adr: 101
total_adr_accepted: 84
total_adr_rejected: 10
total_adr_draft: 6
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: []
---

# CoreMusic Vault — Master Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]] · [[SYNC]] · [[CONTEXT]] · [[RAG]]

**Skills:** Registry SSOT = kök `CLAUDE.md` §Skill Registry — **13 proje skill'i** (`.claude/skills/`, 2026-10-12 disk ölçümü). Global skill envanteri `C:\.claude\skills` (348 dizin, proje dışı — bu makinede diskte YOK/2026-10-23; **diğer PC/server'da mevcut — kullanıcı notu: silinmez**). `.opencode/skills/` diskte YOK (kapatıldı).

---

## Purpose

### §1 Amaç

Bu dosya, CoreMusic `.ai/` vault'unun ana navigasyon noktasıdır. Tüm vault dosyaları kategorize edilmiş ve hızlı erişilebilir biçimde listelenir. **Bu dosya hub'dır**: içerik alt sayfalarda (`index/01-05`), künye buradadır.

---

## Scope

### §2 Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Vault genel bakış | Bu dosya (index.md) |
| Ürün & Ekosistem Vizyonu | [[VISION.md]] |
| Proje Tanımı & Hangi Sorunları Çözer | [[PROJECTS.md]] |
| Keyword arama | [[keys.md]] |
| Konu→dosya retrieval indeksi (hangi soru hangi dosya) | [[RAG.md]] |
| Terimler Sözlüğü | [[glossary.md]] |
| Mimari kararlar (özet) | [[brain.md]] |
| Mimari kararlar (ADR arşivi) | `.ai/.decisions/index.md` (101 ADR: 84/10/6 · v1.5.2) |
| Ajan yetkileri | [[AGENTS.md]] |
| Agent profilleri | [[.agents/AGENTS]] (11 profil) |
| Süreçler | [[WORKFLOW.md]] |
| Bellek yönetimi | [[MEMORY.md]] |
| Aktivite günlüğü | [[log.md]] |
| Sürekli güncelleme döngüsü (baş/orta/son) | [[SYNC.md]] (hedef set 26) |
| Session Checklist | [[CHECKLIST.md]] (§A/§B/§C, 5'er madde) |
| Context Planner spec | [[PLANNER.md]] (Control Plane, 13 alan FM girdisi) |
| ADR kataloğu | §5 bu dosya |
| Servis haritası | §6 bu dosya |
| Veritabanı | §8 bu dosya |
| UI / Mockup / Frontend | [[ui-design/01-mockup-index]] (19 PNG, C01-C16, 45-Tier) |
| Şablon seçimi (Guardrail #16) | [[.templates/index]] (66 dosya · 13 kategori) |
| SQL şema | `.ai/.sql/` (20 `.sql`, 4 DBMS) |
| Açık İş Listesi | [[TODO.md]] (P0/P1/P2) |
| Link/ADR denetimi | `.ai/scripts/wiki-link-check.ps1` (kökten çalıştır) · `.ai/broken-links-report.md` **diskte YOK** (işte üretilir) |

---

### §3 SSOT Core Dosyaları (23 `.ai/` kök `.md` — 2026-10-23 disk ölçümü)

| # | Dosya | Amaç | Sınıf |
|---|-------|------|-------|
| 1 | [[CLAUDE.md]] | Kanonik AI talimatı — boot protokolü §16, 16 guardrail §7, yasaklar §21 | 🔴 CRITICAL |
| 2 | [[AGENTS.md]] | Agent kayıt defteri — 11 uzmanlık + MO, routing §6, handover §9, escalation §10 | 🔴 CRITICAL |
| 3 | [[WORKFLOW.md]] | Süreçler — fazlar, kapılar, ADR lifecycle §8-§13 | 🔴 CRITICAL |
| 4 | [[index.md]] | Bu dosya — vault navigasyon hub'ı | 🔴 CRITICAL |
| 5 | [[keys.md]] | Anahtar kelime haritası — keyword → dosya yönlendirme | 🔴 CRITICAL |
| 6 | [[brain.md]] | Mimari kararlar özeti — ADR türevi, L0-L6, engineering brain | 🔴 CRITICAL |
| 7 | [[MEMORY.md]] | Oturum hafızası — persistent state, cache, session lifecycle | 🔴 CRITICAL |
| 8 | [[log.md]] | Aktivite günlüğü — append-only audit trail | ⚪ LOG |
| 9 | [[engine.md]] | Orkestrasyon motoru — agent koordinasyonu, task dispatch, faz kapanışı | 🔴 CRITICAL |
| 10 | [[VISION.md]] | Ürün ve ekosistem vizyonu — mülkiyet felsefesi, 6 sorun-çözüm, handoff | 🔴 CRITICAL |
| 11 | [[PROJECTS.md]] | CoreMusic nedir, 10 temel yetenek, 6 hedef kullanıcı, sektörel çözümler | 🔴 CRITICAL |
| 12 | [[glossary.md]] | Terimler sözlüğü — Neva Engine, Bit-Perfect, Ambient Aura vb. | 🔴 CRITICAL |
| 13 | [[ROLE.md]] | Rol ve sorumluluk tanımı — rol → teknoloji eşlemesi | 🔴 CRITICAL |
| 14 | [[ULTRA-THINKING.md]] | Ultra düşünme protokolü — karar öncesi zorunlu doğrulama | 🔴 CRITICAL |
| 15 | [[PLANNER.md]] | Context Planner Spec (Control Plane) — otomatik context yükleme sözleşmesi; FM 13 alan | 🟠 ON-DEMAND |
| 16 | [[RAG.md]] | Retrieval indeksi + pipeline — konu→dosya eşlemesi (§3) · pipeline (§4, ÇOĞU PLANNED — ADR-030) | 🔴 CRITICAL |
| 17 | [[CONTEXT.md]] | Vault klasör envanteri — kök/dizin sayıları, boot ilişkisi, disk çelişkileri | 🔴 CRITICAL |
| 18 | [[CHECKLIST.md]] | Session checklist — §A/§B/§C (baş/orta/kapanış), hedef set sınıflandırması §A0 | 🔴 CRITICAL |
| 19 | [[SYNC.md]] | Sürekli güncelleme döngüsü SSOT'u — hedef set 26, üç kapı, tazelik ölçütü §5 | 🔴 CRITICAL |
| 20 | [[PLAN.md]] | Plan görünümü | 🟠 ON-DEMAND |
| 21 | [[TODO.md]] | Açık iş listesi — P0/P1/P2 önceliklendirme | 🟠 ON-DEMAND |
| 22 | [[AGENT-BRIEFING.md]] | Operasyonel brifing — her bölüm disk kanıtına bağlı brief (K000 node, `status: verify-required`) | 🟠 ON-DEMAND |
| 23 | [[PRD.md]] | Vault PRD — `.ai/` belge sistemi gereksinim belgesi (`authority: Derived`, 2026-10-23; v1.0.1 sayım hizası: 33 alt PRD) + kök `PRD.md` aile indeksi (v1.2.0 — 34 yüzey, 2026-10-23) | 🟠 ON-DEMAND |

**Sınıf sayımı:** 🔴 CRITICAL **18** · 🟠 ON-DEMAND **5** (PLAN · PLANNER · TODO · PRD · AGENT-BRIEFING) · ⚪ LOG **1** = 24 satır (LOG dosyası CRITICAL setine de girer; benzersiz dosya = 23).

**K-SPACE KALDIRILDI (2026-10-12):** K-space katman mimarisi (K000–K020 / A0–A5 / `.ai/architecture/` 46 dosya) ve ADR-061 birlikte silindi; 4 mimari şablon + `.workflows/architecture-write.md` + `.superpowers/` kaldırıldı. Yedek: `C:\temp\opencode\vault-backup-2026-10-12`. Bu dosyadaki tüm sayılar 2026-10-12 disk ölçümüyle yeniden yazıldı.

⚠️ **DÜZELTİLDİ (2026-10-10):**
- `[[TECHNICAL_DOCUMENTATION.md]]` satırı **kaldırıldı** — `.ai/` kökünde diskte YOK (0 dosya).
- `[[broken-links-report.md]]` satırı **kaldırıldı** — `.ai/` kökünde diskte YOK.
- `.opencode/skills/` (8 aktif skill) notu **kaldırıldı** — dizin diskte YOK; Registry SSOT kök `CLAUDE.md`'ye taşındı (13 proje skill).
- `total_files: 720` / `total_adr: 80` → 2026-10-10: **804 dosya / 97 ADR** → 2026-10-12: **665 dosya / 91 ADR** (K-space kaldırımı sonrası yeniden ölçüm).

---

### §4 Bölümler

> Bu dosya çok-sayfalı yapıya ayrılmıştır (hub). İçerik aşağıdaki alt sayfalardadır; SSOT künyesi bu dosyadadır.

| Sayfa | Bölüm |
|-------|-------|
| [[index/01-mimari]] | Mimari (ADR tabanlı mimari karar özeti, subdomain topolojisi, ortak çekirdek — K-space 2026-10-12'de kaldırıldı) |
| [[index/02-kurallar]] | Kurallar (guardrail, yasak kalıp, LINT) |
| [[index/03-workflow]] | Workflow (fazlar, kapılar, ADR lifecycle) |
| [[index/04-dogrulama]] | Doğrulama (validator, link check, şablon kontrolü) |
| [[index/05-referanslar]] | Referanslar (harici kaynak, ekosistem, raporlar) |

---

### §5 ADR Kataloğu

**SSOT:** `.ai/.decisions/index.md` (v1.5.2, 2026-10-23). Bu dosyada liste **kopyalanmaz** — yalnız işaretçi verilir.

| Durum | Sayı (2026-10-23 disk ölçümü — `.decisions/index.md` FM ile hizalı) |
|-------|-------------------------------|
| Frozen (001-036) | 36 |
| Active (037-101 + kökteki ADR-091) | 49 |
| Rejected (R-001..R-010) | 10 |
| Draft (103/104/107/108/109/110) | 6 |
| **Toplam dosya** | **101** |

> **Not (2026-10-12):** ADR-061 (K-space katman tanımı) K-space ile birlikte kaldırıldı → toplam 91.
> **Not (2026-10-23):** Sayılar `.decisions/index.md` v1.5.2 FM ile hizalandı (accepted 84 · rejected 10 · draft 6 · kök ADR-091 · toplam 101 — önceki "91 · 80/10/0 · v1.5.0" yazımı bayattı).

---

### §6 Servis Haritası

| Subdomain | Kapsam | Alan |
|-----------|--------|------|
| `coremusic.net` | Ana tanıtım & portal | Sunum |
| `home.coremusic.net` | public/uygulama yüzeyi (sunum) | Sunum |
| `auth.coremusic.net` | kimlik/oturum/JWT RS256/MFA (ADR-043/052/059/095) | Kimlik |
| `api.coremusic.net` | Gateway/BFF (ADR-084) · Origin/CSRF (ADR-094) | API |
| `media.coremusic.net` | medya arşivi/teslim (ADR-092 ULID) | Medya |
| `assets.coremusic.net` | statik varlık (ITCSS katmanları, token'lar) | Varlık |

**Kural:** subdomain'ler birbirine kod IMPORT etmez; yalnız `shared/` + API sözleşmesi.
**Ortak çekirdek:** `shared/` (composer package; `shared/src/{Middleware,PageRouter,Database}/`).

*Not (2026-10-12):* Subdomain dizinleri diskte mevcut (kök `*.coremusic.net/` ×5); topoloji kaynaklı eski `architecture/00-master-index` K-space ile kaldırıldı — ADR atıfları `.ai/.decisions/` içindedir.

---

### §7 Vault Dizin Haritası (21 depth-1 dizin — 2026-10-23 disk ölçümü)

| Dizin | Dosya | Amaç |
|-------|-------|------|
| `ui-design/` | 121 md | Mockup, screens, flow, prompt, reference, tokens |
| `.personas/` | 84 md | Kullanıcı persona dosyaları |
| `.decisions/` | 99 md | ADR arşivi (92 ADR + index/CLAUDE yardımcıları — 2026-10-15 ölçüm: `accepted/` 80 ADR + `rejected/` 10 R + `draft/` 2 ADR + 6 yardımcı CLAUDE/index) |
| `architecture/` | 1187 md | K-space mimari düğümleri (K000-K020, 28 alt dizin + kök index/README — 2026-10-15 ölçüm; §72 satır 7'deki "diskte YOK" iddiası geçersizdir) |
| `electronic/` | 60 md | Donanım/dsp/firmware vault (drivers 10 · dsp 10 · amplifier 8 · hardware 8 · firmware 7 + kök 17 — 2026-10-15 ölçüm) |
| `.templates/` | 66 md | Guardrail #16 şablonları (13 kategori) |
| `ecosystem/` | 13 md | Ekosistem dersleri/kaynakları |
| `.agents/` | 12 md | 11 agent profili + alt registry |
| `.sql/` | 20 .sql + 5 md | SQL şema (4 DBMS: mssql/mysql/postgresql/sqlite) |
| `scripts/` | 10 dosya | Doğrulama/vault betikleri |
| `index/` | 5 md | Master katalog hub alt sayfaları (01-05) |
| `.png/` | 26 PNG + 5 md | Görsel SSOT (salt okunur — alt dizinli: admin-1920 · home-1024 · home-1920 · home-3840 · shared-1024; 2026-10-23 recurse ölçümü — önceki "19 PNG + 4 md" bayattı) |
| `servers/` | 3 md | Sunucu/kurulum notları |
| `SESSIONS/` | 16 md | Session kayıtları (ölçüm 2026-10-23; son eklenen: 2026-10-23-neva-engine-neva-player-prd-uretimi) |
| `.obsidian/` | 5 dosya | Obsidian yapılandırması |
| `.rules/` | 1 md | `senior-mode.md` (error-recovery.md YOK) |
| `prompts/` | 4 md | Prompt arşivi (2026-10-23 disk ölçümü — dizin **yeniden mevcut**; eski "DİZİN YOK (2026-10-15 Test-Path=False)" iddiası tarihseldir, artık geçersiz) |
| `research/` | 7 md | Araştırma notları (2026-10-23 disk ölçümü) |
| `tmp/` | 25 dosya | Geçici çalışma alanı (2026-10-23 disk ölçümü) |
| `reports/` | 18 dosya + 1 dizin | Rapor arşivi (2026-10-22 disk ölçümü: backend-step01-11 = 10 rapor md + 5 curl-pc.ps1 + k000/k006 dil-kapsama + neva-re-dump-aimp-vlc + `k001-taksonomi/` dizini; önceki "12 dosya (11 rapor + index.md)" sayımı bayat — `reports/index.md` diskte YOK) |
| `.diagram/` | — | **DİZİN YOK** (2026-10-15 `Test-Path=False`) |
| `projects/` | 87 md (2026-10-23 disk ölçümü — +2 Neva PRD: `NevaEngine/coremusic-neva-engine-prd` + `NevaPlayer/coremusic-neva-player-prd`, K-6 onaylı 2026-10-23; önceki 84 md / 2026-10-20 sayımı — `10-dil-ffi-feasibility-analizi` + `reverse/runtime-ozel-gozlem-capraz-dogrulama` ekli) | **NovaSearchEngine (2 doc, 2026-10-16):** `01-kurulum-mimari-ve-acik-konular` (proje kökü `app-nodejs-projects/nova-search-engine` — konum düzeltmesi: eski `C:\www\nova-search-engine` terk edildi · ADR-001..007 + debate D1–D7 · OpenRouter canlı doğrulama: nemotron-3-super/3.5-lightning/inkling 200 ✓, gemma-4 429, deepseek-chat-v3-0324:free 404 · coverage %60.30 lines/%52.21 funcs vs %75 kapı AÇIK · 40/40 bun test · tsc 0 · 4 açık madde: MySQL canlı test, coverage, likeCount/publishedAt V2, aylık free-model taraması) · **Neva Connect (11 doc, 2026-10-15):** `NevaConnect/01-wlanapi-sürüm-matrisi-ve-windows-plani` (pconlife 9 DLL + yerel Win10 19041 = 10 DLL MD5 ✓ · PE export matrisi XP 36 → Win7 48 → Win8 103 → Win8.1 108 → Win10 10240 121 → 19041 148 · **her geçişte −0** · REA/Ghidra 12.1.4 çapraz kontrol · 6 katman plan + 4 açık madde) · **02 çok-exe + Windows SDK derleme matrisi** (769 satır: v141_xp/SDK 7.1A vs v143/Win10 SDK, `_WIN32_WINNT` define kuralı, Level 0-3 istisna/fallback, dumpbin /imports kapı, 42 kaynak) · **03 sürüme göre özellik kısıtlama** (678 satır: GetProcAddress + dönüş-kodu ikili kapı, List2→List1 degradation, MSDN min-OS teyitleri, KB940541, 21 kaynak) · **04 kapsam yol haritası** (1129 satır: WiFi 7 = Win11 24H2 + WiFiCx MLO, BT Win32/WinRT, WinUSB, SATA BusType 0x0B/NVMe 0x11, OS×özellik matrisi, 50+ kaynak) · **05 neva-wlan çekirdek modül tasarımı** (666 satır: wifi list/connect/status akışları, SPSC kuyruk + WlanCloseHandle kapanış sırası, WS 9742 JSON sözleşmesi, 17 satır Level hata tablosu; §2.1/§9 felaket yolu ile güncellendi) · **06 test & kalite planı** (560 satır: OS×modül matrisi, 45 birim senaryo, XP uç smoke X1-X4b, N1-N9 negatif, §7.3 T-01…T-12 felaket yolu, GH Actions gate) · **07 XP WZC stack kaynak araştırması** (500 satır: nt5src clone daad8a087 — XP SP1'de wlanapi YOK/WZC tam kaynak, wzc.idl UUID+pipe, NDISUIO OID 802.11 felaket yolu, XP panel lanui envanteri, WZC-wlanapi izomorfizm; §2.2 NDISUIO düzeltmesi) · **08 XP panel davranış sözleşmesi** (595 satır: lanui/wzcui/chklist kaynağından durum sınıfları, 3500 ms liste timer, bağlanma akışı, gerçek IDS_* mesajları, WS 9742 eşleme 22 satır) · **09 XP NDISUIO felaket yolu tasarımı** (554 satır: `\\.\Ndisuio` + 5 IOCTL nuiouser.h kanıtlı, NDISUIO_OID_QUERY/Set yapıları, BSSID listesi walk güvenlik, L0-L4 yerleşimi, karar akışı, T-01…T-12) · **10 WZC/EAPOL/NDISUIO registry değerleri** v1.1.0 (nt5src + yerel Win10/11 HKLM okuması: WZCSVC kökü wirelessspd.c:157-159, WZC profil saklama düzeni storage.h — ActiveSettings/Static# REG_BINARY + LayoutVersion=4, EAPOL gerçek değer adları eapolutil.c:46-55 authPeriod/SupplicantMode vs (2 açık madde kapandı), politika store GPTWirelessPolicy/GPTWiFiPolicy/Cache, netwzc.inx Start=2+ServiceDll, NDISUIO NDIS 6.30 kaydı, WZCSVC→WlanSvc/dot3svc eşleme + LayoutVersion izomorfizmi, 9 maddelik açık liste + XP VM reg query komutları + 3 karar kartı) · **11 windows wlanapi mimarisi** (507 satır: 10 DLL PE analizi pe_arch.py — XP=9 import/WZCSAPI 19 fn + RPCRT4 yalnız UuidFromString, Vista+=async RPC Ndr64AsyncClientCall, mitigasyon XP 0x0 → Win10 0x4160 CFG, bölüm evrimi .didat/`.idata`, export mutabakatı toplam 36→48→153→173→220→258 vs Wlan* 36→48→103→108→121→148 (WFD ailesi Win8+), WNF/threadpool bildirim kanıtı, XP token impersonation + Win10 ControlService, 36 export işlevsel gruplama + delta zinciri, 8 MS Learn HEAD-200, 8 açık madde) + **Neva Engine (49 md):** `00-sdk-lib-envanter`..`10-io-format-32bit-192khz` (11 doc) + **`11-cok-dilli-kod-ornekleri`** (index: 10 dil × 6 örnek, K1-K6) + **`11-examples/` 10 dosya** (`01-c11`·`02-cpp20`·`03-csharp`·`04-php8`·`05-javascript`·`06-typescript`·`07-python`·`08-go`·`09-rust`·`10-html-css`, ~235KB, derlenmedi, 2026-10-16) · **NevaPlayer (8 md):** `01-kapsam-ve-kabul-kriterleri` (v0.2.3) + **`10-dil-ffi-feasibility-analizi`** (650 satır · 34.6 KB · 25 dil FFI uyumluluk + feasibility + **statik (§5) + runtime (§6) + kod örneği (§4, 24 blok) hepsi** · `neva_c.h` R1–R9 (tek başlık / PIMPL / opaque handle / `struct_size` / sembol gizleme) · `neva_session_api_version()` handshake (ADR §50) · ctypes C ABI smoke (ADR §5.5) · blok granülaritesi hesabı cgo ~40 ns → 128 karede ~%0,006 · RT yasağı §6.3 · U1–U9 + R-1..R-4) + `research/3` (`lib-matrix` · `media-stack-web-verification` 112 kaynak · `radio-and-hires`) + `reverse/3`, 2026-10-20) |

**Toplam:** `.ai/` recursive **2530 dosya · 2239 `.md` · 394 dizin** · kök **23 `.md`** · depth-1 **21 dizin** (2026-10-23 doğrudan `Get-ChildItem -Recurse -Force` disk ölçümü — beyin/boot hizalama; önceki 2026-10-22 tablosu (2520·2232·22 kök .md) bayattı, delta +7 dosya/+4 md/+1 kök .md (`PRD.md`)). **2026-10-23 Neva PRD kapanışı:** +3 dosya/+3 md (`SESSIONS/` +1 · `projects/NevaEngine/` +1 · `projects/NevaPlayer/` +1 → 2527→2530 · 2236→2239; projects 84→87 md). **Metodoloji düzeltmesi:** Önceki "673→676 dosya · 443→446 md · 72→73 dizin" serisi **eski tabana +delta** eklenerek üretilmişti ve `architecture/` (1187 md) + `electronic/` (60 md) + `.personas/` (84 md) gibi dizinleri dışarıda bırakıyordu; bu yüzden disk gerçeğiyle (1974/1745/394) uyuşmuyordu. 2026-10-12 tabanı `architecture/` "kaldırıldı" varsayımıyla ölçülmüştü; `.ai/architecture/` K000-K020 ile diskte MEVCUT olduğundan eski taban **geçersizdir**. Bu satırdan itibaren **doğrudan disk ölçümü** esastır; delta listesi yalnız tarihi kayıt olarak korunur: NevaConnect 01→11 = +11 md (10 = WZC/EAPOL/NDISUIO registry değerleri, 11 = wlanapi PE mimarisi, 2026-10-15) · architecture K014+K000 = +2 md · NevaEngine 10-io-format = +1 md · **2026-10-16: NevaEngine doc 11 index + `11-examples/` 10 dil dosyası = +11 md (+1 dizin, projects 49→60; toplam 1974→2144 dosya / 1745→1915 md / 394→395 dizin — paralel turlar dahil doğrudan ölçüm)** · **2026-10-16: NovaSearchEngine doc 01 = +1 md (+1 dizin, projects 60→61; toplam 2144→2243 dosya / 1915→2007 md / 395→406 dizin — doğrudan ölçüm, fark = 2026-10-16 arası .ai/ genelindeki diğer eklemeler dahil)** · kök .md 21→22 (`AGENT-BRIEFING.md` +1).
---

### §8 Veritabanı

| İddia | Durum |
|-------|-------|
| "18 BCNF DB" (ADR-040) | ⚠️ VERIFICATION REQUIRED — diskte 20 `.sql` dosyası; ADR-003 "9 BCNF" der |
| PDO-only, ORM yasak (ADR-002 frozen) | ✅ ADR-001..036 frozen |
| Multi-DB migration (ADR-014) | ✅ ADR kayıtlı |
| Cross-DB outbox + CQRS-semi (ADR-100) | ✅ ADR-101 (2026-10-09) |

---

## Validation

### §9 Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + Control Plane v2 bloğu |
| 2 | Kök dosya sayımı | 23 `.md` (2026-10-23 disk ölçümü; +`AGENT-BRIEFING.md` ve +`PRD.md` eklendiği için 22 iddiası bayattı) |
| 3 | Dizin sayımı | 21 depth-1 dizin (2026-10-23 disk ölçümü; `.agents .decisions .obsidian .personas .png .rules .sql .templates architecture ecosystem electronic index projects prompts reports research scripts servers SESSIONS tmp ui-design` — `prompts/` yeniden mevcut (4 md); yalnız `.diagram/` YOK) |
| 4 | Recursive | 2527 dosya · 2236 md · 394 dizin (2026-10-23 disk ölçümü; eski "665·435·72" ve "1974·1745" tabanları bayat — `architecture/` dahil doğrudan ölçüm) |
| 5 | ADR | 101 (84 kabul / 10 red / 6 taslak + kökteki ADR-091 — `.decisions/index.md` v1.5.2 ile hizalı; 2026-10-23 disk sayımı: `accepted/` 84 + `rejected/` 10 + `draft/` 6 + kök 1) |
| 6 | Skill Registry | 13 proje skill — kök `CLAUDE.md` §Skill Registry ile hizalı |
| 7 | K-space | `architecture/` diskte **MEVCUT** (2026-10-15 16:09: 1187 md · 28 alt dizin · K000-K020 · kök index/README — eski "diskte YOK / kaldırım 2026-10-12" iddiası **geçersizdir**; yedek `C:\temp\opencode\vault-backup-2026-10-12` referansı tarihi kayıt) |
| 8 | Ölü referans | `TECHNICAL_DOCUMENTATION.md` · `broken-links-report.md` · `.opencode/skills` — üçü de kaldırıldı |
| 9 | Hub | §4 alt sayfalar diskte (index/01-05) |
| 10 | Placeholder | `{{` yok, doldurulmamış yer yok |

---

## References

### §10 Bağlantılar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Vault anayasası | [[CLAUDE]] | Guardrail, boot protokolü |
| Agent registry | [[AGENTS]] | Routing, handover |
| Vault süreç | [[WORKFLOW]] | Fazlar, kapılar |
| Retrieval indeksi | [[RAG]] | Konu→dosya eşlemesi |
| Sürekli döngü | [[SYNC]] | Hedef set 26, üç kapı |
| Vault envanteri | [[CONTEXT]] | Dizin/dosya sayıları |
| Karar arşivi | `.ai/.decisions/index.md` | ADR listesi |
| Şablon registry | [[.templates/index]] | 66 şablon / 13 kategori |
| Alt registry | [[.agents/AGENTS]] | 11 agent profili |
| Doğrulama betiği | `.ai/scripts/validate.mjs` | 7 check (fm/tier/ssot/dep/link/orphan/fanout) |
| Link denetimi | `.ai/scripts/wiki-link-check.ps1` | Wiki-link doğrulama |
| Disk kanıtı | 2026-10-23 ölçümü | §3 · §7 · §9 tüm sayılar (beyin/boot hizalama) |

---

**Authority:** Bayram Ali / Vault Steward
**Version:** 30.1.0
**Last Updated:** 2026-10-23
**Mode:** Red Team · Human Mode · Truth Mode
