---
title: "CoreMusic — .ai/.templates Bağlam"
type: template-guide
category: template
folder: ".ai/.templates"
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-06
---

# .ai/.templates — CLAUDE.md

**Zorunlu Bağlantılar / See also:** [[../CLAUDE.md]] · [[./index.md]] · [[../.agents/AGENTS.md]] · [[../AGENTS.md]]

## 1. Amaç

Guardrail #16'nın uygulama noktası; her kod/doküman üretimi öncesi buradan şablon alınır.

Bu dosya, `.ai/.templates/` klasörünün bağlam rehberidir: klasörün ne olduğu, kimin kullandığı ve şablon değişikliklerinin hangi protokolle kaydedildiği. Şablon listesinin/dizin ağacının otoritesi registry dosyasıdır ([[./index.md]]); bu dosya yalnızca klasör bağlamını ve değişiklik protokolünü taşır — kendini Single Source of Truth ilan etmez.

## 2. Kapsam

- **Kapsam:** `.ai/.templates/**` altındaki tüm şablon ve meta dosyalar (index.md, CLAUDE.md + 17 şablon = 19 md dosyası).
- **Kapsam dışı:** şablon listesi, dizin ağacı ve sayım metrikleri (→ registry [[./index.md]]); agent yetki/handover kuralları (→ [[../AGENTS.md]]); agent profilleri (→ [[../.agents/AGENTS.md]]).
- **Kullananlar:** 11 agent profilinin tamamı (Guardrail #16 — dosya üretiminde şablon zorunlu) + insan geliştiriciler + Vault Steward (değişiklik protokolü).

## 3. Mimari

### 3.1 Komşu İlişkiler

Parent [[../CLAUDE.md]] · Index [[./index.md]] · Tüketen tüm agent profilleri [[../.agents/AGENTS.md]]

### 3.2 Mevcut Durum (2026-09-23 disk sayımı)

| Durum | Değer |
|-------|-------|
| Kategori | 10 klasör (adr, agents, backend, documentation, frontend, hardware, infrastructure, other, query, testing) + kök (index.md, CLAUDE.md, session-log-template.md) — eski kayıt: "9 klasör" |
| Dosya sayısı | 19 md (17 şablon + 2 meta) — eski kayıt: "~26 (kök 3 dahil; kökte cpp + session-log dikkat: cpp aslında 'other' kategorisinde de var)" |
| Toplam satır | 4.899 — eski kayıt yok (yeni eklendi) |
| Dikkat notu | cpp şablonu `other/` altındadır (kökte ayrıca cpp dosyası YOKTUR); `session-log-template.md` köktedir, `session/` klasörü YOKTUR |
| Yeniden yazım | 8 dosya Vault Refactor Engine v2.0.0 iskeletiyle yeniden yazıldı (2026-09-23) |

## 4. Kurallar

1. **Guardrail #16 — şablon zorunlu:** Her kod/doküman üretimi öncesi buradan şablon alınır; şablonsuz dosya oluşturulamaz (AI + insan aynı kurala tabidir).
2. **Değişiklik Protokolü:** Şablon değişimi → index + kök referans tabloları + log.
3. **Registry otoritesi:** Sayım/dizin/sayımların tek kaynağı [[./index.md]]; bu dosya şablon listesi iddiası taşımaz, SSOT self-claim yapılmaz.
4. **Frontmatter standardı:** Şablon dosyalarında 7 zorunlu alan (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`); `authority` = `Template (Guardrail #16) — Registry: .ai/.templates/index.md`.
5. **Belirsizlik:** Doğrulanamayan disk/sayı iddiası `⚠️ VERIFICATION REQUIRED` etiketiyle işaretlenir.

## 5. Workflow

ŞABLONU SEÇ → KOPYALA → `{{PLACEHOLDER}}` DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT

1. **ŞABLONU SEÇ:** Hedef dosya tipine göre [[./index.md]] §7.1 tablolarından şablon seç (diskte 📋 Planlanan olanlara geçici dosya yazılamaz — Faz 6).
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

**REFACTOR REPORT:** FILE: CLAUDE.md · PURPOSE: .ai/.templates klasör bağlam + değişiklik protokolü rehberi · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

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
