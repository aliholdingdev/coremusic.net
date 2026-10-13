---
node_id: K009-security/threat-model
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - threat-model-index
  - threat-model-anayasa
  - threat-model-surucu
  - threat-model-ornekler
depends_on:
  - K009-security/security-master
  - K014-network
evidence:
  - "v2 §2.9 satır 327 (ad) — STRIDE + attack.mitre.org / CWE eşlemesi"
  - ".ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md (accepted)"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "Veri akışı diyagramı (DFD) diskte yok ⚠️ VERIFICATION REQUIRED"
  - "Tehdit kataloğu periyodik gözden geçirme tarihi belirsiz ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · çocuk · threat-model (Tehdit Modeli)

> **Soru:** neyi koruyoruz, kim tehdit ediyor, hangi yüzeyden girer, hangi kontrol durdurur?

## §1 ROL (ELI10)

| Adım | Ne olur | Çıktı |
|---|---|---|
| 1 | Kapsam ve varlıklar belirlenir | varlık listesi |
| 2 | Yüzeyler ve güven sınırları çizilir | DFD (yok ⚠️) |
| 3 | STRIDE ile tehdit üretilir | tehdit kaydı |
| 4 | Risk = olasılık × etki | NIST 800-30 r1 puanı |
| 5 | Kontrol eşlemesi yapılır | K009 çocuklarına atama |
| 6 | Kalan risk onaya sunulur | kabul / azalt / devret / yok et |

## §2 STRIDE → K009 ÇOCUK EŞLEMESİ

| STRIDE | Tehdit | K009 kontrolü | Kanıt |
|---|---|---|---|
| Spoofing | Sahte kimlik | `authentication` | AuthMiddleware |
| Tampering | Bozulmuş istek/veri | `input-validation` · `encryption` | Validator, hash |
| Repudiation | İmzalanamayan işlem | `audit` | Redactor, StructuredLogger |
| Information Disclosure | Sızan sır/jeton | `secrets` · `session-security` | gitleaks, cookie bayrakları |
| Denial of Resource | Aşırı istek | `rate-limiting` | 429 |
| Elevation of Privilege | Yetki aşımı | `authorization` | PermissionMiddleware |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `threat-model/index.md` | mimari | 40–100 | bu künye |
| `threat-model/threat-model-anayasa.md` | mimari | ≥500 | tehdit sınıflandırma ve skorlama |
| `threat-model/threat-model-surucu.md` | mimari | ≥500 | tehdit → risk → kontrol akışı |
| `threat-model/threat-model-ornekler.md` | kod | ≥500 | gerçek repo yüzeyine tehdit örnekleri |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik deposu ve kimlik modeli tehditleri | `K010-identitiy` | Yalnız sınır referansı |
| OS/kernel/boot tehditleri | `K000-isletim-sistemi/security` | Yalnız sınır referansı |
| Ağ tehditleri (DDoS, MITM) | `K014-network` | Yalnız entegrasyon |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `security-master` (parent) | yukarı | domen anayasası |
| tüm K009 çocukları | hedef | kontrol ataması |
| `security-testing` | sonrası | tehdit → test senaryosu |
| `K014-network` | kardeş | ağ katmanı tehditleri |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Tehdit modelleme OWASP cheat sheet | https://cheatsheetseries.owasp.org/cheatsheets/Threat_Modeling_Cheat_Sheet.html | 2026-10-13 | HTTP 200 |
| Güvenli ürün tasarımı OWASP cheat sheet | https://cheatsheetseries.owasp.org/cheatsheets/Secure_Product_Design_Cheat_Sheet.html | 2026-10-13 | HTTP 200 |
| Tehdit yüzeyi MITRE ATT&CK referansı | https://attack.mitre.org/ | 2026-10-13 | HTTP 200 |
| NIST risk değerlendirme yöntemi | https://csrc.nist.gov/pubs/sp/800/30/r1/final | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
