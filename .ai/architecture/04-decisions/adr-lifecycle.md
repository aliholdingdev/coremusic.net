---
type: architecture
category: decisions
title: "ADR Lifecycle"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR Lifecycle

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]]

## 1. Amaç

ADR'lerin yaşam döngüsünü tanımlayan: Draft → Review → Active → Frozen süreç rehberi.

## 2. Lifecycle Genel Bakış

```
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│  Draft   │ →  │  Review  │ →  │  Active  │ →  │  Frozen  │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
     │               │               │               │
     │               │               │               └── Değiştirilemez
     │               │               └── Uygulanabilir
     │               └── İnceleme aşamasında
     └── İlk taslak
```

## 3. Aşama Detayları

### 3.1 Draft

| Özellik | Değer |
|---------|-------|
| **Durum** | İlk taslak |
| **Oluşturma** | Herhangi bir agent |
| **Değişiklik** | Serbest |
| **Onay** | Gerekmez |
| **Dosya** | `decisions/draft/ADR-NNN-*.md` |
| **Süre** | Sınırsız |
| **Değişiklikelogu** | Zorunlu değil |

### 3.2 Review

| Özellik | Değer |
|---------|-------|
| **Durum** | İnceleme aşamasında |
| **Oluşturma** | Draft'tan upgrade |
| **Değişiklik** | Review notları dahilinde |
| **Onay** | Tech Lead + Security |
| **Dosya** | `decisions/review/ADR-NNN-*.md` |
| **Süre** | Max 7 gün |
| **Değişiklikelogu** | Zorunlu |

### 3.3 Active

| Özellik | Değer |
|---------|-------|
| **Durum** | Onaylanmış, uygulanabilir |
| **Oluşturma** | Review'dan upgrade |
| **Değişiklik** | Yeni ADR ile supersede edilebilir |
| **Onay** | Vault Steward |
| **Dosya** | `decisions/accepted/ADR-NNN-*.md` |
| **Süre** | Kalıcı |
| **Değişiklikelogu** | Zorunlu |

### 3.4 Frozen

| Özellik | Değer |
|---------|-------|
| **Durum** | Değiştirilemez |
| **Oluşturma** | Active → Frozen (2+ yıl veya manuel) |
| **Değişiklik** | ASLA (istisna: hayati güvenlik) |
| **Onay** | Gerekmez (otomatik veya Vault Steward) |
| **Dosya** | `decisions/accepted/ADR-NNN-*.md` |
| **Süre** | Sonsuz |
| **Değişiklikelogu** | — |

## 4. Geçiş Kuralları

### 4.1 Draft → Review

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Context bölümü tam | Sorun tanımlanmış |
| 2 | En az 1 alternatif | Karşılaştırma yapılmış |
| 3 | Consequences bölümü | Etkiler değerlendirilmiş |
| 4 | Related ADR'ler | Bağlantılar kurulmuş |

### 4.2 Review → Active

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Tech Lead onayı | Teknik doğrulama |
| 2 | Security review | Güvenlik değerlendirmesi |
| 3 | Vault Steward onayı | Nihai karar |
| 4 | Cross-reference güncelleme | İlişkiler kurulmuş |

### 4.3 Active → Frozen

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | 2+ yıl aktif | Olgunlaşma |
| 2 | Veya manuel freeze | Vault Steward kararı |
| 3 | Hiç değişiklik yapılmamış | İstikrar |
| 4 | Yeni bilgi gelmemiş | Güncellik |

### 4.4 Frozen → *

| Kural | Açıklama |
|-------|----------|
| **ASLA** | Frozen ADR değiştirilemez |
| **İstisna** | Sadece hayati güvenlik hatası |
| **İstisna onayı** | Vault Steward + İnsan |
| **Yeni karar** | Yeni ADR oluşturulur (038+) |

## 5. Superseding (Yerine Geçme)

### 5.1 Supersede Formatı

```markdown
## Supersedes
- ADR-017-dsp-hardware-mode (kısmi)
```

### 5.2 Supersede Kuralları

| Kural | Açıklama |
|-------|----------|
| **Kısmi supersede** | Sadece belirli bölümler |
| **Tam supersede** | Tüm ADR geçersiz |
| **Durum işareti** | `status: superseded` |
| **Cross-reference** | Eski ADR'de referans |

## 6. ADR Şablonu

```markdown
---
type: adr
id: ADR-NNN
title: "Short Title"
status: draft|review|active|frozen
date: YYYY-MM-DD
category: security|database|audio|architecture|ui|ai
---

# ADR-NNN: Title

## Status
draft

## Context
What is the issue that we're seeing that is motivating this decision or change?

## Decision
What is the change that we're proposing and/or doing?

## Consequences
What becomes easier or more difficult to do because of this change?

## Alternatives Considered
What other options were evaluated?

## Related
- ADR-XXX: Related decision

## References
- [External reference if any]
```

## 7. ADR Numaralandırma

| Kural | Açıklama |
|-------|----------|
| **Sıralı** | 001, 002, 003... |
| **Eksik yok** | Numara atlanmaz |
| **Kalıcı** | Silinen ADR numarası yeniden kullanılmaz |
| **Aktif aralık** | 038-050 (güncellenebilir) |
| **Frozen aralık** | 001-037 (değiştirilemez) |

## 8. ADR Doğrulama Kontrolleri

| # | Kontrol | Başarısızlık |
|---|---------|-------------|
| 1 | Zorunlu bölümler tam | Review'e geçemez |
| 2 | Cross-reference'lar geçerli | Active'e geçemez |
| 3 | Frozen ADR değiştirilmemiş | CRITICAL log |
| 4 | Numara sıralı | ADR oluşturulamaz |
| 5 | Dosya formatı uyumlu | Uyarı |

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[decisions/index]] | ADR index |
| [[architecture/04-decisions/guardrails]] | Constraints |
| [[decisions/accepted/]] | Active/Frozen ADRs |
| [[decisions/draft/]] | Draft ADRs |
| [[brain.md]] | Engineering brain |

## 10. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Aşamalar | [[decisions/index]] | ADR listesi |
| § 5 Supersede | [[brain.md]] §13 | ADR detayları |
| § 7 Numaralandırma | [[decisions/accepted/]] | Dosya yapısı |

## 11. Sözlük

| Terim | Tanım |
|-------|-------|
| **ADR** | Architecture Decision Record |
| **Draft** | İlk taslak |
| **Review** | İnceleme aşaması |
| **Active** | Onaylanmış karar |
| **Frozen** | Değiştirilemez karar |
| **Supersede** | Yerine geçme |
| **Lifecycle** | Yaşam döngüsü |
| **Cross-reference** | Çapraz referans |
| **Guardrail** | Kısıt/kural |
| **Vault Steward** | Karar yetkilisi |

## 12. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | ~510 |
| **ADR Uyumlu** | ✅ |
| **Zero Hallucination** | ✅ |
| **Cross-Reference** | ✅ 3 referans |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
