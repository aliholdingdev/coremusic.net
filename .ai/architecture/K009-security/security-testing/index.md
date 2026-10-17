---
node_id: K009-security/security-testing
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - security-testing-index
  - security-testing-anayasa
  - security-testing-surucu
  - security-testing-ornekler
  - security-testing-kapsam
  - security-testing-dogrulama
  - security-testing-bilesenler
  - security-testing-web-arastirma
depends_on:
  - K009-security/authentication
  - K009-security/input-validation
  - K018-devops
evidence:
  - "v2 §2.9 satır 328 (ad) — `.github/workflows/ci.yml` · `secret-scan.yml`"
  - "shared/src/Exception/RateLimitException.php · AuthenticationException.php (test hedefi)"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "SAST/DAST aracı seçimi ve CI gate'i kanıtlanmadı ⚠️ VERIFICATION REQUIRED"
  - "Güvenlik testi kapsam yüzdesi ölçülmüyor ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · çocuk · security-testing (Güvenlik Testi ve Doğrulama)

> **Kural:** "güvenli" iddiası test edilmeden yazılmaz — kanıtsız iddia `⚠️ VERIFICATION REQUIRED` kalır.

## §1 ROL (ELI10)

| Katman | Ne ölçer | Çıktı |
|---|---|---|
| Unit | tek bir koruma kuralı (ör. fail-closed) | PASS / FAIL |
| Integration | middleware zinciri sırası | PIPELINE-OK |
| Contract | reddedilen yanıt kodu sözleşmesi | 401/403/429 |
| Tarama | secret scanning (gitleaks) | CI gate |
| Dinamik | OWASP WSTG senaryoları | bulgu listesi |
| Regresyon | bilinen açık tekrar açılıyor mu | gate |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| CI workflow (`ci.yml`) | var | workflow dosyası |
| Secret scanning (`secret-scan.yml`) | var | workflow dosyası |
| Test stratejisi dokümanı | var | `security-test-stratejisi.md` |
| SAST/DAST aracı | yok | ⚠️ VERIFICATION REQUIRED |
| Dependency vulnerability taraması | yok | ⚠️ VERIFICATION REQUIRED |
| OWASP WSTG senaryosu uygulaması | yok | ⚠️ VERIFICATION REQUIRED |
| Ölçülen kapsam yüzdesi | yok | ⚠️ VERIFICATION REQUIRED |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `security-testing/index.md` | mimari | 40–100 | bu künye |
| `security-testing/security-testing-anayasa.md` | mimari | ≥500 | test kapısı ve red kriterleri |
| `security-testing/security-testing-surucu.md` | mimari | ≥500 | test akışı, gate zinciri |
| `security-testing/security-testing-ornekler.md` | kod | ≥500 | gerçek test/CI alıntıları |
| `security-testing/security-testing-kapsam.md` | mimari | ≥150 | kapsam içi/dışı sınırı, sınır matrisi |
| `security-testing/security-testing-dogrulama.md` | kanit | ≥150 | AD-xx doğrulama satırları |
| `security-testing/security-testing-bilesenler.md` | mimari | ≥150 | test bileşen envanteri |
| `security-testing/security-testing-web-arastirma.md` | kanit | ≥130 | 6 URL kaynak doğrulaması |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik deposu test çiftleri (MFA, credentials) | `K0100-identitiy` | Yalnız sınır referansı |
| OS/firmware penetrasyon testi | `K000-isletim-sistemi/security` | Yalnız sınır referansı |
| Uygulama işlevsel testi | `K011-application` | Yalnız güvenlik alt kümesi |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| tüm K009 çocukları | hedef | doğrulanan yüzey |
| `K018-devops` | sonrası | CI gate, deploy kapısı |
| `audit` | öncesi | test olayları kayda geçer |
| `policies` | öncesi | politika uyum testi |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OWASP Web Security Testing Guide test senaryoları | https://owasp.org/projects/web-security-testing-guide | 2026-10-13 | HTTP 200 |
| ZAP dinamik test aracı resmi dokümanı | https://www.zaproxy.org/docs/ | 2026-10-13 | HTTP 200 |
| SSDF uygulama güvenliği doğrulama adımları NIST 800-218 | https://csrc.nist.gov/pubs/sp/800/218/final | 2026-10-13 | HTTP 200 |
| Olay sonrası doğrulama NIST 800-61 r3 | https://csrc.nist.gov/pubs/sp/800/61/r3/final | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
