---
title: "00-master-index — CoreMusic Enterprise Mimari Master Index (K000–K499)"
type: index
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — .ai/architecture/ ağacının tek giriş noktası"
updated: 2026-10-07
tier: 3
domain: architecture-master
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: [".ai/CLAUDE.md"]
---

# 00-master-index — CoreMusic Enterprise Mimari Master Index

> **Giriş sözleşmesi:** Bu dosya `.ai/architecture/` ağacının tek girişidir.
> Kök `CLAUDE.md` §Mimari referans (2026-10-06 · v4.0.0): *"Enterprise Layered Architecture 500 katman (K000–K499), 10 domain, hibrit multi-MD. Giriş: @.ai/architecture/00-master-index.md → domain tabloları 10-domain-d01-….md … 19-domain-d10-….md. Sayım birimi = KATMAN (mantıksal doküman katmanı)."*
> **Durum (2026-10-07):** Eski 500-katman ağacı 2026-10-06'da silinmişti; bu dosya ve 10 domain tablosu **disk kanıtıyla sıfırdan** yeniden yazıldı (P2-12 kullanıcı kararı).

---

## 1. Kapsam ve Sayım

| Öğe | Değer | Kanıt |
|-----|-------|-------|
| Toplam katman (K000–K499) | **500** | 10 domain tablosu × 50 satır (sayım betikle doğrulanır — §5) |
| Domain sayısı | **10** (d01–d10) | `10-domain-d01-*.md` … `19-domain-d10-*.md` |
| Domain tablosu dosyası | 10 | glob: `.ai/architecture/*domain-d*.md` |
| Hibrit multi-MD | Evet — 10 tablo + kanıt stub'ları (MD sayısı değişken, sabit değil) | README §Yapı |
| Eski K0–K20 (21 katman) | Legacy görünüm — korunur, domain'lere eşlenir (§2) | kök `CLAUDE.md` §5 · `.claude/CLAUDE.md` §5 |

## 2. Domain ↔ K- Aralık ↔ Eski Dizin Eşlemesi

| Domain | Dosya | K-aralığı | Kapsam | Eski dizin(ler) (geriye-uyum linkleri) |
|--------|-------|-----------|--------|----------------------------------------|
| d01 | 10-domain-d01-isletim-sistemi-platform.md | K000–K049 | İşletim Sistemi & Platform | `k0-isletim-sistemi/` |
| d02 | 11-domain-d02-ses-motoru-dsp.md | K050–K099 | Ses Motoru & DSP | `k3-ses-motoru/` |
| d03 | 12-domain-d03-donanim-surucu.md | K100–K149 | Donanım & Sürücü | `k1-donanim/` · `k2-surucu/` |
| d04 | 13-domain-d04-veri-yonetimi.md | K150–K199 | Veri Yönetimi | `k5-veri-yonetimi/` |
| d05 | 14-domain-d05-guvenlik-middleware.md | K200–K249 | Güvenlik & Middleware | `k6-guvenlik/` · `k7-middleware/` |
| d06 | 15-domain-d06-servis-api.md | K250–K299 | Servis & API (routing dahil) | `k8-servis/` · `k9-api-routing/` |
| d07 | 16-domain-d07-uygulama-ux.md | K300–K349 | Uygulama & UX | `k10-uygulama/` · `k11-ux/` |
| d08 | 17-domain-d08-yapay-zeka-medya.md | K350–K399 | Yapay Zeka & Medya | `k4-yapay-zeka/` · `k15-medya-streaming/` |
| d09 | 18-domain-d09-izleme-cicd-ag.md | K400–K449 | İzleme, CI/CD & Ağ | `k12-izleme/` · `k13-cicd/` · `k14-ag/` |
| d10 | 19-domain-d10-elektronik-tasarim.md | K450–K499 | Elektronik Tasarım (donanım design) | `k16-class-ab/` … `k20-bom/` · `firmware/` |

**Legacy K0–K20 → domain eşlemesi:** K0→d01 · K1,K2→d03 · K3→d02 · K4→d08 · K5→d04 · K6,K7→d05 · K8,K9→d06 · K10,K11→d07 · K12,K13,K14→d09 · K15→d08 · K16-K20→d10.

## 3. Durum Efsanesi (satır seviyesi — zorunlu)

| Durum | Anlamı | Kanıt standardı |
|-------|--------|-----------------|
| **IMPLEMENTED** | Diskte kod/varlık olarak mevcut ve doğrulandı | Dosya yolu (glob/ls ile) veya ADR dosyası |
| **PARTIAL** | Bir kısmı mevcut, bir kısmı eksik | Hem varlık hem eksiklik kanıtı birlikte |
| **PLANNED** | Plan/ADR var, uygulama yok (veya yokluğu doğrulandı) | ADR/plan dosyası veya `⚠️ VERIFICATION REQUIRED` (yokluk glob ile) |
| **DESIGN** | Tasarım belgesi/bileşen listesi var, donanım/uygulama YOK | `.ai/brain.md` §5 · `.claude/CLAUDE.md` §5 · ADR-089/090 vb. |
| **DEPRECATED** | Kullanımdan kaldırıldı | Neden + alternatif (kanıt) |

> **Frontmatter status enum'u** (validator: `active/draft/proposed/approved/rejected/deprecated`) ile **satır durumu** (bu efsade) farklı katmanlardır; karıştırılmaz.

## 4. Domain Tabloları ve Yapı (Hibrit Multi-MD)

```text
.ai/architecture/
├── 00-master-index.md            ← bu dosya (giriş)
├── README.md                     ← yönelim
├── index.md                      ← eski [[architecture/index]] link uyumu (alias)
├── 10-domain-d01-*.md … 19-domain-d10-*.md   ← 10 tam katman tablosu (500 satır)
├── k0-isletim-sistemi/ … k20-bom/, firmware/  ← wiki-link landing + inventory/ evi (Q12/Q17; anlatı 500-katman katalogda)
├── 03-contracts/ · 07-security/ · l1-security/ · l2-routing/ · l3-presentation/ …
│                                  ← eski vault link hedefleri (stub-with-truth)
├── rules.md · context.md          ← Faz 0 iskeleti (validator bütçesi: context ≤660 satır)
└── 00-enterprise-index.md         ← derived: k0–k20 landing kataloğu (envanter evleri; SSOT değil)
```

**Ayrıntı dosyası yazma kuralı:** yalnız yüksek değerli (vault'ta link'lenen) hedefler için stub-with-truth üretilir; her katman için ayrı dosya YOK (hibrit: tablo esas, MD değişken).

## 5. Link-Status (Self-Check — `.ai/*.md` kök dosyalarındaki `architecture/…` hedefleri)

> Ölçüm: `.ai/*.md` içinden benzersiz `architecture/…` hedefi sayısı (son: `/*$::` temizliği), ardından `.ai/<hedef>` veya `.ai/<hedef>.md` varlık kontrolü.

| Metrik | Değer |
|--------|-------|
| Benzersiz hedef (ÖNCE = SONRA, hedef seti sabit) | **129** |
| Çözülen — unique (ÖNCE) | **17** |
| Çözülen — unique (SONRA) | **82** (%63,6) |
| Çözülen — referans ağırlıklı (ÖNCE → SONRA) | **35 → 167 / 219** (%76,3) |
| Katman sayımı (K000–K499) | **500** (10 tablo × 50 satır; `grep -cE '^\| K[0-9]{3} \|'` = 500, boşluksuz) |

**Kalan 47 çözülmeyen hedef (kasıtlı, §6):** `06-audio/*` 10 · `ai/*` 16 · tekil eski
dokümanlar 19 (`database/network/security-architecture`, `conditional-rendering-php-guide`,
`l3-presentation/responsive-*` ve `scale-router-*`, `k16-k20-electronics/*`,
`k6-k7-security/k06-auth-layer/auth-cross-domain`, `07-security/security/owasp-compliance` vb.) ·
`architecture/adr` 1 — **kasıtlı yasak** (rules.md R1: ADR'ler `.ai/.decisions/` altındadır).

Ölçüm komutu (tekrarlanabilir): `.ai/*.md` içindeki `architecture/…` hedefleri taranır,
her hedef için `.ai/<hedef>` veya `.ai/<hedef>.md` varlık kontrolü yapılır (2026-10-07).

## 6. Kirik Link Notu (Broken-Link Policy)

- Vault'taki ~129 benzersiz `architecture/…` hedefinin bir kısmı bu ağaçta **kasıtlı olarak üretilmedi** (ör. `ai/*` bilgi-tabanı eski ağacı, `06-audio/*` hizmet tasarım dosyaları): bunlar **doğru eşdeğer yere** işaretlenir veya `DEPRECATED` stub ile karşılanır.
- `node .ai/scripts/validate.mjs --check` → `architecture/` ilk segmenti bilinen-ölü-hedef (DEAD_DIRS) sayıldığı için çözülmeyenler **ihlal değil, uyarı** olarak raporlanır; bu bölümdeki sayılar asıl sağlık ölçütüdür.
- Yeni link eklerken hedefi bu dizinde yaratmak zorunludur (Guardrail #3 — Zero Hallucination).

## 7. İlgili Dosyalar

[[architecture/README]] · [[architecture/index]] · domain tabloları (§2) · kural SSOT'u [[architecture/rules]] · hot-memory [[architecture/context]] · anayasa [[../CLAUDE.md]] §5 / `.claude/CLAUDE.md` §5 · ADR defteri [[../.decisions/accepted/ADR-084-api-gateway-architecture]]
