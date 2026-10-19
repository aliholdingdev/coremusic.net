---
title: "Mimari"
type: persona-index
category: personas
version: 1.0.1
status: active
authority: reference
updated: 2026-09-29
---# Mimari## §3 Mimari

### §3.1 Dizin Ağacı (hedef yapı — `.ai/.personas/`)

```text
.ai/.personas/
├── index.md                     → bu dosya — 68 persona kataloğu (kök)
├── methodology.md               → ✅ diskte (2026-09-26) — Level 1/2/3 test seviyeleri (ADR-023 referanslı)
├── mood-taxonomy.md             → ✅ bu oturumda yazıldı — mood küme sınıflandırması
├── test-scenarios-mapping.md    → ✅ bu oturumda yazıldı — persona × senaryo eşlemesi
├── research-bank.md             → 🔄 BAŞKA AJAN YAZIYOR — gerçek-dünya araştırma bankası
├── test-senaryolari/            → ✅ diskte (6 dosya, 2026-09-26) — 6 test senaryosu dosyası (farklı adım)
│   ├── a11y-erisilebilirlik.md
│   ├── arabesk-dans-mood-gecis.md
│   ├── browser-navigasyon.md
│   ├── muzik-kesfi.md
│   ├── playlist-olusturma.md
│   └── sosyal-paylasim.md
├── kiz-cocuk/                   → 17 persona (4-11)
├── genc-kiz/                    → 17 persona (12-17)
├── erkek-cocuk/                 → 12 persona (4-11)
├── genc-erkek/                  → 12 persona (12-17)
├── yetiskin-kadin/              → 5 persona (25-45)
└── yetiskin-erkek/              → 5 persona (25-45)
```

### §3.2 Dosya Roller Tablosu

| Dosya | Tip | Rol | Durum (2026-09-26) | Yazar |
|-------|-----|-----|--------------------|-------|
| `index.md` | `persona-index` | Kök katalog — 68 kişi, 6 grup, dizin ağacı | ✅ bu dosya | Vault Steward (subagent) |
| `mood-taxonomy.md` | `reference` | 25 mood kümesinin tam sınıflandırması | ✅ bu oturumda üretildi | Vault Steward (subagent) |
| `test-scenarios-mapping.md` | `reference` | 20 persona test matrisi + 6 senaryo eşlemesi | ✅ bu oturumda üretildi | Vault Steward (subagent) |
| `research-bank.md` | `reference` | Gerçek-dünya kaynak bankası (nüfus/cihaz/WCAG/KVKK) | 🔄 başka ajan yazıyor | **DOKUNULMAZ** |
| `methodology.md` | `guide` | Level 1/2/3 test seviyeleri, başarı metrikleri | ✅ diskte (2026-09-26) | QA Engineer |
| `<grup>/<ad-s-soyad>-<mood>.md` | `persona` | Tek persona profili (11 alan havuzu, ≥500 satır) | ✅ diskte (68 dosya, 2026-09-26) | QA Engineer + UX Researcher |
| `test-senaryolari/*.md` | `reference` | 6 E2E test senaryosu (eski vault'tan taşınacak) | ✅ diskte (6 dosya, 2026-09-26) | QA Engineer |

### §3.3 Bağımlılık Grafiği

```text
[[.templates/personas/persona-template]] (şablon #37)
        │  üretir
        ▼
  <grup>/<persona>.md ──── mood adı alır ───► [[.personas/mood-taxonomy]]
        │                                        │
        │ senaryo atar                          │ test etkisi
        ▼                                        ▼
[[.personas/test-scenarios-mapping]] ◄── bağlar ── [[.decisions/accepted/ADR-023-persona-driven-testing]]
        │                                   (§2.2a 20 satır · şart 1c)
        ▼
  test-senaryolari/*.md (6) ── kod'a taşınır ──► shared/tests · auth tests (@group persona-NN)
        │
        └── gerçek-dünya iddiaları ──► [[.personas/research-bank]] (⚠️ VERIFICATION REQUIRED pointer'ı)
```

| Bağımlılık | Yön | Not |
|------------|-----|-----|
| persona-template → bu katalog | şablon §7.1'de `[[.personas/index]]` 📋 planlanan satırı | hedef artık `.ai/.personas/index.md` |
| ADR-023 şart 1c → bu dizin | karar → uygulama | eski disk korunur, kopya yok (yalnız özet) |
| mood-taxonomy → persona dosyaları | küme adı zorunlu kaynağı | uydurma küme adı yazılmaz (şablon §3.5.4) |
| test-scenarios-mapping → ADR-023 §2.2a | 20 satır birebir eşleşir | §6'da çapraz kontrol |
| research-bank → her 3 kök dosya | `⚠️ VERIFICATION REQUIRED` pointer'ı | yazılmadan önce link `📋` kalır |

---

