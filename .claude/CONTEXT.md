---
title: "CoreMusic — .claude Klasör Context"
type: docs
category: config
docType: context
date: 2026-10-07
updated: 2026-10-07
version: 1.1.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# .claude — CONTEXT.md

**docType:** context · **Klasör:** `.claude/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`.claude/`, Claude Code (AI aracı) tarafında çalışan **ayarlar + skill + subagent + command** klasörüdür: anayasa kopyası `CLAUDE.md`, yetki/MCP ayarları `settings.json` / `settings.local.json`, geçici plan dizini `plans/`, skill kuralı `rules/skill-usage.md`, prompt hatırlatıcısı `hooks/skill-mandate.cjs`, **12 skill klasörü** `skills/` (hepsi Claude Skill v3.0 — registry: kök `CLAUDE.md` §Skill Registry), **118 subagent tanımı** `agent/` ve **24 slash komutu** `command/`. Kapsam notu: **.claude = Claude Code ayarları + skill/subagent/command tanımları** — kod üretmez, aracın davranışını belirler. Bu doküman klasörün disk envanterini, skill yapısını ve ayar dosyalarındaki doğrulanmamış referansları tanımlar.

| Karar | Kaynak (disk) |
|-------|---------------|
| Boot listesi: `CLAUDE.md` + `.ai/CLAUDE.md` + `.ai/AGENTS.md` + `.ai/WORKFLOW.md` | `settings.json` `instructions[]` |
| MCP sunucuları: filesystem, chrome-devtools, claude-mem aktif; browsermcp, playwright, exa kapalı | `settings.json` `mcpServers` (6 kayıt) |
| Yetki allow-list: `WebSearch`, `Bash(php *)`, `Bash(npm list *)` | `settings.local.json` `permissions.allow` (3 satır) |
| Skill envanteri 12 klasör / 153 dosya (proje) · 5 CoreMusic-global (`C:\.claude\skills`) | `skills/` recursive ölçüm (2026-10-07) · registry: kök `CLAUDE.md` §Skill Registry |
| Subagent tanımları 118 dosya `agent/` · slash komutları 24 dosya `command/` · `.claude/` toplam 304 dosya | recursive ölçüm (2026-10-07) |
| Skill zorunluluğu hatırlatıcısı | `hooks/skill-mandate.cjs` (UserPromptSubmit) + `rules/skill-usage.md` |
| `.claude/CLAUDE.md` ≠ `.ai/CLAUDE.md` | SHA256 farklı · 936 satır vs 991 satır |
| `gitignore`/gizlilik: API anahtarı içerir | `settings.json` `exa.env.EXA_API_KEY` → **REDACTED** (değer yazılmaz) |

> **eli10 (basit):** Bu klasör, Claude'un bu depoda nasıl davrandığını ve hangi ek yeteneklere (skill) sahip olduğunu ayarlar.
> **eli15 (detay):** Ayrı yazıldı çünkü araç ayarı ile proje kodu farklı hızda değişir; kod içine gömülse her sürümde ayar revize edilir. Okunması, boot sırasını ve izin sınırını gösterir. Yazılması, aracın tekrarlanabilir davranmasını sağlar. Envanter olmazsa "hangi skill kurulu" sorusu tahminle cevaplanır ve çakışmalar görünmez.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.claude/` kök envanteri + `plans/` + `skills/` + `agent/` + `command/` (recursive, **304 dosya** — ölçüm 2026-10-07) | `.ai/` vault içeriği (SSOT; `instructions` listesi oradan okur) |
| `settings.json` / `settings.local.json` yapılandırması (değerler REDACTED) | Skill/subagent/command içeriğinin tam metni → ilgili `skills/*/SKILL.md` · `agent/*.md` · `command/*.md` |
| `.opencode/skills` — **kapandı 2026-10-07** (tek ev: `.claude/skills`) | MCP sunucularının çalışma durumu (runtime, diskte değil → UNKNOWN) |
| `.claude/rules` referansı ↔ disk durumu (`rules/skill-usage.md` mevcut) | AI arayüzünün kendi iç ayarları (araç tarafı, diskte değil) |

- **Kullananlar:** Claude Code (boot okuyucu), MO (bu doküman), tüm agent'lar (skill kullanıcısı), Security (REDACTED denetimi).
- **Ön koşul:** Klasör diskte mevcut; sayımlar `vault-utf8-writer verify` + recursive sayım ile alınmış; sayı yoksa `UNKNOWN`.
- **Dokunulmaz:** Aktif skill-sidecar yok — `skills/**/CLAUDE.md` uygulaması 2026-10-07'de kaldırıldı; yalnız `prompt-maker/.archive/CLAUDE.md` tarihçedir, değiştirilmez.

---

## 3. Mimari

### 3.1 Kök Envanter (depth 1 — disk ölçümü 2026-10-07)

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `CLAUDE.md` (936 satır) | Claude Code boot anayasası (MEVCUT — dokunulmaz) | Frontmatter (v27.3.3) + Purpose/Scope/Kurallar bölümleri | Araç her oturumda kuralları okusun | Yalnız vault-sync (MO) | Claude'un bu depoda uyduğu ana kural kitabı. | Kural dosyası ayrıdır çünkü kod ile kural farklı hızda değişir. Okunması, yasakları ve mimariyi tek yerde verir. Yazması, denetlenebilirliği sağlar. `.ai/CLAUDE.md` ile SHA256 farklı (936 vs 991 satır) → senkron durumu VERIFICATION REQUIRED; bu görevde değiştirilmez. |
| `settings.json` (64 satır) | Boot + MCP + izin + referans ayarları | `instructions` (4), `mcpServers` (6), `watcher.ignore` (5), `references` (3) | Aracın davranışı tek yerden yönetsin | Araç/MCP sürümü veya boot listesi değişince | Aracının ayar defteri: neyi okuyacağı, hangi eklentilerin açık olduğu. | Ayrı JSON'dur çünkü aracı okur; `.md` içine gömülse okunmaz. Okunması, boot sırasını ve aktif/kapalı MCP'yi gösterir. Yazması, davranış tekrarlanabilirliğini sağlar. İçinde API anahtarı var → REDACTED, asla `.md`'e kopyalanmaz. |
| `settings.local.json` (10 satır) | Yerel izin allow-list | `permissions.allow`: `WebSearch`, `Bash(php *)`, `Bash(npm list *)` | Hangi komutun izinsiz çalışacağı belli olsun | İzin ihtiyacı değişince (kullanıcı onayı) | Hangi komutlara izin verildiğini tutan kısa liste. | Ayrı dosyadır çünkü kişisel/yerel izin, ortak ayardan ayrılır. Okunması, güvenlik sınırını gösterir. Yazması, denetlenebilir izin bırakır. Genişletilirse (ör. yazma izni) onay gerekir. |
| `plans/README.md` (7 satır) | Geçici plan dizini tanımı | Kural: bu klasör vault'a ait değil, kalıcı doküman `.ai/`'de | Geçici dosya vault'a karışmasın | Kural değişince | Araçların geçici yazdığı klasörün kısa notu. | Geçici ile kalıcıyı ayırır. Okunması, "buraya yazılan silinir" kuralını gösterir. Yazması, vault kirliliğini önler. Kural olmazsa geçici planlar SSOT sanılır. |
| `skills/` (12 dizin, 153 dosya) | Skill kopyaları (SKILL.md + references/scripts/templates/examples — Claude Skill v3.0) | 12 `SKILL.md` · 0 aktif `CLAUDE.md` · kalan `.md`/`.php`/`.sql`/css + 1 `.gitkeep` | Yetenekler aracın yanında taşınsın | Skill sürümü/şablon değişince (Guardrail #16) | Araca verilen iş kılavuzlarının kutusu. | Skill ayrı dizindir çünkü araç onu yükler; vault içine gömülse yüklenemez. Okunması, hangi yeteneğin kurulu olduğunu gösterir. Yazması, sürüm takibini sağlar. `.opencode/skills` kapandı → kopyalık sorunu kalktı (tek ev, 2026-10-07); kayıt: kök `CLAUDE.md` §Skill Registry. |
| `hooks/skill-mandate.cjs` | Her prompt'ta skill zorunluluğu hatırlatıcısı (UserPromptSubmit) | CommonJS hook — registry + skill isimlerini bağlama enjekte eder | Skill Usage Mandate her prompt'ta hatırlansın | Mandate/registry değişince | Skill kuralını hatırlatan küçük tetikleyici. | Ayrıdır çünkü hook, `.md` gibi okunmaz — çalışır kod. Okunması, ne enjekte ettiğini gösterir. Yazması, davranış değişikliğinin kaynağını gösterir. Yanlış yazılırsa mandate sessizce kaybolur. |
| `rules/skill-usage.md` | Genişletilmiş skill kullanım kuralı (SSOT: kök `CLAUDE.md` §Skill Usage Mandate) | Kural maddeleri + örnekler | Mandate'in okunabilir kural yüzeyi | Kök mandate değişince | Skill kuralının örnekli uzun metni. | Ayrıdır çünkü kural dosyası boot dosyasından farklı hızda değişir; SSOT kök `CLAUDE.md`'dir, bu dosya çelişirse kök kazanır. |
| `agent/` (118 dosya) | Subagent tanımları — `Agent` tool ile çağrılan uzman roller | Her dosya: frontmatter (`description`, `mode: subagent`, `model`, `permission`) + rol talimatı | Görevler uzman alt-ajanlara dağıtılsın | Yeni subagent ekleme (onaylı) | Aracın çağırabileceği uzman rollerinin listesi. | Ayrı dizindir çünkü araç subagent'leri buradan yükler (skill ve command'den farklı mekanizma — Agent tool). Okunması, hangi uzmanların çağrılabilir olduğunu gösterir. Yazması, yetenek sınırını bırakır. |
| `command/` (24 dosya) | Slash komut tanımları — `/komut` ile çalıştırılır | Her dosya: frontmatter (`description`, `model`, `subtask`) + komut gövdesi | Sık işler tek tuşla (commit, changelog, prompt-maker, f-tuşları…) | Yeni slash komutu ekleme (onaylı) | Aracın `/` komutlarının tarifleri. | Ayrı dizindir çünkü komut, skill ve subagent'ten farklı çağrılır (kullanıcı `/` ile tetikler). Okunması, mevcut kısayolları gösterir. Yazması, iş akışı kısayolu bırakır. |

> **eli10 (basit):** Dokuz girdi var: kural kitabı, iki ayar dosyası, geçici klasör notu, skill kuralı, hook, 12 yetenek klasörü, 118 subagent tanımı ve 24 slash komutu.
> **eli15 (detay):** Envanter ayrı yazıldı çünkü ölçüm yazılmadan iddia edilmez. Okunması, aracın boot'unu ve yetenek sınırını gösterir. Yazması, plan-uygulama hizasını kanıtlar. Ölçüm yoksa "kaç skill var" tahminle cevaplanır ve kopya/özgün ayrımı kaybolur.

### 3.2 `skills/` Envanteri (depth 3 — 12 dizin / 153 dosya — ölçüm 2026-10-07)

| Skill klasörü | Dosya | İçerik özeti | eli10 | eli15 |
|---------------|-------|--------------|-------|-------|
| `prompt-maker/` | 66 | `SKILL.md` + `references/` (31 md) + `examples/` (4) + `.archive/` (31) | En büyük skill; prompt üretim kılavuzu. | Referans ve arşiv ayrıldığı için dosya sayısı fazladır; ayrıdır ki ana talimat şişmesin. Okunması, hangi kuralın nerede olduğunu gösterir. Yazması, arşivin silinmeden korunmasını sağlar. |
| `database-normalize-maker/` | 20 | `SKILL.md` + `references/` (13 md) + `scripts/` (3 php) + `templates/` (3 sql) | BCNF normalizasyon kılavuzu + denetleyici betikler. | Scripts/templates ayrıdır çünkü çalıştırılabilir/şablon dosyalar vault md'sinden farklıdır. Okunması, kural ve betik ilişkisini gösterir. |
| `skill-maker/` | 16 | `SKILL.md` + `references/` (7 md) + `examples/` (5) + `templates/` (2 md) | Skill yazım kılavuzu + şablonları — **v3.0 format otoritesi**. | Şablon ayrıdır ki yeni skill hep aynı iskeletle üretilsin (Guardrail #16). Okunması, kuralları gösterir. |
| `ui-code-generator/` | 13 | `SKILL.md` + `references/` (7 md) + `templates/` (css/html/php) | Frontend kod üretimi + WCAG/ITCSS kuralları. | Şablonlar ayrıdır; kod ile kural farklı hızda değişir. Okunması, ITCSS/BEM kurallarını gösterir. |
| `ui-analyzer/` | 11 | `SKILL.md` + `references/` (7 md) + `examples/` (3) | Salt-okunur UI denetimi (v3.0'da çok-dosyaya bölündü). | Metodoloji/kriter/puanlama ayrıdır ki analiz raporu tek dosyada şişmesin. |
| `agent-orchestrator/` | 3 | `SKILL.md` + `references/` (2 md — workflow-dispatch merge dahil) | Çok-ajan görev dağıtımı. | Dispatch/risk ayrıdır; ana dosya akış özeti kalır. |
| `audio-engine-cpp/` | 5 | `SKILL.md` + `references/` (3) + `examples/` (1) | C++20/JUCE/ASIO realtime kuralları (yeni, v3.0). | Gerçek-zamanlı kurallar ayrıdır; örnek tek dosyadır. |
| `hardware-electronics/` | 5 | `SKILL.md` + `references/` (3) + `examples/` (1) | Class AB/PSU/termal/PCB/BOM kuralları (yeni, v3.0). | Donanım kısıtları ayrıdır ki BOM/termal değişiminde çakışma olmasın. |
| `php-backend-standards/` | 5 | `SKILL.md` + `references/` (3) + `examples/` (1) | Middleware/PDO/API-first kuralları (yeni, v3.0). | Pipeline, PDO ve contract akışı ayrıdır; tek dosyada büyüse revizyon zorlaşır. |
| `security-hardening/` | 5 | `SKILL.md` + `references/` (3) + `examples/` (1) | CSRF/CSP/crypto yasaklar+protokol (yeni, v3.0). | Güvenlik ayrıdır; çelişen kural olmasın diye tek kaynaktan okunur. |
| `composer-sync/` | 2 | `SKILL.md` + `references/changelog.md` | Composer/vendor senkron talimatı (v3.0 frontmatter). | Minimal skill; komut + changelog yeter. |
| `vault-sync-post/` | 2 | `SKILL.md` + `examples/session-close-sync.md` | Kapanış vault senkron adımları (v3.0 + örnek). | En küçük skill; örnek kapanış turunu gösterir. |

> **eli10 (basit):** On iki yetenek var; altısı referans/örnek dosyalı, beşi 5 dosyalık yeni domain skill'i, ikisi mini talimat.
> **eli15 (detay):** Skill envanteri ayrı tablodur çünkü derinlik/küçüklük ayrımı ölçülmeden yapılamaz. Okunması, hangi yeteneğin ne kadar derin olduğunu gösterir. Yazması, büyüyen skill'leri izlemeyi sağlar. Tablo olmazsa 153 dosyanın ne olduğu bilinmez. Registry tekilliği: kök `CLAUDE.md` §Skill Registry.

### 3.3 Ayar Dosyası Yapısı (`settings.json` — 64 satır)

| Anahtar | Değer (özet) | eli10 | eli15 |
|---------|--------------|-------|-------|
| `instructions` | 4 kayıt: `CLAUDE.md`, `.ai/CLAUDE.md`, `.ai/AGENTS.md`, `.ai/WORKFLOW.md` | Açılışta otomatik okunan dosyalar. | Sıra okuma sırasıdır; boot'un kaynağıdır. Okunması, neyin yükleneceğini gösterir. Yazması, boot tekrarlanabilirliğini sağlar. Listeden biri çıkarsa o kuralsız başlar. |
| `mcpServers` | 6 kayıt: filesystem, chrome-devtools, claude-mem (aktif); browsermcp, playwright, exa (`disabled: true`) | Hangi eklentilerin açık olduğunu tutar. | Ayrı anahtardır çünkü araç davranışı dış bağımlılıktır. Okunması, aktif/kapalı sınırı gösterir. Yazması, denetimi sağlar. Kapalı sunucu açılırsa yeni yetki doğar. |
| `watcher.ignore` | 5 kalıp: `vendor/**`, `node_modules/**`, `.git/**`, `*.log`, `*.cache` | İzlenmeyen yollar. | Ayrıdır ki değişiklik izleme gereksiz dosyayı izlemesin. Okunması, hangi yolların sessiz olduğunu gösterir. |
| `references` | 3 kayıt: `vault` → `.ai`, `rules` → `.claude/rules`, `workflows` → `.workflows` | Sunulan referans klasörleri. | `rules` hedefi **oluşturuldu** (`.claude/rules/skill-usage.md`, 2026-10-07) → referans artık geçerli. Okunması, referans yapısını gösterir. Yazması, denetimi sağlar. |
| `exa.env.EXA_API_KEY` | Değer **REDACTED** (bu dokümanda yazılmaz) | Gizli anahtar içerir. | REDACTED politikası gereği anahtar `.md`'e kopyalanmaz; yalnız `settings.json`'da durur. Sızarsa anahtar iptal edilir. |

> **eli10 (basit):** Ayar dosyası, açılışta okunacakları, açık eklentileri ve izinleri tek yerde tutar.
> **eli15 (detay):** Yapı ayrı yazıldı çünkü JSON aracın okuyacağı biçimdedir; `.md` içine gömülse okunmaz. Okunması, boot ve yetki sınırını gösterir. Yazması, davranış tekrarlanabilirliğini sağlar. İçindeki gizli anahtar REDACTED olarak kalır.

### 3.4 Komşu İlişkiler + Kesişim

| Komşu | Yön | Ne taşır (kanıt) |
|-------|-----|------------------|
| `../AGENTS.md` (kök) | kök → .claude | Master kurallar; boot sırası |
| `../.ai/CLAUDE.md` | vault → .claude | `instructions[1]` (boot listesi) |
| `../.opencode/skills/` | **kapandı (2026-10-07)** | Dizin kaldırıldı; tek ev `.claude/skills` |
| `../.workflows/` | .claude ↔ süreç | `references.workflows` + süreç akışları |
| `../.github/` | .claude ↔ CI | `Bash(php *)` izni test kapısıyla ilgili |

> **eli10 (basit):** Ayarlar vault'tan boot okur, yeteneklerin bir kısmı başka klasörle ad paylaşır.
> **eli15 (detay):** İlişkiler ayrı tabloda çünkü kopya/özgün ayrımı ve boot bağımlılığı görünür olsun. Okunması, nereden ne geldiğini gösterir. Yazması, bağımlılığı izlenir kılar. Tablo olmazsa "bu skill buranın kopyası mı" sorusu cevapsız kalır.

### 3.5 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak) | Disk ölçümü (2026-10-07) | Karar |
|---|----------------|--------------------------|-------|
| 1 | `.claude/settings.json` `references.rules.path = ".claude/rules"` | `.claude/rules/` oluşturuldu + `skill-usage.md` yazıldı (2026-10-07) | **Çözüldü** — referans artık geçerli |
| 2 | ".claude/skills = skill kopyaları" (görev tanımı) | `.opencode/skills` **kapatıldı** (2026-10-07); tek ev `.claude/skills` (12 dizin) | **Çözüldü** — kopyalık/kesişim sorunu kalktı; registry: kök `CLAUDE.md` §Skill Registry |
| 3 | `.claude/CLAUDE.md` = `.ai/CLAUDE.md` varsayımı | SHA256 farklı; 936 vs 991 satır | **Farklı dosyalar** — senkron durumu VERIFICATION REQUIRED (içerik diff'i alınmadı) |
| 4 | `.workflows/CLAUDE.md` §2/`.ai/AGENTS.md` boot listesi | `.claude/` boot'u `settings.json` `instructions[]` üzerinden | Tutarlı — çelişki yok |

> **eli10 (basit):** Ayar dosyası olmayan bir klasöre ve iki kural kitabının aynı sanılmasına işaret ediyor; gerçeği disk söylüyor.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin eski/kanıtsız olduğunu gösterir. Yazması, ölçüm kanıtıyla olur. Tablo olmazsa eksik referans ve kopya iddiası gerçek gibi kullanılır.

### 3.6 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Her şey değişince hangi ayarın ne yaptığı kaybolur |
| 2 | **Tek sorumluluk (tek iş)** | Her skill'in tek görevi ve tek sahibi olur | 153 dosya tek yerde toplansa okunmaz; sorumluluk kaybolur |
| 3 | **Token tek kaynak** | Boot listesi tek yerde (`settings.json` `instructions`) | Her yerde ayrı boot → çelişkili okuma sırası |
| 4 | **Cihaz izolasyonu** | Yerel izin (`settings.local.json`) ortak ayarı bozmaz | Yerel izin herkese yayılır, güvenlik sürprizi |
| 5 | **Vendor karantinası** | MCP/skill 3. taraf kodu kendi klasöründe | Dış kod içimize karışır, güncellemede kaybolur |

> **eli10 (basit):** Bilgiler küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: boot `settings.json`'da, yetenek `skills/`'te, geçici plan `plans/`'ta durur. Böylece biri değişince sadece o değişir. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | Neden var (gerekçe) | Kaynak |
|---|-------|---------------------|--------|
| 1 | Gizli anahtar (ör. `EXA_API_KEY`) `.md` dosyalarına YAZILMAZ | Anahtar sızıntısı (REDACTED politikası) | Kök `AGENTS.md` §3 + `.ai/CLAUDE.md` |
| 2 | `settings.json` değişikliği kullanıcı onaylı | Aracın izin/MCP sınırı değişir | Görev kuralı (Faz 1 onay) |
| 3 | Yeni skill `.ai/.templates/` şablonundan üretilir (Guardrail #16) | Tutarlı iskelet + frontmatter | `.ai/.templates/frontend/context-template.md` §3.2 |
| 4 | Mevcut `CLAUDE.md` dosyaları bu görevde değiştirilmez | Dokunulmazlık + SSOT hiyerarşisi | Görev kuralı |
| 5 | Çelişki (vault ↔ disk) → disk kazanır + §3.5'te işaretlenir | SSOT korunur | `.ai/CLAUDE.md` Hard Guardrails |
| 6 | Kırık referans (`references.rules`) `UNKNOWN`/işaretli kalır, uydurulmaz | Zero-Hallucination | Kök `AGENTS.md` §3 |
| 7 | Skill kopya iddiası ad kesişimiyle kanıtlanır (2/11) | Uydurma "kopya" iddiası yazılmaz | Disk ölçümü (§3.2) |

> **eli10 (basit):** Kurallar, gizli bilginin sızmamasını, ayar değişikliğinin izinli olmasını ve uydurma bilgi girmemesini sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile ayar farklı hızda değişir; JSON içine gömülse araç okuyamaz. Okunmaları, işe başlamadan sınırları gösterir. Yazması, denetimin tekrarlanabilir olmasını sağlar. Kural değişince yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV GELİR → İLGİLİ KURAL OKU (.claude/CLAUDE.md + kök AGENTS.md)
  → ENVANTER ÖLÇ (kök + skills/, 1. kez) → KARAR VER (ilk okumadan)
  → DEĞİŞİKLİK (settings/dosya: onaylı) → DOĞRULAMA (verify + ölçüm)
  → RAPOR (commit atmaz — orkestratöre aittir)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Kural oku: [[CLAUDE.md]] + [[AGENTS.md]] | Kural listesi | Klasör yasağı (REDACTED, dokunulmazlık) | Gizli bilgi/izin ihlali |
| 2 | Envanter ölç (5 kök girdi + 12 skill / 153 dosya) | Ölçüm tablosu | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, yanlış envanter |
| 3 | Değişikliği ilgili dosyaya yap (onaylı) | Diff | Tek sorumluluk sınırı | Yanlış dosyaya yazma |
| 4 | `vault-utf8-writer verify` + §6 kontrolleri | Doğrulama çıktısı | UTF-8 + yapı bozulmaz | Bozuk dosya, mojibake |
| 5 | Çelişki/eksik referansı §3.5'e işaretle | Açık liste | Doğrulanmayan iddia yazılmaz | Yanlış "tamamlandı" |
| 6 | Rapor yaz; **commit ATMA** | Rapor | Yetki sınırı (kök `AGENTS.md` §7/§8) | Yetkisiz commit, revert riski |

> **eli10 (basit):** Sırayla: kuralı oku, dosyaları say, izinle değiştir, doğrula ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlardır çünkü her adımın çıktısı ve gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazması, tekrarlanabilir akış bırakır. Ölçüm ve doğrulama adımı atlanırsa hatalı iddia git geçmişine girer.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı |
| 4 | Envanter | 5 kök girdi + 12 skill / 153 dosya iddiası recursive ölçümle uyumlu (2026-10-07) |
| 5 | Disk kanıtı | Satır sayıları (936/64/10/7) + SHA256 farkı `verify`/hash ile kanıtlı |
| 6 | REDACTED | `EXA_API_KEY` değeri dosyada yok; yalnız "REDACTED" yazıldı |
| 7 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 8 | eli10 + eli15 | §1, §3, §4, §5 bloklarında etiketli blok var; sıralama eli10 → eli15 |
| 9 | Halüsinasyon | `.claude/rules` ve kopya iddiası işaretli; okunmayan skill içeriği iddia edilmedi |
| 10 | Dokunulmaz | Mevcut `CLAUDE.md` dosyaları değiştirilmedi |
| 11 | Emoji | Yalnız `[[...]]` referansı; dekoratif emoji yok |
| 12 | Uzunluk | 200–350 satır bandı (küçük dizin istisnası) |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasör boot kuralı (MEVCUT — değişmez) |
| Klasör rolleri | [[AGENTS.md]] | Kim neye dokunur |
| Klasör süreç | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Kök master kurallar | [[../AGENTS.md]] | §3 Zero-Hallucination, §7 loop |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails (boot `instructions[1]`) |
| Agent registry | [[../.ai/AGENTS.md]] | Skill/agent routing (boot `instructions[2]`) |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları (boot `instructions[3]`) |
| Karşı skill envanteri | `../.opencode/skills/` | **kapandı** — dizin kaldırıldı; tek ev `.claude/skills` |
| Skill şablonu | `../.ai/.templates/` | Guardrail #16 |
| Süreç akışları | [[../.workflows/vault-sync.md]] | Kapanış akışı |
| Ayar kanıtı | `.claude/settings.json` | Boot + MCP + izin (REDACTED) |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-07
