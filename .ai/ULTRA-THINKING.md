---
reference_doc: Freelancer Technical Documentation v1.0
type: protocol
category: ai
title: "AI Düşünme Protokolü"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# AI Düşünme Protokolü (Ultra Thinking)

## Amaç

CoreMusic projesinde çalışan tüm AI agentlarının kullanacağı **düşünme ve karar verme protokolü**.

## Düşünme Katmanları

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ULTRA THINKING PROTOKOLÜ                          │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 4: STRATEJİK DÜŞÜNME                               │   │
│  │    Uzun vadeli etki, mimari bütünlük, teknik borç          │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 3: TAKTİKSEL DÜŞÜNME                               │   │
│  │    Kısa vadeli çözüm, optimizasyon, performans             │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 2: OPERASYONEL DÜŞÜNME                              │   │
│  │    Günlük görevler, bug düzeltmeleri, test                 │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 1: TEMEL DÜŞÜNME                                    │   │
│  │    Kod okuma, dosya arama, basit sorular                   │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Karar Verme Matrisi

| Öncelik | Kaynak | Açıklama |
|---------|--------|----------|
| 1 | ADR Decisions | Daha önce alınmış mimari kararlar |
| 2 | CoreMusic Architecture | Projenin temel prensipleri |
| 3 | Security Requirements | Güvenlik zorunlulukları |
| 4 | Performance Requirements | Hız ve kaynak kullanımı |
| 5 | Maintainability | Uzun vadeli bakım |
| 6 | User Request | Kullanıcı ihtiyacı |

## Doğrulama Kontrolleri

Her karar öncesi:
1. ✅ Vault okundu mu?
2. ✅ ADR kontrol edildi mi?
3. ✅ Güvenlik etkisi değerlendirildi mi?
4. ✅ Performans etkisi değerlendirildi mi?
5. ✅ Alternatifler karşılaştırıldı mı?
6. ✅ Trade-off açıklandı mı?

## Hallüsinasyon Kontrolü

| Durum | Aksiyon |
|-------|---------|
| Doğrulanamayan bilgi | `VERIFICATION REQUIRED` yaz |
| Eski/yanlış bilgi | Düzelt veya sil |
| Kırık wiki-link | Doğru dosya yolunu bul |
| Eksik frontmatter | 7 zorunlu alanı ekle |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 1.0.0
**Mode:** Red Team · Human Mode · Truth Mode
