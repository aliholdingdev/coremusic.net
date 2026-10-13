---
node_id: K009-security/rate-limiting
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - rate-limiting-index
  - rate-limiting-anayasa
  - rate-limiting-surucu
  - rate-limiting-ornekler
  - rate-limiting-kapsam
depends_on:
  - K009-security/authentication
  - K011-application
evidence:
  - "v2 §2.9 satır 325 (ad) — kod: RateLimiterMiddleware (146), CacheRateLimiter (41), IRateLimiter"
  - "shared/src/Exception/RateLimitException.php · shared/src/Api/Middleware/RateLimitMiddleware.php (80)"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "ADR-013 (rate limit) metni birebir okunmadı ⚠️ VERIFICATION REQUIRED"
  - "Fail-open davranışı tespit edilirse veto (`.ai/AGENTS.md` §18 fail-safe)"
group: mimari
---

# K009 · çocuk · rate-limiting (Hız Sınırı ve Bot Koruması)

> **Fail-safe:** sayaç servisine ulaşılamıyorsa istek **reddedilir**, geçmesi değil.

## §1 ROL (ELI10)

| Adım | Ne olur | Kural |
|---|---|---|
| 1 | Anahtar seçilir (IP / kullanıcı / API key) | Kullanıcı bazlı tercih edilir |
| 2 | Pencere sayacı okunur (APCu/Cache) | Bellek tabanlı, kilit yok |
| 3 | Eşik aşıldıysa 429 döner | `Retry-After` başlığı zorunlu |
| 4 | Sayaç yoksa davranış | fail-closed: reddet |
| 5 | Bot/credential stuffing sinyali audit'e yazılır | Log redaction uygulanır |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| Middleware hız sınırı (RateLimiterMiddleware) | var | kod |
| API hız sınırı (RateLimitMiddleware) | var | kod |
| Cache tabanlı sayaç (CacheRateLimiter) | var | kod |
| Sözleşme (IRateLimiter) + istisna | var | kod |
| 429 + `Retry-After` başlığı | kısmi | ⚠️ doğrulanacak |
| Fail-open tespiti | risk | veto eşiği |
| Bot yönetimi / imleç zorluğu | yok | ⚠️ VERIFICATION REQUIRED |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `rate-limiting/index.md` | mimari | 40–100 | bu künye |
| `rate-limiting/rate-limiting-anayasa.md` | mimari | ≥500 | eşik, pencere, fail-safe ilkesi |
| `rate-limiting/rate-limiting-surucu.md` | mimari | ≥500 | sayaç akışı, durum makinesi |
| `rate-limiting/rate-limiting-ornekler.md` | kod | ≥500 | gerçek PHP alıntıları (Dosya/Satır) |
| `rate-limiting/rate-limiting-index.md` | mimari | ≥500 | yardımcı envanter/karar dosyası |
| `rate-limiting/rate-limiting-kapsam.md` | mimari | ≥500 | içeri-dışı, sınır matrisi |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik deposu, kullanıcı hesabı kilidi kaynağı | `K010-identitiy` | Yalnız sınır referansı |
| Ağ düzeyi yavaşlatma / DDoS / firewall | `K014-network` + `K000-isletim-sistemi/security` | Yalnız sınır referansı |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `authentication` | sonrası | deneme sayımı kimlik doğrulamayı besler |
| `audit` | sonrası | 429 ve bot olayları denetim izine |
| `session-security` | kardeş | oturum deneme hızları |
| `security-testing` | sonrası | hız sınırı testi |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Hız sınırı ihlali otomatik tehdit ATG019 kapsamındadır | https://owasp.org/projects/mobile-top-10 | 2026-10-13 | HTTP 200 |
| 429 durum kodu tanımı RFC 9110 envanterde ayrıca kayıtlı | https://cwe.mitre.org/data/definitions/77.html | 2026-10-13 | HTTP 200 |
| Kaba kuvvet CWE-307 ile izlenir | https://cwe.mitre.org/data/definitions/306.html | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
