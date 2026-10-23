---
title: ".workflows — Product Requirements Document"
type: prd
category: surface
version: 1.0.0
status: active
authority: "SSOT: .ai/PRD.md — Yüzey envanteri @.workflows/CONTEXT.md (ölçüm 2026-10-12)"
updated: 2026-10-23
---
# .workflows — PRD

> **Yüzey:** Çalıştırılabilir süreç akışları (`workflow-instruction`) yüzeyi.
> **Şablon:** 15 başlık. Kanıt yoksa `⚠️ VERIFICATION REQUIRED`.

## 1. Amaç

Oturum, ADR, vault-sync, güvenlik denetimi, dağıtım, halüsinasyon kontrolü ve
orkestrasyon akışlarını tek klasörde, kapılarıyla (G1-G6) ve sahiplik tablosuyla
tutmak; agent'ların "nasıl yapılır" adımlarını rastgelelikten kurtarmak.

## 2. Kapsam

- ✅ 11 dosya / 0 alt dizin (ölçüm 2026-10-12); 7 dosya `type: workflow-instruction`
- ✅ 7 akış toplam **1.873 satır**; 10 dosya (CONTEXT hariç) toplam **2.335 satır**
- ✅ `session-init.md` (248) · `deployment.md` (281) · `orchestrator-flow.md` (309) ·
  `adr-creation.md` · `vault-sync.md` (v1.1) · `security-audit` · `hallucination-check`
  (⚠️ VERIFICATION REQUIRED — tam liste CONTEXT §envanter)
- ❌ 7 akış + CLAUDE/AGENTS/WORKFLOW bu görevde değiştirilemez · ❌ PHP/JS/CSS
  kaynak kod · ❌ `git commit`

## 3. Kullanıcı Hikâyeleri

- **US-1** — *Tüm agent'lar:* "İşi doğru kapıdan geçireyim" → akış dosyaları (G1-G6).
- **US-2** — *MO:* "vault senkron + ADR üretimi tek elden yürüsün" → `vault-sync.md` + `adr-creation.md`.
- **US-3** — *Orkestratör:* "Görev doğru ajana gitsin" → `orchestrator-flow.md`.

## 4. Gereksinimler

| # | Gereksinim | Öncelik |
|---|---|---|
| G-1 | 7 akış dosyası frontmatter (`workflow-instruction`) + aşama/yasak bölümleri | P0 |
| G-2 | Kapılar: WORKFLOW.md G1-G6 her akışta görünür | P0 |
| G-3 | Sürüm alanları taze (`vault-sync` 1.1; `adr-creation.md`'de `version` YOK → ⚠️ VERIFICATION REQUIRED) | P1 |
| G-4 | Sahiplik tablosu her akışa bağlı (AGENTS.md §routing) | P1 |
| G-5 | Elle değişiklik yalnız vault-sync üzerinden (audit zinciri) | P0 |

## 5. Kabuk Şema (envanter — ölçüm 2026-10-12)

```text
.workflows/  11 dosya, 0 alt dizin, tamamı .md
  7 × workflow-instruction (1.873 satır):
    session-init 248 · deployment 281 · orchestrator-flow 309 · adr-creation ·
    vault-sync (v1.1) · security-audit · hallucination-check
  AGENTS.md 208 (rol/routing) · WORKFLOW.md 203 (G1-G6 kapıları) · CONTEXT.md (envanter)
```

## 6. Öncelik Sırası (P0 → P3)

`P0` boot/ADR/sync/security/deploy akışları · `G2` orkestrasyon · `P2` sürüm
alanı tamamlama (`adr-creation`) · `P3` envanter tazeliği.

## 7. Kabul Kriterleri

1. 7/7 akış `workflow-instruction` frontmatteriyle okunur.
2. `WORKFLOW.md` G1-G6 kapıları akışlarda görünür.
3. `adr-creation.md` sürüm alanı eksikliği işaretli/giderilmiş (onayla).
4. Satır sayıları CONTEXT ölçümüyle eşit (1.873 / 2.335 — değiştiyse revize).

## 8. Dış Bağımlılıklar

`.ai/WORKFLOW.md` (authority — Derived) · `.ai/.decisions/` (ADR numarası SSOT) ·
`.ai/AGENTS.md` (agent registry) · kök `CLAUDE.md`.

## 9. Riskler

- 🟡 Elle düzenlenen akış → audit zinciri bozulur (düzeltme yalnız MO/vault-sync).
- 🟡 `adr-creation.md` sürüm alanı yok → sürüm takibi kaybolur.
- 🟡 Akış ↔ kural farklı hızda değişir; kural gömülürse her revizyonda aranır
  (CONTEXT §tasarım kararı 4: kural ayrı dosyada).

## 10. Başarı Ölçütleri

akış 7/7 geçerli · kapı ihlali 0 · onaysız değişiklik 0 · envanter ölçüm tarihli.

## 11. Atılacak Adımlar (24–48h)

1. Satır/sayı ölçümünü yenile (11 dosya; 1.873 / 2.335).
2. `adr-creation.md` sürüm alanını onayla-gir.
3. Bu PRD'yi kök `PRD.md` §5 tablosuna satır olarak ekle (orkestratör akışı).

## 12. Referanslar

`[[CONTEXT]]` · `[[AGENTS.md]]` · `[[WORKFLOW.md]]` · `.ai/WORKFLOW.md` ·
`.ai/.decisions/index.md` · `@PRD.md` (aile indeksi).

## 13. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-23 | İlk sürüm (CONTEXT envanteri 2026-10-12 kanıtı) |

## 14. Ekipler / Sahiplik

MO (vault-sync + adr-creation) · Security (security-audit) · DevOps (deployment) ·
orkestratör (orchestrator-flow) — tam sahiplik `[[AGENTS.md]]` §routing.

## 15. Onay

**Onaylamayan sorumlu:** MO · **Onay tarihi:** 2026-10-23 ·
**Karar mercii:** Ürün Sahibi (süreç kapısı değişiklikleri).
