---
node_id: permissions-anayasa
node_type: module
status: active
parent_id: permissions
children: []
depends_on: [permissions-index]
evidence:
  - ".ai/AGENTS.md §5 + §17-§18 (domain boundary + ihlal davranışı, okundu 2026-10-17)"
  - "root AGENTS.md §16 (dokunulmaz yüzeyler + commit yetkisi)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# permissions-anayasa - Ajan Yetki Kuralları

> **Bu dosya ne değildir:** ajan yetkisinin **aracının** kendisi (Claude Code / OpenCode `permissions`
> bloğu, hook betiği, ACL kodu) ve `.claude/settings.json` içeriğinin **tam kopyası** değildir; burada
> **yetki anayasası** vardır — hangi ajan hangi yüzeye yazar, hangi yüzey salt-okunur, ihlalde ne olur,
> kim kilit kırar. Ajan registry'nin tam yetki metni `.ai/AGENTS.md` §5/§17/§18'dedir (SSOT); bu dosya
> o metnin K015-AI altındaki anayasal özetidir ve **üçüncü bir SSOT açmaz** — çelişkide disk kazanır.

## §1 Rol (ELI15)

Permissions alt-domaini, CoreMusic ajan ekosisteminde **"kim neye dokunabilir?"** sorusunun tek yerden
cevaplanmasını sağlayan yetki matrisinin sahibidir. Yetki üç katmandan oluşur: (1) **domain boundary** —
dosya tipi → sorumlu ajan eşlemesi; (2) **yazma beyazlistesi** — bu üretimde yalnız `K015-AI/**/*.md`;
(3) **ihlal davranışı** — revert, log, sistem durdurma. Dosya bir süreç dosyası değildir; adım tarif
etmez, yalnız sınır koyar ve durum raporlar.

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Bu dosyanın rolü ne? | Yetki matrisi + ihlal davranışı anayasası | bu FM · `.ai/AGENTS.md:93-108` |
| Kim uygular? | Tüm ajanlar; müdahele yetkisi MO'da | `.ai/AGENTS.md:217` (§18 #1) |
| Bugünkü yazma izni nedir? | Yalnız `K015-AI/**/*.md` | bu üretim görev tanımı |
| Salt-okunur yüzey nedir? | `shared/src/**` · `.ai/*.md` · `.claude/**` · `.ai/scripts/**` | §6 bu dosyada |
| Yetki kim verir? | Kullanıcı onayı + vault kuralı (Faz 1 kapısı) | kök `AGENTS.md:44` |
| İhlalde ne olur? | Layer → revert + log; domain → sistem durur | `.ai/AGENTS.md:207` · `:217` |

> **eli10:** Bu dosya, her ajanın elindeki kalemi hangi kâğıtta kullanabileceğini yazar.
> **eli15:** Yetki ayrı yazıldı çünkü güvenlik (secret) ve auth (giriş) farklı sorulardır; aynı tabloda
> toplansa "giriş yapabiliyorum" → "her dosyayı yazabilirim" hatası doğar.

## §2 Kapsam ve Sınır — yetki ≠ güvenlik ≠ auth

| Kavram | Ne sorar | Kapsam dışı olan | Örnek |
|--------|----------|-------------------|-------|
| **Yetki (permission)** | Hangi dosyaya/klasöre YAZABİLİRİM? | Kimlik doğrulama, şifreleme | `K015-AI/**/*.md` yazılır; `.claude/**` yazılmaz |
| **Güvenlik (security)** | Secret sızar mı, ihlal edilir mi? | Dosya sahipliği | `EXA_API_KEY` → REDACTED (§9) |
| **Auth (authentication)** | Bu ajan/kullanıcı kim? | Dosya yetkisi | oturum / rol doğrulama (repo: `AuthMiddleware`) |
| **Authz (authorization)** | Bu rolün bu işlem yetkisi var mı? | Dosya glob'u | repo: `PermissionMiddleware` (§21) |

| Kapsam | Kapsam Dışı | Kanıt |
|--------|-------------|-------|
| Ajan → dosya yüzeyi yetki matrisi (§5 registry) | Kullanıcı auth/RBAC akışının tasarımı | `.ai/AGENTS.md:60-65` (§2 Scope) |
| Bu üretim: yazma beyazlistesi + salt-okunur yüzeyler | Hook/izin motorunun uygulama kodu | bu FM |
| İhlal davranışı + escalation + lock yetkisi | Secret değeri üretimi / rotasyonu | `.ai/AGENTS.md:195-225` |
| `settings.json` **izin yüzeyinin** künyeli alıntısı | Dosyanın tamamının kopyası | `.claude/settings.json` (okundu 2026-10-17) |

**Sınır kuralı:** bu anayasa yalnız **ajan dosya yetkisini** bağlar; kullanıcı/RBAC yetkisi ayrı alandır
(`shared/src/Middleware/PermissionMiddleware.php` — §21). İki alan aynı tabloda birleştirilmez.

## §3 Yetki Modeli — dosya tipi → sorumlu ajan (registry §5)

Registry §5, 12 dosya tipi tanımlar; **sorumlu ajan yazma yetkisinin sahibidir**, diğer ajanlar okur ve
gerekirse eşlik eder (sütun "Diğerleri Erişebilir mi?" ✅ = erişim/söz hakkı, **yazma tekelini silmez**).

| # | Dosya Tipi | Sorumlu Agent | Diğerleri | Bu anayasadaki yer |
|---|------------|---------------|-----------|--------------------|
| 1 | `*.php` (Controller, Service, Repository) | Backend Architect | ✅ | yetki tekelini bozma — §4 |
| 2 | `*.js` (Frontend) | UI Designer | ✅ | salt-okunur bu üretimde — §5 |
| 3 | `*.css` (ITCSS layers) | UI Designer | ✅ | salt-okunur bu üretimde — §5 |
| 4 | `*.sql` (Schema, Migration) | Data Engineer | ✅ | salt-okunur bu üretimde — §5 |
| 5 | `*.cpp` / `*.h` (Audio Engine) | Embedded Engineer | ✅ | salt-okunur bu üretimde — §5 |
| 6 | `*.yml` / `*.yaml` (CI/CD) | DevOps Engineer | ✅ | salt-okunur bu üretimde — §5 |
| 7 | `tests/**/*.php` | QA Engineer | ✅ | salt-okunur bu üretimde — §5 |
| 8 | `tests/**/*.test.js` | QA Engineer | ✅ | salt-okunur bu üretimde — §5 |
| 9 | Security middleware | Security Engineer | ✅ | salt-okunur bu üretimde — §5 |
| 10 | `.env` dosyası | Security Engineer | ✅ | secret sınıfı — §9 |
| 11 | `log.md` (audit trail) | Tüm ajanlar (append-only) | ✅ Sadece ekleme | append-only — §26 |
| 12 | `.ai/` vault | MO (koordinasyon) | ✅ Okuma serbest | bu üretim istisnası — §5 |

**Kaynak künyesi:** `Dosya: .ai/AGENTS.md · Satır: 93-108` (§5 Domain Boundaries — 12 satır; kalite
raporu aynı sayıyı "Domain Boundaries: 12 dosya tipi" diye taşır, `Dosya: .ai/AGENTS.md · Satır: 238`).

## §4 Uzatılmış Ehliyet Matrisi — ajan → bu üretimde ne yapabilir

Domain boundary (§3) **dosya tipi**ni; bu matris ise **bu görevin yüzeyini** tanımlar. Bir ajan, kendi
domaininde ehil olsa bile **bu üretimde** yalnız beyazliste dışına çıkamaz — ehliyet ile izin ayrıdır
(yetki ≠ ehliyet: ehil olmak yazma izni vermez).

| # | Ajan / Rol | Ehil olduğu yüzey | Bu üretimde YAZABİLİR | Kaynak |
|---|------------|-------------------|------------------------|--------|
| 1 | MO (Master Orchestrator) | `.ai/` vault koordinasyonu, `log.md` | commit + vault senkronu | `.ai/AGENTS.md:108` · `:161` |
| 2 | Backend Architect | `*.php` | hayır (salt-okunur) | `.ai/AGENTS.md:97` |
| 3 | UI Designer | `*.js` · `*.css` | hayır (salt-okunur) | `.ai/AGENTS.md:98-99` |
| 4 | Data Engineer | `*.sql` | hayır (salt-okunur) | `.ai/AGENTS.md:100` |
| 5 | Security Engineer | security middleware · `.env` | hayır (salt-okunur + secret) | `.ai/AGENTS.md:105-106` |
| 6 | DevOps Engineer | `*.yml` / `*.yaml` | hayır (salt-okunur) | `.ai/AGENTS.md:102` |
| 7 | QA Engineer | `tests/**` | hayır (salt-okunur) | `.ai/AGENTS.md:103-104` |
| 8 | Bu üretimdeki subagent (developer) | — | **yalnız `K015-AI/**/*.md`** | bu FM §5 |
| 9 | Kullanıcı (Bayram Ali) | tüm | evet (onay makamı) | kök `AGENTS.md:44` |

**Kural:** ehil ajan ≠ yetkili ajan. Domain ehliyeti görev dağıtımını (§6 routing) belirler; dosya yazma
izni bu anayasanın beyazlistesiyle belirlir ve dar olan kazanır (least privilege — §18, §29 kaynak 9).

## §5 Yazma Beyazlistesi — bu üretim: `K015-AI/**/*.md`

Beyazliste **daraltıcıdır**: listeyendekinin hepsi yazılabilir; listeyendekinin **hepsi salt-okunurdur**
(deny-by-default — §18). Bu üretim tek dosya yazım yetkisiyle verilmiştir.

| # | Beyazliste kalıbı | Eşleşen | Durum | Kanıt |
|---|-------------------|---------|-------|-------|
| 1 | `.ai/architecture/K015-AI/**/*.md` | hedef: `permissions/permissions-anayasa.md` | ✅ YAZILABİLİR | bu üretim görevi |
| 2 | K015-AI dışı `.md` (ör. `.ai/RAG.md`, kök `AGENTS.md`) | hayır | ❌ salt-okunur | §6 |
| 3 | K015-AI içi `.md` OLMAYAN (json/sql/php/css) | hayır | ❌ salt-okunur | §6 · kök `AGENTS.md:270` |
| 4 | `.claude/**` · `.ai/*.md` (kök) · `shared/src/**` | hayır | ❌ salt-okunur | §6 tablosu |
| 5 | `git commit` | — | ❌ subagent yasak | §8 |

**Tespit edilen tek yazım hedefi (bu üretim):** `.ai/architecture/K015-AI/permissions/permissions-anayasa.md`
— beyazlistenin içindedir. Beyazliste dışı her yazma denemesi **ihlaldir** ve §13/§14 davranışını tetikler.

**Genişletme kuralı:** beyazliste yalnız **kullanıcı onayı**yla genişletilir; ajan kendi beyazlistesine
satır ekleyemez (kendi yetkisini genişletmek fail-open'dır — §18'de yasaktır).

## §6 Salt-Okunur Yüzeyler — hangisi neden

| # | Yüzey | Neden salt-okunur | Kanıt |
|---|-------|-------------------|-------|
| 1 | `shared/src/**` | PHP/JS/CSS kaynak kod — bu revizyon Markdown dışı **yazmaz**; domain sahibi Backend/Security/Data | kök `AGENTS.md:270` (§16.4 #4) |
| 2 | `.ai/*.md` (vault kök boot dosyaları) | `.ai/` vault sahipliği MO'da; okuma serbest, yazma koordinasyonlu | `.ai/AGENTS.md:108` |
| 3 | `.claude/**` | İzin/MCP/secret yüzeyi — onaysız değişiklik güvenlik sınırını oynatır | `.claude/AGENTS.md` §3.3 #2 |
| 4 | `.ai/scripts/**` | Doğrulama betikleri; yalnız repo **kökünden** çalıştırılır, düzenlenmez | `.ai/AGENTS.md:144` (§13.6) |
| 5 | `.ai/.png/**` | Salt-okunur SSOT (görsel kanıt) | kök `AGENTS.md:270` (§16.4 #4) |
| 6 | frozen ADR'ler | Metin okunur/referans edilir — değiştirilmez (revert) | kök `AGENTS.md:270` · `.claude/AGENTS.md` §3.3 #8 |
| 7 | `*.php` / `*.js` / `*.css` / `*.sql` | Kaynak kod yüzeyi (diğer agent'ın domaini — §3) | kök `AGENTS.md:270` |
| 8 | `log.md` | **append-only** — geçmiş satıra dokunulmaz | `.ai/AGENTS.md:107` · §25.3 #3 |
| 9 | `CLAUDE.md` (kök + `skills/**`) | Dokunulmaz — düzeltme yalnız vault-sync | `.claude/AGENTS.md` §3.3 #3 |

**Okuma serbest, yazma yasak** ayrımı: salt-okunur = read ✅ / write ❌. Okuma yasağı yalnız **secret**
sınıfındadır (§9) ve o da değer düzeyindedir, dosya düzeyinde değil.

## §7 Dokunulmaz Yüzeyler — kök §16.4 (değiştirilemez kullanıcı kuralları)

Kullanıcı kaydolmuş kurallar bağlayıcıdır; ajan bunları "iyileştirme" adına bile değiştirmez.

| # | Dokunulmaz kural | İhlalde | Kanıt |
|---|------------------|---------|-------|
| 1 | Widget grid kanonik kuralı (1024: 2×2/1×5/1×5 · 1920: 4×4/1×8/1×8) | sayım/render revize edilir, kural değil | kök `AGENTS.md:267` |
| 2 | `ui-design/screens/T07-embedded/` dizin adı DEĞİŞTİRİLMEZ (~489 wiki-link) | kırık linkler | kök `AGENTS.md:268` |
| 3 | `FIGMA_TOKEN` / `FIGMA_FILE_KEY` yalnız `.ai/.env.figma`'dan; anahtar `.md`/`.json`/`.log`a yazılmaz | anahtar iptali + log ERROR | kök `AGENTS.md:269` |
| 4 | `.ai/.png/**` · frozen ADR'ler · `*.php/*.js/*.css/*.sql` yazmaz | revert | kök `AGENTS.md:270` |
| 5 | Yeni `.md` şablonsuz üretilmez (Guardrail #16); **commit subagent'ta değil** | üretim reddedilir | kök `AGENTS.md:271` |

**İlişki:** bu anayasanın beyazlistesi (§5) §16.4 ile **çelişmez** — §16.4 genel dokunulmazlığı, §5 bu
görevin dar iznini tanımlar; dar olan kazanır. Her ikisi de aynı sonuca varır: kaynak kod ve `.ai/` kökü
bu üretimde yazılmaz.

## §8 Commit Yetkisi — subagent ATMAZ

| # | Kural | Gerekçe | Kanıt |
|---|-------|---------|-------|
| 1 | `git commit` subagent tarafından **ATILMAZ** — orkestratöre aittir | Tarih/entegrasyon tek elden | kök `AGENTS.md:271` (§16.5 #5) |
| 2 | Execution loop adım 9: "Commit (subagent ATMAZ — orkestratöre aittir)" | Süreç tekliği | kök `AGENTS.md:199` |
| 3 | `.claude/` yetki sınırı #6: "Subagent `git commit` ATMAZ" → ihlal: yetkisiz commit → orkestratör | Onaysız tarih kirlenmesi | `.claude/AGENTS.md` §3.3 #6 |
| 4 | Registry Faz 7 kaydı: "commit'i orchestrator atar, subagent atmaz" | Faz kanıtı bütünlüğü | `.ai/AGENTS.md:161` |
| 5 | Çalışma akışı: `... → ÖLÇ+VERIFY → RAPOR (log append; commit ORCHESTRATÖRDE)` | Ölçüm → rapor → commit sırası | `.claude/AGENTS.md` §5 |
| 6 | `log.md` **append-only** — commit beklerken kayıt yine yazılır | Kayıp geçmiş önlenir | `.ai/AGENTS.md:107` |

**Beyaz liste (subagent'ın commit OLMAYAN eylemleri):** dosya yazımı (§5 beyazlistesi) · ölçüm · rapor ·
`log.md` append. **Kırmızı liste:** `git commit` · `git push` · `git checkout/reset` · deploy · secret
yazımı — bunlar ya onay ister ya orkestratöre gider.

## §9 Secret Erişim Kuralları — REDACTED

| # | Bulgu / Kural | Değer | Davranış | Kanıt |
|---|---------------|-------|----------|-------|
| 1 | **Düz metin `EXA_API_KEY` bulgusu** | `.claude/settings.json` içinde `env` bloğunda düz metin anahtar | **REDACTED** — değer hiçbir `.md`'ye yazılmaz | `Dosya: .claude/settings.json · Satır: 88-98` (anahtar satırı 95) |
| 2 | Sensitive data log'da | `[REDACTED]` ile maskeleme | log'a değer kopyalanmaz | `.ai/AGENTS.md:200` (§17 #3) |
| 3 | Anahtar `.md` / `.log` / `.json` içine yazılmaz | — | ihlal: anahtar iptali + log ERROR | kök `AGENTS.md:269` · `.claude/AGENTS.md` §3.3 #1 |
| 4 | Figma token SSOT | yalnız `.ai/.env.figma` (`.gitignore:69`) | betikte sabit anahtar yok | kök `AGENTS.md:269` |
| 5 | Vault `.md`'lerine secret | hiçbir satıra girmez | `REDACTED` (RAG anayasası §15 #5) | `.ai/RAG.md:137` atfı |
| 6 | `.env` sahipliği | Security Engineer | ajan `.env` okuyup özetlemez | `.ai/AGENTS.md:106` |

**Bu anayasada geçen anahtar değeri:** YOK — bulgu **dosya + satır künyesiyle** taşınır, değer asla yazılmaz.
İhlalde sır değil, **sonuç** yazılır: `secret yazımı → REDACTED + iptal önerisi + log ERROR (Security)`.

## §10 `settings.json` İzin Yüzeyi (gerçek alıntı — secret DEĞİL)

Aşağıdaki blok dosyanın **gerçek satırlarıdır**; kopyalama amaçlıdır, izin/hooks yüzeyini gösterir.

```json
// Dosya: .claude/settings.json · Satır: 13-25 (PreToolUse kapısı — gerçek alıntı)
"PreToolUse": [
  {
    "matcher": "Edit|Write",
    "hooks": [
      {
        "type": "command",
        "command": "node \"${CLAUDE_PROJECT_DIR}/.claude/hooks/ai-risk-gate.cjs\"",
        "timeout": 15,
        "statusMessage": "Control Plane HIGH-RISK kapısı (T1/T2)"
      }
    ]
  }
]
```

```json
// Dosya: .claude/settings.json · Satır: 40-47 (boot instructions — gerçek alıntı)
"instructions": [
  "CLAUDE.md",
  ".ai/CLAUDE.md",
  ".ai/AGENTS.md",
  ".ai/WORKFLOW.md",
  ".ai/CONTEXT.md",
  ".ai/architecture/context.md"
]
```

**Secret notu:** aynı dosyanın `mcpServers.exa.env.EXA_API_KEY` satırı **REDACTED**'dir
(`Dosya: .claude/settings.json · Satır: 95` — değer yazılmaz). Alıntı kapsamı bu iki blokla sınırlıdır;
dosyanın tamamı bu anayasanın **kopyası değildir** (§2 sınırı).

## §11 Hook Kapıları — yazma öncesi/sonrası denetim

| # | Hook | Tetikleyici | Ne yapar | Kanıt |
|---|------|-------------|----------|-------|
| 1 | `UserPromptSubmit` → `skill-mandate.cjs` | her prompt | skill registry zorunluluğu | `Dosya: .claude/settings.json · Satır: 3-12` |
| 2 | `PreToolUse` (`matcher: Edit\|Write`) → `ai-risk-gate.cjs` | dosya yazımı **öncesi** | HIGH-RISK kapısı (T1/T2), timeout 15s | `Dosya: .claude/settings.json · Satır: 13-25` |
| 3 | `PostToolUse` (`matcher: Edit\|Write`) → `ai-validate-post.cjs` | dosya yazımı **sonrası** | fm/link/ssot/dep/tier/orphan doğrulama, timeout 60s | `Dosya: .claude/settings.json · Satır: 26-38` |
| 4 | Sıra kuralı | — | gate (öncesi) → yaz → validator (sonrası) — atlanamaz | satır 13 → 26 sırası |
| 5 | İhlal | validator başarısız | dosya geçersiz sayılır; §13 davranışı | `.claude/AGENTS.md` §6 #3 |

**Anayasal okuma:** hook'lar yetkinin **motoru**dur; bu dosya hook **içeriğini** değil, hangi kapının
neden var olduğunu yazar. Motor değiştirilemez (`.claude/**` salt-okunur — §6 #3), yalnız okunur.

## §12 Deny > Ask > Allow — izin sıralaması (web doğrulamalı)

Resmi Claude Code izin sisteminde kural sırası **önce `deny`, sonra `ask`, sonra `allow`**dur ve ilk
eşleşen kural kararı verir; bir seviyedeki `deny`, hiçbir seviyedeki `allow` ile aşılamaz (kaynak §29 #11).

| # | Kural sınıfı | Davranış | Bu anayasadaki karşılığı |
|---|--------------|----------|---------------------------|
| 1 | `deny` | aracı hiçbir şekilde çalıştırmaz | salt-okunur yüzeyler (§6) |
| 2 | `ask` | kullanıcı onayı ister | onay kapıları (kural değişikliği, §4 #9) |
| 3 | `allow` | onaysız çalıştırır | beyazliste `K015-AI/**/*.md` (§5) |
| 4 | Sıra | deny → ask → allow; ilk eşleşme kazanır | dar olan kazanır (§4) |
| 5 | Yönetilen (managed) seviye | hiçbir seviye aşamaz | SSOT çelişkisi: disk/kural kazanır (§19) |

**Yerel karşılık (kanıtlanan boşluk):** bu repo'nun `.claude/settings.json`'ında **`permissions` bloğu
yoktur** (okuma 2026-10-17 — dosya `hooks/instructions/mcpServers/watcher/references/enabledPlugins`
içerir) → izin kuralları dosya bazında `UNKNOWN`; davranış hook kapıları (§11) + kural metinleriyle
sınırlıdır. `⚠️ VERIFICATION REQUIRED`: `settings.local.json` içeriği bu üretimde okunmadı (varlığı
glob 2026-10-12 kanıtlı — `permissions/index.md:11`).

## §13 Yetki İhlali Davranışı — Layer Violation → revert + log

| # | İhlal türü | Davranış | Log seviyesi | Kanıt |
|---|------------|----------|--------------|-------|
| 1 | **Layer violation** (L0→L2/L3, L1→L3, L3→L0) | **derhal revert** | `log CRITICAL` | `.ai/CLAUDE.md:184` |
| 2 | Layer violation (edge-case kaydı) | derhal revert | `log ERROR` | `.ai/AGENTS.md:207` (§17 #7) |
| 3 | Layer violation (warning kaydı) | kod revert edilir | — | `.ai/AGENTS.md:223` (§18 #6) |
| 4 | Pre-flight: domain boundary yanlış ajan | `Layer violation → revert` | — | `.ai/AGENTS.md:272` (§7.2) |
| 5 | Beyazliste dışı yazma (bu anayasa) | revert + ihlal kaydı | `log ERROR` | §5 + bu satır |
| 6 | Dokunulmaz yüzey yazımı | derhal geri al | bildirim | `.claude/AGENTS.md` §3.3 #2/#3/#8 |

**Çelişki notu (dürüstlük):** revert davranışı üç yerde aynıdır; **log seviyesi** iki farklı geçiyor
(`CRITICAL` — `.ai/CLAUDE.md:184` · `ERROR` — `.ai/AGENTS.md:207`). Bu anayasa **ikisini de taşır**,
biriyle diğerini silmez; uygulamada **CRITICAL** (yüksek) kazanır, çünkü `.ai/CLAUDE.md` anayasadır
(vault otorite sırası: CLAUDE > AGENTS — `.ai/CLAUDE.md` §2.1).

## §14 Domain İhlali — sistem durur

| # | Uyarı | Sonuc | Kanıt |
|---|-------|-------|-------|
| 1 | **Domain boundary ihlali** | **Sistem durur, MO müdahale eder** | `.ai/AGENTS.md:217` (§18 #1) |
| 2 | Agent timeout 30s+ | Max 3 retry, sonra escalation | `.ai/AGENTS.md:218` |
| 3 | Hallüsinasyon | `VERIFICATION REQUIRED` etiketi | `.ai/AGENTS.md:219` |
| 4 | Token overflow | Görev başarısız | `.ai/AGENTS.md:220` |
| 5 | Vault bozulması | Git ile kurtarma (`git checkout` + son commit) | `.ai/AGENTS.md:221` · `:208` |
| 6 | Layer violation | Kod revert edilir | `.ai/AGENTS.md:223` |

**Öncelik ayrımı:** layer violation → **geri alma** (devam edilebilir); domain boundary ihlali →
**durma** (MO müdahalesi beklenir). İkisini aynı tepki sanmak tehlikelidir: durma, kurtarıcı revert'ın
yerine geçmez; revert edilen kod yine loglanır (§13 #1).

**MO müdahale biçimi:** görev iade edilir → doğru domain ajana yeniden dağıtılır (routing §6) →
handover onayı alınır (`.ai/AGENTS.md:346-355` §9.2) → doğrulama (§7.6) → devam.

## §15 Escalation Yetkileri

| # | Seviye | Kim | Timeout | Max retry | Kanıt |
|---|--------|-----|---------|-----------|-------|
| 1 | L1 | Domain Lead (uzman ajan) | 30s | 3 | `.ai/AGENTS.md:394-397` (§10.2) |
| 2 | L2 | Tech Lead | 60s | 3 | `.ai/AGENTS.md:395-397` |
| 3 | L3 | Arch Lead | 120s | 3 | `.ai/AGENTS.md:396-397` |
| 4 | Son | **İnsan** — son çare | — | — | `.ai/AGENTS.md:398` |
| 5 | CRITICAL öncelik | timeout 5s, retry 1, yanıt anlık | 5s | 1 | `.ai/AGENTS.md:320` (§8) |
| 6 | Handover reddi | MO devreye girer; tüm handover `log.md`'ye yazılır | 30s | 3 | `.ai/AGENTS.md:351-354` |

| Senaryo | Başlangıç → Hedef | Timeout | Kanıt |
|---------|-------------------|---------|-------|
| Güvenlik açığı | L2 → L3 | 15s | `.ai/AGENTS.md:388` |
| Sistem durması | L2 → **İnsan** | anlık | `.ai/AGENTS.md:389` |
| Mimari çelişki (ADR) | L2 → L3 | 60s | `.ai/AGENTS.md:387` |
| Aynı dosyaya erişilemiyor (lock) | L1 → L2 | 30s | `.ai/AGENTS.md:381` |

**Yetki sınırı:** escalation **yetki devridir, yetki artışı değil** — üst seviye daha geniş **dosya**
yazmaz; yalnız karar/onay makamı yükselir (beyazliste §5 aynen kalır).

## §16 Context Lock Yetkisi — eşzamanlı erişim

| # | Kural | Değer | Kanıt |
|---|-------|-------|-------|
| 1 | Aynı dosyaya eşzamanlı erişim | Context Lock + Queue | `.ai/AGENTS.md:199` (§17 #1) |
| 2 | Kilitleme süresi | **Max 30 saniye** | `.ai/AGENTS.md:405` (§12.1) |
| 3 | Deadlock prevention | MO en eski kilidi kırar | `.ai/AGENTS.md:406` |
| 4 | Öncelik | CRITICAL > HIGH > MEDIUM > LOW | `.ai/AGENTS.md:407` |
| 5 | Logging | Lock acquire/release `log.md`'ye yazılır | `.ai/AGENTS.md:408` |
| 6 | Başarısız acquire | kuyruk → öncelik sırası → timeout → escalation | `.ai/AGENTS.md:414-420` (§12.2) |

**Lock yetkisi kimde:** kilidi **talep eden ajan** alır (dosya yazma yetkisi olan — §5); kilidi **kran**
yalnız §17'deki üç yetkiyle kırılır. 30s dolan kilit otomatik serbesttir — uzatma yetkisi **kimsede yoktur**.

## §17 Deadlock Kırma Yetkileri (§12.3)

| # | Yöntem | Açıklama | Yetkili | Kanıt |
|---|--------|----------|---------|-------|
| 1 | Timeout | Max 30s sonra lock serbest | sistem (otomatik) | `.ai/AGENTS.md:425` |
| 2 | **Priority override** | **CRITICAL diğer kilidi kırar** | CRITICAL görev sahibi | `.ai/AGENTS.md:426` |
| 3 | **MO intervention** | **MO en eski kilidi kırar** | Master Orchestrator | `.ai/AGENTS.md:427` |
| 4 | Queue reset | Tüm kilitler sıfırlanır | MO (senkron kapanış) | `.ai/AGENTS.md:428` |

**Kırma sırası:** (1) 30s timeout bekle → (2) CRITICAL ise priority override uygula → (3) hâlâ kilitliyse
MO'ya raporla, MO en eski kilidi kırar → (4) çelişki sürerse queue reset + escalation (§15).

**Kayıt zorunlu:** her kırma `log.md`'ye **append** olarak yazılır (append-only — §6 #8); kilit kırma
"sessiz yetki" değildir, denetlenebilir olmalıdır. Subagent **kendi kilidini kıramaz** (kendi ihlali,
§13 #5).

## §18 Güvenlik — yetki fail-safe (deny-by-default)

| # | İlke | Uygulama | Kaynak |
|---|------|----------|--------|
| 1 | **Fail-safe / deny by default** | yetki verilmemişse → **RED**, "belki çalışır" yok | OWASP Authorization Cheat Sheet (§29 #10) |
| 2 | Least privilege | yalnız görev için gereken yüzey | NIST SP 800-53 **AC-6** (§29 #9) |
| 3 | Deny > ask > allow | deny hiçbir allow'la aşılmaz | Claude Code docs (§29 #11) |
| 4 | Fail-open yasak | yetki escalation ile **genişlemez** (§15) | bu FM §4/#18 |
| 5 | Onay kapısı | ayar/izin değişikliği = kullanıcı onayı | `.claude/AGENTS.md` §3.3 #2 |
| 6 | Secret fail-safe | değer yazılmaz → `REDACTED` (§9) | `.ai/AGENTS.md:200` |
| 7 | Red loglanır | authz reddi gözlemlenir (sessiz 403 yok) | `PermissionMiddleware.php:49-54` (§21) |

**Yasak listesi (bu anayasada):** kendi beyazlistesini genişletmek · `git commit` · secret değeri yazmak ·
salt-okunur yüzeyi düzenlemek · lock'u kendin kırmak · ihlali loglamadan geçmek · "olmazsa da çalışır"
(fail-open) davranış tasarlamak.

## §19 Zero-Hallucination — yetki tablosu disk kanıtlıdır

| # | Kural | Doğru | Yanlış | Kanıt |
|---|-------|-------|--------|-------|
| 1 | Her yetki satırı dosya + satır künyeli | `.ai/AGENTS.md:93-108` | "registry'de yazıyordu" | bu dosya §3 |
| 2 | Doğrulanamayan iddia | `⚠️ VERIFICATION REQUIRED` | tahmini yetki satırı | `.ai/AGENTS.md:42` |
| 3 | Bilinmeyen değer | `UNKNOWN` | uydurma izin listesi | kök `AGENTS.md` §3 |
| 4 | Sayı iddiası | ölçümle (glob / ReadAllLines) | "yaklaşık N dosya" | `.ai/RAG.md:105` atfı |
| 5 | Yok olan yüzey | "diskte YOK" diye yazılır | varmış gibi linklenmek | `.ai/RAG.md:96` atfı |
| 6 | Çelişkide kazanan | disk / SSOT (`.ai/CLAUDE.md` > `.ai/AGENTS.md`) | sessiz düzeltme | vault otorite sırası §13 |

**Uygulama:** bu anayasadaki her tablo `Dosya: ... · Satır: ...` künyesi taşır; künyesiz tek satır bile
**geçersizdir** ve §27 doğrulama kapısında yakalanır. Web iddiaları (§29) gerçek URL ile taşınır;
URL uydurmak Zero-Hallucination ihlalidir.

## §20 PowerShell — beyazliste tarama kalıbı

> Kalıp: diskte var olan bir betik **değildir** (uydurma dosya iddiası yok); kök çalıştırma kuralı
> `.ai/scripts/*.ps1` için kanıtlıdır — `Dosya: .ai/AGENTS.md · Satır: 144` (yalnızca repo kökünden).

```powershell
# yetki-beyazliste-tarama kalibi (olcum) — yalniz repo kokunden calistir
$repo = 'C:\www\coremusic.net'
$allowed = '^\.ai/architecture/K015-AI/.+\.md$'   # bu uretimin beyazlistesi (§5)

$violations = @()
git -C $repo status --porcelain | ForEach-Object {
    $path = $_.Substring(3).Trim('"')
    if ($path -notmatch $allowed) { $violations += "VIOLATION (beyazliste disi): $path" }
}

if ($violations.Count -gt 0) {
    $violations | ForEach-Object { Write-Error $_ }   # log ERROR — §13
    exit 1                                             # RED — fail-safe (§18)
}
Write-Output "PASS — beyazliste korundu"
exit 0
```

**Okuma:** tarama `git status --porcelain` ile değişen yüzeyi alır, beyazliste regex'ine **aykırı her
yolu** ihlal sayar. Çıktı `exit 1` → §13 revert + log; `exit 0` → §27 kapılarından biri yeşil.

## §21 PHP Kanıtı — `PermissionMiddleware` (repo'daki gerçek yetki denetimi)

Repo'da **ajan dosya yetkisi** kodu yoktur (§22); ancak **kullanıcı/RBAC yetki denetimi** vardır ve bu
anayasanın "deny → log → 403" davranışı birebir kodda temsil edilir:

```php
// Dosya: shared/src/Middleware/PermissionMiddleware.php · Satır: 36-42 (gerçek repo kanıtı)
$requiredPermission = $routeMeta['requiredPermission'] ?? null;
if ($requiredPermission !== null) {
    $userPermissions = $auth['permissions'] ?? [];
    if (!is_array($userPermissions) || !in_array($requiredPermission, $userPermissions, true)) {
        return $this->forbidden('permission_denied', "Required permission: {$requiredPermission}");
    }
}
return $next($request);
```

```php
// Dosya: shared/src/Middleware/PermissionMiddleware.php · Satır: 47-54 (gerçek repo kanıtı)
private function forbidden(string $code, string $message): array
{
    // P5-32: authz reddi gözlemlenir (öncekte sessizce 403 dönüyordu).
    \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('authz_denied', [
        'reason' => $code,
        'uri'    => $_SERVER['REQUEST_URI'] ?? '-',
        'ip'     => $_SERVER['REMOTE_ADDR'] ?? '-',
    ]);
```

**Anayasal karşılığı:** `in_array(..., true)` (strict) = deny-by-default (§18 #1) · `securityEvent(...)` =
ihlal loglanır, sessiz red yok (§26) · `halt: true` + 403 = fail-safe durdurma (`Satır: 56-62`).
Middleware sırası frozen: `...Auth → Permission → Validation → Controller` (`Satır: 13`).

## §22 PHP Kanıtı — route bazlı kontrol + bu alandaki yokluk

```php
// Dosya: shared/src/PageRouter/PageRouterHelper.php · Satır: 24-28 (gerçek repo kanıtı)
public function checkPermission(string $requiredPermission): bool
{
    $permissions = $_SESSION['MM_Permissions'] ?? [];
    return in_array($requiredPermission, $permissions, true);
}
```

Route meta'sı `permission` taşır: `Dosya: shared/src/PageRouter/PageRouter.php · Satır: 183`
(`'permission' => $route->requiredPermission`) · shell'e izin listesi verilir:
`Dosya: shared/src/PageRouter/HtmlShellRenderer.php · Satır: 244`.

**Bu alanda olmayanlar (yokluk — uydurulmaz):**

| # | Yokluk | Davranış | Kanıt |
|---|--------|----------|------|
| 1 | **Ajan dosya yetkisi (glob/ACL) üreten PHP kodu** | "Repo'da bu alan kodu YOK" | bu üretim taraması 2026-10-17 |
| 2 | `settings.json` içi `permissions` bloğu | `UNKNOWN` (§12) | okuma 2026-10-17 |
| 3 | Beyazliste tarama betiği (`.ai/scripts/`) | kalıptır, diskte değildir (§20) | glob `.ai/scripts/*` (10 dosya, hiçbiri bu amaçlı) |
| 4 | Ajan lock/critical-override kodu | kural metnidir (`.ai/AGENTS.md` §12) — kod yok | `.ai/AGENTS.md:401-428` |

## §23 İllüstratif Örnek — Python (ACL checker)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# deny-by-default ACL — upstream docs referansı; repo'da karşılığı YOK (bkz. §22 #1)
from pathlib import Path
import re

WHITELIST = [re.compile(r"^\.ai/architecture/K015-AI/.+\.md$", re.I)]  # §5

def decide(agent: str, path: str, write: bool) -> str:
    if not write:
        return "ALLOW-READ"                      # okuma serbest (§6)
    p = Path(path).as_posix()
    if any(rx.match(p) for rx in WHITELIST):
        return "ALLOW-WRITE"                     # beyazliste içi
    if agent == "orchestrator":                  # yalnız MO genişletebilir
        return "ASK"                             # onay kapısı (§18 #5)
    return "DENY"                                # fail-safe (§18 #1)

assert decide("subagent", ".claude/settings.json", write=True) == "DENY"   # §6 #3
assert decide("subagent", ".ai/architecture/K015-AI/permissions/x.md", True) == "ALLOW-WRITE"
```

**Neden illüstratif:** CoreMusic'te yetki denetimi hook kapıları (§11) + kural metinleriyle yapılır;
repo'da glob/ACL üreten Python kodu **yoktur** (§22 #1).

## §24 İllüstratif Örnek — TypeScript (permission gate)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// yazıma izin kapısı — upstream docs referansı; repo kanıtı DEĞİL
type Verdict = "allow" | "ask" | "deny";

const WRITE_WHITELIST = [/^\.ai\/architecture\/K015-AI\/.+\.md$/i]; // §5

export function gate(path: string, opts: { write: boolean; isOrchestrator?: boolean }): Verdict {
  if (!opts.write) return "allow";                                  // okuma serbest (§6)
  if (WRITE_WHITELIST.some((rx) => rx.test(path))) return "allow";  // beyazliste
  if (opts.isOrchestrator) return "ask";                            // onay (§18 #5)
  return "deny";                                                    // fail-safe
}

// sıra: deny > ask > allow (§12 — upstream docs: code.claude.com/docs/en/permissions)
export function firstMatch(rules: Verdict[], verdict: Verdict): Verdict {
  const rank: Record<Verdict, number> = { deny: 0, ask: 1, allow: 2 };
  return rules.slice().sort((a, b) => rank[a] - rank[b])[0] ?? verdict;
}
```

**Yerel karşılığı:** bu repo `.claude/settings.json`'ında `permissions` bloğu **yok** (§12) → kapı
bugün hook (§11) + kural metniyle temsil edilir; TS bloğu yalnız **tasarım referansıdır**.

## §25 İllüstratif Örnek — Go (deny-by-default evaluator)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// yetki değerlendirmeci — upstream docs referansı, repo kanıtı DEĞİL
package perms

import "regexp"

var whitelist = regexp.MustCompile(`(?i)^\.ai/architecture/K015-AI/.+\.md$`)

type Rule struct{ Path string; Write, Own bool }

func Evaluate(r Rule) string {
    if !r.Write {                     // okuma: yalnız secret sınıfı hariç (§9)
        return "ALLOW-READ"
    }
    if whitelist.MatchString(r.Path) { // beyazliste (§5)
        return "ALLOW-WRITE"
    }
    if r.Own {                        // MO / onay makamı
        return "ASK"
    }
    return "DENY"                     // fail-safe (§18)
}
```

**Kural örneğiyle ilişkisi:** `DENY` dönüşü (i) revert'i (§13), (ii) `log ERROR`'u (§26) ve (iii) gerekirse
sistem durdurmayı (§14) tetikler — kapı tek başına "sessizce reddetmez".

## §26 İhlal Log Formatı — `log.md` append-only

| # | Alan | Değer | Kaynak |
|---|------|-------|--------|
| 1 | Seviye | `ERROR` (layer/beyazliste ihlali) · `CRITICAL` (layer — `.ai/CLAUDE.md:184`) | §13 |
| 2 | Olay | `permission_denied` / `layer_violation` / `domain_boundary_violation` / `lock_broken` | §13-§17 |
| 3 | Yüzey | ihlal edilen dosya yolu (künyeli) | §19 #1 |
| 4 | Ajan | ihlal eden rol (§4) | `.ai/AGENTS.md:77-89` |
| 5 | Sonuç | `reverted` · `stopped` (sistem durdu) · `redacted` | §13-§14, §9 |
| 6 | Kural | **append-only** — geçmiş satıra dokunulmaz | `.ai/AGENTS.md:107` · §25.3 #3 |
| 7 | Sensitive | değer alanı `[REDACTED]` | `.ai/AGENTS.md:200` |

**Örnek kayıt satırı (kalıp — biçim, gerçek log satırı değil):**

```text
2026-10-17T00:00:00Z | ERROR | permission_denied | surface=.claude/settings.json | agent=subagent | action=reverted | note=beyazliste disi yazma denemesi (§5) | value=[REDACTED]
```

**Zorunluluk:** ihlal **loglanmadan** geçilmez; log yoksa ihlal yokmuş gibi görünür → denetim imkânsız
olur (§18 #7: sessiz red yok).

## §27 Doğrulama Kapıları (bu dosya için)

| # | Kapı | Kriter | Yöntem |
|---|------|--------|--------|
| 1 | Satır kapısı | ≥500 tam satır | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` |
| 2 | Frontmatter | 10 alan, sıra birebir; `title`/`parent` anahtarı YOK | gözden geçirme |
| 3 | Authority | son satır birebir | gözden geçirme |
| 4 | Kapsam | yazım yalnız hedef dosya; beyazliste dışı değişiklik 0 | `git status --porcelain` (§20) |
| 5 | Secret | `EXA_API_KEY` değeri dosyada YOK (REDACTED) | grep |
| 6 | Placeholder | `{{` yok | grep |
| 7 | İllüstratif etiket | her illüstratif blokta uyarı satırı var | gözden geçirme (§23-§25) |
| 8 | Künye | her iddiada `Dosya:`/`Satır:` kanıtı | gözden geçirme (§19) |
| 9 | Web kaynağı | 1-3 gerçek URL; uydurma URL yok | §29 listesi |
| 10 | Commit | subagent commit **0** | `.git` durumu — karar orkestratörde (§8) |

## §28 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | `.claude/settings.json`'a `permissions` bloğu (deny/ask/allow) eklenecek mi? | HIGH | `UNKNOWN` — dosyada blok yok (okuma 2026-10-17) · §12 |
| 2 | `settings.local.json` izin listesi nedir? | HIGH | `⚠️ VERIFICATION REQUIRED` — bu üretimde okunmadı (varlık: `permissions/index.md:11`) |
| 3 | Beyazliste kalıbı `.ai/architecture/K015-AI/**/*.md` mi, `K015-AI/**/**/*.md` mi (derinlik)? | MEDIUM | bu üretimde derinlik sınırı yok sayıldı — glob geniş yorumlandı |
| 4 | Layer ihlali logu `CRITICAL` mi `ERROR` mı bağlayıcı? | MEDIUM | çelişki açık — §13: `.ai/CLAUDE.md:184` (CRITICAL) vs `.ai/AGENTS.md:207` (ERROR) |
| 5 | Ajan lock/critical-override'ı üreten kod ne zaman yazılacak? | LOW | kod YOK — kural metniyle yönetiliyor (§22 #4) |
| 6 | `EXA_API_KEY` düz metin bulgusu için anahtar rotasyonu yapıldı mı? | HIGH | `⚠️ VERIFICATION REQUIRED` — bu üretimde kanıt yok (§9) |

## §29 Kaynaklar

| # | Kaynak | Yol / URL | Kullanım |
|---|--------|-----------|----------|
| 1 | Agent registry (SSOT) | `.ai/AGENTS.md` §5 · §17 · §18 · §8-§12 (okundu 2026-10-17) | §3, §13-§17 |
| 2 | Kök master kurallar | `AGENTS.md` §16.4-§16.5 · §7 (commit) | §7, §8 |
| 3 | Klasör kuralları `.claude` | `.claude/AGENTS.md` §3.1-§3.3, §5-§6 | §6, §8, §9 |
| 4 | İzin yüzeyi (gerçek alıntı) | `.claude/settings.json` (126 satır, okundu 2026-10-17) | §10, §11, §12 |
| 5 | Vault anayasası | `.ai/CLAUDE.md:184` (Layer Violation → revert + log CRITICAL) | §13 |
| 6 | Anayasa şablonu (yapı) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` | H1 + § + tablo yapısı |
| 7 | Alt düğüm künyesi | `.ai/architecture/K015-AI/permissions/index.md` (49 satır, draft) | FM `depends_on` |
| 8 | RBAC kod kanıtı | `shared/src/Middleware/PermissionMiddleware.php` · `shared/src/PageRouter/PageRouterHelper.php` | §21, §22 |
| 9 | **NIST SP 800-53 Rev.5 — AC-6 Least Privilege** | https://csrc.nist.gov/pubs/sp/800/53/r5/upd1/final (PDF: https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-53r5.pdf) | §4, §18 (doğrulandı 2026-10-17) |
| 10 | **OWASP Cheat Sheet — Authorization (Deny by Default / Least Privilege)** | https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html | §18 (doğrulandı 2026-10-17) |
| 11 | **Claude Code Docs — Permissions (deny → ask → allow sırası)** | https://code.claude.com/docs/en/permissions | §12 (doğrulandı 2026-10-17) |

## §30 Hızlı Referans + Ölçüm Notu

| # | Soru | Cevap |
|---|------|-------|
| 1 | Bu üretimde ne yazabilirim? | Yalnız `.ai/architecture/K015-AI/**/*.md` (§5) |
| 2 | Her şeyi okuyabilir miyim? | Evet — okuma serbest; **yazma** beyazlisteye bağlı (§6) |
| 3 | Secret bulursam? | `REDACTED` — değer yazılmaz, künye + log yazılır (§9) |
| 4 | Commit atabilir miyim? | HAYIR — orkestratöre ait (§8) |
| 5 | Yanlış katmana yazdım? | revert + `log CRITICAL`/`ERROR` (§13) |
| 6 | Domain ihlaliyse? | sistem durur → MO (§14) |
| 7 | Kilit takılıysa? | 30s → CRITICAL override → MO en eskiyi kırar → queue reset (§16-§17) |
| 8 | Yetkim yetmiyorsa? | escalation L1→L2→L3→İnsan; **yetki artmaz**, karar makamı yükselir (§15) |

| # | Eşik | Araç | Not |
|---|------|------|-----|
| 1 | ≥500 tam satır | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` | <500 → 500-520 bandı |
| 2 | FM 10 alan | gözden geçirme | `title`/`parent` anahtarı YOK |
| 3 | Son satır | gözden geçirme | Authority birebir |
| 4 | Kod blokları | JSON ×2 (settings.json gerçek alıntı) · PowerShell ×1 (kalıp) · PHP ×3 (repo kanıtı) · Python/TS/Go ×3 (illüstratif) · text ×1 (log kalıbı) | illüstratiflerde uyarı etiketi zorunlu |
| 5 | Web kaynağı | 3 (NIST · OWASP · Claude Code Docs) | uydurma URL yasak |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
