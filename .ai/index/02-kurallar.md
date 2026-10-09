---
title: "Kurallar"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 28.4.4
updated: 2026-10-06
total_files: 720
total_adr: 80
total_adr_disk: 60
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: []
---# Kurallar## Rules

### §11B Skills (8 Aktif Skill — Guardrail #16 Zorunlu)

> **Düzeltme (2026-09-24):** Disk kanıtı = **8 aktif SKILL.md**: agent-debate · composer-sync · context-report · db-engine · orchestration · truth-engine · ui-workbench · vault-sync-post (`_archive/` kaldırıldı; eski9 üyenin vault dosyaları hariç her şey `_archive-keep/` altında, toplam 20 dosya). Aşağıdaki 10 satır eski envanterdir — ADR-042 gereği SİLİNMEDİ; güncel sayım kök [[AGENTS.md]] §14.

| # | Skill | Amaç | Konum |
|---|-------|------|-------|
| 1 | `ui-code-generator` | UI/CSS kod üretimi, responsive tasarım | `.opencode/skills/ui-code-generator/SKILL.md` |
| 2 | `ui-analyzer` | UI analizi, mevcut tasarım değerlendirme | `.opencode/skills/ui-analyzer/SKILL.md` |
| 3 | `skill-maker` | Yeni skill oluşturma, template sistemi | `.opencode/skills/skill-maker/SKILL.md` |
| 4 | `hallucination-control` | Halüsinasyon kontrolü, doğrulama | `.opencode/skills/hallucination-control/SKILL.md` |
| 5 | `human-mode` | İnsan modu iletişimi, onay süreçleri | `.opencode/skills/human-mode/SKILL.md` |
| 6 | `red-team-truth-mode` | Güvenlik testi, adversarial analiz | `.opencode/skills/red-team-truth-mode/SKILL.md` |
| 7 | `prompt-maker` | Prompt mühendisliği, AI talimat tasarımı | `.opencode/skills/prompt-maker/SKILL.md` |
| 8 | `agent-orchestrator` | Agent görev dağıtımı, multi-agent koordinasyonu | `.opencode/skills/agent-orchestrator/SKILL.md` |
| 9 | `composer-sync` | Composer dependency yönetimi | `.opencode/skills/composer-sync/SKILL.md` |
| 10 | `database-normalize-maker` | BCNF normalizasyonu, şema tasarımı | `.opencode/skills/database-normalize-maker/SKILL.md` |

**Detay:** Her skill dosyası YAML frontmatter'da `reference:` bloğu ile vault'a bağlıdır.

---

### §12 Vault Altyapısı

> **⚠️ Güncellik Notu (2026-09-26):** Aşağıda referans verilen `sessions/`, `registry/`, `scaffold/`, `knowledge/`, `confidence/`, `research/`, `workflows/`, `testing/`, `projects/` dizinleri vault ağacında mevcut DEĞİLDİR; bu bölümdeki ilgili wiki-linkler kırıktır. Düzeltme seçenekleri (yeniden kurma / referans temizliği) onay listesindedir. **`personas/` maddesi listeden ÇIKARILDI:** persona envanteri **`.ai/.personas/` altında yeniden kuruldu (2026-09-26, disk kanıtlı)** — 5 kök doküman (`index`, `methodology`, `mood-taxonomy`, `test-scenarios-mapping`, `research-bank`) + `test-senaryolari/` (6 dosya) + 6 grup klasörü (`kiz-cocuk`, `genc-kiz`, `erkek-cocuk`, `genc-erkek`, `yetiskin-kadin`, `yetiskin-erkek`); Persona satırı artık kırık değildir (ADR-023 Şart 1c — v1.1.0).

| Kategori | Dosyalar |
|----------|----------|
| Session | [[sessions/index]] |
| Registry | [[registry/dashboard]], [[registry/lifecycle]], [[registry/projects.csv]] |
| Scaffold | [[scaffold/checklist]], [[scaffold/rules]] |
| Reports | [[reports/session-summary-template]] |
| Prompt Archives | [[brain#22-prompt-arsivi]] | 4 ana prompt: genel, SPA router, auth, API |
| Prompt Engine | [[architecture/ai/prompt-engine]] | Prompt üretim ve yönetim motoru |
| Knowledge | [[knowledge/verified]], [[knowledge/unverified]], [[knowledge/rejected]], [[confidence/README]] |
| Subdomains | [[subdomains/README]], [[subdomains/auth.coremusic.net/index]], [[subdomains/music.coremusic.net/index]], [[subdomains/download.coremusic.net/domains/index]] |
| UI-Design | [[ui-design/00-device-matrix]], [[ui-design/01-mockup-index]], [[ui-design/02-component-inventory]], [[ui-design/03-implementation-plan]], [[ui-design/04-accessibility-gaps]], [[ui-design/05-responsive-architecture]] |
| UI-Design Screens | [[ui-design/screens/00-ascii-art-index]] + 20 spec: [[ui-design/screens/T07-embedded/home-dashboard]], [[ui-design/screens/T07-embedded/albums]], [[ui-design/screens/T07-embedded/singer]], [[ui-design/screens/T07-embedded/playlist]], [[ui-design/screens/T07-embedded/browse]], [[ui-design/screens/T07-embedded/wifi-quick]], [[ui-design/screens/T17-monitor-22fhd/home-dashboard]], [[ui-design/screens/shared/login]] … |
| PNG Mockups | `.ai/.png/home-1024/` (12 PNG) + `.ai/.png/home-1920/` (1 PNG) + `.ai/.png/shared-1024/` (6 PNG) = 19 PNG |
| UI-Design Prompt | [[ui-design/prompt/00-prompt-index]], [[ui-design/prompt/screen/00-prompt-index]], [[ui-design/prompt/component/C01-nav-link]], [[ui-design/prompt/layout/01-mobile-stack]], [[ui-design/prompt/page/01-home]] |
| UI-Design Reference | [[ui-design/tokens/design-tokens-master]], [[ui-design/reference/01-php-source-architecture]], [[ui-design/reference/02-text-strings]], [[ui-design/reference/03-icon-asset-catalog]], [[ui-design/reference/04-verification]] |
| UI-Design Flow | [[ui-design/flow/00-flow-index]], [[ui-design/flow/auth/04-select-gender]] |
| Research | [[research/verified/php84-strict-types]], [[research/verified/argon2id]], [[research/verified/aes-256-gcm]], [[research/verified/pcm3168a]], [[research/verified/asio-sdk]], [[research/verified/juce8]], [[architecture/k1-donanim/xmos-xu316]], [[research/verified/trusted-types-domparser]], [[research/verified/itcss-bemit-layer]], [[research/verified/wcag-22-aa]], [[research/verified/mariadb-1011]] |
| Personas | [[.personas/index]], [[.personas/methodology]], [[.personas/mood-taxonomy]], [[.personas/research-bank]], [[.personas/test-scenarios-mapping]] |
| Templates | [[.templates/index]] — 2026-09-24 sayım: 28 dosya (26 şablon + index.md + CLAUDE.md; +2 yeni: claude-md, docs-md); eski ad listesi: PHP, JS, CSS, C++, PHPUnit, Vitest, Migration, GitHub Actions, API-doc, Security-audit, ADR, Arduino, AVR, PIC, C, Node.js, ASP.NET, WikiPage, Query, Session) — ⚠️ VERIFICATION REQUIRED: Arduino/AVR/PIC diskte yok, liste sahip onayına açık |
| Workflows | [[../.workflows/adr-creation]], [[workflows/dev-workflow]], [[workflows/code-review]], [[../.workflows/deployment]], [[../.workflows/hallucination-control]], [[../.workflows/security-audit]], [[../.workflows/session-init]], [[workflows/vault-sync-detailed]] |
| Root | [[engine]], [[index-overview]], [[index-services]], [[index-adr]], [[.decisions/index]], [[research/index]] |

---

