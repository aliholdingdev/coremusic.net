---
title: "K020-adr — ADR Kayıt Mekanizması (Decision Register)"
type: register
category: architecture
node_id: K020-adr/decision-register
node_type: file
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "SSOT: .ai/.decisions/index.md (karar serisi) · .ai/architecture/adr/index.md (mimari seri)"
related:
  - ".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md"
  - ".ai/.templates/adr/adr-nygard-template.md"
  - "../adr/index.md"
group: mimari
---


# K020-adr — ADR Kayıt Mekanizması (Decision Register)

## 1. Bu dosyanın rolü (ELI15)

Bir karar **nerede**, **hangi şablonla** ve **hangi durumda** saklanır sorusunun
cevabıdır. Bu dosya kararların kendisini taşımaz — kararların **nereye yazılacağını**
tarif eder. Karar metinleri serinin kendi klasörlerinde durur.

## 2. Kapsam / Kapsam dışı

| Kapsam | Kapsam dışı |
|---|---|
| Karar yazımı akışı (taslak → kabul/redd) | Kararların metinleri (seri klasörlerinde) |
| İki ADR serisinin ayrımı (karar vs mimari) | Mimari katman tasarımı (K0xx master dosyaları) |
| Şablon seçimi (Guardrail #16) | Vault dışı dokümanlar (README, sunum vb.) |
| Disk sayımı ve senkron kontrolü | Band-2..7 slot ataması (ADR-097 K5) |

## 3. Seri düzeni (pointer — iki seri bilerek ayrıdır)

| Seri | Kapsam | İndeks | Şablon |
|---|---|---|---|
| **Karar serisi** | Proje/mimari kararları — `ADR-001 … ADR-101` + `R-001 … R-010` | `@.ai/.decisions/index.md` | `@.ai/.templates/adr/adr-nygard-template.md` (v1.0.1 — Guardrail #16) |
| **Mimari seri** | Mimari şablon/katman tasarım kararları — kendi numara uzayı | `[[../adr/index]]` | mimari seri şablonları (`adr/templates/`) |

> **Ayrım kuralı:** ADR-026 §3.4 — iki seri **birleştirilmez**; aynı numara iki seride
> farklı şey ifade edebilir, karıştırılmaz. ADR-097 (K-Space V3 kurulumu) bu nedenle
> mimari seriye değil, karar serisinin `draft/` klasörüne yazılmıştır.

### 3.1 Karar serisi dizin düzeni

```text
.ai/.decisions/
├── index.md          — kabul/redd/taslak indeksi (§4A = taslaklar)
├── ADR-091-…md       — KÖKTE (yanlış konumda, indeks §4'te bağlantılandı)
├── accepted/         — kabul edilmiş ADR'ler (numaralı dosyalar + CLAUDE.md)
├── rejected/         — reddedilmiş kararlar (R-001…R-010 + index.md + CLAUDE.md)
└── draft/            — taslak ADR'ler (ADR-097 + CLAUDE.md)
```

## 4. Mevcut disk durumu (ölçüm: 2026-10-12, bu oturum — dosya sayımıyla)

| Klasör | Dosya sayısı | İçerik |
|---|---|---|
| `accepted/` | **80** ADR metni (+`CLAUDE.md`) | `ADR-001 … ADR-101` (arada numara boşlukları mevcut) |
| `rejected/` | **10** R metni (+`index.md`, `CLAUDE.md`) | `R-001 … R-010` |
| `draft/` | **1** ADR metni (+`CLAUDE.md`) | `ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` |
| kök | **1** ADR metni (+`index.md`, `CLAUDE.md`) | `ADR-091-template-engine-no-eval.md` (yanlış konum) |
| **TOPLAM** | **92** ADR/R metni | — |

> **⚠️ SENKRON UYUŞMAZLIĞI (VERIFY REQUIRED):** `@.ai/.decisions/index.md` §4A bu
> ölçümde hâlâ *"Taslak yok — ADR-089 kabule terfi etti (2026-09-24, §4)"* yazıyor ve
> frontmatter `total-draft: 0` diyor. Diskte ise **1 taslak (ADR-097) var**. İndeks,
> ADR-097 yazılmadan önce ölçülmüş → **onaysız satır değiştirilmez**; senkron kaydı
> orkestratöre aittir. (`architecture/index.md` §9 faz A da "ADR-097 draft" der —
> çelişki yalnız indekstedir.)

## 5. Kayıt akışı (adımlar — sırayla)

1. **Karar olgunlaşır** → ilgili alanın `-dogrulama.md` / ADR gerekçesi toplanır.
2. **Şablon seçilir** → Guardrail #16: `@.ai/.templates/adr/adr-nygard-template.md`
   (güvenlik → `adr-security-template.md` · veritabanı → `adr-database-template.md` ·
   frontend → `adr-frontend-template.md` · ses → `adr-audio-template.md`).
3. **Taslak yazılır** → `.ai/.decisions/draft/ADR-###-<slug>.md` · `status: draft`.
4. **İncelenir** → red team / doğrulama turu; bağlam + karar + alternatif + sonuçlar zorunlu.
5. **Karar verilir** → kabul: `accepted/ADR-###-<slug>.md` · redd: `rejected/R-0##-<slug>.md`.
6. **İndeks güncellenir** → `.ai/.decisions/index.md` §3/§4/§4A/§5 + `total-draft`.
7. **Vault senkronu** → `log.md` append-only kaydı (subagent **commit atmaz**).

## 6. Durum geçişleri

```text
draft ──(kabul)──► accepted/     status: accepted
  │
  └──(redd)──────► rejected/R-0## status: rejected

draft ──(3 başarısız tur)──► DUR → üst karar (.ai/AGENTS.md §5/§7)
```

- `status` alanı v2 §4.2 sözlüğüne uyar: `draft` · `verified` · `verify-required` · `conflict`.
- **Numara atama yok** — ADR numarası serideki sıradır; `K021+` slot ataması ADR draft
  süresince yapılmaz (ADR-097 K5).

## 7. K020-adr ile bu defterin ilişkisi

| Soru | Cevap |
|---|---|
| K020-adr, ADR metinlerini kopyalar mı? | **Hayır** — yalnız mekanizmayı ve işaretçileri taşır. |
| Mimari seri (`architecture/adr/decisions/`) boş mu? | Evet — ilk mimari ADR henüz yazılmadı (`[[../adr/index]]` dizini). |
| Karar serisi hangi dosyayla senkronlanır? | `.ai/.decisions/index.md` — bu dosya onu **okur**, değiştirmez. |

## 8. İlişkili kararlar

| Karar | Ne getirdi | Konum |
|---|---|---|
| ADR-097 (draft) | K-Space V3 sıfırdan kurulum · çok-MD şema · band rezervasyonu · §5 isimlendirme · §6 açık sorular | `.ai/.decisions/draft/` |
| ADR-096 | Önceki K-Space V2 rejimi — **silinmiş numara**, kurtarma yok (ADR-097 §1.1) | — |
| ADR-091 | Template engine (`no-eval`) — kökte, yanlış konumda | `.ai/.decisions/` kök |
| ADR-026 | İki ADR serisi ayrımı (§3.4) | `accepted/` |
| ADR-042 | Vault yeniden yapılandırma — satır-satır edit kuralı | `accepted/` |

## 9. Açık sorular (VERIFY REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| 1 | İndeks §4A "Taslak yok" ↔ disk 1 taslak (ADR-097) — hangisi esas, indeks ne zaman senkronlanır? | `⚠️ VERIFY REQUIRED` |
| 2 | Mimari seri ilk ADR'sini ne zaman açar — numara uzayı ne olacak (`ADR-0xx-mimari-…`)? | `⚠️ VERIFY REQUIRED` |
| 3 | Kökteki ADR-091 `accepted/` altına taşınacak mı? (taşıma onay gerektirir) | `⚠️ VERIFY REQUIRED` |
| 4 | Aradaki numara boşlukları (051, 053–055, 057, 060–061, 065–071, 080, 083, 087–088, 096) silinmiş mi, hiç yazılmamış mı? | `⚠️ VERIFY REQUIRED` |
| 5 | ⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1 | `⚠️ VERIFY REQUIRED` |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
