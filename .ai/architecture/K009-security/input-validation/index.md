---
node_id: K009-security/input-validation
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - input-validation-index
  - input-validation-anayasa
  - input-validation-surucu
  - input-validation-ornekler
  - input-validation-kapsam
  - input-validation-dogrulama
  - input-validation-bilesenler
  - input-validation-web-arastirma
depends_on:
  - K011-application
evidence:
  - "v2 §2.9 satır 324 (ad) — kod: ValidationMiddleware, RequestValidationMiddleware (175), Validator"
  - "shared/src/Exception/ValidationException.php"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "Doğrulama kurallarının tek kaynaktan (schema) yönetimi kanıtlanmadı ⚠️ VERIFICATION REQUIRED"
  - "Çıktı kaçışı (escaping) katmanının tam kapsamı okunmadı ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · çocuk · input-validation (Girdi Doğrulama)

> **İlk savunma hattı:** güvenilmeyen her veri, kullanılmadan ÖNCE doğrulanır; güvenilir varsayılmaz.

## §1 ROL (ELI10)

| Adım | Ne olur | Kaçınma |
|---|---|---|
| 1 | Ham girdi alınır | Kaynağa güvenmek yasak |
| 2 | Tür/uzunluk/biçim kontrolü | `filter_var` / şema |
| 3 | İzin listesi (whitelist) uygulanır | Denetleme listesi (blacklist) yetersiz |
| 4 | Parametreli sorgu | SQL birleştirme yasak (`SELECT *` yasak) |
| 5 | Çıktı kaçışı | HTML/JS bağlamına göre escape |
| 6 | Redde | Geçersizse 4xx, ayrıntı sızdırmadan |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| Middleware doğrulama (ValidationMiddleware) | var | kod |
| API istek doğrulama (RequestValidationMiddleware) | var | kod |
| Ortak doğrulayıcı (Validator) | var | kod |
| Doğrulama istisnası (ValidationException) | var | kod |
| DTO bazlı alan kuralları (Api/Dto/Request) | var | kod |
| Şekilden doğrulama (schema) tek kaynak | kısmi | ⚠️ VERIFICATION REQUIRED |
| SQL injection testi | yok | security-testing child'ı |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `input-validation/index.md` | mimari | 40–100 | bu künye |
| `input-validation/input-validation-anayasa.md` | mimari | ≥500 | whitelist ilkesi, yasaklar |
| `input-validation/input-validation-surucu.md` | mimari | ≥500 | doğrulama akışı, red haritası |
| `input-validation/input-validation-ornekler.md` | kod | ≥500 | gerçek PHP alıntıları (Dosya/Satır) |
| `input-validation/input-validation-kapsam.md` | mimari | ≥150 | 11 yüzey envanteri, sınır matrisi, devir tablosu |
| `input-validation/input-validation-dogrulama.md` | kanit | ≥150 | AD-01..AD-18 kanıt satırları + test kapsamı |
| `input-validation/input-validation-bilesenler.md` | mimari | ≥150 | 10 bileşen envanteri + bağlantı diyagramı |
| `input-validation/input-validation-web-arastirma.md` | kanit | ≥130 | 8 URL kaynak tablosu (HTTP 200, 2026-10-15) |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik alanlarının doğrulama modeli (kimlik deposu) | `K0100-identitiy` | Yalnız sınır referansı |
| OS girdi yüzeyi (komut satırı, dosya sistemi izni) | `K000-isletim-sistemi/security` | Yalnız sınır referansı |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `authorization` | sonrası | doğrulanmış girdi üzerinden yetki |
| `rate-limiting` | öncesi | aşırı girdi hacmi sınırı |
| `security-testing` | sonrası | injection testleri |
| `K011-application` | kardeş | uygulama katmanı DTO'ları |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Injection saldırıları OWASP A03:2025 kapsamındadır | https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html | 2026-10-13 | HTTP 200 |
| Parametreli sorgu OWASP kontrolleri | https://cheatsheetseries.owasp.org/cheatsheets/Query_Parameterization_Cheat_Sheet.html | 2026-10-13 | HTTP 200 |
| SQL injection CWE-89 kaydı | https://cwe.mitre.org/data/definitions/89.html | 2026-10-13 | HTTP 200 |
| Girdi doğrulama OWASP WSTG test kapsamı | https://owasp.org/projects/web-security-testing-guide | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
