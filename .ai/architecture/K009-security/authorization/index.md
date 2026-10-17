---
node_id: K009-security/authorization
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - authorization-index
  - authorization-anayasa
  - authorization-surucu
  - authorization-ornekler
  - authorization-kapsam
  - authorization-dogrulama
  - authorization-bilesenler
  - authorization-web-arastirma
depends_on:
  - K009-security/authentication
  - K009-security/security-master
evidence:
  - "v2 §2.9 satır 319 (ad) — kod: PermissionMiddleware, AuthorizationMiddleware, AuthorizationException"
  - "shared/src/Middleware/PermissionMiddleware.php (64 satır, 2026-10-13 envanter)"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "Yetki modeli (RBAC/ABAC) seçimi belgelenmemiş ⚠️ VERIFICATION REQUIRED"
  - "IDOR / özne-nesne yetkisi test kanıtı yok ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · çocuk · authorization (Yetkilendirme Uygulaması)

> **Sıra kuralı:** önce `authentication` (kimlik doğru mu), sonra `authorization` (bu kimlik buna yetkili mi).

## §1 ROL (ELI10)

| Adım | Ne olur | Başarısızlıkta |
|---|---|---|
| 1 | Doğrulanmış kimlik bağlamı (`_auth`) okunur | Bağlam yoksa 401 |
| 2 | Kaynağın sahibi/rolü ile istek eşleştirilir | Eşleşmezse 403 |
| 3 | Nesne bazlı yetki (özne–nesne) tekrar kontrol edilir | İhlalde 403, kayıt açılır |
| 4 | Rol/izin seti cache'ten okunur; cache boşsa fail-closed | Erişilemezse reddet |
| 5 | Reddederken ayrıntı sızdırmaz (generic 403) | Ayrıntı yasak |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| Middleware tabanlı yetki (PermissionMiddleware) | var | kod |
| API katmanı yetki (AuthorizationMiddleware) | var | kod |
| Rol/izin kaynağı | kısmi | ⚠️ K010 ile sınır, kaynak okunmadı |
| Nesne bazlı erişim kontrolü (IDOR koruması) | kanıt yok | ⚠️ VERIFICATION REQUIRED |
| Yetki reddi audit kaydı | kısmi | audit child'ı |
| IDOR / mass-assignment testi | yok | security-testing child'ı |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `authorization/index.md` | mimari | 40–100 | bu künye |
| `authorization/authorization-anayasa.md` | mimari | ≥500 | ilke, yasak, karar kapısı |
| `authorization/authorization-surucu.md` | mimari | ≥500 | yetki akışı, durum makinesi |
| `authorization/authorization-ornekler.md` | kod | ≥500 | gerçek PHP alıntıları (Dosya/Satır) |
| `authorization/authorization-kapsam.md` | mimari | ≥500 | içeri-dışı, sınır matrisi |
| `authorization/authorization-dogrulama.md` | kanit | ≥500 | kontrol + iddia/URL tablosu |
| `authorization/authorization-bilesenler.md` | mimari | ≥500 | kademe-3 bileşen planı |
| `authorization/authorization-web-arastirma.md` | kanit | ≥500 | OWASP / NIST / RFC kaydı |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik deposu, rol/izin modelinin kaynağı | `K0100-identitiy` | Yalnız sınır referansı |
| OS düzeyi yetki (dosya izni, servis hesabı) | `K000-isletim-sistemi/security` | Yalnız sınır referansı |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `authentication` | öncesi | doğrulama sonucunu tüketir |
| `audit` | sonrası | reddi denetim izine yazar |
| `K0100-identitiy` | tek yönlü | rol/izin kaynağı |
| `K009-security` (parent) | yukarı | domen anayasasına tabi |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Erişim kontrolü ihlali OWASP A01:2025 kapsamındadır | https://cheatsheetseries.owasp.org/cheatsheets/Query_Parameterization_Cheat_Sheet.html | 2026-10-13 | HTTP 200 |
| Yetkisiz erişim CWE-306 ile izlenir | https://cwe.mitre.org/data/definitions/306.html | 2026-10-13 | HTTP 200 |
| NIST risk ölçümü olasılık × etki | https://csrc.nist.gov/pubs/sp/800/30/r1/final | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
