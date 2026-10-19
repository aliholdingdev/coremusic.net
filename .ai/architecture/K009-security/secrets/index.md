---
node_id: K009-security/secrets
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - secrets-index
  - secrets-anayasa
  - secrets-surucu
  - secrets-ornekler
  - secrets-kapsam
  - secrets-dogrulama
  - secrets-bilesenler
  - secrets-web-arastirma
depends_on:
  - K009-security/encryption
  - K17-devops
evidence:
  - "v2 §2.9 satır 322 (ad) — kod: ApiKeyRepository, ConfigManager, Log/Redactor"
  - ".github/workflows/secret-scan.yml (gitleaks adımı, envanter 2026-10-13)"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "`.gitleaks.toml` diskte YOK (workflow yorumu) ⚠️ VERIFICATION REQUIRED"
  - "Üretimde secret rotasyonu prosedürü belgelenmemiş ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · çocuk · secrets (Sır ve Anahtar Yönetimi)

> **Kural:** sır asla `.md` / `.json` / `.log`'a yazılmaz — yalnız `.env` veya env substitution.

## §1 ROL (ELI10)

| Adım | Ne olur | Kaçınma |
|---|---|---|
| Üretim | `random_bytes` ile üretilir | `rand()`/`mt_rand()` yasak |
| Saklama | Yalnız ortam değişkeni / gizli depo | Kaynak kod gömüsü yasak |
| Kullanım | Çalışma anında belleğe alınır | Süreç loguna basmak yasak |
| Maskelenme | Log'a yazılırken redaction | `Log/Redactor` ile maskeleme |
| Tarama | Git geçmişinde secret scanning | CI'da gitleaks adımı |
| Rotasyon | Periyodik yenileme + eskiyi iptal | Sonsuz ömür yasak |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| CI secret scanning (gitleaks) | var | workflow dosyası |
| Log redaction (Redactor) | var | kod |
| API anahtar üretimi (ApiKeyRepository) | var | kod |
| `.gitleaks.toml` özel kural seti | yok | ⚠️ workflow yorumu: diskte YOK |
| Ortam değişkeni sırrı `.env` | kısmi | `.gitignore` doğrulanacak |
| Sızıntı yanıtı (rotasyon + ihbar) | yok | ⚠️ VERIFICATION REQUIRED |
| Vault / HSM entegrasyonu | yok | ⚠️ KAPSAM DIŞI ARAŞTIRMA |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `secrets/index.md` | mimari | 40–100 | bu künye |
| `secrets/secrets-anayasa.md` | mimari | ≥500 | sır ilke ve yasakları |
| `secrets/secrets-surucu.md` | mimari | ≥500 | sır yaşam döngüsü akışı |
| `secrets/secrets-ornekler.md` | kod | ≥500 | gerçek PHP + workflow alıntıları |
| `secrets/secrets-kapsam.md` | mimari | ≥500 | içeri-dışı, sınır matrisi |
| `secrets/secrets-dogrulama.md` | kanit | ≥500 | kontrol + iddia/URL tablosu |
| `secrets/secrets-bilesenler.md` | mimari | ≥500 | kademe-3 bileşen planı |
| `secrets/secrets-web-arastirma.md` | kanit | ≥500 | NIST / OWASP kaydı |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik deposu, kullanıcı credentials'ı | `K0100-identitiy` | Yalnız sınır referansı |
| OS anahtar halkası, disk/EFI şifreleme, servis hesabı sırrı | `K000-isletim-sistemi/security` | Yalnız sınır referansı |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `encryption` | sonrası | sır → anahtar → şifreleme |
| `audit` | sonrası | sır erişimi denetim izine yazılır |
| `K17-devops` | kardeş | CI/CD sırları, deployment env |
| `security-testing` | sonrası | sızıntı taraması doğrulaması |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Sır yönetimi kontrolleri OWASP cheat sheet | https://cheatsheetseries.owasp.org/cheatsheets/Software_Supply_Chain_Security_Cheat_Sheet.html | 2026-10-13 | HTTP 200 |
| CVSS sızan bilgi sınıflandırması CWE-798 | https://cwe.mitre.org/data/definitions/798.html | 2026-10-13 | HTTP 200 |
| SSDF sır koruma adımları NIST SP 800-218 | https://csrc.nist.gov/pubs/sp/800/218/final | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
