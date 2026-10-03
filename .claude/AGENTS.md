---
title: "CoreMusic — .claude Agent Talimatları"
type: guide
category: config
docType: agents
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/AGENTS.md + kök AGENTS.md"
---

# .claude — AGENTS.md

**docType:** agents · **Klasör:** `.claude/` · **Sorumlu:** MO (registry)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[CONTEXT.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `.claude/` klasöründe **kimin neye dokunabileceğini** tanımlar: ayar dosyaları, skill kopyaları, geçici plan dizini ve boot kuralı için rol, yetki sınırı, devreye giriş anı. Kapsam notu: **.claude = Claude Code ayarları + skill kopyaları** — bu dosyalar aracın davranışını değiştirdiğinden yetki sınırları koddan dardır (izin/MCP/secret içerir). Bu dosya türevdir; asıl registry [[../.ai/AGENTS.md]]'dir, çelişkide o kazanır.

| Karar | Kaynak (disk) |
|-------|---------------|
| Yeni skill → şablon zorunlu (Guardrail #16) | `.ai/AGENTS.md` §14 #6 "Template Mandatory" |
| Vault/doküman → MO (vault-updater) | `.ai/AGENTS.md` §6 routing satırı |
| Gizli anahtar yüzeyi → Security | `.ai/AGENTS.md` §5 (`.env` benzeri hassas yüzey) + kök `AGENTS.md` §3 |
| Aracı yapılandırması → kullanıcı onayı | Görev kuralı (Faz 1: onaysız değişiklik yok) |
| Subagent commit atmaz | Kök `AGENTS.md` §7/§8 |

> **eli10 (basit):** Bu dosya, ayar ve yetenek dosyalarının kimin olduğunu ve kimin izinsiz değiştiremeyeceğini yazar.
> **eli15 (detay):** Ayrı yazıldı çünkü rol bilgisi ile ayar içeriği farklı hızda değişir; `settings.json` içine gömülse JSON okunamaz. Okunması, işe başlamadan yetki sınırını gösterir. Yazması, denetlenebilir dağıtım bırakır. Rol belirsiz olunca herkes ayar oynatır ve aracın davranışı sürprizleşir.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.claude/` dosya sahipliği + yetki sınırları (138 dosya envanteri) | Genel registry (SSOT → [[../.ai/AGENTS.md]]) |
| Skill üretimi/güncelleme yetkisi ve şartı | Skill içeriğinin kural metinleri → ilgili `skills/*/SKILL.md` |
| `settings.json`/`settings.local.json` onay kapısı | MCP sunucularının runtime davranışı (diskte değil → UNKNOWN) |
| `plans/` geçici dizin kuralı (vault dışı) | AI aracının kendi iç ayarları (araç tarafı) |

- **Kullananlar:** MO (boot dokümanı + skill kaydı), Security (secret/izin denetimi), ilgili domain agent'ı (skill içeriği), kullanıcı (ayar onayı), araç (çalıştırıcı).
- **Ön koşul:** Envanter ölçülmüş (`.claude/CONTEXT.md` §3.1–§3.2); sayı yoksa `UNKNOWN`.
- **Dokunulmaz:** `CLAUDE.md` (kök + `skills/**/CLAUDE.md`) — yalnız okunur.

---

## 3. Mimari

### 3.1 Dosya Sahipliği Tablosu (disk ölçümü: 5 kök girdi + `skills/` — 2026-10-03)

| Dosya/Dizin | Sorumlu Rol | Yetki Sınırı | Neden bu rol | Ne zaman devreye girer | eli10 | eli15 |
|-------------|-------------|--------------|-------------|------------------------|-------|-------|
| `CLAUDE.md` (936 satır) | **MO (vault-updater)** · Security (kural denetimi) | **DOKUNULMAZ** — düzeltme yalnız vault-sync | Anayasa kopyası; boot `instructions[0]` | Vault-sync / kullanıcı onayı | Boot kural kitabının sahibi vault bakımcısıdır. | Dosya her oturumda okunduğu için tek elden değişir. Ne zaman: yalnız senkron oturumunda. Elle değiştirilirse boot ile `.ai/CLAUDE.md` farkı büyür (SHA256 zaten farklı → VERIFICATION REQUIRED). |
| `settings.json` (64 satır) | **Kullanıcı (onay)** · DevOps Engineer (teknik düzenleme) · Security (secret denetimi) | Onaysız değişiklik YOK; `EXA_API_KEY` REDACTED | İzin/MCP/secret yüzeyi | Araç/MCP/boot listesi değişince | Aracın ayar dosyasını yalnız izinle değiştirebilirsin. | Yetki, izin ve gizli anahtar aynı dosyadadır; onaysız değişiklik güvenlik sınırını oynatır. Ne zaman: MCP eklenip çıkarılırken ya boot listesi değişince. Değiştirilmezse eski ayarla sürpriz davranış devam eder. |
| `settings.local.json` (10 satır) | **Kullanıcı** (yerel izin) | `allow` listesi genişletme onaylı | Yerel/kişisel izin ortak ayardan ayrılır | İzin ihtiyacı değişince | Yerel izin listesini sahibi kullanıcıdır. | Yerel dosya ortak ayardan ayrıdır; böylece kişisel izin ekibi etkilemez. Ne zaman: yeni komut izni gerektiğinde. Genişletilirse (ör. dosya yazma) güvenlik sürprizi doğar. |
| `skills/*/SKILL.md` (11 dosya) | **İlgili domain agent'ı** (içerik) · MO (kayıt/şablon) | İçerik domainde, şablon zorunlu (Guardrail #16) | Skill içeriği uzmanlık ister | Yeni yetenek / kural değişimi | Her yeteneğin kendi uzmanı vardır. | İçerik uzmandan gelir; iskelet şablondan gelir, böylece tutarlılık korunur. Ne zaman: yeni yetenek eklenince ya kural değişince. Şablonsuz skill üretilirse yapı sapar. |
| `skills/**/CLAUDE.md` (21 dosya) | **MO (vault-updater)** | **DOKUNULMAZ** (bu görevde) | Klasör boot kuralları | Vault-sync | Skill klasörünün kural defteri vault bakımcısındadır. | Kural ile talimat farklı hızda değişir; SKILL.md'ye gömülse revizyonda kaybolur. Ne zaman: yalnız senkron. Elle değişim SSOT'u bozar. |
| `skills/**/scripts|templates|references` (kalan 92 md + 4 php + 3 sql + 2 css/html + 1 gitkeep) | **İlgili domain agent'ı** (Data/Backend/UI) | Betik/şablon domainine ait; üretim onaylı | Kod = domain sahipliği (registry §5) | Betik/şablon davranışı değişince | Skill'in içindeki kod ve şablonları o alanın sahibi yazar. | İçerik ayrıdır çünkü php/sql/css kuralları domain agent'ınındır (ör. sql → Data Engineer). Ne zaman: betik ya da şablon güncellenince. Onaysız değişirse skill çalışan kodunu kaybeder. |
| `plans/` (1 dosya: `README.md`) | **Araç (yazar)** · MO (vault'a taşımaz) | Vault'a kopyalanmaz; kalıcı doküman `.ai/`'de | Geçici ile kalıcı ayrımı (`README.md` kuralı) | Araç geçici plan yazınca | Geçici plan klasörü kimsenin kalıcı verisi değildir. | Ayrıdır ki geçici dosya SSOT'a karışmasın. Ne zaman: her geçici plan yazımında. Taşınırsa vault kirlenir, silinirse kanıt kaybolmaz (zaten geçicidir). |

> **eli10 (basit):** Her dosyanın bir sahibi var; en hassası ayar ve gizli anahtar taşıyan dosya, o yüzden onay şart.
> **eli15 (detay):** Sahiplik ayrı yazıldı çünkü yetki ile içerik farklı belgelerdedir. Okunması, kime sorulacağını ve kimden onay alınacağını gösterir. Yazması, denetimi ve eskalasyonu mümkün kılar. Sahip belirsiz olunca herkes ayar/skill değiştirir, aracın davranışı sürprizleşir ve gizli anahtar riski doğar.

### 3.2 Görev → Rol Routing (bu klasör keyword'leri)

| Keyword grubu | Birincil rol | İkincil rol | Kaynak |
|---------------|--------------|-------------|--------|
| skill, SKILL.md, yetenek, skill-maker | MO (şablon/kayıt) | İlgili domain agent'ı | `.ai/AGENTS.md` §14 #6 |
| settings, MCP, izin, permission, allow-list | Kullanıcı (onay) | Security Engineer (denetim) | Görev kuralı + registry §5 |
| secret, API key, token (settings içinde) | Security Engineer | DevOps Engineer | Registry §6 security satırı |
| boot, instructions, CLAUDE.md (kural) | MO (vault-updater) | Security Engineer | Registry §6 vault satırı |
| sql/php/css içindeki skill betiği | Data/Backend/UI (domain) | QA Engineer | Registry §5 Domain Boundaries |
| geçici plan, plans/ | Araç (yazar) | MO (kalıcıya taşımaz) | `plans/README.md` kuralı |

> **eli10 (basit):** Görev kelimesine göre doğru kişiye gider: ayar işi onay+güvenlik, skill işi uzman+vault.
> **eli15 (detay):** Routing ayrı tablo çünkü registry'nin tamamı değil, yalnız bu klasörün özeti buradadır. Okunması, yanlış ajana giden görevi baştan önler. Yazması, hızlı dağıtımı sağlar. Tablo olmazsa her görev orkestratörde birikir.

### 3.3 Yetki Sınırları (yasaklar)

| # | Sınır | İhlal durumunda |
|---|-------|-----------------|
| 1 | Gizli anahtar `.md`/`.log`/`.json` dışına yazılmaz (REDACTED) | Anahtar iptali + log ERROR (Security) |
| 2 | `settings.json` onaysız değiştirilemez | Geri alma + kullanıcıya bildirim |
| 3 | Mevcut `CLAUDE.md` dosyaları düzenlenmez | Dokunulmazlık ihlali → derhal geri al |
| 4 | Yeni skill şablonsuz üretilmez (Guardrail #16) | Yapı sapması → üretim reddedilir |
| 5 | `plans/` içeriği `.ai/` vault'una kopyalanmaz | SSOT kirlenmesi |
| 6 | Subagent `git commit` ATMAZ | Yetkisiz commit → orkestratör |
| 7 | Doğrulanmayan iddia `VERIFICATION REQUIRED` | Halüsinasyon etiketi |
| 8 | Frozen ADR metni kopyalanmaz/değiştirilmez | revert |

> **eli10 (basit):** Kesin yasaklar: gizli bilgi yazma, izinsiz ayar değişikliği, şablonsuz skill ve izinsiz commit yok.
> **eli15 (detay):** Sınırlar ayrı yazılır çünkü her madde bir kaybı (sızıntı, güvenlik sürprizi, yapı sapması) önler. Okunması, işe başlamadan hattı gösterir. Yazması, denetimi mümkün kılar. Sınır kalkarsa ayar ve yetenekler sessizce değişir.

### 3.4 Komşu İlişkiler

| Komşu | Yön | Ne taşır |
|-------|-----|----------|
| `../.ai/AGENTS.md` | registry → .claude | Yetkinin kaynağı (SSOT) |
| `../.ai/CLAUDE.md` | vault → .claude | `instructions[1]` (boot) |
| `../.opencode/skills/` | karşılaştır | Kesişim 2/11 (`composer-sync`, `vault-sync-post`) |
| `../.workflows/` | süreç ↔ .claude | vault-sync/kapanış akışı |
| `../AGENTS.md` (kök) | kök → .claude | §7 loop, commit kuralı |

> **eli10 (basit):** Yetki vault'tan gelir; yeteneklerin bir bölümü başka klasörle ad paylaşır.
> **eli15 (detay):** İlişkiler ayrı tabloda çünkü çelişkide hangi kaynağın kazanacağı belli olsun (registry kazanır). Okunması, türev-SSOT sınırını gösterir. Yazması, bağımlılık görünür olur. Tablo unutulursa bu dosya ikinci registry sanılır.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada, diff küçük | Her şey değişince inceleme kör olur |
| 2 | **Tek sorumluluk** | Her skill'in tek sahibi olur | "Bunu kim ekledi" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Boot listesi tek yerde (`settings.json`) | Her yerde ayrı boot → çelişkili sıra |
| 4 | **Cihaz izolasyonu** | Yerel izin ortak ayarı bozmaz | Yerel izin ekibe yayılır, sürpriz |
| 5 | **Vendor karantinası** | MCP/skill 3. taraf kodu kendi klasöründe | Dış kod güncellenemez, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler küçük kutulara ayrılmış; bir kutuyu değiştirmek ötekini bozmaz.
> **eli15 (detay):** Roller ayrı yazılır ki her dosyanın sahibi tek satırda görünsün. Okunması, kime gidileceğini hızlandırır. Yazması, denetlenebilir dağıtım bırakır. Tek dosyada toplansa hem rol hem ayar değişir, sorumluluk kaybolur.

---

## 4. Kurallar

| # | Kural | Neden var | Kaynak |
|---|-------|-----------|--------|
| 1 | Görev önce §3.2 routing'den geçer | Yanlış ajana giden görev geri döner | [[../.ai/AGENTS.md]] §6 |
| 2 | Ayar değişikliği onay ister (§3.3 #2) | İzin/MCP/secret sınırı oynanmaz | Görev kuralı (Faz 1) |
| 3 | Yeni skill şablondan türetilir | Tutarlı iskelet (Guardrail #16) | `.ai/.templates/` + registry §14 |
| 4 | Secret değeri vault `.md` dosyalarına yazılmaz (REDACTED) | Anahtar sızıntısı | Kök `AGENTS.md` §3 |
| 5 | `plans/` → vault değil, geçici | SSOT kirlenmesi | `plans/README.md` |
| 6 | Handover onaysız tamamlanmaz (max 3 retry) | Yetki devri kaybolmaz | [[../.ai/AGENTS.md]] §9 |
| 7 | Subagent commit ATMAZ | Tarih/entegrasyon orkestratörde | Kök `AGENTS.md` §7/§8 |
| 8 | Çelişkide disk kazanır; doğrulanamayan `VERIFICATION REQUIRED` | SSOT + Zero-Hallucination | Kök `AGENTS.md` §3 |

> **eli10 (basit):** Kurallar, gizli bilginin korunmasını, ayarın izinle değişmesini ve skill'lerin şablonla üretilmesini sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile ayar farklı hızda değişir; JSON içine gömülse araç okuyamaz. Okunmaları, başlamadan kapıları gösterir. Yazması, denetimi tekrarlanabilir kılar. Kural değişince yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → ROUTING (§3.2) → SAHİP DOĞRULA (§3.1) → ONAY KAPISI (ayar/skill: gerekirse)
  → KURAL + ENVANTER OKU (CLAUDE.md + CONTEXT.md) → UYGULA (yalnız hedef dosya)
  → ÖLÇ + VERIFY (vault-utf8-writer) → RAPOR (log append; commit ORCHESTRATÖRDE)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Routing ile rol seç (§3.2) | Atanan rol | Yetki sınırı baştan belli | Yetkisiz değişiklik |
| 2 | [[CLAUDE.md]] + [[CONTEXT.md]] oku | Kural + envanter (138 dosya) | Klasör kuralları ve sayılar | Çelişkili/uydurma bilgi |
| 3 | Onay kapısı (ayar/skill) | Onaylı istek | Sürpriz izin/MCP değişikliği olmaz | Güvenlik sürprizi |
| 4 | Değişiklik + ölçüm (`verify`) | Diff + ölçüm | UTF-8 + sayı kanıtı | Bozuk dosya, halüsinasyon |
| 5 | Rapor + `log.md` append | Kayıt | İzlenebilirlik | Kayıp geçmiş |

> **eli10 (basit):** Doğru kişiye git, izin al, kuralı oku, ölçerek değiştir ve kaydet.
> **eli15 (detay):** Adımlar ayrı satırlardır çünkü her adımın çıktısı ve gerekçesi ölçülür. Okunması, sırayı önceden bilmeyi sağlar. Yazması, tekrarlanabilir dağıtım bırakır. Onay kapısı atlanırsa izinsiz ayar değişir ve tüm oturumları etkiler.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: agents` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı |
| 4 | 4 ağırlık sütunu | §3.1'de ne için / neyden / neden var / ne zaman dolu |
| 5 | eli10 + eli15 | §1, §3, §4, §5 bloklarında etiketli blok; eli10 ≤2 cümle, eli15 3-4 cümle |
| 6 | Disk kanıtı | 138 dosya + 11 SKILL.md + 21 CLAUDE.md iddiaları ölçümle uyumlu |
| 7 | REDACTED | `EXA_API_KEY` değeri dosyada yok |
| 8 | Wiki-link | `[[...]]` formatı; hedefler diskte mevcut |
| 9 | Dokunulmaz | Mevcut `CLAUDE.md` dosyaları değiştirilmedi |
| 10 | Türevlik | SSOT iddiası yok; `authority` türev yazıldı |
| 11 | Emoji | Dekoratif emoji yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Boot kuralı (MEVCUT — değişmez) |
| Klasör context | [[CONTEXT.md]] | Envanter + çelişki kaydı |
| Klasör süreç | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | Routing, handover, escalation |
| Kök master kurallar | [[../AGENTS.md]] | §3 Zero-Hallucination, §7 loop |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Skill şablonu | `../.ai/.templates/` | Guardrail #16 kaynağı |
| Karşı skill envanteri | `../.opencode/skills/` | Kesişim ölçümü (2/11) |
| Süreç akışları | [[../.workflows/vault-sync.md]] | Kapanış kaydı |
| CI kapısı | [[../.github/AGENTS.md]] | `.yml` sahipliği |
| Ayar kanıtı | `.claude/settings.json` | Boot + MCP + izin (REDACTED) |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
