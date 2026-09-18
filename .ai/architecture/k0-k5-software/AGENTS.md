# k0-k5-software — Agent Kuralları

## Domain Boundary

Bu klasör, CoreMusic'in yazılım altyapı katmanlarını kapsar:
- K0: İşletim sistemi
- K1: Donanım altyapısı
- K2: Sürücüler
- K3: Ses işleme motoru
- K4: Yapay zeka
- K5: Veri yönetimi

## Erişim Yetkisi

| Agent | Erişim | Açıklama |
|-------|--------|----------|
| data-engineer | K5 dosyaları | Veri yönetimi |
| embedded-engineer | K1, K2 dosyaları | Donanım ve sürücüler |
| backend-architect | K3, K5 dosyaları | Servis ve veri |
| dsp-firmware-engineer | K3 dosyaları | Ses motoru |

## Kurallar

1. Katman bağımlılık kurallarına uy (K0→K5 yönünde)
2. Değişiklik öncesi ilgili ADR'leri kontrol et
3. Her değişiklik için audit log yaz

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
