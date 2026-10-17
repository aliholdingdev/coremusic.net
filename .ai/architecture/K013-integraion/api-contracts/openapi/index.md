---
node_id: K013-integraion/api-contracts/openapi
node_type: technology-deepdive
title: "OpenAPI — Kademe-3 Teknoloji Derin-Dalı İndeksi"
type: technology-deepdive
status: draft
date: 2026-10-13
parent_id: K000–K5000 Architecture Registry
children:
  - openapi-anayasa
  - openapi-sozlesme-dili
  - openapi-dogrulama
  - openapi-kod-ornekleri
  - openapi-web-arastirma
depends_on:
  - K013-integraion/api-contracts
  - K013-integraion
  - K007-data
evidence:
  - "openapi-anayasa.md §Kaynak Tablosu [S1]-[S10] (2026-10-12 çekimi, 10 URL)"
  - "openapi-kod-ornekleri.md §Kaynak Tablosu A/B [R1]-[R5] + [S1]-[S6] (2026-10-12 disk okuması + 6 URL)"
  - "openapi-web-arastirma.md §Kaynak Tablosu [W1]-[W15] (2026-10-12 HEAD 200, 15 URL)"
  - "openapi-sozlesme-dili.md §Kaynak Tablosu (13 URL · 2026-10-13 HEAD 200)"
  - "openapi-dogrulama.md §Kaynak Tablosu (8 URL · 2026-10-13 HEAD 200)"
  - "K013-integraion/api-contracts/index.md (kademe-2 çocuk tanımı)"
  - "shared/src/Api/Routing/RouteTable.php (253 satır, 2026-10-13 okundu)"
validation:
  - "Her içerik dosyası ≥500 satır (anayasa 573 · sozlesme-dili 511 · dogrulama 501 · kod-ornekleri 715 · web-arastirma 517; index hariç 40–100)"
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-13 HEAD 200)"
  - "Uydurma sürüm/arayüz/dosya adı yok; doğrulanamayan satır ⚠️ VERIFICATION REQUIRED / UNKNOWN işaretlidir"
open_questions:
  - "CoreMusic'te OpenAPI belgesi gerçek repo'da var mı? → ⚠️ VERIFICATION REQUIRED (shared/src altında bu turda tespit edilmedi)"
  - "Sözleşme doğrulama üretimde mi testte mi? → UNKNOWN (karar ADR bekliyor)"
  - "K012/integration-service bu belgeyi üretiyor mu, tüketiyor mu? → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# OpenAPI — Kademe-3 Teknoloji Derin-Dalı

> **Bu klasör ne değildir:** OpenAPI aracı kurulumu veya kod üretim rehberi değildir.
> Bu klasör, CoreMusic'in **dışarıya açtığı ve içeriden tükettiği** HTTP API
> sözleşmelerini *belge kanıtıyla* tanımlar; sayılar ve sürümler
> `spec.openapis.org` / `swagger.io` sayfalarından alınmıştır, tahmin değildir.
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Kod ⚠️ PLANNED
> (OpenAPI belgesi `.yaml`/`.json` çıktısı bu turda diske tespit edilmedi).

---

## §0 ELI15 — OpenAPI bir cümlede

OpenAPI, bir HTTP API'nin ne yaptığını "insanın da makinenin de okuyabileceği" tek bir standart
dosyada anlatan tarafsız bir tanımdır: hangi uç nokta var, hangi parametre alıyor, hangi durum
kodunu döndürüyor, hangi şemaya uyuyor; bunların hepsi dile (JSON/YAML) bağımlı olmadan yazılır
ve o tanımın üstüne çizen araçlar doküman/istemci/sunucu iskeleti üretebilir (Kaynak: `openapi-anayasa.md` §0 — [S1], [S2]).

---

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan (``group`` çift-backtick).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | openapi-anayasa.md | Kimlik, tarihçe, sürümler, belge yapısı, `-bilesenler` karşılığı | 3 |
| mimari | openapi-sozlesme-dili.md | Path/operation/parameter/requestBody/response/security + `$ref`/bileşim/sürüm farkı | 3 |
| kod | openapi-dogrulama.md | Doğrulama zinciri: JSON Schema, dialect, linting, CI kapısı | 3 |
| kod | openapi-kod-ornekleri.md | Gerçek YAML/PHP/JS örnekleri, repo kanıtlı veya kaynak-etiketli | 3 |
| kanit | openapi-web-arastirma.md | ≥8 benzersiz resmi URL + iddia/URL/tarih/durum tablosu | 3 |

**Gruplama kuralı:** registry §8.1 + ADR-097 §5.5 — `{kategori}/{teknoloji}/{teknoloji}-{rol}.md`.
`group: mimari | kod | kanit | teknoloji` alanı her dosyanın frontmatter'ında zorunludur.

---

## §1 Hiyerarşi ve Bağımlılık Özeti

| Kademe | Düğüm | Ne katar |
|---|---|---|
| 0 | `K000–K5000 Architecture Registry` | Bütün ağacın kökü |
| 1 | `K013-integraion` | Dış dünya köprüsü — kontratı yazar, adaptörü çevirir |
| 2 | `K013-integraion/api-contracts` | Dış API'nin istek/yanıt sözleşmesi (8 kardeşten biri) |
| **3** | **`K013-integraion/api-contracts/openapi`** | **Sözleşmenin yazıldığı somut standart: OpenAPI** |

**Yukarı bağımlılık (depends_on):** `K013-integraion/api-contracts` → `K013-integraion` → `K007-data` (veri tipi karşılığı) ·
**Aşağı kullanım (used_by):** `K012-services` (dış API çağrısı) · `K010-network` (HTTP taşıma) · `K011-application` (dış çağrı Sözleşmesi) ·
Kademe-2 kardeşleri: `adapters` · `external-systems` · `event-contracts` · `protocol-adapters` · `synchronization` · `error-handling` · `integration-testing`.

---

## §2 Dosya Okuma Sırası ve Açık Sorular

**Okuma sırası (öneri):** `anayasa` (nedir/hangi sürüm) → `sozlesme-dili` (uç nasıl tarif edilir) → `dogrulama` (yanlışlık nasıl yakalanır) → `kod-ornekleri` (gerçek örnekler) → `web-arastirma` (her iddianın arkasındaki URL).

**Açık sorular (tam liste her dosyanın `open_questions` alanında):** (1) CoreMusic repo'sunda OpenAPI belgesi var mı → ⚠️ VERIFICATION REQUIRED · (2) Doğrulama üretimde mi testte mi → UNKNOWN ·
(3) K013 mü yazar, K012/integration-service mi tüketir → ⚠️ VERIFICATION REQUIRED · (4) `event-contracts` (webhook) için ayrı mı, bu şemaya mı bağlanır → UNKNOWN

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · `.ai/architecture/index.md` §5/13 · ADR-097 §5.5 · registry §8.1 · **Tarih: 2026-10-13**
