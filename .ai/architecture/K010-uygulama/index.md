---
type: architecture
category: layer
title: "K010 — Uygulama"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K010 — Uygulama

## §1 Kimlik
- Katman: K010 · Alan: **A3** (K10-K11 — etiket "Presentation/Gösterim"; K010 kapsamı "uygulama" — not §6).
- Kapsam: uygulama katmanı — controller/service akışı, port/adaptor enjeksiyonu.

## §2 Sorumluluk
1. Controller akışı: PageRouter → controller → domain service (plan §4-5/6).
2. DTO çevirisi; controller'ın doğrudan PDO çağırması YASAK (ihlal → revert + log ERROR, plan §4).
3. Port/adaptor: domain servisine yalnız port (interface) enjekte edilir; PDO bir adapter'dır; erişim PSR-11 container binding (plan §5.2).
4. Yeni kod için port zorunlu; mevcut kod için kademeli geçiş (plan §5.2).
5. AuthGuard/return-url politikası hattı (`shared/src/PageRouter/AuthGuard.php`, `ReturnUrlPolicy`).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K008 (servisler) → K005.
- **Üst (çağıran):** K009 (Gateway/BFF controller'ı çağırır).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-085 | Shared Library Hybrid (PageRouter/ortak katman) |
| ADR-086 | Event Driven Architecture (service→service olayları) |
| ADR-056 | Auth Modülü — permission denetimi controller hattında |

## §5 Durum
**IMPLEMENTED (controller/service)** — kanıt (2026-10-09): `api.coremusic.net/include/Controller/AuthController.php` · `auth.coremusic.net/include/Controller/AuthController.php` · `auth.coremusic.net/include/Service/*`. **PLANNED**: port/adaptor enjeksiyonu (plan §5.2, §6 Faz 2).

## §6 Risk / Not
- ⚠️ VERIFICATION REQUIRED: servis→repository oranı ölçülmedi (plan §5.2); mevcut kodun port'a uyumu bilinmiyor.
- A3 etiketi (AGENTS §5) "Presentation"; K010 controller kapsamıyla örtüşme payı **UNKNOWN** — kanonik matris notu.
