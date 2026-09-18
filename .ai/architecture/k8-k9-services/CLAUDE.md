# k8-k9-services — Servis ve API Katmanları

## Bağlam

Bu klasör, CoreMusic'in servis ve API katmanlarını (K8-K9) içerir. 7 backend servisi, API Gateway, BFF ve CQRS burada tanımlıdır.

## İlgili Dosyalar

| Dosya | Katman | İçerik |
|-------|--------|--------|
| k8-services.md | K8 | 7 servis (Control, Media, Audio, Device, Network, AI, Download) |
| k9-api-routing.md | K9 | API Gateway, BFF, CQRS, Event Bus, SPA Router |

## Komşu İlişkiler

| Yön | Hedef Klasör | İlişki |
|-----|-------------|--------|
| Yukarı | k10-k15-application/ | Uygulamalara servis sağlar |
| Aşağı | k6-k7-security/ | Güvenlik katmanından geçer |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
