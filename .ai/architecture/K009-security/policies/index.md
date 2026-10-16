---
node_id: K009-security/policies
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - policies-index
  - policies-anayasa
  - policies-surucu
  - policies-ornekler
  - policies-kapsam
  - policies-dogrulama
  - policies-bilesenler
  - policies-web-arastirma
depends_on:
  - K009-security/security-master
  - K018-devops
evidence:
  - "v2 §2.9 satır 320 (ad) — `.ai/CLAUDE.md` §Security (OWASP Top 10:2025)"
  - ".ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md (accepted)"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200, envanter kesişimi 0"
open_questions:
  - "Parola politikası eşiği (MIN_PASSWORD_LENGTH = 8) OWASP 12+ ile çelişiyor ⚠️ ONAY BEKLİYOR"
  - "KVKK/GDPR politika metinleri vault'ta tam yazılmamış ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · çocuk · policies (Güvenlik Politikaları)

> **Politika = bağlayıcı kural.** Kod bunu uygular; politika olmadan kod keyfi kalır.

## §1 ROL (ELI10)

| Politika alanı | Karar | Kaynak |
|---|---|---|
| Parola | min 8 (repo sabiti) · OWASP 12+ önerisi · Argon2id zorunlu | `.ai/CLAUDE.md` §Security |
| Kripto | AES-256-GCM / ChaCha20 · MD5/SHA-1/DES yasak | `.ai/CLAUDE.md` §Security |
| Sır | `.md`/`.json`/`.log`'a yazım yasak · `.env` / env substitution | `.ai/CLAUDE.md` §Security |
| Katman | katmanlar arası tek yön bağımlılık | `.ai/AGENTS.md` §5 |
| Fail-safe | fail-open yerine fail-closed | `.ai/AGENTS.md` §18 |
| CSRF | ADR-010 (accepted) | `.ai/.decisions/accepted/ADR-010` |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| Kripto yasak listesi | var | vault kuralı |
| Sır yazım yasağı | var | vault kuralı |
| Fail-safe kuralı | var | vault kuralı |
| CSRF stratejisi | var | ADR-010 (accepted) |
| Parola uzunluğu politikası uyumu | çelişki | ⚠️ ONAY BEKLİYOR |
| KVKK veri saklama politikası | kısmi | ⚠️ VERIFICATION REQUIRED |
| Politika → kod izlenebilirliği matrisi | bu çocukta | denetim |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `policies/index.md` | mimari | 40–100 | bu künye |
| `policies/policies-anayasa.md` | mimari | ≥500 | bağlayıcı politika listesi |
| `policies/policies-surucu.md` | mimari | ≥500 | politika → kod → test zinciri |
| `policies/policies-ornekler.md` | kod | ≥500 | politikayı uygulayan gerçek kod |
| `policies/policies-kapsam.md` | mimari | ≥150 | kapsam sınırı (index §2 kalemleri) |
| `policies/policies-dogrulama.md` | kanit | ≥150 | AD-xx doğrulama kaydı (15 kayıt) |
| `policies/policies-bilesenler.md` | mimari | ≥150 | bileşen haritası + bağımlılık |
| `policies/policies-web-arastirma.md` | kanit | ≥130 | 7 dış kaynak (HTTP 200, 2026-10-15) |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik/parola politikasının kimlik deposu tarafı | `K010-identitiy` | Yalnız sınır referansı |
| OS güvenlik politikası (patch, servis, izin) | `K000-isletim-sistemi/security` | Yalnız sınır referansı |
| Sürüm/deploy politikası ayrı domen | `K018-devops` | Yalnız entegrasyon |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `security-master` (parent) | yukarı | domen anayasası |
| `encryption` · `secrets` · `session-security` | uygulama | politika → kod |
| `security-testing` | sonrası | politika uyum testi |
| `K018-devops` | kardeş | CI gate'leri |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OWASP Proactive Controls politika iskeleti | https://owasp.org/projects/proactive-controls | 2026-10-13 | HTTP 200 |
| OWASP Top 10:2025 politika eşlemesi | https://top10.owasp.org/2025 | 2026-10-13 | HTTP 200 |
| NIST risk çerçevesi politika ölçümü | https://csrc.nist.gov/pubs/sp/800/30/r1/final | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
