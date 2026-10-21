---
title: "php-backend-standards — Changelog"
type: reference
category: skills
version: 4.0.0
status: active
date: 2026-10-21
updated: 2026-10-21
---

# Changelog — php-backend-standards

> Kayıt kuralı: her sürümde **ne silindi / ne eklendi / ne düzeltildi** üç başlık.
> Düzeltme (fact-fix) satırları kaynak gösterir → `references/sources.md`.

## 4.0.0 — 2026-10-21 (Dalga 1/3 — tam yeniden yazım)

### Eklendi
- `references/sources.md` — 11 resmi/web kaynağı + 10 disk kaynağı, erişim tarihi 2026-10-21 (N4).
- `references/changelog.md` — bu dosya (N10).
- SKILL.md § katmanları: Clean Architecture katman tablosu, karar tabloları (decision tables),
  doğru/yanlış kod çiftleri, failure mode tablosu, OWASP Top 10:2025 eşlemesi.
- PSR-15/PSR-14/PSR-12/PSR-7 imza referansları (doğrulanmış).
- PHP 8.4 sürüm-durumu tablosu (8.4 security bitişi 31 Dec 2028 — 2026-10-21 itibarıyla geçerli).

### Değiştirildi (fact-fix)
- **ADR-084 "diskte YOK" iddiası kaldırıldı** — `.ai/.decisions/accepted/ADR-084-api-gateway-architecture.md`
  diskte mevcut. Eski 3.0.0 metni bunu yanlış söyleyordu. Yeni durum: dosya VAR,
  fakat `OpenAPI spec` ve `CQRS` **kod tarafında PLANNED** (0 glob / 0 grep).
- **ADR-086 "diskte YOK" iddiası kaldırıldı** — dosya diskte mevcut;
  `shared/src/Events/EventDispatcher.php` `Psr\EventDispatcher\EventDispatcherInterface`
  implement ediyor, 12 event var. Eksik olan: production dispatch wiring (PLANNED).
- Middleware pipeline tablosu `.ai/CLAUDE.md` §6 ile birebir hizalandı (10 kapı + Controller).
- `examples/add-endpoint.md` tek dosyalık kısa listeden
  "raw request → contract → DTO → validation → use case → PDO repository → middleware → status/pagination"
  tam döngüye çevrildi.

### Silindi
- ADR-083'ün "vardır" izlenimi veren herhangi bir ifadesi — diskte dosya YOK,
  bundan böyle yalnız `⚠️ VERIFICATION REQUIRED` etiketiyle anılacak.
- `.ai/architecture/k9-api-routing/middleware-pipeline.md` referansı — diskte YOK (dead link).

## 3.0.0 — (önceki)

- İlk derin sürüm: `pdo-rules.md`, `middleware-pipeline.md`, `api-contract-flow.md`,
  `examples/add-endpoint.md` iskeletleri.
- SKILL.md 83 satır (bu sürümde 500+ satıra çıkarıldı).
- **Bilinen kusurları (4.0.0'da giderildi):** ADR-084/086 için yanlış "diskte YOK" iddiası ·
  kaynak defteri yok · karar/failure tabloları yok · SKILL.md 83 satır (N1 kılavuzunun altı).

## 2.0.0 / 1.0.0

- ⚠️ VERIFICATION REQUIRED — bu sürümler bu revizyon öncesi silinmiş/asla var olmamış olabilir;
  diskte changelog kanıtı yoktur. Uydurma tarih/sürüm yazmamak için satır tutulmadı.
