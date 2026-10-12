---
reference_doc: .ai/.templates/adr/adr-template.md (v2.0.2 — Guardrail #16)
title: "CoreMusic — Cross-DB Yazmada Outbox Zorunlu; CQRS Yalnız Okuma Sorgusu Düzeyinde"
type: adr
category: decisions
date: 2026-10-09
updated: 2026-10-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/architecture/coremusic-mimari-plani.md §8.3 · .ai/reports/2026-10-09-50-yillik-mimari-ilkeler-arastirmasi.md"
---

# CoreMusic — Cross-DB Yazmada Outbox Zorunlu; CQRS Yalnız Okuma Sorgusu Düzeyinde

**Durum:** active (Kullanıcı onayı "devam" 2026-10-09)
**Tarih:** 2026-10-09
**Karar Veren:** Bayram Ali / Vault Steward
**İlgili ADR'ler:** [[accepted/ADR-081-multi-provider-data-sync]] · [[accepted/ADR-003-multi-db-bcnf]] · [[accepted/ADR-040-database-authority]] · [[accepted/ADR-014-multi-db-migration-strategy]]

---

## 1. Bağlam (Context)

Multi-DB (BCNF) yapında iki tehlike: (1) iki DB'ye aynı anda yazan iş akışında **dual-write**
(ikili kayıt) — biri başarılı diğeri başarısızsa veri kalıcı ayrışır; (2) tam CQRS + event
store'ün her sistem için gerekliliği varsayımı — literatür (CQRS post-mortem, rapor §6-E)
tam ayrık altyapının çoğu sistem için fazla karmaşıklık olduğunu gösterir.

## 2. Karar (Decision)

1. **Tek DB içi işlem**: tek transaction (doğal atomiclik — outbox gerekmez).
2. **Cross-DB yazma**: outbox tablosu + WAL zorunlu (ADR-081 modeli); dual-write yasak.
3. **CQRS yalnız okuma sorgusu düzeyinde**: read-only repository metotları (SELECT
   optimizasyonu, `SELECT *` yasak — ADR-002); ayrı okuma DB'si, event store veya write-behind
   cache **kurulmaz** (gereksiz karmaşıklık).
4. Migration: sıralı, geri alınabilir (ADR-014).

## 3. Sonuçlar (Consequences)

- ✅ Cross-DB tutarlılık kanıtlanabilir duruma gelir (CAP ödünleşimi bilinçli seçilir).
- ✅ Tek MySQL yapısı korunur; operasyon basitliği.
- ⚠️ "18 BCNF DB" sayısı **⚠️ VERIFICATION REQUIRED** (ADR-003/040 iddiası; çalışma ağacındaki
  `.ai/.sql/mysql/` sayımıyla doğrulanacak — Data Engineer handover).

## 4. Reddedilenler

- Dual-write (iki DB'ye doğrudan eşzamanlı yazma).
- Tam CQRS/event-store altyapısı (derecesi ihtiyaca göre; tam hali uygun değil — rapor §6-E).

---

*ADR-100 — CoreMusic Vault · Mode: Red Team · Human Mode · Truth Mode*
