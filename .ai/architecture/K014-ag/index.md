---
type: architecture
category: layer
title: "K014 — Ağ"
date: 2026-10-09
status: active
version: 2.0.1
authority: SSOT
---

# K014 — Ağ

## §1 Kimlik
- Katman: K014 · Alan: **A4** (K12-K15).
- Kapsam: ağ/topoloji — subdomain düzeni, başlık dağıtımı (CSP), URL/routing kenarı.

## §2 Sorumluluk
1. 5 subdomain topolojisi: `home.` · `auth.` · `api.` · `media.` · `assets.coremusic.net` (plan §3; disk kanıtı kök dizin).
2. Subdomain'ler birbirine kod IMPORT etmez; yalnız `shared/` + API sözleşmesi (plan §3 — Parnas'72).
3. CSP nonce/strict-dynamic başlığının uçlarda dağıtımı (ADR-012).
4. Clean URL redirect + URL normalization (ADR-009/016), SPA router sözleşmesi (ADR-021).
5. Auth subdomain konsolidasyonu + login redirect session bridge (ADR-043/047).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K007 (edge middleware) → K006 → K000.
- **Üst (çağıran):** K011 UX (istemci) · K009 API (uçlar).

## §4 ADR Bağlantıları
ADR-004 (Multi-Domain SPA) · ADR-009 (Clean URL) · ADR-012 (CSP nonce) · ADR-016 (URL Normalization) ·
ADR-021 (SPA Router Contract) · ADR-043 (Auth Subdomain Consolidation) · ADR-047 (Login Redirect & Session Bridge).

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): kök dizin `api.coremusic.net/` `auth.coremusic.net/` `home.coremusic.net/` `media.coremusic.net/` `assets.coremusic.net/` · subdomain composer.json ×4 (api/auth/home/media).

## §6 Risk / Not
- DNS/CDN/yük dengeleme yapılandırması diskte yok → **UNKNOWN**.
- `assets.coremusic.net/` composer.json'sız (yalnız statik varlık) — kasıt doğrulanmadı.
## §7 Makro Katman Karşılığı
K014-ag → Makro **K4** (bağlayıcı gruplama, 2026-10-10) · Kök eşleme: [[../00-master-index]] (Makro Eşleme bölümü) · makro özet: [[../katmanli-mimari-k0-k4/index]]

- K4 içinde K014 = **ağ/edge katmanı**: subdomain topolojisi, edge başlığı dağıtımı (CSP), URL/routing kenarı — istemciye sunulan her şeyin ağ yüzeyi (makro özet §1 K4).
- Makro komşular: alt = **K3** (doğrudan mikro kenarı YOK) ve **K2/K0 skip-edge'leri**: `K014→K007` · `K014→K006` · `K014→K000` — matris §2'de kayıtlı istisna; norm değil, hükmü ADR bekliyor (makro özet §6.2-1/14/15, §7-6) · üst = K4 içi K011 (istemci, yalnız HTTP).
- Kardeş K4 katmanları: K009 API (uçlar) · K011 UX — birbirine kod importu YASAK (matris §2).

## §8 Arayüz (Girdi/Çıktı & API)
- **Girdi (dışarıdan):** istemci HTTP istekleri — edge başlıkları burada uygulanır/distribüt edilir: CSP nonce/strict-dynamic (ADR-012), Origin/CSRF hattı (ADR-094, K009 §2.1 ile ortak), clean URL redirect + URL normalization (ADR-009/016), SPA router sözleşmesi (ADR-021).
- **Çıktı:** isteğin K009 API uçlarına/K007 middleware'e yönlendirilmesi + statik varlık servisi (`assets.coremusic.net`) + auth redirect session bridge (ADR-043/047).
- **Diskte kanıtlı somut varlıklar (§5, 2026-10-09 ölçümü):**

| Varlık | Gerçek yol | Rol |
|---|---|---|
| 5 subdomain kökü | `api.` `auth.` `home.` `media.coremusic.net` · `assets.coremusic.net` | Topoloji (plan §3; §2.1) |
| Subdomain composer zemini | `api.` / `auth.` / `home.` / `media.coremusic.net/composer.json` (4 dosya) | shared/ autoload zemini |
| CSP kararı | ADR-012 (CSP nonce) | Edge başlığı sözleşmesi (§4) |
| Auth konsolidasyonu | ADR-043 · ADR-047 | Login redirect & session bridge (§4) |

- **İzinli/yasaklı çağrı özeti:** İZNİ = subdomain → `shared/` (composer autoload) + subdomain → API sözleşmesi (HTTP); **YASAK** = subdomain → subdomain kod importu → derhal revert (k4 makro §3.2; §2.2 — Parnas'72). Edge başlıkları → middleware kenarı (`K014→K007·K006`) skip-edge istisna kaydıdır, norm değildir (makro özet §3).
- Edge başlıklarının uygulama kodu (hangi middleware/entry) bu görevde okunmadı → **UNKNOWN** (imza uydurulmaz).

## §9 Hata Modları ve Güvenlik Sınırı
- **Failure modes (katman sınırında):**
  1. **subdomain import ihlali** — kod bağımlılığı doğarsa derhal revert (§2.2; makro özet §5-FM3).
  2. **CSP nonce sızması** — nonce yanlış yerde üretilirse XSS yüzeyi açılır (ADR-012 kapsamı; makro özet §5-FM2).
  3. **Skip-edge'lerin hükmü ADR bekliyor** — `K014→K007/K006/K000` atlamalarını norma çevirecek karar yok (makro özet §7-6).
  4. **DNS/CDN/yük dengeleme yapılandırması diskte yok** (§6) → topoloji/kesinti davranış bilinmiyor.
- **İzolasyon:** bir subdomain hatası diğerine **kod olarak** yayılmaz (import izolasyonu — makro özet §5); auth konsolidasyonu tek noktada (ADR-043) toplanır. Session bridge (ADR-047) oturum taşır, yetki taşımaz.
- **Güvenlik sınırı (kim neyi doğrular):** edge güvenliği bu katmandadır — CSP nonce dağıtımı (ADR-012, §2.3), Origin/CSRF hattı (ADR-094, K009 ile), auth konsolidasyonu (ADR-043/047, §2.5). Kanonik sıra bozulmaz: OriginCheck+CSRF → RateLimit (APCu) → CSP nonce → Auth → PageRouter (makro özet [[../katmanli-mimari-k0-k4/k2-cekirdek-servisler-middleware]] §3.2). K014 yetki kararı üretmez; başlık/orkestrasyon uygular.

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

| Bulgu | Durum |
|---|---|
| DNS/CDN/yük dengeleme yapılandırması | UNKNOWN — diskte yok (§6; makro özet §7-5) |
| Edge başlıklarını uygulayan somut kod/middleware girişi | UNKNOWN — okunmadı |
| `K014→K007/K006/K000` skip-edge'lerinin nihai hükmü | ⚠️ ADR bekliyor (makro özet §7-6) |
| `assets.coremusic.net/` composer.json'sızlığının kasıtı | UNKNOWN (§6) |
| HTTPS/sertifika/redirect zinciri yapılandırması | UNKNOWN — diskte kanıt aranmadı |
