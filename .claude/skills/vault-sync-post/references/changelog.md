# vault-sync-post — Değişiklik Günlüğü (Changelog)

> Kural: skill-maker N10 — sürüm geçmişi `references/changelog.md` içinde tutulur (N9: SKILL.md
> yalnız yönlendirir). Sürüm alanı frontmatter `metadata.version` içindedir; SKILL.md köküne
> sürüm yazılmaz.

Format: Keep a Changelog uyumlu · Sürüm zinciri disk kanıtlıdır (eski SKILL.md frontmatter'i:
`version: 3.0.0` · `previous-version: "1.0.0"` · `updated: 2026-10-07`).

---

## [4.0.0] — 2026-10-21

**Komple yeniden yazım (onaylı reset — Dalga 1/3, retry).** 111 → 668 satır; `references/`
(4 dosya) ve `examples/` (yeniden yazım) kurgulandı; retry turunda sayaçlar disk'le hizalandı.

### Dosya bazlı özet (ölçüm 2026-10-21 — final sayaç, retry sonrası)

| Dosya | Önce | Sonra | Değişiklik |
|-------|------|-------|------------|
| `SKILL.md` | 111 satır | 668 satır | Tam yeniden yazım (N3: 500-2000 bandı) |
| `examples/session-close-sync.md` | 66 satır | 115 satır | Silindi + yeniden yazıldı (info korundu, gerçekler hizalandı) |
| `references/sources.md` | — (yok) | 82 satır | Yeni (N4/N10) |
| `references/sync-touchpoints.md` | — (yok) | 183 satır | Yeni (N4) |
| `references/log-append-rules.md` | — (yok) | 157 satır | Yeni (N4) |
| `references/changelog.md` | — (yok) | 78 satır | Yeni (N10) |
| Dizin toplamı | 177 satır | 1283 satır | ≥1000 bandı karşılandı |

### Added
- `references/sources.md` — Truth Mode kaynak kaydı: 1 web (git-scm `git-log` dokümantasyonu,
  erişim 2026-10-21) + 12 yerel resmi kaynak; kaynak→bölüm eşlemesi; doğrulanmayanlar listesi.
- `references/sync-touchpoints.md` — `.ai/SYNC.md` §3-§5 skill-görünümü: 26 dosyalık hedef set
  (6 kök pointer + 20 `.ai/` kök md), üç kapı (A/B/C), C1-C5, olay→dosya yazım matrisi,
  tazelik ölçütleri, `.claude`/`.opencode` eşitleme.
- `references/log-append-rules.md` — log.md append protokolü: üç format (madde satırı / tarihli
  blok / zaman damgalı işlem bloğu), dedup kuralı, yazma prosedürü, hata modları, rollback.
- `references/changelog.md` — bu dosya (N10).
- `examples/session-close-sync.md` yeniden yazımı — çalışmış girdi→çıktı döngüsü (N5).
- SKILL.md §4: 8 adımlı kapanış akışı (Adım 0 durum toplama → Adım 7 rapor/commit kapısı).
- SKILL.md §8: bilinen disk uyarlılıkları (⚠️ VERIFICATION REQUIRED kayıtları).

### Changed
- Frontmatter `metadata.version: 3.0.0 → 4.0.0`, `previous-version: 1.0.0 → 3.0.0`,
  `updated: 2026-10-21`. `name`, `description` tetikleyicileri ve `license: MIT` korundu (N6/N2).
- SKILL.md, derinliği `references/` katmanına taşıdı (N3 revize bant: 500-2000 satır;
  monolit yazılmadı).

### Fixed
- `.ai/sessions/` (küçük harf) → gerçek dizin **`.ai/SESSIONS/`** (büyük harf, 9 dosya — 2026-10-21 ölçümü).
- `session-save.mjs` / `vault-post-update.mjs` / `vault-cmd.mjs` iddiası: bu araçlar diskte YOK
  (Test-Path = False) → süreç **manuel** olarak anlatılıyor (`.workflows/vault-sync.md` Aşama 8 ⚠️
  notu ile uyumlu).
- `.ai/sessions/context/project-state.md` iddiası kaldırıldı — dosya diskte YOK.
- MEMORY.md §18 "Session History" atıfı → gerçek bölüm **§20 Current Session State** (L403).
- Kök `.md` "12 touchpoint" listesi → SYNC.md seti ile hizalandı (6 kök pointer + 20 `.ai/` kök md).
- `.opencode/` eşitlemesi bağlama oturtuldu: `.opencode/skills/` kapandı (2026-10-07, merge/port);
  kalan eşitleme = `.opencode/CLAUDE.md` == `.ai/CLAUDE.md` üçlü kontrolü.

## [3.0.0] — 2026-10-07

- Claude Skill v3.0 şemasına geçiş (`format: claude-skill-v3` · frontmatter name/description/
  license/metadata).
- `.opencode/skills/vault-sync-post/SKILL.md` birleştirildi (merge notu dosyada korundu).
- Eksik araçlar için `⚠️ VERIFICATION REQUIRED` işaretleri eklendi (session-save ·
  vault-post-update · vault-cmd).

## [1.0.0] — ⚠️ VERIFICATION REQUIRED (tarih diskte yok)

- İlk sürüm. Tarih ve içerik ayrıntıları disk kanıtında yoktur; `previous-version: "1.0.0"`
  frontmatter'ından bilinir.

## [2.0.0] — ⚠️ VERIFICATION REQUIRED (diskte kayıt yok)

- Sürüm atlaması: mevcut frontmatter zinciri 1.0.0 → 3.0.0 gösterir; 2.0.0 için disk kanıtı
  bulunmamıştır. Uydurma kayıt yazılmamıştır.

---
*CoreMusic skill reference — vault-sync-post/changelog.md — Updated: 2026-10-21*
