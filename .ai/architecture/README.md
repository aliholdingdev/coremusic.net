---
title: "README — .ai/architecture Yönelim Dosyası"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız yönelim/okuma sırasıdır"
updated: 2026-10-07
tier: 3
domain: architecture-readme
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# README — .ai/architecture Yönlendirme

> **Ne bu ağaç?** CoreMusic'in 500-katmanlık (K000–K499) enterprise mimari envanteri.
> 2026-10-07'de **sıfırdan, disk kanıtıyla** yeniden yazıldı (eski ağaç 2026-10-06'da silinmişti — P2-12).

## 1. Hızlı Okuma Sırası

1. [[architecture/00-master-index]] — giriş: domain eşlemesi, K000–K499 sayımı, durum efsanesi, Link-Status
2. İlgisi olan **domain tablosu** (10 dosya) — 50 katmanlık tam satır listesi (status + kanıt)
3. Stub-with-truth dosyalar (yalnız eski vault linklerinin hedefleri) — hepsi "bugünkü karşılığı" gösterir
4. Kural SSOT'u [[architecture/rules]] · hot-memory [[architecture/context]] · Faz 0 katalog [[architecture/00-enterprise-index]]

## 2. Domain Tabloları (500 satır)

| Dosya | Domain | K-aralığı |
|-------|--------|-----------|
| [[architecture/10-domain-d01-isletim-sistemi-platform]] | d01 İşletim Sistemi & Platform | K000–K049 |
| [[architecture/11-domain-d02-ses-motoru-dsp]] | d02 Ses Motoru & DSP | K050–K099 |
| [[architecture/12-domain-d03-donanim-surucu]] | d03 Donanım & Sürücü | K100–K149 |
| [[architecture/13-domain-d04-veri-yonetimi]] | d04 Veri Yönetimi | K150–K199 |
| [[architecture/14-domain-d05-guvenlik-middleware]] | d05 Güvenlik & Middleware | K200–K249 |
| [[architecture/15-domain-d06-servis-api]] | d06 Servis & API / Routing | K250–K299 |
| [[architecture/16-domain-d07-uygulama-ux]] | d07 Uygulama & UX | K300–K349 |
| [[architecture/17-domain-d08-yapay-zeka-medya]] | d08 Yapay Zeka & Medya | K350–K399 |
| [[architecture/18-domain-d09-izleme-cicd-ag]] | d09 İzleme, CI/CD & Ağ | K400–K449 |
| [[architecture/19-domain-d10-elektronik-tasarim]] | d10 Elektronik Tasarım | K450–K499 |

## 3. Okuma Kuralları (Zero-Hallucination)

1. **Her satır** bir K-id + durum + kanıt (dosya yolu/ADR) **veya** `⚠️ VERIFICATION REQUIRED` taşır —
   kanıtsız iddia yazmak yasaktır.
2. Durum sözlüğü: 00-master-index §3 (IMPLEMENTED / PARTIAL / PLANNED / DESIGN / DEPRECATED).
3. **Hardware/domain tasarım gerçeği:** K16–K20 (d10) ve donanım satırları **DESIGN**'tır — kod/üretim YOK.
4. Yeni dosya yalnız yüksek değerli (vault'ta link'lenen) hedefler için açılır; her katman için
   ayrı MD **üretilmez** (hibrit multi-MD: tablo esas, MD değişkendir).
5. `node .ai/scripts/validate.mjs --check` bu ağacı da tarar (13 alanlı frontmatter zorunlu).

## 4. Dizin Sözlüğü (geriye-uyum)

`k0-isletim-sistemi` … `k20-bom` + `firmware/` = eski wiki-link dizinleri (stub index'ler korunur) ·
`03-contracts/` · `02-deployment/` · `06-audio/` · `07-security/` · `l1-security/` · `l2-routing/` ·
`l3-presentation/` · `k0-k5-software/` · `k16-k20-electronics/` = eski vault hedefleri (stub-with-truth).
Eski ağacın tamamı için: `00-master-index` §4–§6.

## 5. Kapsam Dışı (bu ağaçta YOK — bilinçli)

- ADR'ler → `.ai/.decisions/accepted/` (asla `architecture/adr/` altına konmaz — rules.md R1)
- Script'ler → `.ai/scripts/` (asla `architecture/scripts/` altına konmaz)
- 06-audio/* servis tasarım dokümanları ve ai/* bilgi-tabanı eski ağacı → **kasıtlı üretilmedi**;
  linkleri çözülmeyen hedefler 00-master-index §6'da raporlanır.
