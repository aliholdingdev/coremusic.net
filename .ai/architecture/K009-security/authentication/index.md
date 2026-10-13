---
node_id: K009-security/authentication
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - authentication-index
  - authentication-anayasa
  - authentication-surucu
  - authentication-ornekler
  - authentication-kapsam
  - authentication-dogrulama
  - authentication-bilesenler
  - authentication-web-arastirma
depends_on:
  - K009-security/security-master
  - K010-identitiy
evidence:
  - "v2 §2.9 satır 318 (ad) — kod: AuthenticationMiddleware, Password.php, BypassAuthMiddleware"
  - "shared/src/Api/Middleware/AuthenticationMiddleware.php (134 satır, 2026-10-13 envanter)"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "MFA kanıtı yok (K010'ya ait) ⚠️ VERIFICATION REQUIRED"
  - "bypass akışı üretime alınmadan önce fail-closed doğrulaması gerekli"
group: mimari
---

# K009 · çocuk · authentication (Kimlik Doğrulama Uygulaması)

> **Sınır:** kimlik MODELİ `K010-identitiy`'de; bu çocuk uygulama katmanında **doğrulama**'dır.

## §1 ROL (ELI10)

| Adım | Ne olur | Başarısızlıkta |
|---|---|---|
| 1 | İstekteki kimlik kanıtı (session / bearer / API key) okunur | Kanıt yoksa 401 |
| 2 | Kanıt doğrulanır: süre, imza, iptal listesi | Geçersizse 401 |
| 3 | Parola hash'i `password_verify` ile karşılaştırılır | Yanlışsa aynı hata (zamanlama sızıntısı yok) |
| 4 | Doğrulandıysa istek `$_auth` bağlamıyla ilerler | — |
| 5 | Bypass dalı yalnızca açıkça etkinse açılır | Varsayılan kapalı (fail-closed) |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| Session tabanlı doğrulama (AuthMiddleware) | var | kod |
| API bearer doğrulama (AuthenticationMiddleware) | var | kod |
| Parola hash (Argon2id + pepper) | var | kod |
| Fail-closed bypass (BypassAuthMiddleware) | var — riskli | kod + risk |
| OAuth2 / OIDC doğrulama akışı (OAuthManager) | var | kod |
| MFA / 2FA | yok | ⚠️ KANIT YOK (K010) |
| Brute-force kilit (account lockout) | kısmi | rate-limiting child'ı |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `authentication/index.md` | mimari | 40–100 | bu künye |
| `authentication/authentication-anayasa.md` | mimari | ≥500 | ilke, yasak, karar kapısı |
| `authentication/authentication-surucu.md` | mimari | ≥500 | akış, sürücü adımları, durum makinesi |
| `authentication/authentication-ornekler.md` | kod | ≥500 | gerçek PHP alıntıları (Dosya/Satır) |
| `authentication/authentication-kapsam.md` | mimari | ≥500 | içeri-dışı, sınır matrisi |
| `authentication/authentication-dogrulama.md` | kanit | ≥500 | kontrol + iddia/URL tablosu |
| `authentication/authentication-bilesenler.md` | mimari | ≥500 | kademe-3 bileşen planı |
| `authentication/authentication-web-arastirma.md` | kanit | ≥500 | OWASP / NIST / RFC kaydı |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik deposu, kimlik modeli, credentials store | `K010-identitiy` | Yalnız sınır referansı |
| OS düzeyi parola/uzak erişim güvenliği | `K000-isletim-sistemi/security` | Yalnız sınır referansı |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `K009-security` (parent) | yukarı | domen anayasasına tabi |
| `authorization` | sonraki | doğrulama SONRASI yetki (PermissionMiddleware) |
| `session-security` | kardeş | oturum yaşam döngüsü ayrı çocuk |
| `K010-identitiy` | tek yönlü | kimlik modeli kaynağı |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Authentication cheat sheet doğrulama kontrolleri | https://cheatsheetseries.owasp.org/cheatsheets/Credential_Stuffing_Prevention_Cheat_Sheet.html | 2026-10-13 | HTTP 200 |
| NIST dijital kimlik doğrulama seviyeleri | https://csrc.nist.gov/pubs/sp/800/63/b/4/final | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
