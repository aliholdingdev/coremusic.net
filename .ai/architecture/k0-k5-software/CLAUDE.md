# k0-k5-software — Yazılım Altyapı Katmanları

## Bağlam

Bu klasör, CoreMusic'in temel yazılım altyapı katmanlarını (K0-K5) içerir. İşletim sisteminden veri yönetimine kadar tüm yazılım temeli bu klasörde tanımlıdır.

## İlgili Dosyalar

| Dosya | Katman | İçerik |
|-------|--------|--------|
| k0-os-layer.md | K0 | İşletim sistemi (Windows, Linux, macOS, RPi5) |
| k1-hardware-layer.md | K1 | Donanım altyapısı (XMOS, PCM3168A, Class AB) |
| k2-driver-layer.md | K2 | Sürücüler (ASIO, WASAPI, ALSA, PipeWire) |
| k3-audio-engine.md | K3 | Ses motoru (Neva Engine, DSP, Mixer) |
| k4-ai-layer.md | K4 | Yapay zeka (Analysis, Recommendation, Auto EQ) |
| k5-data-layer.md | K5 | Veri yönetimi (MySQL 18DB, Redis, APCu) |
| k03-audio-detail.md | Detayı | 6 ses servisi, media pipeline |
| k05-data-detail.md | Detayı | 18 BCNF veritabanı detayı |

## Komşu İlişkiler

| Yön | Hedef Klasör | İlişki |
|-----|-------------|--------|
| Yukarı | k6-k7-security/ | Güvenlik katmanına bağımlı |
| Aşağı | — | En alt katman (L0) |
| Sol | electronics/ | Donanım ile entegrasyon |
| Sağ | firmware/ | Firmware entegrasyonu |

## Değişiklik Protokolü

1. Değişiklik öncesi [[../index-overview]] kontrol
2. Katman bağımlılık kurallarına uy (K0→K1→K2→K3→K4→K5)
3. Audit: [[../../log.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
