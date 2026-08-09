# WORKFLOW.md

**⚠️ BU DOSYA BİR İŞARETÇİDİR (POINTER FILE) — ADR-042 (2026-08-03)**

**Kök `WORKFLOW.md` artık sadece bootstrap işaretçisidir. Asıl süreçler kanonik olarak `.ai/WORKFLOW.md` içindedir.**

**Root `WORKFLOW.md` is now a bootstrap pointer. The canonical workflows live in `.ai/WORKFLOW.md`.**

---

## 10-Step Boot Protocol (Zorunlu)

Her AI asistanı her oturumda şu 9 dosyayı okumalıdır:

| # | Dosya |
|---|-------|
| 1 | `.ai/CLAUDE.md` |
| 2 | `.ai/AGENTS.md` |
| 3 | `.ai/WORKFLOW.md` |
| 4 | `.ai/index.md` |
| 5 | `.ai/keys.md` |
| 6 | `.ai/AGENTS.md` |
| 7 | `.ai/brain.md` |
| 8 | `.ai/MEMORY.md` |
| 9 | `.ai/log.md` |

Madde 10-13 operasyonel alt-adımlar (ADR-042/C8).

---

## 12-Phase Vault Refactoring (Özet)

```
1. Repository Discovery
2. AI Knowledge Discovery
3. Existing Markdown Analysis
4. Conflict Detection
5. Duplicate Detection
6. Gap Detection
7. ⛔ Improvement Proposal → WAIT USER APPROVAL
8. Document Refactoring (In-Place)
9. Cross Reference Update
10. Index Update
11. Validation
12. Quality Report & Vault Sync
```

Tam sürüm: **[[.ai/WORKFLOW.md]]**

---

## 20-Phase Product Lifecycle (Özet)

```
1-6.   Vizyon, DDD Domain Analizi, Use Case'ler, Akış, Bilgi Mimarisi
7-9.   Teknik Mimari (L0-L3), Diyagramlar, Klasör Yapısı
10-14.  Kod Standartları, UI/UX, API, BCNF Veritabanı, OWASP Güvenlik
15-18.  Test Stratejisi, CI/CD, Dokümantasyon
19-20.  MVP Sürümü, 5 Yıllık Yol Haritası
```

---

## MSA Limit (ADR-042/C5)

**Görev başına MAX 15 dosya** (token ekonomisi).

---

## 4 Hard Rules (Süreç)

1. **User Approval Gate (Hard Rule):** İyileştirme teklifi veya mimari plan onaylanmadan (Phase 7) kod/doküman revizyonu başlatılamaz.
2. **In-Place Modification (Hard Rule):** Doküman güncellemeleri aynı dosya üzerinde yapılmalı; adı/konumu onay alınmadan DEĞİŞTİRİLEMEZ.
3. **No Hallucination (Hard Rule):** Doğrulanamayan bilgiler KESİNLİKLE uydurulamaz; `**VERIFICATION REQUIRED**` yazılmalıdır.
4. **Skill Zorunluluğu (Hard Rule):** Vault değişikliği varsa her seans sonunda `vault-sync` zorunludur. Tüm eylemler `.ai/log.md`'ye işlenmelidir.

---

## ADR Yaşam Döngüsü

```
Draft → Review → Active → Frozen
```

- **ADR-001'den ADR-037'ye kadar** olan kararlar **immutabledır (değiştirilemez)**
- **ADR-038+** yeni aktif kararlar `accepted/` dizinine eklenir
- Yeni teklif: `.ai/decisions/accepted/adr-NNN-konu.md`

---

## Workflow Files (`.workflows/`)

| Workflow | Amaç |
|----------|------|
| `session-init.md` | Yeni oturum başlatma |
| `adr-creation.md` | ADR oluşturma |
| `vault-sync.md` | Vault senkronizasyonu |
| `security-audit.md` | Güvenlik denetimi |
| `deployment.md` | Deployment |
| `hallucination-control.md` | Halüsinasyon kontrolü |

---

## Cross References

- **[[README.md]]** — İnsana bakan giriş noktası
- **[[.ai/CLAUDE.md]]** — Kanonik AI talimatı
- **[[.ai/AGENTS.md]]** — Kanonik agent kayıt defteri
- **[[.ai/WORKFLOW.md]]** — **Kanonik süreçler (asıl kaynak)**
- **[[CLAUDE.md]]** — Kök AI sözleşmesi (işaretçi)
- **[[AGENTS.md]]** — Kök agent kayıt defteri (işaretçi)
- **[[.workflows/]]** — 6 executable workflow
- **[[.ai/decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]** — 8 çelişki çözümü

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-04 (P3 trim)
**Mode:** Red Team • Human Mode • Truth Mode
