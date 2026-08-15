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
    - ".ai/ADR/"
    - "Existing architecture"
    - "Existing implementation"

update_policy:
  preserve_history: true
  never_overwrite_decisions: true

changelog:
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

- İlgili ADR'leri oku (ADR-001'den ADR-044'e kadar)
- Frozen ADR'leri kontrol et (001-037 = de)
- Alternatifleri değerlendir
- Teknik kanıt topla

### 3. ADR Taslağı Oluştur (1 saat)
- ADR formatına uy (aşağıdaki şablona bak)
- `.ai/decisions/accepted/adr-NNN-konu.md` olarak kaydet
- Frontmatter zorunlu: type, id, title, status, date, deciders, tags

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

## ADR Numarandırma
- Frozen: ADR-001 → ADR-037 (değiştirilemez)
- Active: ADR-038+ (yeni kararlar)
- Numara asla yeniden kullanılmaz

## Kalite Kontrol Checklist
- [ ] Frontmatter eksiksiz mi?
- [ ] Bağlam bölümü yeterli mi?
- [ ] Gerekçe açık mı?
- [ ] Alternatifler değerlendirildi mi?
- [ ] Frozen ADR ile çelişki var mı?
- [ ] Cross-reference'lar doğru mu?
- [ ] Hallüsinasyon kontrolü yapıldı mı?

## Yasaklar
- Frozen ADR'yi değiştirme (001-037)
- Doğrulanamayan bilgi ekleme
- Teknik kanıt olmadan karar verme

## Related Files
- `.ai/decisions/accepted/` — ADR dosyaları
- `.ai/decisions/index.md` — ADR indeksi
- `.ai/brain.md` — Mimari kararlar
- `.ai/WORKFLOW.md` — ADR yaşam döngüsü

## Activation
- "ADR", "karar", "mimari karar", "decision"
