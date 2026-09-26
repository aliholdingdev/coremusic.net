---
title: "CoreMusic — ADR-024: Ecosystem Modular Docs (Vault-Merkezli Modüler Dokümantasyon · Tek Sahip + Tek SSOT · Şablon + Otomatik Denetim · session-save.mjs + vault-post-update.mjs)"
type: adr
category: documentation
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-024 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-024: Ecosystem Modular Docs (Vault-Merkezli Modüler Dokümantasyon · Tek Sahip + Tek SSOT · Şablon + Otomatik Denetim · session-save.mjs + vault-post-update.mjs)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-024'ü sıfırdan yaz"; karar içeriği kullanıcı onaylı: **(a) vault-merkezli + modül sahipliği** (`.ai/` = tek SSOT merkezi; kök `CLAUDE.md`/`WORKFLOW.md` = yönlendirici + kural; domain klasörleri = kısa kendi `CLAUDE.md'leri` merkeze link; `.agents/` = rol; `.templates/` = şablon; **her dokümanın tek sahibi + tek SSOT**) · **(b) senkron — şablon + otomatik denetim** (link denetimi, şablon uyumu, stale tespit; UTF-8 yazım zorunlu) · **(c) eksik scriptler bu kararın parçası: `session-save.mjs` + `vault-post-update.mjs` yazılır** · **(d) debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL — 3 bağlayıcı şart §5.5) · Tech Lead: ✅**)
**İlgili ADR'ler:** [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı — bu ADR'deki her vault iddiası dosya + sayaç ile etiketli; dosya diskte VAR ✅) · [[ADR-014-multi-db-migration-strategy]] (tek doğruluk kaynağı düzeltme ruhu — "aynı bilgi iki yerde olmaz"; dosya diskte VAR ✅) · [[ADR-015-env-parser-strategy]] (**stale/çelişki örneği:** `.agents/data-engineer.md` ADR-015'i yanlış etiketler, karar metni env parser'dır — §1.1-E; dosya diskte VAR ✅) · [[ADR-017-dsp-hardware-mode]] (**stale örneği:** `:208` ".github/workflows/ = 0" → bugün 2 dosya — §1.1-E; dosya diskte VAR ✅) · [[ADR-023-persona-driven-testing]] (denetim/doküman testi disiplini — bu ADR'nin otomatik denetimi ADR-023 coverage gate'i ile aynı CI kapısı görebilir; dosya diskte VAR ✅) · karar dizini [[../index]] **satır 61** `[[ADR-024-ecosystem-modular-docs]]` (slug eşleşmesi ✅).

> **Numara notu:** "Yeni ADR ≥ 088" kuralı bu yazımda uygulanmaz — `ADR-024-ecosystem-modular-docs` karar dizini `../index.md:61`'de **rezerve boş slottur** (ADR-019/020/021/022/023 aynı istisnayı kaydetmişti). Dikkat: `.ai/architecture/adr/ADR-024-surucu-firmware-birlesme.md` **farklı bir numaralandırma serisidir** (mimari katman ADR'leri) — dosya adı çakışması kozmik değil, iki ayrı dizin; §4.3/4'e alındı.

---

## 1. Bağlam (Context)

CoreMusic dokümantasyonu **büyük ama sahipsiz**: 178 `CLAUDE.md`, 7 `AGENTS.md`, 2 `WORKFLOW.md`, 36 şablon, 25 kabul edilmiş ADR, 24 mimari katman dizini ve ~binlerce satır vault markdown'ı diskte — ama **her bilginin hangi dosyada tek başına yaşadığı, kimin sahip olduğu ve ne sıklıkla denetlendiği yazılı değil**. Post-op senkronizasyon komutları (`session-save.mjs`, `vault-post-update.mjs`) her seans `log.md`'ye "ÇALIŞTIRILAMADI" diye düşüyor (§1.1-C), kırık wiki-link raporu **34 link / 5 dosya** açıkça duruyor (§1.1-D), şablon-hiya uyuşmazlığı ve eski iddia tespiti elle yapılıyor. Bu ADR **kod üretmez** — doküman mimarisini sözleşmeye çevirir: **vault-merkezli modüler yerleşim + tek sahip/tek SSOT kuralı + şablondan yazım + üç kollu otomatik denetim** ve bu denetimin çalışması için gereken **iki eksik script'in bu karar kapsamında yazılmasını** bağlayıcı kılar.

### 1.1 Mevcut Durum

**Vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + sayaç ile):**

**A) PROJE KÖKÜ — IMPLEMENTED (yönlendirici katman):**

- `CLAUDE.md` **VAR** (3.139 bayt), `WORKFLOW.md` **VAR** (28.590 bayt), `README.md` **VAR**, `package.json` **VAR** → **kök `AGENTS.md` YOK** (`Test-Path = False`; agent registry `.ai/AGENTS.md`'de, v22.0.3) → kök katman "yönlendirici" iddiası **kısmen** doğru: kural dosyaları var, agent dosyası yok.
- Kökün yanındaki üç klasör-öncesi katman: `.claude/` (4 öğe), `.opencode/` (8), `.workflows/` (9) → süreç/skill katmanı ayrı dizinlerde (kapsam dışı, yalnız sınır gösterimi).

**B) `.ai/` VAULT YAPISI — IMPLEMENTED (sayaçlar 2026-09-25 taraması):**

| İddia (görev listesi) | Durum | Kanıt (dosya/sayaç) |
|---|---|---|
| `.ai/brain.md` | ✅ VAR | kök `.md` envanterinde |
| `.ai/log.md` | ✅ VAR | 94.119 bayt (bu ADR append'inden sonra — append-only audit) (append-only audit) |
| `.ai/glossary.md` | ✅ VAR | kök `.md` envanterinde |
| `.ai/index.md` | ✅ VAR | master katalog |
| `.ai/keys.md` | ✅ VAR | keyword haritası |
| `.ai/MEMORY.md` | ✅ VAR | session hafızası |
| `.ai/CLAUDE.md` | ✅ VAR | ana sözleşme (16 Guardrail) |
| `.ai/AGENTS.md` | ✅ VAR | agent registry SSOT (v22.0.3) |
| `.ai/WORKFLOW.md` | ✅ VAR | 33.040 bayt süreç dosyası |
| `.ai/.templates/` | ✅ VAR | **36 dosya / 11 alt klasör**; registry `[[../../.templates/index]]` `total_templates: 36`, "36/36 dosya diskte" |
| `.ai/.decisions/` | ✅ VAR | `index.md` (9.488 B) + `CLAUDE.md`; `accepted/` **25 ADR dosyası** + `CLAUDE.md`; `draft/CLAUDE.md`; `rejected/` (`CLAUDE.md` + `index.md`) |
| `.ai/.agents/` | ✅ VAR | **12 dosya** (`AGENTS.md` + 11 agent profili) |
| `.ai/.sql/` | ✅ VAR | 4 alt dizin (mssql · mysql · postgresql · sqlite) + `CLAUDE.md` |
| `.ai/scripts/` (noktasız — görevdeki `.scripts/` YOK) | ✅ VAR `scripts/` | `.ai/.scripts/` → **YOK**; gerçek yol `.ai/scripts/` |
| `.ai/architecture/` | ✅ VAR | **24 alt dizin** = `k0-isletim-sistemi` … `k20-bom` (21 katman) + `adr/` + `firmware/` + `scripts/` → görevdeki "k0-k5" **eksik telaffuz**: k6-k20 de diskte |
| `.ai/security/` | ❌ YOK | `.ai/` altında `security` adlı dizin **0** → güvenlik dokümanı `architecture/k6-guvenlik/` altında (15 dosya + `CLAUDE.md`) |
| `.ai/reports/` | ✅ VAR | **9 dosya** (broken-files-report, faz6-link-ledger, auth-bypass-audit, …) |
| `.ai/` kök `.md` envanteri | ✅ 15 dosya | `AGENTS.md · brain.md · broken-links-report.md · CLAUDE.md · engine.md · glossary.md · index.md · keys.md · log.md · MEMORY.md · PROJECTS.md · ROLE.md · ULTRA-THINKING.md · VISION.md · WORKFLOW.md` |
| `.ai/` alt toplam `CLAUDE.md` | ✅ **34** | repo geneli `CLAUDE.md` = **178** (diğerleri domain/kod klasörlerinde) |

**C) EKSİK SCRIPTLER — PLANNED (bu ADR'nin bağlayıcısı):**

| Script | Durum | Kanıt |
|---|---|---|
| `.ai/scripts/vault-utf8-writer.mjs` | ✅ IMPLEMENTED | 6.962 bayt — yazma tek arayüzü (append/write/insert/verify/repair/scan) |
| `.ai/scripts/vault-faz4-sweep.mjs` | ✅ IMPLEMENTED | 22.435 bayt — toplam vault tarama |
| `.ai/scripts/fix-mojibake.py` | ✅ IMPLEMENTED | 9.273 bayt — mojibake onarımı |
| `.ai/scripts/index.md` | ✅ IMPLEMENTED | 2.720 bayt — envanter ("Toplam: 3 dosya" — kendini saymıyor; disk = 4 dosya) |
| **`.ai/scripts/session-save.mjs`** | ❌ **YOK (PLANNED)** | tüm repo recursive arama = **0**; `scripts/index.md` "?? YOK kaydı" |
| **`.ai/scripts/vault-post-update.mjs`** | ❌ **YOK (PLANNED)** | tüm repo recursive arama = **0**; `scripts/index.md` "?? YOK kaydı" |
| `.ai/scripts/project-state.md` | ❌ YOK | kökte de yok (`log.md:390`) — bu ADR kapsamı **dışı, işaretli** |

**Tekrarlayan arıza kaydı:** `log.md:390 · :404 · :414 · :438 · :459` — beş ayrı seansta aynı cümle: "POST-OP SYNC: `session-save.mjs` + `vault-post-update.mjs` Test-Path=False (YOK) → zorunlu post-op sync **ÇALIŞTIRILAMADI**; senkronizasyon manuel". Yani ⚠️ işareti her oturumda yeniden üretiliyor; **sorun sistemde, çözüm bu ADR.**

**D) LİNK / DOSYA DENETİM RAPORLARI — IMPLEMENTED (salt rapor, düzeltme yapmaz):**

| Rapor | Tarih | Bulgu |
|---|---|---|
| `[[../../broken-links-report]]` (10.550 bayt) | 2026-09-24 | **34 kırık wiki-link / 5 dosya** (`.claude/CLAUDE.md` 27 · `assets.coremusic.net/AGENTS.md` 3 · `home.coremusic.net/CLAUDE.md` 2 · `assets.coremusic.net/CLAUDE.md` 1 · `assets.coremusic.net/Css copy/CLAUDE.md` 1); en büyük başlık: **ADR arşivi hedefi 20 link** (`[[decisions/accepted/ADR-001-…]]` biçimi `.ai/.decisions/` yolunu göstermiyor) |
| `[[../../reports/broken-files-report]]` (11.268 bayt) | 2026-09-23 | **39 bulgu** (3 CRITICAL · 7 HIGH · 12 MEDIUM · 17 LOW) — kod+karma raporu (namespace mismatch vb.), doküman linkinden ayrı katman |

→ Her iki rapor da **vardır ve günceldir**; ikisi de "otomatik denetim" değil, **manuel tarama çıktısı** (link raporu: "Düzeltme yapmaz — salt rapordur").

**E) STALE / ÇELİŞKİ ÖRNEKLERİ — IMPLEMENTED (otomatik stale tespiti bugün YOK):**

- **ADR-017 `:208`:** ".github/workflows/ = 0 dosya yok" → **artık 2 dosya** (`ci.yml`, `secret-scan.yml`, 2026-09-24) — düzeltmeyi ADR-023 `:46` elle yaptı.
- **ADR-015 `:42`:** `.ai/.agents/data-engineer.md` ADR-015'i "cache stratejisi/migration aracı" diye etiketler; `index.md:52`, `brain.md:970`, `keys.md:250` "env parser" der → **karar metni kazandı**, agent profili **stale kaldı** (düzeltmesi §5.1'e yazıldı).
- **ADR-003 slug düzeltmesi:** `keys.md`/`index.md` eski slug `ADR-003-multi-db-9-databases` → `ADR-003-multi-db-bcnf` (`log.md` 2026-09-25, elle).
- **Şablon envanteri:** `.templates/index.md` sayacı 26 → 28 → 32 → 36 olarak elle tazelendi (2026-09-23/24) — **sayaç da stale olabilir** kanıtı.

**F) SAHİPLİK DAĞILIMI — IMPLEMENTED (ama yazılı değil):**

| Katman | Dosya | Sayı | Durum |
|---|---|---|---|
| Yönlendirici (kök) | `CLAUDE.md`, `WORKFLOW.md` | 2 | ✅ IMPLEMENTED (AGENTS.md kökte yok — §1.1-A) |
| SSOT merkezi (`.ai/` kök) | 15 `.md` | 15 | ✅ IMPLEMENTED |
| Domain modülleri | `shared/CLAUDE.md` (8.742 B) · `auth.coremusic.net/CLAUDE.md` (9.433 B) · `home.coremusic.net/CLAUDE.md` (7.215 B) · `assets.coremusic.net/CLAUDE.md` (11.032 B) + aynı 4 domainde `AGENTS.md` | 8 | ✅ IMPLEMENTED — **kısa mı, merkeze link mi? yazılmış değil** |
| Kod klasörü dokümanları | `shared/src/**`, `auth/include/**` altında `CLAUDE.md` | 178 − 34(vault) − 8(domain) ≈ 136 | ⚠️ **çoklu-CLAUDE.md alanı — sahiplik kuralı yok** |
| Rol dokümanları | `.ai/.agents/*.md` | 12 | ✅ IMPLEMENTED |
| Şablonlar | `.ai/.templates/**` | 36 | ✅ IMPLEMENTED (Guardrail #16 zorunlu) |
| Otomatik denetim (link + şablon uyumu + stale) | — | **0** | ❌ **PLANNED** |
| Post-op sync scriptleri | — | **0/2** | ❌ **PLANNED** |

**Sonuç etiketi:** **IMPLEMENTED:** 178 CLAUDE.md / 7 AGENTS.md / 2 WORKFLOW.md, `.ai/` 15 kök `.md` + 16 alt dizin, 36 şablon (registry 36/36), 25 kabul ADR, `.agents/` 12 dosya, `scripts/` 4 dosya, 2 link/dosya raporu (34 kırık link · 39 kod bulgusu), IMPLEMENTED/PLANNED etiket disiplini (`AGENTS.md` §3 Stack Etiketi + `engine.md:382` §9.2). **PLANNED:** `session-save.mjs`, `vault-post-update.mjs`, üç kollu otomatik denetim (link/şablon/stale), domain `CLAUDE.md` kısalma + merkeze link kuralı, `.ai/security/` (k6-guvenlik'e taşınmış durumda), kök `AGENTS.md`. `⚠️ VERIFICATION REQUIRED`: (i) 136 kod-klasörü `CLAUDE.md`'nin hangi bilgiyi taşıdığı **tek tek karşılaştırılmadı** (mükerrer iddia taraması yapılmadı); (ii) 34 kırık linkin ADR-024 sonrasında kaçının kapanacağı **tahmin edilmedi**; (iii) `.ai/architecture/adr/` serisi ile `.ai/.decisions/` serisi arasındaki numara ilişkisi **yazılı değil**.

### 1.2 Sorun Tanımı

1. **Sahipsiz bilgi:** 178 `CLAUDE.md` var; hangisi hangi bilginin **tek** kaynağı belirsiz → aynı iddia iki yerde yaşama (eski ADR-014 düzeltme ruhunun vault dokümanına taşınması gerekli).
2. **Yönlendirici ↔ SSOT ayrımı yazılı değil:** kök `CLAUDE.md` kısa mı olacak, tam mı? Domain `CLAUDE.md` merkeze link mi verecek, kopya mı tutacak? → **cevap yok = herkes kendi yolunu yazıyor.**
3. **Denetim yok:** link kırıklığı (34), şablon dışı dosya, stale iddia (ADR-015/017 örnekleri) — hepsi **manuel ve seans sonunda unutuluyor**; raporlar düzeltme yapmıyor.
4. **Post-op sync her seans kırık:** 5 ayrı `log.md` kaydı aynı hatayı tekrarlıyor (`:390/404/414/438/459`) → "⚠️ manuel" bir **süreç değil, açık yara**.
5. **Şablon şişkinliği ölçülmüyor:** 36 şablon / 16.503 satır envanter var; hangisinin kullanıldığı **sayılmıyor** (adoption 0 mu, 100 mü bilinmiyor).
6. **Kırık link raporu kendi içinde tutarsız hedef üretiyor:** 20 ADR linki `[[decisions/accepted/…]]` yazıyor, gerçek yol `.ai/.decisions/accepted/…` → **link biçimi standardı da karar konusu.**

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (Wikipedia SSOT, diataxis.fr, docs.gitlab.com, docs.github benzeri otorite), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) docs-as-code 2025-26, (b) modüler dokümantasyon + doküman sahipliği, (c) SSOT kalıpları, (d) stale/broken-link tespiti, (e) AI destekli doküman bakımı.** Erişim: **7 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "docs-as-code 2025 modular documentation docs ownership single source of truth best practices" · (2) "stale documentation detection automated link checking docs drift detection 2025" · (3) "documentation ownership model single owner per document DRI docs maintainability" · (4) "single source of truth documentation pattern duplicate content drift wiki 2025 modular docs structure" · (5) "AI-assisted documentation maintenance LLM keep docs up to date 2026 agents documentation quality" · (6) "documentation testing CI link checker markdown lint broken links docs quality gate best practices" · (7) "modular documentation information architecture Diátaxis single source of truth avoid duplicate docs" |
| Web Search **Konusu** | (1) Docs-as-code tanımı (git = SSOT, kodla aynı araçlar/review), paylaşımlı sorumluluk ve standartlar; (2) doküma drift'i (kod docs'tan hızlı değişir) → otomatik link denetimi, stale tespiti, PR'da CI kapısı; (3) doküman sahipliği: adlandırılmış DRI + tanımlı kapsam + görünür metrik, "sahipsiz doküman" uyarı işaretleri; (4) SSOT: bileşen/modüler içerik, kopya değil referans, kopyaların rekabeti ve yaşam döngüsü; (5) AI ajanlı doküman güncellemesi (PR'dan taslak üretimi), agent-created dosyaların bakım açığı; (6) docs linting (Vale/markdownlint), link rot istatistikleri, CI'da asla hata yutma; (7) Diátaxis ve modülerleştirme (tek konu = tek blok, daha az çoğaltma). |
| Web Search **Bağlam** | **~47 adlandırılmış kaynak / 7 sorgu**: writethedocs + konghq + docsie + fern docs-as-code + passo.uno + heretto + r/devops (7) · mintlify drift + dosu.dev (Claude Code + GitHub Actions) + moxiedocs + slite knowledge drift + sotadocs + borghei doc-drift-detector + ferndesk (7) · happysupport DRI + getdx + r/technicalwriting + bardglobal + consepsys + LinkedIn Mehler (6) · **Wikipedia SSOT** + atlassian + **paligo (modüler bileşen)** + slite + strapi + profisee + lucid + heretto (8) · mintlify AI docs 2026 + gitbook AI docs + **arXiv 2605.06464** + mind-core + zylos (5) · **mintlify linting** + fern linting guide (Ocak 2026) + lornajane + **docs.gitlab.com documentation testing** + docsie linting + **Pew link rot** + npm markdown-link-check + dev.to gomarklint (8) · **diataxis.fr** + idratherbewriting + adoc-studio (2026) + emmanuelbernard + cloudcannon + python discuss (6) |
| Web Search **Kısa Açıklama** | **(1) Docs-as-code:** "Git becomes your single source of truth" (konghq), dokümanı kodla aynı araçla yaz (Write the Docs), **aynı review standartları** (fern) ve **paylaşımlı sorumluluk + standart** gerekir (passo.uno) → şablon + review + git zorunlu. **(2) Drift:** kod docs'tan hızlı değişince drift kaçınılmaz (mintlify); PR birleşince Claude Code ile drift yakalayıp düzeltme PR'ı açan akış var (dosu.dev, moxiedocs) → **denetim = CI işi, insan işi değil.** **(3) Sahiplik:** çalışan model "adlandırılmış DRI + kapsam + metrik" (happysupport), docs DRIsı (getdx), teknik doğruluk = geliştirici, dil/edit = teknik yazar (r/technicalwriting) → **tek sahip + net kapsam.** **(4) SSOT:** kopya yerine **bileşen/parça bir kez yazılır, dokümanlar onu referanslar** (paligo); kopyalar "hâlâ aranabilir ve işaretsiz" kaldığı için birbirleriyle yarışır (slite); kopya-yapıştır zamanla **drift eder** (paligo/beijer örneği). **(5) AI bakım:** agent'lar PR/ticket'tan taslak güncellemeyi artık üretiyor (gitbook); ama **agent'ların kendi dosyalarına bakımı ~%17** (arXiv 2605.06464) → AI kendi bıraktığı eski iddiayı temizlemiyor, **denetim yine de gerekli.** **(6) Lint/link:** iç link denetimi PR'da, dış link zamanlanmış (mintlify); "CI'da link hatası **asla yutulmaz**" (lornajane); GitLab docs testi `docs:lint links` + Vale + markdownlint + Lychee (docs.gitlab.com); link rot **haber siteleri %23 / devlet siteleri %21** (Pew). **(7) Modüler:** Diátaxis 4 içerik türü ve "daha az çoğaltma + net amaç" (diataxis.fr, emmanuelbernard); modülerleştirme = "her blok tam bir konu" (adoc-studio 2026). |
| Web Search **Uzun Açıklama** | **(a) Docs-as-code (kaynak 1-8):** Write the Docs rehberi dokümanı kodun araçlarıyla (git, PR, review) yazmayı tarif ediyor; Kong "git = tek doğruluk kaynağı, docs'a aynı yazılım geliştirme pratikleri" diyor; Fern "kod için geçerli review standartlarını docs için de uygula"; Docsie docs-as-code'ın linter/link-check/CI adımlarını anlatıyor; Passo.uno "docs-as-code paylaşımlı hesap verebilirlik ve standart ister — docs altyapıysa yayın da altyapıdır" uyarısını yapıyor; Heretto, LinkedIn (Michta) ve r/devops aynı felsefeyi tekrarlıyor. Çapraz: konghq + writethedocs + fern + docsie aynı hükmü veriyor (**git + review + CI = docs-as-code**). **(b) Drift/stale (kaynak 9-15):** Mintlify drift'i "kod değişimi docs güncellemesinden hızlı çıktığında" tanımlar ve yüzey bazlı ölçüm önerir; Dosu.dev, birleşen PR'dan etkilenen doküman sayfasını okuyup **otomatik düzeltme PR'ı** açan GitHub Actions akışını tam YAML ile verir; Moxiedocs "cuma temizlik PR'ı" ile wiki'nin sessizce çürümesini engeller; Slite knowledge drift'i "yanlış bilgiyi ajanlara aktarma" riski olarak tanımlar; SotaDocs önceliklendirme + geri bildirim döngüsü önerir; borghei/Claude-Skills doc-drift-detector: skorlama + API AST + **link bütünlüğü denetimi** tek skill'de. Çapraz: mintlify + dosu + moxiedocs + sotadocs aynı (**drift = CI'da yakalanır**). **(c) Sahiplik (kaynak 16-21):** HappySupport "üç bileşen: adlandırılmış DRI, tanımlı kapsam, kaliteyi görünür kılan metrik" der; DX docs DRIsı ve açık sahiplik modellerini savunur; r/technicalwriting'te docs-as-code ekiplerinde "geliştirici ekibi teknik doğruluğun sahibidir" uzlaşması; BardGlobal "sahipsiz dokümanın uyarı işaretleri"ni listeler; Consepsys sahipliği denetlenebilirlik kapısı, LinkedIn (Mehler) sahipliği governance fonksiyonu olarak çerçeveler. Çapraz: happysupport + getdx + consepsys aynı (**tek adlandırılmış sahip + kapsam + metrik**). **(d) SSOT/modüler (kaynak 22-29):** Wikipedia SSOT'u "bir bilginin tek otorite kaynağı" olarak tanımlar (kaynakça uyarılı); Atlassian "önce mükerrer/çelişkili bilgiyi denetle, tek yere taşı"; Paligo doküman-temelli yerine **bileşen-temelli yazarlık**: bileşen bir kez yazılır, dokümanlar kopyalamaz **referanslar**, kopya-yapıştır "save as" akışı sürüm drift'i üretir; Slite "kopya belgeler birbiriyle yarışır, SSOT kopyalara yaşam döngüsü verir"; Strapi/Profisee/Lucid/Heretto aynı mimariyi tekrarlıyor. Çapraz: paligo + slite + heretto + atlassian aynı (**kopya yok, referans var**). **(e) AI bakım (kaynak 30-34):** Mintlify "AI-assisted documentation maintenance — elle takip her ürün değişiminde docs'u geride bırakır"; GitBook "proaktif ajanlar PR/ticket'tan taslak güncelleme üretir"; arXiv 2605.06464 ajanların **kendi oluşturdukları dosyalara yalnız ~%17 bakım** yaptığını ölçer; MindCore "AI tek başına güncel tutamaz, nereye bakacağını güvenilir söyler"; Zylos kod→doküman üretiminin olgunlaştığını ama denetimin şart olduğunu vurgular. Çapraz: mintlify + gitbook + mind-core aynı (**AI taslak üretir, sahip onaylar**). **(f) Lint/link (kaynak 35-42):** Mintlify docs linting: prose lint (Vale) + **iç link PR'da, dış link zamanlı** + CI enforcement; Fern Docs Linting Guide (Ocak 2026): link rot hemen otorite kaybettirir, **Pew: haber %23 / devlet %21 sitede en az bir ölü link**; Lorna Jane Mitchell "link denetimi CI hattında koşar ve **hata asla yok sayılmaz**"; GitLab Docs `docs:lint markdown`/`docs:lint links` + Vale + markdownlint + **Lychee** ile çevrimiçi link testi; Docsie linting tarifinde "kırık iç referans → öncelikli rapor + öneri"; markdown-link-check ve gomarklint iç/dış/anchor link'i ayırır. Çapraz: mintlify + fern + gitlab + lornajane aynı (**CI'da link+lint kapısı**). **(g) Modüler mimari (kaynak 43-47):** Diátaxis "dört içerik türü, uygulama kısıtı dayatmaz"; İdRatherBeWriting Diátaxis'in net amaç/sınırlarını; adoc-studio modülerleştirmeyi "tek blok = tek konu, ayakta durabilir" diye tarif eder; Emmanuel Bernard "her doküman kendi amacına hizmet eder → daha az çoğaltma"; CloudCannon ve Python listesi aynı çerçeveyi uyguluyor. Çapraz: diataxis + adoc-studio + emmanuelbernard aynı (**modüler blok + tek konu = tek yer**). ⚠️ Not: **sayfa-içi derin tur yapılmadı** → dosya sayımı/tıklanabilir link gibi sayısal iddialar başlık/özet düzeyindedir; tek kaynaklı tek çıkarım: arXiv %17 rakamı (tek çalışma). |
| Web Search **Paragraf Veri Uzun** | Docs-as-code 2025-26: doküman git'te yaşar, kodla aynı PR/review/CI araçlarından geçer — "Git becomes your single source of truth" (konghq), "kod için geçerli review standartları docs için de" (fern, writethedocs), "docs altyapıysa yayın da altyapıdır" (passo.uno) → vault'ta da her dosya şablondan + review'dan geçer. **Modüler + sahiplik:** tek konu tek blok (diataxis.fr, adoc-studio 2026), her dokümana adlandırılmış sahip (DRI) + tanımlı kapsam + görünür metrik (happysupport, getdx, consepsys), "sahipsiz doküman" uyarı işaretleri (bardglobal) → §2.2a sahip matrisi. **SSOT:** bileşen/parça bir kez yazılır, dokümanlar onu referanslar (paligo); kopyalar "işaretsiz kaldığı için birbiriyle yarışır" (slite) ve kopya-yapıştır drift üretir (atlassian, heretto, strapi) → domain `CLAUDE.md` kısa + merkeze link, kopya yasak (ADR-014 ruhu). **Drift/stale:** kod docs'tan hızlı değişince drift kaçınılmaz (mintlify); PR birleşince Claude Code ile drift yakalayıp düzeltme PR'ı açan akış (dosu.dev), "cuma temizlik PR'ı" (moxiedocs), doc-drift-detector skorlama + link bütünlüğü tek skill'de (borghei/Claude-Skills), önceliklendirme + geri bildirim döngüsü (sotadocs) → denetim 3 (stale) CI işi, insan işi değil. **Lint/link:** iç link PR'da, dış link zamanlanmış (mintlify, fern); GitLab `docs:lint links` + Vale + markdownlint + Lychee (docs.gitlab.com); "CI'da link hatası asla yutulmaz" (lornajane); link rot istatistiği haber siteleri %23 / devlet siteleri %21 (Pew), `npm markdown-link-check` pratik kullanımı → denetim 1 (link) bağlayıcı. **AI-assisted bakım:** agent'lar PR/ticket'tan doküma taslağı üretiyor (gitbook, mintlify 2026) ama agent'ların kendi dosyalarına bakım oranı ~%17 (arXiv 2605.06464) → denetim otomatik, **onay insan** kalır (§4.3/5). **Çıkarımlar (⚠️):** %17 oranı tek çalışmadan; link rot rakamları sayfa-içi turda derin doğrulanmadı. |
| Web Search **Sonucu** | 1) **Docs-as-code doğrulandı** (kaynak 1-8, ≥2 çapraz: writethedocs + konghq + fern + docsie): git + PR + **aynı review standartları** → bu ADR: doküman da repo'da, **şablondan + review'dan** geçer (§2.2a). 2) **Otomatik drift/link denetimi doğrulandı** (kaynak 9-15, 35-42: mintlify + dosu + gitlab + lornajane): iç link PR'da, dış link zamanlı, hata asla yutulmaz → **üç kollu denetim (link/şablon/stale) CI kapısı olur (§2.2c).** 3) **Tek sahip + kapsam + metrik doğrulandı** (kaynak 16-21: happysupport + getdx + consepsys): adlandırılmış DRI, tanımlı kapsam → **her dokümanda `sahip` alanı zorunlu (§2.2a).** 4) **SSOT = referans, kopya değil doğrulandı** (kaynak 22-29: paligo + slite + atlassian + heretto): bileşen bir kez yazılır → **domain `CLAUDE.md` kısa + merkeze link, kopya yasak (§2.2a)** — ADR-014 ruhunun doküman karşılığı. 5) **AI destekli bakım doğrulandı ama sınırlı** (kaynak 30-34: gitbook + mintlify + arXiv 2605.06464): agent taslak üretir, **kendi dosyasına ~%17 bakım yapar** → denetim insan onaylı kalır (§4.3/5). 6) **Modüler + Diátaxis yapısı doğrulandı** (kaynak 43-47: diataxis + adoc-studio + emmanuelbernard): tek konu tek blok → `.ai/` altındaki konu-klasör ayrımı (architecture/ui-design/reports) korunur, **yeniden düzenlemez.** **Toplam ~47 adlandırılmış kaynak, 7 sorgu**; iki çıkarım açıkça işaretlendi: **⚠️** tek çalışma (arXiv %17) ve sayfa-içi tur yapılmadığı için sayısal iddiaların (link rot %23/%21) derin doğrulanmaması. |
| Web Search **Alınan Karar** | **ADR-024 KABUL EDİLİR — VAULT-MERKEZLİ MODÜLER DÖKÜMANTASYON (4 madde):** **(a) Yerleşim + sahiplik:** `.ai/` = tek SSOT merkezi; kök `CLAUDE.md`/`AGENTS.md`/`WORKFLOW.md` = **yönlendirici + kural** (link verir, bilgi tekrarlamaz); domain klasörleri (`shared`, `auth.coremusic.net`, `home.coremusic.net`, `assets.coremusic.net`) = **kısa** kendi `CLAUDE.md`'si + merkeze wiki-link; `.ai/.agents/` = rol dokümanları; `.ai/.templates/` = şablon. **Her dokümanın tek sahibi + tek SSOT'U vardır; aynı bilgi iki yerde yazılmaz** (ADR-014 düzeltme ruhu). **(b) Şablon + otomatik denetim:** tüm doküman `.templates/` şablonundan (Guardrail #16); üç denetim: **link** (kırık wiki-link raporu), **şablon uyumu**, **stale tespit** (eski iddia/çelişki — ADR-015/017 örneği); **UTF-8 yazım tek arayüz** (`vault-utf8-writer.mjs`). **(c) Eksik scriptler bu kararın parçası:** `.ai/scripts/session-save.mjs` + `.ai/scripts/vault-post-update.mjs` **yazılır** (bugün yok → her seans ⚠️ manuel sync); yazıldığında post-op sync zorunlu ve otomatiktir. **(d) Cross-link:** wiki-link `[[göreli/yol]]`, hedef **diskte var olmak zorunda** (yoksa düz metin + `⚠️ VERIFICATION REQUIRED` — dead-link yasak). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: docs-as-code (8), drift/link denetimi (13), sahiplik/DRI (6), SSOT/modüler (8), AI bakım (5), Diátaxis (5) → **~47 adlandırılmış kaynak, 7 sorgu**; çapraz doğrulama ≥2 kaynak altı ana iddiada karşılanır, tek-çalışım ve sayfa-içi derin tur eksikliği açıkça işaretlendi. **Vault tarafı aynı resmi verdi:** yerleşim/şablon/script envanteri **IMPLEMENTED**, otomatik denetim + iki post-op script **PLANNED** (5 tekrarlayan `log.md` kaydı) → bu ADR **sözleşme, kod taahhüdü değil**; uygulaması §5.1 adımlarına bağlıdır. **Kaynak listesi (47):** 1) writethedocs.org — Docs as Code · 2) konghq.com — What is Docs as Code · 3) docsie.io — Docs-as-Code definition · 4) buildwithfern.com — Docs-as-code · 5) passo.uno — What docs as code really means · 6) heretto.com — scaling code documentation · 7) linkedin (Michta) — Docs as Code perspective · 8) reddit r/devops — Documentation as Code · 9) mintlify.com — Stop Documentation Drift · 10) dosu.dev — Claude Code + GitHub Actions drift · 11) moxiedocs.com — Documentation Drift Detection · 12) slite.com — Knowledge drift · 13) sotadocs.com — Detect and Fix Stale Context · 14) github.com/borghei/Claude-Skills — doc-drift-detector · 15) ferndesk.com — automated documentation tools 2026 · 16) happysupport.ai — Who Owns Documentation (DRI) · 17) getdx.com — developer documentation DRI · 18) reddit r/technicalwriting — Who owns documentation · 19) bardglobal.com — Documentation Ownership · 20) consepsys.com — Document ownership · 21) linkedin (Mehler) — Real Role of Document Owners · 22) **en.wikipedia.org — Single source of truth** · 23) atlassian.com — Building a true SSoT · 24) paligo.net — SSOT (modüler bileşen) · 25) slite.com — Single source of truth · 26) strapi.io — What Is a SSoT · 27) profisee.com — Create a SSoT · 28) lucid.co — SSOT guide · 29) heretto.com — Building a SSoT (reuse) · 30) mintlify.com — Best AI Documentation Tools 2026 · 31) gitbook.com — Best AI Documentation Tools 2026 · 32) **arxiv.org/2605.06464** — agent-generated code maintenance · 33) mind-core.com — AI tools for docs teams 2026 · 34) zylos.ai — AI documentation generation 2026 · 35) mintlify.com — Documentation Linting · 36) buildwithfern.com — Docs Linting Guide (Jan 2026) · 37) lornajane.net — Checking Links in Docs-as-Code · 38) **docs.gitlab.com — Documentation testing** · 39) docsie.io — Documentation Linting · 40) pewresearch.org — When Online Content Disappears · 41) **diataxis.fr** · 42) idratherbewriting.com — What is Diátaxis · 43) adoc-studio.app — Modularize Documentation 2026 · 44) emmanuelbernard.com — Exploring Diataxis · 45) cloudcannon.com — Diátaxis redesign · 46) npmjs.com — markdown-link-check · 47) dev.to — gomarklint (üç tür kırık link) |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-005-ultrathink-protocol]] | Kod/vault kanıtı olmayan her iddia etiketli: `.ai/security/` **0**, kök `AGENTS.md` **yok**, kod-klasörü `CLAUDE.md` mükerrerliği **taranmadı**, 34 kırık linkin kapanma oranı **tahmin değil** → `⚠️ VERIFICATION REQUIRED` |
| [[ADR-014-multi-db-migration-strategy]] | "Tek doğruluk kaynağı + mükerrer bilgi yok" ruhu bu ADR'nin çekirdeği; domain `CLAUDE.md` merkezden bilgi **kopyalayamaz**, link verir |
| [[ADR-023-persona-driven-testing]] | Denetim çıktısı **ölçülebilir** olmalı (kayıp/link sayısı/süre); ADR-023 CI gate'i ile **aynı `ci.yml` hattını** paylaşabilir → çalışma zamanı çakışması §4.3/3 |
| `[[../../AGENTS.md]]` §25.3 kural 3 | `log.md` **append-only** — bu işlemde geçmiş satıra dokunulmaz; §7.1 kaydı dahil |
| `[[../../AGENTS.md]]` §26.2 | SSOT hiyerarşisi: çelişkide **kök dosya kazanır** — bu ADR bu hiyerarşiyi bozmaz, sahiplik satırını ekler |
| In-Place Refactoring | Dosya adları (`CLAUDE.md`, `.templates/*`, `scripts/*`, domain dosyaları) **onaysız değiştirilemez/taşınmaz**; bu ADR yalnız yerleşim + sahiplik + denetim kararı yazar |
| Guardrail #16 (şablon zorunlu) | Yeni/vardırılan her doküman `.templates/` şablonundan; şablonsuz dosya üretilmez |
| Frozen ADR-001-037 | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) — bu ADR frozen **değil** |
| REDACTED | Denetim çıktılarında secret/credential `.env` değeri yazılmaz; `.ai/keys.md` içeriği bu ADR'ye kopyalanmaz |
| Numara kuralı | `ADR-024-ecosystem-modular-docs` **rezerve slottur** (`../index.md:61`); yeni ADR ≥088 kuralı bu yazımda uygulanmaz (ADR-019-023 istisnası) |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (bayt-seviyesi, `vault-utf8-writer append`) |

---

## 2. Karar (Decision)

**CoreMusic dokümantasyon mimarisi DÖRT maddeyle bağlayıcı ilan edilir: (a) yerleşim + sahiplik — `.ai/` tek SSOT merkezi, kök dosyalar yalnız yönlendirici + kural, domain klasörleri kısa `CLAUDE.md` + merkeze link, `.agents/` rol, `.templates/` şablon; her dokümanda tek sahip ve tek SSOT, aynı bilgi iki yerde olmaz. (b) senkron — tüm doküman `.templates/` şablonundan yazılır ve ÜÇ denetimden geçer: kırık wiki-link, şablon uyumu, stale tespit (eski iddia/çelişki); UTF-8 yazım tek arayüz `vault-utf8-writer.mjs`. (c) eksik post-op scriptler bu kararın parçasıdır: `.ai/scripts/session-save.mjs` + `.ai/scripts/vault-post-update.mjs` yazılır — yazılana kadar her seans `⚠️` manuel sync, yazıldıktan sonra zorunlu otomatik sync. (d) cross-link: wiki-link `[[göreli/yol]]`, hedef diskte var olmak zorunda; olmayan hedefe link atılmaz, düz metin + `⚠️ VERIFICATION REQUIRED` yazılır.**

### 2.1 Neden Bu Seçenek?

- **Merkez yoksa 178 dosya yarışır:** repo'da 178 `CLAUDE.md` var ve sahiplik kuralı yok (§1.1-F) → "aynı bilgi iki yerde" bugünden tehdit; SSOT literatüründe kopyalar **birbiriyle yarışır** (slite, paligo — §1.3 4).
- **Köke bilgi yazmak link'i öldürür:** kök `CLAUDE.md` (3.139 B) kısa ve yönlendirici; 28.590 B `WORKFLOW.md` süreç sahibi → rollerin netleşmesi tek başına duplicate'ı önler (§2.2a matrisi).
- **Denetim olmayan kural gözden düşer:** 34 kırık link + 5 `log.md` "ÇALIŞTIRILAMADI" kaydı + 3 elle stale düzeltmesi (ADR-015/017/003) → denetim **CI işi** (mintlify, gitlab, lornajane — §1.3 2/6), insan işi değil.
- **Script eksikliği süreç değil, arıza:** 5 seans üst üste aynı ⚠️ → `session-save` + `vault-post-update` bu ADR'nin **parçası**, sonraki işe bırakılamaz.
- **Şablon tek başına yetmez:** 36 şablon var ama uyum denetlenmiyor (§1.1-B) → şablon + uyum denetimi **aynı madde** olmalı (Guardrail #16 ancak ölçülürse işler).
- **AI tek başına temizlemiyor:** ajanların kendi dosyalarına ~%17 bakım yaptığı ölçüldü (arXiv 2605.06464 — §1.3 5) → stale tespiti otomatik, **onay insan** olmalı.
- **Yerleşim değişmez, korunur:** `.ai/` altındaki konu-klasörleri (architecture · ui-design · reports · ecosystem) Diátaxis'in "tek konu tek blok" düzeniyle uyumlu (§1.3 7) → **yeniden düzenlemez, yalnız sahiplik satırı eklenir** (In-Place Refactoring).

### 2.2 Teknik Detaylar

**a) Yerleşim + Sahip Matrisi (bağlayıcı — her satır tek SSOT ilan eder):**

| Katman | Dosya / Dizin | Rol | Sahip (DRI) | Bilgi tekrarlar mı? |
|---|---|---|---|---|
| Yönlendirici | `../../../CLAUDE.md` · `../../../WORKFLOW.md` · kök `AGENTS.md` (**bugün YOK — §5.1/6**) | Kural + yön (okuma sırası, guardrail özeti) | Vault Steward | ❌ Yalnız link + kural cümlesi |
| **SSOT merkezi** | `.ai/` kök 15 `.md` (`CLAUDE`, `AGENTS`, `WORKFLOW`, `brain`, `index`, `keys`, `glossary`, `MEMORY`, `log`, `engine`, `ROLE`, `VISION`, `PROJECTS`, `ULTRA-THINKING`, `broken-links-report`) | Her alanın **tek kaynağı** | MO (vault-updater) + dosya sahibi | ✅ Tek burada |
| Kararlar | `.ai/.decisions/` (`index.md`, `accepted/`, `draft/`, `rejected/`) | ADR tekliği | Vault Steward | ✅ Tek burada |
| Şablonlar | `.ai/.templates/` (36 dosya, registry `[[../../.templates/index]]`) | Şablon envanteri (SRP) | Vault Steward | ❌ Envanter tekrarlanmaz |
| Roller | `.ai/.agents/` (12 dosya; SSOT `.ai/AGENTS.md` §26.2) | Profil indeksi | MO | ❌ Routing tekrarlanmaz |
| **Domain modülleri** | `shared/CLAUDE.md` · `auth.coremusic.net/CLAUDE.md` · `home.coremusic.net/CLAUDE.md` · `assets.coremusic.net/CLAUDE.md` (+ 4 `AGENTS.md`) | **Kısa** kapsam + sorumluluk + **merkeze link** | Domain lead (Backend / Security / UI / Data) | ❌ Ayrıntı `.ai/`'de |
| Kod-klasörü notları | `shared/src/**`, `auth/**`, `assets/**` altı ≈136 `CLAUDE.md` | Yerel bağ notu (kısa) | Dosyanın sahip olduğu agent | ❌ **Kural: 1 paragraf + merkeze link** (§5.1/3 mükerrer taraması) |
| Mimari | `.ai/architecture/` (24 dizin, k0-k20 + `adr/` + `firmware/`) | Katman dokümanı | Domain lead (ilgili katman) | ✅ Katman kendi alanında tek |
| Raporlar | `.ai/reports/` (9) + `.ai/broken-links-report.md` | Salt bulgu (düzeltmez) | Vault Steward | ✅ Rapor tekrar üretilmez, tazelenir |
| Scriptler | `.ai/scripts/` (4 + **2 yeni**) | Araç (doküman değil) | MO (vault-updater) | ❌ Envanter `scripts/index.md` |

**b) Cross-link Kuralları:**

| Kural | Uygulama | İhlal |
|---|---|---|
| Biçim | `[[göreli/yol/dosya]]` — dosyanın **kendi dizininden** göreli (uzantı `.md` yazılmaz) | Kırık link → dead-link |
| Disk kanıtı | Hedef **diskte var olmak zorunda**; yoksa link **atılmaz** → düz metin + `⚠️ VERIFICATION REQUIRED` | Hallucination |
| Köklere çıkış | `.ai/.decisions/accepted/` → merkez `[[../../CLAUDE.md]]`, `[[../../AGENTS.md]]`, `[[../../index]]`; domain → `[[../../../CLAUDE.md]]` | Yanlış derinlik |
| Kod/script yolları | Wiki-link değil **backtick yol** (`.ai/scripts/vault-utf8-writer.mjs`) — script doküman değildir | Geçersiz wiki-link |
| Dead-link işareti | Kapanmayan link için `<!-- dead-link: <slug> no source <tarih> -->` (faz6-D deseni) | Sessizce kırık kalma |
| Yeni link = yeni denetim | Link eklendikten sonra **link denetimi** yeniden koşar | Denetimsiz link |

**c) Üç Denetim (senkron — hepsi bu ADR'nin çıktısı):**

| # | Denetim | Ne yapar | Çıktı | Frekans | Durum |
|---|---|---|---|---|---|
| 1 | **Link denetimi** | Tüm `[[...]]` çözümlenir (dosya-göreli + `.ai/` köklü + `.md` eki), diskte olmayan hedefler listelenir | `broken-links-report.md` (tazelik + adet) | her post-op + haftalık | 🔄 mevcut rapor var, **otomasyon PLANNED** |
| 2 | **Şablon uyumu** | Yeni/değişen doküman `.templates/` iskeleti + frontmatter **7 alan** ile karşılaştırılır | şablon-uyum raporu (dosya, eksik alan) | her yazım sonrası | ❌ **PLANNED** |
| 3 | **Stale tespit** | Eski iddia/çelişki: sayaç ↔ disk (ör. "workflows = 0"), `superseded`/`eski slug`, ADR iddiası ↔ kod gerçekliği | stale-raporu (iddia, eski değer, disk değeri) | her post-op + faz sonu | ❌ **PLANNED** (bugün 3 örnek elle bulundu — §1.1-E) |

**d) Eksik Scriptler (bu kararın maddesi c'si):**

| Script | Arayüz | Zorunlu davranış |
|---|---|---|
| `.ai/scripts/session-save.mjs` | `node .ai/scripts/session-save.mjs --task "<açıklama>" --status <completed\|failed> --agent <ad>` | `MEMORY.md` session bloğunu + `log.md` append satırını UTF-8 yazar; çıkışta JSON `{ok, wrote[]}` |
| `.ai/scripts/vault-post-update.mjs` | `node .ai/scripts/vault-post-update.mjs --scope root` | (1) üç denetimi koşar, (2) `log.md` append, (3) `MEMORY.md` + envanter sayaçlarını tazeler, (4) sonuç doğrular (`log.md`, `MEMORY.md`, `project-state.md` — `project-state.md` **yoksa oluşturur veya kapsam dışı olduğunu söyler, sessiz geçmez**) |
| Ortak | `--dry-run` + doğrulama | Yazım **yalnız** `vault-utf8-writer.mjs` üzerinden (PowerShell yazma cmdlet'i yasak); hata `exit 1` |

**e) UTF-8 Zorunluluğu:** tüm vault yazımı `vault-utf8-writer.mjs` (`append` log.md için tek izinli mod; `write`/`insert-before-marker` yedekli; `verify`/`scan`/`repair` denetim). Denetim 2 (şablon uyumu) **mojibake taramasını da kapsar** (`scan` çıktısı).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Dağınık bağımsız domain dokümanları** (her domain kendi `CLAUDE.md`'sinde tam bilgi tutar) | Yerel erişim hızlı, sahiplik domainde | 4 domain + 136 kod notu = **kopya yarışı**, link/lint yok; ADR-015 `:42` çelişkisi gibi stale'ler çoğalır | SSOT literatürü kopya-yapıştırın drift ürettiğini gösteriyor (paligo/slite — §1.3 4); ADR-014 ruhuna aykırı |
| 2 | **Tek dev kök dosya** (`AGENTS.md`/`README` içinde her şey — merkez değil, tek dosya) | Tek yerde arama, yapı basit | 100KB+ tek dosya → token taşması, sahiplik (kim hangi bölüm) belirsiz, şablon uygulanamaz | Okuma sırası token bütçesini kırar (`AGENTS.md` §13/§24.2); tek dosya = tek sahip sahipsizleşir |
| 3 | **Dış wiki/SaaS (Notion/Confluence) taşınması** | Arama + izin + sürüm hazır | Git dışı SSOT, PR/review yok, offline + REDACTED denetimi zor, link denetimi servise bağımlı | Docs-as-code şartı (git = SSOT, aynı review — §1.3 1) karşılanmaz; `.ai/` zaten repo içinde |
| 4 | **Otomatik üret + otomatik onay** (AI ajanı stale'leri kendi onayıyla düzeltir) | Sıfır insan yükü | Ajanların kendi dosyalarına bakımı ~%17 (arXiv 2605.06464); hatalı düzeltme sessiz yayılır | Denetim **otomatik**, onay **insan** kalır (§2.1); Red Team/Human Mode governance'ı aksi halde anlamsızlaşır |
| 5 | **Docusaurus/MkDocs statik site + yönlendirme** | Link check yerleşik, yayın pretty | İçerik Git'te kalmaz/mükellef iki katman; mevcut `.ai/` yapısı + Obsidian grafiği çöpe gider | Yeni araç katmanı ekleme (YAGNI); mevcut `broken-links-report` + `vault-faz4-sweep` aynı işi repo içinde görür |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek sahip + tek SSOT:** her dokümanda sahip ve kaynak dosya belli → çelişki çözümlenirken "hangisi doğru?" sorusu kapanır (§2.2a matrisi).
- **Kopya engeli baştan:** domain/kod `CLAUDE.md` merkeze link verir, bilgi kopyalamaz → ADR-014 düzeltme ruhu doküman katmanına taşınır.
- **Denetim görünür ve tekrarlanabilir:** 34 kırık link, 3 elle stale düzeltmesi ve 5 "sync çalışmadı" kaydı **sayıya** dönüşür; raporlar otomatik tazelenir.
- **Post-op sync kapanır:** `session-save` + `vault-post-update` yazıldığında her seans sonunda `log.md`/`MEMORY.md` senkronu zorunlu ve doğrulanır (§5.1/1-2).
- **UTF-8 tek kapı:** mojibake onarımı (`repair`, 745 düzeltme geçmişi) yerine **önleme**; yazım tek arayüzden.
- **Şablon adoption'ı ölçülür:** 36 şablonun hangisinin kullanıldığı denetim 2 ile görünür → Guardrail #16 kağıt kural olmaktan çıkar.
- **AI-açınır yapı:** link/şablon/stale denetimi makine-okunur çıktı üretir → agent'lar denetimi koşar, insan onaylar (§1.3 5 ile tutarlı).

### 4.2 Olumsuz Sonuçlar

- **İlk geçiş maliyeti:** 136 kod-klasörü `CLAUDE.md`'nin kısaltılması + merkeze linklenmesi **manuel okuma** ister (§5.1/3-4); tek seferde değil, kademeli.
- **İki senkron script bağımlılığı:** `session-save`/`vault-post-update` yazılmadan madde (c) **yerine getirilmemiş** sayılır → ADR kabul edilmiş ama pipeline henüz yok (açık PLANNED).
- **Denetim çıktısı gürültü üretebilir:** ilk tarama 34+ link ve muhtemel şablon uyuşmazlıkları döker; her seans rapor üretimi log'u şişirir.
- **Sahiplik ataması yetki ister:** "domain lead" kim, kod notu sahibi agent nasıl atanır — atama yapılmazsa matris kağıtta kalır.
- **Kökte `AGENTS.md` yokluğu:** yönlendirici katman bugün eksik (§1.1-A) → ya oluşturulur ya da `.ai/AGENTS.md`'ye link yazılır; ikisi de ek iş.
- **Yazım hızı düşer:** her doküman şablon + 7 alan frontmatter + link disk kanıtı ister (kısa not yazmak bile maliyetli).

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| 1 | **Şablon şişkinliği:** 36 şablon / 16.503 satır; denetim 2 "şablonsuz dosya" derken gerçek ihtiyacı da engelleyebilir (her kısa not için tam ADR iskeleti) | 3 (Olası) | 3 (Orta) | Şablon **kategori** bazlı zorunlu (kısa not → `docs-md-template`/`claude-md-template`, ADR → `adr-template`); adoption metriği denetim 2'de aylık raporlanır |
| 2 | **Denetim gürültüsü:** her post-op'ta 34 eski link + yeni bulgular → rapor "normale" dönüp göz ardı edilir (alert fatigue) | 3 (Olası) | 3 (Orta) | **İki kademeli çıktı:** hata (yeni kırık link = bloklar) vs uyarı (eski/dead-link işaretli); rapor başında adet delta'sı; eski kırık linkler için kapatma takvimi (§5.1/5) |
| 3 | **Sahiplik belirsizliği:** "domain lead" ataması yapılmazsa herkes her dosyaya yazar → tek sahip kuralı kâğıtta kalır | 3 (Olası) | 4 (Yüksek) | `sahip` alanı frontmatter'da zorunlu (denetim 2); atama `AGENTS.md` §5 domain boundary ile eşleşir; atanmamış dosya = `⚠️ UNOWNED` raporda |
| 4 | **İki ADR serisi numara çakışması:** `.ai/architecture/adr/ADR-024-surucu-firmware-birlesme.md` ≠ `.ai/.decisions/accepted/ADR-024-ecosystem-modular-docs.md` | 4 (Çok olası) | 3 (Orta) | Wiki-link **her zaman tam relatif yol + slug** (§2.2b) — kısa `[[ADR-024]]` yasak; iki seri `index.md`'de ayrı sütun/kategori (Documentation vs Electronics/mimari) |
| 5 | **AI-onaylı düzeltme hatası:** otomatik stale düzeltmesi yanlış disk değerini "doğru" kabul eder | 2 (Mümkün) | 4 (Yüksek) | Denetim 3 **salt rapor** (düzeltilmez — mevcut `broken-links-report` ruhu); düzeltme = ayrı onaylı işlem (Red Team · Human Mode) |
| 6 | **Script bağımlılığı kırılırsa:** `session-save`/`vault-post-update` hata verirse senkron yeniden manuale döner (bugünkü durum) | 2 (Mümkün) | 3 (Orta) | `--dry-run` + `exit 1` + `log.md`'de hata satırı; fallback: manuel `vault-utf8-writer append` (bugünkü prosedür, açıkça yazılır) |
| 7 | **34 kırık linkin bir kısmı "sahip onayı" bekler** (dead-link/mark) → denetim 1 kapanamaz | 4 (Çok olası) | 2 (Düşük) | Her kırık link için üç yol: repoint / dead-mark / hedef üret — kararı Vault Steward, süre §5.1/5 |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **`.ai/scripts/session-save.mjs` yaz** (arayüz §2.2d; `--task/--status/--agent`; `MEMORY.md` + `log.md` append; `--dry-run`; UTF-8 yalnız `vault-utf8-writer` üzerinden) | MO (vault-updater) | 1 gün |
| 2 | **`.ai/scripts/vault-post-update.mjs` yaz** (üç denetimi koşar → `log.md` append → `MEMORY.md`/envanter tazeler → sonuç doğrular; `project-state.md` yoksa **açıkça bildirir**) | MO (vault-updater) | 1-2 gün |
| 3 | **Sahiplik satırı + kısa not kuralını uygula:** domain `CLAUDE.md`'lerine `sahip` alanı ekle; 136 kod-klasörü `CLAUDE.md`'sini "1 paragraf + merkeze link" formatına **kademeli** geçir (önce `shared/src/**`) | Domain lead'ler + Vault Steward | 1 hafta (kademeli) |
| 4 | **Mükerrer iddia taraması:** domain/kod notları ile `.ai/` kök SSOT arasında aynı iddia aranır (`⚠️ VERIFICATION REQUIRED` işaretli bulgular: 136 dosya taranmadı — §1.1-F) | Vault Steward + MO | 2 gün |
| 5 | **34 kırık linki kapat:** her satır için repoint / dead-mark / hedef üret (sahip onayı); `broken-links-report.md` tazelenir | Vault Steward (sahip onaylı) | 2 gün |
| 6 | **Yönlendirici katmanı tamamla:** kök `AGENTS.md` **YOK** → ya `.ai/AGENTS.md`'ye işaret eden ince bir kök dosya yaz ya da kök `CLAUDE.md`'ye "agent registry: `.ai/AGENTS.md`" linki koy (onay: Vault Steward) | Vault Steward | 0.5 gün |
| 7 | **Denetim 2 + 3'ü `vault-post-update.mjs` içine göm** (şablon uyumu + frontmatter 7 alan + stale sayaç↔disk karşılaştırması; çıktı iki kademeli — §4.3/2) | MO (vault-updater) | 2 gün |
| 8 | **CI bağlantısı (opsiyonel faz sonu):** `ci.yml`'e `vault-post-update --dry-run` adımı → doküman hatası PR'ı düşürür (ADR-023 gate'i ile aynı hatta, ayrı job) | DevOps + Vault Steward | 1 gün (faz sonu) |
| 9 | **Doğrulama:** `vault-utf8-writer scan` (mojibake 0), link denetimi (yeni kırık link 0), `grep -c '{{' <ADR-024>` (0), `.ai/.decisions/index.md:61` slug eşleşmesi | Vault Steward | 0.5 gün |
| 10 | **Şart 1b — security referansını düzelt:** `.ai/security/` YOK → ya `.ai/security/` dizini oluşturulur ya vault'taki `.ai/security/` referansları `architecture/k6-guvenlik/` hedefine çevrilir (debate Tur 2/2 — §5.5 şart 1b; dosya adları değişmez) | Vault Steward + Security Engineer | 0.5 gün |

### 5.2 Geri Dönüş Planı

**Vazgeçme (madde bazlı):** (c) scriptler yazılmazsa → madde (c) düşer, `.ai/log.md:390/404/414/438/459` kaydı **açık kalmaya devam eder** ve ADR §5.1/1-2 açık görev olarak kalır; (b) denetim 2/3 gürültü üretirse → denetim `--dry-run` + uyarı kademeye çekilir, **link denetimi asla kapatılmaz** (mevcut rapor zaten salt-okunur).

**Tam geri dönüş:** yeni dosya adı/yerleşim değişmediği için (In-Place Refactoring korundu) geri dönüş = (1) `session-save.mjs` + `vault-post-update.mjs` **silinmez**, `scripts/index.md`'de `status: reverted` işaretlenir; (2) `sahip` alanları `<!-- unowned -->` geri alınır (eski dosya içeriği `C:/temp/opencode/vault-backups` yedeğinden); (3) denetim 2/3 kapatılır, **denetim 1 (link) açık kalır** (var olan rapor bu ADR'den önce de vardı); (4) `.ai/log.md`'ye tek satır revert append'i atılır; (5) `.ai/.decisions/index.md:61` satırı `status: reverted` olur; (6) bu ADR **düzenlenmez**, `superseded by ADR-NNN` ile yeni ADR yazılır (şablon §6.3).

**Korunan geri dönüş güvencesi:** `log.md` append-only geçmiş ve `.templates/` envanteri (36/36) bozulmaz; yedekler `writeUtf8` öncesi otomatik alınır.

### 5.3 Debate Kaydı

| Tur | Persona | Durum | Sonuç |
|---|---|---|---|
| Tur 1 | 20 persona | ✅ | **Bulgu:** kök `AGENTS.md` YOK (`CLAUDE.md`/`WORKFLOW.md` var) · repo geneli **178 `CLAUDE.md`** (136'sı kod klasöründe) · `.ai/` içinde **34 `CLAUDE.md`** · `.templates/` **36 dosya** · `.ai/scripts/` **4 dosya + 2 eksik script** (`session-save.mjs`, `vault-post-update.mjs`) · `broken-links-report` **34 kırık link / 5 dosya** · `.ai/security/` **YOK** (içerik `architecture/k6-guvenlik/` altında) · `.ai/AGENTS.md` **v22.0.3** registry SSOT → **15 kabul/neutral, 4 uyarı**; **Critic:** kök `AGENTS.md` eksik + yanlış security referansı **şart** |
| Tur 2 | İtiraz→çözüm | ✅ | **4 itiraz → 4 çözüm:** (1) kök `AGENTS.md` yok → kök `AGENTS.md` yazılır veya `.ai/AGENTS.md`'ye yönlendirici → **şart 1a** · (2) `.ai/security/` yanlış referans → dizin oluş veya `k6-guvenlik`'e çevir → **şart 1b** · (3) 178 `CLAUDE.md` denetimsiz → link/şablon denetimi CI'a bağlanır → **şart 2** · (4) eksik 2 script → `session-save.mjs` + `vault-post-update.mjs` yazılır → **şart 3** |
| Tur 3 | Oy | ✅ | **18 kabul / 2 çekimser / 0 red → KABUL** (3 şart bağlayıcı: 1a-1b · 2 · 3 → §5.5) |
| **Toplam** | **3 tur / 20 persona** | **✅ TAMAMLANDI** | **18/2/0 KABUL** — şartlar §5.5, Tech Lead §7 ✅ (2026-09-25) |

### 5.4 Açık PLANNED Kalemleri (kabul ≠ tamamlandı)

| Kalem | Durum | Kapanış |
|---|---|---|
| `session-save.mjs` · `vault-post-update.mjs` | ❌ PLANNED (diskte 0) | §5.1/1-2 |
| Denetim 2 (şablon uyumu) · Denetim 3 (stale) | ❌ PLANNED | §5.1/7 |
| 136 kod-klasörü `CLAUDE.md` mükerrer taraması | ⚠️ VERIFICATION REQUIRED | §5.1/4 |
| Kök `AGENTS.md` | ❌ YOK | §5.1/6 |
| Debate 3/20 · Tech Lead onayı | ✅ TAMAMLANDI (18/2/0 KABUL + 3 şart) | §5.3, §5.5, §7 |

### 5.5 Debate Şartları (bağlayıcı — 3 madde / 4 alt şart)

> Debate 3 tur / 20 persona sonucu **18 kabul / 2 çekimser / 0 red = KABUL**; kabul **aşağıdaki 3 şartın yerine getirilmesine bağlıdır** (şartlar §5.1 adımlarıyla eşleştirilmiştir; tamamlanmadan madde (b)/(c) "IMPLEMENTED" sayılmaz).

| # | Şart | Kapsam | Sahip | Süre | §5.1 karşılığı |
|---|------|--------|-------|------|---------------|
| **1** | Kök `AGENTS.md` + doğru security referansı | **1a:** kök `AGENTS.md` **YOK** → kök `AGENTS.md` yazılır veya kök `CLAUDE.md`'ye `.ai/AGENTS.md`'ye yönlendirici link konur · **1b:** `.ai/security/` **YOK** (içerik `architecture/k6-guvenlik/` altında) → vault'taki yanlış `.ai/security/` referansları ya `.ai/security/` dizinini oluşturur ya `[[../../architecture/k6-guvenlik]]`'e çevrilir (dosya adı değişmez — In-Place Refactoring) | Vault Steward | 0.5 gün | §5.1/6 (1a) + §5.1/10 (1b) |
| **2** | Otomatik denetim CI bağlantısı + 34 kırık link başlangıç temizliği | Link/şablon denetimi `vault-post-update.mjs` → `ci.yml` bağlanır (`--dry-run`, ADR-023 gate'i ile aynı hat ayrı job) **ve** `broken-links-report.md`'deki **34 kırık link / 5 dosya** başlangıç temizliği yapılır (repoint / dead-mark / hedef üret) | DevOps + Vault Steward | 3 gün | §5.1/5 + §5.1/7 + §5.1/8 |
| **3** | 2 eksik script yazımı | `.ai/scripts/session-save.mjs` + `.ai/scripts/vault-post-update.mjs` yazılır (arayüz §2.2d; diskte 0 → 5 `log.md` "ÇALIŞTIRILAMADI" kaydı kapanır) | MO (vault-updater) | 1-2 gün | §5.1/1-2 |

**Doğrulama (şart kapanışı):** (1a) `Test-Path AGENTS.md = True` veya kök `CLAUDE.md`'de yönlendirici link · (1b) `.ai/security/` referansı 0 **veya** dizin var · (2) `ci.yml`'de denetim adımı + `broken-links-report` başlangıç 34 → kapatma takvimi · (3) `node .ai/scripts/session-save.mjs --help` + `vault-post-update.mjs --help` exit 0 → hepsi `vault-utf8-writer scan` (mojibake 0) sonrası `log.md`'ye append edilir.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme — 16 Guardrail (bu ADR madde (b)'nin kaynağı) |
| [[../../AGENTS.md]] | Agent registry SSOT §5 domain boundary (sahip eşlemesi), §25.3 kural 3 (log append-only), §26.2 SSOT hiyerarşisi, §3 Stack Etiketi (IMPLEMENTED/PLANNED) |
| [[../../WORKFLOW.md]] | Süreçler — post-op sync akışının bugünkü tanımı (manuel ⚠️) |
| [[../../index]] | Master katalog — boot listesi + envanter |
| [[../../brain]] | Mimari karar özeti (bu ADR'nin özeti buraya türetilir) |
| [[../../glossary]] | Terim sözlüğü — SSOT/drift/şablon terimleri için |
| [[../../engine]] | Orkestrasyon motoru §9.2 (`:382`) — IMPLEMENTED/PLANNED matrisi |
| [[../../.templates/index]] | Şablon registry (36/36) — denetim 2'nin referansı |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin zorunlu şablonu (Guardrail #16) |
| [[../../.agents/AGENTS]] | Rol profilleri indeksi (alt registry) |
| [[../../scripts/index]] | Script envanteri — `session-save`/`vault-post-update` "?? YOK kaydı" bu ADR ile kapanır |
| [[../../broken-links-report]] | Link denetimi çıktısı (34 kırık link / 5 dosya, 2026-09-24) |
| [[../../reports/broken-files-report]] | Kod/dosya bulguları (39 bulgu, 2026-09-23) |
| [[../../architecture/k6-guvenlik/CLAUDE]] | Güvenlik katmanı — `.ai/security/` YOK; **şart 1b**'nin doğru referans hedefi (§5.5/1b) |
| [[../../../CLAUDE.md]] | Kök yönlendirici (yönlendirici katman — §2.2a) |
| [[../../../WORKFLOW.md]] | Kök süreç yönlendiricisi |
| [[../../../shared/CLAUDE.md]] | Domain modülü örneği (8.742 B — kısa + merkeze link hedefi) |
| [[../../../auth.coremusic.net/CLAUDE.md]] | Domain modülü örneği (9.433 B) |
| [[../../../home.coremusic.net/CLAUDE.md]] | Domain modülü örneği (7.215 B) |
| [[../../../assets.coremusic.net/CLAUDE.md]] | Domain modülü örneği (11.032 B) |
| [[../index]] | Karar dizini — satır 61 `[[ADR-024-ecosystem-modular-docs]]` (slug ✅) |
| [[ADR-005-ultrathink-protocol]] | Kanıt standardı (`⚠️ VERIFICATION REQUIRED`) |
| [[ADR-014-multi-db-migration-strategy]] | Tek SSOT / mükerrer bilgi yok düzeltme ruhu |
| [[ADR-015-env-parser-strategy]] | Stale/çelişki örneği (§1.1-E) |
| [[ADR-017-dsp-hardware-mode]] | Stale iddia örneği — "workflows = 0" (§1.1-E) |
| [[ADR-023-persona-driven-testing]] | Ölçülebilir denetim disiplini + CI gate eşleşmesi |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-024'ü sıfırdan yaz"; karar içeriği onaylı: vault-merkezli modüler yerleşim + tek sahip/tek SSOT · şablon + üç kollu otomatik denetim · `session-save.mjs` + `vault-post-update.mjs` bu kararın parçası · UTF-8 zorunlu) | 2026-09-25 | ✅ |
| Tech Lead | Debate 3/20 — **18 kabul / 2 çekimser / 0 red → KABUL** (3 şart §5.5: 1a-1b kök `AGENTS.md` + security referansı · 2 CI denetimi + 34 link · 3 iki script) | 2026-09-25 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate ve Onay Notu

**Debate: ✅ TAMAMLANDI (2026-09-25)** — 3 tur / 20 persona: **Tur 1** 20 persona bulgu (kök `AGENTS.md` YOK · 178 `CLAUDE.md`/136 kod klasörü · `.ai/` 34 `CLAUDE.md` · 36 şablon · 4+2 script · 34 kırık link · `.ai/security/` YOK · `.ai/AGENTS.md` v22.0.3 → 15 kabul/neutral + 4 uyarı, Critic: 2 şart) · **Tur 2** 4 itiraz→çözüm → şart 1a/1b/2/3 · **Tur 3** oy: **18 kabul / 2 çekimser / 0 red = KABUL**. Kayıt: §5.3 (turlar) · **§5.5 (3 bağlayıcı şart + doğrulama)** · bu satır. **Tech Lead: ✅ (2026-09-25)** — şartlar §5.1/5-8 + §5.1/10 adımlarıyla takip edilir. **Arch Lead: ⏳** — 3 şart kapanıp Tech Lead ✅ sonrası.

---

**REFACTOR REPORT:** FILE: ADR-024-ecosystem-modular-docs.md · PURPOSE: Vault-merkezli modüler dokümantasyon + tek sahip/tek SSOT + şablon/otomatik denetim + eksik post-op scriptler · VALIDATION: 7 bölüm + §1.3 9 alan (7 sorgu / ~47 kaynak) + frontmatter 7 alan + IMPLEMENTED/PLANNED etiketleri + wiki-link disk kanıtı · RELATED: [[../index]] · [[../../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../.templates/adr/adr-template]]

---

**Template Version:** 1.0.0 (adr-template.md v2.0.0 iskeleti — §1-§7)
**Last Updated:** 2026-09-25
**Mode:** Red Team · Human Mode · Truth Mode
