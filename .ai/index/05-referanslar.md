---
title: "Referanslar"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 30.0.0
updated: 2026-10-12
total_files: 665
total_adr: 91
total_adr_disk: 91
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: []
---

# Referanslar

## References

### §5 Mimari Kararlar (ADR)

**SSOT:** `.ai/.decisions/index.md` (v1.5.0, 2026-10-12) — bu sayfada ADR listesi **kopyalanmaz**; yalnız sayılar ve işaretçi taşınır.

| Durum | Sayı (2026-10-12 disk ölçümü) |
|-------|-------------------------------|
| Frozen (001-036) | 36 |
| Active (037-101) | 45 |
| Rejected (R-001..R-010) | 10 |
| Draft | 0 |
| **Toplam dosya (disk)** | **91** |

> **Not (2026-10-12):** Eski §5.1 (Frozen 001-037, 37 satır) ve §5.2 (Active 038-089, ~30 satır) tabloları kaldırıldı — sayılar (80 / 37 / 31 / 12) disk gerçeğiyle çelişiyordu ve **ADR-061 (Electronics Architecture — K-space)** satırını taşıyordu. ADR-061, K-space katman mimarisiyle birlikte silindi (yedek: `C:\temp\opencode\vault-backup-2026-10-12\decisions-adr061\`).

#### §5.1 Frozen (001-036)

Liste: `.ai/.decisions/index.md` §3 (frozen — değiştirilemez).

#### §5.2 Active (037-101)

Liste: `.ai/.decisions/index.md` §4 (active — güncellenebilir; dosyasız kararlar brain.md appendix'indedir ve §2'de işaretlidir).

#### §5.3 Reddedilen Kararlar

| Dosya | Kapsam |
|-------|--------|
| [[.decisions/rejected/index]] | Reddedilen ADR listesi ve gerekçeleri |

---

### §17 Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 SSOT | [[CLAUDE.md]] | Ana sözleşme |
| § 4 Mimari | [[.decisions/index]] | ADR tabanlı mimari kararlar (K-space kaldırıldı 2026-10-12) |
| § 5 ADR | [[CLAUDE.md]] ADR-042-vault-restructuring-2026-08-03 | Vault standardı |
| § 6 Servisler | [[ecosystem/service-integration]] | Servis entegrasyonu *(yol düzeltildi 2026-10-07)* |
| § 7 Agentlar | [[AGENTS.md]] | Agent yetkileri |
| § 8 DB | [[.sql/mysql]] | 18 BCNF şemaları |
| § 9 Projeler | [[projects/NevaEngine/overview]] | C++ ses motoru |
| § 10 Donanım | [[electronic/hardware-roadmap]] | 3 fazlı geliştirme |
| § 11 Test | [[testing/coverage-targets]] | Kapsama hedefleri |
| § 4A UI Design | [[ui-design/01-mockup-index]] | 19 PNG, C01-C16, Mockup SSOT |
| § 4A.1 Device-Aware | [[brain.md]] §18C | Backend/Frontend sorumluluk sınırları, Tek Bileşen İlkesi |

---

### §24 Referans Linkleri (Dış Kaynak — 2026-10-01)

**awesome-opencode**
- https://github.com/NacioFelix/awesome-opencode
- https://github.com/weisser-dev/awesome-opencode
- https://github.com/jamait/awesome-opencode-skills

**agent skills**
- https://github.com/agentskills/agentskills
- https://github.com/j4flmao/agent-skills
- https://github.com/JazzaAI/agent-skills
- https://github.com/iannil/skills
- https://github.com/luckys/agent-skills
- https://github.com/tars-agentic/agent-skills

**dotnet**
- https://github.com/dotnet/skills
- https://github.com/DevExpress/agent-skills

**agent engineering / practices**
- https://github.com/DenisSergeevitch/agents-best-practices
- https://github.com/obra/superpowers

**frontend**
- https://github.com/vercel-labs/agent-skills
- https://github.com/lumpinif/frontend-ui-engineering
- https://github.com/hueyexe/frontend-agent-skills
- https://github.com/Junaid-PK/frontend-design-skill

**claude code**
- https://github.com/shanraisshan/claude-code-best-practice
- https://github.com/davila7/claude-code-templates
- https://github.com/ykdojo/claude-code-tips
- https://github.com/subinium/awesome-claude-code
- https://github.com/dazuiba/awesome-claude-code-1
- https://github.com/jqueryscript/awesome-claude-code
- https://github.com/itgoyo/awesome-claude-code

**mcp / agent platform**
- https://github.com/mctrinh/awesome-mcp-servers
- https://github.com/modelcontextprotocol/servers
- https://github.com/vakra-dev/awesome-ai-agents
- https://github.com/hammond01/CleanArchitecture

*Not:* harici kaynaklar vault'un (`.ai/`) altındadır; kural/çelişki durumunda SSOT kazanır. Kopya liste: kök `README.md` § "AI Agent & Skill Referansları".

### §18 Metadata

- **Toplam dosya:** 665 (`.ai` recursive, 2026-10-12 Get-ChildItem ölçümü) · **`.md`: 435** · **dizin: 72** — önceki kayıtlar: 804 (2026-10-10), 587 (2026-09-24), 518 (2026-09-23), 787 (Faz 0 — güncel değil). 2026-10-12 düşüşün nedeni K-space kaldırımıdır (`architecture/` 46 md + `.superpowers/` 71 md + 4 mimari şablon + 1 workflow + ADR-061).
- **Toplam ADR:** **91 dosya diskte** (2026-10-12: `accepted/` 80 + `rejected/` 10 + kök 1 (`ADR-091-template-engine-no-eval.md`) · `draft/` 0). Eski "80 karar iddiası / 60 fiziksel dosya" ikili sayımı dosyalaşma tamamlandığı için geçersizdir; karar↔dosya uzlaşması: `.decisions/index.md` §2.
- **Versiyon:** 30.0.0
- **Son Güncelleme:** 2026-10-12 (K-space kaldırımı — §5 SSOT işaretçisine çevrildi; §17/§22 ölü `architecture/` linkleri temizlendi; sayılar disk ölçümüyle yeniden yazıldı)
  - **28.4.3 — Faz 8b (2026-09-29):** PNG envanter gerçeği: 151 hedef · 136 indirilen · 15 gizli node visible:false → API NULL · 13 legacy · toplam 149; "149/151 = 2 eksik" iddiası geçersiz.
  - **28.4.2 — Faz 7 (2026-09-29):** §19.5 disk gerçekliği (kök 6 · screens 21 · flow 21 · prompt 51 · reference 17 · tokens 4+7 · PNG 19 · figma png 151 hedef / 136 indirilen · raw 19 JSON/79.7 MB) eklendi; §4A/§11'de olmayan ekran ve prompt yolları diskteki gerçek adlarla değiştirildi (B-home, E-filemanager, F-quickpanel, 04-vault-registration, 01-1024-embedded, 01-pattern-standard-60-40 → mevcut dosyalar); mockup-index kırılımı 12+1+6'ya tamamlandı.
  - **28.4.0 — Faz 2 (2026-09-27):** .personas 68/68 persona yeniden yazıldı (35.026 satır, ≥500 oranı %100), 4 dosya yeniden adlandırıldı, kırık wiki-link 7→0, personas/index.md sayaçları disk gerçeğiyle hizalandı, templates registry #38.
- **Governance:** Red Team · Human Mode · Truth Mode

---

### §22 PDF Dokümantasyon Yapısı (v1.0)

#### §01 Giriş
- CoreMusic tanımı ve vizyonu
- 10 temel özellik
- 7 kullanım alanı
- 6 hedef kullanıcı grubu
- 6 sektörel çözüm

#### §02 Sistem Mimarisi
- Subdomain topolojisi (6 subdomain + `shared/` ortak çekirdeği)
- Servis diyagramı
- Veri akışı

> **Not (2026-10-12):** Eski "4 katmanlı basitleştirilmiş mimari (L0-L3)" ve "7 katmanlı detaylı mimari (K0-K6)" satırları K-space katman mimarisiyle birlikte kaldırıldı — PDF planında katman sayımı taşınmaz.

#### İlgili Dosyalar
- [[VISION]] — CoreMusic vizyonu
- [[PROJECTS]] — Proje tanımı
- [[.decisions/index]] — ADR indeksi (mimari kararlar)

---

**Authority:** Bayram Ali / Vault Steward
**Version:** 30.0.0
**Last Updated:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
