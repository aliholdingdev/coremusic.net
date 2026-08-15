---
type: template
category: adr-index
title: "CoreMusic — ADR Template Index & Usage Guide"
version: 1.0.0
created: 2026-08-07
updated: 2026-08-07
authority: Vault Steward
governance: Red Team • Human Mode • Truth Mode
tags: [template, index, guide, adr]
---

# CoreMusic — ADR Template Index & Usage Guide

**Bu dosya, tüm ADR şablonlarının indeksini ve kullanım kılavuzunu içerir.**

---

## 📋 Şablon Listesi

| # | Şablon | Amaç | Kullanım Alanı |
|---|--------|------|---------------|
| 1 | `adr-template.md` | Genel ADR şablonu | Tüm kararlar için |
| 2 | `adr-frontend-template.md` | Frontend/CSS/SPA | CSS, JS, UI, responsive |
| 3 | `adr-database-template.md` | Database/SQL/BCNF | SQL, migration, query |
| 4 | `adr-security-template.md` | Security/OWASP | CSRF, CSP, auth, encryption |
| 5 | `adr-audio-template.md` | Audio/DSP/Hardware | DSP, buffer, ASIO, latency |

---

## 🚀 Hızlı Başlangıç

### 1. Şablon Seçimi

```
Yeni ADR oluştururken:

1. Konunuzu belirleyin
2. Aşağıdaki tablodan doğru şablonu seçin
3. Şablonu kopyalayın
4. Doldurun
5. Kaydedin
```

### 2. Konu → Şablon Eşleştirme

| Konu Anahtarı | Şablon | Örnek |
|---------------|--------|-------|
| `CSS, UI, responsive, BEM, ITCSS` | `adr-frontend-template.md` | Yeni tema ekleme |
| `SQL, BCNF, migration, query, schema` | `adr-database-template.md` | Yeni tablo ekleme |
| `CSRF, CSP, auth, OWASP, encryption` | `adr-security-template.md` | CSRF değişikliği |
| `DSP, buffer, ASIO, latency, hardware` | `adr-audio-template.md` | DSP algoritması |
| `API, routing, middleware, PHP` | `adr-template.md` | Yeni endpoint |
| `test, PHPUnit, coverage` | `adr-template.md` | Test stratejisi |
| `CI/CD, Docker, deploy` | `adr-template.md` | Deployment |
| `cache, Redis, APCu` | `adr-template.md` | Cache stratejisi |

### 3. Şablon Kullanımı

```bash
# 1. Şablonu kopyalayın
cp .ai/.templates/adr-frontend-template.md .ai/decisions/accepted/ADR-NNN-baslik.md

# 2. Dosyayı düzenleyin
# frontmatter: id, title, category, status, date, tags
# sections: Executive Summary, Context, Decision, Consequences, ...

# 3. Kaydedin
```

---

## 📖 Şablon Detayları

### 1. adr-template.md (Genel Şablon)

**Amaç:** Tüm ADR türleri için genel şablon

**Bölümler:**
- Executive Summary
- Status
- Context (Problem, İtici Güçler, Kısıtlamalar)
- Decision (Kural Matrisi, Kod Örnekleri)
- Architecture (Mimari diyagram)
- Alternatives Considered
- Consequences (Olumlu/Olumsuz)
- Testing Strategy
- Rollback Plan
- Related Decisions
- Glossary
- Edge Cases
- Warnings
- Limitations
- Dependencies
- Future Roadmap
- Related Documents
- Cross References
- Approval

**Kullanım:**
```bash
cp .ai/.templates/adr-template.md .ai/decisions/accepted/ADR-NNN-baslik.md
```

---

### 2. adr-frontend-template.md (Frontend Şablonu)

**Amaç:** CSS, JS, UI, responsive, ITCSS ile ilgili ADR'ler

**Ek Bölümler:**
- ITCSS Layer Mapping
- BEMIT Namespace
- CSS Custom Properties
- Design Tokens
- Responsive Breakpoints
- WCAG 2.2 AA Compliance
- Theme Engine Integration
- Performance Impact (CLS, LCP, INP)

**Özellikler:**
- ITCSS 9-layer mapping
- BEMIT namespace standardı
- CSS custom properties yapısı
- Theme engine entegrasyonu
- WCAG 2.2 AA uyumluluk
- Responsive breakpoint stratejisi

**Kullanım:**
```bash
cp .ai/.templates/adr-frontend-template.md .ai/decisions/accepted/ADR-NNN-tema-degisikligi.md
```

---

### 3. adr-database-template.md (Database Şablonu)

**Amaç:** SQL, BCNF, migration, query ile ilgili ADR'ler

**Ek Bölümler:**
- 18 BCNF Veritabanı Listesi
- BCNF Normalizasyon Adımları
- Tablo Tasarımı (SQL)
- Foreign Key İlişkileri
- Index Stratejisi
- Query Örnekleri (PDO)
- Migration Dosyası

**Özellikler:**
- ADR-002 uyumlu (PDO, no ORM)
- ADR-003 uyumlu (18 BCNF DB)
- ADR-040 uyumlu (BCNF canonical)
- SELECT * yasak reminder
- Prepared statement examples

**Kullanım:**
```bash
cp .ai/.templates/adr-database-template.md .ai/decisions/accepted/ADR-NNN-yeni-tablo.md
```

---

### 4. adr-security-template.md (Security Şablonu)

**Amaç:** OWASP, CSRF, CSP, auth, encryption ile ilgili ADR'ler

**Ek Bölümler:**
- OWASP Top 10 (2021) Uyumluluk
- Middleware Pipeline Diagram
- CSRF Token Management
- CSP Policy
- Rate Limiting
- Encryption (AES-256-GCM)
- Password Hashing (Argon2id)
- Session Management
- SSRF Protection

**Özellikler:**
- ADR-010 uyumlu (csrf_token)
- ADR-011 uyumlu (session)
- ADR-012 uyumlu (CSP nonce)
- ADR-013 uyumlu (rate limiting)
- ADR-022 uyumlu (DB security)
- ADR-043 uyumlu (auth consolidation)

**Kullanım:**
```bash
cp .ai/.templates/adr-security-template.md .ai/decisions/accepted/ADR-NNN-guvencikoruma.md
```

---

### 5. adr-audio-template.md (Audio Şablonu)

**Amaç:** DSP, buffer, hardware, latency ile ilgili ADR'ler

**Ek Bölümler:**
- Neva Engine Durumu
- DSP Algoritması (C++)
- Ring Buffer (Lock-free)
- ASIO Callback
- Multi-channel Mixer (8.1)
- Hardware Signal Flow
- Latency Targets
- Performance Metrics

**Özellikler:**
- ADR-017 uyumlu (DSP hardware)
- ADR-038 uyumlu (8.1 sound card)
- Zero-allocation reminder
- Lock-free reminder
- 32-bit float precision
- PCM5122 REDDEDİLDİ warning

**Kullanım:**
```bash
cp .ai/.templates/adr-audio-template.md .ai/decisions/accepted/ADR-NNN-dsp-algoritmasi.md
```

---

## 🔧 Customization

### Frontmatter Güncelleme

Her şablonda şu alanları güncelleyin:

```yaml
---
id: "NNN"                    # ADR numarası
title: "ADR-NNN: Başlık"    # Tam başlık
category: "frontend"         # Kategori
status: "draft"              # draft/active/frozen
date: "YYYY-MM-DD"           # Tarih
updated: "YYYY-MM-DD"        # Son güncelleme
authority: "UI Designer"     # Sorumlu ajan
tags: [frontend, css, ...]   # Etiketler
---
```

### Section Ekleme

Şablona yeni section eklemek için:

```markdown
## X. Yeni Section

### X.1 Alt Section

İçerik...

### X.2 Alt Section

İçerik...
```

### Cross-Reference Ekleme

İlgili ADR'leri eklemek için:

```yaml
references:
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
  - "[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]"
```

---

## 📏 Kalite Kontrol Listesi

Her ADR oluşturduktan sonra kontrol edin:

### Frontmatter
- [ ] `id` doğru mu?
- [ ] `title` format doğru mu? (`ADR-NNN: Başlık`)
- [ ] `category` doğru mu? (`frontend`, `database`, `security`, `audio`, `general`)
- [ ] `status` doğru mu? (`draft` olarak başla)
- [ ] `date` doğru mu? (`YYYY-MM-DD`)
- [ ] `tags` yeterli mi?

### İçerik
- [ ] Executive Summary yazıldı mı?
- [ ] Context bölümü dolu mu?
- [ ] Decision bölümü net mi?
- [ ] Alternatives consider edildi mi?
- [ ] Consequences yazıldı mı?
- [ ] Testing strategy var mı?
- [ ] Rollback plan var mı?

### Form
- [ ] Kod örnekleri doğru mu?
- [ ] SQL syntax doğru mu?
- [ ] C++ syntax doğru mu?
- [ ] PHP syntax doğru mu?

### Cross-Reference
- [ ] İlgili ADR'ler referans verildi mi?
- [ ] Vault dosyaları referans verildi mi?
- [ ] Cross-reference diyagramı doğru mu?

---

## 🎯 ADR Oluşturma Adımları

### Adım 1: Konu Belirleme

```
Sorular:
1. Ne değişiyor?
2. Neden değişiyor?
3. Hangi ADR'ler etkileniyor?
4. Risk seviyesi nedir?
```

### Adım 2: Şablon Seçimi

```
Konu Anahtarı → Şablon:
- CSS/UI → adr-frontend-template.md
- SQL/DB → adr-database-template.md
- Security → adr-security-template.md
- Audio/DSP → adr-audio-template.md
- Diğer → adr-template.md
```

### Adım 3: Şablonu Kopyalama

```bash
cp .ai/.templates/adr-[kategori]-template.md .ai/decisions/accepted/ADR-NNN-baslik.md
```

### Adım 4: Doldurma

```
1. Frontmatter'i güncelle
2. Executive Summary yaz
3. Context bölümünü doldur
4. Decision bölümünü yaz
5. Alternatives'ı değerlendir
6. Consequences'ı yaz
7. Testing strategy'yi planla
8. Rollback plan'ı hazırla
9. Cross-references'ı ekle
10. Approval'ı al
```

### Adım 5: Doğrulama

```
1. Kalite kontrol listesini kontrol et
2. Cross-reference'ları doğrula
3. Syntax kontrolü yap
4. Vault'a kaydet
5. log.md'ye ekle
```

---

## 🔗 İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index.md]] | Master katalog |
| [[keys.md]] | Navigasyon haritası |
| [[brain.md]] | Mimari kararlar |
| [[WORKFLOW.md]] | ADR yaşam döngüsü |
| [[decisions/index]] | ADR indeksi |

---

## 📝 Örnek ADR Oluşturma

### Senaryo: Yeni CSS Theme Ekleme

```bash
# 1. Şablon seçimi
# Konu: CSS, tema, renk → adr-frontend-template.md

# 2. Kopyalama
cp .ai/.templates/adr-frontend-template.md .ai/decisions/accepted/ADR-051-dark-theme.md

# 3. Düzenleme (örnek)
---
id: "051"
title: "ADR-051: Dark Theme Desteği"
category: "frontend"
status: "draft"
date: "2026-08-07"
tags: [frontend, theme, css, dark-mode]
---

## Executive Summary
CoreMusic platformuna dark theme desteği eklenecek.

## Context
Kullanıcılar dark mode istiyor. WCAG 2.2 AA uyumlu olmalı.

## Decision
CSS custom properties ile dark theme implemente edilecek.

## Consequences
+ Kullanıcı deneyimi artacak
- Ek CSS dosyası gerekli
```

---

*CoreMusic ADR Template Index v1.0.0 — 2026-08-07*
*Authority: Vault Steward*
*Governance: Red Team • Human Mode • Truth Mode*
