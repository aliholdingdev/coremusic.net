---
node_id: K009-security/audit
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - audit-index
  - audit-anayasa
  - audit-surucu
  - audit-ornekler
depends_on:
  - K009-security/authorization
  - K017-observability
evidence:
  - "v2 §2.9 satır 326 (ad) — kod: Log/Redactor, Log/LoggerFactory, PageRouter/StructuredLogger"
  - "shared/src/Events/Domain/UserLoggedInEvent.php · UserLoggedOutEvent.php"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "Denetim kaydının saklama süresi ve bütünlük koruması belgelenmemiş ⚠️ VERIFICATION REQUIRED"
  - "Log kaynağına yetkisiz yazma engeli (append-only) doğrulanmadı ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · çocuk · audit (Denetim İzi)

> **Ne kaydedilir:** kim, ne zaman, neye erişti, sonuç ne oldu — **sır asla kaydedilmez**.

## §1 ROL (ELI10)

| Adım | Ne olur | Kural |
|---|---|---|
| 1 | Olay üretilir (giriş, çıkış, yetki reddi, sır erişimi) | Her kritik olayda bir kayıt |
| 2 | Alanlar normalize edilir | StructuredLogger: JSON alan adları |
| 3 | Hassas alan maskelenir | `Redactor` — parola/sır/token asla ham |
| 4 | Kayıt yazılır | Append-only, zaman damgalı |
| 5 | Kayıt tüketilir | `K017-observability`'e aktarılır |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| Olay nesneleri (login/logout/password-reset) | var | kod (Events/Domain) |
| Redaction (Redactor) | var | kod |
| Structured logger | var | kod |
| Yetki reddi audit kaydı | kısmi | ⚠️ doğrulanacak |
| Kayıt bütünlüğü (imza/zoetluk) | yok | ⚠️ VERIFICATION REQUIRED |
| Saklama süresi ve KVKK silme | yok | ⚠️ VERIFICATION REQUIRED |
| Saat senkronu / sıralama | OS | `K000-isletim-sistemi` kapsam dışı |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `audit/index.md` | mimari | 40–100 | bu künye |
| `audit/audit-anayasa.md` | mimari | ≥500 | ne loglanır/ne loglanmaz ilkesi |
| `audit/audit-surucu.md` | mimari | ≥500 | olay → kayıt → tüketim akışı |
| `audit/audit-ornekler.md` | kod | ≥500 | gerçek PHP alıntıları (Dosya/Satır) |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik deposu, kullanıcı kimlik alanı | `K010-identitiy` | Yalnız sınır referansı |
| OS/systemd journal, dosya izinleri, zaman sunucusu | `K000-isletim-sistemi/security` | Yalnız sınır referansı |
| Gözlemlenebilirlik metrikleri/trace saklama | `K017-observability` | Yalnız entegrasyon |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `authorization` | öncesi | reddi üreten katman |
| `secrets` | öncesi | sır erişim olayları maskelenir |
| `security-testing` | sonrası | audit kapsamı test edilir |
| `K017-observability` | sonrası | tüketici sistem |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Kayıt ve denetleme NIST 800-184 olay yanıtı rehberinde tanımlı | https://csrc.nist.gov/pubs/sp/800/184/final | 2026-10-13 | HTTP 200 |
| Adli inceleme NIST SP 800-86 | https://csrc.nist.gov/pubs/sp/800/86/final | 2026-10-13 | HTTP 200 |
| OWASP kayıt denetim kontrolleri | https://owasp.org/projects/secure-coding-practices-quick-reference-guide | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
