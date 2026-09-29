---
title: "CoreMusic — .ai/.templates Bağlam"
type: template-guide
category: template
folder: ".ai/.templates"
version: 2.4.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-29
date: 2026-09-06
---

# .ai/.templates — CLAUDE.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]] · [[./index.md]] · [[../.agents/AGENTS.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Guardrail #16'nın uygulama noktası; her kod/doküman üretimi öncesi buradan şablon alınır.

Bu dosya, `.ai/.templates/` klasörünün bağlam rehberidir: klasörün ne olduğu, kimin kullandığı ve şablon değişikliklerinin hangi protokolle kaydedildiği. Şablon listesinin/dizin ağacının otoritesi registry dosyasıdır ([[./index.md]]); bu dosya yalnızca klasör bağlamını ve değişiklik protokolünü taşır — kendini Single Source of Truth ilan etmez.

## 2. Kapsam

- **Kapsam:** `.ai/.templates/**` altındaki tüm şablon ve meta dosyalar (index.md, CLAUDE.md + 39 şablon = 41 md dosyası).
- **Kapsam dışı:** şablon listesi, dizin ağacı ve sayım metrikleri (→ registry [[./index.md]]); agent yetki/handover kuralları (→ [[../AGENTS.md]]); agent profilleri (→ [[../.agents/AGENTS.md]]).
- **Kullananlar:** 11 agent profilinin tamamı (Guardrail #16 — dosya üretiminde şablon zorunlu) + insan geliştiriciler + Vault Steward (değişiklik protokolü).

## 3. Mimari

### 3.1 Komşu İlişkiler

Parent [[../CLAUDE.md]] · Index [[./index.md]] · Tüketen tüm agent profilleri [[../.agents/AGENTS.md]]

### 3.2 Mevcut Durum (2026-09-29 disk sayımı — Faz 6 kapanışı)

| Durum | Değer |
|-------|-------|
| Kategori | 12 klasör (adr, agents, backend, documentation, frontend, hardware, infrastructure, other, personas, query, testing, ui-design) + kök (index.md, CLAUDE.md, session-log-template.md, coremusic-vault-template.md) — eski kayıt: "10 klasör" |
| Dosya sayısı | 41 md (39 şablon + 2 meta) — eski kayıt: 38 md (2026-09-28) · 28 md (2026-09-24) · 28 md (2026-09-23) |
| Toplam satır | 20.088 (2026-09-29, 41 md) — eski kayıt: 17.984 (2026-09-28, 38 md) · 14.695 (2026-09-24, 28 md) · 12.549 (2026-09-23, 26 md) |
| Derinlik dağılımı | 500+ satır olan şablon: 33 (2026-09-29); kısa şablon: 5 (`session-log-template.md` 143 + 4 adet 100-250 aralığı istisna); meta: 2 (`index.md`, `CLAUDE.md` — bu yazımla satır sayıları değişir) |
| Dikkat notu | cpp şablonu `other/` altındadır; `session-log-template.md` köktedir, `session/` klasörü YOKTUR; `hardware/` altında **4 dosya** (`hardware-template`, `arduino`, `avr`, `pic` — son 3'ü 2026-09-29'da üretildi, Faz 6 kapandı) |
| Yeniden yazım | Faz 1 (2026-09-23): index.md, templates/CLAUDE.md, session-log-template.md, subdomains/CLAUDE.md · Faz 2 (2026-09-23): 16 şablon derin yeniden yazım + 7 yeni üretim → 26 dosya / 12.549 satır · 2026-09-24: +2 (`claude-md`, `docs-md`) → 28 / 14.695 · 2026-09-29: +3 (`hardware/{arduino,avr,pic}`) → **41 dosya / 20.088 satır** |

## 4. Kurallar

1. **Guardrail #16 — şablon zorunlu:** Her kod/doküman üretimi öncesi buradan şablon alınır; şablonsuz dosya oluşturulamaz (AI + insan aynı kurala tabidir).
2. **Değişiklik Protokolü:** Şablon değişimi → index + kök referans tabloları + log.
3. **Registry otoritesi:** Sayım/dizin/sayımların tek kaynağı [[./index.md]]; bu dosya şablon listesi iddiası taşımaz, SSOT self-claim yapılmaz.
4. **Frontmatter standardı:** Şablon dosyalarında 7 zorunlu alan (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`); `authority` = `Template (Guardrail #16) — Registry: .ai/.templates/index.md`.
5. **Belirsizlik:** Doğrulanamayan disk/sayı iddiası `⚠️ VERIFICATION REQUIRED` etiketiyle işaretlenir.

### 4.1 Değişiklik Kayıtları (§4.2 Değişiklik Protokolü uygulaması)

| Tarih | Faz | Değişiklik |
|-------|-----|------------|
| 2026-09-23 | vault-rewrite Faz 1 | 4 dosya: `subdomains/CLAUDE.md` (v2.0.0 ELI10), `templates/CLAUDE.md` (v2.1.0), `templates/index.md` (v4.0.0), `session-log-template.md` (v2.0.0) |
| 2026-09-23 | vault-rewrite Faz 2 | **16 şablon derin yeniden yazım + 7 yeni şablon üretildi** (`adr-frontend`, `adr-database`, `adr-security`, `adr-audio`, `adr-index`, `aspnet`, `c`) + registry birleştirme (`index.md` v4.1.0, `templates/CLAUDE.md` v2.2.0, `log.md` append) → **26 dosya / 12.549 satır, 500+ derinlik 23/23 doğrulandı** |
| 2026-09-24 | şablon üretimi | **2 yeni şablon üretildi** (`documentation/claude-md-template.md` 552 satır, `documentation/docs-md-template.md` 558 satır) — her ikisinde de zorunlu "Şablon Önce" (Template-First) bloğu + registry senkronu (`index.md` v4.2.0, `templates/CLAUDE.md` v2.3.0, `log.md` append) → **28 dosya / 14.695 satır, 500+ derinlik 25/25** |
| 2026-09-29 | Faz 6 kapanışı | **3 yeni şablon üretildi** (`hardware/arduino-template.md` 766, `hardware/avr-template.md` 650, `hardware/pic-template.md` 687) — eski vault `.ai/.templates/` içeriği güncel 7 alanlı FM + §1-§7 iskeletine dönüştürüldü, sürüm/standart iddiaları web ile doğrulandı (avr-gcc 15/16, avrdude 8.2, XC8 4.00, MPLAB X 6.35, EN IEC 63000, MISRA C:2025); registry senkronu (`index.md` v4.7.0, `templates/CLAUDE.md` v2.4.0, `log.md` append) → **41 dosya / 20.088 satır, Faz 6 defteri kapandı** |

## 5. Workflow

ŞABLONU SEÇ → KOPYALA → `{{PLACEHOLDER}}` DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT

1. **ŞABLONU SEÇ:** Hedef dosya tipine göre [[./index.md]] §7.1 tablolarından şablon seç (tüm planlanan şablonlar üretildi — Faz 6 kapandı 2026-09-29; hardware için artık 4 seçenek var: `hardware-template`, `arduino`, `avr`, `pic`).
2. **KOPYALA:** Şablon dosyası olduğu gibi hedef yola kopyalanır.
3. **`{{PLACEHOLDER}}` DOLDUR:** Tüm `{{...}}` alanları gerçek değerlerle doldurulur (tarih/placeholder bırakılmaz).
4. **GUARDRAIL #16 DOĞRULA:** §6 kontrol listesi çalıştırılır.
5. **COMMIT:** Şablon dosyasının kendisi değiştiyse → Değişiklik Protokolü (§4.2): index + kök referans tabloları + log.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] sayı/dizin iddiaları registry ([[./index.md]]) ile senkron

**REFACTOR REPORT:** FILE: CLAUDE.md · PURPOSE: .ai/.templates klasör bağlam + değişiklik protokolü rehberi · VALIDATION: 7 alan + §1-§7 + bilgi korunumu + Faz 2 üretim kaydı (§4.1) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[../CLAUDE.md]] — Parent (AI anayasası)
- [[./index]] — Template Registry (şablon listesi otoritesi)
- [[../AGENTS.md]] — Agent Registry (Guardrail #16 routing)
- [[../.agents/AGENTS.md]] — Agent profilleri (şablon tüketicileri)
- [[../WORKFLOW.md]] — Süreç ve fazlar
- [[.templates/index]] — bu klasörün registry'si
- [[../AGENTS.md]] — koordinasyon protokolü

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
