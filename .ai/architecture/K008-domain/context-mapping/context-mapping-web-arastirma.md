---
node_id: "K008-context-mapping-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/web-arastirma"
evidence:
  - "8 URL — context mapping / stratejik modelleme / geçiş paternleri (2026-10-15 tarama, hepsi HTTP 200)"
  - "k008-child-urls-ok.txt — havuz satır 1, 9, 10, 56, 66, 74, 75, 76"
validation: "2026-10-15 — 8 benzersiz URL; K008 geneli çakışma 0 (C4)"
open_questions:
  - "ContextMapper (W-2/W-3) → diskte karşılığı yok; yalnız referans (bilesenler §2)"
group: kanit
---

# context-mapping — Web Araştırması (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> 8 URL **yalnız bu dosyada** (C4). Doğrulama: **2026-10-15 (HTTP GET, hepsi 200)**.
> URL'ler havuzdan byte-exact kopyalanmıştır.

## §1 YENİ TARAMA (W-1…W-8 · 2026-10-15)

| # | URL | Ne için (bu dosyanın iddiası) | Durum |
|---|-----|-------------------------------|-------|
| W-1 | https://alistair.cockburn.us/hexagonal-architecture | Ports & adapters — bağlam sınırının kod içinde soyutlanma modeli (`DomainConfig` = config adapter'ı) | HTTP 200 |
| W-2 | https://contextmapper.org | ContextMapper — context mapping modelleme/otomasyon aracı (adaya aracın referans kaynağı) | HTTP 200 |
| W-3 | https://contextmapper.org/docs | ContextMapper dokümantasyonu — context map DSL ve stratejik modelleme rehberi | HTTP 200 |
| W-4 | https://en.wikipedia.org/wiki/strangler_fig | Strangler fig — eski yapıdan yenisine kademeli devretme paterni (eski K-space geçişi için) | HTTP 200 |
| W-5 | https://martinfowler.com/bliki/stranglerfigapplication.html | Strangler fig application — paternin tanımı ve uygulama kuralları | HTTP 200 |
| W-6 | https://www.amazon.com/domain-driven-design-reference-models-language/dp/0321601912 | Evans — DDD Reference: bounded context / context map terminoloji referansı | HTTP 200 |
| W-7 | https://www.amazon.com/domain-driven-design-tackling-complexity-software/dp/0321125215 | Evans — DDD kitabının kendisi: bounded context ve context map kaynağı | HTTP 200 |
| W-8 | https://www.amazon.com/Implementing-Domain-Driven-Design/dp/0321834577 | Vernon — Implementing DDD: context map'in uygulama boyutundaki karşılığı | HTTP 200 |

## §2 DAĞITIM (C4 KAYDI)

| Alıcı dosya | Adet | Havuz satırları |
|---|---|---|
| bu dosya (context-mapping) | 8 | 1, 9, 10, 56, 66, 74, 75, 76 |

- K008 genelinde aynı URL iki dosyada **bulunmaz** (C4) — 2026-10-15 sayım: 0 çakışma.
- Kardeş `context-mapping-dogrulama` bu URL'lere **pointer ile** geçer (`§1 W-x`).
- **Düzeltme 2026-10-15:** W-8 URL'i locale bozulması (`ı` U+0131 + alt çizgi) nedeniyle canonical biçime getirildi — HTTP GET 200 (2026-10-15) ile yeniden doğrulandı.

## §3 KAPSAM DIŞI

| Konu | Nereye gitti |
|---|---|
| Domain haritası/scheme-port çözümü kanıtları | [[context-mapping-dogrulama]] (repo içi) |
| Hexagonal mimarinin katman uygulaması | `.ai/.decisions/index.md` (ADR-039) |
| K006/K020 sahiplik çelişkisi (C2) | `domain-master.md` §6 — orkestratör kararı |
| Cihaz bağlamı (UA/X-Device-Type) | [[device-management-kapsam]] |

**Kardeş dosyalar:** [[context-mapping-kapsam]] · [[context-mapping-dogrulama]] · [[context-mapping-ornekler]] · [[context-mapping-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
