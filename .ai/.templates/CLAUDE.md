---
title: "CoreMusic — .ai/.templates Bağlam"
type: template-guide
category: template
folder: ".ai/.templates"
version: 2.2.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-06
---

# .ai/.templates — CLAUDE.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]] · [[./index.md]] · [[../.agents/AGENTS.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Guardrail #16'nın uygulama noktası; her kod/doküman üretimi öncesi buradan şablon alınır.

Bu dosya, `.ai/.templates/` klasörünün bağlam rehberidir: klasörün ne olduğu, kimin kullandığı ve şablon değişikliklerinin hangi protokolle kaydedildiği. Şablon listesinin/dizin ağacının otoritesi registry dosyasıdır ([[./index.md]]); bu dosya yalnızca klasör bağlamını ve değişiklik protokolünü taşır — kendini Single Source of Truth ilan etmez.

## 2. Kapsam

- **Kapsam:** `.ai/.templates/**` altındaki tüm şablon ve meta dosyalar (index.md, CLAUDE.md + 24 şablon = 26 md dosyası).
- **Kapsam dışı:** şablon listesi, dizin ağacı ve sayım metrikleri (→ registry [[./index.md]]); agent yetki/handover kuralları (→ [[../AGENTS.md]]); agent profilleri (→ [[../.agents/AGENTS.md]]).
- **Kullananlar:** 11 agent profilinin tamamı (Guardrail #16 — dosya üretiminde şablon zorunlu) + insan geliştiriciler + Vault Steward (değişiklik protokolü).

## 3. Mimari

### 3.1 Komşu İlişkiler

Parent [[../CLAUDE.md]] · Index [[./index.md]] · Tüketen tüm agent profilleri [[../.agents/AGENTS.md]]

### 3.2 Mevcut Durum (2026-09-23 disk sayımı — Faz 2)

| Durum | Değer |
|-------|-------|
| Kategori | 10 klasör (adr, agents, backend, documentation, frontend, hardware, infrastructure, other, query, testing) + kök (index.md, CLAUDE.md, session-log-template.md) — eski kayıt: "9 klasör" |
| Dosya sayısı | 26 md (24 şablon + 2 meta) — eski kayıt: 19 md (17 şablon + 2 meta) |
| Toplam satır | 12.549 (2026-09-23 Faz 2 üretim sonu ölçümü) — eski kayıt: 4.899 / 5.546 |
| Derinlik dağılımı | 500+ satır olan şablon: 23 · kısa şablon: 1 (`session-log-template.md`, 144 — 500+ kuralı kapsamı dışı) · meta: 2 (`index.md`, `CLAUDE.md` — bu yazımla satır sayıları değişir) |
| Dikkat notu | cpp şablonu `other/` altındadır (kökte ayrıca cpp dosyası YOKTUR); `session-log-template.md` köktedir, `session/` klasörü YOKTUR; `hardware/` altında tek dosya `hardware-template.md` (arduino/avr/pic üretilmedi — registry §2 "Planlanan") |
| Yeniden yazım | Faz 1 (2026-09-23): index.md, templates/CLAUDE.md, session-log-template.md, subdomains/CLAUDE.md · Faz 2 (2026-09-23): 16 şablon derin yeniden yazım + 7 yeni üretim (adr-frontend/database/security/audio/index, aspnet, c) → 26 dosya / 12.549 satır |

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

## 5. Workflow

ŞABLONU SEÇ → KOPYALA → `{{PLACEHOLDER}}` DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT

1. **ŞABLONU SEÇ:** Hedef dosya tipine göre [[./index.md]] §7.1 tablolarından şablon seç (diskte 📋 Planlanan olanlara geçici dosya yazılamaz — Faz 6: arduino/avr/pic).
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
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
