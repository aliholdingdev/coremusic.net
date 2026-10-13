---
node_id: K010-identitiy/credentials/credentials-dogrulama
node_type: subdomain-scope
status: draft
parent_id: K010-identitiy/credentials
children: []
depends_on:
  - K010-identitiy
evidence:
  - "§1 iddia tablosu 12 satır (9 ✅ · 2 ⚠️ · 1 UNKNOWN) — 2026-10-12"
  - "Password.php L44/L60 · PasswordTest.php L36 · ApiKeyRepository.php · coremusic_auth.sql — 2026-10-12"
validation: "Bu dosyanın kendisi credentials dalının doğrulama defteridir"
group: kanit
open_questions:
  - "Satır 2/12: parola hash sahipliği ve credential_vault sahipliği ADR-097 K4 bekliyor"
  - "Satır 11: APP_PEPPER operasyon kanıtı UNKNOWN"
---

# credentials — Kanıt Tablosu

> **Kaynak notu:** bu dalın kendi disk kanıtları. **K009'dan iddialar TAŞINMAZ**;
> credential sızıntısı senaryosu yalnız burada, bu dalın kendi sınırından yazılır.
> Yasak: URL, sürüm, sınıf uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Bu dal "Parola / API-anahtar saklama arayüzü"dür; hash algoritması bu katmanda *seçilmez* (master iddiası) | `identity-master.md` satır 34 | 2026-10-12 | ✅ dosya okundu |
| 2 | K007 pgcrypto ↔ K010 credentials çakışması (parola hash'i kimin) karar bekliyor | `identity-master.md` satır 37 · §11/5 | 2026-10-12 | ⚠️ ADR-097 K4 |
| 3 | Şemada parola yalnız hash olarak saklanır: `password_hash varchar(255) 'Argon2id hashed password'` | `.ai/.sql/mysql/coremusic_auth.sql` `users` | 2026-10-12 | ✅ dosya okundu |
| 4 | Runtime hash üretimi Argon2id (m=65536, t=4, p=2) + HMAC-SHA256 pepper | `auth.coremusic.net/include/Domain/ValueObject/Password.php` satır 44 | 2026-10-12 | ✅ dosya okundu |
| 5 | Doğrulama `password_verify`; boş pepper → fail-closed `false` | aynı dosya satır 60 | 2026-10-12 | ✅ dosya okundu |
| 6 | Argon2id öneki test ile kilitli | `auth.coremusic.net/tests/Unit/Domain/ValueObject/PasswordTest.php` satır 36 | 2026-10-12 | ✅ dosya okundu |
| 7 | Servis sırları AES-256-GCM ile vault'ta; 90 günde bir rotasyon aralığı şemada | `.ai/.sql/mysql/coremusic_auth.sql` `credential_vault` | 2026-10-12 | ✅ dosya okundu |
| 8 | Key registry ham anahtar tutmaz (hash-only) | `.ai/.sql/mysql/coremusic_auth.sql` `credential_keys` | 2026-10-12 | ✅ dosya okundu |
| 9 | Credential erişimi append-only audit'e düşer (read/write/rotate/revoke/create) | `.ai/.sql/mysql/coremusic_auth.sql` `credential_audit` | 2026-10-12 | ✅ dosya okundu |
| 10 | API anahtarı ham değeri saklanmaz; doğrulama SHA-256 + `hash_equals` | `shared/src/Repository/ApiKeyRepository.php` | 2026-10-12 | ✅ dosya okundu |
| 11 | Pepper anahtarının saklanması/rotasyonu | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 12 | `credential_vault`'un K010 sahipliği | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 12 |
| Benzersiz dosya yolu | 6 (1 SQL dump · 1 VO PHP · 1 VO test · 1 Repository PHP · 2 vault MD) |
| Benzersiz web URL | 0 (bu dal iddialarını disk'ten kurar) |
| ✅ doğrulandı | 9 (1 · 3–10) |
| ⚠️ VERIFICATION REQUIRED | 2 (2 · 12) |
| UNKNOWN | 1 (11) |

**Geçiş kuralı:** satır 2 ve 12 ADR-097 K4 kararıyla; satır 11 pepper operasyon
kanıtı (A4/ops) ile kapanır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
