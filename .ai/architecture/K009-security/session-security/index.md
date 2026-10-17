---
node_id: K009-security/session-security
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - session-security-index
  - session-security-anayasa
  - session-security-surucu
  - session-security-ornekler
  - session-security-kapsam
  - session-security-dogrulama
  - session-security-bilesenler
  - session-security-web-arastirma
depends_on:
  - K009-security/authentication
  - K0100-identitiy
evidence:
  - "v2 §2.9 satır 321 (ad) — kod: SessionConfig, SessionLifecycle, SessionBootstrapper, SessionKeys"
  - "shared/src/Session/ (4 dosya) · shared/src/Middleware/SessionManagerMiddleware.php"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "Oturum sabitleme (fixation) test kanıtı yok ⚠️ VERIFICATION REQUIRED"
  - "K010 ile sınır: jeton üretimi mi, yaşam döngüsü mü — karar onayı bekliyor"
group: mimari
---

# K009 · çocuk · session-security (Oturum Güvenliği)

> **Sınır:** oturum/jeton YAŞAM DÖNGÜSÜ burada; kimlik modeli ve kimlik deposu `K0100-identitiy`'de.

## §1 ROL (ELI10)

| Aşama | Ne olur | Kural |
|---|---|---|
| Oluşturma | `random_bytes` ile yüksek entropili kimlik | Tahmin edilemez, en az 128 bit |
| Yayınlama | Cookie: `HttpOnly` · `Secure` · `SameSite` | JS erişemez, kanal güvenli |
| Doğrulama | Her istekte imza/süre kontrolü | Süresi dolmuş jeton reddedilir |
| Yenileme | Kritik olayda `session_regenerate_id` | Yetki yükseltmesinde zorunlu |
| Kapanış | `session_destroy` + cookie temizliği | Çıkışta tam silme |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| Cookie bayrakları (HttpOnly/Secure/SameSite) | var | kod (SessionConfig) |
| Oturum yaşam döngüsü (başlat/sonev) | var | kod (SessionLifecycle) |
| API oturum yöneticisi (ApiSessionManager) | var | kod |
| CSRF jetonu ile oturum bağı | var | kod (CsrfMiddleware) |
| CSRF token yenileme / çift gönderim | kısmi | ⚠️ doğrulanacak |
| Oturum sabitleme/hırsızlığı testi | yok | security-testing child'ı |
| Jeton iptal listesi (revocation) | kanıt yok | ⚠️ VERIFICATION REQUIRED |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `session-security/index.md` | mimari | 40–100 | bu künye |
| `session-security/session-security-anayasa.md` | mimari | ≥500 | oturum ilke ve yasakları |
| `session-security/session-security-surucu.md` | mimari | ≥500 | yaşam döngüsü durum makinesi |
| `session-security/session-security-ornekler.md` | kod | ≥500 | gerçek PHP alıntıları (Dosya/Satır) |
| `session-security/session-security-kapsam.md` | mimari | ≥500 | içeri-dışı, sınır matrisi |
| `session-security/session-security-dogrulama.md` | kanit | ≥500 | kontrol + iddia/URL tablosu |
| `session-security/session-security-bilesenler.md` | mimari | ≥500 | kademe-3 bileşen planı |
| `session-security/session-security-web-arastirma.md` | kanit | ≥500 | RFC 6265 / 7519 / 9449 kaydı |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik deposu, credentials, jeton ANAHTARI kaynağı | `K0100-identitiy` | Yalnız sınır referansı |
| OS düzeyi oturum/servis hesabı güvenliği | `K000-isletim-sistemi/security` | Yalnız sınır referansı |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `authentication` | öncesi | kimlik doğrulama sonucu |
| `encryption` | kardeş | jeton imzalama algoritması |
| `secrets` | kardeş | gizli/imza anahtarı kaynağı |
| `rate-limiting` | kardeş | oturum deneme hız sınırları |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Oturum yönetimi kontrolleri cookie bayraklarını zorunlu kılar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Set-Cookie | 2026-10-13 | HTTP 200 |
| OAuth 2.0 Security BCP oturum kaydırmayı tanımlar | https://www.rfc-editor.org/rfc/rfc9700 | 2026-10-13 | HTTP 200 |
| NIST 800-63B oturum yenileme ve zaman aşımı kuralları | https://csrc.nist.gov/pubs/sp/800/63/b/4/final | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
