---
reference_doc: .ai/.templates/adr/adr-template.md (v2.0.2 — Guardrail #16)
title: "CoreMusic — Modül Sınırı = Gizlenecek Karar (Parnas 1972)"
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
  source_of_truth: ".ai/architecture/coremusic-mimari-plani.md §8.1 · .ai/reports/2026-10-09-50-yillik-mimari-ilkeler-arastirmasi.md"
---

# CoreMusic — Modül Sınırı = Gizlenecek Karar (Parnas 1972)

**Durum:** active (Kullanıcı onayı "devam" 2026-10-09 · Expert+Senior review: ŞARTLI → şartlar kapatıldı)
**Tarih:** 2026-10-09
**Karar Veren:** Bayram Ali / Vault Steward
**İlgili ADR'ler:** [[accepted/ADR-002-pdo-mandatory-no-orm]] · [[accepted/ADR-039-7-service-platform-architecture]] · [[accepted/ADR-086-event-driven-architecture]]

---

## 1. Bağlam (Context)

Parnas (1972, CACM 15(12)): modüller **gizli kararlara** göre bölünür; bir modülün veri şeması,
PDO sorgusu, dosya biçimi gibi kararları başka modüller asla görmemelir. CoreMusic'te 9 domain
(music · social · podcast · radio · ai · video · studio · cms · i18n — ADR-072..079 şemaları)
arasında sınır tanımsızlığı, şema sızıntısı ve kademeli bozulma riski doğurur.

## 2. Karar (Decision)

1. Her domain = **tek modül**: namespace + klasör + public arayüz (interface) + private iç.
2. **Modüller arası doğrudan sınıf/`new`/static çağrısı yasaktır**; iletişim yalnız
   (a) modülün public interface'i, veya (b) PSR-14 olayı (ADR-086) ile yapılır.
3. **Tablo sahipliği münhasırdır**: bir tabloyu tek modül YAZAR; okuma paylaşımlı olabilir
   (okuma yolu read-interface üzerinden — şema dışarı sızmaz).
4. Katman ihlali tespitinde: derhal revert + log ERROR (AGENTS.md §5).

## 3. Sonuçlar (Consequences)

- ✅ Domain değişikliği yalnız kendi modülünde değişir (bilgi gizleme kazanımı).
- ✅ Prensip kanıtı: Parnas'72 🟢 · Modular Monolith arXiv:2401.11867 🟢 (rapor §3/§5).
- ⚠️ Mevcut kod tabanında ihlal sayısı **UNKNOWN** (servis→repository oranı ölçülmedi) —
  Faz 0 ölçümü: CI denetimi (kural → uyarı modu → engel, kademeli).
- Şema paylaşımı okuma gerektiğinde read-interface zorunlu; ham repository paylaşımı ihlal sayılır.

## 4. Reddedilenler

- Modüller arası doğrudan repo/DB paylaşımı (Parnas ihlali).
- Mikroservis'e bölünme (R-012 — erken optimizasyon; bkz. ADR-099).

---

*ADR-098 — CoreMusic Vault · Mode: Red Team · Human Mode · Truth Mode*
