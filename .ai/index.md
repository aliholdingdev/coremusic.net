---
title: "CoreMusic Vault — Master Index"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 30.0.0
updated: 2026-10-12
date: 2026-08-09
total_root_files: 22
total_root_md: 21
total_ai_dirs: 17
total_recursive_files: 665
total_recursive_md: 435
total_recursive_dirs: 72
total_adr: 91
total_adr_accepted: 80
total_adr_rejected: 10
total_adr_draft: 0
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

**Skills:** Registry SSOT = kök `CLAUDE.md` §Skill Registry — **13 proje skill'i** (`.claude/skills/`, 2026-10-12 disk ölçümü). Global skill envanteri `C:\.claude\skills` (348 dizin, proje dışı). `.opencode/skills/` diskte YOK (kapatıldı).

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
| Mimari kararlar (ADR arşivi) | `.ai/.decisions/index.md` (91 ADR: 80/10/0 · v1.5.0) |
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

### §3 SSOT Core Dosyaları (21 `.ai/` kök `.md` — 2026-10-12 disk ölçümü)

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

**Sınıf sayımı:** 🔴 CRITICAL **18** · 🟠 ON-DEMAND **3** (PLAN · PLANNER · TODO) · ⚪ LOG **1** = 22 satır (LOG dosyası CRITICAL setine de girer; benzersiz dosya = 21).

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

**SSOT:** `.ai/.decisions/index.md` (v1.5.0, 2026-10-12). Bu dosyada liste **kopyalanmaz** — yalnız işaretçi verilir.

| Durum | Sayı (2026-10-12 disk ölçümü) |
|-------|-------------------------------|
| Frozen (001-036) | 36 |
| Active (037-101) | 45 |
| Rejected (R-001..R-010) | 10 |
| Draft | 0 |
| **Toplam dosya** | **91** |

> **Not (2026-10-12):** ADR-061 (K-space katman tanımı) K-space ile birlikte kaldırıldı → toplam 91.

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

### §7 Vault Dizin Haritası (18 depth-1 dizin — 2026-10-15 16:09 disk ölçümü)

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
| `.png/` | 19 PNG + 4 md | Görsel SSOT (salt okunur) |
| `servers/` | 3 md | Sunucu/kurulum notları |
| `SESSIONS/` | 3 md | Session kayıtları (2026-10-14 runtime-os-gruplar eklendi) |
| `.obsidian/` | 5 dosya | Obsidian yapılandırması |
| `.rules/` | 1 md | `senior-mode.md` (error-recovery.md YOK) |
| `prompts/` | — | **DİZİN YOK** (2026-10-15 `Test-Path=False`; 2026-10-12 "boş dizin" iddiası geçersiz — dizin hiç var olmamış/taşınmış) |
| `reports/` | 12 dosya | Rapor arşivi (11 rapor + index.md — k001 celiski/cihaz-envanteri/domain-esleme/kabul-kriterleri/mevcut-durum/onerilen-agac/tur-envanteri/uygulama-plani + k000 dil-kapsama/os-donanima-esleme + k006 dil-kapsama; 2026-10-14/15 eklendi — "boş dizin" iddiası geçersiz) |
| `.diagram/` | — | **DİZİN YOK** (2026-10-15 `Test-Path=False`) |
| `projects/` | 82 md (2026-10-20 disk sayımı — `10-dil-ffi-feasibility-analizi` eklendi) | **NovaSearchEngine (2 doc, 2026-10-16):** `01-kurulum-mimari-ve-acik-konular` (proje kökü `app-nodejs-projects/nova-search-engine` — konum düzeltmesi: eski `C:\www\nova-search-engine` terk edildi · ADR-001..007 + debate D1–D7 · OpenRouter canlı doğrulama: nemotron-3-super/3.5-lightning/inkling 200 ✓, gemma-4 429, deepseek-chat-v3-0324:free 404 · coverage %60.30 lines/%52.21 funcs vs %75 kapı AÇIK · 40/40 bun test · tsc 0 · 4 açık madde: MySQL canlı test, coverage, likeCount/publishedAt V2, aylık free-model taraması) · **Neva Connect (11 doc, 2026-10-15):** `NevaConnect/01-wlanapi-sürüm-matrisi-ve-windows-plani` (pconlife 9 DLL + yerel Win10 19041 = 10 DLL MD5 ✓ · PE export matrisi XP 36 → Win7 48 → Win8 103 → Win8.1 108 → Win10 10240 121 → 19041 148 · **her geçişte −0** · REA/Ghidra 12.1.4 çapraz kontrol · 6 katman plan + 4 açık madde) · **02 çok-exe + Windows SDK derleme matrisi** (769 satır: v141_xp/SDK 7.1A vs v143/Win10 SDK, `_WIN32_WINNT` define kuralı, Level 0-3 istisna/fallback, dumpbin /imports kapı, 42 kaynak) · **03 sürüme göre özellik kısıtlama** (678 satır: GetProcAddress + dönüş-kodu ikili kapı, List2→List1 degradation, MSDN min-OS teyitleri, KB940541, 21 kaynak) · **04 kapsam yol haritası** (1129 satır: WiFi 7 = Win11 24H2 + WiFiCx MLO, BT Win32/WinRT, WinUSB, SATA BusType 0x0B/NVMe 0x11, OS×özellik matrisi, 50+ kaynak) · **05 neva-wlan çekirdek modül tasarımı** (666 satır: wifi list/connect/status akışları, SPSC kuyruk + WlanCloseHandle kapanış sırası, WS 9742 JSON sözleşmesi, 17 satır Level hata tablosu; §2.1/§9 felaket yolu ile güncellendi) · **06 test & kalite planı** (560 satır: OS×modül matrisi, 45 birim senaryo, XP uç smoke X1-X4b, N1-N9 negatif, §7.3 T-01…T-12 felaket yolu, GH Actions gate) · **07 XP WZC stack kaynak araştırması** (500 satır: nt5src clone daad8a087 — XP SP1'de wlanapi YOK/WZC tam kaynak, wzc.idl UUID+pipe, NDISUIO OID 802.11 felaket yolu, XP panel lanui envanteri, WZC-wlanapi izomorfizm; §2.2 NDISUIO düzeltmesi) · **08 XP panel davranış sözleşmesi** (595 satır: lanui/wzcui/chklist kaynağından durum sınıfları, 3500 ms liste timer, bağlanma akışı, gerçek IDS_* mesajları, WS 9742 eşleme 22 satır) · **09 XP NDISUIO felaket yolu tasarımı** (554 satır: `\\.\Ndisuio` + 5 IOCTL nuiouser.h kanıtlı, NDISUIO_OID_QUERY/Set yapıları, BSSID listesi walk güvenlik, L0-L4 yerleşimi, karar akışı, T-01…T-12) · **10 WZC/EAPOL/NDISUIO registry değerleri** v1.1.0 (nt5src + yerel Win10/11 HKLM okuması: WZCSVC kökü wirelessspd.c:157-159, WZC profil saklama düzeni storage.h — ActiveSettings/Static# REG_BINARY + LayoutVersion=4, EAPOL gerçek değer adları eapolutil.c:46-55 authPeriod/SupplicantMode vs (2 açık madde kapandı), politika store GPTWirelessPolicy/GPTWiFiPolicy/Cache, netwzc.inx Start=2+ServiceDll, NDISUIO NDIS 6.30 kaydı, WZCSVC→WlanSvc/dot3svc eşleme + LayoutVersion izomorfizmi, 9 maddelik açık liste + XP VM reg query komutları + 3 karar kartı) · **11 windows wlanapi mimarisi** (507 satır: 10 DLL PE analizi pe_arch.py — XP=9 import/WZCSAPI 19 fn + RPCRT4 yalnız UuidFromString, Vista+=async RPC Ndr64AsyncClientCall, mitigasyon XP 0x0 → Win10 0x4160 CFG, bölüm evrimi .didat/`.idata`, export mutabakatı toplam 36→48→153→173→220→258 vs Wlan* 36→48→103→108→121→148 (WFD ailesi Win8+), WNF/threadpool bildirim kanıtı, XP token impersonation + Win10 ControlService, 36 export işlevsel gruplama + delta zinciri, 8 MS Learn HEAD-200, 8 açık madde) + **Neva Engine (49 md):** `00-sdk-lib-envanter`..`10-io-format-32bit-192khz` (11 doc) + **`11-cok-dilli-kod-ornekleri`** (index: 10 dil × 6 örnek, K1-K6) + **`11-examples/` 10 dosya** (`01-c11`·`02-cpp20`·`03-csharp`·`04-php8`·`05-javascript`·`06-typescript`·`07-python`·`08-go`·`09-rust`·`10-html-css`, ~235KB, derlenmedi, 2026-10-16) · **NevaPlayer (8 md):** `01-kapsam-ve-kabul-kriterleri` (v0.2.3) + **`10-dil-ffi-feasibility-analizi`** (650 satır · 34.6 KB · 25 dil FFI uyumluluk + feasibility + **statik (§5) + runtime (§6) + kod örneği (§4, 24 blok) hepsi** · `neva_c.h` R1–R9 (tek başlık / PIMPL / opaque handle / `struct_size` / sembol gizleme) · `neva_session_api_version()` handshake (ADR §50) · ctypes C ABI smoke (ADR §5.5) · blok granülaritesi hesabı cgo ~40 ns → 128 karede ~%0,006 · RT yasağı §6.3 · U1–U9 + R-1..R-4) + `research/3` (`lib-matrix` · `media-stack-web-verification` 112 kaynak · `radio-and-hires`) + `reverse/3`, 2026-10-20) |

**Toplam:** `.ai/` recursive **2244 dosya · 2008 `.md` · 406 dizin** · kök **22 `.md`** · depth-1 **19 dizin** (2026-10-16 doğrudan `Get-ChildItem -Recurse -Force` disk ölçümü — NovaSearchEngine kaydı sonrası). **Metodoloji düzeltmesi:** Önceki "673→676 dosya · 443→446 md · 72→73 dizin" serisi **eski tabana +delta** eklenerek üretilmişti ve `architecture/` (1187 md) + `electronic/` (60 md) + `.personas/` (84 md) gibi dizinleri dışarıda bırakıyordu; bu yüzden disk gerçeğiyle (1974/1745/394) uyuşmuyordu. 2026-10-12 tabanı `architecture/` "kaldırıldı" varsayımıyla ölçülmüştü; `.ai/architecture/` K000-K020 ile diskte MEVCUT olduğundan eski taban **geçersizdir**. Bu satırdan itibaren **doğrudan disk ölçümü** esastır; delta listesi yalnız tarihi kayıt olarak korunur: NevaConnect 01→11 = +11 md (10 = WZC/EAPOL/NDISUIO registry değerleri, 11 = wlanapi PE mimarisi, 2026-10-15) · architecture K014+K000 = +2 md · NevaEngine 10-io-format = +1 md · **2026-10-16: NevaEngine doc 11 index + `11-examples/` 10 dil dosyası = +11 md (+1 dizin, projects 49→60; toplam 1974→2144 dosya / 1745→1915 md / 394→395 dizin — paralel turlar dahil doğrudan ölçüm)** · **2026-10-16: NovaSearchEngine doc 01 = +1 md (+1 dizin, projects 60→61; toplam 2144→2243 dosya / 1915→2007 md / 395→406 dizin — doğrudan ölçüm, fark = 2026-10-16 arası .ai/ genelindeki diğer eklemeler dahil)** · kök .md 21→22 (`AGENT-BRIEFING.md` +1).
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
| 2 | Kök dosya sayımı | 22 `.md` (2026-10-15 16:09 disk ölçümü; 21 iddiası 2026-10-12 tabanı — `AGENT-BRIEFING.md` sonradan eklendi) |
| 3 | Dizin sayımı | 18 depth-1 dizin (2026-10-15 disk ölçümü; `.agents .decisions .obsidian .personas .png .rules .sql .templates architecture ecosystem electronic index projects reports scripts servers SESSIONS ui-design` — `prompts/` ve `.diagram/` YOK) |
| 4 | Recursive | 1974 dosya · 1745 md · 394 dizin (2026-10-15 16:09 disk ölçümü; eski "665·435·72" tabanı `architecture/` hariç ölçümldü, geçersiz) |
| 5 | ADR | 92 (80 kabul / 10 red / 2 taslak — `.decisions/index.md` v1.5.0 ile hizalı; 2026-10-15 disk ölçümü `ADR-*.md`=83 + `R-*.md`=10 + 6 yardımcı = 99 md) |
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
| Disk kanıtı | 2026-10-12 ölçümü | §3 · §7 tüm sayılar |

---

**Authority:** Bayram Ali / Vault Steward
**Version:** 30.0.0
**Last Updated:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
