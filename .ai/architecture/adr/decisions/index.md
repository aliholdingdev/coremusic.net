---
title: "Mimari ADR Kararları — Dizin"
type: index
category: architecture
date: 2026-10-12
status: active
version: 1.0.0

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

group: mimari
---

authority: "Mimari seri — SSOT: .ai/architecture/adr/index.md"
---

# Mimari ADR Kararları

**Durum:** boş — bu seride henüz ADR yok.

İlk kayıt başlığı: `ADR-0NN-mimari-<konu>.md` (Nygard şablonu, `.ai/.templates/adr/adr-nygard-template.md`).

> K-Space V3 kurulumuyla ilgili karar **bu seride değil**, karar serisindedir:
> `.ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` (onay bekliyor).

## §A Kayıt akışı (mimari seri — bu diziye ADR nasıl eklenir)

| # | Adım | Kaynak | Not |
|---|---|---|---|
| 1 | Konu adayı belirle (mimari karar — ürün/operasyon değil) | `.ai/architecture/**` kapsamı | karar serisi (ADR-090+) ile karıştırılmaz |
| 2 | Nygard şablonunu aç | `.ai/.templates/adr/adr-nygard-template.md` | Status · Context · Decision · Consequences |
| 3 | Dosya adını biçimlendir | `ADR-0NN-mimari-<konu>.md` | N = sıradaki numara (seri 001'den başlar) |
| 4 | `group: mimari` ekle (frontmatter 9. alan) | registry §8.1 | bu serideki tüm ADR'ler mimari grubundadır |
| 5 | Bağımsızlık/alternatif/sonuç bölümlerini doldur | şablon §4-§6 | her iddia kaynaklı olur |
| 6 | Onay ve kayıt | Vault Steward | onaysız seriye dosya yazılmaz |

**Seri durumu (2026-10-13):** bu dizinde ADR **0** — ilk kayıt henüz açılmadı.
Boş sayfa iddia değildir; `## §GRUPLAR` tablosu ilk dosya açılınca genişletilir.

**Authority:** Bayram Ali / Vault Steward
