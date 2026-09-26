---
title: "CoreMusic — ADR-042: Vault Restructuring (2026-08-03 orijinal yapı + 2026-09-26 yeniden yazım seferi)"
type: "architecture-decision"
category: "vault"
date: "2026-08-03"
updated: "2026-09-26"
version: "1.0.0"
status: "accepted"
authority: "SSOT — bu ADR, CoreMusic `.ai/` vault'unun yeniden yapılandırılması kararının tek kaydıdır (orijinal 2026-08-03 kararı + 2026-09-26 sefer kapsamı); çelişkide bu metin esastır"
kaynak: "Kullanıcı onaylı karar içeriği (a/b/c) + disk kanıtı taraması (2026-09-26: 17 klasör / 15 kök .md / 44 ADR dosyası / 1410 wiki-link / log.md 565 satır) + web araştırması (5 sorgu / 37 atıf / 37 benzersiz kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-042: Vault Restructuring

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam: a-c) · **Tarih:** 2026-08-03 (orijinal karar) · **Sefer güncellemesi:** 2026-09-26 · **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona, 18/2/0 KABUL) · **Tech Lead:** ✅ · **Arch Lead:** ⏳ (kullanıcı onayı ile bekliyor)
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-042-vault-restructuring-2026-08-03`
> **İlgili kararlar:** [[ADR-024-ecosystem-modular-docs]] (vault-merkezli modüler dokümantasyon + **eksik script şartı**: `session-save.mjs` / `vault-post-update.mjs`) · [[ADR-021-spa-router-immutable-contract]] (sözleşme/değişmezlik yaklaşımının ADR-042'deki frozen modelinden farklı örneği) · [[ADR-035-system-prompt-engineering]] (prompt/versioning zinciri) · [[ADR-040-database-authority]] (tek sahip + tek yazıcı mantığının vault'a uygulanması) · [[ADR-041-database-normalization-supplementary]] (aynı seferin şablon/debate format referansı) · [[../index.md]] (`:84` slug satırı)
> **Ad gerekçesi:** slug `ADR-042-vault-restructuring-2026-08-03` **diskteki gerçek index kaydından** alınmıştır (`[[../index.md]]:84`) — uydurulmadı.
> **Numara notu (Truth Mode):** genel kural "yeni ADR'ler 088+/090+" der; bu dosya **kullanıcı atamasıyla** 042 numarasına yazıldı, çünkü `[[../index.md]]:84`, `[[../../AGENTS.md]]` §14/§25.3, `[[../../index.md]]:310,516-517` ve `[[../../.templates/adr/adr-template.md]]:20` bu numarayı **çoktan kayıtlı** tutuyor — numara boş değil, **boşluk dolduruldu** (ADR-041'in numara gerekçesiyle aynı durum).
> **Frozen notu (2026-09-26 kararı):** frozen sistemi **KALDIRILDI** — ADR-001-037 dahil her vault dosyası okunabilir/yazılabilir; yerine **debate (3 tur / 20 persona) + Tech Lead kalite kapısı** konmuştur. Arch Lead onayı henüz ⏳ (kullanıcı onayı ile). Bu dosya frozen değildir, değiştirilebilir (revizyon = `log.md` append).

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-09-26 taraması)

Etiketler: **IMPLEMENTED** = diskte kanıtlanan · **PLANNED** = kararlaştırılmış, karşılığı henüz yok · **ÇELİŞKİ** = iki vault kaydı birbiriyle uyuşmuyor (hiçbiri yumuşatılmadı).

#### A) Vault klasör ağacı — IMPLEMENTED (disk)

| Kök öğe | İçerik (2026-09-26) | Etiket |
|---|---|---|
| `.ai/.agents/` · `.ai/.decisions/` · `.ai/.obsidian/` · `.ai/.personas/` · `.ai/.png/` · `.ai/.rules/` · `.ai/.sql/` · `.ai/.subdomains/` · `.ai/.templates/` | 9 gizli/doküman klasörü | IMPLEMENTED |
| `.ai/architecture/` · `archives/` · `ecosystem/` · `prompts/` · `reports/` · `scripts/` · `servers/` · `ui-design/` | 8 açık klasör (`architecture/` altında K0-K20 + `adr/` + `firmware/` + `scripts/`) | IMPLEMENTED |
| Kök `.md` (15) | `AGENTS.md` · `brain.md` · `broken-links-report.md` · `CLAUDE.md` · `engine.md` · `glossary.md` · `index.md` · `keys.md` · `log.md` · `MEMORY.md` · `PROJECTS.md` · `ROLE.md` · `ULTRA-THINKING.md` · `VISION.md` · `WORKFLOW.md` | IMPLEMENTED |
| `.ai/project-state.md` | **YOK** (`Test-Path=False`) — `[[../../CLAUDE.md]]` post-op senkron talimatı böyle bir dosyayı doğrular, diskte karşılığı 0 | **PLANNED / ⚠️ VERIFICATION REQUIRED** |
| `.ai/.templates/` | 12 alt klasör (`adr`, `agents`, `backend`, `documentation`, `frontend`, `hardware`, `infrastructure`, `other`, `personas`, `query`, `testing`, `ui-design`) + kök 4 dosya → **37 `.md`** (2026-09-26 recursive sayım); `[[../../index.md]]` §18 kaydı **36** (2026-09-24 ölçümü) | **ÇELİŞKİ (37 ↔ 36)** — fark, son sayaç sıfırlamada doğrulanacak |

#### B) Karar ağacı (`.ai/.decisions/`) — IMPLEMENTED (disk) + ÇELİŞKİ

| Kanıt | Değer | Etiket |
|---|---|---|
| `accepted/` ADR dosyası | **44** (`ADR-001` … `ADR-041` + `ADR-081` + `ADR-089` + `ADR-090`) + `CLAUDE.md` | IMPLEMENTED |
| `draft/` | **0 ADR** — yalnız `CLAUDE.md` | IMPLEMENTED |
| `rejected/` | **0 ADR dosyası** — `CLAUDE.md` + `index.md`; `index.md` tablosu **BOŞ** ama `total: 12` yazıyor | **ÇELİŞKİ (12 iddia ↔ 0 dosya)** |
| `[[../index.md]]` frontmatter | v**1.1.1**, **162 satır**, `total-accepted: 68` · `total-rejected: 12` · `total-frozen: 37` · `total-active: 31` · `total-draft: 0` | IMPLEMENTED (kayıt) |
| Aynı dosya §2 tablosu | **Frozen 36** ("ADR-001 ile ADR-036; ADR-037 debate sonrası, frozen YOK") | **ÇELİŞKİ ↔ frontmatter 37** |
| Aynı dosya §3 başlığı | "**Frozen ADR'ler (001-037)**" | **ÇELİŞKİ ↔ §2'nin 36'sı** |
| Aynı dosya §6 kategori tablosu | `TOPLAM Frozen **37** / Active 31 / 68` | **ÇELİŞKİ ↔ §2'nin 36'sı** |
| `[[../../index.md]]` (master) | v**28.3.0**, **756 satır**, `total_files: 587` · `total_adr: 80` · **`total_adr_disk: 0`** (gerçekte 44 dosya var) · `:612` "Toplam 80 ADR (Frozen: 37, Active: 31, Rejected: 12)" | **ÇELİŞKİ (`total_adr_disk: 0` bayat)** |
| Accepted iddiası ↔ disk | 68 kayıtlı ↔ **44 dosya** → **24 kayıt dosyasız** (043-079 aralığındaki satırlar `[[../brain.md]] ADR-0xx` düz metin) | **ÇELİŞKİ** |
| `[[../index.md]]:84` | `[[../CLAUDE.md]] ADR-042-vault-restructuring-2026-08-03` → hatalı referans (`../CLAUDE.md` = `.decisions/CLAUDE.md`, ADR'ye değil) | **HATA → §5.1 adım 3 düzeltmesi** |

> **Sayaç durumu dürüst özeti:** üç dosyada (`[[../index.md]]` 36/37, `[[../../index.md]]` 80/0, kategori tablosu 37) **beş ayrı sayı** dolaşımda. **Düzeltme = SON sayaç sıfırlamasında tek sefer** (§2.2-c-1) — bu ADR sayıları **düzeltmez, yalnız belgeler**.

#### C) log.md — IMPLEMENTED (append-only)

| Kanıt | Değer | Etiket |
|---|---|---|
| Satır / bayt (2026-09-26) | **565 satır · 114.601 bayt** · son bayt `0x0A` (`\n` ile bitiyor) | IMPLEMENTED |
| Mojibake / CJK | **6 mojibake · 12 CJK** — önceki bilinen durumla (6/12) **aynı** → yeni bozulma yok; `log.md`'de küçük mojibake örnekleri geçmiş satırlarda korunuyor (append-only) | IMPLEMENTED (statik) |
| Son kayıt | `ADR-041 debate 3/20 kaydedildi (18/2/0 KABUL) + Tech Lead ✅ + 3 şart` | IMPLEMENTED |

#### D) Scriptler — ADR-024 şartı KARŞILANMIYOR (PLANNED)

| Dosya | Durum | Kanıt |
|---|---|---|
| `.ai/scripts/vault-utf8-writer.mjs` | ✅ VAR (6.962 B) — tek yazma arayüzü (append/write/replace/verify/repair/scan) | disk |
| `.ai/scripts/vault-faz4-sweep.mjs` | ✅ VAR (22.435 B) | disk |
| `.ai/scripts/fix-mojibake.py` | ✅ VAR (9.273 B) | disk |
| `.ai/scripts/index.md` | ✅ VAR (2.720 B) | disk |
| **`.ai/scripts/session-save.mjs`** | ❌ **YOK** — `Test-Path=False` | **PLANNED (ADR-024 şartı)** |
| **`.ai/scripts/vault-post-update.mjs`** | ❌ **YOK** — `Test-Path=False` | **PLANNED (ADR-024 şartı)** |
| Tekrarlayan arıza | `log.md:390 · 404 · 414 · 438 · 459` — beş seansta aynı cümle: "POST-OP SYNC: `session-save.mjs` + `vault-post-update.mjs` Test-Path=False (YOK)" | IMPLEMENTED (arıza kaydı, `[[ADR-024-ecosystem-modular-docs]]` §"Tekrarlayan arıza") |

#### E) Hafıza dosyaları — IMPLEMENTED + PLANNED

| Dosya | Durum (2026-09-26) | Etiket |
|---|---|---|
| `[[../../brain.md]]` | v**26.1.2**, 49.996 B, updated 2026-09-24 — mimari karar özeti (MO/vault-updater türetmesi) | IMPLEMENTED |
| `[[../../keys.md]]` | v**28.3.2**, 40.090 B, updated 2026-09-24 — keyword haritası | IMPLEMENTED |
| `[[../../MEMORY.md]]` | v**25.1.1**, 52.599 B, updated 2026-09-24 — **manuel** session hafızası (`session-save.mjs` yok → otomatik kayıt imkânsız) | IMPLEMENTED (manuel) |
| `project-state.md` | **YOK** — `[[../../CLAUDE.md]]` post-op zinciri 3/3 dosyayı (log/MEMORY/project-state) şart koşuyor; 1'i hiç yok, 2'si manuel | **PLANNED + ⚠️ VERIFICATION REQUIRED** |

#### F) Wiki-link grafiği — IMPLEMENTED (2026-09-26 taraması, 3 adaylı çözümleyici)

| Kanıt | Değer | Etiket |
|---|---|---|
| ADR dosyası / link kullanan dosya | **44 / 44** (link içermeyen ADR **0**) | IMPLEMENTED |
| ADR içi toplam `[[...]]` | **1.410** | IMPLEMENTED |
| Çözülen / **kırık** | **1.409 / 1** → tek kırık: `decisions/accepted/ADR-022-database-hardened-security` (`.ai/` öneki yok; `.ai/decisions/` dizini diskte zaten **YOK** — gerçek yol `.ai/.decisions/`) | IMPLEMENTED (1 kırık) |
| Vault geneli (`.ai/**/*.md`) | **6.417** wiki-link | IMPLEMENTED |
| Önceden bilinen kırık raporu | `[[../../broken-links-report.md]]`: **34 kırık / 5 dosya** (2026-09-24) — ADR dışı kapsam: `.claude/CLAUDE.md` 27 + `assets.coremusic.net/AGENTS.md` 3 + `home.coremusic.net/CLAUDE.md` 2 + `assets.coremusic.net/CLAUDE.md` 1 + `assets.coremusic.net/Css copy/CLAUDE.md` 1; 20'si `decisions/`/`ADR-*` hedefi (o eski adla diskte yok) | IMPLEMENTED (rapor) |
| IMPLEMENTED/PLANNED ayrımı | ADR dosyalarında ayrım **disiplinli** (ör. `[[ADR-041-database-normalization-supplementary]]` §1.1); `[[../../index.md]]:390-391` "PLANNED (kod) / IMPLEMENTED (spec)" etiketli — **uydurma etiket yok**, yalnız sayaç çelişkileri var | IMPLEMENTED (disiplin) |

### §1.2 Sorun Tanımı (Problem)

1. **İki karar aynı dosyada toplanmalı:** vault'un 2026-08-03'teki orijinal yeniden yapısı (klasörler, index, brain, templates, scripts, frozen) ile 2026-09-26'daki **92 ADR'lik yeniden yazım seferi** (80 karar + 12 red) değişiklikleri tek bir meta-karar zincirinde değil; ikisi ayrı ayrı yazılacak olursa "vault'u kim, neye göre, hangi kalite kapısıyla yönetiyor" sorusu cevapsız kalır.
2. **Frozen sistemi seferi bloke ediyor:** 001-037 "dokunulmaz" kuralı, 92 ADR'nin **sıfırdan** yazımıyla (metin değiştirme değil, yeniden üretme) kavramsal olarak çelişiyor; kalite güvencesi frozen yerine **debate + Tech Lead** ile sağlanacağı kullanıcı tarafından onaylanmıştır — ama bu değişiklik hiçbir ADR'de yazılı değil.
3. **Sayaçlar birbirini tutmuyor:** 36 / 37 / 68 / 80 / 44 / 0 (`total_adr_disk`) — altı sayı, üç dosya; hangisinin bağlayıcı olduğu yazılmadığı için her güncelleme yeni çelişki üretiyor.
4. **Wiki-link hijyeni ikiye bölünmüş:** ADR içi 1.410 link neredeyse temiz (1 kırık), ama raporlanan 34 kırık + `[[../index.md]]:84` hatası + 24 dosyasız kayıt bekliyor; haritasız tamir, 6.417 linklik vaultta toptan kırılma riski doğurur.
5. **Post-op otomasyonu yok:** ADR-024'ün şart ettiği `session-save.mjs` + `vault-post-update.mjs` yazılmadı → her seans sonu **manuel**; `MEMORY.md` manuel, `project-state.md` hiç yok → senkron kırılgan ve tekrarlanan bir insan işi.
6. **Red listesi boş:** 12 red kararı iddia ediliyor, `rejected/` altında tek bir dosya yok → "neden reddedildi" sorusu hiçbir yerde gerekçeli cevaplanmıyor.

### §1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `[[../../../.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` (her ana iddia ≥2 çapraz kaynak; kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`).
> **Araç notu:** bu oturumda **5 `websearch` sorgusu** çalıştırıldı (2026-09-26); kaynaklar alan adı düzeyinde listelendi.

| Alan | Değer |
|------|-------|
| Web Search **Query** | `1)` AI agent knowledge vault architecture 2025 2026 persistent memory knowledge base agents · `2)` architecture decision records ADR lifecycle status management best practices 2025 · `3)` single source of truth SSOT documentation practices · `4)` Obsidian wiki links graph broken link detection maintenance automation 2025 · `5)` decision log practices engineering teams record decisions living documentation |
| Web Search **Konusu** | AI ajanı bilgi-vault'u mimarisi · ADR yaşam döngüsü/statü yönetimi · SSOT dokümantasyon · wiki-link grafiği hijyeni/kırık link bakımı · karar kaydı (decision log) pratiği |
| Web Search **Bağlam** | ADR-042 kapsamı: (a) 2026-08-03 orijinal vault yeniden yapısı, (b) 2026-09-26 yeniden yazım seferi (92 ADR, 7 bölüm şablon, 3 tur/20 persona debate, frozen'ın kaldırılması), (c) hedef yapı (sayaç sıfırlama, wiki-link tamir haritası, eksik scriptler). CoreMusic bağlamı: 6.417 wiki-link, 44 ADR dosyası, append-only log, manuel senkron |
| Web Search **Kısa Açıklama** | **(1)** Ajan bilgi vault'ları 2025-26'da "tamamı sakla, dar seç" (persist broadly, retrieve narrowly) ilkesine ve **sınıflandırılmış hafıza** (episodik/semantik/prosedürel + zaman damgası + güven skoru) etiketlerine kaydı; isimsiz, sınıflandırılmamış yığın büyüdükçe retrieval maliyeti artıyor. **(2)** ADR literatürü net: tek karar = tek ADR, açık **status/yaşam döngüsü** zorunlu (Initiating → Researching → Evaluating → Implementing → Maintaining → Sunsetting), eskimiş karar "superseded" olarak **işaretlenir ve arşivlenir, silinmez**; onay ekip işidir (AWS: 1-3 readout, çoğu karar iki yönlü kapı). **(3)** SSOT bir araç değil **durum**dur: her bileşenin tek otoriter kopyası + her yerde ondan yeniden kullanım; senkron bozulunca bakım maliyeti ve yanlış karar riski büyür (OWASP örneği: SSOT yoksa şey "sync'ten düşer"). **(4)** Wiki-link grafiği için olgunlaştı: kırık/etkin olmayan linkler **otomatik taranır** (çoklu-çözümü = yanlış pozitif avı), graf sağlığı ayrı bir rapor (`graph-health.md`) olarak üretilir, **onarım planı + insan onayı** ile uygulanır; kendi scriptini yazan kişi bile yanlış pozitif batağına uyar. **(5)** Decision log pratiği: karar + bağlam + değerlendirilen seçenekler + beklenen sonuç tek yapıda; **living document** — güncellenmeyen/eskiyen karar "superseded" işaretlenir; ama **her şeyi** kaydetmeyin: sinyal/gürültü bozulursa kimse okumaz (yalnız önemli kararlar). |
| Web Search **Uzun Açıklama** | **(1)** Fast.io ajan-bilgi-vault rehberi (merkezi depolama + retrieval katmanı), Mem0 "memory-first routing" (tekrar eden sorgularda hafızadan okuma), AWS "persistent memory for multi-agent AI systems with S3 Vectors" (bellek şeması: `memory_type: episodic\|semantic\|procedural`, `created_at`/`expires_at`, `confidence`, `source`), HuggingFace forum "persist broadly, retrieve narrowly" + "log what happened so the agent can recover, audit, or continue later", zylos.ai (2026-04) "cross-session learning / seçici yazma (MemRL)", LinkedIn knowledge-retention notu — altı kaynak aynı dili kuruyor: **hafıza etiketli, denetlenebilir ve zaman damgalı olmalı; audit logu şart**. **(2)** TechTarget "8 best practices for ADR" (net statü, tek odak), adr.github.io (ADR ekosistemi + JavaLand 2026 sunumu), AWS Architecture Blog "Master ADRs" (200+ ADR deneyimi: tek karar, 1-3 readout, ekip onayı), Microsoft Learn (ADR workload yaşam boyu sürdürülür), GitHub `architecture-decision-record` (5 aşamalı yaşam döngüsü: Initiating → … → Sunsetting), r/softwarearchitecture + StackOverflow (ADR'lerin repo-yanında markdown olarak tutulması) — **statü yoksa ADR sistem çalışmaz; eskimiş karar silinmez, işaretlenir.** **(3)** Wikipedia SSOT maddesi, Mulesoft (tek referans noktası; 900+ uygulama örneği), Docsie (kod notasyonundan üretilen tek kopya), Strapi (7 adım: hedef → envanter → şema → …), Paligo ("her bileşenin tek otoriter versiyonu + her yerde yeniden kullanım"), Profisee (envanter + yönetim), OWASP threat-dragon #1481 (SSOT yoksa bakım zorlaşır, şey senkron düşer) — **çoklu kopya = senkron borcu.** **(4)** Obsidian forum "find links without notes / broken links nasıl otomatik güncellenir" ve "Vault Inspector: broken links + orphan attachments", r/ObsidianMD "Find orphaned files and broken links" eklentileri, note.com "vault link automatic maintenance script … false positives swamp", GitHub `obsidian-broken-links-cleaner` (üçlü doğrulama = 3 yöntem), Medium "LLM Wiki 2" akışı (`wikilinks → backlinks → graph-health.md → LLM-assisted repair plan → **human editorial decision**`), It's FOSS (wikilink ↔ markdown link ikilisi) — **tarama otomatik, onarım insana bağlı; tek yönteme güvenmek yanlış pozitif üretir.** **(5)** Plane decision-log (kayıt: karar, bağlam, seçenekler, beklenen sonuç), monday.com (living document; eskimiş "superseded" işaretlenir; friksiyonu düşür), Tandem engineering decision log (ne zaman kaydedilir, relitigasyonu durdurur), Microsoft engineering playbook üzerinden Maxim Gorin (markdown tablo: Decision/Date/Alternatives/Reasoning/Link/Who), Platform Development Playbook (Decision Record = bağlam + araştırma + seçenekler + sonuç), `joelparkerhenderson/decision-record` (living document + teamwork advice), r/dataengineering (her şeyi kaydetmeyin — PR-story linki traceability için yeterli) — **hafif ama tutarlı; her şeyi yazan log okunmaz olur.** |
| Web Search **Paragraf Veri Uzun** | Beş sorgu tek hücmü veriyor: **büyüyen bilgi yığını, ancak etiket + statü + bağlantı disiplimiyle yönetilebilir** — CoreMusic tam bu noktada. Ajan-vault literatürü "sınıflandırılmış hafıza + audit" derken CoreMusic'in `log.md` append-only + `MEMORY.md` manuel yapısı bu şablona uyuyor ama otomasyonu (`session-save.mjs`) **yok** → hafıza insan eliyle taşınıyor (§1.1-D). ADR literatürü "açık statü + superseded + silinmez arşiv" derken CoreMusic 2026-09-26'da frozen'ı kaldırdı — literatürle **çelişmiyor** (frozen da bir statüydü; yerine debate/Tech Lead kalite kapısı + supersede zinciri kondu), ama **statü alanının kendisi** (`status: accepted`) artık tek koruyucu → statü disiplini ADR-042'ye bağlanır. Wiki-link literatürü "tarama otomatik, onarım onaylı, çoklu çözümleyici" derken CoreMusic'te 1 ADR-içi + 34 raporlanmış kırık + `index.md:84` hatası var → **tamir haritası** şart (§2.2-c-2). SSOT literatürü "tek kopya, her yerde ondan kullan" derken sayaçlar üç dosyaya dağılmış (36/37/68/80/44/0) → **tek sayaç sahibi + sonunda tek sıfırlama** (§2.2-c-1). Decision-log literatürü "hafif + living + superseded" derken 12 red kaydı boş → red ADR'leri de seferin 92'lik nesnesine girer. |
| Web Search **Sonucu** | **37 atıf / 37 benzersiz kaynak** (5 sorgu; alan adı düzeyinde). Çapraz doğrulama ≥2 bağıbsız kaynak: ajan-vault mimarisi (6), ADR yaşam döngüsü/statü (7), SSOT (7), wiki-link grafiği (7), decision log (7) + 3 tekrar eden referans (AWS, GitHub, Microsoft). Dış kaynakla doğrulanamayan tek konu = CoreMusic'in kendi disk envanteri → iç kanıt (2026-09-26 taraması) ile sabitlendi. |
| Web Search **Alınan Karar** | **(a)** orijinal 2026-08-03 yapı **korunur ve tamamlanır**: klasör ağacı + `index.md` + `brain.md` + templates + scripts literatürle uyumlu (sınıflandırılmış, etiketli hafıza) · **(b)** sefer değişiklikleri: 92 ADR sıfırdan yazım (80+12), 7 bölüm şablon + §1.3 9 alan, **3 tur / 20 persona debate zorunlu**, **Tech Lead onayı kalite kapısı**; **frozen kaldırıldı** (statü `accepted` + debate + Tech Lead ile korunur — ADR literatüründeki "açık statü + supersede" modeline geçiş) · **(c)** hedef yapı: **sayaç sıfırlama = sonda tek sefer**, **wiki-link tamir haritası** (tarama otomatik → onarım onaylı, 3 yöntem), **eksik scriptler** (`session-save.mjs` + `vault-post-update.mjs` = ADR-024 şartı) |
| Web Search **Sonuç** | Dış kaynaklar **üç karar bloğunu da destekliyor**; iki gerilim noktası debate'ye (§7.1) bırakıldı: (1) frozen kalkınca koruyucu yalnız statü + debate kaldı → **eski ADR'ler düzenlenebilir hâle geldi**, geçmiş koruması git + supersede'ye kayıyor (literatür: "silinmez, işaretlenir" — CoreMusic'te metin artık düzenlenebilir → **gerilim R5**); (2) "hafif tutun" ilkesi ile 92 ADR'lik seferin hacmi arasındaki gerilim → sayaç sıfırlaması + red dosyaları olmadan sinyal/gürültü bozulur (§5.1 adım 7-10). |

**Kaynak listesi (37 atıf / 37 benzersiz):**

1) fast.io — AI agent knowledge vault · 2) mem0.ai — knowledge base agents w/ persistent memory · 3) r/AI_Agents — multi-agent persistent memory · 4) AWS Storage Blog — persistent memory w/ S3 Vectors · 5) discuss.huggingface.co — memory design for long-running agents ("persist broadly, retrieve narrowly") · 6) zylos.ai — AI agent memory architectures (2026-04) · 7) LinkedIn — knowledge retention in agent architecture · 8) AWS re:Invent 2025 AIM284 — intelligent agent memory · 9) techtarget — 8 best practices for ADRs · 10) adr.github.io — ADR ecosystem · 11) AWS Architecture Blog — Master ADRs (200+ ADR) · 12) Microsoft Learn — maintain an ADR · 13) GitHub architecture-decision-record — 5 lifecycle stages · 14) r/softwarearchitecture — documenting ADRs · 15) StackOverflow — when to write an ADR · 16) Wikipedia — single source of truth · 17) Mulesoft — what is SSOT · 18) Docsie — SSOT best practices · 19) Strapi — how to build an SSOT · 20) Paligo — SSOT content reuse · 21) Profisee — create an SSOT · 22) GitHub OWASP/threat-dragon #1481 — SSOT vs sync drift · 23) Obsidian Forum — find links without notes / automate broken links · 24) Obsidian Forum — Vault Inspector (broken links + orphans) · 25) r/ObsidianMD — find orphaned files and broken links · 26) note.com (lazy_engineer) — vault link maintenance script + false positives · 27) GitHub sarwarkaiser/obsidian-broken-links-cleaner — triple-check detection · 28) Medium (Ken Moriwaki) — LLM Wiki 2: graph-health → repair plan → human decision · 29) It's FOSS — creating and working with links in Obsidian · 30) Plane — decision log: what it is + template · 31) monday.com — decision log system (2026) · 32) Tandem — engineering decision log best practices · 33) Maxim Gorin — documenting decisions (Microsoft playbook tablosu) · 34) Platform Development Playbook — decision records toolkit · 35) GitHub joelparkerhenderson/decision-record — living document · 36) r/dataengineering — decision log opinions · 37) projectmanagement.com — decision log vs changelog.

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| **In-Place Refactoring: dosya adları değişmez** | `.ai/` kök dosyalarının ve klasör adlarının onaysız yeniden adlandırılması yasak; slug `ADR-042-vault-restructuring-2026-08-03` ve `[[../index.md]]:84` metni sabit kalır (yalnız link düzeltmesi) |
| **`log.md` append-only** | 565 satırlık geçmişe dokunulmaz; bu ADR'nin kaydı da tek yeni satır (§5.1 adım 4) |
| **Sayaçlara elle müdahale yok** | 36/37/68/80/44/0 çelişkileri bu ADR'de **raporlanır**, düzeltilmez → düzeltme **son sayaç sıfırlamasında tek sefer** (kullanıcı onayı) |
| **Diskte olmayan hedefe wiki-link kurulmaz** | Yalnız diskte doğrulanmış hedefler `[[...]]` olur; `rejected/` 12 dosyası, `sessions/`, `registry/`, `knowledge/` vb. dizinler linklenmez (§6'da 33/33 doğrulandı) |
| **REDACTED** | `keys.md`/vault'taki hiçbir credential değeri bu ADR'ye (veya hiçbir vault dosyasına) yazılmaz |
| **Debate + Tech Lead kapıları** | Debate ⏳ ve Tech Lead ⏳ tamamlanmadan seferin kalite kapısı **açık sayılmaz**; Arch Lead ⏳ (kullanıcı onayı ile bekliyor) |
| **Eksik scriptler yazılmadan post-op otomasyonu iddia edilmez** | `session-save.mjs` / `vault-post-update.mjs` diskte yok → her senkron raporu **manuel + ⚠️ VERIFICATION REQUIRED** |

---

## §2 Karar (Decision)

**CoreMusic vault yönetimi üç blokta sabitlenir: (a) 2026-08-03 orijinal yeniden yapı (klasör ağacı, `index.md`, `brain.md`, templates, scripts, frozen sistemi) korunur ve tamamlanır; (b) 2026-09-26 yeniden yazım seferi — 92 ADR sıfırdan yazılır (80 karar + 12 red), 7 bölümlük şablon (§1.3 9 alan araştırmalı) zorunludur, 3 tur / 20 persona debate + Tech Lead onayı kalite kapısıdır, frozen sistemi KALDIRILMIŞTIR (001-037 dahil her dosya okunabilir/yazılabilir; koruyucu = debate + Tech Lead), yeni numaralar 081 ve 090'dır (082 ile 083-089 aralığı rezervedir); (c) hedef yapı — sayaç sıfırlama sonda TEK SEFER, wiki-link tamir haritası (ADR-içi 1 kırık + raporlanan 34 + `[[../index.md]]:84` + 24 dosyasız kayıt), eksik scriptler (`session-save.mjs` + `vault-post-update.mjs` = ADR-024 şartı).**

### §2.1 Neden Bu Seçenek?

1. **Kapsam boşluğu gerçek:** Vault'un hem "nasıl yapılandırıldı" (2026-08-03) hem "nasıl yeniden yazılıyor" (2026-09-26) hikâyesi hiçbir tek ADR'de yok; `[[../../AGENTS.md]]` §25.3 kural 1 "Yapı korunur (ADR-042)" diyor, `[[../../.templates/adr/adr-template.md]]:20` "ADR-042 hibrit kuralı" diyor — yani dosya **çoktan referanslanıyor**, içeriği boş.
2. **Literatürle uyumlu (§1.3):** sınıflandırılmış hafıza + audit (6 kaynak), açık statü + supersede + silinmez arşiv (7 kaynak), tek otoriter kopya (7 kaynak), otomatik tarama + onaylı onarım (7 kaynak), hafif ama living decision log (7 kaynak) — dördü de bu karar bloklarını destekliyor.
3. **Frozen yerine debate + Tech Lead:** frozen, kaliteyi **metne** bağlardı (değiştirilemez = iyi); sefer 92 metni sıfırdan ürettiği için koruyucu artık **süreçte** — 3 tur / 20 persona debate + Tech Lead onayı. Kullanıcı onayı ile Arch Lead'e kadar kapı açık.
4. **Sayaç sıfırlaması tek seferde:** ara düzeltmeler her seferinde yeni çelişki üretir (bugün 6 farklı sayı); tek seferde sıfırlama, git geçmişiyle denetlenebilir tek bir "tozlanma anı" bırakır.
5. **ADR-024 şartı bağlayıcı:** beş seansta aynı "Test-Path=False" arızası tekrarlamış (`log.md:390,404,414,438,459`) → script'ler ADR-042'nin hedef yapı şartı olarak yeniden yazılır; araç yokken post-op senkron **manuel ve kırılgan** (§1.1-E).

### §2.2 Teknik Detaylar

#### §2.2-a Karar (a) — 2026-08-03 orijinal yeniden yapı (IMPLEMENTED)

| Öğe | Karar | Kanıt (2026-09-26) | Etiket |
|---|---|---|---|
| Klasör ağacı | `.ai/` = tek SSOT merkezi: `.decisions/` (accepted/draft/rejected), `architecture/` (K0-K20), `.templates/` (12 alt klasör), `scripts/`, `.sql/` (4 motor), `reports/`, `ecosystem/`, `ui-design/`, `archives/`, `prompts/`, `servers/`, `.agents/`, `.personas/`, `.rules/`, `.subdomains/`, `.png/`, `.obsidian/` | 17 klasör + 15 kök `.md` | IMPLEMENTED |
| `index.md` | Master navigasyon (v28.3.0, 756 satır) — kategori bazlı katalog | disk | IMPLEMENTED |
| `brain.md` | Mimari karar özeti (v26.1.2) — ADR'lerden türetilir, ikincil kaynak | disk | IMPLEMENTED |
| Templates | `.templates/` + Guardrail #16 (şablonsuz dosya üretilmez); ADR şablonu 7 bölüm + §1.3 9 alan | 37 `.md` (index: 36 — §1.1-A çelişkisi) | IMPLEMENTED (çelişki işaretli) |
| Scripts | Tek yazma kanalı `vault-utf8-writer.mjs` (UTF-8, BOM'suz, yedekli) + `vault-faz4-sweep.mjs` + `fix-mojibake.py` | 4 dosya | IMPLEMENTED |
| Frozen sistemi (orijinal) | 001-037 dokunulmaz; kalite = metin değişmezliği | ÇALIŞTI, ama 92 ADR seferiyle kavramsal olarak çelişti | **SUPERSEDED → §2.2-b** |
| log.md | Append-only audit trail | 565 satır / 114.601 B / son bayt `\n` | IMPLEMENTED |
| MEMORY / project-state | `MEMORY.md` manuel (v25.1.1); `project-state.md` hiç üretilmedi | disk | IMPLEMENTED (manuel) / **YOK** |

#### §2.2-b Karar (b) — 2026-09-26 yeniden yazım seferi (kullanıcı onaylı)

| # | Değişiklik | Detay | Etiket |
|---|---|---|---|
| 1 | **92 ADR sıfırdan yazılıyor** | **80 karar (accepted) + 12 red (rejected)**; mevcut 44 dosyanın üzerine yazım/ekleme + eksiklerin üretilmesi. Bugün diskte **44/92** (IMPLEMENTED kısmi) | IMPLEMENTED (44) / PLANNED (48) |
| 2 | **7 bölüm şablon** | H1 + künye + §1 Bağlam → §7 Onay; **§1.3 9 alan araştırmalı** (Query, Konusu, Bağlam, Kısa, Uzun, Paragraf, Sonucu, Alınan Karar, Sonuç) — `[[../../.templates/adr/adr-template.md]]` v2.0.0 | IMPLEMENTED (şablon) |
| 3 | **3 tur / 20 persona debate zorunlu** | Sonuç `log.md`'ye + frontmatter `debate` alanına yazılır; örnek: ADR-041 = 3 tur / 20 persona / 18-2-0 KABUL | IMPLEMENTED (örnek var) / **ADR-042: ✅ TAMAMLANDI (3/20, 18-2-0 KABUL)** |
| 4 | **Tech Lead onayı** | Vault Steward ✅ → **Tech Lead ✅ (2026-09-26, §7)** → Arch Lead ⏳ (kullanıcı onayı ile) | IMPLEMENTED (akış) / ✅ (bu ADR) |
| 5 | **FROZEN SİSTEMİ KALDIRILDI** | **001-037 dahil her vault dosyası okunabilir/yazılabilir**; "dokunulmazlık" kalktı. Yerine gelen kalite kapısı: **debate (3/20) + Tech Lead onayı**. Koruma artık: `status` alanı + supersede zinciri + git geçmişi (§1.3 ADR literatürü ile aynı model) | **KARAR (kullanıcı onayı)** |
| 6 | **Yeni numaralar** | Seferin yeni ADR'leri: **`ADR-081-multi-provider-data-sync`** ve **`ADR-090-channel-variant-product-family`** (her ikisi de diskte, accepted). **082** ve **083-089 aralığı rezerve** (sefer için ayrılmış) | IMPLEMENTED (081, 090) / **REZERVE** |
| 7 | **Numara çelişkisi (dürüst kayıt)** | Rezerve listesi "083-089" derken diskte **`ADR-089-classab-24v` MEVCUT** (accepted, 2026-09-24 kabul) → 089'un rezervasyonu çiğnenmiş ya da rezerv listesi revize edilmeli | **⚠️ VERIFICATION REQUIRED → §7.1 madde 2** |
| 8 | **Red metodolojisi** | 12 red kararı gerekçesiyle yazılır (`rejected/` bugün boş); red-only indeks `rejected/index.md` `total: 12` iddiasını dosyalarla doldurur | PLANNED |

#### §2.2-c Karar (c) — Hedef yapı (PLANNED, sıralı)

| # | Hedef | Kapsam | Ne zaman |
|---|---|---|---|
| **1** | **Sayaç sıfırlama — sonda TEK SEFER** | Tek doğrulanmış sayım: `.decisions/index.md` frontmatter (`total-*`) + §2 (36↔37) + §6 (37) + `[[../../index.md]]` (`total_files`, `total_adr`, **`total_adr_disk: 44→gerçek`**, `:612` satırı) + `.templates` 36↔37. Ara düzeltme YASAK (her ara düzeltme yeni çelişki üretir) | 92 ADR yazımı bitince, **bir defalık** |
| **2** | **Wiki-link tamir haritası** | 4 kalem: (i) ADR-içi **1 kırık** (`decisions/accepted/ADR-022-...` → `.decisions/` gerçeği), (ii) raporlanan **34 kırık / 5 dosya** (`[[../../broken-links-report.md]]`), (iii) **`[[../index.md]]:84`** hatası (§5.1 adım 3), (iv) **24 dosyasız accepted kayıt** + `rejected/` 12 boş kayıt. Yöntem (§1.3): otomatik tarama (çoklu çözümleyici) → **onaylı onarım**, toptan link yeniden yazımı YASAK | Sayaç sıfırlamasıyla **aynı pencere** |
| **3** | **Eksik scriptler (ADR-024 şartı)** | `.ai/scripts/session-save.mjs` + `.ai/scripts/vault-post-update.mjs` yazılır; `Test-Path` kontrolü + `scripts/index.md` kaydı; ardından `MEMORY.md` otomasyonu ve `project-state.md` (hiç yok) devreye girer | Sıfırlamadan önce (post-op manuel sürükleme bitsin) |
| **4** | **Kalite kapısı prosedürü** | Her yeni/rewritten ADR: 7 bölüm + §1.3 9 alan → 3 tur / 20 persona debate → Vault Steward ✅ → **Tech Lead** → (Arch Lead ⏳, kullanıcı onayı ile). `status: accepted` bu kapıların **kaydıdır**, frozen'ın yerine geçer | Sürekli |
| **5** | **Red defteri** | 12 red ADR (`R-001`…`R-012` slug'ları `[[../index.md]]` §5'te zaten kayıtlı, `dead-link` işaretli) → dosya olarak `rejected/` altına yazılır | Seferin 92 nesnesi içinde |

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Frozen sistemini koru** (001-037 dokunulmaz + yeni ADR'ler 088+ ile devam) | Geçmiş metin garantisi, tanıdık kural | 92 ADR'lik sefer frozen'la **kavramsal olarak imkânsız** (yeni metin üretilecek); frozen kaliteyi **süreçten** değil **metinden** alır; eski 37 ADR'nin çelişkileri (index 36↔37) düzeltilemez | Kullanıcı onayıyla reddedildi; yerine debate + Tech Lead kapısı (§2.2-b/5). Literatür de statü+supersede modelini destekliyor (§1.3, 7 kaynak) |
| 2 | **Sayaçları hemen düzelt** (36/37/68/80/44/0 → bugünkü gerçek değerler) | Çelişki anında biter | 92 ADR yazımı sürerken sayılar **her gün değişir** → 6-8 hafta boyunca haftada birkaç kez çelişki; ara düzeltmeler git gürültüsü yaratır, denetim izi kaybolur | Tek seferlik **son** sıfırlama seçildi (§2.2-c-1); bugünkü değerler bu ADR'de dürüstçe donduruldu (§1.1-B) |
| 3 | **Wiki-link'leri toptan yeniden yaz** (6.417 linkin hepsini tek geçişte tamir) | Tek işlemde "sıfır kırık" görüntüsü | 6.417 linklik vaultta tek script geçişi = **yanlış pozitif batağı** (§1.3 kaynak 26: "kendi scriptini yazan bile false positives swamp'ine düşer"); In-Place Refactoring ruhuna aykırı; onaysız hedef değişimi | **Harita + fazlı, onaylı onarım** (§2.2-c-2): 4 kalem, öncelik sıralı, her fazda `log.md` append |
| 4 | **Script'leri yeni adla/alternatif isimle yaz** (ADR-024'teki adları kullanmadan) | Aceleyle araç devreye girer | `log.md:390-459` arızası `session-save.mjs` adını **beş kez** tekrarladı; farklı isim = ADR-024 şartı yine karşılanmaz, arıza kaydı yalan olur | ADR-024'teki **aynı adlarla** yazılacak (§2.2-c-3); ADR-024 birincil şarttır |
| 5 | **92 yerine mevcut 44 ADR'yi güncelleyerek sürdür** (sıfırdan yazım yerine revizyon) | Daha az iş, link'ler hiç kırılmaz | Karar metni değişikliği = **yeni ADR** kuralına (şablon §4/10) aykırı; 12 red + 24 dosyasız kayıt yine üretilmeli; "sefer" bütünlüğü kaybolur | Sıfırdan yazım (80+12) kullanıcı onaylı kapsam; mevcut 44 dosya yeniden yazım nesnesi, revizyon değil |

---

## §4 Sonuçlar (Consequences)

### §4.1 Olumlu Sonuçlar

- **Tek meta-karar:** vault'un "nasıl yapıldı" (a), "nasıl yeniden yazılıyor" (b), "nereye gidecek" (c) hikâyesi tek dosyada; `AGENTS.md` §25.3 ve `adr-template.md:20`'deki çıplak "ADR-042" atıfları artık içerikli.
- **Kalite kapısı süreçe taşındı:** debate (3/20) + Tech Lead, frozen'ın yerini alıyor → 92 ADR aynı kalite süzgecinden geçebiliyor; her ADR'nin `debate` + `onay` izi frontmatter'da.
- **Sayım drami sona eriyor:** altı çelişkili sayı (36/37/68/80/44/0) tek seferde dondurulup sıfırlanacak; bu ADR bugünkü değerleri **tarihsel kanıt** olarak sabitliyor (sonradan "ne değişti" sorusu git+ADR ile cevaplanır).
- **Link rotu sınırlı:** ADR-içi 1.410 linkin 1.409'u zaten çözülmüş durumda; harita 4 kalemi sırayla kapatınca 6.417 linklik vaultta toptan kırılma riski yok.
- **Senkron kırılganlığı azalıyor:** ADR-024 şartı yerine getirilince post-op 3 adımı (log / MEMORY / project-state) otomasyona bağlanır; beş seanstır tekrarlanan "Test-Path=False" arızası kapanır.

### §4.2 Olumsuz Sonuçlar

- **Geçmiş koruması zayıfladı:** frozen kalkınca 001-037 de düzenlenebilir → "eski karar böyleydi" güvencesi artık **git + supersede**'de; disiplin yoksa eski metin sessizce değişebilir (R5).
- **Tek koruyucu `status`:** `accepted` alanı ve debate izi yoksa ADR'nin kalitesi görünmez; debate ⏳ kalan dosyalar **yarım kapı** durumunda.
- **PLANNED yükü büyük:** 48 ADR yazılmayı, 12 red dosyası, 2 sayaç sıfırlaması, 2 script, 34+1+1 link tamiri bekliyor → sefer bitene kadar vault "yarı yenilenmiş" (44/92).
- **Manuel senkron sürüyor:** script'ler yazılana kadar her seans sonu elle; `project-state.md` hâlâ yok (post-op zinciri 3/3'ü doğrulayamıyor).
- **Numara rezervi çelişkisi:** 089 diskte mevcut ama "083-089 rezerve" — ya rezerv revize edilir ya 089 sefer dışı işaretlenir (§7.1).

### §4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **R1 Sayım drift'i — sıfırlamadan sonra sayaçlar yeniden dağılırsa** (ara düzeltme gelirse) | Yüksek (>%70) | Orta | **Ara düzeltme yasağı** yazılı (§2.2-c-1); sayımı yalnız son sıfırlama + bu ADR'nin §1.1 tabloları taşır; her sayım `log.md` append ile zaman damgalanır |
| **R2 Link rotu — tamir haritası uygulanmadan dosya/klasör taşıması** | Orta (%40-70) | Yüksek | Harita 4 kalemi **taşımadan önce** kapatılır; toptan link yeniden yazımı yasak (§3-alt.3); her fazda 3 yöntemli tarama (§1.3) + onay |
| **R3 SSOT çelişkisi — `index.md` ↔ `brain.md` ↔ `CLAUDE.md` sayaç/özet ayrışırsa** | Orta (%40-70) | Yüksek | Tek sayaç sahibi (`.decisions/index.md` frontmatter) + "çelişkide kök kazanır" kuralı (`AGENTS.md` §26.2 ruhu); brain özeti ADR'den **türetilir**, ikinci sayı tutmaz |
| **R4 Manuel sync kırılganlığı — 2 script yazılmazsa** | Yüksek (>%70) | Orta | ADR-024 şartı + bu ADR §2.2-c-3 ile bağlandı; yazılmadan önce her post-op **manuel + ⚠️ VERIFICATION REQUIRED** (yalan iddia yasak) |
| **R5 Frozen kalkınca eski ADR sessizce değişirse (geçmiş kaybı)** | Orta (%40-70) | Yüksek | Debate + Tech Lead kapısı her **revizyon** için de geçerli; revizyon = `log.md` append + `updated` alanı; karar değişikliği = **yeni ADR + supersede** (şablon §6.3); git diff denetimi |
| **R6 089 numara çakışması — rezerv listesi ile disk çelişirse** | Olası (%40-70) | Düşük | §7.1 madde 2 debate'de çözülünceye kadar **iki gerçek de yazılı** durur (disk: 089 var; rezerv: 083-089) — uydurma tekilleştirme yok |

### §4.4 Fallback (geri birleşim / geri dönüş)

1. **Debate bu ADR'yi (veya frozen kaldırmasını) reddederse:** yeni ADR "revert of ADR-042 §2.2-b/5" yazılır — frozen modeli geri gelir, 92 ADR'lik sefer **durur ve kapsamı yeniden yazılır**; bu metin düzenlenmez, yön `log.md` append ile işaretlenir.
2. **Tech Lead onayı uzarsa (⏳ çok kalırsa):** seferin yazımı sürebilir ama **hiçbir ADR `active`/kapanış sayılmaz**; kapı `AGENTS.md` §10.1 L2 timeout (60s) mantığıyla MO'ya devreder — hâlâ yoksa insana eskalasyon (L3 → İnsan).
3. **Sayaç sıfırlama başarısız olursa (yeni çelişki çıkarsa):** geri dönüş = `git checkout` ile `.decisions/index.md` + `index.md` frontmatter'inin sıfırlama öncesi hâli + bu ADR §1.1 tabloları referans gösterilerek ara düzeltmesiz yeni tarih seçilir (tek seferlik deneme tekrarlanabilir, ara adıma inilemez).
4. **Wiki-link haritası yanlış pozitif üretirse (40+ yanlış "kırık"):** onarım durdurulur; tarama 3 yöntemli moda (§1.3) alınır, `dead-link` işareti `<!-- dead-link: <slug> no source <tarih> -->` deseniyle konur (faz6-D deseni) — dosyalar **yazılmaz**, yalnız işaretlenir.
5. **Script'ler yazılmazsa:** post-op manuel akış (`[[../../WORKFLOW.md]]` + `[[../../CLAUDE.md]]` §"POST-OPERATION VAULT SYNC") korunur; her seans sonunda eksiklik satırı `log.md`'ye append edilir (bugüne kadar 5 kez edildi — 6.'sı **kayıt**, sürpriz değil).
6. **Tam geri dönüş:** bu ADR frozen değil → yeni ADR "revert of ADR-042" + `[[../index.md]]:84` satırı `—` ile işaretlenir (silinmez); append-only `log.md` nedeniyle geri dönüş de **yeni satır** olur.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | **Kanıt taraması:** vault ağacı (17 klasör / 15 kök .md), `.decisions/` (44 ADR + 0 red + 0 draft), sayaç çelişkileri (36/37/68/80/44/0), `log.md` (565 satır / 6 mojibake / 12 CJK), scripts (4 var / 2 YOK), wiki-link (1.410 / 1 kırık / vault 6.417) | Vault Steward | 3 dk | ✅ UYGULANDI (2026-09-26) |
| 2 | **Bu ADR'yi şablondan üret:** 7 bölüm + §1.3 9 alan dolu + frontmatter 7 zorunlu alan (Guardrail #16), slug diskten (`[[../index.md]]:84`) | Vault Steward | 3 dk | ✅ UYGULANDI (2026-09-26) |
| 3 | **`[[../index.md]]:84` düzeltmesi (RAPOR — uygulama onayla):** MEVCUT `| [[../CLAUDE.md]] ADR-042-vault-restructuring-2026-08-03 \| Vault Restructuring \| Vault |` → HEDEF `| [[accepted/ADR-042-vault-restructuring-2026-08-03]] \| Vault Restructuring \| Vault |` (satır 80/82/83'teki gerçek disk slug formatıyla aynı) | Vault Steward | 1 dk | 📋 **RAPOR EDİLDİ (bu ADR §1.1-B + dönüş raporu) — uygulama SAHİP ONAYIYLA** |
| 4 | **`.ai/log.md` append — 1 satır:** `ADR-042 yazıldı (debate PENDING)` (önceki son satır zaten `\n` ile bitiyor → bölme gerekmedi) | Vault Steward | 1 dk | ✅ UYGULANDI (2026-09-26) |
| 5 | **Debate:** §7.1'deki 5 madde üzerinden 3 tur / 20 persona → sonuç `log.md` append + frontmatter `debate` güncellemesi | Vault Steward + Tech Lead | 1 gün | ✅ UYGULANDI (2026-09-26 — 3/20, 18/2/0 KABUL) |
| 6 | **Tech Lead onayı** (Vault Steward ✅ sonrası) → `status` doğrulanır; Arch Lead kullanıcı onayıyla | Tech Lead → Arch Lead | 1 gün | ✅ Tech Lead (2026-09-26) · ⏳ Arch Lead (kullanıcı onayı ile) |
| 7 | **Sayaç sıfırlama — TEK SEFER (92 ADR bitince):** `.decisions/index.md` (frontmatter `total-*` + §2 36↔37 + §6) + `[[../../index.md]]` (`total_files/total_adr/total_adr_disk` + `:612`) + `.templates` 36↔37 → tek doğrulanmış sayı seti + `log.md` append | Vault Steward | 1 saat | ⏳ PLANNED (sonda) |
| 8 | **Wiki-link tamir haritası (4 kalem):** (i) ADR-içi 1 kırık → `.decisions/` gerçeği, (ii) 34 kırık raporu (5 dosya, 20 hedef `decisions/` yazım hatası), (iii) adım 3'teki `:84` satırı, (iv) 24 dosyasız accepted + 12 boş rejected kaydı → her kalem ayrı onay + `log.md` append | Vault Steward + MO | 2 saat | ⏳ PLANNED |
| 9 | **Eksik scriptler (ADR-024 şartı):** `.ai/scripts/session-save.mjs` + `.ai/scripts/vault-post-update.mjs` — ADR-024 adlarıyla, `scripts/index.md`'ye kayıt, `Test-Path` kanıtı; ardından `MEMORY.md` otomasyonu + `project-state.md` ilk üretimi | MO (vault-updater) + Vault Steward | 2 saat | ⏳ PLANNED |
| 10 | **92 ADR nesnesi:** kalan 48 accepted + **12 red dosyası** (`R-001`…`R-012`, `rejected/index.md` tablosu doldurulur) — her biri 7 bölüm + §1.3 + debate + Tech Lead | Vault Steward + domain agent'lar | 6 hafta | ⏳ PLANNED (44/92 tamam) |
| 11 | **Kalite kapısı devriyesi:** her ADR'de `debate` + §7 satırları kontrolü; frozen'sız koruma = statü + supersede + git diff denetimi | Tech Lead | sürekli | ⏳ PLANNED (adım 5-6 sonrası) |

### §5.2 Geri Dönüş Planı

1. **Adım 3 (index:84) geri alınabilir:** düzeltme tek satırdır; uygulandıktan sonra hata çıkarsa eski satır `git diff` ile geri yüklenir (satır adı/slug değişmediği için wiki-link ağı etkilenmez).
2. **Adım 7 (sayaç sıfırlama) geri alınabilir:** sıfırlama öncesi `.decisions/index.md` + `index.md` yedeği (`vault-utf8-writer.mjs` write modu ilk yedeği otomatik alır: `C:/temp/opencode/vault-backups/`) → `git checkout` / `.bak` ile dönüş; sayılar eski çelişkili hâline döner ama **veri kaybı olmaz** (bu ADR §1.1, eski değerlerin arşividir).
3. **Adım 8 (link tamiri) geri alınabilir:** her kalem ayrı commit/append ile uygulanır → tek tek revert edilir; toptan değişiklik yapılmadığı için blast radius 1 satır.
4. **Adım 9 (script'ler) geri alınabilir:** yeni dosyalar silinir, `scripts/index.md` kaydı append ile geri alınır; manuel post-op akış (`WORKFLOW.md` + `CLAUDE.md`) hiç kesilmediği için hizmet sürekliliği bozulmaz.
5. **Karar düzeyi tam dönüş:** debate/Tech Lead bu ADR'yi veya frozen kaldırmasını reddederse → yeni ADR "revert of ADR-042" yazılır (bu metin **değiştirilmez**), frozen modeli geri gelir, sefer kapsamı yeniden yazılır; `log.md` append-only olduğu için geri dönüş de **yeni satır** olarak görünür.

### §5.3 Debate Şartları (bağlayıcı — 2026-09-26, 3 tur / 20 persona)

> Debate (§7.1) 18/2/0 KABUL ile sonuçlanmıştır; kabul **3 şartla** bağlanmıştır. Şartlar bu ADR'nin bağlayıcı parçasıdır — §5.1 adımları bu şartlar **olmadan** kapanmaz.

| # | Şart | Kapsam | Bağlantı | Durum |
|---|------|--------|----------|-------|
| **1a** | **Kırık link tamiri (ilk madde)** | Tek ADR-içi kırık: `decisions/accepted/ADR-022-database-hardened-security` (`.ai/` öneki yok, `.ai/decisions/` dizini diskte YOK → gerçek yol `.ai/.decisions/`) — **tamir haritasının ilk kalemi** | §2.2-c-2(i), §5.1 adım 8 | ⏳ PLANNED |
| **1b** | **Sayaç SSOT** | 6 çelişkili sayı (36/37/68/80/44/0) + `total_adr_disk: 0` → **tek sayım kaynağı** (`.decisions/index.md` frontmatter) + **son sıfırlama kuralı** (ara düzeltme yasak) | §2.2-c-1, §5.1 adım 7, §4.3-R3 | ⏳ PLANNED (sonda tek sefer) |
| **2** | **Rejected kapanışı** | `rejected/` 0 dosya ↔ `total: 12` → `R-001`…`R-012` dosya yazımı ile kapanır | §2.2-c-5, §2.2-b/8, §5.1 adım 10 | ⏳ PLANNED |
| **3** | **Eksik script — ADR-024 bağı** | `session-save.mjs` + `vault-post-update.mjs` ADR-024 şartına bağlanır; yazılmadan önce neden manuel sync olduğu kayıtlıdır (`log.md:390,404,414,438,459` — 5 tekrar) | §2.2-c-3, §5.1 adım 9, [[ADR-024-ecosystem-modular-docs]] | ⏳ PLANNED |

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| `[[../../CLAUDE.md]]` | Ana sözleşme — 16 Hard Guardrail, post-op senkron zinciri (script yokluğu burada raporlanır) |
| `[[../../AGENTS.md]]` | Agent registry SSOT — §14 "Mandatory 5 Skills (**ADR-042**/C4)", §25.3 kural 1 "Yapı korunur (ADR-042)", §25.3 kural 2 frozen kuralı (**bu ADR ile güncellendi**) |
| `[[../index.md]]` | Karar dizini — `:84` slug satırı (düzeltme raporu §5.1 adım 3), sayaç çelişkileri §1.1-B |
| `[[../../index.md]]` | Master katalog — `total_*` sayaçları + `:612` durum satırı (sıfırlama hedefi §2.2-c-1) |
| `[[../../brain.md]]` | Mimari karar özeti — bu ADR'nin özeti MO/vault-updater tarafından türetilecek (PLANNED) |
| `[[../../keys.md]]` | Keyword haritası — `vault, documentation, ADR, wiki-link` routing anahtarları |
| `[[../../log.md]]` | Append-only audit trail — bu ADR kaydı + script arıza tekrarları (`:390,404,414,438,459`) |
| `[[../../MEMORY.md]]` | Session hafızası — manuel (script yok → PLANNED otomasyon §2.2-c-3) |
| `[[../../WORKFLOW.md]]` | Süreçler — post-op vault sync akışı (manuel fallback §4.4-5) |
| `[[../../broken-links-report.md]]` | Kırık link raporu — 34 kırık / 5 dosya (harita kalemi ii, §2.2-c-2) |
| `[[../../scripts/index]]` | Script envanteri — 4 var / 2 YOK kaydı (adım 9'da güncellenecek) |
| `[[../../.templates/adr/adr-template]]` | Bu ADR'nin şablonu (v2.0.0, 7 bölüm + §1.3 9 alan, Guardrail #16) |
| `[[../../.templates/index]]` | Envanter SRP — template registry (36↔37 çelişkisi sıfırlama hedefi) |
| `[[ADR-024-ecosystem-modular-docs]]` | Vault-merkezli modüler dokümantasyon + **eksik script şartı** (bu ADR §2.2-c-3'ün birincil kaynağı) |
| `[[ADR-021-spa-router-immutable-contract]]` | Sözleşme/değişmezlik örneği — frozen yerine sözleşme+supersede yaklaşımıyla akraba |
| `[[ADR-035-system-prompt-engineering]]` | Prompt/versioning zinciri — seferin 7 bölüm şablon disiplinine komşu |
| `[[ADR-040-database-authority]]` | Tek sahip + tek yazıcı mantığı — vault sayaçlarına uygulanan aynı prensip (§4.3-R3) |
| `[[ADR-041-database-normalization-supplementary]]` | Aynı seferin format referansı (debate 3/20, §1.3 9 alan, IMPLEMENTED/PLANNED disiplini) |
| `[[../../../.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` | §1.3 web araştırma protokolü (≥2 çapraz kaynak) |

**Link denetimi:** §6 + satır içi wiki-link hedefleri 2026-09-26 taramasında **diskte doğrulandı** (3 adaylı çözümleyici: dosya-yöreli + `.ai/` + repo kökü; `.md`/uzantısız/.sql varyantları) — diskte olmayan hedef linklenmedi.

**Debate şartları (§5.3 — 3 şart, bağlayıcı):** (1) **kırık link + sayaç SSOT** — `ADR-022-database-hardened-security` kırık linki tamir haritası ilk maddesi (1a) + 6 çelişkili sayının tek sayım kaynağına bağlanması ve son sıfırlama kuralı (1b); (2) **rejected kapanışı** — `R-001`…`R-012` yazımıyla `rejected/` 0↔`total: 12` çelişkisinin kapatılması; (3) **eksik script ADR-024 bağı** — `session-save.mjs` + `vault-post-update.mjs` şartının [[ADR-024-ecosystem-modular-docs]]'a bağlanması (yazılmadan önce manuel sync gerekçesi `log.md:390-459` kayıtlıdır).

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Vault Steward (kullanıcı onaylı kapsam a-c) | 2026-09-26 | ✅ |
| Tech Lead | Tech Lead (3 tur / 20 persona debate — §7.1) | 2026-09-26 | ✅ |
| Arch Lead | ⏳ (kullanıcı onayı ile bekliyor) | — | ⏳ |

### §7.1 Debate Maddeleri (✅ TAMAMLANDI — 3 tur / 20 persona, 18/2/0 KABUL)

| # | Madde | Kaynak | Beklenen karar |
|---|-------|--------|----------------|
| 1 | **Frozen kaldırma kalıcı mı?** Eski ADR'lerin (001-037) metin koruması artık git + supersede'de; debate, statü+kapı modelini onaylıyor mu, yoksa `updated` alanı + log append zorunluluğu gibi ek bir kilit mi gerekiyor? | §2.2-b/5, §4.3-R5 | Onay / ek şart |
| 2 | **089 numara çelişkisi:** rezerv "083-089" ↔ diskte `ADR-089-classab-24v` mevcut — hangisi geçerli? | §1.1-B, §2.2-b/7 | Rezerv revizesi ya da 089'un sefer dışı ilanı |
| 3 | **Sayaç sıfırlama zamanı:** 92 ADR bitiminde tek sefer mi, yoksa 48/92 gibi kilometre taşlarında mı (ara düzeltme yasağı kalkar mı)? | §2.2-c-1, §4.3-R1 | Tek sefer (öneri) / kilometre taşı |
| 4 | **24 dosyasız accepted kaydın akıbeti:** beyazliste (dosyalar üretilsin) mi, dead-link mi (`[[../index.md]]` §4A/§5'teki `dead-link` deseni gibi)? | §1.1-B, §2.2-c-2(iv) | Üretim (öneri: 92 nesnesi) / dead-link |
| 5 | **Red dosyaları 92'ye dâhil mü?** 12 red ADR'nin `rejected/` altına yazımı seferin parçası mı (yoksa yalnız indeks satırları yeterli mi)? | §2.2-b/8, §4.2 | Dâhil (öneri) |

#### §7.1.1 Tur 1 — Bulgu turu (20 persona)

**Katılım:** 20 persona · **Dağılım:** 15 kabul/neutral · 4 uyarı · **Kaynak:** 37 (§1.3).

**Bulgu listesi:**

1. **44 ADR diskte** (`ADR-001`…`ADR-041` + `ADR-081` + `ADR-089` + `ADR-090`).
2. **Wiki-link:** vault geneli **6.417** / ADR korpusu **1.410** (1.409 çözüldü + **1 kırık**: `ADR-022-database-hardened-security`).
3. **Sayaç çelişkileri — 6 sayı:** 36 / 37 / 68 / 80 / 44 / 0.
4. **`rejected/` 0 ↔ `total: 12`** ve **`total_adr_disk: 0`**.
5. **Eksik scriptler:** `session-save.mjs` + `vault-post-update.mjs` YOK; `project-state.md` YOK.
6. **37 kaynak** (§1.3) doğrulandı; **R5 gerilimi** (frozen'sız koruma = statü + debate); **92 ADR ↔ hafif tutun gerilimi**.

**Uyarılar (4):**

| Persona | Uyarı |
|---|---|
| QA | Sayaç SSOT şart |
| DevOps | 1 kırık link şart |
| Critic | Sayaç SSOT şart |
| Critic | Eksik script şart |

#### §7.1.2 Tur 2 — İtiraz → çözüm

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | 1 kırık link (`ADR-022-database-hardened-security`) | Tamir haritasının **ilk maddesi** | **Şart 1a** |
| 2 | Sayaç 6 çelişki + `total_adr_disk: 0` | **Tek sayım kaynağı** + **son sıfırlama kuralı** (ara düzeltme yasak) | **Şart 1b** |
| 3 | `rejected/` 0 ↔ `total: 12` | **`R-001`…`R-012` yazımı** ile kapanış | **Şart 2** |
| 4 | Eksik scriptler | **ADR-024 şartına bağlama** (neden manuel sync yazılı) | **Şart 3** |

> Şartların tam metni ve bağlantıları: **§5.3** (bağlayıcı) + §6 son satır.

#### §7.1.3 Tur 3 — Oy

| Seçenek | Oy |
|---|---|
| **KABUL** | **18** |
| Çekimser | 2 |
| Red | 0 |

**Sonuç: KABUL (18/2/0) — 3 şartla** (§5.3: 1a-1b kırık link + sayaç SSOT · 2 rejected kapanışı · 3 eksik script ADR-024 bağı). Tech Lead 2026-09-26 ✅.

---

**REFACTOR REPORT:** FILE: ADR-042-vault-restructuring-2026-08-03.md · PURPOSE: Vault yeniden yapılandırma meta-kararı (2026-08-03 orijinal yapı + 2026-09-26 sefer + hedef yapı; frozen kaldırma + debate/Tech Lead kapısı) · VALIDATION: 7 bölüm + §1.3 9 alan dolu (5 sorgu / 37 atıf), disk kanıtı 2026-09-26 (17 klasör · 44 ADR · 1.410 link / 1 kırık · log 565 satır / 6 mojibake / 12 CJK · script 4 var / 2 YOK), IMPLEMENTED/PLANNED etiketli, placeholder 0, slug diskten (`[[../index.md]]:84`) · RELATED: [[../index.md]] · [[../../AGENTS.md]] · [[ADR-024-ecosystem-modular-docs]] · [[ADR-041-database-normalization-supplementary]] · [[../../broken-links-report.md]]

---

## §7 Referanslar (şablon §7 başlığı korunur — onay tablosu üstteki §7'dedir)

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Şablon kaydı | `[[../../.templates/index]]` | Guardrail #16 envanteri (SRP) |
| Karar dizini | `[[../index.md]]` | Slug `:84` + sayaçlar |
| Vault ana sözleşmesi | `[[../../CLAUDE.md]]` | Guardrail'ler + post-op zinciri |
| Agent registry | `[[../../AGENTS.md]]` | ADR-042 atıfları (§14, §25.3) |
| Script envanteri | `[[../../scripts/index]]` | 4 var / 2 YOK (ADR-024 şartı) |
| Kırık link raporu | `[[../../broken-links-report.md]]` | 34 kırık / 5 dosya (harita ii) |
| Web araştırma protokolü | `[[../../../.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` | §1.3 ≥2 çapraz kaynak |

---

*ADR-042 v1.0.0 — 2026-08-03 (orijinal) · 2026-09-26 (sefer kapsamı + yazım)*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Human Mode · Truth Mode*
*Debate: ✅ TAMAMLANDI (3/20, 18/2/0 KABUL) · Tech Lead: ✅ · Arch Lead: ⏳ · Frozen: YOK (kaldırıldı)*
