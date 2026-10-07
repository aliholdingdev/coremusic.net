---
title: "CoreMusic — Prompt Maker Template Directory"
type: template
category: prompt-maker
version: 1.1.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-10-07
---

# CoreMusic — Prompt Maker Template Directory (v1.1.0)

## Overview

**Enterprise AI Agentic Prompt Maker** — kullanıcının ham isteğini **derin, detaylı ve
doğrudan kullanılabilir** AI promptlarına dönüştüren şablon sistemi.

> **⚠️ ZORUNLU ÇIKTI KURALI (v1.1.0 — §0):**
> Bu prompt maker ile üretilen HER final prompt **EN AZ 500 SATIR** olmalıdır.
> Kısa / öz / "kısa tut" prompt üretimi **YASAKTIR**. Uzunluk **derinlikle** sağlanır
> (gerekçe + örnek + kenar durum), dolgu/tekrarla DEĞİL. Teslim öncesi **PASS 3 satır
> sayımı** zorunludur; < 500 ise teslim geçersizdir.

## Directory Structure

```
prompt-maker/
├── template.md                 — Ana şablon (1546 satır) — §0 min-500 sözleşmesi dahil
├── README.md                   — Bu doküman (v1.1.0)
├── examples/                   — ≥500 satırlık üretilmiş prompt örnekleri
│   ├── coremusic-backend-api.md      (565 satır — Format 2 TASK)
│   ├── coremusic-frontend-vanilla.md (505 satır — Format 2 TASK, Vanilla JS)
│   └── coremusic-security-audit.md   (≥500 satır — Format 4 SECURITY AUDIT)
└── formats/
    └── format-templates.md     (557 satır — 8 format iskeleti + min satır bütçeleri)
```

## Dosya Rolleri

| Dosya | Ne yapar | Kim kullanır |
|-------|----------|--------------|
| `template.md` | 21 bölüm + **§0 ÇIKTI UZUNLUK SÖZLEŞMESİ** (min 500 satır, 24 bölüm bütçesi, 3-PASS süreci) | Prompt maker her üretimde |
| `formats/format-templates.md` | 8 format (SYSTEM, TASK, CODE REVIEW, SECURITY AUDIT, RE, REFACTOR, ARCHITECTURE, MIGRATION) + Format Seçim Matrisi + format başına min satır bütçesi | Görev tipine göre format seçerken |
| `examples/*` | Gerçek üretim çıktıları (≥500 satır) — NE NASIL ÜRETİLİR gösterimi | Referans / kalibrasyon |
| `README.md` | Dizin rehberi + entegrasyon | İlk okuma |

## §0 ÇIKTI UZUNLUK SÖZLEŞMESİ (Özet)

1. **Min 500 satır** — her final prompt, hangi formatta olursa olsun.
2. **3-PASS zorunlu:** PASS 1 taslak → PASS 2 derinleştirme → PASS 3 sayım.
3. **24 bölüm min bütçesi** (tablo: `template.md` §0) — toplam ~534+ satır.
4. **Derinlik formülü:** her bölüm/ madde = GEREKÇE + ÖRNEK + KENAR DURUM.
5. **Yasaklar:** tek cümlelik bölüm · açıklamasız madde listesi · dolgu ile şişirme ·
   kapsamı bahane edip bölüme ATMA (ilgisiz bölüm "uygulanabilir değil + gerekçe" ile korunur).
6. **Kırmızı bayrak:** "kısa tut/özetle" talebi 500 satırı DEĞİŞTİRMEZ (yalnız kullanıcı
   YAZILI olarak kaldırabilir).
7. **Doğrulama:** `< 500` ise teslim YASAK; sayım log'u rapora eklenir.

## Kullanım Akışı

```text
1. GİRDİ      → kullanıcının ham isteği
2. FORMAT SEÇ → format-templates.md "Format Seçim Matrisi" (görev tipi)
3. TEMPLATE   → template.md §1-§21 üzerinde çalış; §0 bütçelerini uygula
4. PASS 1     → taslak (bölüm başlıkları + ana maddeler)
5. PASS 2     → her bölümü bütçesine kadar derinleştir (gerekçe+örnek+edge)
6. PASS 3     → satır sayımı (≥500?) → eksikse PASS 2'ye dön
7. VALIDATION → §20 checklist (intent, hallucination, kabul kriterleri)
8. TESLİM     → # GENERATED PROMPT (≥500 satır) + (varsa) DISCOVERY SUMMARY
```

## CoreMusic Bağlamı (prompt bağlanırken)

Prompt çekirdeğine **gerçekten ilgiliyse** eklenir (ilgisiz section ATILMAZ, §0):

- **Stack:** PHP 8.4 (strict_types, Raw PDO — ORM YASAK), Vanilla JS ES2022 (framework YASAK),
  MySQL 9 (18 BCNF), C++20 Neva Engine.
- **Guardrails:** 16 Hard Guardrail; `csrf_token` (NOT `_csrf_token`); middleware sırası
  immutable (§6); mockup-before-frontend (§7.1); In-Place (§7 #4); Vault First (§7 #2).
- **Yasaklı kalıplar:** `SELECT *` · `innerHTML` (veri) · `eval` · `var` · localStorage auth
  (§21).
- **Doğrulama:** web/vault doğrulanmamış iddia → `[VERIFY REQUIRED]` / `UNKNOWN`
  (Zero-Hallucination §18).

## Guardrail Uyumu (Guardrail #16)

- Frontmatter 7 alan zorunlu (title, type, category, version, status, authority, updated).
- Registry: `.ai/.templates/index.md` — bu dizin oraya kayıtlıdır.
- Yeni dosya bu şablon sisteminden üretilir; şablonsuz üretim yasak.

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.1.0 | 2026-10-07 | **§0 ÇIKTI UZUNLUK SÖZLEŞMESİ** eklendi (min 500 satır, 24 bölüm bütçesi, 3-PASS); examples ≥500 satıra yeniden yazıldı; `formats/format-templates.md` (557 satır) eklendi; React örneği silindi (Forbidden Pattern) → Vanilla JS örneği |
| 1.0.0 | 2026-10-07 | İlk entegrasyon (template + README + 3 örnek) |

---

**Authority**: Bayram Ali / Vault Steward
**Last Updated**: 2026-10-07
**Mode**: Red Team · Human Mode · Truth Mode
