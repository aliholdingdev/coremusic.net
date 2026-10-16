---
node_id: "K008-user-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/web-arastirma"
evidence:
  - "8 URL — OWASP kimlik/güvenlik sayfaları (2026-10-15 tarama, hepsi HTTP 200)"
  - "k008-child-urls-ok.txt — havuz satır 2–8, 23"
validation: "2026-10-15 — 8 benzersiz URL; kök + kardeş çakışması 0 (C4)"
open_questions:
  - "Bu kaynaklar domain **gereksinimi** içindir; güvenlik uygulaması K008 dışındadır (master §2.2 — api/security katmanı)"
group: kanit
---

# user — Web Araştırması (K008)

> **Authority: Bayram Ali / Vault Steward.** OWASP kaynakları **yalnız bu dosyada**
> (C4 — kök `domain-web-arastirma.md` §7 ile kesişim 0). Doğrulama:
> **2026-10-15 (HTTP GET, hepsi 200)**.

## §1 YENİ TARAMA (U-1…U-8 · 2026-10-15)

| # | URL | Ne için | Durum |
|---|-----|---------|-------|
| U-1 | https://cheatsheetseries.owasp.org/cheatsheets/authentication_cheat_sheet.html | kimlik doğrulama gereksinim dili | HTTP 200 |
| U-2 | https://cheatsheetseries.owasp.org/cheatsheets/authentication_patterns_cheat_sheet.html | auth kalıpları — domain gereksinimi | HTTP 200 |
| U-3 | https://cheatsheetseries.owasp.org/cheatsheets/authorization_cheat_sheet.html | yetkilendirme (rol) dili | HTTP 200 |
| U-4 | https://cheatsheetseries.owasp.org/cheatsheets/forgot_password_cheat_sheet.html | parola sıfırlama akışı (event: `PasswordResetRequestedEvent`) | HTTP 200 |
| U-5 | https://cheatsheetseries.owasp.org/cheatsheets/json_web_token_cheat_sheet.html | token gereksinimi (session/token sınırı) | HTTP 200 |
| U-6 | https://cheatsheetseries.owasp.org/cheatsheets/multifactor_authentication_cheat_sheet.html | MFA gereksinimi | HTTP 200 |
| U-7 | https://cheatsheetseries.owasp.org/ındexasvs.html | ASVS indeksi — doğrulama ölçüt seti | HTTP 200 |
| U-8 | https://devguide.owasp.org/en/04-design/02-web-app-checklist/06-digital-identity | dijital kimlik tasarım listesi | HTTP 200 |

## §2 DAĞITIM (C4 KAYDI)

| Kontrol | Sonuç |
|---|---|
| Benzersiz URL | 8 (U-1…U-8) |
| K008 geneli çakışma | 0 (2026-10-15 `POOL-COLLISION: 0`) |

## §3 KAPSAM DIŞI

| Dışarıda | Nereye |
|---|---|
| Middleware/auth **uygulaması** (Argon2id, session, rate limit) | security katmanı (master §2.2) |
| `user` event sözleşmeleri | `user-dogrulama.md` (5 event dosya kanıtlı) |
| OAuth sağlayıcı envanteri | `shared/config/oauth-platforms.php` (kod, bu node değil) |

**Kardeş dosyalar:** [[user-kapsam]] · [[user-dogrulama]] · [[user-ornekler]] · [[user-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
