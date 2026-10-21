# Checklist — skill-maker N1–N10 Sonuçları

> Denetlenen dosya: `.claude/skills/composer-sync/SKILL.md`
> Denetim tarihi: 2026-10-21 · Denetleyici: skill-maker checklist (v4.0.0)
> Sonuç: **PASS** (tüm maddeler PASS; BLOCK yok)

---

## N1 — Frontmatter

| Madde | Sonuç | Kanıt |
|-------|-------|-------|
| Yalnız `name`, `description`, `license`, `metadata` anahtarları | PASS | SKILL.md L1–L14 |
| `name` = klasör adı (`composer-sync`) | PASS | klasör adıyla birebir |
| Üst seviye `triggers`/`changelog`/`format`/`updated` YOK | PASS | anahtar listelenmedi |
| `metadata`: version, format, author, category, tags, updated, previous-version | PASS | version 4.0.0, updated 2026-10-21 |
| `description` ≤1024 karakter | PASS | ~250 karakter |
| `description` "Use when ..." ile başlıyor | PASS | "Use when composer dependencies drift..." |
| Türkçe tetikleyiciler korundu | PASS | 'composer', 'vendor', 'autoload', 'junction', 'class not found', 'bağımlılık senkron' |

## N2 — Dosya adı ve yer

| Madde | Sonuç | Kanıt |
|-------|-------|-------|
| Dosya adı `SKILL.md` (büyük harf) | PASS | `.claude\skills\composer-sync\SKILL.md` |
| Klasör yeniden adlandırılmadı | PASS | `composer-sync` sabit |
| CLAUDE.md sidecar YOK | PASS | dizinde yan dosya yok |

## N3 — Boyut kuralları

| Madde | Sonuç | Kanıt |
|-------|-------|-------|
| SKILL.md ≥500 satır | PASS | 861 satır (2026-10-21, Get-Content.Count ölçümü) |
| Dizim toplamı ≥1000 satır | PASS | 1366 satır (7 dosya toplamı, 2026-10-21 18:04 ölçümü) |
| SKILL.md ≤2000 satır | PASS | 861 ≤ 2000 |

## N4 — Kaynak (Truth Mode)

| Madde | Sonuç | Kanıt |
|-------|-------|-------|
| `references/sources.md` var | PASS | oluşturuldu |
| 2–3 resmi getcomposer.org kaynağı | PASS | S1 config.md, S2 cli.md (+S3 Microsoft) |
| URL + erişim tarihi (2026-10-21) | PASS | her kaynakta |
| Doğrulanamayan iddia etiketli | PASS | §6.2 NOT TESTED, §3.1 VERIFICATION REQUIRED |

## N5 — Bağlantı bütünlüğü (orphan ban)

| Dosya | SKILL.md'den linkli mi | Sonuç |
|-------|------------------------|-------|
| `references/sources.md` | §12 + §13 tablosu | PASS |
| `references/checklist.md` | §13 tablosu | PASS |
| `references/windows-junction.md` | §13 tablosu | PASS |
| `references/diagnostics.md` | §13 tablosu | PASS |
| `examples/vendor-sync-loop.md` | §13 tablosu | PASS |
| Eski `references/changelog.md` | [references/changelog.md](changelog.md) — 4.0.0 girişiyle yeniden üretildi | PASS |

## N6 — Bölümler (zorunlu iskelet)

| Bölüm | Sonuç |
|-------|-------|
| Genel bakış / kapsam (§0) | PASS |
| Tetikleyici senaryolar (§1) | PASS |
| Prosedür / çalışma adımları (§3–§9) | PASS |
| Kaynak (§12) | PASS |
| Kontrol listesi / linkler (§13) | PASS |
| Footer (§14 sonu) | PASS |

## N7 — Dil ve kod

| Madde | Sonuç | Kanıt |
|-------|-------|-------|
| Türkçe anlatım | PASS | tüm bölümler |
| Kod blokları dil etiketli (powershell/bash/json/php) | PASS | §3.2, §5.1, §2.1–2.2 |
| Türkçe karakterler bozulmadı | PASS | yazım sonrası UTF-8 doğrulaması yapıldı |

## N8 — Zero-hallucination

| Madde | Sonuç | Kanıt |
|-------|-------|-------|
| Disk kanıtları gerçek yollarla | PASS | shared/composer.json, junction LinkType |
| Uydurma API/komut yok | PASS | dump-autoload seçenekleri S2'den |
| Belirsizlik açıkça işaretli | PASS | media subdomain junction durumu doğrulanacak |

## N9 — Kapsam disiplini (surgical change)

| Madde | Sonuç | Kanıt |
|-------|-------|-------|
| Yalnız composer-sync dizinine yazıldı | PASS | başka skill dizinine dokunulmadı |
| `.ai/`'ya yazma YOK | PASS | |
| git commit YOK | PASS | |

## N10 — Footer

| Madde | Sonuç | Kanıt |
|-------|-------|-------|
| `*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*` | PASS | dosyanın son satırı |

---

**BLOCK listesi:** (boş)

**Bilinçli sapmalar (§5.3 skill-maker):**

1. `references/changelog.md` — geçici olarak silinmişti; spec gereği 4.0.0
   girişiyle yeniden üretildi ve SKILL.md §13 tablosundan bağlandı (orphan=0).
2. `description` tetikleyicileri Türkçe bırakıldı (N1 zorunluluğu) — eski v2
   korunması gereken değerler.
