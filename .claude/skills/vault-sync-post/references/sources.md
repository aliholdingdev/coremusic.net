# vault-sync-post — Kaynaklar (Truth Mode)

> Bu dosya: SKILL.md v4.0.0 içeriğinin her iddiasının dayandığı kaynakların listesidir.
> Kural: 1 web + ≥2 yerel resmi kaynak. Doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED`
> ile işaretlidir; hiçbir değer tahminle doldurulmaz (Zero-Hallucination).

**Ölçüm tarihi:** 2026-10-21 (tüm disk okumaları bu tarihte yapılmıştır).

---

## 1. Web Kaynakları

| # | Kaynak | URL | Erişim | Ne destekliyor |
|---|--------|-----|--------|----------------|
| W1 | git-scm.com — git-log Documentation (Git Projesi resmi dokümantasyonu) | `https://git-scm.com/docs/git-log.html` | 2026-10-21 | `log.md`'nin append-only (yalnız ekleme) denetim izi olarak davranışı: commit geçmişi üzerine yazmaz/revize etmez; kayıt yalnızca eklenir. Kapanış senkronunda "geçmiş satıra dokunma" kuralının gerekçesi buradan gelir. |

> Web kaynak sayısı: 1 (görev şartı ≥1). `git log` semantics için birincil kaynak (Git resmi
> dokümantasyonu) tercih edilmiştir; ikincil blog/çeviri kaynak kullanılmamıştır.

## 2. Yerel Resmi Kaynaklar (disk kanıtı — hepsi okundu)

| # | Dosya | Sürüm/Tarih | Okunan bölüm | Ne destekliyor |
|---|-------|-------------|--------------|----------------|
| L1 | `.workflows/vault-sync.md` | v1.1 (2026-09-24) · 238 satır | Tamamı (L1-238) | 7 aşamalı senkron akışı (tespit → 5 soru → dosya → indeks → çapraz referans → log → doğrulama) · Aşama 6 log blok formatı (`## YYYY-MM-DD HH:mm` + `**İşlem:**`) · **Aşama 8 post-operation sync** + ⚠️ notu (araçlar diskte yok → manuel) · §5 hata modu · §6 yasaklar · §8 aktivasyon |
| L2 | `.ai/SYNC.md` | v1.1.0 (updated 2026-10-12) · 262 satır | L1-124 (§1-§3 + §4 başlangıcı) + §4-§7 (önceki oturum) | **26 hedef dosya seti** (6 kök pointer + 20 `.ai/` kök md) · üç kapı (Baş/Orta/Son) · sınıf etiketleri (CRITICAL/ON-DEMAND/LOG) · §5 tazelik kontrol seti · §6 kurallar (in-place, append-only log, subagent commit atmaz, REDACTED) · §7 doğrulama |
| L3 | `.ai/CHECKLIST.md` | 2026-10 ölçüm | L28-122 + L85-93 (§C) | **§C C1-C5** kapanış maddeleri (C1 kanıt · C2 vault kapanış 5 adım · C3 TODO · C4 post-op manuel sync · C5 `Sonraki adım` satırı) · C-Güçlendirme seti · yasak satırı · §A0 dosya sınıfları |
| L4 | `.ai/WORKFLOW.md` | 2026-10-12 | §8.7 (L562-647) + §8.7A + §8.7B işaretçisi (L694) | Bitiş (kapanış) soruları ve tablosu · §8.7A: **14 `.ai/` kök dosya** okuma/tazelik listesi (§25.4 ile çapraz doğrulandı) · per-session / end-of-session güncelleme sırası · kritik kurallar. ⚠️ Sayı gerilimi: §8.7 başlığı "6 Adım" der, tablo satırları 1,2,3,4,6 (5 yok); CHECKLIST C2 "5 adım" der — üçü arasındaki fark düzeltilmemiştir, kaynak metinlerdeki gibidir. |
| L5 | `.ai/.templates/session-log-template.md` | Guardrail #16 şablonu | Tamamı (önceki oturumda okundu) | Session kaydı şablonu: 7 alanlı frontmatter. Session dosyası yazarken alan adları ŞABLONDAN okunur; bu skill'de kopyalanmaz. |
| L6 | `.ai/scripts/vault-utf8-writer.mjs` | diskte mevcut | Başlık/kullanım satırları + mod listesi (L82-133) | Tek izinli yazma kanalı: mod = `process.argv[2]`; 8 mod: `replace` · `append` · `insert-before-marker` · `write` · `copy` · `verify` · `repair` · `scan`. Yedekleme dizini: `C:/temp/opencode/vault-backups`. Tam bayrak sözdizimi her kullanımda script başlığından doğrulanır. |
| L7 | `.ai/MEMORY.md` | 2026-10-21 ölçüm | L403 · L411 · L701 · L741 | `### §20 Current Session State` (L403) · Known Issue satırı (L411: session-save/vault-post-update araç yokluğu) · `<!-- vault-sync:auto-begin -->` (L701) / `<!-- vault-sync:auto-end -->` (L741) — blok içeriği silinmeden güncellenir |
| L8 | `.ai/log.md` | 2026-10-21 son ölçüm · 219 satır | Format taraması (tam dosya) | İki formatın bir arada yaşadığı kanıtlandı: ~35 madde satırı `- YYYY-MM-DD | konu | detay` + blok başlıkları `## YYYY-MM-DD - konu` (ör. L60, L95). `Sonraki adım` 5 geçiş. Append-only olduğu için sayı gün içinde büyür; sabit sayılmaz. Detay: [[log-append-rules.md]] |
| L9 | `.ai/scripts/` envanteri | 2026-10-21 ölçüm | dizin listesi + Test-Path | `validate.mjs` + `vault-utf8-writer.mjs` MEVCUT; `session-save.mjs` · `vault-post-update.mjs` · `vault-cmd.mjs` · `.ai/project-state.md` **YOK** (Test-Path = False) → kapanış senkronu MANUEL |
| L10 | `.ai/SESSIONS/` envanteri | 2026-10-21 ölçüm | dizin listesi (9 dosya) | Gerçek session kayıt dizini **`.ai/SESSIONS/` (büyük harf)**, dosya deseni `YYYY-MM-DD-<slug>.md`. `.ai/sessions/` (küçük harf) diskte YOK. |
| L11 | Kök `AGENTS.md` + `.ai/AGENTS.md` (v22.2.0) | boot + okuma | §3, §7, §9 · §6 routing, §25.3 | Zero-Hallucination · subagent commit atmaz · log append-only · vault yazım yetkisi (MO) · handover/escalation otoritesi |
| L12 | `.claude/skills/skill-maker/SKILL.md` + `references/` (rules/anti-patterns/checklist) + `templates/skill-template.md` | v3.0 şeması | N1-N10 (L95-144) + tam referanslar | Bu skill'in üretim formatı: frontmatter şeması (name/description/license/metadata) · N1-N10 normları · 500-2000 satır bandı (N3 revize) · orphan yasağı (N4) · örnek zorunluluğu (N5) · changelog zorunluluğu (N10) |

## 5. Doğrulama Yöntemi (Truth Mode Sweep — bu dosyanın kanıtı)

Her yerel kaynak **salt-okunur** komutlarla doğrulandı (2026-10-21):

| Adım | Komut | Sonuç |
|------|-------|-------|
| 1 | `Get-ChildItem .ai/scripts -File` | 10 dosya: `.mjs` = `validate.mjs` + `vault-utf8-writer.mjs`; `.ps1` = **6** (device-matrix-catid · figma-extract · figma-tokens · kalip-abc-check · screens-frontmatter-check · wiki-link-check); `fix-mojibake.py`; `index.md` |
| 2 | `Test-Path` (.ai/scripts/session-save.mjs, vault-post-update.mjs, vault-cmd.mjs, .ai/project-state.md) | Hepsi **False** |
| 3 | `Get-ChildItem .ai/SESSIONS` | 9 dosya, desen `YYYY-MM-DD-<slug>.md` |
| 4 | `Get-ChildItem .ai -Directory` (ad büyük/küçük harf duyarlı eşleşmesi) | Tek dizin: **`SESSIONS`** (büyük harf); `.ai/sessions/` adında ayrı dizin YOK. Not: Windows `Test-Path` büyük/küçük harf **duyarsızdır** (`.ai/sessions` sorgusu `True` döner ama ayrı kanıt sayılmaz — varlık ad eşleşmesiyle doğrulanır) |
| 5 | `Select-String .ai/log.md -Pattern 'Sonraki'` | 5 geçiş; dosya **219 satır** (2026-10-21 son ölçüm — append-only büyüme normaldir) |
| 6 | `Select-String .ai/MEMORY.md -Pattern '§20\|Known Issue\|vault-sync:auto'` | L403 · L411 · L701/L741 |
| 7 | `.ai/scripts/vault-utf8-writer.mjs` okuma (L82-133) | `argv[2]` = mod; 8 `else if` dalı |
| 8 | `.ai/SYNC.md` · `.ai/CHECKLIST.md` · `.ai/WORKFLOW.md` · `.workflows/vault-sync.md` okuma | §-başlıkları ve tablolar birebir alındı |

**Web kaynağı doğrulama notu:** W1 yalnızca **genel ilke** (append-only denetim izi)
için kullanılmıştır; CoreMusic'e özgü kural metinleri yerel kaynaklardan (L1-L12)
alınmıştır. Web'den vault'a özgü iddia (dosya yolu, sayı, script adı) **çıkarılmamıştır**.

## 6. Kaynak → İçerik Eşlemesi (hangi bölüm hangi kaynağa dayanır)

| SKILL.md bölümü | Birincil kaynak | Ek kaynak |
|-----------------|-----------------|-----------|
| §4 Kapanış Akışı (Adım 0-7) | L1 (Aşama 1-8) · L3 (§C C1-C5) | L4 (§8.7/§8.7A) |
| §5 Event→Dosya Matrisi | L2 (§3, §4) | L3 (§A0, C-Güçlendirme) · L4 (§8.7A) |
| §6 log.md Yazım Kuralları | L8 (disk format kanıtı) · L1 (Aşama 6) | L3 (C5) · W1 (append-only gerekçe) |
| §7 Hata Modları & Kurtarma | L1 (§5) · L6 (verify/repair modları) | L9 · L7 |
| §8 Bilinen Disk Uyarlılıkları | L9 · L10 · L4 (§8.7 sayı gerilimi) · L2 (§3.2 sayaç gerilimi) | L7 (Known Issue) |
| §9 Yasaklar | L11 · L2 (§6) · L6 | L1 (§6 yasaklar) |
| Session kaydı (Adım 1) | L10 · L5 | L9 |

## 4. Doğrulanmayan / Bilinmeyenler (açık kayıt)

| İddia | Durum |
|-------|-------|
| `session-save.mjs` · `vault-post-update.mjs` · `vault-cmd.mjs` davranışları | ⚠️ VERIFICATION REQUIRED — dosyalar diskte yok (L9); bu skill onları "varmış gibi" anlatmaz, manuel sürecin karşılığı olarak işaretler |
| `.ai/sessions/context/project-state.md` | ❌ YOK (L9 + eski SKILL.md iddiası düzeltildi) |
| skill'in 2.0.0 sürümü | ⚠️ VERIFICATION REQUIRED — diskte kayıt yok (1.0.0 → 3.0.0 → 4.0.0 zinciri kanıtlı; bkz. [[changelog.md]]) |
| `.opencode/skills/` eşleniği | KAPANDI (2026-10-07, merge/port edildi — L11 `.ai/AGENTS.md` §14) — bu skill yalnız `.claude/skills/` altındadır |
| vault-utf8-writer.mjs tam CLI bayrak sözdizimi | ⚠️ Her kullanımda script başlığından doğrulanır (L6); sabit yazılmaz |
| `MEMORY.md` 1000 satır sınırı | Ölçüldü (2026-10-21): **747 satır** — sınırın altında; kural korunur, aşım olursa §20 arşiv satırına sıkıştırma (silme yok) uygulanır |
| `.ai/SESSIONS/` dosya deseni `YYYY-MM-DD-<slug>.md` | Desen 9 dosyadan çıkarıldı; `HH-MM-SS` varyantı ESKİ iddiaydı (v3 SKILL.md) — diskte kanıtı yok, slug deseni kullanıldı |
| W1 (git-log doc) sayfasının tam metni | Sayfa erişildi (2026-10-21) ancak yalnız append-only ilkesi için kullanıldı; CoreMusic'e özgü hiçbir kural web'den çıkmadı |

---
*CoreMusic skill reference — vault-sync-post/sources.md — Updated: 2026-10-21*
