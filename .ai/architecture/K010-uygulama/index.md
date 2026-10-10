---
type: architecture
category: layer
title: "K010 — Uygulama"
date: 2026-10-09
status: active
version: 2.0.1
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
## §7 Makro Katman Karşılığı
K010-uygulama → Makro **K3** (bağlayıcı gruplama, 2026-10-10) · Kök eşleme: [[../00-master-index]] (Makro Eşleme bölümü) · makro özet: [[../katmanli-mimari-k0-k4/index]]

- K3 içinde K010 = **controller/service yüzeyi**: K4'ten (K009) gelen HTTP isteğini DTO'ya çevirip K008 domain servisine devreden uygulama katmanıdır (makro özet §2 K3-1).
- Makro komşular: alt = **K3 içi** K008 (mikro kenar: `K010→K008` controller → service — izinli, matris §2) → devamında K005 (K2) · üst = **K4** K009 (Gateway çağırır; mikro: `K009→K010`, matris §2).
- Etiket notu: AGENTS §5'te K010 A3 (Presentation) aralığındadır; kapsamı "uygulama"dır — bu makro eşlemesi bağlayıcıdır (makro özet §5 eşlemesi; §6.2-2 izinli kenar).

## §8 Arayüz (Girdi/Çıktı & API)
- **Girdi (üstten):** K009 Gateway → controller çağrısı (matris §2: "K009 API → K010 uygulama · plan §4 (Gateway → controller)") — istek zaten K2 kanonik sırasından (Auth) geçmiştir.
- **Çıktı (aşağıya):** K008 domain servisi çağrısı (matris §2: "K010 uygulama → K008 servisler · plan §4") → servis üzerinden K005; dönüş DTO/yanıttır.
- **Diskte kanıtlı somut varlıklar (§5, 2026-10-09 ölçümü + k2 makro §4):**

| Varlık | Gerçek yol | Rol |
|---|---|---|
| `AuthController` (api) | `api.coremusic.net/include/Controller/AuthController.php` | HTTP → service köprüsü |
| `AuthController` (auth) | `auth.coremusic.net/include/Controller/AuthController.php` | HTTP → service köprüsü |
| `AuthGuard` | `shared/src/PageRouter/AuthGuard.php` | Route guard (PageRouter hattı) |
| `ReturnUrlPolicy` | `shared/src/Security/ReturnUrlPolicy.php` (k2 makro §2.6 sınıf listesi) | Return-url politikası primitifi |

- **İzinli/yasaklı çağrı özeti:** DOĞRU zincir = Controller (K010) → Service (K008) → K005 (PDO zemini); **YASAK** = Controller → PDO/DatabaseManager doğrudan → katman ihlali → derhal revert + log ERROR (k3 makro §3.2, matris §3). Port/adaptor: domain servisine yalnız port (interface) enjekte edilir; PSR-11 container binding (§2.3, plan §5.2) — **PLANNED**.
- Controller method imzaları bu görevde okunmadı → **UNKNOWN** (makro özet §7-10).

## §9 Hata Modları ve Güvenlik Sınırı
- **Failure modes (katman sınırında):**
  1. **controller→PDO bypass ihlali** — tespit → revert + log ERROR (matris §3; makro özet §5-FM1).
  2. **Port'a uyum bilinmiyor** — mevcut kodun port/adaptor modeline uyumu ölçülmedi (§6) → geçiş riski.
  3. **PSR-14 wiring PLANNED** — service→service olayları (ADR-086) implemente değil (makro özet §5-FM3).
- **İzolasyon:** iş kuralı hatası K010/K008 sınırında yakalanır; iç hata yığını K4'e taşınmaz — DTO/hata modeliyle çıkılır (makro özet §5 K3). AuthGuard hatası route'u keser, istek servis katmanına ulaşmaz.
- **Güvenlik sınırı (kim neyi doğrular):** auth doğrulaması K010'da **yeniden** uygulanmaz (K2 kanonik sırası: Auth → PageRouter — makro özet k2 §3.2); K010'un kendi katkısı **permission denetimidir — controller hattında** (ADR-056, §4) ve return-url politikası (ReturnUrlPolicy, Return-Url yönlendirme saldırı yüzeyi). CSRF/Origin/RateLimit K009/K007 sınırındadır.

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

| Bulgu | Durum |
|---|---|
| Controller method imzaları (AuthController ×2, AuthGuard) | UNKNOWN — okunmadı (makro özet §7-10) |
| Port/adaptor enjeksiyonu (plan §5.2, Faz 2) | PLANNED — mevcut kodun port uyumu bilinmiyor (§6) |
| Servis → repository oranı (plan §5.2 Faz 0 ölçümü) | ⚠️ VERIFICATION REQUIRED (§6) |
| PSR-11 container binding uygulama kanıtı | ⚠️ VERIFICATION REQUIRED — plan §5.2 düzeyinde (§2.3) |
| A3 etiketi ↔ controller kapsamı örtüşme payı | UNKNOWN (§6; makro özet §7-1 K004 komşu kaydı gibi etiket uyuşmazlığı sınıfı) |
