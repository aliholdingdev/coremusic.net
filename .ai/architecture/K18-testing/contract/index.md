---
title: "K18-testing/contract — Subdomain Kaydı"
node_id: K18-testing/contract
node_type: subdomain
status: draft
parent_id: K18-testing
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K18-testing    # domain kökü
evidence:
  - "shared/tests/Api/ApiResponseTest.php · RouteTableTest.php · VersioningTest.php — glob 2026-10-12 (master §3 satır 3 ile birebir)"
  - "GatewayMethodRoutingTest · RequestValidationMiddlewareTest · ResponseNormalizationMiddlewareTest · DtoTest · ApiPipelineSecurityTest — aynı dizin, 8 dosya toplam"
validation:
  - "glob 2026-10-12 — API sözleşmesi testleri var"
open_questions:
  - "Sözleşme testleri OpenAPI şemasına mı, el yazımı beklentiye mi dayanıyor? şema dosyası diskte görünmüyor (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K019-testing/contract

## 1. Rol (ELI15)

API'nin dışa verdiği **sözleşme** (request/response şekli, versiyonlama, routing) bozulmadı mı — tüketici tarafını kırmadan değiştirebilmek için.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K18-testing altındadır.
- API'nin kendisi (endpoint/middleware) K009-security / backend alanıdır; burada yalnız **sözleşme uyumu**.
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıtlı — 8 dosya) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K019-testing) · master: testing-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
