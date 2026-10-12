---
reference_doc: .ai/.templates/adr/adr-template.md (v2.0.2 — Guardrail #16)
title: "CoreMusic — Tek Deploy + Zorlanabilir Modül Sınırları (Modular Monolith)"
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
  source_of_truth: ".ai/architecture/coremusic-mimari-plani.md §8.2 · .ai/reports/2026-10-09-50-yillik-mimari-ilkeler-arastirmasi.md"
---

# CoreMusic — Tek Deploy + Zorlanabilir Modül Sınırları (Modular Monolith)

**Durum:** active (Kullanıcı onayı "devam" 2026-10-09)
**Tarih:** 2026-10-09
**Karar Veren:** Bayram Ali / Vault Steward
**İlgili ADR'ler:** [[rejected/R-012-microservices-architecture]] · [[accepted/ADR-039-7-service-platform-architecture]] · [[accepted/ADR-084-api-gateway-architecture]]

---

## 1. Bağlam (Context)

CoreMusic tek PHP 8.4 deployment birimidir; subdomain'ler (`api.` `auth.` `home.` `media.` `assets.`)
çalışma zamanında ayrı süreç değil, modül sınırıdır. Fowler (2015, MonolithFirst) ve Modular
Monolith literatürü (arXiv:2401.11867): mikroservis faydasının çoğu zorlanabilir modül sınırıyla,
dağıtık maliyetin hiçbiri olmadan elde edilir.

## 2. Karar (Decision)

1. **Tek deploy birimi** korunur; erken servis dağıtımı yasak (R-012 ile tutarlı).
2. Modül sınırları **kod tarafından korunur**: namespace/klasör düzeni + CI denetimi
   (ADR-098 kuralları; kademeli: uyarı → hata → engel).
3. `shared/` tek PSR-4 ortak pakettir (ADR-085); subdomain'ler/modüller birbirine kod
   import etmez, yalnız `shared/` + API sözleşmesi (ADR-084) ile konuşur.
4. Parçalanma (servis çıkarma) yalnız **sınırlar netleşince** ve ölçülmüş bir ihtiyaçla;
   her parça için ayrı ADR gerekir.

## 3. Sonuçlar (Consequences)

- ✅ Tek CI/CD, tek rollback (ADR-082 dev/staging ile paralel).
- ✅ Dağıtık sistem acıları (8 saçmalık, CAP) büyük ölçüde savuşturulur.
- ⚠️ `composer.json`'da `php-di`/PSR-11 container varlığı doğrulanmadı — bağımlılık ekleme
  gerekirse ayrı dependency-manager raporu (review şartı).

## 4. Reddedilenler

- Mikroservis dağıtımına geçiş (R-012, erken optimizasyon).
- ESB/WS-* tarzı merkezi entegrasyon katmanı (SOA etiketi öldü, ilke yaşadı — Manes 2009).

---

*ADR-099 — CoreMusic Vault · Mode: Red Team · Human Mode · Truth Mode*
