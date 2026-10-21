# vault-sync-post — Kalite Kapısı (Quality Gate)

> **Kullanım:** Oturum kapanışı / vault eşitleme tamamlanmadan önce bu dosyayı çalıştır.
> Her madde `PASS` / `BLOCK` / `N/A`. **Tek `BLOCK` = kapanış YAPILMAZ.**
> Çıktı biçimi: `GATE: PASS|BLOCK` + maddeler + kanıt (dosya:yol · satır no · SHA).
> Kalıp: `skill-maker/references/checklist.md` (N1-N10) + bu skill'in kapıları.

**Skill:** vault-sync-post · **Format:** Claude Skill v3.0 (N1-N10) · **Updated:** 2026-10-21

---

## A. SKILL.md Biçim Kapıları (N1-N10 — teslimde bir kez)

| # | Madde | Kanıt |
|---|-------|-------|
| A1 (N1) | Frontmatter yalnız `name/description/license/metadata` | head |
| A2 (N2) | description "Use when…" · ≤1024 · tetikleyiciler ('vault güncelle', 'session kapat', 'senkronize', 'log yaz', 'vault-post-update', 'session sync') | head |
| A3 (N3) | SKILL.md 500–2000 · dizin ≥1000 (`(Get-Content).Count`) | ölçüm |
| A4 (N4) | Her `references/` SKILL.md §index tablosundan linkli — **orphan=0** | link taraması |
| A5 (N5) | Örnek/şablon blokları var (kapanış formatı, log satırı) — SKILL.md içinde | scan |
| A6 (N6) | `name` = klasör 1:1 (`vault-sync-post`) | head |
| A7 (N8) | `CLAUDE.md` sidecar YOK | dosya yok |
| A8 (N9) | Footer = `metadata.version` + `Updated: 2026-10-21` | tail |
| A9 (N10) | Kök `changelog[]` yok · sürüm `references/changelog.md` (1.0.0 → 4.0.0) | head |
| A10 | UTF-8 BOM yok | bayt okuma |

## B — Eşitleme Seti (§önce `references/sync-touchpoints.md` — SSOT `.ai/SYNC.md` §3)

```text
[ ] Hedef set okundu (26 dosyalık set · üç kapı A/B/C · C1-C5) — tahminle DEĞİL
[ ] Root .md touchpoint'leri güncellendi (yalnızca gerekenler — her dosyaya dokunma YOK)
[ ] `.claude/` eşitlendi (CLAUDE.md yalnız vault-sync kuralıyla — elle DEĞİL)
[ ] `.opencode/` durumu bilinçli: dizin kapatıldı (2026-10-07 merge/port); gereksiz yazma YOK
[ ] Tazelik kontrolü: hedef dosyada yeni değişiklik var mı → varsa çakışma raporlandı
[ ] Aşırı yazma yok: içerik aynıysa touch yok (değişmeyen dosya güncellenmez)
```

## C — log.md Append (`references/log-append-rules.md` — append-only)

```text
[ ] Format doğru: `## YYYY-MM-DD HH:MM - <değişiklik özeti> - vault sync (session kapanışı)`
[ ] APPEND-ONLY: mevcut satırlar düzenlenmedi/silinmedi (tek satır eklendi)
[ ] Dedup: bugün aynı özetli satır varsa → "mevcut kayıt yeterli" kararı bilinçli yazıldı
[ ] `Sonraki adım:` satırı (gerekiyorsa) biçim kuralına uygun
[ ] log'a secret/anahtar/token yazılmadı (REDACTED)
```

## D — Olay → Dosya Matrisi (§5 özeti — tamı: `references/sync-touchpoints.md` §3)

```text
[ ] Olay tipi belirlendi (session kapanışı / ADR / kural / envanter / deploy kaydı)
[ ] Olaya düşen DOSYALAR matrizden çıkarıldı — matriste olmayan yere yazma YOK
[ ] Session kaydı: `.ai/SESSIONS/{id}.md` şablonla (`.ai/.templates/session-log-template.md`)
      · dosya/şablon yoksa C5 kuralı: "araç diskte yok (§9) — manuel adım" olarak raporlandı
```

## E — Doğrulama (kanıtsız "senkron" yazılmaz)

```text
[ ] Yazılan her dosya sonra okundu (içerik + satır sayısı kanıtı)
[ ] UTF-8 BOM yok · mojibake yok (Türkçe karakterler doğru)
[ ] Değişiklik listesi üretildi (dosya → ne değişti)
[ ] git diff/STATUS yalnız OKUNDU (commit bu skill'de ATILMAZ — orkestratöre ait)
[ ] `.ai/` yazmaları hidden:true aramasıyla doğrulandı (gizli dizin görünürlüğü)
```

## F — Yasaklar (bu skill'in kendisi)

```text
[ ] `git commit` / `git add` ATILMADI (orkestratöre ait — §kural 7)
[ ] Frozen ADR metni kopyalanmadı/değiştirilmedi
[ ] `plans/` içeriği `.ai/`'ye kopyalanmadı (SSOT kirlenmesi)
[ ] Mevcut `CLAUDE.md` dosyaları elle düzenlenmedi (yalnız vault-sync yolu)
[ ] Doğrulanamayan eşitleme iddiası `VERIFICATION REQUIRED` etiketli
```

---

## Çıktı şablonu

```
GATE: PASS|BLOCK
- A: 10/10 (N3: SKILL=668, toplam=1283)
- B: PASS (6/6) · C: BLOCK — C.2: log.md'de mevcut satır düzenlendi, log.md:141
- D/E/F: PASS
Kanıt: dosya:satır
```
