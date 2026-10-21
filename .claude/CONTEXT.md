---
title: "CoreMusic — .claude Context"
type: docs
category: config
date: 2026-10-07
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
docType: context
---

# CoreMusic — .claude Context

**docType:** context · **Klasör:** `.claude/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`.claude/`, Claude Code'ın bu projedeki davranışını ayarlayan dosyaları tutar: boot/izin/MCP ayarları (`settings.json`, `settings.local.json`, `mcp.json`), kural-rol-süreç-envanter belgeleri (`CLAUDE.md`, `AGENTS.md`, `WORKFLOW.md`, `CONTEXT.md`), hook betikleri (`hooks/`), kural notu (`rules/`), geçici plan dizini (`plans/`), yetenek kopyaları (`skills/`), subagent tanımları (`agent/`) ve slash komutları (`command/`). Bu doküman klasörün disk envanterini (ölçüm 2026-10-21: 12 kök girdi = 6 dosya + 6 dizin; recursive 287 dosya / 50 dizin), boot sırasını ve vault ile disk arasındaki çelişkileri tanımlar; kod üretmez, yalnız okunur ve ölçümle güncellenir.

Not: Önceki sürümün mimari katman atıfları bu revizyonda kaldırıldı; yerine yenisi üretilmedi.

| Karar | Kaynak (disk) |
|-------|---------------|
| Kök envanter: 12 girdi (6 dosya + 6 dizin); recursive 287 dosya / 50 dizin | PowerShell recursive ölçüm (2026-10-21) — `settings.local.json` diskte YOK |
| Boot listesi: `instructions` 6 kayıt — 5 hedef diskte mevcut, 1 hedef diskte yok | `settings.json` okuma (2026-10-12) |
| Hook: 3 olay (UserPromptSubmit, PreToolUse, PostToolUse) → `hooks/` 3 betik | `settings.json` `hooks` + `hooks/` sayımı |
| MCP: `settings.json` 6 kayıt (3 tanımlı, 3 `disabled: true`) · `mcp.json` 1 kayıt | iki dosya okuma (2026-10-12) |
| İzin allow-list: 7 satır (1 WebSearch + 6 Bash kalıbı) | `settings.local.json` okuma (2026-10-12) |
| Sayılar: `agent/` 118 · `command/` 24 · `skills/` 13 dizin / 134 dosya | recursive sayım (2026-10-21 — 13 skill Claude Skill v3.0'a sıfırdan yazıldı) |
| Gizli anahtar değeri bu dosyada yazılmaz | REDACTED — kök `AGENTS.md` §3 |

> **eli10 (basit):** Bu klasör, Claude'un bu depoda nasıl davranacağını ve hangi ek yeteneklere sahip olduğunu ayarlar; içindeki dosyalar kod değil, araç ayarı ve tanım dosyalarıdır.
> **eli15 (detay):** Ayrı yazıldı çünkü araç ayarı ile proje kodu farklı hızda değişir; kod içine gömülse her sürümde ayar yeniden aranır. Okunması, boot sırasını, izin sınırını ve envanteri (13 kök girdi) tek yerde gösterir. Yazılması, aracın tekrarlanabilir davranışını sağlar. Envanter olmazsa "kaç skill, kaç subagent var" sorusu tahminle cevaplanır ve vault ile disk farkları görünmez kalır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.claude/` kök envanteri + `agent/` + `command/` + `skills/` + `hooks/` + `rules/` + `plans/` (ölçüm 2026-10-12) | `.ai/` vault içeriği (SSOT; boot listesi oradan okunur) |
| `settings.json` / `settings.local.json` / `mcp.json` yapısı (anahtar değerleri REDACTED) | Skill/subagent/command içeriklerinin tam metni → ilgili `SKILL.md`, `agent/*.md`, `command/*.md` |
| Boot okuma sırası + kök ↔ vault ilişkisi (§3.3) | MCP sunucularının çalışma durumu (runtime, diskte değil → UNKNOWN) |
| Vault ↔ disk çelişki kaydı (§3.4) | Global yetenek kütüphanesi (`C:\.claude\skills`) — bu oturumda erişim REDDEDİLDİ → UNKNOWN |

- **Kullananlar:** Claude Code (boot okuyucu), MO (bu doküman), ilgili domain agent'ı (skill içeriği), kullanıcı (ayar onayı), Security (secret denetimi).
- **Ön koşul:** Klasör diskte mevcut ve tüm sayımlar bu oturumda alınmış; kanıtı olmayan sayısal iddia `UNKNOWN` taşır.
- **Dokunulmaz:** `CLAUDE.md` (bu klasör) — yalnız okunur; düzeltme talebi vault-sync'e rapor edilir.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları, ayarları ve sayıları anlatılır; proje kodu ve vault içeriği konu dışıdır.
> **eli15 (detay):** Kapsam ayrı yazıldı çünkü sınır yoksa bu doküman vault ya da kaynak kod tarifine döner ve okunmaz. Okunması, hangi sorunun buradan cevaplanacağını gösterir. Yazılması, ölçüm ve çelişki kaydının nerede durduğunu sabitler. Sınır kayarsa her görev aynı envanteri yeniden keşfeder ve eski sayılar dolaşıma girer.

---

## 3. Mimari

### 3.1 Kök Envanter (12 girdi — disk ölçümü 2026-10-21)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `CLAUDE.md` | Bu klasörün boot kuralı + anayasa kopyası (MEVCUT — dokunulmaz) | frontmatter (v27.3.3) + Purpose/kural bölümleri | Araç her oturumda kuralları okusun | Yalnız vault-sync (MO) |
| `AGENTS.md` | Dosya sahipliği, yetki sınırı ve routing (bu oturumda okundu) | rol tabloları + yasaklar + routing | Kimin hangi dosyaya dokunacağı baştan belli olsun | Agent registry revizyonunda (MO) |
| `WORKFLOW.md` | Görev adımları ve onay kapıları (docType: workflow) | adım/kapı tabloları (`authority: Derived`) | Onaysız değişiklik yapılmasın | Süreç değişince (MO) |
| `CONTEXT.md` (bu dosya) | Klasör envanteri + çelişki kaydı | 7 bölüm + 5 sütunlu envanter tablosu | Sayılar ölçümle tek yerde dursun | Envanter ölçümü değişince |
| `settings.json` (126 satır) | Boot, hook, MCP, izin, watcher, referans ayarları | 6 anahtar: `hooks` (3), `instructions` (6), `mcpServers` (6), `watcher.ignore` (5), `references` (3), `enabledPlugins` (1) | Aracın davranışı tek yerden yönetsin | Araç/MCP/boot listesi değişince (kullanıcı onayı) |
| `settings.local.json` (13 satır) | Yerel izin allow-list | `permissions.allow` 7 satır | Hangi komutun izinsiz çalışacağı belli olsun | İzin ihtiyacı değişince (kullanıcı onayı) |
| `mcp.json` (9 satır) | 1 MCP sunucu tanımı (`exa-mcp-server`) | `type` + `command` + `environment` — API anahtarı içerir (REDACTED) | MCP tanımı ayrı dosyada dursun | MCP tanımı değişince (onaylı) |
| `agent/` (118 dosya) | Subagent tanımları — `Agent` tool ile çağrılan roller | 118 `.md`: frontmatter (`description`, `mode`, `model`) + rol talimatı | Görevler uzman alt-ajanlara dağıtılsın | Yeni subagent ekleme (onaylı) |
| `command/` (24 dosya) | Slash komut tanımları — `/komut` ile tetiklenir | 24 `.md`: frontmatter + komut gövdesi | Sık işler tek tuşla yapılsın | Yeni slash komutu (onaylı) |
| `skills/` (13 dizin, 134 dosya — 13 skill v3.0 reset 2026-10-21) | Yetenek (skill) kopyaları — Claude Skill v3.0 | 13 `SKILL.md` (her biri 500–2000 satır) + references/scripts/templates/examples; 13 `references/checklist.md` kalite kapısı | Yetenekler aracın yanında taşınsın | Skill kuralı/şablonu değişince (Guardrail #16) |
| `hooks/` (3 dosya) | 3 hook betiği (çalışır kod, `.md` değil) | `skill-mandate.cjs`, `ai-risk-gate.cjs`, `ai-validate-post.cjs` | Kural hatırlatması ve kapılar her prompt/araç çağrısında çalışsın | Mandate/kapı kuralı değişince |
| `rules/` (1 dosya) | Skill kullanım kuralının okunabilir yüzeyi | `skill-usage.md` | Mandate metni tek yerde dursun | Kök mandate değişince |
| `plans/` (1 dosya) | Geçici plan dizini kuralı | `README.md` (6 satır) | Geçici dosya vault'a karışmasın | Kural değişince |

> **eli10 (basit):** On üç girdi var: dört belge, üç ayar dosyası, dört sayı klasörü (subagent, komut, yetenek, hook) ve iki kural/plan dosyası.
> **eli15 (detay):** Envanter ayrı yazıldı çünkü ölçüm yazılmadan iddia edilmez; sayılar bu oturumun disk okumasından gelir. Okunması, aracın boot'unu ve yetenek sınırını tek tabloda gösterir. Yazılması, plan ile disk gerçeğinin hizasını kanıtlar. Tablo olmazsa eski sayılar yeni oturumları yanlış yönlendirir.

**`CLAUDE.md`**
> **eli10 (basit):** Bu klasörde çalışırken uyulacak kuralların yazılı olduğu defterdir.
> **eli15 (detay):** Kural ile kod farklı hızda değişir; kod içine gömülse her revizyonda kural aranır. Okunması, klasör protokolünü ve dokunulmaz yüzeyleri gösterir. Yalnız vault-sync (MO) tarafından düzenlenir. Elle değişilirse boot ile vault arasındaki otorite zinciri bozulur.

**`AGENTS.md`**
> **eli10 (basit):** Bu klasörde kimin hangi dosyaya dokunabileceğini yazar.
> **eli15 (detay):** Yetki bilgisi dosya içeriğinden farklı hızda değişir; envanter içine gömülse rol ile sayı birbirine karışır. Okunması, işe başlamadan sorumluyu ve yasakları gösterir. Agent registry revizyonunda MO günceller. Sahip belirsiz olunca herkes düzenler ve ayarlar sessizce kayar.

**`WORKFLOW.md`**
> **eli10 (basit):** Bir görevin kapı kapı nasıl yürütüleceğini anlatan adım listesidir.
> **eli15 (detay):** Süreç ile envanter farklı dosyalardadır; birleşse her ölçümde süreç metni yeniden yazılır. Okunması, onay ve ölçüm kapılarını önceden gösterir. Süreç değişince MO günceller. Kapı atlanırsa onaysız ayar değişikliği doğrudan girer.

**`CONTEXT.md` (bu dosya)**
> **eli10 (basit):** Klasörün kaç dosya içerdiğini ve eski bilgilerle disk arasındaki farkları yazan kayıt dosyasıdır.
> **eli15 (detay):** Sayı iddiaları tek yerde ve ölçümle durmalıdır; bu yüzden kural ve süreç dosyalarından ayrıdır. Okunması, envanter ve çelişki kaydını verir. Envanter ölçümü değişince güncellenir. Bu dosya olmazsa her oturum "kaç skill var" sorusunu tahminle yanıtlar.

**`settings.json`**
> **eli10 (basit):** Aracın açılışta okuyacağı dosyaları, açık eklentileri ve hook'ları tutan ana ayar defteridir.
> **eli15 (detay):** JSON ayrı durur çünkü aracı okur; `.md` içine gömülse okunmaz. Okunması, boot sırasını, hook kapılarını ve aktif/kapalı MCP sınırlarını gösterir. Yazması, davranış tekrarlanabilirliğini sağlar. İçinde API anahtarı olduğu için değeri bu dosyaya kopyalanmaz (REDACTED).

**`settings.local.json`**
> **eli10 (basit):** Hangi komutların izinsiz çalışabileceğini tutan kısa yerel izin listesidir.
> **eli15 (detay):** Ayrı dosyadır çünkü yerel izin, ortak ayardan ayrılmalıdır; birleşse kişisel izin herkese yayılır. Okunması, güvenlik sınırını gösterir. Yazması, denetlenebilir izin bırakır. Genişletilirse kullanıcı onayı gerekir.

**`mcp.json`**
> **eli10 (basit):** Ayrı bir MCP sunucu tanımı dosyasıdır ve gizli anahtar içerir.
> **eli15 (detay):** Ayrı durur çünkü farklı bir araç biçiminde okunur; `settings.json` içine konsa iki yüzey çelişebilir. Okunması, hangi sunucunun tanımlı olduğunu gösterir. Yazması onay ister. Anahtar değeri asla bu envantere kopyalanmaz; yalnız "REDACTED" yazılır.

**`agent/`**
> **eli10 (basit):** Aracın çağırabileceği uzman rollerinin (subagent) tariflerinin klasörüdür.
> **eli15 (detay):** Ayrı dizindir çünkü subagent, skill ve komut'tan farklı bir mekanizmayla yüklenir. Okunması, hangi uzmanların çağrılabilir olduğunu gösterir. Yazması, yetenek sınırını bırakır. Yeni tanım onaysız eklenirse sürpriz yetki doğar.

**`command/`**
> **eli10 (basit):** Kullanıcının `/` ile tetiklediği kısayol komutlarının tariflerinin klasörüdür.
> **eli15 (detay):** Ayrı dizindir çünkü komut, skill ve subagent'ten farklı tetiklenir. Okunması, mevcut kısayolları gösterir. Yazması, iş akışı kısayolu bırakır. Onaysız eklenirse beklenmedik iş akışı davranışı doğar.

**`skills/`**
> **eli10 (basit):** Araca verilen iş kılavuzlarının (yetenek/skill) durduğu klasördür; 13 klasör vardır.
> **eli15 (detay):** Ayrı dizindir çünkü araç skill'i buradan yükler; vault içine gömülse yüklenemez. Okunması, hangi yeteneklerin kurulu olduğunu sayım olarak gösterir (içerik okunmadı, yalnız dosya sayısı ölçüldü). Yeni skill şablondan üretilir (Guardrail #16). Sayım şaşarsa kopya/özgün ayrımı kaybolur.

**`hooks/`**
> **eli10 (basit):** Prompt ve araç çağrılarında otomatik çalışan üç küçük programdır.
> **eli15 (detay):** Ayrı dururlar çünkü hook çalışır koddur, okunabilir metin değildir. Okunması, neyin ne zaman tetiklendiğini ve ne enjekte ettiğini gösterir. Yazması, davranış değişikliğinin kaynağını bırakır. Yanlış yazılırsa kural hatırlatması veya kapı sessizce kaybolur.

**`rules/`**
> **eli10 (basit):** Skill kullanım kuralının örnekli uzun metnidir.
> **eli15 (detay):** Ayrıdır çünkü kural dosyası boot dosyasından farklı hızda değişir; birleşse kural ile anayasa karışır. Okunması, skill'in ne zaman zorunlu olduğunu gösterir. Kök registry değişince güncellenir. Çelişirse kök kural kazanır.

**`plans/`**
> **eli10 (basit):** Araçların geçici plan yazdığı klasördür; içinde yalnız kural notu vardır.
> **eli15 (detay):** Geçici ile kalıcıyı ayırır; birleşse geçici dosya SSOT sanılır. Okunması, "buraya yazılan vault'a taşınmaz" kuralını gösterir. Kural değişince düzenlenir. Not silinirse geçici planlar vault'a karışabilir.

### 3.2 Ayar Dosyası Yapısı (okuma ölçümü 2026-10-12)

| Dosya / Anahtar | Ne için | Ölçüm (disk) |
|-----------------|---------|--------------|
| `settings.json` → `hooks` | 3 hook olayı | UserPromptSubmit → `skill-mandate.cjs` · PreToolUse (Edit\|Write) → `ai-risk-gate.cjs` · PostToolUse (Edit\|Write) → `ai-validate-post.cjs` |
| `settings.json` → `instructions` | Boot okuma sırası | 6 kayıt: `CLAUDE.md` → `.ai/CLAUDE.md` → `.ai/AGENTS.md` → `.ai/WORKFLOW.md` → `.ai/CONTEXT.md` → 6. kayıt diskte YOK (0 glob isabeti) |
| `settings.json` → `mcpServers` | MCP sunucu listesi | 6 kayıt: filesystem, chrome-devtools, claude-mem (tanımlı) · browsermcp, playwright, exa → `disabled: true` |
| `settings.json` → `watcher.ignore` | İzlenmeyen yollar | 5 kalıp: `vendor/**`, `node_modules/**`, `.git/**`, `*.log`, `*.cache` |
| `settings.json` → `references` | Sunulan referans klasörleri | 3 kayıt: `vault` → `.ai` · `rules` → `.claude/rules` · `workflows` → `.workflows` — üç hedef de diskte mevcut |
| `settings.json` → `enabledPlugins` | Eklenti listesi | 1 kayıt: `frontend-design@claude-plugins-official` → `true` |
| `settings.local.json` | Yerel izin | `permissions.allow` 7 satır (WebSearch + 6 Bash kalıbı) |
| `mcp.json` | Ayrı MCP tanımı | 1 sunucu (`exa-mcp-server`); `EXA_API_KEY` içerir → değer REDACTED |

> **eli10 (basit):** Ayar dosyası, açılışta okunacakları, açık eklentileri, hook kapılarını ve izinleri tek yerde tutar.
> **eli15 (detay):** Yapı ayrı yazıldı çünkü JSON aracın okuyacağı biçimdedir; `.md` içine gömülse okunmaz. Okunması, boot sırasını, hook kapılarını ve aktif/kapalı MCP sınırını gösterir. Yazılması, davranış tekrarlanabilirliğini sağlar. Gizli anahtar değeri hiçbir tabloya kopyalanmaz.

### 3.3 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

```text
settings.json instructions (6 kayıt — 5 mevcut, 1 diskte yok):
  CLAUDE.md → .ai/CLAUDE.md → .ai/AGENTS.md → .ai/WORKFLOW.md → .ai/CONTEXT.md → [6. kayıt: diskte YOK]
  → yalnız ihtiyaç anında: .ai/.templates/ · .workflows/ · rules/skill-usage.md · ilgili vault dosyaları
```

| Bu klasör dosyası | Otorite satırı (frontmatter ölçümü) | Taşıdığı bilgi |
|-------------------|--------------------------------------|----------------|
| `CLAUDE.md` | `authority: SSOT` — kendi iddiası (anayasa kopyası) | Boot kuralı (MEVCUT — dokunulmaz) |
| `AGENTS.md` | `authority: "Derived — SSOT: .ai/AGENTS.md + kök AGENTS.md"` | Dosya sahipliği + yetki |
| `WORKFLOW.md` | `authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"` | Adım → kapı → rapor |
| `CONTEXT.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Envanter + çelişki kaydı (bu dosya) |

> **eli10 (basit):** Aracın boot sırası tek ayar dosyasında; bu klasörün üç belgesi de vault'taki ana dosyalardan türetmiştir.
> **eli15 (detay):** İlişki ayrı tabloda çünkü çelişkide hangi kaynağa bakılacağı belli olsun: türev dosyalar kazanamaz, vault kaynak kazanır. Okunması, boot sırasını ve otorite zincirini gösterir. Yazılması, bu klasör belgelerinin ikinci SSOT sanılmasını engeller. Tablo unutulursa her dosya kendi iddiasıyla bağımsız çalışır ve bilgi ayrışır.

### 3.4 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak, okuma ölçümü 2026-10-12) | Disk ölçümü (2026-10-12) | Karar |
|---|------------------------------------------|---------------------------|-------|
| 1 | Eski `CONTEXT.md` (2026-10-07): "settings.json 64 satır, instructions 4" | 126 satır; `instructions` 6 kayıt | **Disk kazanır** — bu dosya yeniden yazıldı |
| 2 | Eski `CONTEXT.md`: "settings.local.json 10 satır, allow 3" | 13 satır; `allow` 7 satır | **Disk kazanır** |
| 3 | Eski `CONTEXT.md`: "skills 12 dizin / 153 dosya" | 13 dizin / 134 dosya (2026-10-21 reset sonrası) | **Disk kazanır** |
| 4 | Eski `CONTEXT.md`: ".claude/ toplam 304 dosya" | 287 dosya / 50 dizin (recursive, 2026-10-21) | **Disk kazanır** |
| 5 | `WORKFLOW.md` §1 karar tablosu: "instructions[] (4 kayıt)" | 6 kayıt | **Disk kazanır** — WORKFLOW.md bu görevde değiştirilmez, yalnız işaretlenir |
| 6 | `settings.json` `instructions` 6. kaydının hedefi | Hedef diskte YOK (0 glob isabeti) | **İşaretli kalır** — boot'ta eksik hedef; ayar değişikliği kullanıcı onayı ister |
| 7 | `mcp.json` (1 sunucu tanımlı) ↔ `settings.json` `mcpServers` (exa `disabled: true`) | İki ayrı tanımlama yüzeyi | **Çelişki değil, iki dosya** — runtime çalışma durumu diskte değil → UNKNOWN |
| 8 | `settings.local.json` (bu dosyanın 2026-10-12 ölçümünde 13 satır / allow 7 iddiası) | Dosya diskte YOK (2026-10-21, git status temiz — track edilmiyor) | **Disk kazanır** — allow-list iddiaları artık geçersiz; yeniden oluşturulması kullanıcı onayı ister |

> **eli10 (basit):** Eski dosyadaki sayım ve boot kaydı bilgileri eskimiş; gerçeği disk söylüyor ve kazanan disk.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin eski veya kanıtsız olduğunu gösterir. Yazılması, ölçüm kanıtıyla olur. Tablo olmazsa eski "4 kayıt / 12 skill" bilgisi yeni oturumları yanlış yönlendirir.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi ayarın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (ayar → kullanıcı, skill → domain) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Boot listesi tek yerde durur (`settings.json` `instructions`) | Her yerde ayrı boot → çelişkili okuma sırası |
| 4 | **Cihaz izolasyonu** | Yerel izin (`settings.local.json`) ortak ayarı bozmaz | Yerel izin herkese yayılır, güvenlik sürprizi doğar |
| 5 | **Vendor karantinası** | MCP ve skill 3. taraf kodu kendi dizininde durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: boot `settings.json`'da, yetenek `skills/`'te, geçici plan `plans/`'ta durur. Böylece bir yer değiştirince sadece o değişir, diğerleri bozulmaz. Yetenekler tek dosyada toplansa kılavuz okunmaz hale gelir. Hepsi birleşseydi bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 2 | Çelişki (vault ↔ disk) → **disk kazanır** + §3.4 tablosunda işaretlenir | SSOT ihlali |
| 3 | Gizli anahtar değeri (EXA_API_KEY) `.md` dosyalarına YAZILMAZ — yalnız "REDACTED" | Anahtar sızıntısı |
| 4 | `settings.json` / `settings.local.json` / `mcp.json` değişikliği kullanıcı onaylı | İzin/MCP sınırı sessizce değişir |
| 5 | Yeni skill, `.ai/.templates/` şablonundan üretilir (Guardrail #16) | Standart dışı, denetlenemez skill doğar |
| 6 | `CLAUDE.md` (bu klasör) değiştirilmez; düzeltme vault-sync'e rapor edilir | Dokunulmazlık ihlali |
| 7 | `plans/` içeriği `.ai/` vault'una kopyalanmaz | SSOT kirlenmesi |
| 8 | Wiki-link `[[...]]` formatı; diskte olmayan hedef üretilmez | Kırık link |
| 9 | Subagent `git commit` ATMAZ — rapor orkestratöre gider | Yetkisiz commit, revert riski |
| 10 | Emoji yasak; değişken yer tutucusu (çift süslü parantezli desen) kalamaz | Şablon ihlali |

> **eli10 (basit):** Kurallar, gizli bilginin sızmamasını, ayar değişikliğinin izinli olmasını ve uydurma bilgi girmemesini sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile ayar farklı hızda değişir; JSON içine gömülse araç okuyamaz. Okunmaları, işe başlamadan sınırları gösterir. Yazılmaları, denetimin tekrarlanabilir olmasını sağlar. Kural değişince yalnız bu tablo güncellenir; ayar dosyaları sessizce değişmez.

---

## 5. Workflow

```text
ÖLÇ (envanter — ilk okumada) → YAZ (şablona göre, yalnız CONTEXT.md)
  → ÇELİŞKİ KAYDI (vault ↔ disk → disk kazanır) → DOĞRULAMA (§6 tablosu)
  → İLGİLİ INDEX'E 1 SATIR → RAPOR (commit ORCHESTRATÖRDE — subagent atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Kural oku: [[CLAUDE.md]] + [[AGENTS.md]] | Kural listesi | Klasör yasağı (REDACTED, dokunulmazlık) baştan belli olsun | Yasak ihlali, gizli bilgi riski |
| 2 | Envanter ölç (`list_directory` + recursive sayım; sayı uydurulmaz) | Ölçüm tablosu | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, yanlış "eklendi/silindi" iddiası |
| 3 | Şablondan yaz (§1–§7, 5 sütun, eli10/eli15) | Bu dosya | Tutarlı iskelet (Guardrail #16) | Şablon dışı, denetlenemez dosya |
| 4 | Çelişki tablosunu güncelle (§3.4) | Karar satırları | Eski bilgi yeni oturumu yanıltır | "4 boot kaydı / 12 skill" efsanesi sürer |
| 5 | §6 doğrulama tablosunu çalıştır | 12/12 | Teslimden önce kanıt | Hatalı dosya teslim edilir |
| 6 | İlgili index'e 1 satır + rapor; **commit ATMA** | Kayıt + rapor | Yetki sınırı (kök `AGENTS.md` §7/§8) | Yetkisiz commit, revert |

> **eli10 (basit):** İş sırayla yürür: dosyaları say, şablona göre yaz, farkları kaydet, kontrol et ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazılması, tekrarlanabilir ve denetlenebilir bir akış bırakır. Özellikle ölçüm ve doğrulama adımı atlanırsa hatalı iddia dosyaya girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | Şablon alan seti + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Değişken yer tutucusu (çift süslü parantezli desen) kalmadı |
| 4 | Envanter | 12 kök girdi (6 dosya + 6 dizin) + 287 dosya / 50 dizin iddiası bu oturum ölçümüyle uyumlu (2026-10-21) |
| 5 | Sayılar | `agent/` 118 · `command/` 24 · `hooks/` 3 · `plans/` 1 · `rules/` 1 · `skills/` 13 dizin / 134 dosya · settings 126 satır · `settings.local.json` diskte YOK (bkz. §3.4 satır 8) |
| 6 | REDACTED | EXA_API_KEY değeri dosyada yok; yalnız "REDACTED" yazıldı |
| 7 | Wiki-link | `[[...]]` formatı; §7 hedefleri diskte mevcut |
| 8 | eli10 + eli15 | §1, §2, §3.1 (özet + her 13 madde), §3.2–§3.5, §4, §5 bloklarında çift var; eli10 en fazla 2 cümle, eli15 3-4 cümle |
| 9 | Çelişki kuralı | §4'te "disk kazanır" var |
| 10 | Boot sırası | Kök ↔ vault ilişkisi tablosu §3.3'te var |
| 11 | Kaldırılan atıf | Yalnız §1'deki tek satırlık kaldırıldı notu; mimari katman atıfları yok |
| 12 | Emoji | Dekoratif emoji yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasör boot kuralı (MEVCUT — okunur, değişmez) |
| Klasör rolleri | [[AGENTS.md]] | Dosya sahipliği + yetki sınırları |
| Klasör süreci | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Kural notu | [[rules/skill-usage.md]] | Skill kullanım kuralı (diskte mevcut) |
| Geçici plan kuralı | [[plans/README.md]] | Vault dışı geçicilik kuralı (6 satır) |
| Hook kanıtı | [[hooks/skill-mandate.cjs]] | UserPromptSubmit hatırlatıcısı |
| Kök master kurallar | [[../AGENTS.md]] | §3 Zero-Hallucination, §7 loop, commit kuralı |
| Kök anayasa | [[../CLAUDE.md]] | Skill Registry (kayıt sayımının kaynağı) |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails (boot `instructions[1]`) |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | Routing, yetki (boot `instructions[2]`) |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları (boot `instructions[3]`) |
| Vault context | [[../.ai/CONTEXT.md]] | Vault envanteri (boot `instructions[4]`) |
| Template registry | [[../.ai/.templates/index.md]] | Şablon kayıt satırı |
| Bağlayıcı iskelet | [[../.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[../.ai/.templates/documentation/context-md-template.md]] | Envanter tablosu formatı + sayım disiplini |
| Kapanış akışı | [[../.workflows/vault-sync.md]] | Oturum kapanışı senkron adımları |
| Kardeş örnek | [[../.github/CONTEXT.md]] | Aynı şekil tamamlanmış CONTEXT örneği |
| Ayar kanıtı | `.claude/settings.json` · `.claude/settings.local.json` · `.claude/mcp.json` | Boot + MCP + izin (REDACTED) |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12
