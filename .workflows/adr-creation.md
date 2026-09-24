---
title: "CoreMusic — ADR Creation Workflow"
type: workflow-instruction
category: architecture-decisions
authority: SSOT
status: active

mode:
  - Red Team
  - Truth Mode
  - Human Mode

purpose:
  - Architecture Decision Management
  - Technical Decision Governance
  - System Evolution Control
  - Architecture Consistency Protection

reference:
  authority: ".ai/WORKFLOW.md"

  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/MEMORY.md"
    - ".ai/log.md"

  architecture:
    - ".ai/.decisions/accepted/"
    - ".ai/architecture/adr/"
    - "Existing architecture"
    - "Existing implementation"

update_policy:
  preserve_history: true
  never_overwrite_decisions: true

changelog:
  - version: 3.1.0
    date: 2026-09-24
    changes:
      - İki ADR serisi hizası: .ai/.decisions/ (karar) + .ai/architecture/adr/ (mimari ADR-023…026)
      - Kayıt yolları düzeltildi (.ai/decisions → .ai/.decisions/accepted; .ai/ADR → iki seri)
      - Numaralandırma bölümü: frozen 001-037 immutable, active 038-088, draft 089, yeni ≥088; birleştirme REDDEDİLDİ (ADR-026 §3.4)
  - version: 3.0.0
    date: 2026-08-15
    changes:
      - Added ADR lifecycle governance
      - Added approval matrix
      - Added impact analysis rules
      - Added migration and rollback requirements
---

# ADR Creation Workflow

## Purpose
Mimari Karar Kaydı (ADR) oluşturma, inceleme ve onaylama süreci.

## Workflow Steps

### 1. Kararı Belirle (15 dk)
- Kontrol et:

- Karar tipi nedir?
- Mevcut ADR var mı?
- Mevcut mimari etkileniyor mu?
- Çakışan karar bulunuyor mu?

- Karar türleri:

- Architecture
- Technology
- Security
- Database
- Infrastructure
- Policy

- Karar türünü tanımla: Teknoloji / Mimari / Güvenlik / Politika
- Mevcut ADR'leri kontrol et (çakışma var mı?)
- ADR numarasını belirle: `ADR-NNN` (NNN = bir sonraki sıra numarası)

### 2. Araştırma Yap (30 dk)
- İlgili ADR kayıtları
- Architecture dokümantasyonu
- Mevcut kod yapısı
- Teknik standartlar

- Kontrol:

- Alternatif çözümler
- Teknik riskler
- Uzun vadeli etkiler
- Kanıt olmadan teknik karar alınmaz.

- İlgili ADR'leri oku (karar serisi: ADR-001 → ADR-088 + draft 089, `.ai/.decisions/index.md`; mimari seri: `.ai/architecture/adr/` ADR-023…026)
- Frozen ADR'leri kontrol et (001-037 = immutable)
- Alternatifleri değerlendir
- Teknik kanıt topla

### 3. ADR Taslağı Oluştur (1 saat)
- ADR formatına uy (aşağıdaki şablona bak; mimari kararlarda `[[../.ai/.templates/adr/adr-nygard-template]]` tercih edilir — Guardrail #16)
- **Seriye göre kaydet:**
  - Karar serisi → `.ai/.decisions/accepted/adr-NNN-slug.md`
  - Mimari seri → `.ai/architecture/adr/ADR-NNN-slug.md` (ADR-023…026 dizini)
- Frontmatter zorunlu: type, id, title, status, date, deciders, tags (+ 7 alanlı vault FM: title, type, category, version, status, authority, updated)

### 4. İnceleme (30 dk)
- Kalite kontrol listesini uygula (aşağıdaki checklist)
- Cross-reference kontrolü yap
- Hallüsinasyon kontrolü uygula

### 5. Onay Al
- Tech Lead onayı
- Security Engineer onayı (güvenlik ile ilgiliyse)
- Kullanıcı onayı (kritik kararlar için)

### 6. Uygula ve Dokümante Et
- ADR'yi vault'a ekle
- `index.md`, `keys.md`, `brain.md` güncelle
- `log.md`'ye timestamp ile yaz


## ADR Yaşam Döngüsü Kuralları

önerildi (proposed)
   |
   v
inceleme (review)
   |
   v
kabul edildi (accepted)
   |
   v
donduruldu (frozen)


`geçersiz (deprecated)` durumu aşağıdakileri gerektirir:

- Yerine Geçecek ADR (Replacement ADR)
- Migration planı
- Onay

## Onay Matrisi

| Karar | Onay |
|---|---|
| Mimari | Principal Architect |
| Güvenlik | Security Engineer |
| Veritabanı | Data Engineer |
| API | Backend Architect |
| Dağıtım | DevOps |
| Donanım | Embedded Engineer |

## Etki Analizi Kuralları

## Etki Analizi

Etkilenen sistemler:

- Backend
- Frontend
- Veritabanı
- Altyapı
- Güvenlik

Risk seviyesi:

- Düşük
- Orta
- Yüksek

## ADR Formatı

```markdown
---
type: decision
id: "NNN"
title: "Karar Başlığı"
status: "proposed|accepted|frozen|deprecated"
date: "YYYY-MM-DD"
deciders: "Karar verenler"
tags: ["tag1", "tag2"]
---

# Karar: [Başlık]

## Özet
Kısa karar özeti (1-2 cümle)

## Bağlam
Bu karara neden ihtiyaç duyuldu? Mevcut durum nedir?

## Karar
Ne karar verildi?

## Gerekçe
Neden böyle karar verildi?

## Alternatifler
Hangi alternatifler değerlendirildi ve neden reddedildi?

## Sonuçlar
Bu kararın sonuçları neler?

## İlgili Kararlar
Bu kararla ilişkili diğer ADR'ler
```

> **Seri notu (2026-09-24):** İki ADR serisi **kasıtlı ayrıdır** — `.ai/.decisions/` (karar günlüğü) ile `.ai/architecture/adr/` (mimari seri). Aynı numara iki seride farklı slug ile bulunabilir (ör. ADR-024-ecosystem-modular-docs ≠ ADR-024-surucu-firmware-birlesme); her atıfta **slug + konum** birlikte yazılır. Tek seriye birleştirme **REDDEDİLDİ** (ADR-026 §3.4); istek üst karar ister (superstack/product-owner).

## ADR Numarandırma

### Karar Serisi (`.ai/.decisions/`)

- Frozen: ADR-001 → ADR-037 (değiştirilemez, immutable)
- Active: ADR-038 → ADR-088 · Draft: ADR-089 (kaynak: `.ai/.decisions/index.md`)
- **Yeni karar ADR'leri: ADR-088+** uzayında, index'ten boş numara seçilerek açılır
- Numara asla yeniden kullanılmaz

### Mimari Seri (`.ai/architecture/adr/`)

- Kendi numara uzayında ardışık ilerler: mevcut **ADR-023 → ADR-026** (2026-09-24)
- Dosya adı büyük harf: `ADR-NNN-slug.md`
- Kapsam: katman sınırı, adlandırma, sayım, klasör birleşimi gibi mimari kararlar
- Birleştirme (karar serisiyle) REDDEDİLDİ — ADR-026 §3.4

## Kalite Kontrol Checklist
- [ ] Frontmatter eksiksiz mi? (7 alan + ADR alanları)
- [ ] Doğru seri ve boş numara seçildi mi? (karar → .decisions index; mimari → architecture/adr)
- [ ] Bağlam bölümü yeterli mi?
- [ ] Gerekçe açık mı?
- [ ] Alternatifler değerlendirildi mi? (her red: REDDEDİLDİ — gerekçe)
- [ ] Frozen ADR ile çelişki var mı? (001-037 immutable)
- [ ] Cross-reference'lar doğru mu? (slug + konum)
- [ ] Hallüsinasyon kontrolü yapıldı mı?

## Yasaklar
- Frozen ADR'yi değiştirme (001-037)
- İki ADR serisini birleştirme (ADR-026 §3.4 — REDDEDİLDİ)
- Doğrulanamayan bilgi ekleme
- Teknik kanıt olmadan karar verme

## Related Files
- `.ai/.decisions/accepted/` — karar serisi ADR dosyaları
- `.ai/.decisions/index.md` — karar serisi indeksi (001-089 aralığı)
- `.ai/architecture/adr/` — mimari ADR serisi (ADR-023…026)
- `.ai/brain.md` — Mimari kararlar
- `.ai/WORKFLOW.md` — ADR yaşam döngüsü
- `.ai/.templates/adr/adr-nygard-template.md` — Nygard ADR şablonu (Guardrail #16)
- `.ai/.templates/adr/adr-template.md` — Vault klasik ADR şablonu

## Activation
- "ADR", "karar", "mimari karar", "decision"
