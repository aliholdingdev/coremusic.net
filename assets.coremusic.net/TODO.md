---
title: "assets.coremusic.net — TODO Listesi"
type: todo
category: project-tracking
version: 1.3.0
status: active
authority: SSOT
updated: 2026-10-30
---

# assets.coremusic.net — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §7 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Frontend varlıklarının kalan işleri: alt PRD bağlantıları, token çapraz kontrolü, erişilebilirlik denetimi.
**eli15:** Bu turda yazılan 4 alt PRD (js/coreplayer · js/router · js çekirdek · Css) henüz bu dosyaya bağlanmadı; token/ekran gereksinim PRD'si (#26) diskte YOK — bağlama maddesi o dosya üretilince kapanır.

## Açık Maddeler

- [ ] **Alt PRD bağlantıları** — #18–#20 (js/coreplayer · js/router · js çekirdek) ve #21 (Css) alt PRD'leri bu dosyaya bağlanır · Kaynak: `[[PRD.md]]` §7.1
- [ ] **Token/ekran çapraz kontrolü** — gereksinimler #26 (`.ai/ui-design` PRD — **diskte YOK**, bkz. `[[../TODO]]` §2 eksik-PRD maddesi) ile karşılaştırılır · Kaynak: `[[PRD.md]]` §7.2
- [ ] **Erişilebilirlik denetimi** — `accessibility-tester` → PASS kaydı bu PRD §4 kapısına eklenir · Kaynak: `[[PRD.md]]` §7.3
- [ ] **ORTA: `package.json` ikiliği SSOT kararı** — `assets.coremusic.net/package.json` (playwright `1.63.0` sabit) + kök `package.json` (`^1.63.0`) aynı devDependencies'i tutuyor, sürüm spec'leri farklı (disk 2026-10-30) → SSOT tek dosya + spec birleşimi; **canlı semptom:** kökten `npx playwright test -c assets/...` = "did not expect test.describe()" (çift `@playwright/test`), assets içinden lokal binary yeşil · Kaynak: glob + e2e koşusu 2026-10-30
- [ ] **MICRO: `WORKFLOW.md` bayat "package.json YOK" iddiaları** — §1/§10/§304 "package.json YOK · komut yolu görev başında doğrulanır" diyor; diskte VAR (glob 2026-10-30) → satırlar güncellenir · Kaynak: glob 2026-10-30
- [ ] **MICRO: test-artifact gitignore** — `test-results/` + `playwright-report/` diskte; kök `.gitignore` yalnız `.playwright-mcp/` içeriyor (`git check-ignore` 0 isabet) ve assets'te `.gitignore` YOK → üretilir · Kaynak: `git check-ignore` 2026-10-30
- [x] **ORTA: e2e WCAG alt-text başarısızlığı** — 2026-10-30 tekrar: 1 kırmızı (`player-info.spec.ts:274`, alt length 0) → kök neden: test dekoratif görsel kalıbını (`alt=""` + `aria-hidden="true"` — `home.coremusic.net/pages/components/player-info.php:37-38`, WCAG 1.1.1 doğru kalıp) reddediyordu → **test** WCAG'ye hizalandı (boş `alt` yalnız `aria-hidden` ile geçerli; attribute her görselde zorunlu) → `npx playwright test` **18/18 passed** · Kaynak: e2e koşusu 2026-10-30

## TAM Madde Listesi — Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Tek doğruluk listesi; `## Geçmiş & Mikro Ölçek` kanıt detayıdır. Sıra: MICRO → ORTA → EPIC.

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: 5 hata düzeltmesi (CatID + şablon disk gerçeği) — `04764f9`
- [x] MICRO: CSS template v4.0.0 (11 katman) hizası — `f86a488` + kök CLAUDE §10
- [x] ORTA: Figma token SSOT (`.env.figma` + gitignore, betikte sabit anahtar yok) — `cdd5665` `f02d02b`
- [x] ORTA: screens frontmatter Kalıp D (13 dosya) — `1042cf4`
- [x] ORTA: reference+flow+prompt Kalıp A/B/C (70 dosya) — `02a98b9`
- [x] ORTA: kök 6 md yeniden yazım + PNG 149/151 + envanter SSOT — `21f357d`
- [x] EPIC: ui-design vault yeniden yazım Faz 0-7 (21 ekran, 151 hedef/136 indirilen/15 gizli) — `cdd5665 → f86a488` · **Faz 8 QA ⏳**

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: F-1a — #18-#21 dosyalarının disk varlığını `Test-Path` ile doğrula
- [ ] MICRO: F-3a — `accessibility-tester`'ı tek ekranda (login) çalıştır
- [ ] MICRO: F-4a — `power-governor-screen-spec.md` içindeki `⚠️` say → açık gate sayısı
- [ ] MICRO: `Css/01_Abstracts/` kopya artığı (`a-layout-tokens copy*.css`) temizlik kararı (onay ister)
- [ ] MICRO: `a-layout-tokens.css` (eski BASE) YOK → BASE `a-layout-tokens-1024.css` envanter notu korunur
- [ ] ORTA: F-1 Alt PRD bağlantıları #18-#21 (js/coreplayer · js/router · js çekirdek · Css)
- [ ] ORTA: F-2 **#26 `.ai/ui-design` PRD ÜRET** (diskte YOK) + token/ekran çapraz kontrol
- [ ] ORTA: F-3 Erişilebilirlik denetimi → PASS kaydı PRD §4 kapısı
- [ ] EPIC: F-4 Power Governor panel ekranı (PNG gate → `ui-design/screens/pro/`)
- [ ] EPIC: ui-design **Faz 8 QA** (`reference/04-verification`) — ailenin tek açık kapısı

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası)
| Sıra | EPIC iş | İlk MICRO adım |
|---|---|---|
| 1 | F-1 + F-2 PRD hattı (7 eksikten biri #26) | Test-Path doğrulama |
| 2 | F-3 a11y PASS kanıtı | login ekranı denetimi |
| 3 | PNG üretil → panel ekran kodu (mockup gate) | PNG üretimi onayı |
| 4 | Faz 8 QA kapanışı | `04-verification` akışı |## Adım Adım Yürütme (2026-10-25 güçlendirme)

**Kural:** Her adım tek oturum · `[ ]` → çalış · `[x]` + kanıt → kapanır · **YAPMA** bağlayıcı · komutu gönder → agent yapar.

| # | Adım | Ne yapılacak (kaynak) | YAPMA | Hazır komut |
|---|---|---|---|---|
| F-1 | [ ] | Alt PRD bağlantıları #18–#21 (js/coreplayer · js/router · js çekirdek · Css) bu dosyaya bağlanır | bağlantı kırma/şablon bozma | `F-1: #18-#21 alt PRD'lerini assets/TODO'ya wiki-link ile bağla` |
| F-2 | [ ] | Token/ekran çapraz kontrol — #26 (`ui-design` PRD, **diskte YOK**) → üret veya bağla | uydurma token/ölçü | `F-2: .ai/ui-design PRD'sini üret (#26) sonra token/ekran çapraz kontrolü yap` |
| F-3 | [ ] | Erişilebilirlik denetimi `accessibility-tester` → PASS kaydı PRD §4 kapısına | PASS kanıtsız "geçti" yazımı | `F-3: accessibility-tester ile assets denetimi çalıştır, PASS kanıtıyla PRD §4'e yaz` |
| F-4 | [ ] | Power Governor paneli (T-05 spec hazır: `power-governor-screen-spec.md`) — **PNG gelince** ekran koduna geç | **PNG yokken UI kodu** (mockup gate) | `F-4: T-05 panel spec'ini Kalıp D ile ui-design/screens'e taşı — PNG BEKLİYOR notunu koru, kod YAZMA` |

**Yüzey yasakları:** framework (ADR-001) · katman dışı ham hex/px (token yalnız `01_Abstracts`) · `Css/07_Vendors/` dışı vendor · inline style/script (CSP) · font/image onaysız silme · commit.
## Geçmiş & Mikro Ölçek — Tam Liste (2026-10-25)

**Geçmiş — tamamlanan (kanıtlı):**
- [x] `[EPİK]` Figma 15 sayfa çekim + token yeniden üretimi (7 breakpoint, FAIL=0) — `f02d02b`
- [x] `[EPİK]` Kök 6 md yeniden yazım + PNG 149/151 + env SSOT — `21f357d`
- [x] `[ORTA]` screens frontmatter Kalıp D (13 dosya) — `1042cf4` · reference/flow/prompt Kalıp A/B/C (70 dosya) — `02a98b9`
- [x] `[MİKRO]` 5 hata düzeltmesi (CatID + şablon disk gerçeği) — `04764f9`
- [x] `[MİKRO]` CSS template v4.0.0 (11 katman) hizası — `f86a488` + kök CLAUDE §10

**Mikro işler (en küçük → büyük):**
- [ ] `[MİKRO]` F-1 altı: #18-#21 dosyalarının disk varlığını `Test-Path` ile doğrula (5 sn)
- [ ] `[MİKRO]` F-3 altı: `accessibility-tester`'ı tek ekran (login) üzerinde çalıştır
- [ ] `[MİKRO]` F-4 altı: `power-governor-screen-spec.md` içindeki `⚠️` sayısını say → kaç gate açık
- [ ] `[ORTA]` F-1 bağlama → F-2 (#26 PRD üret) → `[EPİK]` F-3 PASS kanıtı + panel ekranı (PNG gate)## Referanslar

- `[[PRD.md]]` (§7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
